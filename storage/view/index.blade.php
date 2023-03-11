@extends('layouts.app')

@section('content')
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

<div id="stock-table"></div>
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
                        sort: true,
                        fixed: 'left'
                    }, {
                        field: 'stock_name',
                        title: '股票名称',
                        width: 150
                    }
                ]
            ]
        });

    });
</script>
@endsection