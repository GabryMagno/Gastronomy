function FilterForm(){
    let reset_button = document.getElementById("reset-filters");
    reset_button.addEventListener("click", () =>{
        document.getElementById("price").value = 100;
        document.getElementById("text-price").innerText =   "100 €";
    });
    var cost =  document.getElementById("price").value;
    document.getElementById("text-price").innerText = cost + " €";
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