$( window ).on( "load", function() {
    var isOnboarding = $('#onboarding').data('onboarding');
    var sessionOnboarding = $('#onboarding').data('sessionOnboarding');
    if (isOnboarding != true && sessionOnboarding != true){
        $('#welcomeModal1').modal('show');
        //Put showed popup in session
        setSessionPopup();
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
    tutorialDashboard();
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

function setSessionPopup(){
    var url = $('#onboarding').data('sessionOnboardingUrl');
    $.ajax({
        type: 'post',
        url: url

    });
}

//Intro Js
function startIntro(steps){
    var intro = introJs();
    /*intro.onbeforechange(function () {
     //Get number before last step (4)
     if (this._currentStep === 4) {
     setOnborading();
     }
     });*/
    intro.setOptions({
        'skipLabel': 'Passer',
        'doneLabel': 'Terminer',
        'prevLabel': 'Précédent',
        'nextLabel': 'Suivant',
        steps: steps
    });
    intro.start();
}

function tutorialDashboard(){
    startIntro(stepsDashboard);
}

function tutorialServices(){
    startIntro(stepsServices);
}
function tutorialNewService(){
    startIntro(stepsNewService);
}

var stepsDashboard = [];

if ($('#advisorebox:hidden').length == 0){
    stepsDashboard.push({
        element: "#advisorebox",
        intro: "<small>Votre conseiller Bureau Vallée toujours à l’écoute pour vous accompagner dans le développement de votre business, trouver des solutions et vous aider à utiliser cette plateforme​.</small>",
        position: 'right'
    })
}if ($('#companybox:hidden').length == 0){
    stepsDashboard.push({
        element: "#companybox",
        intro: "<small>Prenez le temps de remplir votre fiche entreprise pour permettre à la communauté locale de découvrir votre activité, vos valeurs. Choisissez votre photo avec soins. La fiche entreprise est la carte de visite de votre entreprise pour la communauté.</small>",
        position: 'right'
    })
}if ($('#specialOfferbox:hidden').length == 0){
    stepsDashboard.push({
        element: "#specialOfferbox",
        intro: "<small>En créant une offre exclusive dédiée à la communauté, vous mettez en avant vos services et faites découvrir votre activité aux autres membres. Certains proposent des remises, d’autres des essais, laissez libre cours à votre imagination.​<br>N’hésitez plus, profitez des offres exclusives d’autres entreprises ou créez votre propre offre.​<br>En proposant des offres dédiées vous cumulez des points Beev’Up et gagnez en visibilité.​</small>",
        position: 'left'
    })
}if ($('#postpublish:hidden').length == 0){
    stepsDashboard.push({
        element: "#postpublish",
        intro: "<small>Permet de publier un post dans le fil d’actualité.​<br>Vous détectez une opportunité commerciale qui peut intéresser un membre de la communauté, choisissez « Opportunité commerciale » et apportez une solution à votre client.​<br>Vous proposez une offre d’emploi ou vous cherchez un emploi pour une connaissance, choisissez « Emploi » pour bénéficier de toute la force de la communauté locale.​<br>Dans ces deux cas, vous gagnez des point Beev’Up</small>",
        position: 'bottom',
        scrollTo: 'tooltip'
    })
}if ($('#postsbox:hidden').length == 0){
    stepsDashboard.push({
        element: "#postsbox",
        intro: "<small>Partage d’informations, échange d’opportunités commerciales, offres d’emploi, nouveaux arrivants, tout est fait pour être informé de la vie de la communauté locale.​<br>Proposez vos propres nouvelles et gagnez des points Beev’Up en proposant des opportunités commerciales et des offres d’emploi​</small>",
        position: 'top',
        scrollTo: 'tooltip'
    })
}if ($('#communitybox:hidden').length == 0 ){
    stepsDashboard.push({
        element: "#communitybox",
        intro: "<small>Retrouvez les membres de la communauté locale afin de prévoir un moment de convivialité, d’échange du business ou pour découvrir les entreprises locales</small>",
        position: 'bottom'
    })
}if ($('#servicesbox:hidden').length == 0){
    stepsDashboard.push({
        element: "#servicesbox",
        intro: "<small>Proposez vos services à la communauté ou trouvez une solution à vos besoins tout en privilégiant le savoir faire local</small>",
        position: 'bottom'
    })
}if ($('#chatbox:hidden').length == 0){
    stepsDashboard.push({
        element: "#chatbox",
        intro: "<small>​Discutez en direct avec les autres membres de la communauté en utilisant le Chat</small>",
        position: 'bottom'
    })
}if ($('#postsNotifications:hidden').length == 0 ){
    stepsDashboard.push({
        element: "#postsNotifications",
        intro: "<small>Vous êtes notifiés lorsque l’un de vos post est commenté ou apprécié par d’autres membres de la communauté</small>",
        position: 'top',
        scrollTo: 'tooltip'
    })
}if ($('#messagesNotifications:hidden').length == 0){
    stepsDashboard.push({
        element: "#messagesNotifications",
        intro: "<small>D’autres membres de la communauté essaient de vous contacter. Cela tombe bien le Chat est fait pour ça. Vous êtes notifiés dès qu’une nouvelle demande apparait​</small>",
        position: 'top',
    })
}if ($('#opportunities:hidden').length == 0 ){
    stepsDashboard.push({
        element: "#opportunities",
        intro: "<small>Vous êtes notifiés dès qu’une opportunité commerciale est postée. Soyez rapide comme l’éclair pour prendre contact avec le membre de la communauté locale et répondre à cette opportunité​</small>",
        position: 'top',
    })
}

var stepsServices = [];

if ($('#servicessearchbox:hidden').length == 0 ){
    stepsServices.push({
        element: "#servicessearchbox",
        intro: "<small>Le moteur de recherche est là pour vous aider à trouver le service qui correspond le mieux à votre besoin​</small>",
        position: 'right',
    })
}if ($('#specialofferbox:hidden').length == 0 ){
    stepsServices.push({
        element: "#specialofferbox",
        intro: "<small>Choisissez « Offres Exclusives » pour voir toutes les offres exclusives proposées par la communauté locale​</small>",
        position: 'right'
    })
}if ($('#createservicebox:hidden').length == 0 ){
    stepsServices.push({
        element: "#createservicebox",
        intro: "<small>Créez vos propres services et proposez les à la communauté​</small>",
        position: 'right'
    })
}if ($('#myservicesbox:hidden').length == 0 ){
    stepsServices.push({
        element: "#myservicesbox",
        intro: "<small>Affichez la liste de vos services pour les modifier ou les supprimer​</small>",
        position: 'right'
    })
}


var stepsNewService = [
    {
        element: "#service_title",
        intro: "<small>Le titre du service doit être choisi avec attention. 5 à 6 mots maximum définissant le service.​ <br>Par exemple : « Consultation de Naturopathie »</small>",
        position: 'right',
    },
    {
        element: "#service_introduction",
        intro: "<small>Certains appellent cela la Baseline ou le slogan de leur service, d’autres indiquent une description courte.​<br>Par exemple : « Maitriser son stress, Conseils alimentaires (Durée 1h30) »​</small>",
        position: 'right',
    },
    {
        element: "#service_description",
        intro: "<small>Décrivez en détail le contenu de votre offre de service. Plus la description sera pertinent, plus votre service sera trouvé facilement par les membres de la communauté​</small>",
        position: 'left',
    },
    {
        element: "#images",
        intro: "<small>Les photos présentent et mettent en valeur vos services. Un service sans photo est peu mis en valeur et peu regardé par les utilisateurs​</small>",
        position: 'right',
    },
    {
        element: "#isQuotebox",
        intro: "<small>Si votre service dispose d’un prix forfaitaire, renseignez le prix en Hors Taxes sinon choisissez « Sur devis »​</small>",
        position: 'right',
    },
    {
        element: "#isDiscovery",
        intro: "<small>Le meilleur moyen ….​</small>",
        position: 'right',
    },
];