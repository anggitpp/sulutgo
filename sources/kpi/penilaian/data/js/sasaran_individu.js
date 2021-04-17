jQuery("#bSearch").live("change", function (e) {
			

			setCookie(jQuery("#bSearch").val());
		});
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function setCookie(elem){
	createCookie("tipePenilaian",elem);
}

function cekForm(kat)
{
    if(kat == "t"){ jQuery("#formBreakDown").show("slow"); }
    else{ jQuery("#formBreakDown").hide("slow"); }
}