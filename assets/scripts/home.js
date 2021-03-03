//Choose store from modal
$('#btn-choose-store').click(function (){
    $('#chooseStore').modal('show');
});

//Select store in modal
$('#chooseStore button').click(function (){
    //Get selected option value
    let storeReference = $('#chooseStore select option:selected').val();
    //Do redirection with selected store
    window.location.href = Routing.generate('homepage_store', {'store': storeReference});
});

/**
 * Geo localisation user and redirection
 */
var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

//If no geo localisation
if (window.location.pathname === '/')
    navigator.geolocation.getCurrentPosition(success, error, options);

function success(pos) {
    let crd = pos.coords;

    console.log(`Latitude : ${crd.latitude}`);
    console.log(`Longitude : ${crd.longitude}`);

    window.location.href = Routing.generate('homepage_locate', {'locate': crd.latitude + '&' + crd.longitude});
}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}
/*
 * End Geo localisation
**/
