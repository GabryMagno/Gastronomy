function validateUserPersonalSettings() {

	let reset_button = document.getElementById("reset-user-setting");

	reset_button.addEventListener("click", () => {
		deleteError(document.getElementById("username-error"));
		deleteError(document.getElementById("name-error"));
		deleteError(document.getElementById("surname-error"));
		deleteError(document.getElementById("date-error"));
		deleteError(document.getElementById("logo-error"));
          
        resetHint("letter-number-username","Inserire solo lettere o numeri");
		resetHintPrivateInfo("letter-","Inserire Inserire solo lettere");
		resetHintPrivateInfo("min-char-","Minimo 4 carattere","Minimo 4 caratteri");
		resetHintPrivateInfo("max-char-","Massimo 16 caratteri","Massimo 15 caratteri");
		resetHintPrivateInfo("space-","Nessuno spazio consentito","Nessuno spazio consentito");
		resetHint("min-date","Devi avere almeno 18 anni");
		resetHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
		resetHint("max-size-file","Immagini di dimensione massima a 2MB");
		resetHint("type-file","Solo immagini di tipo png, jpeg o jpg");

		DefaultValue("change-username");
		disableButton("reset-user-setting");
	});

	let form = document.getElementById("change-personal-info");

	form.addEventListener("submit", function (event) {
		if (!(validateUsername() && validateName() && validateSurname() && validateDate())) {
			event.preventDefault();
			disableButton("submit-user-setting");
		}
	});
}

function validateUserPasswordSettings(){
	let reset_button = document.getElementById("reset-password-setting");

	reset_button.addEventListener("click", () => {
		deleteError(document.getElementById("old-password-error"));
		deleteError(document.getElementById("password-error"));
		deleteError(document.getElementById("repeat-password-error"));

		EliminateValue("old-password");
		EliminateValue("new-password");
		EliminateValue("confirm-new-password");

        resetHintPassword("lowercase-letter-","Almeno una lettera minuscola");
		resetHintPassword("uppercase-letter-","Almeno una lettera maiuscola");
		resetHintPassword("number-","Almeno un numero");
		resetHintPassword("special-char-","Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ *<> # =)");
		resetHintPassword("min-letter-","Almeno 8 caratteri");
		resetHintPassword("valid-","Formato valido");

		document.getElementById("reset-password-setting").disabled = true;
		document.getElementById("reset-password-setting").classList.add("not-available");
	});

	let form = document.getElementById("change-password-email");

	form.addEventListener("submit", function (event) {
		if (! (validateOldPassword() && validateNewPassword() && validateRepeatPassword())) {
			event.preventDefault();
			disableButton("submit-password-setting");
		}
	});
}

/*  
    ===================================
        FUNZIONI DI USO GENERALE
    ===================================
*/

function resetHint(id,standard){
	document.getElementById(id).textContent = standard;
	document.getElementById(id).classList.remove("fail-hint");
	document.getElementById(id).classList.remove("success-hint");
	document.getElementById(id).style.listStyleType = "disc";
}

function resetHintPrivateInfo(id,standard1,standard2){
	if(id==="letter-"){
    	resetHint(id+"name",standard1);
		resetHint(id+"surname",standard1);
	}else{
		resetHint(id+"username",standard1);
		resetHint(id+"name",standard2);
		resetHint(id+"surname",standard2);
	}
}

function resetHintPassword(id,standard){
	resetHint(id+"old",standard);
	resetHint(id+"new",standard);
}

function disableButton(id){
    document.getElementById(id).classList.add("not-available");
	document.getElementById(id).disabled = true;
}

function enableButton(id){
	document.getElementById(id).classList.remove("not-available");
	document.getElementById(id).disabled = false;
}

function EliminateValue(id){
    document.getElementById(id).value = "";
}

function DefaultValue(id){
	document.getElementById(id).value = document.getElementById(id).defaultValue;
}

function FirstFormUltimateCheck(){
	var errorUsername =  document.getElementById("username-error");
	var errorName = document.getElementById("name-error");
	var errorSurname =  document.getElementById("surname-error");
	var errorDate = document.getElementById("date-error");
	if (errorName || errorSurname || errorUsername || errorDate) disableButton("submit-user-setting");
	else enableButton("submit-user-setting");
}

function SecondFormUltimateCheck(){
	var errorOldPassword = document.getElementById("old-password-error");
	var errorNewPassword = document.getElementById("password-error");
	var errorNewRepeatPassword = document.getElementById("repeat-password-error");
	if (errorOldPassword || errorNewPassword || errorNewRepeatPassword) disableButton("submit-password-setting")
	else enableButton("submit-password-setting");
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

/*  
    =========================================
        FUNZIONI DI VALIDAZIONE E SPECIFICHE
    =========================================
*/

function validateUsername(){
	var Username = document.forms['change-personal-info']['change-username'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ0-9]{4,16}$/; //lettere maiuscole, lettere minuscole e numeri

	if(Username === "user" || Username === "admin"){
		var check = document.getElementById("username-error");
		deleteError(check);
		FirstFormUltimateCheck();
		return true;
	}

	if(Username.length < 4){
		checkCancelButtonPrivateSettings();

        failHint("min-char-username","Minimo 4 caratteri");
		successHint("max-char-username","Massimo 16 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");
         
		if (!(/^[\w]{1,}$/.test(Username)) || Username == "") failHint("letter-number-username", "Inserire solo lettere o numeri");
		else successHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("change-username", "username-error", "Lo <span lang='en'>username</span> deve avere una lunghezza minima di 4 caratteri", 1, 1);
	}

	if(Username.length > 16){
		failHint("max-char-username","Massimo 16 caratteri");
		successHint("min-char-username","Minimo 4 caratteri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		if (!(/^[\w]{1,}$/.test(Username))) failHint("letter-number-username","Inserire solo lettere o numeri");
		else successHint("letter-number-username","Inserire solo lettere o numeri");

		return checkInput("change-username", "username-error", "Lo <span lang='en'>username</span> non deve superare i 16 caratteri", 1, 1);
	}

	if (Username.search(/^[a-zA-ZÀ-Ýß-ÿ0-9]{4,16}$/) != 0 || !allowedChars.test(Username)) {
		successHint("min-char-username","Minimo 4 caratteri");
		successHint("max-char-username","Massimo 16 caratteri");
		failHint("letter-number-username","Inserire solo lettere o numeri");

		if(/\s{1,}/.test(Username)) failHint("space-username","Nessuno spazio consentito");
	    else successHint("space-username","Nessuno spazio consentito");

		return checkInput("change-username", "username-error", "<span lang='en'>Username</span> non valido, usa solo lettere o numeri.", 1, 1);
	}
	var check = document.getElementById("username-error");
	deleteError(check);
	successHint("min-char-username","Minimo 4 caratteri");
	successHint("max-char-username","Massimo 16 caratteri");
	successHint("letter-number-username","Inserire solo lettere o numeri");
	successHint("space-username","Nessuno spazio consentito");
	checkCancelButtonPrivateSettings();
	FirstFormUltimateCheck()
	return true;
}

function validateName() {
	var Name = document.forms['change-personal-info']['change-name'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Name.length < 1){
		checkCancelButtonPrivateSettings();

		failHint("min-char-name","Minimo 1 carattere");
		successHint("max-char-name","Massimo 15 caratteri");

        if(/\s{1,}/.test(Name)) failHint("space-name","Nessuno spazio consentito");
	    else successHint("space-name","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Name)) || Name == "") failHint("letter-name", "Inserire solo lettere");
		else successHint("letter-name","Inserire solo lettere");

		return checkInput("change-name", "name-error", "Se vuoi modificare il nome devi inserire almeno un carattere", 0, 1);
	}

	if(Name.length > 15){
		successHint("min-char-name","Minimo 1 carattere");
		failHint("max-char-name","Massimo 15 caratteri");

		if(/\s{1,}/.test(Name)) failHint("space-name","Nessuno spazio consentito");
	    else successHint("space-name","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Name)) || Name == "") failHint("letter-name", "Inserire solo lettere");
		else successHint("letter-name","Inserire solo lettere");

		return checkInput("change-name", "name-error", "Il nome non deve superare i 15 caratteri", 0, 1);
	}

	if (Name.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Name)) {

		successHint("min-char-name","Minimo 1 carattere");
		successHint("max-char-name","Massimo 15 caratteri");
		failHint("letter-name","Inserire solo lettere")

		if(/\s{1,}/.test(Name)) failHint("space-name","Nessuno spazio consentito");
	    else successHint("space-name","Nessuno spazio consentito");

		return checkInput("change-name", "name-error", "Nome non valido, usa solo lettere.", 0, 1);
	}
	var check = document.getElementById("name-error");
	deleteError(check);
	successHint("min-char-name","Minimo 1 carattere");
	successHint("max-char-name","Massimo 15 caratteri");
	successHint("letter-name","Inserire solo lettere");
	successHint("space-name","Nessuno spazio consentito");
	checkCancelButtonPrivateSettings();
	FirstFormUltimateCheck();
	return true;
}

function validateSurname() {
	var Surname = document.forms['change-personal-info']['change-surname'].value;
	const allowedChars = /^[a-zA-ZÀ-Ýß-ÿ]+$/; // lettere maiuscole e minuscole
    
    if(Surname.length < 1){
		checkCancelButtonPrivateSettings();

		failHint("min-char-surname","Minimo 1 carattere");
		successHint("max-char-surname","Massimo 15 caratteri");

        if(/\s{1,}/.test(Surname)) failHint("space-surname","Nessuno spazio consentito");
	    else successHint("space-surname","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Surname)) || Surname == "") failHint("letter-surname", "Inserire solo lettere");
		else successHint("letter-surname","Inserire solo lettere");

		return checkInput("change-surname", "surname-error", "Se vuoi modificare il cognome devi inserire almeno un carattere", 0);
	}

	if(Surname.length > 15){
        successHint("min-char-surname","Minimo 1 carattere");
		failHint("max-char-surname","Massimo 15 caratteri");

		if(/\s{1,}/.test(Surname)) failHint("space-surname","Nessuno spazio consentito");
	    else successHint("space-surname","Nessuno spazio consentito");
         
		if (!(/^[a-zA-ZÀ-Ýß-ÿ]{1,}$/.test(Surname)) || Name == "") failHint("letter-surname", "Inserire solo lettere");
		else successHint("letter-surname","Inserire solo lettere");

		return checkInput("change-surname", "surname-error", "Il cognome non deve deve superare i 15 caratteri", 0);
	}

	if (Surname.search(/^[a-zA-ZÀ-Ýß-ÿ]{1,15}$/) != 0 || !allowedChars.test(Surname)) {
		successHint("min-char-surname","Minimo 1 carattere");
		successHint("max-char-surname","Massimo 15 caratteri");
		failHint("letter-surname","Inserire solo lettere")

		if(/\s{1,}/.test(Surname)) failHint("space-surname","Nessuno spazio consentito");
	    else successHint("space-surname","Nessuno spazio consentito");

		return checkInput("change-surname", "surname-error", "Cognome non valido, usa solo lettere.", 0);
	}
	var check = document.getElementById("surname-error");
	deleteError(check);
	successHint("min-char-surname","Minimo 1 carattere");
	successHint("max-char-surname","Massimo 15 caratteri");
	successHint("letter-surname","Inserire solo lettere");
	successHint("space-surname","Nessuno spazio consentito");
	checkCancelButtonPrivateSettings();
	FirstFormUltimateCheck()
	return true;
}

//SE INSERIAMO IL CAMPO MODIFICA EMAIL
/*function validateEmail(){
	var Email = document.forms['register-form']['register_email'].value;
	const allowedChars = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if(Email.length < 1){
		return checkInput("register_email", "email-error", "L'<span lang='en'>email</span> è un campo obbligatorio.", 1, QUALE ULTIMATE CHECK);
	}

	if(Email.search(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/) != 0 || !allowedChars.test(Email)){
		return checkInput("register_email", "email-error", "L'<span lang='en'>email</span> inserita non è valida.", 1, QUALE ULTIMATE CHECK);
	}
	var check = document.getElementById("email-error");
	deleteError(check);
	QUALE ULTIMATE CHECK
	return true;

}*/

function validateDate(){
	var birthDate = document.getElementById("change-date").value;
	var day = birthDate.substring(8, 10);
    var month = birthDate.substring(5, 7);
    var year = birthDate.substring(0, 4);
	checkCancelButtonPrivateSettings();
	if (year > 2007) {
		failHint("min-date","Devi avere almeno 18 anni");
		successHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
		return checkInput("change-date", "date-error", "Per avere un profilo devi avere almeno 18 anni", 0, 1);
	} 
	if (year <1900) {
		successHint("min-date","Devi avere almeno 18 anni");
		failHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
		return checkInput("change-date", "date-error", "Per avere un profilo inserire un anno successivo al 1899 (almeno 1 gennaio 1900)", 0, 1);
	}
	var check = document.getElementById("date-error");
	deleteError(check);
	successHint("min-date","Devi avere almeno 18 anni");
	successHint("max-date","Sono accettate solo le date successive al 1 gennaio 1900");
	FirstFormUltimateCheck();
	return true;
}

function resetLogo(img,src){
	let form = document.getElementById("change-personal-info");
	form.addEventListener("reset", () => {
        img.setAttribute("src",src);
	});
}

function chargeNewLogo() {
	const inputImage = document.getElementById("change-logo");
    const imageOutput = document.getElementById("new-image");
    const img = document.getElementById("new-image");
	var src = img.getAttribute("src");
    resetLogo(img,src);
    inputImage.addEventListener("change", async () => {
        let [file] = inputImage.files;

        const Reader = new FileReader();
        Reader.onload = (e) => {
		    const acceptedImgType = ["image/png", "image/jpeg", "image/jpg"];
		    sizeFile = file.size;
		    Byte = Math.round(sizeFile/1024);

		    if(Byte < 2048 ){
		        if(acceptedImgType.includes(file['type'])){
                    imageOutput.setAttribute("src", e.target.result);

					var sizeError = document.getElementById("logo-error");
					var formatError = document.getElementById("logo-error");

					deleteError(sizeError);
					deleteError(formatError);

					successHint("max-size-file","Dimensione massima a 2MB");
					successHint("type-file","Solo formati png, jpeg o jpg");

					document.getElementById("reset-user-setting").disabled = true;
		            document.getElementById("reset-user-setting").classList.add("not-available");
				}else{
					if(Byte > 2048 ) failHint("max-size-file","Dimensione massima a 2MB");
					else successHint("max-size-file","Dimensione massima a 2MB");
					failHint("type-file","Solo formati png, jpeg o jpg");
					return checkInput("change-logo","logo-error","L'estensione dell'immagine caricata non è corretta",1,1);
				}
            }else{
				if(acceptedImgType.includes(file['type'])) successHint("type-file","Solo formati png, jpeg o jpg");
				else failHint("type-file","Solo formati png, jpeg o jpg");
				failHint("max-size-file","Dimensione massima a 2MB");
				return checkInput("change-logo","logo-error","Sono accettati solo immagini di dimensione inferiore a 2<span lang='en' abbr='megabyte'>MB</span>",1,1);
			}
		}

        Reader.readAsDataURL(file);
    });
	return true;
}

function checkHintPassword(password,id){
	if((/[a-zß-ÿ]/.test(password))) successHint("lowercase-letter-"+id,"Almeno una lettera minuscola");
	else failHint("lowercase-letter-"+id,"Almeno una lettera minuscola");

	if((/[A-Z]/.test(password))) successHint("uppercase-letter-"+id,"Almeno una lettera maiuscola");
	else failHint("uppercase-letter-"+id,"Almeno una lettera maiuscola");

	if(/[0-9]/.test(password)) successHint("number-"+id,"Almeno un numero");
	else failHint("number-"+id,"Almeno un numero");

	if((/[.,!?@+\-_€$%&^*<>=#]/.test(password))) successHint("special-char-"+id,"Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
	else failHint("special-char-"+id,"Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
    
	if(password.search(/^(?=.*\d)(?=.*[.,!?@+\-_€$%&^*<>=#])(?=.*[a-z])(?=.*[A-Z]).{8,}$/ !=0 || /\s{1,}/.test(password)) != 0) failHint("valid-"+id,"Formato valido");
	else successHint("valid-"+id,"Formato valido");

}

function successHintPassword(id){
	successHint("lowercase-letter-"+id,"Almeno una lettera minuscola");
	successHint("uppercase-letter-"+id,"Almeno una lettera maiuscola");
	successHint("number-"+id,"Almeno un numero");
	successHint("special-char-"+id,"Almeno un carattere speciale (. , ! ? @ + \ - _ € $ % & ^ * < > # =)");
	successHint("min-letter-"+id,"Almeno 8 caratteri");
	successHint("valid-"+id,"Formato valido");
}

function validateOldPassword(){
    var oldPassword = document.forms['change-password-email']['old-password'].value;
	//const allowedChars = /^(?=.*\d)(?=.*[!@#$%^&#=*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali
    checkCancelButtonPasswordSettings();
    if(oldPassword === "user" || oldPassword === "admin"){
		var check = document.getElementById("old-password-error");
		deleteError(check);
		successHintPassword("old");
		SecondFormUltimateCheck();
		return true;
	}

	else if(oldPassword.length < 8){
		failHint("min-letter-old","Almeno 8 caratteri");

		checkHintPassword(oldPassword,"old");

		return checkInput("old-password", "old-password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1, 2);
	}

	else if(oldPassword.search(/^(?=.*\d)(?=.*[!@#$%^&=#*])(?=.*[a-z])(?=.*[A-Z]).{8,}$/) != 0 || /\s{1,}/.test(oldPassword)){
		successHint("min-letter-old","Almeno 8 caratteri");
        
		checkHintPassword(oldPassword,"old");

		return checkInput("old-password", "old-password-error", "La <span lang='en'>password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.", 1, 2);
	}
    var check = document.getElementById("old-password-error");
	deleteError(check);
	successHintPassword("old");
	SecondFormUltimateCheck();
	return true;
}

function validateNewPassword(){
	var newPassword = document.forms['change-password-email']['new-password'].value;
	//const allowedChars = /^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>]).{8,}$/;// --> .,!?@+\-_€$%&^*<> questi sono i caratteri speciali
	validateRepeatPassword();
    checkCancelButtonPasswordSettings();
	if(newPassword.length < 8){
        failHint("min-letter-new","Almeno 8 caratteri");
		failHint("valid-new","Formato valido");

		checkHintPassword(newPassword,"new");

		return checkInput("new-password", "password-error", "La <span lang='en'>password</span> deve avere una lunghezza minima di 8 caratteri.", 1, 2);
	}

	if(newPassword.search(/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,=#!?@+\-_€$%&^*<>]).{8,}$/) != 0 || /\s{1,}/.test(newPassword)){
        successHint("min-letter-new","Almeno 8 caratteri");
		failHint("valid-new","Formato valido");
        
		checkHintPassword(newPassword,"new");

		return checkInput("new-password", "password-error", "La <span lang='en'>password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.", 1, 2);
	}

    var check = document.getElementById("password-error");
	deleteError(check);
	successHintPassword("new");
	SecondFormUltimateCheck();
	return true;
}

function validateRepeatPassword(){
	var Password = document.forms['change-password-email']['new-password'].value;
	var repeatPassword = document.forms['change-password-email']['confirm-new-password'].value;
	checkCancelButtonPasswordSettings();
	if(repeatPassword !== Password){
		return checkInput("confirm-new-password", "repeat-password-error", "Le <span lang='en'>password</span> non coincidono.", 1, 2);
	}

    var check = document.getElementById("repeat-password-error");
	deleteError(check);
	SecondFormUltimateCheck();
	return true;
}

function checkCancelButtonPrivateSettings(){
	var Nome = document.getElementById("change-name").value;
	var Cognome = document.getElementById("change-surname").value;
	var Username = document.getElementById("change-username").value;
	var Data = document.getElementById("change-date").value;

	if(Nome.length < 1 && Cognome.length < 1 && Username.length < 1 && Data.length < 1) disableButton("reset-user-setting");
	else enableButton("reset-user-setting");
}

function checkCancelButtonPasswordSettings(){
	var oldPassword = document.getElementById("old-password").value;
	var newPassword = document.getElementById("new-password").value;
	var repeatNewPassword = document.getElementById("confirm-new-password").value;

	if(oldPassword.length < 1 && newPassword.length < 1 && repeatNewPassword.length < 1)disableButton("reset-password-setting")
	else enableButton("reset-password-setting");
}
/*  
    ===================================
        GESTIONE CARICAMENTO PAGINA
    ===================================
*/
const listeners = {
	"change-name" : ["input", validateName ],
	"change-surname" : ["input", validateSurname ],
	"change-username" : ["input", validateUsername],
	"change-date" : ["input", validateDate],
	"change-logo" : ["input", chargeNewLogo],
	"old-password" : ["input", validateOldPassword],
	"new-password" : ["input", validateNewPassword],
    "confirm-new-password" : ["input", validateRepeatPassword],
};

window.addEventListener('load', () => {
	document.getElementById("submit-user-setting").disabled = true;
	document.getElementById("submit-user-setting").classList.add("not-available");
	document.getElementById("submit-password-setting").disabled = true;
	document.getElementById("submit-password-setting").classList.add("not-available");
	checkCancelButtonPrivateSettings();
	checkCancelButtonPasswordSettings();
	checkSettings();
	validateUserPersonalSettings();
	validateUserPasswordSettings();
});

function checkSettings() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}