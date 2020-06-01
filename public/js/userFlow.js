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

function setOnborading(){

    var url = document.getElementById("onboarding").dataset.url;
    $.ajax({
        url: url
    });
}