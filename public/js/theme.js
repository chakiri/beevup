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
  $('.modal').modal();
  url = $(this).attr('data-target');
  $.get(url, function (data) {
    $('.modal-content').html(data);
    $('#modal1').modal('open');
  });
 })

})(jQuery);

