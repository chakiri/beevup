document.querySelector('#service_isQuote').onclick = function (){
    var x = document.getElementById('price');
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
};

document.querySelector('#service_isDiscovery').onclick = function (){
    var x = document.getElementById('discovery');
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
};