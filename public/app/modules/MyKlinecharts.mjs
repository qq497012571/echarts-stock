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
    draw_back_line = [];
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


                KlineControl.setup(event.overlay.points[0]['timestamp'])

                // 保存每个级别对应的起始回放时间点
                for (var i = 0, len = KlineControl.options.length; i < len; i++) {
                    var overlay_option = Object.assign({}, event.overlay);
                    overlay_option.points = [{timestamp: KlineControl.options[i].current_timestamp * 1000}]
                    this.draw_back_line[i] = overlay_option;
                }

                console.log('back', this.draw_back_line)

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


/**
 * 控制k线同步行动
 **/
const KlineControl = {
    current_timestamp: null,

    options: [
        { current_index: 0, current_timestamp: null, step: 86400, datelist: [] },
        { current_index: 0, current_timestamp: null, step: 3600, datelist: [] },
        { current_index: 0, current_timestamp: null, step: 1800, datelist: [] },
        { current_index: 0, current_timestamp: null, step: 900, datelist: [] },
        { current_index: 0, current_timestamp: null, step: 300, datelist: [] },
        { current_index: 0, current_timestamp: null, step: 60, datelist: [] },
    ],

    getTodayDatelist: function (date, step) {
        var year = new Date(date).getFullYear();
        var month = new Date(date).getMonth() + 1;
        var day = new Date(date).getDate();

        var morning = [new Date(`${year}/${month}/${day} 09:30:00`).getTime(), new Date(`${year}/${month}/${day} 11:30:00`).getTime()];
        var afternoon = [new Date(`${year}/${month}/${day} 13:00:00`).getTime(), new Date(`${year}/${month}/${day} 15:00:00`).getTime()];
        var datelist = []

        for (let start = morning[0] / 1000, end = morning[1] / 1000; start <= end; start += step) {
            if (start * 1000 == morning[0]) {
                continue;
            }
            datelist.push(new Date(start * 1000).getTime() / 1000);
        }

        for (let start = afternoon[0] / 1000, end = afternoon[1] / 1000; start <= end; start += step) {
            if (start * 1000 == afternoon[0]) {
                continue;
            }
            datelist.push(new Date(start * 1000).getTime() / 1000);
        }


        return datelist;
    },

    setup: function (str) {

        if (typeof str != 'string') {
            var date = new Date(str);
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();
            var str = `${year}-${month}-${day} 00:00:00`;
        }

        console.log('setup', new Date(str).toLocaleString())

        for (let i in this.options) {
            if (i == 0) {
                var s = new Date(str).getTime() / 1000;
                var datelist = [s, s + 86400];
                this.current_timestamp = s;
                this.options[i].last_index = 0;
            } else {
                var datelist = this.getTodayDatelist(str, this.options[i].step);
                this.options[i].last_index = datelist.length - 1;
            }
            this.options[i].current_index = 0;
            this.options[i].datelist = datelist;
            this.options[i].current_timestamp = datelist[0];
        }

    },
    move: function (index, step, refresh = true) {

        if (this.options[index] < 0) {
            return;
        }

        var op = this.options[index];

        if (op.current_index + step > op.last_index) {
            if (index === 0) {
                return this.setup(new Date((op.current_timestamp + 86400) * 1000).toLocaleString());
            } else {
                // 上一级别进1
                return this.move(index - 1, 1);
            }
        } else {
            op.current_index += step;
            op.current_timestamp = op.datelist[op.current_index];


            if (refresh) {
                for (let i = index + 1; i < this.options.length; i++) {
                    let o = this.options[i];
                    o.current_timestamp = op.current_timestamp;
                    o.current_index = o.datelist.indexOf(op.current_timestamp);
                }
            }

            for (let e = index - 1; e > 0; e--) {
                let o = this.options[e];
                if (op.current_timestamp > o.current_timestamp) {
                    this.move(index - 1, 1, false);
                }
            }

        }
    }
}

export { AppKlineCharts, KlineControl }