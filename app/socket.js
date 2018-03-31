var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
const RedisServer = require('redis-server');
var Redis = require("ioredis");
 
// Inicializamos el servidor redis
const redisserver = new RedisServer(6379);

//Si el servidor redis arranca sin problemas entonces
redisserver.open((err) => {
  if (err === null) { 
  //Si el servidor redis da error mostramos el error
  } else {console.log(err);}
  });

//Creamos el cliente redis
const redis = new Redis();


    //   app.get('/', function(req, res) {
    //   res.sendFile(__dirname + '/index.html');
    // });


    //Whenever someone connects this gets executed
    io.on('connection', function(socket) {
       console.log('A user connected in socket: '+socket);
      var sessionid = socket.id;

       socket.on('clientID', function(id) {
          console.log("User: "+id+" SocketID: "+sessionid);
          redis.set(id, "socket:"+sessionid);
          var userID = id;

           });

       socket.on('clientDestiny', function(data) {
          console.log("Destiny: "+data);

            redis.get(data, function(err, result){
              if (err){console.log("Error :"+err);}
              else { var destinyID = result; }    
            });

           });

       socket.on('clientEvent', function(data) {
          console.log(data);
          console.log(data.emisor+" ha enviado un mensaje a "+data.receptor);
          redis.get(data.receptor, function(err, result){
              if (err){console.log("Error :"+err);}
              else { 

                    var destinyID = result;
                    console.log(result);
                    socket.broadcast.to(destinyID).emit('testerEvent', data);



              }    
            });
          

           });

         //Send a message after a timeout of 20seconds
       // setTimeout(function() {
       //    socket.emit('testerEvent', { description: 'A custom event named testerEvent!'});
       // }, 20000);

       //Whenever someone disconnects this piece of code executed
       socket.on('disconnect', function () {
          redis.del();
          console.log('A user disconnected');
       });
    });

    http.listen(61000, function() {
       console.log('listening on *:61000');
    });