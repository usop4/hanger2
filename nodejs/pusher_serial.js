var Pusher = require('pusher-js/node');
var SerialPort = require('serialport');

var sp = new SerialPort('/dev/tty.usbmodem1411', {
    baudRate: 115200
});

var pusher = new Pusher('558d88d3ce23e25aaf24', {
    encrypted: true
});

var channel = pusher.subscribe('test_channel');
channel.bind('my_event', function(data) {
    console.log(data.message);
    if( data.message.match(/^[0-9]{5}$/) ){
        sp.write(data.message);
    }
});
