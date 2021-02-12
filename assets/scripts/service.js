$(document).ready(function(){

    // Service create by theme modal
    $('#btnChooseModel').click(function (){
        //Open modal
        $('#chooseModel').modal();

        //Get url action
        let url = $(this).data('url');

        //Get data form controller
        $.ajax({
            type: 'GET',
            url: url,
            success: function (data){
                $('#chooseModel .modal-content').html(data);
            },
            error: function(xhr){
                alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
            }
        });
    });

    //Change data models by taping keys words
    $('#searchModel input').on('input', function() {
        let query = $(this).val();

        //Get url action
        let url = $('#btnChooseModel').data('url') + '?query=' + query;

        //Get data form controller
        $.ajax({
            type: 'GET',
            url: url,
            success: function (data){
                $('#chooseModel .modal-data').html(data);
            },
            error: function(xhr){
                alert(xhr.status + ' Une erreur est survenue. Réssayez plus tard !');
            }
        });
    });

});
