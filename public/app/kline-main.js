import { fetchKlines, fetchAddMarks, fetchMarks } from "./modules/Api.mjs";
import { AppKlineCharts } from "./modules/MyKlinecharts.mjs";
import { getUrlQuery } from "./modules/Utils.mjs";


var code = getUrlQuery('code');
var app = new AppKlineCharts('kline-charts')
var currentMa = 'day'

registerOverlayKeyup();

$('.draw-bar').on('click', function(){
    console.log($(this).attr('key'))
    switch($(this).attr('key')) {
        case 'draw-line':
            break;
        case 'draw-rect':
            app.chart.createOverlay('sampleRect')
            break;
        case 'draw-notice-line':
            break;
    }
});

loadCharts(code, currentMa)

app.chart.loadMore((timestamp) => {
    fetchKlines(code, currentMa, 142, timestamp).done(function(res) {
        console.log(res.data.length, 'more')
        var hasMore = true
        if (res.data.length != 142) {
            hasMore = false
        }
        res.data.length && app.chart.applyMoreData(
            res.data,
            hasMore
        );

        console.log(app.chart.getDataList())
    })
})

$('.ma-bars-box button').on('click', function() {
    var ma = $(this).attr('key');
    loadCharts(code, ma)
});

$('#add-mark').on('click', function() {
    layer.prompt({
        'title': "预警"
    }, function(value, index, elem) {
        var option = chart.createPriceLineOverlay(value);
        var data = {
            code: code,
            value: value,
            mark_type: 1,
            mark_option: JSON.stringify(option),
        };
        fetchAddMarks(data).done(function() {
            layer.close(index);
        });
    });
});


function loadCharts(code, ma) {
    currentMa = ma
    app.chart.clearData()

    $.when(fetchKlines(code, ma, 284, new Date().getTime()), fetchMarks(code)).done(function(d1, d2) {
        var klines = d1[0].data;
        var marks = d2[0].data;
        klines.length && app.data(klines);
        marks.length && marks.map((m) => {
            chart.createPriceLineOverlayByOption(JSON.parse(m.mark_option))
        });
    });
}

/**
 * 注册页面按键事件
 */
function registerOverlayKeyup() {
    $(document).on('keyup', function(e) {
        console.log(e.which)
    })
}