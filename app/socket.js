// var app = require('express')();
// var http = require('http').Server(app);
// var io = require('socket.io')(http);
var io = require('socket.io').listen(61001);
const RedisServer = require('redis-server');
var Redis = require("ioredis");
 
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
       console.log('A user connected in socket: '+socket.id);
      const sessionid = socket.id;
      var userID;
     

      //Creamos el cliente redis en modo suscripción(para ese socket) y lo suscribimos a los canales correspondientes
      const subredis = new Redis();
      subredis.subscribe('messages', 'alerts', 'admin', function (err, count) {
      });

      //Creamos otro cliente para trabajar en modo normal
      const redis = new Redis();
      

        //El usuario conectado se identifica a través del token para evitar suplantaciones
         socket.on('clientConnected', function(token) {
            console.log("User: "+token+" SocketID: "+sessionid);
            //Primero hay que comprobar que no tenemos otro socket con la misma id. Si existe debe ser eliminado.
            if (redis.exists(token)){ redis.del(token) }
            redis.set(token, sessionid);

            //Aqui podemo añadir un tiempo de expiración si queremos para el regístro. PE. 1 minuto
            //De este modo cada minuto el cliente debe de enviar las credenciales para que le lleguen los mensajes
            //redis.expire(token, 60)

             });

         //Al recibir un  mensaje por alguno de los canales devuelve el canal y el mensaje
         subredis.on('message', function (channel, message) {
                  // Receive message Hello world! from channel news
                  // Receive message Hello again! from channel music
                  console.log('Receive message %s from channel %s', message, channel);
                  

                      //En función del canal al que vaya destinado, hacemos una cosa u otra
                      switch(channel){

                        case 'messages':  
                        //Si es para el canal messages, comprobamos si receptor esta conectado

                                          redis.get(message.receptor, function(err, result){
                                                
                                                if (err){ 
                                                          //Si no esta conectado muestra un error por consola
                                                          console.log("Error :"+err); 

                                                } else { 

                                                      //Si su id esta en nuestra BBDD Redis, nos devuelve el socket 
                                                      var destinyID = result;
                                                      console.log("Message to socket: "+result);
                                                      socket.broadcast.to(destinyID).emit('privateMessage', function(message){

                                                          //Aqui ya se puede actualizar message con el checked == true
                                                          //Despues actualizar la base de datos
                                                          //Despues indicar al emisor que ha sido recibido para que cambie el tick


                                                      });
                                                }    

                                          });



                        break;

                        case 'alerts':

                                        socket.broadcast.emit('alertMessage', message);


                        break;

                        // case 'admin':

                        //                 socket.broadcast.emit('adminMessage', message);


                        // break;

                    }

        });

         //Whenever someone disconnects this piece of code executed
         socket.on('disconnect', function () {
            console.log('A user disconnected');
            subredis.del();
            redis.del();


         });


    });


   // http.listen(61000, function() {
    //   console.log('listening on *:61001');
   // });
