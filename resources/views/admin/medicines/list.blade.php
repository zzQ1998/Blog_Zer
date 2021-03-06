<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8">
    <title>ZER药店销售管理平台</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
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
        <a href="">药品管理</a>
        <a>
          <cite>药品列表</cite></a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i></a>
    </div>
    <div class="x-body">
      <xblock>
        <div class="layui-row">
          <div class="layui-col-md4">
            <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
            <button class="layui-btn" onclick="xadmin.open('添加药品','{{ url('admin/medicines/create') }}',500,600)"><i class="layui-icon"></i>添加药品</button>
            <button class="layui-btn" onclick="xadmin.open('批量添加','{{ url('admin/medicines/create') }}',500,600)"><i class="layui-icon"></i>批量增加</button>
          </div>
          <div class="layui-col-md8">
            <form class="layui-form layui-col-space6" name="sreach"  id="sreach"  method="get" action="{{ url('admin/medicines') }}">
              <div class="layui-inline layui-show-xs-block">
                  <input type="text" name="seachCont" value="" placeholder="输入药品编号或药品名称" autocomplete="off" class="layui-input">
              </div>
              <div class="layui-inline layui-show-xs-block">
                  <button class="layui-btn" lay-submit="" lay-filter="sreach"><i class="layui-icon">&#xe615;</i></button>
              </div>
            </form>
          </div>


        </div>

      </xblock>
      <div class="layui-card-body layui-table-body layui-table-main">
        <table class="layui-table layui-form" id="test" lay-filter="demo">
          <thead>
            <tr>
              <th style="text-align: center;">
                <input type="checkbox" lay-filter="checkall" name="" lay-skin="primary">
              </th>
              <th style="text-align: center; font-weight:bold;">ID</th>
              <th style="text-align: center; font-weight:bold;">药品编号</th>
              <th style="text-align: center; font-weight:bold;">药品图片</th>
              <th style="text-align: center; font-weight:bold;">药品名称</th>
              <th style="text-align: center; font-weight:bold;">药品类别</th>
              <th style="text-align: center; font-weight:bold;">库存</th>
              <th style="text-align: center; font-weight:bold;">单价</th>
              <th style="text-align: center; font-weight:bold;">状态</th>
              <th style="text-align: center; font-weight:bold;">操作</th>
            </tr>
          </thead>
          <tbody>
          @foreach($meds as $v)
            @if ($v['deleted_at']==0)
            <tr>
              <td style="text-align: center; font-weight:bold;">
              <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id="{{ $v['id'] }}"><i class="layui-icon layui-icon-ok"></i></div>
              </td>
              <td style="text-align: center;">{{ $v['id'] }}</td>
              <td style="text-align: center;">{{ $v['medicines_id'] }}</td>
              <td style="text-align: center;"><img src="{{ $v['small_pic'] }}" width="80px" height="60px"/></td>
              <td style="text-align: center;">{{ $v['medicines_name'] }}</td>
                @foreach ($allCate as $c)
                  @if ($c->cate_id==$v['cate_pid'])
                    <td style="text-align: center;"><span style="font-weight: bold">{{ $c->cate_name.' >>'}}</span></br>{{ $v['cate_name'] }}</td>
                  @endif
                @endforeach
              <td style="text-align: center;">{{ $v['num'] }}</td>
              <td style="text-align: center;">{{ $v['price'] }}</td>
              <td class="td-status" style="text-align: center;">
                  @if($v['is_marketable'] == 0)
                <span class="layui-btn layui-btn-warm layui-btn-mini" title="点击上架" onclick="up({{ $v['id'] }})">未上架</span>
                  @else
                <span class="layui-btn layui-btn-normal layui-btn-mini" title="点击下架" onclick="down({{ $v['id'] }})">已上架</span>
                  @endif

              </td>
              <td class="td-manage" style="text-align: center;">
                <a title="添加库存"  onclick="xadmin.open('添加药品库存','{{ url('admin/medicines/editnum/'.$v['id'].'&'.$v['num']) }}',500,300)" href="javascript:;">
                  <i class="layui-icon" style="font-weight: bold"></i>
                </a>
                <a title="编辑" onclick="xadmin.open('药品信息编辑','{{ url('admin/medicines/'.$v['id'].'/edit') }}',400,600,true)" href="javascript:;">
                  <i class="layui-icon">&#xe642;</i>
                </a>
                <a title="删除" onclick="member_del(this,{{ $v['id'] }})" href="javascript:;">
                  <i class="layui-icon">&#xe640;</i>
                </a>
              </td>
            </tr>
            @endif
          @endforeach
          </tbody>
        </table>
      </div>
        {{--  分页  --}}
        <div class="layui-card-body ">
          <div class="page">
              <div>
                {{--  {!! $user->appends($request->all())->render() !!}  --}}
              </div>
          </div>
        </div>
    </div>
    <script>
      layui.use(['form','layer','laydate'], function(){
        var laydate = layui.laydate;
          var form = layui.form;

        //执行一个laydate实例
        laydate.render({
          elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
          elem: '#end' //指定元素
        });

          //搜索监听提交
        form.on('select(brickType)', function(data){//选择框监听事件
            document.sreach.action ="{{ url('admin/medicines') }}";
            $('#sreach').submit();
        });

      });



      /*药品-删除*/
      function member_del(obj,id){
          layer.confirm('确认要删除吗？',function(index){
              //发异步删除数据
              $.post('/admin/medicines/'+id,{'_method':'delete','_token':"{{csrf_token()}}"},function (data) {
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

      /*药品上架*/
      function up(id){
          layer.confirm('确认要上架吗？',function(index){
            $.post('/admin/medicines/up/'+id,{'_method':'post','_token':"{{csrf_token()}}"},function (data) {
              if(data.status==0){
                layer.msg(data.message,{icon:6,time:1000});
                location.replace(location.href);
              }else{
                  layer.msg(data.message,{icon:5,time:1000});
              }
            });
          });
      }

      /*药品下架*/
      function down(id){
        layer.confirm('确认要下架吗？',function(index){
          $.post('/admin/medicines/down/'+id,{'_method':'post','_token':"{{csrf_token()}}"},function (data) {
            if(data.status==0){
              layer.msg(data.message,{icon:6,time:1000});
              location.replace(location.href);
            }else{
                layer.msg(data.message,{icon:5,time:1000});
            }
          });
        });
    }

    </script>

  </body>

</html>