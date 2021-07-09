
$(document).ready(function(){
    $('#service_category').autocomplete({
        lookup: function (query, done) {
            const autocompleteUrl = Routing.generate('service_category_list') + '?query=' + query;

            $.ajax({
                url: autocompleteUrl
            }).then(function(data) {
                var result = {
                    suggestions: data
                };
                done(result);
            });
        },
        /*onSelect: function (suggestion) {
            alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
        }*/
    });


    $('#home_search_postalCode').autocomplete({
        lookup: function (query, done) {
            const autocompleteUrl = Routing.generate('extern_api_communes') + '?query=' + query;

            $.ajax({
                url: autocompleteUrl
            }).then(function(data) {
                var result = {
                    suggestions: data
                };
                done(result);
            });
        },
        onSelect: function (suggestion) {
            console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);

            //Get lat and lon from postalcode
            const url = Routing.generate('extern_geocode', {'code': suggestion.value});

            //Activate btn submit
            $(".search-submit-btn").removeAttr("disabled");
            $(".search-submit-btn").removeClass("orange-btn-greyed");

            $.ajax({
                url: url
            }).then(function(data) {
                $('#home_search_lat').val(data['lat'])
                $('#home_search_lon').val(data['lon'])
            });

        }
    })



});
