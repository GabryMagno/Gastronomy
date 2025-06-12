document.addEventListener("DOMContentLoaded", function(){
    aggiustaMenuResize();
    switchToggle();
});

// GESTIONE DEL MENU AD HAMBURGER
function aggiustaMenuResize(){

    let btn_menu = document.getElementById("menu-button");

    if(btn_menu){
        let menu = document.getElementById("menu");
        let links = menu.querySelectorAll("a");

        //cambia se menu visibile o nascosto
        function menuApriChiudi(){
            if(btn_menu.getAttribute("data-hidden")==="false"){

            } else {

            }
        }

    }
}