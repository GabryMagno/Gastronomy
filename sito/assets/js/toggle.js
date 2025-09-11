document.addEventListener("DOMContentLoaded", function(){
    //Gestione Menu
    aggiustaMenu();
})

// GESTIONE MENU AD HAMBURGER
function aggiustaMenu(){
    let btn_menu = document.getElementById("menu-button");

    if (btn_menu){
        let menu = document.getElementById("menu");
        let links = menu.querySelectorAll("a");

        function apriChiudiMenu(){
            const isOpen = btn_menu.getAttribute("data-hidden") === "false";

            if (window.innerWidth > 767) {
                // Desktop: menu sempre visibile
                menu.setAttribute("aria-hidden", "false");
                menu.setAttribute("tabindex", "0");
                links.forEach(link => {
                    link.setAttribute("tabindex", "0");
                    link.setAttribute("aria-hidden", "false");
                });
                btn_menu.setAttribute("aria-label", "Menu di navigazione");
                btn_menu.setAttribute("aria-expanded", "false");
                btn_menu.setAttribute("aria-hidden", "true");
                btn_menu.setAttribute("tabindex", "-1");
                document.removeEventListener("keydown", escHandler);
            } else {
                // Mobile: logica aperto/chiuso
                menu.setAttribute("aria-hidden", (!isOpen).toString());
                menu.setAttribute("tabindex", isOpen ? "0" : "-1");
                links.forEach(link => {
                    link.setAttribute("tabindex", isOpen ? "0" : "-1");
                    link.setAttribute("aria-hidden", (!isOpen).toString());
                });
                btn_menu.setAttribute("aria-label", isOpen
                    ? "Clicca per comprimere il menu di navigazione"
                    : "Clicca per espandere il menu di navigazione");
                btn_menu.setAttribute("aria-expanded", isOpen.toString());

                if (isOpen) document.addEventListener("keydown", escHandler);
                else document.removeEventListener("keydown", escHandler);
            }
        }

        // HANDLER PER ESC
        function escHandler(event){
            if(event.key === "Escape" || event.key === "Esc"){
                btn_menu.setAttribute("data-hidden", "true");
                apriChiudiMenu();

                btn_menu.focus();
            }
        }

        btn_menu.addEventListener('click', function(){
            if (btn_menu.getAttribute("data-hidden") === "false"){
                btn_menu.setAttribute("data-hidden", "true");
            } else {
                btn_menu.setAttribute("data-hidden", "false");
            }

            apriChiudiMenu();
        });

        // ESECUZIONE AL CARICAMENTO E AL RESIZE DELLA PAGINA
        if (window.innerWidth > 767) {
            btn_menu.setAttribute("data-hidden", "true");
            btn_menu.setAttribute("tabindex", "-1");
        }
        else {
            btn_menu.setAttribute("data-hidden", "true");
            btn_menu.setAttribute("tabindex", "0");
        }
        apriChiudiMenu();

        window.addEventListener('resize', function(){
            if (window.innerWidth > 767) {
                btn_menu.setAttribute("data-hidden", "true");
                btn_menu.setAttribute("tabindex", "-1");
            }
            else {
                btn_menu.setAttribute("data-hidden", "true");
                btn_menu.setAttribute("tabindex", "0");
            }
            apriChiudiMenu();
        });
    }
}