function validateUserLogin() {

	let form = document.getElementById("login_form");

	form.addEventListener("submit", function (event) {
		if (! (validateUsername() && validatePassword()) ) {
			event.preventDefault();
			document.getElementById("submit-login").classList.add("not-available");
			document.getElementById("submit-login").disabled = true;
		}
	});
}

function UltimateCheck(){
	var errorUsername =  document.getElementById("username-error");
	var errorPassword = document.getElementById("password-error");
	
	if (errorUsername || errorPassword){
		document.getElementById("submit-login").disabled = true;
		document.getElementById("submit-login").classList.add("not-available");
	}else{
		document.getElementById("submit-login").disabled = false;
		document.getElementById("submit-login").classList.remove("not-available")
	}
}

function checkInput(id_element, id_error_message, error_message, addHtml){
	var check = document.getElementById(id_error_message);
	deleteError(check);
	var p = messageError(id_error_message);
    if(addHtml == 0) p.innerText = error_message;
	else p.innerHTML = error_message;
	const parent = document.getElementById(id_element).parentNode;
	parent.appendChild(p);
	UltimateCheck();
	return false;
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

function validateUsername(){
	var Username = document.forms['login_form']['login_username'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/; //lettere maiuscole, lettere minuscole e numeri
	
	if(Username.length < 1){
		return  checkInput("login_username", "username-error", "Lo <span lang='en'>username</span> è un campo obbligatorio", 1);
	} 

    if(Username === "user" || Username === "admin") {
        var check = document.getElementById("username-error");
	    deleteError(check);
        UltimateCheck();
        return true;
    }

	if(Username.length < 4){
		return checkInput("login_username", "username-error", "Lo <span lang='en'>username</span> deve avere una lunghezza minima di 4 caratteri", 1);
	}

	if(Username.length > 16){
		return checkInput("login_username", "username-error", "Lo <span lang='en'>username</span> non deve superare i 16 caratteri", 1);
	}

	if (Username.search(/^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/) != 0 || !allowedChars.test(Username)) {
		return checkInput("login_username", "username-error", "<span lang='en'>Username</span> non valido, usa solo lettere o numeri.", 1);
	}
	var check = document.getElementById("username-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

function validatePassword(){
	var Password = document.forms['login_form']['login_password'].value;
	const allowedChars = /^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali

	if(Password.length < 1){
		return checkInput("login_password", "password-error", "La <span lang='en'>password</span> è un campo obbligatorio.", 1);
	}

    if(Password === "user" || Password === "admin"){
        var check = document.getElementById("password-error");
	    deleteError(check);
        UltimateCheck();
        return true;
    }

	if(Password.length < 8){
		return checkInput("login_password", "password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1);
	}

	if(Password.search(/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/) != 0 || !allowedChars.test(Password)){
		return checkInput("login_password", "password-error", "La <span lang='en'>password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.", 1);
	}

    var check = document.getElementById("password-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

const listeners = {
	"login_username" : ["input", validateUsername],
	"login_password" : ["input", validatePassword],
};

window.addEventListener('load', () => {
	document.getElementById("submit-login").disabled = true;
	document.getElementById("submit-login").classList.add("not-available");
	checkLogin();
	validateUserLogin();
});

function checkLogin() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}