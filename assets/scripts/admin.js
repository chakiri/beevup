import '../styles/admin.css';
import 'mobile-nav';


"use strict";
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
}


if($('#store_country').length  > 0) {
    var action = getUrlParameter('action');
    if($('#store_country').val() == 'AF' && action =='new') {
        $('#store_country').val("FR");
    }
}
