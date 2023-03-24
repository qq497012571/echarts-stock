@extends('layouts.content')

<div class="demoTable">
    搜索ID：
    <div class="layui-inline">
        <input class="layui-input" name="id" id="demoReload" autocomplete="off">
    </div>
    <button class="layui-btn layui-btn-sm">搜索</button>
    <button class="layui-btn layui-btn-sm" id="sync-btn">同步雪球</button>
</div>
<table id="sotck-table" lay-filter="stock-table"></table>

@section('script')

<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="detail">K线图</a>
    <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
</script>

<script type="module" src="/app/list-main.js"></script>
@endsection