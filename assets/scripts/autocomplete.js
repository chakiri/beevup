
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
});
