function ValidateUserReservation(){
    let form = document.getElementById("prenotazione-degustazione");
    if(form){
		form.addEventListener("submit", function (event) {
			if (! (validateDateReservation()) ) {
				event.preventDefault();
				document.getElementById("submit-reservation").classList.add("not-available");
				document.getElementById("submit-reservation").disabled = true;
			}
		});
	}
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

function validateDateReservation(){
    var reservationDate = document.getElementById("data-prenotazione").value;
	var day = reservationDate.substring(8, 10);
    var month = reservationDate.substring(5, 7);
    var year = reservationDate.substring(0, 4);
    var inputDate = new Date(month + "/" + day + "/" + year);

    // Get today's date
    var todaysDate = new Date();
    console.log(todaysDate);

    // call setHours to take the time out of the comparison

	if (inputDate.setHours(0,0,0,0) <= todaysDate.setHours(0,0,0,0)) {
        var check = document.getElementById("reservation-error");
        deleteError(check);
        var p = messageError("reservation-error");
        var now_day = todaysDate.getDate();
        var now_month = todaysDate.getMonth() + 1;
        var now_year = todaysDate.getFullYear();
        if(now_day < 10) now_day = "0"+todaysDate.getDate();
        if(now_month < 10) now_month = "0"+(todaysDate.getMonth()+1); 
        p.innerHTML= "La prenotazione può essere effettuata solo per i giorni successivi ad oggi: <time datetime=" + (now_day) + "/" + (now_month) +"/" + now_year +">" + (now_day) + "/" + (now_month) +"/" + now_year+"</time>";
        document.getElementById("submit-reservation").classList.add("not-available");
		document.getElementById("submit-reservation").disabled = true;
        const parent = document.getElementById("data-prenotazione").parentNode;
        parent.appendChild(p);
        return false;
	}
	var check = document.getElementById("reservation-error");
	deleteError(check);
	document.getElementById("submit-reservation").classList.remove("not-available");
	document.getElementById("submit-reservation").disabled = false;
	return true;
}

const listeners = {
    "data-prenotazione" : ["input", validateDateReservation],
};

window.addEventListener('load', () => {
	if(document.getElementById("submit-reservation")){
		document.getElementById("submit-reservation").classList.add("not-available");
        document.getElementById("submit-reservation").disabled = true;
	}
    ValidateUserReservation();
    checkReservation();
});

function checkReservation() {
	for (var id in listeners) {
		if (!document.getElementById(id)) {
			continue;
		}
		document.getElementById(id).addEventListener(listeners[id][0], listeners[id][1]);
	}
}