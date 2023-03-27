@extends('layouts.content')
@section('content')
<div class="layui-container">
    <form class="layui-form" action="" lay-filter="alarm_form">
        <div class="layui-form-item">
            <label class="layui-form-label">条件</label>
            <div class="layui-input-inline">
                <select name="timing_type" lay-verify="required" class="layui-disabled">
                    <option value="1">升破</option>
                    <option value="2">跌破</option>
                </select>
            </div>
            <div class="layui-input-inline">
                <input type="text" class="layui-input layui-disabled" name="price" readonly>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">触发</label>
            <div class="layui-input-inline">
                <input type="radio" name="time_type" value="ONCE" title="仅一次" checked>
                <input type="radio" name="time_type" value="ERVER" title="每次">
            </div>
            <div class="layui-form-mid layui-word-aux">警报仅会被触发一次, 不会重复</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">到期时间</label>
            <div class="layui-input-inline">
                <input type="text" name="expire_time" class="layui-input" id="expire_time">
            </div>
            <div class="layui-form-mid layui-word-aux">默认7天后过期</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">警报名称</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" name="title">
            </div>
        </div>
        <div class="layui-form-item layui-form-text">
            <label class="layui-form-label">消息</label>
            <div class="layui-input-block">
                <textarea name="remark" placeholder="请输入消息内容" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name="overlay_id">
                <button type="button" class="layui-btn layui-btn-primary" onclick="parent.layer.close(parent.layer.getFrameIndex(window.name))">关闭</button>
                <button class="layui-btn" lay-submit lay-filter="alarm_form_submit" id="alarm_form_submit">保存</button>
            </div>
        </div>
    </form>
</div>


@endsection

@section('script')
<script>
    //Demo
    layui.use(['form', 'laydate'], function() {
        var form = layui.form;
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            type: 'datetime',
            elem: '#expire_time',
            format: 'yyyy-MM-dd HH:mm',
            value: new Date(parseInt(parseInt((new Date().getTime() / 1000)) + (7 * 86400) + '000')),
        });

        //提交
        form.on('submit(alarm_form_submit)', function(data) {
            $.post('/api/stock/saveAlarm', data.field).done(res => {
                parent.layer.msg('创建成功')
                parent.layer.close(parent.layer.getFrameIndex(window.name));
            });
            return false;
        });

        $(window).on('keyup', e => {
            if (e.target != window.document.body) {
                return;
            }
            if (e.keyCode == 32 || e.keyCode == 13) {
                $('#alarm_form_submit').trigger('click');
            }
        });

        listenMsg((type, payload) => {
            type == 'alarm_form' && form.val("alarm_form", payload);
            console.log('listen data => ', type, payload)
        });

    });
</script>
@endsection