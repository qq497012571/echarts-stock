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
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm draw-bar" key="draw-alarm-line">
                    <i class="iconfont icon-naozhong"></i>
                </button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm draw-bar" key="draw-line">
                    <i class="layui-icon layui-icon-flag"></i>
                </button>
                <button type="button" class="layui-btn layui-btn-primary layui-btn-sm draw-bar" key="draw-rect">
                    <i class="iconfont icon-juxing"></i>
                </button>
            </div>
            <div id="kline-charts" style="height: 90%"></div>
        </div>
    </div>
</div>


@section('script')
<script type="module" src="/app/kline-main.js"></script>
@endsection