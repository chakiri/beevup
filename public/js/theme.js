(function ($) {
  "use strict";

  // Preloader (if the #preloader div exists)
  $(window).on('load', function () {
    if ($('#preloader').length) {
      $('#preloader').delay(100).fadeOut('slow', function () {
        $(this).remove();
      });
    }
  });

  // Back to top button
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
      $('.back-to-top').fadeIn('slow');
    } else {
      $('.back-to-top').fadeOut('slow');
    }
  });
  $('.back-to-top').click(function(){
    $('html, body').animate({scrollTop : 0},1500, 'easeInOutExpo');
    return false;
  });

  // Initiate the wowjs animation library
  new WOW().init();

  // Header scroll class
  $(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
      $('#header').addClass('header-scrolled');
      $('.logo img').addClass('logo-scrolled');
    } else {
      $('#header').removeClass('header-scrolled');
      $('.logo img').removeClass('logo-scrolled');
    }
  });

  if ($(window).scrollTop() > 100) {
    $('#header').addClass('header-scrolled');
  }

  // Smooth scroll for the navigation and links with .scrollto classes
  $('.main-nav a, .mobile-nav a, .scrollto').on('click', function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      if (target.length) {
        var top_space = 0;

        if ($('#header').length) {
          top_space = $('#header').outerHeight();

          if (! $('#header').hasClass('header-scrolled')) {
            top_space = top_space - 20;
          }
        }

        $('html, body').animate({
          scrollTop: target.offset().top - top_space
        }, 1500, 'easeInOutExpo');

        if ($(this).parents('.main-nav, .mobile-nav').length) {
          $('.main-nav .active, .mobile-nav .active').removeClass('active');
          $(this).closest('li').addClass('active');
        }

        if ($('body').hasClass('mobile-nav-active')) {
          $('body').removeClass('mobile-nav-active');
          $('.mobile-nav-toggle i').toggleClass('fa-times fa-bars');
          $('.mobile-nav-overly').fadeOut();
        }
        return false;
      }
    }
  });

  // Navigation active state on scroll
  var nav_sections = $('section');
  var main_nav = $('.main-nav, .mobile-nav');
  var main_nav_height = $('#header').outerHeight();

  $(window).on('scroll', function () {
    var cur_pos = $(this).scrollTop();
  
    nav_sections.each(function() {
      var top = $(this).offset().top - main_nav_height,
          bottom = top + $(this).outerHeight();
  
      if (cur_pos >= top && cur_pos <= bottom) {
        main_nav.find('li').removeClass('active');
        main_nav.find('a[href="#'+$(this).attr('id')+'"]').parent('li').addClass('active');
      }
    });
  });

  // jQuery counterUp (used in Whu Us section)
  $('[data-toggle="counter-up"]').counterUp({
    delay: 10,
    time: 1000
  });

  // Porfolio isotope and filter
  $(window).on('load', function () {
    var portfolioIsotope = $('.portfolio-container').isotope({
      itemSelector: '.portfolio-item'
    });
    $('#portfolio-flters li').on( 'click', function() {
      $("#portfolio-flters li").removeClass('filter-active');
      $(this).addClass('filter-active');
  
      portfolioIsotope.isotope({ filter: $(this).data('filter') });
    });
  });

  // Testimonials carousel (uses the Owl Carousel library)
  $(".testimonials-carousel").owlCarousel({
    autoplay: true,
    dots: true,
    loop: true,
    items: 1
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
 })

 /* end publish post */

  $('.like-button').click(function(){
    var postId = $(this).attr('data-post-id');
    var postLikesNumber = Number($('#post-likes-number-'+postId).text());
    var newLikeStructure = '';
    var action = '';
    if($(this).hasClass('post-liked')== false)
    {
      action = 'add';
      $(this).addClass('post-liked');
      url = 'post/'+postId+'/update-post-likes/add';
      var dataPath = $('#post-'+postId+'-likes-number').attr('data-path');
      newLikeStructure = `<span id="likes-list-`+postId+`" 
      class="post-likes"  
      data-post="`+postId+`"  
      data-toggle="modal" 
      data-target="#LikesList"
      data-whatever="@mdo"
      data-path="`+dataPath+`">
      <i id="post-likes-icon-`+postId+`" class="fa fa-thumbs-up text-primary" aria-hidden="true"></i> 
                            <span id ="post-likes-number-`+postId+`" class="post-likes-number">1</span></span>`;
    }
    else{
      action = 'remove';
      $(this).removeClass('post-liked');
      url = 'post/'+postId+'/update-post-likes/remove';
    }
    $.get(url, function (data) {
      if(action =='add') {
         if(Number(postLikesNumber) > 0) {
          $('#post-likes-number-'+postId).text(Number(postLikesNumber) + 1);
          } else {
            $('#post-'+postId+'-likes-number').prepend(newLikeStructure);
        }
    }  else {
      if(Number(postLikesNumber) - 1 == 0) {
        $('#post-likes-icon-'+postId).remove();
        $('#post-likes-number-'+postId).remove();
      } else {
      $('#post-likes-number-'+postId).text(Number(postLikesNumber) - 1);
      }
    }
    });
    
  });


 /* comment button*/
 $('.comment-button').click(function(){
  var postId = $(this).attr('data-post-id');
  $('#comment-section-'+postId).removeClass('hidden').addClass('visible');
  $('#comments-section-'+postId).removeClass('hidden').addClass('visible');
 })
 /* end comment*/



 var span = $('<span>').css('display','inline-block')
.css('word-break','break-all').appendTo('body').css('visibility','hidden');
 function initSpan(textarea){
  span.text(textarea.text())
      .width(textarea.width())      
      .css('font',textarea.css('font'));
}

 $('.post-add-comment').on({
  input: function(){
     var text = $(this).val();      
     span.text(text);      
     $(this).height(text ? span.height() : '1.1em');
  },
  focus: function(){           
     initSpan($(this));
  },
  keypress: function(e){
     //cancel the Enter keystroke, otherwise a new line will be created
     //This ensures the correct behavior when user types Enter 
     //into an input field
      if(e.which == 13 ) {
      var postId = $(this).attr('data-post-id');
      $(this).closest("form").submit();
      var url = $('.add-comment-form').attr('data-target')+'/'+postId;
      var userName = $(this).attr('data-user');
      var currentCommentNumber = parseInt($("#post-"+postId+'-comments-number').text());
      var newCommentsNumber = 0;
      newCommentsNumber = currentCommentNumber + 1;
      if(newCommentsNumber > 1)
       {
          $('#post-'+postId+'-comments-number').text(newCommentsNumber);
       } else {
        newCommentsNumber = 1;
        var numberOfCommentsStructure = `<span id="post-`+postId+`-comments-number">` +newCommentsNumber+ ` </span>
                                          commentaire`;
        $('#comments-button-'+postId).prepend(numberOfCommentsStructure);
      }
       
      var comment = $("#post-add-comment-"+postId).val();
      var commentUserImg = $("#current-user-img-"+postId).attr('data-img');
      var WindowWidth = $( window ).width();
      if(WindowWidth < 1024)
      {
        var userImageWidth = 'col-2';
        var commentWidth = 'col-8';
      }
      else
      {
        var userImageWidth = 'col-1';
        var commentWidth = 'col-9';
      }
       
      if(comment != '')
      {
          $.ajax({
            type: "POST",
            url: url,
            data: "comment=" + comment,
            success: function(data) {
              $(".post-add-comment").val('');
              var newCommentId = data;
              var target = $('.delete-comment-btn').attr('data-target');
             
              if(target != null && target != undefined) {
                target = target.replace(/[0-9]+/, newCommentId);
              }
              else{
                target = $('.post-add-comment').attr('data-target');
                target = target.replace(/[0-9]+/, newCommentId);
                
              }
              var commentStructure = `
                  <div id='comment-id-`+newCommentId+`' class='row user-comment' style='background-color:#f3f6f8;border-radius:10px;padding:10px'>
                    <div class="user-image `+userImageWidth+`" style="flot:left">
                      <a href='#'>
                          <img class='media-object photo-profile' src='/images/profiles/`+commentUserImg+`'  width='32' height='32' alt=''>
                        </a>
                    </div>
                    <div class='comment `+commentWidth+`'  style='flot:left'>
                        <a href='#' class='comment-user'><p class="comment-owner">`+userName+`</p></a> 
                        <p class='comment-time'>à l\'instant</p>
                        <div id="comment-description-`+newCommentId+`" class='comment-text'>`
                        +comment+
                        `</div>
                    </div>
                    <div class='delete-comment col-2'>
                      <button class='delete-comment-btn' data-comment-id='`+newCommentId+`' data-post-id='`+postId+`' data-target="`+target+`">
                          <i class='fa fa-times' aria-hidden='true'></i>
                      </button>
                      <button class="edit-comment-btn"
                      data-comment-id='`+newCommentId+`'
                      data-post-id='`+postId+`'
                      >
                         <i class="fa fa-pencil" aria-hidden="true"></i>
                      </button>

                    </div>
                </div>`
          $('#comments-section-'+postId).prepend(commentStructure);
          $("#post-add-comment-"+postId).removeAttr('style');
            }
          });
    }
    e.preventDefault();
  }
  }

 });
 $('.comments-link').click(function(e){
    e.preventDefault();
    var postId = $(this).attr('data-post-id');
    $('#comments-section-'+postId).removeClass('hidden').addClass('visible');
 });

 $('.delete-post-btn').click(function(e){
 
 var postId = $(this).attr('data-post-id');
 var url = $(this).attr('data-target');
 $.get(url, function (data) {
   $('.modal').modal('hide');
   $('#post-id-'+postId).addClass('post-deleted');
  });

 });
 $('.delete-post-btn-lg').click(function(e){
   var postId = $(this).attr('data-post')
   $('#modal-delete-post-'+postId).modal();
 })

 $( '.add-comment-form' ).submit(function( event ) {
  event.preventDefault();
});

$('body').on('click', '.delete-comment-btn', function () {
  var commentId = $(this).attr('data-comment-id');
  var postId = $(this).attr('data-post-id');
  var url = $(this).attr('data-target');
  var commentNumber = parseInt($('#post-'+postId+'-comments-number').text()) - 1;
   if(commentNumber > 0) {
    $('#post-'+postId+'-comments-number').text(commentNumber);
  } else {
    $('#comments-button-'+postId).empty();
  }
  $('#comment-id-'+commentId).remove();
  $.get(url, function (data) {
 });
})


/**
 * filter News
 */

 $('#news-filter').change(function() {
 
  var selectedItem = $('#news-filter').val();
  $(".post").each(function(){

    if(selectedItem == 'LastPublished'){
      if ($(this).attr('data-post-pusblished') > 1)
      {
        $(this).addClass('hide-item');
      } else {
        $(this).removeClass('hide-item');
      }
    } else if (selectedItem == 'All'){
      $(this).removeClass('hide-item');

    }
    else{
    if ($(this).attr('data-category') != selectedItem)
    {
      $(this).addClass('hide-item');
    }
    else{
      $(this).removeClass('hide-item');
    }
    }
});
});

$('.btn-show-more').click(function(){
   $('.post-hidden').removeClass('post-hidden');
});
$('.dashboard-notification').click(function(){
  var notificationNumber = $(this).attr('data-notif');
  if(notificationNumber > 0) {
    $('.bell-badge').hide();
    $.get('/updateNotifications', function (data) {
    });
  }
})
$('body').on('click', '.post-likes', function () {

  
  var url = $(this).attr('data-path');
  $.get(url, function (data) {
   
    $('.modal-likes-list').html(data);
  });
})

/** edit post */
$('body').on('click', '.edit-comment-btn', function () {
  $('.edit-comment-btn').prop('disabled', true);
  var commentId = $(this).attr('data-comment-id');
  var isAlreadyUpdated = $('.updated-comment-text').text();
  if(isAlreadyUpdated)
  {
    $('.updated-comment-text').text('');
  }
 var oldDescription = $('#comment-description-'+commentId).text();
 $('#comment-description-'+commentId).text('');
 
 $('#comment-description-'+commentId).append(`<textarea id ='post-edit-comment-`+commentId+`' class='post-edit-comment'>`+$.trim(oldDescription)+`</textarea>
                                              <div id ='update-comment-btns-`+commentId+`' class="update-comment-btns">
                                                <button id ='comment-cancel-`+commentId+`'
                                                        class="comment-cancel-update custom-btn" 
                                                        data-comment-id='`+commentId+`'
                                                        data-old-description=`+oldDescription+`
                                                > Annuler
                                                </button>
                                                <button id ='comment-confirm-`+commentId+`' 
                                                        class="comment-confirm-update custom-btn" 
                                                        data-comment-id='`+commentId+`'>
                                                        Valider
                                                </button>
                                                </div>`);
});

$('body').on('click', '.comment-confirm-update', function () {
  var commentId = $(this).attr('data-comment-id');
  var updatedText = $('#post-edit-comment-'+commentId).val();
  $('#post-edit-comment-'+commentId).hide();
  $('#update-comment-btns-'+commentId).hide();
  
  $('#comment-description-'+commentId).text(updatedText)
  /** if we need to add the word updated to the comment */
  //.append("  <span class='updated-comment-text'>(modifié)</span>");
  url = 'comment/'+commentId+'/update-comment/'+updatedText;
  $.get(url, function (data) {
    $('.edit-comment-btn').prop('disabled', false);
   });
  /** update comment */
});

$('body').on('click', '.comment-cancel-update', function () {
  var oldDescription = $(this).attr('data-old-description');
  var commentId = $(this).attr('data-comment-id');
  $('#post-edit-comment-'+commentId).hide();
  $('#update-comment-btns-'+commentId).hide();
  $('#comment-description-'+commentId).text(oldDescription);
  $('.edit-comment-btn').prop('disabled', false);
});

/** end edit post */

/**report  abuse */
$('.report-abuse-btn').click(function(e){
  var postId = $(this).attr('data-post');
  
 $('#modal-report-abuse-post-'+postId).modal();
  var url = $(this).attr('data-target') ;
  
  $.get(url, function (data) {
    //$('#abuse-post-btn-'+postId).toggle();
    $('#modal-report-abuse-post-content-'+postId).html(data);
    $('#modal-report-abuse-post-'+postId).modal();
   });
})
$('.report-post-btn').click(function(){
  var postId = $(this).attr('data-post');
})

$('body').on('click', '.report-abuse-submit-btn', function (e) {

  e.preventDefault();
 
 var postId = $(this).attr('data-post');
 var commentId = $(this).attr('data-comment');
 var url = $(this).attr('data-target');
 var description ='';
 if(postId > 0) {
   description =$('.abuse-description-'+postId).val();
 }
 else {
  description =$('.abuse-description-'+commentId).val();
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
                    $('#modal-report-abuse-post-' + postId).modal('hide');
                    $('.message').removeClass('success-message').empty();
                    $('.abuse-description-' + postId).val('');
                    $('#abuse-post-btn-'+ postId).hide();

                }, 1500);
                //
            } else {
                setTimeout(function () {
                    $('#modal-report-abuse-comment-' + commentId).modal('hide');
                    $('.message').removeClass('success-message').empty();
                    $('.abuse-description-' + commentId).val('');
                    $('#report-comment-abuse-btn-' + commentId).hide();
                    

                }, 1500);
            }

        }
    });
}
})

$('.report-comment-abuse-btn').click(function(e){
  var commentId = $(this).attr('data-comment');
  
 $('#modal-report-abuse-comment-'+commentId).modal();
  var url = $(this).attr('data-target') ;
  
  $.get(url, function (data) {
   
    $('#modal-report-abuse-comment-content-'+commentId).html(data);
    $('#modal-report-abuse-comment-'+commentId).modal();
   });
})

$('.abuse-approve').click(function () {
  var abuseId = $(this).data("abuse-id");
  var untreatedAbuseNb = 0;
  $('#spinner-approve-'+abuseId).removeClass('spinner-hidden');
  $('#spinner-approve-'+abuseId).addClass('spinner-visible');
    
  url = 'edit/abuse/1/'+abuseId;
    $.get(url, function (data) {
     
      untreatedAbuseNb = $('.abuse-approve').length;
      $('#abuse-'+abuseId).addClass('approved-box');
      $('#abuse-'+abuseId).slideToggle( "slow");
      if(untreatedAbuseNb == 1) {
        $('.abuse-section').append( "<div class='box'>Vous avez traité tous les abus</div>" );
      }
    });
})

$('.abuse-reject').click(function () {
  var abuseId = $(this).data("abuse-id");
  var untreatedAbuseNb = 0;
  $('#spinner-reject-'+abuseId).removeClass('spinner-hidden');
  $('#spinner-reject-'+abuseId).addClass('spinner-visible');
    
  url = 'edit/abuse/0/'+abuseId;
    $.get(url, function (data) {
      untreatedAbuseNb = $('.abuse-reject').length;
      $('#abuse-'+abuseId).addClass('rejeccted-box');
      $('#abuse-'+abuseId).slideToggle( "slow");
      if(untreatedAbuseNb == 1) {
        $('.abuse-section').append( "<div class='box'>Vous avez traité tous les abus</div>" );
      }
    });
})


/** end report abuse */
/**
$( window ).resize(function() {
  if ($(window).width() < 1024)
  {
    $('.post').children('.user-profil-photo').removeClass('col-1').addClass('col-2');
    $('.post').children('.col-10').removeClass('col-10').addClass('col-9');

    $('.page').children('.col-lg-3').removeClass('col-lg-3').removeClass('col-md-6').addClass('col-12 asid-section');
    $('.page').children('.col-lg-9').removeClass('col-lg-9').removeClass('col-md-6').addClass('col-12 content-section');
  
    $('.user-comment').children('.user-image').removeClass('col-1').addClass('col-2');
    $('.user-comment').children('.comment').removeClass('col-9').addClass('col-7');
  }
  else {
    $('.post').children('.col-2').removeClass('col-2').addClass('col-1');
    $('.post').children('.col-8').removeClass('col-8').addClass('col-9');

    $('.page').children('.asid-section').removeClass('col-12').addClass('col-lg-3').addClass('col-md-6');
    $('.page').children('.content-section').removeClass('col-12').addClass('col-md-9').addClass('col-md-6');
  }
});
if ($(window).width() < 1024)
{
  $('.post').children('.user-profil-photo').removeClass('col-1').addClass('col-2');
  $('.post').children('.col-10').removeClass('col-10').addClass('col-9');

  $('.page').children('.col-lg-3').removeClass('col-lg-3').removeClass('col-md-6').addClass('col-12');
  $('.page').children('.col-lg-9').removeClass('col-lg-9').removeClass('col-md-6').addClass('col-12');

  $('.user-comment').children('.user-image').removeClass('col-1').addClass('col-2');
  $('.user-comment').children('.comment').removeClass('col-9').addClass('col-8');
}*/

    $('.add-favoris').click(function() {
        var url = '';
        var userId = $(this).attr('data-user-id');

        if ($(this).hasClass("text-warning")) {
            url = $(this).attr('data-delete');
            $('#result-user-item-' + userId).removeClass('text-warning').addClass('text-muted');
            if( $('.profil-add-favoris').length ) {
                $('.profil-add-favoris').text('').append("<i class='fa fa-heart'></i> Ajouter aux Favoris");
            }

        } else {
            url = $(this).attr('data-target');
            $('#result-user-item-' + userId).removeClass('text-muted').addClass('text-warning');
            if( $('.profil-add-favoris').length ) {
                $('.profil-add-favoris').text('').append("<i class='fa fa-heart'></i> Favoris");
            }

        }
       $.get(url, function (data) {
        });
    });
    $('.add-company-favoris').click(function() {
        var url = '';
        var companyId = $(this).attr('data-company-id');
        if ($(this).hasClass("text-warning")) {
            url = $(this).attr('data-delete');
            $('#result-company-item-' + companyId).removeClass('text-warning').addClass('text-muted');

        } else {
            url = $(this).attr('data-target');
            $('#result-company-item-' + companyId).removeClass('text-muted').addClass('text-warning');

        }
        $.get(url, function (data) {
        });
    });

$('#search_type').change(function(){
    if($( this ).val() == 'company')
    {
        $('#search_category').attr("disabled", false);
    }
    else {
        $('#search_category').attr("disabled", true);
        $('#search_category').val('');
    }
})
    $('.input-textarea').on({
        input: function(){

            var text = $(this).val();
            span.text(text);
            $(this).height(text ? span.height() : '1.1em');
        },
        focus: function(){
            initSpan($(this));
        }
    });

    $('body').on('click', '#opportunity-notification', function (e) {

    var url ='';
    var opportunityNumber = 0;
    url = $(this).attr('data-target');
    $.get(url, function (data) {
        $('#opportunity-notification').attr('id', 'no-opportunity-notification');
        $('.opportunity-badge').hide();
    });
});

$('#display-opportunity-offer').click(function(){
    $('.post').each(function(){
        if ($(this).attr('data-category') != 'Opportunities')
        {
            $(this).addClass('hide-item');
        }
        else {
                $(this).removeClass('hide-item');
        }
    });
    });
    $('.post-action-icon').click(function(){
        var postId = $(this).attr('data-post');

        if ( $( '#post-actions-'+postId ).length >= 1) {
            $('#post-actions-' + postId).toggle();
        }
    });
    $('.accpet-condition').click(function () {

        if ($(this).is(':checked')) {

            $('#inscription-btn').removeAttr('disabled');

        } else {
            $('#inscription-btn').attr('disabled', true);
        }
    });
$('.company-category').change(function(){
    var selectedItem = $('#company_category').val();
   if(selectedItem ==7)
   {
       $('.other-category').removeClass('none');
   } else {
       $('.other-category').addClass('none');
   }

})
/*fix responsive issues*/

   $('#registration_company_siret').width($('#registration_name').width());
    var footerHeight = $('.footer-copyright').height();
    var mt5Margin = 0;
    var headerHeight = $('#header').height();

    $('.inbox-message').height($(document).height() - headerHeight - 120);
   // $('.inbox-message').height(300);

    if ((".mt-5").length > 0) {
        if($(".mt-5").css("marginTop")) {
            mt5Margin = parseInt($(".mt-5").css("marginTop").replace('px', ''));
        }
    }
    if($('.content-wrapper-404').length > 0) {
        $('.content-wrapper-404').height($(document).height() - headerHeight - footerHeight - mt5Margin - 76);
    }


/*fix responsive issues*/

})(jQuery);

