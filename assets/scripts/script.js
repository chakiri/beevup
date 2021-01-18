import $ from 'jquery';
import Cropper from 'cropperjs';

//  =============  mobile navigation =========
if ($('.main-nav').length) {
    $('.main-nav').addClass('mobile-nav-cls');
    var $mobile_nav = $('.main-nav').clone().prop({
        class: 'mobile-nav d-lg-none'
    });
    $('body').append($mobile_nav);
    $('body').prepend('<button type="button" class="mobile-nav-toggle d-lg-none"><i class="fa fa-bars"></i></button>');
    $('body').append('<div class="mobile-nav-overly"></div>');

    $(document).on('click', '.mobile-nav-toggle', function(e) {
        $('body').toggleClass('mobile-nav-active');
        $('.mobile-nav-toggle i').toggleClass('fa-times fa-bars');
        $('.mobile-nav-overly').toggle();
    });

    $(document).on('click', '.mobile-nav .drop-down > a', function(e) {
        e.preventDefault();
        $(this).next().slideToggle(300);
        $(this).parent().toggleClass('active');
    });

    $(document).click(function(e) {
        var container = $(".mobile-nav, .mobile-nav-toggle");
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            if ($('body').hasClass('mobile-nav-active')) {
                $('body').removeClass('mobile-nav-active');
                $('.mobile-nav-toggle i').toggleClass('fa-times fa-bars');
                $('.mobile-nav-overly').fadeOut();
            }
        }
    });
} else if ($(".mobile-nav, .mobile-nav-toggle").length) {
    $(".mobile-nav, .mobile-nav-toggle").hide();
}

//  =============  fade ut displayed alert  =========
$(window).on('load', function(){
    if($('.sponsorship-page').length ==0) {
        setTimeout(function () {
            $('#alert').fadeOut("linear")
        }, 5000);
    }
});


//  ============= Recommandation Approve and reject buttons =========
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
});


//  ============= Add new recommandation message  =========
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

//  ============= popover display in show service =========
$("[data-toggle=popover]").popover();

//toogle
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});


//  ============= Set Name to all upload file =========

$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});


//  =============toast popup showing score =========
window.loadToast = function()
{
    $('.toast').toast('show');
}

//  =============Set Session cookie =========
$('#cookies a').click(function(){
    const url = $(this).data('url');

    $.ajax({
        type: 'GET',
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


//  ============= cropper image =========

    "use strict";

    function createPreviousImageBloc(id){
       let idValue =  (id.slice(id.length - 1) =='e') ? '' : id.slice(id.length - 1);
        return document.getElementById('previous-image'+idValue) ;
    }
    function getFieldId(serviceClassName){
        if(serviceClassName.slice(-1) ==    1)   return 1;
        if(serviceClassName.slice(-1) ==    2)   return 2;
        if(serviceClassName.slice(-1) ==    3)   return 3;
        if(serviceClassName.slice(-1) ==  'e')   return '';

    }
     function getDeleteFileUrl(serviceId, fieldId){
        let url ='/service/'+serviceId+'/delete/'+fieldId;
        return url;
     }

    $(document).on('click', '.delete-img-service', function(e) {
        $(this).addClass('d-none');
        let fieldId = $(this).attr('data-input-id');
        let serviceId = $(this).attr('data-service-id');

        if (fieldId == 'service_imageFile1') {
            $('#previous-image1').empty();
            $('#service_imageFile1').val('');
            serviceCropper1 = '';
        }
        if (fieldId == 'service_imageFile2') {
            $('#previous-image2').empty();
            $('#service_imageFile2').val('');
            serviceCropper2 = '';
        }
        if (fieldId == 'service_imageFile3') {
            $('#previous-image3').empty();
            $('#service_imageFile3').val('');
            serviceCropper3 = '';
        }
        if(serviceId != ''){
        let deletFileUrl = getDeleteFileUrl(serviceId, fieldId);
        $.ajax({
            url: deletFileUrl,
            type: 'POST',
            async: false,
            processData: false,
            contentType: false,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function (data) { },
            error: function () {
                alert("Un problème est survenu. Veuillez réessayer")
            }
        })
    }

    });



    var fileInput = document.getElementsByClassName('form-imageFile')[0];
    //var cropper;
    var previousImage = document.createElement("img");

    previousImage.classList.add('previous-img');
    previousImage.classList.add('hide-bloc');
    var previousImageBloc = document.getElementById('previous-image');
    if(previousImageBloc != null) {

        previousImageBloc.appendChild(previousImage);
   }

    var ServiceCropper ='';
    var serviceCropper1 ='';
    var serviceCropper2 ='';
    var serviceCropper3 ='';
    var reader1 = new FileReader();
    var reader2 = new FileReader();
    var reader3 = new FileReader();

    window.previousImage = function(e)
    {

        let serviceInputsId = ['service_imageFile1','service_imageFile2','service_imageFile3']
        let serviceFieldId ='';
        /* to check if the page is service or [profile, store, company] */
        if(e != undefined){
             serviceFieldId = e.target.id;
            $('#previous-image'+getFieldId(serviceFieldId)).empty();
            $('#previous-image'+getFieldId(serviceFieldId)+ ' + span').removeClass('d-none');
        } else {
            $('#previous-image').empty();
        }
        if(serviceInputsId.includes(serviceFieldId)) {
             var fileInput = document.getElementById(e.target.id);

        } else {
            var fileInput = document.getElementsByClassName('form-imageFile')[0];
        }

        var cropper;
        var cutbtn = document.createElement("span");

        if(serviceFieldId != undefined) {
            cutbtn.setAttribute('data-image', serviceFieldId);
    }
        if(e != undefined) {
            cutbtn.classList.add('cut-btn' + getFieldId(serviceFieldId));
            cutbtn.innerHTML = "Rogner la photo";
        }



        var previousImage = document.createElement("img");
        previousImage.classList.add('previous-img');
        previousImage.classList.add('hide-bloc');
        if(serviceInputsId.includes(serviceFieldId)){
        var previousImageBloc = createPreviousImageBloc(serviceFieldId);

    } else {
        var previousImageBloc = document.getElementById('previous-image');
    }
        if(previousImageBloc != null) {

            previousImageBloc.appendChild(previousImage);
            previousImageBloc.appendChild(cutbtn);
        }

        previousImage.classList.remove('hide-bloc');
        if(serviceInputsId.includes(serviceFieldId)) {
           var fileInput = document.getElementById(serviceFieldId);
        } else {
            var fileInput = document.getElementsByClassName('form-imageFile')[0];
        }
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
                    cropper = new Cropper(previousImage, {
                        aspectRatio: 1
                    });
                } else {

                    cropper = new Cropper(previousImage, {
                        aspectRatio: 1
                    })
                    if(serviceFieldId.slice(-1) == 1){
                        serviceCropper1 = cropper;
                        reader1 = reader;
                    }
                     if(serviceFieldId.slice(-1) ==2){
                        serviceCropper2 = cropper;
                         reader2 = reader;
                    }
                     if(serviceFieldId.slice(-1) ==3){
                        serviceCropper3 = cropper;
                         reader3 = reader;
                    }

                     if(serviceFieldId.slice(-1) =='e'){
                        ServiceCropper = cropper;
                    }

                 }
            });
        }
        let form = document.getElementById('BVform');
        function handler ()
        {
            if(fileInput.files[0]) {
                event.preventDefault()
                $('.hide-load').addClass('load-ajax-form');
                if (cropper) {

                    cropper.getCroppedCanvas({
                        maxHeight: 1000,
                        maxWidth: 1000,

                    }).toBlob(function (blob) {
                        ajaxWithAxios(blob, form, cropper);
                     })
                }
                else {

                    $('.hide-load').removeClass('load-ajax-form');
                    // $(form).find('[name*="imageFile"]').first().parent('div').before("Le fichier que vous venez de uploder n'est pas correct");
                    $('.file-not-correct').text('Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)');
                }
            }
        }
        if(form != null)
        {
            form.addEventListener('submit',handler);
        }


        $(document).on('click', '.cut-btn'+getFieldId(serviceFieldId), function(e) {
        previousImageBloc = createPreviousImageBloc($(this).attr('data-image'));
        $('.cropper-modal').addClass('imageCupped'+getFieldId(serviceFieldId));
        var previousCuppedImage = document.createElement("img");
        previousCuppedImage.classList.add('previous-cupped-img'+getFieldId($(this).attr('data-image')));
        $('#previous-image'+getFieldId($(this).attr('data-image'))).empty();
        previousImageBloc.appendChild(previousCuppedImage);
            var resetBtn = document.createElement("span");
            resetBtn.innerHTML = "Annuler";
            resetBtn.classList.add('reset-btn'+ getFieldId(serviceFieldId));
            resetBtn.setAttribute('data-image', serviceFieldId);
            previousImageBloc.appendChild(resetBtn);
        if(getFieldId($(this).attr('data-image')) == 1){
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', serviceCropper1.getCroppedCanvas().toDataURL());
         }
            else if(getFieldId($(this).attr('data-image')) == 2){
                $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', serviceCropper2.getCroppedCanvas().toDataURL());
            }
        else if(getFieldId($(this).attr('data-image')) == 3){
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', serviceCropper3.getCroppedCanvas().toDataURL());
        }
        else {
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', ServiceCropper.getCroppedCanvas().toDataURL());
        }


    });
        $(document).on('click', '.reset-btn'+getFieldId(serviceFieldId), function(e) {
             previousImageBloc = createPreviousImageBloc($(this).attr('data-image'));
            $('.imageCupped'+getFieldId(serviceFieldId)).removeClass('imageCupped'+getFieldId(serviceFieldId));
            var previousImage = document.createElement("img");
            previousImage.classList.add('previous-img'+getFieldId(serviceFieldId));
            $('#previous-image'+getFieldId(serviceFieldId)).empty();
            if(getFieldId(serviceFieldId) == 1) {

                previousImage.src = reader1.result;
                previousImageBloc.appendChild(previousImage);
                serviceCropper1 = new Cropper(previousImage, {
                    aspectRatio: 1
                });

            } else if(getFieldId(serviceFieldId) == 2){
                previousImage.src = reader2.result;
                previousImageBloc.appendChild(previousImage);
                serviceCropper2 = new Cropper(previousImage, {
                    aspectRatio: 1
                });

            } else if(getFieldId(serviceFieldId) == 3){

                previousImage.src = reader3.result;
                previousImageBloc.appendChild(previousImage);
                serviceCropper3 = new Cropper(previousImage, {
                    aspectRatio: 1
                });

            } else {

               previousImage.src = reader.result;
                previousImageBloc.appendChild(previousImage);
                ServiceCropper = new Cropper(previousImage, {
                    aspectRatio: 1
                });

            }


            previousImageBloc.appendChild(cutbtn);


        });

   }

    //====================== fix service issue =========//
    let form = document.getElementById('BVformService');
    if(form != null)
    {
        form.addEventListener('submit', function (event)
        {
            let blob0 ='';
            let blob1 ='';
            let blob2 ='';
            let blob3 ='';
            let imageDimension = {maxHeight: 1000, maxWidth: 1000 };

            if(fileInput.files[0]  || fileInput != null ) {

                event.preventDefault();


                $('.hide-load').addClass('load-ajax-form');

                if (serviceCropper1 != '') {
                   serviceCropper1.getCroppedCanvas(imageDimension).toBlob(function (blob) {  blob1 = blob; });  }

                if (serviceCropper2 != '') {
                    serviceCropper2.getCroppedCanvas(imageDimension).toBlob(function (blob) {   blob2 = blob;  });
                 }

                if (serviceCropper3 != '') {
                    serviceCropper3.getCroppedCanvas(imageDimension).toBlob(function (blob) { blob3 = blob;  });
                 }

                 if (ServiceCropper != '') {

                    ServiceCropper.getCroppedCanvas(imageDimension).toBlob(function (blob) { blob0 = blob;
                   });

                }

                setTimeout(function(){  ajaxWithAxios(blob0, form, ServiceCropper, blob1, blob2, blob3); }, 500); }
         });
    }

    //====================== fix service issue =========//
    function update_img_url(){
        var url =   $('.upload-photo').attr('data-url');
        return url;
    }

    function serviceRedirectedUrl(id)
    {
        let serviceId = id;
        let companySlug = $('.data-entity-id').attr('data-company-slug');
        let companyId= $('.data-entity-id').attr('data-company-id');
        let previousUrl = $('.data-entity-id').attr('data-previous');
        let url='';
        let hostname = location.hostname;
        let protocol = location.protocol;
        let port     = location.port;
        let portURL ='';
        if (port !=''){
            portURL = ':'+port;
        }
        if(previousUrl !='company')
            url = protocol+'//'+hostname+portURL+'/service/'+serviceId ;
        else
            url = protocol+'//'+hostname+portURL+'/company/'+companySlug+'/'+companyId;

        return url;
    }

    function ajaxWithAxios(blob, form, cropper,blob1, blob2, blob3)
    {

       let url = update_img_url();
       let data = new FormData(form);

        data.append('file', blob);
        data.append('file1', blob1);
        data.append('file2', blob2);
        data.append('file3', blob3);
        $.ajax({
            url: url,
            type: 'POST',
            async: false,
            data:data,
            processData: false,
            contentType: false,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data){
                if(data.result == 0) {
                    $('.hide-load').removeClass('load-ajax-form');
                    let error='';
                    for (var key in data.data) {
                        error = "<p class='form-error'>"+data.data[key]+"</p>";
                        $(form).find('[name*="'+key+'"]').first().parent('div').next(".form-error").remove();
                        $(form).find('[name*="'+key+'"]').first().parent('div').after(error);
                        /*if(key=='imageFile') {
                            if(cropper !=null) {
                               cropper.destroy();
                            }
                        }*/
                    }
                    if($('.serviceError').length > 0){
                        $('.serviceError').text('');
                        $('.serviceError').append(error);
                    }
                   // alert(error);
                }
                else {
                    //  ============= if data.message is a number so it's a service json return =========

                    if(Number.isInteger(data.message)){
                        let url2 = serviceRedirectedUrl(data.message);
                        window.location = url2;

                        //  ============= profile, store or company image upload =========
                    } else {
                        // =================================append copper Image ============
                        $('#previous-image').empty();
                        $('#update-img-modal').modal('hide');
                        $('.main-img').attr('src', cropper.getCroppedCanvas().toDataURL());
                    }

                }


            },
            error: function(){
                alert("Un problème est survenu. Veuillez réessayer")
            }
        });
    }

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
        /* if(subDomain !='account' && subDomain !='service'){
             dataEntityId = dataSlug ;
         }*/
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


    // ============= update profile image =========
    $('.update-image').click(function(e){

        var url = $(this).attr('data-url') ;
        $.get(url, function (data) {
            $('.modal-content-update-profile-img').html(data);

        });
    });

// ============= Other javascript =========

$(window).on("load", function() {
    "use strict";

    //  ============= PORTFOLIO SLIDER FUNCTION =========
    $('.profiles-slider').addClass("d-block");
    $('.profiles-slider').slick({
        slidesToShow: 3,
        slick:true,
        slidesToScroll: 1,
        prevArrow:'<span class="slick-previous"></span>',
        nextArrow:'<span class="slick-nexti"></span>',
        autoplay: true,
        dots: false,
        autoplaySpeed: 2000,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: false
                }
            },
            {
                breakpoint: 991,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });
 // ===================custom input field ========

    if($('#sponsorship_message').length > 0) {
        let userStore = $('#sponsorship_message').attr('data-store');
        let emailSignature = $('#sponsorship_message').attr('data-email-footer');


        $.ajax({
            url: 'emailContent/1',
            type: 'POST',
            async: false,
            processData: false,
            contentType: false,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data){

                $('#sponsorship_message').val(data.replaceAll("<br/>","\r\n").replaceAll("{%userStore%}",userStore).replaceAll("{%emailSignature%}",emailSignature));


            },
            error: function(){
                alert("Un problème est survenu. Veuillez réessayer")
            }
        });

    }
    });
//Archive beContacted
$('.be-contacted-archive').click(function(){
    let btn = $(this);
    let url = $(this).data('target');

    $.ajax({
        type: 'POST',
        url: url,
        success: function(){
            console.log('archivé');
            btn.parents('.box').css('display', 'none');
        },
        error: function(){
            alert('Une erreur s\'est produite. Veuillez réessayer.');
        }
    });
});
//Wainting beContacted
$('.be-contacted-waiting').click(function(){
    let btn = $(this);
    let url = $(this).data('target');

    $.ajax({
        type: 'POST',
        url: url,
        success: function(){
            console.log('en attente');
            btn.parents('.box').css('display', 'none');
        },
        error: function(){
            alert('Une erreur s\'est produite. Veuillez réessayer.');
        }
    });
 });

//CGU link and buttun beContacted
if(!$('#be_contacted_acceptConditions').is(':checked')){
    $('.be-contacted-submit').addClass('orange-btn-greyed');
}
if ($('#be_contacted_acceptConditions').length > 0 ){
    if($('.accpet-condition').is(':checked')){
        $('.be-contacted-submit').attr('disabled', false);
    }
    $('.form-check-label').append(' <a target=\'_blank\' href=\'https://beevup.fr/media/pdf/CGU.pdf\' class=\'genaral-condition\' >les Conditions générales d\'utilisation</a>');
}
$('.accpet-condition').click(function () {
    if ($(this).is(':checked')) {
        $('.be-contacted-submit').removeAttr('disabled');
        $('.be-contacted-submit').removeClass('orange-btn-greyed');
    } else {
        $('.be-contacted-submit').attr('disabled', true);
        $('.be-contacted-submit').addClass('orange-btn-greyed');
    }
});

/*========== get siret =========== */

function call_api(){
    if($('.siret-list').length > 0){
        $('.siret-list').remove();
    }
    let companyName =  $('#registration_name').val();
    if(companyName != '') {
        getSiret(companyName);
    } else {
        if($('.error').length > 0){
            $('.error').remove();
        }
        let emptyCompanyError = document.createElement("p");
        emptyCompanyError.className ='error';
        emptyCompanyError.append('Vous devez saisir le nom d\'entreprise');
        insertAfter(document.getElementById("registration_name"), emptyCompanyError);
    }
}

$('.get-siret').click(function(){
    call_api();
});

function set_error(text){
    if($('.error').length > 0){
        $('.error').empty();
        $('.error').append(text);
    } else {
        let error = document.createElement("p");
        error.className ='error';
        error.append(text);
        insertAfter(document.getElementById("registration_name"), error);
    }
}

function setAdress(streetNumber, streetName, postalCode, city,country){
    $('#registration_addressNumber').val(streetNumber);
    $('#registration_addressStreet').val(streetName);
    $('#registration_addressPostCode').val(postalCode);
    $('#registration_city').val(city);
    $('#registration_country').val(country);
}


$("#registration_get_siret_from_api").change(function() {
    if(this.checked) {
        $('.siret-list').show();
        call_api();
    } else {
        $('.siret-list').hide();
    }
});

$('#registration_name').change(function(){
    if($("#registration_get_siret_from_api").is(':checked')){
        if($('.siret-list').length > 0){
            $('.siret-list').remove();
        }
        let companyName =  $('#registration_name').val();
        if(companyName != '') {
            getSiret(companyName);
        } else {
            set_error('Vous devez saisir le nom d\' entreprise');
        }
    }
});

/*========== select a siret item from sirets list =========== */

$('body').on('change', '.siret-list', function () {
    $('#registration_company_siret').val($('.siret-list').val());
    let streetNumber = $('option:selected', this).attr('data-street-number');
    let streetName  = $('option:selected', this).attr('data-street-name');
    let postalCode  = $('option:selected', this).attr('data-postal-code');
    let city = $('option:selected', this).attr('data-city');
    let country = 'FR';
    $('#registration_get_siret_from_api').prop('checked', false);
    $('.siret-list').hide();

     /*** Add adress if exist ***/
     setAdress(streetNumber, streetName, postalCode, city,  country );

});


function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}


function getSiret(companyName) {
   var data = "q=denominationUniteLegale%3A%20%22companyName%22%20OR%20nomUniteLegale%3AcompanyName&champs=denominationUniteLegale%2CcodePostalEtablissement%2Csiret%2CcomplementAdresseEtablissement%2CnumeroVoieEtablissement%2CcodePaysEtrangerEtablissement%2ClibelleCommuneEtablissement%2ClibelleVoieEtablissement&nombre=1500";

    data = data.replace('companyName',companyName);

    $.ajax({

        url: 'https://api.insee.fr/entreprises/sirene/V3/siret',
        headers: {
            Accept: "application/json",
            "Content-Type": "application/x-www-form-urlencoded"
        },
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer 0a876b7c-0e43-3dca-98bb-8c2b3e32bcfa');
        },
        data: data,
        async: false,
        processData: false,
        contentType: 'application/x-www-form-urlencode',
        success: function (data) {
            let etablissements = data.etablissements;
            console.log(etablissements);
            console.log(etablissements.length);
            function sortByPostalCode(a,b) {
                return parseInt(a.adresseEtablissement.codePostalEtablissement, 10) - parseInt(b.adresseEtablissement.codePostalEtablissement, 10);
            }
            etablissements =  etablissements.sort(sortByPostalCode);



            var selectBox = document.createElement("select");
            selectBox.className = "form-control siret-list";
            let i = 0;
            selectBox.options[selectBox.options.length] = new Option ('séléctionnez votre entreprise', '0');

            for (i = 0; i < etablissements.length; ++i) {
                selectBox.options[selectBox.options.length] = new Option(etablissements[i].adresseEtablissement.codePostalEtablissement + '-' +etablissements[i].uniteLegale.denominationUniteLegale  , etablissements[i].siret);
                selectBox.options[selectBox.options.length-1].setAttribute('data-street-number',etablissements[i].adresseEtablissement.numeroVoieEtablissement );
                selectBox.options[selectBox.options.length-1].setAttribute('data-street-name',etablissements[i].adresseEtablissement.libelleVoieEtablissement );
                selectBox.options[selectBox.options.length-1].setAttribute('data-city',etablissements[i].adresseEtablissement.libelleCommuneEtablissement );
                selectBox.options[selectBox.options.length-1].setAttribute('data-postal-code',etablissements[i].adresseEtablissement.codePostalEtablissement );
            }

            var div = document.getElementById("box-get-siret");
            insertAfter(div, selectBox);
            if($('.error').length > 0){
                $('.error').remove();
            }




        },
        error: function () {
            set_error('Aucun résultat trouvé');
        }
    });
}

/*========== auto complete =========== */
function setNoResult(){
    let elem = document.createElement("DIV");
    elem.setAttribute("class", "no-result");
    insertAfter(document.getElementById("company_addressStreet"), elem);
    let item = document.createElement("p");
    item.innerText('Aucun Resultat trouvé');
}

function getStreetName(adress, adressNumber) {
    let result = '';
    if(adress.indexOf(adressNumber) !== -1 && adressNumber != ' '){
         result = adress.substr(adressNumber.toString().length, adress.length);
      } else
          result = adress;
    return result;
}

function createSuggestionList(data){

    if($('.autocomplete-items').length > 0){
        $('.autocomplete-items').remove();
    }
    let list = document.createElement("DIV");
    list.setAttribute("class", "autocomplete-items");
    insertAfter(document.getElementById("company_address"), list);
    for (let i = 0; i < data.features.length; i++) {
        let item = document.createElement("DIV");
        let streetNumber ='';
            if(data.features[i].properties.housenumber != undefined){
                streetNumber = data.features[i].properties.housenumber ;
            } else {
                streetNumber = '1';
            }

        item.setAttribute("class", "autoComplete-item");
        item.setAttribute("data-postalcode", data.features[i].properties.postcode);
        item.setAttribute("data-street-name", getStreetName(data.features[i].properties.name, streetNumber));
        item.setAttribute("data-city", data.features[i].properties.city);
        item.setAttribute("data-street-number", streetNumber);


        if(data.features[i].properties.name != undefined) {
            item.innerHTML = "<strong>" + data.features[i].properties.name + " "+ data.features[i].properties.city +" - "+data.features[i].properties.postcode+"</strong>";
            list.append(item);
        }
    }
}


function autoComplete(address) {
    var url = "https://api-adresse.data.gouv.fr/search/?q=adressVar&type=housenumber&autocomplete=1&limit=5";
    url = url.replace('adressVar',address);


    $.ajax({

        url: url,
        type: 'GET',
        dataType: 'JSON',
        async: true,
        processData: false,
        contentType: 'application/x-www-form-urlencode',
        success: function (data) {
           if(data){
             createSuggestionList(data);
            } else {
               setNoResult();
           }
        },
        error: function () {
            alert("Un problème est survenu. Veuillez réessayer")
        }
    });
}
$('#company_address').keyup(function () {
    let street = $('#company_address').val();
    if(street) {
        autoComplete(street);
    }
});

/* =========== select an item from adress suggestions list ===== */

$('body').on('click', '.autoComplete-item', function () {
    let postalCode =  $(this).attr('data-postalcode');
    let streetNumber = $(this).attr('data-street-number');
    let streetName = $(this).attr('data-street-name');
    let city = $(this).attr('data-city');
    let country = 'FR';
    $('#company_addressNumber').val(streetNumber);
    $('#company_addressStreet').val(streetName);
    $('#company_addressPostCode').val(postalCode);
    $('#company_city').val(city);
    $('#company_country').val('FR');
    $('#company_address').val(streetNumber+ ' '+ streetName + ' ' +city+ ' '+postalCode) ;
    $('.autocomplete-items').remove();

});

/* =========== set a complete adress in company form ===== */


if ($('.company-complete-adress').length > 0 )
{
  $('#company_address').val($('.company-complete-adress').attr('data-complete-adress'));
}

