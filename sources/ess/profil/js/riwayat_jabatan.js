jQuery(document).ready(function () {
    jQuery("#form").validate().settings.ignore = [];
    jQuery("#inp\\[npwp_no\\]").mask("99.999.999.9-999.999");
    jQuery("#ktpFilename").on("change", function ()
    {
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader)
        return; // no file selected, or no FileReader support

      if (/^image/.test(files[0].type)) { // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
          jQuery("#ktpPreview").css("background-image", "url(" + this.result + ")");
        };
      }
    });
    jQuery("#kkFilename").on("change", function ()
    {
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader)
        return; // no file selected, or no FileReader support

      if (/^image/.test(files[0].type)) { // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
          jQuery("#kkPreview").css("background-image", "url(" + this.result + ")");
        };
      }
    });
    jQuery("#picFilename").on("change", function ()
    {
      var files = !!this.files ? this.files : [];
      if (!files.length || !window.FileReader)
        return; // no file selected, or no FileReader support

      if (/^image/.test(files[0].type)) { // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file

        reader.onloadend = function () { // set image data as background of div
          jQuery("#fotoPreview").css("background-image", "url(" + this.result + ")");
        };
      }
    });
});

function getAnakan(idInduk, idAnak, mode, getPar){
	kodeInduk = document.getElementById('inp['+idInduk+']');
	kodeAnak = document.getElementById('inp['+idAnak+']');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodeAnak.options.length-1; i>=0; i--){
				kodeAnak.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodeAnak.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodeAnak.options.add(opt);
					jQuery("#inp\\["+idAnak+"\\]").trigger("chosen:updated");
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=anakan&par[kodeInduk]="+ kodeInduk.value + getPar, true);
	xmlHttp.send(null);
	return false;
}