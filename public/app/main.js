class KlineCharts {

    MA_PANNER_ID = 'candle_pane'
    OVERLAY_LINE_NAME = 'OVERLAY_LINE_NAME'

    constructor(domId) {

        this.id = domId

        this.chart = klinecharts.init(this.id)
        this.chart.setLeftMinVisibleBarCount(30)
        this.chart.setRightMinVisibleBarCount(50)
        this.chart.setLocale('zh-CN')
        this.chart.setStyles({
            // 蜡烛图
            candle: {
                type: 'candle_up_stroke',
                bar: {
                    upColor: '#dd2200',
                    downColor: '#009933',
                    noChangeColor: '#888888'
                }
            },
        });

        this.setIndicatorMA()
        this.setIndicatorVOL()
        this.setIndicatorMACD()
    }

    handleKlines(klines) {
        return klines;
        // return klines.map(function (data) {
        //     return {
        //         timestamp: data.timestamp,
        //         open: +data.open,
        //         high: +data.high,
        //         low: +data.low,
        //         close: +data.close,
        //         volume: Math.ceil(+data.volume),
        //         // turnover: +data.turnoverrate,
        //     }
        // })
    }

    data(klines) {
        let newdata = this.handleKlines(klines)
        this.chart.applyNewData(newdata)
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
    createIndicatorMA(ma = [5, 10, 30, 60]) {
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


