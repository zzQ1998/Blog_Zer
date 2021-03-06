<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>用户添加-Zer</title>
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
        <div class="layui-row">
            <form class="layui-form">
                <input type="hidden" name="limit" value="0">
                {{--  //输入用户名  --}}
                <div class="layui-form-item">
                    <label for="L_username" class="layui-form-label">
                            <span class="x-red">*</span>登录账号:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_username" name="username" required="" lay-verify="required|nikename" autocomplete="off" class="layui-input"></div>
                    <div class="layui-form-mid layui-word-aux">
                        4到11个字符,账号将会成为您唯一的登入名
                    </div>
                    </div>
                {{--  //输入真实姓名  --}}
                <div class="layui-form-item">
                    <label for="L_userrname" class="layui-form-label">
                            <span class="x-red">*</span>真实姓名:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_userrname" name="userrname" required="" lay-verify="required|rname" autocomplete="off" class="layui-input"></div>
                </div>
                {{--  //输入邮箱  --}}
                <div class="layui-form-item">
                    <label for="L_email" class="layui-form-label">
                            <span class="x-red">*</span>邮箱:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_email" name="email" required="" lay-verify="required|email" autocomplete="off" class="layui-input"></div>
                </div>
                {{--  //输入手机号码  --}}
                <div class="layui-form-item">
                    <label for="L_userphone" class="layui-form-label">
                            手机号码:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_userphone" name="phone" required="" lay-verify="phone" autocomplete="off" class="layui-input"></div>
                </div>
                {{--  //密码  --}}
                <div class="layui-form-item">
                    <label for="L_pass" class="layui-form-label">
                            <span class="x-red">*</span>密码:</label>
                    <div class="layui-input-inline">
                        <input type="password" id="L_pass" name="pass" required="" lay-verify="required|pass" autocomplete="off" class="layui-input"></div>
                    <div class="layui-form-mid layui-word-aux">4到16个字符</div>
                </div>
                {{--  //确认密码  --}}
                <div class="layui-form-item">
                    <label for="L_repass" class="layui-form-label">
                            <span class="x-red">*</span>确认密码:</label>
                    <div class="layui-input-inline">
                        <input type="password" id="L_repass" name="repass" required="" lay-verify="required|repass" autocomplete="off" class="layui-input"></div>
                </div>

                <div class="layui-form-item">
                    <label for="L_repass" class="layui-form-label"></label>
                    <button class="layui-btn" lay-filter="add" lay-submit="">添加账号</button></div>
            </form>
        </div>
    </div>
    <script>
        layui.use(['form', 'layer', 'jquery'],
            function() {
                $ = layui.jquery;
                var form = layui.form,
                    layer = layui.layer;

                //自定义验证规则
                form.verify({
                    nikename: function(value) {
                        if (value.length < 4) {
                            return '昵称至少得4个字符啊';
                        }
                    },
                    rname:[/[^0-9]+/g,'请输入正确格式的姓名！'],
                    pass: [/(.+){4,16}$/, '密码必须4到16位'],
                    repass: function(value) {
                        if ($('#L_pass').val() != $('#L_repass').val()) {
                            return '两次密码不一致';
                        }
                    }
                });

                //监听提交
                form.on('submit(add)',function(data) {
                        //阻止默认提交，发异步，把数据提交给php
                        $.ajax({
                            type:'POST',
                            url:'/admin/user',//请求路由
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