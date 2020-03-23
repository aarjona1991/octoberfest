let headerTag = document.querySelector('header'); // captura el target header
let startY = (headerTag.offsetHeight) * 2; // captura el doble del height del header para saber cuando se paso con el scroll
let vw = window.innerWidth; // captura el width del viewport
let vwAvailable = 768; // Estabece la resolución a partir de la cual se puede ejecutar es script

window.addEventListener('scroll', function () {
    if (vw > vwAvailable) {
        if (window.pageYOffset > startY) {
            headerTag.classList.remove("on-top");
        } else {
            headerTag.classList.add("on-top");
        }
    }
});