// function cekStatus() {
//   cat = document.getElementById('inp[cat]').value;
//   leave_date = document.getElementById('inp[leave_date]').value;
//   if (cat == 532 && leave_date == '' || cat == 3195 && leave_date == '') {
//     alert("Tanggal Keluar harus diisi..");
//     return false;
//   }else{
//     return validation(document.form);
//   }
// }

jQuery(document).ready(function () {
  jQuery("#form").validate().settings.ignore = [];
  // jQuery("#inp\\[npwp_no\\]").mask("99.999.999.9-999.999");
  jQuery("#ktpFilename").on("change", function () {
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
  jQuery("#famFilename").on("change", function () {
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
  jQuery("#picFilename").on("change", function () {
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
  jQuery("#cat").bind("change", function () {
    if (jQuery(this).val() === "532") {
      alert("hehe");
      addAttributeRequired("leave_date");
      jQuery("#leave_date").rules("add", {
        required: true,
        messages: {
          required: "Field Tanggal Keluar harus diisi.."
        }
      });
    } else {
      // jQuery("#inp[leave_date]").rules("remove");
      // var stext = jQuery("#inp[leave_date]").parent().parent().children("label").html().replace('&nbsp;&nbsp;<span class="required">*)</span>', '');
      // jQuery("#inp[leave_date]").parent().parent().children("label").html(stext);
    }

  });
});

function setProses(getPar) {
  formInput = document.getElementById("formInput");
  prosesImg = document.getElementById("prosesImg");
  progresBar = document.getElementById("progresBar");

  prosesImg.style.display = "block";
  fileData = document.getElementById('fileData').files[0];

  var xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      response = xmlHttp.responseText.trim();
      // alert(response);
      if (response) {
        if (response.substring(0, 8) == "fileData") {
          fileData = response.replace("fileData", "");
          setData(fileData, getPar, 6);
          formInput.style.display = "none";
          prosesImg.style.display = "none";
          progresBar.style.display = "block";
        } else {
          prosesImg.style.display = "none";
          alert(response);
        }
      } else {
        prosesImg.style.display = "none";
        alert("file harus dalam format .xls atau .xlsx");
      }
    }
  }

  xmlHttp.open("POST", "ajax.php?par[mode]=tab" + getPar, true);
  xmlHttp.setRequestHeader("Enctype", "multipart/form-data")
  var formData = new FormData();

  formData.append("fileData", fileData);

  xmlHttp.send(formData);
}

function setData(fileData, getPar, rowData) {
  persenBar = document.getElementById("persenBar");
  progresCnt = document.getElementById("progresCnt");
  progresRes = document.getElementById("progresRes");

  var xmlHttp = getXMLHttp();
  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      response = xmlHttp.responseText.trim();
      // alert(response);
      if (response) {
        dta = response.split("\t");
        persenBar.style.width = dta[0] + "%";
        progresCnt.innerHTML = dta[1];
        //progresRes.innerHTML = progresRes.innerHTML + "<br>" + dta[2];
        setData(fileData, getPar, (rowData * 1 + 1));
      } else {
        endProses(getPar);
      }
    }
  }
  xmlHttp.open("GET", "ajax.php?par[mode]=dat&par[fileData]=" + fileData + "&par[rowData]=" + rowData + getPar, true);
  console.log("ajax.php?par[mode]=dat&par[fileData]=" + fileData + "&par[rowData]=" + rowData + getPar);
  xmlHttp.send(null);
  return false;
}

function endProses(getPar) {
  progresEnd = document.getElementById("progresEnd");
  progresRes = document.getElementById("progresRes");
  persenBar = document.getElementById("persenBar");

  var xmlHttp = getXMLHttp();
  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      response = xmlHttp.responseText.trim();
      if (response) {
        persenBar.className = "value bluebar";
        progresRes.innerHTML = response;
        progresEnd.style.display = "block";
      }
    }
  }
  xmlHttp.open("GET", "ajax.php?par[mode]=end" + getPar, true);
  xmlHttp.send(null);
  return false;
}