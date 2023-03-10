@extends('layouts.app')

@section('content')
<div>
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">token</label>
            <div class="layui-input-inline">
                <input type="text" name="token" required lay-verify="required" placeholder="请输入token" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-input-inline">
                <button class="layui-btn" lay-submit lay-filter="formDemo" id="login-btn">立即提交</button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    layui.use(['form', 'layer'], function() {
        var form = layui.form;
        var layer = layui.layer
        //提交
        form.on('submit(formDemo)', function(data) {

            $.post('/user/login', data.field, function(res){
                console.log(111111111,res)
                $.cookie('access_token', res.data.token)

                if (res.code == 200) {
                    layer.msg('登录成功')
                } else {
                    layer.msg('登录失败')
                }

                // if (data.data.token) {
                //     layer.msg('登录成功')
                // } else {
                //     layer.msg('登录失败')
                // }
            },'json');

            return false;
        });


    });
</script>
@endsection