jQuery(document).ready(function() {
	jQuery("#checkAll").change(function(){
		jQuery("#dtPeserta tbody input[type='checkbox']").prop("checked", jQuery(this).prop("checked"));
		jQuery.uniform.update();
	});
});