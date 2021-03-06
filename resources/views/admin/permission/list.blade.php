<!DOCTYPE html>
<html class="x-admin-sm">
    <head>
        <meta charset="UTF-8">
        <title>欢迎页面-X-admin2.2</title>
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
        <div class="x-nav">
            <span class="layui-breadcrumb">
            <a href="">首页</a>
            <a href="">管理员管理</a>
            <a>
            <cite>权限管理</cite></a>
        </span>
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">
            <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i></a>
        </div>
        <div class="layui-fluid">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">
                            <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
                            <button class="layui-btn" onclick="xadmin.open('添加新权限','{{ url('admin/permission/create') }}',600,400)"><i class="layui-icon"></i>添加新权限</button>
                        </div>
                        <div class="layui-card-body ">
                            <table class="layui-table layui-form">
                                <thead>
                                <tr>
                                    <th style="text-align: center;">
                                        <input type="checkbox" lay-filter="checkall" name="" lay-skin="primary">
                                    </th>
                                    <th style="text-align: center; font-weight:bold;">ID编号</th>
                                    <th style="text-align: center; font-weight:bold;">权限名称</th>
                                    <th style="text-align: center; font-weight:bold;">权限路由</th>
                                    <th style="text-align: center; font-weight:bold;">描述</th>
                                    <th style="text-align: center; font-weight:bold;">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permission as $p)
                                        <tr>
                                            <td style="text-align: center;">
                                                <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id="{{ $p->id }}"><i class="layui-icon layui-icon-ok"></i></div>
                                            </td>
                                            <td style="text-align: center; font-weight:bold;">{{ $p->id }}</td>
                                            <td>{{ $p->per_name }}</td>
                                            <td>{{ $p->per_url }}</td>
                                            <td>{{ $p->per_describe }}</td>
                                            <td style="text-align: center;">
                                            <a title="权限编辑"  onclick="xadmin.open('权限编辑','{{ url('admin/permission/'.$p->id.'/edit') }}',600,400)" href="javascript:;">
                                                <i class="layui-icon">&#xe642;</i>
                                            </a>
                                            <a title="删除" onclick="member_del(this,{{ $p->id }})" href="javascript:;">
                                                <i class="layui-icon">&#xe640;</i>
                                            </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="layui-card-body ">
                            <div class="page">
                                <div>
                                    {!! $permission->appends($request->all())->render() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
<script>
    layui.use(['laydate','form'], function(){
        var laydate = layui.laydate;
        var form = layui.form;
        // 监听全选
        form.on('checkbox(checkall)', function(data) {
            if (data.elem.checked) {
                $('.layui-form-checkbox').addClass('layui-form-checked');
            }else{
                $('.layui-form-checkbox').removeClass('layui-form-checked');
            }
            form.render('checkbox');//重新渲染
        });

        //执行一个laydate实例
        laydate.render({
          elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
          elem: '#end' //指定元素
        });
    });

    /*用户-停用*/
        function member_stop(obj,id){
            layer.confirm('确认要停用吗？',function(index){

                if($(obj).attr('title')=='启用'){

                //发异步把用户状态进行更改
                $(obj).attr('title','停用')
                $(obj).find('i').html('&#xe62f;');

                $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已停用');
                layer.msg('已停用!',{icon: 5,time:1000});

                }else{
                $(obj).attr('title','启用')
                $(obj).find('i').html('&#xe601;');

                $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                layer.msg('已启用!',{icon: 5,time:1000});
            }

        });
    }

    /*用户-删除*/
        function member_del(obj,id){
            layer.confirm('确认要删除吗？',function(index){
             //ajax传输
                $.post('/admin/permission/'+id,{"_method":"delete","_token":"{{ csrf_token() }}"},function(data){
                    if(data.status==0){
                        //发异步删除数据
                        $(obj).parents("tr").remove();
                        layer.msg(data.message,{icon:6,time:1000});
                    }else{
                        layer.msg(data.message,{icon:5,time:1000});
                    }
                });
            });
        }


            //批量删除方法
    function delAll(argument) {
        //获取到要批量删除的用户id，存到ids数组里面
        var ids = [];
        // 获取选中的id
        $("tbody .layui-form-checked").not('.header').each(function(i,v) {
            var u = $(v).attr("data-id");
            ids.push(u);
        });
        {{--  alert(ids);  --}}
        layer.confirm('确认要删除选中的数据吗？', function(index) {
            //ajax 的post传输
            $.get('/admin/permission/del',{'ids':ids},function(data){
                if(data.status==0){
                    //捉到所有被选中的，发异步进行删除
                    layer.msg(data.message, {
                        icon: 6,time:1000
                    });
                    $(".layui-form-checked").not('.header').parents('tr').remove();

                }else{
                    layer.msg(data.message,{icon:5,time:1000});
                }
            });

        });
    }
    $(function () {//单选框点击事件
        $(".layui-form-checkbox").click(function(){
            if($(this).hasClass("layui-form-checked")){
                $(this).removeClass("layui-form-checked");
            }else{
                $(this).addClass("layui-form-checked");
            }

        });
    });
    </script>
    <script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
        })();</script>
</html>