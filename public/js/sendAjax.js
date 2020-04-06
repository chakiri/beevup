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

//Function ajax to send data to php for saving not seeied messages
function saveNotification(subject){
    var user = document.getElementById("chatPlateform").dataset.from;
    var url = document.getElementById("chatPlateform").dataset.url;
    $.ajax({
        type: 'post',
        url: url,
        data: {
            user: user,
            subject: subject
        },
        success: function (response) {
            console.log('notify ajax success');
        }
    });

}