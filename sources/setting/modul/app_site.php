<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "images/menu/";
	$arrAkses = array("View", "Add", "Edit", "Delete");
	
	function chk(){
		global $inp,$par;
		if(getField("select kodeMenu from app_menu where kodeSite='$par[kodeSite]'"))
		return "sorry, data has been use";
	}
	
	function hapusIcon(){
		global $s,$inp,$par,$fFile,$cUsername;
		$iconSite = getField("select iconSite from app_site where kodeSite='$par[kodeSite]'");
		if(file_exists($fFile.$iconSite) and $iconSite!="")unlink($fFile.$iconSite);
		
		$sql="update app_site set iconSite='' where kodeSite='$par[kodeSite]'";
		db($sql);

		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
	}
	
	function hapus(){
		global $s,$inp,$par,$fFile,$cUsername;
		$iconSite = getField("select iconSite from app_site where kodeSite='$par[kodeSite]'");
		if(file_exists($fFile.$iconSite) and $iconSite!="")unlink($fFile.$iconSite);
		
		$sql="delete from app_site where kodeSite='$par[kodeSite]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,kodeSite")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername;			
		
		$fileIcon = $_FILES["iconSite"]["tmp_name"];
		$fileIcon_name = $_FILES["iconSite"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$iconSite = "icon-".$par[kodeSite].".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $iconSite);			
		}
		if(empty($iconSite)) $iconSite = getField("select iconSite from app_site where kodeSite='$par[kodeSite]'");
		
		repField();
		
		$aksesSite = "";
		if(is_array($arrAkses)){
			while(list($kodeAkses)=each($arrAkses)){
				$aksesSite.= (isset($acc[$kodeAkses]) || empty($kodeAkses)) ? 1 : 0;
			}
		}					
		
		$sql="update app_site set namaSite='$inp[namaSite]', iconSite='$iconSite', urutanSite='".setAngka($inp[urutanSite])."', statusSite='$inp[statusSite]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeSite='$par[kodeSite]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername,$kodeModul;		
		$kodeSite=getField("select kodeSite from app_site order by kodeSite desc")+1;		
		
		$fileIcon = $_FILES["iconSite"]["tmp_name"];
		$fileIcon_name = $_FILES["iconSite"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$iconSite = "icon-".$kodeSite.".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $iconSite);			
		}
				
		repField();
		
		$aksesSite = "";
		if(is_array($arrAkses)){
			while(list($kodeAkses)=each($arrAkses)){
				$aksesSite.= (isset($acc[$kodeAkses]) || empty($kodeAkses)) ? 1 : 0;
			}
		}		
		
		$sql="insert into app_site (kodeSite, kodeModul, namaSite, iconSite, urutanSite, statusSite, createBy, createTime) values ('$kodeSite', $kodeModul, '$inp[namaSite]', '$iconSite', '".setAngka($inp[urutanSite])."', '$inp[statusSite]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess,$kodeModul;
		
		$sql="select * from app_site where kodeSite='$par[kodeSite]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);									
		
		if(empty($r[urutanSite])) $r[urutanSite] = getField("select urutanSite from app_site where kodeModul='$kodeModul' order by urutanSite desc limit 1") + 1;
				
		$false =  $r[statusSite] == "f" ? "checked=\"checked\"" : "";
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[namaSite]","you must fill modul");
		setValidation("is_null","inp[urutanSite]","you must fill order");
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
						<label class=\"l-input-small\">Modul</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaSite]\" name=\"inp[namaSite]\"  value=\"$r[namaSite]\" class=\"mediuminput\" maxlength=\"150\"/>
						</div>
					</p>										
					<p>
						<label class=\"l-input-small\">Icon</label>
						<div class=\"field\">";
							$text.=empty($r[iconSite])?
								"<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"iconSite\" name=\"iconSite\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
								</div>":
								"<img src=\"".$fFile."".$r[iconSite]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
								<a href=\"?par[mode]=delIco".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>									
					<p>
						<label class=\"l-input-small\">Order</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[urutanSite]\" name=\"inp[urutanSite]\"  value=\"".getAngka($r[urutanSite])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusSite]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusSite]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
							<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".getField("select count(*) from app_menu where kodeSite='$par[kodeSite]'")."\">
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

	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$kodeModul;
		
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
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>		
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeSite")."',825,450);\"><span>Add Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Modul</th>
					<th width=\"50\">Order</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"75\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
				
		$filter ="where kodeModul='".$kodeModul."'";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(namaSite) like '%".strtolower($par[filter])."%'				
		)";
	
		$sql="select * from app_site $filter order by urutanSite";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$statusSite = $r[statusSite] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaSite]</td>
					<td align=\"right\">".getAngka($r[urutanSite])."</td>					
					<td align=\"center\">$statusSite</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";									
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeSite]=$r[kodeSite]".getPar($par,"mode,kodeSite")."',825,450);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeSite]','".getPar($par,"mode,kodeSite")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
?>