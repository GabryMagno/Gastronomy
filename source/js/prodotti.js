function FilterForm(){
    var vote = document.getElementById("grade").value;
    var cost =  document.getElementById("price").value;
    document.getElementById("rate").innerText = vote + " / 10";
    document.getElementById("text-price").innerText = cost + " €";
}

function UpdateVoto(){
    const Voto = document.forms["filter-content"]["grade"].value;
    document.getElementById("rate").innerText = Voto + " / 10";
    return Voto;
}

function UpdatePrezzo(){
    const Prezzo = document.forms["filter-content"]["price"].value;
    document.getElementById("text-price").innerText = Prezzo + " €";
    return Prezzo;
}

const filters = {
    "grade" : ["input", UpdateVoto],
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