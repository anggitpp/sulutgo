<?php
session_start();
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/export/";
$fKTP = "files/emp/ktp/";
$fPIC = "files/emp/pic/";
$fKK = "files/emp/kk/";

$id = $cID;

if(!empty($_GET['empid'])){
    $cID = $_GET['empid'];
}else{
    $cID = $_SESSION["curr_emp_id"];
}
$par[id] = $cID;
$par[id] = empty($par[id]) ? $id : $par[id];
$_SESSION["curr_emp_id"] = $par[id];

// echo $_SESSION["curr_emp_id"];

// echo $par[id];

function anakan() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]'  order by namaData");
    return implode("\n", $data);
}
function divisi() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    where t2.kodeCategory='X04' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function departemen() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    where t3.kodeCategory='X04' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function group() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk where t3.kodeCategory='X04' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function line() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
    JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk
    where t5.kodeCategory='X04' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function kotaDom() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]'  order by namaData");
    return implode("\n", $data);
}

function uploadKTP($id){
    global $db,$s,$inp,$par,$fKTP;		
    $fileUpload = $_FILES["ktpFilename"]["tmp_name"];
    $fileUpload_name = $_FILES["ktpFilename"]["name"];
    if(($fileUpload!="") and ($fileUpload!="none")){	
        fileUpload($fileUpload,$fileUpload_name,$fKTP);			
        $ktp_filename = "ktp-".$id.".".getExtension($fileUpload_name);
        fileRename($fKTP, $fileUpload_name, $ktp_filename);			
    }
    if(empty($ktp_filename)) $ktp_filename = getField("select ktp_filename from emp where id='$id'");
    
    return $ktp_filename;
}

function uploadPIC($id){
    global $db,$s,$inp,$par,$fPIC;		
    $fileUpload = $_FILES["picFilename"]["tmp_name"];
    $fileUpload_name = $_FILES["picFilename"]["name"];
    if(($fileUpload!="") and ($fileUpload!="none")){	
        fileUpload($fileUpload,$fileUpload_name,$fPIC);			
        $pic_filename = "doc-".$id.".".getExtension($fileUpload_name);
        fileRename($fPIC, $fileUpload_name, $pic_filename);			
    }
    if(empty($pic_filename)) $pic_filename = getField("select pic_filename from emp where id='$id'");
    
    return $pic_filename;
}

function uploadKK($id){
    global $db,$s,$inp,$par,$fKK;		
    $fileUpload = $_FILES["kkFilename"]["tmp_name"];
    $fileUpload_name = $_FILES["kkFilename"]["name"];
    if(($fileUpload!="") and ($fileUpload!="none")){	
        fileUpload($fileUpload,$fileUpload_name,$fKK);			
        $kk_filename = "doc-".$id.".".getExtension($fileUpload_name);
        fileRename($fKK, $fileUpload_name, $kk_filename);			
    }
    if(empty($kk_filename)) $kk_filename = getField("select kk_filename from emp where id='$id'");
    
    return $kk_filename;
}

function ubah(){
    global $s, $db, $inp, $par, $cUsername, $arrParam;

    $inp[birth_date] = setTanggal($inp[birth_date]);
    $inp[join_date] = setTanggal($inp[join_date]);
    $inp[leave_date] = setTanggal($inp[leave_date]);
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    $inp[npwp_date] = setTanggal($inp[npwp_date]);
    $inp[bpjs_date] = setTanggal($inp[bpjs_date]);
    $inp[bpjs_date_ks] = setTanggal($inp[bpjs_date_ks]);

    $fileKTP = uploadKTP($par[id]);
    $filePIC = uploadPIC($par[id]);
    $fileKK = uploadKK($par[id]);

    //--------------------- UBAH DATA PEGAWAI -----------------------

    $sql = "update emp set depan = '$inp[depan]', kk_no = '$inp[kk_no]', belakang = '$inp[belakang]', tipe = '$inp[tipe]', facebook = '$inp[facebook]', twitter = '$inp[twitter]', instagram = '$inp[instagram]', name = '$inp[name]', alias = '$inp[alias]', reg_no = '$inp[reg_no]', birth_place = '$inp[birth_place]', birth_date = '$inp[birth_date]', ktp_no = '$inp[ktp_no]', gender = '$inp[gender]', ktp_address = '$inp[ktp_address]', ktp_prov = '$inp[ktp_prov]', ktp_city = '$inp[ktp_city]', dom_address = '$inp[dom_address]', dom_prov = '$inp[dom_prov]', dom_city = '$inp[dom_city]', cell_no = '$inp[cell_no]', phone_no = '$inp[phone_no]', religion = '$inp[religion]', cat = '$inp[cat]', status = '$inp[status]', join_date = '$inp[join_date]', leave_date = '$inp[leave_date]', uni_cloth = '$inp[uni_cloth]', uni_pant = '$inp[uni_pant]', uni_shoe = '$inp[uni_shoe]', marital = '$inp[marital]', email = '$inp[email]', npwp_no = '$inp[npwp_no]', npwp_date = '$inp[npwp_date]', bpjs_no = '$inp[bpjs_no]', bpjs_date = '$inp[bpjs_date]', bpjs_no_ks = '$inp[bpjs_no_ks]', bpjs_date_ks = '$inp[bpjs_date_ks]', blood_type = '$inp[blood_type]', blood_resus = '$inp[blood_resus]', ktp_filename = '$fileKTP', pic_filename = '$filePIC', kk_filename = '$fileKK', upd_by = '$cUsername', upd_date = '".date('Y-m-d H:i:s')."' where id = '$par[id]'";
    db($sql);

    //--------------------- UBAH DATA POSISI -----------------------
    
    if(!empty($inp[idPosisi])){
    $sql = "update emp_phist set pos_name = '$inp[pos_name]', rank = '$inp[rank]', grade = '$inp[grade]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', region = '$inp[region]', location = '$inp[location]', remark = '$inp[remark]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', line_id = '$inp[line_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', laporan_id = '$inp[laporan_id]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', shift_id = '$inp[shift_id]', skala = '$inp[skala]', kategori_id = '$inp[kategori_id]', group_id = '$inp[group_id]', perdin = '$inp[perdin]', status = '1', upd_by = '$cUsername', upd_date = '".date('Y-m-d H:i:s')."' where id = '$inp[idPosisi]' ";
    db($sql);
    }else{
    $idPhist = getField("select id from emp_phist order by id desc limit 1")+1;
    $sql="insert into emp_phist (id, parent_id, pos_name, rank, grade, sk_no, sk_date, start_date, end_date, region, location, status, remark, dir_id, div_id, dept_id, unit_id, line_id, prov_id, city_id, laporan_id, leader_id, administration_id, replacement_id, replacement2_id, lembur, payroll_id, shift_id, skala, kategori_id, group_id, perdin, cre_by, cre_date) values ('$idPhist', '$par[id]', '$inp[pos_name]', '$inp[rank]', '$inp[grade]', '$inp[sk_no]', '$inp[sk_date]', '$inp[start_date]', '$inp[end_date]', '$inp[region]', '$inp[location]', '1', '$inp[remark]', '$inp[dir_id]', '$inp[div_id]', '$inp[dept_id]', '$inp[group_id]', '$inp[line_id]', '$inp[prov_id]', '$inp[city_id]', '$inp[laporan_id]', '$inp[leader_id]', '$inp[administration_id]', '$inp[replacement_id]', '$inp[replacement2_id]', '$inp[lembur]', '$inp[payroll_id]', '$inp[shift_id]', '$inp[skala]', '$inp[kategori_id]', '$inp[group_id]', '$inp[perdin]', '$cUsername', '".date('Y-m-d H:i:s')."')";
    db($sql);
    }

    $idPegawai = $par[id];
	$idGrade = $inp[grade];
	$idSkala = $inp[skala];
	$nomorPokok = $inp[sk_no];
	$tanggalPokok = $inp[start_date];
	$nilaiPokok = getField("select nilai from pay_acuan_gapok where idGrade='".$idGrade."' and idSkala='".$idSkala."'");
	$namaGrade = getField("select namaData from mst_data where kodeData='".$idGrade."'");
	$namaSkala = getField("select namaData from mst_data where kodeData='".$idSkala."'");
		
	$arrKet = array();
	if(!empty($inp[pos_name])) $arrKet[] = $inp[pos_name];
	if(!empty($namaGrade)) $arrKet[] = $namaGrade;
	if(!empty($namaSkala)) $arrKet[] = $namaSkala;
	$keteranganPokok = implode(", ", $arrKet);

	if($idPokok = getField("select idPokok from pay_pokok where idPegawai='".$idPegawai."' and tanggalPokok='".$tanggalPokok."'")){
		$sql="update pay_pokok set nomorPokok='$nomorPokok', tanggalPokok='$tanggalPokok', nilaiPokok='".setAngka($nilaiPokok)."', keteranganPokok='$keteranganPokok', updateBy='$cUsername', updateTime='".date("Y-m-d H:i:s")."' where idPokok='".$idPokok."'";
	}else{
		$idPokok = getField("select idPokok from pay_pokok order by idPokok desc limit 1")+1;
		$sql="insert into pay_pokok (idPokok, idPegawai, nomorPokok, tanggalPokok, nilaiPokok, keteranganPokok, createBy, createTime) values ('$idPokok', '$idPegawai', '$nomorPokok', '$tanggalPokok', '$nilaiPokok', '$keteranganPokok', '$cUsername', '".date("Y-m-d H:i:s")."')";
	}

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?".getPar($par,"mode,id")."';</script>";
}

function form(){
    global $s,$db,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess,$cUsername,$sUser,$kodeModul,$sGroup,$m,$areaCheck,$arrParam;

    $sql="SELECT * FROM emp WHERE id='$par[id]'";
    $res=db($sql);
    $r=mysql_fetch_array($res);
    
    $sql_="SELECT * FROM emp_phist WHERE parent_id='$par[id]' AND status = '1'";
    $res_=db($sql_);
    $r_=mysql_fetch_array($res_);

    // $__validate["formid"] = "myForm";

    $false = $r[tipe] == "r" ? "checked=\"checked\"" : "";
    $true = empty($false) ? "checked=\"checked\"" : "";
    $budget = $r[budget] == "b" ? "checked=\"checked\"" : "";
    $unbudget = empty($budget) ? "checked=\"checked\"" : "";

    setValidation("is_null","inp[reg_no]","anda harus mengisi NPP");
    setValidation("is_null","inp[ktp_no]","anda harus mengisi KTP");
    setValidation("is_null","inp[birth_place]","anda harus mengisi NPP");
    setValidation("is_null","birth_date","anda harus mengisi NPP");
    setValidation("is_null","inp[pos_name]","anda harus mengisi Jabatan pada tab Posisi");
    setValidation("is_null","inp[rank]","anda harus mengisi Pangkat pada tab Posisi");
    setValidation("is_null","inp[location]","anda harus mengisi Lokasi Kerja pada tab Posisi");
    setValidation("is_null","inp[start_date]","anda harus mengisi Mulai pada tab Posisi");
    setValidation("is_null","inp[dir_id]","anda harus mengisi Perusahaan pada tab Posisi > Organisasi");
    setValidation("is_null","inp[cat]","anda harus mengisi Status Pegawai pada tab Status");
    setValidation("is_null","inp[status]","anda harus mengisi Status Aktif pada tab Status");
    setValidation("is_null","inp[join_date]","anda harus mengisi Mulai Kerja pada tab Status");
    

	
	$text = getValidation();

    $text.="<div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread(ucwords($mode . " data")) . "
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
  <p style=\"position:absolute;top:5px;right:5px;\">";
        if(isset($menuAccess[$s]["edit"])){ $text.= "<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" />";}
        $text.="
        <a href=\"#\" onclick=\"openBox('ajax.php?par[mode]=printCV&par[id]=".$cID.getPar($par,'mode')."',1200,600);\" id=\"btnExpExcel\" class=\"btn btn1 btn_inboxi\"><span>Print CV</span></a>
    </p>
    <input type = \"hidden\" id=\"inp[idPegawai]\" value=\"$r[id]\">
    <input type = \"hidden\" name=\"inp[idPosisi]\" value=\"$r_[id]\">
    <input type = \"hidden\" id=\"par[mode]\" value=\"$par[id]\">
	<style>
	.chosen-container{ min-width:250px; }
	</style>
    <br>
    <ul class=\"hornav\">
      <li class=\"current\"><a href=\"#data\">Data Pegawai</a></li>
      <li><a href=\"#posisi\">Posisi</a></li>
      <li><a href=\"#status\">Status</a></li>        
      <li><a href=\"#photo\">Foto</a></li>        
             
    </ul>
    <br>
    ";

   
    //------------------------------------------------------------- DATA PEGAWAI START --------------------------------------------
    $text.="
    <div id=\"data\" class=\"subcontent\" style=\"margin:0\">
    
    
    <table style=\"width:100%\">
    <tr>
    <td style=\"width:50%\">
    <p>           
        <label class=\"l-input-small2\">Nama Lengkap <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[name]\" name=\"inp[name]\"  value=\"$r[name]\" class=\"mediuminput\" style=\"width:250px;text-transform: uppercase;\" maxlength=\"30\" />            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small2\" >Gelar Depan </label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[depan]\" name=\"inp[depan]\"  value=\"$r[depan]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small2\" >Gelar Belakang </label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[belakang]\" name=\"inp[belakang]\"  value=\"$r[belakang]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
    </p>
	<p>           
        <label class=\"l-input-small2\" >Panggilan </label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[alias]\" name=\"inp[alias]\"  value=\"$r[alias]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small2\" >Tempat Lahir <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	".comboData("select t1.kodeData id, concat(t2.namaData,' - ',t1.namaData) description from mst_data t1 
            JOIN mst_data t2 ON t1.kodeInduk=t2.kodeData 
            where 
            t2.kodeCategory='".$arrParameter[3]."' 
            AND t2.kodeInduk='1'
            AND t1.kodeCategory='".$arrParameter[4]."'
            order by t2.namaData, t1.namaData", "id", "description", "inp[birth_place]", " ", $r[birth_place], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>
		<label class=\"l-input-small2\">Tanggal Lahir <span class=\"required\">*)</span></label>
		<div class=\"field\">
			<input type=\"text\" id=\"birth_date\" name=\"inp[birth_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[birth_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	
	<p>
        <label class=\"l-input-small2\">Alamat KTP</label>
        <div class=\"field\">
            <textarea id=\"inp[ktp_address]\" name=\"inp[ktp_address]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:250px;\">$r[ktp_address]</textarea>
        </div>
	</p>
	<p>           
        <label class=\"l-input-small2\" >Provinsi</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S02' order by namaData", "id", "description", "inp[ktp_prov]", " ", $r[ktp_prov], "onchange=\"getAnakan('ktp_prov','ktp_city','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small2\" >Kota</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[ktp_prov]' and statusData = 't' order by namaData", "id", "description", "inp[ktp_city]", " ", $r[ktp_city], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small2\" >Telp. Rumah </label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[phone_no]\" name=\"inp[phone_no]\"  value=\"$r[phone_no]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
    </p>
    
    </td>
	<td style=\"width:50%\">
	<p>           
    <label class=\"l-input-small\" >NPP <span class=\"required\">*)</span></label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[reg_no]\" name=\"inp[reg_no]\"  value=\"$r[reg_no]\" class=\"mediuminput\" style=\"width:250px;\" />            
    </div>          
    </p>
    <p>           
    <label class=\"l-input-small\" >No. KTP <span class=\"required\">*)</span></label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[ktp_no]\" name=\"inp[ktp_no]\"  value=\"$r[ktp_no]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"16\" onkeyup=\"cekPhone(this);\" />            
    </div>          
    </p>
    <p>           
    <label class=\"l-input-small\" >No. KK</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[kk_no]\" name=\"inp[kk_no]\"  value=\"$r[kk_no]\" class=\"mediuminput\" style=\"width:250px;\" onkeyup=\"cekPhone(this);\" />            
    </div>          
    </p>
    <p>           
        <label class=\"l-input-small\" >Tipe</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'JP' order by namaData", "id", "description", "inp[tipe]", " ", $r[tipe], "onchange=\"getAnakan('tipe','grade','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
    </p>   
	<p>
	<label class=\"l-input-small\">Jenis Kelamin</label>
	<div class=\"field\">
		<input type=\"radio\" id=\"new\" name=\"inp[gender]\" value=\"M\" $true /> <span class=\"sradio\">Laki-Laki</span>
		<input type=\"radio\" id=\"rep\" name=\"inp[gender]\" value=\"F\" $false /> <span class=\"sradio\">Perempuan</span>
	</div>
	</p> 
	
    <p>
        <label class=\"l-input-small\">Alamat Domisili</label>
        <div class=\"field\">
            <textarea id=\"inp[dom_address]\" name=\"inp[dom_address]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:250px;\">$r[dom_address]</textarea>
        </div>
    </p>
    <p>           
        <label class=\"l-input-small\" >Provinsi</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S02' order by namaData", "id", "description", "inp[dom_prov]", " ", $r[dom_prov], "onchange=\"getAnakan('dom_prov','dom_city','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small\" >Kota</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[dom_prov]' and statusData = 't' order by namaData", "id", "description", "inp[dom_city]", " ", $r[dom_city], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >Agama</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S07' order by namaData", "id", "description", "inp[religion]", " ", $r[religion], "", "250px","chosen-select")."            
        </div>          
    </p> 
    <p>           
        <label class=\"l-input-small\" >No. HP</label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[cell_no]\" name=\"inp[cell_no]\"  value=\"$r[cell_no]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
    </p>
    
    </td>
    </tr>
    </table>
    </div>
";

//----------------------------------------------------------- DATA PEGAWAI END --------------------------------------------

//----------------------------------------------------------- DATA POSISI START ---------------------------------------------
    
$text.="
	<div id=\"posisi\" class=\"subcontent\" style=\"margin:0;display:none;\">
	<div class=\"notibar announcement\" style=\"background-color: #FFD863; border-color: #FFD863; color: #000; margin: 0 20px 30px 20px;\">
    <a class=\"close\">X</a>
    <p><b>Informasi : </b>Input posisi baru dapat dilakukan dengan klik <a href=\"http://bangunkapasitas.net/2018/jrp/index.php?c=27&p=117&m=1154&s=1154\" target = \"_blank\"><b>DISINI</b></a></p>
  </div>
  <script type=\"text/javascript\">
  jQuery(document).ready(function() {

      function hidePanel() {     
        jQuery(\"a.close\").click();
      }
      
      setTimeout(hidePanel, 60000);
    });
  </script>
	<table style=\"width:100%\">
    <tr>
    <td style=\"width:50%\">
    <p>           
        <label class=\"l-input-small2\" >Jabatan <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[pos_name]\" name=\"inp[pos_name]\"  value=\"$r_[pos_name]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small2\" >Grade</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'GTU' and kodeInduk = '$r[tipe]' order by namaData", "id", "description", "inp[grade]", " ", $r_[grade], "onchange=\"getAnakan('grade','skala','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>	
	<p>           
        <label class=\"l-input-small2\" >Nomor SK</label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[sk_no]\" name=\"inp[sk_no]\"  value=\"$r_[sk_no]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
	</p>
	<p>
		<label class=\"l-input-small2\">Mulai <span class=\"required\">*)</span></label>
		<div class=\"field\">
			<input type=\"text\" id=\"start_date\" name=\"inp[start_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r_[start_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	<p>           
        <label class=\"l-input-small2\" >Region</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'RG' order by namaData", "id", "description", "inp[region]", " ", $r_[region], "onchange=\"getAnakan('region','location','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	
	<p>           
        <label class=\"l-input-small2\" >Keterangan</label>
        <div class=\"field\">           
        	<input type=\"text\" id=\"inp[remark]\" name=\"inp[remark]\"  value=\"$r_[remark]\" class=\"mediuminput\" style=\"width:250px;\" />            
        </div>          
	</p>
	</td>
    <td style=\"width:50%\">
    <p>           
        <label class=\"l-input-small\" >Pangkat <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S09' order by namaData", "id", "description", "inp[rank]", " ", $r_[rank], "", "250px","chosen-select")."            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small\" >Skala</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'GSG' and kodeInduk = '$r_[grade]' order by namaData", "id", "description", "inp[skala]", " ", $r_[skala], "", "250px","chosen-select")."            
        </div>          
	</p>
	
	<p>
		<label class=\"l-input-small\">Tanggal SK</label>
		<div class=\"field\">
			<input type=\"text\" id=\"sk_date\" name=\"inp[sk_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r_[sk_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	<p>
		<label class=\"l-input-small\">Selesai</label>
		<div class=\"field\">
			<input type=\"text\" id=\"end_date\" name=\"inp[end_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r_[end_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	<p>           
        <label class=\"l-input-small\" >Location <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S06' and kodeInduk = '$r_[region]' order by namaData", "id", "description", "inp[location]", " ", $r_[location], "", "250px","chosen-select")."            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small\" >Kategori</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'KP' order by namaData", "id", "description", "inp[kategori_id]", " ", $r_[kategori_id], "", "250px","chosen-select")."            
        </div>          
	</p>
	</td>
	</tr>
	</table>

    <ul class=\"editornav\">
      <li class=\"current\"><a href=\"#organisasi\">ORGANISASI</a></li>
      <li><a href=\"#lokasi\">LOKASI</a></li>
      <li><a href=\"#struktur\">STRUKTUR</a></li>
      <li><a href=\"#setting\">SETTING</a></li>        
             
    </ul>
    
";	

//----------------------------------------------------------- DATA ORGANISASI START ---------------------------------------------
$text.="

<div id =\"organisasi\" class=\"subcontent1\">
<br>
	
	<p>           
        <label class=\"l-input-small\" >".$arrParameter[38]." <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X04' order by kodeInduk,urutanData", "id", "description", "inp[dir_id]", " ", $r_[dir_id], "onchange=\"getAnakan('dir_id','div_id','divisi','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >".$arrParameter[39]."</label>
        <div class=\"field\">           
        	".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
            JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
            where t2.kodeCategory='X04' and t1.kodeInduk = '$r_[dir_id]' order by t1.urutanData", "id", "description", "inp[div_id]", " ", $r_[div_id], "onchange=\"getAnakan('div_id','dept_id','departemen','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >".$arrParameter[40]."</label>
        <div class=\"field\">           
        	".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
            JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
            JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
            where t3.kodeCategory='X04' and t1.kodeInduk = '$r_[div_id]' order by t1.urutanData", "id", "description", "inp[dept_id]", " ", $r_[dept_id], "onchange=\"getAnakan('dept_id','unit_id','group','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >".$arrParameter[41]."</label>
        <div class=\"field\">           
        	".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
            JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
            JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
            JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
            where t4.kodeCategory='X04' and t1.kodeInduk = '$r_[dept_id]' order by t1.urutanData", "id", "description", "inp[unit_id]", " ", $r_[unit_id], "onchange=\"getAnakan('unit_id','line_id','line','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >".$arrParameter[57]."</label>
        <div class=\"field\">           
        	".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
            JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
            JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
            JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
            JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk
            where t5.kodeCategory='X04' and t1.kodeInduk = '$r_[unit_id]' order by t1.urutanData", "id", "description", "inp[line_id]", " ", $r_[line_id], "", "250px","chosen-select")."            
        </div>          
    </p>
    </div>
";
//----------------------------------------------------------- DATA ORGANISASI END ---------------------------------------------

//----------------------------------------------------------- DATA LOKASI START ---------------------------------------------
$text.="
<div id =\"lokasi\" class=\"subcontent1\" style=\"margin:0;display:none;\">
<br>
	<p>           
        <label class=\"l-input-small\" >Provinsi</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S02' order by namaData", "id", "description", "inp[prov_id]", " ", $r_[prov_id], "onchange=\"getAnakan('prov_id','city_id','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >Kota</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r_[prov_id]' order by namaData", "id", "description", "inp[city_id]", " ", $r_[city_id], "", "250px","chosen-select")." 
        </div>          
    </p>
</div>
	";
//----------------------------------------------------------- DATA LOKASI END ---------------------------------------------

//----------------------------------------------------------- DATA STRUKTUR START ---------------------------------------------
$text.="
<div id =\"struktur\" class=\"subcontent1\" style=\"margin:0;display:none;\">
<br>
	<p>           
        <label class=\"l-input-small\" >Laporan</label>
        <div class=\"field\">           
        	".comboData("select id, name description from dta_pegawai order by name", "id", "description", "inp[laporan_id]", " ", $r_[laporan_id], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >Atasan</label>
        <div class=\"field\">           
        	".comboData("select id, name description from dta_pegawai order by name", "id", "description", "inp[leader_id]", " ", $r_[leader_id], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >Admin</label>
        <div class=\"field\">           
        	".comboData("select id, name description from dta_pegawai order by name", "id", "description", "inp[administration_id]", " ", $r_[administration_id], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >Pengganti 1</label>
        <div class=\"field\">           
        	".comboData("select id, name description from dta_pegawai order by name", "id", "description", "inp[replacement_id]", " ", $r_[replacement_id], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small\" >Pengganti 2</label>
        <div class=\"field\">           
        	".comboData("select id, name description from dta_pegawai order by name", "id", "description", "inp[replacement2_id]", " ", $r_[replacement2_id], "", "250px","chosen-select")."            
        </div>          
	</p>
</div>
	";
//----------------------------------------------------------- DATA STRUKTUR END ---------------------------------------------

//----------------------------------------------------------- DATA SETTING START ---------------------------------------------
$text.="
<div id =\"setting\" class=\"subcontent1\" style=\"margin:0;display:none;\">
<br>
	<p>
	<label class=\"l-input-small\">Hak Lembur</label>
	<div class=\"field\">
		<input type=\"radio\" id=\"new\" name=\"inp[lembur]\" value=\"1\" $true /> <span class=\"sradio\">Ya</span>
		<input type=\"radio\" id=\"rep\" name=\"inp[lembur]\" value=\"0\" $false /> <span class=\"sradio\">Tidak</span>
	</div>
	</p>
	<p>           
        <label class=\"l-input-small\" >Jenis Payroll</label>
        <div class=\"field\">           
        	".comboData("select idJenis id, namaJenis description from pay_jenis where statusJenis = 't' order by namaJenis", "id", "description", "inp[payroll_id]", " ", $r_[payroll_id], "", "250px","chosen-select")."            
        </div>          
    </p>
    <p>           
        <label class=\"l-input-small\" >Location Process</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S06' order by namaData", "id", "description", "inp[group_id]", " ", $r_[group_id], "", "250px","chosen-select")."            
        </div>          
    </p>
	<p>           
        <label class=\"l-input-small\" >Shift Kerja</label>
        <div class=\"field\">           
        	".comboData("select idShift id, namaShift description from dta_shift where statusShift = 't' order by namaShift", "id", "description", "inp[shift_id]", " ", $r_[shift_id], "", "250px","chosen-select")."            
        </div>          
    </p>
    ";
    $r_[perdin] = empty($r_[perdin]) ? getField("select kodeData from mst_data where kodeCategory = 'PJG' order by urutanData DESC LIMIT 1") : $r_[perdin];
    $text.="
    <p>           
    <label class=\"l-input-small\" >Perjalanan Dinas</label>
    <div class=\"field\">           
        ".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'PJG' order by namaData", "id", "description", "inp[perdin]", " ", $r_[perdin], "", "250px","chosen-select")."            
    </div>          
</p>
    </div>
	";

//------------------------------------------------------------- DATA SETTING END --------------------------------------------
	$text.="

    </div>

    ";

//------------------------------------------------------------- DATA POSISI END --------------------------------------------

//------------------------------------------------------------- DATA STATUS START --------------------------------------
    
$text.="
	<div id=\"status\" class=\"subcontent\" style=\"margin:0;display:none;\">
	
    <table style=\"width:100%\">
    <tr>
	<td style=\"width:50%\">
	<div class=\"widgetbox\">
	<div class=\"title\">
	<h3>STATUS PEGAWAI</h3>
	</div>
	</div>
    <p>           
        <label class=\"l-input-small2\" >Status Pegawai <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S04' order by urutanData", "id", "description", "inp[cat]", " ", $r[cat], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
        <label class=\"l-input-small2\" >Status Aktif <span class=\"required\">*)</span></label>
        <div class=\"field\">           
        	".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S05' order by urutanData", "id", "description", "inp[status]", " ", $r[status], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>
		<label class=\"l-input-small2\">Mulai Bekerja <span class=\"required\">*)</span></label>
		<div class=\"field\">
			<input type=\"text\" id=\"join_date\" name=\"inp[join_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[join_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	<p>
		<label class=\"l-input-small2\">Tanggal Keluar</label>
		<div class=\"field\">
			<input type=\"text\" id=\"leave_date\" name=\"inp[leave_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[leave_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	</td>
	<td style=\"width:50%\">
	<div class=\"widgetbox\">
	<div class=\"title\">
	<h3>UKURAN SERAGAM</h3>
	</div>
	</div>
	<p>           
    <label class=\"l-input-small\" >Baju</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[uni_cloth]\" name=\"inp[uni_cloth]\"  value=\"$r[uni_cloth]\" class=\"mediuminput\" style=\"width:50px;\" />            
    </div>          
	</p>
	<p>           
    <label class=\"l-input-small\" >Celana</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[uni_pant]\" name=\"inp[uni_pant]\"  value=\"$r[uni_pant]\" class=\"mediuminput\" style=\"width:50px;\" />            
    </div>          
	</p>
	<p>           
    <label class=\"l-input-small\" >Sepatu</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[uni_shoe]\" name=\"inp[uni_shoe]\"  value=\"$r[uni_shoe]\" class=\"mediuminput\" style=\"width:50px;\" />            
    </div>          
    </p>
	</td>
    </tr>
	</table>
	<div class=\"widgetbox\">
	<div class=\"title\">
	<h3>DETAIL INFO</h3>
	</div>
	</div>
	<table style=\"width:100%\">
    <tr>
	<td style=\"width:50%\">

    <p>           
        <label class=\"l-input-small2\" >Status Perkawinan</label>
        <div class=\"field\">           
        	".comboData("select kodeData id, concat(namaData,' - ',keteranganData) description from mst_data where kodeCategory = 'S08' order by namaData", "id", "description", "inp[marital]", " ", $r[marital], "", "250px","chosen-select")."            
        </div>          
	</p>
	<p>           
    <label class=\"l-input-small2\" >Email</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[email]\" name=\"inp[email]\"  value=\"$r[email]\" class=\"mediuminput\" style=\"width:150px;\" />            
    </div>          
	</p>
	<p>           
    <label class=\"l-input-small2\" >No. NPWP</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[npwp_no]\" name=\"inp[npwp_no]\"  value=\"$r[npwp_no]\" class=\"mediuminput\" style=\"width:150px;\" />            
    </div>          
	</p>
	<p>
		<label class=\"l-input-small2\">Tgl. NPWP</label>
		<div class=\"field\">
			<input type=\"text\" id=\"npwp_date\" name=\"inp[npwp_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[npwp_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	</td>
	<td style=\"width:50%\">
	<p>           
    <label class=\"l-input-small\" >No. BPJS TK</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[bpjs_no]\" name=\"inp[bpjs_no]\"  value=\"$r[bpjs_no]\" class=\"mediuminput\" style=\"width:150px;\" />            
    </div>          
	</p>
	<p>
		<label class=\"l-input-small\">Tgl. BPJS TK</label>
		<div class=\"field\">
			<input type=\"text\" id=\"bpjs_date\" name=\"inp[bpjs_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[bpjs_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	<p>           
    <label class=\"l-input-small\" >No. BPJS KS</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[bpjs_no_ks]\" name=\"inp[bpjs_no_ks]\"  value=\"$r[bpjs_no_ks]\" class=\"mediuminput\" style=\"width:150px;\" />            
    </div>          
	</p>
	<p>
		<label class=\"l-input-small\">Tgl. BPJS KS</label>
		<div class=\"field\">
			<input type=\"text\" id=\"bpjs_date_ks\" name=\"inp[bpjs_date_ks]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[bpjs_date_ks]) . "\" class=\"vsmallinput hasDatePicker\"/>
		</div>
	</p>
	<p>
		<label class=\"l-input-small\">Gol. Darah</label>
		<div class=\"field\">
			".comboArray("inp[blood_type]",array("A","B","O","AB"),$r[blood_type])." &nbsp;&nbsp; <b>Rhesus</b> &nbsp;
			".comboArray("inp[blood_type]",array("+","-"),$r[blood_type])."
		</div>
	</p>
	</td>
    </tr>
    </table>

    <div class=\"widgetbox\">
	<div class=\"title\">
	<h3>SOSIAL MEDIA</h3>
	</div>
    </div>
    <table style=\"width:100%\">
    <tr>
    <td style=\"width:50%\">
    <p>           
    <label class=\"l-input-small2\" >Facebook</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[facebook]\" name=\"inp[facebook]\"  value=\"$r[facebook]\" class=\"mediuminput\" style=\"width:250px;\" />            
    </div>          
    </p>
    <p>           
    <label class=\"l-input-small2\" >Twitter</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[twitter]\" name=\"inp[twitter]\"  value=\"$r[twitter]\" class=\"mediuminput\" style=\"width:250px;\" />            
    </div>          
	</p>
    </td>
    <td style=\"width:50%\">
    <p>           
    <label class=\"l-input-small\" >Instagram</label>
    <div class=\"field\">           
        <input type=\"text\" id=\"inp[instagram]\" name=\"inp[instagram]\"  value=\"$r[instagram]\" class=\"mediuminput\" style=\"width:250px;\" />            
    </div>          
	</p>
    </td>
    </tr>
    </table>
    

</div>
";
//------------------------------------------------------------- DATA STATUS END --------------------------------------

//------------------------------------------------------------- DATA FOTO START --------------------------------------
$text.="
<div id=\"photo\" class=\"subcontent\" style=\"display: none;margin-top: 0px;\">
  <p>
        <label class=\"l-input-small\">File KTP</label>
        <div id=\"ktpPreview\"
        ";
        if ($r[ktp_filename] != "") {
          $text.= "style=\"background-image:  url(".APP_URL."/files/emp/ktp/".$r[ktp_filename].")\" ";
        }
        $text.="></div>
        <br/>
        <input id=\"ktpFilename\" type=\"file\" name=\"ktpFilename\" class=\"img\" style=\"padding-left: 240px;\" />   
     </p>
      <p>
        <label class=\"l-input-small\">Foto</label>
        <div id=\"fotoPreview\" 
        ";
        if ($r[pic_filename] != "") {
          $text.= "style=\"background-image:  url(".APP_URL."/files/emp/pic/".$r[pic_filename].")\" ";
        }
        $text.="
        ></div>
        <br/>
        <input id=\"picFilename\" type=\"file\" name=\"picFilename\" class=\"img\" style=\"padding-left: 240px;\" />   
      </p>
      <p>
        <label class=\"l-input-small\">KK</label>
        <div id=\"kkPreview\" 
        ";
        if ($r[kk_filename] != "") {
          $text.= "style=\"background-image:  url(".APP_URL."/files/emp/kk/".$r[kk_filename].")\" ";
        }
        $text.="
        ></div>
        <br/>
        <input id=\"kkFilename\" type=\"file\" name=\"kkFilename\" class=\"img\" style=\"padding-left: 240px;\" />   
      </p>
</div>

<style>
  #p0 {
    margin: 5px 0;
  }

  #ktpPreview {
    border: #069 solid 1px;
    padding-left: 160px;
    width: 180px;
    height: 180px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
  }
  #fotoPreview {
    border: #069 solid 1px;
    width: 215px;
    height: 250px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
  }
  #kkPreview {
    border: #069 solid 1px;
    padding-left: 160px;
    width: 180px;
    height: 180px;
    background-position: center center;
    background-size: cover;
    -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
    display: inline-block;
  }
  fieldset {
    border: 2px solid #0A246A;
    border-radius: 8px;
    margin-left: 10px;
    margin-right: 10px;
    margin-bottom: 10px;
  }
  legend {
    font-weight: bold;
    font-size: 1.2em;
    color: #0A246A;
    /*border: 1px solid #03F;*/
    padding: 5px;
  }
  fieldset label{
    margin-left: 10px;
  }
</style>



</div>";

return $text;

}

function pdf(){
    global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
    require_once 'plugins/PHPPdf.php';
    
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
    $arrName = arrayQuery("select id, name from emp");

    $sql="select *,(CASE WHEN gender = 'M' THEN 'Laki-Laki' ELSE (CASE WHEN gender = 'F' THEN 'Perempuan' ELSE '' END) END) as gender from emp where id = '$par[id]'";
    $res=db($sql);
    $r=mysql_fetch_array($res);

    $r_[join_date] = $r[join_date];
    $r_[leave_date] = $r[leave_date];
    $sql__="select * from emp_char where parent_id = '$par[id]'";
    $res__=db($sql__);
    $r__=mysql_fetch_array($res__);
    
    $pdf = new PDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetLeftMargin(15);
    
    
    $pdf->Cell(30,20,'',0,0,'L');
    if(!empty($r[pic_filename])){
    $gambar = "files/emp/pic/".$r[pic_filename];
    
    $pdf->Image($gambar, 155,40,35);
    }else{
    $gambar = "files/emp/pic/nophoto.jpg";
    $pdf->Image($gambar,155,40,40);
    }
    $pdf->Cell(40,6,'',0,0,'L');
    // $pdf->Ln(1); 
    

    $pdf->Ln();
    $pdf->SetFont('Arial','B',12);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','B',8);
        
    $pdf->setFillColor(230,230,230);
    // $pdf->Ln(6); 
    $pdf->SetFont('Arial','B',12); 
    $pdf->Cell(60,6,' ',0,0,'L');
    $pdf->Cell(25,6,'',0,0,'L');
    $pdf->Cell(80,6,' ',0,0,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(3,6,"RAHASIA",0,0,'L');
    $pdf->Ln(7);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',12); 
    $pdf->Cell(60,6,' ',0,0,'L');
    $pdf->Cell(25,6,'BIODATA KARYAWAN',0,0,'L');
    $pdf->Cell(80,6,' ',0,0,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(3,6,"",0,0,'L');
    $pdf->Ln();
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'NPP',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$r[reg_no],0,0,'L');
    // $pdf->Ln(7);

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'JABATAN',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$r[pos_name],0,0,'L');
    // $pdf->Ln(7);

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'PANGKAT',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$arrMaster[$r[rank]],0,0,'L');
    // $pdf->Ln(7);

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'GRADE',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$arrMaster[$r[grade]],0,0,'L');
    // $pdf->Ln(7); 

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'SKALA',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$arrMaster[$r[skala]],0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Line(15,40,195,40);
    $pdf->Line(15,40,15,100);
    $pdf->Line(195,40,195,100);
    $pdf->Line(15,100,195,100);  
    $pdf->Ln(4);
    $gelar="";
    if(!empty($r[depan]))
      $gelar=$r[depan]." ".$r[belakang];

    $r[name] =explode(",",$r[name]);


    $cekGelar="-";
    if(!empty($r[depan]))
      $cekGelar=getField("SELECT srtf_name from emp_training where parent_id='$par[id]' and perihal='AAMAI'");

    $pdf->Ln();
    $pdf->SetLeftMargin(20);
    $pdf->SetWidths(array(40,90));
      $pdf->SetAligns(array('L')); 
      $pdf->Row(array("NAMA\tb","".$r[name][0]." \t"));
      $pdf->Row(array("NPP\tb","".$r[reg_no]."\t"));
      $pdf->Row(array("GELAR DEPAN\tb","".$r[depan]."\t"));
      $pdf->Row(array("GELAR BELAKANG\tb","".$r[belakang]."\t"));

    $pdf->Ln(25);
    
    
    $pdf->SetLeftMargin(15);
    $pdf->Ln(20);
    $pdf->SetFont('Arial','',10);
    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));

    $pdf->Row(array("DATA PRIBADI\tb"));
    $pdf->SetFont('Arial','',8);
    $pdf->SetWidths(array(45,45,45,45));
      $pdf->SetAligns(array('L'));

      $pdf->Row(array("TEMPAT LAHIR\tb","".$arrMaster[$r[birth_place]]."\t","STATUS\tb",$arrMaster[$r[cat]]."\t"));
      $pdf->Row(array("TANGGAL LAHIR\tb","".getTanggal($r[birth_date],'t')."\t","NO.KTP\tb","".$r[ktp_no]."\t"));
      $pdf->Row(array("JENIS KELAMIN\tb","".$r[gender]."\t","EMAIL\tb","".$r[email]."\t"));
      $pdf->Row(array("AGAMA\tb","".$arrMaster[$r[religion]]."\t","FACEBOOK\tb","".$r[facebook]."\t"));
      $pdf->Row(array("NOMOR HP\tb","".$r[cell_no]."\t","INSTAGRAM\tb","".$r[instagram]."\t"));
      // $pdf->Row(array("TEMPAT LAHIR\tb","".$arrMaster[$r[birth_place]]."\tb","STATUS\tb","\tb"));

    // $pdf->SetFont('Arial','B',13);
    // $pdf->setFillColor(0,0,0);
    // $pdf->SetTextColor(255,255,255);

    // $pdf->Cell(180,8,'DATA PRIBADI',0,0,'C','#000000');
    // $pdf->SetTextColor(0,0,0);

        
    // $pdf->setFillColor(230,230,230);
    // $pdf->Ln(15);  
    // $pdf->SetFont('Arial','',8);

    

  

    
    // $pdf->Cell(35,6,'TEMPAT LAHIR',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[birth_place]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'STATUS',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TANGGAL LAHIR',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,getTanggal($r[birth_date],'t'),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NO.KTP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[ktp_no],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'JENIS KELAMIN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[gender],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'EMAIL',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[email],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'AGAMA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
  
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // // $pdf->Cell(35,6,'ALAMAT DOMISILI',0,0,'L','true');
    // // $pdf->Cell(3,6,' ',0,0,'C');
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Cell(5,6,$r[dom_address],0,0);
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Ln(7);

    // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[dom_prov]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'FACEBOOK',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // // $pdf->Cell(60,6,' ',0,0,'C');
    
    
    // // $pdf->Ln(7);

    // // $pdf->Cell(35,6,'TELP RUMAH',0,0,'L','true');
    // // $pdf->Cell(3,6,' ',0,0,'C');
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Cell(5,6,$r[phone_no],0,0);
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NOMOR HP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[cell_no],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'INSTAGRAM',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'instagraam@inta.com',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    $pdf->Ln(7);
    $sql="select * from emp_char where parent_id = '$par[id]'";
    $res=db($sql);
    $r=mysql_fetch_array($res);

    // $pdf->Cell(35,6,'KARAKTER PRIBADI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[characteristic],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // $pdf->Cell(35,6,'HOBI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[hobby],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // $pdf->Cell(35,6,'KEAHLIAN KHUSUS',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[abilities],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // $pdf->Cell(35,6,'ORGANISASI SOSIAL',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[organization],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("POSISI SAAT INI\tb"));
    $pdf->SetFont('Arial','',8);
    $sql="SELECT * from emp_phist where parent_id = '$par[id]' and status='1' order by start_date desc";
    $res=db($sql);
    $getUnit =getField("SELECT location from dta_pegawai where parent_id='$par[id]'");
    $r=mysql_fetch_array($res);
    $pdf->SetWidths(array(45,45,45,45));
      $pdf->SetAligns(array('L'));
  //    $r_[leave_date] = !empty($r_[leave_date]) ? formatDate($r_[leave_date]) : "current";
    // $r_[masaKerjaEfektif] = formatDate($r_[join_date])." - ".$r_[leave_date];

    
      $pdf->Row(array("JABATAN\tb","".strtoupper($r[pos_name])."\t","MKE\tb",formatDate($r_[join_date])."\t"));
    //  $r_[leave_date] = !empty($r_[leave_date]) ? $r_[leave_date] : "current";
      // $r[mkePeriode] = substr($r_[join_date], 0, 4)." - ".$r_[leave_date];
      $mkePeriode = getField("SELECT replace(
        case when coalesce(leave_date,NULL) IS NULL or leave_date='0000-00-00' or leave_date='' THEN
        CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
        ELSE
        CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
        END,' 0 bln','') masaKerja from dta_pegawai where parent_id='$par[id]'");
   
      $pdf->Row(array("UNIT KERJA\tb",$arrMaster[$getUnit]."\t","MKE PERIODE\tb","".$mkePeriode."\t"));
    //  $r_[end_date] = !empty($r[end_date]) ? formatDate($r[end_date]) : "current";
      // $r[tglMasaKerjaJabatan] = formatDate($r[start_date])." - ".$r_[end_date];
    
      $arrData = arrayQuery("select kodeData, namaData from mst_data");
    $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
      $getDateMKJs=getField("select min(start_date) from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."' and start_date!='0000-00-00' and start_date is not null");
    if(empty($getDateMKJs)) $getDateMKJs = $r[start_date];
    if(in_array($getDateMKJs, array("", "0000-00-00"))) $getDateMKJs = $r[join_date];
    if($getDateMKJs < $r[join_date]) $getDateMKJs = $r[join_date];

      $pdf->Row(array("GRADE / TJ\tb","".$arrMaster[$r[grade]]."\t","MKJ\tb","".formatDate($getDateMKJs)."\t"));
    //  $r_[end_date] = !empty($r[end_date]) ? substr($r[end_date],0,4) : "current";
      // $r[mkjPeriode] = substr($r[start_date],0,4)." - ".$r_[end_date];
      $start_date=getField("select start_date from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."'  and status='1' and start_date is not null");
    // if(empty($start_date)) $start_date = $r[start_date];
    // if(in_array($start_date, array("", "0000-00-00"))) $start_date = $r[join_date];
    // if($start_date < $r[join_date]) $start_date = $r[join_date];
    
    // $end_date = $r[end_date];
    // if(in_array($end_date, array("", "0000-00-00"))) $end_date = date("Y-m-d");
    // if($end_date > date("Y-m-d")) $end_date = date("Y-m-d");
    // $dMKJP = selisihHari($start_date, $end_date);
    // $yMKJP = getAngka(floor($dMKJP/ 365));
    // $mMKJP = getAngka(floor(($dMKJP % 365) / 30));

  //    $mkjPPeriode = empty($mMKJP) ? "" : $yMKJP." thn ".$mMKJP." bln";     
      $arrData = arrayQuery("select kodeData, namaData from mst_data");
    $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
      $start_date=getField("select min(start_date) from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."' and start_date!='0000-00-00' and start_date is not null");
    if(empty($start_date)) $start_date = $r[start_date];
    if(in_array($start_date, array("", "0000-00-00"))) $start_date = $r[join_date];
    if($start_date < $r[join_date]) $start_date = $r[join_date];
    
    $end_date = $r[end_date];
    if(in_array($end_date, array("", "0000-00-00"))) $end_date = date("Y-m-d");
    if($end_date > date("Y-m-d")) $end_date = date("Y-m-d");
    $dMKJ = selisihHari($start_date, $end_date);
    $yMKJ = getAngka(floor($dMKJ/ 365));
    $mMKJ = getAngka(floor(($dMKJ % 365) / 30));
    
    $grade = $arrData[$r[grade]];
    if(is_array($arrGrade)){
      reset($arrGrade);
      while(list($id, $val) = each($arrGrade)){
        if (preg_match("/\b".$val."\b/i", $grade))
          $grade = "";
      }
    }
    if (preg_match("/\bnon\b/i", $grade))
      $grade = "";
    
    $mkjPeriode = empty($grade) ? "" : $yMKJ." thn ".$mMKJ." bln";    

      $pdf->Row(array("SG\tb","".$arrMaster[$r[skala]]."\t","MKJ PERIODE\tb","".$mkjPeriode."\t"));

      


    

    // $pdf->Cell(35,6,'JABATAN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,strtoupper($r[pos_name]),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[leave_date] = !empty($r_[leave_date]) ? $r_[leave_date] : "current";
    // $r_[masaKerjaEfektif] = $r_[join_date]." - ".$r_[leave_date];
    // $pdf->Cell(5,6,$r_[masaKerjaEfektif],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7); 

    // $pdf->Cell(35,6,'UNIT KERJA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKE PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[leave_date] = !empty($r_[leave_date]) ? substr($r_[leave_date], 0, 4) : "current";
   //   $r[mkePeriode] = substr($r_[join_date], 0, 4)." - ".$r_[leave_date];
    // $pdf->Cell(5,6,$r[mkePeriode],0,0);
    // $pdf->SetFont('Arial','',8);
    
    // $pdf->Ln(7); 

    // $pdf->Cell(35,6,'GRADE / TJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[grade]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[end_date] = !empty($r[end_date]) ? $r[end_date] : "current";
   //   $r[tglMasaKerjaJabatan] = $r[start_date]." - ".$r_[end_date];
    // $pdf->Cell(5,6,$r[tglMasaKerjaJabatan],0,0);
    // $pdf->SetFont('Arial','',8);
      
    // $pdf->Ln(7); 

    // $pdf->Cell(35,6,'MKJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[end_date] = !empty($r[end_date]) ? $r[end_date] : "current";
   //   $r[tglMasaKerjaJabatan] = $r[start_date]." - ".$r_[end_date];
    // $pdf->Cell(5,6,$r[tglMasaKerjaJabatan],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKJ PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[end_date] = !empty($r[end_date]) ? substr($r[end_date],0,4) : "current";
   //  $r[mkjPeriode] = substr($r[start_date],0,4)." - ".$r_[end_date];
    // $pdf->Cell(5,6,$r[mkjPeriode],0,0);
    // $pdf->SetFont('Arial','',8);
      
    // $pdf->Ln(7); 

  
    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT MENJABAT',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'TANGGAL GRADE TJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT MENJABAT PER',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'GRADE TJ PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7); 

    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT SG',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'TANGGAL PENSIUN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT SG PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    $pdf->Ln(0);  

    

    
    

    // }

    $pdf->AddPage();

    

    //POSISI

    $posisiexist = getField("select count(id) from emp_phist where parent_id = '$par[id]'");
    // if($posisiexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT JABATAN\tb"));
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,25,45,70,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR SK\tb","TGL EFEKTIF\tb","POSISI\tb","KATEGORI\tb"));
  $pdf->SetWidths(array(10,25,45,70,30));
    $pdf->SetAligns(array('L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_phist where parent_id='$par[id]' order by sk_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
    $no = $no.".";
    $r[end_date] = !empty($r[end_date]) ? formatDate($r[end_date]) : "current";
    $r[tglEfektif] = formatDate($r[start_date])." - ".$r[end_date];
       $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[tglEfektif]."\tu",$r[pos_name]."\tu",$arrMaster[$r[kategori_id]]."\tu"));
       // $total += $r[nilai];
  }

  $pdf->Ln();
  //KARIR

  $karirexist = getField("select count(id) from emp_career where parent_id = '$par[id]'");
  // if($karirexist!=0){

  $pdf->SetWidths(array(180));
    $pdf->SetAligns(array('L'));
    $pdf->SetFont('Arial','',10);
  $pdf->Row(array("RIWAYAT TUGAS\tb")); 
  $pdf->SetFont('Arial','',8);

  $pdf->SetWidths(array(10,30,30,80,30));
$pdf->SetAligns(array('L','L','L','L','L','L'));

$pdf->Row(array("NO.\tb","NOMOR SK\tb","TANGGAL\tb","PERIHAL\tb","TIPE\tb",));


  if($karirexist!=0){
    $pdf->SetWidths(array(10,30,30,80,30));
$pdf->SetAligns(array('L','L','L','L','L','L'));
  $pdf->SetFont('Arial','',8);
$sql = "select * from emp_career where parent_id='$par[id]' order by sk_date desc";
$res=db($sql);
$no=0;
while ($r=mysql_fetch_array($res)) {
  $no++;
  $no = $no.".";
   $pdf->Row(array($no."\tu",$r[sk_no]."\tu",formatDate($r[sk_date])."\tu",$r[sk_subject]."\tu",$arrMaster[$r[sk_type]]."\tu"));
   // $total += $r[nilai];
}
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"."\tu"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1860' order by idKategori,createDate asc ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PENJENJANGAN\tb"));  
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,40,105,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","PENJENJANGAN\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,40,105,25));
    $pdf->SetAligns(array('L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1860' order by idKategori,createDate asc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1861' order by idKategori,createDate asc ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT MODUL\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","MODUL\tb","SERTIFIKASI\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1861' order by idKategori,createDate asc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$arrMaster[$r[idKategori]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1862' order by idKategori,createDate asc ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("SERTIFIKASI PROFESI\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","SERTIFIKASI/GELAR\tb","SERTIFIKASI\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1862' order by idKategori,createDate asc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$arrMaster[$r[idKategori]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_training where parent_id = '$par[id]' ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PELATIHAN\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,90,30,30,20));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","PERIHAL\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,90,30,30,20));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_training where parent_id='$par[id]' order by trn_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[trn_subject]."\tu",$arrMaster[$r[trn_cat]]."\tu",$arrMaster[$r[trn_type]]."\tu",$r[trn_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
  
    
    // }
    $pdf->Ln();

    //PENGHARGAAN

    $rewardexist = getField("select count(id) from emp_reward where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PENGHARGAAN\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,45,30,30,25,20,20));
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","NOMOR PENGHARGAAN\tb","PERIHAL\tb","PENERBIT\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
  if($rewardexist!=0){
    $pdf->SetWidths(array(10,45,30,30,25,20,20));
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_reward where parent_id='$par[id]' order by rwd_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[rwd_no]."\tu",$r[rwd_subject]."\tu",$r[rwd_agency]."\tu",$arrMaster[$r[rwd_cat]]."\tu",$arrMaster[$r[rwd_type]]."\tu",$r[rwd_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
  
    // }

//PENDIDIKAN

    $eduexist = getField("select count(id) from emp_edu where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PENDIDIKAN\tb"));  

    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,30,50,35,30,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","TINGKATAN\tb","NAMA LEMBAGA\tb","JURUSAN\tb","KOTA\tb","TAHUN\tb"));
  if($eduexist!=0){
    $pdf->SetWidths(array(10,30,50,35,30,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_edu where parent_id='$par[id]' order by edu_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[edu_type]]."\tu",$r[edu_name]."\tu",$arrMaster[$r[edu_dept]]."\tu",$arrMaster[$r[edu_city]]."\tu",$r[edu_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
  


//KESEHATAN

    $healthexist = getField("select count(id) from emp_health where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT KESEHATAN\tb")); 
    // $pdf->Ln(15);  
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,30,50,40,50));
    $pdf->SetAligns(array('L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","TANGGAL\tb","NAMA TEMPAT\tb","DOKTER\tb","KETERANGAN\tb"));
  if($healthexist!=0){
    $pdf->SetWidths(array(10,30,50,40,50));
    $pdf->SetAligns(array('L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_health where parent_id='$par[id]' order by hlt_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",getTanggal($r[hlt_date])."\tu",$r[hlt_place]."\tu",$r[hlt_doctor]."\tu",$r[hlt_remark]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    
    // }

    $pdf->Ln();


$familyexist = getField("select count(id) from emp_family where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("KELUARGA\tb"));    
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,80,30,60));
    $pdf->SetAligns(array('L','L','L'));

    $pdf->Row(array("NO.\tb","NAMA\tb","HUBUNGAN\tb","TTL\tb"));
    if($familyexist!=0){
    $pdf->SetWidths(array(10, 80, 30, 60));
    $pdf->SetAligns(array('L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_family where parent_id='$par[id]' order by birth_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[name]."\tu",$arrMaster[$r[rel]]."\tu",$arrMaster[$r[birth_place]]." ".getTanggal($r[birth_date])."\tu"));
       $total += $r[nilai];
    }
  }else{
      $pdf->SetWidths(array(180));
    $pdf->SetAligns(array('C'));
      $pdf->Row(array("-- data kosong --"));
    }
    
    // }

    $pdf->Ln();
    $pdf->AddPage();
  

$kontakexist = getField("select count(id) from emp_contact where parent_id = '$par[id]' ");
    // if($kontakexist!=0){
      // $pdf->AddPage();

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("KONTAK\tb"));
    $pdf->SetFont('Arial','',8);

    
    $sql="select * from emp_contact where parent_id = '$par[id]' order by cre_date desc";
    $res=db($sql);
    $r=mysql_fetch_array($res);

    $pdf->SetWidths(array(90,90));
      $pdf->SetAligns(array('L'));

    $pdf->Row(array("SERUMAH\tb","BEDA RUMAH\tb"));     
     
    $pdf->SetWidths(array(45,45,45,45));
      $pdf->SetAligns(array('L'));
      $pdf->Row(array("NAMA\tb","".strtoupper($r[sr_nama])."\t","NAMA\tb","".strtoupper($r[br_nama])."\t"));
      $pdf->Row(array("HUBUNGAN\tb","".$r[sr_hub]."\t","HUBUNGAN\tb","".$r[br_hub]."\t"));
      $pdf->Row(array("NO. TELP\tb","".$r[sr_phone]."\t","NO. TELP\tb","".$r[br_phone]."\t"));
      $pdf->Row(array("ALAMAT\tb","".$r[sr_address]."\t","ALAMAT\tb","".$r[br_address]."\t"));
      $pdf->Row(array("PROVINSI\tb","".$arrMaster[$r[sr_prov]]."\t","PROVINSI\tb","".$arrMaster[$r[br_prov]]."\t"));
      $pdf->Row(array("KAB/KOTA\tb","".$arrMaster[$r[sr_city]]."\t","KAB/KOTA\tb","".$arrMaster[$r[br_city]]."\t"));
   //    $pdf->Ln(7);
    // $pdf->Cell(35,6,'NAMA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,strtoupper($r[sr_nama]),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NAMA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,strtoupper($r[br_nama]),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    
    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[sr_hub],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[br_hub],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // $pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[sr_phone],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[br_phone],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);


    // $pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[sr_address],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[br_address],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[sr_prov]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[br_prov]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // $pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[sr_city]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[br_city]],0,0);
    // $pdf->SetFont('Arial','',8);
    $pdf->Ln();
    

//PERINGATAN

    $punishexist = getField("select count(id) from emp_punish where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PERINGATAN\tb"));  
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","NOMOR PERINGATAN\tb","PERIHAL\tb","PENERBIT\tb","TIPE\tb","TAHUN\tb"));
  if($punishexist!=0){
    $pdf->SetWidths(array(10,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_punish where parent_id='$par[id]' order by pnh_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[pnh_no]."\tu",$r[pnh_subject]."\tu",$r[pnh_agency]."\tu",$arrMaster[$r[pnh_type]]."\tu",$r[pnh_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
    
//ASET

    $healthexist = getField("select count(id) from emp_asset where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("PINJAMAN ASET\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,30,30,30,50,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","ASET\tb","NO. SERI\tb","KATEGORI\tb","TIPE\tb","TANGGAL\tb"));
  if($healthexist!=0){
    $pdf->SetWidths(array(10,30,30,30,50,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_asset where parent_id='$par[id]' order ast_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[ast_name]."\tu",$r[ast_no]."\tu",$r[ast_usage]."\tu",$arrMaster[$r[ast_type]]."\tu",getTanggal($r[ast_date])."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
    
//    //KONTRAK

//    $kontrakexist = getField("select count(id) from emp_pcontract where parent_id = '$par[id]'");

//    // if($kontrakexist!=0){
//      // $pdf->AddPage();

//    $pdf->SetFont('Arial','B',13);
//    $pdf->setFillColor(0,0,0);
//    $pdf->SetTextColor(255,255,255);
//    $pdf->Cell(180,8,'RIWAYAT KONTRAK',0,0,'C','#000000');
//    $pdf->SetTextColor(0,0,0);        
//    $pdf->setFillColor(230,230,230);
//    $pdf->Ln(15); 
//    $pdf->SetFont('Arial','',8);

//    $pdf->SetWidths(array(10,30,50,30,30,30,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//  $pdf->Row(array("NO.\tb","NOMOR SK\tb","PERIHAL\tb","TGL SK\tb","TGL BERLAKU\tb","TGL BERAKHIR\tb"));
//  if($kontrakexist!=0){
//      $pdf->SetWidths(array(10,30,50,30,30,30,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//    $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_pcontract where parent_id='$par[id]'";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[subject]."\tu",$r[sk_date]."\tu",$r[start_date]."\tu",$r[end_date]."\tu"));
//        // $total += $r[nilai];
//  }
// }else{
//  $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//  $pdf->Row(array("-- data kosong --"));
// }
    
//    // }

//    $pdf->Ln();
//    $pdf->Ln();

    

    

    


    

//    $bankexist = getField("select count(id) from emp_bank where parent_id = '$par[id]'");
//    // if($bankexist!=0){

//    $pdf->SetFont('Arial','B',13);
//    $pdf->setFillColor(0,0,0);
//    $pdf->SetTextColor(255,255,255);
//    $pdf->Cell(180,8,'REKENING BANK',0,0,'C','#000000');
//    $pdf->SetTextColor(0,0,0);        
//    $pdf->setFillColor(230,230,230);
//    $pdf->Ln(15); 
//    $pdf->SetFont('Arial','',8);

//    $pdf->SetWidths(array(10,60,30,30,50));
//     $pdf->SetAligns(array('L','L','L','L','L'));

//  $pdf->Row(array("NO.\tb","NAMA BANK\tb","NO REKENING\tb","CABANG\tb","REMARK\tb"));
//  if($bankexist!=0){
//     $pdf->SetWidths(array(10,60,30,30,50));
//     $pdf->SetAligns(array('L','L','L','L','L'));
//    $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_bank where parent_id='$par[id]'";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$arrMaster[$r[bank_id]]."\tu",$r[branch]."\tu",$r[account_no]."\tu",$r[remark]."\tu"));
//        $total += $r[nilai];
//  }
// }else{
//  $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//  $pdf->Row(array("-- data kosong --"));
// }
    
//    // }

//    $pdf->Ln();
//    $pdf->Ln();


    

    

    

    //KERJA

//    $workexist = getField("select count(id) from emp_pwork where parent_id = '$par[id]'");
    

//    $pdf->SetFont('Arial','B',13);
//    $pdf->setFillColor(0,0,0);
//    $pdf->SetTextColor(255,255,255);
//    $pdf->Cell(180,8,'RIWAYAT KERJA',0,0,'C','#000000');
//    $pdf->SetTextColor(0,0,0);        
//    $pdf->setFillColor(230,230,230);
//    $pdf->Ln(15); 
//    $pdf->SetFont('Arial','',8);

//    $pdf->SetWidths(array(10,60,50,35,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//  $pdf->Row(array("NO.\tb","PERUSAHAAN\tb","JABATAN\tb","BAGIAN\tb","TAHUN\tb"));
//  if($workexist!=0){
//     $pdf->SetWidths(array(10,60,50,35,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//    $pdf->SetFont('Arial','',8);

//     $sql = "select *,concat(year(start_date),' - ',year(end_date)) as edu_years from emp_pwork where parent_id='$par[id]'";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[company_name]."\tu",$r[position]."\tu",$r[dept]."\tu",$r[edu_years]."\tu"));
//        // $total += $r[nilai];
//  }
// }else{
//  $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//  $pdf->Row(array("-- data kosong --"));
// }
    
//    // }

//    $pdf->Ln();
//    $pdf->Ln();

    

    

    //REKENING

  

    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'PENGALAMAN PRIBADI',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);
    // $sql_="select *,concat(year(start_date),' - ',year(end_date))  dtRange from emp_pwork where parent_id = '$par[id]'";
    // $res_=db($sql_);
    // while($r_=mysql_fetch_array($res_)){ 
    // $pdf->Cell(40,6,$r_[dtRange],0,0,'L');
    // $pdf->Cell(5,6,$r_[position].' pada '.$r_[company_name],0,0,'L');
    
    // $pdf->Ln(5);   
    // }
    // $pdf->Ln(10);    
    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'PENDIDIKAN FORMAL',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);
    // $sql_="select * from emp_edu where parent_id = '$par[id]'";
    // $res_=db($sql_);
    // while($r_=mysql_fetch_array($res_)){ 
    // $pdf->Cell(40,6,'Lulus Tahun '.$r_[edu_year],0,0,'L');
    // $pdf->Cell(5,6,$r_[edu_name],0,0,'L');
    // $pdf->Ln(5);   
    // }
    // $pdf->Ln(10);    

    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'PRESTASI',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);

    // $sql_="select * from emp_reward where parent_id = '$par[id]'";
    // $res_=db($sql_);
    // while($r_=mysql_fetch_array($res_)){ 
    // $pdf->Cell(40,6,$arrMaster[$r_[rwd_type]],0,0,'L');
    // $pdf->Cell(5,6,$r_[rwd_subject],0,0,'L');
    // $pdf->Ln(5);   
    // }

    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'KEAHLIAN KHUSUS',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(180,6,$r__[abilities],0,0,'L');
    // $pdf->Ln(10);

    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'ORGANISASI SOSIAL',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(180,6,$r__[organization],0,0,'L');
      
    
    
    
    $pdf->Output(); 
  }

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){			
        case "anakan":
        $text = anakan();
        break;
        case "printCV":
        $text = pdf();
        break;
        default:
		$text = empty($_submit) ? form() : ubah();
		break;
	}
	return $text;
}	
?>