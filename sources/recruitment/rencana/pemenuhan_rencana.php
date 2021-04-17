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
    
    case "pilihPelamar":
    $text = pilihPelamar();
    break;

    case "addEmp":
    $text = addEmp();
    break;

    case "delEmp":
    $text = delEmp();
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

function pilihPelamar(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cID;
  $lastID = getField("SELECT id_pemenuhan from rec_pemenuhan order by id_pemenuhan desc limit 1")+1;
  $sql = db("INSERT into rec_pemenuhan (id_pemenuhan, id_usulan, id_pelamar, catatan, status, created_date, created_by) VALUES ('$lastID','$par[id]','$par[id_pelamar]','$inp[catatan]','1',now(),'$cID')");

  echo "<script>closeBox();</script>";
  echo "<script>reloadPage();</script>";
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
       ";
     }
     $text.="
   </div>	
 </form>
 <br clear=\"all\" />
 <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
   <thead>
    <tr>
     <th width=\"20\" rowspan=\"2\" style=\"vertical-align:middle;\">No.</th>
     <th width=\"100\" rowspan=\"2\" style=\"vertical-align:middle;\">Tanggal</th>
     <th width=\"100\" rowspan=\"2\" style=\"vertical-align:middle;\">Nomor</th>
     <th width=\"170\" rowspan=\"2\" style=\"vertical-align:middle;\">Pengusul</th>
     <th width=\"100\" rowspan=\"2\" style=\"vertical-align:middle;\">Job Posisi</th>
     <th width=\"200\" colspan=\"2\">JML Orang</th>
     <th width=\"70\" rowspan=\"2\" style=\"vertical-align:middle;\">Action</th>
   </tr>
   <tr>
     <th>Rencana</th>
     <th>Dipenuhi</th>
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

  $sWhere = " WHERE t1.id is not null";
  
  if (!empty($_GET['fSearch']))
   $sWhere.= " and (				
 lower(t1.id) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'			
 )";

 $arrOrder = array(	
   "t1.no",
   "created_date",
   "t1.no",
   "t1.no"
   );

 $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

 $sql="SELECT t1.id, t1.no, t1.emp_id, t1.propose_date, t1.person_needed, t1.approve, t1.status, t1.id_posisi, t1.person_needed, t2.subject from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi
 $sWhere order by $orderBy $sLimit";

 $res=db($sql);
 $json = array(
   "iTotalRecords" => mysql_num_rows($res),
   "iTotalDisplayRecords" => getField("SELECT count(*) from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi
     $sWhere"),
   "aaData" => array(),
   );

 $no=intval($_GET['iDisplayStart']);
 while($r=mysql_fetch_array($res)){
   $no++;
   $r[emp_id] = getField("select name from emp where id = '$r[emp_id]'");
   $r[id_posisi] = getField("select subject from rec_job_posisi where id_posisi = '$r[id_posisi]'");
   $r[terpenuhi] = getField("SELECT count(id_pemenuhan) from rec_pemenuhan where status = '1'");
   $data=array(
    "<div align=\"center\">$no</div>",				
    "<div align=\"center\">".getTanggal($r[propose_date])."</div>",
    "<div align=\"left\">$r[no]</div>",
    "<div align=\"left\">$r[emp_id]</div>",
    "<div align=\"left\">".$r[id_posisi]."</div>",
    "<div align=\"center\">".getAngka($r[person_needed])."</div>",
    "<div align=\"center\">".getAngka($r[terpenuhi])."</div>",
    "
    <div align=\"center\">
      <a href='?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode")."' class='edit' title='Edit Data'></a>
    </div>
    ",
    );
   $json['aaData'][]=$data;
 }
 return json_encode($json);
}

function addEmp(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam;
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
  <div style=\"position:absolute; right:20px; top:14px;\">
    <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
      <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:10px;\" id=\"dyntable\">
        <thead>
          <tr>
            <th width=\"50\">No</th>
            <th width=\"*\">Nama</th>
            <th width=\"150\">Pendidian</th>
            <th width=\"100\">Gender</th>
            <th width=\"70\">Umur</th>
            <th width=\"50\">Pilih</th>
          </tr>
        </thead>
        <tbody>";
          $sql = db("SELECT t1.id, t1.id_pelamar, t1.name, t1.gender, t1.id_posisi, t1.administrasi, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE()),' thn ') umur, t2.subject, t3.edu_type, t3.edu_dept from rec_applicant t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi left join rec_applicant_edu t3 on t1.id = t3.parent_id order by t1.name");
          while($r = mysql_fetch_assoc($sql)){
            $no++;
            $text.="
            <tr>
              <td align=\"center\">$no</td>
              <td align=\"left\">$r[name]</td>
              <td align=\"left\">$r[subject]</td>
              <td align=\"left\">".($r[gender]=='M'?"Male":"Female")."</td>
              <td align=\"center\">$r[umur]</td>
              <td align=\"center\"><a href=\"?par[mode]=pilihPelamar&par[id_pelamar]=$r[id]".getPar($par,"mode, id_pelamar")."\" class=\"edit\"></a></td>
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

function form(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$folder_upload;

  $sql = db("SELECT t1.id, t1.no, t1.emp_id, t1.propose_date, t1.person_needed, t1.approve, t1.emp_sta, t1.id_posisi, t1.person_needed, t2.subject from rec_plan t1 join rec_job_posisi t2 on t1.id_posisi = t2.id_posisi WHERE t1.id ='$par[id]'");
  $r = mysql_fetch_array($sql);
  $r[emp_id] = getField("select name from emp where id = '$r[emp_id]'");
  $r[id_posisi] = getField("select subject from rec_job_posisi where id_posisi = '$r[id_posisi]'");

  $script3 = "onclick=\"openBox('popup.php?par[mode]=addEmp".getPar($par,"mode")."',960,450)\"";

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
      <div style=\"position:absolute; right:20px; top:14px;\">
        <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Kembali\" onclick=\"window.location='index.php?".getPar($par,"mode")."';\"/>
      </div>
      <p>
        <label class=\"l-input-small\">No Usulan</label>
        <span class=\"field\">
          &nbsp;".$r[no]."
        </span>
      </p>
      <p>
        <label class=\"l-input-small\">Tanggal</label>
        <span class=\"field\">
          &nbsp;".getTanggal($r[propose_date])."
        </span>
      </p>
      <p>
        <label class=\"l-input-small\">Pengusul</label>
        <span class=\"field\">
          &nbsp;".$r[emp_id]."
        </span>
      </p>
      <p>
        <label class=\"l-input-small\">Posisi</label>
        <span class=\"field\">
          &nbsp;".$r[id_posisi]."
        </span>
      </p>
      <p>
        <label class=\"l-input-small\">Jumlah</label>
        <span class=\"field\">
          &nbsp;".getAngka($r[person_needed])." Orang
        </span>
      </p>
      <p>
        <label class=\"l-input-small\">Status Pegawai</label>
        <span class=\"field\">
          &nbsp;".namaData($r[emp_sta])."
        </span>
      </p>
    </fieldset>
    <br>
    <div>
      <h4 style='float:left';>Pelamar</h4>
      <a  style='float:right'; href='# 'class=\"btn btn1 btn_document\" $script3><span>Tambah</span></a>
    </div>
    <br style='clear:both;'>
    <hr>
  </form>
  <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:10px;\" id=\"dyntable\">
    <thead>
      <tr>
        <th width=\"20\">No</th>
        <th width=\"150\">Nama</th>
        <th width=\"150\">Pendidikan</th>
        <th width=\"70\">Gender</th>
        <th width=\"70\">Umur</th>
        <th width=\"70\">Seleksi</th>
        <th width=\"70\">Hasil</th>
        <th width=\"70\">Action</th>
      </tr>
    </thead>
    <tbody>";
      $sql = db("SELECT * from rec_pemenuhan where id_usulan = '$par[id]'");
      while ($r = mysql_fetch_assoc($sql)) {
        $no++;
        $r[nama] = getField("SELECT name from rec_applicant where id = '$r[id_pelamar]'");
        $r[pendidikan] = namaData(getField("SELECT edu_type from rec_applicant_edu where parent_id = '$r[id_pelamar]'"));

        $r[gender] = getField("SELECT gender from rec_applicant where id = '$r[id_pelamar]'");
        if($r[gender] == 'M'){
          $r[gender] = "Male";
        }else{
          $r[gender] = "Female";
        }
        $r[umur] = getField("SELECT CONCAT(TIMESTAMPDIFF(YEAR, birth_date, CURRENT_DATE()),' thn ') umur FROM rec_applicant where id = '$r[id_pelamar]'");

        $r[hasil] = "<img src=\"styles/images/f.png\">";
        $text.="
        <tr>
          <td>$no</td>
          <td>$r[nama]</td>
          <td>$r[pendidikan] $r[id_pelamar]</td>
          <td>$r[gender]</td>
          <td align=\"center\">$r[umur]</td>
          <td align=\"center\">".getAngka($r[seleksi])."</td>
          <td align=\"center\">$r[hasil]</td>
          <td align=\"center\">
            <a href=\"?par[mode]=delEmp&par[id_pelamar]=$r[id_pelamar]".getPar($par,"mode, id_pelamar")."\" class=\"delete\"></a>
          </td>
        </tr>
        ";
      }
      $text.="
    </tbody>
  </table>
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

  function delEmp(){
    global $s, $inp, $par, $folder_upload;
    $sql = "DELETE FROM rec_pemenuhan WHERE id_pelamar = '$par[id_pelamar]' and id_usulan = $par[id]";
    db($sql);
    echo "<script>window.location.href='index.php?".getPar($par,"mode,id, id_pelamar")."';</script>";
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