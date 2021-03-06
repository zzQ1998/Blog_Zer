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
          <cite>删除列表</cite></a>
      </span>
      <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
        <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i></a>
    </div>
    <div class="x-body">
      <xblock>
        <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量恢复</button>
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
            @if ($v['deleted_at']!=0)
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
                  <span class="layui-btn layui-btn-warm  layui-btn-mini" title="点击恢复" onclick="recover({{ $v['id'] }})">已删除</span>
                </td>
                <td class="td-manage" style="text-align: center;">
                  <a title="编辑"  onclick="xadmin.open('药品信息编辑','{{ url('admin/medicines/'.$v['id'].'/edit') }}',400,600,true)" href="javascript:;">
                    <i class="layui-icon">&#xe642;</i>
                  </a>
                </td>
              </tr>
            @endif
          @endforeach
          </tbody>
        </table>
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

          //监听提交
          form.on('submit(search)', function(data){
              console.log(data);
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
              })

          });
      }

      /*删除恢复*/
      function recover(id){
        layer.confirm('确认要恢复吗？',function(index){
          $.post('/admin/medicines/recover/'+id,{'_method':'post','_token':"{{csrf_token()}}"},function (data) {
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