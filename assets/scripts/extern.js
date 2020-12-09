// ============= beContacted =========
import $ from "jquery";

/**
 *Load beContacted form in modal
 */
$('.be-contacted-btn').click(function(){
    $('#beContacted').modal();
    let url = $(this).attr('data-target');
    $.get(url, function (data) {
        $('#beContacted .modal-content').html(data);
    });
});
//display errors forms beContacted in modal
$('#beContactedForm').submit(function( event ) {
    console.log('hhhd');
    event.preventDefault();

    let formSerialize = $(this).serialize();
    let url = $(this).attr('action');
    let redirectUrl = $(this).data('redirect');

    $.ajax({
        type: "POST",
        url: url,
        data: formSerialize,
        success: function(data) {
            window.location.href = redirectUrl;
        },
        error: function(xhr) {
            for (var key in xhr.responseJSON.data) {
                $('#beContactedForm input[name="be_contacted[' + key + ']"]').after('<ul class="errors"><li>' + xhr.responseJSON.data[key] + '</li></ul>');
            }
        }
    });
});