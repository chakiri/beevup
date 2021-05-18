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
    });

    /**
     * Active/Disable charter sign btn
     */
    $('#expert_meeting_isInCompany').click(function () {
        if ($(this).is(':checked')) {
            $('.row-address-meeting').removeClass('d-none');
        } else {
            $('.row-address-meeting').addClass('d-none');
        }
    });
    if(!$('#expert_meeting_isInCompany').is(':checked')){
        $('.row-address-meeting').addClass('d-none');
    }

    /**
     * Select option select by checked checkbox
     */
    $('.form-check-input').click(function (){
        let timeId = $(this).attr('id');
        $('select[name="expert_booking[timeSlot]"]').find('option[value=' + timeId + ']').attr("selected",true);
        //Empty all others checked checkboxes
        $('.form-check-input').not(this).prop('checked', false);
    });

    /**
     * Check checkbox by option select
     */
    let timeSlotValue = $('select[name="expert_booking[timeSlot]"]').val();     //Get value of option select
    if (timeSlotValue){
        $('.form-check-input[id="' + timeSlotValue + '"]').prop( "checked", true )      //Check checkbox by option select
    }

    /**
     * Display company address if company selected
     */
    $('select[name="expert_booking[isVisio]"]').on('change', function (){
       if ($(this).val() == false){
            $('.company-address').removeClass('d-none');
       }else{
           $('.company-address').addClass('d-none');
       }
    });
    if ($('select[name="expert_booking[isVisio]"]').val() == true){
        $('.company-address').addClass('d-none');
    }
});