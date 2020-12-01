import '../styles/admin.css';
import Cropper from 'cropperjs';

function handler (event, fileInput, cropper, form,targetUrl,  redirectUrl)
{
    if(fileInput.files[0]) {
        $('.hide-load').addClass('load-ajax-form');
        cropImage(cropper,form, targetUrl, redirectUrl);
    }
}


function cropImage(cropper,form,targetUrl, redirectUrl){
    if (cropper) {
        cropper.getCroppedCanvas({ maxHeight: 1000,maxWidth: 1000,})
            .toBlob(function (blob) {
                sendForm(blob, form, cropper,targetUrl, redirectUrl);
            })
    } else {
        $('.hide-load').removeClass('load-ajax-form');
        setError('file-not-correct','Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)');
    }
}

function sendForm(blob, form, cropper, targetUrl, redirectUrl)
{
    let data = new FormData(form);
    data.append('file', blob);

    $.ajax({
        url: targetUrl,
        type: 'POST',
        async: false,
        data:data,
        processData: false,
        contentType: false,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(data){
            window.location = redirectUrl;
        },
        error: function(){
            alert("Un problème est survenu. Veuillez réessayer")
        }
    });
}

function get_hostname() {
    return document.location.hostname;
}
function get_port(){
    return location.port;
}
function get_protocol(){
    return window.location.protocol;
}
function get_current_url()
{
    let url ='';
    if(get_port() !='') {
        url = get_protocol()+'//'+get_hostname() + ':'+get_port();

    } else {
        url = get_protocol()+'//'+get_hostname() ;
    }
    return url;
}
function setError(message, messageContainer){
    $('.'+messageContainer).text(message);

}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
}
var action = getUrlParameter('action');
 if($('#store_country').val() == 'AF' && action =='new') {
        $('#store_country').val("FR");
    }

var cropper;
var previousImage = document.createElement("img");
previousImage.classList.add('previous-img');
previousImage.classList.add('hide-bloc');
var previousImageBloc = $('.easyadmin-vich-image');
if(previousImageBloc != null) {
    previousImageBloc.append(previousImage);
}
$( "#publicity_imageFile_file" ).change(function() {
    var fileInput = document.getElementsByClassName('custom-file-input')[0];
    var file = fileInput.files[0];
    let reader = new FileReader();
    if(reader != null){
        reader.addEventListener('load', function (event) {
            previousImage.src = reader.result
        }, false)
    }

    if(file){
        reader.readAsDataURL(file);
    }
    if(previousImage != null) {
        previousImage.addEventListener('load', function () {

            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(previousImage, {
                aspectRatio: 1
            });
        });
    }
    let form = document.getElementById('edit-publicity-form');
    let entityId = $('#edit-publicity-form').attr('data-entity-id');
    var targetUrl = get_current_url()+'/admin/updatePublicity/'+entityId;
    var redirectedUrl = get_current_url()+'/admin/?entity=Publicity';

    if(form != null)
    {
        form.addEventListener('submit', function(e){
            handler(e, fileInput, cropper, form, targetUrl, redirectedUrl);
        });
    }


});

