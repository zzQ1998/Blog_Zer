<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    {{--  除了检查 POST 参数中的 CSRF 令牌外， VerifyCsrfToken 中间件还会检查 X-CSRF-TOKEN 请求头。你应该将令牌保存在 HTML meta 标签中  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.style')
    @include('admin.public.script')
</head>

<body>
    <div class="x-body">
        <form class="layui-form" id="art_form">
{{--            {{ csrf_field() }}--}}
            {{--  //选择药品分类  --}}
                <div class="layui-form-item">
                    <label class="layui-form-label"> <span class="x-red">*</span>药品类别:</label>
                    <div class="layui-input-inline">
                    <select name="cateid" lay-verify="">
                        @foreach($cates as $k=>$v)
                            @if ($v->cate_pid==0)
                                <option value="{{ $v->cate_id }}" disabled>{{ $v->cate_name }}</option>
                            @else
                                <option value="{{ $v->cate_id }}">{{ $v->cate_name }}</option>
                            @endif

                        @endforeach
                    </select>
                    </div>
                </div>
            {{--  药品id  --}}
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>药品编号:</label>
                <div class="layui-input-inline" style="font-size: 18px;">
                <input type="text" name="uid" required="" value="" class="layui-input">
                </div>
            </div>

            {{--  药品名称  --}}
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>药品名称:</label>
                <div class="layui-input-inline" style="font-size: 18px;">
                {{ csrf_field() }}
                <input type="text" name="mname" required="" value="" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label"><span class="x-red">*</span>生产公司:</label>
                <div class="layui-input-inline">
                    <input type="text" name="company" value="" required="" autocomplete="off" class="layui-input" >
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</label>
                <div class="layui-form-mid"><span style="font-weight: bold;color:red;font-size:16px;">￥</span></div>
                <div class="layui-input-inline">
                    <input type="text" name="price" value="" lay-verify="" autocomplete="off" class="layui-input" style="margin-left:-10px;color:red;font-size:20px;font-weight:bold;border: 0;background-color:rgba(255, 255, 255, 0);">
                </div>
            </div>

            {{--  //上传图片  --}}
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">药品图片:</label>
                <div class="layui-input-block layui-upload">
                    <input type="hidden" id="img1" class="hidden" name="art_thumb" value="">
                    <button type="button" class="layui-btn" id="test1">
                        <i class="layui-icon">&#xe67c;</i>上传图片
                    </button>
                    <input type="file" name="photo" id="photo_upload" style="display: none;" />
                </div>
            </div>

            {{--  //显示缩略图  --}}
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <img src="" alt="" id="art_thumb_img" style="max-width: 350px; max-height:100px;">
                </div>
            </div>

            {{--  //填写描述  --}}
            <div class="layui-form-item">
                <label for="L_art_tag" class="layui-form-label">
                药品介绍:
                </label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" class="layui-textarea" name="describe"></textarea>
                </div>
            </div>

            {{--  //添加按钮  --}}
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    添加新药品
                </button>
            </div>
        </form>
    </div>
</body>
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
    });

    //Markdown AJAX
    $('#previewBtn').click(function(){
        $.ajax({
            url:"/admin/article/pre_mk",
            type:"post",
            data:{
                cont:$('#z-textarea').val()
            },
            success:function(res){
                $('#z-textarea-preview').html(res);
            },
            error:function(err){
                console.log(err.responseText);
            }
        });
    })


</script>
    <script>
        layui.use(['form','layer','upload','element'], function(){
            $ = layui.jquery;
            var form = layui.form
            ,layer = layui.layer;
            var upload = layui.upload;
            var element = layui.element;

            //图片上传的方法，点击上传图片，触发该方法；
            $('#test1').on('click',function () {
                $('#photo_upload').trigger('click');
                $('#photo_upload').on('change',function () {//内容改变触发该方法

                    var mid = $("input[name='uid']").val();
                    //判断药品编号有没有填写
                    if(mid==null){
                        alert("请先填写药品编号!");
                    }else{
                        var obj = this;
                        var formData = new FormData($('#art_form')[0]);//将id为art_form的表单中的数据打包起来，放到formData变量中。
                        $.ajax({
                            url: '/admin/medicines/addimg',
                            type: 'post',
                            data: formData,
                            // 因为data值是FormData对象，不需要对数据做处理
                            processData: false,
                            contentType: false,
                            success: function(data){//接收返回值，并判断
                                if(data['ServerNo']=='200'){
                                    // 如果成功
                                    $('#art_thumb_img').attr('src',data['ResultData']);
                                    $('input[name=art_thumb]').val(data['ResultData']);
                                    $(obj).off('change');
                                }else{
                                    // 如果失败
                                    alert(data['ResultData']);
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                var number = XMLHttpRequest.status;
                                var info = "错误号"+number+"文件上传失败!";
                                alert(info);
                            },
                            async: true
                        });
                    }
                });

        });

        //监听提交
        form.on('submit(add)',function(data) {
            //阻止默认提交，发异步，把数据提交给php
            $.ajax({
                type:'POST',
                url:'/admin/medicines',//请求路由
                dataType:'json',//返回数据格式
                headers: {//这样POST就会传一个headers
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:data.field,//提交的表单数据
                success:function(data){//判断是否成功
                    //弹层提示添加成功，并刷新父页面
                    if(data.status==0){
                        layer.alert(data.message,{icon:6},function(){
                            parent.location.reload(true);//刷新父页面
                        });
                    }else{
                        layer.alert(data.message,{icon:5});
                    }
                },
                error:function(){
                    //提示错误信息
                }
            });
            return false;
        });


        });
    </script>
</html>