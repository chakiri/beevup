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
    $('#post_category').val($(this).data('id'));

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
            //If div infolike is hidden
            if (nbLikes > 0){
                $('#nblikespost' + idPost).parents('.likes-info').removeClass("d-none");
            }else{
                $('#nblikespost' + idPost).parents('.likes-info').addClass("d-none");
            }
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
    const url = $(this).data('url');
    const idPost = $(this).parents('.post-interaction').data('id');
    var text = $(this).parent().find('textarea').val();
    var srcImage = $(this).parent().find('img').attr('src');

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
                var idComment = response.idComment;
                var elementHTML = '<div class="box-comment d-flex"><div class="d-flex mb-2"><img src="' + srcImage + '" class="rounded-circle small-avatar" alt="avatar image"> <div class="comment-content"> <span>' + text + '</span></div></div> <div class="hover-btn"><button class="btn delete-comment" data-url="/comment/' + idComment + '/remove"><small>supprimer</small></button></div></div>';
                $(this).parent().find('textarea').val('');
                $(this).parents('.box-comment-input').after(elementHTML);
                $('#nbcommentspost' + idPost).html(nbComments);
                //If > 0 display div
                if(nbComments > 0){
                    $('#nbcommentspost' + idPost).parents('.comments-info').removeClass("d-none");
                }
            },
            error: function (xhr, ajaxOptions, thrownError){
                alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
            }
        });
    }
});

/**
 * Remove comment
 */
// on click on id is handling all clicked deleted btn on #post-interaction even if it's added after bounding by js
$('.post-interaction').on('click', '.delete-comment', function() {
    var comment = $(this).parents('.box-comment');
    const url = $(this).data('url');
    const idPost = $(this).parents('.post-interaction').data('id');

    $.ajax({
        type: 'GET',
        url: url,
        success: function(response){
            var nbComments = response.comments;
            console.log("comment removed !");
            comment.remove();
            $('#nbcommentspost' + idPost).html(nbComments);
            //If 0 hide div
            if(nbComments == 0){
                $('#nbcommentspost' + idPost).parents('.comments-info').addClass("d-none");
            }
        },
        error: function(xhr){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });
});

$('.comments .comment').click(function(){
    $(this).parent().next().next().find('textarea').focus();
});

/**
 * new abus (open modal )
 */
$('.report-abuse-btn').click(function(e){

    var postId = $(this).attr('data-post');
    var url = $(this).attr('data-url') ;
    $.get(url, function (data) {
        $('.modal-content-report-abus').html(data);
        $('.modal-content-report-abus').attr( 'data-post-id', postId );
    });
})
/**
 * submit abus
 */
$('body').on('click', '.report-abuse-submit-btn', function (e) {

    e.preventDefault();

    var postId = $('.modal-content').attr('data-post-id');
    var url = $(this).attr('data-target');
    var description ='';
    if(postId > 0) {
       // description = $('.abuse-description-cls').val();
        description = $(this).parents('.card-body').find('textarea').val();
    }
    else {
        description = $('.abuse-description-cls').val();
    }
    var data = {description : description};

    if(description != '') {
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (data, dataType) {
                $('.message').addClass('success-message ').append('Notre équipe traitera votre réclamation au plus vite.\n' +
                    'Merci pour votre aide et bonne journée');
                if (postId != 0) {

                    setTimeout(function () {
                        $('.modal-report-post ').modal('hide');
                        $('.message').removeClass('success-message').empty();
                        $('#abuse_description').val('');
                       // $('#abuse-post-btn-'+ postId).hide();

                    }, 2000);
                    //
                } else {

                    setTimeout(function () {
                        $('#modal-report-abuse-comment-' + commentId).modal('hide');
                        $('.message').removeClass('success-message').empty();
                        $('.abuse-description-' + commentId).val('');
                        $('#report-comment-abuse-btn-' + commentId).hide();


                    }, 2000);
                }

            }
        });
    }
})

/**
 * update poste
 */

$('.update-post-btn').click(function(e){
    var postId = $(this).attr('data-post');
    var url = $(this).attr('data-url') ;
    $.get(url, function (data) {
        $('.modal-content-update-post').html(data);
    });
})