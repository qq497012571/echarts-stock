@extends('layouts.content')

<div class="layui-container">
    <div class="layui-row">
        <div id="qrcode"></div>
    </div>
</div>

@section('script')
<script>

    loadQrCode();

    function loadQrCode() {
        var code;
        $.post('/api/user_profile/qrcode', {}, function(res) {
            var qrcode = res.data.qrcode
            code = qrcode.match(/code=(.*)/)[1]
            $('#qrcode').qrcode({
                text: qrcode
            });

            watchQrCodeState(code)
        });
    }

    function watchQrCodeState(code) {
        var timer = setInterval(function() {
            $.get('/api/user_profile/qrcodeState', {
                code: code
            }, function(res) {
                // {"code":0,"msg":"success","data":{"result":{"success":true,"message":"","data":{"status":0},"result_code":200}}}
                var result = res.data.result.data;
                if (result.status == 4) {
                    clearInterval(timer)
                    return layer.alert('二维码已经失效, 请关闭窗口重新点击按钮!');
                }

                if (result.status == 2) {
                    clearInterval(timer)
                    return layer.alert('登录成功!', {end: function(){
                        parent.window.location.reload()
                    }})
                }

                console.log(res.data.result)
            });
        }, 2000)
    }
</script>
@endsection