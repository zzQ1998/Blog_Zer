<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-21 15:25:39
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-12-05 16:23:25
 */

namespace App\Http\Middleware;

use Closure;
use App\Model\User;
use App\Model\Role;
use App\Model\Permission;

class HasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //"App\Http\Controllers\Admin\LoginController@index"
        //1、获取当前请求的路由，对应的控制器方法名
        $route = \Route::current()->getActionName();
        $route=substr($route,0,strpos($route,'@'));
        // dd($route);
        //2、获取当前用户的权限组
        $user = User::find(session()->get('user')->user_id);
        //2.1 获取当前用户的角色
        $role = $user->role;
        // dd($role);
        //2.2根据用户拥有的角色，找对应的权限。
        $arr_per_url=[];//存放权限对应的per_url字段值
        foreach ($role as $v) {
            $perms = $v->permission;
            foreach ($perms as $p) {
                // $arr_per_url[] = $p->per_url;
                $arr_per_url[] =substr($p->per_url,0,strpos($p->per_url,'@'));
            }
        }
        //删除重复的权限
        $arr_per_url = array_unique($arr_per_url);
        //判断当前请求的路由对应控制器的方法名，是否在当前用户拥有的权限列表中，也就是是否在$arr_per_url中
        // dd($arr_per_url);
        if(in_array($route,$arr_per_url)){
            return $next($request);
        }else{
            return redirect('noaccess');
        }
    }
}
