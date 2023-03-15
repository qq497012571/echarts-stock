@extends('layouts.content')


<div class="layui-fluid" style="padding: 10px;">
    <div class="layui-row">
        <div class="layui-col-md12">
            <div class="layui-btn-group">
                @foreach($stock_ma_btn as $btn)
                <a href="{{$btn['href']}}" class="{{$btn['class']}}">{{$btn['text']}}</a>
                @endforeach
            </div>

            <div class="layui-btn-group">
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="addMark(1)">
                    <i class="layui-icon layui-icon-flag"></i>
                </button>
            </div>
            <div id="kline-charts" style="height: 80%"></div>
        </div>
    </div>
</div>
@section('script')
<script>
    var data = @json($data, JSON_PRETTY_PRINT);
    var marks = @json($marks, JSON_PRETTY_PRINT);
    var chart = new KlineCharts('kline-charts')

    if (data.klines.length) {
        chart.data(data.klines)
        console.log('K线加载完毕...')
    }

    if (marks.length) {
        marks.map((v)=>{chart.createPriceLineOverlayByOption(JSON.parse(v.mark_option))})
        console.log('mark标记加载完毕...')
    }

    function addMark(markType = 1) {
        var code = '{{$code}}';
        layer.prompt(function(value, index, elem){
            var option = chart.createPriceLineOverlay(value);
            var addMarkPromise = $.post('/stock/addMark', {code: code, value: value, mark_type: markType, mark_option: JSON.stringify(option)});
            addMarkPromise.done(function(){
                layer.close(index);
            });
        });
    }

</script>
@endsection