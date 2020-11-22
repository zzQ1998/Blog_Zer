<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>后台登录-Zer</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    {{--  除了检查 POST 参数中的 CSRF 令牌外， VerifyCsrfToken 中间件还会检查 X-CSRF-TOKEN 请求头。你应该将令牌保存在 HTML meta 标签中  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.style')
    @include('admin.public.script')
</head>

<body>
    <div class="x-body">
        <form class="layui-form">
            {{ csrf_field() }}
            <div class="layui-form-item">
                <label for="L_email" class="layui-form-label">
                    <span class="x-red">*</span>父级分类
                </label>
                <div class="layui-input-inline">
                    <select name="cate_pid">
                        <option value="0">==顶级分类==</option>
                        @foreach($cate as $v)
                            <option value="{{ $v->cate_id }}">{{ $v->cate_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="L_username" class="layui-form-label">
                    <span class="x-red">*</span>分类名称
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_username" name="cate_name" required=""
                        autocomplete="off" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_catetitle" class="layui-form-label">
                    <span class="x-red">*</span>分类标题
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_catetitle" name="cate_title" required=""
                        autocomplete="off" class="layui-input" lay-verify="required">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_cate_order" class="layui-form-label">
                    <span class="x-red">*</span>排序
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_cate_order" name="cate_order" required=""
                        autocomplete="off" class="layui-input" lay-verify="required">

                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    增加
                </button>
            </div>
    </form>
    </div>
    <script>
        layui.use(['form','layer'], function(){
            $ = layui.jquery;
            var form = layui.form
            ,layer = layui.layer;


            //监听提交
            form.on('submit(add)', function(data){

                //发异步，把数据提交给php
                $.ajax({
                    type:'POST',
                    url:'/admin/cate',
                    dataType:'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:data.field,
                    success:function(data){
                        // 弹层提示添加成功，并刷新父页面
                        // console.log(data);
                        if(data.status == 0){
                            layer.alert(data.message,{icon:6},function(){
                                parent.location.reload(true);
                            });
                        }else{
                            layer.alert(data.message,{icon:5});
                        }
                    },
                    error:function(){
                        //错误信息
                    }

                });
                return false;
            });
        });
    </script>
    <script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
      })();</script>
  </body>

</html>