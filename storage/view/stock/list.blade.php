@extends('layouts.content')

<div class="demoTable">
    搜索ID：
    <div class="layui-inline">
        <input class="layui-input" name="id" id="demoReload" autocomplete="off">
    </div>
    <button class="layui-btn" data-type="reload">搜索</button>
</div>
<table id="sotck-table" lay-filter="stock-table"></table>

@section('script')

<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="detail">K线图</a>
    <a class="layui-btn layui-btn-xs" lay-event="more">更多 <i class="layui-icon layui-icon-down"></i></a>
</script>

<script>
    layui.use('table', function() {
        var table = layui.table;

        //第一个实例
        table.render({
            elem: '#sotck-table',
            url: '/api/stock/list' //数据接口
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
                        field: 'name',
                        title: '名称',
                        width: 200
                    }, {
                        field: 'code',
                        title: '代码',
                        width: 200
                    }, {
                        fixed: 'right',
                        title: '操作',
                        width: 150,
                        align: 'center',
                        toolbar: '#barDemo'
                    }
                ]
            ]
        });

        // 单元格工具事件
        table.on('tool(stock-table)', function(obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
            var data = obj.data //获得当前行数据
                ,
                layEvent = obj.event; //获得 lay-event 对应的值
            if (layEvent === 'detail') {
                var index = layer.open({
                    title: data.name,
                    type: 2,
                    area: ['auto', '700px'],
                    fixed: false, //不固定
                    maxmin: true,
                    content: '/stock/kline?code=' + data.code,
                });
                layer.full(index);
            } else if (layEvent === 'more') {
                //下拉菜单
                dropdown.render({
                    elem: this //触发事件的 DOM 对象
                        ,
                    show: true //外部事件触发即显示
                        ,
                    data: [{
                        title: '编辑',
                        id: 'edit'
                    }, {
                        title: '删除',
                        id: 'del'
                    }],
                    click: function(menudata) {
                        if (menudata.id === 'del') {
                            layer.confirm('真的删除行么', function(index) {
                                obj.del(); //删除对应行（tr）的DOM结构
                                layer.close(index);
                                //向服务端发送删除指令
                            });
                        } else if (menudata.id === 'edit') {
                            layer.msg('编辑操作，当前行 ID:' + data.id);
                        }
                    },
                    align: 'right' //右对齐弹出
                        ,
                    style: 'box-shadow: 1px 1px 10px rgb(0 0 0 / 12%);' //设置额外样式
                })
            }
        });

    });
</script>

@endsection