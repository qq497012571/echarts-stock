@extends('layouts.content')


<div>
    <form class="layui-form" action="/login" method="POST">
        <div class="layui-form-item">
            <label class="layui-form-label">email</label>
            <div class="layui-input-inline">
                <input type="text" name="email" required lay-verify="required" placeholder="请输入email" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-input-inline">
                <button class="layui-btn" lay-submit lay-filter="formDemo" id="login-btn">立即提交</button>
            </div>
        </div>
    </form>
</div>