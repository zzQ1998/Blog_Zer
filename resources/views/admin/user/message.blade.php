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
            <a href="">设置</a>
            <a><cite>个人信息</cite></a>
        </span>
        <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" onclick="location.reload()" title="刷新">
          <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i></a>
    </div>
    <div class="layui-fluid">
        <div class="layui-row layui-col-space15">
          <div class="layui-col-md12">
            <div class="layui-card">
              <div class="layui-card-header">设置我的资料</div>
              {{--  进度条  --}}
              <div class="layui-progress layui-progress-big" lay-showPercent="yes">
                <div class="layui-progress-bar layui-bg-green" lay-percent="{{ $num }}%"></div>
              </div>
              <div class="layui-card-body layui-row " pad15="" >

                <form class="layui-form layui-col-md6" lay-filter="" id="art_form" >
                  <div class="layui-form-item">
                    <label class="layui-form-label">我的角色</label>
                    <div class="layui-input-inline">
                      <select name="role" lay-verify="">
                        @if ($user->limit==1)
                        <option value="1" selected="">管理员身份</option>
                        <option value="2" disabled="">员工身份</option>
                        @else
                        <option value="1" disabled="">管理员身份</option>
                        <option value="2" selected="">员工身份</option>
                        @endif

                      </select>
                      <div class="layui-unselect layui-form-select"><div class="layui-select-title"><input type="text" placeholder="请选择" value="超级管理员" readonly="" class="layui-input layui-unselect"><i class="layui-edge"></i></div><dl class="layui-anim layui-anim-upbit"><dd lay-value="1" class="layui-this">超级管理员</dd><dd lay-value="2" class=" layui-disabled">普通管理员</dd><dd lay-value="3" class=" layui-disabled">审核员</dd><dd lay-value="4" class=" layui-disabled">编辑人员</dd></dl></div>
                    </div>
                    <div class="layui-form-mid layui-word-aux">当前角色不可更改为其它角色</div>
                  </div>
                  <div class="layui-form-item">
                    <label class="layui-form-label">用户名</label>
                    <div class="layui-input-inline">
                      {{ csrf_field() }}
                      <input type="hidden" name="uid" value="{{ $user->user_id }}">
                      <input type="text" name="username" value="{{$user->user_name}}" readonly="" class="layui-input">
                    </div>
                    <div class="layui-form-mid layui-word-aux">不可修改。一般用于后台登入名</div>
                  </div>
                  <div class="layui-form-item">
                    <label class="layui-form-label">真实姓名</label>
                    <div class="layui-input-inline">
                      <input type="text" name="userrname" value="{{$user->user_rname}}" lay-verify="nickname" autocomplete="off" placeholder="请输入昵称" class="layui-input">
                    </div>
                  </div>
                  {{--  <div class="layui-form-item">
                    <label class="layui-form-label">性别</label>
                    <div class="layui-input-block">
                      <input type="radio" name="sex" value="男" title="男"><div class="layui-unselect layui-form-radio"><i class="layui-anim layui-icon"></i><div>男</div></div>
                      <input type="radio" name="sex" value="女" title="女" checked=""><div class="layui-unselect layui-form-radio layui-form-radioed"><i class="layui-anim layui-icon"></i><div>女</div></div>
                    </div>
                  </div>  --}}
                  <div class="layui-form-item">
                    <label class="layui-form-label">头像</label>
                    <div class="layui-input-inline">
                      {{--  lay-verify="required" 为输入框必须填  --}}
                      <input name="avatar" lay-verify="" id="LAY_avatarSrc" placeholder="图片地址" value="{{ $user->img }}" class="layui-input">
                    </div>
                      <div class="layui-input-block layui-upload">
                          <input type="hidden" id="img1" class="hidden" name="art_thumb" value="">
                          <button type="button" class="layui-btn" id="test1">
                              <i class="layui-icon">&#xe67c;</i>更改头像
                          </button>
                          <input type="file" name="photo" id="photo_upload" style="display: none;" />
                      </div>
                    <div class="layui-form-mid layui-word-aux" style="">建议尺寸250*250，支持jpg、png、gif，最大不能超过150KB</div>

                  </div>
                  <div class="layui-form-item">
                    <label class="layui-form-label">手机</label>
                    <div class="layui-input-inline">
                      <input type="text" name="phone" value="{{$user->phone}}" lay-verify="phone" autocomplete="off" class="layui-input">
                    </div>
                  </div>
                  <div class="layui-form-item">
                    <label class="layui-form-label">邮箱</label>
                    <div class="layui-input-inline">
                      <input type="text" name="email" value="{{$user->email}}" lay-verify="email" autocomplete="off" class="layui-input">
                    </div>
                  </div>
                  <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">个人简介</label>
                    <div class="layui-input-block">
                      <textarea name="resume" placeholder="请输入内容" class="layui-textarea">{{$user->resume}}</textarea>
                    </div>
                  </div>
                  <div class="layui-form-item">
                    <div class="layui-input-block">
                      <button class="layui-btn" lay-submit="" lay-filter="edit">确认修改</button>
                      <button type="reset" class="layui-btn layui-btn-primary">重新填写</button>
                    </div>
                  </div>
                </form>
                <div class="layui-col-md6" >
                  {{--  头像显示  --}}
                  @if ($user->img)
                    <img src="{{ $user->img }}" width="250" height="250" id="art_thumb_img" style="margin-left: 30%; border-radius:100%;box-shadow:5px 4px 10px rgba(0,0,0,.4);"/>
                  @else
                  <img src="{{ asset('home/images/defaultAvatar.png') }}" width="250" height="250" id="art_thumb_img" style="margin-left: 30%; border-radius:100%;box-shadow: 10px 10px 10px rgba(0,0,0,.5);"/>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</body>
<script>
    layui.use(['form','layer','upload','element'], function(){
      $ = layui.jquery;
      var form = layui.form
      ,layer = layui.layer;
      var upload = layui.upload;
      var element = layui.element;

      //头像上传的方法，点击上传图片，触发该方法；
      $('#test1').on('click',function () {
        var  uid = $("input[name='uid']").val();
          $('#photo_upload').trigger('click');
          $('#photo_upload').on('change',function () {//内容改变触发该方法
              var obj = this;
              var formData = new FormData($('#art_form')[0]);//将id为art_form的表单中的数据打包起来，放到formData变量中。
              $.ajax({
                  url: '/admin/user/uploadimg',
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
              type:'POST',
              url:'/admin/user/updatebyuser',//请求路由
              dataType:'json',//返回数据格式
              headers: {//这样POST就会传一个headers
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data:data.field,//提交的表单数据
              success:function(data){//判断是否成功
                  //弹层提示添加成功，并刷新父页面
                  if(data.status==0){
                      layer.alert(data.message,{icon:6},function(){
                        location.reload(true);//刷新父页面
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
        $(".layui-form-checked").not('.header').each(function(i,v) {
            var u = $(v).attr("data-id");
            alert("12:"+u);
            ids.push(u);
        });
        alert(ids);
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
</script>

</html>