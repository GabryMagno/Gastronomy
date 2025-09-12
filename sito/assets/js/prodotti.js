function FilterForm(){
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
    const priceInput = document.getElementById("price");
    document.getElementById("text-price").innerText = priceInput.value + " €";
}

function UpdatePrezzo(){
    const Prezzo = document.forms["filter-content"]["price"].value;
    document.getElementById("text-price").innerText = Prezzo + " €";
    return Prezzo;
}

const filters = {
    "price" : ["input", UpdatePrezzo],
}

function Update(){
    for (var id in filters) {
        if (!document.getElementById(id)) {
            continue;
		}
		document.getElementById(id).addEventListener(filters[id][0], filters[id][1]);
	}
}

window.addEventListener("load", () => {
    Update();
    FilterForm();
})