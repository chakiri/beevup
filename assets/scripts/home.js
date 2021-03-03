//modal choose store for homepage search
/*if (window.location.pathname === '/'){
    if (window.location.search === '' || window.location.search === '?store=BV001'){
        $('#chooseStore').modal('show');
        $('#search_store_querySearch').prop("disabled", true);
    }
    $('#btn-choose-store').click(function (){
        $('#chooseStore').modal('show');
    });
}*/

var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

function success(pos) {
    var crd = pos.coords;

    console.log(`Latitude : ${crd.latitude}`);
    console.log(`Longitude : ${crd.longitude}`);

    $.ajax({
        url: Routing.generate('homepage_locate', {'locate': crd.latitude + '&' + crd.longitude}),
        type: "GET",
        success: function (){
            console.log('redirection succeed');
        },
        error: function()
        {
            console.log('redirection failed');
        }
    })

}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}

//If no geo localisation
/*if (window.location.pathname === '/')
    navigator.geolocation.getCurrentPosition(success, error, options);*/

console.log(Routing.generate('homepage_locate2'))