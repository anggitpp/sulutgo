<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/upload/";

function delete()
{
	global $s, $inp, $par, $cUsername;

	$filter = "where t1.tanggalAbsen='" . setTanggal($par[tanggalAbsen]) . "'";

	if (!empty($par[idLokasi]))
		$filter .= " and t2.location='" . $par[idLokasi] . "'";

	if ($par[search] == "Nama")
		$filter .= " and lower(t2.name) like '%" . strtolower($par[filter]) . "%'";
	else if ($par[search] == "NIK")
		$filter .= " and lower(t2.reg_no) like '%" . strtolower($par[filter]) . "%'";
	else
		$filter .= " and (
			lower(t2.name) like '%" . strtolower($par[filter]) . "%'
				or lower(t2.reg_no) like '%" . strtolower($par[filter]) . "%'
			)";

	$sql = "delete t1 from att_absen t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) " . $filter;
	db($sql);
	echo "<script>window.location='?" . getPar($par, "mode") . "';</script>";
}

function setTable()
{
	global $s, $inp, $par, $fFile, $cUsername, $arrParameter;
	if (!in_array(strtolower(substr($_FILES[fileData][name], -3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name], -4)), array("xlsx"))) {
		return "file harus dalam format .xls atau .xlsx";
	} else {
		$fileUpload = $_FILES[fileData][tmp_name];
		$fileUpload_name = $_FILES[fileData][name];
		if (($fileUpload != "") and ($fileUpload != "none")) {
			fileUpload($fileUpload, $fileUpload_name, $fFile);
			$fileData = md5($cUsername . "-" . date("Y-m-d H:i:s")) . "." . getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileData);

			db("DROP TABLE IF EXISTS tmp_absen");
			db("CREATE TABLE IF NOT EXISTS tmp_absen (
					  idAbsen int(11) NOT NULL,
					  nikPegawai varchar(30) NOT NULL,
					  tanggalAbsen date NOT NULL,
					  masukAbsen time NOT NULL,
					  pulangAbsen time NOT NULL,
					  keteranganAbsen varchar(150) NOT NULL,
					  statusAbsen char(1) NOT NULL,
					  createBy varchar(30) NOT NULL,
					  createTime datetime NOT NULL,
					  PRIMARY KEY (idAbsen)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

			$inputFileName = $fFile . $fileData;
			require_once('plugins/PHPExcel/IOFactory.php');

			try {
				$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
			} catch (Exception $e) {
				die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
			}

			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow();
			$highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

			// $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			// for ($row = 1; $row <= $highestRow; $row++){ 					
			// 	$rowData = $sheet->rangeToArray('A' . $row . ':F' . $row, NULL, TRUE, TRUE);				
			// 	$rowData[0][1] = setTanggal($rowData[0][1]);				
			// 	$dta = $rowData[0];

			// 	$nikPegawai = trim(str_replace("-","",$dta[1]));			
			// 	$tanggalAbsen = trim($dta[2]);				
			// 	$idPegawai = getField("select id from emp where trim(reg_no)='".$nikPegawai."' and status='".$status."'");

			// 	$sql="delete from att_absen where idPegawai='".$idPegawai."' and tanggalAbsen='".$tanggalAbsen."'";
			// 	if($idPegawai > 0) db($sql);
			// }


			return "fileData" . $fileData;
		}
	}
}

function setData()
{
	global $s, $inp, $par, $fFile, $cUsername, $arrParameter;

	$inputFileName = $fFile . $par[fileData];
	require_once('plugins/PHPExcel/IOFactory.php');

	//  Read your Excel workbook
	try {
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFileName);
	} catch (Exception $e) {
		die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
	}

	//  Get worksheet dimensions
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();

	$result = $par[rowData] . ". ";
	if ($par[rowData] <= $highestRow) {
		$rowData = $sheet->rangeToArray('A' . $par[rowData] . ':F' . $par[rowData], NULL, TRUE, TRUE);
		$dta = $rowData[0];
		$no = 1;
		if (!in_array(trim(strtolower($dta[2])), array("", "Tanggal"))) {
			$no++;
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[6] . "' order by urutanData limit 1");
			$result .= $dta[2];
			$nikPegawai = str_replace(".", "",trim($dta[1]));
			$tanggalAbsen = trim($dta[2]);
			$idPegawai = getField("select id from emp where trim(reg_no)='" . $nikPegawai . "' and status='" . $status . "'");
			$masukAbsen = $sheet->getCell('D' . $par[rowData])->getFormattedValue();
			$pulangAbsen = $sheet->getCell('E' . $par[rowData])->getFormattedValue();

			$statusAbsen = $idPegawai > 0 ? "t" : "f";
			$keteranganAbsen = $idPegawai > 0 ? "OK" : "ID Absen : " . $kodeSetting . " belum terdaftar";

			if ((!empty($masukAbsen) || !empty($pulangAbsen))) {
				$cekExist = getField("select idAbsen from att_absen where idPegawai = '$idPegawai' and tanggalAbsen = '$tanggalAbsen'");
				if (empty($cekExist)) {
					$idAbsen = getField("select idAbsen from att_absen order by idAbsen desc limit 1") + 1;
					$sql = "insert into att_absen (idAbsen, idPegawai, tanggalAbsen, tanggalAbsen_masuk, tanggalAbsen_pulang, masukAbsen, pulangAbsen, createBy, createTime) values ('" . $idAbsen . "', '" . $idPegawai . "', '" . $tanggalAbsen . "', '" . $tanggalAbsen . "', '" . $tanggalAbsen . "', '" . $masukAbsen . "', '" . $pulangAbsen . "', '" . $cUsername . "', '" . date('Y-m-d H:i:s') . "')";
					db($sql);
					$result .= $sql;

					$sql = "update att_absen set durasiAbsen=CASE WHEN pulangAbsen >= masukAbsen THEN TIMEDIFF(pulangAbsen, masukAbsen) ELSE ADDTIME(TIMEDIFF('24:00:00', masukAbsen), TIMEDIFF(pulangAbsen, '00:00:00')) END where idAbsen='" . $idAbsen . "' and masukAbsen!='00:00:00' and pulangAbsen!='00:00:00'";
					db($sql);
				} else {
					$sql = "update att_absen set tanggalAbsen = '$tanggalAbsen', tanggalAbsen_masuk = '$tanggalAbsen', tanggalAbsen_pulang = '$tanggalAbsen', masukAbsen = '$masukAbsen', pulangAbsen = '$pulangAbsen', durasiAbsen=CASE WHEN pulangAbsen >= masukAbsen THEN TIMEDIFF(pulangAbsen, masukAbsen) ELSE ADDTIME(TIMEDIFF('24:00:00', masukAbsen), TIMEDIFF(pulangAbsen, '00:00:00')) END,  updateBy = '$cUsername', updateTime = '" . date('Y-m-d H:i:s') . "' where idAbsen = '$cekExist'";
					db($sql);
					$result .= $sql;
				}

				$result.="masuk ga kosong masuk absen";
			}


			$idAbsen = getField("select idAbsen from tmp_absen order by idAbsen desc limit 1") + 1;
			$sql = "insert into tmp_absen (idAbsen, nikPegawai, tanggalAbsen, masukAbsen, pulangAbsen, keteranganAbsen, statusAbsen, createBy, createTime) values ('" . $idAbsen . "', '" . $nikPegawai . "', '" . $tanggalAbsen . "', '" . $masukAbsen . "', '" . $pulangAbsen . "', '" . $keteranganAbsen . "', '" . $statusAbsen . "', '" . $cUsername . "', '" . date('Y-m-d H:i:s') . "')";
			db($sql);

			// $result.="masuk ga kosong tanggal";
			// $result.="select id from emp where trim(reg_no)='".$nikPegawai."' and status='".$status."'";

			//sleep(1);
		}

		// $result =$masukAbsen;
		// $result.="select idAbsen from att_absen where idPegawai = '$idPegawai' and tanggalAbsen = '$tanggalAbsen'";

		$progresData = getAngka($par[rowData] / $highestRow * 100, 0);
		return $progresData . "\t(" . $progresData . "%) " . getAngka($par[rowData]) . " of " . getAngka($highestRow);
	}
}

function endProses()
{
	global $s, $inp, $par, $cUsername;

	$file = "files/Upload Absen.log";
	if (file_exists($file)) unlink($file);
	$fileName = fopen($file, "w");

	$tanggalAbsen = date('Y-m-d');
	$sql = "select * from tmp_absen where tanggalAbsen!='0000-00-00' order by idAbsen";
	$res = db($sql);
	$text .= "NO. \tNIK\t\tTANGGAL\t\tMASUK\tKELUAR\tSTATUS\r\n";
	$no = 1;
	while ($r = mysql_fetch_array($res)) {
		$text .= $no . ". \t" . $r[nikPegawai] . "\t\t" . getTanggal($r[tanggalAbsen]) . "\t" . substr($r[masukAbsen], 0, 5) . "\t" . substr($r[pulangAbsen], 0, 5) . "\t" . $r[keteranganAbsen] . "\r\n";
		if ($r[statusAbsen] == "t") $tanggalAbsen = $r[tanggalAbsen];
		$no++;
	}

	$tanggalAbsen = getField("select tanggalAbsen from att_absen where tanggalAbsen <= '" . $tanggalAbsen . "' order by tanggalAbsen desc limit 1");

	fwrite($fileName, $text);
	fclose($fileName);
	sleep(1);

	db("DROP TABLE IF EXISTS tmp_absen");
	return getTanggal($tanggalAbsen) . "\tupload data selesai : " . getTanggal(date('Y-m-d'), "t") . ", " . date('H:i');
}

function hapus()
{
	global $s, $inp, $par, $cUsername;
	$sql = "delete from att_absen where idAbsen='$par[idAbsen]'";
	db($sql);
	echo "<script>window.location='?" . getPar($par, "mode,idAbsen") . "';</script>";
}

function formUpload()
{
	global $s, $inp, $par, $fFile, $arrTitle, $arrParameter, $menuAccess;

	$text .= "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
					" . getBread(ucwords("upload data")) . "					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\" enctype=\"multipart/form-data\">	
				<div id=\"formInput\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">
							<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:225px;\" maxlength=\"100\" />
							<div class=\"fakeupload\">
								<input type=\"file\" id=\"fileData\" name=\"fileData\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
							</div>
						</div>
					</p>				
					<p>
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=fmtAbsen\" class=\"detil\">* download template.xlsx</a></div>
						<input type=\"button\" class=\"btnSubmit radius2\" name=\"btnSimpan\" value=\"Upload\" onclick=\"setProses('" . getPar($par, "mode") . "');\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>						
					</p>
				</div>
				<div id=\"prosesImg\" align=\"center\" style=\"display:none; position:absolute; left:50%; top:50%;\">						
					<img src=\"styles/images/loaders/loader6.gif\">
				</div>
				<div id=\"progresBar\" class=\"progress\" style=\"display:none;\">						
					<strong>Progress</strong> <span id=\"progresCnt\">(0%) </span>
					<div class=\"bar2\" style=\"height:25px;\"><div id=\"persenBar\" class=\"value orangebar\" style=\"width: 0%; height:20px;\"></div></div>
				</div>					
				<span id=\"progresRes\"></span>
				<div id=\"progresEnd\" class=\"progress\" style=\"margin-top:30px; display:none;\">
					<input type=\"hidden\" id=\"tanggalAbsen\" name=\"tanggalAbsen\" value=\"" . date('d/m/Y') . "\">
					<a href=\"download.php?d=logAbsen\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>				
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?par[tanggalAbsen]=' + document.getElementById('tanggalAbsen').value + '" . getPar($par, "mode,tanggalAbsen") . "';\"/>
				</div>
			</form>	
			</div>";
	return $text;
}

function lihat()
{
	global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $areaCheck;
	if (empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');
	$cutil = new Common();
	$cols = 10;
	$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols - 1;
	$text = table($cols, array(4, 5, $cols));

	$text .= "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread() . "
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				Lokasi Kerja : " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[7] . "' and kodeData in ($areaCheck) order by urutanData", "kodeData", "namaData", "par[idLokasi]", "All", $par[idLokasi], "onchange=\"document.getElementById('form').submit();\"", "310px") . "
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				<input type=\"text\" id=\"tanggalAbsen\" name=\"par[tanggalAbsen]\" size=\"10\" maxlength=\"10\" value=\"" . $par[tanggalAbsen] . "\" class=\"vsmallinput hasDatePicker\" onchange=\"document.getElementById('form').submit();\" />
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
		</form>
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; top:0; right:0; margin-top:95px; margin-right:30px;\">
				<input id=\"bView\" type=\"button\" value=\"+ View\" class=\"btn btn_search btn-small\" onclick=\"
					document.getElementById('bView').style.display = 'none';
					document.getElementById('bHide').style.display = 'block';
					document.getElementById('dFilter').style.visibility = 'visible';
					document.getElementById('fSet').style.height = '250px';
				\" />
				<input id=\"bHide\" type=\"button\" value=\"- Hide\" class=\"btn btn_search btn-small\" style=\"display:none\" onclick=\"
					document.getElementById('bView').style.display = 'block';
					document.getElementById('bHide').style.display = 'none';
					document.getElementById('dFilter').style.visibility = 'collapse';
					document.getElementById('fSet').style.height = '90px';
				\" />
			</div>
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px; height:90px;\">						
				<legend style=\"padding:10px; margin-left:20px;\"><h4>FILTER PENCARIAN</h4></legend>						
				<p>
					<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">NAMA</label>
					<div class=\"field\" style=\"margin-left:150px;\">
						<input type=\"text\" id=\"sSearch\" name=\"sSearch\" value=\"\" style=\"width:290px;\"/>
					</div>
				</p>
				<div id=\"dFilter\" style=\"visibility:collapse;\">
					<p>
						<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">KANTOR</label>
						<div class=\"field\" style=\"margin-left:150px;\">
							" . comboData("select kodeData, namaData from mst_data where kodeCategory='X03' and statusData='t' order by urutanData", "kodeData", "namaData", "pSearch", "ALL", "", "", "300px;") . "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">UNIT KERJA</label>
						<div class=\"field\" style=\"margin-left:150px;\">";
	$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
								JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
								where t2.kodeCategory='X03' AND t1.statusData = 't' order by t1.urutanData";
	$text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "bSearch", $_GET['bSearch'], "", "class='chosen-select' style=\"width: 300px\"");
	$text .= "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">" . strtoupper($arrParameter[40]) . "</label>
						<div class=\"field\" style=\"margin-left:150px;\">";
	$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
								JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
								JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
								where t3.kodeCategory='X03' AND t1.statusData = 't' order by t1.urutanData";
	$text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "tSearch", $_GET['tSearch'], "", "class='chosen-select' style=\"width: 300px\"");
	$text .= "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">" . strtoupper($arrParameter[41]) . "</label>
						<div class=\"field\" style=\"margin-left:150px;\">";
	$sql = "select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
								JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
								JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
								JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
								where t4.kodeCategory='X03' AND t1.statusData = 't' order by t1.urutanData";
	$text .= $cutil->generateSelectChainedWithOption($sql, "id", "description", "kodeInduk", "mSearch", $_GET['mSearch'], "", "class='chosen-select' style=\"width: 300px\"");
	$text .= "
						</div>
					</p>
				</div>
			</fieldset>			
		
		<br clear=\"all\" />	
		<div id=\"pos_l\">
			" . comboKey("cSearch", array("0" => "Semua Status", 1 => "Sudah Absen", 2 => "Belum Absen"), $_GET['cSearch']) . "
		</div>	
		</form>
		<div id=\"pos_r\">";
	if (isset($menuAccess[$s]["add"])) $text .= "<a href=\"#Add\" class=\"btn btn1 btn_inboxo\" onclick=\"openBox('popup.php?par[mode]=upl" . getPar($par, "mode,idMesin") . "',725,250);\"><span>Upload Data</span></a>";
	if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=all" . getPar($par, "mode") . "\" class=\"btn btn1 btn_trash\" style=\"margin-left:5px;\" onclick=\"return confirm('anda yakin akan menghapus data absensi tanggal " . getTanggal(setTanggal($par[tanggalAbsen]), "t") . " ?');\"><span>Delete Data</span></a>";
	$text .= "
		</div>
		
		<br clear=\"all\" />				
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"2\" style=\"vertical-align:middle;\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:150px; vertical-align:middle;\">Nama</th>
					<th rowspan=\"2\" style=\"vertical-align:middle;\" width=\"150\">NIK</th>
					<th colspan=\"2\" style=\"width:80px; vertical-align:middle;\">Jadwal</th>					
					<th colspan=\"2\" style=\"width:80px; vertical-align:middle;\">Aktual</th>
					<th rowspan=\"2\" style=\"width:40px; vertical-align:middle;\">Durasi</th>
					<th rowspan=\"2\" style=\"vertical-align:middle;\" width=\"100\">Keterangan</th>
					<th rowspan=\"2\" style=\"vertical-align:middle;\" width=\"50\">Detail</th>
				</tr>
				<tr>
					<th style=\"width:40px; vertical-align:middle;\">Masuk</th>
					<th style=\"width:40px; vertical-align:middle;\">Pulang</th>
					<th style=\"width:40px; vertical-align:middle;\">Masuk</th>
					<th style=\"width:40px; vertical-align:middle;\">Pulang</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		</div>
		<script type=\"text/javascript\">
			jQuery(document).ready(function(){
				jQuery(\"#bSearch\").chained(\"#pSearch\");
				jQuery(\"#bSearch\").trigger(\"chosen:updated\");

				jQuery(\"#pSearch\").bind(\"change\", function () {
					jQuery(\"#bSearch\").trigger(\"chosen:updated\");
				});

				jQuery(\"#tSearch\").chained(\"#bSearch\");
				jQuery(\"#tSearch\").trigger(\"chosen:updated\");

				jQuery(\"#bSearch\").bind(\"change\", function () {
					jQuery(\"#tSearch\").trigger(\"chosen:updated\");
				});

				jQuery(\"#mSearch\").chained(\"#tSearch\");
				jQuery(\"#mSearch\").trigger(\"chosen:updated\");

				jQuery(\"#tSearch\").bind(\"change\", function () {
					jQuery(\"#mSearch\").trigger(\"chosen:updated\");
				});
			});
		</script>";
	return $text;
}

function detail()
{

	global $s, $par, $arrTitle, $ui;

	$_SESSION["curr_emp_id"] = $par[idPegawai];
	?>
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<?= getBread(ucwords($par['mode'] . " data")) ?>
	</div>
	<div style="padding:10px;">
	<?php
		$sql = "select * from dta_absen where idPegawai='$par[idPegawai]' and '" . setTanggal($par[tanggalAbsen]) . "' between date(mulaiAbsen) and date(selesaiAbsen) and keteranganAbsen='$par[keteranganAbsen]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$dtaNormal = getField("select concat(kodeShift, ',\t', namaShift, ',\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho')");
		$dtaShift = getField("select concat(t2.kodeShift, ',\t', t2.namaShift, ',\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]'");
		$dtaJadwal = getField("select concat(t2.kodeShift, ',\t', t2.namaShift, ',\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2 on (t1.idShift=t2.idShift) where t1.idPegawai='$r[idPegawai]' and tanggalJadwal='" . setTanggal($par[tanggalAbsen]) . "'");

		list($r[kodeShift], $r[namaShift], $r[mulaiShift], $r[selesaiShift]) = empty($dtaShift) ? explode("\t", $dtaNormal) : explode("\t", $dtaShift);
		if (!empty($dtaJadwal)) list($r[kodeShift], $r[namaShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaJadwal);
		if ($r[mulaiShift] == "00:00:00" && $r[selesaiShift] == "00:00:00"  && !in_array(trim(strtolower($r[kodeShift])), array("c", "off"))) list($r[kodeShift], $r[namaShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $dtaNormal);


		list($r[tanggalAbsen], $r[masukAbsen]) = explode(" ", $r[mulaiAbsen]);
		list($r[tanggalAbsen], $r[pulangAbsen]) = explode(" ", $r[selesaiAbsen]);

		if ($r[masukAbsen] == "00:00:00") $r[masukAbsen] = "";
		if ($r[pulangAbsen] == "00:00:00") $r[pulangAbsen] = "";
		?>
		</div>
			<div class="contentwrapper">
	<?php require_once "tmpl/emp_header_basic.php"; ?>

			<form id="form" class="stdform">
			<div id="general">
				<div class="widgetbox">
					<div class="title" style="margin-top:30px; margin-bottom:0px;"><h3>DATA ABSENSI</h3></div>
				</div>				
				<p><?= $ui->createSpan("Tanggal", getTanggal(setTanggal($par[tanggalAbsen]), "t")) ?></p>
				<p><?= $ui->createSpan("Jadwal Kerja", $r[namaShift]." " . substr($r[mulaiShift], 0, 5) . " - " . substr($r[selesaiShift], 0, 5) . "&nbsp;") ?></p>
				<?php
		list($masukAbsen_sn, $pulangAbsen_sn) = explode("\t", $r[nomorAbsen]);
		$nomorAbsen = ($masukAbsen_sn == $pulangAbsen_sn || empty($pulangAbsen_sn)) ? $masukAbsen_sn : $masukAbsen_sn . " / " . $pulangAbsen_sn;

		$arrMode = array(
			"Izin Cuti" => "detCuti",
			"Izin Dinas" => "detDinas",
			"Izin Sementara" => "detSementara",
			"Izin Pelatihan" => "detPelatihan",
			"Izin Sakit" => "detSakit",
		);

		if (empty($r[keteranganAbsen])){
			?>
				<p><?= $ui->createSpan("Aktual", substr($r[masukAbsen], 0, 5) . " - " . substr($r[pulangAbsen], 0, 5) . "&nbsp;") ?></p>
				<p><?= $ui->createSpan("SN Mesin", $nomorAbsen . "&nbsp;") ?></p>
				<?php
		}else{
			?>
			<p><?= $ui->createSpan("Keterangan", $r[keteranganAbsen] . "&nbsp;") ?></p>
			<p><?= $ui->createSpan("Nomor", "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=". $arrMode["$r[keteranganAbsen]"] . "&par[id]=$r[idAbsen]" . getPar($par, "mode,idMesin") . "',1000,550);\">$r[nomorAbsen]</a>&nbsp;") ?></p>
<?php
		}
	?>
	</div>
			<p>					
				<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, 'mode,idPegawai') ?>';" style="float:right;" />
			</p>
			</form>
			<?php
		}

	function detailSementara()
	{
		global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

		$sql = "select * from att_izin where idIzin='$par[id]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		if (empty($r[nomorIzin])) $r[nomorIzin] = gNomor();
		if (empty($r[tanggalIzin])) $r[tanggalIzin] = date('Y-m-d');
		list($mulaiIzin_tanggal, $mulaiIzin) = explode(" ", $r[mulaiIzin]);
		list($selesaiIzin_tanggal, $selesaiIzin) = explode(" ", $r[selesaiIzin]);

		$sql_ = "select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='" . $r[idPegawai] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);

		$text .= "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Izin Sementara</h1>
					" . getBread() . "								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">" . $r[nomorIzin] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NIK</label>
							<span class=\"field\">" . $r_[nikPegawai] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">" . $r_[namaPegawai] . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">" . getTanggal($r[tanggalIzin], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">" . $r_[namaJabatan] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">" . $r_[namaDivisi] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SEMENTARA</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">" . getTanggal($mulaiIzin_tanggal, "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Waktu</label>
							<span class=\"field\">" . substr($mulaiIzin, 0, 5) . " <strong>s.d</strong> " . substr($selesaiIzin, 0, 5) . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[keteranganIzin]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">";

		$sql_ = "select
						id as idPengganti,
						reg_no as nikPengganti,
						name as namaPengganti
					from emp where id='" . $r[idPengganti] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);
		$text .= "<p>
							<label class=\"l-input-small\">Pengganti</label>
							<span class=\"field\">" . $r_[nikPengganti] . " - " . $r_[namaPengganti] . "&nbsp;</span>
						</p>";


		$sql_ = "select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='" . $r[idAtasan] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);

		$persetujuanIzin = $r[persetujuanIzin] == "t" ? "<img src=\"styles/images/t.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Belum Diproses\">";
		$persetujuanIzin = $r[persetujuanIzin] == "f" ? "<img src=\"styles/images/f.png\" align=\"left\" style=\"margin-top:1px; margin-right:5px;\" title=\"Ditolak\">" : $persetujuanIzin;

		list($approveTanggal, $approveWaktu) = explode(" ", $r[approveTime]);
		$approveTime = getTanggal($approveTanggal) . " " . substr($approveWaktu, 0, 5);
		$approveTime = getTanggal($approveTanggal) == "" ? "Belum Diproses" : $approveTime;

		$text .= "<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">" . $r_[nikAtasan] . " - " . $r_[namaAtasan] . "&nbsp;</span>
						</p>						
					</td>
					</tr>
					</table>
					<div style=\"float:right; margin-top:-30px;\">
						<table width=\"100%\">
						<tr>
						<td>Tahun ini sudah pernah melakukan izin : <strong>" . getField("select count(*) from att_izin where idPegawai='$r[idPegawai]' and persetujuanIzin='t'") . " Kali</strong></td>
						<td>
							<table>
							<tr>
							<td style=\"padding-left:100px;\"><strong>Approval</strong> :</td>
							<td>" . $persetujuanIzin . " " . $approveTime . "</td>
							</tr>
							</table>
						</td>
						</tr>
						</table>
					</div>";

		$persetujuanIzin = "Belum Diproses";
		$persetujuanIzin = $r[persetujuanIzin] == "t" ? "Disetujui" : $persetujuanIzin;
		$persetujuanIzin = $r[persetujuanIzin] == "f" ? "Ditolak" : $persetujuanIzin;

		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL</h3></div>
					</div>					
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $persetujuanIzin . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[catatanIzin]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>					
				</div>
			</form>
			</div>";
		return $text;
	}

	function detailCuti()
	{
		global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

		$sql = "select * from att_cuti where idCuti='$par[id]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$sql_ = "select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='" . $r[idPegawai] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);

		$text .= "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Izin Cuti</h1>
					" . getBread() . "
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">" . $r[nomorCuti] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NIK</label>
							<span class=\"field\">" . $r_[nikPegawai] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">" . $r_[namaPegawai] . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">" . getTanggal($r[tanggalCuti], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">" . $r_[namaJabatan] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">" . $r_[namaDivisi] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA CUTI</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Mulai</label>
							<span class=\"field\">" . getTanggal($r[mulaiCuti], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Selesai</label>
							<span class=\"field\">" . getTanggal($r[selesaiCuti], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[keteranganCuti]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">";

		$sql_ = "select
						id as idPengganti,
						reg_no as nikPengganti,
						name as namaPengganti
					from emp where id='" . $r[idPengganti] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);
		$text .= "<p>
							<label class=\"l-input-small\">Pengganti</label>
							<span class=\"field\">" . $r_[nikPengganti] . " - " . $r_[namaPengganti] . "&nbsp;</span>
						</p>";


		$sql_ = "select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='" . $r[idAtasan] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);


		$text .= "<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">" . $r_[nikAtasan] . " - " . $r_[namaAtasan] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div style=\"float:right; margin-right:20px; margin-top:-40px;\">
						Jatah Cuti  Tahun ini : <strong>12 Hari</strong>, Jatah <strong>" . getAngka($r[jatahCuti]) . " Hari</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Diambil <strong>" . getAngka($r[jumlahCuti]) . " Hari</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Sisa <strong>" . getAngka($r[sisaCuti]) . " Hari</strong>
					</div>";

		$persetujuanCuti = "Belum Diproses";
		$persetujuanCuti = $r[persetujuanCuti] == "t" ? "Disetujui" : $persetujuanCuti;
		$persetujuanCuti = $r[persetujuanCuti] == "f" ? "Ditolak" : $persetujuanCuti;

		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $persetujuanCuti . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[catatanCuti]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";

		$sdmCuti = "Belum Diproses";
		$sdmCuti = $r[sdmCuti] == "t" ? "Disetujui" : $sdmCuti;
		$sdmCuti = $r[sdmCuti] == "f" ? "Ditolak" : $sdmCuti;

		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $sdmCuti . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[noteCuti]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";

		$text .= "</div>
			</form>
			</div>";
		return $text;
	}

	function detailDinas()
	{
		global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

		$sql = "select * from att_dinas where idDinas='$par[id]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$sql_ = "select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='" . $r[idPegawai] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);

		$text .= "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Izin Dinas</h1>
					" . getBread() . "								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">" . $r[nomorDinas] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NIK</label>
							<span class=\"field\">" . $r_[nikPegawai] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">" . $r_[namaPegawai] . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">" . getTanggal($r[tanggalDinas], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">" . $r_[namaJabatan] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">" . $r_[namaDivisi] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN DINAS</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Mulai</label>
							<span class=\"field\">" . getTanggal($r[mulaiDinas], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Selesai</label>
							<span class=\"field\">" . getTanggal($r[selesaiDinas], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[keteranganDinas]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">";

		$sql_ = "select
						id as idPengganti,
						reg_no as nikPengganti,
						name as namaPengganti
					from emp where id='" . $r[idPengganti] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);
		$text .= "<p>
							<label class=\"l-input-small\">Pengganti</label>
							<span class=\"field\">" . $r_[nikPengganti] . " - " . $r_[namaPengganti] . "&nbsp;</span>
						</p>";


		$sql_ = "select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='" . $r[idAtasan] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);


		$text .= "<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">" . $r_[nikAtasan] . " - " . $r_[namaAtasan] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";

		$persetujuanDinas = "Belum Diproses";
		$persetujuanDinas = $r[persetujuanDinas] == "t" ? "Disetujui" : $persetujuanDinas;
		$persetujuanDinas = $r[persetujuanDinas] == "f" ? "Ditolak" : $persetujuanDinas;
		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $persetujuanDinas . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[catatanDinas]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";


		$sdmDinas = "Belum Diproses";
		$sdmDinas = $r[sdmDinas] == "t" ? "Disetujui" : $sdmDinas;
		$sdmDinas = $r[sdmDinas] == "f" ? "Ditolak" : $sdmDinas;
		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $sdmDinas . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[noteDinas]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";

		$text .= "</div>
			</form>
			</div>";
		return $text;
	}

	function detailPelatihan()
	{
		global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

		$sql = "select * from att_pelatihan where idPelatihan='$par[id]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$sql_ = "select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='" . $r[idPegawai] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);

		$text .= "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Izin Pelatihan</h1>
					" . getBread() . "								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">" . $r[nomorPelatihan] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NIK</label>
							<span class=\"field\">" . $r_[nikPegawai] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">" . $r_[namaPegawai] . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">" . getTanggal($r[tanggalPelatihan], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">" . $r_[namaJabatan] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">" . $r_[namaDivisi] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN DINAS</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Mulai</label>
							<span class=\"field\">" . getTanggal($r[mulaiPelatihan], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Selesai</label>
							<span class=\"field\">" . getTanggal($r[selesaiPelatihan], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[keteranganPelatihan]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">";

		$sql_ = "select
						id as idPengganti,
						reg_no as nikPengganti,
						name as namaPengganti
					from emp where id='" . $r[idPengganti] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);
		$text .= "<p>
							<label class=\"l-input-small\">Pengganti</label>
							<span class=\"field\">" . $r_[nikPengganti] . " - " . $r_[namaPengganti] . "&nbsp;</span>
						</p>";


		$sql_ = "select
						id as idAtasan,
						reg_no as nikAtasan,
						name as namaAtasan
					from emp where id='" . $r[idAtasan] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);


		$text .= "<p>
							<label class=\"l-input-small\">Atasan</label>
							<span class=\"field\">" . $r_[nikAtasan] . " - " . $r_[namaAtasan] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";

		$persetujuanPelatihan = "Belum Diproses";
		$persetujuanPelatihan = $r[persetujuanPelatihan] == "t" ? "Disetujui" : $persetujuanPelatihan;
		$persetujuanPelatihan = $r[persetujuanPelatihan] == "f" ? "Ditolak" : $persetujuanPelatihan;
		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $persetujuanPelatihan . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[catatanPelatihan]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";


		$sdmPelatihan = "Belum Diproses";
		$sdmPelatihan = $r[sdmPelatihan] == "t" ? "Disetujui" : $sdmPelatihan;
		$sdmPelatihan = $r[sdmPelatihan] == "f" ? "Ditolak" : $sdmPelatihan;
		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $sdmPelatihan . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[notePelatihan]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";

		$text .= "</div>
			</form>
			</div>";
		return $text;
	}

	function detailSakit()
	{
		global $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

		$sql = "select * from att_sakit where idSakit='$par[id]'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$sql_ = "select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='" . $r[idPegawai] . "'";
		$res_ = db($sql_);
		$r_ = mysql_fetch_array($res_);

		$text .= "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Izin Sakit</h1>
					" . getBread() . "
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">" . $r[nomorSakit] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NIK</label>
							<span class=\"field\">" . $r_[nikPegawai] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">" . $r_[namaPegawai] . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">" . getTanggal($r[tanggalSakit], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">" . $r_[namaJabatan] . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Gedung</label>
							<span class=\"field\">" . $r_[namaDivisi] . "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA IZIN SAKIT</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Mulai</label>
							<span class=\"field\">" . getTanggal($r[mulaiSakit], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Selesai</label>
							<span class=\"field\">" . getTanggal($r[selesaiSakit], "t") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[keteranganSakit]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Perawatan</label>
							<span class=\"field\">" . getField("select namaData from mst_data where kodeData='$r[idPerawatan]'") . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Surat Dokter</label>
							<span class=\"field\">";
		$text .= empty($r[fileSakit]) ? "" : "<a href=\"download.php?d=sakit&f=$r[idSakit]\"><img src=\"" . getIcon($fFile . "/" . $r[fileSakit]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
		$text .= "&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>";

		$persetujuanSakit = "Belum Diproses";
		$persetujuanSakit = $r[persetujuanSakit] == "t" ? "Disetujui" : $persetujuanSakit;
		$persetujuanSakit = $r[persetujuanSakit] == "f" ? "Ditolak" : $persetujuanSakit;

		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $persetujuanSakit . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[catatanSakit]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";

		$sdmSakit = "Belum Diproses";
		$sdmSakit = $r[sdmSakit] == "t" ? "Disetujui" : $sdmSakit;
		$sdmSakit = $r[sdmSakit] == "f" ? "Ditolak" : $sdmSakit;
		$text .= "<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">" . $sdmSakit . "&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">" . nl2br($r[noteSakit]) . "&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";

		$text .= "</div>
			</form>
			</div>";
		return $text;
	}

	function lData()
	{
		global $s, $par, $menuAccess, $areaCheck;
		if (empty($par[tanggalAbsen])) $par[tanggalAbsen] = date('d/m/Y');

		$arrNormal = getField("select concat(kodeShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where trim(lower(namaShift)) in ('pagi', 'ho')");
		$arrShift = arrayQuery("select t1.idPegawai, concat(t2.kodeShift, '\t', t2.mulaiShift, '\t', t2.selesaiShift) from att_setting t1 join dta_shift t2 on (t1.idShift=t2.idShift)");
		$arrJadwal = arrayQuery("select idPegawai, concat(t2.kodeShift, '\t', t1.mulaiJadwal, '\t', t1.selesaiJadwal) from dta_jadwal t1 join dta_shift t2  on (t1.idShift=t2.idShift) where t1.tanggalJadwal='" . setTanggal($par[tanggalAbsen]) . "'");
		$arrAbsen = arrayQuery("select idPegawai, concat(time(mulaiAbsen), '\t', time(selesaiAbsen),'\t', durasiAbsen, '\t', keteranganAbsen, '\t', idAbsen) from dta_absen where date(mulaiAbsen) = '" . setTanggal($par[tanggalAbsen]) . "'");
		// $arrRegion = arrayQuery("select idAbsen, concat(start_region_offset, '\t', end_region_offset, '\t', idPegawai, '\t', tanggalAbsen) from att_absen where tanggalAbsen = '".setTanggal($par[tanggalAbsen])."'");

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);

		$sWhere = "where t1.status = '535' and t2.location in ($areaCheck)";

		if (!empty($par[idLokasi]))
			$sWhere .= " and t2.location='" . $par[idLokasi] . "'";

		if (!empty($_GET['sSearch']))
			$sWhere .= " and lower(t1.name) like '%" . mysql_real_escape_string(strtolower($_GET['sSearch'])) . "%'";

		if (!empty($_GET['pSearch'])) $sWhere .= " and t2.dir_id='" . $_GET['pSearch'] . "'";
		if (!empty($_GET['bSearch'])) $sWhere .= " and t2.div_id='" . $_GET['bSearch'] . "'";
		if (!empty($_GET['tSearch'])) $sWhere .= " and t2.dept_id='" . $_GET['tSearch'] . "'";
		if (!empty($_GET['mSearch'])) $sWhere .= " and t2.unit_id='" . $_GET['mSearch'] . "'";

		$pegawaiAbsen = getField("select group_concat(idPegawai) from att_absen where date(tanggalAbsen) = '" . setTanggal($par[tanggalAbsen]) . "'");

		if ($_GET['cSearch'] == 1) {
			$sWhere .= " and t1.id IN($pegawaiAbsen)";
		} else if ($_GET['cSearch'] == 2) {
			$sWhere .= " and t1.id NOT IN($pegawaiAbsen)";
		} else {
			$sWhere .= "";
		}



		$arrOrder = array(
			"t1.name",
			"t1.name",
			"t1.reg_no",
			"",
			"",
			"",
			"",
			"",
			"",
		);

		$orderBy = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
		$sql = "SELECT t1.id as idPegawai, name, reg_no from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $sWhere group by t1.id order by name $sLimit";
		$res = db($sql);

		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(id) from dta_pegawai"),
			"aaData" => array(),
		);

		$no = intval($_GET['iDisplayStart']);
		while ($r = mysql_fetch_array($res)) {
			$no++;

			list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = isset($arrShift["$r[idPegawai]"]) ? explode("\t", $arrShift["$r[idPegawai]"]) : explode("\t", $arrNormal);
			list($r[masukAbsen], $r[pulangAbsen], $r[durasiAbsen], $r[keteranganAbsen], $r[idAbsen]) = explode("\t", $arrAbsen[$r[idPegawai]]);

			if (isset($arrJadwal["$r[idPegawai]"]))
				list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrJadwal["$r[idPegawai]"]);

			if ($r[mulaiShift] == "00:00:00" && $r[selesaiShift] == "00:00:00"  && !in_array(trim(strtolower($r[kodeShift])), array("c", "off"))) list($r[kodeShift], $r[mulaiShift], $r[selesaiShift]) = explode("\t", $arrNormal);



			$controlShift = "<a href=\"?par[mode]=det&par[idPegawai]=$r[idPegawai]&par[keteranganAbsen]=$r[keteranganAbsen]" . getPar($par, "mode,idPegawai,keteranganAbsen") . "\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";

			$masuk =  substr($r[masukAbsen], 0, 5);
			$keluar = substr($r[pulangAbsen], 0, 5);
			$durasi = substr(str_replace("-", "", $r[durasiAbsen]), 0, 5);

			// $r[startRegion] = date('H:m', strtotime($masuk . "UTC") + $r[startRegion]); 

			if ($masuk == "00:00") $masuk = "";
			if ($keluar == "00:00") $keluar = "";
			if ($durasi == "00:00") $durasi = "";

			list($jamMasuk, $menitMasuk) = explode(":", $masuk);
			list($jamKeluar, $menitKeluar) = explode(":", $keluar);

			$menitDatang = ($jamMasuk * 60) + $menitMasuk;
			$menitKeluar = ($jamKeluar * 60) + $menitKeluar;

			// $nik = $r[reg_no];

			// if($nik != $r[reg_no]){

			$data = array(
				"<div align=\"center\">" . $no . ".</div>",
				"<div align=\"left\">" . strtoupper($r[name]) . "</div>",
				"<div align=\"left\">" . $r[reg_no] . "</div>",
				"<div align=\"center\">" . substr($r[mulaiShift], 0, 5) . "</div>",
				"<div align=\"center\">" . substr($r[selesaiShift], 0, 5) . "</div>",
				"<div align=\"center\">" . $masuk . "</div>",
				"<div align=\"center\">" . $keluar . "</div>",
				"<div align=\"center\">" . $durasi . "</div>",

				"<div align=\"left\">" . $r[keteranganAbsen] . "</div>",

				"<div align=\"center\">" . $controlShift . "</div>",
			);

			// $nik = $r[reg_no];

			// }

			$json['aaData'][] = $data;


			// $nik = $r[reg_no];
			// $arr["$r[idPegawai]"]=$r;
		}

		// $cekEvent = getField("select id from dta_event where mulaiEvent = '".setTanggal($par[tanggalAbsen])."'");

		// if(is_array($arr)){				
		// 	reset($arr);		
		// 	while(list($idPegawai, $r)=each($arr)){
		$no++;



		// 	}
		// }			

		return json_encode($json);
	}

	function getContent($par)
	{

		global $s, $_submit, $menuAccess;
		switch ($par[mode]) {
			case "lst":
				$text = lData();
				break;

			case "detCuti":
				$text = detailCuti();
				break;
			case "detDinas":
				$text = detailDinas();
				break;
			case "detSementara":
				$text = detailSementara();
				break;
			case "detPelatihan":
				$text = detailPelatihan();
				break;
			case "detSakit":
				$text = detailSakit();
				break;
			case "det":
				$text = detail();
				break;

			case "end":
				if (isset($menuAccess[$s]["add"])) $text = endProses();
				break;
			case "dat":
				if (isset($menuAccess[$s]["add"])) $text = setData();
				break;
			case "tab":
				if (isset($menuAccess[$s]["add"])) $text = setTable();
				break;

			case "all":
				if (isset($menuAccess[$s]["delete"])) $text = delete();
				else $text = lihat();
				break;
			case "upl":
				$text = isset($menuAccess[$s]["add"]) ? formUpload() : lihat();
				break;
			default:
				$text = lihat();
				break;
		}
		return $text;
	}
