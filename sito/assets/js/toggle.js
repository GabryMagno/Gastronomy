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

            menu.setAttribute("aria-hidden", (!isOpen).toString());
            btn_menu.setAttribute("aria-expanded", isOpen.toString());

            if (isOpen) {
                menu.setAttribute("tabindex", "0");

                links.forEach(link => {
                    link.setAttribute("tabindex", (link.id === "here" ? "-1" : "0"));
                    link.setAttribute("aria-hidden", (link.id === "here" ? "true" : "false"));
                });

                btn_menu.setAttribute("aria-label", "Clicca per comprimere il menu di navigazione");

                document.addEventListener("keydown", escHandler);
            } else {
                menu.setAttribute("tabindex", "-1");

                if (window.innerWidth > 767) {
                    links.forEach(link => {
                        link.setAttribute("tabindex", (link.id === "here" ? "-1" : "0"));
                        link.setAttribute("aria-hidden", (link.id === "here" ? "true" : "false"));
                    });
                } else {
                    links.forEach(link => {
                        link.setAttribute("tabindex", "-1");
                        link.setAttribute("aria-hidden", "true");
                    });

                    btn_menu.setAttribute("aria-label", "Clicca per espandere il menu di navigazione");

                    document.removeEventListener("keydown", escHandler);
                }
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