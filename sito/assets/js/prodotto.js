function validateUserComment() {
    let star_buttons = document.getElementsByName("rating");
    star_buttons.forEach(star => {
        star.addEventListener("click", () =>{
            document.getElementById("reset-comment").classList.remove("not-available");
		    document.getElementById("reset-comment").disabled = false;
        })
    })

    let reset_button = document.getElementById("reset-comment");
    if(reset_button){
        reset_button.addEventListener("click", () =>{
            var check = document.getElementById("comment-error");
            deleteError(check);
            resetHint("min-char-comment","Minimo 30 caratteri");
            resetHint("max-char-comment","Massimo 300 caratteri");

            document.getElementsByName("rating").forEach(star => {
                star.checked = false;
            });
            
            document.getElementById("submit-comment").classList.add("not-available");
            document.getElementById("submit-comment").disabled = true;
            document.getElementById("user-comment").value = "";
            document.getElementById("reset-comment").classList.add("not-available");
            document.getElementById("reset-comment").disabled = true;
            document.getElementById("user-comment").focus();
        });
    }

	let form = document.getElementById("valutazione");


	form.addEventListener("submit", function (event) {
		if (! ((validateComment)) ) {
			event.preventDefault();
			document.getElementById("submit-comment").classList.add("not-available");
			document.getElementById("submit-comment").disabled = true;
		}
	});
}//validateDateOrder

function ValidateUserOrder(){
    let form = document.getElementById("prenotazione");

    if(form){
        form.addEventListener("submit", function (event) {
            if (! (validateDateOrder()) ) {
                event.preventDefault();
                document.getElementById("submit-order").classList.add("not-available");
                document.getElementById("submit-order").disabled = true;
            }else{
                document.getElementById("submit-order").classList.remove("not-available");
                document.getElementById("submit-order").disabled = false;
            }
        });
    }
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

/*function validateNumberElementOrder(){
    
}*/

function validateDateOrder(){
    var orderDate = document.getElementById("data-ritiro").value;
	var day = orderDate.substring(8, 10);
    var month = orderDate.substring(5, 7);
    var year = orderDate.substring(0, 4);
    var inputDate = new Date(month + "/" + day + "/" + year);
    console.log(inputDate);

    // Get today's date
    var todaysDate = new Date();

    // call setHours to take the time out of the comparison

	if (inputDate.setHours(0,0,0,0) <= todaysDate.setHours(0,0,0,0)) {
        var check = document.getElementById("date-error");
        deleteError(check);
        var p = messageError("date-error");
        var now_day = todaysDate.getDate();
        var now_month = todaysDate.getMonth() + 1;
        var now_year = todaysDate.getFullYear();
        if(now_day < 10) now_day = "0"+todaysDate.getDate();
        if(now_month < 10) now_month = "0"+(todaysDate.getMonth()+1); 
        p.innerHTML= "L'ordine può essere ritirato solo nei giorni successivi ad oggi: " + (now_day) + "-" + (now_month) +"-" + now_year;
        document.getElementById("submit-order").classList.add("not-available");
		document.getElementById("submit-order").disabled = true;
        const parent = document.getElementById("data-ritiro").parentNode;
        parent.appendChild(p);
        return false;
	}
	var check = document.getElementById("date-error");
	deleteError(check);
	document.getElementById("submit-order").classList.remove("not-available");
	document.getElementById("submit-order").disabled = false;
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

function validateComment(){
    const Comment = document.forms['valutazione']['user-comment'].value;
    if(Comment.length < 1){
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.add("not-available");
		document.getElementById("reset-comment").disabled = true;
        
        failHint("min-char-comment","Minimo 30 caratteri");
        successHint("max-char-comment","Massimo 300 caratteri");
        return false;
    }
    else if(Comment.length < 30 || /^\\s*$/.test(Comment)){
        var check = document.getElementById("comment-error");
        deleteError(check);
        var p = messageError("comment-error");
        p.innerText = "La lunghezza minima del commento non deve essere inferiore ai 30 caratteri";
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.remove("not-available");
	    document.getElementById("reset-comment").disabled = false;
        const parent = document.getElementById("user-comment").parentNode;
        parent.appendChild(p);
        failHint("min-char-comment","Minimo 30 caratteri");
        successHint("max-char-comment","Massimo 300 caratteri");
        return false;
    }else if(!Comment.replace(/\s/g, '').length){
        var check = document.getElementById("comment-error");
        deleteError(check);
        var p = messageError("comment-error");
        p.innerText = "La lunghezza minima del commento non deve essere inferiore ai 30 caratteri";
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.remove("not-available");
	    document.getElementById("reset-comment").disabled = false;
        const parent = document.getElementById("user-comment").parentNode;
        parent.appendChild(p);
        return false;
    }else if(Comment.length > 300){
        var check = document.getElementById("comment-error");
        deleteError(check);
        var p = messageError("comment-error");
        p.innerText = "La lunghezza massima del commento non deve superare i 300 caratteri";
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.remove("not-available");
	    document.getElementById("reset-comment").disabled = false;
        const parent = document.getElementById("user-comment").parentNode;
        parent.appendChild(p);
        successHint("min-char-comment","Minimo 30 caratteri");
        failHint("max-char-comment","Massimo 300 caratteri");
        return false;
    }
    var check = document.getElementById("comment-error");
    deleteError(check);
    successHint("min-char-comment","Minimo 30 caratteri");
    successHint("max-char-comment","Massimo 300 caratteri");
    document.getElementById("submit-comment").classList.remove("not-available");
	document.getElementById("submit-comment").disabled = false;
    document.getElementById("reset-comment").classList.remove("not-available");
	document.getElementById("reset-comment").disabled = false;
    return true;

}

const listeners = {
	"user-comment" : ["input", validateComment],
    "data-ritiro" : ["input", validateDateOrder],
};

window.addEventListener('load', () => {
	if(document.getElementById("submit-comment")) document.getElementById("submit-comment").disabled = true;
	if(document.getElementById("submit-comment")) document.getElementById("submit-comment").classList.add("not-available");
    if(document.getElementById("reset-comment")) document.getElementById("reset-comment").classList.add("not-available");
	if(document.getElementById("reset-comment")) document.getElementById("reset-comment").disabled = true;
    if(document.getElementById("submit-order")) document.getElementById("submit-order").disabled = true;
	if(document.getElementById("submit-order")) document.getElementById("submit-order").classList.add("not-available");
    ValidateUserOrder();
    checkComment();
	validateUserComment();
});

function checkComment() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}