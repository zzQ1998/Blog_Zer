<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-16 21:30:37
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-11-19 16:08:34
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Model\Role;
use App\Model\Permission;
use Carbon\Carbon;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //1、获得数据对象
        // $role= Role::get();
        $role = Role::OrderBy('id','asc')
        ->paginate($request->input('num')!=0?$request->input('num'):6);//每次查询的数据条数

        $allPermission = \DB::select('select * from pharmacy_role_permission');
        $permission = Permission::get();
        //2、返回到角色列表页面
        return view('admin.role.list',compact('role','request','allPermission','permission'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //显示添加页面
        return view('admin.role.add');
    }

    /**
     * 执行添加操作.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input =$request->except('_token');
        //1、接收前台表单提交的数据
        $input =$request->except('_token');
        //2、进行表单验证，并判断是否已经存在该用户名的账号
        $role_name=$input['name'];
        $role =Role::where('role_name',$role_name)->first();
        if($role){
            $data = [
                'status' => 1,
                'message' => '该角色名已经存在!'
            ];
            return $data;
        }

        //3、添加到数据库中的user表
        $created_at = Carbon::now()->getPreciseTimestamp(0);//获得当前时间的10位时间戳
        // $a= date('Y-m-d H:i:s',$created_at);//时间戳转日期
        $res = Role::create(['role_name'=>$role_name,'created_at'=>$created_at]);
        //4、根据添加是否成功，给客户端返回一个json格式的反馈
        if($res){
            $data = [
                'status' =>0,
                'message' =>'添加成功!'
            ];
        }else{
            $data = [
                'status' => 1,
                'message' => '添加失败!'
            ];
        }
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     *跳转到编辑页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //1、根据id找到数据库中的对象
        $role = Role::find($id);
        //获取所有权限列表
        $perms = Permission::get();
        //获得当前角色拥有的权限
        $own_perms =$role->permission;
        // dd($own_perms);
        //获得角色拥有的权限id
        $own_pers=[];
        foreach ($own_perms as $v) {
            $own_pers[] = $v->id;
        }
        //2、将数据一起携带着跳转到编辑页面
        return view('admin.role.edit',compact('role','perms','own_pers'));
    }

    /**
     * 执行修改操作
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
            $input = $request->all();
            // dd( $input);
            \DB::beginTransaction();//事务开启
        try{
            //先删除当前角色已有的权限
            \DB::table('pharmacy_role_permission')->where('role_id',$input['role_id'])->delete();
            //再给表添加新授予的权限

            if(!empty($input['permission_id'])){
                foreach ($input['permission_id'] as $value) {
                    \DB::table('pharmacy_role_permission')->insert(['role_id'=>$input['role_id'],'permission_id'=>$value]);
                }
            }
            $role = Role::find($input['role_id']);
            $role->update(['role_name'=>$input['name'],'role_describe'=>$input['desc']]);
            \DB::commit();
            $a=0;
        }catch(\Exception $e){
            \DB::rollBack();
            $a=1;
        }
            if($a==1){
                $data = [
                    'status'=>1,
                    'message'=>'信息修改失败!'
                ];
            }else{
                $data = [
                    'status'=>0,
                    'message'=>'信息修改成功!'
                ];
            }
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //1、根据id获取数据库对象
        $role = Role::find($id);
        //2、进行删除对象
        $res = $role->delete();
        //3、进行判断返回数据
        if($res){
            $data = [
                'status'=>0,
                'message'=>'角色删除成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'角色删除失败!'
            ];
        }
        return $data;
    }
}
