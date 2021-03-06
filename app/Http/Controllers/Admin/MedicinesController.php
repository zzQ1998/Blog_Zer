<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-12-05 14:56:48
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-12-07 14:58:05
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Cate;
use App\Model\Medicines;
use Carbon\Carbon;
use Image;//引用图片组件
use Storage;
use Redis;
use Illuminate\Support\Str;

class MedicinesController extends Controller
{
    private $listkey = 'LIST:MEDICINES';//用来存放需要获取药品的id值
    private $hashkey = 'HASH:MEDICINES';//用来存放药品


    public function selectMeds(Request $request){
        //这里利用Redis缓存机制，如果缓存中已经存了药品列表就从Redis中取出，否则就从数据库中取出。
        //获取药品列表对象
        // $medicines =Medicines::get();
        // dd($medicines);
        //定义一个变量，存放着所有的药品记录；
        $meds =[];
        if (Redis::exists($this->listkey)) {
            //如果Redis中存在要取的数据，就直接返回
            //$lists中存放着所有要获取药品的id
            if(empty($request->input('seachCont'))){
                $lists = Redis::lrange($this->listkey,0,-1);//利用lrange类获取列表
                foreach ($lists as $k => $v) {
                    $meds[] = Redis::hgetall($this->hashkey.$v);//每次取出一篇药品
                }
            }else{
                $lists = Redis::lrange($this->listkey,0,-1);//利用lrange类获取列表
                foreach ($lists as $k => $v) {
                    $arr = Redis::hgetall($this->hashkey.$v);
                    if(Str::contains($arr['medicines_name'],$request->input('seachCont'))||Str::contains($arr['medicines_id'],$request->input('seachCont'))){
                        $meds[] = Redis::hgetall($this->hashkey.$v);//取出符合条件的药品
                    }
                }
            }


        } else {
                //1、如果redis中没有，连接mysql数据库，取出需要的数据
                // $meds = Medicines::get()->toArray();
                $meds = \DB::select('select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id');
                $meds = json_decode(json_encode($meds),true);//先将数据集编码成json字符串，再解码成数组
                //2、存入redis中
                foreach ($meds as $k => $v) {
                    //将药品的id添加到listkey变量中
                    Redis::rpush($this->listkey,$v['id']);
                    //将药品内容添加到hashkey变量中
                    Redis::hmset($this->hashkey.$v['id'],$v);
                }
        }
        return $meds;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $meds = $this->selectMeds($request);
        //3、并将数据返回给客户端
        //所有药品类别结果集
        $allCate = Cate::get();
        //返回一个添加页面
        return view('admin.medicines.list',compact('meds','allCate'));
    }

    public function indexDel(Request $request)
    {
        $meds = $this->selectMeds($request);
        // dd($meds);
        //3、并将数据返回给客户端
        //所有药品类别结果集
        $allCate = Cate::get();

        //返回一个添加页面
        return view('admin.medicines.dellist',compact('meds','allCate'));
    }

    /**
     * Show the form for creating a new resource.
     *返回用户添加页面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //获取所有分类
        $cates = (new Cate)->tree();
        //返回一个添加页面
        return view('admin.medicines.add',compact('cates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //1、接收前台表单提交的数据
        $input =$request->all();
        // dd($input);
        //2、进行表单验证，并判断是否已经存在该用户名的账号
        $mid=$input['uid'];
        $medicines =Medicines::where('medicines_id',$mid)->first();
        if($medicines){
            $data = [
                'status' => 1,
                'message' => '该药品已经存在!请在仓库中直接添加库存。'
            ];
            return $data;
        }
        //3、添加到数据库中的medicines表
        $mname = $input['mname'];
        $company = $input['company'];
        $cate_id = $input['cateid'];
        $price = $input['price'];
        $small_pic = $input['art_thumb'];
        $describe = $input['describe'];

        $res = Medicines::create(['medicines_id'=>$mid,'medicines_name'=>$mname,'company'=>$company,'describe'=>$describe,'cate_id'=>$cate_id,'small_pic'=>$small_pic,'price'=>$price]);

        //4、根据添加是否成功，给客户端返回一个json格式的反馈
        if($res){//如果药品数据数据添加成功
            $meds = \DB::select("select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where medicines_id = '" .$mid."'");
            $meds = json_decode(json_encode($meds), true);
            $meds =$meds[0];
            //将新药品的id添加到listkey变量中
            Redis::rpush($this->listkey,$mid);
            //将新药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$mid,$meds);
            $data = [
                'status' =>0,
                'message' =>'药品信息添加成功!'
            ];
        }else{
            $data = [
                'status' => 1,
                'message' => '药品信息添加失败!'
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
        //跳转到修改页面
        $medicines = Medicines::find($id);
        //获取所有分类
        $cates = (new Cate)->tree();
        return view('admin.medicines.edit',compact('medicines','cates'));
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
        //获取ajax异步传输过来的数据
        $medicines = Medicines::find($id);
        $input =$request->all();
        $meds = \DB::select('select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id ='.$id);
        $meds=$meds[0];

        //对对象进行赋值
        $medicines->medicines_id=$input['medicines_id'];
        $meds->medicines_id=$input['medicines_id'];
        $medicines->medicines_name=$input['mname'];
        $meds->medicines_name=$input['mname'];
        $medicines->company=$input['company'];
        $meds->company=$input['company'];
        $medicines->describe=$input['resume'];
        $meds->describe=$input['resume'];
        $medicines->num=$input['num'];
        $meds->num=$input['num'];
        $medicines->cate_id=$input['cateid'];
        $meds->cate_id=$input['cateid'];
        $medicines->price=$input['price'];
        $meds->price=$input['price'];
        //获得新的类别名称
        $cate = Cate::find($input['cateid']);
        $meds->cate_name = $cate->cate_name;
        $meds->cate_pid = $cate->cate_pid;
        //执行保存
        $res = $medicines->save();
        $meds = json_decode(json_encode($meds), true);//先将数据集编码成json字符串，再解码成数组
        // dd($meds);
        //删除指定hashkey变量
        Redis::del($this->hashkey.$meds['id']);
         //将药品内容添加到hashkey变量中
        Redis::hmset($this->hashkey.$meds['id'],$meds);
        if($res){
            $data = [
                'status'=>0,
                'msg'=>'信息修改成功'
            ];
        }else{
            $data = [
                'status'=>1,
                'msg'=>'信息修改失败'
            ];
        }
        return $data;
    }

    //跳转增加药品库存页面
    public function editNum($id,$num){
        return view('admin.medicines.addNum',compact('id','num'));
    }

    //增加药品库存方法
    public function addNum(Request $request){
        $input = $request->all();
        $id = $input['id'];
        $num = $input['num'];
        $addnum = $input['addnum'];
        $res = \DB::table('pharmacy_medicines')->where('id','=',$id)->update(['num'=>$num+$addnum]);
        //进行判断返回数据
        if($res){
            $meds = \DB::select("select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id = '" .$id."'");
            $meds = json_decode(json_encode($meds), true);
            $meds =$meds[0];
            //删除redis指定的值
            Redis::del($this->hashkey.$id);
            //将药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$id,$meds);

            $data = [
                'status'=>0,
                'message'=>'库存添加成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'库存添加失败!'
            ];
        }
        return $data;
    }

    //药品上架
    public function up($id){
        $res = \DB::table('pharmacy_medicines')->where('id','=',$id)->update(['is_marketable'=>1]);
              //进行判断返回数据
        if($res){
            $meds = \DB::select("select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id = '" .$id."'");
            $meds = json_decode(json_encode($meds), true);
            $meds =$meds[0];
            //删除redis指定的值
            Redis::del($this->hashkey.$id);
            //将药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$id,$meds);

            $data = [
                'status'=>0,
                'message'=>'信息修改成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'信息修改失败!'
            ];
        }
        return $data;
    }

    //药品下架
    public function down($id){
        $res = \DB::table('pharmacy_medicines')->where('id','=',$id)->update(['is_marketable'=>0]);
              //进行判断返回数据
        if($res){
            $meds = \DB::select("select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id = '" .$id."'");
            $meds = json_decode(json_encode($meds), true);
            $meds =$meds[0];
            // dd($meds);
            //删除redis指定的值
            Redis::del($this->hashkey.$id);
            //将药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$id,$meds);

            $data = [
                'status'=>0,
                'message'=>'信息修改成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'信息修改失败!'
            ];
        }
        return $data;
    }

    //删除恢复
    public function recover($id){
        $res = \DB::table('pharmacy_medicines')->where('id','=',$id)->update(['deleted_at'=>0]);
              //进行判断返回数据
        if($res){
            $meds = \DB::select("select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id = '" .$id."'");
            $meds = json_decode(json_encode($meds), true);
            $meds =$meds[0];
            //删除redis指定的值
            Redis::del($this->hashkey.$id);
            //将药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$id,$meds);

            $data = [
                'status'=>0,
                'message'=>'信息修改成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'信息修改失败!'
            ];
        }
        return $data;
    }

    /**
     * 执行删除操作
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //1、根据id获取数据库对象
        $deleted_at = Carbon::now()->getPreciseTimestamp(0);//获得当前时间的10位时间戳
        $res = \DB::table('pharmacy_medicines')->where('id','=',$id)->update(['deleted_at'=>$deleted_at]);
        //进行判断返回数据
        if($res){
            $meds = \DB::select("select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id = '" .$id."'");
            $meds = json_decode(json_encode($meds), true);
            $meds =$meds[0];
            //删除redis指定的值
            Redis::del($this->hashkey.$id);
            //将药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$id,$meds);

            $data = [
                'status'=>0,
                'message'=>'药品删除成功!'
            ];
        }else{
            $data = [
                'status'=>1,
                'message'=>'药品删除失败!'
            ];
        }
        return $data;
    }

    //上传药品图片
    public function updateImg(Request $request){
        //获取上传 文件/图片
        $file =$request->file('photo');
        // dd($request->input('uid'));
        //判断上传 文件/图片 是否成功（是否有效）
        if (!$file->isValid()) {
            return response()->json([
                'ServerNo'=>'400',
                'ResultData'=>'无效的上传 文件/图片 '
            ]);
        }
        //调用上传图片方法
        $cont = $this->uploadImg($file);
        $res = $cont[0];
        $newfileName = $cont[1];

        if($res){
            $medicines = Medicines::find($request->input('uid'));
            $res_img = $medicines->update(['small_pic'=>env('QINIU_DOMAIN').$newfileName]);
            $meds = \DB::select('select id,medicines_id,medicines_name,company,`describe`,num,a.cate_id,cate_name,small_pic,price,sales,is_marketable,cate_pid,deleted_at from pharmacy_medicines a left join pharmacy_category b on a.cate_id=b.cate_id where id ='.$request->input('uid'));
            $meds = json_decode(json_encode($meds), true);
            $meds =  $meds[0];
            //删除redis指定的值
            Redis::del($this->hashkey.$meds['id']);
            //将药品内容添加到hashkey变量中
            Redis::hmset($this->hashkey.$meds['id'],$meds);
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

    //添加药品图片
    public function addImg(Request $request){
        //获取上传 文件/图片
        $file =$request->file('photo');
        // dd($request->input('uid'));
        //判断上传 文件/图片 是否成功（是否有效）
        if (!$file->isValid()) {
            return response()->json([
                'ServerNo'=>'400',
                'ResultData'=>'无效的上传 文件/图片 '
            ]);
        }
        //调用上传图片方法
        $cont = $this->uploadImg($file);
        $res = $cont[0];
        $newfileName = $cont[1];

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
    //上传图片的方法
    public function uploadImg($file){
        $cont = Array();
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
        //2.给图片加文字水印；
        $img->text("@Zer", $imageWidth*0.5, $imageHeight*0.5, function ($font) {
            //设置字体类型（可引入字体库）
            $font->file(base_path()."/public/admin/fonts/summer.ttf");//base_path();得到根目录地址
            //设置字体大小
            $font->size(30);
            //设置字体颜色
            $font->color(array(240, 248,255, 0.5));
            // $font->color("#f0f8ff");
            //设置字体内边距
            $font->align('center');
            $font->valign('center');
            //倾斜角度
            $font->angle(mt_rand(-36,36));
        });
         //3.将文件上传到七牛云存储的指定仓库,并更新数据库
        $res = Storage::disk('qiniu')->put($newfileName, $img->encode());
        $cont[0] = $res;
        $cont[1] = $newfileName;
        return $cont;

    }
}
