var Pusher = require('pusher-js/node');
var SerialPort = require('serialport');
var conf = require('config');
var fs = require('fs');

var sp = null;

var files = fs.readdirSync('/dev/');
files.forEach(function(file){
    if( file.match(/tty.usbmodem/) ){
        sp = new SerialPort('/dev/'+file, {
            baudRate: 115200
        });
    }
})

if( sp == null ){
    console.log("disconnected");
    return;
}else{
    var pusher = new Pusher(conf.app_id, {
        encrypted: true
    });
    var channel = pusher.subscribe('test_channel');
    channel.bind('my_event', function(data) {
        console.log(data.message);
        if( data.message.match(/^[0-9]{5}$/) ){
            sp.write(data.message);
        }
    });
}
