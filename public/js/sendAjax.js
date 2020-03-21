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
            // $('#res').html("Vos données seront sauvegardées");
        }
    });

    return false;
}