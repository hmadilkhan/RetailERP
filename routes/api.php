<?php

use App\Http\Controllers\Api\Shopify\ErpWebhookController;
use App\Http\Controllers\EasypaisaTestController;
use App\Http\Controllers\apiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebsiteImageController;

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

Route::get('/depart/{id}', [apiController::class, 'departmentJSON']);
Route::post('/subdepart', [apiController::class, 'subdepartmentJSON']);
Route::get('/products/{id}', [apiController::class, 'productJSON']);
Route::get('/get-single-product/{id}', [apiController::class, 'productById']);
Route::get('/department-wise-products/{id}', [apiController::class, 'productJSONByDepartment']);
Route::post('/login', [apiController::class, 'login']);

/******************************************** API ROUTES *************************************************************/


Route::post('add-customer', [apiController::class, 'add_customer']);
Route::get('getCustomers/{id}', [apiController::class, 'getCustomers']);
Route::get('getInventory', [apiController::class, 'productJSON']);
Route::get('getInventoryById/{id}', [apiController::class, 'productJSONByID']);
Route::get('getInventoryByDepartment/{id}', [apiController::class, 'productByDepartment']);
Route::get('getInventoryBySubdepartment/{id}', [apiController::class, 'productBySubdepartment']);
Route::post('getsearch', [apiController::class, 'getjsonsearch']);

Route::get('getInventoryByRelated/{id}', [apiController::class, 'productByRelated']);

Route::get('getmultiimage/{id}', [apiController::class, 'getmultiimage']);

Route::get('getDepartments/{id}', [apiController::class, 'getDepartments']);
//Route::post('add-sales','apiController@add_sales');
Route::get('getCountry', [apiController::class, 'getCountry']);
Route::get('getCity', [apiController::class, 'getCity']);
Route::get('topcollection/{id}', [apiController::class, 'topcollection']);
Route::get('newproduct/{id}', [apiController::class, 'newproduct']);
Route::post('add-sales', [apiController::class, 'addSales']);
Route::post('add-sales-details', [apiController::class, 'addSalesDetails']);

Route::get('website/image/{filename}/{mode?}/{webid?}',[WebsiteImageController::class,'show_image_website']);
Route::get('optimizeimage',[WebsiteImageController::class,'Optimize_testing']);

/******************************************** API ROUTES *************************************************************/

Route::post('/webhooks/order-created', [ErpWebhookController::class, 'orderCreated']);
Route::post('/webhooks/order-updated', [ErpWebhookController::class, 'orderUpdated']);

Route::prefix('staging/easypaisa')->group(function () {
    Route::post('/otc', [EasypaisaTestController::class, 'testOtc']);
    Route::get('/ma', [EasypaisaTestController::class, 'maInfo']);
    Route::post('/ma', [EasypaisaTestController::class, 'testMa']);
    Route::post('/inquire', [EasypaisaTestController::class, 'inquire']);
});


