
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


    /* Autocomplete search form postal code */
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
            $('#home_search_lat').val(suggestion.data.latitude);
            $('#home_search_lon').val(suggestion.data.longitude);

            //Activate btn submit
            $(".search-submit-btn").removeAttr("disabled");
            $(".search-submit-btn").removeClass("orange-btn-greyed");
        }
    })

});
