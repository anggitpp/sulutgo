function myFunction(value) {
var checkBox = document.getElementById("inp[evaluasi_status]");
var demo = document.getElementById("fieldVendor");
  
  if (checkBox.checked == true){
    demo.style.display = "block";
  } else {
     demo.style.display = "none";
  }
}