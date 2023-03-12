@extends('layouts.content')

<div class="container" style="padding: 10px;">
    <div id="kline-charts" style="height: 99%"></div>
<!-- 

    <button class="layui-btn" onclick="doChart('start')">开始</button>
    <button class="layui-btn" onclick="doChart('pause')">暂停</button>
    <button class="layui-btn" onclick="doChart('buy')">买</button>
    <button class="layui-btn" onclick="doChart('sell')">卖</button>
    <button class="layui-btn" onclick="doChart('buy_line')">画线买入</button>
    <button class="layui-btn" onclick="doChart('sell_line')">画线卖出</button> -->

    <!-- <button class="layui-btn layui-btn-sm" onclick="klines(101)">日K</button>
    <button class="layui-btn layui-btn-sm" onclick="klines(60)">60分钟</button> -->
</div>

@section('script')
<script>
    class KlineCharts {

        MA_PANNER_ID = 'candle_pane'

        constructor(domId) {

            this.chart = klinecharts.init(domId)
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

            this.chart.setBarSpace(60)

            this.setIndicatorMA()
            this.setIndicatorVOL()
            this.setIndicatorMACD()
        }

        handleKlines(klines) {
            return klines.map(function(data) {
                return {
                    timestamp: data.timestamp,
                    open: +data.open,
                    high: +data.high,
                    low: +data.low,
                    close: +data.close,
                    volume: Math.ceil(+data.volume),
                    turnover: +data.turnover,
                }
            })
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
                regenerateFigures: function(params) {
                    return params.map(function(p, i) {
                        return {
                            key: "ma".concat(i + 1),
                            title: "MA".concat(p, ": "),
                            type: 'line'
                        };
                    });
                },
                calc: function(dataList, indicator) {
                    var params = indicator.calcParams,
                        figures = indicator.figures;
                    var closeSums = [];
                    return dataList.map(function(kLineData, i) {
                        var ma = {};
                        var close = kLineData.close;
                        params.forEach(function(p, index) {
                            var _a;
                            closeSums[index] = ((_a = closeSums[index]) !== null && _a !== void 0 ? _a : 0) + close;
                            if (i >= p - 1) {
                                ma[figures[index].key] = closeSums[index] / p;
                                closeSums[index] -= dataList[i - (p - 1)].close;
                            }
                        });
                        console.log('calc ma', ma)
                        return ma;
                    });
                }
            });

            this.chart.createIndicator('MY_MA', false, {
                id: this.MA_PANNER_ID
            });
        }

    }

    var data = @json($data);
    var chart = new KlineCharts('kline-charts')

    chart.data(data.klines)


    var timerId;
    var index;
    var status;
    var current_action;

    function doChart(action) {


        switch (action) {
            case 'start':
                if (current_action == 'start') {
                    return
                }
                if (!timerId) {
                    index = 1001
                    chart.data(data.klines.slice(0, 1000))
                }

                timerId = setInterval(() => {
                    chart.updateData(data.klines[index])
                    index += 1
                }, 1000);

                current_action = action
                break;
            case 'pause':
                if (current_action == 'pause') {
                    return
                }
                clearInterval(timerId)

                current_action = action

            case 'buy':
                chart.chart.createOverlay({
                    name: "simpleTag",
                    extendData: chart.current_bar.close,
                    points: [{
                        value: chart.current_bar.close
                    }],
                    styles: {
                        line: {
                            show: true,
                            style: 'dashed',
                            dashedValue: [4, 4],
                            size: 1,
                        }
                    }
                });
                chart.buy_bar = chart.current_bar;
                current_action = action
                break;
        }
    }
</script>
@endsection