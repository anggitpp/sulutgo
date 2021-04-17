<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	

$_SESSION["entity_id"] = "";
$_SESSION["curr_emp_id"] = (isset($_GET["empid"]) ? $_GET["empid"] : $_SESSION["curr_emp_id"] );
if (empty($_SESSION["curr_emp_id"])) {
    echo 
  "<script>
    alert(\"Silakan memilih Pegawai terlebih dahulu...\");
    window.location.href=\"".APP_URL . "/?c=3&p=8&m=79&s=82\";
  </script>";
//  header("Location: " . APP_URL . "/index.php?c=3&p=8&m=79&s=82");
}
function tambah(){

	global $s, $inp, $par, $cUsername, $arrProduk, $cUsername;
	
	repField();
		$inp[start_date] = setTanggal($inp[start_date]);
		$idPegawai = $par[idPegawai];
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
			// echo $sql;
		}else{
			$idPokok = getField("select idPokok from pay_pokok order by idPokok desc limit 1")+1;
			$sql="insert into pay_pokok (idPokok, idPegawai, nomorPokok, tanggalPokok, nilaiPokok, keteranganPokok, createBy, createTime) values ('$idPokok', '$idPegawai', '$nomorPokok', '$tanggalPokok', '$nilaiPokok', '$keteranganPokok', '$cUsername', '".date("Y-m-d H:i:s")."')";
			// echo $sql;
		}
		
		// die();	
		if($nilaiPokok > 0)
			db($sql);

	$id = getField("select id from emp_phist order by id desc limit 1") + 1;
	$inp[sk_date] = setTanggal($inp[sk_date]);
	
	$inp[end_date] = setTanggal($inp[end_date]);

	$sql = "insert into emp_phist (id, parent_id, pos_name, sk_no, sk_date, rank, grade, start_date, end_date, region, location, status, kategori_id, remark, dir_id, div_id, dept_id, unit_id, prov_id, city_id, laporan_id, leader_id, administration_id, replacement_id, replacement2_id, lembur, payroll_id, shift_id, skala,  cre_date, cre_by) values ('$id', '$inp[parent_id]', '$inp[pos_name]', '$inp[sk_no]', '$inp[sk_date]', '$inp[rank]', '$inp[grade]', '$inp[start_date]', '$inp[end_date]', '$inp[region]', '$inp[location]', '$inp[status]', '$inp[kategori_id]', '$inp[remark]', '$inp[dir_id]', '$inp[div_id]', '$inp[dept_id]', '$inp[unit_id]', '$inp[prov_id]', '$inp[city_id]', '$inp[laporan_id]', '$inp[leader_id]', '$inp[administration_id]', '$inp[replacement_id]', '$inp[replacement2_id]', '$inp[lembur]', '$inp[payroll_id]', '$inp[shift_id]','$inp[skala]', '".date('Y-m-d H:i:s')."', '$cUsername')";
	
	db($sql);

	if($inp[status] == 1){
		$sql = "update emp_phist set status = '0' where parent_id = '$inp[parent_id]' AND id != '$id'";
		db($sql);
	}

	
	echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah(){

	global $s, $inp, $par, $cUsername, $arrProduk;
	repField();
	
	$inp[start_date] = setTanggal($inp[start_date]);
	$idPegawai = $inp[parent_id];
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
		// echo $sql;
	}else{
		$idPokok = getField("select idPokok from pay_pokok order by idPokok desc limit 1")+1;
		$sql="insert into pay_pokok (idPokok, idPegawai, nomorPokok, tanggalPokok, nilaiPokok, keteranganPokok, createBy, createTime) values ('$idPokok', '$idPegawai', '$nomorPokok', '$tanggalPokok', '$nilaiPokok', '$keteranganPokok', '$cUsername', '".date("Y-m-d H:i:s")."')";
		// echo $sql;
	}
	
	// die();	
	if($nilaiPokok > 0)
		db($sql);
	
	$inp[sk_date] = setTanggal($inp[sk_date]);
	$inp[end_date] = setTanggal($inp[end_date]);

	$sql = "update emp_phist set pos_name='$inp[pos_name]',sk_no='$inp[sk_no]', sk_date='$inp[sk_date]', rank='$inp[rank]', grade='$inp[grade]', start_date='$inp[start_date]', end_date='$inp[end_date]', region='$inp[region]', location='$inp[location]', status='$inp[status]', kategori_id='$inp[kategori_id]', remark='$inp[remark]', dir_id='$inp[dir_id]', div_id='$inp[div_id]', dept_id='$inp[dept_id]', unit_id='$inp[unit_id]', prov_id='$inp[prov_id]', city_id='$inp[city_id]', laporan_id='$inp[laporan_id]', leader_id='$inp[leader_id]', administration_id='$inp[administration_id]', replacement_id='$inp[replacement_id]', replacement2_id='$inp[replacement2_id]',lembur='$inp[lembur]', payroll_id='$inp[payroll_id]', shift_id='$inp[shift_id]',upd_by = '$cUsername', upd_date = '".date('Y-m-d H:i:s')."' where id='$par[id]'";
	db($sql);

	if($inp[status] == 1){
		$sql = "update emp_phist set status = '0' where parent_id = '$inp[parent_id]' AND id != '$par[id]'";
		db($sql);
	}

	echo "<script>alert('UPDATE DATA BERHASIL')</script>";
	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
	
}

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck,$arrParam;
	$htemp = new Emp();
$htemp->id = $_SESSION["curr_emp_id"];
$htemps = $htemp->getByIdHeader();
foreach ($htemps as $htemp) {
  $htemp = $htemp;
}

$cutil = new Common();
$ui = new UIHelper();
	$par[idPegawai] = $_SESSION["curr_emp_id"];
	if(empty($par[tahunJalan])) $par[tahunJalan]=date('Y');		
	$text.="
	<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."
	<span class=\"pagedesc\">&nbsp;</span>
	</div>";
	$par[kodeData] = $par[kodeData] == 0 ? 1 : $par[kodeData];
		
	$text.="<div id=\"contentwrapper\" class=\"contentwrapper\">
	
	<form action=\"\" method=\"post\" class=\"stdform\" id=\"form\">
	<table style=\"width: 100%;margin-top:10px;\">
    <tr>
      <td rowspan=\"3\" style=\"width: 10%; padding-left: 10px; padding-right: 10px; padding-top: 5px;\">
        <img alt=\"".$htemp["regNo"]."\" width=\"100%\" height=\"100px\" src=\"files/emp/pic/" . ($htemp["picFilename"] == "" ? "nophoto.jpg" : $htemp["picFilename"])."\" class=\"pasphoto\">
      </td>
      <td style=\"width: 40%;vertical-align: top; padding-left: 5px; padding-right: 10px;\">";
        
        $text.= $ui->createPLabelSpanDisplay('Nama ', $htemp['name']);
        $text.= $ui->createPLabelSpanDisplay('NPP', $htemp['regNo']);
        $text.= $ui->createPLabelSpanDisplay('Jabatan', $htemp['jabatan']);
        $text.="
      </td>
      <td style=\"width: 40%;vertical-align: top; padding-top:8px; padding-left: 5px; padding-right: 10px;\">
        <p>&nbsp;<br/></p>";
        $text.= $ui->createPLabelSpanDisplay("Status", $htemp["category"]);
        $text.= $ui->createPLabelSpanDisplay("Unit", $htemp["unit"]);
        $text.="

      </td>
    </tr>    
  </table>
	";
	
	// $text.= include './tmpl/__emp_header__.php';
	$text.="	
	
	</form>
	<div id=\"pos_r\" >
        ";
        if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
	    $text.="</td>
    </div>
	<br clear=\"all\"/>
	
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
	<thead>
	<tr>
	<th  width=\"20\">No.</th>
	<th  width=\"*\">Posisi</th>
	<th  width=\"120\">Jabatan</th>
	<th  width=\"120\">Lokasi</th>
	<th  width=\"100\">Tahun</th>
	<th  width=\"100\">Nomor SK</th>
	<th  width=\"50\">Filename</th>";

	if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
	$text.="<th  width=\"50\">Kontrol</th>";
	}
	
	$text.="
	</tr>
	</thead>
	<tbody>";

$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
$sql="select *, year(start_date) as tahunMulai, year(end_date) as tahunSelesai from emp_phist where parent_id = '$par[idPegawai]'";
// echo $sql;
$res=db($sql);
while($r=mysql_fetch_array($res)){			
	$r[tahunSelesai] = empty($r[tahunSelesai]) || $r[tahunSelesai] == "0000" ? "current" : $r[tahunSelesai];
	$r[periode] = $r[tahunMulai]." - ".$r[tahunSelesai];
	$r[status] = $r[status] == 1 ? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">";
	$no++;
	$text.="
	<td>$no.</td>
	<td>$r[pos_name]</td>
	<td align=\"left\">".$arrMaster[$r[rank]]."</td>
	<td align=\"left\">".$arrMaster[$r[location]]."</td>
	<td align=\"center\">".$r[periode]."</td>
	<td align=\"center\">".$r[sk_no]."</td>
	<td align=\"left\">-</td>
	";
	if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
		$text.="<td align=\"center\">";
			if(isset($menuAccess[$s]["edit"]))
				$text.="<a href=\"?par[mode]=edit&par[id]=$r[id]&par[idPegawai]=$r[parent_id]".getPar($par,"mode,id")."'\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";	
			if(isset($menuAccess[$s]["delete"]))
				$text.= "<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
		$text.="</td>";
	}
	$text.="
	</tr>";				
}	

$text.="</tbody>
</table>

</div><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";


return $text;
}	

function form(){

	global $s,$inp,$par,$fFile,$arrTitle,$menuAccess, $dFile, $tab, $cUsername, $kodeGroup, $Member, $member_id, $arrParameter;

	$sql="select * from emp_phist where id='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$sql_="select t1.id, t1.name, t2.rank, t2.pos_name, t1.reg_no, t1.cat, t1.join_date from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') where t1.id='$par[idPegawai]'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);		

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");						

	$true =  $r[status] == "1" ? "checked=\"checked\"" : "";
	$false =  empty($true) ? "checked=\"checked\"" : "";

	$tlembur =  $r[lembur] == "1" ? "checked=\"checked\"" : "";
	$flembur =  empty($tlembur) ? "checked=\"checked\"" : "";

	$female =  $r[gender] == "p" ? "checked=\"checked\"" : "";
	$male =  empty($female) ? "checked=\"checked\"" : "";
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."	
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div style=\"top:10px; right:25px; position:absolute\">
				<input type=\"hidden\" id=\"inp[tab]\" name=\"inp[tab]\" class=\"mediuminput\" value=\"$tab\" style=\"width:395px;\" maxlength=\"125\"/>					
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location='?" . getPar($par, "mode, id") . "';\"/>
			</div>
			<fieldset>
			<style>
			#inp_prov_id__chosen, #inp_city_id__chosen, #inp_laporan_id__chosen, #inp_leader_id__chosen, #inp_administration_id__chosen, #inp_replacement_id__chosen, #inp_replacement2_id__chosen, #inp_payroll_id__chosen, #inp_shift_id__chosen {
			min-width: 500px;
			}
			</style>
      <table style=\"width:100%\">
      <tr>
      <td style=\"width:50%\">
			<p>
			<label class=\"l-input-small2\">Nama</label>
			<span class=\"field\" id =\"pangkat\">".$r_[name]."&nbsp;</span>
		</p>
        <p>
          <label class=\"l-input-small2\">Pangkat</label>
          <span class=\"field\" id =\"pangkat\">".$arrMaster[$r_[rank]]."&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small2\">Jabatan</label>
          <span class=\"field\" id =\"jabatan\">".$r_[pos_name]."&nbsp;</span>
        </p>
      </td>
      <td style=\"width:50%\">
        <p>
          <label class=\"l-input-small\">NPP</label>
          <span class=\"field\" id =\"nik\">".$r_[reg_no]."&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">Status</label>
          <span class=\"field\" id =\"cat\">".$arrMaster[$r_[cat]]."&nbsp;</span>
        </p>
        <p>
          <label class=\"l-input-small\">Mulai Kerja</label>
          <span class=\"field\" id =\"mulaiKerja\">".getTanggal($r_[join_date])."&nbsp;</span>
        </p>
      </td>
      </tr>
      </table>
      </fieldset>
			
			<fieldset>
				<legend> DATA JABATAN </legend>
				
					
				
				<table style=\"width:100%\">
				<tr>
				<td style=\"width:50%\">
				<p>
					<label class=\"l-input-small2\">Jabatan</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[pos_name]\" 	 name=\"inp[pos_name]\" class=\"smallinput\" value=\"$r[pos_name]\" />	
					</div>
				</p>
				<p>
				<label class=\"l-input-small2\">Grade / JP</label>
				<div class=\"field\">
					" . comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
					JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
					where t2.kodeCategory='JP' and t1.kodeInduk='".$r_[tipe]."' order by t1.urutanData", "id", "description", "inp[grade]", "----", $r[grade], "onchange=\"getSkala('" . getPar($par, "mode,grade") . "');\"", "225px","chosen-select") . "
				
				</div>
			</p>				
				<p>
					<label class=\"l-input-small2\">Nomor SK</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[sk_no]\" style=\"width:215px;\" name=\"inp[sk_no]\" class=\"smallinput\" value=\"$r[sk_no]\" />	
					</div>
				</p>
				<p>
					<label class=\"l-input-small2\">Mulai</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[start_date]\" name=\"inp[start_date]\"  value=\"".getTanggal($r[start_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small2\">Region</label>
					<div class=\"field\">
						" . comboData("select * from mst_data where kodeCategory='RG' and statusData='t' order by namaData", "kodeData", "namaData", "inp[region]", "----", $r[region], "", "225px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small2\" >Status</label>
					<div class=\"field\">     
						
						<input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"0\" $false /> <span class=\"sradio\">Tidak Aktif</span>
						<input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"1\" $true /> <span class=\"sradio\">Aktif</span>      

					</div>
				</p>
				</td>
				<td style=\"width:50%\">
				<p>
					<label class=\"l-input-small\">Pangkat</label>
					<div class=\"field\">
						" . comboData("select * from mst_data where kodeCategory='".$arrParameter[11]."' and statusData='t' order by namaData", "kodeData", "namaData", "inp[rank]", " ", $r[rank], "", "225px","chosen-select") . "
					
					</div>
				</p>
				<p>
				<label class=\"l-input-small\">Skala</label>
				<div class=\"field\">
					" . comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
					 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
					 where t2.kodeCategory='GTU' AND t1.kodeInduk = '$r[grade]' order by t1.urutanData", "id", "description", "inp[skala]", "----", $r[skala], "", "225px","chosen-select") . "
				
				</div>
			</p>
			
				<p>
					<label class=\"l-input-small\">Tanggal SK</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[sk_date]\" name=\"inp[sk_date]\"  value=\"".getTanggal($r[sk_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Selesai</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[end_date]\" name=\"inp[end_date]\"  value=\"".getTanggal($r[end_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Location</label>
					<div class=\"field\">
						" . comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
					   JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
					   where t2.kodeCategory='RG' order by t1.urutanData", "id", "description", "inp[location]", "----", $r[location], "", "225px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						" . comboData("select * from mst_data where kodeCategory='KP' and statusData='t' order by namaData", "kodeData", "namaData", "inp[kategori_id]", "----", $r[kategori_id], "", "225px","chosen-select") . "
					
					</div>
				</p>
				</td>
				</tr>
				</table>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[remark]\" name=\"inp[remark]\"  rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:60%;\">$r[remark]</textarea>
					</div>
				</p>

				<br clear=\"all\">
				<ul class=\"hornav\">
					<li class=\"current\"><a href=\"#tab_organisasi\">Organisasi</a></li>
					<li><a href=\"#tab_lokasi\">Lokasi</a></li>
					<li><a href=\"#tab_struktur\">Struktur</a></li>
					<li><a href=\"#tab_setting\">Setting</a></li>
				</ul>
				<div id=\"tab_organisasi\" class=\"subcontent\" style=\"margin-top: 0px;\">   
				<p>
					<label class=\"l-input-small\">Direktorat</label>
					<div class=\"field\">
						" . comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X04' order by kodeInduk,urutanData", "id", "description", "inp[dir_id]", "----", $r[dir_id], "onchange=\"getDivisi('" . getPar($par, "mode,dir_id") . "');\"", "500px","chosen-select") . "
					
					</div>
				</p>

				<p>
					<label class=\"l-input-small\">Divisi</label>
					<div class=\"field\">
						" . comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       where t2.kodeCategory='X04' AND t1.kodeInduk = '$r[dir_id]' order by t1.urutanData", "id", "description", "inp[div_id]", "----", $r[div_id], "onchange=\"getDepartemen('" . getPar($par, "mode,div_id") . "');\"", "500px","chosen-select") . "
					
					</div>
				</p>

				<p>
					<label class=\"l-input-small\">Departemen</label>
					<div class=\"field\">
						" . comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       where t3.kodeCategory='X04' AND t1.kodeInduk = '$r[div_id]' order by t1.urutanData", "id", "description", "inp[dept_id]", "----", $r[dept_id], "onchange=\"getSub('" . getPar($par, "mode,dept_id") . "');\"", "500px","chosen-select") . "
					
					</div>
				</p>

				<p>
					<label class=\"l-input-small\">Sub Departemen</label>
					<div class=\"field\">
						" . comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                       JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                       JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
                       where t4.kodeCategory='X04' AND t1.kodeInduk = '$r[dept_id]' order by t1.urutanData", "id", "description", "inp[unit_id]", "----", $r[unit_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				</div>
				<div id=\"tab_lokasi\" class=\"subcontent\" style=\"margin-top: 0px; display:none;\"> 

				<p>
					<label class=\"l-input-small\">Provinsi</label>
					<div class=\"field\">
						" . comboData("select kodeData id, namaData description from mst_data where kodeCategory='".$arrParameter[3]."' AND kodeInduk='1' order by urutanData", "id", "description", "inp[prov_id]", "----", $r[prov_id], "onchange=\"getKota('" . getPar($par, "mode,prov_id") . "');\"", "500px","chosen-select") . "
					
					</div>
				</p>

				<p>
					<label class=\"l-input-small\">Kota</label>
					<div class=\"field\">
						" . comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[4]."' AND kodeInduk = '$r[prov_id]' order by kodeInduk,urutanData", "id", "description", "inp[city_id]", "----", $r[city_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				</div>
				<div id=\"tab_struktur\" class=\"subcontent\" style=\"margin-top: 0px; display:none;\">
				<p>
					<label class=\"l-input-small\">Laporan</label>
					<div class=\"field\">
						" . comboData("select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name", "id", "description", "inp[laporan_id]", "----", $r[laporan_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Atasan</label>
					<div class=\"field\">
						" . comboData("select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name", "id", "description", "inp[leader_id]", "----", $r[leader_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Administrasi</label>
					<div class=\"field\">
						" . comboData("select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name", "id", "description", "inp[administration_id]", "----", $r[administration_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Pengganti</label>
					<div class=\"field\">
						" . comboData("select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name", "id", "description", "inp[replacement_id]", "----", $r[replacement_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Pengganti 2</label>
					<div class=\"field\">
						" . comboData("select id id, concat(reg_no,' - ',name) description from emp WHERE status=535 order by name", "id", "description", "inp[replacement2_id]", "----", $r[replacement2_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				</div>
				<div id=\"tab_setting\" class=\"subcontent\" style=\"margin-top: 0px; display:none;\"> 
				<p>
					<label class=\"l-input-small\" >Hak Lembur</label>
					<div class=\"field\">     
						
						<input type=\"radio\" id=\"inp[lembur]\" name=\"inp[lembur]\" value=\"0\" $flembur /> <span class=\"sradio\">Tidak</span>    
						<input type=\"radio\" id=\"inp[lembur]\" name=\"inp[lembur]\" value=\"1\" $tlembur /> <span class=\"sradio\">Ya</span>   

					</div>
				</p> 
				<p>
					<label class=\"l-input-small\">Jenis Payroll</label>
					<div class=\"field\">
						" . comboData("select idJenis id, namaJenis description from pay_jenis where statusJenis='t' order by namaJenis", "id", "description", "inp[payroll_id]", "----", $r[payroll_id], "", "500px","chosen-select") . "
					
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Shift Kerja</label>
					<div class=\"field\">
						" . comboData("select idShift id, namaShift description from dta_shift where statusShift='t' order by namaShift", "id", "description", "inp[shift_id]", "----", $r[shift_id], "", "500px","chosen-select") . "
					
					</div>
				</p>	
				</div>

				</fieldset>
				
				
		</form>	
	
	</div>
	


	";
	return $text;

}
function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){
		case "edit":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
		break;
		case "add":
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
		break;
		case "del":
		if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
		break;
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>