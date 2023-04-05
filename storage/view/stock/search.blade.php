@extends('layouts.content')
@section('style')
<style>
    html,
    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .search-container {
        background-color: #3b3e49;
        overflow: hidden;
    }

    .search-input-box {
        display: flex;
        flex-direction: row;
        align-items: center;
        background-color: #1e222d;
        width: 100%;
        color: #a3a6af;
    }

    .search-input {
        width: 100%;
        height: 40px;
        background: none;
        outline: none;
        border: none;
        font-size: 16px;
        color: #a3a6af;
    }

    .search-input-box i {
        color: white;
        font-size: 20px;
    }

    .search-list-box {
        color: aliceblue;
        list-style: none;
        padding: 0;
        margin: 0;
        height: 400px;
        overflow: auto;
    }

    .search-list-box li {
        position: relative;
        padding: 0px 30px;
        margin-top: 1px;
        display: flex;
        flex-direction: row;
        align-items: center;
        height: 40px;
        background-color: #1e222d;
    }

    .search-list-box li:hover {
        background-color: #2a2e39;
    }

    .search-list-box li .left {
        position: absolute;
        left: 20px;
    }

    .search-list-box li .right {
        position: absolute;
        right: 20px;
    }

    .search-list-box li i {
        font-size: 20px;
    }

    .search-list-box li span {
        padding: 5px;
        color: #2962ff;
        cursor: pointer;
    }

    .search-list-box li span:hover {
        background-color: #1e222d;
    }

    .search-list-box::-webkit-scrollbar {
        width: 3px;
    }

    .search-list-box::-webkit-scrollbar-track {
        background-color: #1e222d;
    }

    .search-list-box::-webkit-scrollbar-thumb {
        background: #4e4e4e;
        border-radius: 25px;
    }
</style>
@endsection

@section('content')
<div class="search-container">
    <div class="search-input-box">
        <i class="iconfont icon-sousuo"></i>
        <input type="text" class="search-input" placeholder="搜索" autocomplete="false">
    </div>
    <ul class="search-list-box" id="search-list-box">

    </ul>
</div>
@endsection

@section('script')
<script id="list-item-tpl" type="text/html">
    @{{# layui.each(d.data, function(index, item){ }}
        <li>
            <div class="left">@{{item.name}}/@{{item.code}}</div>
            <div class="right">
                <span>
                    @{{# if(item.hasexist != 1){ }}
                        <i class="iconfont icon-plus action" code="@{{item.code}}" exchange="@{{item.exchange}}" state="@{{item.state}}"></i>
                        @{{# } else { }}
                            <i class="iconfont icon-chacha action" code="@{{item.code}}" exchange="@{{item.exchange}}" state="@{{item.state}}"></i>
                            @{{# } }}
                </span>
            </div>
        </li>
        @{{# }); }}
</script>
<script>
    layui.use(['laytpl', 'flow', 'jquery'], function() {
        var laytpl = layui.laytpl;
        var flow = layui.flow;
        var $ = layui.jquery;
        var serachUrl = '/api/stock/search';

        $('.search-input').focus();

        $('.search-input').on('change', function(e) {
            var code = $(this).val();
            var limit = 15;
            $('#search-list-box').html('')

            flow.load({
                elem: '#search-list-box',
                scrollElem: '#search-list-box',
                isAuto: true,
                done: function(page, next) { //执行下一页的回调
                    console.log('search', page)
                    $.get(serachUrl, {
                        code: code,
                        limit: limit,
                        page: page,
                    }, function(res) {
                        console.log('search', res)
                        var data = res.data
                        laytpl($('#list-item-tpl').html()).render({
                            data,
                        }, function(html) {
                            next(html, data.length == limit)

                        });
                    });
                }
            });
        });


        $(document).on('click', '.action', function() {
            const that = $(this);
            const code = $(this).attr('code');
            const state = $(this).attr('state');
            const exchange = $(this).attr('exchange');
            const actionAdd = $(this).hasClass('icon-plus');

            if (['SH', 'SZ', 'CN'].indexOf(exchange) === -1) {
                return layer.msg('只允许添加A股相关股票');
            }

            if (actionAdd) {
                if(state == 3) {
                    return layer.msg('该股票已退市');
                }
                $.post('/api/user_stock/add', {
                    code: code
                }, function(res) {
                    sendMsg('add_stock_notify', {})
                    that.removeClass('icon-plus').addClass('icon-chacha');
                });
            } else {
                $.post('/api/user_stock/cancel', {
                    code: code
                }, function(res) {
                    sendMsg('del_stock_notify', {})
                    that.removeClass('icon-chacha').addClass('icon-plus');
                });
            }

        });
    });
</script>
@endsection