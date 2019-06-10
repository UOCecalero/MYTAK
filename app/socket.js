// var app = require('express')();
// var http = require('http').Server(app);
// var io = require('socket.io')(http);
var io = require('socket.io').listen(61001);
const RedisServer = require('redis-server');
var Redis = require("ioredis"); 

var redis = new Redis(); //Cliente Redis en modo normal
var pubredis = new Redis(); //Cliente Redis en modo pub
const subredis = new Redis(); //Cliente redis en modo Sub
      subredis.subscribe(/** 'ports', **/'outcomeMessage', 'alerts', 'admin', function (err, count) {
        //Hacer algo con el error de redis al suscribirse
        //Coun devuelve el numero de canales a los que estamos suscritos
      });

// // Inicializamos el servidor redis
// const redisserver = new RedisServer(6379);

// //Si el servidor redis arranca sin problemas entonces
// redisserver.open((err) => {
//   if (err === null) { 
//   //Si el servidor redis da error mostramos el error
//   } else {console.log(err);}
//   });
 
    //Cuando alguien se conecta
    io.on('connection', function(socket) {
      
      console.log('A user connected in socket: '+ socket.id);
      const sessionid = socket.id;

        //El usuario conectado se identifica a través del token para evitar suplantaciones
         socket.on('clientConnected', function(token) {
            console.log("User: "+token+" SocketID: "+sessionid+"\n");

            //Comprueba que no hay otro socket con la misma id. Si existe lo elimina.
            if (redis.exists(token)){ redis.del(token) }
            redis.set(token, sessionid);
            redis.expire(token, 60); //Le damos un tiempo de expiración al set (1min

             });

         //Al recibir un  mensaje por alguno de los canales devuelve el canal y el mensaje
         subredis.on('message', function (channel, message) {
                  // Receive message Hello world! from channel news
                  // Receive message Hello again! from channel music
                  console.log('Receive message %s in channel %s ', message, channel);

                      //En fucnión del canal Redis por el que nos evíe Laravel
                      switch(channel){

                        case 'outcomeMessage':  
                        //Si es para el canal outcomeMessage, comprobamos si receptor esta conectado

                              redis.get(message.receptor, function(err, result){
                                    
                                    if (err){ 
                                              //Si no esta conectado muestra un error por consola
                                              console.log("Error :"+err); 

                                    } else { 

                                          //Si su id (token) esta en nuestra BBDD Redis, nos devuelve el socket 
                                          const destinyID = result;
                                          console.log("Message to socket: "+result);
                                          socket.broadcast.to(destinyID).emit('privateMessage', function(message){

                                              //Aqui ya se puede actualizar message con el checked == true
                                              //Despues indicar al emisor que ha sido recibido para que cambie el tick

                                          });
                                    }    

                              });

                        break;

                        case 'alerts':
                                        socket.broadcast.emit('alertMessage', message);
                        break;

                        case 'admin':
                                        socket.broadcast.emit('adminMessage', message);
                        break;

                    }

        });

         //Esto es un mensaje enviado por el cliente
         socket.on('privateMessage', function(tokenAndMessage) {

            console.log("Message :"+tokenAndMessage);
            //Aqui hay que un mensaje por el canal de entrada de mensajes para que lo procese Laravel y lo lance por el canal messages (el canal de salida de mensajes si lo considera oportino después de almacenar el mensaje y hacer las comprobaciones pertinentes) 
            pubredis.publish('incomeMessage', tokenAndMessage);

         });

         socket.on('clientDisconnected', function( token ) {

            //Aqui hay que un mensaje por el canal de entrada de mensajes para que lo procese Laravel y lo lance por el canal messages (el canal de salida de mensajes si lo considera oportino después de almacenar el mensaje y hacer las comprobaciones pertinentes) 
            redis.del(token);

         });

         //Whenever someone disconnects this piece of code executed
         socket.on('disconnect', function () {
            console.log('A user disconnected');
            subredis.del();
            redis.del();
         });


    });

