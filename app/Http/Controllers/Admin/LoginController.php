<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-09 15:48:17
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-11-22 00:08:35
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Validator;
use App\Model\User;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    //后台登录页
    public function login()
    {
        return view('admin.login');
    }

    // 验证码生成
    public function captcha($tmp)
    {
        $phrase = new PhraseBuilder;
        // 设置验证码位数
        $code = $phrase->build(4);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        $builder->setBackgroundColor(220, 210, 230);

        $builder->setMaxAngle(25);

        $builder->setMaxBehindLines(0);

        $builder->setMaxFrontLines(0);

        // 可以设置图片宽高及字体
        $builder->build($width = 100, $height = 40, $font = null);
        // 获取验证码的内容
        $phrase = $builder->getPhrase();
        // 把内容存入session
        \Session::flash('code', $phrase);
        // 生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    //处理用户登录的方法
    public function doLogin(Request $request)
    {
        //1、接收表单的数据
        $input =$request->except('_token');
        //2、进行表单验证
        // $validator = Validator::make('需要验证的表单数据','验证规则','错误提示信息');

        $rule = [
            'username'=>'required|between:4,11',//|numeric
            'password'=>'required|between:4,16',//|alpha_dash
        ];
        $msg = [
            'username.required'=>'用户名必须输入',
            'username.between'=>'用户名长度在4到11位之间',
            // 'username.numeric'=>'用户名必须为纯数字',
            'password.required'=>'密码必须输入',
            'password.between'=>'密码长度在4到16位之间',
            // 'password.alpha_dash'=>'密码必须包含字母、数字，下划线',
        ];
        $validator = Validator::make($request->all(),$rule,$msg);

        if ($validator->fails()) {//如果验证失败
            return redirect('admin/login')
                        ->withErrors($validator)
                        ->withInput();
        }

        //3、验证表单内容（是否有此用户，密码是否正确，验证码是否正确）

        //1、判断用户名
        $user =User::where('user_name',$input['username'])->first();
        if(!$user){
            return redirect('admin/login')->with('errors','该用户名不存在');
        }
        //2、判断密码
        if($input['password']!=Crypt::decrypt($user->user_pass)){
            return redirect('admin/login')->with('errors','密码输入不正确');
        }
        //3、判断验证码
        if( strtolower($input['code'])!= strtolower(session()->get('code'))){//strtolower()将全部字符转化为小写，这样就不区分大小写了;
            return redirect('admin/login')->with('errors','验证码不正确');
        }
        //4、保存用户信息到session中
        session()->put('user',$user);
        $a=session()->get('user');
        //dd($a->user_name);
        // dd($a->user_rname);
        //5、跳转到后台
        return redirect('admin/index');

    }
    //加密算法
    public function encrypt()
    {
        //基本用法：
        //1、md5加密，生成一个32位的字符串
        // $str ='sunday'.'123456';//一般用md5加密的时候，会在密码前加一个延值，防止破密；
        // return md5($str);

        //2、哈希加密，生成一个65位的字符串,但同一个字符串，每次进行哈希加密后得到结果是不同的
        // $value = '123456';
        // $hashedValue = Hash::make($str);
        // Hash::check($value, $hashedValue)//$value指的是表单传进来的密码，$hashedValue是指数据库中的密码
        // if(Hash::check($value, $hashedValue)){
        //     return '密码正确'
        // }else {
        //     #return '密码错误';
        // }

        //3、crypt加密，生成一个255位的字符串,但同一个字符串，每次进行哈希加密后得到结果是不同的
        $str = '123456';
        // $crypt_str = Crypt::encrypt($str);
        // return $crypt_str;

        //检验与数据库中的密码是否相同
        //假装$crypr_str为数据库中存的密码，$str为表单输入的密码
        $crypt_str = 'eyJpdiI6InVoV09MTVp4ZkZZRGdtRkVxbGFINHc9PSIsInZhbHVlIjoiMDl3Ym9WRFVZbTY2TnhCOHNkOGE5Zz09IiwibWFjIjoiMjIzMjcxM2M3N2IwNzRjYTJiYjAwOGExNTA3OTBhMDdjZjllYTBhN2E1ZDdlNDUxYTI3OTZiMTVmMjc2NmQxMSJ9';
        if (Crypt::decrypt($crypt_str)) {
            return '密码正确';
        } else {
            return '密码错误';
        }
    }

    //显示后台首页
    public function index(){
        return view('admin.index');
    }

    //显示欢迎界面
    public function welcome(){
        return view('admin.welcome');
    }

    //退出登录方法
    public function logout(){
        //清空session中的用户信息
        session()->flush();
        //跳转到登录界面
        return redirect('admin/login');
    }
    //跳转到没有权限，对应的页面
    public function noaccess(){
        return view('admin.error.noaccess');
    }
}
