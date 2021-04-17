function unsetOption(idOption)

{

    parameter = document.getElementById('inp['+idOption+']');

    for(var i=parameter.options.length-1; i>=1; i--){

        parameter.remove(i);

        jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");

    }

}

function unsetOption2(idOption)

{

    parameter = document.getElementById(idOption);

    for(var i=parameter.options.length-1; i>=1; i--){

        parameter.remove(i);

        jQuery(idOption).trigger("chosen:updated");

    }

}

function changeObyek(kodeData, getPar) {

    unsetOption('subyek');

    jQuery.post("ajax.php?par[mode]=getSubObyek&par[subobyek]="+kodeData+getPar+"").done(function(result)

    {

        data = jQuery.parseJSON(result);

        for(var i=0; i<data.length; i++)

        {

            jQuery("#inp\\[subyek\\]").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');

            jQuery("#inp\\[subyek\\]").trigger("chosen:updated");

        }

    });

   // console.log("ajax.php?par[mode]=getSekolah&par[sekolah]="+idsekolah+getPar+"");
}
function changeObyek2(kodeData, getPar) {

    unsetOption('mSearch');

    jQuery.post("ajax.php?par[mode]=getSubObyek&par[subobyek]="+kodeData+getPar+"").done(function(result)

    {

        data = jQuery.parseJSON(result);

        for(var i=0; i<data.length; i++)

        {

            jQuery("#mSearch").append('<option value="'+data[i].kodeData+'">'+data[i].namaData+'</option>');

            jQuery("#mSearch").trigger("chosen:updated");

        }

    });

   // console.log("ajax.php?par[mode]=getSekolah&par[sekolah]="+idsekolah+getPar+"");
}