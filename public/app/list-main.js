import { fetchCancelUserStock } from "./modules/Api.mjs";


layui.use(['table', 'dropdown'], function () {
    var table = layui.table;
    //第一个实例
    var tableObj = table.render({
        elem: '#stock-table',
        url: '/api/user_stock/list',
        page: true,
        initSort: {
            field: 'percent'
            , type: 'desc'
        },
        limit: 20,
        cols: [
            [ //表头
                {
                    field: 'name',
                    title: '名称',
                    width: 120
                }, {
                    field: 'code',
                    title: '代码',
                    width: 100
                }, {
                    field: 'current',
                    title: '当前价',
                    width: 100
                }, {
                    field: 'percent',
                    title: '涨跌幅',
                    width: 150,
                    sort: true,
                    templet: function (d) {
                        if (d.percent >= 0) {
                            var styleColor = 'color: red';
                        } else {
                            var styleColor = 'color: green';
                        }
                        return '<span style="' + styleColor + '">' + d.chg + '(' + d.percent + '%)</span>';
                    }
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

    //触发排序事件 
    table.on('sort(stock-table)', function (obj) { //注：sort 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
        console.log(obj.field); //当前排序的字段名
        console.log(obj.type); //当前排序类型：desc（降序）、asc（升序）、null（空对象，默认排序）
        console.log(this); //当前排序的 th 对象

        //尽管我们的 table 自带排序功能，但并没有请求服务端。
        //有些时候，你可能需要根据当前排序的字段，重新向服务端发送请求，从而实现服务端排序，如：
        table.reload('stock-table', {
            initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
            , where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                field: obj.field //排序字段
                , order: obj.type //排序方式
            }
        });
    });



    $('#add-btn').click(function () {
        layer.open({
            type: 2,
            title: null,
            scrollbar: false,
            maxmin: false,
            resize: false,
            scrollbar: false,
            area: ['300px', '410px'],
            content: '/stock/search',
        });
    });

    $('#sync-btn').click(function () {
        tableObj.reload({
            id: 'stock-table',
            where: {
                "sync_stock": 1
            }
        });
    });

    loopReloadData();

    listenMsg(function (type) {
        if (type == 'add_stock_notify' || type == 'del_stock_notify') {
            table.reloadData('stock-table');
        }
    });


    // 单元格工具事件
    table.on('tool(stock-table)', function (obj) { //注：tool 是工具条事件名，test 是 table 原始容器的属性 lay-filter="对应的值"
        var data = obj.data //获得当前行数据
            ,
            layEvent = obj.event; //获得 lay-event 对应的值
        if (layEvent === 'detail') {
            var index = layer.open({
                title: data.name,
                type: 2,
                // area: ['auto', '700px'],
                fixed: false, //不固定
                maxmin: true,
                content: '/stock/kline?code=' + data.code,
            });
            layer.full(index);
        } else if (layEvent === 'del') {
            console.log(data.id)
            fetchCancelUserStock({ code: data.code }).done(() => {
                obj.del()
                layer.msg('删除成功')
            })
        }
    });


    function loopReloadData() {
        setInterval(() => {
            tableObj.reloadData()
        }, 3000);
    }

});