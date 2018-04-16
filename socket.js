var server = require('http').Server();
var io = require('socket.io')(server);

var Redis = require('ioredis');
var redis = new Redis();

redis.subscribe('test-channel', function(err, count) {
    //
});

redis.on('message', function(channel, message) {
    console.log("Received");       // gets printed
    message = JSON.parse(message);
    console.log(channel + ':' + message.event, message.data);
    io.emit(channel + ':' + message.event, message.data);
});

io.on('connection', function(socket){
  console.log("connected");
  io.emit('user connected');
});

server.listen(6001);