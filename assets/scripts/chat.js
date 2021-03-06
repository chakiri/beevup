/*Put functions in global variables to use it outside the file*/
window.sender = sender;
window.saveNotification = saveNotification;
window.checkFirstMessage = checkFirstMessage;
window.callModalAllUsersTopic = callModalAllUsersTopic;
window.sendToAllUsersOfTopic = sendToAllUsersOfTopic;

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

    //Empty textarea after sending
    document.getElementById("messageToSend").value = '';
    //Put back textarea to start height
    $("#messageToSend").css('height', '44px');

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

    console.log(userid, receiverid);

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

/**
 * Function calling modal and fill form to send private message to all users of topics
 */
function callModalAllUsersTopic()
{
    //Open empty modal
    $('#allUsersTopic').modal('show');

    //Get url of controller
    const url = $('#btnAllUsersTopic').data('url');

    //Get form from controller
    $.ajax({
        type: 'get',
        url: url,
        success: function(data){
            $('#allUsersTopic .modal-content').html(data);
        },
        error: function(xhr){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });
}

/**
 * Function to call controller and save message to all users from topics
 */
function sendToAllUsersOfTopic()
{
    let chatPlateform = $('#chatPlateform');
    let from = chatPlateform.data('from');
    let subject = chatPlateform.data('subject');
    let message = $("#messageToAllUsers textarea").val();

    let url = $("#messageToAllUsers button").data('url');

    $.ajax({
        type: 'post',
        url: url,
        data: {
            from: from,
            subject: subject,
            message: message
        },
        error: function(xhr){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });
}

/**
 *Auto resize textarea
 */
//Calcul height entry
function calcHeight(value) {
    let numberOfLineBreaks = (value.match(/\n/g) || []).length;
    // min-height + lines x line-height + padding + border
    let newHeight = 20 + numberOfLineBreaks * 20 + 12 + 2;
    return newHeight;
}
//Change height of textarea
function changeHeight(textarea){
    textarea.addEventListener("keyup", (e) => {
        textarea.style.height = calcHeight(textarea.value) + "px";
    });
}
//Select textareas
let textarea = document.querySelector("#messageToSend");
if (textarea) changeHeight(textarea);
let textarea2 = document.querySelector("#messageToAllUsers textarea");
if (textarea2) changeHeight(textarea2);