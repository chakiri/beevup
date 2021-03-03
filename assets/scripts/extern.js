// ============= beContacted =========
import $ from "jquery";
import 'bootstrap';


//display errors forms beContacted in modal
$('#beContactedForm').submit(function( event ) {
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
                $('#beContactedForm input[name="be_contacted[' + key + ']"]').nextAll().remove();
                $('#beContactedForm input[name="be_contacted[' + key + ']"]').after('<ul class="errors"><li>' + xhr.responseJSON.data[key] + '</li></ul>');
            }
        }
    });
});


