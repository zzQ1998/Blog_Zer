<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- 引入页面描述和关键字模板 -->
    <title>@yield('title')</title>
    <meta name="description" content="猿圈专注于提供多元化的阅读体验，以阅读提升生活品质" />
    <meta name="keywords" content="猿圈,悦读,阅读,文字,历史,杂谈,散文,见闻,游记,人文,科技,杂碎,冷笑话,段子,语录" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    @include('home.public.styles')
    @include('home.public.script')
</head>
<body id="wrap" class="home blog">
<!-- Nav -->
<!-- Moblie nav-->
<div id="body-container">

    <!-- Moblie nav -->
    @include('home.public.navmenu');
    <!-- /.Moblie nav -->
    <section id="content-container" style="background:rgba(57, 61, 73, .15); ">
    {{--header start--}}
    @include('home.public.header')
    {{--header end--}}
    <!-- Main Wrap -->
            @section('main-wrap')

            {{--右侧边栏 start--}}
            @include('home.public.aside')
            {{--右侧边栏 end--}}

            @show
    <!--/.Main Wrap -->

            {{--footer --}}
            @include('home.public.footer')
            {{--footer --}}

    </section>
</div>
<script type="text/javascript" src="{{ asset('home/js/theme.js') }}"></script>
{{--登录--}}
@include('home.public.signin')
{{--登录--}}
</body>
</html>