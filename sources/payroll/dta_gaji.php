<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/export/";
	$fData = "files/upload/";
	
	function setTable(){
		global $s,$inp,$par,$fData,$cUsername,$arrParameter;
		if(!in_array(strtolower(substr($_FILES[fileData][name],-3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name],-4)), array("xlsx"))){
			return "file harus dalam format .xls atau .xlsx";
		}else{
			$fileUpload = $_FILES[fileData][tmp_name];
			$fileUpload_name = $_FILES[fileData][name];
			if(($fileUpload!="") and ($fileUpload!="none")){						
				fileUpload($fileUpload,$fileUpload_name,$fData);
				$fileData = md5($cUsername."-".date("Y-m-d H:i:s")).".".getExtension($fileUpload_name);
				fileRename($fData, $fileUpload_name, $fileData);
				
				db("DROP TABLE IF EXISTS tmp_master");
				db("CREATE TABLE IF NOT EXISTS tmp_master (					  
					  idMaster int(11) NOT NULL,
					  namaMaster varchar(150) NOT NULL,
					  nikMaster varchar(30) NOT NULL,
					  statusMaster varchar(150) NOT NULL,
					  createBy varchar(30) NOT NULL,
					  createTime datetime NOT NULL,
					  PRIMARY KEY (idMaster)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
				
				$inputFileName = $fData.$fileData;
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
		global $s,$inp,$par,$fData,$cUsername,$arrParameter,$arrParam;
		$inputFileName = $fData.$par[fileData];
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
			$arrMaster = arrayQuery("select idKomponen, idKomponen from pay_jenis_komponen where idJenis='".$par[idJenis]."'");
			$rowData = $sheet->rangeToArray('A' . $par[rowData] . ':' .numToAlpha(3 + count($arrMaster)). $par[rowData], NULL, TRUE, FALSE);
			$dta = $rowData[0];
			
			$result.= " ".$dta[1];		
			if(is_numeric(trim(strtolower($dta[0])))){
				$namaPegawai = trim($dta[1]);
				$nikPegawai = trim($dta[2]);
				$pphCatatan = strtolower(trim($dta[3])) == "nett" ? "t" : "f";
				
				$idPegawai = getField("select id from emp where trim(reg_no)='".$nikPegawai."'");
				if(empty($idPegawai)) $idPegawai = getField("select id from emp where trim(lower(name))='".strtolower($namaPegawai)."'");
				
				$statusMaster = $idPegawai > 0 ? "OK" : "NPP : ".$nikPegawai." belum terdaftar";
				if($idPegawai > 0){	
				
					$idCatatan = getField("select max(idCatatan) from pay_catatan") + 1;
					$sql=getField("select idCatatan from pay_catatan where idPegawai='".$idPegawai."'")?
					"update pay_catatan set pphCatatan='".$pphCatatan."' where idPegawai='".$idPegawai."'":
					"insert into pay_catatan (idCatatan, idPegawai, pphCatatan, createBy, createTime) values ('".$idCatatan."', '".$idPegawai."', '".$pphCatatan."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
					db($sql);
				
					$sql="select * from dta_komponen where statusKomponen='t' and idJenis='".$par[idJenis]."' order by tipeKomponen, urutanKomponen";
					$res=db($sql);	
					while($r=mysql_fetch_array($res)){
						if(isset($arrMaster["$r[idKomponen]"]))
							$arrKomponen["$r[tipeKomponen]"]["$r[idKomponen]"] = $r;
					}
					
					$cols=4;
					if(is_array($arrKomponen["t"])){		  
						reset($arrKomponen["t"]);
						while (list($idKomponen) = each($arrKomponen["t"])){
							$nilaiKomponen = setAngka($dta[$cols]);
							$sql=getField("select idKomponen from pay_komponen where idPegawai='".$idPegawai."' and idKomponen='".$idKomponen."'")?
							"update pay_komponen set nilaiKomponen='".setAngka($nilaiKomponen)."', flagKomponen='t', statusKomponen='t' where idKomponen='".$idKomponen."' and idPegawai='".$idPegawai."'":
							"insert into pay_komponen (idKomponen, idPegawai, tipeKomponen, nilaiKomponen, flagKomponen, statusKomponen, createBy, createTime) values ('".$idKomponen."', '".$idPegawai."', 't', '".setAngka($nilaiKomponen)."', 't', 't', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
							if($nilaiKomponen > 0)
								db($sql);
							else
								db("update pay_komponen set flagKomponen='f' where idKomponen='".$idKomponen."' and idPegawai='".$idPegawai."'");
							$cols++;
						}
					}
					if(is_array($arrKomponen["p"])){		  
						reset($arrKomponen["p"]);
						while (list($idKomponen) = each($arrKomponen["p"])) {
							$nilaiKomponen = setAngka($dta[$cols]);
							$sql=getField("select idKomponen from pay_komponen where idPegawai='".$idPegawai."' and idKomponen='".$idKomponen."'")?
							"update pay_komponen set nilaiKomponen='".setAngka($nilaiKomponen)."', flagKomponen='t', statusKomponen='t' where idKomponen='".$idKomponen."' and idPegawai='".$idPegawai."'":
							"insert into pay_komponen (idKomponen, idPegawai, tipeKomponen, nilaiKomponen, flagKomponen, statusKomponen, createBy, createTime) values ('".$idKomponen."', '".$idPegawai."', 'p', '".setAngka($nilaiKomponen)."', 't', 't', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
							if($nilaiKomponen > 0)
								db($sql);
							else
								db("update pay_komponen set flagKomponen='f' where idKomponen='".$idKomponen."' and idPegawai='".$idPegawai."'");
							$cols++;
						}
					}
				}
				
				$idMaster = getField("select idMaster from tmp_master order by idMaster desc limit 1") + 1;
				$sql="insert into tmp_master (idMaster, namaMaster, nikMaster, statusMaster, createBy, createTime) values ('".$idMaster."', '".$namaPegawai."', '".$nikPegawai."', '".$statusMaster."', '".$cUsername."', '".date('Y-m-d H:i:s')."')";
				db($sql);
			}
			
			$progresData = getAngka($par[rowData]/$highestRow * 100);	
			return $progresData."\t(".$progresData."%) ".getAngka($par[rowData])." of ".getAngka($highestRow)."\t".$result;	
		}
	}
	
	function endProses(){
		global $s,$inp,$par,$cUsername;
		
		$file = "files/Upload Master.log";
		if(file_exists($file))unlink($file);
		$fileName = fopen($file, "w");
								
		$sql="select * from tmp_master order by idMaster";
		$res=db($sql);
		$text.= "NPP\t\tNAMA\r\n";
		while($r=mysql_fetch_array($res)){
			$text.= $r[nikMaster]."\t".$r[namaMaster]."\t".$r[statusMaster]."\r\n";			
		}
				
		fwrite($fileName, $text);
		fclose($fileName);		
		usleep(5000);
		
		db("DROP TABLE IF EXISTS tmp_master");		
		return "upload data selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
	}
	
	function hapus(){
		global $s,$inp,$par,$cUsername;
		repField();
		
		$sql="delete from pay_jenis  where idJenis='$par[idJenis]'";
		db($sql);
		
		$sql="delete from pay_jenis_komponen  where idJenis='$par[idJenis]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idJenis")."';</script>";	
	}
	
	function update(){
		global $s,$inp,$par,$dta,$det,$cUsername;		
		repField();
		
		db("delete from pay_jenis_komponen where idJenis='$par[idJenis]'");
		if(is_array($dta)){		  
			reset($dta);
			while (list($idKomponen, $tipeMaster) = each($dta)) {				
				$sql="insert into pay_jenis_komponen (idJenis, idKomponen, tipeMaster, createBy, createTime) values ('$par[idJenis]', '$idKomponen', '$tipeMaster', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
			}
		}
		
		echo "<script>window.location='?".getPar($par,"mode,idJenis")."';</script>";	
	}
	
	function ubah(){
		global $s,$inp,$par,$dta,$det,$cUsername;		
		repField();
		
		$sql="update pay_jenis set kodeJenis='$inp[kodeJenis]', namaJenis='$inp[namaJenis]', keteranganJenis='$inp[keteranganJenis]', statusJenis='$inp[statusJenis]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idJenis='$par[idJenis]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";	
	}
	
	function tambah(){
		global $s,$inp,$par,$dta,$det,$cUsername;		
		$idJenis=getField("select idJenis from pay_jenis order by idJenis desc limit 1")+1;				
		repField();
		
		$sql="insert into pay_jenis (idJenis, kodeJenis, namaJenis, keteranganJenis, statusJenis, createBy, createTime) values ('$idJenis', '$inp[kodeJenis]', '$inp[namaJenis]', '$inp[keteranganJenis]', '$inp[statusJenis]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}	
	
	
	function formUpload(){
		global $c,$p,$m,$s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess;
						
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".getField("select namaJenis from pay_jenis where idJenis='".$par[idJenis]."'")."</h1>
					".getBread(ucwords("upload data"))."					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\" onsubmit=\"return validation(document.form);\"  enctype=\"multipart/form-data\">	
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
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"detil\">* download template.xls</a></div>
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
					<a href=\"download.php?d=logMaster\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?&c=$c&p=$p&m=$m&s=$s';\"/>
				</div>
			</form>	
			<br><span id=\"templateInfo\" style=\"color:#ff0000\"># mohon download template terlebih dahulu, <strong><u>format kolom berbeda</u></strong> untuk setiap kategori gaji</font>
			</div>";
			
			if($par[mode] == "xls"){			
				xls();			
				$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
			}
		return $text;
	}	
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$sql="select * from pay_jenis where idJenis='".$par[idJenis]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false = $r[statusJenis] == "f" ? "checked=\"checked\"" : "";
		$true = empty($false) ? "checked=\"checked\"" : "";
		
		setValidation("is_null", "inp[kodeJenis]", "anda harus mengisi kode");
		setValidation("is_null", "inp[namaJenis]", "anda harus mengisi kategori");
		$text = getValidation();
		
		$text.="<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\">									
		<p>
		<label class=\"l-input-small\">Kode</label>
		<div class=\"field\">
		<input type=\"text\" id=\"inp[kodeJenis]\" name=\"inp[kodeJenis]\"  value=\"$r[kodeJenis]\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"50\"/>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Kategori</label>
		<div class=\"field\">
		<input type=\"text\" id=\"inp[namaJenis]\" name=\"inp[namaJenis]\"  value=\"$r[namaJenis]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\"/>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Keterangan</label>
		<div class=\"field\">
		<textarea id=\"inp[keteranganJenis]\" name=\"inp[keteranganJenis]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keteranganJenis]</textarea>
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Status</label>
		<div class=\"fradio\">
		<input type=\"radio\" id=\"true\" name=\"inp[statusJenis]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
		<input type=\"radio\" id=\"false\" name=\"inp[statusJenis]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>
		</div>
		</p>
		<div style=\"position:absolute; top:0; right:0; margin-right:20px; margin-top:10px;\">
		<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" />
		<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
		</div>
		</form>
		</div>";
		return $text;
	}
	
	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$sql="select * from pay_jenis where idJenis='".$par[idJenis]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" >	
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" style=\"margin-top:30px;\">
					<thead>
						<tr>							
							<th width=\"50%\">PENERIMAAN</th>							
							<th width=\"50%\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";
									
				$sql="select * from dta_komponen where statusKomponen='t' and idJenis='".$par[idJenis]."' ".$filter." order by tipeKomponen, urutanKomponen";
				$res=db($sql);	
				while($r=mysql_fetch_array($res)){
					if($tipeKomponen != $r[tipeKomponen]) $urutanKomponen=1;
					$arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
					$tipeKomponen = $r[tipeKomponen];
					$urutanKomponen++;
				}				
				
				$cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));								
				$arrMaster = arrayQuery("select idKomponen, tipeMaster, idJenis from pay_jenis_komponen where idJenis='".$par[idJenis]."'");
				
				for($i=1; $i<=max($cntKomponen); $i++){
					$checkPenerimaan = isset($arrMaster[$arrKomponen["t"][$i]["idKomponen"]]["t"]) ? "checked=\"checked\"" : "";
					$checkPotongan = isset($arrMaster[$arrKomponen["p"][$i]["idKomponen"]]["p"]) ? "checked=\"checked\"" : "";
					$text.="<tr>
							<td>";
					$text.=empty($arrKomponen["t"][$i]["namaKomponen"]) ? "&nbsp;" : "<input type=\"checkbox\" id=\"dta[".$arrKomponen["t"][$i]["idKomponen"]."]\" name=\"dta[".$arrKomponen["t"][$i]["idKomponen"]."]\" value=\"t\" ".$checkPenerimaan." /> ".$arrKomponen["t"][$i]["namaKomponen"]."";
					$text.="</td>
							<td>";
					$text.=empty($arrKomponen["p"][$i]["namaKomponen"]) ? "&nbsp;" : "<input type=\"checkbox\" id=\"dta[".$arrKomponen["p"][$i]["idKomponen"]."]\" name=\"dta[".$arrKomponen["p"][$i]["idKomponen"]."]\" value=\"p\" ".$checkPotongan." /> ".$arrKomponen["p"][$i]["namaKomponen"]."";
					$text.="</td>
						</tr>";
				}
				
				$text.="</tbody>
					</table>
					<div style=\"position:absolute; top:0; right:0; margin-right:20px; margin-top:10px;\">
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" />
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>
					</div>
					</form>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess;
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		
		</div>    
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
		<div id=\"pos_l\" style=\"float:left;\">
		<p>
		<span>Search : </span>
		<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
		<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
		</p>
		</div>
		<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idPkp")."',675,350);\"><span>Tambah Data</span></a>";
		$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
		<tr>
		<th width=\"20\">No.</th>
		<th width=\"100\">Kode</th>
		<th>Kategori</th>
		<th>Keterangan</th>
		<th width=\"50\">Status</th>
		<th width=\"50\">View</th>";
		if(isset($menuAccess[$s]["add"])) $text.="<th width=\"50\">Upload</th>";
		if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
		</thead>
		<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
		lower(kodeJenis) like '%".strtolower($par[filter])."%'
		or lower(namaJenis) like '%".strtolower($par[filter])."%'
		or lower(keteranganJenis) like '%".strtolower($par[filter])."%'
		)";
		
		$arrJumlah = arrayQuery("select payroll_id, count(*) from emp_phist where status='1' group by 1");
		
		$sql="select * from pay_jenis $filter order by idJenis";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$statusJenis = $r[statusJenis] == "t"?
			"<img src=\"styles/images/t.png\" title=\"Active\">":
			"<img src=\"styles/images/f.png\" title=\"Not Active\">";
			
			$text.="<tr>
			<td>$no.</td>
			<td>$r[kodeJenis]</td>
			<td>$r[namaJenis]</td>	
			<td>".nl2br($r[keteranganJenis])."</td>
			<td align=\"center\">$statusJenis</td>
			<td align=\"center\"><a href=\"?par[mode]=det&par[idJenis]=$r[idJenis]".getPar($par,"mode,idJenis")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a></td>";
			if(isset($menuAccess[$s]["add"]))
			$text.="<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=upl&par[idJenis]=$r[idJenis]".getPar($par,"mode,idJenis")."',675,350);\" title=\"Upload Data\" class=\"add\"><span>Upload</span></a></td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idJenis]=$r[idJenis]".getPar($par,"mode,idJenis")."',675,350);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idJenis]=$r[idJenis]".getPar($par,"mode,idJenis")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
		</table>
		</div>";
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck;
		require_once 'plugins/PHPExcel.php';
		$arrMaster = arrayQuery("select idKomponen, idKomponen from pay_jenis_komponen where idJenis='".$par[idJenis]."'");
		$sql="select * from dta_komponen where statusKomponen='t' and idJenis='".$par[idJenis]."' order by tipeKomponen, urutanKomponen";
		$res=db($sql);	
		while($r=mysql_fetch_array($res)){
			if(isset($arrMaster["$r[idKomponen]"]))
				$arrKomponen["$r[tipeKomponen]"]["$r[idKomponen]"] = $r;
		}
		
		$sql="select t1.* from pay_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.idJenis='".$par[idJenis]."' and t1.flagKomponen='t' and t1.statusKomponen='t'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrData["$r[idPegawai]"]["$r[idKomponen]"] = $r[nilaiKomponen];
		}
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		
		$cols=5;
		if(is_array($arrKomponen["t"])){		  
			reset($arrKomponen["t"]);
			while (list($idKomponen) = each($arrKomponen["t"])) {
				$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
				$cols++;
			}
		}
		if(is_array($arrKomponen["p"])){		  
			reset($arrKomponen["p"]);
			while (list($idKomponen) = each($arrKomponen["p"])) {
				$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
				$cols++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);	
		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(30);	
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
		$objPHPExcel->getActiveSheet()->mergeCells('B1:B2');
		$objPHPExcel->getActiveSheet()->mergeCells('C1:C2');
		$objPHPExcel->getActiveSheet()->mergeCells('D1:D2');
		if(count($arrKomponen["t"]) > 0)
			$objPHPExcel->getActiveSheet()->mergeCells('E1:'.numToAlpha(5 + count($arrKomponen["t"]) - 1).'1');
		
		if(count($arrKomponen["p"]) > 0)
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha(5 + count($arrKomponen["t"])).'1:'.numToAlpha(5 + count($arrKomponen["t"]) + count($arrKomponen["p"]) - 1).'1');
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'PERHITUNGAN PPH 21');
		$cols=5;
		if(is_array($arrKomponen["t"])){		  
			reset($arrKomponen["t"]);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'1', 'PENERIMAAN');
			while (list($idKomponen, $r) = each($arrKomponen["t"])) {
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'2', strtoupper($r[namaKomponen]));
				$cols++;
			}
		}
		if(is_array($arrKomponen["p"])){		  
			reset($arrKomponen["p"]);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'1', 'POTONGAN');
			while (list($idKomponen, $r) = each($arrKomponen["p"])) {
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'2', strtoupper($r[namaKomponen]));
				$cols++;
			}
		}
		$cols--;
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).'1')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).'1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A2:'.numToAlpha($cols).'2')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).'2')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$rows = 3;						
		$arrPPH = arrayQuery("select idPegawai, pphCatatan from pay_catatan");
		$sql="select * from dta_pegawai where payroll_id='".$par[idJenis]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;				
			$pphCatatan = $arrPPH["".$r[id].""] == "t" ? "NETT" : "GROSS";
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));				
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$rows, $r[reg_no], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $pphCatatan);				
			
			$cols=5;
			if(is_array($arrKomponen["t"])){		  
				reset($arrKomponen["t"]);
				while (list($idKomponen) = each($arrKomponen["t"])) {
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrData["$r[id]"][$idKomponen]);
					$cols++;
				}
			}
			if(is_array($arrKomponen["p"])){		  
				reset($arrKomponen["p"]);
				while (list($idKomponen) = each($arrKomponen["p"])) {
					$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrData["$r[id]"][$idKomponen]);
					$cols++;
				}
			}
			$cols--;
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$rows.':'.numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	

			$rows++;
		}
		$rows--;
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B1:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C1:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D1:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols=5;
		if(is_array($arrKomponen["t"])){		  
			reset($arrKomponen["t"]);
			while (list($idKomponen, $r) = each($arrKomponen["t"])) {
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'1:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$cols++;
			}
		}
		if(is_array($arrKomponen["p"])){		  
			reset($arrKomponen["p"]);
			while (list($idKomponen, $r) = each($arrKomponen["p"])) {
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'1:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$cols++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A3:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){	
			case "del":
			if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "det":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? detail() : update(); else $text = detail();
			break;
			case "edit":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "add":
			if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
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
				
			case "xls":
				$text = isset($menuAccess[$s]["add"]) ? formUpload() : lihat();
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