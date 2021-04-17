<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";					
	$fFile = "files/upload/";
	$fPokok = "files/pokok/";
	$fMelekat = "files/melekat/";
$fExport = "files/export/";
	
	function ubah(){
		global $db,$s,$inp,$par,$dta,$arrParameter,$cUsername;		
		repField();
		/*
		db("delete from pay_komponen where idPegawai='$par[idPegawai]'");
		if(is_array($dta)){		  
			reset($dta);
			while (list($idKomponen, $tipeKomponen) = each($dta)) {				
				$sql="insert into pay_komponen (idPegawai, idKomponen, tipeKomponen, createBy, createTime) values ('$par[idPegawai]', '$idKomponen', '$tipeKomponen', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
			}
		}
		*/
		$idCatatan=getField("select idCatatan from pay_catatan order by idCatatan desc limit 1")+1;
		$sql=getField("select idCatatan from pay_catatan where idPegawai='$par[idPegawai]'")?
		"update pay_catatan set keteranganCatatan='$inp[keteranganCatatan]', pphCatatan='$inp[pphCatatan]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPegawai='$par[idPegawai]'":
		"insert into pay_catatan (idCatatan, idPegawai, keteranganCatatan, createBy, createTime) values ('$idCatatan', '$par[idPegawai]', '$inp[keteranganCatatan]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		$id = getField("select id from emp_bank order by id desc limit 1") + 1;
		$sql=getField("select id from emp_bank where parent_id='".$par[idPegawai]."' and status in ('1', 't')")?
		"update emp_bank set bank_id='$inp[bank_id]', branch='$inp[branch]', account_no='$inp[account_no]', upd_by='$cUsername', upd_date='".date('Y-m-d H:i:s')."' where parent_id='".$par[idPegawai]."' and status in ('1', 't')":
		"insert into emp_bank (id, parent_id, bank_id, branch, account_no, status, cre_by, cre_date) values ('$id', '$par[idPegawai]', '$inp[bank_id]', '$inp[branch]', '$inp[account_no]', 't', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idPegawai")."';</script>";	
	}
	
	function umr(){
		global $db,$s,$inp,$par;
		$ump = getAngka(setAngka(getField("select ump from mst_ump where kodeKota='".getField("select location from dta_pegawai where id='".$par[idPegawai]."'")."'")));
		return $ump;
	}
	
	function uploadMelekat($idMelekat){
		global $db,$s,$inp,$par,$fMelekat;		
		$fileUpload = $_FILES["fileMelekat"]["tmp_name"];
		$fileUpload_name = $_FILES["fileMelekat"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fMelekat);			
			$fileMelekat = "doc-".$idMelekat.".".getExtension($fileUpload_name);
			fileRename($fMelekat, $fileUpload_name, $fileMelekat);			
		}
		if(empty($fileMelekat)) $fileMelekat = getField("select fileMelekat from pay_melekat where idMelekat='$idMelekat'");
		
		return $fileMelekat;
	}
	
	function hapusFile_melekat(){
		global $db,$s,$inp,$par,$fMelekat,$cUsername;					
		$fileMelekat = getField("select fileMelekat from pay_melekat where idMelekat='$par[idMelekat]'");
		if(file_exists($fMelekat.$fileMelekat) and $fileMelekat!="")unlink($fMelekat.$fileMelekat);
		
		$sql="update pay_melekat set fileMelekat='' where idMelekat='$par[idMelekat]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=editMelekat".getPar($par,"mode")."';</script>";
	}
	
	function hapusMelekat(){
		global $db,$s,$inp,$par,$arrParameter,$fMelekat,$cUsername;			
		$fileMelekat = getField("select fileMelekat from pay_melekat where idMelekat='$par[idMelekat]'");
		if(file_exists($fMelekat.$fileMelekat) and $fileMelekat!="")unlink($fMelekat.$fileMelekat);
		
		$sql="delete from pay_melekat where idMelekat='$par[idMelekat]'";
		db($sql);
		echo "<script>window.location='?par[mode]=dat".getPar($par,"mode,idMelekat")."';</script>";
	}
	
	function ubahMelekat(){
		global $db,$s,$inp,$par,$arrParameter,$cUsername;			
		repField();
		$fileMelekat=uploadMelekat($par[idMelekat]);
		
		$sql="update pay_melekat set idKomponen='$inp[idKomponen]', tanggalMelekat='".setTanggal($inp[tanggalMelekat])."', fileMelekat='$fileMelekat', nilaiMelekat='".setAngka($inp[nilaiMelekat])."', keteranganMelekat='$inp[keteranganMelekat]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambahMelekat(){
		global $db,$s,$inp,$par,$arrParameter,$cUsername;	
		repField();
		$idMelekat=getField("select idMelekat from pay_melekat order by idMelekat desc limit 1")+1;			
		$fileMelekat=uploadMelekat($idMelekat);		
		
		$sql="insert into pay_melekat (idMelekat, idPegawai, idKomponen, tanggalMelekat, fileMelekat, nilaiMelekat, keteranganMelekat, statusMelekat, createBy, createTime) values ('$idMelekat', '$par[idPegawai]', '$inp[idKomponen]', '".setTanggal($inp[tanggalMelekat])."', '$fileMelekat', '".setAngka($inp[nilaiMelekat])."', '$inp[keteranganMelekat]', '$statusMelekat', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function ubahKomponen(){
		global $db,$s,$inp,$par,$arrParameter,$cUsername;			
		repField();
		
		$sql=getField("select idKomponen from pay_komponen where idPegawai='".$par[idPegawai]."' and idKomponen='".$par[idKomponen]."'")?
		"update pay_komponen set nilaiKomponen='".setAngka($inp[nilaiKomponen])."', flagKomponen='".$inp[flagKomponen]."', statusKomponen='".$inp[statusKomponen]."' where idKomponen='".$par[idKomponen]."' and idPegawai='".$par[idPegawai]."'":
		"insert into pay_komponen (idKomponen, idPegawai, tipeKomponen, nilaiKomponen, flagKomponen, statusKomponen, createBy, createTime) values ('".$par[idKomponen]."', '".$par[idPegawai]."', '".$inp[tipeKomponen]."', '".setAngka($inp[nilaiKomponen])."', '".$inp[flagKomponen]."', '".$inp[statusKomponen]."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function uploadPokok($idPokok){
		global $db,$s,$inp,$par,$fPokok;		
		$fileUpload = $_FILES["filePokok"]["tmp_name"];
		$fileUpload_name = $_FILES["filePokok"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fPokok);			
			$filePokok = "doc-".$idPokok.".".getExtension($fileUpload_name);
			fileRename($fPokok, $fileUpload_name, $filePokok);			
		}
		if(empty($filePokok)) $filePokok = getField("select filePokok from pay_pokok where idPokok='$idPokok'");
		
		return $filePokok;
	}
	
	function hapusFile_pokok(){
		global $db,$s,$inp,$par,$fPokok,$cUsername;					
		$filePokok = getField("select filePokok from pay_pokok where idPokok='$par[idPokok]'");
		if(file_exists($fPokok.$filePokok) and $filePokok!="")unlink($fPokok.$filePokok);
		
		$sql="update pay_pokok set filePokok='' where idPokok='$par[idPokok]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=editPokok".getPar($par,"mode")."';</script>";
	}
	
	function hapusPokok(){
		global $db,$s,$inp,$par,$arrParameter,$fPokok,$cUsername;			
		$filePokok = getField("select filePokok from pay_pokok where idPokok='$par[idPokok]'");
		if(file_exists($fPokok.$filePokok) and $filePokok!="")unlink($fPokok.$filePokok);
		
		$sql="delete from pay_pokok where idPokok='$par[idPokok]'";
		db($sql);
		echo "<script>window.location='?par[mode]=dat".getPar($par,"mode,idPokok")."';</script>";
	}
	
	function ubahPokok(){
		global $db,$s,$inp,$par,$arrParameter,$cUsername;			
		repField();
		$filePokok=uploadPokok($par[idPokok]);
		
		$sql="update pay_pokok set nomorPokok='$inp[nomorPokok]', tanggalPokok='".setTanggal($inp[tanggalPokok])."', filePokok='$filePokok', nilaiPokok='".setAngka($inp[nilaiPokok])."', keteranganPokok='$inp[keteranganPokok]', umrPokok='$inp[umrPokok]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPokok='$par[idPokok]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambahPokok(){
		global $db,$s,$inp,$par,$arrParameter,$cUsername;	
		repField();
		$idPokok=getField("select idPokok from pay_pokok order by idPokok desc limit 1")+1;			
		$filePokok=uploadPokok($idPokok);		
		
		$sql="insert into pay_pokok (idPokok, idPegawai, nomorPokok, tanggalPokok, filePokok, nilaiPokok, keteranganPokok, umrPokok, statusPokok, createBy, createTime) values ('$idPokok', '$par[idPegawai]', '$inp[nomorPokok]', '".setTanggal($inp[tanggalPokok])."', '$filePokok', '".setAngka($inp[nilaiPokok])."', '$inp[keteranganPokok]', '$inp[umrPokok]', '$statusPokok', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
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
				
				db("delete from pay_pokok where nomorPokok='".$inp[nomorPokok]."' and tanggalPokok='".setTanggal($inp[tanggalPokok])."'");
				db("DROP TABLE IF EXISTS tmp_pokok");		
				db("CREATE TABLE IF NOT EXISTS tmp_pokok (					  
					  idPokok int(11) NOT NULL,
					  nomorPokok varchar(50) NOT NULL,
					  tanggalPokok date NOT NULL,
					  namaPokok varchar(150) NOT NULL,
					  nikPokok varchar(30) NOT NULL,
					  nilaiPokok decimal(20,2) NOT NULL,
					  jabatanPokok decimal(20,2) NOT NULL,
					  keahlianPokok decimal(20,2) NOT NULL,
					  prestasiPokok decimal(20,2) NOT NULL,
					  lainPokok decimal(20,2) NOT NULL,
					  statusPokok varchar(150) NOT NULL,
					  createBy varchar(30) NOT NULL,
					  createTime datetime NOT NULL,
					  PRIMARY KEY (idPokok)
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
				
				return "fileData".$fileData."|".$inp[nomorPokok]."|".$inp[tanggalPokok];
			}	
		}
	}
	
	function setData(){
		global $s,$inp,$par,$fFile,$cUsername,$arrParameter,$arrParam;
		
		list($fileData, $nomorPokok, $tanggalPokok) = explode("|", $par[fileData]);
		
		$inputFileName = $fFile.$fileData;
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
			$rowData = $sheet->rangeToArray('A' . $par[rowData] . ':D' . $par[rowData], NULL, TRUE, TRUE);				
			$dta = $rowData[0];
			
			$result.= " ".$dta[1]." ".$dta[3];		
			if(is_numeric(trim(strtolower($dta[0])))){
				$namaPegawai = str_replace("'", "\'", trim($dta[1]));
				$nikPegawai = trim($dta[2]);
				$nilaiPokok = trim($dta[3]);
				$nilaiJabatan = trim($dta[4]);
				$nilaiKeahlian = trim($dta[5]);
				$nilaiPrestasi = trim($dta[6]);
				$nilaiLain = trim($dta[7]);
				
				$idPegawai = getField("select id from emp where trim(reg_no)='".$nikPegawai."'");
				if(empty($idPegawai)) $idPegawai = getField("select id from emp where trim(lower(name))='".strtolower($namaPegawai)."'");
				
				$statusPokok = $idPegawai > 0 ? "OK" : "NPP : ".$nikPegawai." belum terdaftar";
				if($idPegawai > 0){	
					$idPokok = getField("select idPokok from pay_pokok order by idPokok desc limit 1") + 1;
					
					$sql="insert into pay_pokok (idPokok, idPegawai, nomorPokok, tanggalPokok, nilaiPokok, statusPokok, createBy, createTime) values ('$idPokok', '$idPegawai', '$nomorPokok', '".setTanggal($tanggalPokok)."', '".setAngka($nilaiPokok)."', 't', '$createBy', '$createTime')";
					db($sql);
				}
				
				$idPokok = getField("select idPokok from tmp_pokok order by idPokok desc limit 1") + 1;
				$sql="insert into tmp_pokok (idPokok, nomorPokok, tanggalPokok, namaPokok, nikPokok, nilaiPokok, jabatanPokok, keahlianPokok, prestasiPokok, lainPokok, statusPokok, createBy, createTime) values ('".$idPokok."', '".$nomorPokok."', '".setTanggal($tanggalPokok)."', '".$namaPegawai."', '".$nikPegawai."', '".setAngka($nilaiPokok)."', '".setAngka($nilaiJabatan)."', '".setAngka($nilaiKeahlian)."', '".setAngka($nilaiPrestasi)."', '".setAngka($nilaiLain)."', '".$statusPokok."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
				db($sql);
			}
			
			$progresData = getAngka($par[rowData]/$highestRow * 100);	
			return $progresData."\t(".$progresData."%) ".getAngka($par[rowData])." of ".getAngka($highestRow)."\t".$result;	
		}
	}
	
	function endProses(){
		global $s,$inp,$par,$cUsername;
		
		$file = "files/Upload Update.log";
		if(file_exists($file))unlink($file);
		$fileName = fopen($file, "w");
								
		$sql="select * from tmp_pokok order by idPokok";
		$res=db($sql);
		$text.= "NPP\t\tNAMA\r\n";
		while($r=mysql_fetch_array($res)){
			$text.= $r[nikPokok]."\t".$r[namaPokok]."\t".$r[statusPokok]."\r\n";			
		}
				
		fwrite($fileName, $text);
		fclose($fileName);		
		usleep(5000);
		
		db("DROP TABLE IF EXISTS tmp_pokok");		
		return "upload data selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
		
		
	function formUpload(){
		global $c,$p,$m,$s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess;
						
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Upload Data</h1>
					".getBread(ucwords("upload data"))."					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\" onsubmit=\"return validation(document.form);\"  enctype=\"multipart/form-data\">	
				<div id=\"formInput\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">Tanggal SK</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalPokok\" name=\"inp[tanggalPokok]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalPokok])."\" class=\"vsmallinput hasDatePicker\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">No. SK</label>
						<div class=\"field\">
							<input type=\"text\" id=\"nomorPokok\" name=\"inp[nomorPokok]\"  value=\"$r[nomorPokok]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"150\"/>
						</div>
					</p>
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
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=fmtPokok\" class=\"detil\">* download template.xls</a></div>
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
					<a href=\"download.php?d=logPokok\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>				
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?&c=$c&p=$p&m=$m&s=$s';\"/>
				</div>
			</form>	
			</div>";
		return $text;
	}	
		
	function formMelekat(){
		global $db,$s,$inp,$par,$fMelekat,$arrTitle,$arrParameter,$menuAccess;		
		$sql="select * from pay_melekat where idMelekat='$par[idMelekat]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[tanggalMelekat])) $r[tanggalMelekat] = date("Y-m-d");
		
		setValidation("is_null","tanggalMelekat","anda harus mengisi tanggal");
		setValidation("is_null","inp[idKomponen]","anda harus mengisi komponen");
		setValidation("is_null","inp[nilaiMelekat]","anda harus mengisi nilai");
		setValidation("is_null","inp[keteranganMelekat]","anda harus mengisi keterangan");
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Gaji Melekat</h1>
					".getBread(ucwords("gaji melekat"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalMelekat\" name=\"inp[tanggalMelekat]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalMelekat])."\" class=\"vsmallinput hasDatePicker\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Komponen</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[10]."' order by urutanData","kodeData","namaData","inp[idKomponen]"," ",$r[idKomponen],"", "360px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiMelekat]\" name=\"inp[nilaiMelekat]\"  value=\"".getAngka($r[nilaiMelekat])."\" class=\"mediuminput\" style=\"text-align:right; width:100px;\" onkeyup=\"cekAngka(this);\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganMelekat]\" name=\"inp[keteranganMelekat]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganMelekat]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
							$text.=empty($r[fileMelekat])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
								<div class=\"fakeupload\" style=\"width:300px;\">
									<input type=\"file\" id=\"fileMelekat\" name=\"fileMelekat\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<a href=\"download.php?d=melekat&f=$r[idMelekat]\"><img src=\"".getIcon($fMelekat."/".$r[fileMelekat])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
								<input type=\"file\" id=\"fileMelekat\" name=\"fileMelekat\" style=\"display:none;\" />
								<a href=\"?par[mode]=delMelekat_file".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>				
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}	
	
	function formKomponen(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter;					
		$sql="select * from dta_komponen where idKomponen='$par[idKomponen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);							
		$tipeKomponen = $r[tipeKomponen] == "t" ? "Penerimaan" : "Potongan";
						
		setValidation("is_null","tanggalPokok","anda harus mengisi tanggal sk");		
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Komponen ".$tipeKomponen."</h1>
					".getBread(ucwords("komponen ".strtolower($tipeKomponen).""))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Komponen</label>
						<span class=\"field\">".$r[namaKomponen]."&nbsp;
						<input type=\"hidden\" id=\"inp[tipeKomponen]\" name=\"inp[tipeKomponen]\"  value=\"".$r[tipeKomponen]."\"/>
						</span>
					</p>";
		
		$arrData = arrayQuery("select idKomponen, tipeMaster, idJenis from pay_jenis_komponen where idJenis='".getField("select payroll_id from emp_phist where parent_id='".$par[idPegawai]."' and status='1'")."' and tipeMaster='".$r[tipeKomponen]."'");	
		
		$sql="select * from pay_komponen where idKomponen='".$par[idKomponen]."' and idPegawai='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$khusus =  $r[flagKomponen] == "t" ? "checked=\"checked\"" : "";
		$standard =  empty($khusus) ? "checked=\"checked\"" : "";		
		$display = $khusus ? "block" : "none";				
		
		$true =  ($r[statusKomponen] == "t" || isset($arrData["$par[idKomponen]"])) ? "checked=\"checked\"" : "";
		$false =  empty($true) ? "checked=\"checked\"" : "";
					
			$text.="<p>
						<label class=\"l-input-small\">Tipe</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"standard\" name=\"inp[flagKomponen]\" value=\"f\" $standard onclick=\"setKomponen();\" /> <span class=\"sradio\">Standard</span>
							<input type=\"radio\" id=\"khusus\" name=\"inp[flagKomponen]\" value=\"t\" $khusus onclick=\"setKomponen();\"/> <span class=\"sradio\">Khusus</span>							
						</div>
					</p>
					<div id=\"nilaiKomponen\" style=\"display:".$display.";\">
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiKomponen]\" name=\"inp[nilaiKomponen]\"  value=\"".getAngka($r[nilaiKomponen])."\" class=\"mediuminput\" style=\"text-align:right; width:100px;\" onkeyup=\"cekAngka(this);\" maxlength=\"150\"/>
						</div>
					</p>
					</div>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusKomponen]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusKomponen]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>							
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function formPokok(){
		global $db,$s,$inp,$par,$fPokok,$arrTitle,$menuAccess;		
		$sql="select * from pay_pokok where idPokok='$par[idPokok]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[tanggalPokok])) $r[tanggalPokok] = date("Y-m-d");
		$ya =  $r[umrPokok] == "t" ? "checked=\"checked\"" : "";
		$tidak =  empty($ya) ? "checked=\"checked\"" : "";		
		
		setValidation("is_null","tanggalPokok","anda harus mengisi tanggal sk");
		setValidation("is_null","inp[nomorPokok]","anda harus mengisi no. sk");
		setValidation("is_null","inp[nilaiPokok]","anda harus mengisi nilai");
		setValidation("is_null","inp[keteranganPokok]","anda harus mengisi keterangan");
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Gaji Pokok</h1>
					".getBread(ucwords("gaji pokok"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Tanggal SK</label>
						<div class=\"field\">
							<input type=\"text\" id=\"tanggalPokok\" name=\"inp[tanggalPokok]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalPokok])."\" class=\"vsmallinput hasDatePicker\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">No. SK</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nomorPokok]\" name=\"inp[nomorPokok]\"  value=\"$r[nomorPokok]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Sesuai UMR Daerah</label>
						<div class=\"fradio\">							
							<input type=\"radio\" id=\"ya\" name=\"inp[umrPokok]\" value=\"t\" $ya onclick=\"setUmr('".getPar($par,"mode")."');\"/> <span class=\"sradio\">Ya</span>	
							<input type=\"radio\" id=\"tidak\" name=\"inp[umrPokok]\" value=\"f\" $tidak onclick=\"setUmr('".getPar($par,"mode")."');\" /> <span class=\"sradio\">Tidak</span>						
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiPokok]\" name=\"inp[nilaiPokok]\"  value=\"".getAngka($r[nilaiPokok])."\" class=\"mediuminput\" style=\"text-align:right; width:100px;\" onkeyup=\"cekAngka(this);\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganPokok]\" name=\"inp[keteranganPokok]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganPokok]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">File SK</label>
						<div class=\"field\">";
							$text.=empty($r[filePokok])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
								<div class=\"fakeupload\" style=\"width:300px;\">
									<input type=\"file\" id=\"filePokok\" name=\"filePokok\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<a href=\"download.php?d=pokok&f=$r[idPokok]\"><img src=\"".getIcon($fPokok."/".$r[filePokok])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
								<input type=\"file\" id=\"filePokok\" name=\"filePokok\" style=\"display:none;\" />
								<a href=\"?par[mode]=delPokok_file".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>				
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>
			<script>
				document.getElementById('inp[nilaiPokok]').readOnly = document.getElementById('ya').checked;
			</script>";
		return $text;
	}
	
	function data(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$idPayroll;
		$_SESSION["curr_emp_id"] = $par[idPegawai];
		$subTitle = empty($arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]) ? "" : $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ";
		$iuranPensiun = getField("select join_date from emp where id='".$par[idPegawai]."'") < "2008-01-01" ? "PPMP" : "PPIP";
		
		echo "<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$subTitle.$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div style=\"padding:10px;\">";
				
		require_once "tmpl/emp_header_basic.php";		
		
		$text.="</div>
				<br clear=\"all\">
				<div class=\"contentwrapper\">
				<div id=\"general\">
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>GAJI POKOK</h3></div>
					</div>	
					<div style=\"position:absolute; right:0; margin-top:-65px; margin-right:20px;\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=addPokok".getPar($par,"mode,idPokok")."',700,450);\"><span>Tambah Data</span></a>";
				$text.="</div>					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>							
							<th width=\"75\">Tanggal</th>
							<th width=\"150\">No. SK</th>
							<th width=\"150\">Nilai</th>
							<th width=\"150\">Sesuai UMR</th>
							<th style=\"min-width:150px;\">Keterangan</th>
							<th width=\"50\">Kontrol</th>
						</tr>
					</thead>
					<tbody>";
								
				$sql="select * from pay_pokok where idPegawai='".$par[idPegawai]."' order by tanggalPokok";
				$res=db($sql);
				$no=1;
				while($r=mysql_fetch_array($res)){
					$umrPokok = $r[umrPokok] == "t" ? "Ya" : "Tidak";
					$text.="<tr> 
							<td>$no.</td>
							<td align=\"center\">".getTanggal($r[tanggalPokok])."</td>
							<td>$r[nomorPokok]</td>
							<td align=\"right\">".getAngka($r[nilaiPokok])."</td>
							<td>$umrPokok</td>
							<td>$r[keteranganPokok]</td>";
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
						$text.="<td align=\"center\">";
						if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editPokok&par[idPokok]=$r[idPokok]".getPar($par,"mode,idPokok")."',700,450);\"><span>Edit</span></a>";
						if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=delPokok&par[idPokok]=$r[idPokok]".getPar($par,"mode,idPokok")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						$text.="</td>";
					}
					$text.="</tr>";
					$no++;
				}	
				
				$text.="</tbody>
					</table>
					
				<!--			
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>TUNJANGAN MELEKAT</h3></div>
					</div>
					<div style=\"position:absolute; right:0; margin-top:-65px; margin-right:20px;\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=addMelekat".getPar($par,"mode,idMelekat")."',700,450);\"><span>Tambah Data</span></a>";
				$text.="</div>					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable2\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>							
							<th>Komponen</th>							
							<th width=\"150\">Nilai</th>
							<th style=\"min-width:150px;\">Keterangan</th>
							<th width=\"50\">Kontrol</th>
						</tr>
					</thead>
					<tbody>";
								
				$sql="select * from pay_melekat t1 join mst_data t2 on (t1.idKomponen=t2.kodeData) where idPegawai='".$par[idPegawai]."' order by tanggalMelekat";
				$res=db($sql);
				$no=1;
				while($r=mysql_fetch_array($res)){													
					$text.="<tr>
							<td>$no.</td>							
							<td>$r[namaData]</td>
							<td align=\"right\">".getAngka($r[nilaiMelekat])."</td>
							<td>$r[keteranganMelekat]</td>";
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
						$text.="<td align=\"center\">";
						if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editMelekat&par[idMelekat]=$r[idMelekat]".getPar($par,"mode,idMelekat")."',700,450);\"><span>Edit</span></a>";
						if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=delMelekat&par[idMelekat]=$r[idMelekat]".getPar($par,"mode,idMelekat")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
						$text.="</td>";
					}
					$text.="</tr>";
					$no++;
				}	
				
				$text.="</tbody>
					</table>
				-->";
				
				# GET KOMPONEN GAJI
				$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
				$res_=db($sql_);
				$r_=mysql_fetch_array($res_);				
				$arrData = arrayQuery("select idKomponen, tipeMaster, idJenis from pay_jenis_komponen where idJenis='".$r_[payroll_id]."'");
				$idLokasi = $r_[group_id];
				$idJenis = $r_[payroll_id];
				
				$sql="select t1.*, t2.tipeKomponen from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t2.idJenis='".$r_[payroll_id]."'";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$arrData["$r[idKomponen]"]["$r[tipeKomponen]"] = $r[nilaiKomponen]."\t".$r[flagKomponen];
					$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $r[statusKomponen];
				}			
				
				list($idProses, $detailProses) = explode("\t", getField("select concat(idProses, '\t', detailProses) from pay_proses where idLokasi='$idLokasi' and idJenis='$idJenis' order by idProses desc"));
				$sql="select * from ".$detailProses." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t1.idProses='".$idProses."' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$arrProses["$r[idKomponen]"] = $r[nilaiProses];
				}

				$sql="select * from dta_komponen where statusKomponen='t' and idJenis='".$r_[payroll_id]."' order by tipeKomponen desc, urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
					
					#GAJI POKOK
					$tanggalProses = date("t", strtotime(date('Y')."-".date('m')."-01"));
					$nilaiPokok = getField("select nilaiPokok from pay_pokok where idPegawai='".$par[idPegawai]."' and tanggalPokok<='".date('Y')."-".date('m')."-".$tanggalProses."' order by tanggalPokok desc limit 1");
								
					#TUNJANGAN JABATAN
					$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);
					
					$nilaiJabatan = getField("select sum(t1.nilaiTunjangan) from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r_[rank]."' and idGrade='".$r_[grade]."'");
					
					$fieldFungsional = $r_[location] == getField("select kodeData from mst_data where kodeCategory='".$arrParameter[7]."' order by urutanData limit 1") ? "pusatFungsional" : "cabangFungsional";			
					$nilaiFungsional = getField("select sum(t1.".$fieldFungsional.") from pay_fungsional t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r_[rank]."' and idGrade='".$r_[grade]."'");
					
					#POTONGAN KOPERASI
					$nilaiKoperasi = getField("select sum(nilai) from pay_koperasi where idPangkat='".$r_[rank]."'");
					
					$nilaiKomponen = 0;
						
					# Gaji Pokok
					if($r[flagKomponen] == 1){ 						
						$nilaiKomponen = $nilaiPokok;
					}
					
					# Tunjangan Jabatan
					if($r[flagKomponen] == 2){
						$nilaiKomponen = $nilaiJabatan;
					}
					
					# Potongan Pinjaman
					if($r[flagKomponen] == 3){
						$nilaiKomponen = getField("select t2.nilaiAngsuran from ess_pinjaman t1 join ess_angsuran t2 on (t1.idPinjaman=t2.idPinjaman) where t1.idPegawai='".$par[idPegawai]."' and t1.persetujuanPinjaman='t' and t1.sdmPinjaman='t' and month(tanggalAngsuran)='".date('m')."' and year(tanggalAngsuran)='".date('Y')."'");
					}
						
					# Potongan Koperasi
					if($r[flagKomponen] == 5){
						$nilaiKomponen = $nilaiKoperasi;
					}	
						
					# Tunjangan Fungsional
					if($r[flagKomponen] == 6){
						$nilaiKomponen = $nilaiFungsional;
					}	
						
					# Komponen Penerimaan & Potongan
					if($r[flagKomponen] == 0){
						#fixed
						if($r[dasarKomponen] == 4){
							$nilaiKomponen = $r[nilaiKomponen];
						}
						
						#formula
						if($r[dasarKomponen] < 2 ){		
							$arrFormula=array();
							$nilaiKomponen = 0;
							$sql___="select * from pay_formula_detail where idFormula='".$r[idPengali]."'";
							$res___=db($sql___);							
							while($r___=mysql_fetch_array($res___)){							
								if($r___[operasi1Detail] == "*")
									$nilaiDetail = $r___[nilaiDetail] * $dasarKomponen["$r___[idKomponen]"];
								if($r___[operasi1Detail] == "/")
									$nilaiDetail = $r___[nilaiDetail] / $dasarKomponen["$r___[idKomponen]"];
								if($r___[operasi1Detail] == "+")
									$nilaiDetail = $r___[nilaiDetail] + $dasarKomponen["$r___[idKomponen]"];
								if($r___[operasi1Detail] == "-")
									$nilaiDetail = $r___[nilaiDetail] - $dasarKomponen["$r___[idKomponen]"];
								
								if($r___[operasi2Detail] == "+")
									$nilaiKomponen += $nilaiDetail;
								
								if($r___[operasi2Detail] == "-")
									$nilaiKomponen -= $nilaiDetail;
								
								$arrFormula["".$r___[nilaiDetail]."\t".$r___[operasi1Detail].""] = $r___[idFormula];
							}		
							
							$___idKomponen = getField("select idKomponen from pay_formula where idFormula='".$r[idPengali]."'");
							
							if(count($arrFormula) == 1 && !empty($___idKomponen)){
								$nilaiFormula = 0;
								if(is_array($arrFormula)){
									reset($arrFormula);
									while (list($detFormula) = each($arrFormula)){	
										list($nilaiDet, $operasiDet) = explode("\t", $detFormula);
										if($operasiDet == "*")
											$nilaiFormula = $nilaiDet * $dasarKomponen[$___idKomponen];
										if($operasiDet == "/")
											$nilaiFormula = $nilaiDet / $dasarKomponen[$___idKomponen];
										if($operasiDet == "+")
											$nilaiFormula = $nilaiDet + $dasarKomponen[$___idKomponen];
										if($operasiDet == "-")
											$nilaiFormula = $nilaiDet - $dasarKomponen[$___idKomponen];
									}
								}
								$nilaiKomponen = $nilaiKomponen < $nilaiFormula ? $nilaiFormula : $nilaiKomponen;
							}
						}
					}
					
					list($nilaiData, $flagData) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
					$nilaiData = $flagData == "t"? $nilaiData : $nilaiKomponen;
					$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiData : 0;
					$nilaiKomponen = isset($arrProses["$r[idKomponen]"]) ? $arrProses["$r[idKomponen]"] : $nilaiKomponen;
					
					if($r[maxKomponen] > 0 && $nilaiKomponen > $r[maxKomponen]) $nilaiKomponen = $r[maxKomponen];
					$dasarKomponen["$r[idKomponen]"] = $nilaiKomponen;
					
					if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
					$dtaNilai["".$r[idKomponen].""] = $nilaiKomponen;
					$r[nilaiKomponen] = $nilaiKomponen;						
										
					$dtaKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
					$tipeKomponen = $r[tipeKomponen];
					$urutanKomponen++;
				}
			
				if(is_array($dtaKomponen)){
					reset($dtaKomponen);
					while (list($tipeKomponen) = each($dtaKomponen)){	
						if(is_array($dtaKomponen[$tipeKomponen])){
							reset($dtaKomponen[$tipeKomponen]);
							while (list($urutanKomponen, $r) = each($dtaKomponen[$tipeKomponen])){
								/*
								//BPJS Kesehatan
								if(in_array($r[idKomponen] , array(38))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[44];
									
								//BPJS JHT	
								}else if(in_array($r[idKomponen] , array(28))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[56];
									
								//BPJS JKK
								}else if(in_array($r[idKomponen] , array(58))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[45];
									
								//BPJS JKM
								}else if(in_array($r[idKomponen] , array(59))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[46];	
									
								//BPJS JP
								}else if(in_array($r[idKomponen] , array(7))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[57];	
									
								}else{
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"];
								}
								*/
								$arrKomponen[$tipeKomponen][$urutanKomponen] = $r;
							}
						}
					}
				}
			
				$text.="<ul class=\"hornav\" style=\"margin:0 3px; margin-top:30px; \">
							<li class=\"current\"><a href=\"#tAktif\">Aktif</a></li>
							<li><a href=\"#tTidak\">Tidak Aktif</a></li>												
						</ul>
						<div id=\"tAktif\" class=\"subcontent\" style=\"padding:0px; border:0px; margin-top:2px;\">
							<fieldset style=\"padding:10px;\">
							<div class=\"widgetbox\">
								<div class=\"title\" style=\"margin-bottom:0px; margin-top:10px; \"><h3>KOMPONEN PENERIMAAN</h3></div>
							</div>
							<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
							<thead>
								<tr>
									<th width=\"20\">No.</th>							
									<th width=\"100\">Kode</th>
									<th>Komponen</th>
									<th width=\"150\">Nilai</th>
									<th width=\"50\">Khusus</th>
									<th width=\"50\">Status</th>
									<th width=\"50\">Kontrol</th>
								</tr>
							</thead>
							<tbody>";
					$no=1;				
					if(is_array($arrKomponen["t"])){		  
						reset($arrKomponen["t"]);
						while (list($idKomponen, $r) = each($arrKomponen["t"])){
							if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
							}else{
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
							}
							
							list($nilaiKomponen, $flagKomponen) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
							$nilaiKomponen = $flagKomponen == "t" ? $nilaiKomponen : $r[nilaiKomponen];
							$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiKomponen : 0;
							$statusKomponen = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							$flagKomponen = $flagKomponen == "t" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							
							if($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f"){
								$text.="<tr>
										<td>$no.</td>			
										<td>$r[kodeKomponen]</td>
										<td>$r[namaKomponen]</td>
										<td align=\"right\">".getAngka($nilaiKomponen)."</td>
										<td align=\"center\">".$flagKomponen."</td>
										<td align=\"center\">".$statusKomponen."</td>
										<td align=\"center\"><a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editKomponen&par[idKomponen]=$r[idKomponen]".getPar($par,"mode,idKomponen")."',700,450);\"><span>Edit</span></a></td>
									</tr>";
									
								$totalPenerimaan+=$nilaiKomponen;
								$no++;	
							}
						}
					}	
					
					$text.="<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><strong>Total Penerimaan</strong></td>
								<td align=\"right\">".getAngka($totalPenerimaan)."</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";
					$text.="</tbody>
						</table>
						<div class=\"widgetbox\">
							<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>KOMPONEN POTONGAN</h3></div>
						</div>
						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
							<thead>
								<tr>
									<th width=\"20\">No.</th>							
									<th width=\"100\">Kode</th>
									<th>Komponen</th>
									<th width=\"150\">Nilai</th>
									<th width=\"50\">Khusus</th>
									<th width=\"50\">Status</th>
									<th width=\"50\">Kontrol</th>
								</tr>
							</thead>
							<tbody>";
					$no=1;				
					if(is_array($arrKomponen["p"])){		  
						reset($arrKomponen["p"]);
						while (list($idKomponen, $r) = each($arrKomponen["p"])){
							if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
							}else{
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
							}
							
							list($nilaiKomponen, $flagKomponen) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
							$nilaiKomponen = $flagKomponen == "t" ? $nilaiKomponen : $r[nilaiKomponen];
							$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiKomponen : 0;
							$statusKomponen = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							$flagKomponen = $flagKomponen == "t" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							
							if($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f"){
								$text.="<tr>
										<td>$no.</td>			
										<td>$r[kodeKomponen]</td>
										<td>$r[namaKomponen]</td>
										<td align=\"right\">".getAngka($nilaiKomponen)."</td>
										<td align=\"center\">".$flagKomponen."</td>
										<td align=\"center\">".$statusKomponen."</td>
										<td align=\"center\"><a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editKomponen&par[idKomponen]=$r[idKomponen]".getPar($par,"mode,idKomponen")."',700,450);\"><span>Edit</span></a></td>
									</tr>";
									
								$totalPotongan+=$nilaiKomponen;
								$no++;						
							}
						}
					}	
					
					$text.="<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><strong>Total Potongan</strong></td>
								<td align=\"right\">".getAngka($totalPotongan)."</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";					
					$text.="</tbody>
						</table>
						</fieldset>
					</div>
					<div id=\"tTidak\" class=\"subcontent\" style=\"padding:0px; border:0px; margin-top:2px; display:none\">
						<fieldset style=\"padding:10px;\">
						<div class=\"widgetbox\">
							<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>KOMPONEN PENERIMAAN</h3></div>
						</div>
						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
							<thead>
								<tr>
									<th width=\"20\">No.</th>							
									<th width=\"100\">Kode</th>
									<th>Komponen</th>
									<th width=\"150\">Nilai</th>
									<th width=\"50\">Khusus</th>
									<th width=\"50\">Status</th>
									<th width=\"50\">Kontrol</th>
								</tr>
							</thead>
							<tbody>";
					$no=1;				
					if(is_array($arrKomponen["t"])){		  
						reset($arrKomponen["t"]);
						while (list($idKomponen, $r) = each($arrKomponen["t"])){
							if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
							}else{
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
							}
							
							list($nilaiKomponen, $flagKomponen) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
							$nilaiKomponen = $flagKomponen == "t" ? $nilaiKomponen : $r[nilaiKomponen];
							$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiKomponen : 0;
							$statusKomponen = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							$flagKomponen = $flagKomponen == "t" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							
							if($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] == "f"){
								$text.="<tr>
										<td>$no.</td>	
										<td>$r[kodeKomponen]</td>
										<td>$r[namaKomponen]</td>
										<td align=\"right\">".getAngka($nilaiKomponen)."</td>
										<td align=\"center\">".$flagKomponen."</td>
										<td align=\"center\">".$statusKomponen."</td>
										<td align=\"center\"><a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editKomponen&par[idKomponen]=$r[idKomponen]".getPar($par,"mode,idKomponen")."',700,450);\"><span>Edit</span></a></td>
									</tr>";							
								$no++;	
							}
						}
					}	
					
				
					$text.="</tbody>
						</table>						
						<div class=\"widgetbox\">
							<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>KOMPONEN POTONGAN</h3></div>
						</div>	
						<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
							<thead>
								<tr>
									<th width=\"20\">No.</th>							
									<th width=\"100\">Kode</th>
									<th>Komponen</th>
									<th width=\"150\">Nilai</th>
									<th width=\"50\">Khusus</th>
									<th width=\"50\">Status</th>
									<th width=\"50\">Kontrol</th>
								</tr>
							</thead>
							<tbody>";
					$no=1;				
					if(is_array($arrKomponen["p"])){		  
						reset($arrKomponen["p"]);
						while (list($idKomponen, $r) = each($arrKomponen["p"])){
							if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
							}else{
								$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
							}
							
							list($nilaiKomponen, $flagKomponen) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
							$nilaiKomponen = $flagKomponen == "t" ? $nilaiKomponen : $r[nilaiKomponen];
							$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiKomponen : 0;
							$statusKomponen = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							$flagKomponen = $flagKomponen == "t" ?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";
							
							if($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] == "f"){
								$text.="<tr>
										<td>$no.</td>			
										<td>$r[kodeKomponen]</td>
										<td>$r[namaKomponen]</td>
										<td align=\"right\">".getAngka($nilaiKomponen)."</td>
										<td align=\"center\">".$flagKomponen."</td>
										<td align=\"center\">".$statusKomponen."</td>
										<td align=\"center\"><a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editKomponen&par[idKomponen]=$r[idKomponen]".getPar($par,"mode,idKomponen")."',700,450);\"><span>Edit</span></a></td>
									</tr>";
									
								$no++;						
							}
						}
					}	
				
					$text.="</tbody>
						</table>
						</fieldset>
					</div>";
				
				$text.="<br>
					<!--						
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>TUNJANGAN JABATAN</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable3\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>							
							<th>Pangkat</th>							
							<th>Grade</th>
							<th width=\"150\">Nilai</th>
							<th style=\"min-width:150px;\">Keterangan</th>
						</tr>
					</thead>
					<tbody>";
								
				$sql="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
				$res=db($sql);
				$r=mysql_fetch_array($res);
				
				$sql="select t1.*, t2.namaData as namaPangkat, t3.namaData as namaGrade  from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r[rank]."' and idGrade='".$r[grade]."'";
				$res=db($sql);
				$no=1;
				while($r=mysql_fetch_array($res)){										
					$text.="<tr>
							<td>$no.</td>			
							<td>$r[namaPangkat]</td>
							<td>$r[namaGrade]</td>
							<td align=\"right\">".getAngka($r[nilaiTunjangan])."</td>
							<td>$r[keteranganTunjangan]</td>
							</tr>";
					$no++;
				}
				
				$text.="</tbody>
				</table>
				
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>KOMPONEN GAJI</h3></div>
					</div>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\">PENERIMAAN</th>							
							<th width=\"50%\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";
				
								
				$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));								
				
				for($i=1; $i<=max($cntKomponen); $i++){
					$checkPenerimaan = isset($arrData[$arrKomponen["t"][$i]["idKomponen"]]["t"]) ? "checked=\"checked\"" : "";
					$checkPotongan = isset($arrData[$arrKomponen["p"][$i]["idKomponen"]]["p"]) ? "checked=\"checked\"" : "";
					$text.="<tr>
							<td>";
					
					$text.=empty($arrKomponen["t"][$i]["namaKomponen"]) ? "&nbsp;" : "<input type=\"checkbox\" id=\"dta[".$arrKomponen["t"][$i]["idKomponen"]."]\" name=\"dta[".$arrKomponen["t"][$i]["idKomponen"]."]\" value=\"t\" ".$checkPenerimaan." /> ".$arrKomponen["t"][$i]["namaKomponen"]."";
					$text.="</td>
							<td>";
					$text.=empty($arrKomponen["p"][$i]["namaKomponen"]) ? "&nbsp;" : "<input type=\"checkbox\" id=\"dta[".$arrKomponen["p"][$i]["idKomponen"]."]\" name=\"dta[".$arrKomponen["p"][$i]["idKomponen"]."]\" value=\"p\" ".$checkPotongan." /> ".$arrKomponen["p"][$i]["namaKomponen"]."";
					$text.="</td>
						</tr>";
				}
				#END KOMPONEN GAJI
				
				$sql="select * from pay_catatan where idPegawai='$par[idPegawai]'";	
				$res=db($sql);
				$r=mysql_fetch_array($res);
				$nett =  $r[pphCatatan] == "t" ? "checked=\"checked\"" : "";
				$gross =  empty($nett) ? "checked=\"checked\"" : "";		
		
				$text.="</tbody>
					</table>	
					-->
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\">
					<p>
						<label class=\"l-input-small\">Status PPh</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"gross\" name=\"inp[pphCatatan]\" value=\"f\" $gross /> <span class=\"sradio\">Gross</span>
							<input type=\"radio\" id=\"nett\" name=\"inp[pphCatatan]\" value=\"t\" $nett /> <span class=\"sradio\">Nett</span>							
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div>
							<table>
							<tr>
							<td><textarea id=\"inp[keteranganCatatan]\" name=\"inp[keteranganCatatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:400px;\">$r[keteranganCatatan]</textarea></td>
							<td style=\"width:350px; vertical-align:top; text-align:center;\"><h1>THP : ".getAngka(setAngka(getAngka($totalPenerimaan)) - setAngka(getAngka($totalPotongan)))."</h1></td>
							</tr>
							</table>
						</div>
					</p>";
				$sql="select * from emp_bank where parent_id='".$par[idPegawai]."' and status in ('1', 't') and bank_id > 0";
				$res=db($sql);
				$r=mysql_fetch_array($res);
				$text.="<p>
						<label class=\"l-input-small\">No. Rekening</label>
						<div>
							<input type=\"text\" id=\"inp[account_no]\" name=\"inp[account_no]\"  value=\"".$r[account_no]."\" class=\"mediuminput\" style=\"width:300px;\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Bank</label>
						<div>
							".comboData("select * from mst_data where statusData='t' and kodeCategory='S13' order by urutanData","kodeData","namaData","inp[bank_id]"," ",$r[bank_id],"", "310px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Cabang</label>
						<div>
							<input type=\"text\" id=\"inp[branch]\" name=\"inp[branch]\"  value=\"".$r[branch]."\" class=\"mediuminput\" style=\"width:300px;\" />
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>
					</p>
					
				</form>
				</div>				
			";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;
		$arrKode = arrayQuery("select kodeMaster, kodeData from mst_data where kodeCategory = 'S04' and statusData = 't'");

        $queryUnit = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X03' order by t1.namaData";

        $subTitle = empty($arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]) ? "" : $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ";
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$subTitle.$arrTitle[$s]."</h1>
				".getBread()."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<p style='position: absolute; top: 5px; right: 10px;'>
			    ".comboData($queryUnit, "id","description", "par[idUnit]", "- ALL CABANG -", $par[idUnit], "onchange=\"document.getElementById('form').submit():\"")."
			</p>
			
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>	
			<div id=\"pos_r\">
			<a href=\"?par[mode]=xls".getPar($par, 'mode')."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_inboxo\" onclick=\"openBox('popup.php?par[mode]=upl".getPar($par,"mode")."',725,350);\"><span>Upload Data</span></a>";
		$text.="
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">ID</th>
					<th style=\"min-width:150px;\">Jabatan</th>	
					<th style=\"min-width:100px;\">Job Grade</th>					
					<th style=\"min-width:150px;\">Unit Kerja</th>
					<th style=\"width:100px;\">Gaji Pokok</th>
					<th style=\"width:100px;\">T. Jabatan</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
				
		$filterCat = empty($arrParam[$s]) ? "" : "and t1.cat = '" . $arrKode[$arrParam[$s]] . "'";
		$filter = "WHERE t1.status = '535' $filterCat";
		// if(!empty($subTitle))
		// 	$filter.= " and t1.cat=".$cat."";

        if(!empty($par[idUnit]))
            $filter.=" and t2.div_id = '$par[idUnit]'";

		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
				
		$sql="select * from pay_pokok order by tanggalPokok";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrPokok["$r[idPegawai]"] = $r[nilaiPokok];
		}
		
		$sql="select t1.* from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.flagKomponen='1' ";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			if(!isset($arrPokok["$r[idPegawai]"]))
			$arrPokok["$r[idPegawai]"] = $r[nilaiKomponen];
		}

		
		$defJabatan = arrayQuery("select t1.idJenis, t2.tipeKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.statusKomponen='t' and t2.flagKomponen='2'");
		$defTunjangan = arrayQuery("select idPangkat, idGrade, sum(t1.nilaiTunjangan) from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) group by 1,2");
        
		$sql="select t1.*, t2.idJenis from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.flagKomponen='2' and t1.statusKomponen='t' and t1.flagKomponen='t'";
        $res=db($sql);
        while($r=mysql_fetch_array($res)){
            $arrJabatan["$r[idPegawai]"] = $r[nilaiKomponen];
        }
		
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select t1.*, t2.pos_name, t2.div_id, t2.rank, t2.grade, t2.payroll_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$nilaiPokok = $arrPokok["$r[id]"];
			
			$nilaiTunjangan = isset($defJabatan["$r[payroll_id]"]) ? $defTunjangan["$r[rank]"]["$r[grade]"] : 0;
			$nilaiTunjangan = $arrJabatan["$r[id]"] > 0 ? $arrJabatan["$r[id]"] : $nilaiTunjangan;
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrMaster["$r[grade]"]."</td>
					<td>".$arrMaster["$r[div_id]"]."</td>
					<td align=\"right\">".getAngka($nilaiPokok)."</td>
					<td align=\"right\">".getAngka($nilaiTunjangan)."</td>
					<td align=\"center\">
					<a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=det&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."',925,550);\"><span>Detail</span></a>";				
					if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=dat&par[idPegawai]=$r[id]".getPar($par,"mode,idPegawai")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
				$text.="</td>
				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>";

        if ($par[mode] == "xls") {
            xls();
            $text.= "<iframe src=\"download.php?d=exp&f=exp-payroll-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
        }

        return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter;
		
		$sql="select * from emp where id='".$par[idPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
		
		$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$namaPegawai = $r[name];
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					Data Gaji
				</h1>
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">				
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NAMA</label>
							<span class=\"field\">".$r[name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r[reg_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">JABATAN</label>
							<span class=\"field\">".$r_[pos_name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">PANGKAT / GRADE</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[rank]."'")."&nbsp;/&nbsp;".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")."&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">SKALA</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[skala]."'")."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">STATUS</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[marital]."'")."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NO BPJS TK</label>
							<span class=\"field\">".$r[bpjs_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">LOKASI</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[location]."'")."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">PENERIMAAN</th>							
							<th width=\"50%\" style=\"text-align:left;\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";
																
				# GET KOMPONEN GAJI
				$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
				$res_=db($sql_);
				$r_=mysql_fetch_array($res_);	
				$idLokasi = $r_[group_id];
				$idJenis = $r_[payroll_id];
				
				$arrData = arrayQuery("select idKomponen, tipeMaster, idJenis from pay_jenis_komponen where idJenis='".$r_[payroll_id]."'");
				
				$sql="select t1.*, t2.tipeKomponen from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t2.idJenis='".$r_[payroll_id]."' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$arrData["$r[idKomponen]"]["$r[tipeKomponen]"] = $r[nilaiKomponen]."\t".$r[flagKomponen];
					$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $r[statusKomponen];
				}
			
				list($idProses, $detailProses) = explode("\t", getField("select concat(idProses, '\t', detailProses) from pay_proses where idLokasi='$idLokasi' and idJenis='$idJenis' order by idProses desc"));
				$sql="select * from ".$detailProses." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t1.idProses='".$idProses."' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$arrProses["$r[idKomponen]"] = $r[nilaiProses];
				}

				$sql="select * from dta_komponen where statusKomponen='t' and realisasiKomponen='t'  and idJenis='".$r_[payroll_id]."' order by tipeKomponen desc, urutanKomponen";
				$res=db($sql);
				while($r=mysql_fetch_array($res)){
					$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
					
					#GAJI POKOK
					$tanggalProses = date("t", strtotime(date('Y')."-".date('m')."-01"));
					$nilaiPokok = getField("select nilaiPokok from pay_pokok where idPegawai='".$par[idPegawai]."' and tanggalPokok<='".date('Y')."-".date('m')."-".$tanggalProses."' order by tanggalPokok desc limit 1");
								
					#TUNJANGAN JABATAN
					$sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and status='1'";
					$res_=db($sql_);
					$r_=mysql_fetch_array($res_);
					
					$nilaiJabatan = getField("select sum(t1.nilaiTunjangan) from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r_[rank]."' and idGrade='".$r_[grade]."'");
					
					$fieldFungsional = $r_[location] == getField("select kodeData from mst_data where kodeCategory='".$arrParameter[7]."' order by urutanData limit 1") ? "pusatFungsional" : "cabangFungsional";			
					$nilaiJabatan = getField("select sum(t1.".$fieldFungsional.") from pay_fungsional t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) where idPangkat='".$r_[rank]."' and idGrade='".$r_[grade]."'");
					
					#POTONGAN KOPERASI
					$nilaiKoperasi = getField("select sum(nilai) from pay_koperasi where idPangkat='".$r_[rank]."'");
					
					$nilaiKomponen = 0;
						
					# Gaji Pokok
					if($r[flagKomponen] == 1){ 						
						$nilaiKomponen = $nilaiPokok;
					}
					
					# Tunjangan Jabatan
					if($r[flagKomponen] == 2){
						$nilaiKomponen = $nilaiJabatan;
					}
					
					# Potongan Pinjaman
					if($r[flagKomponen] == 3){
						$nilaiKomponen = getField("select t2.nilaiAngsuran from ess_pinjaman t1 join ess_angsuran t2 on (t1.idPinjaman=t2.idPinjaman) where t1.idPegawai='".$par[idPegawai]."' and t1.persetujuanPinjaman='t' and t1.sdmPinjaman='t' and month(tanggalAngsuran)='".date('m')."' and year(tanggalAngsuran)='".date('Y')."'");
					}
						
					# Potongan Koperasi
					if($r[flagKomponen] == 5){
						$nilaiKomponen = $nilaiKoperasi;
					}		
						
					# Tunjangan Fungsional
					if($r[flagKomponen] == 6){
						$nilaiKomponen = $nilaiFungsional;
					}	
						
					# Komponen Penerimaan & Potongan
					if($r[flagKomponen] == 0){
						#fixed
						if($r[dasarKomponen] == 4){
							$nilaiKomponen = $r[nilaiKomponen];
						}
						
						#formula
						if($r[dasarKomponen] < 2 ){																				
							$nilaiKomponen = 0;
							$sql___="select * from pay_formula_detail where idFormula='".$r[idPengali]."'";
							$res___=db($sql___);							
							while($r___=mysql_fetch_array($res___)){							
								if($r___[operasi1Detail] == "*")
									$nilaiDetail = $r___[nilaiDetail] * $dasarKomponen["$r___[idKomponen]"];
								if($r___[operasi1Detail] == "/")
									$nilaiDetail = $r___[nilaiDetail] / $dasarKomponen["$r___[idKomponen]"];
								if($r___[operasi1Detail] == "+")
									$nilaiDetail = $r___[nilaiDetail] + $dasarKomponen["$r___[idKomponen]"];
								if($r___[operasi1Detail] == "-")
									$nilaiDetail = $r___[nilaiDetail] - $dasarKomponen["$r___[idKomponen]"];
								
								if($r___[operasi2Detail] == "+")
									$nilaiKomponen += $nilaiDetail;
								
								if($r___[operasi2Detail] == "-")
									$nilaiKomponen -= $nilaiDetail;
							}										
						}
					}
					
					if(isset($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"])){
						$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = $arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"];
					}else{
						$arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? "t" : "f";
					}
					
					list($nilaiData, $flagData) = explode("\t", $arrData["$r[idKomponen]"]["$r[tipeKomponen]"]);
					$nilaiData = $flagData == "t" ? $nilaiData : $nilaiKomponen;
					$nilaiKomponen = isset($arrData["$r[idKomponen]"]["$r[tipeKomponen]"]) ? $nilaiData : 0;
					
					$nilaiKomponen = isset($arrProses["$r[idKomponen]"]) ? $arrProses["$r[idKomponen]"] : $nilaiKomponen;
					if($r[maxKomponen] > 0 && $nilaiKomponen > $r[maxKomponen]) $nilaiKomponen = $r[maxKomponen];
					
					$dasarKomponen["$r[idKomponen]"] = $nilaiKomponen;
					
					
					if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
					$r[nilaiKomponen] = $nilaiKomponen;						
					$dtaNilai["".$r[idKomponen].""] = $nilaiKomponen;
					if($arrStatus["$r[idKomponen]"]["$r[tipeKomponen]"] != "f"){
						$dtaKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
						$tipeKomponen = $r[tipeKomponen];
						$urutanKomponen++;
					}
				}		
				
				if(is_array($dtaKomponen)){
					reset($dtaKomponen);
					while (list($tipeKomponen) = each($dtaKomponen)){	
						if(is_array($dtaKomponen[$tipeKomponen])){
							reset($dtaKomponen[$tipeKomponen]);
							while (list($urutanKomponen, $r) = each($dtaKomponen[$tipeKomponen])){
								//BPJS Kesehatan
								if(in_array($r[idKomponen] , array(38))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[44];
									
								//BPJS JHT	
								}else if(in_array($r[idKomponen] , array(28))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[56];
									
								//BPJS JKK
								}else if(in_array($r[idKomponen] , array(58))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[45];
									
								//BPJS JKM
								}else if(in_array($r[idKomponen] , array(59))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[46];	
									
								//BPJS JP
								}else if(in_array($r[idKomponen] , array(7))){
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"] + $dtaNilai[57];	
									
								}else{
									$r[nilaiKomponen] = $dtaNilai["$r[idKomponen]"];
								}
								$arrKomponen[$tipeKomponen][$urutanKomponen] = $r;
							}
						}
					}
				}
				
				$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));								
				
				for($i=1; $i<=max($cntKomponen); $i++){
					$text.="<tr>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["t"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["t"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($arrKomponen["t"][$i]["nilaiKomponen"])."</span>
							<span style=\"float:right;\">Rp.</span>";								
					$text.="</td>
							<td style=\"padding:3px 20px;\">";
					$text.=empty($arrKomponen["p"][$i]["namaKomponen"])? "&nbsp;":
							"<span style=\"float:left; width:30px;\">".$i.".</span>
							<span style=\"float:left;\">".$arrKomponen["p"][$i]["namaKomponen"]."</span>
							<span style=\"float:right; text-align:right; width:125px;\">".getAngka($arrKomponen["p"][$i]["nilaiKomponen"])."</span>
							<span style=\"float:right;\">Rp.</span>";
					$text.="</td>
						</tr>";
						
					$totKomponen["t"]+=$arrKomponen["t"][$i]["nilaiKomponen"];
					$totKomponen["p"]+=$arrKomponen["p"][$i]["nilaiKomponen"];
				}				
				
				$text.="<tr>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["t"])."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["p"])."</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
					</tr>";
				
				$text.="</tbody>
					</table>
					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>
						<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\"><strong>THP</strong></span>
								<span style=\"float:right; text-align:right; width:125px;\">".getAngka($totKomponen["t"]-$totKomponen["p"])."</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>".trim(terbilang($totKomponen["t"]-$totKomponen["p"]))." Rupiah</span>
							</td>
						</tr>
					</tbody>
					</table>";
					
			$sql="select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status in ('1', 't')";
			$res=db($sql);
			$r=mysql_fetch_array($res);
			$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">Ditransfer ke :</th>							
							<th width=\"50%\" style=\"text-align:left;\">Catatan :</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td style=\"padding:3px 20px; height:75px;\">
							Rek. ".$r[namaBank]." ".$r[branch]."&nbsp;<br>
							No. Acc ".$r[account_no]."&nbsp;<br>
							a.n. ".$namaPegawai."
						</td>
						<td style=\"padding:3px 20px; height:75px;\">".nl2br(getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'"))."&nbsp;</td>
						</tr>
					</tbody>
					</table>
					
				</div>
				</form>
			</div>";
		
		return $text;
	}

function xls()
{

    global $s, $par, $arrTitle, $arrParam, $fExport;

    $arrKode = arrayQuery("select kodeMaster, kodeData from mst_data where kodeCategory = 'S04' and statusData = 't'");

    $direktori = $fExport;
    $namaFile = "exp-payroll-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";

    $field = array("no", "nama", "id", "jabatan", "job grade","unit kerja", "gaji pokok", "t.jabatan");

    $sql="select * from pay_pokok order by tanggalPokok";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
        $arrPokok["$r[idPegawai]"] = $r[nilaiPokok];
    }

    $sql="select t1.* from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.flagKomponen='1' ";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
        if(!isset($arrPokok["$r[idPegawai]"]))
            $arrPokok["$r[idPegawai]"] = $r[nilaiKomponen];
    }


    $defTunjangan = arrayQuery("select idPangkat, idGrade, sum(t1.nilaiTunjangan) from pay_tunjangan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) group by 1,2");

    $sql="select t1.* from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.flagKomponen='2' ";
    $res=db($sql);
    while($r=mysql_fetch_array($res)){
        $arrJabatan["$r[idPegawai]"] = $r[nilaiKomponen];
    }

    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $filterCat = "and t1.cat = '" . $arrKode[$arrParam[$s]] . "'";
    $filter = "WHERE t1.status = '535' $filterCat $filterPensiun";

    if (!empty($par[idUnit]))
        $filter .= " and t2.div_id = '$par[idUnit]'";
    $sql = "SELECT t1.id, t1.name, t1.reg_no, t2.pos_name, t2.grade, t2.rank, t2.div_id from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') $filter";
    $res = db($sql);
    $no = 0;
    while ($r = mysql_fetch_assoc($res)) {
        $no++;

        $nilaiPokok = $arrPokok["$r[id]"];
        $nilaiTunjangan = $arrJabatan["$r[id]"] > 0 ? $arrJabatan["$r[id]"] : $defTunjangan["$r[rank]"]["$r[grade]"];

        $data[] = array(
            $no . "\t center",
            $r[name] . "\t left",
            $r[reg_no] . "\t center",
            $r[pos_name] . "\t left",
            $arrMaster[$r[grade]] . "\t left",
            $arrMaster[$r[div_id]] . "\t left",
            getAngka($nilaiPokok) . "\t right",
            getAngka($nilaiTunjangan) . "\t right"
        );
    }
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);
}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){		
			case "umr":
				$text = umr();				
			break;
			case "det":
				$text = detail();				
			break;
			case "dat":				
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? data() : ubah(); else $text = lihat();
			break;
			
			case "editKomponen":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formKomponen() : ubahKomponen(); else $text = lihat();
			break;
			
			case "delPokok_file":
				$text = isset($menuAccess[$s]["edit"]) ? hapusFile_pokok() : lihat();
			break;
			case "delPokok":
				$text = isset($menuAccess[$s]["delete"]) ? hapusPokok() : lihat();
			break;
			case "editPokok":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formPokok() : ubahPokok(); else $text = lihat();
			break;
			case "addPokok":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formPokok() : tambahPokok(); else $text = lihat();
			break;
			
			case "delMelekat_file":
				$text = isset($menuAccess[$s]["edit"]) ? hapusFile_melekat() : lihat();
			break;
			case "delMelekat":
				$text = isset($menuAccess[$s]["delete"]) ? hapusMelekat() : lihat();
			break;
			case "editMelekat":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formMelekat() : ubahMelekat(); else $text = lihat();
			break;
			case "addMelekat":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formMelekat() : tambahMelekat(); else $text = lihat();
			break;
			
			case "end":
				if(isset($menuAccess[$s]["add"])) $text = endProses();
			break;
			case "dta":
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
