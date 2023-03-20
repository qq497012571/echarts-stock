$(function () {

    var socket = io('ws://localhost:9502', {
        transports: ["websocket"],
        // query: "token=" + token
    });

    socket.on('connect', data => {
        console.log('服务器连接成功!');
        // socket.emit('event', 'hello, hyperf', console.log);
        socket.emit('join-room', 'room1', console.log);
        // setInterval(function () {
        //     socket.emit('say', '{"room":"room1", "message":"Hello Hyperf."}');
        // }, 1000);
    });


    socket.on('event', data => {
        console.log("client => ", data)
    });
});

