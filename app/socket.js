// var app = require('express')();
// var http = require('http').Server(app);
// var io = require('socket.io')(http);
var io = require('socket.io').listen(61001);
const RedisServer = require('redis-server');
var Redis = require("ioredis"); 

const redis = new Redis({
    port: 6379,          // Redis port
    host: '127.0.0.1',   // Redis host
    family: 4,           // 4 (IPv4) or 6 (IPv6)
    db: 1

  }); //Cliente Redis en modo clave valor

const pubredis = new Redis(); //Cliente Redis en modo pub
const subredis = new Redis({
    port: 6379,          // Redis port
    host: '127.0.0.1',   // Redis host
    family: 4,           // 4 (IPv4) or 6 (IPv6)
    db: 0

  }); //Cliente redis en modo Sub
      subredis.subscribe('outcomeMessage', 'alerts', 'admin', function (err, count) {
        //Hacer algo con el error de redis al suscribirse
        console.log( "Listening in "+count+" channels");
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
            // console.log("User: "+token+" SocketID: "+sessionid+"\n");

            const object = { "token" : token, "port" : sessionid } ;
            const JSONObject = JSON.stringify(object);
            console.log("UserConnected :"+JSONObject );

            //Manda el token por el canal de puertos para hacer algunas comprobaciones. Si se pasan es devuelto por setPorts
            pubredis.publish('ports', JSONObject);
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

                              console.log(message);
                              const messageObject = JSON.parse(message);
                              console.log("GET user:"+messageObject.receptor);
                              redis.get("user:"+messageObject.receptor)
                                    .then(function(result){ 
                                          //Si su id (token) esta en nuestra BBDD Redis, nos devuelve el socket 
                                          console.log("Message to socket: "+result);
                                          socket.broadcast.to(result).emit('privateMessage', message);
                                        })
                                    .catch(function(err){
                                              //Si no esta conectado muestra un error por consola
                                              console.log("Error al obtener el puerto de envío :"+err);
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

            // const tokenAndMessageJSON = JSON.stringify(tokenAndMessage);
            // const portJSON = JSON.stringify( {"port" : sessionid } );
            // const JSONObject = tokenAndMessageJSON + portJSON;

            tokenAndMessage['port'] = sessionid;
            const JSONObject = JSON.stringify( tokenAndMessage );

            console.log("Message :"+JSONObject );
            //Aqui hay que un mensaje por el canal de entrada de mensajes para que lo procese Laravel y lo lance por el canal messages (el canal de salida de mensajes si lo considera oportino después de almacenar el mensaje y hacer las comprobaciones pertinentes) 
            pubredis.publish('incomeMessage', JSONObject);

         });

         //Whenever someone disconnects this piece of code executed
         socket.on('disconnect', function () {

            redis.get("port:"+sessionid)
            .then(function(id){  
                redis.del("port:"+sessionid); 
                redis.del("user:"+id);
              })
            .catch(function(error){  console.log("ERROR :"+error) });

            // subredis.quit();
            // redis.quit();
         });


    });

