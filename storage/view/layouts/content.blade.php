<html>

<head>
    <title>App Name - @yield('title')</title>
    <link rel="stylesheet" href="/css/font.css">
    <link rel="stylesheet" href="/layui/css/layui.css">
    @yield('style')
</head>

<body>
    <div class="layui-fluid">
        @yield('content')
    </div>
    <script type="text/javascript" src="/klinecharts.js"></script>
    <script type="text/javascript" src="/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="/jquery.qrcode.min.js"></script>
    <script type="text/javascript" src="/autoComplete.min.js"></script>
    <script type="text/javascript" src="/layui/layui.js"></script>
    <script type="text/javascript" src="/app/common.js"></script>
    @yield('script')
</body>

</html>