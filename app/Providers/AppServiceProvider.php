<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Model\Cate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //获取所有的分类
        $cate = Cate::get();
        //声明一级分类的变量
        $cateOne = [];
        //声明二级分类的变量
        $cateTwo = [];

        foreach ($cate as $k => $v) {

            //取出所有的一级类，存放到cateOne
            if($v->cate_pid == 0){
                $cateOne[$k] = $v;
                //获取当前一级类下的二级类
                foreach ($cate as $m => $n) {
                    if($v->cate_id == $n->cate_pid){
                        $cateTwo[$k][$m] = $n;
                    }
                }
            }
        }
        //利用share方法，将变量值传到视图中
        view()->share('cateone',$cateOne);
        view()->share('catetwo',$cateTwo);

    }
}
