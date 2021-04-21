
//  ============= cropper image =========

"use strict";

import Cropper from "cropperjs";

function createPreviousImageBloc(id){
    let idValue =  (id.slice(id.length - 1) =='e') ? '' : id.slice(id.length - 1);
    return document.getElementById('previous-image'+idValue) ;
}
function getFieldId(serviceClassName){
    if(serviceClassName.slice(-1) ==    1)   return 1;
    if(serviceClassName.slice(-1) ==    2)   return 2;
    if(serviceClassName.slice(-1) ==    3)   return 3;
    if(serviceClassName.slice(-1) ==  'e')   return '';
}

$(document).on('click', '.delete-img-service', function(e) {
    $(this).addClass('d-none');
    let fieldId = $(this).attr('data-input-id');
    let serviceId = $(this).attr('data-service-id');

    if (fieldId == 'service_imageFile1') {
        $('#previous-image1').empty();
        $('#service_imageFile1').val('');
        serviceCropper1 = '';
    }
    if (fieldId == 'service_imageFile2') {
        $('#previous-image2').empty();
        $('#service_imageFile2').val('');
        serviceCropper2 = '';
    }
    if (fieldId == 'service_imageFile3') {
        $('#previous-image3').empty();
        $('#service_imageFile3').val('');
        serviceCropper3 = '';
    }
    if(serviceId){
        const deletFileUrl = Routing.generate('delete-file', {'id': serviceId, 'fileId': fieldId});
        $.ajax({
            url: deletFileUrl,
            type: 'POST',
            async: false,
            processData: false,
            contentType: false,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function (data) { },
            error: function () {
                alert("Un problème est survenu. Veuillez réessayer")
            }
        })
    }

});

var fileInput = document.getElementsByClassName('form-imageFile')[0];
//var cropper;
var previousImage = document.createElement("img");
previousImage.classList.add('previous-img');
previousImage.classList.add('hide-bloc');
var previousImageBloc = document.getElementById('previous-image');

if(previousImageBloc != null) {
    previousImageBloc.appendChild(previousImage);
}

var ServiceCropper ='';
var serviceCropper1 ='';
var serviceCropper2 ='';
var serviceCropper3 ='';

var reader1 = new FileReader();
var reader2 = new FileReader();
var reader3 = new FileReader();

window.previousImage = function(e)
{
    let serviceInputsId = ['service_imageFile1','service_imageFile2','service_imageFile3']
    let serviceFieldId ='';
    /* to check if the page is service or [profile, store, company] */
    if(e != undefined){
        serviceFieldId = e.target.id;
        $('#previous-image'+getFieldId(serviceFieldId)).empty();
        $('#previous-image'+getFieldId(serviceFieldId)+ ' + span').removeClass('d-none');
    } else {
        $('#previous-image').empty();
    }
    if(serviceInputsId.includes(serviceFieldId)) {
        var fileInput = document.getElementById(e.target.id);

    } else {
        var fileInput = document.getElementsByClassName('form-imageFile')[0];
    }

    var cropper;
    var cutbtn = document.createElement("span");

    if(serviceFieldId != undefined) {
        cutbtn.setAttribute('data-image', serviceFieldId);
    }
    if(e != undefined) {
        cutbtn.classList.add('cut-btn' + getFieldId(serviceFieldId));
        cutbtn.innerHTML = "Enregistrer la photo";
    }

    var previousImage = document.createElement("img");
    previousImage.classList.add('previous-img');
    previousImage.classList.add('hide-bloc');
    if(serviceInputsId.includes(serviceFieldId)){
        var previousImageBloc = createPreviousImageBloc(serviceFieldId);
    }else {
        var previousImageBloc = document.getElementById('previous-image');
    }
    if(previousImageBloc != null) {
        previousImageBloc.appendChild(previousImage);
        previousImageBloc.appendChild(cutbtn);
    }

    previousImage.classList.remove('hide-bloc');
    if(serviceInputsId.includes(serviceFieldId)) {
        var fileInput = document.getElementById(serviceFieldId);
    } else {
        var fileInput = document.getElementsByClassName('form-imageFile')[0];
    }
    var file = fileInput.files[0];
    let reader = new FileReader();
    if(reader != null){
        reader.addEventListener('load', function (event) {
            previousImage.src = reader.result;
        }, false)
    }

    if(file){
        if(file != null) {
            reader.readAsDataURL(file);
        }
    }

    if(previousImage != null) {
        previousImage.addEventListener('load', function () {
            if (cropper) {
                cropper.destroy();
                cropper = new Cropper(previousImage, {
                    aspectRatio: 1
                });
            } else {
                cropper = new Cropper(previousImage, {
                    aspectRatio: 1
                })
                if(serviceFieldId.slice(-1) == 1){
                    serviceCropper1 = cropper;
                    reader1 = reader;
                }
                if(serviceFieldId.slice(-1) ==2){
                    serviceCropper2 = cropper;
                    reader2 = reader;
                }
                if(serviceFieldId.slice(-1) ==3){
                    serviceCropper3 = cropper;
                    reader3 = reader;
                }
                if(serviceFieldId.slice(-1) =='e'){
                    ServiceCropper = cropper;
                }
            }
        });
    }

    let form = document.getElementById('BVform');

    if(form != null)
    {
        form.addEventListener('submit',handler);
    }

    function handler () {
        if(fileInput.files[0]) {
            event.preventDefault()
            /*$('.hide-load').addClass('load-ajax-form');*/
            if (cropper) {
                cropper.getCroppedCanvas({
                    maxHeight: 1000,
                    maxWidth: 1000,

                }).toBlob(function (blob) {
                    ajaxWithAxios(blob, form, cropper);
                })
            }
            else {
                /*$('.hide-load').removeClass('load-ajax-form');*/
                // $(form).find('[name*="imageFile"]').first().parent('div').before("Le fichier que vous venez de uploder n'est pas correct");
                $('.file-not-correct').text('Ce type de fichier n\'est pas autorisé.Merci d\'en essayer un autre(jpeg, png, jpg)');
            }
        }
    }

    $(document).on('click', '.cut-btn'+getFieldId(serviceFieldId), function(e) {
        previousImageBloc = createPreviousImageBloc($(this).attr('data-image'));
        $('.cropper-modal').addClass('imageCupped'+getFieldId(serviceFieldId));
        var previousCuppedImage = document.createElement("img");
        previousCuppedImage.classList.add('previous-cupped-img'+getFieldId($(this).attr('data-image')));
        $('#previous-image'+getFieldId($(this).attr('data-image'))).empty();
        previousImageBloc.appendChild(previousCuppedImage);
        var resetBtn = document.createElement("span");
        resetBtn.innerHTML = "Annuler";
        resetBtn.classList.add('reset-btn'+ getFieldId(serviceFieldId));
        resetBtn.setAttribute('data-image', serviceFieldId);
        previousImageBloc.appendChild(resetBtn);
        if(getFieldId($(this).attr('data-image')) == 1){
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', serviceCropper1.getCroppedCanvas().toDataURL());
        }
        else if(getFieldId($(this).attr('data-image')) == 2){
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', serviceCropper2.getCroppedCanvas().toDataURL());
        }
        else if(getFieldId($(this).attr('data-image')) == 3){
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', serviceCropper3.getCroppedCanvas().toDataURL());
        }
        else {
            $('.previous-cupped-img'+getFieldId($(this).attr('data-image'))).attr('src', ServiceCropper.getCroppedCanvas().toDataURL());
        }

    });
    $(document).on('click', '.reset-btn'+getFieldId(serviceFieldId), function(e) {
        previousImageBloc = createPreviousImageBloc($(this).attr('data-image'));
        $('.imageCupped'+getFieldId(serviceFieldId)).removeClass('imageCupped'+getFieldId(serviceFieldId));
        var previousImage = document.createElement("img");
        previousImage.classList.add('previous-img'+getFieldId(serviceFieldId));
        $('#previous-image'+getFieldId(serviceFieldId)).empty();
        if(getFieldId(serviceFieldId) == 1) {

            previousImage.src = reader1.result;
            previousImageBloc.appendChild(previousImage);
            serviceCropper1 = new Cropper(previousImage, {
                aspectRatio: 1
            });
        } else if(getFieldId(serviceFieldId) == 2){
            previousImage.src = reader2.result;
            previousImageBloc.appendChild(previousImage);
            serviceCropper2 = new Cropper(previousImage, {
                aspectRatio: 1
            });

        } else if(getFieldId(serviceFieldId) == 3){
            previousImage.src = reader3.result;
            previousImageBloc.appendChild(previousImage);
            serviceCropper3 = new Cropper(previousImage, {
                aspectRatio: 1
            });
        } else {
            previousImage.src = reader.result;
            previousImageBloc.appendChild(previousImage);
            ServiceCropper = new Cropper(previousImage, {
                aspectRatio: 1
            });
        }

        previousImageBloc.appendChild(cutbtn);
    });
}

//====================== Execute controller form with ajax =========//
let form = document.getElementById('BVformService');
if(form != null) {
    form.addEventListener('submit', function (event) {
        let blob0 ='';
        let blob1 ='';
        let blob2 ='';
        let blob3 ='';
        let imageDimension = {maxHeight: 1000, maxWidth: 1000 };

        if(fileInput.files[0]  || fileInput != null ) {
            //Disable execution controller
            event.preventDefault();

            $('.hide-load').addClass('load-ajax-form');

            if (serviceCropper1 != '') {
                serviceCropper1.getCroppedCanvas(imageDimension).toBlob(function (blob) {  blob1 = blob; });  }
            if (serviceCropper2 != '') {
                serviceCropper2.getCroppedCanvas(imageDimension).toBlob(function (blob) {   blob2 = blob;  });
            }
            if (serviceCropper3 != '') {
                serviceCropper3.getCroppedCanvas(imageDimension).toBlob(function (blob) { blob3 = blob;  });
            }
            if (ServiceCropper != '') {
                ServiceCropper.getCroppedCanvas(imageDimension).toBlob(function (blob) { blob0 = blob;});
            }
            //Execute callback ajax to submit form
            setTimeout(function(){  ajaxWithAxios(blob0, form, ServiceCropper, blob1, blob2, blob3); }, 500); }
    });
}

//***** fix service issue ******* //

//Get update image path url
function update_img_url(){
    return $('#submit-photo').attr('data-url');
}

//Callback ajax
function ajaxWithAxios(blob, form, cropper,blob1, blob2, blob3)
{
    let url = update_img_url();
    console.log(url);
    let data = new FormData(form);

    data.append('file', blob);
    data.append('file1', blob1);
    data.append('file2', blob2);
    data.append('file3', blob3);

    $.ajax({
        url: url,
        type: 'POST',
        async: false,
        data:data,
        processData: false,
        contentType: false,
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(data){
            //If ajax return response with error
            if(data.result == 0) {
                $('.hide-load').removeClass('load-ajax-form');
                for (var key in data.data) {
                    let error = "<p class='form-error'>"+data.data[key]+"</p>";
                    $(form).find('[name*="'+key+'"]').first().parent('div').next(".form-error").remove();
                    $(form).find('[name*="'+key+'"]').first().parent('div').after(error);
                }
                if($('.serviceError').length > 0){
                    $('.serviceError').text('');
                    $('.serviceError').append(error);
                }
            }
            //if json return with service response
            else {
                //Get canevas to display preview on right img
                let canevas = $('#submit-photo').attr('data-canevas');

                if(Number.isInteger(data.serviceId)){
                    //Redirect form after submit it with ajax
                    window.location = data.urlRedirection;

                    //It's mean that response its from other entities (profile, store, company, ...)
                } else {
                    //Append copper Image
                    $('#previous-image').empty();
                    $('#update-img-modal').modal('hide');
                    if(cropper != '') {
                        if (canevas){
                            $('.' + canevas + ' .main-img').attr('src', cropper.getCroppedCanvas().toDataURL());
                        }else{
                            $('.main-img').attr('src', cropper.getCroppedCanvas().toDataURL());
                        }
                    }
                }

            }
        },
        error: function(){
            alert("Un problème est survenu. Veuillez réessayer")
        }
    });
}

// ============= update image =========
$('.update-image').click(function(e){
    var url = $(this).attr('data-url');
    var canevas = $(this).attr('data-canevas');

    $.get(url, function (data) {
        $('.modal-content-update-profile-img').html(data);
        //Set the url in dataurl
        $('#submit-photo').attr('data-url', url);
        $('#submit-photo').attr('data-canevas', canevas);
    });
});