/*
document.addEventListener("DOMContentLoaded", function () {
    const btn_menu = document.getElementById("menu-button");
    const menu = document.getElementById("menu");
    const links = menu.querySelectorAll("a");

    function chiudiMenu() {
        btn_menu.setAttribute("data-hidden", "false");
        menu.style.display = "none";
        links.forEach(link => {
            link.setAttribute("tabindex", "-1");
            link.setAttribute("aria-hidden", "true");
        });
    }

    function apriMenu() {
        btn_menu.setAttribute("data-hidden", "true");
        menu.style.display = "block";
        links.forEach(link => {
            link.setAttribute("tabindex", (link.id === "here" ? "-1" : "0"));
            link.setAttribute("aria-hidden", (link.id === "here" ? "true" : "false"));
        });
    }

    function aggiornaStatoMenu() {
        if (window.innerWidth > 767) {
            // Desktop: mostra sempre il menu
            menu.style.display = "block";
            btn_menu.style.display = "none"; // Nascondi bottone su desktop
            links.forEach(link => {
                link.setAttribute("tabindex", (link.id === "here" ? "-1" : "0"));
                link.setAttribute("aria-hidden", (link.id === "here" ? "true" : "false"));
            });
        } else {
            // Mobile: mostra bottone e chiudi menu inizialmente
            btn_menu.style.display = "inline-block";
            chiudiMenu();
        }
    }

    // Click bottone menu
    btn_menu.addEventListener("click", function () {
        const isHidden = btn_menu.getAttribute("data-hidden") === "false";
        if (isHidden) {
            apriMenu();
        } else {
            chiudiMenu();
        }
    });

    // Inizializzazione
    aggiornaStatoMenu();
    window.addEventListener("resize", aggiornaStatoMenu);
});
*/
