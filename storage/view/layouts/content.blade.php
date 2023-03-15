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
    <script type="text/javascript" src="/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="/echarts.min.js"></script>
    <script type="text/javascript" src="/layui/layui.js"></script>
    <script type="text/javascript" src="/app/api.js"></script>
    <script type="text/javascript" src="/app/main.js"></script>
    <script>
        function getUrlQuery(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.location.search.substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]); return null; //返回参数值
        }
    </script>
    @yield('script')
</body>

</html>