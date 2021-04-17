  <?php
  if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
  $fFile = "files/export/";
  $folder_upload = "../images/article/";

function det2(){
  global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;            
  $text.="
  <div class=\"pageheader\">
  <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
  ".getBread(ucwords($par[mode]." data"))."               
</div>
<div class=\"contentwrapper\">
  <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."#detail\" enctype=\"multipart/form-data\">  
    <div style=\"top:10px; right:35px; position:absolute\">
      <input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idPelatihan")."';\"/>
    </div>
    <div id=\"general\" style=\"margin-top:20px;\">         
      ".dtaPelatihan("RENCANA PELATIHAN")."         
      <fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
        <legend style=\"padding:10px; margin-left:20px;\"><h4>DETAIL BIAYA</h4></legend>
        <strong>TOTAL Rp. ".getAngka(getField("select sum(nilaiRab) from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]'"))." </strong>";
        if(isset($menuAccess[$s]["add"]))
          $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idRab")."',725,450);\" style=\"float:right; margin-top:-5px; margin-bottom:10px;  margin-right:10px;\"><span>Tambah Data</span></a>";
        $text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
        <thead>
          <tr>
            <th width=\"20\">No.</th>
            <th>Uraian</th>
            <th width=\"75\">Jumlah</th>
            <th width=\"75\">Satuan</th>
            <th width=\"100\">Nilai</th>
            <th width=\"100\">Total</th>";
            if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
            $text.="</tr>
          </thead>
          <tbody>";

            $sql="select * from plt_pelatihan_rab where idPelatihan='$par[idPelatihan]' and statusRab='t' order by idRab";
            $res=db($sql);
            $no=1;
            while($r=mysql_fetch_array($res)){    
              $text.="<tr>
              <td>$no.</td>
              <td>$r[judulRab]</td>
              <td align=\"right\">".getAngka($r[jumlahRab])."</td>
              <td>$r[satuanRab]</td>
              <td align=\"right\">".getAngka($r[hargaRab])."</td>
              <td align=\"right\">".getAngka($r[nilaiRab])."</td>";
              if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
                $text.="<td align=\"center\">";           
                if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idRab]=$r[idRab]".getPar($par,"mode,idRab")."',725,450);\"><span>Edit</span></a>";       

                if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idRab]=$r[idRab]".getPar($par,"mode,idRab")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
                $text.="</td>";
              }
              $text.="</tr>";
              $no++;
            }

            if($no == 1){
              $text.="<tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>";       
              if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
                $text.="<td>&nbsp;</td>";
              $text.="</tr>";
            }

            $text.="</tbody>
          </table>
        </fieldset>
      </div>        
    </form>";
    return $text;
  }

  function proses(){
    global $db,$s,$par,$dta_,$not,$cUsername;

    $notin = implode(",", $dta_);
    if(is_array($dta_)){
      reset($dta_);
      while(list($num, $id) = each($dta_)){
        $sql = "update pen_cmc_usulan set approval = 't' where id_cmc_usulan = '$id'";

        db($sql);
      }
    }

    $sql = "update pen_cmc_usulan set approval = '' where id_program = '$par[program]' and id_cmc_usulan NOT IN ($notin)";

    db($sql);

    echo "<script>window.location='?par[mode]=detail".getPar($par,"mode")."';</script>";
  }

  function lihat(){
    global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$arrParameter,$areaCheck;
    if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
    $text.="
    <div class=\"pageheader\">
      <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
      ".getBread()."
      <span class=\"pagedesc\">&nbsp;</span>
    </div> 
    
    <p style=\"position: absolute; right: 20px; top: 10px;\">
    </p>   

    <div id=\"contentwrapper\" class=\"contentwrapper\">
      <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
        <div id=\"pos_l\" style=\"float:left;\">
          <p>
            <td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder='Search'/></td>
            ".comboData("select id_kordinator, nama_group from kordinator_program order by id_kordinator", "id_kordinator", "nama_group", "par[filterKordinator]","All Kordinator","$par[filterKordinator]","250px")."

            <td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
          </p>
        </div>  

        <div id=\"pos_r\" style=\"float:right; margin-top:5px;\">
          ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"","", "All")."
        </div> 
      </form>
      <br clear=\"all\" />
      <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
       <thead>
        <tr>
          <th style=\"vertical-align:middle\" rowspan=\"2\" width=\"20\">No.</th>
          <th style=\"vertical-align:middle\" rowspan=\"2\" width=\"*\">Program Pelatihan</th>
          <th style=\"vertical-align:middle\" rowspan=\"2\" width=\"70\">Kode</th>
          <th style=\"vertical-align:middle\" rowspan=\"2\" rowspan=\"2\" width=\"100\">PESERTA</th>
          <th style=\"vertical-align:middle\" rowspan=\"2\" width=\"70\">Biaya</th>
          <th style=\"vertical-align:middle\" rowspan=\"2\" width=\"70\">Analisa</th>
          <th style=\"vertical-align:middle\" rowspan=\"2\" width=\"70\">Memo</th>
          <th colspan=\"2\" width=\"200\">Approval</th>

        </tr>
        <tr>
          <th width=\"100\">Diklat</th>
          <th width=\"100\">Direksi</th>
        </tr>
      </thead>
      <tbody>";
        $filter = "where t1.idPelatihan is not null";
        if(!empty($par[tahunPelatihan]))
          $filter.= " and ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";

        if(!empty($par[idKategori]))
          $filter.=" and t1.idKategori='".$par[idKategori]."'";

        if(!empty($par[filter]))    
          $filter.= " and (
        lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
        or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
        or lower(t1.namaPegawai) like '%".strtolower($par[filter])."%'
        or lower(t2.namaTrainer) like '%".strtolower($par[filter])."%'
        )";
        
        if(!empty($par[lokasi]))
          $filter .= " AND t1.idLokasi IN ($areaCheck)";
        
        $sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaTrainer else t1.namaPegawai end as namaPic from (
        select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
        ) as t1 left join dta_trainer t2 on (t1.idTrainer=t2.idTrainer) left outer join budget_pelatihan_perencanaan a on (a.id_pelatihan_perencanaan = t1.idPelatihan) $filter order by t1.idPelatihan";
        $res=db($sql);

        while($r = mysql_fetch_assoc($res)){
          $r[approval_1] = getField("SELECT approval_1 from pen_cmc_usulan where id_program = '$r[program_pelatihan]'");

          $r[approval_2] = getField("SELECT approval_2 from pen_cmc_usulan where id_program = '$r[program_pelatihan]'");

          $approval_1 = "<img src=\"styles/images/p.png\" title='Belum Diproses'>";
          if ($r[approval_1] == 't') {
            $approval_1 = "<img src=\"styles/images/t.png\" title='Setuju'>";
          }
          if ($r[approval_1] == 'f') {
            $approval_1 = "<img src=\"styles/images/f.png\" title='Tolak'>";
          }
          if ($r[approval_1] == 'p') {
            $approval_1 = "<img src=\"styles/images/o.png\" title='Pending'>";
          }

          $approval_2 = "<img src=\"styles/images/p.png\" title='Belum Diproses'>";
          if ($r[approval_2] == 't') {
            $approval_2 = "<img src=\"styles/images/t.png\" title='Setuju'>";
          }
          if ($r[approval_2] == 'f') {
            $approval_2 = "<img src=\"styles/images/f.png\" title='Tolak'>";
          }
          if ($r[approval_2] == 'p') {
            $approval_2 = "<img src=\"styles/images/o.png\" title='Pending'>";
          }

          $nilaiRab = getField("select sum(nilaiRab) from plt_pelatihan_rab where idPelatihan='$r[idPelatihan]'");

          $nilaiPegawai = getField("select sum(u_hotel + u_harian + u_transport) from budget_pelatihan where id_pelatihan='$r[program_pelatihan]' AND id_pelatihan_perencanaan = '$r[idPelatihan]'");

          $biaya = $nilaiRab + $nilaiPegawai;

          @$no++;
          $jumlah = getField("select count(id_coaching) from pen_cmc_usulan where id_program = $r[program_pelatihan]");
          $peserta = getField("select count(idPeserta) from plt_pelatihan_peserta where idPelatihan = $r[idPelatihan]");
          $r[analisa_pelatihan] = getField("SELECT analisa_pelatihan from pen_cmc_usulan where id_program = '$r[program_pelatihan]'");
          $text.="
          <tr>
            <td align=\"center\">$no</td>
            <td><a href=\"#\"
              onclick=\"openBox('popup.php?par[mode]=popup_detail&par[id_program]=$r[program_pelatihan]". getPar($par, 'mode, id_program')."', 1000, 625)\">".$r[judulPelatihan]."</a></td>
              <td align=\"center\">".(!empty($r[kodePelatihan])?"$r[kodePelatihan]":" - ")."</td>
              <td align=\"center\"><a href=\"index.php?par[mode]=detail&par[program]=$r[program_pelatihan]&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,program,idPelatihan")."\">".getAngka($peserta)."</a>
              </td>
              <td align=\"right\"><a href=\"?par[mode]=det2&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">".getAngka($biaya)."</a></td>
              <td align=\"center\">".(!empty($r[analisa_pelatihan])?"<img src='".getIcon($r[analisa_pelatihan])."' style='width:20px;' onclick=\"openBox('view.php?doc=analisa_pelatihan&par[id_program]=$r[program_pelatihan]".getPar($par,"mode, id_program")."',900,525);\">":" - ")."</td>
              <td align=\"center\">".(!empty($r[filePelatihan])?"<img src=\"".getIcon($r[filePelatihan])."\" style='width:20px;' onclick=\"openBox('view.php?doc=rencana_pelatihan&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode, idPelatihan")."',900,525);\">":" - ")."</td>
              <td align=\"center\">";
                $text .= (empty($r[approval_1]) && !isset($menuAccess[$s]['apprlv1'])) ? "$approval_1" : "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=app1&par[program]=$r[program_pelatihan]".getPar($par, 'mode,program')."',800,375);\" title=\"Approval\">$approval_1</a></td>";$text.="
              </td>
              <td align=\"center\">";
                $text .= (empty($r[approval_2]) && !isset($menuAccess[$s]['apprlv1'])) ? "$approval_2" : "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=app2&par[program]=$r[program_pelatihan]".getPar($par, 'mode,program')."',800,375);\" title=\"Approval\">$approval_2</a></td>";$text.="
              </td>
            </tr>";
          }

          $text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
          <thead>
           <th style=\"text-align:left;background-color: transparent;text;border:none;color:black;\">Legend :</th></thead>
           <td style=\"text-align:left\" colspan=\"10\">
            1. ".$arrParameter[59]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</br>
            2. ".$arrParameter[60]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</br>
            3. ".$arrParameter[61]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</br>
            4. ".$arrParameter[62]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
          </td>
          <td style=\"text-align:left\" colspan=\"10\">
           <img src=\"styles/images/p.png\" style=\"text-align:left\"> : ".$arrParameter[63]."&nbsp;&nbsp;</br>
           <img src=\"styles/images/t.png\" style=\"text-align:left\"> : ".$arrParameter[66]."</br>
           <img src=\"styles/images/o.png\" style=\"text-align:left\"> : ".$arrParameter[64]."</br>
           <img src=\"styles/images/f.png\" style=\"text-align:left\"> : ".$arrParameter[65]."</td>
         </td>
       </table>
     </tbody>
   </table>";
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

function detail(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$arrParameter;
  $sql = "SELECT * FROM ctg_program WHERE id_program = '$par[program]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  $idBiaya = getField("SELECT id_pelatihan_perencanaan FROM budget_pelatihan_perencanaan WHERE id_pelatihan_perencanaan = '$par[idPelatihan]'");

  $namaModul = getField("SELECT keterangan FROM app_site WHERE kodeSite='$r[id_modul]'");
  $namaKategori = getField("SELECT keterangan FROM app_menu WHERE kodeMenu='$r[id_kategori]'");

  if(empty($_submit) && empty($par[tahun])) $par[tahun] = date('Y');
  $text.="
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    <div class=\"simpan\" style=\"float:right; margin-top:-50px; margin-right:20px;\">";

      $text.="<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, program,pelatihan")."';\"/>";


      $text.="</div>
      ".getBread()."
      <span class=\"pagedesc\">&nbsp;</span>

    </div> 

    <p style=\"position: absolute; right: 20px; top: 10px;\">
    </p>   

    <div id=\"contentwrapper\" class=\"contentwrapper\">
     <form action=\"\" method=\"post\" id=\"form2\" class=\"stdform\" onsubmit=\"return false;\">
       <div id=\"pos_l\" style=\"float:left;\">

       </div>



       <fieldset style=\"padding:10px; border-radius: 10px; margin-bottom: 20px;\">
        <legend>Katalog Program</legend>
        <!--<p>
          <label class=\"l-input-small\">Modul</label>
          <span class=\"field\">".$namaModul."&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">Kategori</label>
          <span class=\"field\">".$namaKategori."&nbsp;</span>
        </p>-->
        <p>
          <label class=\"l-input-small\">Program</label>
          <span class=\"field\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=popup_detail&par[id_program]=$par[program]".getPar($par, "mode, id_program")."', 1000, 625)\">
            <img src=\"styles/images/icons/detail.png\" style=\"padding: 0px 2px; margin-bottom: -3px;\" />$r[program]&nbsp;</a></span>
          </p>
          <p>
            <label class=\"l-input-small\">Durasi</label>
            <span class=\"field\">$r[durasi] Hari</span>
          </p>
        </fieldset>
      </form>
      <div class=\"widgetbox\">
        <div class=\"title\" style=\"margin:0;\">
          <h3>".$arrTitle[$s]."</h3>
        </div>
      </div>
      <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\"> 

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:20px;\" id=\"dyntables\">
          <thead>
            <tr>
              <th rowspan = \"2\"width=\"10\">No.</th>
              <th rowspan = \"2\"width=\"190\">Nama</th>
              <th rowspan = \"2\"width=\"175\">Jabatan</th>
              <th rowspan = \"2\"width=\"115\">Posisi</th>
              <th rowspan = \"2\"width=\"115\">Umur</th>
              <th rowspan = \"2\"width=\"115\">Saran</th>
            </tr>
          </thead>
          <tbody>";


           $sql="select *,b.idPegawai as idp from plt_pelatihan as a
           join plt_pelatihan_peserta as b on(a.idPelatihan = b.idPelatihan)
           where a.idPelatihan = '$par[idPelatihan]'
           ";
           $res = db($sql);
           while ($r = mysql_fetch_assoc($res)) {
             $jabatanid = getField("SELECT pos_name from sdm_posisi where id_pegawai = '$r[idp]' and id = (select max(id) as last_id from sdm_posisi where id_pegawai = '$r[idp]')");
             $namaJabatan = getField("SELECT kodeMaster FROM mst_data where kodeData = '$jabatanid'");

             $no++;
             $text.= "
             <tr>
              <td align=\"right\">$no.</td>
              <td align=\"left\">".getField("SELECT name FROM emp WHERE id= '$r[idp]' ")."</td>
              <td align=\"left\">".namaData($jabatanid)."</td>
              <td align=\"left\">".$r[posisiPeserta]."</td>
              <td align=\"left\">".$r[umurPeserta]."</td>
              <td align=\"left\">".$r[saranPeserta]."</td>

            </tr> 
            ";
          }

          $text.="
        </tbody>
      </table>
    </form>
  </div>

  ";
  return $text;
}

function xls(){   
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck, $cID;

  $direktori = $fFile;
  $namaFile = "exp-".$arrTitle[$s].".xls";
  $judul = "".$arrTitle[$s]."";
  $field = array("no",  "waktu", "nama", "umur", "telp whatsapp", "email", "paket", "modal usaha", "lokasi usaha", "pesan", "status mitra");

  $sql ="select *, concat(' ',telp_wa,' ')as telp from dta_mitra where status_mitra != 1 order by created_date asc";

  $res=db($sql);
  $no = 0;
  while($r=mysql_fetch_array($res))
  {
    $no++;
    $data[] = array($no . "\t center", 
      getTanggal2($r[created_date],"d/m/Y h:i:s") . "\t center",
      $r[nama] . "\t left", 
      $r[umur]." Tahun" . "\t center",
      $r[telp] . "\t left",
      $r[email] . "\t left",
      namaData($r[paket]) . "\t left",
      getAngka($r[modal_usaha]) . "\t right",
      $r[lokasi_usaha] . "\t left",
      $r[pesan] . "\t left", 
      ($r[status_mitra]==1?"Approved":"Pending") . "\t center");
  }
  exportXLS($direktori, $namaFile, $judul, 11, $field, $data);
}


function approval_1()
{
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;
  $sql = "select * from pen_cmc_usulan where id_program ='$par[program]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  if (empty($r[id_user1])) {
    $r[id_user1] = $cUsername;
  }
  if (empty($r[tanggal_usulan1]) || $r[tanggal_usulan1] == '0000-00-00 00:00:00') {
    $r[tanggal_usulan1] = date('Y-m-d H:i:s');
  }
  list($tanggal_usulan1) = explode(' ', $r[tanggal_usulan1]);

  $pending = $r[approval_1] == 'p' ? 'checked="checked"' : '';
  $false = $r[approval_1] == 'f' ? 'checked="checked"' : '';
  $true = (empty($pending) && empty($false)) ? 'checked="checked"' : '';

  $approval_1 = 'Belum Diproses';
  if ($r[approval_1] == 't') {
    $approval_1 = 'Setuju';
  }
  if ($r[approval_1] == 'f') {
    $approval_1 = 'Tolak';
  }
  if ($r[approval_1] == 'p') {
    $approval_1 = 'Tunda';
  }

  setValidation('is_null', 'inp[keterangan_usulan1]', 'anda harus mengisi keterangan');
  $text = getValidation();

  $text .= '<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">APPROVAL USULAN PELATIHAN</h1>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form id="form" name="form" method="post" class="stdform" action="?_submit=1'.getPar($par).'" onsubmit="return validation(document.form);">
     <div id="general" class="subcontent">
      <p>
       <label class="l-input-small">Nama</label>';
       $text .= isset($menuAccess[$s]['apprlv1']) ?
       '<div class="field">
       <input type="text" id="inp[id_user1]" name="inp[id_user1]"  value="'.getField("select namaUser from app_user where username='$r[id_user1]'").'" class="mediuminput" style="width:300px;" disabled="disabled"/>
     </div>' :
     '<span class="field">
     '.getField("select namaUser from app_user where username='$r[id_user1]'").'&nbsp;
   </span>';
   $text .= '
 </p>
 <p>
   <label class="l-input-small">Tanggal</label>';
   $text .= isset($menuAccess[$s]['apprlv1']) ?
   '<div class="field">
   <input type="text" id="tanggal_usulan1" name="inp[tanggal_usulan1]" size="10" maxlength="10" value="'.getTanggal($tanggal_usulan1).'" class="vsmallinput hasDatePicker"  disabled="disabled"/>
 </div>' :
 '<span class="field">
 '.getTanggal($tanggal_usulan1, 't').'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Keterangan</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"field\">
  <textarea id=\"inp[keterangan_usulan1]\" name=\"inp[keterangan_usulan1]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keterangan_usulan1]</textarea>
</div>" :
'<span class="field">
'.nl2br($r[keterangan_usulan1]).'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Status</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"fradio\">
  <input type=\"radio\" id=\"true\" name=\"inp[approval_1]\" value=\"t\" $true /> <span class=\"sradio\">Setuju</span>
  <input type=\"radio\" id=\"pending\" name=\"inp[approval_1]\" value=\"p\" $pending /> <span class=\"sradio\">Tunda</span>
  <input type=\"radio\" id=\"false\" name=\"inp[approval_1]\" value=\"f\" $false /> <span class=\"sradio\">Tolak</span>
</div>" :
'<span class="field">
'.$approval_1.'&nbsp;
</span>';
$text .= '</p>
<p style="position:absolute;right:5px;top:-55px;">';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  '<input type="submit" class="submit radius2" name="btnSave" value="Save"/>
  <input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>' :
  '
  <br clear="all">';
  $text .= '</p>
</div>
</form>
</div>';

return $text;
}


function approval_2()
{
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;
  $sql = "select * from pen_cmc_usulan where id_program ='$par[program]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  if (empty($r[id_user2])) {
    $r[id_user2] = $cUsername;
  }
  if (empty($r[tanggal_usulan2]) || $r[tanggal_usulan2] == '0000-00-00 00:00:00') {
    $r[tanggal_usulan2] = date('Y-m-d H:i:s');
  }
  list($tanggal_usulan2) = explode(' ', $r[tanggal_usulan2]);

  $pending = $r[approval_2] == 'p' ? 'checked="checked"' : '';
  $false = $r[approval_2] == 'f' ? 'checked="checked"' : '';
  $true = (empty($pending) && empty($false)) ? 'checked="checked"' : '';

  $approval_2 = 'Belum Diproses';
  if ($r[approval_2] == 't') {
    $approval_2 = 'Setuju';
  }
  if ($r[approval_2] == 'f') {
    $approval_2 = 'Tolak';
  }
  if ($r[approval_2] == 'p') {
    $approval_2 = 'Tunda';
  }

  setValidation('is_null', 'inp[keterangan_usulan2]', 'anda harus mengisi keterangan');
  $text = getValidation();

  $text .= '<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">APPROVAL USULAN PELATIHAN</h1>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form id="form" name="form" method="post" class="stdform" action="?_submit=1'.getPar($par).'" onsubmit="return validation(document.form);">
     <div id="general" class="subcontent">
      <p>
       <label class="l-input-small">Nama</label>';
       $text .= isset($menuAccess[$s]['apprlv1']) ?
       '<div class="field">
       <input type="text" id="inp[id_user2]" name="inp[id_user2]"  value="'.getField("select namaUser from app_user where username='$r[id_user2]'").'" class="mediuminput" style="width:300px;" disabled="disabled"/>
     </div>' :
     '<span class="field">
     '.getField("select namaUser from app_user where username='$r[id_user2]'").'&nbsp;
   </span>';
   $text .= '
 </p>
 <p>
   <label class="l-input-small">Tanggal</label>';
   $text .= isset($menuAccess[$s]['apprlv1']) ?
   '<div class="field">
   <input type="text" id="tanggal_usulan2" name="inp[tanggal_usulan2]" size="10" maxlength="10" value="'.getTanggal($tanggal_usulan2).'" class="vsmallinput hasDatePicker"  disabled="disabled"/>
 </div>' :
 '<span class="field">
 '.getTanggal($tanggal_usulan2, 't').'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Keterangan</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"field\">
  <textarea id=\"inp[keterangan_usulan2]\" name=\"inp[keterangan_usulan2]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keterangan_usulan2]</textarea>
</div>" :
'<span class="field">
'.nl2br($r[keterangan_usulan]).'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Status</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"fradio\">
  <input type=\"radio\" id=\"true\" name=\"inp[approval_2]\" value=\"t\" $true /> <span class=\"sradio\">Setuju</span>
  <input type=\"radio\" id=\"pending\" name=\"inp[approval_2]\" value=\"p\" $pending /> <span class=\"sradio\">Tunda</span>
  <input type=\"radio\" id=\"false\" name=\"inp[approval_2]\" value=\"f\" $false /> <span class=\"sradio\">Tolak</span>
</div>" :
'<span class="field">
'.$approval_2.'&nbsp;
</span>';
$text .= '</p>
<p style="position:absolute;right:5px;top:-55px;">';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  '<input type="submit" class="submit radius2" name="btnSave" value="Save"/>
  <input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>' :
  '
  <br clear="all">';
  $text .= '</p>
</div>
</form>
</div>';

return $text;
}

function approval_3()
{
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;
  $sql = "select * from pen_cmc_usulan where id_program ='$par[program]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  if (empty($r[id_user3])) {
    $r[id_user3] = $cUsername;
  }
  if (empty($r[tanggal_usulan3]) || $r[tanggal_usulan3] == '0000-00-00 00:00:00') {
    $r[tanggal_usulan3] = date('Y-m-d H:i:s');
  }
  list($tanggal_usulan3) = explode(' ', $r[tanggal_usulan3]);

  $pending = $r[approval_3] == 'p' ? 'checked="checked"' : '';
  $false = $r[approval_3] == 'f' ? 'checked="checked"' : '';
  $true = (empty($pending) && empty($false)) ? 'checked="checked"' : '';

  $approval_3 = 'Belum Diproses';
  if ($r[approval_3] == 't') {
    $approval_3 = 'Setuju';
  }
  if ($r[approval_3] == 'f') {
    $approval_3 = 'Tolak';
  }
  if ($r[approval_3] == 'p') {
    $approval_3 = 'Tunda';
  }

  setValidation('is_null', 'inp[keterangan_usulan2]', 'anda harus mengisi keterangan');
  $text = getValidation();

  $text .= '<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">APPROVAL USULAN PELATIHAN</h1>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form id="form" name="form" method="post" class="stdform" action="?_submit=1'.getPar($par).'" onsubmit="return validation(document.form);">
     <div id="general" class="subcontent">
      <p>
       <label class="l-input-small">Nama</label>';
       $text .= isset($menuAccess[$s]['apprlv1']) ?
       '<div class="field">
       <input type="text" id="inp[id_user3]" name="inp[id_user3]"  value="'.getField("select namaUser from app_user where username='$r[id_user3]'").'" class="mediuminput" style="width:300px;" disabled="disabled"/>
     </div>' :
     '<span class="field">
     '.getField("select namaUser from app_user where username='$r[id_user3]'").'&nbsp;
   </span>';
   $text .= '
 </p>
 <p>
   <label class="l-input-small">Tanggal</label>';
   $text .= isset($menuAccess[$s]['apprlv1']) ?
   '<div class="field">
   <input type="text" id="tanggal_usulan3" name="inp[tanggal_usulan3]" size="10" maxlength="10" value="'.getTanggal($tanggal_usulan3).'" class="vsmallinput hasDatePicker"  disabled="disabled"/>
 </div>' :
 '<span class="field">
 '.getTanggal($tanggal_usulan3, 't').'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Keterangan</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"field\">
  <textarea id=\"inp[keterangan_usulan3]\" name=\"inp[keterangan_usulan3]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keterangan_usulan3]</textarea>
</div>" :
'<span class="field">
'.nl2br($r[keterangan_usulan3]).'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Status</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"fradio\">
  <input type=\"radio\" id=\"true\" name=\"inp[approval_3]\" value=\"t\" $true /> <span class=\"sradio\">Setuju</span>
  <input type=\"radio\" id=\"pending\" name=\"inp[approval_3]\" value=\"p\" $pending /> <span class=\"sradio\">Tunda</span>
  <input type=\"radio\" id=\"false\" name=\"inp[approval_3]\" value=\"f\" $false /> <span class=\"sradio\">Tolak</span>
</div>" :
'<span class="field">
'.$approval_3.'&nbsp;
</span>';
$text .= '</p>
<p style="position:absolute;right:5px;top:-55px;">';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  '<input type="submit" class="submit radius2" name="btnSave" value="Save"/>
  <input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>' :
  '
  <br clear="all">';
  $text .= '</p>
</div>
</form>
</div>';

return $text;
}



function approval_4()
{
  global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;
  $sql = "select * from pen_cmc_usulan where id_program ='$par[program]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  if (empty($r[id_user4])) {
    $r[id_user4] = $cUsername;
  }
  if (empty($r[tanggal_usulan4]) || $r[tanggal_usulan4] == '0000-00-00 00:00:00') {
    $r[tanggal_usulan4] = date('Y-m-d H:i:s');
  }
  list($tanggal_usulan4) = explode(' ', $r[tanggal_usulan4]);

  $pending = $r[approval_4] == 'p' ? 'checked="checked"' : '';
  $false = $r[approval_4] == 'f' ? 'checked="checked"' : '';
  $true = (empty($pending) && empty($false)) ? 'checked="checked"' : '';

  $approval_4 = 'Belum Diproses';
  if ($r[approval_4] == 't') {
    $approval_4 = 'Setuju';
  }
  if ($r[approval_4] == 'f') {
    $approval_4 = 'Tolak';
  }
  if ($r[approval_4] == 'p') {
    $approval_4 = 'Tunda';
  }

  setValidation('is_null', 'inp[keterangan_usulan4]', 'anda harus mengisi keterangan');
  $text = getValidation();

  $text .= '<div class="centercontent contentpopup">
  <div class="pageheader">
    <h1 class="pagetitle">APPROVAL USULAN PELATIHAN</h1>
  </div>
  <div id="contentwrapper" class="contentwrapper">
    <form id="form" name="form" method="post" class="stdform" action="?_submit=1'.getPar($par).'" onsubmit="return validation(document.form);">
     <div id="general" class="subcontent">
      <p>
       <label class="l-input-small">Nama</label>';
       $text .= isset($menuAccess[$s]['apprlv1']) ?
       '<div class="field">
       <input type="text" id="inp[id_user4]" name="inp[id_user4]"  value="'.getField("select namaUser from app_user where username='$r[id_user4]'").'" class="mediuminput" style="width:300px;" disabled="disabled"/>
     </div>' :
     '<span class="field">
     '.getField("select namaUser from app_user where username='$r[id_user4]'").'&nbsp;
   </span>';
   $text .= '
 </p>
 <p>
   <label class="l-input-small">Tanggal</label>';
   $text .= isset($menuAccess[$s]['apprlv1']) ?
   '<div class="field">
   <input type="text" id="tanggal_usulan4" name="inp[tanggal_usulan4]" size="10" maxlength="10" value="'.getTanggal($tanggal_usulan4).'" class="vsmallinput hasDatePicker"  disabled="disabled"/>
 </div>' :
 '<span class="field">
 '.getTanggal($tanggal_usulan4, 't').'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Keterangan</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"field\">
  <textarea id=\"inp[keterangan_usulan4]\" name=\"inp[keterangan_usulan4]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keterangan_usulan4]</textarea>
</div>" :
'<span class="field">
'.nl2br($r[keterangan_usulan4]).'&nbsp;
</span>';
$text .= '</p>
<p>
  <label class="l-input-small">Status</label>';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  "<div class=\"fradio\">
  <input type=\"radio\" id=\"true\" name=\"inp[approval_4]\" value=\"t\" $true /> <span class=\"sradio\">Setuju</span>
  <input type=\"radio\" id=\"pending\" name=\"inp[approval_4]\" value=\"p\" $pending /> <span class=\"sradio\">Tunda</span>
  <input type=\"radio\" id=\"false\" name=\"inp[approval_4]\" value=\"f\" $false /> <span class=\"sradio\">Tolak</span>
</div>" :
'<span class="field">
'.$approval_4.'&nbsp;
</span>';
$text .= '</p>
<p style="position:absolute;right:5px;top:-55px;">';
  $text .= isset($menuAccess[$s]['apprlv1']) ?
  '<input type="submit" class="submit radius2" name="btnSave" value="Save"/>
  <input type="button" class="cancel radius2" value="Cancel" onclick="closeBox();"/>' :
  '
  <br clear="all">';
  $text .= '</p>
</div>
</form>
</div>';

return $text;
}


function update()
{
  global $db,$s,$inp,$par,$detail,$cUsername;
  repField();

  $sql = "update pen_cmc_usulan set keterangan_usulan1='$inp[keterangan_usulan1]', approval_1='$inp[approval_1]', id_user1='$cUsername', tanggal_usulan1='".date('Y-m-d H:i:s')."' where id_program='$par[program]'";
  db($sql);

  echo "<script>window.parent.location='index.php?".getPar($par, 'mode,id_program')."';</script>";
}

function update2()
{
  global $db,$s,$inp,$par,$detail,$cUsername;
  repField();

  $sql = "update pen_cmc_usulan set keterangan_usulan2='$inp[keterangan_usulan2]', approval_2='$inp[approval_2]', id_user2='$cUsername', tanggal_usulan2='".date('Y-m-d H:i:s')."' where id_program='$par[program]'";
  db($sql);

  echo "<script>window.parent.location='index.php?".getPar($par, 'mode,id_program')."';</script>";
}

function update3()
{
  global $db,$s,$inp,$par,$detail,$cUsername;
  repField();

  $sql = "update pen_cmc_usulan set keterangan_usulan3='$inp[keterangan_usulan3]', approval_3='$inp[approval_3]', id_user3='$cUsername', tanggal_usulan3='".date('Y-m-d H:i:s')."' where id_program='$par[program]'";
  db($sql);

  echo "<script>window.parent.location='index.php?".getPar($par, 'mode,id_program')."';</script>";
}

function update4()
{
  global $db,$s,$inp,$par,$detail,$cUsername;
  repField();

  $sql = "update pen_cmc_usulan set keterangan_usulan4='$inp[keterangan_usulan4]', approval_4='$inp[approval_4]', id_user4='$cUsername', tanggal_usulan4='".date('Y-m-d H:i:s')."' where id_program='$par[program]'";
  db($sql);

  echo "<script>window.parent.location='index.php?".getPar($par, 'mode,id_program')."';</script>";
}

function getContent($par){
  global $s,$_submit,$menuAccess;
  switch($par[mode]){
    case "popup_detail":
    include "program_kategori_popup_detail.php";
    break;
    
    case "det2":
    $text = det2();
    break;

    case "detail":
    $text = empty($_submit) ? detail() : proses(); 
    break;
    default:
    $text = lihat();
    break;

    case 'app1':
    if (isset($menuAccess[$s]['apprlv1'])) {
      $text = empty($_submit) ? approval_1() : update();
    } else {
      $text = approval_1();
    }
    break;

    case 'app2':
    if (isset($menuAccess[$s]['apprlv1'])) {
      $text = empty($_submit) ? approval_2() : update2();
    } else {
      $text = approval_2();
    }
    break;

    case 'app3':
    if (isset($menuAccess[$s]['apprlv1'])) {
      $text = empty($_submit) ? approval_3() : update3();
    } else {
      $text = approval_3();
    }
    break;

    case 'app4':
    if (isset($menuAccess[$s]['apprlv1'])) {
      $text = empty($_submit) ? approval_4() : update4();
    } else {
      $text = approval_4();
    }
    break;



  }
  return $text;
}
?>