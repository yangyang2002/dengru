<?php
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


/** 
 *   子域名  
 */
Route::namespace('Index')->group(function () {

    #1月10号    登录视图
    Route::get('login',"IndexController@login");
    Route::any('loginDo',"IndexController@loginDo");
    Route::get('list',"IndexController@list");
    Route::get('index',"IndexController@index");
});
Route::any('Dimension',"Index\LoginController@Dimension");//生成二维码
Route::any('userLogin',"Index\LoginController@userLogin");//生成二维码

