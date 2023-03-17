/**
 * 覆盖物: 画矩形框
 */
const drawRectOverlay = {
    // 名称
    name: 'sampleRect',
    // 完成一个圆的绘制需要三个步骤
    totalStep: 3,
    needDefaultPointFigure: true,
    // needDefaultXAxisFigure: true,
    // needDefaultYAxisFigure: true,
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


drawRectOverlay['onRemoved'] = function (event) {
    console.log('onRemoved', event)
};
drawRectOverlay['onDrawEnd'] = function (event) {
    console.log('onDrawEnd', event)
};
drawRectOverlay['onPressedMoveEnd'] = function (event) {
    console.log('onPressedMoveEnd', event)
};


class AppKlineCharts {

    MA_PANNER_ID = 'candle_pane'
    OVERLAY_LINE_NAME = 'OVERLAY_LINE_NAME'

    constructor(id) {

        this.chart = klinecharts.init(id)
        this.chart.setLocale('zh-CN')
        // this.chart.setBarSpace(50)
        // this.chart.setStyles({
        //     // 蜡烛图
        //     candle: {
        //         type: 'candle_up_stroke',
        //         bar: {
        //             upColor: '#dd2200',
        //             downColor: '#009933',
        //             noChangeColor: '#888888'
        //         }
        //     },
        // });

        klinecharts.registerOverlay(drawRectOverlay)

        this.setIndicatorMA()
        this.setIndicatorVOL()
        this.setIndicatorMACD()
    }

    data(klines) {
        this.chart.applyNewData(klines)
    }

    updateData(data) {

        this.current_bar = [data].map((data) => {
            return {
                timestamp: data.timestamp,
                open: +data.open,
                high: +data.high,
                low: +data.low,
                close: +data.close,
                volume: Math.ceil(+data.volume),
                turnover: +data.turnover,
            }
        })[0]

        this.chart.updateData(this.current_bar)
    }

    /**
     * 自定义均线指标
     */
    setIndicatorMA(ma = [70, 250]) {
        this.chart.removeIndicator(this.MA_PANNER_ID, 'MY_MA')
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
            name: 'MY_MA',
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
        this.chart.createIndicator('MY_MA', false, {
            id: this.MA_PANNER_ID
        });
    }

    createPriceLineOverlay(price) {
        var option = {
            name: "simpleTag",
            extendData: price,
            points: [{
                value: price
            }],
            styles: {
                line: {
                    show: true,
                    style: 'dashed',
                    dashedValue: [4, 4],
                    size: 2,
                }
            },
            lock: false
        };
        this.chart.createOverlay(option);
        return option
    }


    createPriceLineOverlayByOption(option) {
        this.chart.createOverlay(option);
        return option
    }
}

export { AppKlineCharts }