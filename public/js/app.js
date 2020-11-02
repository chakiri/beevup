/* fade ut displayed alert */
$(window).load(function(){
    setTimeout(function(){ $('#alert').fadeOut("linear" ) }, 2000);
});

/* Recommandation Approve and reject buttons*/
var url = '';
var untreatedRecommandationNb = 0;
$('.recommandation-approve').click(function () {
    var recommandationId = $(this).data("recommandation-id");
    $('#spinner-approve-'+recommandationId).removeClass('spinner-hidden');
    $('#spinner-approve-'+recommandationId).addClass('spinner-visible');

    url = 'edit/recommandation/1/'+recommandationId;
    $.get(url, function (data) {
        untreatedRecommandationNb = $('.recommandation-approve').length;
        $('#recommandation-'+recommandationId).addClass('approved-box');
        $('#recommandation-'+recommandationId).slideToggle( "slow");
        if(untreatedRecommandationNb == 1)
        {
            $('.recommandation-section').append( "<div class='box'>Vous avez traité tous les recommandation</div>" );
        }
    });
});


$('.recommandation-reject').click(function () {
    var recommandationId = $(this).data("recommandation-id");
    $('#spinner-reject-'+recommandationId).removeClass('spinner-hidden');
    $('#spinner-reject-'+recommandationId).addClass('spinner-visible');

    url = 'edit/recommandation/0/'+recommandationId;
    $.get(url, function (data) {
        untreatedRecommandationNb = $('.recommandation-reject').length;
        $('#recommandation-'+recommandationId).addClass('rejeccted-box');
        $('#recommandation-'+recommandationId).slideToggle( "slow");
        if(untreatedRecommandationNb == 1) {
            $('.recommandation-section').append( "<div class='box'>Vous avez traité tous les recommandation</div>" );
        }
    });
})
/* end Recommandation Approve and reject buttons*/

/*Add new recommandation message */
$('.add-recommandation').click(function ()
{
    //Get data
    var url = $(this).attr('data-target');
    var serviceId = $(this).attr('data-service');
    var companyId = $(this).attr('data-company');
    var storeId = $(this).attr('data-store');

    //Open modal
    $('#recommandation').modal();

    $.get(url, function (data) {
        //Put the formulaire in modal content
        $('#recommandation .modal-content').html(data);

        //Insert data into hidden form
        $('#recommandation .form-service').val(serviceId);
        $('#recommandation .form-company').val(companyId);
        $('#recommandation .form-store').val(storeId);
    });
});
/*end new recommandation message */

//popover display in show service
$("[data-toggle=popover]").popover();

//toogle
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/**
 *Set Name to all upload file
 */
$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});

/* toast popup showing score */
function loadToast(){
    $('.toast').toast('show');
}

/**
*Set Session cookie
*/
$('#cookies a').click(function(){
    const url = $(this).data('url');

    $.ajax({
        type: 'SET',
        url: url,
        success: function (){
            console.log('Set cookie session');
            $('#cookies').hide();
        },
        error: function(xhr){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });
});

/*************** crop image ******************************/
(function ($) {
    "use strict";

    var fileInput =  document.getElementsByClassName('form-imageFile')[0];
    var cropper;
    var previousImage = document.createElement("img");
    previousImage.classList.add('previous-img');
    previousImage.classList.add('hide-bloc');
    var previousImageBloc = document.getElementById('previous-image');
    previousImageBloc.appendChild(previousImage);


    window.previousImage = function()
    {

        previousImage.classList.remove('hide-bloc');
        var file = fileInput.files[0];
        let reader = new FileReader();
        reader.addEventListener('load', function (event) {
            previousImage.src = reader.result
        }, false)

        if(file){
            reader.readAsDataURL(file);
        }

    }

    previousImage.addEventListener('load', function () {
        if (cropper) {
            cropper.destroy();
            cropper = new Cropper(previousImage, {
                aspectRatio: 1
            });
        }
        else
            cropper =  new Cropper(previousImage,{
                aspectRatio: 1
            })
    });

    let form = document.getElementById('BVform');

    form.addEventListener('submit', function (event)
    {

        if(fileInput.files[0]) {

            event.preventDefault()
            $('.hide-load').addClass('load-ajax-form');
            cropper.getCroppedCanvas({
                maxHeight: 1000,
                maxWidth: 1000,

            }).toBlob(function (blob) {
                ajaxWithAxios(blob);
            })
        }

    });

    function urls(){
        let hostname = location.hostname;
        let protocol = location.protocol;
        let port     = location.port;
        let portURL ='';
        if (port !=''){
            portURL = ':'+port;
        }
        let subDomain = window.location.pathname.split('/')[1];
        let dataEntityId = $('.data-entity-id').attr('data-entity-id');
        let dataSlug = $('.data-entity-id').attr('data-slug');
        let action = protocol+'//'+hostname+portURL+'/'+subDomain+'/'+dataEntityId+'/edit';
        if(subDomain !='account' && subDomain !='service'){
            dataEntityId = dataSlug ;
        }
        if(subDomain =='service'){

            let subDomain2 = window.location.pathname.split('/')[2];
            let subDomain3 = window.location.pathname.split('/')[3];
            if(subDomain2 =='new'){
                action = protocol+'//'+hostname+portURL+'/'+subDomain+'/new';
                if(subDomain3 !=undefined){
                    action = protocol+'//'+hostname+portURL+'/'+subDomain+'/new/'+subDomain3;
                }
            }

        }
        let redirectedUrl = protocol+'//'+hostname+portURL+'/'+subDomain+'/'+dataEntityId;

        let urls = [];
        urls['url'] = action;
        urls['redirectedUrl'] = redirectedUrl;

        return urls;
    }


    function ajaxWithAxios(blob)
    {
        let url = urls()['url'];
        let redirectedUrl = urls()['redirectedUrl'];

        let data = new FormData(form);
        data.append('file', blob);

        $.ajax({
            url: url,
            type: 'POST',
            async: false,
            data:data,
            processData: false,
            contentType: false,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(){
                console.log(redirectedUrl);
                window.location = redirectedUrl;
            },
            error: function(){
                alert("Un problème est survenu. Veuillez réessayer")
            }
        });
    }
})(jQuery);
/*************** END crop image ******************************/