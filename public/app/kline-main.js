import { fetchKlines, fetchAddMarks, fetchMarks } from "./modules/Api.mjs";
import { AppKlineCharts } from "./modules/MyKlinecharts.mjs";
import { getUrlQuery } from "./modules/Utils.mjs";


var code = getUrlQuery('code');
var app = new AppKlineCharts('kline-charts')
var currentMa = 'day'
var first = true;


registerOverlayKeyup();


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
            app.createOverlay({name: 'sampleRect'})
            break;
        case 'draw-alarm-line':
            var data = app.chart.getDataList();
            var option = {
                name: 'alarm_line',
                extendData: data[data.length-1]['close'],
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


$('.ma-bars-box button').on('click', function () {
    var ma = $(this).attr('key');
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


function loadCharts(code, ma) {
    currentMa = ma
    app.chart.clearData()

    $.when(fetchKlines(code, ma, 284, new Date().getTime()), fetchMarks(code)).done(function (d1, d2) {
        var klines = d1[0].data;
        var marks = d2[0].data;
        klines.length && app.data(klines);
        marks.length && marks.map((m) => {
            app.createOverlay(JSON.parse(m.option), !first)
        });
        first = false;
        app.chart.resize();
        console.log('resize')
    });

}

/**
 * 注册页面按键事件
 */
function registerOverlayKeyup() {
    $(document).on('keyup', function (e) {
        console.log(e.which)
    })
}