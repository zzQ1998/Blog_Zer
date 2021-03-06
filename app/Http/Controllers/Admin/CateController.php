<?php
/*
 * @Descripttion:
 * @version: 请写项目版本
 * @Author: @周泽钦
 * @Date: 2020-11-22 15:59:39
 * @LastEditors: @周泽钦
 * @LastEditTime: 2020-12-04 16:57:17
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Cate;

class CateController extends Controller
{
    //修改文章排序
    public function changeOrder(Request $request){
        //1、接收前端传回的数据，改变的排序和cate_id
        $input = $request->except('_token');
        //2、通过分类id获取当前分类
        $cate =Cate::find($input['cate_id']);
        //3、修改当前分类的排序值
        $res = $cate->update(['cate_order'=>$input['cate_order']]);
        if ($res) {
            $data = [
                'status'=>0,
                'message'=>'排序修改成功！'
            ];
        } else {
            $data = [
                'status'=>1,
                'message'=>'排序修改失败！'
            ];
        }
        return $data;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //1、获得数据表内容
        // $cates = Cate::get();
        $cates = (new Cate())->tree();
        // dd($cates);
        return view('admin.cate.list',compact('cates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //获取一级类
        $cate =Cate::where('cate_pid',0)->get();
        return view('admin.cate.add',compact('cate'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //1、接收添加的分类数据
        $input =$request->except('_token');
        //2、进行表单验证
        $cate =Cate::where('cate_name',$input['cate_name'])->first();
        if($cate){
            $data = [
                'status' => 1,
                'message' => '该分类已经存在!'
            ];
            return $data;
        }
        //3、添加到数据库中
        $res = Cate::create($input);
        //4、判断是否添加成功
        if($res){
            $data =[
                'status' => 0,
                'message' => '添加成功!',
            ];
        }else{
            $data =[
                'status' => 1,
                'message' => '添加失败!',
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
        //根据id查找类别
        $cate = Cate::find($id);
        // dd($cate->cate_id);
        return view('admin.cate.edit',compact('cate'));
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
