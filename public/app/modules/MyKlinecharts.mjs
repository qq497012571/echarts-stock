import { fetchAddMarks, fetchRemoveMarks } from "./Api.mjs";
import { getUrlQuery } from "./Utils.mjs";


const UP_COLOR = '#ff3d3d'
const DOWN_COLOR = '#10cc55'

/**
 * 覆盖物: 画矩形框
 */
const drawRectOverlay = {
    name: 'sampleRect',
    totalStep: 3,
    needDefaultPointFigure: true,
    needDefaultXAxisFigure: true,
    needDefaultYAxisFigure: true,
    // 创建点对应的图形信息
    createPointFigures: ({ coordinates }) => {
        if (coordinates.length === 2) {
            const width = Math.abs(coordinates[0].x - coordinates[1].x)
            const height = Math.abs(coordinates[0].y - coordinates[1].y)
            const x = coordinates[0].x < coordinates[1].x ? coordinates[0].x : coordinates[1].x
            const y = coordinates[0].y < coordinates[1].y ? coordinates[0].y : coordinates[1].y
            // 图表内置了基础图形'circle'，可以直接使用
            return {
                key: 'sampleRect',
                type: 'rect',
                attrs: {
                    x: x,
                    y: y,
                    width: width,
                    height: height,
                },
                styles: {
                    style: 'stroke_fill',
                    borderSize: 0.5
                }
            };
        }
        return []
    },
};


klinecharts.registerOverlay(drawRectOverlay)


var drawBackLine = {
    name: 'draw_back_line',
    lock: true,
    totalStep: 2,
    needDefaultPointFigure: true,
    needDefaultXAxisFigure: true,
    needDefaultYAxisFigure: true,
    createPointFigures: function (_a) {
        var coordinates = _a.coordinates, bounding = _a.bounding;
        console.log(_a)
        return [
            {
                type: 'line',
                attrs: {
                    coordinates: [
                        {
                            x: coordinates[0].x,
                            y: 0
                        }, {
                            x: coordinates[0].x,
                            y: bounding.height
                        }
                    ]
                }
            }
        ];
    }
};


klinecharts.registerOverlay(drawBackLine)

/**
 * 覆盖物, 画预警线
 */
var alarmLine = {
    name: 'alarm_line',
    totalStep: 2,
    needDefaultPointFigure: true,
    needDefaultXAxisFigure: true,
    needDefaultYAxisFigure: true,
    createPointFigures: function (_a) {
        var coordinates = _a.coordinates, bounding = _a.bounding, precision = _a.precision, overlay = _a.overlay;
        var _b = (overlay.points)[0].value, value = _b === void 0 ? 0 : _b;
        //【(成本价-现价)÷成本价】×100%=涨跌幅度
        var newPrice = overlay.extendData.newbar.close;
        var drawPrice = value.toFixed(precision.price);
        var rate = ((drawPrice - newPrice) / drawPrice * 100).toFixed(2);
        var text = '预警: ' + value.toFixed(precision.price) + ' (' + rate + '%)'
        var textX = text.length * 7;

        return [{
            type: 'line',
            attrs: {
                coordinates: [
                    {
                        x: 0,
                        y: coordinates[0].y
                    }, {
                        x: bounding.width,
                        y: coordinates[0].y
                    }
                ]
            },
            styles: {
                style: 'dashed',
                color: '#e07203',
                dashedValue: [4, 2],
            }
        }, {
            type: 'rectText',
            ignoreEvent: true,
            attrs: { x: bounding.width - textX, y: coordinates[0].y, text: text, baseline: 'bottom' },
            styles: {
                style: 'fill',
                color: '#bcbec6',
                borderSize: 1,
                borderStyle: 'solid',
                backgroundColor: '#1e222d',
            }
        }];
    }
};

klinecharts.registerOverlay(alarmLine)

class AppKlineCharts {

    static chart = {}

    back_line = []
    draw_back_index = 0;
    draw_back_line = null;
    switch_draw_back_line = false;

    constructor(id) {

        AppKlineCharts.chart = this.chart = klinecharts.init(id)

        this.chart.setLocale('zh-CN')
        this.chart.setStyles({
            grid: {
                show: false,
            },
            // 蜡烛柱
            candle: {
                type: 'candle_up_stroke',
                bar: {
                    upColor: UP_COLOR,
                    downColor: DOWN_COLOR,
                    noChangeColor: '#888888'
                }
            },
            indicator: {
                bars: [{
                    // 'fill' | 'stroke' | 'stroke_fill'
                    style: 'fill',
                    // 'solid' | 'dashed'
                    borderStyle: 'solid',
                    borderSize: 1,
                    borderDashedValue: [2, 2],
                    upColor: UP_COLOR,
                    downColor: DOWN_COLOR,
                    backgroundColor: 'blue',
                    // noChangeColor: 'yellow'
                }],
            }
        })

        this.registerOverlay()
        this.setIndicatorMA()
        this.setIndicatorVOL()
        this.setIndicatorMACD()
    }

    createOverlay(option, override = false) {

        option['onPressedMoveEnd'] = this.saveOverlay.bind(this);
        option['onDrawEnd'] = this.saveOverlay.bind(this);
        option['onRemoved'] = this.removeOverlay.bind(this);

        if (option['points'] !== undefined) {
            if (option.name === 'alarm_line') {
                option.points = option.points.map(function (p) {
                    return { value: p.value }
                });
            }
        }

        if (override) {
            return this.chart.overrideOverlay(option);
        }

        return this.chart.createOverlay(option);
    }

    saveOverlay(event) {
        var overlay = event.overlay;
        var extendData = event.overlay.extendData || {};
        var alarm_form = {}

        switch (event.overlay.name) {
            case 'alarm_line':
                var value = parseFloat(overlay.points[0].value.toFixed(2));
                alarm_form = {
                    "price": value,
                    "timing_type": value > extendData.newbar.close ? 1 : 2,
                    "remark": `${extendData.code} ${value > extendData.newbar.close ? '升破' : '跌破'} ${value}`,
                    "overlay_id": `${overlay.id}`,
                }
                layer.open({
                    title: `针对${extendData.code}创建警报`,
                    type: 2,
                    area: ['570px', '550px'],
                    content: '/stock/alarmForm',
                    success: function (layero, index) {
                        layer.setTop(layero)
                        sendMsg('alarm_form', alarm_form);
                    }
                });
                break;
            case 'draw_back_line':
                this.switch_draw_back_line = true;
                var data = this.chart.getDataList();
                var startData = data.slice(0, event.overlay.points[0]['dataIndex'] + 1)
                this.back_line = data.slice(event.overlay.points[0]['dataIndex'] + 1);
                this.chart.applyNewData(startData)

                const day_overlay_option = Object.assign({}, event.overlay);
                day_overlay_option.points = [{ timestamp: event.overlay.points[0]['timestamp'] }]

                const ma60_overlay_option = Object.assign({}, event.overlay);
                ma60_overlay_option.points = [{ timestamp: ((event.overlay.points[0]['timestamp'] / 1000) + (34200 + (60 * 60))) * 1000 }]

                const ma30_overlay_option = Object.assign({}, event.overlay);
                ma30_overlay_option.points = [{ timestamp: ((event.overlay.points[0]['timestamp'] / 1000) + (34200 + (30 * 60))) * 1000 }]

                const ma15_overlay_option = Object.assign({}, event.overlay);
                ma15_overlay_option.points = [{ timestamp: ((event.overlay.points[0]['timestamp'] / 1000) + (34200 + (15 * 60))) * 1000 }]

                const ma5_overlay_option = Object.assign({}, event.overlay);
                ma5_overlay_option.points = [{ timestamp: ((event.overlay.points[0]['timestamp'] / 1000) + (34200 + (5 * 60))) * 1000 }]

                const ma1_overlay_option = Object.assign({}, event.overlay);
                ma1_overlay_option.points = [{ timestamp: ((event.overlay.points[0]['timestamp'] / 1000) + (34200 + (1 * 60))) * 1000 }]

                this.draw_back_line = {
                    'day': {option: day_overlay_option, start: day_overlay_option.points[0]['timestamp']},
                    '60m': {option: ma60_overlay_option, start: ma60_overlay_option.points[0]['timestamp']},
                    '30m': {option: ma30_overlay_option, start: ma30_overlay_option.points[0]['timestamp']},
                    '15m': {option: ma15_overlay_option, start: ma15_overlay_option.points[0]['timestamp']},
                    '5m': {option: ma5_overlay_option, start: ma5_overlay_option.points[0]['timestamp']},
                    '1m': {option: ma1_overlay_option, start: ma1_overlay_option.points[0]['timestamp']},
                };

                console.log('draw_back_line', this.draw_back_line, event.overlay.points);

                return;
                break;
            default: 32, 400
                return console.log('save error')
                break;
        }

        fetchAddMarks({ code: getUrlQuery('code'), overlay_id: event.overlay.id, option: JSON.stringify(event.overlay), mark_type: event.overlay.name, alarm_form: JSON.stringify(alarm_form) })
            .then(res => {
            })
    }

    removeOverlay(event) {
        fetchRemoveMarks({ code: getUrlQuery('code'), overlay_id: event.overlay.id })
            .then(res => {
                console.log('removeOverlay', res)
            })
    }

    // 注册矩形
    registerOverlay() {
        klinecharts.registerOverlay(drawRectOverlay)
    }

    data(data) {
        this.chart.applyNewData(data)
    }

    updateData(data) {
        this.chart.updateData(data)
    }

    /**
     * 自定义均线指标
     */
    setIndicatorMA(ma = [70, 250]) {
        this.createIndicatorMA(ma)
    }

    /**
     * 自定义指标
     */
    setIndicatorVOL() {
        this.chart.createIndicator('VOL', false, {
            id: "vol_panne",
            paneOptions: {
                height: 150,
                height: 100,
            }
        })
    }

    /**
     * MACD指标
     */
    setIndicatorMACD() {
        // 创建一个副图技术指标MACD
        this.chart.createIndicator('MACD', false, {
            id: "macd_panne",
            paneOptions: {
                height: 150,
                height: 100,
            }
        })
    }

    /**
     * 创建均线指标到主图上
     */
    createIndicatorMA(ma) {
        var calcParams = ma
        var figures = ma.map((k) => {
            return {
                key: 'ma' + k,
                title: 'MA' + k + ': ',
                type: 'line'
            }
        })

        klinecharts.registerIndicator({
            name: 'MA',
            shortName: 'MA',
            series: 'price',
            calcParams: calcParams,
            precision: 2,
            shouldOhlc: true,
            figures: figures,
            regenerateFigures: function (params) {
                return params.map(function (p, i) {
                    return {
                        key: "ma".concat(i + 1),
                        title: "MA".concat(p, ": "),
                        type: 'line'
                    };
                });
            },
            calc: function (dataList, indicator) {
                var params = indicator.calcParams,
                    figures = indicator.figures;
                var closeSums = [];
                return dataList.map(function (kLineData, i) {
                    var ma = {};
                    var close = kLineData.close;
                    params.forEach(function (p, index) {
                        var _a;
                        closeSums[index] = ((_a = closeSums[index]) !== null && _a !== void 0 ? _a : 0) + close;
                        if (i >= p - 1) {
                            ma[figures[index].key] = closeSums[index] / p;
                            closeSums[index] -= dataList[i - (p - 1)].close;
                        }
                    });
                    return ma;
                });
            }
        });
        this.chart.createIndicator('MA', false, {
            id: 'candle_pane'
        });
    }
}

export { AppKlineCharts }