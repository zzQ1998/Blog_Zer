<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>后台登录-Zer</title>
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
    @include('admin.public.style')
    @include('admin.public.script')

</head>

<body class="login-bg">

    <div class="login layui-anim layui-anim-up">
        <div class="message" style="font-size: 20px;">Zer-管理登录</div>

        @if ($errors)
            <div class="alert alert-danger">
                <ul>
                    @if (is_object($errors))

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach

                    @else
                        <li>{{ $errors }}</li>
                    @endif

                </ul>
            </div>
        @endif

        <div id="darkbannerwrap"></div>

        <form method="post" class="layui-form" action="{{ url('admin/dologin') }}">
            {{ csrf_field() }}
            <input name="username" placeholder="用户名" type="text" lay-verify="required|username" class="layui-input">
            <hr class="hr15">
            <input name="password" lay-verify="required|pass" placeholder="密码" type="password" class="layui-input">
            <hr class="hr15">
            {{--  验证码  --}}
            <input name="code" style="width: 150px;" type="text" placeholder="验证码" lay-verify="required">
            <a onclick="javascript:re_captcha();" style="margin-left: 1%">
                <img src="{{ URL('/code/captcha/1') }}" id="127ddf0de5a04167a9e427d883690ff6">
            </a>
            <hr class="hr15">
            <input value="登   录" lay-submit lay-filter="login" style="width:100%; font-size:15px" type="submit">
            <hr class="hr20">
        </form>
    </div>


    {{--  <script>
        $(function() {
            layui.use('form', function() {
                var form = layui.form;
                // layer.msg('玩命卖萌中', function(){
                //   //关闭后的操作
                //   });
                //监听提交
                form.on('submit('admin/dologin')', function(data) {
                    layer.msg(JSON.stringify(data.field), function() {
                        //location.href = '/admin/index';
                    });
                    return false;
                });
            });
        });
    </script>  --}}


    <script type="text/javascript">
        //单击更新验证码
        function re_captcha(){
            $url = "{{ URL('/code/captcha') }}";
            $url = $url + "/" + Math.random();
            document.getElementById('127ddf0de5a04167a9e427d883690ff6').src = $url;
        }

        //自定义表单验证，正则表达式
        layui.use('form', function () {
            var form = layui.form;
            form.verify({
                username: function(value, item){ //value：表单的值、item：表单的DOM对象
                    if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                        return '用户名不能有特殊字符';
                    }
                    if(/(^\_)|(\__)|(\_+$)/.test(value)){
                        return '用户名首尾不能出现下划线\'_\'';
                    }
                    {{--  if(/^\d+\d+\d$/.test(value)){
                        return '用户名不能全为数字';
                    }  --}}
                }

                //我们既支持上述函数式的方式，也支持下述数组的形式
                //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
                ,pass: [
                    /^[\S]{6,12}$/
                    ,'密码必须6到12位，且不能出现空格'
                ]
                });
                //当你自定义了类似上面的验证规则后，你只需要把 key 赋值给输入框的 lay-verify 属性即可：
        });
    </script>

    <!-- 底部结束 -->
</body>

</html>