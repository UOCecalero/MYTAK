// var app = require('express')();
// var http = require('http').Server(app);
// var io = require('socket.io')(http);
var io = require('socket.io').listen(61001);
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
redis.subscribe('messages', 'alerts', 'admin', function (err, count) {
  // Now we are subscribed to both the 'news' and 'music' channels.
  // `count` represents the number of channels we are currently subscribed to.
});

    //   app.get('/', function(req, res) {
    //   res.sendFile(__dirname + '/index.html');
    // });

    redis.on('message', function (channel, message) {
                  // Receive message Hello world! from channel news
                  // Receive message Hello again! from channel music
                  console.log('Receive message %s from channel %s', message, channel);
                  
        });


    //Whenever someone connects this gets executed
    io.on('connection', function(socket) {
       console.log('A user connected in socket: '+socket);
      var sessionid = socket.id;

         socket.on('clientConnected', function(id) {
            console.log("User: "+id+" SocketID: "+sessionid);
            redis.set(id, sessionid);
            var userID = id;

             });

         redis.on('message', function (channel, message) {
                  // Receive message Hello world! from channel news
                  // Receive message Hello again! from channel music
                  console.log('Receive message %s from channel %s', message, channel);
                  


                switch(channel){

                  case 'messages':  


                                    redis.get(message.receptor, function(err, result){
                        
                                          if (err){ console.log("Error :"+err); }
                                          else { 

                                                var destinyID = result;
                                                console.log("Message to socket: "+result);
                                                socket.broadcast.to(destinyID).emit('privateMessage', message);
                                          }    

                                    });



                  break;

                  case 'alerts':

                                  socket.broadcast.emit('alertMessage', message);


                  break;

                  // case 'admin':

                  //                 socket.broadcast.emit('adminMessage', message);


                  // break;

                };


        });

         //Whenever someone disconnects this piece of code executed
         socket.on('disconnect', function () {
            redis.del();
            console.log('A user disconnected');

            //Hacer un ping a todos los usuarios de la lista de sockets o bien comparar todos los sockets conectados con los que tenemos en la lista

         });


    });


   // http.listen(61000, function() {
    //   console.log('listening on *:61001');
   // });
