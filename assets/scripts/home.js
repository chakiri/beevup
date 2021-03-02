//modal choose store for homepage search
if (window.location.pathname === '/'){
    if (window.location.search === '' || window.location.search === '?store=BV001'){
        $('#chooseStore').modal('show');
        $('#search_store_querySearch').prop("disabled", true);
    }
    $('#btn-choose-store').click(function (){
        $('#chooseStore').modal('show');
    });
}

var options = {
    enableHighAccuracy: true,
    timeout: 5000,
    maximumAge: 0
};

function success(pos) {
    var crd = pos.coords;

    console.log('Votre position actuelle est :');
    console.log(`Latitude : ${crd.latitude}`);
    console.log(`Longitude : ${crd.longitude}`);
    console.log(`La précision est de ${crd.accuracy} mètres.`);

    /*$.ajax({
        url: Routing.generate('homepage'),
        type: "GET",
        data: {
            latitude: crd.latitude,
            longitude: crd.longitude
        },
        success: function (){
            console.log(Routing.generate('homepage'));
            console.log('yesss');
        }
    })*/

    $.post(Routing.generate('homepage'),{
        latitude: crd.latitude,
        longitude: crd.longitude,
    })
}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}

navigator.geolocation.getCurrentPosition(success, error, options);

