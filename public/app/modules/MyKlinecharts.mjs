import { fetchAddMarks,fetchRemoveMarks } from "./Api.mjs";
import { getUrlQuery } from "./Utils.mjs";

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
    // 绘制结束回调事件，可缺省
    // onDrawEnd?: (event: OverlayEvent) => boolean,
    // 按住拖动结束回调事件，可缺省
    // onPressedMoveEnd: (event: OverlayEvent) => boolean,
    // 删除回调事件，可缺省
    // onRemoved?: (event: OverlayEvent) => boolean,
};


klinecharts.registerOverlay(drawRectOverlay)


class AppKlineCharts {

    constructor(id) {

        this.chart = klinecharts.init(id)
        this.chart.setLocale('zh-CN')

        this.registerOverlay()
        this.setIndicatorMA()
        this.setIndicatorVOL()
        this.setIndicatorMACD()
    }

    createOverlay(option, override = false) {

        option['onPressedMoveEnd'] = this.saveOverlay;
        option['onDrawEnd'] = this.saveOverlay;
        option['onRemoved'] = this.removeOverlay;

        if (option['points'] !== undefined) {
            // option.points = option.points.map(function (p) {
            //     return {timestamp: p.timestamp, value: p.value}
            // });
        }

        if (override) {
            console.log('override', option.points)
            return this.chart.overrideOverlay(option);
        }

        console.log('create', option.points)
        return this.chart.createOverlay(option);
    }

    saveOverlay(event) {
        var mark_type;
        switch (event.overlay.name) {
            case 'priceLine':
                mark_type = 'line'
                break;
            case 'sampleRect':
                mark_type = 'rect'
                break;
            default:
                return console.log('save error')
                break;
        }

        fetchAddMarks({ code: getUrlQuery('code'), overlay_id: event.overlay.id, option: JSON.stringify(event.overlay), mark_type: mark_type})
            .then(res => {
            })
    }

    removeOverlay(event) {
        fetchRemoveMarks({ code: getUrlQuery('code'), overlay_id: event.overlay.id})
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
            id: "vol_panne"
        })
    }

    /**
     * MACD指标
     */
    setIndicatorMACD() {
        // 创建一个副图技术指标MACD
        this.chart.createIndicator('MACD', false, {
            id: "macd_panne"
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