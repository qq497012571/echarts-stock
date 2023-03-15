@extends('layouts.content')


<div class="layui-fluid" style="padding: 10px;">
    <div class="layui-row">
        <div class="layui-col-md12">

            <div class="layui-btn-group ma-bars-box">
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="day">日k</button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="week">周k</button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="60m">60分</button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="30m">30分</button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="15m">15分</button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="5m">5分</button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" key="1m">1分</button>
            </div>

            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" id="add-mark">
                    <i class="layui-icon layui-icon-flag"></i>
                </button>
            </div>
            <div id="kline-charts" style="height: 80%"></div>
        </div>
    </div>
</div>
@section('script')
<script>
    $(function() {

        var code = getUrlQuery('code');
        var chart = new KlineCharts('kline-charts')

        loadCharts(code, 'day')

        $('.ma-bars-box button').on('click', function(){
            var ma = $(this).attr('key');
            loadCharts(code, ma)
        });

        $('#add-mark').on('click', function() {
            layer.prompt(function(value, index, elem) {
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
        })


        function loadCharts(code, ma) {

            chart.chart.clearData()

            $.when(fetchKlines(code, ma), fetchMarks(code)).done(function(d1, d2) {
                var klines = d1[0].data;
                var marks = d2[0].data;
                klines.length && chart.data(klines);
                marks.length && marks.map((m) => {
                    chart.createPriceLineOverlayByOption(JSON.parse(m.mark_option))
                });
            });

        }
    })
</script>
@endsection