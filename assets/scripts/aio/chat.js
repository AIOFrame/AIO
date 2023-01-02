let server;

function chat_init() {
    server = new WebSocket('ws://localhost:8080/chat');
    server.onmessage = function(e) { console.log(e.data); };
    server.onopen = function(e) { conn.send('Hello Me!'); };
}

function open() {
    //server.onopen(  )
}

function send( message ) {
    server.send( JSON.stringify({'msg':message,'data':data}));
}

function load( messages ) {
    //server.
}