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

Auth::routes();

Route::get('/', 'HomeController@index');

Route::resource('customers', 'CustomersController');
Route::post('customers/{id}/activate', 'CustomersController@activate');
Route::post('customers/{id}/deactivate', 'CustomersController@deactivate');

Route::resource('vendors', 'VendorsController');
Route::post('vendors/{id}/activate', 'VendorsController@activate');
Route::post('vendors/{id}/deactivate', 'VendorsController@deactivate');

Route::resource('products', 'ProductsController');
Route::post('products/{id}/activate', 'ProductsController@activate');
Route::post('products/{id}/deactivate', 'ProductsController@deactivate');
Route::post('products/search_code', 'ProductsController@search_code');
Route::post('products/search_description', 'ProductsController@search_description');
Route::post('products/get_price', 'ProductsController@get_price');
Route::post('products/rpt_compare', 'ProductsController@rpt_compare');
Route::get('products/{product}/rpt_history/{vendor}', 'ProductsController@rpt_history');

Route::resource('purchase_orders', 'PurchaseOrdersController');
Route::post('purchase_orders/{id}/cancel', 'PurchaseOrdersController@cancel');
Route::post('purchase_orders/{id}/save_payment', 'PurchaseOrdersController@save_payment');
Route::get('purchase_orders/{id}/print_pdf', 'PurchaseOrdersController@print_pdf');

Route::resource('cotizations', 'CotizationsController');
Route::post('cotizations/{id}/cancel', 'CotizationsController@cancel');
Route::post('cotizations/{id}/save_payment', 'CotizationsController@save_payment');
Route::get('cotizations/{id}/print_pdf', 'CotizationsController@print_pdf');

Route::resource('statuses', 'StatusesController');

Route::resource('vendor_prices', 'VendorPricesController');

Route::resource('payment_types', 'PaymentTypesController');

Route::resource('payments', 'PaymentsController');
Route::post('payments/{id}/cancel', 'PaymentsController@cancel');

/*Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);*/