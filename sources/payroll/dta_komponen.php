<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/komponen/";
	
	function upload($idKomponen){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["fileKomponen"]["tmp_name"];
		$fileUpload_name = $_FILES["fileKomponen"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$fileKomponen = "doc-".$idKomponen.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileKomponen);			
		}
		if(empty($fileKomponen)) $fileKomponen = getField("select fileKomponen from dta_komponen where idKomponen='$idKomponen'");
		
		return $fileKomponen;
	}
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$fileKomponen = getField("select fileKomponen from dta_komponen where idKomponen='$par[idKomponen]'");
		if(file_exists($fFile.$fileKomponen) and $fileKomponen!="")unlink($fFile.$fileKomponen);
		
		$sql="update dta_komponen set fileKomponen='' where idKomponen='$par[idKomponen]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername,$fFile;				
		$fileKomponen = getField("select fileKomponen from dta_komponen where idKomponen='$par[idKomponen]'");
		if(file_exists($fFile.$fileKomponen) and $fileKomponen!="")unlink($fFile.$fileKomponen);		
		
		$sql="delete from dta_komponen where idKomponen='$par[idKomponen]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idKomponen")."';</script>";
	}
		
	function update(){
		global $s,$inp,$par,$dta,$det,$cUsername;		
		repField();
		
		$sql="select * from dta_komponen where idJenis='".$inp[idSumber]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrKomponen[] = $r;
		}
		
		$sql="select * from pay_jenis_komponen where idJenis='".$inp[idSumber]."'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$arrJenis[] = $r;
		}
		
		db("delete from dta_komponen where idJenis='".$inp[idTarget]."'");
		if (is_array($arrKomponen)) {		  
			reset($arrKomponen);
			while(list($id, $r) = each($arrKomponen)){
				$idKomponen = getField("select idKomponen from dta_komponen order by idKomponen desc limit 1")+1;
				$sql="insert into dta_komponen (idKomponen, idJenis, idPengali, tipeKomponen, kodeKomponen, tanggalKomponen, mulaiKomponen, selesaiKomponen, namaKomponen, keteranganKomponen, pajakKomponen, perhitunganKomponen, penerimaKomponen, realisasiKomponen, dasarKomponen, nilaiKomponen, maxKomponen, fileKomponen, detailKomponen, urutanKomponen, statusKomponen, flagKomponen, createBy, createTime) values ('$idKomponen', '$inp[idTarget]', '$r[idPengali]', '$r[tipeKomponen]', '$r[kodeKomponen]', '$r[tanggalKomponen]', '".setAngka($r[mulaiKomponen])."', '".setAngka($r[selesaiKomponen])."', '$r[namaKomponen]', '$r[keteranganKomponen]', '$r[pajakKomponen]', '$r[perhitunganKomponen]', '$r[penerimaKomponen]', '$r[realisasiKomponen]', '$r[dasarKomponen]', '".setAngka($r[nilaiKomponen])."', '".setAngka($r[maxKomponen])."', '$r[fileKomponen]', '$r[detailKomponen]', '".setAngka($r[urutanKomponen])."', '$r[statusKomponen]', '$r[flagKomponen]', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
				
				$arrID["".$r[idKomponen].""] = $idKomponen;
			}
		}
		
		db("delete from pay_jenis_komponen where idJenis='".$inp[idTarget]."'");
		if(is_array($arrJenis)){		  
			reset($arrJenis);
			while (list($id, $r) = each($arrJenis)) {
				$idKomponen=$arrID["".$r[idKomponen].""];
				$sql="insert into pay_jenis_komponen (idJenis, idKomponen, tipeMaster, createBy, createTime) values ('$inp[idTarget]', '$idKomponen', '$r[tipeMaster]', '$cUsername', '".date('Y-m-d H:i:s')."')";
				db($sql);
			}
		}
		
		echo "<script>closeBox();reloadPage();</script>";	
	}
		
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();		
		$fileKomponen=upload($par[idKomponen]);
		$penerimaKomponen = implode(",",$inp[penerimaKomponen]);
		
		$sql="update pay_parameter set nilaiParameter='$inp[kodeKomponen]' where nilaiParameter='".getField("select kodeKomponen from dta_komponen where idKomponen='$par[idKomponen]'")."'";
		db($sql);
		
		$sql="update dta_komponen set idPengali='$inp[idPengali]', kodeKomponen='$inp[kodeKomponen]', tanggalKomponen='".setTanggal($inp[tanggalKomponen])."', mulaiKomponen='".setAngka($inp[mulaiKomponen])."', selesaiKomponen='".setAngka($inp[selesaiKomponen])."', namaKomponen='$inp[namaKomponen]', keteranganKomponen='$inp[keteranganKomponen]', pajakKomponen='$inp[pajakKomponen]', perhitunganKomponen='$inp[perhitunganKomponen]', penerimaKomponen='$penerimaKomponen', realisasiKomponen='$inp[realisasiKomponen]', dasarKomponen='$inp[dasarKomponen]', nilaiKomponen='".setAngka($inp[nilaiKomponen])."', maxKomponen='".setAngka($inp[maxKomponen])."', fileKomponen='$fileKomponen', detailKomponen='$inp[detailKomponen]', urutanKomponen='".setAngka($inp[urutanKomponen])."', statusKomponen='$inp[statusKomponen]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idKomponen='$par[idKomponen]'";
		db($sql);				
		
		echo "<script>window.location='?".getPar($par,"mode,idKomponen")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;				
		repField();				
		$idKomponen = getField("select idKomponen from dta_komponen order by idKomponen desc limit 1")+1;		
		$fileKomponen=upload($idKomponen);
		$penerimaKomponen = implode(",",$inp[penerimaKomponen]);
		
		$sql="insert into dta_komponen (idKomponen, idJenis, idPengali, tipeKomponen, kodeKomponen, tanggalKomponen, mulaiKomponen, selesaiKomponen, namaKomponen, keteranganKomponen, pajakKomponen, perhitunganKomponen, penerimaKomponen, realisasiKomponen, dasarKomponen, nilaiKomponen, maxKomponen, fileKomponen, detailKomponen, urutanKomponen, statusKomponen, createBy, createTime) values ('$idKomponen', '$par[idJenis]', '$inp[idPengali]', '$par[tipeKomponen]', '$inp[kodeKomponen]', '".setTanggal($inp[tanggalKomponen])."', '".setAngka($inp[mulaiKomponen])."', '".setAngka($inp[selesaiKomponen])."', '$inp[namaKomponen]', '$inp[keteranganKomponen]', '$inp[pajakKomponen]', '$inp[perhitunganKomponen]', '$penerimaKomponen', '$inp[realisasiKomponen]', '$inp[dasarKomponen]', '".setAngka($inp[nilaiKomponen])."', '".setAngka($inp[maxKomponen])."', '$fileKomponen', '$inp[detailKomponen]', '".setAngka($inp[urutanKomponen])."', '$inp[statusKomponen]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);		
		
		echo "<script>window.location='?".getPar($par,"mode,idKomponen")."';</script>";
	}
		
	function sinkronisasi(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		setValidation("is_null", "inp[idSumber]", "anda harus mengisi sumber");
		setValidation("is_null", "inp[idTarget]", "anda harus mengisi target");
		$text = getValidation();
		
		$text.="<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Sinkronisasi Master Komponen</h1>
		".getBread()."
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\">									
		<p>
		<label class=\"l-input-small\">Sumber</label>
		<div class=\"field\">
		".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","inp[idSumber]"," ","", "", "300px")."
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Target</label>
		<div class=\"field\">
		".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","inp[idTarget]"," ","", "", "300px")."
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
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from dta_komponen where idKomponen='$par[idKomponen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$arrStatus = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[5]."' and statusData='t' order by urutanData");				
		
		if(empty($r[tanggalKomponen])) $r[tanggalKomponen] = date('Y-m-d');
		if(empty($r[urutanKomponen])) $r[urutanKomponen] = getField("select urutanKomponen from dta_komponen where tipeKomponen='$par[tipeKomponen]' order by urutanKomponen desc limit 1") + 1;
		
		$pajakKomponen = $r[pajakKomponen] == "t" ? "checked=\"checked\"" : "";
		$perhitunganKomponen = $r[perhitunganKomponen] == "t" ? "checked=\"checked\"" : "";
		
		$sendiri =  $r[realisasiKomponen] == "f" ? "checked=\"checked\"" : "";		
		$slip =  empty($sendiri) ? "checked=\"checked\"" : "";		
		
		$false =  $r[statusKomponen] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		$penerimaKomponen = explode(",",$r[penerimaKomponen]);
		if (is_array($arrStatus)) {		  
			reset($arrStatus);
			while (list($kodeData, $namaData) = each($arrStatus)) {
				$checkKomponen[$kodeData] = in_array($kodeData, $penerimaKomponen) ? "checked=\"checked\"" : "";
			}
		}
		
		$fixed = $r[dasarKomponen] == 4 ? "checked=\"checked\"" : "";
		$proses = $r[dasarKomponen] == 3 ? "checked=\"checked\"" : "";
		$tabel = $r[dasarKomponen] == 2 ? "checked=\"checked\"" : "";
		$formula = $r[dasarKomponen] < 2 ? "checked=\"checked\"" : "";
		
		if($r[dasarKomponen] < 2){ #formula
			$nilaiKomponen = "block";
			$maxKomponen = "block";
		}else if($r[dasarKomponen] == 4){ #fixed
			$nilaiKomponen = "block";
			$maxKomponen = "none";
		}else if($r[dasarKomponen] == 3){ #proses
			$nilaiKomponen = "none";
			$maxKomponen = "none";
		}else{ #tabel
			$nilaiKomponen = "none";
			$maxKomponen = "none";
		}
		
		setValidation("is_null","inp[kodeKomponen]","anda harus mengisi kode");
		setValidation("is_null","tanggalKomponen","anda harus mengisi tanggal");	
		setValidation("is_null","inp[namaKomponen]","anda harus mengisi komponen");
		$text = getValidation();

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">					
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Tipe</label>
							<div style=\"margin-left:170px;\" class=\"field\">
								<input type=\"text\" id=\"inp[tipeKomponen]\" name=\"inp[tipeKomponen]\"  value=\"".$arrTitle[$s]."\" class=\"mediuminput\" style=\"width:100px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Kode</label>
							<div style=\"margin-left:170px;\" class=\"field\">
								<input type=\"text\" id=\"inp[kodeKomponen]\" name=\"inp[kodeKomponen]\"  value=\"$r[kodeKomponen]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"30\"/>
							</div>
						</p>
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Tanggal</label>
							<div style=\"margin-left:170px;\" class=\"field\">
								<input type=\"text\" id=\"tanggalKomponen\" name=\"inp[tanggalKomponen]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalKomponen])."\" class=\"vsmallinput hasDatePicker\" />
							</div>
						</p>
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Komponen</label>
							<div style=\"margin-left:170px;\" class=\"field\">
								<input type=\"text\" id=\"inp[namaKomponen]\" name=\"inp[namaKomponen]\"  value=\"$r[namaKomponen]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\"/>
							</div>
						</p>
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Keterangan</label>
							<div style=\"margin-left:170px;\" class=\"field\">
								<textarea id=\"inp[keteranganKomponen]\" name=\"inp[keteranganKomponen]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keteranganKomponen]</textarea>
							</div>
						</p>
					</td>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Perhitungan Pajak</label>
							<div style=\"margin-left:175px;\" class=\"fradio\">
								<input type=\"checkbox\" id=\"inp[pajakKomponen]\" name=\"inp[pajakKomponen]\" value=\"t\" $pajakKomponen /> Ya
							</div>
						</p>
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Rutin Bulanan</label>
							<div style=\"margin-left:175px;\" class=\"fradio\">
								<input type=\"checkbox\" id=\"inp[perhitunganKomponen]\" name=\"inp[perhitunganKomponen]\" value=\"t\" $perhitunganKomponen /> Ya
							</div>
						</p>
						<div style=\"display:none\">
							<label style=\"width:150px;\" class=\"l-input-small\">Penerima</label>
							<div class=\"fradio\" style=\"margin-left:175px;\">";						
						if (is_array($arrStatus)) {		  
							reset($arrStatus);
							while (list($kodeData, $namaData) = each($arrStatus)) {							
								$text.="<input type=\"checkbox\" id=\"inp[penerimaKomponen][$kodeData]\" name=\"inp[penerimaKomponen][$kodeData]\" value=\"$kodeData\" ".$checkKomponen[$kodeData]." /> ".$namaData."<br>";					
							}
						}
						$text.="</div>
						</div>
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Realisasi</label>
							<div style=\"margin-left:175px;\" class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[realisasiKomponen]\" value=\"t\" $slip /> <span class=\"sradio\">Slip Gaji</span>
								<input type=\"radio\" id=\"false\" name=\"inp[realisasiKomponen]\" value=\"f\" $sendiri /> <span class=\"sradio\">Kasir</span>
							</div>
						</p>													
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Urutan</label>
							<div style=\"margin-left:170px;\" class=\"field\">
								<input type=\"text\" id=\"inp[urutanKomponen]\" name=\"inp[urutanKomponen]\"  value=\"".getAngka($r[urutanKomponen])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>	
						<p>
							<label style=\"width:150px;\" class=\"l-input-small\">Status</label>
							<div style=\"margin-left:175px;\" class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[statusKomponen]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
								<input type=\"radio\" id=\"false\" name=\"inp[statusKomponen]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>
							</div>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:0px;\"><h3>DASAR PERHITUNGAN</h3></div>
					</div>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Dasar Perhitungan</label>
						<div style=\"margin-left:175px;\" class=\"fradio\">
							<input type=\"radio\" id=\"formula\" name=\"inp[dasarKomponen]\" value=\"1\" onclick=\"setDasar();\" $formula /> <span class=\"sradio\">Formula</span>
							<input type=\"radio\" id=\"tabel\" name=\"inp[dasarKomponen]\" value=\"2\" onclick=\"setDasar();\" $tabel /> <span class=\"sradio\">Tabel</span>
							<input type=\"radio\" id=\"proses\" name=\"inp[dasarKomponen]\" value=\"3\" onclick=\"setDasar();\" $proses /> <span class=\"sradio\">Proses</span>
							<input type=\"radio\" id=\"fixed\" name=\"inp[dasarKomponen]\" value=\"4\" onclick=\"setDasar();\" $fixed /> <span class=\"sradio\">Fixed</span>
						</div>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Periode Cut Off</label>
						<div style=\"margin-left:170px;\" class=\"field\">
							<input type=\"text\" id=\"inp[mulaiKomponen]\" name=\"inp[mulaiKomponen]\"  value=\"".getAngka($r[mulaiKomponen])."\" class=\"mediuminput\" style=\"text-align:right; width:30px;\" onkeyup=\"cekAngka(this);\" maxlength=\"2\" /> s.d <input type=\"text\" id=\"inp[selesaiKomponen]\" name=\"inp[selesaiKomponen]\"  value=\"".getAngka($r[selesaiKomponen])."\" class=\"mediuminput\" style=\"text-align:right; width:30px;\" onkeyup=\"cekAngka(this);\" maxlength=\"2\" />
						</div>
					</p>
					<div id=\"nilaiKomponen\" style=\"display:".$nilaiKomponen."\"> 
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Nilai</label>
						<div style=\"margin-left:170px;\" class=\"field\">
							<input type=\"text\" id=\"inp[nilaiKomponen]\" name=\"inp[nilaiKomponen]\"  value=\"".getAngka($r[nilaiKomponen],2)."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekNumber(this);\" />
						</div>
					</p>
					</div>
					<div id=\"maxKomponen\" style=\"display:".$maxKomponen."\"> 
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Max</label>
						<div style=\"margin-left:170px;\" class=\"field\">
							<input type=\"text\" id=\"inp[maxKomponen]\" name=\"inp[maxKomponen]\"  value=\"".getAngka($r[maxKomponen],2)."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekNumber(this);\" />
						</div>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Komponen Dari</label>
						<div style=\"margin-left:170px;\" class=\"field\">
							".comboData("select * from pay_formula where statusFormula='t' order by namaFormula","idFormula","namaFormula","inp[idPengali]"," ",$r[idPengali],"", "300px")."
						</div>
					</p>
					</div>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">File</label>
						<div style=\"margin-left:170px;\"  class=\"field\">";
							$text.=empty($r[fileKomponen])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:245px;\" maxlength=\"100\" />
								<div class=\"fakeupload\" style=\"width:300px;\">
									<input type=\"file\" id=\"fileKomponen\" name=\"fileKomponen\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<a href=\"download.php?d=komponen&f=$r[idKomponen]\"><img src=\"".getIcon($fFile."/".$r[fileKomponen])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
								<input type=\"file\" id=\"fileKomponen\" name=\"fileKomponen\" style=\"display:none;\" />
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label style=\"width:150px;\" class=\"l-input-small\">Keterangan</label>
						<div style=\"margin-left:170px;\" class=\"field\">
							<textarea id=\"inp[detailKomponen]\" name=\"inp[detailKomponen]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[detailKomponen]</textarea>
						</div>
					</p>
				</div>	
				<br clear=\"all\">
				<div style=\"position:absolute; top:0; right:0; margin-right:20px; margin-top:10px;\">
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idKomponen")."';\"/>
				</div>
			</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$arrParam,$menuAccess;
		$par[tipeKomponen] = $arrParam[$s];
		if(empty($par[idJenis])) $par[idJenis] = getField("select idJenis from pay_jenis where statusJenis='t' order by idJenis limit 1");
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; top:0; right:0; margin-right:20px; margin-top:10px;\">
			".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","par[idJenis]"," ",$par[idJenis], "onchange=\"document.getElementById('form').submit();\"", "300px", "chosen-select")."";
		if(isset($menuAccess[$s]["add"])) $text.=" <a href=\"#Set\" class=\"btn btn1 btn_tag\" onclick=\"openBox('popup.php?par[mode]=set".getPar($par,"mode,idJenis")."',675,250);\"><span>Sinkronisasi</span></a>";
		$text.="
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
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idKomponen")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" style=\"width:20px; vertical-align:middle;\">No.</th>
					<th rowspan=\"2\" style=\"width:100px; vertical-align:middle;\">Kode</th>
					<th rowspan=\"2\" style=\"min-width:150px; vertical-align:middle;\">Komponen</th>
					<th colspan=\"5\" >Perhitungan</th>
					<th colspan=\"3\" >Pembayaran</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" style=\"width:50px; vertical-align:middle;\">Kontrol</th>";
		$text.="</tr>
				<tr>
					<th width=\"65\">Pajak</th>
					<th width=\"65\">Formula</th>
					<th width=\"65\">Tabel</th>
					<th width=\"65\">Proses</th>
					<th width=\"65\">Fixed</th>
					<th width=\"65\">Bulanan</th>
					<th width=\"65\">Slip</th>
					<th width=\"65\">Kasir</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where tipeKomponen='".$par[tipeKomponen]."' and idJenis='".$par[idJenis]."'";
		if(!empty($par[filter]))
		$filter.= " and (
			lower(namaKomponen) like '%".strtolower($par[filter])."%'
		)";				
		
		$sql="select * from dta_komponen $filter order by urutanKomponen";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;			
			
			$pajakKomponen = $r[pajakKomponen] == "t"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			$formulaKomponen = $r[dasarKomponen] == "1"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			$tabelKomponen = $r[dasarKomponen] == "2"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			$prosesKomponen = $r[dasarKomponen] == "3"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			$fixedKomponen = $r[dasarKomponen] == "4"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			
			$perhitunganKomponen = $r[perhitunganKomponen] == "t"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			$slipKomponen = $r[realisasiKomponen] == "t"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			$kasirKomponen = $r[realisasiKomponen] == "f"? "<img src=\"styles/images/checked.png\" >" : "<img src=\"styles/images/unchecked.png\" >";
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[kodeKomponen]</td>
					<td>$r[namaKomponen]</td>
					<td align=\"center\">$pajakKomponen</td>
					<td align=\"center\">$formulaKomponen</td>
					<td align=\"center\">$tabelKomponen</td>
					<td align=\"center\">$prosesKomponen</td>
					<td align=\"center\">$fixedKomponen</td>
					<td align=\"center\">$perhitunganKomponen</td>
					<td align=\"center\">$slipKomponen</td>
					<td align=\"center\">$kasirKomponen</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idKomponen]=$r[idKomponen]".getPar($par,"mode,idKomponen")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
				if(isset($menuAccess[$s]["delete"])) $text.= empty($r[flagKomponen]) ?
				"<a href=\"?par[mode]=del&par[idKomponen]=$r[idKomponen]".getPar($par,"mode,idKomponen")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>":
				"<a href=\"#\" onclick=\"alert('sorry, data has been use');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "delFile":
				$text = isset($menuAccess[$s]["edit"]) ? hapusFile() : lihat();
			break;
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "set":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? sinkronisasi() : update(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>