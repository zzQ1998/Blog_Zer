<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-09 00:20:15
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-12-06 00:58:33
 */

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
    return redirect('admin/index');
});


//验证码路由
Route::get('code/captcha/{tmp}', 'Admin\LoginController@captcha');

Route::group(['prefix' => 'admin','namespace'=>'Admin'], function() {
    //后台登录路由
    Route::get('login','LoginController@login');
    //加密算法路由
    Route::get('encrypt','LoginController@encrypt');
    //表单验证路由
    Route::post('dologin','LoginController@doLogin');
});
    Route::get('noaccess','Admin\LoginController@noaccess');

//将需要完成登录后才能执行的页面，分到一个组(要进行登录后才能操作的界面),middleware中间件作用，就是如果需要操作里面，就需要在中间件里注册过
Route::group(['prefix' => 'admin','namespace'=>'Admin','middleware'=>['isLogin','hasRole']], function() {
    //后台首页路由
    // Route::get('admin/index','Admin\LoginController@index');
    Route::get('index','LoginController@index');
    //欢迎界面路由
    Route::get('welcome','LoginController@welcome');
    //后台退出登录路由
    Route::get('logout','LoginController@logout');

    //后台用户模块相关路由
    Route::get('user/del','UserController@delAll');
    Route::get('user/indexAd','UserController@indexAd');
    Route::get('user/createAd','UserController@createAd');
    Route::resource('user', 'UserController');//资源路由


    //角色模块
    Route::resource('role', 'RoleController');

    //分类路由
    Route::resource('cate','CateController');
    //修改排序路由
    Route::post('cate/changeorder','CateController@changeOrder');

    //文章模块路由
    //上传路由
    Route::post('article/upload','ArticleController@upload');
    Route::resource('article', 'ArticleController');


    //网站配置模块路由
    Route::post('config/changecontent','ConfigController@changeContent');
    Route::get('config/putcontent','ConfigController@putContent');
    Route::resource('config','ConfigController');

});



