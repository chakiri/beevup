if (document.querySelector('#service_isDiscovery')){
    document.querySelector('#service_isQuote').onclick = function (){
        var x = document.getElementById('price');
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    };
}

if (document.querySelector('#service_isDiscovery')) {
    document.querySelector('#service_isDiscovery').onclick = function () {
        var x = document.getElementById('discovery');
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    };

    var checkBox = document.getElementById("service_isDiscovery");
    var x = document.getElementById('discovery');

    if (checkBox.checked == true){
        x.style.display = "block";
    }else{
        x.style.display = "none";
    }
}

if (document.querySelector('#isMedia')) {

    var media = document.querySelector('#mediaBox');

    document.querySelector('#isMedia').onclick = function () {
        if (media.style.display === "none") {
            media.style.display = "block";
        } else {
            media.style.display = "none";
        }
    };

    if (document.querySelector('#isMedia').checked == true){
        media.style.display = "block";
    }else{
        media.style.display = "none";
    }

    var video = document.querySelector('#urlYoutube');
    var image = document.querySelector('#imageFile');

    document.querySelector('#isVideo').onclick = function () {

        if (video.style.display === "none") {
            video.style.display = "block";
            image.style.display = "none";
            document.getElementById("post_imageFile").value = "";
            $(".custom-file-label").html("Choisir une image");
        } else {
            video.style.display = "none";
            image.style.display = "block";
            document.querySelector('#post_urlYoutube').value = "";
        }
    };

     if (document.querySelector('#isVideo').checked == true){
         video.style.display = "block";
         image.style.display = "none";
     }else{
         video.style.display = "none";
         image.style.display = "block";
     }
}