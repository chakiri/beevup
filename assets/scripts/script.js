import $ from 'jquery';

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

/*$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});*/


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

function setAdress(streetNumber,  streetName, postalCode, city,country){
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
    let streetType = ($('option:selected', this).attr('data-street-type') != 'null' ) ? $('option:selected', this).attr('data-street-type') : '' ;
    let streetName  = streetType + ' ' + $('option:selected', this).attr('data-street-name');
    let postalCode  = $('option:selected', this).attr('data-postal-code');
    let city = $('option:selected', this).attr('data-city');
    let country = 'FR';
    $('#registration_get_siret_from_api').prop('checked', false);
    $('.siret-list').hide();

    /*** Add adress if exist ***/
    setAdress(streetNumber,  streetName, postalCode, city,  country );

});

function insertAfter(referenceNode, newNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function getSiret(companyName) {
    var data = "q=denominationUniteLegale%3A%20%22companyName%22%20OR%20nomUniteLegale%3AcompanyName&champs=denominationUniteLegale%2CcodePostalEtablissement%2Csiret%2CcomplementAdresseEtablissement%2CnumeroVoieEtablissement%2CcodePaysEtrangerEtablissement%2ClibelleCommuneEtablissement%2ClibelleVoieEtablissement%2CtypeVoieEtablissement&nombre=1500";

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
                if(etablissements[i].uniteLegale.denominationUniteLegale !='') {
                    selectBox.options[selectBox.options.length] = new Option(etablissements[i].adresseEtablissement.codePostalEtablissement + '-' + etablissements[i].uniteLegale.denominationUniteLegale, etablissements[i].siret);
                } else {
                    selectBox.options[selectBox.options.length] = new Option(etablissements[i].adresseEtablissement.codePostalEtablissement + '-' + etablissements[i].uniteLegale.nomUniteLegale, etablissements[i].siret);

                }
                selectBox.options[selectBox.options.length-1].setAttribute('data-street-number',etablissements[i].adresseEtablissement.numeroVoieEtablissement );
                selectBox.options[selectBox.options.length-1].setAttribute('data-street-type',etablissements[i].adresseEtablissement.typeVoieEtablissement );
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
        if(data.features[i].properties.housenumber != undefined  ){
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
            item.innerHTML = "<strong>" + data.features[i].properties.name + " "+ data.features[i].properties.postcode +" - "+data.features[i].properties.city+"</strong>";
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
    $('#company_addressNumber').val(streetNumber);
    $('#company_addressStreet').val(streetName);
    $('#company_addressPostCode').val(postalCode);
    $('#company_city').val(city);
    $('#company_country').val('FR');
    $('#company_address').val(streetNumber+ ' '+ streetName + ' ' +postalCode+ ' '+city) ;
    $('.autocomplete-items').remove();
});

/* =========== set a complete adress in proper field ===== */
$(document).ready(function(){
    let completeAddress = $('#company_addressNumber').val() + ' ' + $('#company_addressStreet').val() + ' ' +  $('#company_addressPostCode').val() + ' ' +  $('#company_city').val();
    $('#company_address').val(completeAddress);
});


/* ======================get gallery images ========== */
$('.select-from-gallery').click(function(){

    var url = $(this).attr('data-url') ;
    let dataInput = $(this).attr('data-input');
    let idButton = $(this).attr('data-ID');
    $.get(url, function (data) {
        $('#imageGallery .modal-content').attr('data-input', dataInput);
        $('#imageGallery .modal-content').attr('data-id', idButton);
        $('#imageGallery .modal-content').html("<form> <input type='text' class='search-gallery'/></form>");
        $('#imageGallery .modal-content').html(data);
    });
})
$('body').on('click', '.gallery-img', function () {
    unselectAllImages();
    let id = $(this).attr('data-id');
    let status = $(this).attr('data-status');
    let path = $(this).attr('src');
    changeIconColor(id,status);
    $(this).addClass(changeClassName(status));
});

function unselectAllImages(){
    $('.gallery-icon-ok').css('color','gray');
}
function changeIconColor(id,status){
    let color = ''
    color = (status =='') ? 'orange' : 'gray';
    status =  (status =='') ? 'selected' : '';
    $('.gallery-icon-'+id).css('color',color);
    $('.gallery-img-'+id).attr('data-status',status);
}
function changeClassName(status){
    $('.gallery-img').removeClass('selected-image');
    let className =  (status == 'selected') ? '': 'selected-image';
    return className;
}

$('body').on('click', '.save-selected-image', function () {
    let path = $('.selected-image').attr('src');
    let imageFileName = getImageOriginalName(path);
    let dataInput = $('#imageGallery .modal-content').attr('data-input');
    let id = $('#imageGallery .modal-content').attr('data-id');
    $('#'+dataInput).attr('value',imageFileName);
    $('#previous-image'+id+' img').attr('src',path);
    $('#imagegallery').modal('toggle');
});

function getImageOriginalName(str){
    let result = str.split("/");
    let originalName = '';
    if(result.length > 0) {
         originalName = result[result.length - 1];
    }
    return originalName;
}

$('body').on('click', '.search-gallery-btn', function (e) {
    e.preventDefault();
    $('.search-load').removeClass('hide-load');
    let url = $(this).attr('data-url');
    let searchValue =$('.gallery-search').val();
    url = url.replace('keyWords', searchValue);
    $.get(url, function (data) {
        $('.search-load').addClass('hide-load');
        $('#imageGallery .modal-content').html(data);
   });
});
$('#service_price').change(function(){
    let taxRate =  getTaxRate($('#service_vatRate').val());
    let price = $('#service_price').val();
    if(taxRate != ''){

        $('#service_priceTTC').val(parseFloat(price) + parseFloat(price) / 100 * taxRate);
    }
});
$('#service_vatRate').change(function(){
    let taxRate =  getTaxRate($('#service_vatRate').val());
    let price = $('#service_price').val();
    if(taxRate != ''){

        $('#service_priceTTC').val(parseFloat(price) + parseFloat(price) / 100 * taxRate);
    }
});

/*** I created this function because the parseFloat does not works ***/
function getTaxRate(val)
{
    if (val == "2,1")
        return  2.1;
    else if (val == "5,5")
       return  5.5;
    else if(val == "5,5")
        return 2.1;
    else
        return val;
}