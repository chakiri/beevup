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
    $('#post_category').val($(this).data('name'));

    //Put active on click
    $('.icon-type-post').each(function(){
        $(this).find('a').removeClass('active');
    });
    $(this).find('a').addClass('active');
});

/**
 * Disable submit button while type empty
 */

$('.modal-add-post :submit').click(function(e){
    if ($('#post_category').val() == ""){
        e.preventDefault();
        $('.type-post-error').css('display', 'block ');
    }
});
//Only video or image
$('.image').click(function () {
    $('#post_urlYoutube').val("");
});
$('.video').click(function () {
    $('#post_imageFile').val('');
    $(".custom-file-label").html("Une image vaut mille mots");
});

/* end publish post */