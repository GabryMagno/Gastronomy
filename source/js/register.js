function validateUserRegister() {

	let form = document.getElementById("register-form");

	form.addEventListener("submit", function (event) {
		if (! (validateNewUsername()) ) {
			event.preventDefault();
			document.getElementById("submit-register").classList.add("not-available");
			document.getElementById("submit-register").disabled = true;
		}
	});
}

function UltimateCheck(){
	var errorName = document.getElementById("name-error");
	if (errorName){
		document.getElementById("submit-register").disabled = true;
		document.getElementById("submit-register").classList.add("not-available");
	}else{
		document.getElementById("submit-register").disabled = false;
		document.getElementById("submit-register").classList.remove("not-available")
	}
}

function validateName() {
	var Name = document.forms['register-form']['register_nome'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Name.length <= 0){
		var check = document.getElementById("name-error");
		deleteError(check);
		var p = messageError("name-error");
	    p.innerText = "Il nome utente è un campo obbligatorio";
		const parent = document.getElementById("register_nome").parentNode;
		parent.appendChild(p);
		UltimateCheck();
		return false;
	}

	if(Name.length > 15){
		var check = document.getElementById("name-error");
		deleteError(check);
		var p = messageError("name-error");
	    p.innerText = "Il nome utente non deve essere più lungo di 15 caratteri";
		const parent = document.getElementById("register_nome").parentNode;
		parent.appendChild(p);
		UltimateCheck();
		return false;
	}

	if (Name.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Name)) {
		var check = document.getElementById("name-error");
		deleteError(check);
		var p = messageError("name-error");
	    p.innerHTML = "Nome non valido, usa solo lettere.";
		const parent = document.getElementById("register_nome").parentNode;
		parent.appendChild(p);
		UltimateCheck();
		return false;
	}
	var check = document.getElementById("name-error");
	deleteError(check);
	UltimateCheck();
	return true;
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

const listeners = {
	"register_nome" : ["input", validateName ],
};

window.addEventListener('load', () => {
	document.getElementById("submit-register").disabled = true;
	document.getElementById("submit-register").classList.add("not-available")
	checkRegister();
	validateUserRegister();
});

function checkRegister() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}