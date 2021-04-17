<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/upload/";
	if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
	if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		
	$sql="select * from dta_libur where statusLibur='t' and '".$par[tahunJadwal].str_pad($par[bulanJadwal], 2, "0", STR_PAD_LEFT)."' between concat(year(mulaiLibur), LPAD(month(mulaiLibur), 2, '0')) and concat(year(selesaiLibur), LPAD(month(selesaiLibur), 2, '0'))";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$tanggalLibur = $r[mulaiLibur];
		while($tanggalLibur <= $r[selesaiLibur]){
			list($tahunLibur, $bulanLibur, $hariLibur) = explode("-", $tanggalLibur);			
			$arrLibur[$tanggalLibur] = $tanggalLibur;
			
			$tanggalLibur = date("Y-m-d", dateAdd("d", 1, mktime(0,0,0, $bulanLibur, $hariLibur, $tahunLibur)));
		}
	}
	
	function gJadwal(){
		global $s,$inp,$par;
		$sql="select * from dta_shift where idShift='".$par[idShift]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$r[mulaiShift] = substr($r[mulaiShift],0,5);
		$r[selesaiShift] = substr($r[selesaiShift],0,5);
		
		return json_encode($r);
	}
	
	function setTable(){
		global $s,$inp,$par,$fFile,$cUsername,$arrParameter;		
		repField();				
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		
		if(!in_array(strtolower(substr($_FILES[fileData][name],-3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name],-4)), array("xlsx"))){			
			return "file harus dalam format .xls";
		}else{
			$fileUpload = $_FILES[fileData][tmp_name];
			$fileUpload_name = $_FILES[fileData][name];
			if(($fileUpload!="") and ($fileUpload!="none")){						
				fileUpload($fileUpload,$fileUpload_name,$fFile);
				$fileData = md5($cUsername."-".date("Y-m-d H:i:s")).".".getExtension($fileUpload_name);
				fileRename($fFile, $fileUpload_name, $fileData);			
			}
			
			
			db("DROP TABLE IF EXISTS tmp_jadwal");		
			db("CREATE TABLE IF NOT EXISTS tmp_jadwal (				 
				  idJadwal int(11) NOT NULL,
				  idPegawai int(11) NOT NULL,
				  bulanJadwal int(11) NOT NULL,
				  tahunJadwal int(11) NOT NULL,
				  tanggalJadwal_01 varchar(11) NOT NULL,
				  tanggalJadwal_02 varchar(11) NOT NULL,
				  tanggalJadwal_03 varchar(11) NOT NULL,
				  tanggalJadwal_04 varchar(11) NOT NULL,
				  tanggalJadwal_05 varchar(11) NOT NULL,
				  tanggalJadwal_06 varchar(11) NOT NULL,
				  tanggalJadwal_07 varchar(11) NOT NULL,
				  tanggalJadwal_08 varchar(11) NOT NULL,
				  tanggalJadwal_09 varchar(11) NOT NULL,
				  tanggalJadwal_10 varchar(11) NOT NULL,
				  tanggalJadwal_11 varchar(11) NOT NULL,
				  tanggalJadwal_12 varchar(11) NOT NULL,
				  tanggalJadwal_13 varchar(11) NOT NULL,
				  tanggalJadwal_14 varchar(11) NOT NULL,
				  tanggalJadwal_15 varchar(11) NOT NULL,
				  tanggalJadwal_16 varchar(11) NOT NULL,
				  tanggalJadwal_17 varchar(11) NOT NULL,
				  tanggalJadwal_18 varchar(11) NOT NULL,
				  tanggalJadwal_19 varchar(11) NOT NULL,
				  tanggalJadwal_20 varchar(11) NOT NULL,
				  tanggalJadwal_21 varchar(11) NOT NULL,
				  tanggalJadwal_22 varchar(11) NOT NULL,
				  tanggalJadwal_23 varchar(11) NOT NULL,
				  tanggalJadwal_24 varchar(11) NOT NULL,
				  tanggalJadwal_25 varchar(11) NOT NULL,
				  tanggalJadwal_26 varchar(11) NOT NULL,
				  tanggalJadwal_27 varchar(11) NOT NULL,
				  tanggalJadwal_28 varchar(11) NOT NULL,
				  tanggalJadwal_29 varchar(11) NOT NULL,
				  tanggalJadwal_30 varchar(11) NOT NULL,
				  tanggalJadwal_31 varchar(11) NOT NULL				  
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
			
			$inputFileName = $fFile.$fileData;
			require_once ('plugins/PHPExcel/IOFactory.php');			
			require_once ('plugins/PHPExcel/Shared/Date.php');
			
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
			$highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

			//  Loop through each row of the worksheet in turn
			for ($row = 1; $row <= $highestRow; $row++){ 
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':AH' . $row, NULL, TRUE, FALSE);			
				list($noPegawai, $nikPegawai, $namaPegawai, $tanggalJadwal_01, $tanggalJadwal_02, $tanggalJadwal_03, $tanggalJadwal_04, $tanggalJadwal_05, $tanggalJadwal_06, $tanggalJadwal_07, $tanggalJadwal_08, $tanggalJadwal_09, $tanggalJadwal_10, $tanggalJadwal_11, $tanggalJadwal_12, $tanggalJadwal_13, $tanggalJadwal_14, $tanggalJadwal_15, $tanggalJadwal_16, $tanggalJadwal_17, $tanggalJadwal_18, $tanggalJadwal_19, $tanggalJadwal_20, $tanggalJadwal_21, $tanggalJadwal_22, $tanggalJadwal_23, $tanggalJadwal_24, $tanggalJadwal_25, $tanggalJadwal_26, $tanggalJadwal_27, $tanggalJadwal_28, $tanggalJadwal_29, $tanggalJadwal_30, $tanggalJadwal_31) = $rowData[0];				
				$idPegawai = getField("select id from dta_pegawai where reg_no='".$nikPegawai."'");
				if($idPegawai > 0){
					$idJadwal = getField("select idJadwal from tmp_jadwal order by idJadwal desc limit 1") + 1;
					$sql="insert into tmp_jadwal (idJadwal, idPegawai, bulanJadwal, tahunJadwal, tanggalJadwal_01, tanggalJadwal_02, tanggalJadwal_03, tanggalJadwal_04, tanggalJadwal_05, tanggalJadwal_06, tanggalJadwal_07, tanggalJadwal_08, tanggalJadwal_09, tanggalJadwal_10, tanggalJadwal_11, tanggalJadwal_12, tanggalJadwal_13, tanggalJadwal_14, tanggalJadwal_15, tanggalJadwal_16, tanggalJadwal_17, tanggalJadwal_18, tanggalJadwal_19, tanggalJadwal_20, tanggalJadwal_21, tanggalJadwal_22, tanggalJadwal_23, tanggalJadwal_24, tanggalJadwal_25, tanggalJadwal_26, tanggalJadwal_27, tanggalJadwal_28, tanggalJadwal_29, tanggalJadwal_30, tanggalJadwal_31) values ('$idJadwal', '$idPegawai', '$par[bulanJadwal]', '$par[tahunJadwal]', '$tanggalJadwal_01', '$tanggalJadwal_02', '$tanggalJadwal_03', '$tanggalJadwal_04', '$tanggalJadwal_05', '$tanggalJadwal_06', '$tanggalJadwal_07', '$tanggalJadwal_08', '$tanggalJadwal_09', '$tanggalJadwal_10', '$tanggalJadwal_11', '$tanggalJadwal_12', '$tanggalJadwal_13', '$tanggalJadwal_14', '$tanggalJadwal_15', '$tanggalJadwal_16', '$tanggalJadwal_17', '$tanggalJadwal_18', '$tanggalJadwal_19', '$tanggalJadwal_20', '$tanggalJadwal_21', '$tanggalJadwal_22', '$tanggalJadwal_23', '$tanggalJadwal_24', '$tanggalJadwal_25', '$tanggalJadwal_26', '$tanggalJadwal_27', '$tanggalJadwal_28', '$tanggalJadwal_29', '$tanggalJadwal_30', '$tanggalJadwal_31')";
					db($sql);
				}
			}
			/*
			$dtaShift=arrayQuery("select kodeShift, concat(idShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where statusShift='t' order by idShift");						
			
			$arrNik = array();			
			if(is_array($arr)){				
				reset($arr);		
				while(list($id, $val)=each($arr)){					
					list($noPegawai, $nikPegawai, $namaPegawai) = $val;
					if($noPegawai > 0){
						$idPegawai = getField("select id from dta_pegawai where reg_no='".$nikPegawai."'");
						
						if($idPegawai > 0){
							$tanggal=1;
							for($i=3; $i<=34; $i++){
								$kodeShift = trim($val[$i]);
								if(!empty($kodeShift)){
									$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($tanggal, 2, "0", STR_PAD_LEFT);
									
									#shift									
									list($idShift, $mulaiJadwal, $selesaiJadwal) = explode("\t", $dtaShift[$kodeShift]);
													
									$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1") + 1;
									$sql=getField("select idJadwal from dta_jadwal where idPegawai='$idPegawai' and tanggalJadwal='$tanggalJadwal'") ?
									"update dta_jadwal set idShift='$idShift', mulaiJadwal='$mulaiJadwal', selesaiJadwal='$selesaiJadwal' where idPegawai='$idPegawai' and tanggalJadwal='$tanggalJadwal'":
									"insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, createBy, createTime) values ('$idJadwal', '$idPegawai', '$idShift', '$tanggalJadwal', '$mulaiJadwal', '$selesaiJadwal', '$cUsername', '".date('Y-m-d H:i:s')."')";
									db($sql);
									
									$tanggal++;
								}
							}
						}else{							
							$arrNik[$nikPegawai] = $nikPegawai;
						}			
					}
				}
				
				if(count($arrNik) > 0){
					echo "<script>
							alert('NPP : ".implode(", ", $arrNik)." belum terdaftar.');
							window.location='?".getPar($par)."';
						</script>";
				}
				
			}	*/		
			if(file_exists($fFile.$fileData) and $fileData!="")unlink($fFile.$fileData);
			
			return "fileData".$fileData;
		}				
	}
	
	function setData(){
		global $s,$inp,$par,$fFile,$cUsername,$arrParameter;
		
		$dtaShift=arrayQuery("select kodeShift, concat(idShift, '\t', mulaiShift, '\t', selesaiShift) from dta_shift where statusShift='t' order by idShift");
		$highestRow=getField("select max(idJadwal) from tmp_jadwal") * 31;
		
		$idJadwal = ceil($par[rowData]/31);
		$sql="select * from tmp_jadwal where idJadwal='".$idJadwal."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$result = $idJadwal;
		if($par[rowData] <= $highestRow){
			$idPegawai = $r[idPegawai];			
			$tanggal = $par[rowData]%31 == 0 ? 31 : $par[rowData]%31;
			$kodeShift = trim($r["tanggalJadwal_".str_pad($tanggal, 2, "0", STR_PAD_LEFT)]);
			//$result.=" X $par[rowData] : ".$tanggal;
			if(!empty($kodeShift)){				
				#shift									
				list($idShift, $mulaiJadwal, $selesaiJadwal) = explode("\t", $dtaShift[$kodeShift]);
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($tanggal, 2, "0", STR_PAD_LEFT);				
				if(empty($idShift))	$idShift = 0;
				if(empty($mulaiJadwal))	$mulaiJadwal = "00:00:00";
				if(empty($selesaiJadwal)) $selesaiJadwal = "00:00:00";
				
				$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1") + 1;
				$sql=getField("select idJadwal from dta_jadwal where idPegawai='$idPegawai' and tanggalJadwal='$tanggalJadwal'") ?
				"update dta_jadwal set idShift='$idShift', mulaiJadwal='$mulaiJadwal', selesaiJadwal='$selesaiJadwal' where idPegawai='$idPegawai' and tanggalJadwal='$tanggalJadwal';\n":
				"insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, createBy, createTime) values ('$idJadwal', '$idPegawai', '$idShift', '$tanggalJadwal', '$mulaiJadwal', '$selesaiJadwal', '$cUsername', '".date('Y-m-d H:i:s')."');\n";								
				db($sql);
			}
								
			$progresData = getAngka($par[rowData]/$highestRow * 100);	
			return $progresData."\t(".$progresData."%) ".getAngka($par[rowData])." of ".getAngka($highestRow)."\t".$result;	
		}
	}
	
	function endProses(){
		global $s,$inp,$par,$cUsername;
			
		return "upload data selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
	
	
	function hapusDetail(){
		global $s,$inp,$par,$cUsername;				
		$sql="delete from dta_jadwal where idJadwal='$par[idJadwal]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode,idJadwal")."';</script>";
	}
	
	function hapus(){
		global $s,$inp,$par,$cUsername;				
		$sql="delete from dta_jadwal where idPegawai='".$par[idPegawai]."' and month(tanggalJadwal)='".$par[bulanJadwal]."' and year(tanggalJadwal)='".$par[tahunJadwal]."'";
		db($sql);	
		echo "<script>window.location='?".getPar($par,"mode,idPegawai")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField();
		
		if(is_array($inp[idShift])){
			reset($inp[idShift]);
				while(list($tanggal)=each($inp[idShift])){	
					if(is_array($inp[idShift][$tanggal])){
						reset($inp[idShift][$tanggal]);
							while(list($id)=each($inp[idShift][$tanggal])){								
								$idJadwal = $inp[idJadwal][$tanggal][$id];
								$idShift = $inp[idShift][$tanggal][$id];
								$mulaiJadwal = $inp[mulaiJadwal][$tanggal][$id];
								$selesaiJadwal = $inp[selesaiJadwal][$tanggal][$id];
								$keteranganJadwal = $inp[keteranganJadwal][$tanggal][$id];
								$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($tanggal, 2, "0", STR_PAD_LEFT);

								if(empty($idJadwal)){
									$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1")+1;
									$sql="insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, keteranganJadwal, createBy, createTime) values ('$idJadwal', '$par[idPegawai]', '$idShift', '$tanggalJadwal', '$mulaiJadwal', '$selesaiJadwal', '$keteranganJadwal', '$cUsername', '".date('Y-m-d H:i:s')."')";
								}else{
									$sql="update dta_jadwal set idShift='$idShift', mulaiJadwal='$mulaiJadwal', selesaiJadwal='$selesaiJadwal', keteranganJadwal='$keteranganJadwal' where idJadwal='$idJadwal'";
								}
								db($sql);
							
							}
					}
			}
		}		
		
		echo "<script>window.location='?".getPar($par,"mode,idPegawai")."';</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$cUsername;				
		repField();		
		$idJadwal = getField("select idJadwal from dta_jadwal order by idJadwal desc limit 1")+1;		
		
		$sql="insert into dta_jadwal (idJadwal, idPegawai, idShift, tanggalJadwal, mulaiJadwal, selesaiJadwal, keteranganJadwal, createBy, createTime) values ('$idJadwal', '$par[idPegawai]', '".$inp[idShift][0][0]."', '".setTanggal($inp[tanggalJadwal])."', '$inp[mulaiJadwal]', '$inp[selesaiJadwal]', '$inp[keteranganJadwal]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);	
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function formUpload(){
		global $s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess;
				
		setValidation("is_null","fileData","anda harus mengisi file");	
		$text = getValidation();

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
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=fmtJadwal\" class=\"detil\">* download template.xlsx</a></div>
						<input type=\"button\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Upload\" onclick=\"setProses('".getPar($par,"mode")."');\"/>
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
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?".getPar($par,"mode")."';\"/>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function formInput(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$arrShift=arrayQuery("select idShift, namaShift from dta_shift where statusShift='t' order by idShift");			
		$arrShift[""]=" ";
		ksort($arrShift);
		reset($arrShift);
			
		setValidation("is_null","tanggalJadwal","anda harus mengisi tanggal");
		setValidation("is_null","inp[idShift][0][0]","anda harus mengisi shift");
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">										
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalJadwal\" name=\"inp[tanggalJadwal]\" size=\"10\" maxlength=\"10\" value=\"".date("d/m/Y")."\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Shift</label>
						<div class=\"field\">
							".comboKey("inp[idShift][0][0]", $arrShift, "", "onchange=\"getJadwal('0', '0', '".getPar($par,"mode, idShift")."');\"", "400px;")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Masuk</label>
						<div class=\"field\">
							<input type=\"text\" id=\"mulaiJadwal_0_0\" name=\"inp[mulaiJadwal]\" size=\"10\" maxlength=\"5\" value=\"\" class=\"vsmallinput\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keluar</label>
						<div class=\"field\">
							<input type=\"text\" id=\"selesaiJadwal_0_0\" name=\"inp[selesaiJadwal]\" size=\"10\" maxlength=\"5\" value=\"\" class=\"vsmallinput\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</div>
					</p>				
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganJadwal]\" name=\"inp[keteranganJadwal]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[keteranganJadwal]</textarea>
						</div>
					</p>
				</div>				
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
				</p>
			</form>	
			</div>";
		return $text;
	}
	
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$arrLibur;
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread(ucwords($par[mode]." data"))."								
			</div>
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				".comboMonth("par[bulanJadwal]", $par[bulanJadwal], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunJadwal]", $par[tahunJadwal], "", "onchange=\"document.getElementById('form').submit();\"")."
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
				<input type=\"hidden\" id=\"par[idPegawai]\" name=\"par[idPegawai]\" value=\"".$par[idPegawai]."\" >
				<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"".$par[mode]."\" >
			</div>
			</form>
			<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
		
		$sql="select * from dta_jadwal where idPegawai='".$par[idPegawai]."' and month(tanggalJadwal)='".$par[bulanJadwal]."' and year(tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			list($tahun, $bulan, $tanggal)=explode("-", $r[tanggalJadwal]);
			$arr["idShift"][intval($tanggal)][]=$r[idShift];
			$arr["mulaiJadwal"][intval($tanggal)][]=$r[mulaiJadwal];
			$arr["selesaiJadwal"][intval($tanggal)][]=$r[selesaiJadwal];
			$arr["keteranganJadwal"][intval($tanggal)][]=$r[keteranganJadwal];
			$arr["idJadwal"][intval($tanggal)][]=$r[idJadwal];
		}
		
		$text.="</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  enctype=\"multipart/form-data\">	
				<div id=\"general\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idJadwal")."',875,450);\" style=\"position:absolute; right:0; margin-right:20px;\"><span>Tambah Data</span></a>";
		$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>JADWAL KERJA</h3></div>
					</div>				
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th rowspan=\"2\" width=\"20\">No.</th>
							<th rowspan=\"2\" style=\"width:150px;\">Tanggal</th>
							<th rowspan=\"2\" style=\"min-width:150px;\">Shift</th>
							<th colspan=\"2\" style=\"width:150px;\">Jadwal</th>
							<th rowspan=\"2\" style=\"min-width:150px;\">Keterangan</th>";
					if(isset($menuAccess[$s]["delete"]))
					$text.="<th rowspan=\"2\" style=\"width:50px;\">Control</th>";
						$text.="</tr>
						<tr>							
							<th style=\"width:75px;\">Masuk</th>
							<th style=\"width:75px;\">Keluar</th>
						</tr>
					</thead>
					<tbody>";			
			$arrShift=arrayQuery("select idShift, namaShift from dta_shift where statusShift='t' order by idShift");			
			$arrShift[0] = "OFF";
			ksort($arrShift);
			reset($arrShift);
			
			list($normalShift, $mulaiNormal, $selesaiNormal)=explode("\t", getField("select concat(idShift,'\t', mulaiShift, '\t', selesaiShift) from dta_shift where statusShift='t' order by idShift limit 1"));			
			for($i=1; $i<=$day; $i++){
				$week = date("w", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
				$color = in_array($week, array(0,6)) ? " style=\"background:#f2dbdb;\"" : "";
										
				if(is_array($arr[idJadwal][$i])){				
					reset($arr[idJadwal][$i]);		
					while(list($id, $idJadwal)=each($arr[idJadwal][$i])){					
						$text.="<tr ".$color.">";
						$text.=$id == 0 ? "<td>$i.</td>
							<td>".str_pad($i, 2, "0", STR_PAD_LEFT)." ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal]."</td>":
							"<td>&nbsp;</td>
							<td>&nbsp;</td>";
						$text.="<td align=\"center\">".comboKey("inp[idShift][$i][$id]", $arrShift, $arr[idShift][$i][$id], "onchange=\"getJadwal('".$i."', '".$id."', '".getPar($par,"mode, idShift")."');\"", "98%;")."</td>
							<td align=\"center\">
							<input type=\"text\" id=\"mulaiJadwal_".$i."_".$id."\" name=\"inp[mulaiJadwal][$i][$id]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[mulaiJadwal][$i][$id],0,5)."\" class=\"vsmallinput\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
							</td>
							<td align=\"center\">
							<input type=\"text\" id=\"selesaiJadwal_".$i."_".$id."\" name=\"inp[selesaiJadwal][$i][$id]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[selesaiJadwal][$i][$id],0,5)."\" class=\"vsmallinput\" style=\"background: #fff  url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
							</td>
							<td align=\"center\"><input type=\"text\" id=\"inp[keteranganJadwal][$i][$id]\" name=\"inp[keteranganJadwal][$i][$id]\"  value=\"".$arr[keteranganJadwal][$i][$id]."\" class=\"mediuminput\" style=\"width:85%;\" />
							<input type=\"hidden\" id=\"inp[idJadwal][$i][$id]\" name=\"inp[idJadwal][$i][$id]\" value=\"".$idJadwal."\"></td>";
						if(isset($menuAccess[$s]["delete"])) $text.="<td align=\"center\"><a href=\"?par[mode]=delDet&par[idJadwal]=".$idJadwal."".getPar($par,"mode,idJadwal")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";
						$text.="</tr>";
					}
				}else{			
					if(empty($arr[idShift][$i][0])) $arr[idShift][$i][0] = $normalShift;
					if(empty($arr[mulaiJadwal][$i][0])) $arr[mulaiJadwal][$i][0] = $mulaiNormal;
					if(empty($arr[selesaiJadwal][$i][0])) $arr[selesaiJadwal][$i][0] = $selesaiNormal;
					$text.="<tr ".$color.">
						<td>$i.</td>
						<td>".str_pad($i, 2, "0", STR_PAD_LEFT)." ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal]."</td>
						<td align=\"center\">".comboKey("inp[idShift][$i][0]", $arrShift, $arr[idShift][$i][0], "onchange=\"getJadwal('".$i."', '0', '".getPar($par,"mode, idShift")."');\"", "98%;")."</td>
						<td align=\"center\">
						<input type=\"text\" id=\"mulaiJadwal_".$i."_0\" name=\"inp[mulaiJadwal][$i][0]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[mulaiJadwal][$i][0],0,5)."\" class=\"vsmallinput\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</td>
						<td align=\"center\">
						<input type=\"text\" id=\"selesaiJadwal_".$i."_0\" name=\"inp[selesaiJadwal][$i][0]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[selesaiJadwal][$i][0],0,5)."\" class=\"vsmallinput\" style=\"background: #fff  url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</td>
						<td align=\"center\"><input type=\"text\" id=\"inp[keteranganJadwal][$i][0]\" name=\"inp[keteranganJadwal][$i][0]\"  value=\"".$arr[keteranganJadwal][$i][0]."\" class=\"mediuminput\" style=\"width:85%;\" />
						<input type=\"hidden\" id=\"inp[idJadwal][$i][0]\" name=\"inp[idJadwal][$i][0]\" value=\"0\"></td>";
					if(isset($menuAccess[$s]["delete"])) $text.="<td align=\"center\"><a href=\"?par[mode]=delDet&par[idJadwal]=".$arr[idJadwal][$i][0]."".getPar($par,"mode,idJadwal")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";
					$text.="</tr>";
				}
			}
					
			$text.="</tbody>					
					</table>
				</div>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>
				</p>
			</form>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$arrLibur;		
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));				
		
		$cols = 4+$day;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		for($i=4; $i<=4+$day; $i++){
			$arrNot[] = $i;
		}
		$text = table($cols, $arrNot, "lst", "true", "h");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
				".$arrParameter[39]." : ".comboData("select * from mst_data where statusData='t' and kodeCategory='X05' order by urutanData","kodeData","namaData","par[idDivisi]","ALL",$par[idDivisi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
				<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
				".comboMonth("par[bulanJadwal]", $par[bulanJadwal], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunJadwal]", $par[tahunJadwal], "", "onchange=\"document.getElementById('form').submit();\"")."
				<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			</form>
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
				".comboArray("pSearch", array("All", "Nama", "NPP"), $par[paramData])."
			</p>
			</div>			
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_inboxo\" onclick=\"openBox('popup.php?par[mode]=upl".getPar($par,"mode")."',725,250);\"><span>Upload Data</span></a>";
		$text.=" <a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"2\" style=\"vertical-align:middle;\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"min-width:100px; vertical-align:middle;\">NPP</th>
					<th rowspan=\"2\" style=\"min-width:350px; vertical-align:middle;\">Nama</th>";
			
			$arrHari = array("MG", "SN", "SL", "RB", "KM", "JM", "SB");
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
				$hari = date('w', strtotime( $tanggalJadwal));
				$background = (in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur)) ? "background:#ffbbbb;" : "";
				$text.="<th style=\"min-width:20px; vertical-align:middle; ".$background." color:#000;\">".$arrHari[$hari]."</th>";
			}
			
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\"  style=\"min-width:50px; vertical-align:middle;\">Kontrol</th>";
			$text.="</tr>
				<tr>";
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
				$hari = date('w', strtotime( $tanggalJadwal));
				$background = (in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur)) ? "background:#ffbbbb;" : "";
				$text.="<th style=\"min-width:20px; vertical-align:middle; ".$background." color:#000;\">$i</th>";
			}
			$text.="</tr>
			</thead>
			<tbody></tbody>
			<tfoot>";
		
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
			
			$filter="where id is not null and status='".$status."'";
			if(!empty($par[idDivisi])) $filter.= " and div_id = '".$par[idDivisi]."'";			
			$sql="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[idShift];
			}			
						
			$sql="select * from dta_pegawai $filter order by name";
			$res=db($sql);		
			$defShift=getField("select idShift from dta_shift where statusShift='t' order by idShift limit 1");
			while($r=mysql_fetch_array($res)){			
				for($i=1; $i<=$day; $i++){
					$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);				
					
					if(is_array($arrJadwal["$r[id]"][$tanggalJadwal])){				
						reset($arrJadwal["$r[id]"][$tanggalJadwal]);		
						while(list($id, $idShift)=each($arrJadwal["$r[id]"][$tanggalJadwal])){
							$hari = date('w', strtotime( $tanggalJadwal));
							if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
								$idShift = $idShift;
							}else{
								$idShift = empty($idShift) ? $defShift : $idShift;
							}
							if(empty($idShift)) $idShift = 0;
							$cntJadwal[$idShift][$tanggalJadwal]++;	
						}
					}else{
						$idShift = $defShift;
						$hari = date('w', strtotime( $tanggalJadwal));
						if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
							$idShift = $idShift;
						}else{
							$idShift = empty($idShift) ? $defShift : $idShift;
						}
						if(empty($idShift)) $idShift = 0;
						$cntJadwal[$idShift][$tanggalJadwal]++;	
					}
				}			
			}
			
			$sql="select * from dta_shift where statusShift='t' order by idShift";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$text.="<tr>
					<td width=\"20\">&nbsp;</td>
					<td style=\"min-width:100px; vertical-align:middle;\">&nbsp;</td>
					<td style=\"min-width:350px; vertical-align:middle;\"><strong>$r[namaShift]</strong></td>";			
							
				for($i=1; $i<=$day; $i++){
					$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
					$text.="<td align=\"right\">".getAngka($cntJadwal["$r[idShift]"][$tanggalJadwal])."</td>";
					$sumJadwal[$tanggalJadwal]+=$cntJadwal["$r[idShift]"][$tanggalJadwal];
				}
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<td style=\"min-width:50px; vertical-align:middle;\">&nbsp;</td>";	
				$text.="</tr>";
			}
			
			$text.="<tr>
					<td width=\"20\">&nbsp;</td>
					<td style=\"min-width:100px; vertical-align:middle;\">&nbsp;</td>
					<td style=\"min-width:350px; vertical-align:middle;\"><strong>OFF</strong></td>";
							
				for($i=1; $i<=$day; $i++){
					$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
					$text.="<td align=\"right\">".getAngka($cntJadwal[0][$tanggalJadwal])."</td>";
					$sumJadwal[$tanggalJadwal]+=$cntJadwal[0][$tanggalJadwal];
				}
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<td style=\"min-width:50px; vertical-align:middle;\">&nbsp;</td>";	
				$text.="</tr>";
			
			$text.="<tr>
					<td width=\"20\">&nbsp;</td>
					<td style=\"min-width:100px; vertical-align:middle;\">&nbsp;</td>
					<td style=\"min-width:350px; vertical-align:middle;\"><strong>JUMLAH</strong></td>";
							
				for($i=1; $i<=$day; $i++){
					$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
					$text.="<td align=\"right\">".getAngka($sumJadwal[$tanggalJadwal])."</td>";
				}
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<td style=\"min-width:50px; vertical-align:middle;\">&nbsp;</td>";	
				$text.="</tr>
			</tfoot>
			</table>
			</div>";
		
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s]." - ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}
		
		return $text;
	}			
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
		
		$sql="select * from att_setting where idPegawai='$par[idPegawai]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
				
		$statusMesin =  $r[statusMesin] == "f" ? "Tidak Aktif" : "Aktif";
		$text.="</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" class=\"stdform\">	
				<div id=\"general\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>SETTING</h3></div>
					</div>				
					<p>
						<label class=\"l-input-small\">Jadwal Kerja</label>
						<span class=\"field\">".getField("select namaShift from dta_shift where idShift='$r[idShift]'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Mesin Jadwal</label>
						<span class=\"field\">".getField("select namaMesin from dta_mesin where idMesin='$r[idMesin]'")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[keteranganSetting])."&nbsp;</span>
					</p>
					
					
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>KARTU AKSES</h3></div>
					</div>
					<p>
						<label class=\"l-input-small\">No. Kartu</label>
						<span class=\"field\">".$r[nomorSetting]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Masa Berlaku</label>
						<span class=\"field\">".getTanggal($r[mulaiSetting],"t")." <strong>s.d</strong> ".getTanggal($r[selesaiSetting],"t")."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$statusMesin."&nbsp;</span>
					</p>
				</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\" />
				</p>
				</form>";
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrParameter,$arrLibur;
		require_once 'plugins/PHPExcel.php';
				
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$cols = 4;
		for($i=1; $i<=$day; $i++){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(5);
			$cols++;
		}		
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$namaDivisi = getField("select namaData from mst_data where kodeData='".$par[idDivisi]."'");
		$namaDepartemen = getField("select namaData from mst_data where kodeData='".$par[idDepartemen]."'");				
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'SCHEDULE'.strtoupper(getField("select concat(' ',namaData) from mst_data where kodeData='".$par[idDivisi]."'")).'');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanJadwal]).' '.$par[tahunJadwal]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols-1).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
		$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols-1).'5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);				
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'ID ABSEN');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA');
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		
		$cols=4;
		$arrHari = array("MG", "SN", "SL", "RB", "KM", "JM", "SB");
		for($i=1; $i<=$day; $i++){			
			$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
			$hari = date('w', strtotime( $tanggalJadwal));
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', $arrHari[$hari]);		
			$cols++;
		}
		
		$cols=4;
		for($i=1; $i<=$day; $i++){
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', $i);		
			$cols++;
		}		
									
		$rows = 6;
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		$filter="where id is not null and status='".$status."'";
			if(!empty($par[idDivisi])) $filter.= " and div_id = '".$par[idDivisi]."'";			
		$sql="select t1.*, t2.kodeShift from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			if(empty($r[kodeShift])) $r[kodeShift] = "X";
			$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[kodeShift];
		}
		
		$defShift=getField("select kodeShift from dta_shift where statusShift='t' order by idShift limit 1");
		$sql="select * from dta_pegawai $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols-1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);							
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[reg_no]);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[name]));
			
			$cols=4;
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);				
								
				$kodeShift = array();
				if(is_array($arrJadwal["$r[id]"][$tanggalJadwal])){				
					reset($arrJadwal["$r[id]"][$tanggalJadwal]);		
					while(list($id, $idShift)=each($arrJadwal["$r[id]"][$tanggalJadwal])){
						$kodeShift[] = $idShift;						
					}
				}else{
					$kodeShift[] = $defShift;					
				}
				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, implode("/", $kodeShift));
				
				$cols++;				
			}	
			$rows++;
		}					
		
		$arrJadwal = array();	
		$filter="where id is not null and status='".$status."'";
		if(!empty($par[idDivisi])) $filter.= " and div_id = '".$par[idDivisi]."'";			
		$sql="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[idShift];
		}			
					
		$sql="select * from dta_pegawai $filter order by name";
		$res=db($sql);		
		$defShift=getField("select idShift from dta_shift where statusShift='t' order by idShift limit 1");
		while($r=mysql_fetch_array($res)){			
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);				
				
				if(is_array($arrJadwal["$r[id]"][$tanggalJadwal])){				
					reset($arrJadwal["$r[id]"][$tanggalJadwal]);		
					while(list($id, $idShift)=each($arrJadwal["$r[id]"][$tanggalJadwal])){
						$hari = date('w', strtotime( $tanggalJadwal));
						if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
							$idShift = $idShift;
						}else{
							$idShift = empty($idShift) ? $defShift : $idShift;
						}
						if(empty($idShift)) $idShift = 0;
						$cntJadwal[$idShift][$tanggalJadwal]++;	
					}
				}else{
					$idShift = $defShift;
					$hari = date('w', strtotime( $tanggalJadwal));
					if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
						$idShift = $idShift;
					}else{
						$idShift = empty($idShift) ? $defShift : $idShift;
					}
					if(empty($idShift)) $idShift = 0;
					$cntJadwal[$idShift][$tanggalJadwal]++;	
				}
			}			
		}

		$sql="select * from dta_shift where statusShift='t' order by idShift";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':'.numToAlpha($cols).$rows)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols-1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[namaShift]));
			
			$cols=4;
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $cntJadwal["$r[idShift]"][$tanggalJadwal]);
				$sumJadwal[$tanggalJadwal]+=$cntJadwal["$r[idShift]"][$tanggalJadwal];									
				
				$cols++;
			}	
			$rows++;
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':'.numToAlpha($cols).$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols-1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper("OFF"));
		
		$cols=4;
		for($i=1; $i<=$day; $i++){
			$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $cntJadwal[0][$tanggalJadwal]);
			$sumJadwal[$tanggalJadwal]+=$cntJadwal[0][$tanggalJadwal];	
			$cols++;
		}
		$rows++;
		
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rows.':'.numToAlpha($cols).$rows)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols-1).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper("TOTAL"));
		
		$cols=4;
		for($i=1; $i<=$day; $i++){
			$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $sumJadwal[$tanggalJadwal]);
			$cols++;
		}

		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$cols=4;
		for($i=1; $i<=$day; $i++){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		}		
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(85);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower(getBulan($par[bulanJadwal])." ".$par[tahunJadwal])));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s]." - ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal])).".xls");
	}
	
	function lData(){
		global $s,$par,$menuAccess,$arrLibur,$arrParameter;		
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		$sWhere= "where id is not null and status='".$status."'";		
		if(!empty($par[idDivisi]))
			$sWhere.= " and div_id = '".$par[idDivisi]."'";
		
		if($_GET['pSearch'] == "Nama")
			$sWhere.= " and lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else if($_GET['pSearch'] == "NPP")
			$sWhere.= " and lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'";
		else
			$sWhere.= " and (
				lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";			
					
		$sql="select t1.*, t2.kodeShift from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			if(empty($r[kodeShift])) $r[kodeShift] = "X";
			$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[kodeShift];
		}
		
		$arrOrder = array(	
			"name",			
			"reg_no",
			"name",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];		
		$sql="select * from dta_pegawai $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => count(arrayQuery("select id from dta_pegawai $sWhere")),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		$defShift=getField("select kodeShift from dta_shift where statusShift='t' order by idShift limit 1");
		while($r=mysql_fetch_array($res)){
			$no++;			
			$statusShift=$r[statusShift] == "t" ?
					"<img src=\"styles/images/t.png\" title=\"Active\">":
					"<img src=\"styles/images/f.png\" title=\"Not Active\">";			
			
			$controlRoster="";
			
			if(isset($menuAccess[$s]["edit"]))
			$controlRoster.="<a href=\"?par[mode]=edit&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"]))
			$controlRoster.=" <a href=\"?par[mode]=del&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			
			$data=array();
			$data[]="<div align=\"center\">".$no.".</div>";			
			$data[]="<div align=\"center\">".$r[reg_no]."</div>";
			$data[]="<div align=\"left\">".strtoupper($r[name])."</div>";
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);				
				
				$kodeShift = array();
				if(is_array($arrJadwal["$r[id]"][$tanggalJadwal])){				
					reset($arrJadwal["$r[id]"][$tanggalJadwal]);		
					while(list($id, $idShift)=each($arrJadwal["$r[id]"][$tanggalJadwal])){
						$kodeShift[] = $idShift;						
					}
				}else{
					$kodeShift[] = $defShift;					
				}

				
				$hari = date('w', strtotime( $tanggalJadwal));
				if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
					$kodeShift = $kodeShift;
				}else{
					$kodeShift = empty($kodeShift) ? $defShift : $kodeShift;
				}
				
				$background = "";																
				if(strtolower($kodeShift) == "x") $background = "style=\"background:#bfbfbf; color:#000;\"";
				
				$data[]="<div align=\"center\" ".$background.">".implode("/", $kodeShift)."</div>";
			}
			$data[]="<div align=\"center\">".$controlRoster."</div>";
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
			break;
			
			case "get":
				$text = gJadwal();
			break;
			case "det":
				$text = detail();
			break;
			case "delDet":
				if(isset($menuAccess[$s]["delete"])) $text = hapusDetail(); else $text = lihat();
			break;			
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formInput() : tambah(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
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