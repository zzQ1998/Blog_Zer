<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-12 13:23:04
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-11-21 14:26:44
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Model\User;
use App\Model\Role;

class UserController extends Controller
{
    /**
     * 获取用列表
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //1、获取提交的请求数据
        // $input =$request->all();//因为是get请求所以不用except('_token');
        // dd($input);

        $user = User::OrderBy('user_id','asc')
            ->where(function($query) use($request){//先进行排序
                $username =$request->input('username');
                $query->where('limit','=', 0);
                if(!empty($username)){//进行模糊查询
                    $query->Where('user_name','like','%'.$username.'%')
                          ->orWhere('user_rname','like','%'.$username.'%');//或语句
                }
            })
            ->paginate($request->input('num')!=0?$request->input('num'):6);//每次查询的数据条数

        // $user = User::get();
        return view('admin.user.list',compact('user','request'));//返回列表页面，并携带user
    }

    public function indexAd(Request $request)
    {
        $user = User::OrderBy('user_id','asc')
            ->where(function($query) use($request){//先进行排序
                $username =$request->input('username');
                $query->where('limit','=', 1);
                if(!empty($username)){//进行模糊查询
                    $query->Where('user_name','like','%'.$username.'%')
                          ->orWhere('user_rname','like','%'.$username.'%');//或语句
                }
            })
            ->paginate($request->input('num')!=0?$request->input('num'):6);//每次查询的数据条数

            $allRole = \DB::select('select * from blog_user_role');//获得 blog_user_role表对象
            // dd($allRole);
            $role = Role::get();
        // $user = User::get();
        return view('admin.user.listad',compact('user','request','allRole','role'));//返回列表页面，并携带user
    }
    /**
     *
     *返回用户添加页面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.add');
    }
    /**
     *
     *返回管理员添加页面
     * @return \Illuminate\Http\Response
     */
    public function createAd()
    {
        $role = Role::get();
        return view('admin.user.addad',compact('role'));
    }
    /**
     *执行添加操作
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //1、接收前台表单提交的数据
        $input =$request->all();
        //2、进行表单验证，并判断是否已经存在该用户名的账号
        $username=$input['username'];
        $user =User::where('user_name',$username)->first();
        if($user){
            $data = [
                'status' => 1,
                'message' => '该用户名已经存在!'
            ];
            return $data;
        }

        //3、添加到数据库中的user表
        // $username=$input['username'];
        $userrname=$input['userrname'];
        $pass=Crypt::encrypt($input['pass']);
        $email = $input['email'];
        $phone = $input['phone'];
        $limit = $input['limit'];
        $res = User::create(['user_name'=>$username,'user_pass'=>$pass,'user_rname'=>$userrname,'email'=>$email,'phone'=>$phone,'limit'=>$limit]);
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
     *显示一条数据
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //显示修改页面
        // return view('admin.user.edit');
    }

    /**
     * 返回到一个修改页面
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sign_E = mb_substr($id,-1);//跳转标记，1为修改页面；2位修改密码页面
        $id=substr($id,0,-1);
        $user =User::find($id);
        $role = Role::get();
        //获取管理员拥有的角色身份
        $own_roles =$user->role;
        // dd($own_roles);
         //获得管理员用户拥有的角色id
        $own_rols=[];
        foreach ($own_roles as $v) {
            $own_rols[] =$v->id;
        }
        if ($sign_E==1) {
            return view('admin.user.edit',compact('user','role','own_rols'));
        } else {
            return view('admin.user.password',compact('user'));
        }


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
        //1、根据id获取要修改的对象
        $sign_E = mb_substr($id,-1);//跳转标记，1为修改页面；2位修改密码页面
        $id=substr($id,0,-1);
        $user = User::find($id);

        //2、获取前端表单里的数据
        // $input = $request->except(['_token']);
        $input =$request->all();
        //根据标识进行分类处理
        if ($sign_E==1) {
            $user->user_rname = $input['userrname'];
            $user->email = $input['email'];
            $user->phone = $input['phone'];
        } else if($sign_E == 3){
            $user->status = $input['status'];
        }else {
            $user->user_pass = Crypt::encrypt($input['newpass']);
        }
        \DB::beginTransaction();
        try{
            //3、进行修改
            $res =$user->save();
            //如果是管理员在bolg_user_role表修改数据
            if($input['limit']==1){
                //先删除当前角色已有的权限
                \DB::table('blog_user_role')->where('user_id',$input['uid'])->delete();
                //再给表添加新授予的权限
                if(!empty($input['role_id'])){
                    foreach ($input['role_id'] as $value) {
                        \DB::table('blog_user_role')->insert(['user_id'=>$input['uid'],'role_id'=>$value]);
                    }
                }
            }
            \DB::commit();
            $a=1;
        }catch(\Exception $e){
            \DB::rollBack();
            $a=0;
        }
        if ($res&&$a=1) {
            $data = [
                'status'=>0,
                'message'=>'信息修改成功!'
            ];
        } else {
            $data = [
                'status'=>1,
                'message'=>'信息修改失败!'
            ];
        }
        return $data;
    }

    /**
     * 执行删除操作.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //1、根据id获取数据库对象
        $user = User::find($id);
        //2、进行删除对象
        $res = $user->delete();
        //3、进行判断返回数据
        if($res){
            $data = [
                'status'=>0,
                'message'=>'删除成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'删除失败!'
            ];
        }
        return $data;
    }

    //删除所有选中用户
    public function delAll(Request $request){
        //获取需要批量删除的id数组
        $input =$request->input('ids');
        $res = User::destroy($input);//删除数组对应id的数据
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
