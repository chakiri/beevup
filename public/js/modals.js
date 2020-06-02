$( window ).on( "load", function() {
    var isOnboarding = $('#onboarding').data('onboarding');
    if (isOnboarding != true){
        $('#welcomeModal1').modal('show');
    }
});

$('.next').click(function(){
    nextModal($(this))
});

$('.prev').click(function(){
    prevModal($(this))
});

$('input[name=onboarding]').click(function () {
    var value = $(this).is(':checked');
    setOnborading(value);
    //Change all checkboxes value
    $('input[name=onboarding]').each(function(){
        $(this).prop('checked', value);
    });
});

$('#startIntro').click(function () {
    hideModal($(this));
    startIntro();
});

function nextModal(e){
    var modalNum = e.closest("#welcomeModals").data('num');
    $('#welcomeModal'+modalNum).modal('hide');
    var nextModal = modalNum + 1;
    $('#welcomeModal'+nextModal).modal('show');
}

function prevModal(e){
    var modalNum = e.closest("#welcomeModals").data('num');
    $('#welcomeModal'+modalNum).modal('hide');
    var prevModal = modalNum - 1;
    $('#welcomeModal'+prevModal).modal('show');
}

function hideModal(e){
    var modalNum = e.closest("#welcomeModals").data('num');
    $('#welcomeModal'+modalNum).modal('hide');
}

function setOnborading(value){
    var url = document.getElementById("onboarding").dataset.url;
    $.ajax({
        type: 'post',
        url: url,
        data: {
            value: value
        }
    });
}

//Intro Js
function startIntro(){
    var intro = introJs();
    /*intro.onbeforechange(function () {
     //Get number before last step (4)
     if (this._currentStep === 4) {
     setOnborading();
     }
     });*/
    intro.setOptions({ 'skipLabel': 'Passer', 'doneLabel': 'Terminer', 'prevLabel': 'Précédent', 'nextLabel': 'Suivant' });
    intro.start();
}