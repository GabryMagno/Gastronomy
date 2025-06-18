function validateUserAdvice() {
    let reset_button = document.getElementById("reset-advice");
    reset_button.addEventListener("click", () =>{
        var check = document.getElementById("advice-error");
        deleteError(check);
        resetHint("min-char-advice","Minimo 30 caratteri");
        resetHint("max-char-advice","Massimo 300 caratteri");
        document.getElementById("submit-advice").classList.add("not-available");
		document.getElementById("submit-advice").disabled = true;
        document.getElementById("user-advice").value = "";
        document.getElementById("reset-advice").classList.add("not-available");
		document.getElementById("reset-advice").disabled = true;
        document.getElementById("user-advice").focus();
    })

	let form = document.getElementById("advice");

	form.addEventListener("submit", function (event) {
		if (! (validateAdvice()) ) {
			event.preventDefault();
			document.getElementById("submit-advice").classList.add("not-available");
			document.getElementById("submit-advice").disabled = true;
		}
	});
}

function resetHint(id,standard){
	document.getElementById(id).textContent = standard;
	document.getElementById(id).classList.remove("fail-hint");
	document.getElementById(id).classList.remove("success-hint");
	document.getElementById(id).style.listStyleType = "disc";
}

function failHint(id,standard){
	document.getElementById(id).classList.remove("success-hint");
	//if(check_num === 1) document.getElementById(id).addHtml = "❌ " + standard
	document.getElementById(id).textContent = "❌ " + standard;//in caso va messo else all'inizio e check_num come parametro della funzione
	document.getElementById(id).classList.add("fail-hint");
	document.getElementById(id).setAttribute("aria-label","Non valido:" + standard);
	document.getElementById(id).style.listStyleType = "none";
}

function successHint(id,standard){
	document.getElementById(id).classList.remove("fail-hint");
	//if(check_num) document.getElementById(id).addHtml = "✔️ " + standard
    document.getElementById(id).textContent = "✔️ " + standard;//in caso va messo else all'inizio e check_num come parametro della funzione
	document.getElementById(id).classList.add("success-hint"); 
	document.getElementById(id).setAttribute("aria-label","Valido:" + standard);
	document.getElementById(id).style.listStyleType = "none";
}

function messageError(id){
	var p = document.createElement("p");
	p.setAttribute("role","alert");
	p.setAttribute("id",id);
	p.classList.add("error");
	return p;
}

function deleteError(p){
	if(p) p.remove();
}

function validateAdvice(){
    const Advice = document.forms['advice']['user-advice'].value;
    if(Advice.length < 1){
        document.getElementById("submit-advice").classList.add("not-available");
		document.getElementById("submit-advice").disabled = true;
        document.getElementById("reset-advice").classList.add("not-available");
		document.getElementById("reset-advice").disabled = true;

        failHint("min-char-advice","Minimo 30 caratteri");
        successHint("max-char-advice","Massimo 300 caratteri");

        return false;
    }
    else if(Advice.length < 30){
        var check = document.getElementById("advice-error");
        deleteError(check);

        var p = messageError("advice-error");
        p.innerText = "La lunghezza minima del messaggio non deve essere inferiore ai 30 caratteri";

        document.getElementById("submit-advice").classList.add("not-available");
		document.getElementById("submit-advice").disabled = true;
        document.getElementById("reset-advice").classList.remove("not-available");
	    document.getElementById("reset-advice").disabled = false;

        const parent = document.getElementById("user-advice").parentNode;
        parent.appendChild(p);

        failHint("min-char-advice","Minimo 30 caratteri");
        successHint("max-char-advice","Massimo 300 caratteri");

        return false;
    }else if(!Advice.replace(/\s/g, '').length){
        var check = document.getElementById("comment-error");
        deleteError(check);

        var p = messageError("comment-error");
        p.innerText = "La lunghezza minima del messaggio non deve essere inferiore ai 30 caratteri";

        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.remove("not-available");
	    document.getElementById("reset-comment").disabled = false;

        const parent = document.getElementById("user-comment").parentNode;
        parent.appendChild(p);

        return false;
    }else if(Advice.length > 300){
        var check = document.getElementById("advice-error");
        deleteError(check);

        var p = messageError("advice-error");
        p.innerText = "La lunghezza massima del messaggio non deve superare i 300 caratteri";

        document.getElementById("submit-advice").classList.add("not-available");
		document.getElementById("submit-advice").disabled = true;
        document.getElementById("reset-advice").classList.remove("not-available");
	    document.getElementById("reset-advice").disabled = false;

        const parent = document.getElementById("user-advice").parentNode;
        parent.appendChild(p);

        successHint("min-char-advice","Minimo 30 caratteri");
        failHint("max-char-advice","Massimo 300 caratteri");

        return false;
    }
    var check = document.getElementById("advice-error");
    deleteError(check);

    successHint("min-char-advice","Minimo 30 caratteri");
    successHint("max-char-advice","Massimo 300 caratteri");

    document.getElementById("submit-advice").classList.remove("not-available");
	document.getElementById("submit-advice").disabled = false;
    document.getElementById("reset-advice").classList.remove("not-available");
	document.getElementById("reset-advice").disabled = false;
    return true;

}

const listeners = {
	"user-advice" : ["input", validateAdvice],
};

window.addEventListener('load', () => {
	document.getElementById("submit-advice").disabled = true;
	document.getElementById("submit-advice").classList.add("not-available");
    document.getElementById("reset-advice").classList.add("not-available");
	document.getElementById("reset-advice").disabled = true;
    checkAdvice();
	validateUserAdvice();
});

function checkAdvice() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}