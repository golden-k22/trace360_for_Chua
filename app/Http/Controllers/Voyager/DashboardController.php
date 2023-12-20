<?php
/**
 * Created by PhpStorm.
 * User: Apollo
 * Date: 10/16/2021
 * Time: 8:27 AM
 */

namespace App\Http\Controllers\Voyager;


use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
class DashboardController
{
    public function overview(Request $request){
        $view = 'dashboard.dashboard-overview';

//        if (view()->exists("voyager::$slug.edit-detail")) {
//            $view = "voyager::$slug.edit-detail";
//        }
//
//        $categoryId=$id;
        return Voyager::view($view);
    }
    
    public function currentProductionStatus(Request $request){
        $view='dashboard.current-production-status';
        return Voyager::view($view);
    }
    public function overallEquipmentEffectiveness(Request $request){
        $view='dashboard.overall-equipment-effectiveness';
        return Voyager::view($view);
    }
    public function statisticalProcessControl(Request $request){
        $view='dashboard.statistical-process-control';
        return Voyager::view($view);
    }
    public function monthlyProductionSummary(Request $request){
        $view='dashboard.monthly-production-summary';
        return Voyager::view($view);
    }

//    public function operatorPerformance(Request $request){
//        return Voyager::view('dashboard.operator-performance');
//    }
//    public function recipeTrend(Request $request){
//        return Voyager::view('dashboard.recipe-trend');
//    }
//    public function eventSummary(Request $request){
//        return Voyager::view('dashboard.event-summary');
//    }
}