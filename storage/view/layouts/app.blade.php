<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>layout 管理系统大布局 - Layui</title>
    <link rel="stylesheet" href="/layui/css/layui.css">
</head>

<body>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域（可配合layui 已有的水平导航） -->
            <ul class="layui-nav layui-layout-left">
                <!-- 移动端显示 -->
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
                    <i class="layui-icon layui-icon-spread-left"></i>
                </li>

                <li class="layui-nav-item layui-hide-xs"><a href="/stock/kline?code=SH600600" target="body">图表</a></li>
                <!-- <li class="layui-nav-item layui-hide-xs"><a href="">nav 2</a></li>
                <li class="layui-nav-item layui-hide-xs"><a href="">nav 3</a></li>
                <li class="layui-nav-item">
                    <a href="javascript:;">nav groups</a>
                    <dl class="layui-nav-child">
                        <dd><a href="">menu 11</a></dd>
                        <dd><a href="">menu 22</a></dd>
                        <dd><a href="">menu 33</a></dd>
                    </dl>
                </li> -->
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layui-hide layui-show-md-inline-block">
                    <a href="javascript:;">
                        {{$user['name']}}
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="/logout">登出</a></dd>
                    </dl>
                </li>
               
            </ul>
        </div>

        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree" lay-filter="test">
                    <!-- <li class="layui-nav-item"><a href="javascript:;">市场</a></li> -->
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">个人</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/stock/list" target="body">我的自选</a></dd>
                            <dd><a href="/home/profile" target="body">账号配置</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body" style="padding: 10px;">
            <iframe src="" name="body" frameborder="0" width="100%" height="100%" scrolling="no"></iframe>
        </div>
    </div>
    <script type="text/javascript" src="/layui/layui.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
    <script>
        var socket = io('ws://120.46.152.2:9502', {
            transports: ["websocket"]
        });
        socket.on('connect', data => {
            var user_id = '{{$user["id"]}}'
            socket.emit('join-room', user_id, console.log);
        });
        socket.on('alarm', data => {
            var data = JSON.parse(data)
            layer.alert(data.data['remark'], {title: data.data['title'] ? data.data['title'] : "警报"})
        });
    </script>
</body>

</html>