<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/user/";

function cek(){
	global $s,$db,$inp,$par;		
	if(getField("select username from app_user where username='$inp[username]' and username!='$par[username]'"))
		return "sorry, username \" $inp[username] \" already exist";
}		

function gPegawai(){
	global $s,$db,$inp,$par;
	$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$data["idPegawai"] = $r[id];
	$data["nikPegawai"] = $r[reg_no];
	$data["namaPegawai"] = strtoupper($r[name]);

	return json_encode($data);
}

function fotoUser(){
	global $s,$db,$inp,$par,$fFile,$cUsername;		
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
		if($par[username] == $cUsername) setcookie("cFoto",$fotoUser);

		$tFoto = getField("select fotoUser from app_user where username='$par[username]'");
		if(file_exists($fFile.$tFoto) and $tFoto!="")unlink($fFile.$tFoto);			
	}

	return empty($fotoUser) ? getField("select fotoUser from app_user where username='$par[username]'") : $fotoUser;
}

function hapusPic(){
	global $s,$db,$inp,$par,$fFile,$cUsername;
	if($par[username] == $cUsername) setcookie("cFoto","");

	$fotoUser = getField("select fotoUser from app_user where username='$par[username]'");
	if(file_exists($fFile.$fotoUser) and $fotoUser!="")unlink($fFile.$fotoUser);

	$sql="update app_user set fotoUser='' where username='$par[username]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
}

function hapus(){
	global $s,$db,$inp,$par,$fFile,$cUsername;
	$fotoUser = getField("select fotoUser from app_user where username='$par[username]'");
	if(file_exists($fFile.$fotoUser) and $fotoUser!="")unlink($fFile.$fotoUser);

	$sql="delete from app_user where username='$par[username]'";
	db($sql);
	echo "<script>window.location='?".getPar($par,"mode,username")."';</script>";
}

function ubahPas(){
	global $s,$db,$inp,$par,$cUsername;		
	$sql="update app_user set password='".encodePass($inp[password])."', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where username='$par[username]'";
	db($sql);
	echo "<script>closeBox();reloadPopup();</script>";
}

function ubah(){
	global $s,$db,$inp,$par,$cUsername,$det;		
	repField();
	$fotoUser = fotoUser();
	$sql="update app_user set username='$inp[username]', kodeGroup='$inp[kodeGroup]', idPegawai='$inp[idPegawai]', namaUser='$inp[namaUser]', keteranganUser='$inp[keteranganUser]', fotoUser='$fotoUser', statusUser='$inp[statusUser]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where username='$par[username]'";
	db($sql);				

	db("delete from app_user_area where username='$par[username]'");
	if (is_array($det)) {
		while (list($kodeArea) = each($det)) {     
			$sql = "insert into app_user_area (kodeArea, username, createBy, createTime) values ('$kodeArea', '$par[username]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
			db($sql);      
		}
	} 

	echo "<script>closeBox();reloadPage();</script>";
}

function tambah(){
	global $s,$db,$inp,$par,$cUsername,$det;				
	repField();
	$fotoUser = fotoUser();
	$sql="insert into app_user (username, kodeGroup, idPegawai, password, namaUser, keteranganUser, fotoUser, statusUser, createBy, createTime) values ('$inp[username]', '$inp[kodeGroup]', '$inp[idPegawai]', '".encodePass($inp[password])."', '$inp[namaUser]', '$inp[keteranganUser]', '$fotoUser', '$inp[statusUser]', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);

	if (is_array($det)) {
		while (list($kodeArea) = each($det)) {     
			$sql = "insert into app_user_area (kodeArea, username, createBy, createTime) values ('$kodeArea', '$inp[username]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
			db($sql);      
		}
	}

	echo "<script>closeBox();reloadPage();</script>";
}

function formPas(){
	global $s,$db,$inp,$par,$menuAccess;		
	setValidation("is_null","inp[password]","you must fill password");
	setValidation("is_null","inp[repassword]","you must fill re-type password");
	$text = getValidation();	

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Reset Password</h1>
		".getBread(ucwords("reset password"))."
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
	global $s,$db,$inp,$par,$fFile,$arrTitle,$arrParameter,$menuAccess,$cUsername,$sUser,$kodeModul,$sGroup;

	$sql="select * from app_user where username='$par[username]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					

	$false =  $r[statusUser] == "f" ? "checked=\"checked\"" : "";		
	$true =  empty($false) ? "checked=\"checked\"" : "";				

	setValidation("is_null","inp[username]","you must fill username");
	if($par[mode]=="add"){
		setValidation("is_null","inp[password]","you must fill password");
		setValidation("is_null","inp[repassword]","you must fill re-type password");
	}
	setValidation("is_null","inp[namaUser]","you must fill real name");		
	setValidation("is_null","inp[kodeGroup]","you must fill group");		
	$text = getValidation();

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<ul class=\"hornav\" style=\"margin: 0; margin-top: 10px;\">
				<li class=\"current\"><a href=\"#general\">General</a></li>
				<li><a href=\"#area_akses\">Area Akses</a></li>
			</ul>
			<div id=\"general\" class=\"subcontent\" style=\"display:block;\">					
				<p>
					<label class=\"l-input-small\">Username</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[username]\" name=\"inp[username]\"  value=\"$r[username]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
						<input type=\"hidden\" id=\"inp[mode]\" name=\"inp[mode]\" value=\"$par[mode]\"/>
					</div>
				</p>";
				if($par[mode] == "add")
					$text.="<p>
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
			</p>";

			if ($cUsername != $sUser) $filter= " and namaGroup != '$sGroup' ";
			$text.="<p>
			<label class=\"l-input-small\">NPP</label>
			<div class=\"field\">								
				<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
				<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"".getField("select reg_no from emp where id='".$r[idPegawai]."'")."\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."');\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"...\" onclick=\"openBox('popup.php?par[mode]=peg".getPar($par,"mode,filter")."',700,425);\" />
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
				".comboData("select * from app_group t1 join app_group_modul t2 on (t1.kodeGroup=t2.kodeGroup) where t1.statusGroup='t' and t2.kodeModul=".$kodeModul." $filter order by namaGroup","kodeGroup","namaGroup","inp[kodeGroup]"," ",$r[kodeGroup],"", "360px")."
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
					<input type=\"radio\" id=\"true\" name=\"inp[statusUser]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
					<input type=\"radio\" id=\"false\" name=\"inp[statusUser]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>
				</div>
			</p>
			<p>";
				if($par[mode] == "edit")
					$text.="<a href=\"#Reset\" style=\"float:right;\" class=\"btn btn1 btn_refresh\" onclick=\"openBox('popup.php?par[mode]=pas".getPar($par,"mode")."',650,250);\"><span>Reset Password</span></a>";
				$text .="
			</p>
			<br clear=\"all\">
		</div>
		<div id=\"area_akses\" class=\"subcontent\" style=\"display:none;\">
			<p>
				<label class=\"l-input-small\">Area Akses</label>
				<div class=\"field\" style=\"margin-left:175px;\">";

					$det = arrayQuery("select kodeArea, username from app_user_area where username='$par[username]'");
					$sql_="select * from mst_data where statusData='t' AND kodeCategory = 'S06' order by urutanData";	
					$res_=db($sql_);
					while($r_=mysql_fetch_array($res_)){
						$checked = isset($det["$r_[kodeData]"]) ? "checked=\"checked\"" : "";
						$text.="<input type=\"checkbox\" id=\"det[".$r_[kodeData]."]\" name=\"det[".$r_[kodeData]."]\" value=\"".$r_[kodeModul]."\" $checked /> ".$r_[namaData]."<br>";
					}
					$text.="</div>
				</p>
			</div>
			<p style=\"position: absolute; right: 20px; top: 10px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\" onclick=\"return save('".getPar($par,"mode")."');\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>";
				$text.="
			</p>
		</form>	
	</div>";
	return $text;
}

function lihat(){
	global $s,$db,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser,$kodeModul;						
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
			if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,username")."',825,550);\"><span>Add Data</span></a>";
			$text.="</div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"50\">Photo</th>
					<th>Real Name</th>
					<th width=\"150\">Username</th>
					<th width=\"250\">Level 3</th>
					<th width=\"125\">Last Login</th>
					<th width=\"50\">Status</th>";
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
					$text.="</tr>
				</thead>
				<tbody>";

		//if(!empty($par[filter]))
					$filter="where t3.kodeModul=".$kodeModul."";
					$filter.=" and (
					lower(t1.username) like '%".strtolower($par[filter])."%'
					or lower(t1.namaUser) like '%".strtolower($par[filter])."%'
					or lower(t2.namaGroup) like '%".strtolower($par[filter])."%'			
					)";
					if ($cUsername != $sUser) $filter.= " and t1.username != '$sUser' ";

					$sql="select * from app_user t1 join app_group t2 join app_group_modul t3 on (t1.kodeGroup=t2.kodeGroup and t2.kodeGroup=t3.kodeGroup) $filter order by t1.username";
					$res=db($sql);		
					while($r=mysql_fetch_array($res)){
						$no++;
						$statusUser = $r[statusUser] == "t"?
						"<img src=\"styles/images/t.png\" title='Active'>":
						"<img src=\"styles/images/f.png\" title='Not Active'>";

						if($r[loginUser] == "0000-00-00 00:00:00") $r[loginUser] = "";			
						list($tanggalLogin, $waktuLogin) = explode(" ",$r[loginUser]);
						$spacing = empty($r[loginUser]) ? "-" : "@";			

						$text.="<tr>
						<td>$no.</td>
						<td align=\"center\">";
							if(!empty($r[fotoUser])) {$text.="<img src=\"".$fFile."".$r[fotoUser]."\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\"> ";}
							$text.="</td><td>$r[namaUser]</td>					
							<td>$r[username]</td>
							<td>$r[namaGroup]</td>					
							<td align=\"center\">".getTanggal($tanggalLogin)." ".$spacing." ".substr($waktuLogin,0,5)."</td>
							<td align=\"center\">$statusUser</td>";
							if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
								$text.="<td align=\"center\">";				
								if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[username]=$r[username]".getPar($par,"mode,username")."',825,500);\"><span>Edit</span></a>";
								if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[username]','".getPar($par,"mode,username")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
								$text.="</td>";
							}
							$text.="</tr>";				
						}	

						$text.="</tbody>
					</table>
				</div>";
				return $text;
			}		

			function pegawai(){
				global $s,$db,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;		
				$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Daftar Pegawai</h1>
					".getBread()."

				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
					<form action=\"\" method=\"post\" class=\"stdform\">
						<div id=\"pos_l\" style=\"float:left;\">
							<table>
								<tr>
									<td>Search : </td>
									<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
									<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
									<td>
										<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\" />
										<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\" />
									</td>
								</tr>
							</table>
						</div>
					</form>
					<br clear=\"all\" />
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th style=\"min-width:100px;\">NPP</th>
								<th style=\"min-width:400px;\">Nama</th>					
								<th style=\"max-width:50px;\">Kontrol</th>
							</tr>
						</thead>
						<tbody>";

							$filter = "where reg_no is not null";

							if($par[search] == "Nama")
								$filter.= " and lower(name) like '%".strtolower($par[filter])."%'";
							else if($par[search] == "NPP")
								$filter.= " and lower(reg_no) like '%".strtolower($par[filter])."%'";
							else
								$filter.= " and (
							lower(name) like '%".strtolower($par[filter])."%'
							or lower(reg_no) like '%".strtolower($par[filter])."%'
							)";		

							$sql="select * from emp $filter order by name";
							$res=db($sql);
							while($r=mysql_fetch_array($res)){			
								$no++;

								$text.="<tr>
								<td>$no.</td>
								<td>$r[reg_no]</td>
								<td>".strtoupper($r[name])."</td>					
								<td align=\"center\">
									<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>
								</td>
							</tr>";
						}	

						$text.="</tbody>
					</table>
				</div>
			</div>";
			return $text;
		}

		function getContent($par){
			global $s,$db,$_submit,$menuAccess;
			switch($par[mode]){
				case "cek":
				$text = cek();
				break;
				case "get":
				$text = gPegawai();
				break;
				case "peg":
				$text = pegawai();
				break;
				case "pas":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formPas() : ubahPas(); else $text = lihat();
				break;
				case "delPic":				
				if(isset($menuAccess[$s]["edit"])) $text = hapusPic(); else $text = lihat();
				break;
				case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
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