<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-12 00:08:55
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-11-12 00:13:51
 */

namespace App\Http\Middleware;

use Closure;

class IsLogin
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
        //如果用户已经登录，就继续，如果没有登录，就返回登录界面
        if (session()->get('user')) {

            return $next($request);

        } else {

            return redirect('admin/login')->with('errors','请先完成登录，谢谢！');;

        }


    }
}
