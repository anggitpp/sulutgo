jQuery(document).ready(function() {
	_page = parseInt(document.getElementById("_page").value);
	_len = parseInt(document.getElementById("_len").value);
	
	jQuery("#subtable").dataTable( {
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": false,		
		"bFilter": false,		
		"sDom": "rt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);			
		},
		
		"fnFooterCallback": function ( nFoot, aData, iStart, iEnd, aiDisplay ){
			subTotal = 0;
			for(i=iStart ; i<iEnd ; i++) {
				subTotal = subTotal * 1 + convert(aData[aiDisplay[i]][5]) * 1;
			}
			document.getElementById("subTotal").innerHTML = formatNumber(subTotal);
		}
	} );
} );	