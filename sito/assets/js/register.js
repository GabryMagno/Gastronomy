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

function validateUsername(){
	var Username = document.forms['register-form']['register_username'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/; //lettere maiuscole, lettere minuscole e numeri
	// DA TOGLIERE UNA VOLTA AGGIUNTI USER E ADMIN
	if(Username === "user") {
        var check = document.getElementById("username-error");
	    deleteError(check);
		successHint("max-char-username","Massimo 16 caratteri");
		successHint("letter-number-username","Minimo 4 caratteri");
		successHint("min-char-username","Inserire solo lettere o numeri");
		successHint("space-username","Nessuno spazio consentito");
        UltimateCheck();
        return true;
    }

	if(Username.length < 1){
		successHint("max-char-username","Massimo 16 caratteri");

		failHint("min-char-username","Minimo 4 caratteri");
		failHint("letter-number-username","Inserire solo lettere o numeri");
		successHint("space-username","Nessuno spazio consentito");

		return  checkInput("register_username", "username-error", "Lo <span lang='en'>username</span> è un campo obbligatorio", 1);
	} 

	if(Username.length < 4){
		successHint("max-char-username","Massimo 16 caratteri");
		failHint("min-char-username","Minimo 4 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		if (!(/^[\w]{1,}$/.test(Username)) || Username == "") failHint("letter-number-username", "Inserire solo lettere o numeri");
		else successHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("register_username", "username-error", "Lo <span lang='en'>username</span> deve avere una lunghezza minima di 4 caratteri", 1);
	}

	if(Username.length > 16){
		successHint("min-char-username","Massimo 4 caratteri");
		failHint("max-char-username","Minimo 16 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		if (!(/^[\w]{1,}$/.test(Username)) || Username == "") failHint("letter-number-username", "Inserire solo lettere o numeri");
		else successHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("register_username", "username-error", "Lo <span lang='en'>username</span> non deve superare i 16 caratteri", 1);
	}

	if (Username.search(/^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/) != 0 || !allowedChars.test(Username)) {
		successHint("max-char-username","Massimo 16 caratteri");
		successHint("letter-number-username","Minimo 4 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		failHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("register_username", "username-error", "<span lang='en'>Username</span> non valido, usa solo lettere o numeri.", 1);
	}
	var check = document.getElementById("username-error");
	deleteError(check);
	successHint("max-char-username","Massimo 16 caratteri");
	successHint("letter-number-username","Minimo 4 caratteri");
	successHint("min-char-username","Inserire solo lettere o numeri");
	successHint("space-username","Nessuno spazio consentito");
	UltimateCheck();
	return true;
}

function validateName() {
	var Name = document.forms['register-form']['register_nome'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Name.length <= 0){
		failHint("min-char-name","Minimo 1 carattere");
		successHint("max-char-name","Massimo 15 caratteri");

        if(/\s{1,}/.test(Name)) failHint("space-name","Nessuno spazio consentito");
	    else successHint("space-name","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Name)) || Name == "") failHint("letter-name", "Inserire solo lettere");
		else successHint("letter-name","Inserire solo lettere");
		return checkInput("register_nome", "name-error", "Il nome è un campo obbligatorio", 0);
	}

	if(Name.length > 15){
		successHint("min-char-name","Minimo 1 carattere");
		failHint("max-char-name","Massimo 15 caratteri");

		if(/\s{1,}/.test(Name)) failHint("space-name","Nessuno spazio consentito");
	    else successHint("space-name","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Name)) || Name == "") failHint("letter-name", "Inserire solo lettere");
		else successHint("letter-name","Inserire solo lettere");

		return checkInput("register_nome", "name-error", "Il nome non deve superare i 15 caratteri", 0);
	}

	if (Name.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Name)) {
		successHint("min-char-name","Minimo 1 carattere");
		successHint("max-char-name","Massimo 15 caratteri");
		failHint("letter-name","Inserire solo lettere");

		if(/\s{1,}/.test(Name)) failHint("space-name","Nessuno spazio consentito");
	    else successHint("space-name","Nessuno spazio consentito");

		return checkInput("register_nome", "name-error", "Nome non valido, usa solo lettere.", 0);
	}
	var check = document.getElementById("name-error");
	deleteError(check);
	successHint("min-char-name","Minimo 1 carattere");
	successHint("max-char-name","Massimo 15 caratteri");
	successHint("letter-name","Inserire solo lettere");
	successHint("space-name","Nessuno spazio consentito");
	UltimateCheck();
	return true;
}

function validateSurname() {
	var Surname = document.forms['register-form']['register_cognome'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Surname.length <= 0){
		failHint("min-char-surname","Minimo 1 carattere");
		successHint("max-char-surname","Massimo 15 caratteri");

        if(/\s{1,}/.test(Surname)) failHint("space-surname","Nessuno spazio consentito");
	    else successHint("space-surname","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Surname)) || Surname == "") failHint("letter-surname", "Inserire solo lettere");
		else successHint("letter-surname","Inserire solo lettere");

		return checkInput("register_cognome", "surname-error", "Il cognome è un campo obbligatorio", 0);
	}

	if(Surname.length > 15){
		successHint("min-char-surname","Minimo 1 carattere");
		failHint("max-char-surname","Massimo 15 caratteri");

		if(/\s{1,}/.test(Surname)) failHint("space-surname","Nessuno spazio consentito");
	    else successHint("space-surname","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Surname)) || Surname == "") failHint("letter-surname", "Inserire solo lettere");
		else successHint("letter-surname","Inserire solo lettere");

		return checkInput("register_cognome", "surname-error", "Il cognome non deve deve superare i 15 caratteri", 0);
	}

	if (Surname.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Surname)) {
		successHint("min-char-surname","Minimo 1 carattere");
		successHint("max-char-surname","Massimo 15 caratteri");
		failHint("letter-surname","Inserire solo lettere")

		if(/\s{1,}/.test(Surname)) failHint("space-surname","Nessuno spazio consentito");
	    else successHint("space-surname","Nessuno spazio consentito");

		return checkInput("register_cognome", "surname-error", "Cognome non valido, usa solo lettere.", 0);
	}
	var check = document.getElementById("surname-error");
	deleteError(check);
	successHint("min-char-surname","Minimo 1 carattere");
	successHint("max-char-surname","Massimo 15 caratteri");
	successHint("letter-surname","Inserire solo lettere");
	successHint("space-surname","Nessuno spazio consentito");
	UltimateCheck();
	return true;
}

function validateEmail(){
	var Email = document.forms['register-form']['register_email'].value;
	const allowedChars = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if(Email.length < 1){
		failHint("domain-char","Il dominio deve contenere almeno due caratteri");
		successHint("space-email","Nessuno spazio consentito");

		return checkInput("register_email", "email-error", "L'<span lang='en'>email</span> è un campo obbligatorio.", 1);
	}

	if(Email.search(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) != 0 || !allowedChars.test(Email)){
		if(/\s{1,}/.test(Email)) failHint("space-email","Nessuno spazio consentito");
		else successHint("space-email","Nessuno spazio consentito");

		if(!(/^[a-zA-Z0-9](\.?[a-zA-Z0-9_-]+)*@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}/.test(Email))) failHint("domain-char","Il dominio deve contenere almeno due caratteri");
		else successHint("domain-char","Il dominio deve contenere almeno due caratteri");
		return checkInput("register_email", "email-error", "L'<span lang='en'>email</span> inserita non è valida.", 1);
	}
	var check = document.getElementById("email-error");
	deleteError(check);
	successHint("domain-char","Il dominio deve contenere almeno due caratteri");
	successHint("space-email","Nessuno spazio consentito");
	UltimateCheck();
	return true;

}

function validateDate(){
	var birthDate = document.getElementById("register_datanascita").value;
	var day = birthDate.substring(8, 10);
    var month = birthDate.substring(5, 7);
    var year = birthDate.substring(0, 4);

    //CONTROLLI SUL CORRETTO FORMATO DELLE DATE??
	/*if(month == 2 && day > 28){
		if(year > 2007) failHint("min-date","Devi avere almeno 18 anni");
		else successHint("min-date","Devi avere almeno 18 anni");

		if(year < 1900)  failHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
		else successHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");

		return checkInput("register_datanascita", "date-error", "Febbraio ha 28 giorni(tranne per gli anni bisestili)", 0);
	}else*/

	if (year > 2007) {
		failHint("min-date","Devi avere almeno 18 anni");
		successHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
		
		return checkInput("register_datanascita", "date-error", "Per registrarti devi avere almeno 18 anni", 0);
	}else
	if (year <1900) {
		successHint("min-date","Devi avere almeno 18 anni");
		failHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");

		return checkInput("register_datanascita", "date-error", "Per registrarsi inserire un anno successivo al 1899 (almeno 1900)", 0);
	}

	var check = document.getElementById("date-error");
	deleteError(check);
	successHint("min-date","Devi avere almeno 18 anni");
	successHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
	UltimateCheck();
	return true;
}

function checkHintPassword(password){
	if((/[a-zß-ÿ]/.test(password))) successHint("lowercase-letter-password","Almeno una lettera minuscola");
	else failHint("lowercase-letter-password","Almeno una lettera minuscola");

	if((/[A-Z]/.test(password))) successHint("uppercase-letter-password","Almeno una lettera maiuscola");
	else failHint("uppercase-letter-password","Almeno una lettera maiuscola");

	if(/[0-9]/.test(password)) successHint("number-password","Almeno un numero");
	else failHint("number-password","Almeno un numero");

	if((/[.,!?@+\-_€$%&^*<>=#]/.test(password))) successHint("special-char-password","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
	else failHint("special-char-password","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
    
	if(password.search(/^(?=.*\d)(?=.*[.,!?@+\-_€$%&^*<>=#])(?=.*[a-z])(?=.*[A-Z]).{8,}$/ !=0 || /\s{1,}/.test(password)) != 0) failHint("valid-password","Formato valido");
	else successHint("valid-password","Formato valido");

}

function successHintPassword(){
	successHint("lowercase-letter-password","Almeno una lettera minuscola");
	successHint("uppercase-letter-password","Almeno una lettera maiuscola");
	successHint("number-password","Almeno un numero");
	successHint("special-char-password","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
	successHint("min-letter-password","Almeno 8 caratteri");
	successHint("valid-password","Formato valido");
}

function validatePassword(){
	var Password = document.forms['register-form']['register_password'].value;
	const allowedChars = /^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>=]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali
	validateRepeatPassword();
    
    //DA TOGLIERE UNA VOLTA AGGIUNTI ADMIN E USER
	if(Password === "user"){
		var check = document.getElementById("password-error");
		deleteError(check);
		successHintPassword();
		SecondFormUltimateCheck();
		return true;
	}

	if(Password.length < 1){
		failHint("min-letter-password","Almeno 8 caratteri");

		checkHintPassword(Password);

		return checkInput("register_password", "password-error", "La <span lang='en'>password</span> è un campo obbligatorio.", 1);
	}

	if(Password.length < 8){
		failHint("min-letter-password","Almeno 8 caratteri");
        
		checkHintPassword(Password);

		return checkInput("register_password", "password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1);
	}

	if(Password.search(/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>=]).{8,}$/) != 0 || !allowedChars.test(Password)){
		successHint("min-letter-password","Almeno 8 caratteri");
        
		checkHintPassword(Password);
		
		return checkInput("register_password", "password-error", "La <span lang='en'>password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.", 1);
	}

    var check = document.getElementById("password-error");
	deleteError(check);
	successHintPassword();
	UltimateCheck();
	return true;
}

function validateRepeatPassword(){
	var Password = document.forms['register-form']['register_password'].value;
	var repeatPassword = document.forms['register-form']['register_password2'].value;

	if(repeatPassword !== Password){
		failHint("equal-passwords","Le password coincidono");
		return checkInput("register_password2", "repeat-password-error", "Le <span lang='en'>password</span> non coincidono.", 1);
	}
    var check = document.getElementById("repeat-password-error");
	deleteError(check);
	successHint("equal-passwords","Le password coincidono");
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