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
	
	function upload(){
		global $s,$inp,$par,$fFile,$cUsername,$arrParameter;		
		repField();				
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		
		if(!in_array(strtolower(substr($_FILES[fileData][name],-3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name],-4)), array("xlsx"))){			
			echo formUpload();
			echo "<script>
					alert('file harus dalam format .xls');
					window.location='?".getPar($par)."';
				</script>";
		}else{
			$fileUpload = $_FILES[fileData][tmp_name];
			$fileUpload_name = $_FILES[fileData][name];
			if(($fileUpload!="") and ($fileUpload!="none")){						
				fileUpload($fileUpload,$fileUpload_name,$fFile);
				$fileData = md5($cUsername."-".date("Y-m-d H:i:s")).".".getExtension($fileUpload_name);
				fileRename($fFile, $fileUpload_name, $fileData);			
			}
			
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
				$arr[] = $rowData[0];
			}
			
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
			}			
			if(file_exists($fFile.$fileData) and $fileData!="")unlink($fFile.$fileData);
		}
				
		echo "<script>window.parent.location='index.php?".getPar($par,"mode")."';</script>";
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
		global $s,$inp,$par,$cUsername, $det_;
		repField();
		// print_r($inp[idShift]);
		// die();
		
		if(is_array($det_[idShift])){
			reset($det_[idShift]);
				while(list($tanggal)=each($det_[idShift])){	
					if(is_array($det_[idShift][$tanggal])){
						reset($det_[idShift][$tanggal]);
							while(list($id)=each($det_[idShift][$tanggal])){								
								$idJadwal = $det_[idJadwal][$tanggal][$id];
								$idShift = $det_[idShift][$tanggal][$id];
								$mulaiJadwal = $det_[mulaiJadwal][$tanggal][$id];
								$selesaiJadwal = $det_[selesaiJadwal][$tanggal][$id];
								$keteranganJadwal = $det_[keteranganJadwal][$tanggal][$id];
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
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">
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
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Upload\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>						
					</p>
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
				$tanggalJadwal = date("Y-m-d", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT)));
				$color = in_array($week, array(0,6)) ? " style=\"background:#f2dbdb;\"" : "";
										
				if(is_array($arr[idJadwal][$i])){				
					reset($arr[idJadwal][$i]);		
					while(list($id, $idJadwal)=each($arr[idJadwal][$i])){					
						$text.="<tr ".$color.">";
						$text.=$id == 0 ? "<td>$i.</td>
							<td>".str_pad($i, 2, "0", STR_PAD_LEFT)." ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal]."</td>":
							"<td>&nbsp;</td>
							<td>&nbsp;</td>";
						$text.="<td align=\"center\">".comboKey("det_[idShift][$i][$id]", $arrShift, $arr[idShift][$i][$id], "onchange=\"getJadwal('".$i."', '".$id."', '".getPar($par,"mode, idShift")."');\"", "98%;")."</td>
							<td align=\"center\">
							<input type=\"text\" id=\"mulaiJadwal_".$i."_".$id."\" name=\"det_[mulaiJadwal][$i][$id]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[mulaiJadwal][$i][$id],0,5)."\" class=\"vsmallinput\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
							</td>
							<td align=\"center\">
							<input type=\"text\" id=\"selesaiJadwal_".$i."_".$id."\" name=\"det_[selesaiJadwal][$i][$id]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[selesaiJadwal][$i][$id],0,5)."\" class=\"vsmallinput\" style=\"background: #fff  url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
							</td>
							<td align=\"center\"><input type=\"text\" id=\"det_[keteranganJadwal][$i][$id]\" name=\"det_[keteranganJadwal][$i][$id]\"  value=\"".$arr[keteranganJadwal][$i][$id]."\" class=\"mediuminput\" style=\"width:85%;\" />
							<input type=\"hidden\" id=\"det_[idJadwal][$i][$id]\" name=\"det_[idJadwal][$i][$id]\" value=\"".$idJadwal."\"></td>";
						if(isset($menuAccess[$s]["delete"])) $text.="<td align=\"center\"><a href=\"?par[mode]=delDet&par[idJadwal]=".$idJadwal."".getPar($par,"mode,idJadwal")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a></td>";
						$text.="</tr>";
					}
				}else{			
					if(empty($arr[idShift][$i][0]) && !in_array($week, array(0,6)) && !in_array($tanggalJadwal, $arrLibur)) $arr[idShift][$i][0] = $normalShift;
					if(empty($arr[mulaiJadwal][$i][0]) && !in_array($week, array(0,6)) && !in_array($tanggalJadwal, $arrLibur)) $arr[mulaiJadwal][$i][0] = $mulaiNormal;
					if(empty($arr[selesaiJadwal][$i][0]) && !in_array($week, array(0,6)) && !in_array($tanggalJadwal, $arrLibur)) $arr[selesaiJadwal][$i][0] = $selesaiNormal;
					$text.="<tr ".$color.">
						<td>$i.</td>
						<td>".str_pad($i, 2, "0", STR_PAD_LEFT)." ".getBulan($par[bulanJadwal])." ".$par[tahunJadwal]."</td>
						<td align=\"center\">".comboKey("det_[idShift][$i][0]", $arrShift, $arr[idShift][$i][0], "onchange=\"getJadwal('".$i."', '0', '".getPar($par,"mode, idShift")."');\"", "98%;")."</td>
						<td align=\"center\">
						<input type=\"text\" id=\"mulaiJadwal_".$i."_0\" name=\"det_[mulaiJadwal][$i][0]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[mulaiJadwal][$i][0],0,5)."\" class=\"vsmallinput\" style=\"background: #fff url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</td>
						<td align=\"center\">
						<input type=\"text\" id=\"selesaiJadwal_".$i."_0\" name=\"det_[selesaiJadwal][$i][0]\" size=\"10\" maxlength=\"5\" value=\"".substr($arr[selesaiJadwal][$i][0],0,5)."\" class=\"vsmallinput\" style=\"background: #fff  url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;\"/>
						</td>
						<td align=\"center\"><input type=\"text\" id=\"det_[keteranganJadwal][$i][0]\" name=\"det_[keteranganJadwal][$i][0]\"  value=\"".$arr[keteranganJadwal][$i][0]."\" class=\"mediuminput\" style=\"width:85%;\" />
						<input type=\"hidden\" id=\"det_[idJadwal][$i][0]\" name=\"det_[idJadwal][$i][0]\" value=\"0\"></td>";
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
		global $s, $par, $arrTitle, $arrParameter, $menuAccess, $arrLibur, $areaCheck;	

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
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 700px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "310px")."
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
										<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
										".comboMonth("par[bulanJadwal]", $par[bulanJadwal], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunJadwal]", $par[tahunJadwal], "", "onchange=\"document.getElementById('form').submit();\"")."
										<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 230px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
										".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X04' order by t1.urutanData", "id", "description", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
				</div>
			<div id=\"pos_l\" style=\"float:left;\">
				<p>
					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
					".comboArray("pSearch", array("All", "Nama", "NPP"), $par[paramData])."
					".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[statusPegawai]","All",$par[statusPegawai],"onchange=\"document.getElementById('form').submit();\"", "110px")."
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
			$filter="where id is not null and status='".$status."' and location in ( $areaCheck )";
			if(!empty($par[idLokasi])) $filter.= " and location = '".$par[idLokasi]."'";	
			if(!empty($par[statusPegawai])) $filter.= " and cat = '".$par[statusPegawai]."'";			
			if(!empty($par[divId])) $filter.= " and div_id='".$par[divId]."'";
			if(!empty($par[deptId])) $filter.= " and dept_id='".$par[deptId]."'";
			if(!empty($par[unitId])) $filter.= " and unit_id='".$par[unitId]."'";
			
			$sql="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(empty($r[kodeShift])) $r[kodeShift] = "X";
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
							if(empty($idShift)) $idShift = "OFF";
							$cntJadwal[$idShift][$tanggalJadwal]++;	
						}
					}else{
						$idShift = $defShift;
						$hari = date('w', strtotime( $tanggalJadwal));
						if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
							$idShift = "OFF";
						}else{
							$idShift = empty($idShift) ? $defShift : $idShift;
						}
						if(empty($idShift)) $idShift = "OFF";
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
					$text.="<td align=\"right\">".getAngka($cntJadwal["OFF"][$tanggalJadwal])."</td>";
					$sumJadwal[$tanggalJadwal]+=$cntJadwal["OFF"][$tanggalJadwal];
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
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrLibur,$areaCheck;
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
		$filter="where id is not null and status='".$status."' and location in ( $areaCheck )";
		
		if(!empty($par[idLokasi])) $filter.= " and location = '".$par[idLokasi]."'";			
		if(!empty($par[statusPegawai])) $filter.= " and cat = '".$par[statusPegawai]."'";			
		if(!empty($par[divId])) $filter.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId])) $filter.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId])) $filter.= " and unit_id='".$par[unitId]."'";
		
		$sql="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(empty($r[kodeShift])) $r[kodeShift] = "X";
			$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[kodeShift];
			$arrKode["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[kodeShift];
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
				$hari = date('w', strtotime( $tanggalJadwal));
				if(is_array($arrJadwal["$r[id]"][$tanggalJadwal])){				
					reset($arrJadwal["$r[id]"][$tanggalJadwal]);		
					while(list($id, $idShift)=each($arrJadwal["$r[id]"][$tanggalJadwal])){
						$kodeShift[] = $idShift;						
					}
				}else if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
					$kodeShift[] = "OFF";
				}else{
					$kodeShift[] = $defShift;	
				}
				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, implode("/", $kodeShift));
				
				$cols++;				
			}	
			$rows++;
		}	

		$sql="select * from dta_jadwal t1 left join dta_shift t2 on (t1.idShift=t2.idShift) where month(t1.tanggalJadwal)='".$par[bulanJadwal]."' and year(t1.tanggalJadwal)='".$par[tahunJadwal]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(empty($r[kodeShift])) $r[kodeShift] = "X";
			$arrJadwal["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[idShift];
			$arrKode["$r[idPegawai]"]["$r[tanggalJadwal]"][] = $r[kodeShift];
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
						if(empty($idShift)) $idShift = "OFF";
						$cntJadwal[$idShift][$tanggalJadwal]++;	
					}
				}else{
					$idShift = $defShift;
					$hari = date('w', strtotime( $tanggalJadwal));
					if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
						$idShift = "OFF";
					}else{
						$idShift = empty($idShift) ? $defShift : $idShift;
					}
					if(empty($idShift)) $idShift = "OFF";
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
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $cntJadwal["OFF"][$tanggalJadwal]);
			$sumJadwal[$tanggalJadwal]+=$cntJadwal["OFF"][$tanggalJadwal];	
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
		global $s,$par,$menuAccess,$arrLibur,$areaCheck,$arrParameter;		
		if(empty($par[bulanJadwal])) $par[bulanJadwal] = date('m');
		if(empty($par[tahunJadwal])) $par[tahunJadwal] = date('Y');
		$day = date("t", strtotime($par[tahunJadwal]."-".$par[bulanJadwal]."-01"));
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		$sWhere="where id is not null and status='".$status."' and location in ( $areaCheck )";
		
		if(!empty($par[idLokasi])) $sWhere.= " and location = '".$par[idLokasi]."'";	
		if(!empty($par[statusPegawai])) $sWhere.= " and cat = '".$par[statusPegawai]."'";		
		if(!empty($par[divId])) $sWhere.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId])) $sWhere.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId])) $sWhere.= " and unit_id='".$par[unitId]."'";
		
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
			$data[]="<div align=\"left\">".$r[reg_no]."</div>";
			$data[]="<div align=\"left\">".strtoupper($r[name])."</div>";
			for($i=1; $i<=$day; $i++){
				$tanggalJadwal = $par[tahunJadwal]."-".$par[bulanJadwal]."-".str_pad($i, 2, "0", STR_PAD_LEFT);				
				
				$kodeShift = array();
				$hari = date('w', strtotime( $tanggalJadwal));
				if(is_array($arrJadwal["$r[id]"][$tanggalJadwal])){				
					reset($arrJadwal["$r[id]"][$tanggalJadwal]);		
					while(list($id, $idShift)=each($arrJadwal["$r[id]"][$tanggalJadwal])){
						$kodeShift[] = $idShift;						
					}
				}else if((in_array($hari, array(0,6)) || in_array($tanggalJadwal, $arrLibur))){
					$kodeShift[] = "OFF";
				}else{
					$kodeShift[] = $defShift;	
				}

				$background = "";																
				if(strtolower(implode("/", $kodeShift)) == "off") $background = "style=\"background:#ffbbbb; color:#000;\"";
				
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
			case "upl":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formUpload() : upload(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>