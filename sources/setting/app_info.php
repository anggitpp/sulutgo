<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "images/info/";	
	
	function hapusFile(){
		global $s,$inp,$par,$fFile,$cUsername;
		$fileInfo = getField("select fileInfo from app_info where kodeInfo='$par[kodeInfo]'");
		if(file_exists($fFile.$fileInfo) and $fileInfo!="")unlink($fFile.$fileInfo);
		
		$sql="update app_info set fileInfo='' where kodeInfo='$par[kodeInfo]'";
		db($sql);

		echo "<script>window.location='?".getPar($par,"mode")."';</script>";
	}
	
	function hapusLogo(){
		global $s,$inp,$par,$fFile,$cUsername;
		$logoInfo = getField("select logoInfo from app_info where kodeInfo='$par[kodeInfo]'");
		if(file_exists($fFile.$logoInfo) and $logoInfo!="")unlink($fFile.$logoInfo);
		
		$sql="update app_info set logoInfo='' where kodeInfo='$par[kodeInfo]'";
		db($sql);

		echo "<script>window.location='?".getPar($par,"mode")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername;
		
		$fileIcon = $_FILES["fileInfo"]["tmp_name"];
		$fileIcon_name = $_FILES["fileInfo"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$fileInfo = "img-".$par[kodeInfo].".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $fileInfo);			
		}
		if(empty($fileInfo)) $fileInfo = getField("select fileInfo from app_info where kodeInfo='$par[kodeInfo]'");
		
		$fileLogo = $_FILES["logoInfo"]["tmp_name"];
		$fileLogo_name = $_FILES["logoInfo"]["name"];
		if(($fileLogo!="") and ($fileLogo!="none")){						
			fileUpload($fileLogo,$fileLogo_name,$fFile);			
			$logoInfo = "logo-".$par[kodeInfo].".".getExtension($fileLogo_name);
			fileRename($fFile, $fileLogo_name, $logoInfo);			
		}
		if(empty($logoInfo)) $logoInfo = getField("select logoInfo from app_info where kodeInfo='$par[kodeInfo]'");
		
		repField();
		
		$sql="update app_info set namaInfo='$inp[namaInfo]', keteranganInfo='$inp[keteranganInfo]', fileInfo='$fileInfo', logoInfo='$logoInfo', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeInfo='$par[kodeInfo]'";
		db($sql);
		
		echo "<script>
				alert('update success !!');
				window.location='?".getPar($par,"mode")."';
			</script>";
	}		
	
	function form(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		$par[kodeInfo]=1;
		
		$sql="select * from app_info where kodeInfo='$par[kodeInfo]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);															
		
		setValidation("is_null","inp[namaInfo]","you must fill title");
		setValidation("is_null","inp[keteranganInfo]","you must fill text");		
		$text = getValidation();	

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Title</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaInfo]\" name=\"inp[namaInfo]\"  value=\"$r[namaInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Text</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[keteranganInfo]\" name=\"inp[keteranganInfo]\"  value=\"$r[keteranganInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Header</label>
						<div class=\"field\">";
							$text.=empty($r[fileInfo])?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fileInfo\" name=\"fileInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<img src=\"".$fFile."".$r[fileInfo]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delIco".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Login</label>
						<div class=\"field\">";
							$text.=empty($r[logoInfo])?
								"<input type=\"text\" id=\"logoTemp\" name=\"logoTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"logoInfo\" name=\"logoInfo\" class=\"realupload\" size=\"50\" onchange=\"this.form.logoTemp.value = this.value;\" />
								</div>":
								"<img src=\"".$fFile."".$r[logoInfo]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delImg".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Update\"/>						
					</p>
				</div>
			</form>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		$par[kodeInfo]=1;
		
		$sql="select * from app_info where kodeInfo='$par[kodeInfo]'";
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
						<label class=\"l-input-small\">Title</label>
						<span class=\"field\">$r[namaInfo]&nbsp;</span>
					</p>					
					<p>
						<label class=\"l-input-small\">Text</label>
						<span class=\"field\">$r[keteranganInfo]&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Login</label>
						<span class=\"field\">";
						if(!empty($r[fileInfo]))
						$text.="<img src=\"".$fFile."".$r[fileInfo]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
						$text.="&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Logo Header</label>
						<span class=\"field\">";
						if(!empty($r[logoInfo]))
						$text.="<img src=\"".$fFile."".$r[logoInfo]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">";
						$text.="&nbsp;</span>
					</p>
				</div>
			</form>";
		return $text;
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "delIco":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
			break;
			case "delImg":
				if(isset($menuAccess[$s]["edit"])) $text = hapusLogo(); else $text = lihat();
			break;
			default:
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
		}
		return $text;
	}	
?>