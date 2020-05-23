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

Route::get("mobile_users/{user_id}", "MobileUserController@show");
Route::post("mobile_users", "AuthController@register");
Route::post("mobile_users/sign_in", "AuthController@login")->name("login");
Route::post("mobile_users/password", "AuthController@resetPasswordLink");
Route::post("users/sign_in", "AuthController@loginPartner")->name("login_partner");
Route::delete("mobile_users/sign_out", "AuthController@logout");

Route::post("mobile_users/password", "AuthController@resetPasswordLink");
Route::get("mobile_users/password/edit/{id}", "AuthController@resetPasswordPage");
Route::put("mobile_users/password", "AuthController@resetPassword");

Route::post("sale_records", "SaleRecordController@store");
Route::get("sale_records", "SaleRecordController@index");

Route::post("bonuses/user_bonus", "BonusController@show");

Route::get("point_of_sales", "PointOfSaleController@index");
Route::get("point_of_sales/{id}", "PointOfSaleController@show");

Route::get("activity_types", "ActivityTypeController@index");

Route::post("partners/partners_list", "PartnerController@index");
Route::post("partners/search", "PartnerController@search");
Route::get("partners/{id}", "PartnerController@show");
Route::post("point_of_sales", "PointOfSaleController@index");
Route::post("point_of_sales/search", "PointOfSaleController@search");


