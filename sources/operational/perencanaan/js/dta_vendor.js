function chk(getPar){
	nomorVendor=document.getElementById("inp[nomorVendor]").value;		
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			if(xmlHttp.responseText){					
				alert(xmlHttp.responseText);										
			}else{
				if(validation(document.form)){
					document.getElementById("form").submit();
				}
			}
		}
	}
	
	xmlHttp.open("GET", "ajax.php?par[mode]=cek&inp[nomorVendor]=" + nomorVendor + getPar, true);
	xmlHttp.send(null);
	return false;
}

function update(getPar){
	kodePropinsi = document.getElementById('inp[kodePropinsi]').value;
	kodeKota = document.getElementById('inp[kodeKota]').value;
	nomorVendor = document.getElementById('inp[nomorVendor]').value;
	namaVendor = document.getElementById('inp[namaVendor]').value;
	aliasVendor = document.getElementById('inp[aliasVendor]').value;
	alamatVendor = document.getElementById('inp[alamatVendor]').value;
	teleponVendor = document.getElementById('inp[teleponVendor]').value;
	faxVendor = document.getElementById('inp[faxVendor]').value;
	emailVendor = document.getElementById('inp[emailVendor]').value;
	webVendor = document.getElementById('inp[webVendor]').value;		
	logoVendor = document.getElementById('logoVendor').files[0];	
	
	statusVendor = 'p';
	if(document.getElementById('true').checked == true) statusVendor = 't';
	if(document.getElementById('false').checked == true) statusVendor = 'f';
	
	pendirianInfo = document.getElementById('inp[pendirianInfo]').value;
	pendirianInfo_tanggal = document.getElementById('pendirianInfo_tanggal').value;	
	izinInfo = document.getElementById('inp[izinInfo]').value;
	izinInfo_tanggal = document.getElementById('izinInfo_tanggal').value;
	peringkatInfo = document.getElementById('inp[peringkatInfo]').value;
	akreditasiInfo = document.getElementById('inp[akreditasiInfo]').value;
	dikmenInfo = document.getElementById('inp[dikmenInfo]').value;
	
	siupIdentity = document.getElementById('inp[siupIdentity]').value;
	siupIdentity_file = document.getElementById('siupIdentity_file').files[0];
	tdpIdentity = document.getElementById('inp[tdpIdentity]').value;
	tdpIdentity_file = document.getElementById('tdpIdentity_file').files[0];
	idIdentity = document.getElementById('inp[idIdentity]').value;
	idIdentity_file = document.getElementById('idIdentity_file').files[0];
	npwpIdentity = document.getElementById('inp[npwpIdentity]').value;
	npwpIdentity_file = document.getElementById('npwpIdentity_file').files[0];
	alamatIdentity = document.getElementById('inp[alamatIdentity]').value;		
	
	var xmlHttp=new XMLHttpRequest();
    xmlHttp.onreadystatechange=function(){
        if (xmlHttp.readyState==4 && xmlHttp.status==200){
        	if(xmlHttp.responseText) alert(xmlHttp.responseText);
        }
    }
	
	xmlHttp.open("POST","ajax.php?par[mode]=update" + getPar,true);
    xmlHttp.setRequestHeader("Enctype", "multipart/form-data")
    var formData = new FormData();
	
	formData.append("inp[kodePropinsi]", kodePropinsi);	
	formData.append("inp[kodeKota]", kodeKota);
	formData.append("inp[nomorVendor]", nomorVendor);
	formData.append("inp[namaVendor]", namaVendor);
	formData.append("inp[aliasVendor]", aliasVendor);
	formData.append("inp[alamatVendor]", alamatVendor);	
	formData.append("inp[teleponVendor]", teleponVendor);
	formData.append("inp[faxVendor]", faxVendor);
	formData.append("inp[emailVendor]", emailVendor);
	formData.append("inp[webVendor]", webVendor);    
	formData.append("logoVendor", logoVendor);
	formData.append("inp[statusVendor]", statusVendor);
		
	formData.append("inp[pendirianInfo]", pendirianInfo);
	formData.append("inp[pendirianInfo_tanggal]", pendirianInfo_tanggal);	
	formData.append("inp[izinInfo]", izinInfo);
	formData.append("inp[izinInfo_tanggal]", izinInfo_tanggal);
	formData.append("inp[peringkatInfo]", peringkatInfo);
	formData.append("inp[akreditasiInfo]", akreditasiInfo);
	formData.append("inp[dikmenInfo]", dikmenInfo);
	
	formData.append("inp[siupIdentity]", siupIdentity);
	formData.append("siupIdentity_file", siupIdentity_file);
	formData.append("inp[tdpIdentity]", tdpIdentity);
	formData.append("tdpIdentity_file", tdpIdentity_file);
	formData.append("inp[idIdentity]", idIdentity);
	formData.append("idIdentity_file", idIdentity_file);
	formData.append("inp[npwpIdentity]", npwpIdentity);
	formData.append("npwpIdentity_file", npwpIdentity_file);
	formData.append("inp[alamatIdentity]", alamatIdentity);	
	
    xmlHttp.send(formData);		
	
}

function getKota(getPar){
	kodePropinsi = document.getElementById('inp[kodePropinsi]');
	kodeKota = document.getElementById('inp[kodeKota]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeKota.options.length-1; i>=0; i--){
				kodeKota.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodeKota.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodeKota.options.add(opt);
				}
				jQuery(".chosen-select").trigger("chosen:updated");
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=kta&par[kodePropinsi]="+ kodePropinsi.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

// google maps
var geocoder;
var map;
var marker;

function initialize() {
	var lat = document.getElementById('inp[latitudeAddress]').value;
	var lng = document.getElementById('inp[longitudeAddress]').value;

	geocoder = new google.maps.Geocoder();
	var latLng = new google.maps.LatLng(lat, lng);
	var myMapParams = { zoom: 16, center: latLng, mapTypeId: google.maps.MapTypeId.ROADMAP };
	map = new google.maps.Map(document.getElementById('mapCanvas'), myMapParams);
	var myMarkerParams = { position: latLng, map: map, draggable: true };
	marker = new google.maps.Marker(myMarkerParams);

	updateMarkerPosition(latLng);
	geocodePosition(latLng);

	google.maps.event.addListener(marker, 'dragstart', 
	function() {
		updateMarkerAddress('Dragging...,Dragging...');
	});

	google.maps.event.addListener(marker, 'drag', 
	function() {              
		updateMarkerPosition(marker.getPosition());
	});

	google.maps.event.addListener(marker, 'dragend', 
	function() {                
		geocodePosition(marker.getPosition());
	});
}

function setGeocode(getPar) {
	var kodeKota = document.getElementById('inp[kodeKota]').value;
	var xmlHttp = getXMLHttp();	
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){									
			if(xmlHttp.responseText){
				var alamatAddress = document.getElementById('inp[alamatAddress]').value;
				var address = alamatAddress.concat(',', xmlHttp.responseText, ',', 'ID');
				geocoder.geocode({ 'address': address },
				function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						marker.setMap(null);
						map.setCenter(results[0].geometry.location);
						var myMarkerParams = { position: results[0].geometry.location, map: map, draggable: true };
						marker = new google.maps.Marker(myMarkerParams);
						
						updateMarkerPosition(results[0].geometry.location);
						geocodePosition(results[0].geometry.location);
						
						google.maps.event.addListener(marker, 'dragstart',
						function() {
							updateMarkerAddress('Dragging...,Dragging...');
						});

						google.maps.event.addListener(marker, 'drag',
						function() {
							updateMarkerPosition(marker.getPosition());
						});

						google.maps.event.addListener(marker, 'dragend',
						function() {
							geocodePosition(marker.getPosition());
						});
					} else {
						//alert('error ' + status);
					}
				});
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=geo&par[kodeKota]="+ kodeKota + getPar, true);
	xmlHttp.send(null);
	return false;
}

function geocodePosition(pos) {
	geocoder.geocode({ latLng: pos },
	function(results) {
		if (results && results.length > 0) {
			updateMarkerAddress(results[0].formatted_address);
		} else {
			updateMarkerAddress('-,-');
		}
	});
}        

function updateMarkerPosition(latLng) {
	document.getElementById('inp[latitudeAddress]').value = latLng.lat();
	document.getElementById('inp[longitudeAddress]').value = latLng.lng();
}

function updateMarkerAddress(str) {
	var arrStr = str.split(',');
	document.getElementById('inp[alamatAddress]').value = arrStr[0];
}