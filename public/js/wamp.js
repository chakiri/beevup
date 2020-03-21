var currenttopic = document.getElementById("chatPlateform").dataset.topic;
console.log('current topic : ' + currenttopic);
ab.debug(true, true);
var conn = new ab.Session('ws://127.0.0.1:8080',
    function() {
        console.log('Connection established on ' + currenttopic);
        conn.subscribe(currenttopic, function(topic, data) {
            // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
            console.log('New message published by ' + data.user + ' to topic ' + data.topic + ' : ' + data.message);
            addMessageToCanvas(data);
        });
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);

function addMessageToCanvas(data){

    const chatContent = document.getElementById("chat");
    //Get message HTML
    const messageHTML = "<div class='message'><strong>" + data.user + " : </strong>" + data.message + "</div>";

    //insert messageHTML in the chat
    chatContent.innerHTML += messageHTML;
}