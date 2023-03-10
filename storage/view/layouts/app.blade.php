<html>
    <head>
        <title>App Name - @yield('title')</title>
        <link rel="stylesheet" href="/layui/css/layui.css">
        <style>
            .container {
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>
        <script src="/jquery-3.6.4.min.js"></script>
        <script src="/jquery.cookie.min.js"></script>
        <script src="/layui/layui.js"></script>
        @yield('script')
    </body>
</html>