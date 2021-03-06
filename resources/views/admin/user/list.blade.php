<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>后台用户列表-Zer</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    {{--  除了检查 POST 参数中的 CSRF 令牌外， VerifyCsrfToken 中间件还会检查 X-CSRF-TOKEN 请求头。你应该将令牌保存在 HTML meta 标签中  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.style')
    @include('admin.public.script')

    <style>
        .user{
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="x-nav">
        <span class="layui-breadcrumb">
            <a href="">首页</a>
            <a href="">演示</a>
            <a>
            <cite>导航元素</cite></a>
        </span>
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">
            <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i></a>
    </div>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md12">
                <div class="layui-card" >
                    <div class="layui-card-body ">
                        <form class="layui-form layui-col-space6" name="sreach"  id="sreach" style="margin-left: 19%" method="get" action="{{ url('admin/user') }}">
                            {{--  选择表单  --}}
                            <div class="layui-inline layui-show-xs-block layui-form-item" style="margin-top: 2.5%">
                                <div class="layui-input-block">
                                    <select name="num" lay-verify="required" lay-filter="brickType">
                                    <option value="0" @if ($request->input('num')==0) selected @endif>请选择每页条数</option>
                                    <option value="3" @if ($request->input('num')==3) selected @endif>3</option>
                                    <option value="6" @if ($request->input('num')==6) selected @endif>6</option>
                                    <option value="8" @if ($request->input('num')==8) selected @endif>8</option>
                                    </select>
                                </div>
                            </div>

                            {{--  日期表单<div class="layui-inline layui-show-xs-block">
                                <input class="layui-input" autocomplete="off" placeholder="开始日" name="start" id="start">
                            </div>
                            <div class="layui-inline layui-show-xs-block">
                                <input class="layui-input" autocomplete="off" placeholder="截止日" name="end" id="end">
                            </div>  --}}


                            <div class="layui-inline layui-show-xs-block">
                                <input type="text" name="username" value="{{ $request->input('username') }}" placeholder="用户名或者真实姓名" autocomplete="off" class="layui-input">
                            </div>
                            <div class="layui-inline layui-show-xs-block">
                                <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
                            </div>
                        </form>
                    </div>
                    <div class="layui-card-header">
                        <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
                        <button class="layui-btn" onclick="xadmin.open('添加用户','{{ url('admin/user/create') }}',600,400)"><i class="layui-icon"></i>添加</button>
                    </div>
                    <div class="layui-card-body layui-table-body layui-table-main">
                        <table class="layui-table layui-form">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">
                                        <input type="checkbox" lay-filter="checkall" name="" lay-skin="primary">
                                    </th>
                                    <th style="text-align: center; font-weight:bold;">编号</th>
                                    <th style="text-align: center; font-weight:bold;">用户名</th>
                                    <th style="text-align: center; font-weight:bold;">真实姓名</th>
                                    {{--  <th style="text-align: center; font-weight:bold;">密码</th>  --}}
                                    <th style="text-align: center; font-weight:bold;">邮箱</th>
                                    <th style="text-align: center; font-weight:bold;">角色</th>
                                    <th style="text-align: center; font-weight:bold;">账号激活</th>
                                    <th style="text-align: center; font-weight:bold;">账号状态</th>
                                    <th style="text-align: center; font-weight:bold;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{--表格遍历开始    --}}
                                @foreach ($user as $u)
                                <tr class="user">
                                    <td>
                                        {{--  <input type="checkbox" name="id" value="1" lay-skin="primary" data-id="{{ $u->user_id }}">  --}}
                                        <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id="{{ $u->user_id }}"><i class="layui-icon layui-icon-ok"></i></div>
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $u->user_name }}</td>
                                    <td>{{ $u->user_rname }}</td>
                                    {{--  <td title="{{ $u->user_pass }}">{{ str_limit($u->user_pass,18,'......') }}</td>  --}}
                                    <td>{{ $u->email }}</td>
                                    <td style="font-weight: bold">
                                        @foreach ($allRole as $ar)
                                            @if ($ar->user_id==$u->user_id)
                                                @foreach ($role as $a)
                                                    @if ($ar->role_id==$a->id)
                                                        {{ '"'.$a->role_name.'"' }}
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="td-status">
                                        @if ($u->active==1)
                                            <span class="layui-btn layui-btn-normal layui-btn-mini">已激活</span>
                                        @else
                                            <span class="layui-btn layui-btn-warm layui-btn-mini">未激活</span>
                                        @endif
                                    </td>
                                    <td class="td-status">
                                        @if ($u->status==1)
                                            <span class="layui-btn layui-btn-normal layui-btn-mini">已启用</span>
                                        @else
                                            <span class="layui-btn layui-btn-warm layui-btn-mini">已停用</span>
                                        @endif
                                    </td>
                                    <td class="td-manage">
                                        @if ($u->status==1)
                                        <a onclick="member_stop(this,'{{ $u->user_id }}','{{ $u->status }}')" href="javascript:;" title="禁用">
                                            <i class="layui-icon">&#x1006;</i>
                                        </a>
                                        @else
                                        <a onclick="member_stop(this,{{ $u->user_id }},{{ $u->status }})" href="javascript:;" title="启用">
                                            <i class="layui-icon">&#xe605;</i>
                                        </a>
                                        @endif

                                        <a title="编辑/授权" onclick="xadmin.open('编辑/授权','{{ url('admin/user/'.$u->user_id.'1/edit') }}',600,400)" href="javascript:;">
                                            <i class="layui-icon">&#xe642;</i>
                                        </a>
                                        <a onclick="xadmin.open('修改密码','{{ url('admin/user/'.$u->user_id.'2/edit') }}',600,400)" title="修改密码" href="javascript:;">
                                            <i class="layui-icon">&#xe631;</i>
                                        </a>
                                        <a title="删除" onclick="member_del(this,{{ $u->user_id }})" href="javascript:;">
                                            <i class="layui-icon">&#xe640;</i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            {{--表格遍历结束    --}}
                            </tbody>
                        </table>
                    </div>
                    {{--  分页  --}}
                    <div class="layui-card-body ">
                        <div class="page">
                            <div>
                                {!! $user->appends($request->all())->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




</body>
<script>
    layui.use(['form'], function() {
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

        form.on('select(brickType)', function(data){//选择框监听事件
            document.sreach.action ="{{ url('admin/user') }}";
            $('#sreach').submit();
        });

        {{--  //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });  --}}


    });

    /*用户-停用*/
    function member_stop(obj, uid, ustatus) {
        var s;
        if(ustatus == 1){
            s = 0
        }else(
            s = 1
        )
        layer.confirm('确认要修改吗？','zhanghao', function(index) {
            $.ajax({
                type:'PUT',
                url:'/admin/user/'+uid+3,//请求路由
                dataType:'json',//返回数据格式
                headers: {//这样POST就会传一个headers
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{
                    "status":s,
                },
                success:function(data){//判断是否成功
                    //弹层提示添加成功，并刷新父页面
                    if(data.status==0){
                        layer.alert(data.message,{icon:6},function(){
                            location.reload(true);//刷新本页面

                        });
                    }else{
                        layer.alert(data.message,{icon:5});
                    }
                },
                error:function(){
                    //提示错误信息
                }
            });
        });
    }

    /*用户-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？', function(index) {
            //ajax传输
            $.post('/admin/user/'+id,{"_method":"delete","_token":"{{ csrf_token() }}"},function(data){
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
        //获取到要批量删除的用户id
        var ids = [];

        // 获取选中的id
        $("tbody .layui-form-checked").not('.header').each(function(i,v) {
            var u = $(v).attr("data-id");
            ids.push(u);
        });
        layer.confirm('确认要删除选中的数据吗？', function(index) {
            //ajax 的post传输
            $.get('/admin/user/del',{'ids':ids},function(data){
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

</html>