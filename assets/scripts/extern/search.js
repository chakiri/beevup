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

    $('#home_search_lat').val(crd.latitude);
    $('#home_search_lon').val(crd.longitude);

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
        $('#home_search_postalCode').val(data['codesPostaux'][0] + ' - ' + data['nom']);

        //Activate btn submit
        $(".search-submit-btn").removeAttr("disabled");
        $(".search-submit-btn").removeClass("orange-btn-greyed");
    });
}

$('#home_search_postalCode').on('input', function() {
    // If change disable btn
    $(".search-submit-btn").prop('disabled', true);
    $(".search-submit-btn").addClass("orange-btn-greyed");
});

if ($('#home_search_postalCode').val() == ''){
    // If change disable btn
    $(".search-submit-btn").prop('disabled', true);
    $(".search-submit-btn").addClass("orange-btn-greyed");
}