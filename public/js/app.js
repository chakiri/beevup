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
$('.add-recommandation').click(function () {

    var companyId = 0 ;
    var serviceId = 0;
    var company = "" ;
    $('#recommandation').modal();
    url = $(this).attr('data-target');
    companyId = $(this).attr('data-company');
    serviceId = $(this).attr('data-service');
    company = $(this).attr('data-company');
    $.get(url, function (data) {
        $('#recommandation .modal-content').html(data);
        $('#recommandation .form-company').val(companyId);
        $('#recommandation .form-service').val(serviceId);
        $('#modal1').modal('open');
    });
})
/*end new recommandation message */

//popover display in show service
$("[data-toggle=popover]").popover();

//toogle
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/**
 *Set Session cookie
 */
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