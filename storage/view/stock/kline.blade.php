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
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" onclick="mark(1)">
                    <i class="layui-icon layui-icon-flag"></i>
                </button>
            </div>
            <div id="kline-charts" style="height: 80%"></div>
        </div>
    </div>
</div>
@section('script')
<script>

    var data = JSON.parse('@json($data)');
    var marks = JSON.parse('@json($marks)');
    var chart = new KlineCharts('kline-charts')
    chart.data(data.klines)


    // createPriceLineOverlay


    function mark(markType = 1) {
        var code = '{{$code}}';

        layer.prompt(function(value, index, elem){
            var option = chart.createPriceLineOverlay(value);
            $.post('/stock/addMark', {code: code, mark_type: markType, mark_option: JSON.stringify(option)}, function(res) {

            });
            layer.close(index);
        });
    }

</script>
@endsection