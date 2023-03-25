
$(function () {


    layui.use(['form', 'layer'], function () {
        var form = layui.form;
        var layer = layui.layer;

        $('#create-cookie').click(function () {
            layer.open({
                type: 2,
                title: '用雪球APP扫码登录',
                skin: 'layui-layer-rim', //加上边框
                area: ['292px', '330px'], //宽高
                content: '/home/qrcode'
            });
        })
    })

})