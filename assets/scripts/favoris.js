
$('.add-favoris').click(function() {

    var url = '';
    var userId = $(this).attr('data-user-id');
    var selectedProfileId = $(this).attr('data-profile-id');
    var selectedProfileName = $('.company-user-profile-' +selectedProfileId).text();
    var selectedProfileURL = $('.company-user-profile-' +selectedProfileId).attr('href');
    var selectedProfileImageUrl = $('.company-user-profile-img-' +selectedProfileId).attr('src');
    var selectProfileCompanyName = $('.user-companyName-'+selectedProfileId).text();
    var selectProfileCompanyURL = $('.user-companyName-'+selectedProfileId).attr('href');
    //var selectProfileCompanyURL = $('.user-companyName-'+selectedProfileId).text();

    if ($(this).hasClass("text-warning")) {

         /***** remove from favoris *****/
        url = $(this).attr('data-delete');
        $('#result-user-item-' + userId).removeClass('text-warning').addClass('text-muted');
        $('#result-user-item-' + userId).removeClass('fas').addClass('far');

        if( $('.profil-add-favoris').length) {

            $('.profil-add-favoris').text('').append("Ajouter aux favoris");
            $('.profil-add-favoris').removeClass('text-warning').addClass('text-muted');
        }

        $('.favorit-user-'+selectedProfileId).remove();
        if($('.suggestion-usd').length ==0){
            $('.suggestions-list').append(` <div class="p-3"><p>Aucun favori</p></div>`);
        }


    }
    else {


        /***** add to favoris  *****/
        url = $(this).attr('data-target');
        $('#result-user-item-' + userId).removeClass('text-muted').addClass('text-warning');
        $('#result-user-item-' + userId).removeClass('far').addClass('fas');




        /**********add to favoris list *******************/
           if( $('.profil-add-favoris').length > 0 ) {
               $('.profil-add-favoris').text('').append("Supprimer favoris");
                $('.profil-add-favoris').removeClass('text-muted').addClass('text-warning');
            }


        if ($('.favorit-user-'+userId).length == 0) {
            var addSelectUserToFavorisList = `
             <div class='suggestion-usd favorit-user-` + selectedProfileId + `'>
                        <div class="" style="float:left">
                            <a href="` + selectedProfileURL + `">
                            <img src="` + selectedProfileImageUrl + `" style="width: 35px;height:35px">
                             </a>
                        </div>
                                                                     
                 
                  <div class="sgt-text hover-info">
                   <h4>
                         <a href="` + selectedProfileURL + `">
                          ` + selectedProfileName + `
                         </a>
                   </h4>
                   <span class="company-link">
                     <a href="` + selectProfileCompanyURL + `"> ` + selectProfileCompanyName + ` </a>
                   </span>
                   <div class="popup-info box">
                    <div class="d-flex">
                        <a href="#">
                            <img src="` + selectedProfileImageUrl + `" class="rounded-circle medium-avatar" alt="avatar image">
                        </a>
                        <div>
                            <p> ` + selectedProfileName + `</p>
                            <small>` + selectProfileCompanyName + `</small>
                        </div>
                    </div>
                    <div class="d-flex pt-3">
                        <a type="button" href="/chat/private/` + userId + `" class="btn small-btn orange-bg mr-2">Contacter</a>
                        <a type="button" href="` + selectedProfileURL + `" class="btn small-btn white-bg">Voir la fiche</a>
                    </div>
                </div>
                  </div>
             </div>`;
            if($('.suggestion-usd').length ==0){
                $('.p-3').remove();
            }
            $('.suggestions-list').append(addSelectUserToFavorisList);
        }

    }
    $.get(url, function (data) {
    });
});

/********************Add admin company to favorit ************************/

$('.add-company-favoris').click(function() {
    var url = '';
    var companyAdministratorName =   $(this).attr('data-company-administrator-name');
    var companyAdministratorImg =    $(this).attr('data-company-administrator-img');

    var userId =  $(this).attr('data-company-administrator-id');
    var selectedProfileId = $(this).attr('data-company-administrator-profile-id');
    var companyId = $(this).attr('data-company-id');
    var companyName =  $(this).attr('data-company-name');
    if ($(this).hasClass("text-warning")) {
        url = $(this).attr('data-delete');
        $('#result-company-item-' + companyId).removeClass('text-warning').addClass('text-muted');
        $('#result-company-item-' + companyId).removeClass('fas').addClass('far');
        $('.favorit-user-'+selectedProfileId).remove();
        if( $('#result-company-item-' + companyId).length ==0) {
           $('.company-page').text('').append("Ajouter aux favoris");
            $('.add-company-favoris').removeClass('text-warning').addClass('text-muted');



        }
        if($('.acompany-page').length > 0){
            $('.add-company-favoris').text('').append("Ajouter aux favoris");
        }


    } else {
        url = $(this).attr('data-target');
        $('#result-company-item-' + companyId).removeClass('text-muted').addClass('text-warning');
        $('#result-company-item-' + companyId).removeClass('far').addClass('fas');
        if( $('#result-company-item-' + companyId).length ==0) {

            $('.company-page').text('').append("Supprimer favoris");
             $('.add-company-favoris').removeClass('text-muted').addClass('text-warning');


        }
        if($('.acompany-page').length > 0){
            $('.add-company-favoris').text('').append("Supprimer favoris");

        }
        if ($('.favorit-user-'+selectedProfileId).length == 0) {
            var addSelectUserToFavorisList = `
             <div class='suggestion-usd favorit-user-`+selectedProfileId+`'>
                        <div class="" style="float:left">
                            <a href="/account/` + selectedProfileId + `">
                            <img src="`+companyAdministratorImg+`" style="width: 35px;height:35px">
                             </a>
                        </div>
                                                                     
                 
                  <div class="sgt-text hover-info">
                   <h4>
                         <a href="/account/`+ selectedProfileId + `">
                          ` + companyAdministratorName + `
                         </a>
                   </h4>
                   <span class="company-link">
                     <a href="/company/` + companyId + `"> ` + companyName + ` </a>
                   </span>
                   <div class="popup-info box">
                    <div class="d-flex">
                        <a href="#">
                            <img src="` + companyAdministratorImg + `"  class="rounded-circle medium-avatar" alt="avatar image">
                        </a>
                        <div>
                            <p> ` + companyAdministratorName + `</p>
                            <small>` + companyName + `</small>
                        </div>
                    </div>
                    <div class="d-flex pt-3">
                        <a type="button" href="/chat/private/` + userId + `" class="btn small-btn orange-bg mr-2">Contacter</a>
                        <a type="button" href="/account/` + selectedProfileId + `" class="btn small-btn white-bg">Voir la fiche</a>
                    </div>
                </div>
                  </div>
             </div>`;
            if($('.suggestion-usd').length ==0){
                $('.p-3').remove();
            }
            $('.suggestions-list').append(addSelectUserToFavorisList);
        }


    }
    $.get(url, function (data) {
    });
});