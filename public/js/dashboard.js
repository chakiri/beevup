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
                console.log('Comment added !');
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
 * Set seen opportunities notification
 */
function setSeenOpportunityNotification(e){

    const url = $(e).data('url');

    $.ajax({
        type: 'GET',
        url: url,
        success: function (response){
            console.log('opportunity notification seen');
        },
        error: function(xhr){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });
}

/**
 *Set Session cookie
 */
$('#cookies a').click(function(){
    const url = $(this).data('url');

    $.ajax({
        type: 'GET',
        url: url,
        success: function (){
            console.log('Set cookie session');
            $('#cookies').hide();
        },
        error: function(xhr){
            alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
        }
    });
});