@extends('layouts.app')

@section('style')
<style>
    .stock-container-menu button {
        /* cursor: pointer;
        background-color: #1677ff;
        border-radius: 2px;
        margin-right: 8px;
        height: 24px;
        line-height: 26px;
        padding: 0 6px;
        font-size: 12px;
        color: #fff;
        border: none;
        outline: none; */
    }
</style>
@endsection

@section('content')
<div class="layui-row layui-col-space10">

    <div class="home-header">
        <ul class="layui-nav">
            <li class="layui-nav-item layui-this"><a href="">选中</a></li>
            <li class="layui-nav-item">
                <a href="javascript:;">常规hello world!!!!!!!!!!!!!!!!!!!!!!</a>
            </li>
            <li class="layui-nav-item"><a href="">导航</a></li>
            <li class="layui-nav-item">
                <a href="javascript:;">子级</a>
                <dl class="layui-nav-child">
                    <dd><a href="">菜单111</a></dd>
                    <dd><a href="">菜单2</a></dd>
                    <dd><a href="">菜单3</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item">
                <a href="javascript:;">选项</a>
                <dl class="layui-nav-child">
                    <dd><a href="">选项1</a></dd>
                    <dd class="layui-this"><a href="">选项2</a></dd>
                    <dd><a href="">选项3</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="">演示</a></li>
        </ul>
    </div>
</div>


<div class="layui-row layui-col-space10">
    <div class="layui-col-xs3">
        <div class="grid-demo grid-demo-bg1">
            <div class="demoTable">
                搜索ID：
                <div class="layui-inline">
                    <input class="layui-input" name="id" id="demoReload" autocomplete="off">
                </div>
                <button class="layui-btn" data-type="reload">搜索</button>
            </div>
            <div id="stock-table" lay-filter="stock-table"></div>
        </div>
    </div>
    <div class="layui-col-xs9">

    </div>
</div>



<div class="layui-row layui-col-space15">
    <div class="layui-col-md6">
        <div class="layui-panel">
            <div class="stock-container-menu">
                <button class="layui-btn layui-btn-sm">分时K</button>
                <button class="layui-btn layui-btn-sm">日K</button>
                <button class="layui-btn layui-btn-sm">周K</button>
                <button class="layui-btn layui-btn-sm">60分</button>
                <button class="layui-btn layui-btn-sm">30分</button>
                <button class="layui-btn layui-btn-sm">15分</button>
                <button class="layui-btn layui-btn-sm">5分</button>
                <button class="layui-btn layui-btn-sm">1分</button>
            </div>
            <div id="stock-echarts" style="height: 350px;width:100%"></div>
        </div>
    </div>
    <div class="layui-col-md6">
        <div class="layui-panel">
            <div style="padding: 50px 30px;">一个面板</div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    layui.use('table', function() {
        var table = layui.table;

        //第一个实例
        table.render({
            elem: '#stock-table',
            height: 312,
            url: '/stock/list' //数据接口
                ,
            page: true //开启分页
                ,
            cols: [
                [ //表头
                    {
                        field: 'id',
                        title: 'ID',
                        width: 80,
                        fixed: 'left'
                    }, {
                        field: 'name',
                        title: '名称',
                        width: 150
                    }, {
                        field: 'code',
                        title: '代码',
                        width: 150
                    }
                ]
            ]
        });

        //行单击事件（双击事件为：rowDouble）
        table.on('row(stock-table)', function(obj) {
            var data = obj.data;
            //标注选中样式
            obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
            showStock('stock-echarts', data.code);
        });

    });
</script>
@endsection