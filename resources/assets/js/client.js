const PRIVATE_CHANNEL = 'gate';

//

var io = require('socket.io-client');

var host = window.location.host.split(':')[0];
var socket = io.connect('//' + host + ':8000', {secure: true, rejectUnauthorized: false});

socket.on('connect', function () {
    console.log('CONNECT');
    
    socket.on('event', function (data) {
        console.log('EVENT', data);
    });
    
    socket.on('messages.new', function (data) {
        console.log('NEW PRIVATE MESSAGE', data);
    });
    
    socket.on('disconnect', function () {
        console.log('disconnect');
    });
    
    // Kick it off
    // Can be any channel. For private channels, Laravel should pass it upon page load (or given by another user).
    socket.emit('subscribe-to-channel', {channel: PRIVATE_CHANNEL});
    console.log('SUBSCRIBED TO <' + PRIVATE_CHANNEL + '>');
})