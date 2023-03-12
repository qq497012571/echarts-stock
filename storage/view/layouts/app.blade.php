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
            <div class="layui-logo layui-hide-xs layui-bg-black">layout demo</div>
            <!-- 头部区域（可配合layui 已有的水平导航） -->
            <ul class="layui-nav layui-layout-left">
                <!-- 移动端显示 -->
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-header-event="menuLeft">
                    <i class="layui-icon layui-icon-spread-left"></i>
                </li>

                <li class="layui-nav-item layui-hide-xs"><a href="">nav 1</a></li>
                <li class="layui-nav-item layui-hide-xs"><a href="">nav 2</a></li>
                <li class="layui-nav-item layui-hide-xs"><a href="">nav 3</a></li>
                <li class="layui-nav-item">
                    <a href="javascript:;">nav groups</a>
                    <dl class="layui-nav-child">
                        <dd><a href="">menu 11</a></dd>
                        <dd><a href="">menu 22</a></dd>
                        <dd><a href="">menu 33</a></dd>
                    </dl>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right">
                <li class="layui-nav-item layui-hide layui-show-md-inline-block">
                    <a href="javascript:;">
                        <img src="//tva1.sinaimg.cn/crop.0.0.118.118.180/5db11ff4gw1e77d3nqrv8j203b03cweg.jpg" class="layui-nav-img">
                        tester
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="">Your Profile</a></dd>
                        <dd><a href="">Settings</a></dd>
                        <dd><a href="">Sign out</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item" lay-header-event="menuRight" lay-unselect>
                    <a href="javascript:;">
                        <i class="layui-icon layui-icon-more-vertical"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="layui-side layui-bg-black">
            <div class="layui-side-scroll">
                <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
                <ul class="layui-nav layui-nav-tree" lay-filter="test">
                    <li class="layui-nav-item"><a href="javascript:;">市场</a></li>
                    <li class="layui-nav-item layui-nav-itemed">
                        <a class="" href="javascript:;">个人</a>
                        <dl class="layui-nav-child">
                            <dd><a href="/stock/list" target="body">我的预警</a></dd>
                            <dd><a href="javascript:;">模拟炒股</a></dd>
                            <dd><a href="javascript:;">炒股练习</a></dd>
                            <dd><a href="javascript:;">策略选股</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>
        <div class="layui-body" style="padding: 10px;">
            <iframe src="" name="body" frameborder="0" width="100%" height="100%"></iframe>
        </div>
    </div>
    <script type="text/javascript" src="/klinecharts.js"></script>
    <script src="/echarts.min.js"></script>
    <script src="/jquery-3.6.4.min.js"></script>
    <script src="/jquery.cookie.min.js"></script>
    <script src="/layui/layui.js"></script>
    <script src="/app/main.js"></script>
    <script>
        //JS 
        layui.use(['element', 'layer', 'util'], function() {
            var element = layui.element,
                layer = layui.layer,
                util = layui.util,
                $ = layui.$;

            //头部事件
            util.event('lay-header-event', {
                //左侧菜单事件
                menuLeft: function(othis) {
                    layer.msg('展开左侧菜单的操作', {
                        icon: 0
                    });
                },
                menuRight: function() {
                    layer.open({
                        type: 1,
                        content: '<div style="padding: 15px;">处理右侧面板的操作</div>',
                        area: ['260px', '100%'],
                        offset: 'rt' //右上角
                            ,
                        anim: 5,
                        shadeClose: true
                    });
                }
            });
        });
    </script>
</body>

</html>