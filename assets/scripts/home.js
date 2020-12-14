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

if($('#mapid').length > 0 && window.innerWidth > 769) {
    var allStores = '';
    $.ajax({
        url: '/map',
        type: 'POST',
        async: false,
        success: function (data) {
            allStores = JSON.parse(data);
        }
    });

    var currentUserLongitude = "";
    var currentUserLatitude = "";
    var mymap = L.map('mapid').setView([51.505, -0.09], 13);
    mymap.locate({setView: true, watch: true}) /* This will return map so you can do chaining */
        .on('locationfound', function (e) {
            var greenIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            var marker = L.marker([e.latitude, e.longitude], {icon: greenIcon}).addTo(mymap).bindPopup("<b>Je suis là</b>").openPopup();
            currentUserLongitude = e.longitude;
            currentUserLatitude = e.latitude;

            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',{
                attribution: ''}).addTo(mymap);
            for (var i = 0; i < allStores.stores.length; i++) {
                if (distance(currentUserLatitude, currentUserLongitude, allStores.stores[i].lat, parseFloat(allStores.stores[i].lng), "K") <= 1000) {
                    var marker = L.marker([allStores.stores[i].lat, parseFloat(allStores.stores[i].lng)]).addTo(mymap).bindPopup("<b>" + allStores.stores[i].name + "</b><br/><span style='color:#FF7F50'>" + allStores.stores[i].adress + "</span>");
                }
            }
        });
    var popup = L.popup();
}