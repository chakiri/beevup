// import "bootstrap-datepicker";
import "bootstrap-datepicker/js/locales/bootstrap-datepicker.fr"
$(document).ready(function (){
    //Disable datepicker for date already choose
    $('.datepicker').each(function(){
        if ($(this).is('[readonly="readonly"]')){
            $(this).removeClass('js-datepicker');
        }
    });
    /*$('.js-datepicker').datepicker({
        locale : 'fr',
        language : 'fr',
        format: 'dd/mm/yyyy',
        startDate: '0d'
    });*/

});