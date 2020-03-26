var currentTopic = document.getElementById("chatPlateform").dataset.topic;
var currentUserFirstname = document.getElementById("userStatus").dataset.userfirstname;
var message = document.getElementById("message").value;
var enterKeyCode = 13;

console.log('current topic : ' + currentTopic);
updateScroll();

ab.debug(true, true);
var conn = new ab.Session('ws://127.0.0.1:8080',
    function() {
        console.log('Connection established on ' + currentTopic);
        conn.subscribe(currentTopic, function(topic, data) {
            if (data.type === 'notification'){
                console.log('Notif from topic : ' + data.topicFrom);
                addNotifToTopic(data.topicFrom);
                //Save notif not saw by user on topic
                saveNotifToUser(data.topicFrom);
            }else{
                // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                console.log('New message published by ' + data.user + ' to topic ' + data.topic + ' : ' + data.message);
                addMessageToCanvas(data);
            }
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
    if (currentUserFirstname === data.user){
         messageHTML = "<div class='message'><div class='text-right'><p class='name-user'>Moi</p><span class='span-style-me'>" + data.message + "</span></div></div>";
    }else {
         messageHTML = "<div class='message'><div><p class='name-user'>" + data.user + "</p><span class='span-style'>" + data.message + "</span></div></div>";
    }
    //insert messageHTML in the chat
    chatContent.innerHTML += messageHTML;

    //update scroll to the bottom
    updateScroll();
}

function addNotifToTopic(topic){
    var channel = document.querySelector('[data-channel~="' + topic +'"]');
    var badge = channel.querySelector(".badge");
    var notifs = +badge.textContent;

    var newNotifs = notifs + 1;

    badge.innerHTML = newNotifs ;
}

function updateScroll(){
    var element = document.getElementById("chat");
    element.scrollTop = element.scrollHeight;
}

//Check for Entry key
message.onkeyup = function (e){
    if (e.keyCode === enterKeyCode) {
        sender();
    }
}