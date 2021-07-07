import '../../styles/extern/search.css';

import '../autocomplete';

$('.js-search-locate-btn').click(function (){
    getLatLonFromLocalization();
});

/**
* Function Geo localization to get lat and lon
*/
function getLatLonFromLocalization() {
    var options = {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
    };

    navigator.geolocation.getCurrentPosition(success, error, options);
}


function success(pos) {
    let crd = pos.coords;

    console.log(`Latitude : ${crd.latitude}`);
    console.log(`Longitude : ${crd.longitude}`);

    getDataFromLatLon(crd.latitude, crd.longitude)
}

function error(err) {
    console.warn(`ERREUR (${err.code}): ${err.message}`);
}

function getDataFromLatLon(lat, lon){
    const url = Routing.generate('extern_api_locate') + '?lat=' + lat + '&lon=' + lon;

    $.ajax({
        url: url
    }).then(function(data) {
        console.log(data);
        $('#home_search_postalCode').val(data['codesPostaux'][0]);
    });
}