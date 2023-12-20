<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use MathPHP\Statistics\Regression;

class DashboardApiController extends Controller
{
    public function getWeighingDurationData(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        $result = DB::table('batch_conts as b')->select('r.name', DB::raw('extract(epoch from (b.end_time-b.start_time)) as duration'), 'u.name as user')
            ->join('recipe_flows as rf', 'rf.id', '=', 'fr_rec_flow_id')
            ->join('production_orders as po', 'po.id', '=', 'fr_po_id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'fr_wo_id')
            ->join('recipes as r', 'r.id', '=', 'rl.fr_rec_id')
            ->join('users as u', 'u.id', '=', 'b.fr_user_id')
            ->whereBetween('b.created_at', [$date_start, $date_end])
            ->where([['status', '=', 2], ['fr_process_step', '=', 1]]);

        $categories = (clone $result)->distinct('name')->pluck('name');
        $userNames = (clone $result)->distinct('user')->pluck('user');
        $weighingArr = (clone $result)->get();
        $dataset = [];
        for ($index = 0; $index < count($userNames); $index++) {
            $dataset[$index]['name'] = $userNames[$index];
            for ($cat_index = 0; $cat_index < count($categories); $cat_index++) {
                $dataset[$index]['data'][$cat_index] = 0;
                $item_count = 0;
                $total_time = 0;
                for ($w_index = 0; $w_index < count($weighingArr); $w_index++) {
                    if ($weighingArr[$w_index]->name == $categories[$cat_index] && $weighingArr[$w_index]->user == $userNames[$index]) {
                        $total_time += $weighingArr[$w_index]->duration;
                        $item_count += 1;
                    }
                }
                if ($item_count != 0) {
                    $dataset[$index]['data'][$cat_index] = round($total_time / $item_count / 60);
                }
            }
        }
        return Response::json(['categories' => $categories, 'dataset' => $dataset], 200);
    }

    public function getOutstandingOrders(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

//        $result = DB::table('batch_conts as b')->select('b.id','r.name', 'wo.description', 'po.qty', 'status', 'b.created_at')
        $result = DB::table('batch_conts as b')->select('b.id', 'r.name')
            ->join('recipe_flows as rf', 'rf.id', '=', 'fr_rec_flow_id')
            ->join('production_orders as po', 'po.id', '=', 'fr_po_id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'fr_wo_id')
            ->join('recipes as r', 'r.id', '=', 'rl.fr_rec_id')
            ->whereBetween('b.created_at', [$date_start, $date_end])
            ->where([['status', '=', 3], ['fr_process_step', '=', 1]])
            ->get();
        return Response::json(['dataset' => $result], 200);
    }


    public function getTimeConsumingIngredients(Request $request)
    {

        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        $ingredients = DB::table('ingredients as i')->select('i.name', DB::raw('sum(extract(epoch from (wpd.end_time-wpd.start_time))) as duration'))
            ->join('ingredient_lists as il', 'il.fr_ing_id', '=', 'i.id')
            ->join('weigh_process_data as wpd', 'wpd.fr_ing_list_id', '=', 'il.id')
            ->whereBetween('wpd.end_time', [$date_start, $date_end])
            ->groupBy('i.name')
            ->orderBy('duration', 'desc')
            ->get();
        $result = [];
        for ($index = 0; $index < count($ingredients); $index++) {
            $result[$index]['name'] = $ingredients[$index]->name;
            $result[$index]['time_space'] = $ingredients[$index]->duration;
        }
//        usort($result, function($a, $b) {
//            return $a['time_space'] <= $b['time_space'];
//        });
        return Response::json(['dataset' => $result], 200);
    }


    public function getRecipeTrend(Request $request)
    {

        $from = $request->input('from');
        $to = $request->input('to');

        /** Get Categories for all selected date range. */
        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        $categories = $this->getProductionOrdersOfRange($date_start, $date_end)->pluck('name');

        /** Get Dataset for each months. */
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthCnt = $to['month'] - $from['month'] + 1;
        for ($m_index = 0; $m_index < $monthCnt; $m_index++) {
            $dataset[$m_index]['name'] = $monthNames[$from['month'] + $m_index];
            $dataset[$m_index]['data'] = [];
            $cur_year = $from['year'];
            $cur_month = $from['month'] + $m_index + 1;
            $start_day = 1;
            if ($m_index == 0) {
                $start_day = $from['day'];
            }
            $end_day = date('t', strtotime($cur_year . "-" . $cur_month));
            if ($m_index == $monthCnt - 1) {
                $end_day = $to['day'];
            }

            $date_start = Carbon::create($cur_year, $cur_month, $start_day, 0, 0, 0, 'Asia/Singapore');
            $date_end = Carbon::create($cur_year, $cur_month, $end_day, 23, 59, 59, 'Asia/Singapore');

            $result = $this->getProductionOrdersOfRange($date_start, $date_end);

            for ($cat_index = 0; $cat_index < count($categories); $cat_index++) {
                $dataset[$m_index]['data'][$cat_index] = 0;
                foreach ($result as $row) {
                    if ($row->name == $categories[$cat_index]) {
                        $dataset[$m_index]['data'][$cat_index] = $row->sum;
                    }
                }
            }

        }
        return Response::json(['categories' => $categories, 'dataset' => $dataset], 200);
    }


    private function getProductionOrdersOfRange($date_start, $date_end)
    {

        $umCompletedList = DB::table('batch_conts as b')->select('fr_po_id')
            ->whereBetween('b.created_at', [$date_start, $date_end])
            ->where('status', '!=', '2')
            ->groupBy('fr_po_id')
            ->orderBy('fr_po_id')
            ->pluck('fr_po_id');
        $allList = DB::table('batch_conts as b')->select('fr_po_id')
            ->whereBetween('b.created_at', [$date_start, $date_end])
            ->groupBy('fr_po_id')
            ->orderBy('fr_po_id')
            ->pluck('fr_po_id');

        $diffArr = array_diff($allList->toArray(), $umCompletedList->toArray());

        $completedList = [];
        foreach ($diffArr as $key => $value) {
            $completedList[] = $value;
        }

        $production_orders = DB::table('production_orders as po')
            ->select('r.name', DB::raw('sum(po.qty)'))
            ->join('recipe_lists as rl', 'po.fr_rec_list_id', '=', 'rl.id')
            ->join('recipes as r', 'r.id', '=', 'rl.fr_rec_id')
            ->whereIn('po.id', $completedList)
            ->groupBy('r.id')
            ->get();
        return $production_orders;
    }

    public function getOperatorPerformance(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        $users = DB::table('weigh_process_data')->select('fr_user_id')
            ->whereBetween('end_time', [$date_start, $date_end])
            ->groupBy('fr_user_id')->pluck('fr_user_id');

        $dataset = [];
        for ($index = 0; $index < count($users); $index++) {
            $userName = DB::table('users')->select('name')->where('id', '=', $users[$index])->pluck('name')->first();

            $durations = DB::table('weigh_process_data as w')->select('w.fr_user_id', 'r.name as rec_name', 'rl.fr_wo_id as work_order', DB::raw('sum(extract(epoch from (end_time-start_time))) as duration'))
                ->whereBetween('w.end_time', [$date_start, $date_end])
                ->join('production_orders as po', 'fr_po_id', '=', 'po.id')
                ->join('recipe_lists as rl', 'po.fr_rec_list_id', '=', 'rl.id')
                ->join('recipes as r', 'rl.fr_rec_id', '=', 'r.id')
                ->groupBy('rl.fr_wo_id', 'rec_name', 'fr_user_id')
                ->where('fr_user_id', '=', $users[$index])
                ->orderBy('rl.fr_wo_id', 'asc')
                ->pluck('duration');

            $inter_mins = [];
            for ($t_ind = 0; $t_ind < count($durations); $t_ind++) {
                $seconds = $durations[$t_ind];
                if ($t_ind == 0)
                    $average_duration = $seconds;
                elseif ($t_ind == 1) {
                    $p_sec = $durations[$t_ind - 1];
                    $average_duration = ($p_sec + $seconds) / 2;
                } elseif ($t_ind == 2) {
                    $pp_sec = $durations[$t_ind - 2];
                    $p_sec = $durations[$t_ind - 1];
                    $average_duration = ($pp_sec + $p_sec + $seconds) / 3;
                } else {
                    $ppp_sec = $durations[$t_ind - 3];
                    $pp_sec = $durations[$t_ind - 2];
                    $p_sec = $durations[$t_ind - 1];
                    $average_duration = ($ppp_sec + $pp_sec + $p_sec + $seconds) / 4;
                }
                $inter_mins[$t_ind] = round($average_duration);
            }
            $dataset[$index]['name'] = $userName;
            $dataset[$index]['data'] = $inter_mins;

        }
        return Response::json(['dataset' => $dataset], 200);
    }


    public function getEventSummary(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        /** Get Categories for all selected date range. */
        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');
        $result = DB::table('event_lists as el')->select('el.id', 'el.event_cat', 'el.event_sub_cat', DB::raw('COUNT(dd.id)'))
            ->join('diagnostic_data as dd', 'dd.fr_event_id', '=', 'el.id')
            ->whereBetween('dd.time_stamp', [$date_start, $date_end])
            ->groupBy('el.id')
            ->get();
        $categories = $result->pluck('event_sub_cat');

        /** Get Dataset for each months. */
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthCnt = $to['month'] - $from['month'] + 1;
        for ($m_index = 0; $m_index < $monthCnt; $m_index++) {
            $dataset[$m_index]['name'] = $monthNames[$from['month'] + $m_index];
            $dataset[$m_index]['data'] = [];
            $cur_year = $from['year'];
            $cur_month = $from['month'] + $m_index + 1;
            $start_day = 1;
            if ($m_index == 0) {
                $start_day = $from['day'];
            }
            $end_day = date('t', strtotime($cur_year . "-" . $cur_month));
            if ($m_index == $monthCnt - 1) {
                $end_day = $to['day'];
            }

            $date_start = Carbon::create($cur_year, $cur_month, $start_day, 0, 0, 0, 'Asia/Singapore');
            $date_end = Carbon::create($cur_year, $cur_month, $end_day, 23, 59, 59, 'Asia/Singapore');

            $result = DB::table('event_lists as el')->select('el.id', 'el.event_cat', 'el.event_sub_cat', DB::raw('COUNT(dd.id)'))
                ->join('diagnostic_data as dd', 'dd.fr_event_id', '=', 'el.id')
                ->whereBetween('dd.time_stamp', [$date_start, $date_end])
                ->groupBy('el.id')
                ->get();

            for ($cat_index = 0; $cat_index < count($categories); $cat_index++) {
                $dataset[$m_index]['data'][$cat_index] = 0;
                foreach ($result as $row) {
                    if ($row->event_sub_cat == $categories[$cat_index]) {
                        $dataset[$m_index]['data'][$cat_index] = $row->count;
                    }
                }
            }

        }
        return Response::json(['categories' => $categories, 'dataset' => $dataset], 200);
    }

    public function getCurrentProgress(Request $request)
    {
        $cur_year = $request->input('year');
        $cur_month = $request->input('month') + 1;
        $cur_day = $request->input('day');
//        $cur_year=$request->input('year');
//        $cur_month=9;
//        $cur_day=7;

        $date_start = Carbon::create($cur_year, $cur_month, $cur_day, 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($cur_year, $cur_month, $cur_day, 23, 59, 59, 'Asia/Singapore');


        $total_po_cnt = DB::table('work_orders as wo')->select(DB::raw('COUNT(po.id)'))
            ->join('recipe_lists as rl', 'rl.fr_wo_id', '=', 'wo.id')
            ->join('production_orders as po', 'rl.id', '=', 'po.fr_rec_list_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end])->pluck('count');
        $uncompleted_po_list = DB::table('work_orders as wo')->select('po.id as production_order_id')
            ->join('recipe_lists as rl', 'rl.fr_wo_id', '=', 'wo.id')
            ->join('production_orders as po', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('batch_conts as bc', 'po.id', '=', 'bc.fr_po_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end])
            ->where('bc.status', '!=', 2)
            ->groupBy('po.id')
            ->get();
        if ($total_po_cnt[0] > 0) {
            $current_progress = ($total_po_cnt[0] - count($uncompleted_po_list)) / $total_po_cnt[0];
        } else {
            $current_progress = 0;
        }

        $result = DB::table('batch_conts as bc')->select('r.name as name', 'bc.fr_rec_flow_id as rec_flow_id')
            ->join('recipe_flows as rf', 'rf.id', '=', 'bc.fr_rec_flow_id')
            ->join('recipes as r', 'r.id', '=', 'rf.fr_rec_id')
            ->whereBetween('bc.start_time', [$date_start, $date_end])
            ->where('bc.status', '=', 1)
            ->orderBy('bc.start_time', 'desc')
            ->get();
        $recipeStarted = $result->pluck('name');
        $rec_flow_ids = $result->pluck('rec_flow_id');
        if (count($recipeStarted) > 0) {
            $current_production = $recipeStarted[0];
            $rec_flow_id = $rec_flow_ids[0];
        } else {
            $current_production = "None";
            $rec_flow_id = 0;
        }

        $preData = [];
        if ($rec_flow_id != 0) {
            $historyDur = DB::table('batch_conts as bc')->select('bc.id', DB::raw('extract(epoch from (bc.end_time-bc.start_time)) as duration'))
                ->where([['bc.fr_rec_flow_id', '=', $rec_flow_id], ['bc.status', '=', 2]])
                ->orderBy('bc.start_time', 'desc')
                ->limit(10)
                ->pluck('duration');
            for ($index = 0; $index < count($historyDur); $index++) {
                $preData[] = [$index + 1, (int)$historyDur[count($historyDur) - $index - 1]];
            }
        }

        $estimated_time = "0 hr 00 mins";
        if (count($preData) > 0) {
            $regression = new Regression\PowerLaw($preData);
            $y = $regression->evaluate(count($preData) + 1);
            $estHours = floor($y / (60 * 60));
            $estMins = round(($y % 3600) / 60);
            $estimated_time = $estHours . " hr " . $estMins . " mins";
        }

        return Response::json(['progress' => $current_progress, 'production' => $current_production, 'estimated_time' => $estimated_time], 200);
    }

    public function getCurrentOutstandingOrders(Request $request)
    {
        $status = $request->input('status'); // 3 : outstanding orders.
        $date_end = Carbon::now();
        $date_start = Carbon::create($date_end->year, $date_end->month, $date_end->day, 0, 0, 0, 'Asia/Singapore');

        $available_pos = DB::table('batch_conts as bc')->selectRaw('distinct on (po.id) po.id as po_no, bc.status as status,r.name as recipe, rl.fr_rec_id')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->join('recipes as r', 'r.id', '=', 'rl.fr_rec_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end])
            ->orderBy('po.id', 'asc')
            ->orderBy('bc.id', 'asc')
            ->get()->toArray();

        $po_array = [];
        foreach ($available_pos as $key => $value) {
            if ($value->status == $status) {
                $po_array[] = $value;
            }
        }
        return Response::json(['dataset' => $po_array], 200);
    }

    public function getCompletedOrders(Request $request)
    {
        $status = $request->input('status'); // 2 : completed orders.

        $date_end = Carbon::now();
        $date_start = Carbon::create($date_end->year, $date_end->month, $date_end->day, 0, 0, 0, 'Asia/Singapore');


        $available_pos = DB::table('batch_conts as bc')->selectRaw('distinct on (po.id) po.id as po_no, bc.status as status,r.name as recipe, rl.fr_rec_id')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->join('recipes as r', 'r.id', '=', 'rl.fr_rec_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end])
            ->orderBy('po.id', 'asc')
            ->orderBy('bc.id', 'desc')
            ->get()->toArray();

        $po_array = [];
        foreach ($available_pos as $key => $value) {
            if ($value->status == $status) {
                $po_array[] = $value;
            }
        }
        return Response::json(['dataset' => $po_array], 200);
    }

    public function getProgressOrders(Request $request)
    {
        $status = $request->input('status'); // 1 : in progress.

        $date_end = Carbon::now();
        $date_start = Carbon::create($date_end->year, $date_end->month, $date_end->day, 0, 0, 0, 'Asia/Singapore');
        // Get today Production orders which status != 5.
        $not_aborted_pos = DB::table('batch_conts as bc')->select('po.id as po_id')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end])
            ->groupBy('po.id')
            ->havingRaw('SUM(CASE WHEN bc.status =5 THEN 1 ELSE 0 END) = 0')
            ->pluck('po_id');

        // Filter Production orders 1st process status !=3
        $available_pos = DB::table('batch_conts as bc')->selectRaw('distinct on (po.id) po.id as po_id, bc.status as status')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->wherein('po.id', $not_aborted_pos)
            ->orderBy('po.id', 'asc')
            ->orderBy('bc.id', 'asc')
            ->get()->toArray();

        $po_array = [];
        foreach ($available_pos as $key => $value) {
            if ($value->status != 3) {
                $po_array[] = $value->po_id;
            }
        }

        $totalCnt = DB::table('batch_conts')->selectRaw('distinct fr_po_id, count(*) as "count"')
            ->groupBy('fr_po_id');
        $sub_batch_conts = DB::table('batch_conts as bc')->select('bc.id as bc_id', 'bc.start_time as start_time', 'r.name as recipe_name', 'ps.group as process_group', 'ps.process_name as process_name', 'pc.name as proc_cat_name', DB::raw('extract(epoch from now() AT TIME ZONE \'Asia/Singapore\'-bc.start_time) as duration'), 'bc.status', 'count as totalSteps', DB::raw('row_number() OVER (PARTITION BY bc.fr_po_id ORDER BY bc.id) AS step_order'), 'po.id as po_id',
            'ms.symbol', 'pd.is_sec_pack as is_sec_pack')
            ->join('recipe_flows as rf', 'rf.id', '=', 'bc.fr_rec_flow_id')
            ->join('recipes as r', 'r.id', '=', 'rf.fr_rec_id')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
//            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->join('process_steps as ps', 'ps.id', '=', 'rf.fr_process_step')
            ->join('process_categories as pc', 'pc.id', '=', 'ps.process_categories')
            ->leftJoin('physical_devices as pd', 'pd.id', '=', 'bc.fr_devices')
            ->leftJoin('measures as ms', 'ms.id', '=', 'bc.fr_measures')
            ->leftJoinSub($totalCnt, 'rfc', function ($join) {
                $join->on('po.id', '=', 'rfc.fr_po_id');
            })
            ->whereIn('po.id', $po_array)
//            ->where('bc.status','=',1)
            ->orderBy('bc.id', 'asc');

//$quantity = DB::table('cont_item_data')->selectRaw('distinct fr_batch_cont_id, count(fr_batch_cont_id) as "quantity"')
//            ->where('quality_status', '=', true)
//            ->groupBy('fr_batch_cont_id');
//
//        $tray_cnt = DB::table('po_containers')->selectRaw('distinct fr_batch_cont_id, count(fr_batch_cont_id) as "tray_cnt"')
//            ->where('quantity', '>', 0)
//            ->groupBy('fr_batch_cont_id');
//        $sub_batch_conts = DB::table('batch_conts as bc')->select('bc.id as bc_id', 'bc.start_time as start_time', 'r.name as recipe_name', 'ps.group as process_group', 'ps.process_name as process_name', 'pc.name as proc_cat_name', DB::raw('extract(epoch from now() AT TIME ZONE \'Asia/Singapore\'-bc.start_time) as duration'), 'bc.status', 'count as totalSteps', DB::raw('row_number() OVER (PARTITION BY bc.fr_po_id ORDER BY bc.id) AS step_order'), 'po.id as po_id',
//            DB::raw('CASE WHEN (ps.group=1 or ps.group=2) THEN -1 ELSE qty.quantity END AS quantity'), 'ms.symbol', 'tc.tray_cnt')
//            ->join('recipe_flows as rf', 'rf.id', '=', 'bc.fr_rec_flow_id')
//            ->join('recipes as r', 'r.id', '=', 'rf.fr_rec_id')
//            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
//            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
////            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
//            ->join('process_steps as ps', 'ps.id', '=', 'rf.fr_process_step')
//            ->join('process_categories as pc', 'pc.id', '=', 'ps.process_categories')
//            ->leftJoin('measures as ms', 'ms.id', '=', 'bc.fr_measures')
//            ->leftJoinSub($totalCnt, 'rfc', function ($join) {
//                $join->on('po.id', '=', 'rfc.fr_po_id');
//            })
//            ->leftJoinSub($quantity, 'qty', function ($join) {
//                $join->on('bc.id', '=', 'qty.fr_batch_cont_id');
//            })
//            ->leftJoinSub($tray_cnt, 'tc', function ($join) {
//                $join->on('bc.id', '=', 'tc.fr_batch_cont_id');
//            })
//            ->whereIn('po.id', $po_array)
////            ->where('bc.status','=',1)
//            ->orderBy('bc.id', 'asc');
//

        $progress_conts = DB::table(DB::raw("({$sub_batch_conts->toSql()}) as sub"))
            ->mergeBindings($sub_batch_conts)
            ->select('sub.*')
            ->whereIn('sub.status', [1, 4, 7, 8])
//            ->where('sub.status','=',1)
            ->get()
            ->toArray();

        $progress_conts_array = [];
        $prev_po_id = 0;
        foreach ($progress_conts as $key => $value) {
            if ($value->process_group == 3) {
                if ($value->is_sec_pack == true) {
                    //$value->quantity=DB::table('po_containers')->selectRaw('SUM(quantity) as "quantity"')
                    //    ->where('fr_batch_cont_id','=',$value->bc_id)
                    //    ->pluck('quantity')->first();
                    $value->quantity = DB::table('container_items as ci')->selectRaw('COUNT(ci.id) as "quantity"')
                        ->join('po_containers as pc', 'pc.id', '=', 'ci.fr_po_cont_id')
                        ->where('pc.fr_batch_cont_id', '=', $value->bc_id)
                        ->pluck('quantity')->first();
                    $value->tray_cnt = DB::table('po_containers')->selectRaw('count(*) as "tray_cnt"')
                        ->where('fr_batch_cont_id', '=', $value->bc_id)
                        ->pluck('tray_cnt')->first();
                } else {
                    $value->quantity = DB::table('cont_item_data')->selectRaw('count(fr_batch_cont_id) as "quantity"')
                        ->where('quality_status', '=', true)
                        ->where('fr_batch_cont_id', '=', $value->bc_id)
                        ->pluck('quantity')->first();
                    $value->tray_cnt = -1;
                }
            } else {
                $value->quantity = -1;
                $value->tray_cnt = -1;
            }


            if ($prev_po_id != $value->po_id) {
                $prev_po_id = $value->po_id;
                $step_order = [];
                $step_order[] = $value->step_order;
                $value->step_order = $step_order;
                $status = [];
                $status[] = $value->status;
                $value->status = $status;
                $value->start_time = DB::table('batch_conts as bc')->select('start_time')
                    ->where('bc.fr_po_id', '=', $prev_po_id)
                    ->orderBy('bc.id', 'asc')
                    ->pluck('start_time')
                    ->first();
                if ($value->process_group == 1) {
                    $value->duration = DB::table('batch_conts as bc')->select(DB::raw('extract(epoch from now() AT TIME ZONE \'Asia/Singapore\'-bc.start_time) as duration'))
                        ->join('recipe_flows as rf', 'rf.id', '=', 'bc.fr_rec_flow_id')
                        ->join('process_steps as ps', 'ps.id', '=', 'rf.fr_process_step')
                        ->where('bc.fr_po_id', '=', $prev_po_id)
                        ->where('ps.group', '=', 1)
                        ->whereNotNull('bc.start_time')
                        ->whereIn('bc.status', [1, 2, 7])
                        ->orderBy('bc.id', 'desc')
                        ->pluck('duration')
                        ->first();
                }
                if ($value->process_group == 2) {
                    $value->duration = DB::table('batch_conts as bc')->select(DB::raw('extract(epoch from now() AT TIME ZONE \'Asia/Singapore\'-bc.start_time) as duration'))
                        ->join('recipe_flows as rf', 'rf.id', '=', 'bc.fr_rec_flow_id')
                        ->join('process_steps as ps', 'ps.id', '=', 'rf.fr_process_step')
                        ->where('bc.fr_po_id', '=', $prev_po_id)
                        ->where('ps.group', '=', 2)
                        ->whereNotNull('bc.start_time')
                        ->whereIn('bc.status', [1, 2, 7])
                        ->orderBy('bc.id', 'desc')
                        ->pluck('duration')
                        ->first();
                }
                if ($value->process_group == 3) {
                    $value->duration = DB::table('batch_conts as bc')->select(DB::raw('extract(epoch from now() AT TIME ZONE \'Asia/Singapore\'-bc.start_time) as duration'))
                        ->join('recipe_flows as rf', 'rf.id', '=', 'bc.fr_rec_flow_id')
                        ->join('process_steps as ps', 'ps.id', '=', 'rf.fr_process_step')
                        ->where('bc.fr_po_id', '=', $prev_po_id)
                        ->where('ps.group', '=', 3)
                        ->whereNotNull('bc.start_time')
                        ->whereIn('bc.status', [1, 2, 7])
                        ->orderBy('bc.id', 'desc')
                        ->pluck('duration')
                        ->first();
                }

                $progress_conts_array[] = $value;
            } else {
                end($progress_conts_array)->last_process_name = $value->process_name;
                end($progress_conts_array)->step_order[] = $value->step_order;
                end($progress_conts_array)->status[] = $value->status;
                end($progress_conts_array)->quantity = $value->quantity;
                end($progress_conts_array)->tray_cnt = $value->tray_cnt;
                end($progress_conts_array)->symbol = $value->symbol;
            }
        }
//        The status = color
//        1-Started - orange
//        2-Completed - green (if all 2, card disappear)
//        3-Hold/New - blank
//        4-Waiting - blank
//        5-Abort - (if any is 5, card disappear)
//        6-In use and Hold - green
//        7-Pause - orange

//        select * from (select
//            wo.id as work_order_id,
//            rl.id as wo_item_id,
//            po.id as po_id,
//                row_number() OVER (PARTITION BY bc.fr_po_id ORDER BY bc.id) AS group_order,
//            r.id as recipe_id,
//                bc.start_time as start_time,
//                CASE
//                    WHEN (ps.group=1 or ps.group=2) THEN 0
//                    ELSE bc.id
//                 END AS modifiedpvc,
//            r."name" as recipe_name,
//            bc.id as batch_cont_id,
//            rf.id as rec_flow_id,
//                ps."group" as process_group,
//            ps.process_name as process_name,
//            pc."name" as proc_cat_name,
//                bc.status as status,
//                extract(epoch from (bc.end_time-bc.start_time)) as duration
//        from
//            public.batch_conts bc
//        inner join public.recipe_flows rf on
//            bc.fr_rec_flow_id = rf.id
//        inner join public.recipes r on
//            rf.fr_rec_id = r.id
//        inner join public.production_orders po on
//            bc.fr_po_id = po.id
//        inner join public.recipe_lists rl on
//            po.fr_rec_list_id = rl.id
//        inner join public.work_orders wo on
//            rl.fr_wo_id = wo.id
//        inner join public.process_steps ps on
//            ps.id = rf.fr_process_step
//        inner join public.process_categories pc on
//            pc.id = ps.process_categories
//        where
//            wo.id = 190
//        order by
//            bc.id asc
//                ) as foo

        return Response::json(['dataset' => $progress_conts_array], 200);
    }


    public function getAllRecipes(Request $request)
    {

        $all_recipes = DB::table('recipes as rc')->selectRaw('rc.id as value, rc.name as label')
            ->get();

        return Response::json(['dataset' => $all_recipes], 200);
    }

    public function getProductionOrders(Request $request)
    {
        $selected_recipe = $request->input('recipe');

        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        $available_pos = DB::table('work_orders as wo')->selectRaw('po.id as value, po.id as label')
            ->join('recipe_lists as rl', 'rl.fr_wo_id', '=', 'wo.id')
            ->join('production_orders as po', 'rl.id', '=', 'po.fr_rec_list_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end]);
        if ($selected_recipe != null) {
            $available_pos = $available_pos
                ->where('rl.fr_rec_id', $selected_recipe['value']);
        }
        $available_pos = $available_pos->get();

        return Response::json(['dataset' => $available_pos], 200);
    }

    public function getOverallEquipmentEffectiveness(Request $request)
    {
        $selected_recipe = $request->input('recipe');
        $selected_products = $request->input('po_nos');
        $selected_device = $request->input('device');

        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        // Get today Production orders which status != 5.
        $not_aborted_pos = DB::table('batch_conts as bc')->select('po.id as po_id')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->whereBetween('wo.prod_date', [$date_start, $date_end])
            ->groupBy('po.id')
            ->havingRaw('SUM(CASE WHEN bc.status =5 THEN 1 ELSE 0 END) = 0')
            ->pluck('po_id');

        // Filter Production orders 1st process status !=3
        $available_pos = DB::table('batch_conts as bc')->selectRaw('distinct on (po.id) po.id as po_id, bc.status as status')
            ->join('production_orders as po', 'bc.fr_po_id', '=', 'po.id')
            ->join('recipe_lists as rl', 'rl.id', '=', 'po.fr_rec_list_id')
            ->join('work_orders as wo', 'wo.id', '=', 'rl.fr_wo_id')
            ->wherein('po.id', $not_aborted_pos)
            ->orderBy('po.id', 'asc')
            ->orderBy('bc.id', 'asc');


        if ($selected_recipe != null) {
            $available_pos->where('rl.fr_rec_id', $selected_recipe['value']);
        }
        if (count($selected_products) > 0) {
            foreach ($selected_products as $product) {
                $products[] = $product['value'];
            }
            $available_pos->whereIn('po.id', $products);
        }
        $available_pos=$available_pos->get()->toArray();

        $po_array = [];
        foreach ($available_pos as $key => $value) {
            $po_data=array("po_id"=>0,);
            if ($value->status != 3) {

                $po_data["po_id"]= $value->po_id;
                $oee_log_setting = DB::table('oee_log_settings as ols')->select('*')
                    ->leftJoin('physical_devices as pd', 'pd.id', '=', 'ols.fr_device_id')
                    ->where('ols.fr_po_id', '=',$po_data["po_id"])
                    ->orderBy("ols.fr_bc_id",'asc')
                    ->get();
                $devices=[];
                $status=3;
                foreach ($oee_log_setting as $oee_key => $oee_value) {
                    $devices[] = [
                        'label' => $oee_value->name,
                        'value' => $oee_value->fr_device_id
                    ];
                    $status=$oee_value->status;
                }
                $po_data["devices"]=$devices;

                if ($selected_device == null) {
                    $ordered_oee_logs = DB::table('oee_log_data as old')->selectRaw('old.log_time,
                    old.log_time,
                    old.product_name,
                    old.fr_po_id as po_id,
                    old.po_from_time as from,
                    old.po_lapse as lapse,
                    old.complete,
                    old.target,
                    old.sec_output,
                   ROUND((SUM(old.cycle_percent * old.weight)/SUM(old.weight))::numeric,4) AS weighted_cycle_percent,
                   ROUND((SUM(old.availability * old.weight)/SUM(old.weight))::numeric,4) AS weighted_availability,  
                   ROUND((SUM(old.yield * old.weight)/SUM(old.weight))::numeric,4) AS weighted_yield, 
                   ROUND((SUM(old.quantity * old.weight)/SUM(old.weight))::numeric,4) AS weighted_quantity
                ')
                        ->where('old.fr_po_id', '=', $po_data["po_id"])
                        ->groupBy('old.log_time', 'old.fr_po_id', 'old.po_from_time', 'old.po_lapse', 'old.product_name', 'old.complete', 'old.target', 'old.sec_output')
                        ->orderBy('old.log_time', 'desc')
                        ->limit(5)
                        ->get();
                }else{
                    $ordered_oee_logs = DB::table('oee_log_data as old')->selectRaw('old.log_time,
                    old.log_time,
                    old.product_name,
                    old.fr_po_id as po_id,
                    old.po_from_time as from,
                    old.po_lapse as lapse,
                    old.complete,
                    old.target,
                    old.sec_output,
                    old.cycle_percent AS weighted_cycle_percent,
                    old.availability AS weighted_availability,  
                    old.yield AS weighted_yield, 
                    old.quantity AS weighted_quantity
                ')
                        ->where('old.fr_po_id', '=', $po_data["po_id"])
                        ->where('old.fr_device_id','=',$selected_device['value'])
                        ->orderBy('old.log_time', 'desc')
                        ->limit(5)
                        ->get();
                }

                $oee_recent_5=[];

                $product_name="No Data";
                $po_from="No Data";
                $po_lapse="No Data";
                $complete=0;
                $target=0;
                $sec_output=0;
                $cycle_pct="No Data";
                $availability="No Data";
                $yield="No Data";
                $quantity="No Data";
                $oee_overall="No Data";
                foreach ($ordered_oee_logs as $oee_log_key => $oee_log_value) {

                    if ($oee_log_key==0){

                        $product_name=$oee_log_value->product_name;

                        $po_from=$oee_log_value->from;
                        $po_lapse=$oee_log_value->lapse;
                        $complete=$oee_log_value->complete;
                        $target=$oee_log_value->target;
                        $sec_output=$oee_log_value->sec_output;

                        $cycle_pct=$oee_log_value->weighted_cycle_percent;
                        $availability=$oee_log_value->weighted_availability;
                        $yield=$oee_log_value->weighted_yield;
                        $quantity=$oee_log_value->weighted_quantity;
                        $oee_overall=$oee_log_value->weighted_availability*$oee_log_value->weighted_yield*$oee_log_value->weighted_quantity/10000;
                    }else{
                        $cycle_pct=$oee_log_value->weighted_cycle_percent;
                        $availability=$oee_log_value->weighted_availability;
                        $yield=$oee_log_value->weighted_yield;
                        $quantity=$oee_log_value->weighted_quantity;
                    }

                    $oee_recent_5['cycle_pct'][]=$cycle_pct;
                    $oee_recent_5['availability'][]=$availability;
                    $oee_recent_5['yield'][]=$yield;
                    $oee_recent_5['quantity'][]=$quantity;
                }
                $po_data['recipe_name']=$product_name;
                $po_data['start_time']=$po_from;
                $po_data['lapse_time']=$po_lapse;
                $po_data['current']=$complete;
                $po_data['target']=$target;
                $po_data['sec_output']=$sec_output;
                $po_data['oee_overall']=round($oee_overall);
                $po_data['oee_datas']=$oee_recent_5;
                $po_data['status']=$status;
                $po_array[]=$po_data;
            }
        }

        array_slice($po_array,0,3);

        return Response::json(['dataset' => $po_array], 200);


    }


    public function getSPCHeaderData(Request $request)
    {
        $selected_devices = $request->input('devices');
        $selected_products = $request->input('po_nos');
        $selected_sensors = $request->input('sensors');
        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');

        $available_pdds = DB::table('public.product_process_data as ppd')->selectRaw(
            'ppd.id, ppd.timestamp, ppd.value1, ppd.f_item_id,
            ppd.f_sensor_id as sensor_id,s.name as sensor_name,
            ppd.fr_deviceid as device_id, pd.name as device_name, 
            ppd.fr_poid product_id, r.name as product_name, ppd.fr_bcid')
            ->leftJoin('public.physical_devices as pd', 'pd.id', '=', 'ppd.fr_deviceid')
            ->leftJoin('production_orders as po', 'po.id', '=', 'ppd.fr_poid')
            ->leftJoin('public.batch_conts as bc', 'bc.id', '=', 'ppd.fr_bcid')
            ->leftJoin('public.recipes as r', 'r.id', '=', 'bc.fr_recipe_id')
            ->leftJoin('public.sensors as s', 's.id', '=', 'ppd.f_sensor_id')
            ->whereBetween('ppd.timestamp', [$date_start, $date_end]);


        if (count($selected_products) > 0) {
            foreach ($selected_products as $product) {
                $products[] = $product['value'];
            }
            $available_pdds->whereIn('po.id', $products);
        }
        if (count($selected_devices) > 0) {
            foreach ($selected_devices as $product) {
                $devices[] = $product['value'];
            }
            $available_pdds->whereIn('ppd.fr_deviceid', $devices);
        }
        if (count($selected_sensors) > 0) {
            foreach ($selected_sensors as $product) {
                $sensors[] = $product['value'];
            }
            $available_pdds->whereIn('ppd.f_sensor_id', $sensors);
        }

        $pos=[];
        $available_pos=$available_pdds
            ->get()
            ->pluck('product_id','product_name')
            ->unique()->toArray();
        foreach ($available_pos as $key => $value) {
            $po_data=array("label"=>$key,'value'=>$value);
            $pos[]=$po_data;
        }
        $devices=[];
        $available_devices=$available_pdds
            ->get()
            ->pluck('device_id','device_name')
            ->unique()->toArray();
        foreach ($available_devices as $key => $value) {
            $d_data=array("label"=>$key,'value'=>$value);
            $devices[]=$d_data;
        }

        $sensors=[];
        $available_sensors=$available_pdds
            ->get()
            ->pluck('sensor_id','sensor_name')
            ->unique()->toArray();
        foreach ($available_sensors as $key => $value) {
            $s_data=array("label"=>$key,'value'=>$value);
            $sensors[]=$s_data;
        }
        $menu_data=array("products"=>$pos,'devices'=>$devices,'sensors'=>$sensors);
        $timestamps=$available_pdds
            ->get()
            ->pluck('timestamp')
            ->toArray();
        $values=$available_pdds
            ->get()
            ->pluck('value1')
            ->toArray();



        return Response::json(['dataset' => $menu_data], 200);
    }




    public function refreshPlotChart(Request $request)
    {
        $selected_devices = $request->input('devices');
        $selected_products = $request->input('po_nos');
        $selected_sensors = $request->input('sensors');
        $from = $request->input('from');
        $to = $request->input('to');

        $date_start = Carbon::create($from['year'], $from['month'] + 1, $from['day'], 0, 0, 0, 'Asia/Singapore');
        $date_end = Carbon::create($to['year'], $to['month'] + 1, $to['day'], 23, 59, 59, 'Asia/Singapore');



        $j_selected_devices=escapeshellarg(json_encode($selected_devices));
        $j_selected_products=escapeshellarg(json_encode($selected_products));
        $j_selected_sensors=escapeshellarg(json_encode($selected_sensors));
//        $j_from=escapeshellarg(json_encode($from));
        $j_to=escapeshellarg(json_encode($to));
        $j_from=escapeshellarg($date_start->format('d/m/Y  H:i:s'));

        $command = escapeshellcmd("python ./py/spcxmrrules10-3.py $j_selected_devices, $j_selected_products $j_selected_sensors $j_from $j_to");
        $output_data = shell_exec($command);
        error_log($output_data);



//        $available_pdds = DB::table('public.product_process_data as ppd')->selectRaw(
//            'ppd.id, to_char(ppd.timestamp, \'HH24:MI:SS\') as timestamp, ppd.value1')
//            ->leftJoin('public.physical_devices as pd', 'pd.id', '=', 'ppd.fr_deviceid')
//            ->leftJoin('production_orders as po', 'po.id', '=', 'ppd.fr_poid')
//            ->leftJoin('public.batch_conts as bc', 'bc.id', '=', 'ppd.fr_bcid')
//            ->leftJoin('public.recipes as r', 'r.id', '=', 'bc.fr_recipe_id')
//            ->leftJoin('public.sensors as s', 's.id', '=', 'ppd.f_sensor_id')
//            ->whereBetween('ppd.timestamp', [$date_start, $date_end]);
//
//
//        if (count($selected_products) > 0) {
//            foreach ($selected_products as $product) {
//                $products[] = $product['value'];
//            }
//            $available_pdds->whereIn('po.id', $products);
//        }
//        if (count($selected_devices) > 0) {
//            foreach ($selected_devices as $product) {
//                $devices[] = $product['value'];
//            }
//            $available_pdds->whereIn('ppd.fr_deviceid', $devices);
//        }
//        if (count($selected_sensors) > 0) {
//            foreach ($selected_sensors as $product) {
//                $sensors[] = $product['value'];
//            }
//            $available_pdds->whereIn('ppd.f_sensor_id', $sensors);
//        }
//        $timestamps=$available_pdds
//            ->get()
//            ->pluck('timestamp')
//            ->toArray();
//        $values=$available_pdds
//            ->get()
//            ->pluck('value1')
//            ->toArray();



//        $timestamps=[
//            "16:02:17", "16:02:29", "16:02:52", "16:03:04", "16:03:26", "16:03:49",
//            "16:04:01", "16:04:13", "16:04:25", "16:04:37", "16:04:49", "16:05:15",
//            "16:05:27", "16:05:39", "16:05:51", "16:06:03", "16:06:15", "16:06:27",
//            "16:06:39", "16:06:52", "16:07:04", "16:07:16", "16:07:28", "16:07:40",
//            "16:08:12", "16:08:25", "16:08:47", "16:09:02", "16:09:14", "16:09:39",
//            "16:09:52", "16:10:04", "16:10:16", "16:10:28", "16:10:40", "16:10:52",
//            "16:11:05", "16:11:17", "16:11:29", "16:11:41", "16:11:53", "16:12:05",
//            "16:12:17", "16:12:29", "16:12:41", "16:12:53", "16:13:05", "16:13:17",
//            "16:13:29", "16:13:41", "16:13:53", "16:14:05", "16:14:28", "16:14:40",
//            "16:14:52", "16:15:04", "16:15:16", "16:15:28", "16:15:40", "16:15:52",
//            "16:16:15", "16:16:27", "16:16:50", "16:17:02", "16:17:14", "16:17:26",
//            "16:17:38", "16:17:50", "16:18:02", "16:18:14", "16:18:26", "16:18:38",
//            "16:18:51", "16:19:03", "16:19:15", "16:19:37", "16:19:49", "16:20:01",
//            "16:20:14", "16:20:25", "16:20:48", "16:21:00", "16:21:22", "16:21:45",
//            "16:21:57", "16:22:09", "16:22:22", "16:23:20", "16:26:22", "16:26:34",
//            "16:26:46", "16:27:19", "16:27:31", "16:30:41", "16:31:03"
//        ];
//        $values = [
//            377.3481, 369.7120, 385.5853, 379.4078, 389.5321, 379.6655, 381.5523,
//            375.2896, 379.8365, 346.9752, 384.6413, 367.1380, 376.0619, 370.5704,
//            370.9138, 381.2951, 376.9196, 368.3392, 369.9695, 361.2178, 344.7445,
//            374.3454, 367.4818, 370.1412, 357.5286, 362.1621, 380.0938, 381.8106,
//            380.9522, 366.7948, 374.1745, 409.4381, 364.5645, 332.3893, 371.5147,
//            370.9992, 374.6029, 352.5528, 369.7979, 373.0589, 362.9336, 364.4787,
//            377.0058, 355.8986, 378.3778, 370.1412, 377.6054, 363.8778, 372.2862,
//            360.9612, 365.5088, 382.5822, 370.8279, 393.0497, 354.5261, 366.3652,
//            363.7917, 369.8846, 364.2207, 374.6033, 363.8778, 381.2951, 374.2596,
//            375.7188, 373.8307, 369.1121, 363.1055, 361.7328, 354.6120, 381.6389,
//            360.3601, 385.9281, 376.5764, 339.1674, 345.2594, 375.3755, 352.2090,
//            340.4545, 337.7945, 399.8281, 364.7362, 366.7954, 377.0051, 373.4870,
//            389.7897, 391.9341, 361.9904, 17.3315, 363.7920, 375.7187, 384.6413,
//            368.4255, 399.3988, 179.2364, 369.9701
//        ];

//        error_log(json_encode($timestamps));
//        $json_timestamps=escapeshellarg(json_encode($timestamps));
//        $json_values=escapeshellarg(json_encode($values));
////        ini_set('memory_limit', '2048M');;
//        $command = escapeshellcmd("python ./py/spcxmrrules10-3.py $json_timestamps, $json_values");
//        $output_data = shell_exec($command);
////        error_log($output_data);
        return Response::json(['generated' => "True"], 200);
    }
}