function FilterForm(){
    const priceInput = document.getElementById("price");
    const textPrice = document.getElementById("text-price");

    priceInput.addEventListener("input", () => {
        priceInput.setAttribute("value", priceInput.value);
        textPrice.innerText = priceInput.value + " €";
    });

    let reset_button = document.getElementById("reset-filters");
    reset_button.addEventListener("click", (e) =>{
        e.preventDefault();

        const checkbox = document.querySelectorAll('input[type="checkbox"]');
        checkbox.forEach(cb => cb.checked = false);

        document.getElementsByName("rating").forEach(star => {
            star.checked = false;
        });

        const priceInput = document.getElementById("price");
        priceInput.value = 100;
        document.getElementById("text-price").innerText = priceInput.value + " €";

    });
}

function updateAria() {
    const details = document.getElementById("filter-toggle");
    const summary = document.getElementById("toggle-summary");
    const text = document.getElementById("summary-text");
    if (window.innerWidth < 768){
        summary.setAttribute("aria-expanded", details.open ? "true" : "false");
        text.innerText = details.open ? "Chiudi i filtri" : "Apri i filtri";
    }
}

function toggleFiltersOnResize() {
    const filterToggle = document.getElementById("filter-toggle");
    if (window.innerWidth >= 768) filterToggle.setAttribute("open", "");
    else filterToggle.removeAttribute("open");
}


window.addEventListener("load", () => {
   
    const details = document.getElementById("filter-toggle");
    details.addEventListener("toggle", updateAria);
    
    document.getElementById("price").value = document.getElementById("price").getAttribute("max");
    window.addEventListener("resize", toggleFiltersOnResize);
    toggleFiltersOnResize();
    FilterForm();
})