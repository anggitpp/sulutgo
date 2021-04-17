function unsetOption(idOption)

{

    parameter = document.getElementById('inp['+idOption+']');

    for(var i=parameter.options.length-1; i>=1; i--){

        parameter.remove(i);

        jQuery("#inp\\["+idOption+"\\]").trigger("chosen:updated");

    }

}

function changeObyek(id_sasaran, getPar) {

    unsetOption('id_sub_ruang');

    jQuery.post("ajax.php?par[mode]=getSubObyek&par[subobyek]="+id_sasaran+getPar+"").done(function(result)

    {

        data = jQuery.parseJSON(result);

        for(var i=0; i<data.length; i++)

        {

            jQuery("#inp\\[id_sub_ruang\\]").append('<option value="'+data[i].id_sasaran+'">'+data[i].sasaran+'</option>');

            jQuery("#inp\\[id_sub_ruang\\]").trigger("chosen:updated");

        }

    });

   // console.log("ajax.php?par[mode]=getSekolah&par[sekolah]="+idsekolah+getPar+"");
}