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

window.addEventListener("load", () => {
    FilterForm();
})