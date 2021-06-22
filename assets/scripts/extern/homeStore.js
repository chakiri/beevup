//Choose store from modal
$('#btn-choose-store').click(function (){
    $('#chooseStore').modal('show');
});

//Select store in modal
$('#chooseStore button').click(function (){
    //Get selected option value
    let storeReference = $('#chooseStore select option:selected').val();
    //Do redirection with selected store
    window.location.href = Routing.generate('homestore', {'store': storeReference});
});

/**
 * Geo localisation user and redirection
 */
var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

//If there is no params
if (window.location.pathname === '/' && !getUrlParameter('locate') && !getUrlParameter('store'))
    navigator.geolocation.getCurrentPosition(success, error, options);

function success(pos) {
    let crd = pos.coords;

    console.log(`Latitude : ${crd.latitude}`);
    console.log(`Longitude : ${crd.longitude}`);

    window.location.href = Routing.generate('homestore', {'locate': crd.latitude + ',' + crd.longitude});
}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}
/*
 * End Geo localisation
**/

//Function get params from url
function getUrlParameter(sParam) {
    let sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
}

//Scroll to the top of page
$('.scrollToTop').click(function (){
    window.scrollTo(0, 0);
});
