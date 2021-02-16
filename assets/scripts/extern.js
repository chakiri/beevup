// ============= beContacted =========
import $ from "jquery";
import 'bootstrap';

/**
 *Load beContacted form in modal
 */
$('.be-contacted-btn').click(function(){
    $('#beContacted').modal();
    let url = $(this).attr('data-target');
    $.get(url, function (data) {
        $('#beContacted .modal-content').html(data);
    });
});
//display errors forms beContacted in modal
$('#beContactedForm').submit(function( event ) {
    event.preventDefault();

    let formSerialize = $(this).serialize();
    let url = $(this).attr('action');
    let redirectUrl = $(this).data('redirect');

    $.ajax({
        type: "POST",
        url: url,
        data: formSerialize,
        success: function(data) {
            window.location.href = redirectUrl;
        },
        error: function(xhr) {
            for (var key in xhr.responseJSON.data) {
                $('#beContactedForm input[name="be_contacted[' + key + ']"]').nextAll().remove();
                $('#beContactedForm input[name="be_contacted[' + key + ']"]').after('<ul class="errors"><li>' + xhr.responseJSON.data[key] + '</li></ul>');
            }
        }
    });
});


//============== open street map =========//
function distance(lat1, lon1, lat2, lon2, unit) {
    var radlat1 = Math.PI * lat1/180
    var radlat2 = Math.PI * lat2/180
    var theta = lon1-lon2
    var radtheta = Math.PI * theta/180
    var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    if (dist > 1) {
        dist = 1;
    }
    dist = Math.acos(dist)
    dist = dist * 180/Math.PI
    dist = dist * 60 * 1.1515
    if (unit=="K") { dist = dist * 1.609344 }
    if (unit=="N") { dist = dist * 0.8684 }
    return dist
}


if($('.external_company_show #mapid').length > 0 && window.innerWidth > 769) {
    var latitude = $('.map').data('lat');
    var longitude = $('.map').data('lon');

    // Creating map options
    var mapOptions = {
        center: [latitude, longitude],
        zoom: 13
    }
    //Check if map already initialize
    var container = L.DomUtil.get('mapid');
    if(container != null){
        container._leaflet_id = null;
    }

    // Creating a map object
    var map = L.map('mapid', mapOptions);

    // Creating a Layer object
    var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

    // Adding layer to the map
    map.addLayer(layer);

    // Creating a marker
    var marker = L.marker([latitude, longitude]);

    // Adding marker to the map
    marker.addTo(map);
}


