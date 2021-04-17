<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";
$folder_upload = "files/dokumentasi/";
function getContent($par){
  global $s,$_submit,$menuAccess;
  switch($par[mode]){

    case "lst":
    $text=lData();
    break;  

    case "detail":
    if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? detail() : tambahDok(); else $text = detail();
    break;

    case "delete_file":
    $text = delete_file();
    break;

    case "delete_foto":
    $text = delete_foto();
    break;

    case "delete":
    if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
    break;

    case "edit":
    if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
    break;

    case "add":
    if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
    break;

    default:
    $text = lihat();
    break;
  }
  return $text;
}

function tambahDok(){
  global $s, $inp, $par, $cID, $arrParam, $folder_upload;
  repField($inp);
  $lastID = getField("SELECT id FROM pen_cmc_dokumentasi ORDER BY id DESC LIMIT 1") + 1;
  if(empty($par[idf])){
    if($inp[kategori] == '1863'){
      $sql = "INSERT INTO pen_cmc_dokumentasi (id, id_cmc, kategori, file, keterangan, created_date, created_by) VALUES ('$lastID','$par[id]','$inp[kategori]','$inp[link]','$inp[keterangan]',now(),'$cID')";
    }else{
      $file = uploadFiles("$lastID", "file", "$folder_upload", "$inp[kategori] - ");
      $sql = "INSERT INTO pen_cmc_dokumentasi (id, id_cmc, kategori, file, keterangan, created_date, created_by) VALUES ('$lastID','$par[id]','$inp[kategori]','$file','$inp[keterangan]',now(),'$cID')";
    }
  }else{
    if($inp[kategori] == '1863'){
      $sql = "UPDATE pen_cmc_dokumentasi SET file = '$inp[link]', keterangan = '$inp[keterangan]' where id = '$par[idf]'";
    }else{
      $file = uploadFiles("$lastID", "file", "$folder_upload", "$inp[kategori] - ");
      $sql = "UPDATE pen_cmc_dokumentasi SET file = '$file', keterangan = '$inp[keterangan]' where id = '$par[idf]'";
    }
  }

  /*var_dump($sql);
  die();*/

  db($sql);
  echo "<script>alert('Dokumentasi berhasil disimpan');</script>";
  echo "<script>closeBox();</script>";
  echo "<script>reloadPage();</script>";
}

function lihat(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor;
  $cols = 7;  
  $text = table($cols, array($cols,($cols-1)));
  
  $year = date("Y");

  $cek_tahun = getField("SELECT DISTINCT(YEAR(tanggal)) FROM pen_cmc order by tanggal ASC");

  if(empty($cek_tahun)){
    $tahun_awal = $year;
  }else{
    $tahun_awal = $cek_tahun;
  }

  $text.="
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    ".getBread()."
    <span class=\"pagedesc\">&nbsp;</span>
  </div> 
  <div id=\"contentwrapper\" class=\"contentwrapper\">
   <form action=\"\" method=\"post\" id=\"form\" class=\"stdform\" onsubmit=\"return false;\">
     <div id=\"pos_l\" style=\"float:left;\">
       <p>					
        <input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$_GET['fSearch']."\" style=\"width:200px;\"/>
        ".comboYear("bSearch", $bSearch, "5", "","150","","$tahun_awal","$year","chosen-select")."
      </p>
    </div>

    <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">";
      if(isset($menuAccess[$s]["add"])) {
       $text.="
       <!--<a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>-->
       <a href=\"index.php?par[mode]=add".getPar($par,"mode")."\" id=\"\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>
       ";
     }
     $text.="
   </div>	
 </form>
 <br clear=\"all\" />
 <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
   <thead>
    <tr>
     <th width=\"20\">No.</th>
     <th width=\"100\">Tanggal</th>
     <th width=\"200\">Peserta</th>
     <th width=\"200\">Konselor</th>
     <th width=\"100\">Kategori</th>
     <th width=\"50\">File</th>
     <th width=\"70\">Kontrol</th>
   </tr>
 </thead>
 <tbody></tbody>
</table>
";
if($par[mode] == "xls"){			
 xls();			
 $text.="<iframe src=\"download.php?d=exp&f=exp-".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

$text.="
<script>
 jQuery(\"#btnExport\").live('click', function(e){
  e.preventDefault();
  window.location.href=\"?par[mode]=xls\"+\"".getPar($par,"mode")."\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
});
</script>
";
return $text;
}


function lData(){
  global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$folder_upload;	
  if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
    $sLimit="limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
  $kodeCounseling = getField("SELECT kodeData FROM mst_data WHERE kodeMaster = 'CMC3'");
  $sWhere = " WHERE a.id is not null and kategori = '$kodeCounseling'";
  
  if (!empty($_GET['fSearch']))
   $sWhere.= " and (				
 lower(b.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
 )";

 if (!empty($_GET['bSearch']))
   $sWhere.= " and year(a.tanggal) = $_GET[bSearch]";

 $arrOrder = array(	
   "b.name",
   "a.created_date",
   "b.name",
   "b.name"
   );

 $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

 $sql="select a.id, a.tanggal, b.name as peserta, c.name as atasan, d.namaData as kategori
 from pen_cmc as a
 join emp as b on(a.peserta = b.id)
 join emp as c on(a.atasan = c.id)
 join mst_data as d on(a.id_kategori = d.kodeData)
 $sWhere order by $orderBy $sLimit";

 $res=db($sql);
 $json = array(
   "iTotalRecords" => mysql_num_rows($res),
   "iTotalDisplayRecords" => getField("select count(a.id)
    from pen_cmc as a
    join emp as b on(a.peserta = b.id)
    join emp as c on(a.atasan = c.id)
    $sWhere"),
   "aaData" => array(),
   );

 $no=intval($_GET['iDisplayStart']);
 while($r=mysql_fetch_array($res)){
   $no++;
   $r[file] = getField("select count(id) from pen_cmc_dokumentasi where id_cmc = $r[id]");
   $data=array(
    "<div align=\"center\">$no</div>",				
    "<div align=\"center\">".getTanggal($r[tanggal])."</div>",
    "<div align=\"left\">$r[peserta]</div>",
    "<div align=\"left\">$r[atasan]</div>",
    "<div align=\"left\">$r[kategori]</div>",
    "<div align=\"center\">".getAngka($r[file])."</div>",
    "
    <div align=\"center\">
      <!--<a href='#' class='detail' title='Lihat Data' onclick=\"openBox('popup.php?par[mode]=detail&par[id]=$r[id]".getPar($par,"mode")."',800,450)\"></a>-->

      <a href='?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode")."' class='edit' title='Edit Data'></a>

      <a href='?par[mode]=delete&par[id]=$r[id]".getPar($par,"mode")."' class='delete' title='Hapus Data' onclick=\"return confirm('are you sure to delete data ?');\"></a>
    </div>
    ",
    );
   $json['aaData'][]=$data;
 }
 return json_encode($json);
}

function detail(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam, $folder_upload;
  $sql = db("select * from pen_cmc_dokumentasi where id = $par[idf]");
  $r = mysql_fetch_array($sql);
  if(empty($r[kategori])){
    $stylef = "display:none";
  }else{
    if($r[kategori] == '4033'){
      $stylef = "";
      $stylev = "display:none";
    }elseif($r[kategori] == '4034'){
      $stylef = "";
      $stylev = "display:none";
    }elseif($r[kategori] == '4035'){
      $stylef = "display:none";
      $stylev = "";
    }
  }


  $text .="
  <style>
      #inp_kategori__chosen{
    min-width:220px;
  }
</style>
<div class=\"pageheader\">
  <h1 class=\"pagetitle\">Dokumentasi</h1>
  <span class=\"pagedesc\">&nbsp;</span> 
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
  <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
    <div style=\"position:absolute; right:20px; top:14px;\">
      <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
      <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Batal\" onclick=\"closeBox();\"/>
    </div>
    <p>
      <label class=\"l-input-small\">Kategori</label>
      <div class=\"field\">
        ".comboData("SELECT kodeData, namaData from mst_data WHERE kodeCategory ='CMCD' ORDER BY namaData ASC","kodeData","namaData","inp[kategori]","Pilih Kategori","$r[kategori]","onchange=\"ifile(this.value);\"","210px","chosen-select","")."
      </div>
    </p>
    <div id=\"svideo\" style=\"$stylev\">
      <p>
        <label class=\"l-input-small\">Link youtube</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[link]\" name=\"inp[link]\"  value=\"$r[file]\" class=\"mediuminput\" style=\"width:320px;\"/>
        </div>
      </p>
    </div>

    <div id=\"sfile\" style=\"$stylef\">
      <p>
        <label class=\"l-input-small\">Foto / File</label>
        <div class=\"field\">";
          if(empty($r[file])){
            $text.="<input type=\"file\" id=\"file\" name=\"file\" class=\"mediuminput\" style=\"width: 300px; margin-top: 5px;\">";
          }else{
            $text.="
            <img src=\"$folder_upload".$r[file]."\" title=\"Download\" style=\"margin-top: 5px; width:300px;\">

            <a href=\"?par[mode]=delete_file&par[idf]=$r[id]".getPar($par, "mode")."\" onclick=\"return confirm('are you sure to delete this file?')\" >Delete</a>";
          }

          $text.="</div>
        </p>
      </div>
      <p>
        <label class=\"l-input-small\">Keterangan</label>
        <div class=\"field\">
          <textarea name=\"inp[keterangan]\" style='width:300px;'/>$r[keterangan]</textarea>
        </div>
      </p>
    </form>
  </div>
  <script>
    function ifile(nilai){
      if(nilai == '4035'){
        jQuery('#svideo').show(500);
        jQuery('#sfile').hide(500);
      }else{
        jQuery('#svideo').hide(500);
        jQuery('#sfile').show(500);
      }
    }
  </script>
  ";
  return $text;
}

function form(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$folder_upload;

  $sql = db("SELECT * FROM pen_cmc WHERE id ='$par[id]'");
  $r = mysql_fetch_array($sql);

  if(empty($r[tanggal])){
    $r[tanggal] = date("Y-m-d");
  }

  if(empty($r[isue])){
    $default = "checked";
  }
  setValidation("is_null", "inp[peserta]", "Anda belum memilih peserta");
  setValidation("is_null", "inp[atasan]", "Anda belum memilih Konselor");
  echo getValidation();
  if($par[mode] == "edit"){
    $action="onclick=\"openBox('popup.php?par[mode]=detail&par[id]=$r[id]".getPar($par,"mode")."',600,350)\"";
  }else{
    $action = "onclick=\"alert('Silahkan klik tombol simpan terlebih dahulu');\"";
  }

  $icon_edit = "<img src='styles/images/icons/edit.png' style='width:20px;'>";
    $icon_delete = "<img src='styles/images/icons/delete.png' style='width:20px;'>";
  $text .="
  <style>
        #inp_kodeRekening__chosen{
    min-width:250px;
  }
</style>
<div class=\"pageheader\">
  <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
  ".getBread(ucwords("import data"))."
  <span class=\"pagedesc\">&nbsp;</span> 
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
  <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
    <fieldset>
      <legend><h5>Informasi</h5></legend>
      <div style=\"position:absolute; right:20px; top:14px;\">
        <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
        <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Batal\" onclick=\"window.location='index.php?".getPar($par,"mode")."';\"/>

      </div>
      <p>
        <label class=\"l-input-small\">Peserta</label>
        <div class=\"field\">
         ".comboData("SELECT id, name from emp WHERE status ='535' order by name asc","id","name","inp[peserta]","Piih Peserta","$r[peserta]","","250px","chosen-select","")."
       </div>
     </p>
     <p>
      <label class=\"l-input-small\">Konselor</label>
      <div class=\"field\">
       ".comboData("SELECT id, name from emp WHERE status ='535' order by name asc","id","name","inp[atasan]","Pilih Konselor","$r[atasan]","","250px","chosen-select","")."
     </div>
   </p>
   <p>
    <label class=\"l-input-small\">Tanggal</label>
    <div class=\"field\">
      <input type=\"text\" name=\"inp[tanggal]\"  value=\"".getTanggal($r[tanggal])."\" class=\"hasDatePicker\" style=\"width:220px;\"/>
    </div>
  </p>
  <p>
    <label class=\"l-input-small\">Waktu Mulai</label>
    <div class=\"field\">
      <input type=\"text\" name=\"inp[mulai]\"  value=\"$r[mulai]\" class=\"hasTimePicker\" style=\"width:120px;\"/> s.d <input type=\"text\" name=\"inp[selesai]\"  value=\"$r[selesai]\" class=\"hasTimePicker\" style=\"width:120px;\"/>
    </div>
  </p>
  <p>
    <label class=\"l-input-small\">Judul Bahasan</label>
    <div class=\"field\">
      <input type=\"text\" name=\"inp[judul]\"  value=\"$r[judul]\" class=\"mediuminput\" style=\"width:520px;\"/>
    </div>
  </p>
  <p>
    <label class=\"l-input-small\">Kategori</label>
    <div class=\"field\">
    	".comboData("SELECT kodeData, namaData from mst_data WHERE kodeInduk='".$arrParam[$s]."' AND kodeCategory ='CMC2' ORDER BY namaData ASC","kodeData","namaData","inp[id_kategori]","Pilih Kategori","$r[id_kategori]","","210px","chosen-select","")."
    </div>
  </p>
  <p>
    <label class=\"l-input-small\">Goal <br><font size='1'>(Tujuan)</font></label>
    <div class=\"field\">
      <textarea name=\"inp[keterangan]\" style='width:520px;'/>$r[keterangan]</textarea>
    </div>
  </p>
  <br>
  <p>
    <label class=\"l-input-small\">Realty <br><font size='1'>(Kebutuhan yang terungkap)</font></label>
    <div class=\"field\">
      <textarea name=\"inp[uraian]\" style='width:520px;'/>$r[uraian]</textarea>
    </div>
  </p>
  <br>
  <p>
    <label class=\"l-input-small\">Option <br><font size='1'>(Pilihan Selesai)</font></label>
    <div class=\"field\">
      <textarea name=\"inp[kesimpulan]\" style='width:520px;'/>$r[kesimpulan]</textarea>
    </div>
  </p>
  <br>
  <p>
    <label class=\"l-input-small\">
      Will <br>
      <font size='1'>(Tindakan keputusan yang diambil)</font>
    </label>
    <div class=\"field\">
      <textarea name=\"inp[will]\" style='width:520px;'/>$r[will]</textarea>
    </div>
  </p>
  <br>
  <p>
    <label class=\"l-input-small\">Kategori</label>
    <div class=\"field\">
      <div class=\"fradio\">
        <input type=\"radio\" id=\"inp[isue]\" $default name=\"inp[isue]\" value=\"1\" style=\"width:300px;\" ".($r[isue] == '1' ? "checked" : '')."/> Teknis

        <input type=\"radio\" id=\"inp[isue]\" name=\"inp[isue]\" value=\"0\" style=\"width:300px;\" ".($r[isue] == '0' ? "checked" : '')."/> Non Teknis
      </div>
    </div>
  </p>
</fieldset>
<br>

<div class=\"widgetbox\" style=\"margin-bottom: -10px\">
  <div class=\"title\"><h3>Dokumentasi</h3></div>
  <a href=\"#\" $action style=\"float:right; position:relative; top:-45px;\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>
</div>
<br>
<ul class=\"hornav\" style=\"margin:10px 0px !important;\">
  <li class=\"current\"><a href=\"#foto\">Foto</a></li>
  <li><a href=\"#file\">File</a></li>
  <li><a href=\"#video\">Video</a></li>
</ul>

<div class=\"subcontent\" id=\"foto\" style=\"border-radius:0; display: block;\">
  <ul class=\"listfile\">";
  $sql = db("SELECT * FROM pen_cmc_dokumentasi WHERE kategori = '4033' and id_cmc = $par[id]");
    while($r = mysql_fetch_assoc($sql)){
      $text .= "
      <li style=\"border:1px solid #eee;\">
        <img src='$folder_upload/$r[file]' width='300px'>
        <div style=\"text-align:center;border-top:1px solid #eee;\">

          <a onclick=\"openBox('popup.php?par[mode]=detail&par[idf]=$r[id]".getPar($par,"mode")."',600,350)\" href=\"#\" style=\"padding: 5px 15px; text-align: center; display: inline-block; border:none; border-right:1px solid #eee; cursor: pointer;\">$icon_edit</a>

          <a href=\"index.php?par[mode]=delete_foto&par[idf]=$r[id]".getPar($par,"mode, idf")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?');\" style=\"padding: 5px 15px; text-align: center; display: inline-block; border:none;\">$icon_delete</a>
        </div>
      </li>";
    }
    $text.="
  </ul>
</div>

<div class=\"subcontent\" id=\"file\" style=\"border-radius:0; display: none;\">
  <table class=\"stdtable stdtablequick\">
    <thead>
      <tr>
        <th rowspan='2'>NO</th>
        <th colspan='2'>FILE</th>
        <th rowspan='2'>KETERANGAN</th>
        <th rowspan='2'>KONTROL</th>
      </tr>
      <tr>
        <th>D/L</th>
        <th>VIEW</th>
      </tr>
    </thead>
    <tbody>";
      $sql = db("SELECT * FROM pen_cmc_dokumentasi WHERE kategori = '4034' and id_cmc = $par[id]");
      while($r = mysql_fetch_assoc($sql)){
        @$no++;
        $text .= "
        <tr>
          <td align='center'>$no</td>
          <td align='center'>
            <a href='$folder_upload/$r[file]' target=_blank><img src='".getIcon("$folder_upload/$r[file]")."' width='20px'></a>
          </td>
          <td align='center'></td>
          <td>$r[keterangan]</td>
          <td align='center'>
            <a onclick=\"openBox('popup.php?par[mode]=detail&par[idf]=$r[id]".getPar($par,"mode")."',600,350)\" href=\"#\" style=\"padding: 5px 15px; text-align: center; display: inline-block; border:none; border-right:1px solid #eee; cursor: pointer;\">$icon_edit</a>

            <a href=\"index.php?par[mode]=delete_foto&par[idf]=$r[id]".getPar($par,"mode, idf")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?');\" style=\"padding: 5px 15px; text-align: center; display: inline-block; border:none;\">$icon_delete</a>
          </td>
        </tr>

        ";
      }
      $text.="
    </tbody>
  </table>
</div>

<div class=\"subcontent\" id=\"video\" style=\"border-radius:0; display: none;\">";
  $sql = db("SELECT * FROM pen_cmc_dokumentasi WHERE kategori = '4035' and id_cmc = $par[id]");
  while($r = mysql_fetch_assoc($sql)){
    $r[file] = str_replace("watch?v=", "embed/", "$r[file]");
    $text .= "<iframe style=\"\" width=\"300\" height=\"150\" src=\"$r[file]\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
  }
  $text.="
</div>
</form>
</div>

";

return $text;
}

function tambah(){
  global $s,$inp,$par,$cID,$arrParam,$folder_upload;
  repField($inp);

  $lastID = getField("select id from pen_cmc order by id desc limit 1") + 1;
  $kodeCounseling = getField("SELECT kodeData FROM mst_data WHERE kodeMaster = 'CMC3'");
  $sql = "INSERT INTO pen_cmc (id, kategori, atasan, peserta, tanggal, mulai, selesai, judul, id_kategori, keterangan, uraian, kesimpulan, will, isue, created_date, created_by) VALUES ('$lastID','$kodeCounseling','$inp[atasan]','$inp[peserta]','".setTanggal($inp[tanggal])."','$inp[mulai]','$inp[selesai]','$inp[judul]','$inp[id_kategori]','$inp[keterangan]','$inp[uraian]','$inp[kesimpulan]','$inp[will]','$inp[isue]',now(),'$cID')";

   /* var_dump($sql);
   die();*/

   db($sql);
   echo "<script>alert('Data berhasil disimpan');</script>";
   echo "<script>window.location.href='index.php?par[mode]=edit&par[id]=$lastID".getPar($par,"mode")."';</script>";
 }

 function ubah(){
  global $s,$inp,$par,$arrParam,$folder_upload,$cID;
  repField($inp);
  $sql = "UPDATE pen_cmc SET atasan = '$inp[atasan]', peserta = '$inp[peserta]', tanggal = '".setTanggal($inp[tanggal])."', mulai = '$inp[mulai]', selesai = '$inp[selesai]', judul = '$inp[judul]', id_kategori = '$inp[id_kategori]', keterangan = '$inp[keterangan]', uraian = '$inp[uraian]', kesimpulan = '$inp[kesimpulan]', will = '$inp[will]', isue = '$inp[isue]', updated_date = now(), updated_by = '$cID' WHERE id = '$par[id]'";

    /*var_dump($sql);
    die();*/

    db($sql);
    echo "<script>alert('Data berhasil diubah');</script>";
    echo "<script>window.location.href='index.php?par[mode]=edit".getPar($par,"mode")."';</script>";
  }

  function hapus(){
    global $s, $inp, $par, $folder_upload;
    $file = getField("SELECT file FROM pen_cmc_dokumentasi WHERE id_cmc ='$par[id]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "DELETE FROM pen_cmc WHERE id = '$par[id]'";
    db($sql);
    echo "<script>window.location.href='index.php?".getPar($par,"mode,id")."';</script>";
  }

  function delete_foto(){
    global $s, $inp, $par, $folder_upload;
    $file = getField("SELECT file FROM pen_cmc_dokumentasi WHERE id ='$par[idf]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "DELETE FROM pen_cmc_dokumentasi WHERE id = '$par[idf]'";
    db($sql);
    echo "<script>alert('Foto berhasil dihapus');window.location.href='index.php?par[mode]=edit".getPar($par,"mode,idf")."';</script>";
  }

  function delete_file(){
    global $s, $inp, $par, $folder_upload;
    $file = getField("SELECT file FROM pen_cmc_dokumentasi WHERE id ='$par[idf]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "UPDATE pen_cmc_dokumentasi set file = '' WHERE id = '$par[idf]'";
    db($sql);
    echo "<script>window.location.href='popup.php?par[mode]=detail".getPar($par,"mode")."';</script>";
  }

  ?>