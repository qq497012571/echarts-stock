@extends('layouts.content')
@section('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.7/dist/css/autoComplete.min.css">
@endsection

<div class="layui-row">
    <div class="layui-row layui-col-space5">
        <button class="layui-btn layui-btn-sm" id="add-btn">添加自选</button>
    </div>
</div>

<table id="stock-table" lay-filter="stock-table"></table>

@section('script')

<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="detail">K线图</a>
    <a class="layui-btn layui-btn-xs" lay-event="del">删除</a>
</script>

<script type="module" src="/app/list-main.js"></script>
@endsection