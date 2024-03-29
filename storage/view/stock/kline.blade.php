@extends('layouts.content')
@section('style')
<style>
    .red {
        color: #ef5350;
    }

    .green {
        color: #26a69a;
    }

    .layui-fluid {
        padding: 10px;
        background-color: #2a2e39;
    }

    .header-tool-bar {
        background-color: #131722;
        margin-bottom: 5px;
        color: #d1d4dc;
    }

    .klinecharts-pro-period-bar {
        display: flex;
        flex-direction: row;
        align-items: center;
        box-sizing: border-box;
        height: 38px;
        width: 100%;
    }

    .klinecharts-pro-period-bar .item {
        transition: all .2s;
        box-sizing: border-box;
        cursor: pointer;
    }

    .klinecharts-pro-period-bar .period {
        padding: 7px 9px;
        margin: 0 4px;
        border-radius: 2px;
    }

    .klinecharts-pro-period-bar .period:hover {
        background-color: #2a2e39;
    }

    .klinecharts-pro-period-bar .item.selected {
        color: #1e53c9;
    }

    .klinecharts-pro-period-bar .tools {
        padding: 7px 9px;
        margin: 0 4px;
        border-radius: 2px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -o-user-select: none;
        user-select: none;
    }

    .klinecharts-pro-period-bar .tools:hover {
        background-color: #2a2e39;
    }

    .klinecharts-pro-period-bar .period+.tools {
        border-left: solid 1px #2a2e39;
        margin-left: 8px;
    }

    .right-container {
        height: 90%;
    }

    .right-box {
        width: 100%;
        height: 90%;
        background-color: red;
    }

    .right-bar-box {
        display: flex;
        flex-direction: column;
        background-color: #131722;
        height: 90%;
        align-items: center;
        box-sizing: border-box;
    }

    .right-bar-box span {
        height: 40px;
        width: 100%;
        text-align: center;
        line-height: 40px;
        border-radius: 5%;
    }

    .right-bar-box i {
        font-size: 26px;
        color: white;
        cursor: pointer;
    }

    .right-bar-box span:hover {
        background-color: #1e53c9;
    }

    .right-bar-box .select {
        background-color: #1e53c9;
    }

    .stock-list-container {
        width: 100%;
        height: 95%;
        background-color: #131722;
        overflow: auto;
    }

    .stock-list-container::-webkit-scrollbar {
        width: 3px;
    }

    .stock-list-container::-webkit-scrollbar-track {
        background-color: black;
    }

    .stock-list-container::-webkit-scrollbar-thumb {
        background: #4e4e4e;
        border-radius: 25px;
    }

    .list-item {
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        color: red;
    }

    .list-item li {
        border-bottom: 1px solid #2a2e39;
        color: white;
        cursor: pointer;
    }

    .list-item li.selected {
        background-color: #19567f;
    }

    .list-item li:hover {
        background-color: #19567f;
    }

    .list-item li .left {
        float: left;
        padding: 5px;
    }

    .list-item li .right {
        float: right;
        padding: 5px;
    }

    .stock-list-header {
        display: flex;
        background-color: #131722;
        color: white;
        font-size: 18px;
        border-bottom: 1px solid #2a2e39;
    }

    .stock-list-header .layui-icon-addition {
        font-size: 18px;
    }

    .stock-list-header span {
        display: block;
        padding: 9px;
        cursor: pointer;
    }

    .stock-list-header span:hover {
        background-color: #19567f;
    }

    .stock-table {
        width: 100%;
        color: white;
        text-align: center;
        background-color: #131722;
    }

    .stock-table thead {
        border-bottom: 1px solid gray;
        color: #363a45;
    }

    .stock-table td {
        border-right: 1px solid gray;
    }

    .stock-table tr:hover {
        background-color: #19567f;
    }
</style>
@endsection
<div class="layui-fluid">
    <div class="layui-row header-tool-bar">
        <div class="klinecharts-pro-period-bar">
            <span class="item period selected" key="day">日K</span>
            <span class="item period" key="week">周</span>
            <span class="item period" key="60m">60分</span>
            <span class="item period" key="30m">30分</span>
            <span class="item period" key="15m">15分</span>
            <span class="item period" key="5m">5分</span>
            <span class="item period" key="1m">1分</span>
            <div class="item tools draw-bar" key="draw-alarm-line">
                <span>
                    <i class="item iconfont icon-naozhong1"></i>警报
                </span>
            </div>
            <div class="item tools draw-bar" key="draw-back-line">
                <span>
                    <i class="item iconfont icon-hanhan-01-01"></i>回放
                </span>
            </div>
            <div class="item tools draw-bar" key="draw-next-line">
                <span>
                    <i class="item iconfont icon-btn_nextstep"></i>下一步
                </span>
            </div>
        </div>
    </div>

    <div class="layui-row layui-col-space4">
        <div class="layui-col-md10">
            <div id="kline-charts" style="height: 90%;background-color: #161a25;"></div>
        </div>
        <div class="layui-col-md2 layui-col-space1">
            <div class="layui-col-md12">
                <div class="right-container">
                    <div class="stock-list-header">
                        <span id="add_stock"><i class="layui-icon layui-icon-addition"></i></span>
                    </div>
                    <div class="stock-list-container" id="list-item-container">
                        <ul class="list-item" id="stock-list-box"></ul>
                    </div>
                </div>
            </div>
            <!-- <div class="layui-col-md2">
                <div class="right-bar-box">
                    <span class="select"><i class="item iconfont icon-yewubaobiao"></i></span>
                    <span><i class="item iconfont icon-naozhong1"></i></span>
                </div>
            </div> -->
        </div>
    </div>
</div>


@section('script')
<script id="list-item-tpl" type="text/html">
    @{{# layui.each(d.data, function(index, item){ }}
        <li id="stock-item-@{{item.code}}" code="@{{item.code}}" class="@{{# if(d.currentCode == item.code){ }}selected@{{# } }}">
            <div class="left">
                <p>@{{item.name}}</p>
                <p>@{{item.code}}</p>
            </div>
            @{{# if(item.percent>=0){ }}
                <div class="right red">
                    <p>@{{item.current}}</p>
                    <p>@{{item.percent}}%</p>
                </div>
                @{{# } else { }}
                    <div class="right green">
                        <p>@{{item.current}}</p>
                        <p>@{{item.percent}}%</p>
                    </div>
                    @{{# } }}
        </li>
        @{{# }); }}
</script>
<script type="module" src="/app/kline-main.js"></script>
@endsection