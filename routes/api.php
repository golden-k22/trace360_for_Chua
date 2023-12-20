<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/dashboard/v1/weighing-duration', 'Api\DashboardApiController@getWeighingDurationData');
Route::post('/dashboard/v1/weighing-duration', 'Api\DashboardApiController@getWeighingDurationData');

Route::get('/dashboard/v1/outstanding-orders', 'Api\DashboardApiController@getOutstandingOrders');
Route::post('/dashboard/v1/outstanding-orders', 'Api\DashboardApiController@getOutstandingOrders');

Route::get('/dashboard/v1/timeconsuming-ingredients', 'Api\DashboardApiController@getTimeConsumingIngredients');
Route::post('/dashboard/v1/timeconsuming-ingredients', 'Api\DashboardApiController@getTimeConsumingIngredients');

Route::get('/dashboard/v1/recipe-trend', 'Api\DashboardApiController@getRecipeTrend');
Route::post('/dashboard/v1/recipe-trend', 'Api\DashboardApiController@getRecipeTrend');

Route::get('/dashboard/v1/operator-performance', 'Api\DashboardApiController@getOperatorPerformance');
Route::post('/dashboard/v1/operator-performance', 'Api\DashboardApiController@getOperatorPerformance');

Route::get('/dashboard/v1/event-summary', 'Api\DashboardApiController@getEventSummary');
Route::post('/dashboard/v1/event-summary', 'Api\DashboardApiController@getEventSummary');

Route::post('/dashboard/v1/current-progress', 'Api\DashboardApiController@getCurrentProgress');
// For New Dashboards from 2023
Route::get('/dashboard/v1/current-outstanding-orders', 'Api\DashboardApiController@getCurrentOutstandingOrders');
Route::get('/dashboard/v1/completed-orders', 'Api\DashboardApiController@getCompletedOrders');
Route::get('/dashboard/v1/progress-orders', 'Api\DashboardApiController@getProgressOrders');

Route::get('/dashboard/v1/recipes', 'Api\DashboardApiController@getAllRecipes');
Route::post('/dashboard/v1/production-orders', 'Api\DashboardApiController@getProductionOrders');
Route::post('/dashboard/v1/overall-equipment-effectiveness', 'Api\DashboardApiController@getOverallEquipmentEffectiveness');
Route::post('/dashboard/v1/refresh-spc-plot', 'Api\DashboardApiController@refreshPlotChart');
Route::post('/dashboard/v1/spc-header-datas', 'Api\DashboardApiController@getSPCHeaderData');