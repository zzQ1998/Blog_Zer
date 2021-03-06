<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //返回到权限管理模块页面
        //1、获得数据对象
        $permission = Permission::OrderBy('id','asc')
        ->paginate($request->input('num')!=0?$request->input('num'):6);//每次查询的数据条数

        //2、返回到角色列表页面
        return view('admin.permission.list',compact('permission','request'));

    }

    /**
     * Show the form for creating a new resource.
     *添加页面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //跳转到添加页面
        return view('admin.permission.add');
    }

    /**
     * Store a newly created resource in storage.
     *执行添加操作
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //1、接收前台表单提交的数据
        $input =$request->all();
        //2、进行表单验证，并判断是否已经存在该用户名的账号
        $pername=$input['per_name'];
        $per =Permission::where('per_name',$pername)->first();
        if($per){
            $data = [
                'status' => 1,
                'message' => '该用户名已经存在!'
            ];
            return $data;
        }

        //3、添加到数据库中的permission表
        $pername=$input['per_name'];
        $url=$input['per_url'];
        $desc = $input['desc'];
        $res = Permission::create(['per_name'=>$pername,'per_url'=>$url,'per_describe'=>$desc]);

        //4、根据添加是否成功，给客户端返回一个json格式的反馈
        if($res){//如果权限表数据添加成功

            $data = [
                'status' =>0,
                'message' =>'权限添加成功!'
            ];
        }else{
            $data = [
                'status' => 1,
                'message' => '权限添加失败!'
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $per = Permission::find($id);

        return view('admin.permission.edit',compact('per'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
//        使用模型修改表记录的两种方法,方法一
        $per = Permission::find($id);
        $per->per_name = $input['per_name'];
        $per->per_url = $input['per_url'];
        $res = $per->save();

        if($res){
//            return 1111;
            $data = [
                'status'=>0,
                'msg'=>'修改成功'
            ];
        }else{
//            return 2222;
            $data = [
                'status'=>1,
                'msg'=>'修改失败'
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
        $per = Permission::find($id);
        //2、进行删除对象
        $res = $per->delete();
        //3、进行判断返回数据
        if($res){
            $data = [
                'status'=>0,
                'message'=>'权限删除成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'权限删除失败!'
            ];
        }
        return $data;

    }

        //删除所有选中权限
        public function delAll(Request $request){
            //获取需要批量删除的id数组
            $input =$request->input('ids');
            $res = Permission::destroy($input);//删除数组对应id的数据
            if ($res) {
                $data = [
                    'status'=>0,
                    'message'=>'批量删除成功!'
                ];
            } else {
                $data = [
                    'status'=>1,
                    'message'=>'删除失败!'
                ];
            }
            return $data;
        }
}
