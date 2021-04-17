<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "images/profile/";	
	
	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;
		$fileProfile = getField("select fileProfile from pay_profile where idProfile='$par[idProfile]'");
		if(file_exists($fFile.$fileProfile) and $fileProfile!="")unlink($fFile.$fileProfile);
		
		$sql="update pay_profile set fileProfile='' where idProfile='$par[idProfile]'";
		db($sql);

		echo "<script>window.location='?".getPar($par,"mode")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername;
		
		$fileIcon = $_FILES["fileProfile"]["tmp_name"];
		$fileIcon_name = $_FILES["fileProfile"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$fileProfile = "img-".$par[idProfile].".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $fileProfile);			
		}
		if(empty($fileProfile)) $fileProfile = getField("select fileProfile from pay_profile where idProfile='$par[idProfile]'");
						
		repField();
		
		$sql="update pay_profile set judulProfile='$inp[judulProfile]', namaProfile='$inp[namaProfile]', alamatProfile='$inp[alamatProfile]', keteranganProfile='$inp[keteranganProfile]', fileProfile='$fileProfile', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idProfile='$par[idProfile]'";
		db($sql);
		
		echo form().
			"<script>
				alert('update success !!');
				window.location='?".getPar($par,"mode")."';
			</script>";
	}		
	
	function form(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		$par[idProfile]=1;
		
		$sql="select * from pay_profile where idProfile='$par[idProfile]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);															
		
		setValidation("is_null","inp[judulProfile]","anda harus mengisi judul");
		setValidation("is_null","inp[namaProfile]","anda harus mengisi nama perusahaan");
		setValidation("is_null","inp[alamatProfile]","anda harus mengisi alamat");
		setValidation("is_null","inp[keteranganProfile]","anda harus mengisi keterangan");
		$text = getValidation();	

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Judul</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[judulProfile]\" name=\"inp[judulProfile]\"  value=\"$r[judulProfile]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
						</div>
					</p>	
					<p>
						<label class=\"l-input-small\">Nama Perusahaan</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaProfile]\" name=\"inp[namaProfile]\"  value=\"$r[namaProfile]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Alamat</label>
						<div class=\"field\">
							<textarea id=\"inp[alamatProfile]\" name=\"inp[alamatProfile]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[alamatProfile]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganProfile]\" name=\"inp[keteranganProfile]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganProfile]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Logo</label>
						<div class=\"field\">";
							$text.=empty($r[fileProfile])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fileProfile\" name=\"fileProfile\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<img src=\"".$fFile."".$r[fileProfile]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delIco".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Update Data\"/>						
					</p>
				</div>
			</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		$par[idProfile]=1;
		
		$sql="select * from pay_profile where idProfile='$par[idProfile]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);															
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Judul</label>
						<span class=\"field\">$r[judulProfile]&nbsp;</span>
					</p>					
					<p>
						<label class=\"l-input-small\">Nama Perusahaan</label>
						<span class=\"field\">$r[namaProfile]&nbsp;</span>
					</p>					
					<p>
						<label class=\"l-input-small\">Alamat</label>
						<span class=\"field\">".nl2br($r[alamatProfile])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[keteranganProfile])."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Logo</label>
						<span class=\"field\">";
						if(!empty($r[fileProfile]))
						$text.="<img src=\"".$fFile."".$r[fileProfile]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
						$text.="&nbsp;</span>
					</p>
				</div>
			</form>";
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "delIco":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
			break;			
			default:
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
		}
		return $text;
	}	
?>