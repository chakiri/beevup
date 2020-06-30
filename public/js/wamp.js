var from = document.getElementById("chatPlateform").dataset.from;           //current User
var subject = document.getElementById("chatPlateform").dataset.subject;     //Subject send to
var isPrivate = document.getElementById("chatPlateform").dataset.private;   //If private chat
var message = document.getElementById("messageToSend").value;
var currentUserFirstname = document.getElementById("userStatus").dataset.userfirstname;
var enterKeyCode = 13;

console.log('current user : ' + from);
updateScroll();

ab.debug(true, true);
var conn = new ab.Session('wss://'+window.location.hostname+ ':8888',
    function() {
        console.log('Connection established on ' + subject);
        //Subscribe user
        conn.subscribe(from, function(current, data) {
            // This called when subscribe callback is executed
            console.log('New message published by ' + data.user + ' to ' + data.subject + ' : ' + data.message);
            //If is private chat
            if (data.isprivate == true){
                handlePrivateMessage(data);
            }else{
                handleTopicMessage(data);
            }
        });

        //If topic, subscribe also topic
        if (isPrivate != true){
            conn.subscribe(subject, function(current, data) {
                handleTopicMessage(data);
            });
        }
    },
    function() {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);


function handlePrivateMessage(data){
    //Add message if it's the sender or if it's subject page
    if (data.from === subject || data.from === from){
        addMessageToCanvas(data);
    }else{
        //send notification
        console.log("notif to user " +data.from);
        addNotification(data.from);
        saveNotification(data.from);
    }
}

function handleTopicMessage(data){
    //If it's topics
    if (data.subject == subject){
        addMessageToCanvas(data);
    }else{
        //send notification
        console.log("notif to topic " +data.subject);
        addNotification(data.subject);
        saveNotification(data.subject);
    }
}

function addMessageToCanvas(data){
    const chatContent = document.getElementById("chat");

    var currentAvatar = document.getElementById('currentAvatar').src;
    var subjectAvatar = document.getElementById('subjectAvatar').src;

    const date = new Date().getDate() + "/" + ((new Date().getMonth())+1) + " " + new Date().getHours() + ":" + new Date().getMinutes();

    //Get message HTML
    if (currentUserFirstname === data.user){
         //messageHTML = "<div class='message'><div class='text-right'><p class='name-user'>Moi</p><span class='span-style-me'>" + data.message + "</span></div></div>";
        messageHTML = "<li class='replies'><div><img src='" + currentAvatar + "' class=\"img-fluid\"><p>" + data.message + "</p></div><span><small>" + date + "</small></span></li>"
    }else {
         //messageHTML = "<div class='message'><div><p class='name-user'>" + data.user + "</p><span class='span-style'>" + data.message + "</span></div></div>";
        messageHTML = "<li class='sent'><div><img src='" + subjectAvatar + "' class='img-fluid'><p>" + data.message + "</p></div><span><small>" + date + "</small></span></li>"
    }
    //insert messageHTML in the chat
    chatContent.innerHTML += messageHTML;

    //update scroll to the bottom
    updateScroll();
}

function addNotification(from){
    var channel = document.querySelector('[data-channel~="' + from +'"]');

    //If channel exist in user channels
    if (channel !== null){
        var badge = channel.querySelector(".badge");
        var notifs = +badge.textContent;

        var newNotifs = notifs + 1;

        badge.innerHTML = newNotifs ;
    }
}

function updateScroll(){
    var element = document.getElementById("wrapChat");
    element.scrollTop = element.scrollHeight;
}

//Check for Entry key
message.onkeyup = function (e){
    if (e.keyCode === enterKeyCode) {
        sender();
    }
}