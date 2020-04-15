document.querySelector('#service_isPayant').onclick = function (){
    var x = document.getElementById('price');
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
};