// import "bootstrap-datepicker";
import "bootstrap-datepicker/js/locales/bootstrap-datepicker.fr"
$(document).ready(function (){
    $('.js-datepicker').datepicker({
        locale : 'fr',
        language : 'fr',
        format: 'dd/mm/yyyy',
        startDate: '0d'
    });
});