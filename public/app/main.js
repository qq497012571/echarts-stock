const UP_COLOR = '#dd2200'
const DOWN_COLOR = 'rgb(0, 153, 51)'
const DEFAULT_KLINE_NUM = 50


function MA(close, dayCount) {
    var result = [];
    for (var i = 0, len = close.length; i < len; i++) {
        if (i < dayCount) {
            result.push("-");
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += parseFloat(close[i - j]);
        }

        result.push((sum / dayCount).toFixed(2));
    }
    return result;
}


function calculateEMA(values, days) {
    let k = 2 / (days + 1);
    let ema = [];
    let sma = 0;
    for (let i = 0; i < values.length; i++) {
        if (i < days - 1) {
            sma += values[i];
            ema.push(null);
        } else if (i === days - 1) {
            sma += values[i];
            ema.push(sma / days);
        } else {
            let prevEma = ema[i - 1];
            let currValue = values[i];
            let currEma = (currValue - prevEma) * k + prevEma;
            ema.push(currEma);
        }
    }
    return ema;
}

function calculateMACD(values) {
    let ema12 = calculateEMA(values, 12);
    let ema26 = calculateEMA(values, 26);
    let dif = [];
    for (let i = 0; i < values.length; i++) {
        let currDif = ema12[i] - ema26[i];
        dif.push(currDif);
    }
    let dea = calculateEMA(dif, 9);
    let macd = [];
    for (let i = 0; i < values.length; i++) {
        let currMacd = (dif[i] - dea[i]) * 2;
        macd.push(currMacd.toFixed(2));
    }
    return { dif, dea, macd };
}


/**
 * 展示股票图表
 */
function showEchartsByStock(domId, code) {
    var myChart = echarts.init(document.getElementById(domId));
    myChart.showLoading()
    $.get('/stock/get', {code: code}, function (res) {
        myChart.hideLoading();  // 隐藏 loading 效果

        var data = res.data;
        var name = data.name
        var preKPrice = data.preKPrice
        var stocklist =  data.klines;
        var dateCategory = stocklist.map((item) => { return item.date })
        var macd = calculateMACD(stocklist.map((item) => { return parseFloat(item.close) }))
    
        // 指定图表的配置项和数据
        /** @type EChartsOption */
        var option = {
            legend: {
                data: ['MA70', 'MA250']
            },
            visualMap: [
                {
                    show: false,
                    seriesIndex: 4,
                    dimension: 2,
                    pieces: [
                        {
                            value: 1,
                            color: UP_COLOR,
                        },
                        {
                            value: -1,
                            color: DOWN_COLOR
                        }
                    ]
                },
            ],
            grid: [
                {
                    left: '10%',
                    right: '8%',
                    height: '180px'
                },
                {
                    left: '10%',
                    right: '8%',
                    top: '250px',
                    height: '50px'
                },
                {
                    left: '10%',
                    right: '8%',
                    top: '300px',
                    height: '50px'
                }
            ],
            xAxis: [{
                type: "category",
                data: dateCategory,
                show: false,
                axisLabel: {
                    show: false
                }
            }, {
                type: "category",
                gridIndex: 1,
                show: false,
                data: dateCategory,
                axisLabel: {
                    show: false
                }
            }, {
                type: "category",
                gridIndex: 2,
                data: dateCategory,
                axisLabel: {
                    show: true
                }
            }],
            yAxis: [{
                scale: true,
                splitArea: {
                    show: true,
                },
            }, {
                scale: true,
                gridIndex: 1,
                axisLabel: { show: false },
                axisLine: { show: false },
                axisTick: { show: false },
                splitLine: { show: false }
            }, {
                scale: true,
                gridIndex: 2,
                axisLabel: { show: false },
                axisLine: { show: false },
                axisTick: { show: false },
                splitLine: { show: false }
            }
            ],
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'cross',
                },
                formatter: function (params) {

                    console.log('tooltip event', params[0].axisValue, params[0].data)

                    switch (params[0].componentIndex) {
                        case 0:
                            return `
                            <table style="font-size: 10px">
                                <tr>
                                    <td>日期</td>
                                    <td align="right">${params[0].axisValue}</td>
                                </tr>
                                <tr>
                                    <td>开盘价</td>
                                    <td align="right"><span>${params[0].data[1]}</span></td>
                                </tr>
                                <tr>
                                    <td>最高价</td>
                                    <td align="right"><span style="color: ${params[0].data[4] > params[0].data[1] ? UP_COLOR : DOWN_COLOR}">${params[0].data[4]}</span></td>
                                </tr>
                                <tr>
                                    <td>最低价</td>
                                    <td align="right"><span style="color: ${params[0].data[3] > params[0].data[1] ? UP_COLOR : DOWN_COLOR}">${params[0].data[3]}</span></td>
                                </tr>
                                <tr>
                                    <td>收盘价</td>
                                    <td align="right"><span style="color: ${params[0].data[2] > params[0].data[1] ? UP_COLOR : DOWN_COLOR}">${params[0].data[2]}</span></td>
                                </tr>
                                <tr>
                                    <td>涨跌幅</td>
                                    <td align="right"><span style="color: ${params[0].data[8] > 0 ? UP_COLOR : DOWN_COLOR}">${params[0].data[8]}%</span></td>
                                </tr>
                                <tr>
                                    <td>振幅</td>
                                    <td align="right"><span>${params[0].data[7]}%</span></td>
                                </tr>
                                <tr>
                                    <td>成交量</td>
                                    <td align="right">${params[0].data[5] / 10000}万</td>
                                </tr>
                                <tr>
                                    <td>成交额</td>
                                    <td align="right">${params[0].data[6]}</td>
                                </tr>
                                <tr>
                                    <td>换手率</td>
                                    <td align="right">${params[0].data[9]}%</td>
                                </tr>
                            </table>
                            `;
                        case 3:
                            // return `成交量： ${params[0].data.value}<br/>`;
                            break;
                        default:
                            break;
                    }

                },
            },
            dataZoom: [
                {
                    type: 'inside',
                    show:false,
                    xAxisIndex: [0, 1, 2],
                    startValue: stocklist.length - DEFAULT_KLINE_NUM,
                    endValue: stocklist.length,
                },
                {
                    type: 'slider',
                    show:false,
                    xAxisIndex: [0, 1, 2],
                    startValue: stocklist.length - DEFAULT_KLINE_NUM,
                    endValue: stocklist.length,
                },
            ],
            series: [
                {
                    type: 'candlestick',
                    data: stocklist.map((item) => { return [item.open, item.close, item.low, item.high, item.volume, item.volume_money, item.fullrate, item.rate, item.hand_rate] }),
                    itemStyle: {
                        color: '#fff',
                    },
                    markPoint: {
                        label: {
                            formatter: function (param) {
                                return param.value;
                            }
                        },
                        data: [
                            { type: "max", valueDim: 'highest'},
                            { type: "min", valueDim: 'lowest' },
                        ],
                    },
                },
                {
                    name: 'MA70',
                    type: 'line',
                    data: MA(stocklist.map((item) => { return [item.close] }), 70),
                    smooth: true,
                    symbol: 'none',
                    lineStyle: {
                        opacity: 0.5,
                        width: 1.5,
                    },
                },
                {
                    name: 'MA250',
                    type: 'line',
                    data: MA(stocklist.map((item) => { return [item.close] }), 250),
                    smooth: true,
                    symbol: 'none',
                    lineStyle: {
                        opacity: 0.5,
                        width: 1.5,
                    },
                },
                {
                    name: '成交量',
                    type: 'bar',
                    data: stocklist.map((item) => { 
                        let color = item.close > item.open ? '#fff' : DOWN_COLOR
                        let borderColor = item.close > item.open ? UP_COLOR : DOWN_COLOR
                        return {value: item.volume, itemStyle: {color: color, borderColor: borderColor}} 
                    }),
                    xAxisIndex: 1,
                    yAxisIndex: 1,
                },
                {
                    name: 'macd',
                    type: 'bar',
                    barWidth: 1,
                    data: macd.macd.map((item, index) => { return [index, item, item > 0 ? 1 : -1] }),
                    xAxisIndex: 2,
                    yAxisIndex: 2,
                },
                {
                    name: 'dea',
                    type: 'line',
                    data: macd.dea,
                    xAxisIndex: 2,
                    yAxisIndex: 2,
                    smooth: true,
                    symbol: 'none',
                    lineStyle: {
                        opacity: 0.5,
                        width: 1.5,
                        color: "#ffa32a",
                    },
                },
                {
                    name: 'dif',
                    type: 'line',
                    data: macd.dif,
                    xAxisIndex: 2,
                    yAxisIndex: 2,
                    smooth: true,
                    symbol: 'none',
                    lineStyle: {
                        opacity: 0.5,
                        width: 1.5,
                        color: "#3787d7",
                    },
                },
            ]
    
        };
        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);

    });


    
}



function showStock(domId, code) {
    // 初始化图表
    var chart = klinecharts.init(domId)
    chart.setLocale('zh-CN')

    // 创建一个主图技术指标
    chart.createIndicator('MA', false, { id: 'candle_pane' })
    // 创建一个副图技术指标VOL
    chart.createIndicator('VOL', false, {id: "vol_panne"})
    // 创建一个副图技术指标MACD
    chart.createIndicator('MACD', false, {id: "macd_panne"})


    $.get('/stock/get', {code: code}, function (res) {
        var data = res.data;
        var name = data.name
        var preKPrice = data.preKPrice
        var stocklist =  data.klines;

        console.log(stocklist)

        // 加载数据
        var chartDataList = stocklist.map(function (data) {
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

        chart.applyNewData(chartDataList)
    })
    
}