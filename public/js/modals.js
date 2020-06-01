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