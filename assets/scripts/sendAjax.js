/*Put functions in global variables to use it outside the file*/
window.sender = sender;
window.saveNotification = saveNotification;
window.checkFirstMessage = checkFirstMessage;

function sender() {
    var from = document.getElementById("chatPlateform").dataset.from;
    var subject = document.getElementById("chatPlateform").dataset.subject;
    var message = document.getElementById("messageToSend").value;
    var isprivate = document.getElementById("chatPlateform").dataset.private;
    var url = document.getElementById("messageToSend").dataset.url;

    console.log(message);

    if (message == "") {
        alert("Entrez un message valide");
        return false;
    }

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

    document.getElementById("messageToSend").value = '';
    document.getElementById("messageToSend").css('height', '44px');

    return false;
}

//Function ajax to send data to php for saving not seeied messages
function saveNotification(subject) {
    var user = document.getElementById("chatPlateform").dataset.from;
    var url = document.getElementById("chatPlateform").dataset.url;

    $.ajax({
        type: 'post',
        url: url,
        data: {
            user: user,
            subject: subject
        },
        success: function () {
            console.log('notify ajax success');
        },
        error: function (xhr, ajaxOptions, thrownError){
            console.log(xhr.status);
            console.log(thrownError);
        }
    });

    return false;
}

//Function ajax check if fisrt message and send mail
function checkFirstMessage(userid, receiverid){
    var url = document.getElementById("chatPlateform").dataset.urlfirstmail;

    $.ajax({
        type: 'post',
        url: url,
        data: {
            userid: userid,
            receiverid: receiverid
        }
    });

    return false;
}