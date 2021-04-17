<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "images/menu/";

function order(){
	global $inp,$par;		
	$result = getField("select urutanData from mst_data where kodeInduk='$inp[kodeInduk]' and kodeCategory='$par[kodeCategory]' order by urutanData desc limit 1") + 1;
	return $result;
}

function cek(){
	global $inp,$par;		
	if(getField("select kodeCategory from mst_category where kodeCategory='$inp[kodeCategory]' and kodeCategory!='$par[kodeCategory]'"))
		return "sorry, code \" $inp[kodeCategory] \" already exist";
}

function chk(){
	global $inp,$par;
	if(getField("select kodeCategory from mst_category where kodeInduk='$par[kodeCategory]'") || getField("select kodeCategory from mst_data where kodeCategory='$par[kodeCategory]'"))
		return "sorry, data has been use";
}

function chkDet(){
	global $inp,$par;
	if(getField("select kodeData from mst_data where kodeInduk='$par[kodeData]'"))
		return "sorry, data has been use";
}

function chkNama(){
	global $inp,$par;
	if(getField("select namaData from mst_data where kodeInduk='$par[kodeData]'"))
		return "sorry, name has been use";
}

function hapusDet(){
	global $s,$inp,$par,$arrParameter,$cUsername;

	$sql="delete from mst_data where kodeData='$par[kodeData]'";
	db($sql);
	echo "<script>window.location='?par[mode]=det".getPar($par,"mode,kodeData")."';</script>";
}

function ubahDet(){
	global $s,$inp,$par,$arrParameter,$cUsername;		
	repField();	

	$sql="update mst_data set kodeInduk='$inp[kodeInduk]', namaData='$inp[namaData]', keteranganData='$inp[keteranganData]', urutanData='".setAngka($inp[urutanData])."', kodeMaster = '$inp[kodeMaster]', statusData='$inp[statusData]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeData='$par[kodeData]'";
	db($sql);
	echo "<script>closeBox();reloadPage();</script>";
}

function tambahDet(){
	global $s,$inp,$par,$arrParameter,$cUsername;		
	$kodeData=getField("select kodeData from mst_data order by kodeData desc limit 1")+1;						

	repField();
	$sql="insert into mst_data (kodeData, kodeInduk, kodeMenu, kodeReport, kodeCategory, namaData, keteranganData, urutanData, kodeMaster, statusData, createBy, createTime) values ('$kodeData', '$inp[kodeInduk]', '$kodeMenu', '$kodeReport', '$par[kodeCategory]', '$inp[namaData]', '$inp[keteranganData]', '".setAngka($inp[urutanData])."', '$inp[kodeMaster]', '$inp[statusData]', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);
	echo "<script>closeBox();reloadPage();</script>";
}

function hapusIcon(){
	global $s,$inp,$par,$fFile,$cUsername;
	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");
	$iconMenu = getField("select iconMenu from app_menu where kodeMenu='$kodeMenu'");
	if(file_exists($fFile.$iconMenu) and $iconMenu!="")unlink($fFile.$iconMenu);

	$sql="update app_menu set iconMenu='' where kodeMenu='$kodeMenu'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
}

function hapus(){
	global $s,$inp,$par,$fFile,$cUsername;
	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");
	$iconMenu = getField("select iconMenu from app_menu where kodeMenu='$kodeMenu'");
	if(file_exists($fFile.$iconMenu) and $iconMenu!="")unlink($fFile.$iconMenu);

	$sql="delete from app_menu where kodeMenu='$kodeMenu'";
	db($sql);

	$sql="delete from mst_category where kodeCategory='$par[kodeCategory]'";
	db($sql);
	echo "<script>window.location='?par[mode]=view".getPar($par,"mode,kodeCategory")."';</script>";
}

function ubah(){
	global $s,$inp,$par,$fFile,$cUsername, $c;
	// echo $c;
	// die();					
	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");		
	$fileIcon = $_FILES["iconMenu"]["tmp_name"];
	$fileIcon_name = $_FILES["iconMenu"]["name"];
	if(($fileIcon!="") and ($fileIcon!="none")){						
		fileUpload($fileIcon,$fileIcon_name,$fFile);			
		$iconMenu = "ico-".$kodeMenu.".".getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $iconMenu);			
	}
	if(empty($iconMenu)) $iconMenu = getField("select iconMenu from app_menu where kodeMenu='$kodeMenu'");

	if($c == 18){
		$par[kodeModul] = $inp[kodeModul];
		$par[kodeSite] = getField("select kodeSite from app_site where kodeModul = '$par[kodeModul]' AND namaSite = 'Setting'");
		$r[kodeMenu] = getField("select kodeMenu from app_menu where kodeModul = '$par[kodeModul]' AND namaMenu = 'Master Data'");
		$sql="update app_menu set kodeModul = '$par[kodeModul]',kodeSite = '$par[kodeSite]',kodeInduk = '$r[kodeMenu]', namaMenu='$inp[namaCategory]', iconMenu='$iconMenu', urutanMenu='".setAngka($inp[urutanCategory])."', statusMenu='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeMenu='$kodeMenu'";
		db($sql);
		// echo $sql;

		$sql="update mst_category set kodeModul = '$par[kodeModul]', kodeCategory='$inp[kodeCategory]', kodeInduk='$inp[kodeInduk]', namaCategory='$inp[namaCategory]', keteranganCategory='$inp[keteranganCategory]', urutanCategory='".setAngka($inp[urutanCategory])."', statusCategory='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeCategory='$par[kodeCategory]'";
		db($sql);
		// echo $sql;	
	}else{
		$sql="update app_menu set namaMenu='$inp[namaCategory]', iconMenu='$iconMenu', urutanMenu='".setAngka($inp[urutanCategory])."', statusMenu='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeMenu='$kodeMenu'";
		db($sql);

		$sql="update mst_category set kodeCategory='$inp[kodeCategory]', kodeInduk='$inp[kodeInduk]', namaCategory='$inp[namaCategory]', keteranganCategory='$inp[keteranganCategory]', urutanCategory='".setAngka($inp[urutanCategory])."', statusCategory='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeCategory='$par[kodeCategory]'";
		db($sql);	
	}
	// die();
	repField();		
	// $sql="update app_menu set namaMenu='$inp[namaCategory]', iconMenu='$iconMenu', urutanMenu='".setAngka($inp[urutanCategory])."', statusMenu='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeMenu='$kodeMenu'";
	// db($sql);

	// $sql="update mst_category set kodeCategory='$inp[kodeCategory]', kodeInduk='$inp[kodeInduk]', namaCategory='$inp[namaCategory]', keteranganCategory='$inp[keteranganCategory]', urutanCategory='".setAngka($inp[urutanCategory])."', statusCategory='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeCategory='$par[kodeCategory]'";
	// db($sql);				

	$sql="update mst_data set kodeCategory='$inp[kodeCategory]' where kodeCategory='$par[kodeCategory]'";
	db($sql);

	$sql="update mst_category set kodeInduk='$inp[kodeCategory]' where kodeInduk='$par[kodeCategory]'";
	db($sql);

	$sql="update app_parameter set nilaiParameter='$inp[kodeCategory]' where nilaiParameter='$par[kodeCategory]'";
	db($sql);

	echo "<script>closeBox();reloadPage();</script>";
}

function tambah(){
	global $c,$s,$inp,$par,$fFile,$cUsername;					
	$kodeMenu=getField("select kodeMenu from app_menu order by kodeMenu desc")+1;		
	$sql="select * from app_menu where kodeMenu='$s'";
	$res=db($sql);
	$r=mysql_fetch_array($res);		
	$levelMenu=$r[levelMenu] + 1;		

	$fileIcon = $_FILES["iconMenu"]["tmp_name"];
	$fileIcon_name = $_FILES["iconMenu"]["name"];
	if(($fileIcon!="") and ($fileIcon!="none")){						
		fileUpload($fileIcon,$fileIcon_name,$fFile);
		$iconMenu = "ico-".$kodeMenu.".".getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $iconMenu);			
	}

	repField();	

	echo $sql="insert into app_menu (kodeMenu, kodeModul, kodeSite, kodeInduk, namaMenu, targetMenu, aksesMenu, iconMenu, urutanMenu, statusMenu, levelMenu, createBy, createTime) values ('$kodeMenu', '$par[kodeModul]', '$par[kodeSite]', '$r[kodeMenu]', '$inp[namaCategory]', '$r[targetMenu]', '$r[aksesMenu]', '$iconMenu', '".setAngka($inp[urutanCategory])."', '$inp[statusCategory]', '$levelMenu', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);

	$sql="insert into mst_category (kodeCategory, kodeModul, kodeInduk, kodeMenu, namaCategory, keteranganCategory, urutanCategory, statusCategory, createBy, createTime) values ('$inp[kodeCategory]', '$par[kodeModul]', '$inp[kodeInduk]', '$kodeMenu', '$inp[namaCategory]', '$inp[keteranganCategory]', '".setAngka($inp[urutanCategory])."', '$inp[statusCategory]', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);
	echo "<script>closeBox();reloadPage();</script>";
}

function formDet(){
	global $s,$inp,$par,$fFile,$arrTitle,$menuAccess;
	$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='$par[kodeCategory]'");
	$namaCategory = getField("select namaCategory from mst_category where kodeCategory='$kodeCategory'");

	$sql="select * from mst_data where kodeData='$par[kodeData]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);				

	if(empty($r[urutanData])) $r[urutanData] = getField("select urutanData from mst_data where kodeInduk='$par[kodeInduk]' and kodeCategory='$par[kodeCategory]' order by urutanData desc limit 1") + 1;
	if(empty($r[kodeInduk])) $r[kodeInduk] = $par[kodeInduk];

	$false =  $r[statusData] == "f" ? "checked=\"checked\"" : "";		
	$true =  empty($false) ? "checked=\"checked\"" : "";

	if(!empty($kodeCategory))
		setValidation("is_null","inp[kodeInduk]","you must fill ".strtolower($namaCategory));		
	setValidation("is_null","inp[namaData]","you must fill name");
	setValidation("is_null","inp[urutanData]","you must fill order");
	$text = getValidation();

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords(str_replace("Det","",$par[mode])." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div style=\"top:13px; right:35px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
			</div>
			<div id=\"general\" class=\"subcontent\">";
				if(!empty($kodeCategory))
					$text.="<p>
				<label class=\"l-input-small\">".$namaCategory."</label>
				<div class=\"field\">
					".comboData("select * from mst_data where kodeCategory='$kodeCategory' and statusData='t' order by urutanData","kodeData","namaData","inp[kodeInduk]"," ",$r[kodeInduk],"onchange=\"order('".getPar($par, "mode")."');\"", "360px")."
				</div>
			</p>";			
			$text.="<p>
			<label class=\"l-input-small\">Nama</label>
			<div class=\"field\">
				<input type=\"text\" id=\"inp[namaData]\" name=\"inp[namaData]\"  value=\"$r[namaData]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
			</div>
		</p>					
		<p>
			<label class=\"l-input-small\">Deskripsi</label>
			<div class=\"field\">
				<textarea id=\"inp[keteranganData]\" name=\"inp[keteranganData]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganData]</textarea>
			</div>
		</p>					
		<table width=\"100%\">
			<tr>
				<td width=\"50%\">
					<p>
						<label class=\"l-input-small2\">Urutan</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[urutanData]\" name=\"inp[urutanData]\"  value=\"".getAngka($r[urutanData])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
				</td>
				<td width=\"50%\">
					<p>
						<label class=\"l-input-small\">Kode</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[kodeMaster]\" name=\"inp[kodeMaster]\" value=\"$r[kodeMaster]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"10\" />
						</div>
					</p>
				</td>
			</tr>
		</table>
		<p>
			<label class=\"l-input-small\">Status</label>
			<div class=\"fradio\">
				<input type=\"radio\" id=\"true\" name=\"inp[statusData]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
				<input type=\"radio\" id=\"false\" name=\"inp[statusData]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
				<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".getField("select count(*) from mst_data where kodeInduk='$par[kodeData]'")."\">
			</div>
		</p>
		
	</div>
</form>	
</div>";
return $text;
}

function form(){
	global $c, $s,$inp,$par,$fFile,$arrTitle,$menuAccess;		
	// print_r($par);
	$sql="select t1.*, t2.iconMenu from mst_category t1 join app_menu t2 on (t1.kodeMenu=t2.kodeMenu) where t1.kodeCategory='$par[kodeCategory]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					

	if(empty($r[urutanCategory])) $r[urutanCategory] = getField("select urutanCategory from mst_category where kodeModul='".$par[kodeModul]."' order by urutanCategory desc limit 1") + 1;

	$false =  $r[statusCategory] == "f" ? "checked=\"checked\"" : "";		
	$true =  empty($false) ? "checked=\"checked\"" : "";

	setValidation("is_null","inp[kodeCategory]","you must fill code");
	setValidation("is_null","inp[namaCategory]","you must fill category");
	setValidation("is_null","inp[urutanCategory]","you must fill order");
	$text = getValidation();

	$text.="<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);chkNama();\" enctype=\"multipart/form-data\">	
			<div style=\"top:13px; right:35px; position:absolute\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
			</div>
			<div id=\"general\" class=\"subcontent\">
				<p>
					<label class=\"l-input-small\">Code</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[kodeCategory]\" name=\"inp[kodeCategory]\"  value=\"$r[kodeCategory]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"10\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Parent</label>
					<div class=\"field\">
						".comboData("select * from mst_category where kodeModul='".$par[kodeModul]."' and kodeCategory!='$par[kodeCategory]' and statusCategory='t' order by urutanCategory","kodeCategory","namaCategory","inp[kodeInduk]"," ",$r[kodeInduk],"", "360px")."
					</div>
				</p>
				";
				if($c == 18){
					$text.="
				<p>
					<label class=\"l-input-small\">Modul</label>
					<div class=\"field\">
						".comboData("select * from app_modul where statusModul='t' order by urutanModul","kodeModul","namaModul","inp[kodeModul]"," ",$r[kodeModul],"", "360px")."
					</div>
				</p>";
				}$text.="
				<p>
					<label class=\"l-input-small\">Category</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaCategory]\" name=\"inp[namaCategory]\"  value=\"$r[namaCategory]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>
				</p>					
				<p>
					<label class=\"l-input-small\">Description</label>
					<div class=\"field\">
						<textarea id=\"inp[keteranganCategory]\" name=\"inp[keteranganCategory]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganCategory]</textarea>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Icon</label>
					<div class=\"field\">";
						$text.=empty($r[iconMenu])?
						"<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
						<div class=\"fakeupload\">
							<input type=\"file\" id=\"iconMenu\" name=\"iconMenu\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
						</div>":
						"<img src=\"".$fFile."".$r[iconMenu]."\" align=\"left\" height=\"25\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delIco".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Order</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[urutanCategory]\" name=\"inp[urutanCategory]\"  value=\"".getAngka($r[urutanCategory])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusCategory]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusCategory]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
							<input type=\"hidden\" id=\"count\" name=\"count\" value=\"".(getField("select count(*) from mst_category where kodeInduk='$par[kodeCategory]'") + getField("select count(*) from mst_data where kodeCategory='$par[kodeCategory]'"))."\">
						</div>
					</p>

				</div>
			</form>	
		</div>";
		return $text;
	}
	
	function detail(){
		global $p,$m,$s,$inp,$par,$arrTitle,$menuAccess;
		$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='$par[kodeCategory]'");
		$namaKategori = getField("select namaCategory from mst_category where kodeCategory='$par[kodeCategory]'");
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]." - ".$namaKategori."</h1>
		".getBread()."

	</div>    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p>
					<span>Search : </span>
					<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" style=\"width:230px;\" />";
					if(!empty($kodeCategory))
						$text.=" ".comboData("select * from mst_data where kodeCategory='$kodeCategory' and statusData='t' order by urutanData","kodeData","namaData","par[kodeInduk]","All",$par[kodeInduk],"", "250px");
					$text.="".setPar($par, "filter, kodeInduk")."
					<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
				</p>
			</div>
			<div id=\"pos_r\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=addDet".getPar($par,"mode,kodeData")."',825,375);\"><span>Add Data</span></a>";
				$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>					
						<th>Name</th>
						<th width=\"50\">Order</th>
						<th width=\"50\">Status</th>";
						if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
						$text.="</tr>
					</thead>
					<tbody>";

						$filter ="where kodeCategory='$par[kodeCategory]'";
						if(!empty($par[kodeInduk])) $filter.=" and kodeInduk='$par[kodeInduk]'";
						if(!empty($par[filter]))			
							$filter.=" and (
						lower(namaData) like '%".strtolower($par[filter])."%'
						)";

						$sql="select * from mst_data $filter order by kodeInduk, urutanData";
						// echo $sql;
						$res=db($sql);
						while($r=mysql_fetch_array($res)){			
							$no++;
							$statusData = $r[statusData] == "t"?
							"<img src=\"styles/images/t.png\" title='Active'>":
							"<img src=\"styles/images/f.png\" title='Not Active'>";			
							$text.="<tr>
							<td>$no.</td>
							<td>$r[namaData]</td>
							<td align=\"right\">".getAngka($r[urutanData])."</td>					
							<td align=\"center\">$statusData</td>";
							if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
								$text.="<td align=\"center\">";				
								if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editDet&par[kodeData]=$r[kodeData]".getPar($par,"mode,kodeData")."',825,375);\"><span>Edit</span></a>";
								if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"delDet('$r[kodeData]','".getPar($par,"mode,kodeData")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
								$text.="</td>";
							}
							$text.="</tr>";
						}

						$text.="</tbody>
					</table>
				</div>";
				return $text;
			}

			function lihat(){
				global $c,$p,$m,$s,$inp,$par,$arrTitle,$menuAccess;		
				// $par[kodeModul] =$c;
				// print_r($par);
				// echo $par[mode];
				$par[kodeSite] =$p;
				$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>			
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">			
				<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
					<div style=\"position:absolute; right:10px; top:10px;\">";


						$text.="<a href=\"?".getPar($par,"mode")."\"><img src=\"styles/images/icons.png\" style=\"float:left; margin-right:5px; margin-top:1px;\" title=\"Thumnail\"></a>";
						if(isset($menuAccess[$s]["add"]) && !empty($par[kodeModul])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeCategory")."',825,500);\" style=\"margin-top:0px;\"><span>New Master</span></a>";
						$text.="</div>
						<div id=\"pos_l\" style=\"float:left;\">
							<p>
								<span>Search : </span>
								<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:200px;\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
								<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" style=\"width:200px;\" size=\"50\" value=\"$par[mode]\" class=\"mediuminput\" />
								".comboData("select * from app_modul order by urutanModul","kodeModul","namaModul","par[kodeModul]","-- MODUL --",$par[kodeModul],"onchange=\"document.getElementById('form').submit();\"","210px;","chosen-select")."
								<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
							</p>
						</div>			
					</form>
					<br clear=\"all\" />
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th width=\"100\">Modul</th>
								<th width=\"100\">Code</th>
								<th>Category</th>
								<th width=\"50\">Order</th>
								<th width=\"50\">Status</th>";
								if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
								$text.="</tr>
							</thead>
							<tbody>";
								$filter = "where kodeCategory is not null";
								if(!empty($par[kodeModul])){
									$filter.=" and kodeModul='".$par[kodeModul]."'";
								}
								if(!empty($par[filter]))			
									$filter.=" and (
								lower(kodeCategory) like '%".strtolower($par[filter])."%'
								or lower(namaCategory) like '%".strtolower($par[filter])."%'
								)";

								$sql="select * from mst_category $filter order by urutanCategory";
								// echo $sql;
								$arrModul = arrayQuery("select kodeModul, namaModul from app_modul");
								$res=db($sql);
								while($r=mysql_fetch_array($res)){			
									$no++;
									$statusCategory = $r[statusCategory] == "t"?
									"<img src=\"styles/images/t.png\" title='Active'>":
									"<img src=\"styles/images/f.png\" title='Not Active'>";			
									$text.="<tr>
									<td>$no.</td>
									<td>".$arrModul[$r[kodeModul]]."</td>
									<td>$r[kodeCategory]</td>
									<td>";
										$text.=isset($menuAccess["$r[kodeMenu]"]["view"])?
										"<a href=\"?par[mode]=det&par[kodeCategory]=$r[kodeCategory]".getPar($par,"mode,filter")."\" class=\"detil\">$r[namaCategory]</a>":
										"$r[namaCategory]";
										$text.="</td>
										<td align=\"right\">".getAngka($r[urutanCategory])."</td>					
										<td align=\"center\">$statusCategory</td>";
										if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
											$text.="<td align=\"center\">";				
											if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeCategory]=$r[kodeCategory]".getPar($par,"mode,kodeCategory")."',825,500);\"><span>Edit</span></a>";
											if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeCategory]','".getPar($par,"mode,kodeCategory")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
											$text.="</td>";
										}
										$text.="</tr>";
									}

									$text.="</tbody>
								</table>
							</div>";
							return $text;
						}

						function icon(){
							global $c,$p,$m,$s,$inp,$par,$fFile,$arrTitle,$menuAccess,$c,$p,$s;
							// $par[kodeModul] =$c;
							// echo $par[mode];
							$par[kodeSite] =$p;

							$text.="<div class=\"pageheader\">
							<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>				
							".getBread()."				

						</div>
						<div id=\"contentwrapper\" class=\"contentwrapper\">											
							<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
								<div style=\"position:absolute; right:10px; top:10px;\">";

									if($c == 1)
										$text.="<div style=\"float:left; margin-right:5px;\">
									Sub Modul : ".comboData("select * from app_site where statusSite='t' order by urutanSite","kodeModul","namaSite","par[kodeModul]","",$par[kodeModul],"onchange=\"document.getElementById('form').submit();\"")."
									<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\">
								</div>";

								$text.="
								".comboData("select * from app_modul order by urutanModul","kodeModul","namaModul","par[kodeModul]","-- MODUL --",$par[kodeModul],"onchange=\"document.getElementById('form').submit();\"","210px;","chosen-select")."
								<a href=\"?par[mode]=view".getPar($par,"mode")."\"><img src=\"styles/images/rows.png\" style=\"float:left; margin-right:5px; margin-top:1px;\" title=\"List\"></a>";
								if(isset($menuAccess[$s]["add"]) && !empty($par[kodeModul])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeCategory")."',825,500);\" style=\"margin-top:0px;\"><span>New Master</span></a>";
								$text.="</div>
							</form>
							<div class=\"logincontent\">
								<ul class=\"shortcuts\">";	
									if(!empty($par[kodeModul])){
										$filter ="where t1.kodeModul='".$par[kodeModul]."'";
									}
									$sql="select * from mst_category t1 join app_menu t2 on (t1.kodeMenu=t2.kodeMenu) $filter order by t1.urutanCategory";
									$res=db($sql);
									while($r=mysql_fetch_array($res)){
										$icons = empty($r[iconMenu]) ? "styles/images/paper.png" : $fFile.$r[iconMenu];			
										$text.="<li>";
										$text.=isset($menuAccess["$r[kodeMenu]"]["view"])?
										"<a href=\"?c=$c&p=$p&m=$m&s=$s&par[mode]=det&par[kodeCategory]=$r[kodeCategory]\" style=\"background-image: url($icons);\"><span>$r[namaCategory]</span></a>":
										"<a href=\"#\" style=\"background-image: url($icons); background-color: #ddd;\"><span>$r[namaCategory]</span></a>";
										$text.="</li>";
									}
									$text.="</ul>
								</div>
							</div>";
							return $text;
						}

						function getContent($par){
							global $s,$_submit,$menuAccess;
							switch($par[mode]){
								case "order":
								$text = order();
								break;

								case "cek":
								$text = cek();
								break;
								case "chk":
								$text = chk();
								break;
								case "chkDet":
								$text = chkDet();
								break;
								case "chkNama":
								$text = chkNama();
								break;
								case "delDet":
								if(isset($menuAccess[$s]["delete"])) $text = hapusDet(); else $text = detail();
								break;
								case "editDet":
								if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formDet() : ubahDet(); else $text = detail();
								break;
								case "addDet":
								if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDet() : tambahDet(); else $text = detail();
								break;
								case "det":
								$text = detail();
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
								case "view":
								$text = lihat();
								break;
								default:
								$text = icon();
								break;
							}
							return $text;
						}	
						?>