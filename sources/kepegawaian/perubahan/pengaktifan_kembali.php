<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/export/";
$folder_upload = "files/kepegawaian/perubahan/sk/";

function getContent($par) {

  global $s,$_submit,$menuAccess;

  switch($par[mode]){
    case "simpanApp":
    $text = simpanApp();
    break;

    case "approve":
    $text = approve();
    break;

    case "delEmp":
    $text = delEmp();
    break;

    case "pilihPegawai":
    $text = pilihPegawai();
    break;

    case "addEmp":
    $text = addEmp();
    break;

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

function simpanApp(){
  global $s,$inp,$par,$cUsername,$arrParam,$folder_upload;
  repField();

  $sql = "UPDATE kepegawaian_perubahan_status SET app_id = '$inp[app_id]', app_tanggal = '".setTanggal($inp[app_tanggal])."', app_status = '$inp[app_status]', app_keterangan = '$inp[app_keterangan]' WHERE id_perubahan = '$par[id_perubahan]'";

    db($sql);

    if($inp[app_status]=='1'){
      $sql2 = db("SELECT id_pegawai from kepegawaian_perubahan_sdm where id_perubahan_status = '$par[id_perubahan]'");
      while($r2 = mysql_fetch_assoc($sql2)){
        db("UPDATE emp SET status = '$par[status_pegawai]' where id = '$r2[id_pegawai]'");
      }
    }

    echo "<script>alert('Data berhasil disimpan');</script>";
    echo "<script>parent.window.location.href='index.php?".getPar($par,"mode")."';</script>";
  }

  function approve(){
    global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$folder_upload, $cID, $cUsername;
    $sql = db("SELECT * FROM kepegawaian_perubahan_status WHERE id_perubahan = '$par[id_perubahan]'");
    $r = mysql_fetch_array($sql);
    if(empty($r[app_status])){
      $default = "checked";
    }

    if($r[app_tanggal] == "0000-00-00"){
      $r[app_tanggal] = date("Y-m-d");
    }

    if(empty($r[app_id])){
      $r[app_id] = "$cID";
    }
    if(empty($r[app_nama])){
      $r[app_nama] = "$cUsername";
    }
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
      <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?par[mode]=simpanApp".getPar($par,"mode")."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
        <div style=\"position:absolute; right:20px; top:14px;\">
          <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
          <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
        </div>
        <fieldset>
          <p>
            <label class=\"l-input-small\">Tanggal</label>
            <div class=\"field\">
              <input type=\"text\" id=\"inp[app_tanggal]\" name=\"inp[app_tanggal]\"  value=\"".getTanggal($r[app_tanggal])."\" class=\"hasDatePicker\"/>
            </div>
          </p>
          <p>
            <label class=\"l-input-small\">Nama</label>
            <div class=\"field\">
              <input type=\"hidden\" id=\"inp[app_id]\" name=\"inp[app_id]\"  value=\"$r[app_id]\" class=\"smallinput\" style=\"width:220px;\"/>
              <input type=\"text\" id=\"inp[app_nama]\" name=\"inp[app_nama]\"  value=\"$r[app_nama]\" class=\"smallinput\" style=\"width:220px;\"/>
            </div>
          </p>
          <p>
            <label class=\"l-input-small\">Status</label>
            <div class=\"field\">
              <div class=\"fradio\">
                <input type=\"radio\" id=\"inp[app_status]\" name=\"inp[app_status]\" value=\"1\" style=\"width:300px;\" ".($r[app_status] == '1' ? "checked" : '')."/> Setuju

                <input type=\"radio\" id=\"inp[app_status]\" name=\"inp[app_status]\" value=\"2\" style=\"width:300px;\" ".($r[app_status] == '2' ? "checked" : '')."/> Tidak

                <input type=\"radio\" id=\"inp[app_status]\" name=\"inp[app_status]\" value=\"3\" style=\"width:300px;\" ".($r[app_status] == '3' ? "checked" : '')." $default/> Proses
              </div>
            </div>
          </p>
          <p>
            <label class=\"l-input-small\">Keterangan</label>
            <div class=\"field\">
              <textarea rows=\"5\" style=\"width:300px;\" class=\"mediuminput\" name=\"inp[app_keterangan]\">$r[app_keterangan]</textarea>
            </div>
          </p>
        </fieldset>
      </form>
    </div>
  </div>";
  return $text;
}

function delEmp() {

  global $par;

  db("DELETE FROM `kepegawaian_perubahan_sdm` WHERE `id_pegawai` = '$par[id_pegawai]' AND id_perubahan_status = $par[id_perubahan]");
  
  echo "<script>window.location.href='index.php?".getPar($par, "mode, id_perubahan, id_pegawai")."&par[mode]=edit&par[id_perubahan]=$par[id_perubahan]';</script>";
}

function pilihPegawai(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cID;
  $lastID = getField("SELECT id_perubahan_sdm from kepegawaian_perubahan_sdm order by id_perubahan_sdm desc limit 1")+1;
  $sql = db("INSERT into kepegawaian_perubahan_sdm (id_perubahan_sdm, id_perubahan_status, id_pegawai, created_date, created_by) VALUES ('$lastID','$par[id_perubahan]','$par[id_pegawai]',now(),'$cID')");

  echo "<script>closeBox();</script>";
  echo "<script>reloadPage();</script>";
}

function addEmp() {

  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam;

  $text .="
  <style>
        #inp_kodeRekening__chosen{
    min-width:250px;
  }
</style>
<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">Pilih Pegawai</h1>
    <span class=\"pagedesc\">&nbsp;</span> 
  </div>
  <div style=\"position:absolute; right:20px; top:14px;\">
    <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
      <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:10px;\" id=\"dyntable\">
        <thead>
          <tr>
            <th width=\"50\">No</th>
            <th width=\"*\">Nama</th>
            <th width=\"150\">NPP</th>
            <th width=\"200\">Jabatan</th>
            <th width=\"50\">Pilih</th>
          </tr>
        </thead>
        <tbody>";

          $added  = arrayQuery("SELECT `id_pegawai` FROM `kepegawaian_perubahan_sdm` WHERE `id_perubahan_status` = '$par[id_perubahan]'");
          $added  = implode(",", $added);

          $filter = empty($added) ? "" : "NOT IN ($added)";
          
          $sql = db("SELECT * FROM `emp` WHERE `status` != '535' AND `id` $filter");

          while($r = mysql_fetch_assoc($sql)) {
            $no++;
            $r[jabatan] = getField("SELECT `pos_name` from `emp_phist` where `parent_id` = $r[id]");
            $text.="
            <tr>
              <td align=\"center\">$no</td>
              <td align=\"left\">$r[name]</td>
              <td align=\"left\">$r[reg_no]</td>
              <td align=\"left\">$r[jabatan]</td>
              <td align=\"center\"><a href=\"?par[mode]=pilihPegawai&par[id_pegawai]=$r[id]".getPar($par,"mode, id_pegawai")."\"><img src=\"styles/images/icons/check.png\" width=\"13\"></a></td>
            </tr>";
          }
          $text.="
        </tbody>
      </table>
    </form>
  </div>
</div>";
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

    <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">";
      if(isset($menuAccess[$s]["add"])) {
       $text.="
       <a href=\"#\" id=\"btnExport2\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>
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
     <th width=\"50\">No.</th>
     <th width=\"130\">Tanggal</th>
     <th width=\"*\">Nomor</th>
     <th width=\"130\">Status</th>
     <th width=\"70\">Jml SDM</th>
     <th width=\"70\">SK</th>
     <th width=\"70\">APP</th>
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

  $sWhere = " WHERE id_perubahan is not null and tipe = ".$arrParam[$s]."";
  
  if (!empty($_GET['fSearch']))
   $sWhere.= " and (        
 lower(nomor) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'       
 or lower(judul) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
 )";

 $arrOrder = array( 
   "tanggal",
   "created_date",
   "judul",
   "nomor"
   );

 $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

 $sql="select *
 from kepegawaian_perubahan_status
 $sWhere order by $orderBy $sLimit";

 $res=db($sql);
 $json = array(
   "iTotalRecords" => mysql_num_rows($res),
   "iTotalDisplayRecords" => getField("select count(id_perubahan) from kepegawaian_perubahan_status $sWhere"),
   "aaData" => array(),
   );

 $no=intval($_GET['iDisplayStart']);
 while($r=mysql_fetch_array($res)){
   $no++;

   if($r[app_status] == '1')
   {
    $r[app_status] = "<img src=\"styles/images/t.png\">";
  }elseif($r[app_status] == '2'){
    $r[app_status] = "<img src=\"styles/images/f.png\">";
  }else{
    $r[app_status] = "<img src=\"styles/images/p.png\">";
  }

  $r[jml_sdm] = getField("SELECT count(id_pegawai) from kepegawaian_perubahan_sdm where id_perubahan_status = $r[id_perubahan]");

  if($r[app_status]!='1'){
    $delete = "<a href='?par[mode]=delete&par[id_perubahan]=$r[id_perubahan]".getPar($par,"mode")."' class='delete' title='Hapus Data' onclick=\"return confirm('are you sure to delete data ?');\"></a>";
  }else{
    $delete = "";
  }

  $file = $r['file'] ? "<img src='".getIcon($r[file])."'  onclick=\"openBox('view.php?doc=file_sk&par[id_perubahan]=$r[id_perubahan]',900, 500);\">" : "-";

  $data=array(
    "<div align=\"center\">$no</div>",        
    "<div align=\"center\">".getTanggal($r[tanggal])."</div>",
    "<div align=\"left\">$r[nomor]</div>",
    "<div align=\"left\">".namaData($r[kategori])."</div>",
    "<div align=\"center\">".getAngka($r[jml_sdm])."</div>",
    "<div align=\"center\">$file</div>",
    "<div align=\"center\"><a href='#' onclick=\"openBox('popup.php?par[mode]=app&par[id_perubahan]=$r[id_perubahan]&par[status_pegawai]=$r[kategori]".getPar($par,"mode, id_perubahan, status_pegawai")."',900,500)\">".$r[app_status]."</a></div>",
    "
    <div align=\"center\">
      <a href='?par[mode]=edit&par[id_perubahan]=$r[id_perubahan]".getPar($par,"mode")."' class='edit' title='Edit Data'></a>

      $delete
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

  $sql = db("SELECT * FROM kepegawaian_perubahan_status WHERE id_perubahan ='$par[id_perubahan]'");
  $r = mysql_fetch_array($sql);

  if(empty($r[tanggal])){
    $r[tanggal] = date("Y-m-d");
  }

  $urut = getField("SELECT id_perubahan FROM kepegawaian_perubahan_status ORDER BY id_perubahan DESC LIMIT 1") + 1;
  $kode = "PK";
  $bulan = date("m");
  $tahun = date("Y");
  if(empty($r[nomor])){
    $r[nomor] = "$urut/$kode/$bulan/$tahun ";
  }

  if(!empty($par[id_perubahan])){
    $script3 = "onclick=\"openBox('popup.php?par[mode]=addEmp".getPar($par,"mode")."',960, 400)\"";
  }else{
    $script3 = "onclick=\"alert('Silahkan simpan terlebih dahulu info perubahan status');\"";
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
      <legend>INFO</legend>
      <div style=\"position:absolute; right:20px; top:14px;\">";
        if($r[app_status]!='1'){
          $text.="
          <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>";
        }
        $text.="
        <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Kembali\" onclick=\"window.location='index.php?".getPar($par,"mode")."';\"/>

      </div>
      <p>
        <label class=\"l-input-small\">Tanggal</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[tanggal]\" name=\"inp[tanggal]\"  value=\"".getTanggal($r[tanggal])."\" class=\"hasDatePicker\"/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Nomor</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"$r[nomor]\" class=\"smallinput\" style=\"width:220px;\"/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Judul</label>
        <div class=\"field\">
          <input type=\"text\" id=\"inp[judul]\" name=\"inp[judul]\"  value=\"$r[judul]\" class=\"smallinput\" style=\"width:420px;\"/>
        </div>
      </p>
      <p>
        <label class=\"l-input-small\">Perubahan Status</label>
        <div class=\"field\">
         ".comboData("SELECT * FROM `mst_data` WHERE `kodeCategory` ='S05' AND `kodeData` = '535'","kodeData","namaData","inp[kategori]","","$r[kategori]","","250px","chosen-select","")."
       </div>
     </p>
     <p>
      <label class=\"l-input-small\">Keterangan</label>
      <div class=\"field\">
        <textarea rows=\"5\" style=\"width:420px;\" class=\"mediuminput\" name=\"inp[keterangan]\">$r[keterangan]</textarea>
      </div>
    </p>
    <p>
      <label class=\"l-input-small\">File SK</label>
      <div class=\"field\">";
        if(empty($r[file])){
          $text.="<input type=\"file\" id=\"file\" name=\"file\" class=\"mediuminput\" style=\"width: 300px; margin-top: 5px;\">";
        }else{
          $text.="
          <img src=\"".getIcon($r[file])."\" title=\"Download\" style=\"margin-top: 5px;\">

          <a href=\"?par[mode]=delete_file".getPar($par, "mode")."\" onclick=\"return confirm('are you sure to delete this file?')\" >Delete</a>";
        }

        $text.="</div>
      </p>
    </fieldset>
    <br>
    <div>
      <h4 style='float:left; margin-top:10px;'>NAMA SDM</h4>";
      if($r[app_status]!='1'){
        $text.="
        <a  style='float:right'; href='# 'class=\"btn btn1 btn_document\" $script3><span>Tambah</span></a>";
      }
      $text.="
    </div>
    <br style='clear:both;'>
    <hr>
  </form>
  <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:10px;\" id=\"dyntable\">
    <thead>
      <tr>
        <th width=\"20\">No</th>
        <th width=\"*\">Nama</th>
        <th width=\"150\">NPP</th>
        <th width=\"150\">Jabatan</th>
        <th width=\"70\">Gender</th>
        <th width=\"100\">Masa Kerja</th>
        <th width=\"70\">Kontrol</th>
      </tr>
    </thead>
    <tbody>";
      $sql2 = db("SELECT a.`id_pegawai`, b.name, b.reg_no, c.pos_name, b.gender from kepegawaian_perubahan_sdm as a
        join emp as b on(a.id_pegawai = b.id)
        left join emp_phist as c on(a.id_pegawai = c.parent_id)
        where a.id_perubahan_status = '$par[id_perubahan]'");
      while ($r2 =mysql_fetch_assoc($sql2)) {

        $no++;
        $r2[gender] = ($r2[gender]=='M'?"Male":"Female");
        $r2[masa_kerja] = " - ";

        if ($r[app_status] != '1') {
          $delete = "<a href='?par[mode]=delEmp&par[id_pegawai]=$r2[id_pegawai]".getPar($par,"mode, id_pegawai")."' class='delete' title='Hapus Data' onclick=\"return confirm('Konfirmasi hapus data');\"></a>";
        }else{
          $delete = " - ";
        }
        
        $text.="
        <tr>
          <td>$no</td>
          <td>$r2[name]</td>
          <td>$r2[reg_no]</td>
          <td>$r2[pos_name]</td>
          <td align='center'>$r2[gender]</td>
          <td align='center'>$r2[masa_kerja]</td>
          <td align='center'>
            $delete
          </td>
        </tr>";

      }
      $text.="
    </tbody>
  </table>
</div>

";

return $text;
}

function tambah(){
  global $s,$inp,$par,$cID,$arrParam,$folder_upload;
  repField($inp);
  $lastID = getField("SELECT id_perubahan FROM kepegawaian_perubahan_status ORDER BY id_perubahan DESC LIMIT 1") + 1;

  $file = uploadFiles("$lastID", "file", "$folder_upload", "SK");

  $sql = "INSERT INTO kepegawaian_perubahan_status (id_perubahan, tipe, tanggal, nomor, judul, kategori, keterangan, file, created_date, created_by) VALUES ('$lastID','$arrParam[$s]','".setTanggal($inp[tanggal])."','$inp[nomor]','$inp[judul]','$inp[kategori]','$inp[keterangan]','$file',now(),'$cID')";

    /*var_dump($sql);
    die();*/
    
    db($sql);
    echo "<script>alert('Data berhasil disimpan');</script>";
    echo "<script>window.location='?".getPar($par, "mode")."';</script>"; 
  }

  function ubah() {

    global $s,$inp, $par, $arrParam, $folder_upload, $cID;

    repField($inp);

    $file = uploadFiles("$par[id_perubahan]", "file", "$folder_upload", $arrParam[$s]);

    $sql_file = $file ? ", `file` = '$file'" : "";
    $sql = "UPDATE `kepegawaian_perubahan_status` SET `tanggal` = '".setTanggal($inp[tanggal])."', `nomor` = '$inp[nomor]', `judul` = '$inp[judul]', `kategori` = '$inp[kategori]', `keterangan` = '$inp[keterangan]' $sql_file, `updated_date` = NOW(), `updated_by` = '$cID' WHERE `id_perubahan` = '$par[id_perubahan]'";

    db($sql);
    echo "<script>alert('Data berhasil diubah');</script>";
    echo "<script>window.location='?".getPar($par, "mode")."';</script>";
  }

  function hapus(){
    global $s, $inp, $par, $folder_upload;
    $file = getField("SELECT file FROM kepegawaian_perubahan_status WHERE id_perubahan ='$par[id_perubahan]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "DELETE FROM kepegawaian_perubahan_status WHERE id_perubahan = '$par[id_perubahan]'";
    db($sql);
    echo "<script>window.location.href='index.php?".getPar($par,"mode,id_perubahan")."';</script>";
  }

  function delete_file() {
    global $s, $inp, $par, $folder_upload, $cUsername;
    $file = getField("SELECT file FROM kepegawaian_perubahan_status WHERE id_perubahan ='$par[id_perubahan]'");
    if (file_exists($folder_upload . $file) and $file != "")
      unlink($folder_upload . $file);

    $sql = "UPDATE kepegawaian_perubahan_status SET file='' WHERE id_perubahan='$par[id_perubahan]'";
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