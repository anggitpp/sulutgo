function getNomor(getPar) {
	tanggalLembur = document.getElementById("tanggalLembur");
	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			if (xmlHttp.responseText) {
				document.getElementById("inp[nomorLembur]").value = xmlHttp.responseText;
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=no&tanggalLembur=" + tanggalLembur.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function setPegawai(nikPegawai, getPar) {
	parent.document.getElementById("inp[nikPegawai]").value = nikPegawai;
	parent.getPegawai(getPar);
	closeBox();
}

function getPegawai(getPar) {
	nikPegawai = document.getElementById("inp[nikPegawai]").value;

	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			response = xmlHttp.responseText.trim();
			if (response) {
				var data = JSON.parse(response);
				document.getElementById("inp[idPegawai]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikPegawai]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaPegawai]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				document.getElementById("inp[namaJabatan]").value = data["namaJabatan"] == undefined ? "" : data["namaJabatan"];
				document.getElementById("inp[namaDivisi]").value = data["namaDivisi"] == undefined ? "" : data["namaDivisi"];

				document.getElementById("inp[idAtasan]").value = data["idAtasan"] == undefined ? "" : data["idAtasan"];
				document.getElementById("inp[nikAtasan]").value = data["nikAtasan"] == undefined ? "" : data["nikAtasan"];
				document.getElementById("inp[namaAtasan]").value = data["namaAtasan"] == undefined ? "" : data["namaAtasan"];

				if (data["idPegawai"] == null && nikPegawai.length > 0)
					alert("maaf, nik : \"" + nikPegawai + "\" belum terdaftar");
			}
		}
	}

	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikPegawai + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getAtasan(getPar) {
	nikAtasan = document.getElementById("inp[nikAtasan]").value;

	var xmlHttp = getXMLHttp();
	xmlHttp.onreadystatechange = function () {
		if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			response = xmlHttp.responseText.trim();
			if (response) {
				var data = JSON.parse(response);
				document.getElementById("inp[idAtasan]").value = data["idPegawai"] == undefined ? "" : data["idPegawai"];
				document.getElementById("inp[nikAtasan]").value = data["nikPegawai"] == undefined ? "" : data["nikPegawai"];
				document.getElementById("inp[namaAtasan]").value = data["namaPegawai"] == undefined ? "" : data["namaPegawai"];
				if (data["idPegawai"] == null && nikAtasan.length > 0)
					alert("maaf, nik : \"" + nikAtasan + "\" belum terdaftar");
			}
		}
	}

	xmlHttp.open("GET", "ajax.php?par[mode]=get&par[nikPegawai]=" + nikAtasan + getPar, true);
	xmlHttp.send(null);
	return false;
}

jQuery(document).ready(function () {
	_page = parseInt(document.getElementById("_page").value);
	_len = parseInt(document.getElementById("_len").value);

	var dTable = jQuery('#dynfilter').dataTable({
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": false,
		"bFilter": false,
		"sDom": "frt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);

	var dTable = jQuery('#dynpegawai').dataTable({
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"bInfo": null,
		"bPaginate": false,
		"bLengthChange": false,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": false,
		"bFilter": true,
		"sDom": "frt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);
});

function getAll() {
	chkAll = document.getElementById('chkAll');
	tbody = document.getElementById('chkPersonil');

	for (i = 0; i < tbody.rows.length; ++i) {
		var obj = document.getElementById('chk_' + i);
		obj.checked = chkAll.checked == true ? true : false;
		getPegawai(obj);
	}

	jQuery.uniform.update();
}

function getPegawai(dta) {
	dtaPegawai = document.getElementById('inp[dtaPegawai]');
	if (dta.checked == true)
		dtaPegawai.value = dtaPegawai.value + '\n' + dta.value;
	else
		dtaPegawai.value = dtaPegawai.value.replace('\n' + dta.value, '');
}

function setPegawai() {
	parent.document.getElementById('inp[txtPegawai]').value = document.getElementById('inp[dtaPegawai]').value;
	parent.tiny.box.hide();
	parent.fnClickAddRow()

}

function fnClickDelRow(obj) {
	if (confirm("anda yakin akan menghapus data ini ?")) {
		var row = jQuery(obj).closest('tr');
		var nRow = row[0];
		jQuery('#dynpegawai').dataTable().fnDeleteRow(nRow);
	}
}

function fnClickAddRow() {
	arr = document.getElementById('inp[txtPegawai]').value.split("\n");
	for (i = 0; i < arr.length; i++) {
		dta = arr[i].split("\t");
		if (dta[0].length > 0) {
			jQuery('#dynpegawai').dataTable().fnAddData([
				"<input type=\"hidden\" id=\"det[" + dta[0] + "]\" name=\"det[" + dta[0] + "]\" value=\"det[" + dta[0] + "]\">" + dta[1],
				dta[2],
				dta[3],
				"<div align=\"center\" style=\"cursor:pointer\" onclick=\"fnClickDelRow(this);\"><img src=\"styles/images/icons/delete.png\"></div>",
			]);
		}
	}

	document.getElementById('inp[txtPegawai]').value = "";
}

function setData() {
	tbody = document.getElementById('chkPersonil');

	result = "";
	for (i = 0; i < tbody.rows.length; ++i) {
		var obj = document.getElementById('chk_' + i);
		if (obj.checked == true) result = result + obj.value + "\n";
	}
	parent.document.getElementById("inp[nikPegawai]").value = result.substr(0, result.length - 2);
	closeBox();
}


function getPersonil() {
	pesertaAktivitas = parent.document.getElementById("inp[nikPegawai]");
	tempPeserta = document.getElementById("inp[tempPeserta]");
	tempPersonil = document.getElementById("inp[tempPersonil]");
	data = pesertaAktivitas.value.split(", ");
	arr = tempPersonil.value.split(", ");

	tempPeserta.value = pesertaAktivitas.value;

	for (i = 0; i < arr.length - 1; i++) {
		namaPersonil = document.getElementById("inp[" + arr[i] + "]");
		namaPersonil.checked = inArray(data, namaPersonil.value);
	}

}