<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/menu/";
$arrAkses = array("View", "Add", "Edit", "Delete");

$arrSubSettingMenu = array(
	array(
		"namaMenu" => "User Management", 
		"targetMenu" => "", 
		"aksesMenu" => "1000000", 
		"iconMenu" => "ico-65.png", 
		"urutanMenu" => "1",
		'group_menu' => array(),
		"childMenu" => array(
			array(
				"namaMenu" => "User List", 
				"targetMenu" => "setting/modul/app_user", 
				"aksesMenu" => "1111000", 
				"iconMenu" => "ico-66.png", 
				"urutanMenu" => "1",
				'group_menu' => array("add", "view", "edit", "delete"),
				),
			array(
				"namaMenu" => "User Akses", 
				"targetMenu" => "setting/modul/app_akses", 
				"aksesMenu" => "1010000", 
				"iconMenu" => "ico-67.png", 
				"urutanMenu" => "2",
				'group_menu' => array("view", "edit"),
				),
			array(
				"namaMenu" => "Log Access", 
				"targetMenu" => "setting/app_log", 
				"aksesMenu" => "1000000", 
				"iconMenu" => "ico-5.png", 
				"urutanMenu" => "3",
				'group_menu' => array("view"),
				)
			)
		),
	array(
		"namaMenu" => "Master Data", 
		"targetMenu" => "setting/modul/app_master", 
		"aksesMenu" => "1111000", 
		"iconMenu" => "ico-68.png", 
		"urutanMenu" => "2",
		'group_menu' => array("add", "view", "edit", "delete"),
		"childMenu" => array()
		),
	array(
		"namaMenu" => "Spesific Data", 
		"targetMenu" => "", 
		"aksesMenu" => "1000000", 
		"iconMenu" => "ico-69.png", 
		"urutanMenu" => "3",
		'group_menu' => array(),
		"childMenu" => array(
			array(
				"namaMenu" => "Sub Modul", 
				"targetMenu" => "setting/modul/app_site", 
				"aksesMenu" => "1111000", 
				"iconMenu" => "ico-70.png", 
				"urutanMenu" => "1",
				'group_menu' => array("add", "view", "edit", "delete"),
				),
			array(
				"namaMenu" => "Menu", 
				"targetMenu" => "setting/modul/app_menu", 
				"aksesMenu" => "1111000", 
				"iconMenu" => "ico-71.png", 
				"urutanMenu" => "2",
				'group_menu' => array("add", "view", "edit", "delete"),
				),
			)
		)
	);
// var_dump($arrSubSettingMenu);
function chk(){
	global $inp,$par;
	if(getField("select kodeGroup from app_group_modul where kodeModul='$par[kodeModul]'"))
		return "sorry, data has been use";
}

function hapusIcon(){
	global $s,$inp,$par,$fFile,$cUsername;
	$iconModul = getField("select iconModul from app_modul where kodeModul='$par[kodeModul]'");
	if(file_exists($fFile.$iconModul) and $iconModul!="")unlink($fFile.$iconModul);

	$sql="update app_modul set iconModul='' where kodeModul='$par[kodeModul]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
}

function hapus(){
	global $s,$inp,$par,$fFile,$cUsername;
	$iconModul = getField("select iconModul from app_modul where kodeModul='$par[kodeModul]'");
	if(file_exists($fFile.$iconModul) and $iconModul!="")unlink($fFile.$iconModul);

	$sql="delete from app_modul where kodeModul='$par[kodeModul]'";
	db($sql);

	$sql="delete from app_site where kodeModul='$par[kodeModul]'";
	db($sql);

	$sql="delete from app_menu where kodeModul='$par[kodeModul]'";
	db($sql);
	
	echo "<script>window.location='?".getPar($par,"mode,kodeModul")."';</script>";
}

function ubah(){
	global $s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername;			

	$fileIcon = $_FILES["iconModul"]["tmp_name"];
	$fileIcon_name = $_FILES["iconModul"]["name"];
	if(($fileIcon!="") and ($fileIcon!="none")){						
		fileUpload($fileIcon,$fileIcon_name,$fFile);			
		$iconModul = "img-".$par[kodeModul].".".getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $iconModul);			
	}
	if(empty($iconModul)) $iconModul = getField("select iconModul from app_modul where kodeModul='$par[kodeModul]'");

	repField();

	$aksekodeSite = "";
	if(is_array($arrAkses)){
		while(list($kodeAkses)=each($arrAkses)){
			$aksekodeSite.= (isset($acc[$kodeAkses]) || empty($kodeAkses)) ? 1 : 0;
		}
	}					

	if ($inp[statusLink] == 's') {
		$sql="update app_modul set namaModul='$inp[namaModul]', kategoriModul='$inp[kategoriModul]',statusLink = '$inp[statusLink]',modul_link = '', folderModul='$inp[folderModul]', iconModul='$iconModul', urutanModul='".setAngka($inp[urutanModul])."', statusModul='$inp[statusModul]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeModul='$par[kodeModul]'";

	}else{
		$sql="update app_modul set namaModul='$inp[namaModul]', kategoriModul='$inp[kategoriModul]', statusLink = '$inp[statusLink]',modul_link = '$inp[modul_link]', folderModul='$inp[folderModul]', iconModul='$iconModul', urutanModul='".setAngka($inp[urutanModul])."', statusModul='$inp[statusModul]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeModul='$par[kodeModul]'";
	}
	db($sql);
	echo "<script>closeBox();reloadPage();</script>";
}

function tambah(){
	global $s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername, $arrSubSettingMenu;		
	$kodeModul=getField("select kodeModul from app_modul order by kodeModul desc")+1;		

	$fileIcon = $_FILES["iconModul"]["tmp_name"];
	$fileIcon_name = $_FILES["iconModul"]["name"];
	if(($fileIcon!="") and ($fileIcon!="none")){						
		fileUpload($fileIcon,$fileIcon_name,$fFile);			
		$iconModul = "img-".$kodeModul.".".getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $iconModul);			
	}

	repField();

	$aksekodeSite = "";
	if(is_array($arrAkses)){
		while(list($kodeAkses)=each($arrAkses)){
			$aksekodeSite.= (isset($acc[$kodeAkses]) || empty($kodeAkses)) ? 1 : 0;
		}
	}		

	if ($inp[statusLink] == 's') {
		$inp[urutanModul] = getField("select urutanModul from app_modul where kategoriModul = '$inp[kategoriModul]' order by urutanModul desc limit 1")+1;
		$sql="insert into app_modul (kodeModul,kategoriModul, namaModul, folderModul, iconModul,statusLink,modul_link,urutanModul, statusModul, createBy, createTime) values ('$kodeModul', '$inp[kategoriModul]', '$inp[namaModul]', '$inp[folderModul]', '$iconModul', 's','','".setAngka($inp[urutanModul])."', '$inp[statusModul]', '$cUsername', '".date('Y-m-d H:i:s')."')";
	}else{
		$inp[urutanModul] = getField("select urutanModul from app_modul where kategoriModul = '$inp[kategoriModul]' order by urutanModul desc limit 1")+1;
		$sql="insert into app_modul (kodeModul,kategoriModul, namaModul, folderModul, iconModul,statusLink,modul_link,urutanModul, statusModul, createBy, createTime) values ('$kodeModul', '$inp[kategoriModul]', '$inp[namaModul]', '$inp[folderModul]', '$iconModul', 'p','$inp[modul_link]','".setAngka($inp[urutanModul])."', '$inp[statusModul]', '$cUsername', '".date('Y-m-d H:i:s')."')";
	}
	db($sql);

	$kodeSite=getField("select kodeSite from app_site order by kodeSite desc")+1;	
	$sql = "INSERT INTO app_site (kodeSite, kodeModul, namaSite, iconSite, urutanSite, statusSite, createBy, createTime) VALUES ('$kodeSite', '$kodeModul', 'Setting', 'icon-2.png', '1', 't', '$cUsername', '".date("Y-m-d H:i:s")."')";
	db($sql);

	foreach($arrSubSettingMenu as $objSubSettingMenu){
		$kodeMenu = getField("SELECT kodeMenu FROM app_menu ORDER BY kodeMenu DESC LIMIT 1")+1;
		$sql = "INSERT INTO app_menu (kodeMenu,kategoriModul, kodeModul, kodeSite, kodeInduk, namaMenu, targetMenu, parameterMenu, aksesMenu, iconMenu, urutanMenu, statusMenu, levelMenu, createBy, createTime) VALUES ('$kodeMenu', '$objSubSettingMenu[kategoriModul]','$kodeModul', '$kodeSite', '0', '$objSubSettingMenu[namaMenu]', '$objSubSettingMenu[targetMenu]', '', '$objSubSettingMenu[aksesMenu]', '$objSubSettingMenu[iconMenu]', '$objSubSettingMenu[urutanMenu]', 't', '1', '$cUsername', '".date("Y-m-d H:i:s")."')";
		// echo $sql."<br><br>";
		db($sql);

		foreach($objSubSettingMenu[group_menu] as $objGroupMenu){
			$sql = "INSERT INTO app_group_menu (kodeGroup, kodeMenu, statusGroup, createBy, createTime) VALUES ('1', '$kodeMenu', '$objGroupMenu', '$cUsername', '".date("Y-m-d H:i:s")."')";
			// echo $sql."<br><br>";
			db($sql);
		}

		foreach($objSubSettingMenu[childMenu] as $objChildMenu){
			$kodeMenu2 = getField("SELECT kodeMenu FROM app_menu ORDER BY kodeMenu DESC LIMIT 1")+1;
			$sql = "INSERT INTO app_menu (kodeMenu, kategoriModul,kodeModul, kodeSite, kodeInduk, namaMenu, targetMenu, parameterMenu, aksesMenu, iconMenu, urutanMenu, statusMenu, levelMenu, createBy, createTime) VALUES ('$kodeMenu2','$objChildMenu[kategoriModul]', '$kodeModul', '$kodeSite', '$kodeMenu', '$objChildMenu[namaMenu]', '$objChildMenu[targetMenu]', '', '$objChildMenu[aksesMenu]', '$objChildMenu[iconMenu]', '$objChildMenu[urutanMenu]', 't', '2', '$cUsername', '".date("Y-m-d H:i:s")."')";
			// echo $sql."<br><br>";
			db($sql);

			foreach($objChildMenu[group_menu] as $objGroupMenu){
				$sql = "INSERT INTO app_group_menu (kodeGroup, kodeMenu, statusGroup, createBy, createTime) VALUES ('1', '$kodeMenu2', '$objGroupMenu', '$cUsername', '".date("Y-m-d H:i:s")."')";
				// echo $sql."<br><br>";
				db($sql);
			}
		}
	}

	echo "<script>closeBox();reloadPage();</script>";
}

function form(){
	global $s,$inp,$par,$fFile,$arrModul,$arrAkses,$arrTitle,$menuAccess;

	$sql="select * from app_modul where kodeModul='$par[kodeModul]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);									

	if(empty($r[urutanModul])) $r[urutanModul] = getField("select urutanModul from app_modul order by urutanModul desc limit 1") + 1;

	$false =  $r[statusModul] == "f" ? "checked=\"checked\"" : "";
	$true =  empty($false) ? "checked=\"checked\"" : "";
	
	$false1 =  $r[statusLink] == "p" ? "checked=\"checked\"" : "";
	$true1 =  empty($false1) ? "checked=\"checked\"" : "";

	$styleVendor = $r[statusLink] == "p" ? "style=\"display:block;\"" : "style=\"display:none;\"";

	setValidation("is_null","inp[namaModul]","you must fill modul");
	setValidation("is_null","inp[folderModul]","you must fill folder");
	setValidation("is_null","inp[urutanModul]","you must fill order");
	$text = getValidation();	

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">										
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						".comboData("select * from mst_data where statusData='t' and kodeCategory='BE' order by urutanData","kodeData", "namaData","inp[kategoriModul]","",$r[kategoriModul],"", "250px","")."
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Modul</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaModul]\" name=\"inp[namaModul]\"  value=\"$r[namaModul]\" class=\"mediuminput\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Folder</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[folderModul]\" name=\"inp[folderModul]\"  value=\"$r[folderModul]\" class=\"mediuminput\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Icon</label>
					<div class=\"field\">";
						$text.=empty($r[iconModul])?
						"<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
						<div class=\"fakeupload\">
							<input type=\"file\" id=\"iconModul\" name=\"iconModul\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
						</div>":
						"<img src=\"".$fFile."".$r[iconModul]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delIco".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="</div>
					</p>									
					<p>
						<label class=\"l-input-small\">Order</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[urutanModul]\" name=\"inp[urutanModul]\"  value=\"".getAngka($r[urutanModul])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusModul]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusModul]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
							<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".getField("select count(*) from app_group_modul where kodeModul='$par[kodeModul]'")."\">
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" >Tipe</label>
						<div class=\"field\">           
							<input type=\"radio\" id=\"true1\" name=\"inp[statusLink]\" value=\"s\" $true1 onclick=\"document.getElementById('fieldVendor').style.display = 'none';\" /> <span class=\"sradio\">Modul</span>		
							<input type=\"radio\" id=\"false1\" name=\"inp[statusLink]\" value=\"p\" $false1 onclick=\"document.getElementById('fieldVendor').style.display = 'block';\" /> <span class=\"sradio\">Link</span>							

						</div>          
					</p>	

					<div id=\"fieldVendor\" $styleVendor>
						<p>
							<label class=\"l-input-small\">URL</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[modul_link]\" name=\"inp[modul_link]\"  value=\"$r[modul_link]\" class=\"mediuminput\" maxlength=\"350\"/>
							</div>
						</p>	
					</div>
				</div>
				<p style=\"position:absolute;top:10px;right:5px;\">
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					</p>
			</form>	
		</div>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$fFile;
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p>
					<span>Search : </span>
					<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" style=\"width:230px;\" />";
						$text.=" ".comboData("select * from mst_data where statusData='t' and kodeCategory='BE' order by urutanData","kodeData","namaData","par[kategoriModul]","All",$par[kategoriModul],"", "250px");
					$text.="".setPar($par, "filter, kategoriModul")."
					<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
				</p>
			</div>		
			<div id=\"pos_r\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeModul")."',825,450);\"><span>Add Data</span></a>";
				$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th style =\"vertical-align:middle\" width=\"20\">Icon</th>
						<th>Modul</th>
						<th width=\"50\">Order</th>
						<th width=\"50\">Status</th>";
						if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"75\">Control</th>";
						$text.="</tr>
					</thead>
					<tbody>";

						$filter ="where kodeModul is not null";
												if(!empty($par[kategoriModul])) $filter.=" and kategoriModul='$par[kategoriModul]'";

if(!empty($par[filter]))			
							$filter.=" and (
						lower(namaModul) like '%".strtolower($par[filter])."%'				
						)";

						$sql="select * from app_modul $filter order by urutanModul";
						$res=db($sql);
						while($r=mysql_fetch_array($res)){			
							$no++;
							$statusModul = $r[statusModul] == "t"?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";			
							$text.="<tr>
							<td>$no.</td>
							<td align=\"center\"><img src=\"".$fFile."".$r[iconModul]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\"></td>
							<td>$r[namaModul]</td>
							<td align=\"right\">".getAngka($r[urutanModul])."</td>					
							<td align=\"center\">$statusModul</td>";
							if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
								$text.="<td align=\"center\">";									
								if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeModul]=$r[kodeModul]".getPar($par,"mode,kodeModul")."',825,450);\"><span>Edit</span></a>";
								if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeModul]','".getPar($par,"mode,kodeModul")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
				global $s,$_submit,$menuAccess;
				switch($par[mode]){
					case "chk":
					$text = chk();
					break;
					case "delIco":
					if(isset($menuAccess[$s]["edit"])) $text = hapusIcon(); else $text = lihat();
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
