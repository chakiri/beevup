import $ from 'jquery';

      "use strict";

      // Preloader (if the #preloader div exists)
      $(window).on('load', function () {
        if ($('#preloader').length) {
          $('#preloader').delay(100).fadeOut('slow', function () {
            $(this).remove();
          });
        }
      });


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

     /**
      * publish a new post
      */
     $('.add-post').click(function(){
      $('.modal-add-post').modal();
      var url = $(this).attr('data-target');
      $.get(url, function (data) {
        $('.modal-post-content').html(data);
        //$('#modal1').modal('open');
      });
     })

     /* end publish post */

      $('.like-button').click(function(){

          $('.like-button').attr("disabled", true);
        var postId = $(this).attr('data-post-id');
        var postLikesNumber = Number($('#post-likes-number-'+postId).text());
        var newLikeStructure = '';
        var action = '';
        if($(this).hasClass('post-liked')== false)
        {
          action = 'add';
          $(this).addClass('post-liked');
          var url = 'post/'+postId+'/update-post-likes/add';
          var dataPath = $('#post-'+postId+'-likes-number').attr('data-path');
          newLikeStructure = `<span id="likes-list-`+postId+`" 
          class="post-likes"  
          data-post="`+postId+`"  
          data-toggle="modal" 
          data-target="#LikesList"
          data-whatever="@mdo"
          data-path="`+dataPath+`">
          <i id="post-likes-icon-`+postId+`" class="fas fa-thumbs-up text-primary" aria-hidden="true"></i> 
                                <span id ="post-likes-number-`+postId+`" class="post-likes-number">1</span></span>`;
        }
        else{
          action = 'remove';
          $(this).removeClass('post-liked');
          var url = 'post/'+postId+'/update-post-likes/remove';
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
            $('.like-button').attr("disabled", false);
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
               $('#post-'+postId+'-comments-text').text('commentaires');
           } else {
            newCommentsNumber = 1;
            var numberOfCommentsStructure = `<span id="post-`+postId+`-comments-number">` +newCommentsNumber+ ` </span>
                                             <span id="post-`+postId+`-comments-text"> commentaire </span>`;
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
                             <i class="fas fa-pencil" aria-hidden="true"></i>
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
        if(commentNumber == 1){
            $('#post-'+postId+'-comments-text').text('commentaire');
        }
      } else {
        $('#comments-button-'+postId).empty();
      }
      $('#comment-id-'+commentId).remove();
      $.get(url, function (data) {
     });
    })

        //=========filter News =========//

     $('#news-filter').change(function() {

      var selectedItem = $('#news-filter').val();
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


    //====================== edit post =========//

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
      var url = 'comment/'+commentId+'/update-comment/'+updatedText;
      $.get(url, function (data) {
        $('.edit-comment-btn').prop('disabled', false);
       });
     });

    $('body').on('click', '.comment-cancel-update', function () {
      var oldDescription = $(this).attr('data-old-description');
      var commentId = $(this).attr('data-comment-id');
      $('#post-edit-comment-'+commentId).hide();
      $('#update-comment-btns-'+commentId).hide();
      $('#comment-description-'+commentId).text(oldDescription);
      $('.edit-comment-btn').prop('disabled', false);
    });

    //====================== report abus =========//

   $('.report-post-btn').click(function(){
      var postId = $(this).attr('data-post');
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

      var url = 'edit/abuse/1/'+abuseId;
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

      var url = 'edit/abuse/0/'+abuseId;
        $.get(url, function (data) {
          untreatedAbuseNb = $('.abuse-reject').length;
          $('#abuse-'+abuseId).addClass('rejeccted-box');
          $('#abuse-'+abuseId).slideToggle( "slow");
          if(untreatedAbuseNb == 1) {
            $('.abuse-section').append( "<div class='box'>Vous avez traité tous les abus</div>" );
          }
        });
    })

    if($('#search_type').length > 0)
    {
        if($('#search_type').val() =='company')
        {
            $('#search_category').attr("disabled", false);
        }
    }
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

    $('body').on('click', '#opportunity-notification', function (e) {

    var url ='';
    var opportunityNumber = 0;
    url = $(this).attr('data-target');
    $.get(url, function (data) {
        $('#opportunity-notification').attr('id', 'no-opportunity-notification');
        $('.opportunity-badge').hide();
    });
   });

    if(!$('.accpet-condition').is(':checked')){
        $('#inscription-btn').addClass('orange-btn-greyed');
    }

    $('#display-opportunity-offer').click(function(){
        $('#news-filter').val("Opportunité commerciale");
        $('.post').each(function(){
            if ($(this).attr('data-category') != 'Opportunité commerciale')
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
            $('#inscription-btn').removeClass('orange-btn-greyed');
        } else {
            $('#inscription-btn').attr('disabled', true);
            $('#inscription-btn').addClass('orange-btn-greyed');
        }
    });

   if ( $('#company_introduction').length > 0 )
   {
       if($('#company_introduction').val() ==' '){
           $( '#company_introduction').val(function( index, value ) {
               return value.trim();
           });
       }

   }

    if ($('#registration_acceptConditions').length > 0 || $('#reset_password_acceptConditions').length > 0 ){
        if($('#registration_acceptConditions').is(':checked')){
            $('#inscription-btn').attr('disabled', false);
        }

        $('.form-check-label').append(' <a target=\'_blank\' href=\'https://beevup.fr/media/pdf/CGU.pdf\' class=\'genaral-condition\' >les Conditions générales d\'utilisation</a>');
    }
    if($('#company_country').length  > 0) {

        if($('#company_country').val() == '') {
            $('#company_country').val("FR");
        }
    }
    $('.code-bv-btn').click(function(){
        $('.modal-get-bar-code').modal();
    });


    //====================== fix responsive issues =========//

   $('#registration_company_siret').width($('#registration_name').width());
    var footerHeight = $('.footer-copyright').height();
    var mt5Margin = 0;
    var headerHeight = $('#header').height();
    var availableScreenHeight = screen.availHeight;

    if($('.entity-description').length > 0 )
        {
            if($('.entity-description').text() != '') {
                var descriptionText = $('.entity-description').text().replace(/<br>/g, '\r\n');
                $('.entity-description').val(descriptionText);
            }
        }

    if($('#profile_introduction').length > 0 )
        {
            if($('#profile_introduction').text() != '') {
                var descriptionText = $('#profile_introduction').text().replace(/<br>/g, '\r\n');
                $('#profile_introduction').val(descriptionText);
            }
        }

    function updateCoordinate(callback) {

        navigator.geolocation.getCurrentPosition(
            function (position) {
                var AllStores = '';
                var returnValue = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                }
                $.ajax({
                    url: '/map',
                    type: 'POST',
                    async: false,
                    success: function(data){
                        AllStores = JSON.parse( data );
                    }
                });

                callback(returnValue, AllStores );
            }
        )}


    //============== load more posts =========//

    $('body').on('click', '.load-more-posts-section', function () {
        var minPostId = $(this).attr('data-first-post');
        var pathNameUrl  = window.location.pathname;
        var url = pathNameUrl+'/load_more/'+minPostId;
        $('.load-more-posts-section').remove();
        if(minPostId != 'undefined') {
            $.get(url, function (data) {
                $('#postsbox').append(data);
            });
        }
    })

    //============== open street map =========//

    function distance(lat1, lon1, lat2, lon2, unit) {
        var radlat1 = Math.PI * lat1/180
        var radlat2 = Math.PI * lat2/180
        var theta = lon1-lon2
        var radtheta = Math.PI * theta/180
        var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
        if (dist > 1) {
            dist = 1;
        }
        dist = Math.acos(dist)
        dist = dist * 180/Math.PI
        dist = dist * 60 * 1.1515
        if (unit=="K") { dist = dist * 1.609344 }
        if (unit=="N") { dist = dist * 0.8684 }
        return dist
    }
    if($('#mapid').length > 0 && window.innerWidth > 769) {
        var allStores = '';
        $.ajax({
            url: '/map',
            type: 'POST',
            async: false,
            success: function (data) {
                allStores = JSON.parse(data);
            }
        });

        var currentUserLongitude = "";
        var currentUserLatitude = "";
        var mymap = L.map('mapid').setView([51.505, -0.09], 13);
        mymap.locate({setView: true, watch: true}) /* This will return map so you can do chaining */
            .on('locationfound', function (e) {
                var greenIcon = new L.Icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                var marker = L.marker([e.latitude, e.longitude], {icon: greenIcon}).addTo(mymap).bindPopup("<b>Je suis là</b>").openPopup();
                currentUserLongitude = e.longitude;
                currentUserLatitude = e.latitude;

                L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png',{
                    attribution: ''}).addTo(mymap);
                for (var i = 0; i < allStores.stores.length; i++) {
                    if (distance(currentUserLatitude, currentUserLongitude, allStores.stores[i].lat, parseFloat(allStores.stores[i].lng), "K") <= 1000) {
                       var marker = L.marker([allStores.stores[i].lat, parseFloat(allStores.stores[i].lng)]).addTo(mymap).bindPopup("<b>" + allStores.stores[i].name + "</b><br/><span style='color:#FF7F50'>" + allStores.stores[i].adress + "</span>");
                     }
                }
             });
             var popup = L.popup();
        }
     $('.close-subscription-notification').click(function(){
     $('.warning-subscription').remove();

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
});




