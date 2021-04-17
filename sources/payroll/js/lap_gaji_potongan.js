jQuery(document).ready(function(){
	jQuery('a.togglemenu').click();
});
function nextDate(getPar){
	bulanProses=document.getElementById("par[bulanProses]");
	tahunProses=document.getElementById("par[tahunProses]");
	
	bulan = bulanProses.value == 12 ? 01 : bulanProses.value * 1 + 1;	
	tahun = bulanProses.value == 12 ? tahunProses.value * 1 + 1 : tahunProses.value;
	
	bulanProses.value = bulan > 9 ? bulan : "0" + bulan;
	tahunProses.value = tahun;
	
	document.getElementById('form').submit();
}

function prevDate(getPar){
	bulanProses=document.getElementById("par[bulanProses]");
	tahunProses=document.getElementById("par[tahunProses]");
	
	bulan = bulanProses.value == 01 ? 12 : bulanProses.value * 1 - 1;	
	tahun = bulanProses.value == 01 ? tahunProses.value * 1 - 1 : tahunProses.value;	
	
	bulanProses.value = bulan > 9 ? bulan : "0" + bulan;
	tahunProses.value = tahun;
	
	document.getElementById('form').submit();
}

jQuery(document).ready(function() {
	var dTable = jQuery('#dynscroll_custom').dataTable({		
		"sScrollX": "100%",
		"sScrollY": "500px",
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[-1], ["All"]],
		"bPaginate": false,
		"bSort": false,		
		"bFilter": false,		
		"sDom": "rt<'bottom'lp><'clear'>",
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