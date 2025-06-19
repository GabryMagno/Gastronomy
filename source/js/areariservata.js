function validateLoginForm(){

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

function checkInput(id_element, id_error_message, error_message, addHtml, whatCheck){
	var check = document.getElementById(id_error_message);
	deleteError(check);
	var p = messageError(id_error_message);
    if(addHtml == 0) p.innerText = error_message;
	else p.innerHTML = error_message;
	const parent = document.getElementById(id_element).parentNode;
	parent.appendChild(p);
    if(whatCheck === 1) FirstFormUltimateCheck();
    else if(whatCheck === 2) SecondFormUltimateCheck();
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

}

function checkHintPassword(password){
	if((/[a-zß-ÿ]/.test(password))) successHint("lowercase-letter","Almeno una lettera minuscola");
	else failHint("lowercase-letter","Almeno una lettera minuscola");

	if((/[A-Z]/.test(password))) successHint("uppercase-letter-"+id,"Almeno una lettera maiuscola");
	else failHint("uppercase-letter","Almeno una lettera maiuscola");

	if(/[0-9]/.test(password)) successHint("number-"+id,"Almeno un numero");
	else failHint("number","Almeno un numero");

	if((/[.,!?@+\-_€$%&^*<>=#]/.test(password))) successHint("special-char","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
	else failHint("special-char","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
    
	if(password.search(/^(?=.*\d)(?=.*[.,!?@+\-_€$%&^*<>=#])(?=.*[a-z])(?=.*[A-Z]).{8,}$/ !=0 || /\s{1,}/.test(password)) != 0) failHint("valid","Formato valido");
	else successHint("valid","Formato valido");

}

function successHintPassword(){
	successHint("lowercase-letter","Almeno una lettera minuscola");
	successHint("uppercase-letter","Almeno una lettera maiuscola");
	successHint("number","Almeno un numero");
	successHint("special-char","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
	successHint("min-letter","Almeno 8 caratteri");
	successHint("valid","Formato valido");
}


function validatePassword(){
    var Password = document.forms['login-user']['login_password'].value;
	//const allowedChars = /^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali
	validateRepeatPassword();
    if(Password === "user" || Password === "admin"){
        successHintPassword();
        return true;
    } 
	if(Password.length < 8){
        failHint("min-letter","Almeno 8 caratteri");
		failHint("valid","Formato valido");

		checkHintPassword(Password);

		return checkInput("new-password", "password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1, 2);
	}

	if(Password.search(/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,=#!?@+\-_€$%&^*<>]).{8,}$/) != 0 || /\s{1,}/.test(Password)){
        successHint("min-letter","Almeno 8 caratteri");
		failHint("valid","Formato valido");
        
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