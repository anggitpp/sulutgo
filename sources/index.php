<?php	
	if(!getUser()) echo "<script>logout();</script>";	
	$fFile = "images/user/";	
	
	function cek(){
		global $s,$inp,$par,$cUsername;		
		if(getField("select username from app_user where username='$inp[username]' and username!='$cUsername'"))
		return "sorry, username \" $inp[username] \" already exist";
	}		
	
	function fotoUser(){
		global $s,$inp,$par,$fFile,$cUsername;		
		if(in_array($_FILES["fotoUser"]["type"],array('image/jpg','image/jpeg','image/gif','image/png'))){
			$image =$_FILES["fotoUser"]["name"];
			$uploadFile = $_FILES['fotoUser']['tmp_name'];
						
			$oldFile = $fFile.$image;
			$ext = getExtension($image);
			$fotoUser = md5(date("Y-m-d H:i:s").uniqid(rand(), true)).".".$ext;
			$newFile = $fFile.$fotoUser;
			$ext = getExtension($oldFile);
				
			if($ext=="jpg" || $ext=="jpeg" ) $src = imagecreatefromjpeg($uploadFile);
			if($ext=="png") $src = imagecreatefrompng($uploadFile);
			if($ext=="gif") $src = imagecreatefromgif($uploadFile);				
									
			$maxWidth = $maxHeight = 100;
			list($width,$height)=getimagesize($uploadFile);
			$ratioH = $maxHeight/$height;
			$ratioW = $maxWidth/$width;
			$ratio = min($ratioH, $ratioW);
			$newWidth = ($width>$maxWidth || $height>$maxHeight) ? intval($ratio*$width) : $width;
			$newHeight = ($width>$maxWidth || $height>$maxHeight) ? intval($ratio*$height) : $height;
					
			
			$tmp=imagecreatetruecolor($newWidth,$newHeight);
			imagecopyresampled($tmp,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);				
			$filename = $fFile. $_FILES['fotoUser']['name'];		
			imagejpeg($tmp,$filename,100);
			
			imagedestroy($src);
			imagedestroy($tmp);
											
			fileRename("", $oldFile, $newFile);						
			if($cUsername == $cUsername) setcookie("cFoto",$fotoUser);
			
			$tFoto = getField("select fotoUser from app_user where username='$cUsername'");
			if(file_exists($fFile.$tFoto) and $tFoto!="")unlink($fFile.$tFoto);			
		}
		
		return empty($fotoUser) ? getField("select fotoUser from app_user where username='$cUsername'") : $fotoUser;
	}
	
	function hapusPic(){
		global $s,$inp,$par,$fFile,$cUsername;
		if($cUsername == $cUsername) setcookie("cFoto","");
		
		$fotoUser = getField("select fotoUser from app_user where username='$cUsername'");
		if(file_exists($fFile.$fotoUser) and $fotoUser!="")unlink($fFile.$fotoUser);
		
		$sql="update app_user set fotoUser='' where username='$cUsername'";
		db($sql);
		
		echo "<script>window.location='?par[mode]=profile".getPar($par,"mode")."'</script>";
	}
	
	function ubahPas(){
	global $s,$inp,$par,$cUsername;		
	$sql="update app_user set password='".encodePass($inp[password])."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where username='$cUsername'";
	db($sql);
	echo "<script>closeBox();reloadPopup();</script>";
}
	
	function ubah(){
		global $s,$inp,$par,$cUsername;		
		repField();
		$fotoUser = fotoUser();
		$sql="update app_user set username='$inp[username]', namaUser='$inp[namaUser]', keteranganUser='$inp[keteranganUser]', fotoUser='$fotoUser', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where username='$cUsername'";
		db($sql);				
		
		setcookie("cNama","");
		setcookie("cFoto","");
		
		setcookie("cNama",$inp[namaUser]);
		setcookie("cFoto",$fotoUser);
		
		if($cUsername != $inp[username])
		echo "<script>alert(\"username has been changed, please login with your new username\");</script>";		
		echo "<script>window.parent.location.reload();</script>";		
	}
	
	function formPas(){
		global $s,$inp,$par,$menuAccess;

		echo "<script>
		function pas(){
			password=document.getElementById(\"inp[password]\");
			repassword=document.getElementById(\"inp[repassword]\");
			if(password.value && repassword.value && password.value != repassword.value){
				alert(\"password must be the same\");
				repassword.focus();
				return false;
			}
			
			if(validation(document.form)){
				document.getElementById(\"form\").submit();
			}
			return false;
		}
		</script>";
		
		setValidation("is_null","inp[password]","you must fill password");
		setValidation("is_null","inp[repassword]","you must fill re-type password");
		$text = getValidation();	
 
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Reset Password</h1>
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\">	
				<div id=\"general\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">Password</label>
						<div class=\"field\">
							<input type=\"password\" id=\"inp[password]\" name=\"inp[password]\" value=\"\" class=\"mediuminput\" style=\"width:200px;\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Re-type Password</label>
						<div class=\"field\">
							<input type=\"password\" id=\"inp[repassword]\" name=\"inp[repassword]\" value=\"\" class=\"mediuminput\" style=\"width:200px;\"/>
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return pas();\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function form(){
		global $s,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess,$sUser,$cUsername;
		
		echo "<script>
				function save(getPar){
				username=document.getElementById(\"inp[username]\").value;			
				
				var xmlHttp = getXMLHttp();		
				xmlHttp.onreadystatechange = function(){	
					if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
						if(xmlHttp.responseText){					
							alert(xmlHttp.responseText);										
						}else{
							if(validation(document.form)){
								document.getElementById(\"form\").submit();
							}
						}
					}
				}
				
				xmlHttp.open(\"GET\", \"ajax.php?par[mode]=cek&inp[username]=\" + username + getPar, true);
				xmlHttp.send(null);
				return false;
			}
			</script>";
		
		$sql="select * from app_user where username='$cUsername'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
				
		$false =  $r[statusUser] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[username]","you must fill username");		
		setValidation("is_null","inp[namaUser]","you must fill real name");		
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">User Profile</h1>					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Username</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[username]\" name=\"inp[username]\"  value=\"$r[username]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
							<input type=\"hidden\" id=\"inp[mode]\" name=\"inp[mode]\" value=\"$par[mode]\"/>
						</div>
					</p>
					<p>
					<label class=\"l-input-small\">Real Name</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaUser]\" name=\"inp[namaUser]\"  size=\"50\" value=\"$r[namaUser]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"50\"/>
					</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Level 3</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaGroup]\" name=\"inp[namaGroup]\"  size=\"50\" value=\"".getField("select namaGroup from app_group where kodeGroup='$r[kodeGroup]'")."\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"50\" disabled />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Note</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganUser]\" name=\"inp[keteranganUser]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganUser]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Photo</label>
						<div class=\"field\">";
							$text.=empty($r[fotoUser])?
								"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fotoUser\" name=\"fotoUser\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
								</div>":
								"<img src=\"".$fFile."".$r[fotoUser]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delPic".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusUser]\" value=\"t\" $true disabled /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusUser]\" value=\"f\" $false disabled /> <span class=\"sradio\">Not Active</span>
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return save('".getPar($par,"mode")."');\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>";
				$text.="<a href=\"#Reset\" style=\"float:right;\" class=\"btn btn1 btn_refresh\" onclick=\"openBox('popup.php?par[mode]=pas".getPar($par,"mode")."',650,250);\"><span>Reset Password</span></a>";
				$text.="</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess;					
		if(empty($arrTitle[$s])) $arrTitle[$s] = "Dashboard";
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				
			</div>";
		return $text;
	}	
	
	function getContent($par){
		global $s,$_submit,$menuAccess,$cUsername;
		switch($par[mode]){
			case "cek":
				$text = cek();
			break;
			case "pas":
				if($cUsername) $text = empty($_submit) ? formPas() : ubahPas(); else $text = lihat();
			break;
			case "delPic":				
				if($cUsername) $text = hapusPic(); else $text = lihat();
			break;
			case "profile":
				if($cUsername) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;		
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
