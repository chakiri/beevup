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
 * add inputs on clicks to publish post
 */
$('.image').click(function () {
    //If not image link from article
    if ($('#post_imageLink').val() == ''){
        if ($('.image-file').hasClass('d-none')){
            $('.image-file').removeClass('d-none');
        }else{
            $('.image-file').addClass('d-none');
        }
    }
});
$('.video').click(function () {
    if ($('.url-youtube').hasClass('d-none')){
        $('.url-youtube').removeClass('d-none');
    }else{
        $('.url-youtube').addClass('d-none');
    }
});
$('.article').click(function () {
    if ($('.url-article').hasClass('d-none')){
        $('.url-article').removeClass('d-none');
    }else{
        $('.url-article').addClass('d-none');
    }
});

/**
 *Get Json data from API
 */
$('#post_urlLink').on('change', function () {
    var url = $('#post_urlLink').val();
    var urlApi = 'https://app-1969cdc6-f757-4c93-b03f-00ff5d016840.cleverapps.io/zebulon/testscrap2.php?url=' + url;
    $.ajax({
        type: 'GET',
        url: urlApi,
        dataType: 'json',
        success: function (result) {
            $('#post_title').val(result.data.title);
            $('#post_description').val(result.data.description);
            console.log(result.data.image);
            if (result.data.image){
                //$('.image-file').addClass('d-none');
                //$('.image-link').removeClass('d-none');
                $('.image').addClass('cursor-not-allowed');
                $('#post_imageLink').val(result.data.image);
                if ($('.image-file').not('.d-none')){
                    $('.image-file').addClass('d-none');
                }
            }
        }
    });
});

/**
 *Disable data article
 */
$('.disable-article').click(function(){
    $('#post_urlLink').val('');
    $('#post_title').val('');
    $('#post_description').val('');
    $('#post_imageLink').val('');
    $('.image').removeClass('cursor-not-allowed');
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
    if (icon.hasClass('fas')){
        icon.removeClass('fas');
        icon.addClass('far');
    }else if (icon.hasClass('far')){
        icon.removeClass('far');
        icon.addClass('fas');
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
    var commentId = $(this).attr('data-comment');
    var url = $(this).attr('data-url') ;
    $.get(url, function (data) {
        $('.modal-content-report-abus').html(data);
        $('.modal-content-report-abus').attr( 'data-post-id', postId );
        $('.modal-content-report-abus').attr( 'data-comment-id', commentId );
    });
})
/**
 * submit abus
 */
$('body').on('click', '.report-abuse-submit-btn', function (e) {

    e.preventDefault();

    var postId = $('.modal-content').attr('data-post-id');
    var commentId =$('.modal-content').attr('data-comment-id');
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

/**
 * GET POST LIKES
 */


$('body').on('click', '.likes-info', function () {


    var url = $(this).attr('data-path');
    $.get(url, function (data) {

        $('.modal-likes-list').html(data);

    });
})


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
 * Display data toggle
 */
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/**
 *Set Name to all upload file
 */
$('.custom-file-input').on('change', function(event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);
});

$('textarea').on({
    input: function(){

        var text = $(this).val();
        span.text(text);
        $(this).height(text ? span.height() : '1.1em');
    },
    focus: function(){
        initSpan($(this));
    }
});