
updateScroll();

var currentUser = document.getElementById("chatPlateform").dataset.currentUserId;

ab.debug(true, true);
var conn = new ab.Session('ws://127.0.0.1:8080',
    function() {
        console.log('Connection established on ' + currentUser);
        conn.subscribe(currentUser, function(topic, data) {
            console.log(topic);
            console.log(data);
        });
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);



function updateScroll(){
    var element = document.getElementById("chat");
    element.scrollTop = element.scrollHeight;
}

//Check for Entry key
var message = document.getElementById("message").value;
var enterKeyCode = 13;

message.onkeyup = function (e){
    if (e.keyCode === enterKeyCode) {
        sender();
    }
}