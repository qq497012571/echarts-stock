import { fetchCancelUserStock } from "./modules/Api.mjs";



layui.use(['table', 'dropdown'], function () {
    var table = layui.table;
    //第一个实例
    var tableObj = table.render({
        id: 'sotck-table',
        elem: '#sotck-table',
        url: '/api/user_stock/list',
        page: true,
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

    $('#add-btn').click(function () {
        layer.open({
            type: 2,
            area: ['380px', '430px'],
            skin: 'layui-layer-rim',
            content: '/stock/search'
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

    loopReloadData(tableObj);

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


    function loopReloadData(tableObj) {
        setInterval(() => {
            tableObj.reloadData()
        }, 3000);
    }




});