<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-22 15:59:31
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-11-22 23:15:06
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Cate extends Model
{
    //1、用户模型关联数据表
    public $table = 'blog_category';
    //2、关联表的主键
    public $primaryKey = 'cate_id';
    //3、允许被批量操作的字段
    // protected $fillable = [
    //     'user_name','user_pass'
    // ];
    protected $guarded = [];    //这个方法为不允许操作的字段，这里如果为空的话就是指数据库的字段都可以进行操作

    //4、禁用一下模型的时间戳，是否维护crated_at和update_at字段
    public $timestamps =false;

    //格式化分类数据
    public function tree(){
        //获取所有的分类数据
        $cates = $this->orderBy('cate_order','asc')->get();
        // dd($cates);
        //格式化(排序、二级类缩进);
        return $this->getTree($cates);
    }

    public function getTree($category){
        //排序
        //存放最终排完序的分类数据
        $arr = [];
        //先获取一级类
        foreach ($category as $k => $value) {
            //一级类
            if($value->cate_pid==0){
                $arr[] = $value;
                //获取一级类下的二级类
                foreach ($category as $m => $n) {
                    if($value->cate_id==$n->cate_pid){
                        //给分类名称添加缩进
                        $n->cate_name = '|-----'.$n->cate_name;
                        $arr[] = $n;
                    }
                }
            }
        }
        return $arr;
    }
}
