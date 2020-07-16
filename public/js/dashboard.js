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


/**
 * Update likes post
 */
$('.updateLike').click(function(){
    const url = $(this).data('url');
    var idPost = $(this).data('id');

    $.ajax({
        type: "POST",
        url: url,
        success: function (response) {
            var nbLikes = response.likes;
            $('#nblikespost' + idPost).html(nbLikes);
        },
        error: function (xhr, ajaxOptions, thrownError){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });

    var icon = $(this).find('i');
    if (icon.hasClass('fa-thumbs-up')){
        icon.removeClass('fa-thumbs-up');
        icon.addClass('fa-thumbs-o-up');
    }else if (icon.hasClass('fa-thumbs-o-up')){
        icon.removeClass('fa-thumbs-o-up');
        icon.addClass('fa-thumbs-up');
    }
});

/**
 * Comment post
 */
$('.submit-comment-box').click(function(){
    //console.log($(this).next(".box-comment"));
    const url = $(this).data('url');
    const idPost = $(this).data('id');
    var text = $(this).parent().find('textarea').val();
    var srcImage = $(this).parent().find('img').attr('src');

    var elementHTML = '<div class="box-comment d-flex mb-2"><img src="' + srcImage + '" class="rounded-circle small-avatar" alt="avatar image"> <div class="comment-content"> <span>' + text + '</span> </div> </div>';
    if (text){
        $.ajax({
            context: this,
            type: 'POST',
            url: url,
            data: {
                'content': text
            },
            success: function(response){
                var nbComments = response.comments;
                $(this).parent().find('textarea').val('');
                $(this).parents('.box-comment-input').after(elementHTML);
                $('#nbcommentspost' + idPost).html(nbComments);
            },
            error: function (xhr, ajaxOptions, thrownError){
                alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
            }
        });
    }

});