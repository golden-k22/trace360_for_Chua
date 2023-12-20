<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    /* for recipe flows*/
    Route::get('recipes/{id}/detail/edit','Voyager\RecipeController@editInDetail')->name('recipes.{ingId}.detail.edit');
    Route::put('recipes/{id}/detail/update','Voyager\RecipeController@updateInDetail')->name('recipes.{ingId}.detail.update');
    Route::get('recipe-flows/category/{id}', 'Voyager\RecipeController@category')->name('recipe-flows.category.{id}');
    Route::get('recipe-flows/new-order/{id}', ['uses' => 'Voyager\RecipeFlowController@createInCategory',  'as' => 'create'] );
    Route::post('recipe-flows/physicaldevice-recipeflow/{id}', 'Voyager\RecipeFlowController@getPhysicalDeviceRecipeflow')->name('recipe-flows.physical-device.{id}.get');

    /*for ingredient lists*/
    Route::get('ingredient-lists/category/{recipeId}', 'Voyager\IngredientListController@category')->name('ingredient-lists.category.{recipeId}');
    Route::get('ingredient-lists/category/{recipeId}/create', 'Voyager\IngredientListController@createInCategory');
    Route::post('ingredient-lists/category/{recipeId}', 'Voyager\IngredientListController@storeInCategory');
    Route::post('ingredient-lists/delete/{id}', 'Voyager\IngredientListController@destroy');

    Route::get('production-activate', 'Voyager\ProductionOrderController@getActivate');
    Route::post('production-activate/', 'Voyager\ProductionOrderController@updateActivate');
    
    Route::get('physical-devices/{deviceId}/ingredient-racks', 'Voyager\IngredientRackController@ingRackOfDevice')->name('physical-devices.{deviceId}.ingredient-racks');
    Route::get('physical-devices/{deviceId}/detail/racks/create', 'Voyager\IngredientRackController@createIngredientRack');
    Route::get('physical-devices/{deviceId}/detail/edit', 'Voyager\PhysicalDeviceController@editInDetail')->name('physical-devices.{deviceId}.detail/edit');
    Route::put('physical-devices/{deviceId}/detail/update', 'Voyager\PhysicalDeviceController@updateInDetail')->name('physical-devices/{deviceId}/detail/update');

    Route::get('ingredients/{ingId}/detail/edit', 'Voyager\IngredientController@editInDetail')->name('ingredients.{ingId}.detail.edit');
    Route::put('ingredients/{ingId}/detail/update', 'Voyager\IngredientController@updateInDetail')->name('ingredients.{ingId}.detail.update');
    Route::get('ingredients/{ingId}/lot-codes', 'Voyager\LotCodeController@lotCodeOfIngredient')->name('ingredients.{ingId}.lot-codes');
    Route::get('ingredients/{ingId}/lot-codes/create', 'Voyager\LotCodeController@createInDetail')->name('ingredients.{ingId}.lot-codes.create');

    Route::get('work-orders/{id}/detail/edit','Voyager\WorkOrderController@editInDetail')->name('work-orders.{id}.detail.edit');
    Route::put('work-orders/{id}/detail/update','Voyager\WorkOrderController@updateInDetail')->name('work-orders.{id}.detail.update');
    Route::get('work-orders/{id}/work-order-items','Voyager\WorkOrderItemController@indexInDetail')->name('work-orders/{id}/work-order-items');
    Route::get('work-orders/{id}/work-order-items/create','Voyager\WorkOrderItemController@createInDetail')->name('work-orders/{id}/work-order-items/create');


    Route::get('work-orders/{id}/production-orders','Voyager\ProductionOrderController@indexOfWorkOrder')->name('work-orders.{id}.production-orders');
    Route::get('work-orders/{categoryId}/production-orders/{recipeId}','Voyager\ProductionOrderController@indexOfItem')->name('work-orders.{categoryId}.production-orders.{recipeId}');


    // Dashboard
    Route::get('overview', 'Voyager\DashboardController@overview')->name('dashboards.overview');
    Route::get('current-production-status', 'Voyager\DashboardController@currentProductionStatus')->name('dashboards.current-production-status');
    Route::get('overall-equipment-effectiveness', 'Voyager\DashboardController@overallEquipmentEffectiveness')->name('dashboards.overall-equipment-effectiveness');
    Route::get('statistical-process-control', 'Voyager\DashboardController@statisticalProcessControl')->name('dashboards.statistical-process-control');
    Route::get('monthly-production-summary', 'Voyager\DashboardController@monthlyProductionSummary')->name('dashboards.monthly-production-summary');

});


