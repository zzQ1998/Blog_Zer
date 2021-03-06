<!DOCTYPE html>
<html>

    <head>
    <meta charset="UTF-8">
    <title>药品修改页面</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    {{--  除了检查 POST 参数中的 CSRF 令牌外， VerifyCsrfToken 中间件还会检查 X-CSRF-TOKEN 请求头。你应该将令牌保存在 HTML meta 标签中  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.style')
    @include('admin.public.script')
    </head>
<body>
        <div class="layui-fluid">
            <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card">
                <div class="layui-card-header"></div>
                <div class="layui-card-body layui-row" pad15="" >
                    <div class="layui-col-md4">
                        {{--  图片显示  --}}
                        <form class="layui-form layui-col-md8" lay-filter="" id="art_form1">
                            {{ csrf_field() }}
                            <input type="hidden" name="uid" value="{{$medicines->id}}">
                            <button type="button" id="test1" style="background-color:rgba(255, 255, 255, 0);margin-left: 10%;">
                                <img src="{{ $medicines->small_pic }}" width="250" height="250" id="art_thumb_img" style="box-shadow: 10px 10px 10px rgba(0,0,0,.5);" id="test1"/>
                            </button>
                            <input type="file" name="photo" id="photo_upload" style="display: none;" />
                        </form>
                    </div>

                    <form class="layui-form layui-col-md8" lay-filter="" id="art_form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">药品名称:</label>
                            <div class="layui-input-inline" style="font-size: 18px;">
                            {{ csrf_field() }}
                            <input type="hidden" name="uid" value="{{$medicines->id}}">
                            <input type="text" name="mname" value="{{$medicines->medicines_name}}" class="layui-input" style="border:none;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">药品类别:</label>
                            <div class="layui-input-inline">
                            <select name="cateid" lay-verify="">
                                @foreach($cates as $k=>$v)
                                    @if ($v->cate_pid==0)
                                        <option value="{{ $v->cate_id }}" disabled>{{ $v->cate_name }}</option>
                                    @else
                                        @if ($medicines->cate_id==$v->cate_id)
                                            <option value="{{ $v->cate_id }}" selected >{{ $v->cate_name }}</option>
                                        @else
                                            <option value="{{ $v->cate_id }}">{{ $v->cate_name }}</option>
                                        @endif

                                    @endif

                                @endforeach
                            </select>
                            <div class="layui-unselect layui-form-select"><div class="layui-select-title"><input type="text" placeholder="请选择" value="超级管理员" readonly="" class="layui-input layui-unselect"><i class="layui-edge"></i></div><dl class="layui-anim layui-anim-upbit"><dd lay-value="1" class="layui-this">超级管理员</dd><dd lay-value="2" class=" layui-disabled">普通管理员</dd><dd lay-value="3" class=" layui-disabled">审核员</dd><dd lay-value="4" class=" layui-disabled">编辑人员</dd></dl></div>
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">药品编号:</label>
                            <div class="layui-input-inline">
                            <input type="text" name="medicines_id" value="{{ $medicines->medicines_id }}" lay-verify="" autocomplete="off" readonly="" class="layui-input" style="border: 0">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">生产公司:</label>
                            <div class="layui-input-inline">
                                <input type="text" name="company" value="{{ $medicines->company }}" lay-verify="" autocomplete="off" class="layui-input" style="border: 0">
                            </div>
                        </div>

                        <div class="layui-form-item">
                            <label class="layui-form-label">库&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存:</label>
                            <div class="layui-input-inline">
                                <input type="text" name="num" value="{{ $medicines->num }}" lay-verify="" autocomplete="off" class="layui-input"style="border: 0;">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">单&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;价:</label>
                            <div class="layui-form-mid"><span style="font-weight: bold;color:red;font-size:16px;">￥</span></div>
                            <div class="layui-input-inline">
                                <input type="text" name="price" value="{{$medicines->price}}" lay-verify="" autocomplete="off" class="layui-input" style="margin-left:-10px;color:red;font-size:20px;font-weight:bold;border: 0;">
                            </div>
                        </div>
                        <div class="layui-form-item layui-form-text">
                            <label class="layui-form-label">药品介绍:</label>
                            <div class="layui-input-block">
                            <textarea name="resume" placeholder="请输入内容" class="layui-textarea">{{$medicines->describe}}</textarea>
                            </div>
                        </div>
                        <div class="layui-form-item" style="margin-left: -20%;">
                            <div class="layui-input-block">
                            <button class="layui-btn" lay-submit="" lay-filter="edit">确认修改</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
                            </div>
                        </div>
                    </form>

                </div>
                </div>
            </div>
            </div>
        </div>

        <script>
        layui.use(['form','layer','upload'], function(){
            $ = layui.jquery;
            var form = layui.form
            ,layer = layui.layer;
            var upload = layui.upload;

            //头像上传的方法，点击上传图片，触发该方法；
            $('#test1').on('click',function () {
                var  uid = $("input[name='uid']").val();
                $('#photo_upload').trigger('click');
                $('#photo_upload').on('change',function () {//内容改变触发该方法
                    var obj = this;
                    var formData = new FormData($('#art_form1')[0]);//将id为art_form的表单中的数据打包起来，放到formData变量中。
                    $.ajax({
                        url: '/admin/medicines/uploadimg',
                        type: 'post',
                        data: formData,
                        // 因为data值是FormData对象，不需要对数据做处理
                        processData: false,
                        contentType: false,
                        success: function(data){//接收返回值，并判断
                            if(data['ServerNo']=='200'){
                                // 如果成功'input[name=art_thumb]'
                                $('#art_thumb_img').attr('src', data['ResultData']);
                                $('input[name=avatar]').attr('value', data['ResultData']);
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
                });
                });

            //监听提交修改
            form.on('submit(edit)',function(data) {
                var  uid = $("input[name='uid']").val();
                    //阻止默认提交，发异步，把数据提交给php
                    $.ajax({
                        type:'PUT',
                        url:'/admin/medicines/'+uid,//请求路由,这里可能加id就用PUT，不加就用POST
                        dataType:'json',//返回数据格式
                        headers: {//这样POST就会传一个headers
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:data.field,//提交的表单数据
                        success:function(data){//判断是否成功
                            //弹层提示添加成功，并刷新父页面
                            if(data.status==0){
                                layer.alert(data.msg,{icon:6},function(){
                                // {{--  var index = parent.layer.getFrameIndex(window.name);
                                    //parent.layui.table.reload('#test');//重载父页表格，参数为表格ID
                                    //parent.layer.close(index);  --}}
                                    parent.location.reload(true);//刷新页面

                                });
                            }else{
                                layer.alert(data.msg,{icon:5});
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

</body>

</html>