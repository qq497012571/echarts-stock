@extends('layouts.content')
@section('style')
<style>
    .layui-fluid {
        padding: 10px;
        background-color: #2a2e39
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
    }

    .klinecharts-pro-period-bar .tools:hover {
        background-color: #2a2e39;
    }

    .klinecharts-pro-period-bar .period+.tools {
        border-left: solid 1px #2a2e39;
        margin-left: 8px;
    }

    .right-box {
        width: 100%;
        height: 90%;
        /* background-color: #131722; */
        background-color: red;
    }

    .right-bar-box {
        display: flex;
        flex-direction: column;
        /* text-align: center; */
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


    .list-item {
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
        color: red;
    }

    .list-item li {
        height: 50px;
        width: 100%;
        border-bottom: 1px solid #2a2e39;
        color: white;
        cursor: pointer;
    }


    .list-item li:hover {
        background-color: #19567f;
    }

    .list-item li .left {
        float: left;
        margin-top: 5px;
        padding: 5px;
    }

    .list-item li .right {
        float: right;
        margin-top: 5px;
        padding: 5px;
    }
</style>
@endsection
<div class="layui-fluid">
    <div class="layui-row layui-col-space5 header-tool-bar">
        <div class="klinecharts-pro-period-bar">
            <span class="item period selected" key="day">日K</span>
            <span class="item period" key="week">周</span>
            <span class="item period" key="60m">60分</span>
            <span class="item period" key="30m">30分</span>
            <span class="item period" key="15m">15分</span>
            <span class="item period" key="5m">5分</span>
            <span class="item period" key="1m">1分</span>
            <div class="item tools draw-bar" key="draw-alarm-line">
                <i class="item iconfont icon-naozhong1 " style="font-size: 16px">警报</i>
            </div>
        </div>
    </div>

    <div class="layui-row layui-col-space4">
        <div class="layui-col-md10">
            <div id="kline-charts" style="height: 90%;background-color: #161a25;"></div>
        </div>
        <div class="layui-col-md2 layui-col-space1">
            <div class="layui-col-md10">
                <div style="height: 90%; width:100%; background-color:#131722;overflow:auto">
                    <ul class="list-item">
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                        <li>
                            <div class="left">
                                <p>纯碱2305</p>
                                <p>sh600001</p>
                            </div>
                            <div class="right">
                                <p>16516</p>
                                <p>-8%</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="layui-col-md2">
                <div class="right-bar-box">
                    <span><i class="item iconfont icon-yewubaobiao"></i></span>
                    <span><i class="item iconfont icon-naozhong1"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>


@section('script')
<script type="module" src="/app/kline-main.js"></script>
@endsection