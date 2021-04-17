function getSub(getPar){
  kategoriModul = document.getElementById('par[kategoriModul]').value;
  wew = document.getElementById('par[wew]');

  var xmlHttp = getXMLHttp();   
  xmlHttp.onreadystatechange = function(){  
    if(xmlHttp.readyState == 4 && xmlHttp.status==200){     
      for(var i=wew.options.length-1; i>=0; i--){
        wew.remove(i);
      }
      if(xmlHttp.responseText){
        var arr = xmlHttp.responseText.split("\n");           
        var opt = document.createElement("OPTION");
        opt.value = "";   
        opt.text = "";
        wew.options.add(opt);
        for(var i=0; i<arr.length; i++){              
          var opt = document.createElement("OPTION");
          var val = arr[i].split("\t");
          opt.value = val[0];    
          opt.text = val[1];
          if(opt.value) wew.options.add(opt);
          jQuery("#par\\[wew\\]").trigger("chosen:updated");
        }
      }
    }
  }
  xmlHttp.open("GET", "ajax.php?par[mode]=submod&par[kategoriModul]="+ kategoriModul + getPar, true);
  xmlHttp.send(null);
  return false;
}