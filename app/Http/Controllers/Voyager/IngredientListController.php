<?php

namespace App\Http\Controllers\Voyager;

use App\Ingredient;
use App\IngredientList;
use App\Recipe;
use App\RecipeFlow;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadDataAdded;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class IngredientListController extends VoyagerBaseController
{
    public function category(Request $request, $recipeId) {

        $slug = "ingredient-lists";
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
//        $this->authorize('edit', app($dataType->model_name));

        //for ordering
        if (!isset($dataType->order_column) || !isset($dataType->order_display_column)) {
            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                    'message'    => __('voyager::bread.ordering_not_set'),
                    'alert-type' => 'error',
                ]);
        }

        $model = app($dataType->model_name);
        $query = $model::select('*')
            ->where('fr_rec_id', '=', $recipeId)
            ->orderBy($dataType->order_column, $dataType->order_direction);
        $usesSoftDeletes = true;
        $showSoftDeleted = false;
        if(!is_null($request->softDelete) && $request->softDelete == 1){
            $showSoftDeleted = true;
            $query = $query->withTrashed();
        }
        $getter = $dataType->server_side ? 'paginate' : 'get';
        $results = call_user_func([$query, $getter]);

        foreach ($results as $result){
            $ingredient = Ingredient::select('name')
                ->where('id', '=', $result->fr_ing_id)
                ->first();
            $quantityUnit = DB::table('measures')
                ->select('symbol')
                ->where('id', '=', $result->fr_measures)
                ->first();
            $result->ingName = $ingredient->name;
            $result->quantityUnit = $quantityUnit->symbol;
        }
        $display_column = $dataType->order_display_column;
        $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->whereField($display_column)->first();
        $recipeName = $this->getRecipeNameFromId($recipeId);
        $title = "Ingredient List For ".$recipeName."(Id = ".$recipeId.")";

        $categoryId=$recipeId;
        $active_tab='ingredient-lists';
        return Voyager::view('voyager::ingredient-lists.category', compact(
            'dataType',
            'display_column',
            'dataRow',
            'results',
            'title',
            'usesSoftDeletes',
            'showSoftDeleted',
            'categoryId',
            'active_tab'
        ));
    }

    public function createInCategory($recipeId)
    {
        $slug = "ingredient-lists";
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

        $recipeName = $this->getRecipeNameFromId($recipeId);
        $categoryId=$recipeId;
        $active_tab='ingredient-lists';
        return Voyager::view('voyager::ingredient-lists.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'recipeName', 'categoryId','active_tab'));
    }

    public function store(Request $request)
    {
        $recipeId = $request->input('fr_rec_id');
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('add', app($dataType->model_name));
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());
        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $indexRoute = "admin/".$dataType->slug."/category/".$recipeId;
                if(is_null($request->input('isFromCategory'))) {
                    $indexRoute = "admin/".$dataType->slug;
                }
                $redirect = Redirect::to($indexRoute);
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }

//    public function show(Request $request, $id)
//    {
//        $slug = $this->getSlug($request);
//        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
//        $isSoftDeleted = false;
//        if (strlen($dataType->model_name) != 0) {
//            $model = app($dataType->model_name);
//            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
//            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
//                $model = $model->withTrashed();
//            }
//            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
//                $model = $model->{$dataType->scope}();
//            }
//            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
//            if ($dataTypeContent->deleted_at) {
//                $isSoftDeleted = true;
//            }
//        } else {
//            // If Model doest exist, get data from table name
//            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
//        }
//        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);
//        $this->removeRelationshipField($dataType, 'read');
//        $this->authorize('read', $dataTypeContent);
//        $isModelTranslatable = is_bread_translatable($dataTypeContent);
//        $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);
//
//        $backUrl = "/admin/".$dataType->slug."/category/".$this->getRecipeId($id);
//        if(is_null($request->recipeId)) {
//            $backUrl = "/admin/".$dataType->slug;
//        }
//
//        return Voyager::view('voyager::ingredient-lists.read', compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted', 'backUrl'));
//    }


    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }
        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }
        $this->removeRelationshipField($dataType, 'edit');
        $this->authorize('edit', $dataTypeContent);
        $isModelTranslatable = is_bread_translatable($dataTypeContent);
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);


        if(is_null($request->recipeId)){
            return Voyager::view('voyager::ingredient-lists.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
        }

        $categoryId=$this->getRecipeId($id);
        $recipeName = $this->getRecipeNameFromId($categoryId);
        $active_tab='ingredient-lists';
        return Voyager::view('voyager::ingredient-lists.edit-add', compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'recipeName', 'id', 'categoryId', 'active_tab'));
    }

    public function update(Request $request, $id)
    {
        $recipeId=DB::table('ingredient_lists')->select('fr_rec_id')->where('id', $id )->pluck('fr_rec_id')->first();
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;
        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = $model->findOrFail($id);
        }
        $this->authorize('edit', $data);
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        if (auth()->user()->can('browse', app($dataType->model_name))) {
            $indexRoute = "admin/".$dataType->slug."/category/".$recipeId;
            if(is_null($request->input('isFromCategory'))) {
                $indexRoute = "admin/".$dataType->slug."/category/".$recipeId;
            };
            $redirect = Redirect::to($indexRoute);
        } else {
            $redirect = redirect()->back();
        }

        return $redirect->with([
            'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
            'alert-type' => 'success',
        ]);
    }

    public function restore(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $this->authorize('delete', app($dataType->model_name));
        $model = call_user_func([$dataType->model_name, 'withTrashed']);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        $data = $model->findOrFail($id);
        $displayName = $dataType->getTranslatedAttribute('display_name_singular');
        $res = $data->restore($id);
        $data = $res
            ? [
                'message'    => __('voyager::generic.successfully_restored')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_restoring')." {$displayName}",
                'alert-type' => 'error',
            ];
        $recipeId = $this->getRecipeId($id);
        $route = "admin/".$dataType->slug."/category/".$recipeId;
        if(is_null($request->recipeId)) {
            $route = "admin/".$dataType->slug;
        }
        return Redirect::to($route)->with($data);
    }

    public function destroy(Request $request, $id)
    {
        $slug = "ingredient-lists";//$this->getSlug($request);
        $recipeId = $this->getRecipeId($id);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
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
                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
                'alert-type' => 'error',
            ];
        $route = "admin/".$dataType->slug."/category/".$recipeId;
        if(is_null($request->input('isFromCategory'))) {
            $route = "admin/".$dataType->slug;
        };

        return Redirect::to($route)->with($data);
    }

    private function getRecipeId($recipeFlowId) {
        $data = IngredientList::withTrashed()->find($recipeFlowId);
        return $data->fr_rec_id;
    }

    private function getRecipeNameFromId($recipeId){
        $recipe = Recipe::withTrashed()->select('name')
            ->where('id', '=', $recipeId)
            ->first();
        return $recipe->name;
    }
}
