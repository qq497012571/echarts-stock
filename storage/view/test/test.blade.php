@extends('layouts.content')
<div class="layui-fluid" style="padding: 10px;">
    <div class="layui-row">
        <div id="test-demo" style="width:1200px;height:400px"></div>
        <button onclick="ak()" id="draw">矩形</button>
    </div>
</div>
@section('script')
<script type="module" src="/app/my.js"></script>
@endsection