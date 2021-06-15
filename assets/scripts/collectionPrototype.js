
$(document).ready(function() {
    var $wrapper = $('.js-time-slots-wrapper');
    $wrapper.on('click', '.js-time-slot-remove', function(e) {
        e.preventDefault();
        $(this).closest('.js-time-slot-item')
            .fadeOut()
            .remove();
    });
    $wrapper.on('click', '.js-time-slot-add', function(e) {
        e.preventDefault();
        // Get the data-prototype explained earlier
        var prototype = $wrapper.data('prototype');
        // get the new index
        var index = $wrapper.data('index');
        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newForm = prototype.replace(/__name__/g, index);
        // increase the index with one for the next item
        $wrapper.data('index', index + 1);
        // Display the form in the page before the "new" link
        $(this).before(newForm);

        //Add Datepicker to all new js-datepicker
        $('.js-datepicker').datepicker({
            locale : 'fr',
            language : 'fr',
            format: 'dd/mm/yyyy',
            startDate: '0d'
        });
    });

    /**
     * Disable validation form if timeSlots empty
     */
    $('form[name="expert_meeting"]').find(':submit').click(function (e) {
        if ($(".js-time-slot-item").length === 0){
            e.preventDefault();
            alert('Vous devez choisir une date');
        }
        //Disable submit form if error in times
        let error = verifTime();
        if (error != ''){
            e.preventDefault();
            alert(error);
        }
    });

    /**
     * Display company address if company selected
     */
    $('#expert_meeting_way_1').click(function () {
        if ($(this).is(':checked')) {
            $('.row-address-meeting').removeClass('d-none');
        } else {
            $('.row-address-meeting').addClass('d-none');
        }
    });
    /*$('#expert_meeting_way_0').click(function () {
        if ($(this).is(':checked')) {
            $('.row-address-meeting').addClass('d-none');
        }
    });*/
    if(!$('#expert_meeting_way_1').is(':checked')){
        $('.row-address-meeting').addClass('d-none');
    }else{
        $('.row-address-meeting').removeClass('d-none');
    }
    /*$('select[name="expert_meeting[isVisio]"]').on('change', function (){
        if ($(this).val() == false){
            $('.row-address-meeting').removeClass('d-none');
        }else{
            $('.row-address-meeting').addClass('d-none');
        }
    });
    if ($('select[name="expert_meeting[isVisio]"]').val() == true){
        $('.row-address-meeting').addClass('d-none');
    }*/

    /**
     * Select option select by checked checkbox
     */
    $('.calendar .form-check-input').click(function (){
        //if checkbox checked
        if ($(this).is(':checked')){
            let timeId = $(this).attr('id');
            $('select[name="expert_booking[slot]"]').val(timeId);
        }else{
            $('select[name="expert_booking[slot]"]').val("");
        }
        //Empty all others checked checkboxes
        $('.calendar .form-check-input').not(this).prop('checked', false);
    });

    /**
     * Check checkbox by option select
     */
    let timeSlotValue = $('select[name="expert_booking[slot]"]').val();     //Get value of option select
    if (timeSlotValue){
        $('.form-check-input[id="' + timeSlotValue + '"]').prop( "checked", true )      //Check checkbox by option select
    }

    /**
     * Display company address if company selected
     */
    $('select[name="expert_booking[way]"]').on('change', function (){
       if ($(this).val() === 'company'){
            $('.company-address').removeClass('d-none');
       }else{
           $('.company-address').addClass('d-none');
       }
    });
    if ($('select[name="expert_booking[way]"]').val() === 'company'){
        $('.company-address').removeClass('d-none');
    }

    /**
     * Display confirmation modal for booking form
     */
    $('form[name="expert_booking"]').find(':submit').click(function (e){
        e.preventDefault();
        if (!$('#expert_booking_slot').val()) {
            alert('Vous devez choisir un créneau');
        }else {
            $('#confirmExpertBooking').modal();

            //Get value fields form
            let expertName = $('.expertName').text();
            let expertCompany = $('.expertCompany').text();
            let expertExpertise = $('.expertExpertise').text();
            let expertAddress = $('.expertAddress').text();
            let way = $('#expert_booking_way').val();
            let description = $('#expert_booking_description').val();

            let url = Routing.generate('expert_booking_confirm_submit', {'slot': $('#expert_booking_slot').val()});

            $.ajax({
                url: url
            }).then(function(data) {
                $('#confirmExpertBooking .modal-content').html(data);
                $('span.name').text(expertName);
                $('span.company').text(expertCompany);
                $('span.expertise').text(expertExpertise);
                $('span.description').text(description);

                if (way === 'visio'){
                    $('.isVisio').removeClass('d-none');
                }else{
                    $('.isCompany').removeClass('d-none');
                    $('span.address').text(expertAddress);
                }

                //Cancel button
                $('.cancel').click(function(){
                    $('#confirmExpertBooking').modal('hide');
                });

                $('.confirmSubmit').click(function(){
                    //Submit form
                    $('form[name="expert_booking"]').submit();
                });
            });
        }
    });

    //Confirm expert booking
    $('.expert-booking-confirm-btn').click(function(){
        let btn = $(this);

        let url = $(this).data('target');

        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                console.log('expert booking confirmed');
                btn.parents('.box').css('display', 'none');
            },
            error: function(){
                alert('Une erreur s\'est produite. Veuillez réessayer.');
            }
        });
    });

    //Cancel expert booking
    $('.expert-booking-cancel-btn').click(function(){
        let btn = $(this);

        let url = $(this).data('target');

        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                console.log('expert booking canceled');
                btn.parents('.box').css('display', 'none');
            },
            error: function(){
                alert('Une erreur s\'est produite. Veuillez réessayer.');
            }
        });
    });

    //Archive expert booking
    $('.expert-booking-archive-btn').click(function(){
        let btn = $(this);

        let url = $(this).data('target');

        $.ajax({
            type: 'POST',
            url: url,
            success: function(){
                console.log('expert booking archived');
                btn.parents('.box').css('display', 'none');
            },
            error: function(){
                alert('Une erreur s\'est produite. Veuillez réessayer.');
            }
        });
    });
});

//Verification times timeSlot
function verifTime(){
    let message = '';

    $('.js-time-slot-item').each(function(){
        let date = $(this).find('.datepicker').val();
        let startTimeHour = $(this).find('.js-startTime').find(':selected').text();
        let endTimeHour = $(this).find('.js-endTime').find(':selected').text();

        if (startTimeHour > endTimeHour){
            message =  'Ces créneaux ne sont pas valides';
        }

        //Get date with en format
        let arrayExplodedDate = date.split('/');
        let dateFormat = arrayExplodedDate[2] + '-' + arrayExplodedDate[1] + '-' + arrayExplodedDate[0];

        //Initiate dateTime
        let dateTime = new Date(dateFormat);
        let currentDate = new Date();
        let currentTime = currentDate.getHours() + '' + currentDate.getMinutes();

        //Get only date without time
        dateTime.setHours(0,0,0,0);
        currentDate.setHours(0,0,0,0);

        //compare endTime with actual time
        if (dateTime.valueOf() == currentDate.valueOf()){
            if (endTimeHour < currentTime){
                message = 'Ces créneaux sont impossibles à programmer car ils sont antérieurs à la date du jour';
            }
        }
    });

    return message;
}