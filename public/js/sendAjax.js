function sender() {
    var from = document.getElementById("chatPlateform").dataset.from;
    var subject = document.getElementById("chatPlateform").dataset.subject;
    var message = document.getElementById("message").value;
    var isprivate = document.getElementById("chatPlateform").dataset.private;
    var url = document.getElementById("message").dataset.url;
    $.ajax({
        type: 'post',
        url: url,
        data: {
            from: from,
            subject: subject,
            message: message,
            isprivate: isprivate
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