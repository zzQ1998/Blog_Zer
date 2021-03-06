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
use Mail;
use Image;//引用图片组件
use Storage;
use Redis;

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

            $allRole = \DB::select('select * from pharmacy_user_role');//获得 blog_user_role表对象
            // dd($allRole);
            $role = Role::get();
        // $user = User::get();
        // dd($request->all());
        return view('admin.user.list',compact('user','request','allRole','role'));//返回列表页面，并携带user
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

            $allRole = \DB::select('select * from pharmacy_user_role');//获得 blog_user_role表对象
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
        $token = md5($input['email'].$input['pass'].'123');
        $expire = time()+3600*24;//如果这封邮件一天以内没有被激活，就失效了
        $phone = $input['phone'];
        $limit = $input['limit'];
        $res = User::create(['user_name'=>$username,'user_pass'=>$pass,'user_rname'=>$userrname,'email'=>$email,'phone'=>$phone,'limit'=>$limit,'token'=>$token,'expire'=>$expire]);

        //4、根据添加是否成功，给客户端返回一个json格式的反馈
        if($res){//如果用户数据添加成功
            Mail::send('email.active', ['user'=>$res], function ($m) use ($res) {
                $m->to($res->email, $res->user_rname)->subject('账号激活邮箱');//这是一个邮件发送给谁的方法
            });

            $data = [
                'status' =>0,
                'message' =>'员工账号添加成功!请通知员工在邮箱内激活账号。'
            ];
        }else{
            $data = [
                'status' => 1,
                'message' => '员工账号添加失败!'
            ];
        }
        return $data;
    }

    //添加员工账号邮箱激活
    public function active(Request $request){
        //找到要激活的用户，将用户的active字段改成1

        $user = User::findOrFail($request->userid);

        //验证token的有效性，保证链接是通过邮箱中的激活链接发送的
        if($request->token  != $user->token){
            return '当前链接非有效链接，请确保您是通过邮箱的激活链接来激活的';
        }
        //激活时间是否已经超时
        if(time() > $user->expire){
            return '激活链接已经超时，请重新注册';
        }

        $res = $user->update(['active'=>1]);
        //激活成功，跳转到登录页
        if($res){
            return redirect('admin/login')->with('errors','账号激活成功，可以进行登录啦!');
        }else{
            return '邮箱激活失败，请检查激活链接，或者联系管理员';
        }
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
        \DB::beginTransaction();//事务开启
        try{
            //3、进行修改
            $res =$user->save();
            //如果是管理员在pharmacy_user_role表修改数据
            if($sign_E==1){
                if ($user->status==1) {
                    //先删除当前角色已有的权限
                    \DB::table('pharmacy_user_role')->where('user_id', $input['uid'])->delete();
                    //再给表添加新授予的权限
                    if (!empty($input['role_id'])) {
                        foreach ($input['role_id'] as $value) {
                            \DB::table('pharmacy_user_role')->insert(['user_id'=>$input['uid'],'role_id'=>$value]);
                        }
                    }
                }else{
                    throw new \Exception("2");
                }
            }
            \DB::commit();
            $a=1;
        }catch(\Exception $e){
            \DB::rollBack();
            // return $e->getMessage();
            if($e->getMessage()==2){
                $a=2;
            }else{
                $a=0;
            }

        }
        if ($res&&$a==1) {
            $data = [
                'status'=>0,
                'message'=>'信息修改成功!'
            ];
        }else {
            if($a==2){
                $data = [
                    'status'=>1,
                    'message'=>'请将账号状态改为启用!'
                ];
            }else{
                $data = [
                    'status'=>1,
                    'message'=>'信息修改失败!'
                ];
            }

        }
        return $data;
    }

    public function updateByuser(Request $request)
    {
        //1、根据id获取要修改的对象
        $user = User::find($request->input('uid'));
        // dd($user);
        //2、获取前端表单里的数据
        $input =$request->all();
        //根据标识进行分类处理
        $user->user_rname = $input['userrname'];
        $user->email = $input['email'];
        $user->phone = $input['phone'];
        $user->resume = $input['resume'];
         //3、进行修改
        $res =$user->save();
        //4、返回信息
        if ($res) {
            $data = [
                'status'=>0,
                'message'=>'个人信息修改成功!'
            ];
        }else {
                $data = [
                    'status'=>1,
                    'message'=>'个人信息修改失败!'
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

    //跳转到个人信息界面
    public function message(){
        $user_id = session('user')->user_id;
        $user = User::find($user_id);
        $user_Message = $user->toArray();//将$user集合转成数组
        $res = array();
        //将需要计算进度条字段，重新放到一个新数组中
        $res[0] = $user_Message['user_name'];
        $res[1] = $user_Message['user_rname'];
        $res[2] = $user_Message['img'];
        $res[3] = $user_Message['phone'];
        $res[4] = $user_Message['email'];
        $res[5] = $user_Message['user_pass'];
        $res[6] = $user_Message['resume'];
        //将过滤为空后数组个数除以原数组个数*100，在向上取整；
        $num = ceil(count(array_filter($res))/count($res)*100);

        return view('admin.user.message',compact('user','num'));
    }

    //修改头像操作
    public function uploadImg(Request $request){
        // dd($request->input('uid'));
        // dd($request->all());
        //获取上传的图片
        $file =$request->file('photo');
        // return $file;
        //判断上传 文件/图片 是否成功（是否有效）
        if (!$file->isValid()) {
            return response()->json([
                'ServerNo'=>'400',
                'ResultData'=>'无效的上传 文件/图片 '
            ]);
        }
        //获取原 文件/图片 的拓展名
        $ext = $file->getClientOriginalExtension();// 文件/图片 拓展名
        //重写新 文件/图片 名
        $newfileName = md5(time().rand(1000,9999)).'.'.$ext;

        // 文件/图片 上传的指定路径
        $path =public_path('uploads');

        //1.获取图片并设置图像大小(这里控制图像为原图的30%)；
        $img = Image::make($file);
        $imageWidth = $img->width()*0.3;
        $imageHeight = $img->height()*0.3;
        $img->resize($imageWidth, $imageHeight);

        //2.将文件上传到七牛云存储的指定仓库,并更新数据库(问：这里可以用事务吗)
        $res = Storage::disk('qiniu')->put($newfileName, $img->encode());
        if($res){
            $user = User::find($request->input('uid'));
            $res_img = $user->update(['img'=>env('QINIU_DOMAIN').$newfileName]);
        }

        //判断 文件/图片 是否上传成功，如果成功则返回“ 文件/图片 上传成功”
        if (!$res) {
            return response()->json([
                'ServerNo'=>'400',
                'ResultData'=>'文件上传失败！'
            ]);
        } else {
            return response()->json([
                'ServerNo'=>'200',
                'ResultData'=>env('QINIU_DOMAIN').$newfileName//返回图片保存路径
            ]);
        }

    }
}
