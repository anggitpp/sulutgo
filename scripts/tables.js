jQuery.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
  return {
	"iStart":         oSettings._iDisplayStart,
	"iEnd":           oSettings.fnDisplayEnd(),
	"iLength":        oSettings._iDisplayLength,
	"iTotal":         oSettings.fnRecordsTotal(),
	"iFilteredTotal": oSettings.fnRecordsDisplay(),
	"iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
	"iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
  };
};

jQuery(document).ready(function(){
	_page = parseInt(document.getElementById("_page").value);
	_len = parseInt(document.getElementById("_len").value);

	jQuery('#dynscroll_v').dataTable( {
        "sScrollY": "300px",   
		"bInfo": null,
        "bPaginate": false,
		"bLengthChange": false,
		"bSort": false,		
		"bFilter": false,	
		
    } );
	
	jQuery('#dynscroll_hv').dataTable( {
        "sScrollY": "225px",   
		"sScrollX": "100%",
		"bInfo": null,
        "bPaginate": false,
		"bLengthChange": false,
		"bSort": false,		
		"bFilter": false, 
		
    } );
	
	var dTable = jQuery('#dynscroll').dataTable({		
		"sScrollX": "100%",
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": true,		
		"bFilter": false,		
		"sDom": "rt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);	
	
	var dTable = jQuery('#dyntable').dataTable({		
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": true,		
		"bFilter": false,		
		"sDom": "rt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);	
	
	var dTable = jQuery('#dyntable0').dataTable({		
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
		}
	});
	dTable.fnPageChange(_page);	
	
	var dTable = jQuery('#dynreport').dataTable({		
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": true,		
		"bFilter": false,		
		"sDom": "rt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		},
		"aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0 ] }
		],
	});
	dTable.fnPageChange(_page);	
	
	for(i=1; i<=10; i++){
		var dTable = jQuery('#dyntable' + i).dataTable({
			"sPaginationType": "full_numbers",
			"iDisplayLength": _len,
			"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
			"bSort": true,			
			"bFilter": false,		
			"sDom": "rt<'bottom'lip><'clear'>",
			"oLanguage": {
				"sEmptyTable": "&nbsp;"
			},
			"fnDrawCallback": function () {
				jQuery("#_page").val(this.fnPagingInfo().iPage);
				jQuery("#_len").val(this.fnPagingInfo().iLength);
			}
		});
		dTable.fnPageChange(_page);
	}
	
	
	var dTable = jQuery('#dynsimple').dataTable({
		"sPaginationType": "full_numbers",
		"iDisplayLength": _len,
		"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
		"bSort": true,			
		"bFilter": false,
		"bLengthChange": false,
		"bInfo": false,
		"sDom": "rt<'bottom'lip><'clear'>",
		"oLanguage": {
			"sEmptyTable": "&nbsp;"
		},
		"fnDrawCallback": function () {
			jQuery("#_page").val(this.fnPagingInfo().iPage);
			jQuery("#_len").val(this.fnPagingInfo().iLength);
		}
	});
	dTable.fnPageChange(_page);
	
	for(i=1; i<=10; i++){
		var dTable = jQuery('#dynsimple' + i).dataTable({
			"sPaginationType": "full_numbers",
			"iDisplayLength": 5,
			"aLengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
			"bSort": true,			
			"bFilter": false,
			"bLengthChange": false,
			"bInfo": false,
			"sDom": "rt<'bottom'lip><'clear'>",
			"oLanguage": {
				"sEmptyTable": "&nbsp;"
			},
			"fnDrawCallback": function () {
				jQuery("#_page").val(this.fnPagingInfo().iPage);
				jQuery("#_len").val(this.fnPagingInfo().iLength);
			}
		});
		dTable.fnPageChange(_page);
	}
	
	jQuery('input:radio').uniform();
	jQuery('input:checkbox').uniform();
});


