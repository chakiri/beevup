var currentTopic = document.getElementById("chatPlateform").dataset.topic;
var currentUserFirstname = document.getElementById("userStatus").dataset.userfirstname;

console.log('current topic : ' + currentTopic);
updateScroll();

ab.debug(true, true);
var conn = new ab.Session('ws://127.0.0.1:8080',
    function() {
        console.log('Connection established on ' + currentTopic);
        conn.subscribe(currentTopic, function(topic, data) {
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

    console.log(currentUserFirstname);
    console.log(data.user);

    //Get message HTML
    if (currentUserFirstname === data.user){
         messageHTML = "<div class='message'><p class='text-right'><strong>Moi : </strong>" + data.message + "</p></div>";
    }else {
         messageHTML = "<div class='message'><p><strong>" + data.user + " : </strong>" + data.message + "</p></div>";
    }
    //insert messageHTML in the chat
    chatContent.innerHTML += messageHTML;

    //update scroll to the bottom
    updateScroll();
}

function updateScroll(){
    var element = document.getElementById("chat");
    element.scrollTop = element.scrollHeight;
}