<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/upload/";
	
	function gData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$areaCheck,$arrParameter;				
		$totalHari = selisihHari(setTanggal($_GET['mSearch']), setTanggal($_GET['tSearch'])) + 1;		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere= " where t1.id is not null and t1.status='".$status."' and t1.location in ( $areaCheck )";
		
		if(!empty($par[idLokasi]))
			$sWhere .= " and t1.location = '".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";
				
		list($tahunService, $bulanService) = explode("-", setTanggal($_GET['tSearch']));
		$sql="select t2.* from dta_pegawai t1 join pay_service t2 on (t1.id=t2.idPegawai) ".$sWhere." and t2.tahunService='".$tahunService."' and t2.bulanService='".intval($bulanService)."'";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			$arrService["".$r[idPegawai].""] = $r;
		}
		
		$jumlahHari = $liburHari = $kerjaHari = $alphaHari = $sakitHari = $ijinHari = $eoHari = $cutiHari = $serviceHari = 0;		
		$sql="select * from dta_pegawai t1 join pay_service t2 on (t1.id=t2.idPegawai) $sWhere";		
		$res=db($sql);						
		while($r=mysql_fetch_array($res)){						
			if(isset($arrService["".$r[id].""])){
				$s=$arrService["".$r[id].""];
				$jumlahHari = $s[hariService];
				$liburHari = $s[liburService];
				$kerjaHari = $s[kerjaService];
				$alphaHari = $s[alphaService];
				$sakitHari = $s[sakitService];
				$ijinHari = $s[ijinService];
				$eoHari = $s[eoService];
				$cutiHari = $s[cutiService];
				$serviceHari = $s[jumlahService];
			}else{
				$jumlahHari = $totalHari;
				$liburHari = 0;
				$kerjaHari = $jumlahHari - $liburHari;
				$alphaHari = 0;
				$sakitHari = 0;
				$ijinHari = 0;
				$eoHari = 0;
				$cutiHari = 0;
				$serviceHari = $kerjaHari - $alphaHari - $sakitHari - $ijinHari - $cutiHari;
			}
			
			$tHari+=$jumlahHari;
			$tLibur+=$liburHari;
			$tKerja+=$kerjaHari;
			$tAlpha+=$alphaHari;
			$tSakit+=$sakitHari;
			$tIjin+=$ijinHari;
			$tEo+=$eoHari;
			$tCuti+=$cutiHari;
			$tService+=$serviceHari;			
		}
		
		return getAngka($tHari)."\t".getAngka($tLibur)."\t".getAngka($tKerja)."\t".getAngka($tAlpha)."\t".getAngka($tSakit)."\t".getAngka($tIjin)."\t".getAngka($tEo)."\t".getAngka($tCuti)."\t".getAngka($tService);
	}
	
	function lData(){
		global $s,$par,$cUsername,$sUser,$sGroup,$menuAccess,$areaCheck,$arrParameter;		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		$totalHari = selisihHari(setTanggal($_GET['mSearch']), setTanggal($_GET['tSearch'])) + 1;
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere= " where t1.id is not null and t1.status='".$status."' and t1.location in ( $areaCheck )";
		
		if(!empty($par[idLokasi]))
			$sWhere .= " and t1.location = '".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";
		
		$arrOrder = array(	
			"t1.name",
			"t1.name",
			"t1.pos_name",
			"t1.reg_no",
		);
		
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		
		list($tahunService, $bulanService) = explode("-", setTanggal($_GET['tSearch']));
		$sql="select t2.* from dta_pegawai t1 join pay_service t2 on (t1.id=t2.idPegawai) ".$sWhere." and t2.tahunService='".$tahunService."' and t2.bulanService='".intval($bulanService)."'";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			$arrService["".$r[idPegawai].""] = $r;
		}
		
		$sql="select * from dta_pegawai t1 join pay_service t2 on (t1.id=t2.idPegawai) $sWhere order by $orderBy $sLimit";		
		$res=db($sql);		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_pegawai t1 join pay_service t2 on (t1.id=t2.idPegawai) $sWhere"),
			"aaData" => array(),
		);				
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;			
			
			if(isset($arrService["".$r[id].""])){
				$s=$arrService["".$r[id].""];
				$jumlahHari = $s[hariService];
				$liburHari = $s[liburService];
				$kerjaHari = $s[kerjaService];
				$alphaHari = $s[alphaService];
				$sakitHari = $s[sakitService];
				$ijinHari = $s[ijinService];
				$eoHari = $s[eoService];
				$cutiHari = $s[cutiService];
				$serviceHari = $s[jumlahService];
			}else{
				$jumlahHari = $totalHari;
				$liburHari = 0;
				$kerjaHari = $jumlahHari - $liburHari;
				$alphaHari = 0;
				$sakitHari = 0;
				$ijinHari = 0;
				$eoHari = 0;
				$cutiHari = 0;
				$serviceHari = $kerjaHari - $alphaHari - $sakitHari - $ijinHari - $cutiHari;
			}
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\">".strtoupper($r[name])."</div>",
				"<div align=\"left\">".strtoupper($r[pos_name])."</div>",
				"<div align=\"left\">".$r[reg_no]."</div>",				
				"<div align=\"right\">".getAngka($jumlahHari)."</div>",
				"<div align=\"right\">".getAngka($liburHari)."</div>",
				"<div align=\"right\">".getAngka($kerjaHari)."</div>",
				"<div align=\"right\">".getAngka($alphaHari)."</div>",
				"<div align=\"right\">".getAngka($sakitHari)."</div>",
				"<div align=\"right\">".getAngka($ijinHari)."</div>",
				"<div align=\"right\">".getAngka($eoHari)."</div>",
				"<div align=\"right\">".getAngka($cutiHari)."</div>",
				"<div align=\"right\">".getAngka($serviceHari)."</div>",
			);
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
				
	function setTable(){
		global $s,$inp,$par,$fFile,$cUsername,$arrParameter;
		if(!in_array(strtolower(substr($_FILES[fileData][name],-3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name],-4)), array("xlsx"))){
			return "file harus dalam format .xls atau .xlsx";
		}else{
			$fileUpload = $_FILES[fileData][tmp_name];
			$fileUpload_name = $_FILES[fileData][name];
			if(($fileUpload!="") and ($fileUpload!="none")){						
				fileUpload($fileUpload,$fileUpload_name,$fFile);
				$fileData = md5($cUsername."-".date("Y-m-d H:i:s")).".".getExtension($fileUpload_name);
				fileRename($fFile, $fileUpload_name, $fileData);
				
				db("DROP TABLE IF EXISTS tmp_upload");		
				db("CREATE TABLE IF NOT EXISTS tmp_upload (					  
					  idService int(11) NOT NULL,
					  bulanService int(11) NOT NULL,
					  tahunService int(11) NOT NULL,					  
					  nikService varchar(30) NOT NULL,
					  namaService varchar(150) NOT NULL,
					  hariService int(11) NOT NULL,
					  liburService int(11) NOT NULL,
					  kerjaService int(11) NOT NULL,
					  alphaService int(11) NOT NULL,
					  sakitService int(11) NOT NULL,
					  ijinService int(11) NOT NULL,
					  eoService int(11) NOT NULL,
					  cutiService int(11) NOT NULL,
					  jumlahService int(11) NOT NULL,
					  statusService varchar(150) NOT NULL,
					  createBy varchar(30) NOT NULL,
					  createTime datetime NOT NULL,
					  PRIMARY KEY (idService)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				
				$inputFileName = $fFile.$fileData;
				require_once ('plugins/PHPExcel/IOFactory.php');
				
				try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$objPHPExcel = $objReader->load($inputFileName);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
				}				
				
				return "fileData".$fileData;
			}	
		}
	}
	
	function setData(){
		global $s,$inp,$par,$fFile,$cUsername,$arrParameter,$arrParam;
		
		$inputFileName = $fFile.$par[fileData];
		require_once ('plugins/PHPExcel/IOFactory.php');			
		
		//  Read your Excel workbook
		try {
			$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($inputFileName);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0); 
		$highestRow = $sheet->getHighestRow(); 		
		
		$result=$par[rowData].". ";
		if($par[rowData] <= $highestRow){	
			$rowData = $sheet->rangeToArray('A' . $par[rowData] . ':M' . $par[rowData], NULL, TRUE, TRUE);				
			$dta = $rowData[0];
			
			$result.= " ".$dta[0]." ".$dta[6];		
			if(is_numeric(trim(strtolower($dta[0])))){
				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
								
				$tahunService = trim($dta[0]);
				$bulanService = trim($dta[1]);
				$nikPegawai = trim($dta[2]);
				$namaService = trim($dta[3]);
				$hariService = setAngka(trim($dta[4]));
				$liburService = setAngka(trim($dta[5]));
				$kerjaService = setAngka(trim($dta[6]));
				$alphaService = setAngka(trim($dta[7]));
				$sakitService = setAngka(trim($dta[8]));
				$ijinService = setAngka(trim($dta[9]));
				$eoService = setAngka(trim($dta[10]));
				$cutiService = setAngka(trim($dta[11]));
				$jumlahService = $kerjaService - $alphaService - $sakitService - $ijinService;
				
				$idPegawai = getField("select id from emp where reg_no='".$nikPegawai."' and status='".$status."'");				
				$statusService = $idPegawai > 0 ? "OK" : "NPP : ".$nikPegawai." belum terdaftar";
												
				if($idPegawai > 0){					
					$sql="delete from pay_service where bulanService='".$bulanService."' and tahunService='".$tahunService."' and idPegawai='".$idPegawai."'";
					db($sql);										
					$sql="insert into pay_service (bulanService, tahunService, idPegawai, hariService, liburService, kerjaService, alphaService, sakitService, ijinService, eoService, cutiService, jumlahService, createBy, createTime) values ('".$bulanService."', '".$tahunService."', '".$idPegawai."', '".$hariService."', '".$liburService."', '".$kerjaService."', '".$alphaService."', '".$sakitService."', '".$ijinService."', '".$eoService."', '".$cutiService."', '".$jumlahService."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
					db($sql);										
				}				
				
				$idService = getField("select idService from tmp_upload order by idService desc limit 1") + 1;
				$sql="insert into tmp_upload (idService, bulanService, tahunService, nikService, namaService, hariService, liburService, kerjaService, alphaService, sakitService, ijinService, eoService, cutiService, jumlahService, statusService, createBy, createTime) values ('".$idService."', '".$bulanService."', '".$tahunService."', '".$nikPegawai."', '".$namaService."', '".$hariService."', '".$liburService."', '".$kerjaService."', '".$alphaService."', '".$sakitService."', '".$ijinService."', '".$eoService."', '".$cutiService."', '".$jumlahService."', '".$statusService."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
				db($sql);
								
				usleep(5000);
			}
			
			$progresData = getAngka($par[rowData]/$highestRow * 100);	
			return $progresData."\t(".$progresData."%) ".getAngka($par[rowData])." of ".getAngka($highestRow)."\t".$result;	
		}
	}
	
	function endProses(){
		global $s,$inp,$par,$cUsername;
		
		$file = "files/Upload Service.log";
		if(file_exists($file))unlink($file);
		$fileName = fopen($file, "w");
								
		$sql="select * from tmp_upload order by idService";
		$res=db($sql);
		$text.= "TAHUN\tBULAN\tNPP\tNAMA\tJML HARI\tJML LIBUR\tJML HARI KERJA\tALPHA\tSAKIT\tIJIN\tEO\tCUTI\tSTATUS\r\n";
		while($r=mysql_fetch_array($res)){
			$text.= $r[tahunService]."\t".$r[bulanService]."\t".$r[nikService]."\t".$r[namaService]."\t".getAngka($r[hariService])."\t".$r[liburService]."\t".$r[kerjaService]."\t".$r[alphaService]."\t".$r[sakitService]."\t".$r[ijinService]."\t".$r[eoService]."\t".$r[cutiService]."\t".$r[jumlahService]."\t".$r[statusService]."\r\n";			
		}
				
		fwrite($fileName, $text);
		fclose($fileName);		
		usleep(5000);
		
		db("DROP TABLE IF EXISTS tmp_upload");		
		return "upload data selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
	
	function formUpload(){
		global $s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess;
						
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords("upload data"))."					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\" enctype=\"multipart/form-data\">	
				<div id=\"formInput\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">
							<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:290px;\" maxlength=\"100\" />
							<div class=\"fakeupload\">
								<input type=\"file\" id=\"fileData\" name=\"fileData\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
							</div>
						</div>
					</p>				
					<p>
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=fmtService\" class=\"detil\">* download template.xlsx</a></div>
						<input type=\"button\" class=\"btnSubmit radius2\" name=\"btnSimpan\" value=\"Upload\" onclick=\"setProses('".getPar($par,"mode")."');\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>						
					</p>
				</div>
				<div id=\"prosesImg\" align=\"center\" style=\"display:none; position:absolute; left:50%; top:50%;\">						
					<img src=\"styles/images/loaders/loader6.gif\">
				</div>
				<div id=\"progresBar\" class=\"progress\" style=\"display:none;\">						
					<strong>Progress</strong> <span id=\"progresCnt\">(0%) </span>
					<div class=\"bar2\"><div id=\"persenBar\" class=\"value orangebar\" style=\"width: 0%;\"></div></div>
				</div>					
				<span id=\"progresRes\"></span>
				<div id=\"progresEnd\" class=\"progress\" style=\"margin-top:30px; display:none;\">
					<input type=\"hidden\" id=\"tanggalAbsen\" name=\"tanggalAbsen\" value=\"".date('d/m/Y')."\">
					<a href=\"download.php?d=logService\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>				
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?".getPar($par,"mode")."';\"/>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$arrParameter, $areaCheck;
		if(empty($_GET[mSearch])){
			$m = date("m") == 1 ? 12 : date("m")-1;
			$Y = date("m") == 1 ? date("Y")-1 : date("Y");
			$mSearch = "21/".str_pad($m, 2, "0", STR_PAD_LEFT)."/".$Y;
		}else{
			$mSearch = $_GET[mSearch];
		}				
		$tSearch = empty($_GET[tSearch]) ? date('20/m/Y') : $_GET[tSearch];
		$text = table(13, array(5,6,7,8,9,10,11,12,13));		
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "200px")."
									</td>
									<td style=\"vertical-align:top;\" id=\"bView\">
										<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'none';
										document.getElementById('bHide').style.display = 'table-cell';
										document.getElementById('dFilter').style.visibility = 'visible';							
										document.getElementById('fSet').style.height = 'auto';
										document.getElementById('fSet').style.padding = '10px';
										\">
									</td>
									<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
										<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 9px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'table-cell';
										document.getElementById('bHide').style.display = 'none';
										document.getElementById('dFilter').style.visibility = 'collapse';							
										document.getElementById('fSet').style.height = '0px';
										document.getElementById('fSet').style.padding = '0px';
										\">					
									</td>
									<td>
										<input type=\"button\" class=\"cancel radius2\" value=\"Back\" onclick=\"window.location = '?".preg_replace("/(&[ms]=\w+)/", "", getPar())."';\"/>
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE statusData='t' and kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE statusData='t' and kodeCategory = 'X06' and kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, upper(namaData) as namaData FROM mst_data WHERE statusData='t' and kodeCategory = 'X07' and kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
				</div>	
				
				<div id=\"pos_l\" style=\"float:left\">
				<table>
					<tr>
					<td>Periode : </td>
					<td><input type=\"text\" id=\"mSearch\" name=\"mSearch\" size=\"10\" maxlength=\"10\" value=\"".$mSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\" /></td>
					<td>s.d</td>
					<td><input type=\"text\" id=\"tSearch\" name=\"tSearch\" size=\"10\" maxlength=\"10\" value=\"".$tSearch."\" class=\"vsmallinput hasDatePicker\" onchange=\"gData('".getPar($par, "mode")."');\" /></td>
					</tr>
				</table>
				</div>
				<div id=\"pos_r\" style=\"float:right\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_inboxo\" onclick=\"openBox('popup.php?par[mode]=upl".getPar($par,"mode,idMesin")."',725,250);\"><span>Upload Data</span></a>";		
		$text.=" <a href=\"#\" onclick=\"window.location='?par[mode]=xls&mSearch=' + document.getElementById('mSearch').value + '&tSearch=' + document.getElementById('tSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
				</div>	
				<br clear=\"all\" />	
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th style=\"width:30px; vertical-align:middle;\">No.</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Nama</th>
					<th style=\"min-width:100px; vertical-align:middle;\">Jabatan</th>
					<th style=\"width:100px; vertical-align:middle;\">No ID</th>
					<th style=\"width:50px; vertical-align:middle;\">Jumlah Hari</th>
					<th style=\"width:50px; vertical-align:middle;\">Jumlah Libur</th>
					<th style=\"width:50px; vertical-align:middle;\">Jumlah HK</th>
					<th style=\"width:50px; vertical-align:middle;\">Alpa</th>
					<th style=\"width:50px; vertical-align:middle;\">Sakit</th>
					<th style=\"width:50px; vertical-align:middle;\">Ijin</th>
					<th style=\"width:50px; vertical-align:middle;\">EO</th>
					<th style=\"width:50px; vertical-align:middle;\">Cuti</th>
					<th style=\"width:50px; vertical-align:middle;\">Jumlah Service</th>
				</tr>
			</thead>
			<tbody></tbody>			
			<tfoot>
				<tr>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\"><strong>TOTAL</strong></td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>
					<td style=\"vertical-align:middle;\">&nbsp;</td>					
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tHari\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tLibur\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tKerja\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tAlpha\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tSakit\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tIjin\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tEo\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tCuti\">&nbsp;</td>
					<td style=\"vertical-align:middle; text-align:right;\" id=\"tService\">&nbsp;</td>
				</tr>
			</tfoot>
			</table>
			</div>
			<script>
				gData('".getPar($par, "mode")."');
			</script>";
			
		if($par[mode] == "xls"){
			xls();
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
			
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$areaCheck;
		require_once 'plugins/PHPExcel.php';
			
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);	
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(50);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:M2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:M3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper("Rekap Tunjangan Service"));
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getTanggal($_GET[mSearch],"t")." s.d ".getTanggal($_GET[tSearch],"t"));
		$objPHPExcel->getActiveSheet()->setCellValue('A3', "Departemen : ".getField("select namaData from mst_data where kodeData='".$par[deptId]."'"));
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:M4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
				
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'JABATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'NO. ID');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'JML HARI');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JML LIBUR');
		$objPHPExcel->getActiveSheet()->setCellValue('G4', 'JML HK');
		$objPHPExcel->getActiveSheet()->setCellValue('H4', 'ALPHA');
		$objPHPExcel->getActiveSheet()->setCellValue('I4', 'SAKIT');
		$objPHPExcel->getActiveSheet()->setCellValue('J4', 'IJIN');
		$objPHPExcel->getActiveSheet()->setCellValue('K4', 'EO');
		$objPHPExcel->getActiveSheet()->setCellValue('L4', 'CUTI');
		$objPHPExcel->getActiveSheet()->setCellValue('M4', 'JML SERVICE');
								
		$rows = 5;
		$totalHari = selisihHari(setTanggal($_GET['mSearch']), setTanggal($_GET['tSearch'])) + 1;		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere= " where t1.id is not null and t1.status='".$status."' and t1.location in ( $areaCheck )";
		
		if(!empty($par[idLokasi]))
			$sWhere .= " and t1.location = '".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";
				
		list($tahunService, $bulanService) = explode("-", setTanggal($_GET['tSearch']));
		$sql="select t2.* from dta_pegawai t1 join pay_service t2 on (t1.id=t2.idPegawai) ".$sWhere." and t2.tahunService='".$tahunService."' and t2.bulanService='".intval($bulanService)."'";
		$res=db($sql);		
		while($r=mysql_fetch_array($res)){
			$arrService["".$r[idPegawai].""] = $r;
		}
		
		$jumlahHari = $liburHari = $kerjaHari = $alphaHari = $sakitHari = $ijinHari = $eoHari = $cutiHari = $serviceHari = 0;		
		$sql="select * from dta_pegawai t1 $sWhere";		
		$res=db($sql);						
		while($r=mysql_fetch_array($res)){						
			$no++;
			
			if(isset($arrService["".$r[id].""])){
				$s=$arrService["".$r[id].""];
				$jumlahHari = $s[hariService];
				$liburHari = $s[liburService];
				$kerjaHari = $s[kerjaService];
				$alphaHari = $s[alphaService];
				$sakitHari = $s[sakitService];
				$ijinHari = $s[ijinService];
				$eoHari = $s[eoService];
				$cutiHari = $s[cutiService];
				$serviceHari = $s[jumlahService];
			}else{
				$jumlahHari = $totalHari;
				$liburHari = 0;
				$kerjaHari = $jumlahHari - $liburHari;
				$alphaHari = 0;
				$sakitHari = 0;
				$ijinHari = 0;
				$eoHari = 0;
				$cutiHari = 0;
				$serviceHari = $kerjaHari - $alphaHari - $sakitHari - $ijinHari - $cutiHari;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[name]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[pos_name]);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $jumlahHari);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $liburHari);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $kerjaHari);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $alphaHari);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $sakitHari);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $ijinHari);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $eoHari);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$rows, $cutiHari);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$rows, $serviceHari);
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':M'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);			
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':M'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':M'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[<0](#,##0);#,##;');
						
			$tHari+=$jumlahHari;
			$tLibur+=$liburHari;
			$tKerja+=$kerjaHari;
			$tAlpha+=$alphaHari;
			$tSakit+=$sakitHari;
			$tIjin+=$ijinHari;
			$tEo+=$eoHari;
			$tCuti+=$cutiHari;
			$tService+=$serviceHari;			
			
			$rows++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('B')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, "TOTAL");		
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $tHari);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $tLibur);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $tKerja);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $tAlpha);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $tSakit);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $tIjin);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $tEo);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$rows, $tCuti);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$rows, $tService);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':M'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':M'.$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[<0](#,##0);#,##;');
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':M'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('L4:L'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('M4:M'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:M'.$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:M'.$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:M'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle("Laporan");
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;			
			case "get":
				$text=gData();
			break;
			
			case "end":
				if(isset($menuAccess[$s]["add"])) $text = endProses();
			break;
			case "dat":
				if(isset($menuAccess[$s]["add"])) $text = setData();
			break;
			case "tab":
				if(isset($menuAccess[$s]["add"])) $text = setTable();
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
?>