<html>

<head>
    <title>App Name - @yield('title')</title>
    <link rel="stylesheet" href="/layui/css/layui.css">
    @yield('style')
</head>

<body>
    <div class="layui-fluid">
        @yield('content')
    </div>
    <script type="text/javascript" src="/klinecharts.js"></script>
    <script src="/echarts.min.js"></script>
    <script src="/jquery-3.6.4.min.js"></script>
    <script src="/jquery.cookie.min.js"></script>
    <script src="/layui/layui.js"></script>
    <script src="/app/main.js"></script>
    @yield('script')
</body>

</html>