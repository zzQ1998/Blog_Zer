<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-16 17:12:20
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-11-18 23:42:20
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //1、用户模型关联数据表
    public $table = 'pharmacy_role';

    //2、关联表的主键
    public $primaryKey = 'id';
    //3、允许被批量操作的字段
    // protected $fillable = [
    //     'user_name','user_pass'
    // ];

    protected $guarded = [];    //这个方法为不允许操作的字段，这里如果为空的话就是指数据库的字段都可以进行操作

    //4、禁用一下模型的时间戳，是否维护crated_at和update_at字段
    public $timestamps =false;

    //添加动态属性，关联权限模型，多对多
    public function permission()
    {
        return $this->belongsToMany('App\Model\Permission', 'pharmacy_role_permission', 'role_id', 'permission_id');
    }

}
