import { fetchKlines, fetchAddMarks, fetchMarks, fetchQuotes } from "./modules/Api.mjs";
import { AppKlineCharts, KlineControl } from "./modules/MyKlinecharts.mjs";
import { getUrlQuery, Timer } from "./modules/Utils.mjs";

layui.use(['laytpl', 'flow', 'jquery'], function () {
    var laytpl = layui.laytpl;
    var flow = layui.flow;

    var code = getUrlQuery('code')
    var app = new AppKlineCharts('kline-charts')
    var currentMa = 'day'
    var currentCode = code
    var codes = new Set();
    var first = true;
    var lastBarTimestamp;
    const currentMaMap = {
        'week': -1,
        'day': 0,
        '60m': 1,
        '30m': 2,
        '15m': 3,
        '5m': 4,
        '1m': 5,
    };


    // 加载股票图表
    loadCharts(code, currentMa)

    listenStockList();

    listenMsg(function(type, payload){
        if (type == 'add_stock_notify' || type == 'del_stock_notify') {
            Timer.stop('listenStockList')
            listenStockList()
        }
    })

    function listenStockList() {

        $('#stock-list-box').html('');

        // 加载股票列表
        flow.load({
            elem: '#stock-list-box' //流加载容器
            , scrollElem: '#list-item-container'
            , isAuto: true
            , done: function (page, next) { //执行下一页的回调
                fetchQuotes({ page, limit: 20 }).done(function (res) {
                    var data = res.data
                    var pages = Math.ceil(res.count / 20)
                    data.length && data.map(r => codes.add(r.code))
                    laytpl($('#list-item-tpl').html()).render({ data, currentCode: currentCode }, function (html) {
                        next(html, page < pages)

                        $('.list-item li').click(function () {
                            if (code != $(this).attr('code')) {
                                window.location.href = '/stock/kline?code=' + $(this).attr('code')
                            }
                        })
                    });
                });
            }
        });

        Timer.add('listenStockList', () => {
            console.log('================更新右侧菜单行情================')
            fetchQuotes({ page: 1, limit: Array.from(codes).length }).done(function (res) {
                var data = res.data;
                $(data).each(function (i, v) {
                    var p = `<p>${v.current}</p><p>${v.percent}%</p>`
                    $(`#stock-item-${v.code} .right`).html(p);
                    if (v.percent >= 0) {
                        $(`#stock-item-${v.code} .right`).removeClass('green').addClass('red')
                    } else {
                        $(`#stock-item-${v.code} .right`).removeClass('red').addClass('green')
                    }
                });
            });
        }, 3000).start();
    }

    app.chart.loadMore((timestamp) => {
        fetchKlines(code, currentMa, 142, timestamp).done(function (res) {
            var hasMore = true
            if (res.data.length != 142) {
                hasMore = false
            }
            res.data.length && app.chart.applyMoreData(
                res.data,
                hasMore
            );
        });
    })


    $('.right-bar-box span').on('click', function () {
        if ($(this), $(this).hasClass('select')) {
            return
        }
        $('.right-bar-box span').removeClass('select') && $(this).addClass('select')
    });


    $('.right-bar-box span').on('click', function () {
        if ($(this), $(this).hasClass('select')) {
            return
        }
        $('.right-bar-box span').removeClass('select') && $(this).addClass('select')
    });



    $('#add_stock').click(function () {
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

    $(document).on('keyup', function (e) {
        if (e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 27) {
            layer.closeAll()
        }
    });

    $('.draw-bar').on('click', function () {
        switch ($(this).attr('key')) {
            case 'draw-back-line':
                if (app.switch_draw_back_line) {
                    return layer.msg('已经正在回放啦, 请重新开始');
                }

                if (!app.switch_draw_back_line && currentMa == 'day') {
                    var data = app.chart.getDataList();
                    var option = {
                        name: 'draw_back_line',
                        extendData: { newbar: data[data.length - 1], code: code }
                    };
                    app.createOverlay(option);
                    Timer.stopAll();
                } else {
                    layer.msg('仅支持日线级别开始回放');
                }
                break;
            case 'draw-next-line':
                if (app.switch_draw_back_line && app.back_line.length) {
                    let next = app.back_line.shift();
                    app.updateData(next);
                    if (currentMa == 'day') {
                        KlineControl.setup(next.timestamp);
                    } else {
                        KlineControl.move(currentMaMap[currentMa], 1);
                    }
                }
                break;
            case 'draw-rect':
                app.createOverlay({ name: 'sampleRect' })
                break;
            case 'draw-alarm-line':
                var data = app.chart.getDataList();
                var extendData = { newbar: data[data.length - 1], code: code }
                var option = {
                    name: 'alarm_line',
                    extendData: extendData,
                    styles: {
                        line: {
                            style: 'dashed',
                            color: 'red',
                            dashedValue: [4, 4]
                        }
                    },
                };
                app.createOverlay(option);
                break;
        }
    });


    $('.period').on('click', function () {
        var ma = $(this).attr('key');
        $('.period').removeClass('selected') && $(this).addClass('selected')
        loadCharts(code, ma)
    });

    $('#add-mark').on('click', function () {
        layer.prompt({
            'title': "预警"
        }, function (value, index, elem) {
            var option = chart.createPriceLineOverlay(value);
            var data = {
                code: code,
                value: value,
                mark_type: 1,
                mark_option: JSON.stringify(option),
            };
            fetchAddMarks(data).done(function () {
                layer.close(index);
            });
        });
    });

    function loadCharts(code, ma, num = 284, refreshTick = false) {

        currentMa = ma;
        !refreshTick && app.chart.clearData()

        $.when(fetchKlines(code, ma, num, new Date().getTime()), fetchMarks(code)).done(function (d1, d2) {
            var klines = d1[0].data;
            var marks = d2[0].data;
            var lastBarTimestamp = klines[klines.length - 1].timestamp;


            if (app.switch_draw_back_line) {
                console.log('back', KlineControl.options[currentMaMap[currentMa]].current_timestamp)
                var newklines = [];
                for (let i = 0; i < klines.length; i++) {
                    if (klines[i].timestamp == KlineControl.options[currentMaMap[currentMa]].current_timestamp * 1000) {
                        newklines = klines.slice(0, i + 1);
                        app.back_line = klines.slice(i + 1, klines.length);
                        break;
                    }
                }

                if (newklines.length) {
                    app.data(newklines)
                    console.log('back', app.draw_back_line[currentMaMap[currentMa]])
                    app.createOverlay(app.draw_back_line[currentMaMap[currentMa]], !first)
                }
            } else {
                if (refreshTick) {
                    app.updateData(klines[0])
                } else {
                    app.data(klines);
                }

                marks.length && marks.map((m) => {
                    var option = JSON.parse(m.option)
                    var data = app.chart.getDataList();
                    var extendData = { newbar: data[data.length - 1], code: code }
                    option.extendData = extendData;
                    app.createOverlay(option, !first)
                });

                Timer.add('loadCharts', () => {
                    console.log('================更新图表数据================')
                    loadCharts(code, ma, 1, true)
                }, 2000).start();
            }

            first = false;
            app.chart.resize();
        });


    }


})


