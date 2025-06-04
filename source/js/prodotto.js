function validateUserComment() {
    let reset_button = document.getElementById("reset-comment");
    reset_button.addEventListener("click", () =>{
        var check = document.getElementById("comment-error");
        deleteError(check);
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("user-comment").value = "";
        document.getElementById("reset-comment").classList.add("not-available");
		document.getElementById("reset-comment").disabled = true;
    })

	let form = document.getElementById("valutazione");

	form.addEventListener("submit", function (event) {
		if (! (validateAdvice()) ) {
			event.preventDefault();
			document.getElementById("submit-comment").classList.add("not-available");
			document.getElementById("submit-comment").disabled = true;
		}
	});
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
    const Advice = document.forms['valutazione']['user-comment'].value;
    if(Advice.length < 1){
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.add("not-available");
		document.getElementById("reset-comment").disabled = true;

    }
    else if(Advice.length < 30){
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
    }
    else if(Advice.length > 300){
        var check = document.getElementById("comment-error");
        deleteError(check);
        var p = messageError("comment-error");
        p.innerText = "La lunghezza massima del messaggio non deve superare i 300 caratteri";
        document.getElementById("submit-comment").classList.add("not-available");
		document.getElementById("submit-comment").disabled = true;
        document.getElementById("reset-comment").classList.remove("not-available");
	    document.getElementById("reset-comment").disabled = false;
        const parent = document.getElementById("user-comment").parentNode;
        parent.appendChild(p);
        return false;
    }
    var check = document.getElementById("comment-error");
    deleteError(check);
    document.getElementById("submit-comment").classList.remove("not-available");
	document.getElementById("submit-comment").disabled = false;
    document.getElementById("reset-comment").classList.remove("not-available");
	document.getElementById("reset-comment").disabled = false;
    return true;

}

const listeners = {
	"user-comment" : ["input", validateComment],
};

window.addEventListener('load', () => {
	document.getElementById("submit-comment").disabled = true;
	document.getElementById("submit-comment").classList.add("not-available");
    document.getElementById("reset-comment").classList.add("not-available");
	document.getElementById("reset-comment").disabled = true;
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