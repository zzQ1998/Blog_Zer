<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>欢迎页面-X-admin2.0</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    {{--  除了检查 POST 参数中的 CSRF 令牌外， VerifyCsrfToken 中间件还会检查 X-CSRF-TOKEN 请求头。你应该将令牌保存在 HTML meta 标签中  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('admin.public.style')
    @include('admin.public.script')
</head>

<body>
    <div class="x-body">
        <form class="layui-form" id="art_form" action="{{ url('admin/article') }}" method="post">
{{--            {{ csrf_field() }}--}}
            {{--  //选择文章分类  --}}
            <div class="layui-form-item">
                <label for="L_email" class="layui-form-label">
                    <span class="x-red">*</span>分类
                </label>
                <div class="layui-input-inline">
                    <select name="cate_id">
                        {{--<option value="0">==顶级分类==</option>--}}
                        @foreach($cates as $k=>$v)
                            <option value="{{ $v->cate_id }}">{{ $v->cate_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">
                    <span class="x-red">*</span>
                </div>
            </div>
            {{--  //文章标题  --}}
            <div class="layui-form-item">
                <label for="L_art_title" class="layui-form-label">
                    <span class="x-red">*</span>文章标题
                </label>
                <div class="layui-input-block">
                    {{csrf_field()}}
                    <input type="text" id="L_art_title" name="art_title" required=""
                        autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_art_editor" class="layui-form-label">
                    <span class="x-red">*</span>编辑
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_art_editor" name="art_editor" required=""
                        autocomplete="off" class="layui-input">
                </div>
            </div>
            {{--  //上传图片  --}}
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">缩略图</label>
                <div class="layui-input-block layui-upload">
                    <input type="hidden" id="img1" class="hidden" name="art_thumb" value="">
                    <button type="button" class="layui-btn" id="test1">
                        <i class="layui-icon">&#xe67c;</i>上传图片
                    </button>
                    <input type="file" name="photo" id="photo_upload" style="display: none;" />
                </div>
            </div>

            {{--  //显示缩略图  --}}
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label"></label>
                <div class="layui-input-block">
                    <img src="" alt="" id="art_thumb_img" style="max-width: 350px; max-height:100px;">
                </div>
            </div>

            {{--  //填写关键词  --}}
            <div class="layui-form-item">
                <label for="L_art_tag" class="layui-form-label">
                    <span class="x-red">*</span>关键词
                </label>
                <div class="layui-input-inline">
                    <input type="text" id="L_art_tag" name="art_tag" required=""
                        autocomplete="off" class="layui-input">
                </div>
            </div>

            {{--  //填写描述  --}}
            <div class="layui-form-item">
                <label for="L_art_tag" class="layui-form-label">
                    <span class="x-red">*</span>描述
                </label>
                <div class="layui-input-block">
                    <textarea placeholder="请输入内容" class="layui-textarea" name="art_description"></textarea>
                </div>
            </div>

            {{--  //填写文章内容  --}}
            <div class="layui-form-item">
                <label for="L_art_tag" class="layui-form-label">
                    <span class="x-red">*</span>内容
                </label>
                {{--  //使用百度编辑器UEditor实现文章内容编写  --}}
                <div class="layui-input-block">
                    {{--  //配置文件  --}}
                    <script type="text/javascript" charset="utf-8" src="/ueditor/ueditor.config.js"></script>
                    {{--  //编辑器源码文件  --}}
                    <script type="text/javascript" charset="utf-8" src="/ueditor/ueditor.all.min.js"> </script>
                    <script type="text/javascript" charset="utf-8" src="/ueditor/lang/zh-cn/zh-cn.js"></script>

                    {{--  //加载编辑器的容器  --}}
                    <script id="editor" type="text/plain" name="editor" style="width:800px;height:400px;">

                    </script>
                    <script type="text/javascript">
                    {{--  //实例化编辑器
                    //建议使用工厂方法getEditor创建和引用编辑器实例，如果在某个闭包下引用该编辑器，直接调用UE.getEditor('editor')就能拿到相关的实例  --}}
                    var ue = UE.getEditor('editor');
                    </script>

                </div>
            </div>

            {{--  //添加按钮  --}}
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label">
                </label>
                <button  class="layui-btn" lay-filter="add" lay-submit="">
                    增加
                </button>
            </div>
        </form>
    </div>
</body>
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' }
    });

    //Markdown AJAX
    $('#previewBtn').click(function(){
        $.ajax({
            url:"/admin/article/pre_mk",
            type:"post",
            data:{
                cont:$('#z-textarea').val()
            },
            success:function(res){
                $('#z-textarea-preview').html(res);
            },
            error:function(err){
                console.log(err.responseText);
            }
        });
    })


</script>
    <script>
        layui.use(['form','layer','upload','element'], function(){
            $ = layui.jquery;
            var form = layui.form
            ,layer = layui.layer;
            var upload = layui.upload;
            var element = layui.element;

            //图片上传的方法，点击上传图片，触发该方法；
            $('#test1').on('click',function () {
                $('#photo_upload').trigger('click');
                $('#photo_upload').on('change',function () {//内容改变触发该方法
                    var obj = this;

                    var formData = new FormData($('#art_form')[0]);//将id为art_form的表单中的数据打包起来，放到formData变量中。
                    $.ajax({
                        url: '/admin/article/upload',
                        type: 'post',
                        data: formData,
                        // 因为data值是FormData对象，不需要对数据做处理
                        processData: false,
                        contentType: false,
                        success: function(data){//接收返回值，并判断
                            if(data['ServerNo']=='200'){
                                // 如果成功
                                {{--$('#art_thumb_img').attr('src', '{{ env('ALIOSS_DOMAIN')  }}'+data['ResultData']);--}}
                                $('#art_thumb_img').attr('src', '{{ env('QINIU_DOMAIN')  }}'+data['ResultData']);
                                {{--  $('#art_thumb_img').attr('src', '/uploads/'+data['ResultData']);  --}}//上传到本地
                                $('input[name=art_thumb]').val('data');
                                $(obj).off('change');
                            }else{
                                // 如果失败
                                alert(data['ResultData']);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            var number = XMLHttpRequest.status;
                            var info = "错误号"+number+"文件上传失败!";
                            // 将菊花换成原图
                            // $('#pic').attr('src', '/file.png');
                            alert(info);
                        },
                        async: true
                    });
                });

        });

          //监听提交
        form.on('submit(add)', function(data){

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