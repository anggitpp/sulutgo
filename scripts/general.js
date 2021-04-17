/*
 * 	Additional function for this template
 *	Written by ThemePixels
 *	http://themepixels.com/
 *
 *	Copyright (c) 2012 ThemePixels (http://themepixels.com)
 *
 *	Built for Amanda Premium Responsive Admin Template
 *  http://themeforest.net/category/site-templates/admin-templates
 */


jQuery.noConflict();

jQuery(document).ready(function () {

	///// SHOW/HIDE USERDATA WHEN USERINFO IS CLICKED /////

	jQuery('.userinfo').click(function () {
		if (!jQuery(this).hasClass('active')) {
			jQuery('.userinfodrop').show();
			jQuery(this).addClass('active');
		} else {
			jQuery('.userinfodrop').hide();
			jQuery(this).removeClass('active');
		}
		//remove notification box if visible
		jQuery('.notification').removeClass('active');
		jQuery('.noticontent').remove();

		return false;
	});

	jQuery(".hasDatePicker").datepicker({
		dateFormat: "dd/mm/yy"
	});

	jQuery(".hasTimePicker").timepicker({});

	///// SHOW/HIDE NOTIFICATION /////

	jQuery('.notification a').click(function () {
		var t = jQuery(this);
		var url = t.attr('href');
		if (!jQuery('.noticontent').is(':visible')) {
			jQuery.post(url, function (data) {
				t.parent().append('<div class="noticontent">' + data + '</div>');
			});
			//this will hide user info drop down when visible
			jQuery('.userinfo').removeClass('active');
			jQuery('.userinfodrop').hide();
		} else {
			t.parent().removeClass('active');
			jQuery('.noticontent').hide();
		}
		return false;
	});



	///// SHOW/HIDE BOTH NOTIFICATION & USERINFO WHEN CLICKED OUTSIDE OF THIS ELEMENT /////


	jQuery(document).click(function (event) {
		var ud = jQuery('.userinfodrop');
		var nb = jQuery('.noticontent');

		//hide user drop menu when clicked outside of this element
		if (!jQuery(event.target).is('.userinfodrop')
			&& !jQuery(event.target).is('.userdata')
			&& ud.is(':visible')) {
			ud.hide();
			jQuery('.userinfo').removeClass('active');
		}

		//hide notification box when clicked outside of this element
		if (!jQuery(event.target).is('.noticontent') && nb.is(':visible')) {
			nb.remove();
			jQuery('.notification').removeClass('active');
		}
	});


	///// NOTIFICATION CONTENT /////

	jQuery('.notitab a').live('click', function () {
		var id = jQuery(this).attr('href');
		jQuery('.notitab li').removeClass('current'); //reset current
		jQuery(this).parent().addClass('current');
		if (id == '#messages')
			jQuery('#activities').hide();
		else
			jQuery('#messages').hide();

		jQuery(id).show();
		return false;
	});



	///// SHOW/HIDE VERTICAL SUB MENU /////

	jQuery('.vernav > ul li a, .vernav2 > ul li a').each(function () {
		var url = jQuery(this).attr('href');
		jQuery(this).click(function () {
			if (jQuery(url).length > 0) {
				if (jQuery(url).is(':visible')) {
					if (!jQuery(this).parents('div').hasClass('menucoll') &&
						!jQuery(this).parents('div').hasClass('menucoll2'))
						jQuery(url).slideUp();
				} else {
					jQuery('.vernav ul ul, .vernav2 ul ul').each(function () {
						jQuery(this).slideUp();
					});
					if (!jQuery(this).parents('div').hasClass('menucoll') &&
						!jQuery(this).parents('div').hasClass('menucoll2'))
						jQuery(url).slideDown();
				}
				return false;
			}
		});
	});

	///// COLOR PICKER 2 /////
	jQuery('#colorSelector').ColorPicker({
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#colorSelector span').css('backgroundColor', '#' + hex);
			jQuery('#isiWarna').val('#' + hex);
		}
	});

	///// SHOW/HIDE SUB MENU WHEN MENU COLLAPSED /////
	jQuery('.menucoll > ul > li, .menucoll2 > ul > li').live('mouseenter mouseleave', function (e) {
		if (e.type == 'mouseenter') {
			jQuery(this).addClass('hover');
			jQuery(this).find('ul').show();
		} else {
			jQuery(this).removeClass('hover').find('ul').hide();
		}
	});


	///// HORIZONTAL NAVIGATION (AJAX/INLINE DATA) /////

	jQuery('.hornav a').click(function () {

		//this is only applicable when window size below 450px
		if (jQuery(this).parents('.more').length == 0)
			jQuery('.hornav li.more ul').hide();

		//remove current menu
		jQuery('.hornav li').each(function () {
			jQuery(this).removeClass('current');
		});

		jQuery(this).parent().addClass('current');	// set as current menu

		var url = jQuery(this).attr('href');
		if (jQuery(url).length > 0) {
			jQuery('.contentwrapper .subcontent').hide();
			jQuery(url).show();
		} else {
			jQuery.post(url, function (data) {
				jQuery('#contentwrapper').html(data);
				jQuery('.stdtable input:checkbox').uniform();	//restyling checkbox
			});
		}
		return false;
	});

	///// HORIZONTAL NAVIGATION (AJAX/INLINE DATA) /////

	jQuery('.editornav a').click(function () {

		//this is only applicable when window size below 450px
		if (jQuery(this).parents('.more').length == 0)
			jQuery('.editornav li.more ul').hide();

		//remove current menu
		jQuery('.editornav li').each(function () {
			jQuery(this).removeClass('current');
		});

		jQuery(this).parent().addClass('current');	// set as current menu

		var url = jQuery(this).attr('href');
		if (jQuery(url).length > 0) {
			jQuery('.contentwrapper .subcontent1').hide();
			jQuery(url).show();
		} else {
			jQuery.post(url, function (data) {
				jQuery('#contentwrapper').html(data);
				jQuery('.stdtable input:checkbox').uniform();	//restyling checkbox
			});
		}
		return false;
	});


	///// SEARCH BOX WITH AUTOCOMPLETE /////

	var availableTags = [
		"ActionScript",
		"AppleScript",
		"Asp",
		"BASIC",
		"C",
		"C++",
		"Clojure",
		"COBOL",
		"ColdFusion",
		"Erlang",
		"Fortran",
		"Groovy",
		"Haskell",
		"Java",
		"JavaScript",
		"Lisp",
		"Perl",
		"PHP",
		"Python",
		"Ruby",
		"Scala",
		"Scheme"
	];
	jQuery('#keyword').autocomplete({
		source: availableTags
	});


	///// SEARCH BOX ON FOCUS /////

	jQuery('#keyword').bind('focusin focusout', function (e) {
		var t = jQuery(this);
		if (e.type == 'focusin' && t.val() == 'Enter keyword(s)') {
			t.val('');
		} else if (e.type == 'focusout' && t.val() == '') {
			t.val('Enter keyword(s)');
		}
	});


	///// NOTIFICATION CLOSE BUTTON /////

	jQuery('.notibar .close').click(function () {
		jQuery(this).parent().fadeOut(function () {
			jQuery(this).remove();
		});
	});


	///// COLLAPSED/EXPAND LEFT MENU /////
	jQuery('.togglemenu').click(function () {
		if (!jQuery(this).hasClass('togglemenu_collapsed')) {

			//if(jQuery('.iconmenu').hasClass('vernav')) {
			if (jQuery('.vernav').length > 0) {
				if (jQuery('.vernav').hasClass('iconmenu')) {
					jQuery('body').addClass('withmenucoll');
					jQuery('.iconmenu').addClass('menucoll');
				} else {
					jQuery('body').addClass('withmenucoll');
					jQuery('.vernav').addClass('menucoll').find('ul').hide();
				}
			} else if (jQuery('.vernav2').length > 0) {
				//} else {
				jQuery('body').addClass('withmenucoll2');
				jQuery('.iconmenu').addClass('menucoll2');
			}

			jQuery(this).addClass('togglemenu_collapsed');
			jQuery('.loginbox').attr('style', 'display:none;');
			jQuery('.infobox').attr('style', 'display:none;');
			jQuery('.vernav2 ul ul li ').attr('style', 'background:#e0e0e0;');
			jQuery('.iconmenu > ul > li > a').each(function () {
				var label = jQuery(this).text().toUpperCase();
				jQuery(this).attr('title', label);
				jQuery('<li><span>' + label + '</span></li>').insertBefore(jQuery(this).parent().find('ul li:first-child'));
			});
		} else {
			//if(jQuery('.iconmenu').hasClass('vernav')) {
			if (jQuery('.vernav').length > 0) {
				if (jQuery('.vernav').hasClass('iconmenu')) {
					jQuery('body').removeClass('withmenucoll');
					jQuery('.iconmenu').removeClass('menucoll');
				} else {
					jQuery('body').removeClass('withmenucoll');
					jQuery('.vernav').removeClass('menucoll').find('ul').show();
				}
			} else if (jQuery('.vernav2').length > 0) {
				//} else {
				jQuery('body').removeClass('withmenucoll2');
				jQuery('.iconmenu').removeClass('menucoll2');
			}
			jQuery(this).removeClass('togglemenu_collapsed');
			jQuery('.loginbox').attr('style', 'display:block; width:90%; margin-bottom:10px;');
			jQuery('.infobox').attr('style', 'display:block;');
			jQuery('.vernav2 ul ul li ').attr('style', '');

			jQuery('.iconmenu ul ul li:first-child').remove();

			jQuery('.iconmenu > ul > li > a').each(function () {
				jQuery(this).attr('title', '');
			});
		}
	});



	///// RESPONSIVE /////
	if (jQuery(document).width() < 640) {
		jQuery('.togglemenu').addClass('togglemenu_collapsed');
		if (jQuery('.vernav').length > 0) {

			jQuery('.iconmenu').addClass('menucoll');
			jQuery('body').addClass('withmenucoll');
			jQuery('.centercontent').css({ marginLeft: '56px' });
			if (jQuery('.iconmenu').length == 0) {
				jQuery('.togglemenu').removeClass('togglemenu_collapsed');
			} else {
				jQuery('.iconmenu > ul > li > a').each(function () {
					var label = jQuery(this).text();
					jQuery('<li><span>' + label + '</span></li>')
						.insertBefore(jQuery(this).parent().find('ul li:first-child'));
				});
			}

		} else {

			jQuery('.iconmenu').addClass('menucoll2');
			jQuery('body').addClass('withmenucoll2');
			jQuery('.centercontent').css({ marginLeft: '36px' });

			jQuery('.iconmenu > ul > li > a').each(function () {
				var label = jQuery(this).text();
				jQuery('<li><span>' + label + '</span></li>')
					.insertBefore(jQuery(this).parent().find('ul li:first-child'));
			});
		}
	}


	jQuery('.searchicon').live('click', function () {
		jQuery('.searchinner').show();
	});

	jQuery('.searchcancel').live('click', function () {
		jQuery('.searchinner').hide();
	});



	///// ON LOAD WINDOW /////
	function reposSearch() {
		if (jQuery(window).width() < 520) {
			if (jQuery('.searchinner').length == 0) {
				jQuery('.search').wrapInner('<div class="searchinner"></div>');
				jQuery('<a class="searchicon"></a>').insertBefore(jQuery('.searchinner'));
				jQuery('<a class="searchcancel"></a>').insertAfter(jQuery('.searchinner button'));
			}
		} else {
			if (jQuery('.searchinner').length > 0) {
				jQuery('.search form').unwrap();
				jQuery('.searchicon, .searchcancel').remove();
			}
		}
	}
	reposSearch();

	///// ON RESIZE WINDOW /////
	jQuery(window).resize(function () {

		if (jQuery(window).width() > 640)
			jQuery('.centercontent').removeAttr('style');

		reposSearch();

	});


	///// CHANGE THEME /////
	jQuery('.changetheme a').click(function () {
		var c = jQuery(this).attr('class');
		if (jQuery('#addonstyle').length == 0) {
			if (c != 'default') {
				jQuery('head').append('<link id="addonstyle" rel="stylesheet" href="css/style.' + c + '.css" type="text/css" />');
				jQuery.cookie("addonstyle", c, { path: '/' });
			}
		} else {
			if (c != 'default') {
				jQuery('#addonstyle').attr('href', 'css/style.' + c + '.css');
				jQuery.cookie("addonstyle", c, { path: '/' });
			} else {
				jQuery('#addonstyle').remove();
				jQuery.cookie("addonstyle", null);
			}
		}
	});

	///// LOAD ADDON STYLE WHEN IT'S ALREADY SET /////
	if (jQuery.cookie('addonstyle')) {
		var c = jQuery.cookie('addonstyle');
		if (c != '') {
			jQuery('head').append('<link id="addonstyle" rel="stylesheet" href="css/style.' + c + '.css" type="text/css" />');
			jQuery.cookie("addonstyle", c, { path: '/' });
		}
	}


	if (jQuery('.chosen-select').length > 0) {
		jQuery('.chosen-select').each(function () {
			var search = (jQuery(this).attr("data-nosearch") === "true") ? true : false,
				opt = {};
			if (search) opt.disable_search_threshold = 9999999;
			jQuery(this).chosen(opt);
		});
	}

});





function getXMLHttp() {
	var xmlHttp
	try {
		//Firefox, Opera 8.0+, Safari
		xmlHttp = new XMLHttpRequest();
	}
	catch (e) {
		//Internet Explorer
		try {
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e) {
				alert("Your browser does not support AJAX!")
				return false;
			}
		}
	}
	return xmlHttp;
}

function page(targ, selObj, restore) {
	window.location = selObj.options[selObj.selectedIndex].value;
}

function logout() {
	parent.window.location = "logout.php";
}

function formatAngka(value) {
	a = value;
	if (a == undefined) return false;
	x = a.split(".");
	b = x[0].replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g, '');
	c = "";

	panjang = b.length;
	j = 0;
	for (i = panjang; i > 0; i--) {
		j = j + 1;
		if (((j % 3) == 1) && (j != 1)) {
			c = b.substr(i - 1, 1) + "," + c;
		} else {
			c = b.substr(i - 1, 1) + c;
		}
	}
	if (x[1] == undefined) {
		return c;
	} else {
		return c + "." + x[1].substr(0, 2);
	}
}

function formatDecimal(value) {
	a = value;
	x = a.split(".");
	b = x[0].replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g, '');
	c = "";

	panjang = b.length;
	j = 0;
	for (i = panjang; i > 0; i--) {
		j = j + 1;
		if (((j % 3) == 1) && (j != 1)) {
			c = b.substr(i - 1, 1) + "," + c;
		} else {
			c = b.substr(i - 1, 1) + c;
		}
	}

	if (x.length == 1) {
		return c;
	} else {
		return c + "." + x[1].substr(0, 2);
	}

}

function formatNumber(num) {
	if (num < 1) return "";
	num = num.toString().replace(/\$|\,/g, '');
	if (isNaN(num))
		num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num * 100 + 0.50000000001);
	cents = num % 100;
	num = Math.floor(num / 100).toString();
	if (cents < 10)
		cents = "0" + cents;

	for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
		num = num.substring(0, num.length - (4 * i + 3)) + ',' +
			num.substring(num.length - (4 * i + 3));

	return (((sign) ? '' : '-') + num);
}

function formatCurrency(val) {

	x = val.split(".");
	num = x[0];

	if (num < 1) return "";
	num = num.toString().replace(/\$|\,/g, '');
	if (isNaN(num))
		num = "0";
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num * 100 + 0.50000000001);
	cents = num % 100;
	num = Math.floor(num / 100).toString();
	if (cents < 10)
		cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
		num = num.substring(0, num.length - (4 * i + 3)) + ',' +
			num.substring(num.length - (4 * i + 3));

	if (x.length == 1)
		return (((sign) ? '' : '-') + num);
	else
		return (((sign) ? '' : '-') + num + "." + x[1].substr(0, 2));
}

function cekArea(elem, limit) {
	cek = limit - elem.value.length;
	if (cek < 0) {
		elem.value = elem.value.substring(0, limit);
		alert("Maximum character : " + limit);
	}
}

function cekNumber(elem) {
	replace = formatAngka(elem.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g, ''));
	if (replace.length == 0) replace = 0;
	elem.value = replace;
}


function setAngka(elem) {
	replace = formatCurrency(elem.value);
	if (replace.length == 0) replace = 0;
	elem.value = replace;
}

function cekAngka(elem) {
	replace = formatCurrency(elem.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g, ''));
	if (replace.length == 0) replace = 0;
	elem.value = replace;
}

function cekPhone(elem) {
	replace = elem.value.replace(/[\\A-Za-z!"?$%^&*_={};.\:'/@#~,?\<>?|`?\]\[]/g, '');
	elem.value = replace;
}

function replaceAll(Source, stringToFind, stringToReplace) {
	var temp = Source;
	if (temp == undefined) return false;
	var index = temp.indexOf(stringToFind);
	while (index != -1) {
		temp = temp.replace(stringToFind, stringToReplace);
		index = temp.indexOf(stringToFind);
	}
	return temp;
}

function convert(val) {
	return nilai = replaceAll(val, ",", "");
}

function openBox(url, w, h) {
	url = replaceAll(url, " xandx ", "&");
	tiny.box.show({ iframe: url, width: w, height: h, close: true });
}

function closeBox() {
	parent.tiny.box.hide();
}

function reloadPage() {
	_par = parent.document.getElementById("_par").value;
	_page = parent.document.getElementById("_page").value;
	_len = parent.document.getElementById("_len").value;
	parent.location = "index.php?_p=" + _page + "&_l=" + _len + _par;
}

function reloadPopup() {
	_par = parent.document.getElementById("_par").value;
	_page = parent.document.getElementById("_page").value;
	_len = parent.document.getElementById("_len").value;
	parent.location = "popup.php?_p=" + _page + "&_l=" + _len + _par;
}

function hideMenu() {
	//if(jQuery('.iconmenu').hasClass('vernav')) {
	if (jQuery('.vernav').length > 0) {
		if (jQuery('.vernav').hasClass('iconmenu')) {
			jQuery('body').removeClass('withmenucoll');
			jQuery('.iconmenu').removeClass('menucoll');
		} else {
			jQuery('body').removeClass('withmenucoll');
			jQuery('.vernav').removeClass('menucoll').find('ul').show();
		}
	} else if (jQuery('.vernav2').length > 0) {
		//} else {
		jQuery('body').removeClass('withmenucoll2');
		jQuery('.iconmenu').removeClass('menucoll2');
	}
	jQuery(".togglemenu").addClass('togglemenu_collapsed');
	jQuery('.loginbox').attr('style', 'display:block; width:90%; margin-bottom:10px;');
	jQuery('.infobox').attr('style', 'display:block;');
	jQuery('.vernav2 ul ul li ').attr('style', '');

	jQuery('.iconmenu ul ul li:first-child').remove();

	jQuery('.iconmenu > ul > li > a').each(function () {
		jQuery(this).attr('title', '');
	});
}

function login() {
	username = document.getElementById("username");
	password = document.getElementById("password");

	if (username.value.length <= 0) {
		alert("you must fill username");
		username.focus();
		return false;
	}

	if (password.value.length <= 0) {
		alert("you must fill password");
		password.focus();
		return false;
	}

	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			if (xmlHttp.responseText) {
				alert(xmlHttp.responseText);
			} else {
				document.getElementById("login").submit();
			}
		}
	}

	xmlHttp.open("GET", "ajax.php?par[mode]=lgn&inp[username]=" + username.value + "&inp[password]=" + password.value, true);
	xmlHttp.send(null);
	return false;
}

function hideMenu() {
	//if(jQuery('.iconmenu').hasClass('vernav')) {
	if (jQuery('.vernav').length > 0) {
		if (jQuery('.vernav').hasClass('iconmenu')) {
			jQuery('body').addClass('withmenucoll');
			jQuery('.iconmenu').addClass('menucoll');
		} else {
			jQuery('body').addClass('withmenucoll');
			jQuery('.vernav').addClass('menucoll').find('ul').hide();
		}
	} else if (jQuery('.vernav2').length > 0) {
		//} else {
		jQuery('body').addClass('withmenucoll2');
		jQuery('.iconmenu').addClass('menucoll2');
	}

	jQuery(this).addClass('togglemenu_collapsed');
	jQuery('.loginbox').attr('style', 'display:none;');

	jQuery('.iconmenu > ul > li > a').each(function () {
		var label = jQuery(this).text().toUpperCase();
		jQuery(this).attr('title', label);
		jQuery('<li><span>' + label + '</span></li>')
			.insertBefore(jQuery(this).parent().find('ul li:first-child'));
	});
}

function urlExist(url) {
	var http = new XMLHttpRequest();
	http.open('HEAD', url, false);
	http.send();
	return http.status != 404;
}

function gMonth() {
	var date = jQuery('#calendar').fullCalendar('getDate');
	month = date.getMonth() + 1;
	return month < 10 ? '0' + month : month;
}

function gYear() {
	var date = jQuery('#calendar').fullCalendar('getDate');
	return date.getFullYear();
}

function getTanggal(tanggal) {
	if (tanggal.length > 0) {
		var arr = tanggal.split("-");
		return arr[2] + "/" + arr[1] + "/" + arr[0];
	} else {
		return "";
	}
}

function setTanggal(tanggal) {
	if (tanggal.length > 0) {
		var arr = tanggal.split("/");
		return arr[2] + "-" + arr[1] + "-" + arr[0];
	} else {
		return "";
	}
}

function getSub(idInduk, idAnak, getPar, mode = "") {
	if (mode === "") {
		mode = "subData";
	}
	kodeInduk = document.getElementById('inp[' + idInduk + ']');
	kodeAnak = document.getElementById('inp[' + idAnak + ']');
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			for (var i = kodeAnak.options.length - 1; i >= 0; i--) {
				kodeAnak.remove(i);
				jQuery("#inp\\[" + idAnak + "\\]").trigger("chosen:updated");
			}
			if (xmlHttp.responseText) {
				var arr = xmlHttp.responseText.split("\n");
				var opt = document.createElement("OPTION");
				opt.value = "";
				opt.text = "";
				kodeAnak.options.add(opt);
				for (var i = 0; i < arr.length; i++) {
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];
					opt.text = val[1];
					if (opt.value) kodeAnak.options.add(opt);
					jQuery("#inp\\[" + idAnak + "\\]").trigger("chosen:updated");
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=" + mode + "&par[kodeInduk]=" + kodeInduk.value + getPar, true);
	console.log("ajax.php?par[mode]=" + mode + "&par[kodeInduk]=" + kodeInduk.value + getPar);
	xmlHttp.send(null);
	return false;
}

function dateDiff(fromDate, toDate, interval) {
	/*
	 * DateFormat month/day/year hh:mm:ss
	 * ex.
	 * datediff('01/01/2011 12:00:00','01/01/2011 13:30:00','seconds');
	 */

	awal = fromDate.split("/");
	akhir = toDate.split("/");

	var second = 1000, minute = second * 60, hour = minute * 60, day = hour * 24, week = day * 7;
	fromDate = new Date(awal[1] + "/" + awal[0] + "/" + awal[2]);
	toDate = new Date(akhir[1] + "/" + akhir[0] + "/" + akhir[2]);
	var timediff = toDate - fromDate;
	if (isNaN(timediff)) return NaN;
	switch (interval) {
		case "years": return toDate.getFullYear() - fromDate.getFullYear();
		case "months": return (
			(toDate.getFullYear() * 12 + toDate.getMonth())
			-
			(fromDate.getFullYear() * 12 + fromDate.getMonth())
		);
		case "weeks": return Math.floor(timediff / week);
		case "days": return Math.floor(timediff / day);
		case "hours": return Math.floor(timediff / hour);
		case "minutes": return Math.floor(timediff / minute);
		case "seconds": return Math.floor(timediff / second);
		default: return undefined;
	}
}

function dateAdd(strDate, intNum) {
	if (!intNum) intNum = 0;
	arr = strDate.split("/");
	dateStr = arr[1] + "/" + arr[0] + "/" + arr[2];
	sdate = new Date(dateStr);
	sdate.setDate(sdate.getDate() + intNum * 1);
	rd = sdate.getDate() < 10 ? "0" + sdate.getDate() : sdate.getDate();
	rm = (sdate.getMonth() + 1) < 10 ? "0" + (sdate.getMonth() + 1) : (sdate.getMonth() + 1);
	return rd + "/" + rm + "/" + sdate.getFullYear();
}

function terbilang(bilangan) {

	bilangan = String(bilangan);
	var angka = new Array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
	var kata = new Array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan');
	var tingkat = new Array('', 'Ribu', 'Juta', 'Milyar', 'Triliun');

	var panjang_bilangan = bilangan.length;

	/* pengujian panjang bilangan */
	if (panjang_bilangan > 15) {
		kaLimat = "Diluar Batas";
		return kaLimat;
	}

	/* mengambil angka-angka yang ada dalam bilangan, dimasukkan ke dalam array */
	for (i = 1; i <= panjang_bilangan; i++) {
		angka[i] = bilangan.substr(-(i), 1);
	}

	i = 1;
	j = 0;
	kaLimat = "";


	/* mulai proses iterasi terhadap array angka */
	while (i <= panjang_bilangan) {

		subkaLimat = "";
		kata1 = "";
		kata2 = "";
		kata3 = "";

		/* untuk Ratusan */
		if (angka[i + 2] != "0") {
			if (angka[i + 2] == "1") {
				kata1 = "Seratus";
			} else {
				kata1 = kata[angka[i + 2]] + " Ratus";
			}
		}

		/* untuk Puluhan atau Belasan */
		if (angka[i + 1] != "0") {
			if (angka[i + 1] == "1") {
				if (angka[i] == "0") {
					kata2 = "Sepuluh";
				} else if (angka[i] == "1") {
					kata2 = "Sebelas";
				} else {
					kata2 = kata[angka[i]] + " Belas";
				}
			} else {
				kata2 = kata[angka[i + 1]] + " Puluh";
			}
		}

		/* untuk Satuan */
		if (angka[i] != "0") {
			if (angka[i + 1] != "1") {
				kata3 = kata[angka[i]];
			}
		}

		/* pengujian angka apakah tidak nol semua, lalu ditambahkan tingkat */
		if ((angka[i] != "0") || (angka[i + 1] != "0") || (angka[i + 2] != "0")) {
			subkaLimat = kata1 + " " + kata2 + " " + kata3 + " " + tingkat[j] + " ";
		}

		/* gabungkan variabe sub kaLimat (untuk Satu blok 3 angka) ke variabel kaLimat */
		kaLimat = subkaLimat + kaLimat;
		i = i + 3;
		j = j + 1;

	}

	/* mengganti Satu Ribu jadi Seribu jika diperlukan */
	if ((angka[5] == "0") && (angka[6] == "0")) {
		kaLimat = kaLimat.replace("Satu Ribu", "Seribu");
	}

	return kaLimat + "Rupiah";
}
