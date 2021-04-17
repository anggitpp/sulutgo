<?php

if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";


function bagian() {
	global $s, $id, $inp, $par, $arrParameter;
	$data = arrayQuery("select concat(kodeAddress, '\t', namaBagian) from dta_customer_address where kodeCustomer='$par[kodeCustomer]' order by namaBagian");
	// echo $data;
	return implode("\n", $data);
}

function getNumberRep($tipe=''){
  // DP170001
  // $tipe = "DP";
	$bulan = date('m');
	$getlastNumber = getField("SELECT nomor FROM ro_rep WHERE month(creDate) = $bulan AND tipe='1' order by nomor DESC LIMIT 1");
	$str   = (empty($getlastNumber)) ? "000" : substr($getlastNumber,0,3);

	$incNum = str_pad($str + 1, 3, "0", STR_PAD_LEFT);
	$year   = date("Y");
	$month = getRomawi(date("n"));

	return $incNum."/".$tipe."/".$month."/".$year;
}

function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m, $arrParameter,$areaCheck;

	if (empty($par[tahunRep]))
		$par[tahunRep] = date('Y');
	$par[divisi] = isset($par["divisi"]) ? $par["divisi"] : "";

	$sekarang = date("Y-m-d");
	// $_SESSION["appl_year"] = $par[tahunSeleksi];
	// $_SESSION["appl_cvac"] = $par[posisi];

		// $selTypeCount = getField("SELECT COUNT(*) cnt FROM mst_data where kodeCategory='R09' ".$filter."", "cnt");
		// $rSelName = getField("SELECT urutanData, namaData FROM mst_data where kodeCategory='R09' ".$filter." ORDER BY urutanData");
		// $rSelType = getField("SELECT urutanData description FROM mst_data WHERE kodeCategory='R09' ".$filter." ORDER BY urutanData");

		$cols=7;
	
	

	


	$text = table($cols, array(($cols-0),($cols-0),$cols));

		// $text = table($cols, array(5,6,$cols));



	$text.="<div class=\"pageheader\">

	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

	".getBread()."

	<span class=\"pagedesc\">&nbsp;</span>

</div>    

<div id=\"contentwrapper\" class=\"contentwrapper\">

	<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
		<fieldset id=\"fSet\" style=\"padding:10px; border-radius: none; height:60px;\">						
			
			<table>
				<tr>
					<td width=\"80%\">					

						<p>
                  <label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">LOKASI</label>
                  <div class=\"field\" style=\"margin-left:240px;\">
                 ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "kodeData", "namaData", "cLocId", "--LOKASI--", $cLocId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."
                  </div>
                </p>

					</td>
					<td width=\"20%\">
						<input id=\"bView\" type=\"button\" value=\"+\" style=\"font-size: 16px;\" class=\"btn btn_search btn-small\" onclick=\"
						document.getElementById('bView').style.display = 'none';
						document.getElementById('bHide').style.display = 'block';
						document.getElementById('dFilter').style.visibility = 'visible';
						document.getElementById('fSet').style.height = '180px';
						\" />
						<input id=\"bHide\" type=\"button\" value=\"-\" class=\"btn btn_search btn-small\" style=\"display:none;font-size: 16px;\" onclick=\"
						document.getElementById('bView').style.display = 'block';
						document.getElementById('bHide').style.display = 'none';
						document.getElementById('dFilter').style.visibility = 'collapse';
						document.getElementById('fSet').style.height = '60px';
						\" />
					</td>
				</tr>
			</table>


			<div id=\"dFilter\" style=\"visibility:collapse;\">

				 <p>
                <label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39])."</label>
                <div class=\"field\" style=\"margin-left:150px;\">
                  ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "cDivId", "--".strtoupper($arrParameter[39])."--", $cDivId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."
                </div>
              </p>
              <p>
                <label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40])."</label>
                <div class=\"field\" style=\"margin-left:150px;\">
                  ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$cDivId' ORDER BY urutanData", "kodeData", "namaData", "cDeptId", "--".strtoupper($arrParameter[40])."--", $cDeptId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."
                </div>
              </p>
              <p>
                <label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41])."</label>
                <div class=\"field\" style=\"margin-left:150px;\">
                  ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$cDeptId' ORDER BY urutanData", "kodeData", "namaData", "cUnitId", "--".strtoupper($arrParameter[41])."--", $cUnitId, "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select")."
                </div>
              </p>
				
			</div>
		</fieldset>			
	</form>
	<div id=\"pos_r\">
		<a href=\"?par[mode]=xls" . getPar($par, "mode,kodeAktifitas") . "\" class=\"btn btn1 btn_document\" style=\"margin-left:5px;margin-top:10px;\"><span>Export Data</span></a></div>
		
		<br clear=\"all\" />

		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

			<thead>

				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">NAMA</th>
					<th width=\"100\">NPP</th>

					<th width=\"150\">JABATAN</th>
					<th width=\"100\">TANGGAL LAHIR</th>
					<th width=\"150\">TANGGAL SELESAI</th>
					<th width=\"100\">MASA KERJA</th>



				</thead>

				<tbody></tbody>
			</table>

		</div>";

		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=Data Replacement ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}


		return $text;

	}



	function lData(){

		global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
		// global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;	
		if($_GET[json]==1){
			header("Content-type: application/json");
		}

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	// echo $sLimit;
		if (empty($par[tahunRep]))
			$par[tahunRep] = date('Y');

		$filters= " where t1.idOfficer = '".$_SESSION['koor']."' AND t1.tipe ='1' ";

	// if (!empty($_GET['fSearch']))

	// 	$filters.= " and (				

	// lower(t2.namaCustomer) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%' OR 				
	// lower(t3.pos_available) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%' OR 				
	// lower(t4.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'

	// )";




		$arrOrder = array(	

			"t2.namaCustomer",
			"t1.tanggal",
			"t2.namaCustomer",
			"t3.pos_available",
			"t4.name",
			"t4.name",
			"t4.name",
			"",




			);



//       	$arrTahapan = array('36' => '602', '37' => '603', '38' => '604', '39' => '605', '40' => '606', '41' => '607');
// // $arrTahapan[$rd[phaseId]]

// 		// $filters = "and urutanData='".$arrParam[$s]."'";

		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	if (!empty($locId))
      $sql.=" AND t2.location=$locId";
    if (!empty($divId))
      $sql.=" AND t2.div_id=$divId";
    if (!empty($deptId))
      $sql.=" AND t2.dept_id=$deptId";
    if (!empty($unitId))
      $sql.=" AND t2.unit_id=$unitId";

	// if(!empty($_GET['pSearch'])){
	// 	$filters .= " AND t1.idOfficer ='".$_GET['pSearch']."'";
	// }

	// if(!empty($par[tahunKebutuhan])){
	// 	$filters .= " AND year(propose_date) ='$par[tahunKebutuhan]'";
	// }

   //    if($m != '36' AND $m != '41'){
   //      $join = "LEFT JOIN rec_selection_appl t7 ON t7.appl_id=t1.applicant_id AND t7.parent_id=t5.id";
   //      $on = "AND t7.phase_id = ".$arrTahapan[$s]."-1 AND t7.sel_status = '601' ";
   //    }

		$sql = " 
		select *,CONCAT(TIMESTAMPDIFF(YEAR, t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date, CURRENT_DATE ) % 12, ' bln') lamaKerja from emp t1 join emp_phist t2 on t1.id = t2.parent_id where t2.status = '0' 
		$sLimit
		";

// echo $sql;
// echo json_encode($sql);


		// $sql="select * from mt_dokumen t1 join mst_data t2 on (t1.idBagian=t2.kodeData) $sWhere order by $orderBy $sLimit";

		$res=db($sql);



		$json = array(

			"iTotalRecords" => mysql_num_rows($res),

			"iTotalDisplayRecords" => getField("SELECT count(*) FROM ro_rep t1 join dta_customer t2 on t1.mitra = t2.kodeCustomer join rec_plan t3 on t1.posisi = t3.id join emp t4 on t1.idOfficer = t4.id left join dta_customer_address t5 on t2.kodeCustomer = t5.kodeCustomer $filters"),
			// app_user t1 join app_group t2 on (t1.kodeGroup=t2.kodeGroup) $sWhere order by t1.username $sLimit

			"aaData" => array(),

			);







		$no=intval($_GET['iDisplayStart']);

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		while($r=mysql_fetch_array($res)){

			$no++;

			$data=array(

				"<div align=\"center\">".$no.".</div>",				

				"<div align=\"left\">$r[name]</div>",

				"<div align=\"center\">$r[reg_no]</div>",

				"<div align=\"left\">$r[pos_name]</div>",

				"<div align=\"center\">".getTanggal($r[birth_date])."</div>",

				"<div align=\"center\">".getTanggal($r[end_date])."</div>",

				"<div align=\"left\">$r[lamaKerja]</div>",
				

				);





			$json['aaData'][]=$data;


		}

		return json_encode($json);

	}	

	function formPegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID, $areaCheck;
		$par[idPegawai] = $cID;  

		$sql = "select * from ro_rep where id = '$par[id]' " ;
		$res=db($sql);
		$r=mysql_fetch_array($res);		
    // print_r($par);
		$text.="
		<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Daftar Pegawai</h1>
				".getBread()."
				<span class=\"pagedesc\">&nbsp;</span>
			</div>    

			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" enctype=\"multipart/form-data\">

					<fieldset>
						<legend> LOKASI KERJA </legend>
						<table width=\"100%\">
							<tr>
								<td style=\"vertical-align: top\" width=\"50%\">
									<p>
										<label style=\"width:100px;\" class=\"l-input-small\">Mitra</label>
										<div class=\"field\" style=\"margin-left:100px;\" >
											<div style=\"margin-left:30px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaCustomer from dta_customer where kodeCustomer ='$r[mitra]'")."</div>
										</div>
									</p>
									<p>
										<label style=\"width:100px;\" class=\"l-input-small\">Bagian </label>
										<div class=\"field\" style=\"margin-left:100px;\" >
											<div style=\"margin-left:30px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaBagian from dta_customer_address where kodeAddress ='$r[bagian]'")."</div>
										</div>
									</p>
									<p>
										<label style=\"width:100px;\" class=\"l-input-small\">Posisi</label>
										<div class=\"field\" style=\"margin-left:100px;\">
											<div style=\"margin-left:30px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select pos_available from rec_plan where id ='$r[posisi]'")."</div>
										</div>
									</p>
								</td>


							</tr>
						</table>
					</fieldset>

					<p style=\"position: absolute; right: 24px; top: 250px;\">
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"PILIH\" />
					</p>

					<br clear=\"all\" />
					<br clear=\"all\" />
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll_x\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th style=\"min-width:250px;\">Nama</th>
								<th width=\"100\">Pendidikan</th>
								<th style=\"min-width:150px;\">Jurusan</th>                
								<th style=\"min-width:120px;\">Gender</th>
								<th style=\"min-width:100px;\">Usia</th>
								<th width=\"30\">Pilih</th>
							</tr>
						</thead>
						<tbody id=\"chkPersonil\">";

							$filter = "where mitra = '$r[mitra]' and bagian = '$r[bagian]' and posisi = '$r[posisi]' and ro = '$r[idOfficer]' AND t1.id NOT IN (select idPegawai from ro_rep_tad where idRep = '$par[id]')";




// $filter .= " AND t2.location IN ($areaCheck)";

							$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
							$sql="select *,t1.id as idp, CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') applAge from emp t1 join emp_phist t2 on t1.id = t2.parent_id $filter ";
// ECHO $sql;
							$res=db($sql);
							while($r=mysql_fetch_array($res)){
								$no++;
    // $r[gender] = $r[gender] == "F" || "f" ? "Perempuan" : "Laki-Laki";
								$r[gender] = $r[gender] ==  "f" ? "Perempuan" : "Laki-Laki";


								$text.="<tr>
								<td>$no.</td>
								<td>".strtoupper($r[name])."</td>
								<td>".$arrMaster[$r[edu_type]]."</td>
								<td>".$arrMaster[$r[edu_dept]]."</td>
								<td>$r[gender]</td>
								<td>$r[applAge]</td>
								<td align=\"center\"><input type=\"checkbox\" id=\"det_[".$r[idp]."]\" name=\"det_[".$r[idp]."]\" value=\"".$r[idp]."\" onclick=\"getPegawai(this);\" $checked /></td>
								<input type=\"hidden\" name = \"inp[kodeRep]\" value=\"$par[id]\">
							</tr>";
						}  

						$text.="</tbody>
					</table>
				</div>
			</form>
		</div>";
		return $text;
	}

	function lihat_kebutuhan() {
		global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername, $cKodePegawai, $cGroup, $sUser, $dStatus, $dWilayah, $dBulan, $dTahun;

  // if (empty($par[kodeWilayah]))
  //   $par[kodeWilayah] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[79] . "' order by urutanData limit 1");

		$kodeMenu = 547;
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			";
			include 'rencana_view.php';


    // $text.="&nbsp;&nbsp;" . comboData("select * from dta_layanan where isActive=1 order by namaLayanan", "idLayanan", "namaLayanan", "par[filterLayanan]", "Semua", $par[filterLayanan], "onchange=\"document.getElementById('form').submit();\"");
    // if ($dWilayah == 833) {
    //   $text.="&nbsp;&nbsp;" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[79] . "' order by urutanData", "kodeData", "namaData", "par[kodeWilayah]", "", $par[kodeWilayah], "onchange=\"document.getElementById('form').submit();\"");
    // }

//  $text.=comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[79] . "' order by urutanData", "kodeData", "namaData", "par[kodeWilayah]", "", $par[kodeWilayah], "onchange=\"document.getElementById('form').submit();\"")
			$text.="
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\"> 
				<div style=\"position:absolute;top:5px;right:5px;margin-top:50px;margin-right:30px;\">

				</div>

			</form>
			<div id=\"pos_r\">
				<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\" openBox('popup.php?par[mode]=addPegawai" . getPar($par, "mode") . "',1050,580);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Data</span></a>
			</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"150\">NAMA</th>
					<th width=\"70\">PENDIDIKAN</th>
					<th width=\"70\">JURUSAN</th>
					<th width=\"70\">FAKULTAS</th>
					<th width=\"70\">UNIVERSITAS</th>
					<th width=\"50\">GENDER</th>
					<th width=\"75\">USIA</th>
					";           
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th style=\"vertical-align:middle\" width=\"50\">Control</th>";
					$text.="


				</tr>
			</thead>
			<tbody>";

				$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
				$sql="select *,t1.id as idp,t2.edu_type, t2.edu_dept, t2.edu_fac, t2.edu_name,
				CONCAT(TIMESTAMPDIFF(YEAR,  t1.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.birth_date,  CURRENT_DATE ) % 12, ' bln') applAge
				from rec_applicant t1 left join rec_applicant_edu t2 on t1.id = t2.parent_id where t1.idProyek = '$par[idp]' AND tipeKebutuhan = '1'  order by t1.name ";
// echo $sql;
				$res = db($sql);
				while ($r = mysql_fetch_array($res)) {
  // echo $r[kodeAktifitas];
					$no++;
  // $r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Perempuan";
					$r[gender] = $r[gender] ==  "f" ? "Perempuan" : "Laki-Laki";

					$text.="<tr>
					<td>$no.</td>
					<td>$r[name]  </td>
					<td>".$arrMaster[$r[edu_type]]."</td>
					<td>".$arrMaster[$r[edu_dept]]."</td>
					<td>".$arrMaster[$r[edu_fac]]."</td>
					<td>$r[edu_name]</td>
					<td>$r[gender]</td>
					<td>$r[applAge]</td>

					";
					if(isset($menuAccess[$s]["delete"])){
						$text.="<td align=\"center\">";       

						if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=delPeg&par[id]=$r[idp]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						$text.="</td>";
					}
					$text.="

				</tr>";

				$kodeData = $r[kodeData];
			}

			$text.="</tbody>
		</table>
	</div>";
	return $text;
}


function tambahPegawai(){
	global $db,$s,$inp,$par,$det,$det_,$cUsername;       

   // print_r($par); 
   // echo $inp[kodePro];

	if (is_array($det_)) {
		while (list($idPelamar) = each($det_)) { 
      // echo $par[kodeAktifitas];


			// $sql="insert ro_rep_tad set idProyek = '$inp[kodeProyek]', tipeKebutuhan = '3' where id = '$idPelamar'";
   //      // echo $sql;
			// db($sql);
			$id = getField("select id from ro_rep_tad order by id desc limit 1") + 1;
	// $inp[tanggal] = setTanggal($inp[tanggal]);

			$sql = "insert into ro_rep_tad (id, idRep,idPegawai, creDate, creBy) values ('$id', '$inp[kodeRep]','$idPelamar' ,'" . date('Y-m-d H:i:s') . "','$cUsername')";
		// echo $sql;
		// die();

			db($sql);



		}
	}
  // die();
	echo "<script>closeBox();reloadPage();</script>";

}

function hapusPegawai() {
	global $s, $inp, $par, $fFile, $cUsername;

	$sql = "delete from ro_rep_tad where id='$par[idr]'";
	// echo $sql;
	// die();
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode,idr") . "';</script>";
}

function hapus(){
	global $s,$inp,$par,$fFile,$cUsername;


	$sql="delete from ro_rep where id='$par[id]'";
	db($sql);
	$sql="delete from ro_rep_tad where idRep='$par[id]'";
	db($sql);
	
	$idRep = getField("select id from ro_rep_tad_appl where idRep = '$par[id]'");

	$sql="delete from ro_rep_tad_appl_file where parent_id='$idRep'";
	db($sql);
	$sql="delete from ro_rep_tad_appl_aktivitas where parent_id='$idRep'";
	db($sql);
	// echo $sql;
	// die();
	
	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}



function tambah() {
	global $s, $inp, $par, $cUsername, $arrParam;
	// echo $_SESSION['koor'];
	// die();

	$inp[nomor] = getNumberRep("RPL");
	$id = getField("select id from ro_rep order by id desc limit 1") + 1;
	// $inp[tanggal] = setTanggal($inp[tanggal]);

	$sql = "insert into ro_rep (id, nomor, tipe, idOfficer,creDate, creBy) values ('$id', '$inp[nomor]','1','".$_SESSION['koor']."', '" . date('Y-m-d H:i:s') . "','$cUsername')";
		// echo $sql;
		// die();

	db($sql);

	echo "<script>window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function update(){

	global $inp,$par,$cUsername;	

	repField();
	// die();

	$inp[tanggal] = setTanggal($inp[tanggal]);

	$sql="update ro_rep set tanggal='$inp[tanggal]', ket='$inp[ket]', mitra='$inp[mitra]', bagian='$inp[bagian]',
	posisi='$inp[posisi]',updDate = '".date('Y-m-d H:i:s')."', updBy='$cUsername' where id='$par[id]'";
	db($sql);	
		// echo $sql;
		// die();


	

}

function ubah(){
	global $s, $inp, $par, $cUsername, $arrParam;
	
	$inp[tanggal] = setTanggal($inp[tanggal]);

	$sql="update ro_rep set tanggal='$inp[tanggal]',  ket='$inp[ket]', mitra='$inp[mitra]', bagian='$inp[bagian]',
	posisi='$inp[posisi]',updDate = '".date('Y-m-d H:i:s')."', updBy='$cUsername' where id='$par[id]'";
	db($sql);	

	echo "<script>window.location='?par[mode]=edit&par[id]=$par[id]" . getPar($par, "mode") . "';</script>";
}

function updateStatus(){
	global $s,$inp,$par,$cUsername, $det;				
	repField();
	// $fotoUser = fotoUser();
	// $file = uploadFile($par[idf]);
	$inp[apprDate] = setTanggal($inp[apprDate]);
	$sql="update ro_rep set apprBy = '$inp[apprBy]', apprSta = '$inp[apprSta]',  apprDate = '$inp[apprDate]',  apprRemark = '$inp[apprRemark]' where id = '$par[idr]'";
	// echo $sql;
	// die();
	db($sql);

	echo "<script>closeBox();reloadPage();</script>";
}

function formStatus(){
	global $s,$inp,$par,$menuAccess,$fFile,$cUsername;	

	// file_get_contents
	// echo "<script>window.parent.update('".getPar($par,"mode")."');</script>";
	$sql="SELECT * FROM ro_rep WHERE id='$par[idr]'";
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);	

	$r[apprBy] = empty($r[apprBy]) ? $cUsername : $r[apprBy];

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">APPROVAL</h1>
		".getBread(ucwords("Approval"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" enctype=\"multipart/form-data\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\">	
			<p style=\"position:absolute;right:5px;top:5px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
			</p>
			

			<p>           
				<label class=\"l-input-small\" >Approve By </label>
				<div class=\"field\">           
					<input type=\"text\" id=\"inp[apprBy]\" name=\"inp[apprBy]\"  value=\"$r[apprBy]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"30\" readonly/>            
				</div>          
			</p>
			<p>
				<label class=\"l-input-small\">Tanggal</label>
				<div class=\"fieldC\">
					<input type=\"text\" id=\"tp\" name=\"inp[apprDate]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[apprDate]) . "\" class=\"vsmallinput hasDatePicker\"/>
				</div>
			</p>
			<p>
				<label class=\"l-input-small\">Status ".$arrMenu[$m]."</label>
				<span class=\"fieldB\">
					".comboKey("inp[apprSta]", array("","f"=>"Ditolak", "t"=>"Disetujui"), $r[apprSta])."
				</span>
			</p>
			<p>
				<label class=\"l-input-small\">Keterangan</label>
				<span class=\"fieldB\">
					<textarea style=\"width: 200px;\" id=\"inp[apprRemark]\" name=\"inp[apprRemark]\">$r[apprRemark]</textarea>
				</span>
			</p>


			
		</form>	
	</div>";
	return $text;
}


function form(){
	global $s,$db,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess,$cUsername,$sUser,$kodeModul,$sGroup,$m,$areaCheck,$arrParam;
		// $plan = getField("select plan_id from rec_vacancy where id = '$par[cvac]'");
		// $cat = getField("select cat from rec_plan where id = '$plan'");
		// $arrSeleksi = array('602' => 'Panggilan', '603' => 'Psikotest', '604' => 'Wawancara HR', '605' => 'Wawancara User', '606' => 'MCU', '607' => 'Hasil');
		// $arrMenu = array('36' => 'Panggilan', '37' => 'Psikotest', '38' => 'Wawancara HR', '39' => 'Wawancara User', '40' => 'MCU', '41' => 'Hasil');

	$sql="SELECT * FROM ro_rep WHERE id='$par[id]'";
	$r[idOfficer] = empty($r[idOfficer]) ? $_SESSION['koor'] : $r[idOfficer];
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);		


	
	setValidation("is_null","inp[subject]","you must fill Judul Permintaan");
	setValidation("is_null","tp","you must fill Tanggal Pengajuan");		
	setValidation("is_null","tk","you must fill Tanggal Kebutuhan");		
	setValidation("is_null","inp[emp_id]","you must fill NPP - Nama");
	setValidation("is_null","inp[edu_id]","you must fill Pendidikan dari");
	setValidation("is_null","inp[edu_id2]","you must fill Pendidikan sampai");
	setValidation("is_null","inp[pos_available]","you must fill Job Posisi");
	setValidation("is_null","inp[emp_sta]","you must fill Status Pegawai");

	$text = getValidation();

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
	" . getBread(ucwords($mode . " data")) . "
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"formseleksi\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	<p style=\"position:absolute;top:5px;right:5px;\"> 
		<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" />
		<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode") . "';\"/>
	</p>
	<div class=\"widgetbox\">
		<div class=\"title\" style=\"margin-bottom:0px;\"><h3> LOKASI KERJA </h3></div>
	</div>
	<table style=\"width: 100%\">
		<tr>
			<td style=\"width: 80%\">
				<p>           
					<label class=\"l-input-small\" >Nama RO <span class=\"required\">*)</span></label>
					<div class=\"field\">           
						<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaUser from app_user where idPegawai ='$r[idOfficer]'")."</div>      
					</div>          
				</p>
				";
				if(empty($r[mitra])){

					$text.="
					<p>
						<label class=\"l-input-small\">Mitra</label>           
						<div class=\"fieldC\">".comboData("select * from dta_customer where statusCustomer='t' AND idRelation = '$r[idOfficer]' order by namaCustomer", "kodeCustomer", "namaCustomer", "inp[mitra]", " ", $r[mitra],"onchange=\"getBagian('" . getPar($par, "mode,mitra") . "');\"","350px","chosen-select")."</div>

					</p>";
				}else{
					$text.="<p>           
					<label class=\"l-input-small\" >Mitra <span class=\"required\">*)</span></label>
					<div class=\"field\">      
						<input type=\"hidden\" id=\"inp[mitra]\" name=\"inp[mitra]\"  value=\"$r[mitra]\"/>      
						<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaCustomer from dta_customer where kodeCustomer ='$r[mitra]'")."</div>      
					</div>          
				</p>";
			}
			if(empty($r[bagian])){

				$text.="

				<p>
					<label class=\"l-input-small\">Bagian</label>           
					<div class=\"fieldC\">".comboData("select kodeAddress id, namaBagian description from dta_customer_address where kodeCustomer='$r[mitra]' order by namaBagian", "id", "description", "inp[bagian]", " ", $r[bagian],"","350px","chosen-select")."</div>
				</p>";
			}else{
				$text.="<p>           
				<label class=\"l-input-small\" >Bagian</label>
				<div class=\"field\">           
					<input type=\"hidden\" id=\"inp[bagian]\" name=\"inp[bagian]\"  value=\"$r[bagian]\"/> 
					<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaBagian from dta_customer_address where kodeAddress ='$r[bagian]'")."</div>      
				</div>          
			</p>	
			";
		}
		if(empty($r[posisi])){


			$text.="
			<p>
				<label class=\"l-input-small\">Posisi</label>           
				<div class=\"fieldC\">".comboData("select * from rec_plan where cat='3' order by pos_available", "id", "pos_available", "inp[posisi]", " ", $r[posisi],"","350px","chosen-select")."</div>
			</p>";
		}else{
			$text.="
			<p>           
				<label class=\"l-input-small\" >Posisi <span class=\"required\">*)</span></label>
				<div class=\"field\">  
					<input type=\"hidden\" id=\"inp[posisi]\" name=\"inp[posisi]\"  value=\"$r[posisi]\"/>     
					<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select pos_available from rec_plan where id ='$r[posisi]'")."</div>      
				</div>          
			</p>";
		}$text.="



	</td>
	<td style=\"width: 20%\">

	</td>


</tr>
</table>
<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-bottom:0px;\"><h3> OFFICER </h3></div>
</div>
<table style=\"width: 100%\">
	";
	if($par[mode]=="add"){
		$r[nomor]=getNumberRep("RPL");
	}else{
		$r[nomor]=$r[nomor];
	}
	$text.="


	<tr>
		<td style=\"width: 80%\">
			<p>           
				<label class=\"l-input-small\" >Nomor </label>
				<div class=\"field\">           
					<input type=\"text\" id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"$r[nomor]\" class=\"mediuminput\" style=\"width:340px;\" maxlength=\"30\" readonly/>            
				</div>          
			</p>

			<p>
				<label class=\"l-input-small\">Tanggal <span class=\"required\">*)</span></label>
				<div class=\"fieldC\">
					<input type=\"text\" id=\"tp\" name=\"inp[tanggal]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[tanggal]) . "\" class=\"vsmallinput hasDatePicker\"/>
				</div>
			</p>
			<p>           
				<label class=\"l-input-small\" >Alasan</label>
				<div class=\"field\">           
					<input type=\"text\" id=\"inp[ket]\" name=\"inp[ket]\"  value=\"$r[ket]\" class=\"mediuminput\" style=\"width:450px;\" maxlength=\"30\"/>            
				</div>          
			</p>  
		</td>
		<td style=\"width: 20%\">

		</td>

	</tr>
</table>




</form>
<br clear=\"all\"/>
<form id=\"\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
	<div style=\"position:absolute;right:20px;top:445px;\">
		<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('".getPar($par , "mode")."');openBox('popup.php?par[mode]=addPegawai" . getPar($par, "mode") . "',1050,580);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Data</span></a>
	</div>
</form>

<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th width=\"150\">NAMA</th>
			<th width=\"70\">PENDIDIKAN</th>
			<th width=\"70\">JURUSAN</th>
			<th width=\"70\">FAKULTAS</th>
			<th width=\"70\">UNIVERSITAS</th>
			<th width=\"50\">GENDER</th>
			<th width=\"75\">USIA</th>
			";           
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th style=\"vertical-align:middle\" width=\"50\">Control</th>";
			$text.="


		</tr>
	</thead>
	<tbody>";

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select *,t1.id as idr,CONCAT(TIMESTAMPDIFF(YEAR,  t2.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t2.birth_date,  CURRENT_DATE ) % 12, ' bln') applAge from ro_rep_tad t1 join emp t2 on t1.idPegawai = t2.id join emp_edu t3 on t2.id = t3.parent_id where t1.idRep = '$par[id]'  ";
// echo $sql;
		$res = db($sql);
		while ($r = mysql_fetch_array($res)) {
  // echo $r[kodeAktifitas];
			$no++;
  // $r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Perempuan";
			$r[gender] = $r[gender] ==  "f" ? "Perempuan" : "Laki-Laki";

			$text.="<tr>
			<td>$no.</td>
			<td>$r[name] </td>
			<td>".$arrMaster[$r[edu_type]]."</td>
			<td>".$arrMaster[$r[edu_dept]]."</td>
			<td>".$arrMaster[$r[edu_fac]]."</td>
			<td>$r[edu_name]</td>
			<td>$r[gender]</td>
			<td>$r[applAge]</td>
			";
			if(isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";       

				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=delPeg&par[idr]=$r[idr]".getPar($par,"mode,idr")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="



		</tr>";

		$kodeData = $r[kodeData];
	}

	$text.="</tbody>
</table>
</form>

</div>";
return $text;
}

function detail(){
	global $s,$db,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess,$cUsername,$sUser,$kodeModul,$sGroup,$m,$areaCheck,$arrParam;
		// $plan = getField("select plan_id from rec_vacancy where id = '$par[cvac]'");
		// $cat = getField("select cat from rec_plan where id = '$plan'");
		// $arrSeleksi = array('602' => 'Panggilan', '603' => 'Psikotest', '604' => 'Wawancara HR', '605' => 'Wawancara User', '606' => 'MCU', '607' => 'Hasil');
		// $arrMenu = array('36' => 'Panggilan', '37' => 'Psikotest', '38' => 'Wawancara HR', '39' => 'Wawancara User', '40' => 'MCU', '41' => 'Hasil');

	$sql="SELECT * FROM ro_rep WHERE id='$par[id]'";
	$r[idOfficer] = empty($r[idOfficer]) ? $_SESSION['koor'] : $r[idOfficer];
	// echo $sql;
	$res=db($sql);
	$r=mysql_fetch_array($res);		


	
	setValidation("is_null","inp[subject]","you must fill Judul Permintaan");
	setValidation("is_null","tp","you must fill Tanggal Pengajuan");		
	setValidation("is_null","tk","you must fill Tanggal Kebutuhan");		
	setValidation("is_null","inp[emp_id]","you must fill NPP - Nama");
	setValidation("is_null","inp[edu_id]","you must fill Pendidikan dari");
	setValidation("is_null","inp[edu_id2]","you must fill Pendidikan sampai");
	setValidation("is_null","inp[pos_available]","you must fill Job Posisi");
	setValidation("is_null","inp[emp_sta]","you must fill Status Pegawai");

	$text = getValidation();

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
	" . getBread(ucwords($mode . " data")) . "
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"formseleksi\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
		<div class=\"widgetbox\">
			<div class=\"title\" style=\"margin-bottom:0px;\"><h3> LOKASI KERJA </h3></div>
		</div>
		<table style=\"width: 100%\">
			<tr>
				<td style=\"width: 80%\">
					<p>           
						<label class=\"l-input-small\" >Nama RO <span class=\"required\">*)</span></label>
						<div class=\"field\">           
							<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaUser from app_user where idPegawai ='$r[idOfficer]'")."</div>      
						</div>          
					</p>
					";
					if(empty($r[mitra])){

						$text.="
						<p>
							<label class=\"l-input-small\">Mitra</label>           
							<div class=\"fieldC\">".comboData("select * from dta_customer where statusCustomer='t' AND idRelation = '$r[idOfficer]' order by namaCustomer", "kodeCustomer", "namaCustomer", "inp[mitra]", " ", $r[mitra],"onchange=\"getBagian('" . getPar($par, "mode,mitra") . "');\"","350px","chosen-select")."</div>

						</p>";
					}else{
						$text.="<p>           
						<label class=\"l-input-small\" >Mitra <span class=\"required\">*)</span></label>
						<div class=\"field\">      
							<input type=\"hidden\" id=\"inp[mitra]\" name=\"inp[mitra]\"  value=\"$r[mitra]\"/>      
							<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaCustomer from dta_customer where kodeCustomer ='$r[mitra]'")."</div>      
						</div>          
					</p>";
				}
				if(empty($r[bagian])){

					$text.="

					<p>
						<label class=\"l-input-small\">Bagian</label>           
						<div class=\"fieldC\">".comboData("select kodeAddress id, namaBagian description from dta_customer_address where kodeCustomer='$r[mitra]' order by namaBagian", "id", "description", "inp[bagian]", " ", $r[bagian],"","350px","chosen-select")."</div>
					</p>";
				}else{
					$text.="<p>           
					<label class=\"l-input-small\" >Bagian</label>
					<div class=\"field\">           
						<input type=\"hidden\" id=\"inp[bagian]\" name=\"inp[bagian]\"  value=\"$r[bagian]\"/> 
						<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select namaBagian from dta_customer_address where kodeAddress ='$r[bagian]'")."</div>      
					</div>          
				</p>	
				";
			}
			if(empty($r[posisi])){


				$text.="
				<p>
					<label class=\"l-input-small\">Posisi</label>           
					<div class=\"fieldC\">".comboData("select * from rec_plan where cat='3' order by pos_available", "id", "pos_available", "inp[posisi]", " ", $r[posisi],"","350px","chosen-select")."</div>
				</p>";
			}else{
				$text.="
				<p>           
					<label class=\"l-input-small\" >Posisi <span class=\"required\">*)</span></label>
					<div class=\"field\">  
						<input type=\"hidden\" id=\"inp[posisi]\" name=\"inp[posisi]\"  value=\"$r[posisi]\"/>     
						<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getField("select pos_available from rec_plan where id ='$r[posisi]'")."</div>      
					</div>          
				</p>";
			}$text.="



		</td>
		<td style=\"width: 20%\">

		</td>


	</tr>
</table>
<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-bottom:0px;\"><h3> OFFICER </h3></div>
</div>
<table style=\"width: 100%\">
	";
	if($par[mode]=="add"){
		$r[nomor]=getNumberRep("RPL");
	}else{
		$r[nomor]=$r[nomor];
	}
	$text.="


	<tr>
		<td style=\"width: 80%\">
			<p>           
				<label class=\"l-input-small\" >Nomor <span class=\"required\">*)</span></label>
				<div class=\"field\">  

					<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">$r[nomor]</div>      
				</div>          
			</p>

			<p>           
				<label class=\"l-input-small\" >Tanggal</label>
				<div class=\"field\">  

					<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">".getTanggal($r[tanggal])."</div>      
				</div>          
			</p>
			<p>           
				<label class=\"l-input-small\" >Alasan</label>
				<div class=\"field\">  

					<div style=\"margin-left:110px;margin-top:10px;border: 0;outline: 0;background: transparent;border-bottom: 1px solid #ccc; width:300px;\">$r[ket]</div>      
				</div>          
			</p>
		</td>
		<td style=\"width: 20%\">

		</td>

	</tr>
</table>




</form>
<br clear=\"all\"/>
<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-bottom:0px;\"><h3> DATA PEGAWAI </h3></div>
</div>
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll_x\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th width=\"150\">NAMA</th>
			<th width=\"70\">PENDIDIKAN</th>
			<th width=\"70\">JURUSAN</th>
			<th width=\"70\">FAKULTAS</th>
			<th width=\"70\">UNIVERSITAS</th>
			<th width=\"50\">GENDER</th>
			<th width=\"75\">USIA</th>
			

		</tr>
	</thead>
	<tbody>";

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select *,CONCAT(TIMESTAMPDIFF(YEAR,  t2.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t2.birth_date,  CURRENT_DATE ) % 12, ' bln') applAge from ro_rep_tad t1 join emp t2 on t1.idPegawai = t2.id join emp_edu t3 on t2.id = t3.parent_id where t1.idRep = '$par[id]'  ";
// echo $sql;
		$no=0;
		$res = db($sql);
		while ($r = mysql_fetch_array($res)) {
  // echo $r[kodeAktifitas];
			$no++;
  // $r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Perempuan";
			$r[gender] = $r[gender] ==  "f" ? "Perempuan" : "Laki-Laki";

			$text.="<tr>
			<td>$no.</td>
			<td>$r[name]  </td>
			<td>".$arrMaster[$r[edu_type]]."</td>
			<td>".$arrMaster[$r[edu_dept]]."</td>
			<td>".$arrMaster[$r[edu_fac]]."</td>
			<td>$r[edu_name]</td>
			<td>$r[gender]</td>
			<td>$r[applAge]</td>
			



		</tr>";

		$kodeData = $r[kodeData];
	}

	$text.="</tbody>
</table>

<div class=\"widgetbox\">
	<div class=\"title\" style=\"margin-bottom:0px;\"><h3 style=\"margin-top:20px;\"> DATA RECRUITMENT </h3></div>
</div>

<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll_x\">
	<thead>
		<tr>
			<th width=\"20\">No.</th>
			<th width=\"*\">NAMA</th>
			<th width=\"100\">PENDIDIKAN</th>
			<th width=\"100\">JURUSAN</th>
			<th width=\"100\">FAKULTAS</th>
			<th width=\"100\">UNIVERSITAS</th>
			<th width=\"50\">GENDER</th>
			<th width=\"75\">USIA</th>
			
			

		</tr>
	</thead>
	<tbody>";

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select *,t3.id as idp,t1.id as idr,t2.edu_type, t2.edu_dept, t2.edu_fac, t2.edu_name,
		CONCAT(TIMESTAMPDIFF(YEAR,  t3.birth_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t3.birth_date,  CURRENT_DATE ) % 12, ' bln') applAge
		from ro_rep_tad_appl t1 join rec_applicant t3 on t1.idPegawai = t3.id left join rec_applicant_edu t2 on t1.id = t2.parent_id where t1.idRep = '$par[id]' AND t1.tipe = '1' group by t1.idPegawai  order by t3.name ";
// echo $sql;
		$no=0;
		$res = db($sql);
		while ($r = mysql_fetch_array($res)) {
  // echo $r[kodeAktifitas];
			$no++;
  // $r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Perempuan";
			$r[gender] = $r[gender] ==  "f" ? "Perempuan" : "Laki-Laki";

			$text.="<tr>
			<td>$no.</td>
			<td>$r[name]  </td>
			<td>".$arrMaster[$r[edu_type]]."</td>
			<td>".$arrMaster[$r[edu_dept]]."</td>
			<td>".$arrMaster[$r[edu_fac]]."</td>
			<td>$r[edu_name]</td>
			<td>$r[gender]</td>
			<td>$r[applAge]</td>
			

			



		</tr>";

		$kodeData = $r[kodeData];
	}

	$text.="</tbody>
</table>
</form>

</div>";
return $text;
}

function xls(){		
	global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID;
	require_once 'plugins/PHPExcel.php';

	// if(empty($par[tgl1])) $par[tgl1] = date('d/m/Y',strtotime('-30 days'));
	// if(empty($par[tgl2])) $par[tgl2] = date('d/m/Y');

	$sekarang = date('Y-m-d');

	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cName)
	->setLastModifiedBy($cName)
	->setTitle($arrTitle["".$_GET[p].""]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');		
	$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');		
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', "DATA REPLACEMENT");

	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
	$objPHPExcel->getActiveSheet()->setCellValue('B4', "Tanggal");
	$objPHPExcel->getActiveSheet()->setCellValue('C4', "Mitra");
	$objPHPExcel->getActiveSheet()->setCellValue('D4', "Posisi");
	$objPHPExcel->getActiveSheet()->setCellValue('E4', "Pemohon");
	$objPHPExcel->getActiveSheet()->setCellValue('F4', "Jumlah");		
	$objPHPExcel->getActiveSheet()->setCellValue('G4', "Dipenuhi");		

	$rows=5;		
	$reg_no = getField("select reg_no from emp where id = '$par[idPegawai]'");

	$filter = "where year(t1.mulaiLembur)='$par[tahunLembur]' AND month(t1.mulaiLembur)='$par[bulanLembur]'";
	if (isset($menuAccess[$s]["apprlv2"])){
		$filter.="";
	}else{
		$filter.="and (t1.createBy='".$reg_no."' or t1.idAtasan='".$par[idPegawai]."' or t1.idAtasan2='".$par[idPegawai]."')";
	}
	if(!empty($par[filter]))		
		$filter.= " and (
	lower(t1.nomorLembur) like '%".strtolower($par[filter])."%'
	or lower(t2.reg_no) like '%".strtolower($par[filter])."%'	
	or lower(t2.name) like '%".strtolower($par[filter])."%'	
	)";

						// $filter .= " AND t2.location IN ($areaCheck)";

	$sql="select * from att_lembur t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $filter order by t1.nomorLembur";
	$res=db($sql);
						// echo $sql;
						// $arrPegawai = arrayQuery("sle")
	$arrApproval = arrayQuery("select id, name from emp");
	$arrNPP = arrayQuery("select id, reg_no from emp");

	while($r=mysql_fetch_array($res)){			
		$no++;




		$jumahkaryawan = getField("select count(idPegawai) from att_lembur where idGroup = '$r[idGroup]'");
		$persetujuanLembur = $r[persetujuanLembur] == "t"? "Disetujui" : "Belum di Proses";
		$persetujuanLembur = $r[persetujuanLembur] == "f"? "Ditolak" : $persetujuanLembur;
		$persetujuanLembur = $r[persetujuanLembur] == "r"? "Diperbaiki" : $persetujuanLembur;

		$persetujuanLembur2 = $r[persetujuanLembur2] == "t"? "Disetujui" : "Belum di Proses";
		$persetujuanLembur2 = $r[persetujuanLembur2] == "f"? "Ditolak" : $persetujuanLembur2;
		$persetujuanLembur2 = $r[persetujuanLembur2] == "r"? "Diperbaiki" : $persetujuanLembur2;

		$sdmLembur = $r[sdmLembur] == "t"? "Disetujui" : "Belum di Proses";
		$sdmLembur = $r[sdmLembur] == "f"? "Ditolak" : $sdmLembur;
		$sdmLembur = $r[sdmLembur] == "r"? "Diperbaiki" : $sdmLembur;

		list($mulaiLembur_tanggal, $mulaiLembur) = explode(" ", $r[mulaiLembur]);
		list($selesaiLembur_tanggal, $selesaiLembur) = explode(" ", $r[selesaiLembur]);

							// $persetujuanLink = isset($menuAccess[$s]["edit"]) ? "?par[mode]=app&par[idLembur]=$r[idLembur]&par[idGroup]=$r[idGroup]".getPa r($par,"mode,idLembur") : "#";

			// $sdmLink = isset($menuAccess[$s]["apprlv2"]) ? "?par[mode]=sdm&par[idLembur]=$r[idLembur]&par[idGroup]=$r[idGroup]".getPar($par,"mode,idLembur") : "#";
		$durasi = convertMinsToHours(selisihMenit($r[mulaiLembur], $r[selesaiLembur]), '%02d : %02d');
		$jamdurasi = substr($durasi, 0,2);

		$point = (($jamdurasi*2)-0.5)*$jumahkaryawan;

		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			


		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[nomorLembur]);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $arrNPP[$r[idPegawai]]);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, strtoupper($arrApproval[$r[idPegawai]]));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumahkaryawan);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, substr($mulaiLembur,0,5));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, substr($selesaiLembur,0,5));


		$rows++;
	}

	$rows--;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A4:G'.$rows)->getAlignment()->setWrapText(true);						

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

	$objPHPExcel->getActiveSheet()->setTitle("DATA REPLACEMENT");
	$objPHPExcel->setActiveSheetIndex(0);

	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save($fExport."Data Replacement ".$sekarang.".xls");
}

function getContent($par){
	global $s,$_submit,$menuAccess,$cUsername;
	switch($par[mode]){


		case "lst":

		$text=lData();

		break;					
		case "peg":
		$text = pegawai();
		break;
		case "addFile":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formFile() : tambahFile(); else $text = lihat();
		break;
		case "addPegawai":
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formPegawai() : tambahPegawai(); else $text = lihat_kebutuhan();
		break;
		case "edit":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
		break;
		case "delPeg":
		$text = hapusPegawai();
		break;
		case "del":
		$text = hapus();
		break;

		case "lihat_kebutuhan":
		$text =  lihat_kebutuhan(); 
		break;
		case "add":
		$text = tambah(); 
		break;

		case "bagian":
		$text = bagian();
		break;

		case "detail":
		$text = detail();
		break;

		case "updStatus":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formStatus() : updateStatus(); else $text = lihat();
		break;

		case "update":
		$text = update(); 
		break;

		default:
		$sql="select * from ro_rep where (tanggal = '0000-00-00' or tanggal is null) and creBy='$cUsername'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$sql_="delete from ro_rep where id='$r[id]'";
			db($sql_);	
			$sql_="delete from ro_rep_tad where idRep='$r[id]'";
			db($sql_);							
		}
		$text = lihat();
		break;
	}
	return $text;
}	
?>	