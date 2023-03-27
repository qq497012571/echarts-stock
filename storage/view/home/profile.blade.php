@extends('layouts.content')

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>账号信息</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">邮箱</label>
            <div class="layui-input-inline">
                <input type="text" name="email" lay-verify="email" autocomplete="off" class="layui-input layui-disabled" value="{{$user['email'] ?? ''}}">
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">雪球cookie</label>
        <div class="layui-input-block">
            <textarea placeholder="请输入内容" class="layui-textarea" readonly>{{$user['xueqiu_cookie'] ?? ''}}</textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button type="submit" class="layui-btn" lay-submit="" lay-filter="save-submit">保存</button>
            <button class="layui-btn" id="create-cookie" type="button">雪球APP扫码登录</button>
        </div>
    </div>
</form>

@section('script')
<script type="module" src="/app/profile-main.js"></script>
@endsection