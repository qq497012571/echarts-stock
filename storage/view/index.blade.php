<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hyperf</title>
</head>
<body>
<form action="" method="post">
    <input type="text">
</form>
<script src="https://cdn.bootcdn.net/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
<script src="/js/main.js"></script>
<script>
    var socket = io('ws://127.0.0.1:9502', { transports: ["websocket"] , query: 'token=xxxxx'});
    socket.on('connect', data => {
        socket.emit('event', 'hello, hyperf', console.log);
        socket.emit('join-room', 'room1', console.log);
        setInterval(function () {
            socket.emit('say', '{"room":"room1", "message":"Hello Hyperf."}');
        }, 1000);
    });
    socket.on('event', console.log);
</script>
</body>
</html>