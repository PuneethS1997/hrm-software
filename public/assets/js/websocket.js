const socket = new WebSocket("ws://localhost:8080");

socket.onopen = () => {
 console.log("Connected to chat server");
};

socket.onmessage = (event) => {

 const data = JSON.parse(event.data);

 if(data.type === "message"){
   appendMessage(data);
 }

 if(data.type === "typing"){
   showTyping(data.from);
 }

};
