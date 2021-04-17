<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";
$folder_upload = "../images/article/";
function getContent($par){
  global $s,$_submit,$menuAccess;
  switch($par[mode]){

    case "lst":
    $text=lData();
    break;  

    case "detail":
    $text = detail();
    break;

    case "delete_file":
    $text = delete_file();
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

function lihat(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor;
  $cols = 8;  
  $text = table($cols, array($cols,($cols-1),($cols-2),($cols-3),($cols-4),($cols-5),($cols-6)));
  $text.="
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    ".getBread()."
    <span class=\"pagedesc\">&nbsp;</span>
  </div> 

  <p style=\"position: absolute; right: 20px; top: 10px;\">

  </p>   

  <div id=\"contentwrapper\" class=\"contentwrapper\">
   <form action=\"\" method=\"post\" id=\"form\" class=\"stdform\" onsubmit=\"return false;\">
     <div id=\"pos_l\" style=\"float:left;\">
       <p>					
        <input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$_GET['fSearch']."\" style=\"width:200px;\"/>
      </p>
    </div>

    <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">
       <a href=\"#\" id=\"btnExport2\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>
   </div> 
 </form>
 <br clear=\"all\" />
 <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
   <thead>
    <tr>
     <th width=\"50\">No.</th>
     <th width=\"*\">Nama</th>
     <th width=\"150\">NPP</th>
     <th width=\"150\">Jabatan</th>
     <th width=\"70\">Gender</th>
     <th width=\"70\">Umur</th>
     <th width=\"100\">Masa Kerja</th>
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
  global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam;	
  if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
    $sLimit="limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

  $sWhere = " WHERE `id` IS NOT NULL AND `status` = '536'";
  
  if (!empty($_GET['fSearch']))
   $sWhere.= " and (				
 lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				
 or lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
 )";

 $arrOrder = array(	
   "name",
   "name",
   "name",
   "name"
   );

 $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

 $res = db("SELECT * FROM `emp` $sWhere ORDER BY $orderBy $sLimit");

 $json = array(
   "iTotalRecords" => mysql_num_rows($res),
   "iTotalDisplayRecords" => getField("SELECT COUNT(`id`) FROM `emp` $sWhere"),
   "aaData" => array(),
   );

 $no=intval($_GET['iDisplayStart']);
 while($r=mysql_fetch_array($res)) {

  $jabatan = getField("SELECT `pos_name` FROM `emp_phist` WHERE `parent_id` = '$r[id]' AND `status` = '1'");

  $now    = new DateTime(date('Y-m-d'));

  $born   = new DateTime($r['birth_date']);
  $born   = $now->diff($born)->y;

  $join   = new DateTime($r['join_date']);
  $join   = $now->diff($join)->y;

   $no++;

   $data=array(
    "<div align=\"center\">$no</div>",
    "<div align=\"left\">".$r[name]."</div>",
    "<div align=\"left\">$r[reg_no]</div>",
    "<div align=\"left\">$jabatan</div>",
    "<div align=\"center\">".$r[gender]."</div>",
    "<div align=\"center\">$born tahun</div>",
    "<div align=\"center\">$join tahun</div>",
    "
    <div align=\"center\">
      <a href='?par[mode]=detail&par[id]=$r[id]".getPar($par,"mode")."' class='edit' title='Edit Data'></a>
    </div>
    ",
    );
   $json['aaData'][]=$data;
 }
 return json_encode($json);
}

function detail(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam;
  $sql = db("SELECT * FROM data_artikel WHERE id = '$par[id]'");
  $r = mysql_fetch_array($sql);
  $text .="
  <style>
        #inp_kodeRekening__chosen{
    min-width:250px;
  }
</style>
<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    ".getBread(ucwords("import data"))."
    <span class=\"pagedesc\">&nbsp;</span> 
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
      <fieldset>
        <legend>Detail Data</legend>
        <p>
          <label class=\"l-input-small\">Date Post</label>
          <span class=\"field\">
            &nbsp;".getTanggal2($r[created_date],"d/m/Y")."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Author</label>
          <span class=\"field\">
            &nbsp;".$r[author]."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Title</label>
          <span class=\"field\">
            &nbsp;".$r[title]."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Detail</label>
          <span class=\"field\">
            &nbsp;".$r[description]."
          </span>
        </p>
      </fieldset>
    </form>
  </div>
</div>";
return $text;
}

function form(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$folder_upload;

  $sql = db("SELECT * FROM data_artikel WHERE id ='$par[id]'");
  $r = mysql_fetch_array($sql);

  if(empty($r[status])){
    $default = "checked";
  }

  if(empty($r[created_date])){
    $r[created_date] = date("d/m/Y");
  }
  setValidation("is_null", "inp[tanggal]", "Anda belum mengisi tanggal");
  setValidation("is_null", "inp[author]", "Anda belum mengisi nama author");
  setValidation("is_null", "inp[title]", "Anda belum mengisi title");
  setValidation("is_null", "inp[detail]", "Anda belum mengisi detail");
  echo getValidation();
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
      <legend>FORM ARTIKEL</legend>
      <div style=\"position:absolute; right:20px; top:14px;\">
        <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
        <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Batal\" onclick=\"window.location='index.php?".getPar($par,"mode")."';\"/>

      </div>
      <p>
        <label class=\"l-input-small\">Date Post</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[author]\" name=\"inp[author]\"  value=\"".getTanggal2($r[created_date],"d/m/Y")."\" class=\"smallinput\" style=\"width:220px;\" disabled/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Author</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[author]\" name=\"inp[author]\"  value=\"$r[author]\" class=\"smallinput\" style=\"width:220px;\" autofocus/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Title</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[title]\" name=\"inp[title]\"  value=\"$r[title]\" class=\"smallinput\" style=\"width:620px;\"/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Foto Fitur</label>
        <div class=\"field\">";
          if(empty($r[image])){
            $text.="<input type=\"file\" id=\"file\" name=\"file\" class=\"mediuminput\" style=\"width: 300px; margin-top: 5px;\">";
          }else{
            $text.="
            <img src=\"$folder_upload".$r[image]."\" title=\"Download\" style=\"margin-top: 5px; width:300px;\">

            <a href=\"?par[mode]=delete_file".getPar($par, "mode")."\" onclick=\"return confirm('are you sure to delete this file?')\" >Delete</a>";
          }

          $text.="</div>
        </p>
        <p>
          <div>
            <textarea rows=\"5\" style=\"width:300px;\" class=\"tinymce\" name=\"inp[description]\">$r[description]</textarea> 
          </div>  
        </p>
        <p>
          <label class=\"l-input-small\">Status</label>
          <div class=\"field\">
            <div class=\"fradio\">
              <input type=\"radio\" id=\"inp[status]\" $default name=\"inp[status]\" value=\"1\" style=\"width:300px;\" ".($r[status] == '1' ? "checked" : '')."/> Aktif

              <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"0\" style=\"width:300px;\" ".($r[status] == '0' ? "checked" : '')."/> Tidak Aktif
            </div>
          </div>
        </p>
      </fieldset>
    </form>
  </div>

  ";

  return $text;
}

function tambah(){
  global $s,$inp,$par,$cUsername,$arrParam,$folder_upload;
  repField($inp);
  $lastID = getField("SELECT id FROM data_artikel ORDER BY id DESC LIMIT 1") + 1;
  $file = uploadFiles("$lastID", "file", "$folder_upload", "".$arrParam[$s]."");

  $sql = "INSERT INTO data_artikel (id, category, author, title, description, status, created_date, created_by, image) VALUES ('$lastID','".$arrParam[$s]."','$inp[author]','$inp[title]','$inp[description]','$inp[status]',now(),'$cUsername','$file')";

    /*var_dump($sql);
    die();*/
    
    db($sql);
    echo "<script>alert('Data berhasil disimpan');closeBox();</script>";
    echo "<script>reloadPage();</script>";
  }

  function ubah(){
    global $s,$inp,$par,$arrParam,$folder_upload;
    repField($inp);
    $cek_image = getField("SELECT image FROM data_artikel WHERE id = '$par[id]'");
    if(!empty($cek_image)){
      $sql = "UPDATE data_artikel SET author = '$inp[author]', title = '$inp[title]', description = '$inp[description]', status = '$inp[status]', updated_date = now(), updated_by = '$cUsername' WHERE id = '$par[id]'";
    }else{
      $file = uploadFiles("$par[id]", "file", "$folder_upload", "".$arrParam[$s]."");
      $sql = "UPDATE data_artikel SET author = '$inp[author]', title = '$inp[title]', description = '$inp[description]', status = '$inp[status]', updated_date = now(), updated_by = '$cUsername', image = '$file' WHERE id = '$par[id]'";
    }
    /*var_dump($sql);
    die();*/

    db($sql);
    echo "<script>alert('Data berhasil diubah');</script>";
    echo "<script>closeBox();reloadPage();</script>";
  }

  function hapus(){
    global $s, $inp, $par, $folder_upload;
    $file = getField("SELECT image FROM data_artikel WHERE id ='$par[id]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "DELETE FROM data_artikel WHERE id = '$par[id]'";
    db($sql);
    echo "<script>window.location.href='index.php?".getPar($par,"mode,id")."';</script>";
  }

  function delete_file() {
    global $s, $inp, $par, $folder_upload, $cUsername;
    $file = getField("SELECT image FROM data_artikel WHERE id ='$par[id]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "UPDATE data_artikel SET image='' WHERE id='$par[id]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>"; 
  }

  function xls(){		
    global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck, $cID;

   $direktori = $fFile;
   $namaFile = "exp-".$arrTitle[$s].".xls";
   $judul = "".$arrTitle[$s]."";
   $field = array("no",  "author", "title"=>array("author","waktu"), "status", "waktu");

   $variabel = getField("select religion from emp where id = $cID");
   $sql ="select namaMenu from app_menu where kodeInduk = 1261 order by urutanMenu";

   $res=db($sql);
   $no = 0;
   while($r=mysql_fetch_array($res))
   {
    $no++;
    $tgl_proses = getField("select tanggal from nonpayroll_proses where variabel = '$variabel' and tahun = ".$par[tahun]." and kode_kategori = '".strtolower($r[namaMenu])."'");

    $id_nonpayroll = getField("select id_nonpayroll from nonpayroll_proses where variabel = '$variabel' and tahun = ".$par[tahun]." and kode_kategori = '".strtolower($r[namaMenu])."'");

    $nilai = getField("select nilai_total from nonpayroll_hasil where id_nonpayroll = $id_nonpayroll and id_pegawai = $cID");
    $data[] = array($no . "\t center", 
      $r[author] . "\t left", 
      $r[author] . "\t left",
      $r[created_date] . "\t center",
      $r[status] . "\t center", 
      $r[created_date] . "\t center");
  }
  exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}
?>