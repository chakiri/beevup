/**
 * filter News
 */

$('#news-filter li').click(function() {

    var selectedItem = $(this).attr('value');
    if(selectedItem == 'All'){
        $(".post").each(function () {
            $(this).removeClass('hide-item');
        });
    } else {
        $(".post").each(function () {
            if ($(this).attr('data-category') != selectedItem) {
                $(this).addClass('hide-item');
            } else {
                $(this).removeClass('hide-item');
            }
        });
    }

    $('.btn-show-more').hide();

});

/**
 * publish a new post
 */
$('.add-post').click(function(){
    $('.modal-add-post').modal();
    url = $(this).attr('data-target');
    $.get(url, function (data) {
        $('.modal-post-content').html(data);
        //$('#modal1').modal('open');
    });
});

/**
 * Synchronise icons type with choice select
 */
$('.icon-type-post').click(function(){
    console.log($(this).attr('id'));
    $('#post_category').val($(this).attr('id'));
});

/**
 * Disable submit button while type empty
 */
// $('#create-post-form :input[type="submit"]').prop('disabled', true);
// $('#create-post-form :input[type="submit"]').css({'cursor': 'not-allowed'});

$('.modal-add-post :submit').click(function(e){
    console.log($('#post_category').val());
    if ($('#post_category').val() == ""){
        e.preventDefault();
        $('.type-post-error').css('display', 'block ');
    }
});

/* end publish post */