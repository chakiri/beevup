//connection to the websocket server
const socket = new WebSocket("ws://localhost:3001");

//Initialize chat
const defaultChannel = 'general';
const channels = ['general', 'random', 'room'];
const botName = 'ChatBot';

//Initialize elements
const chatContent = document.getElementById("chat");
const channelsList = document.getElementById("channelsList");
var userName = document.getElementById('userName').dataset.user;

//Send message on OPEN connection
socket.addEventListener("open", function(){
    console.log("CONNECTED");   //Connected to the server

    //Add default channel
    addChannel(defaultChannel);
    activateChannel(defaultChannel);


    var message = {
        action: 'subscribe',
        channel: defaultChannel,
        user: userName
    };
    socket.send(JSON.stringify(message));
});

//Event listener on message reception in the websocket server
socket.addEventListener("message", function(e){
    console.log(e.data);
    try{
        //const message = JSON.parse(e.data);
        addMessageToChannel(e.data);
    }catch(e) {
        console.log(e.error);
    }
});

socket.addEventListener("close", function () {
    botMessageToChannel('Connection closed')
});

socket.addEventListener("error", function () {
    botMessageToChannel('An error occured')
});

//On click send message to websocket server
document.getElementById("sendBtn").addEventListener("click", function () {
    var message = {
        action: 'messageToChannel',
        channel: defaultChannel,
        user: userName,
        message: document.getElementById("message").value
    };
    //Send message serialized
    socket.send(JSON.stringify(message));
    //addMessageToChannel(message.message);

    //Reset inputs
    document.getElementById("message").value = '';
});

//Add message to the chat
function addMessageToChannel(message){
    //Serialize message
    var messageData = JSON.parse(message);

    //Get message HTML
    const messageHTML = "<div class='message'><strong>" + messageData.user + " : </strong>" + messageData.message + "</div>";

    //insert messageHTML in the chat
    chatContent.innerHTML += messageHTML;
}

//Send message auto
function botMessageToChannel(messageStr){
    var message = {
        action: 'messageToChannel',
        channel: defaultChannel,
        user: botName,
        message: messageStr
    }
    addMessageToChannel(message);
}

//Add channel to the list html
function addChannel(channel){
    var channelStr = '#' + channel;

    //Get channel in list
    const channelHTML = "<li><a href='" + channelStr + "'><span>" + channelStr + "</span></a></li>";
    //Add channel to list
    channelsList.innerHTML += channelHTML;
}

function activateChannel(channel){
    var channelStr = '#' + channel;

    //Get channel
    var channelLink = document.querySelector('a[href="' + channelStr + '"]');

    channelLink.parentElement.classList.add('font-weight-bold');
}