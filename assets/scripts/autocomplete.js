
$(document).ready(function(){
    $('#service_category').autocomplete({hint: false}, [
        {
            displayKey: 'name',
            debounce: 500, // only request every 1/2 second
            source: function(query, cb) {
                const autocompleteUrl = Routing.generate('service_category_list') + '?query=' + query;
                $.ajax({
                    url: autocompleteUrl
                }).then(function(data) {
                    if (data.categories.length > 0){
                        cb(data.categories);
                    }else{
                        cb([{ name: '0 résultat trouvé'}])
                    }
                });
            },
        }
    ])
});
