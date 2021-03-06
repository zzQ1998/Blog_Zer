<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-Zer</title>
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
            <form class="layui-form " style="margin-left: 15%;margin-top: 5%;">
                <input type="hidden" name="uid" value="{{ $user->user_id }}">
                <input type="hidden" name="limit" value="{{ $user->limit }}">
                <div class="layui-form-item">
                    <label for="L_username" class="layui-form-label">
                            用户名:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_username" name="username" disabled="disabled" value="{{ $user->user_name }}" autocomplete="off" class="layui-input" placeholder="真实姓名"></div>
                </div>
                {{--  //输入真实姓名  --}}
                <div class="layui-form-item">
                    <label for="L_userrname" class="layui-form-label">
                            真实姓名:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_userrname" name="userrname" value="{{ $user->user_rname }}" required="" lay-verify="required|rname" autocomplete="off" class="layui-input" placeholder="真实姓名"></div>
                </div>
                {{--  选择角色  --}}
                {{--  @if ($user->limit==1)  --}}
                <div class="layui-form-item">
                    <label class="layui-form-label"><span class="x-red">*</span>选择角色</label>
                    <div class="layui-input-block">
                        @foreach ($role as $r)
                            @if (in_array($r->id,$own_rols))
                                <input type="checkbox" checked name="role_id[]" lay-skin="primary" value="{{ $r->id }}" title="{{$r->role_name}}">
                            @else
                                <input type="checkbox" name="role_id[]" lay-skin="primary" value="{{ $r->id }}" title="{{$r->role_name}}">
                            @endif
                        @endforeach
                    </div>
                </div>
                {{--  @endif  --}}


                {{--  //输入邮箱  --}}
                <div class="layui-form-item">
                    <label for="L_email" class="layui-form-label">
                            邮箱:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_email" name="email" value="{{ $user->email }}" required="" lay-verify="required|email" autocomplete="off" class="layui-input" placeholder="邮箱"></div>
                </div>
                {{--  //输入手机号码  --}}
                <div class="layui-form-item">
                    <label for="L_userphone" class="layui-form-label">
                            手机号码:</label>
                    <div class="layui-input-inline">
                        <input type="text" id="L_userphone" name="phone" value="{{ $user->phone }}" required="" lay-verify="phone" autocomplete="off" class="layui-input" placeholder="手机号码"></div>
                </div>

                <div class="layui-form-item" style="margin-left: 15%">
                    <label for="L_repass" class="layui-form-label"></label>
                    <button class="layui-btn" lay-filter="edit" lay-submit="">修改</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        layui.use(['form', 'layer'],
            function() {
                $ = layui.jquery;
                var form = layui.form,
                    layer = layui.layer;
                  //自定义验证规则
                form.verify({
                    rname:[/[^0-9]+/g,'请输入正确格式的姓名！'],
                });

                //监听提交
                form.on('submit(edit)',function(data) {
                    var  uid = $("input[name='uid']").val();
                        //阻止默认提交，发异步，把数据提交给php
                        $.ajax({
                            type:'PUT',
                            url:'/admin/user/'+uid+1,//请求路由
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
                                        //parent.layui.table.reload('userlist');//刷新表格
                                        //xadmin.close();
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