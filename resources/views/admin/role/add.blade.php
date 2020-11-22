<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-Zer</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    @include('admin.public.style')
    @include('admin.public.script')
</head>

<body>
    <div class="layui-fluid">
        <div class="layui-row">
            <form class="layui-form layui-form-pane">
                {{ csrf_field() }}
                <div class="layui-form-item">
                    <label for="name" class="layui-form-label">
                        <span class="x-red">*</span>角色名
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" id="name" name="name" required="" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">
                        拥有权限
                    </label>
                    <table class="layui-table layui-input-block">
                        <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" name="like1[write]" lay-skin="primary" lay-filter="father" title="用户管理">
                                </td>
                                <td>
                                    <div class="layui-input-block">
                                        <input name="id[]" lay-skin="primary" type="checkbox" title="用户停用" value="2">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="11" title="用户删除">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="12" title="用户修改">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="13" title="用户改密">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="14" title="用户列表">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="15" title="用户改密">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="16" title="用户列表">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="17" title="用户改密">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="18" title="欢迎界面">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="like2[write]" lay-skin="primary" type="checkbox" title="文章管理" lay-filter="father">
                                </td>
                                <td>
                                    <div class="layui-input-block">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="21" title="文章添加">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="22" title="文章删除">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="23" title="文章修改">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="24" title="文章改密">
                                        <input name="id[]" lay-skin="primary" type="checkbox" value="25" title="文章列表">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label for="desc" class="layui-form-label">
                        描述
                    </label>
                    <div class="layui-input-block">
                        <textarea placeholder="请输入内容" id="desc" name="desc" class="layui-textarea"></textarea>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-left: 45%">
                    <button class="layui-btn" lay-submit="" lay-filter="add">增加</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        layui.use(['form', 'layer'], function() {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;

           //监听提交
            form.on('submit(add)',function(data) {
            //阻止默认提交，发异步，把数据提交给php
            $.ajax({
                type:'POST',
                url:'/admin/role',//请求路由
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
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</body>

</html>