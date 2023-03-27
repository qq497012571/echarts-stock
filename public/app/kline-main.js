import { fetchKlines, fetchAddMarks, fetchMarks } from "./modules/Api.mjs";
import { AppKlineCharts } from "./modules/MyKlinecharts.mjs";
import { getUrlQuery } from "./modules/Utils.mjs";


var code = getUrlQuery('code');
var app = new AppKlineCharts('kline-charts')
var currentMa = 'day'
var first = true;


loadCharts(code, currentMa)

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


$(document).on('keyup', function (e) {
    if (e.keyCode == 32 || e.keyCode == 13 || e.keyCode == 27) {
        layer.closeAll()
    }
});

$('.draw-bar').on('click', function () {
    switch ($(this).attr('key')) {
        case 'draw-line':
            var option = {
                name: 'priceLine',
                styles: {
                    line: {
                        style: 'solid',
                        color: 'red',
                        size: 1
                    }
                },
            };
            app.createOverlay(option);
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

    if (currentMa != ma && window.loadChartsTimerId != undefined) {
        clearTimeout(window.loadChartsTimerId);
        window.loadChartsTimerId = undefined
    }

    currentMa = ma
    !refreshTick && app.chart.clearData()

    $.when(fetchKlines(code, ma, num, new Date().getTime()), fetchMarks(code)).done(function (d1, d2) {
        var klines = d1[0].data;
        var marks = d2[0].data;

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

        first = false;
        app.chart.resize();

        if (window.loadChartsTimerId == undefined) {
            window.loadChartsTimerId = setInterval(() => {
                loadCharts(code, ma, 1, true)
            }, 2000);
        }

    });


}

