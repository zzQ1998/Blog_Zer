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


// //前台路由
// Route::get('index','Home\IndexController@index');//博客首页路由
// Route::get('lists/{id}','Home\IndexController@lists');//文章列表路由
// Route::get('detail/{id}','Home\IndexController@detail');//文章链接路由

// //收藏路由
// Route::post('collect','Home\IndexController@collect');

// //前台登录
// Route::get('login','Home\LoginController@login');
// Route::post('dologin','Home\LoginController@doLogin');
// Route::get('loginout','Home\LoginController@loginOut');

// //邮箱注册激活路由
// Route::get('emailregister','Home\RegisterController@register');
// Route::post('doregister','Home\RegisterController@doRegister');

// Route::get('active','Home\RegisterController@active');//激活
// Route::get('forget','Home\RegisterController@forget');
// Route::post('doforget', 'Home\RegisterController@doforget');
// Route::get('reset','Home\RegisterController@reset');//重新设置密码页面
// Route::post('doreset','Home\RegisterController@doreset');//重新设置密码操作




// //手机注册页路由
// Route::get('phoneregister','Home\RegisterController@phoneReg');
// //发送手机验证码
// Route::get('sendcode','Home\RegisterController@sendCode');
// Route::post('dophoneregister','Home\RegisterController@doPhoneRegister');

Route::get('active','Admin\UserController@active');//邮箱激活


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
    Route::get('user/message','UserController@message');
    Route::post('user/uploadimg','UserController@uploadImg');
    Route::post('user/updatebyuser','UserController@updateByuser');
    Route::resource('user', 'UserController');//资源路由


    //角色模块
    Route::resource('role', 'RoleController');
    //权限模块
    Route::get('permission/del','PermissionController@delAll');//注意要放在对应资源路由的前面
    Route::resource('permission', 'PermissionController');


    //分类路由
    Route::resource('cate','CateController');
    //修改排序路由
    Route::post('cate/changeorder','CateController@changeOrder');

    //药品模块路由
    //上传路由
    Route::post('medicines/upload','MedicinesController@upload');
    Route::post('medicines/uploadimg','MedicinesController@updateImg');
    Route::post('medicines/addimg','MedicinesController@addImg');
    Route::get('medicines/indexDel','MedicinesController@indexDel');
    Route::post('medicines/up/{id}','MedicinesController@up');
    Route::post('medicines/down/{id}','MedicinesController@down');
    Route::post('medicines/recover/{id}','MedicinesController@recover');
    Route::get('medicines/editnum/{id}&{num}','MedicinesController@editNum');
    Route::post('medicines/addnum','MedicinesController@addNum');
    Route::resource('medicines', 'MedicinesController');


    //网站配置模块路由
    Route::post('config/changecontent','ConfigController@changeContent');
    Route::get('config/putcontent','ConfigController@putContent');
    Route::resource('config','ConfigController');

});



