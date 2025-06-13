document.addEventListener("DOMContentLoaded", function(){
    aggiustaMenuResize();
    switchToggle();
});

// GESTIONE DEL MENU AD HAMBURGER
function aggiustaMenuResize(){

    const btn_menu = document.getElementById("menu-button");

    if(btn_menu){
        const menu = document.getElementById("menu");
        const links = menu.querySelectorAll("a");

        // Imposta stato iniziale
        if(!btn_menu.hasAttribute("data-hidden")){
            btn_menu.setAttribute("data-hidden", "true"); // = menu chiuso all’inizio
            menu.style.display = "none";
        }

        // Funzione toggle apertura/chiusura menu
        function menuApriChiudi(){
            if(btn_menu.getAttribute("data-hidden")==="false"){
                // Chiudi menu
                btn_menu.setAttribute("data-hidden", "true");
                menu.style.display = "none";

            } else {
                // Apri menu
                btn_menu.setAttribute("data-hidden", "false");
                menu.style.display = "block";
            }
        }

        // Evento click sul bottone
        btn_menu.addEventListener("click", menuApriChiudi);

    }
}