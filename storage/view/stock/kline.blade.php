@extends('layouts.content')

<div class="container" style="padding: 10px;">
    <div id="kline-charts" style="height: 550px"></div>
    <button class="layui-btn" id="draw-circle">画圆</button>
    <!-- <button class="layui-btn layui-btn-sm" onclick="klines(101)">日K</button>
    <button class="layui-btn layui-btn-sm" onclick="klines(60)">60分钟</button> -->
</div>

@section('script')
<script>
    class KlineCharts {

        constructor(domId) {
            this.chart = klinecharts.init(domId)
            this.chart.setLocale('zh-CN')
            // this.chart.setStyles()
            this.chart.setStyles({
                // 蜡烛图
                candle: {
                    // 蜡烛图类型 'candle_solid'|'candle_stroke'|'candle_up_stroke'|'candle_down_stroke'|'ohlc'|'area'
                    type: 'candle_up_stroke',
                    // 蜡烛柱
                    bar: {
                        upColor: '#dd2200',
                        downColor: '#009933',
                        noChangeColor: '#888888'
                    }
                },
            });
            // // 创建一个主图技术指标
            // this.chart.createIndicator('MA', false, {
            //     id: 'candle_pane'
            // })
            // // 创建一个副图技术指标VOL
            // this.chart.createIndicator('VOL', false, {
            //     id: "vol_panne"
            // })
            // // 创建一个副图技术指标MACD
            // this.chart.createIndicator('MACD', false, {
            //     id: "macd_panne"
            // })

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
            // this.klines = data.klines;
            // this.name = data.name;
            let newdata = this.handleKlines(klines)
            this.chart.applyNewData(newdata)
        }

        setOverlayType(name) {
            this.chart.createOverlay(name)
        }


        removeOverlay(name) {

        }

        updateData(data) {
            console.log(data, this.chart.getDataList())
            this.chart.updateData([data].map((data)=>{return {
                    timestamp: data.timestamp,
                    open: +data.open,
                    high: +data.high,
                    low: +data.low,
                    close: +data.close,
                    volume: Math.ceil(+data.volume),
                    turnover: +data.turnover,
                }})[0])
        }

    }

    var data = @json($data);
    var chart = new KlineCharts('kline-charts')
    var index = 1001
    chart.data(data.klines.slice(0,1000))

    setInterval(() => {
        index += 1
        chart.updateData(data.klines[index])
    }, 1000);

</script>
@endsection