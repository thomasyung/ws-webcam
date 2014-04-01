var WebSocketServer = require('ws').Server
  , http = require('http')
  , express = require('express')
  , app = express()
  , port = process.env.PORT || 5000;

app.use(express.static(__dirname + '/'));

var server = http.createServer(app);
server.listen(port);

console.log('http server listening on %d', port);

var wss = new WebSocketServer({server: server});
console.log('websocket server created');

wss.broadcast = function(data) {
    for(var i in this.clients)
        this.clients[i].send(data);
};

wss.on('connection', function(ws) {
  console.log('websocket connection open');
  var obj = {};
  obj.evt = 'connection';
  obj.message = 'Connection made...';
  ws.send(JSON.stringify(obj));
  ws.on('close', function() {
    console.log('websocket connection close');
  });
  ws.on('message', function(message){
    wss.broadcast(message);
  });
});
