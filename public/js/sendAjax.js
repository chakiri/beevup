function sender() {
    var sentToTopic = document.getElementById("chatPlateform").dataset.topic;
    var message = document.getElementById("message").value;
    var url = document.getElementById("message").dataset.url;
    $.ajax({
        type: 'post',
        url: url,
        data: {
            topic: sentToTopic,
            message: message,
        },
        success: function (response) {
            // $('#res').html("Vos données sont envoyés");
        }
    });

    document.getElementById("message").value = '';

    return false;
}

//Function ajax to send data to php for saving not seeied message
function saveNotifToUser(topic){
    var currentUserId = document.getElementById("notifications").dataset.userid;
    var url = document.getElementById("notifications").dataset.url;
    $.ajax({
        type: 'post',
        url: url,
        data: {
            topic: topic,
            userid: currentUserId,
        },
        success: function (response) {
            console.log('notify ajax success');
        }
    });

}