function validateLoginForm(){
    let form = document.getElementById("login-user");

	form.addEventListener("submit", function (event) {
		if (! (validateUsername() && validatePassword()) ) {
			event.preventDefault();
			document.getElementById("submit-login").classList.add("not-available");
			document.getElementById("submit-login").disabled = true;
		}
	});
}

function LoginFormUltimateCheck(){
    const usernameError = document.getElementById("username-error");
    const passwordError = document.getElementById("password-error");
    if(usernameError || passwordError) DisableLoginButton();
    else EnableLoginButton();
}

function DisableLoginButton(){
    document.getElementById("submit-login").disabled = true;
	document.getElementById("submit-login").classList.add("not-available");
}

function EnableLoginButton(){
    document.getElementById("submit-login").disabled = false;
	document.getElementById("submit-login").classList.remove("not-available");
}

function checkInput(id_element, id_error_message, error_message, addHtml){
	var check = document.getElementById(id_error_message);
	deleteError(check);
	var p = messageError(id_error_message);
    if(addHtml == 0) p.innerText = error_message;
	else p.innerHTML = error_message;
	const parent = document.getElementById(id_element).parentNode;
	parent.appendChild(p);
    LoginFormUltimateCheck();
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
	var Username = document.forms['login-user']['login_username'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/; //lettere maiuscole, lettere minuscole e numeri

	if(Username === "user" || Username === "admin") {
        var check = document.getElementById("username-error");
	    deleteError(check);
		successHint("max-char-username","Massimo 16 caratteri");
		successHint("letter-number-username","Minimo 4 caratteri");
		successHint("min-char-username","Inserire solo lettere o numeri");
		successHint("space-username","Nessuno spazio consentito");
        LoginFormUltimateCheck();
        return true;
    }
	
	if(Username.length < 1){
		successHint("max-char-username","Massimo 16 caratteri");

		failHint("min-char-username","Minimo 4 caratteri");
		failHint("letter-number-username","Inserire solo lettere o numeri");
		successHint("space-username","Nessuno spazio consentito");

		return  checkInput("login_username", "username-error", "Lo <span lang='en'>username</span> è un campo obbligatorio", 1);
	} 

	if(Username.length < 4){
		successHint("max-char-username","Massimo 16 caratteri");
		failHint("min-char-username","Minimo 4 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		if (!(/^[\w]{1,}$/.test(Username)) || Username == "") failHint("letter-number-username", "Inserire solo lettere o numeri");
		else successHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("login_username", "username-error", "Lo <span lang='en'>username</span> deve avere una lunghezza minima di 4 caratteri", 1);
	}

	if(Username.length > 16){
		successHint("min-char-username","Massimo 4 caratteri");
		failHint("max-char-username","Minimo 16 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		if (!(/^[\w]{1,}$/.test(Username)) || Username == "") failHint("letter-number-username", "Inserire solo lettere o numeri");
		else successHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("login_username", "username-error", "Lo <span lang='en'>username</span> non deve superare i 16 caratteri", 1);
	}

	if (Username.search(/^[a-zA-ZÀ-Ýß-ÿ0-9]{1,16}$/) != 0 || !allowedChars.test(Username)) {
		successHint("max-char-username","Massimo 16 caratteri");
		successHint("letter-number-username","Minimo 4 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		failHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("login_username", "username-error", "<span lang='en'>Username</span> non valido, usa solo lettere o numeri.", 1);
	}
	var check = document.getElementById("username-error");
	deleteError(check);
	successHint("max-char-username","Massimo 16 caratteri");
	successHint("letter-number-username","Minimo 4 caratteri");
	successHint("min-char-username","Inserire solo lettere o numeri");
	successHint("space-username","Nessuno spazio consentito");
	LoginFormUltimateCheck();
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
    var Password = document.forms['login-user']['login_password'].value;
	//const allowedChars = /^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali

    if(Password === "user" || Password === "admin"){
        successHintPassword();
		LoginFormUltimateCheck();
        return true;
    } 

	if(Password.length < 8){
        failHint("min-letter-password","Almeno 8 caratteri");
		failHint("valid-password","Formato valido");

		checkHintPassword(Password);

		return checkInput("new-password", "password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1, 2);
	}

	if(Password.search(/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,=#!?@+\-_€$%&^*<>]).{8,}$/) != 0 || /\s{1,}/.test(Password)){
        successHint("min-letter-password","Almeno 8 caratteri");
		failHint("valid-password","Formato valido");
        
		checkHintPassword(Password);

		return checkInput("new-password", "password-error", "La <span lang='en'>password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.", 1, 2);
	}

    var check = document.getElementById("password-error");
	deleteError(check);
	successHintPassword();
	LoginFormUltimateCheck();
	return true;
}

const listeners = {
	"login_username" : ["input", validateUsername],
	"login_password" : ["input", validatePassword],
};

window.addEventListener('load', () => {
	document.getElementById("submit-login").disabled = true;
	document.getElementById("submit-login").classList.add("not-available");
	checkSettings();
	validateLoginForm();
});

function checkSettings() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}