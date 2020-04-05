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
  /* Recommandation Approve and reject buttons*/
   var url = '';
   var untreatedRecommandationNb = 0;
   $('.recommandation-approve').click(function () {
   var recommandationId = $(this).data("recommandation-id");
   $('#spinner-approve-'+recommandationId).removeClass('spinner-hidden');
  $('#spinner-approve-'+recommandationId).addClass('spinner-visible');
    
    url = 'edit/recommandation/1/'+recommandationId;
        $.get(url, function (data) {
           untreatedRecommandationNb = $('.recommandation-approve').length;
           $('#recommandation-'+recommandationId).addClass('approved-box');
          $('#recommandation-'+recommandationId).slideToggle( "slow");
          if(untreatedRecommandationNb == 1)
          {
            $('.recommandation-section').append( "<div class='box'>Vous avez traité tous les recommandation</div>" );
          }
        });
    });

      
    $('.recommandation-reject').click(function () {
      var recommandationId = $(this).data("recommandation-id");
      $('#spinner-reject-'+recommandationId).removeClass('spinner-hidden');
      $('#spinner-reject-'+recommandationId).addClass('spinner-visible');
        
      url = 'edit/recommandation/0/'+recommandationId;
        $.get(url, function (data) {
          untreatedRecommandationNb = $('.recommandation-reject').length;
          $('#recommandation-'+recommandationId).addClass('rejeccted-box');
          $('#recommandation-'+recommandationId).slideToggle( "slow");
          if(untreatedRecommandationNb == 1) {
            $('.recommandation-section').append( "<div class='box'>Vous avez traité tous les recommandation</div>" );
          }
        });
    })
  /* end Recommandation Approve and reject buttons*/ 
  
  /*Add new recommandation message */ 
  $('.add-recommandation').click(function () {
    
    var companyId = 0 ;
    var serviceId = 0;
    var company = "" ;
    $('.modal').modal();
    url = $(this).attr('data-target');
    companyId = $(this).attr('data-company');
    serviceId = $(this).attr('data-service');
    company = $(this).attr('data-company');
    $.get(url, function (data) {
      $('.modal-content').html(data);
      $('.form-company').val(companyId);
      $('.form-service').val(serviceId);
      $('#modal1').modal('open');
    });
  })
 /*end new recommandation message */ 

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
      <i id="post-likes-icon-`+postId+`" class="fa fa-thumbs-o-up text-primary" aria-hidden="true"></i> 
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
    
  })


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

 $('textarea').on({
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
              var commentStructure = `
                  <div id='comment-id-`+newCommentId+`' class='row user-comment' style='background-color:#f3f6f8;border-radius:10px;padding:10px'>
                    <div class="user-image col-1" style="flot:left">
                      <a href='#'>
                          <img class='media-object photo-profile' src='/images/profil/photo/`+commentUserImg+`'  width='32' height='32' alt=''>
                        </a>
                    </div>
                    <div class='comment col-10'  style='flot:left'>
                        <a href='#' class='comment-user'><p>`+userName+`</p></a> 
                        <a href='#' class='comment-time'>à l\'instant</a>
                        <div class='comment-text'>`
                        +comment+
                        `</div>
                    </div>
                    <div class='delete-comment col-1'>
                      <button class='delete-comment-btn' data-comment-id='`+newCommentId+`' data-post-id='`+postId+`' data-target="`+target+`">
                          <i class='fa fa-times' aria-hidden='true'></i>
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

})(jQuery);

