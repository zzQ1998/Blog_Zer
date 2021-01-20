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
use App\Model\Article;
use Image;//引用图片组件
use Storage;
use Redis;

class ArticleController extends Controller
{

    //文章上传
    public function upload(Request $request){
        //获取上传 文件/图片
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

        //2.给图片加文字水印；
        $img->text("@Blog_zer", $imageWidth*0.5, $imageHeight*0.5, function ($font) {
            //设置字体类型（可引入字体库）
            $font->file(base_path()."/public/admin/fonts/summer.ttf");//base_path();得到根目录地址
            //设置字体大小
            $font->size(50);
            //设置字体颜色
            $font->color(array(240, 248,255, 0.5));
            // $font->color("#f0f8ff");
            //设置字体内边距
            $font->align('center');
            $font->valign('center');
            //倾斜角度
            $font->angle(mt_rand(-36,36));
        });

        //3.1上传图片并返回一个值
        // $res =$img->save($path.'/'.$newfileName);

        //(常规办法)将 文件/图片 从临时 文件/图片 目录 移动 到指定目录
        // $res = $file->move($path,$newfileName);

        //3.2将文件上传到OSS(阿里云)的指定仓库
        // $osskey:文件上传到SSO仓库后的新文件名
        // $filePath:要上传的文件资源
        // $res = OSS::upload($file,$file->getRealPath());

        //3.3将文件上传到七牛云存储的指定仓库
        // Storage::disk('qiniu');
        // $res = Storage::disk('qiniu')->writeStream($newfileName,fopen($img->getRealPath(),'r'));
        $res = Storage::disk('qiniu')->put($newfileName, $img->encode());


        //判断 文件/图片 是否上传成功，如果成功则返回“ 文件/图片 上传成功”
        if (!$res) {
            return response()->json([
                'ServerNo'=>'400',
                'ResultData'=>'文件上传失败！'
            ]);
        } else {
            return response()->json([
                'ServerNo'=>'200',
                'ResultData'=>$newfileName//返回图片保存路径
            ]);
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {//这里利用Redis缓存机制，如果缓存中已经存了文章列表就从Redis中取出，否则就从数据库中取出。
        //获取文章列表对象
        // $article =Article::get();

        //定义一个变量，存放着所有的文章记录；
        $arts =[];

        $listkey = 'LIST:ARTICLE';//用来存放需要获取文章的id值
        $hashkey = 'HASH:ARTICLE';//用来存放文章

        if (Redis::exists($listkey)) {
            //如果Redis中存在要取的数据，就直接返回
            //$lists中存放着所有要获取文章的id
            $lists = Redis::lrange($listkey,0,-1);//利用lrange类获取列表

            foreach ($lists as $k => $v) {
                $arts[] = Redis::hgetall($hashkey.$v);//每次取出一篇文章
            }

        } else {
            //1、如果redis中没有，连接mysql数据库，取出需要的数据
            $arts = Article::get()->toArray();
            //2、存入redis中
            foreach ($arts as $k => $v) {
                //将文章的id添加到listkey变量中
                Redis::rpush($listkey,$v['art_id']);
                //将文章添加到hashkey变量中
                Redis::hmset($hashkey.$v['art_id'],$v);
            }
            //3、并将数据返回给客户端
        }



        //返回一个添加页面
        return view('admin.article.list',compact('arts'));
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
        return view('admin.article.add',compact('cates')
    );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
