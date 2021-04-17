jQuery(document).ready(function () {
  jQuery("#form").validate().settings.ignore = [];
  // jQuery("#inp\\[npwp_no\\]").mask("99.999.999.9-999.999");
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
  jQuery("#famFilename").on("change", function ()
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