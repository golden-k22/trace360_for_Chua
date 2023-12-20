<?php

namespace App\Http\Controllers\Voyager;

use App\PhysicalDevice;
use App\PhysicalDeviceRecipeFlow;
use App\RecipeFlow;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use PhpOption\None;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

use Exception;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataRestored;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
use \stdClass;
use Carbon\Carbon;

class RecipeFlowController extends VoyagerBaseController
{

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
                $query = $query->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$query, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $physicalDevices = PhysicalDevice::all();
        $selectedDevices = [];
        $queryResults = DB::select('select physicaldevice_id from physicaldevice_recipeflow where recipeflow_id = ' . $id.' and deleted_at ISNULL');
        for ($index = 0; $index < count($queryResults); $index++) {
            $selectedDevices[$index] = $queryResults[$index]->physicaldevice_id;
        }
        $categoryId = DB::table('recipe_flows')->select('fr_rec_id')->where('id', $id)->pluck('fr_rec_id')->first();
        $active_tab = 'recipe-flows';

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'categoryId', 'active_tab', 'physicalDevices', 'selectedDevices'));
    }

    public function getPhysicalDeviceRecipeflow(Request $request, $id)
    {
        if ($id == 0 && $request->devices == "") {
            return response()->json(['html' => ""]);
        }elseif ($request->devices == ""){
            return response()->json(['html' => ""]);
        }
        $queryResults = DB::table('physicaldevice_recipeflow as pr')->select('physicaldevice_id')->wherein('physicaldevice_id', $request->devices)->where('recipeflow_id', $id)->pluck('physicaldevice_id')->toArray();
        $recipeflow_devices = [];
        for ($m_index = 0; $m_index < count($request->devices); $m_index++) {
            $device_setting = new stdClass;
            if (in_array($request->devices[$m_index], $queryResults)) {
                $device_setting = DB::table('physicaldevice_recipeflow as pr')->select('pr.physicaldevice_id', 'pd.name', 'cycle_time', 'lapse_time', 'qty_per_ctn', 'ctn_x_count', 'ctn_y_count', 'ctn_z_count', 'pack_qty', 'pack_material')
                    ->join('physical_devices as pd', 'pd.id', '=', 'pr.physicaldevice_id')
                    ->where('pr.physicaldevice_id', $request->devices[$m_index])
                    ->where('pr.recipeflow_id', $id)
                    ->first();
//                echo "found\n";
                array_push($recipeflow_devices, $device_setting);
            } else {
                $device_name = DB::table('physical_devices as pd')->select('pd.id', 'pd.name')
                    ->where('pd.id', $request->devices[$m_index])
                    ->first()->name;
                $device_setting->physicaldevice_id = $request->devices[$m_index];
                $device_setting->name = $device_name;
                $device_setting->cycle_time = null;
                $device_setting->lapse_time = null;
                $device_setting->qty_per_ctn = null;
                $device_setting->ctn_x_count = null;
                $device_setting->ctn_y_count = null;
                $device_setting->ctn_z_count = null;
                $device_setting->pack_qty = null;
                $device_setting->pack_material = null;
                array_push($recipeflow_devices, $device_setting);
            }
        }
        $output = view('voyager::recipe-flows.device_recipeflow_content')->with('physicalDevices', $recipeflow_devices)->render();
        return response()->json(['html' => $output]);
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $data = $query->findOrFail($id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });
        $original_data = clone($data);

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);

        // Update physicaldevice_recipeflow

        $physicaldevice_ids = $request->get('device_input_field');
        $now = Carbon::now('utc')->toDateTimeString();
        DB::table('physicaldevice_recipeflow')
            ->where('recipeflow_id', $id)
            ->whereNotIn('physicaldevice_id', $physicaldevice_ids)
            ->update(
                ['deleted_at' => $now]
            );

        // Update & Add new records.
        for ($m_index = 0; $m_index < count($physicaldevice_ids); $m_index++) {
            $is_record = DB::table('physicaldevice_recipeflow')
                ->where('physicaldevice_id', $physicaldevice_ids[$m_index])
                ->where('recipeflow_id', $id)
                ->count();

            $cycle_time = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-cycle');
            $lapse_time = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-lapse');
            $qty = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-qty');
            $qty_ctnx = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-ctnx');
            $qty_ctny = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-ctny');
            $qty_ctnz = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-ctnz');
            $pack_qty = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-pack-qty');
            $pack_mat = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-pack-mat');
            if ($is_record != 0) {
                DB::table('physicaldevice_recipeflow')
                    ->where('physicaldevice_id', $physicaldevice_ids[$m_index])
                    ->where('recipeflow_id', $id)
                    ->update([
                        'cycle_time' => $cycle_time,
                        'lapse_time' => $lapse_time,
                        'qty_per_ctn' => $qty,
                        'ctn_x_count' => $qty_ctnx,
                        'ctn_y_count' => $qty_ctny,
                        'ctn_z_count' => $qty_ctnz,
                        'pack_qty' => $pack_qty,
                        'pack_material' => $pack_mat,
                        'deleted_at' => null
                    ]);
            } else {
                $physicaldevice_recipeflow = new PhysicalDeviceRecipeFlow();
                $physicaldevice_recipeflow->recipeflow_id = $id;
                $physicaldevice_recipeflow->physicaldevice_id = $physicaldevice_ids[$m_index];
                $physicaldevice_recipeflow->cycle_time = $cycle_time;
                $physicaldevice_recipeflow->lapse_time = $lapse_time;
                $physicaldevice_recipeflow->qty_per_ctn = $qty;
                $physicaldevice_recipeflow->ctn_x_count = $qty_ctnx;
                $physicaldevice_recipeflow->ctn_y_count = $qty_ctny;
                $physicaldevice_recipeflow->ctn_z_count = $qty_ctnz;
                $physicaldevice_recipeflow->pack_qty = $pack_qty;
                $physicaldevice_recipeflow->pack_material = $pack_mat;
                $physicaldevice_recipeflow->save();
            }
        }

        event(new BreadDataUpdated($dataType, $data));

        $categoryId = DB::table('recipe_flows')->select('fr_rec_id')->where('id', $id)->pluck('fr_rec_id')->first();

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $redirect = redirect()->route('recipe-flows.category.{id}', $categoryId);
        } else {
            $redirect = redirect()->back();
        }


        return $redirect->with([
            'message' => __('voyager::generic.successfully_updated') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************


    public function createInCategory($recipeId)
    {
        $slug = "recipe-flows";
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('add', app($dataType->model_name));
        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }
        $this->removeRelationshipField($dataType, 'add');
        $isModelTranslatable = is_bread_translatable($dataTypeContent);
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'add', $isModelTranslatable);

        $physicalDevices = PhysicalDevice::all();
        $selectedDevices = [];
        $categoryId = $recipeId;
        $active_tab = 'recipe-flows';
        return Voyager::view('voyager::recipe-flows.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'categoryId', 'active_tab', 'physicalDevices', 'selectedDevices'));
    }

    /**
     * POST BRE(A)D - Store data.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        // Insert physicaldevice_recipeflow
        $recipeflow_id=$data->id;
        $physicaldevice_ids = $request->get('device_input_field');
        for ($m_index = 0; $m_index < count($physicaldevice_ids); $m_index++) {
            $cycle_time = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-cycle');
            $lapse_time = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-lapse');
            $qty = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-qty');
            $qty_ctnx = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-ctnx');
            $qty_ctny = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-ctny');
            $qty_ctnz = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-ctnz');
            $pack_qty = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-pack-qty');
            $pack_mat = $request->get('device-input-' . $physicaldevice_ids[$m_index] . '-pack-mat');

            $physicaldevice_recipeflow = new PhysicalDeviceRecipeFlow();
            $physicaldevice_recipeflow->recipeflow_id = $recipeflow_id;
            $physicaldevice_recipeflow->physicaldevice_id = $physicaldevice_ids[$m_index];
            $physicaldevice_recipeflow->cycle_time = $cycle_time;
            $physicaldevice_recipeflow->lapse_time = $lapse_time;
            $physicaldevice_recipeflow->qty_per_ctn = $qty;
            $physicaldevice_recipeflow->ctn_x_count = $qty_ctnx;
            $physicaldevice_recipeflow->ctn_y_count = $qty_ctny;
            $physicaldevice_recipeflow->ctn_z_count = $qty_ctnz;
            $physicaldevice_recipeflow->pack_qty = $pack_qty;
            $physicaldevice_recipeflow->pack_material = $pack_mat;
            $physicaldevice_recipeflow->save();
        }

        event(new BreadDataAdded($dataType, $data));

        $categoryId = $request->fr_rec_id;
        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route('recipe-flows.category.{id}', $categoryId);
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message' => __('voyager::generic.successfully_added_new') . " {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }



    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Init array of IDs
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL
            $ids[] = $id;
        }
        foreach ($ids as $id) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            // Check permission
            $this->authorize('delete', $data);

            $model = app($dataType->model_name);
            if (!($model && in_array(SoftDeletes::class, class_uses_recursive($model)))) {
                $this->cleanup($dataType, $data);
            }
        }

        $displayName = count($ids) > 1 ? $dataType->getTranslatedAttribute('display_name_plural') : $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->destroy($ids);
        $data = $res
            ? [
                'message' => __('voyager::generic.successfully_deleted') . " {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message' => __('voyager::generic.error_deleting') . " {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataDeleted($dataType, $data));
        }

        $categoryId = DB::table('recipe_flows')->select('fr_rec_id')->where('id', $id)->pluck('fr_rec_id')->first();

        return redirect()->route('recipe-flows.category.{id}', $categoryId);
    }

    public function restore(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $model = app($dataType->model_name);
        $this->authorize('delete', $model);

        // Get record
        $query = $model->withTrashed();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope' . ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        $data = $query->findOrFail($id);

        $displayName = $dataType->getTranslatedAttribute('display_name_singular');

        $res = $data->restore($id);
        $data = $res
            ? [
                'message' => __('voyager::generic.successfully_restored') . " {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message' => __('voyager::generic.error_restoring') . " {$displayName}",
                'alert-type' => 'error',
            ];

        if ($res) {
            event(new BreadDataRestored($dataType, $data));
        }

        $categoryId = DB::table('recipe_flows')->select('fr_rec_id')->where('id', $id)->pluck('fr_rec_id')->first();

        return redirect()->route('recipe-flows.category.{id}', $categoryId)->with($data);
    }


}
