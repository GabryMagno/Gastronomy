function validateUserRegister() {

	let form = document.getElementById("register-form");

	form.addEventListener("submit", function (event) {
		if (! (validateName() && validateSurname() && validateUsername() && validateDate() && validateEmail() && validatePassword() && validateRepeatPassword()) ) {
			event.preventDefault();
			document.getElementById("submit-register").classList.add("not-available");
			document.getElementById("submit-register").disabled = true;
		}
	});
}

function UltimateCheck(){
	var errorUsername =  document.getElementById("username-error");
	var errorName = document.getElementById("name-error");
	var errorSurname =  document.getElementById("surname-error");
	var errorDate = document.getElementById("date-error");
	var errorEmail = document.getElementById("email-error");
	var errorPassword = document.getElementById("password-error");
	var errorRepeatPassword = document.getElementById("repeat-password-error");
	if (errorName || errorSurname || errorUsername || errorDate || errorEmail || errorPassword || errorRepeatPassword){
		document.getElementById("submit-register").disabled = true;
		document.getElementById("submit-register").classList.add("not-available");
	}else{
		document.getElementById("submit-register").disabled = false;
		document.getElementById("submit-register").classList.remove("not-available")
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
	var Username = document.forms['register-form']['register_username'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/; //lettere maiuscole, lettere minuscole e numeri
	
	if(Username.length < 1){
		return  checkInput("register_username", "username-error", "Lo <span lang='en'>username</span> è un campo obbligatorio", 1);
	} 

	if(Username.length < 4){
		return checkInput("register_username", "username-error", "Lo <span lang='en'>username</span> deve avere una lunghezza minima di 4 caratteri", 1);
	}

	if(Username.length > 16){
		return checkInput("register_username", "username-error", "Lo <span lang='en'>username</span> non deve superare i 16 caratteri", 1);
	}

	if (Username.search(/^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/) != 0 || !allowedChars.test(Username)) {
		return checkInput("register_username", "username-error", "<span lang='en'>Username</span> non valido, usa solo lettere o numeri.", 1);
	}
	var check = document.getElementById("username-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

function validateName() {
	var Name = document.forms['register-form']['register_nome'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Name.length <= 0){
		return checkInput("register_nome", "name-error", "Il nome è un campo obbligatorio", 0);
	}

	if(Name.length > 15){
		return checkInput("register_nome", "name-error", "Il nome non deve superare i 15 caratteri", 0);
	}

	if (Name.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Name)) {
		return checkInput("register_nome", "name-error", "Nome non valido, usa solo lettere.", 0);
	}
	var check = document.getElementById("name-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

function validateSurname() {
	var Surname = document.forms['register-form']['register_cognome'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Surname.length <= 0){
		return checkInput("register_cognome", "surname-error", "Il cognome è un campo obbligatorio", 0);
	}

	if(Surname.length > 15){
		return checkInput("register_cognome", "surname-error", "Il cognome non deve deve superare i 15 caratteri", 0);
	}

	if (Surname.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Surname)) {
		return checkInput("register_cognome", "surname-error", "Cognome non valido, usa solo lettere.", 0);
	}
	var check = document.getElementById("surname-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

function validateEmail(){
	var Email = document.forms['register-form']['register_email'].value;
	const allowedChars = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if(Email.length < 1){
		return checkInput("register_email", "email-error", "L'<span lang='en'>email</span> è un campo obbligatorio.", 1);
	}

	if(Email.search(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) != 0 || !allowedChars.test(Email)){
		return checkInput("register_email", "email-error", "L'<span lang='en'>email</span> inserita non è valida.", 1);
	}
	var check = document.getElementById("email-error");
	deleteError(check);
	UltimateCheck();
	return true;

}

function validateDate(){
	var birthDate = document.getElementById("register_datanascita").value;
	var day = birthDate.substring(8, 10);
    var month = birthDate.substring(5, 7);
    var year = birthDate.substring(0, 4);

	if(month == 2 && day > 28){
		return checkInput("register_datanascita", "date-error", "Febbraio ha 28 giorni(tranne per gli anni bisestili)", 0);
	}else
	if (year > 2007) {
		return checkInput("register_datanascita", "date-error", "Per registrarti devi avere almeno 18 anni", 0);
	}else
	if (year <1900) {
		return checkInput("register_datanascita", "date-error", "Per registrarsi inserire un anno successivo al 1899 (almeno 1900)", 0);
	}

	var check = document.getElementById("date-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

function validatePassword(){
	var Password = document.forms['register-form']['register_password'].value;
	const allowedChars = /^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali
	validateRepeatPassword();

	if(Password.length < 1){
		return checkInput("register_password", "password-error", "La <span lang='en'>password</span> è un campo obbligatorio.", 1);
	}

	if(Password.length < 8){
		return checkInput("register_password", "password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1);
	}

	if(Password.search(/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/) != 0 || !allowedChars.test(Password)){
		return checkInput("register_password", "password-error", "La <span lang='en'>password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.", 1);
	}

    var check = document.getElementById("password-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

function validateRepeatPassword(){
	var Password = document.forms['register-form']['register_password'].value;
	var repeatPassword = document.forms['register-form']['register_password2'].value;

	if(repeatPassword !== Password){
		return checkInput("register_password2", "repeat-password-error", "Le <span lang='en'>password</span> non coincidono.", 1);
	}

    var check = document.getElementById("repeat-password-error");
	deleteError(check);
	UltimateCheck();
	return true;
}

const listeners = {
	"register_nome" : ["input", validateName ],
	"register_cognome" : ["input", validateSurname ],
	"register_username" : ["input", validateUsername],
	"register_datanascita" : ["input", validateDate],
	"register_email" : ["input", validateEmail],
	"register_password" : ["input", validatePassword],
    "register_password2" : ["input", validateRepeatPassword],
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