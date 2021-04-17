<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/upload/";
		
	function ubah(){
		global $s,$inp,$par,$cUsername,$arrParameter;
		repField();
		
		$sql="update pay_upload set nilaiUpload='".setAngka($inp[nilaiUpload])."', keteranganUpload='".$inp[keteranganUpload]."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where bulanUpload='$par[bulanUpload]' and tahunUpload='$par[tahunUpload]' and idPegawai='$par[idPegawai]' and idKomponen='$par[idKomponen]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}	
	
	
	function hapus(){
		global $s,$inp,$par,$cUsername;
		$sql="delete from pay_upload where bulanUpload='$par[bulanUpload]' and tahunUpload='$par[tahunUpload]' and idPegawai='$par[idPegawai]' and idKomponen='$par[idKomponen]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";	
	}	
	
	function semua(){
		global $s,$inp,$par,$cUsername;
		$sql="delete from pay_upload where bulanUpload='$par[bulanUpload]' and tahunUpload='$par[tahunUpload]' and idKomponen='$par[idKomponen]'";
		db($sql);
		
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode")."';</script>";
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
					  idUpload int(11) NOT NULL,
					  bulanUpload int(11) NOT NULL,
					  tahunUpload int NOT NULL,					  
					  nikUpload varchar(30) NOT NULL,
					  namaUpload varchar(150) NOT NULL,
					  nilaiUpload decimal(20,2) NOT NULL,
					  keteranganUpload text NOT NULL,
					  statusUpload varchar(150) NOT NULL,
					  createBy varchar(30) NOT NULL,
					  createTime datetime NOT NULL,
					  PRIMARY KEY (idUpload)
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
			$rowData = $sheet->rangeToArray('A' . $par[rowData] . ':F' . $par[rowData], NULL, TRUE, TRUE);				
			$dta = $rowData[0];
			
			$result.= " ".$dta[0]." ".$dta[3];		
			if(is_numeric(trim(strtolower($dta[0])))){
				$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
								
				$tahunUpload = trim($dta[0]);
				$bulanUpload = trim($dta[1]);
				$nikPegawai = trim($dta[2]);
				$namaUpload = trim(str_replace("'","\'",$dta[3]));
				$nilaiUpload = setAngka(trim($dta[4]));
				$keteranganUpload = trim($dta[5]);
				
				$idPegawai = getField("select id from emp where reg_no='".$nikPegawai."' and status='".$status."'");				
				$idKomponen = getField("select idKomponen from dta_komponen where kodeKomponen='".$arrParam[$s]."'");
				$statusUpload = $idPegawai > 0 ? "OK" : "NPP : ".$nikPegawai." belum terdaftar";
												
				if($idPegawai > 0){					
					$sql="delete from pay_upload where bulanUpload='".$bulanUpload."' and tahunUpload='".$tahunUpload."' and idPegawai='".$idPegawai."' and idKomponen='".$idKomponen."' and keteranganUpload='".$keteranganUpload."' ";
					db($sql);	
					$sql="insert into pay_upload (bulanUpload, tahunUpload, idPegawai, idKomponen, nilaiUpload, keteranganUpload, createBy, createTime) values ('".$bulanUpload."', '".$tahunUpload."', '".$idPegawai."', '".$idKomponen."', '".$nilaiUpload."', '".$keteranganUpload."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
					db($sql);										
				}				
				
				$idUpload = getField("select idUpload from tmp_upload order by idUpload desc limit 1") + 1;
				$sql="insert into tmp_upload (idUpload, bulanUpload, tahunUpload, nikUpload, namaUpload, nilaiUpload, keteranganUpload, statusUpload, createBy, createTime) values ('".$idUpload."', '".$bulanUpload."', '".$tahunUpload."', '".$nikPegawai."', '".$namaUpload."', '".setAngka($nilaiUpload)."', '".$keteranganUpload."', '".$statusUpload."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
				db($sql);
								
				usleep(5000);
			}
			
			$progresData = getAngka($par[rowData]/$highestRow * 100);	
			return $progresData."\t(".$progresData."%) ".getAngka($par[rowData])." of ".getAngka($highestRow)."\t".$result;	
		}
	}
	
	function endProses(){
		global $s,$inp,$par,$cUsername;
		
		$file = "files/Upload Manual.log";
		if(file_exists($file))unlink($file);
		$fileName = fopen($file, "w");
								
		$sql="select * from tmp_upload order by idUpload";
		$res=db($sql);
		$text.= "TAHUN\tBULAN\tNPP\tNAMA\tNILAI\tKETERANGAN\tSTATUS\r\n";
		while($r=mysql_fetch_array($res)){
			$text.= $r[tahunUpload]."\t".$r[bulanUpload]."\t".$r[nikUpload]."\t".$r[namaUpload]."\t".getAngka($r[nilaiUpload])."\t".$r[keteranganUpload]."\t".$r[statusUpload]."\r\n";			
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
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=fmtUpload\" class=\"detil\">* download template.xlsx</a></div>
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
					<a href=\"download.php?d=logManual\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>				
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?par[tanggalAbsen]=' + document.getElementById('tanggalAbsen').value + '".getPar($par,"mode,tanggalAbsen")."';\"/>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$arrParameter, $areaCheck;
		if(empty($par[tahunUpload])) $par[tahunUpload] = date('Y');
		$par[idKomponen] = getField("select idKomponen from dta_komponen where kodeKomponen='".$arrParam[$s]."'");
						
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
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
										<span style=\"margin-left: 30px;\">Tahun : </span>
										".comboYear("par[tahunUpload]", $par[tahunUpload], "", "onchange=\"document.getElementById('form').submit();\"")."
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
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
					<table>
						<tr>
						<td>Search : </td>				
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>				
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>						
			</form>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Bulan</th>
					<th width=\"125\">Waktu</th>
					<th>Petugas</th>
					<th width=\"125\">Total Pegawai</th>
					<th width=\"50\">Upload</th>
				</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[idLokasi]))
			$filter = " and t2.location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$filter.= " and t2.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$filter.= " and t2.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$filter.= " and t2.unit_id='".$par[unitId]."'";
		
		$sql="select t1.*, t2.namaUser from pay_upload t1 left join app_user t2 on (t1.createBy=t2.username) where t1.tahunUpload='".$par[tahunUpload]."' and idKomponen='".$par[idKomponen]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$arrProses["$r[bulanUpload]"] = $r;
		}
		
		if(isset($menuAccess[$s]["add"]))
		$uploadUpload = " onclick=\"openBox('popup.php?par[mode]=upl&par[bulanUpload]=$i".getPar($par,"mode,bulanUpload")."',725,325);\"";
		
		for($i=1; $i<=12; $i++){
			$r = $arrProses[$i];
			$jumlahPegawai = getField("select count(*) from pay_upload t1 join dta_pegawai t2 on (t1.idPegawai=t2.id) where t1.tahunUpload='$par[tahunUpload]' and idKomponen='".$par[idKomponen]."' and t1.bulanUpload='".$i."' AND t2.location IN ( $areaCheck ) ".$filter);			
			$statusUpload =  getAngka($jumlahPegawai) > 0 ? "<img src=\"styles/images/t.png\" title=\"Sudah Diupload\">" : "<img src=\"styles/images/f.png\" title=\"Belum Diupload\">";		
			
			list($tanggalCreate, $waktuCreate) = explode(" ",$r[createTime]);			
			$waktuUpload = getTanggal($tanggalCreate) != "" ? getTanggal($tanggalCreate)." @ ".substr($waktuCreate,0,5) : "";
			
			$text.="<tr>
					<td>$i.</td>
					<td>".getBulan($i)."</td>
					<td align=\"center\">".$waktuUpload."</td>
					<td>$r[namaUser]</td>
					<td align=\"center\"><a href=\"?par[mode]=det&par[bulanUpload]=$i".getPar($par,"mode,bulanUpload")."\" class=\"detil\">".getAngka($jumlahPegawai)."</a></td>
					<td align=\"center\"><a href=\"#\" ".$uploadUpload." class=\"detil\">".$statusUpload."</a></td>					
					</tr>";
		}
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$areaCheck;						
		if(empty($par[tahunUpload])) $par[tahunUpload] = date('Y');
		if(empty($par[bulanUpload])) $par[bulanUpload] = date('m');
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."
					<div style=\"float:right;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</div>
				</h1>
				".getBread(ucwords("detail gaji"))."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:20px; top:5px;\">
			<p>				
				<span>Lokasi Kerja : </span>
				".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' and kodeData in ($areaCheck) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "210px")."
				<span>Periode : </span>
				".comboMonth("par[bulanUpload]", $par[bulanUpload], "onchange=\"document.getElementById('form').submit();\"")." 
				".comboYear("par[tahunUpload]", $par[tahunUpload], "", "onchange=\"document.getElementById('form').submit();\"")."
			</p>
			</div>	
			<div style=\"float:right;\">
				Status Pegawai : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData","kodeData","namaData","par[idStatus]","All",$par[idStatus],"onchange=\"document.getElementById('form').submit();\"")."
				".setPar($par,"idStatus,idLokasi,bulanUpload,tahunUpload,search,filter")."
				<input type=\"button\" class=\"cancel radius2\" value=\"Hapus Data\" onclick=\"if(confirm('anda yakin akan menghapus data ini ?'))window.location.href='?par[mode]=all".getPar($par,"mode")."';\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location.href='?".getPar($par,"mode,bulanUpload")."';\"/>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:200px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>		
			</form>
			<br clear=\"all\" />";
			
			$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
			$filter= " where t1.status='".$status."' and t2.bulanUpload='".$par[bulanUpload]."' and t2.tahunUpload='".$par[tahunUpload]."' and t2.idKomponen='".$par[idKomponen]."'";
			if(!empty($par[idStatus]))
				$filter = " and t1.cat=".$par[idStatus]."";		
			
			if(!empty($par[idLokasi]))
				$filter.= " and t1.location='".$par[idLokasi]."'";
			
			if($par[search] == "Nama")
				$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
			else if($par[search] == "NPP")
				$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
			else
				$filter.= " and (
					lower(t1.name) like '%".strtolower($par[filter])."%'
					or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
				)";
			
			$sql="select * from dta_pegawai t1 join pay_upload t2 on (t1.id=t2.idPegawai) $filter and t1.location IN ( $areaCheck ) group by t1.id, t2.keteranganUpload order by t1.name";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){				
				$arrDetail[] = $r;
				$sumNilai+= $r[nilaiUpload];				
			}
			
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th>Nilai</th>
					<th>Keterangan</th>
					<th width=\"50\">Kontrol</th>
				</tr>				
				<tr>
					<td style=\"border-right:1px;\">&nbsp;</td>
					<td style=\"padding:5px 10px; border-right:1px; text-align:left;\">JUMLAH</td>
					<td style=\"border-right:1px;\">&nbsp;</td>
					<td style=\"padding:5px 10px; border-right:1px; text-align:right; font-weight:normal\">".getAngka($sumNilai)."</td>
					<td style=\"border-right:1px;\">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</thead>
			<tbody>";
						
			if (is_array($arrDetail)) {
				asort($arrDetail);
				reset($arrDetail);
				while (list($idDetail, $r) = each($arrDetail)) {
				$no++;
				$text.="<tr>
						<td>$no.</td>
						<td>".strtoupper($r[name])."</td>
						<td>$r[reg_no]</td>						
						<td align=\"right\">".getAngka($r[nilaiUpload])."</td>
						<td>$r[keteranganUpload]</td>	
						<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=dta&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',1000,550);\"><span>Detail</span></a></td>
					</tr>";				
				}
			}
			
			$text.="</tbody>			
			<tfoot>
				<tr>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:left; font-weight:bold\">JUMLAH</td>
					<td>&nbsp;</td>
					<td style=\"padding:5px 10px; text-align:right;\">".getAngka($sumNilai)."</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</tfoot>
			</table>			
			</div>";
		return $text;
	}		
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Detail Posting Proses Gaji</h1>
					".getBread(ucwords("detail data"))."
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
		
		$sql="select * from pay_upload where bulanUpload='$par[bulanUpload]' and tahunUpload='$par[tahunUpload]' and idPegawai='$par[idPegawai]' and idKomponen='$par[idKomponen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">							
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\" style=\"margin-left:150px;\">
							<input type=\"text\" id=\"inp[nilaiUpload]\" name=\"inp[nilaiUpload]\"  value=\"".getAngka($r[nilaiUpload])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganUpload]\" name=\"inp[keteranganUpload]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganUpload]</textarea>
						</div>
					</p>					
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Hapus Data\" style=\"float:right\" onclick=\"if(confirm('anda yakin akan menghapus data ini ?'))window.location.href='?par[mode]=del".getPar($par,"mode")."';\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function data(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		echo "<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Detail Posting Proses Gaji</h1>
					".getBread(ucwords("detail data"))."
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/__emp_header__.php";
		
		$sql="select * from pay_upload where bulanUpload='$par[bulanUpload]' and tahunUpload='$par[tahunUpload]' and idPegawai='$par[idPegawai]' and idKomponen='$par[idKomponen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">							
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<span class=\"field\" style=\"margin-left:150px;\">
							".getAngka($r[nilaiUpload])."&nbsp;
						</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">
							".nl2br($r[keteranganUpload])."&nbsp;
						</span>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "det":
				$text = detail();
			break;
			
			case "dta":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = data();
			break;
			case "del":
				if(isset($menuAccess[$s]["edit"])) $text = hapus();
			break;
			case "all":
				if(isset($menuAccess[$s]["edit"])) $text = semua();
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