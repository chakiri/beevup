//declare map
let map = null;

function initMap(){
    map =  L.map('mapid').setView([51.505, -0.09], 13);
}

class Utils {
    static getCurrentPosition(success) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => success('js', position.coords),
                () => success('php', Utils.getCurrentPositionWithPhp()),
                { enableHighAccuracy: true }
            );
        } else {
            console.error('navigator.geolocation is not enable to this navigator');
            success('php', Utils.getCurrentPositionWithPhp());
        }
    }

    static getCurrentPositionWithPhp() {
        console.log('Get position by PHP');
    }
}

//Map page inscription
if($('#mapid').length > 0) {
    var allStores = '';
    $.ajax({
        url: '/map',
        type: 'POST',
        async: false,
        success: function (data) {
            allStores = JSON.parse(data);
        }
    });

    //Init map
    if (!map)   initMap();

    //marker current user
    var greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    map.locate({setView: true, watch: true}) /* This will return map so you can do chaining */
        .on('locationfound', function (e) {

            L.marker([e.latitude, e.longitude], {icon: greenIcon}).addTo(map).bindPopup("<b>Je suis là</b>").openPopup();

            let currentUserLongitude = e.longitude;
            let currentUserLatitude = e.latitude;

            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',{attribution: ''}).addTo(map);

            for (var i = 0; i < allStores.stores.length; i++) {
                if (distance(currentUserLatitude, currentUserLongitude, allStores.stores[i].lat, parseFloat(allStores.stores[i].lng), "K") <= 1000) {
                    L.marker([allStores.stores[i].lat, parseFloat(allStores.stores[i].lng)]).addTo(map).bindPopup("<b>" + allStores.stores[i].name + "</b><br/><span style='color:#FF7F50'>" + allStores.stores[i].adress + "</span>");
                }
            }
        });

    L.popup();
}

//Calculate distance between lat and long
function distance(lat1, lon1, lat2, lon2, unit) {
    var radlat1 = Math.PI * lat1/180
    var radlat2 = Math.PI * lat2/180
    var theta = lon1-lon2
    var radtheta = Math.PI * theta/180
    var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);

    if (dist > 1)   dist = 1;

    dist = Math.acos(dist)
    dist = dist * 180/Math.PI
    dist = dist * 60 * 1.1515
    if (unit=="K")  dist = dist * 1.609344
    if (unit=="N")  dist = dist * 0.8684

    return dist
}

