
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
        $('#result-user-item-' + userId).removeClass('fa-star').addClass('fa-star-o');


        if( $('.profil-add-favoris').length) {
            $('.profil-add-favoris').text('').append("Ajouter aux Favoris");
        }
        $('.favorit-user-'+selectedProfileId).remove();

    } else {
        /***** add to favoris  *****/





        url = $(this).attr('data-target');
        $('#result-user-item-' + userId).removeClass('text-muted').addClass('text-warning');
        $('#result-user-item-' + userId).removeClass('fa-star-o').addClass('fa-star');

        /**********add to favoris list *******************/



        if( $('.profil-add-favoris').length ) {
            $('.profil-add-favoris').text('').append("Favoris");
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
    if(companyAdministratorImg == ''){
        companyAdministratorImg ='http://via.placeholder.com/90x90';
    }
    var userId =  $(this).attr('data-company-administrator-id');
    var selectedProfileId = $(this).attr('data-company-administrator-profile-id');
    var companyId = $(this).attr('data-company-id');
    var companyName =  $(this).attr('data-company-name');
    if ($(this).hasClass("text-warning")) {
        url = $(this).attr('data-delete');
        $('#result-company-item-' + companyId).removeClass('text-warning').addClass('text-muted');
        $('#result-company-item-' + companyId).removeClass('fa-star').addClass('fa-star-o');
        $('.favorit-user-'+selectedProfileId).remove();


    } else {
        url = $(this).attr('data-target');
        $('#result-company-item-' + companyId).removeClass('text-muted').addClass('text-warning');
        $('#result-company-item-' + companyId).removeClass('fa-star-o').addClass('fa-star');
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
                            <img src="http://via.placeholder.com/90x90" class="rounded-circle medium-avatar" alt="avatar image">
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
            $('.suggestions-list').append(addSelectUserToFavorisList);
        }


    }
    $.get(url, function (data) {
    });
});