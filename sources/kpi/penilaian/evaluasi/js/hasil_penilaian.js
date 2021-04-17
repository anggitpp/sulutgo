function unsetOption(idOption)
{
    opt = document.getElementById(idOption);
	for(var i=opt.options.length-1; i>=1; i--){
		opt.remove(i);
		jQuery("#"+idOption).trigger("chosen:updated");
	}
}

function getPosisi(idTipe, getPar)
{
    unsetOption('combo2');
    
    jQuery.post("ajax.php?par[mode]=getPosisi&par[idTipe]="+idTipe+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#combo2").append('<option value="'+data[i].idKode+'">'+data[i].subKode+'</option>');
            jQuery("#combo2").trigger("chosen:updated");
        }
    });
}

function getBulanRealisasi(idTahun, getPar)
{
    unsetOption('combo4');
    
    jQuery.post("ajax.php?par[mode]=getBulanRealisasi&par[idTahun]="+idTahun+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++)
        {
            jQuery("#combo4").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');
            jQuery("#combo4").trigger("chosen:updated");
        }
    });
}