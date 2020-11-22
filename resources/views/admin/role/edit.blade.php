<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-Zer</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    {{--  除了检查 POST 参数中的 CSRF 令牌外， VerifyCsrfToken 中间件还会检查 X-CSRF-TOKEN 请求头。你应该将令牌保存在 HTML meta 标签中  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.style')
    @include('admin.public.script')
</head>

<body>
    <div class="layui-fluid">
        <div class="layui-row">
            <form class="layui-form layui-form-pane">
                {{--  {{ csrf_field() }}  --}}
                <input type="hidden" name='role_id' value="{{ $role->id }}">
                <div class="layui-form-item">
                    <label for="name" class="layui-form-label">
                        <span class="x-red">*</span>角色名
                    </label>
                    <div class="layui-input-inline">
                        <input type="text" id="name" name="name" required="" lay-verify="required" autocomplete="off" class="layui-input" value="{{ $role->role_name }}">
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
                                    <input type="checkbox" checked name="like1" disabled="disable" lay-skin="primary" lay-filter="father" title="用户管理">
                                </td>
                                <td>
                                    <div class="layui-input-block">
                                        @foreach ($perms as $p)
                                            @if ($p->sign==1)
                                                @if (in_array($p->id,$own_pers)){{-- in_array($p->id,$own_pers)是指$own_pers数组中与$p->id相匹配的，就是在权限和角色关联表中找到对应的角色 --}}
                                                    <input name="permission_id[]" lay-skin="primary" type="checkbox" checked value="{{ $p->id }}" title="{{ $p->per_name }}">
                                                @else
                                                    <input name="permission_id[]" lay-skin="primary" type="checkbox" value="{{ $p->id }}" title="{{ $p->per_name }}">
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="like2" lay-skin="primary" type="checkbox" disabled="disable" checked title="文章管理"  lay-filter="father">
                                </td>
                                <td>
                                    <div class="layui-input-block">
                                        @foreach ($perms as $p){{-- 遍历角色权限 --}}
                                            @if ($p->sign==2)
                                                @if (in_array($p->id,$own_pers))
                                                    <input name="permission_id[]" lay-skin="primary" type="checkbox" checked value="{{ $p->id }}" title="{{ $p->per_name }}">
                                                @else
                                                    <input name="permission_id[]" lay-skin="primary" type="checkbox" value="{{ $p->id }}" title="{{ $p->per_name }}">
                                                @endif
                                            @endif
                                        @endforeach
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
                    <button class="layui-btn" lay-submit="" lay-filter="add">修改</button>
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
                var rid=$("input[name='role_id']").val();
            //阻止默认提交，发异步，把数据提交给php
                $.ajax({
                    type:'PUT',
                    url:'/admin/role/'+rid,//请求路由
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