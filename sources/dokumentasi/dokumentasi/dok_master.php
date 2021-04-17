<?php


if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
$fManual = "files/FileMenu/";
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

function icon(){
	global $c,$p,$m,$s,$inp,$par,$fFile,$arrTitle,$menuAccess;
	$par[kodeModul] =$c;
	$par[kodeSite] =$p;

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>				
	".getBread()."				

</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">											
	<form id=\"form\" name=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<div style=\"position:absolute; right:0px; margin-right:20px; margin-top:-60px;\">";

			if($c == 1)
				$text.="<div style=\"float:left; margin-right:5px;\">
			Sub Modul : ".comboData("select * from app_site where statusSite='t' order by urutanSite","kodeModul","namaSite","par[kodeModul]","",$par[kodeModul],"onchange=\"document.getElementById('form').submit();\"")."
			<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\">
		</div>";

		$text.="<a href=\"?par[mode]=view".getPar($par,"mode")."\"><img src=\"styles/images/rows.png\" style=\"float:left; margin-right:5px; margin-top:1px;\" title=\"List\"></a>";
		if(isset($menuAccess[$s]["add"]) && $par[kodeModul] == $c) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeCategory")."',825,500);\" style=\"margin-top:0px;\"><span>New Master</span></a>";
		$text.="</div>
	</form>
	<div class=\"logincontent\">
		<ul class=\"shortcuts\">";	

			$filter ="where t1.kodeModul='".$par[kodeModul]."'";
			$sql="select * from mst_category t1 join app_menu t2 on (t1.kodeMenu=t2.kodeMenu) $filter order by t1.urutanCategory";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$icons = empty($r[iconMenu]) ? "styles/images/paper.png" : $fFile.$r[iconMenu];			
				$text.="<li>";
				$text.=isset($menuAccess["$r[kodeMenu]"]["view"])?
				"<a href=\"?c=$c&p=$p&m=$m&s=$r[kodeMenu]&par[mode]=det&par[kodeCategory]=$r[kodeCategory]\" style=\"background-image: url($icons);\"><span>$r[namaCategory]</span></a>":
				"<a href=\"#\" style=\"background-image: url($icons); background-color: #ddd;\"><span>$r[namaCategory]</span></a>";
				$text.="</li>";
			}
			$text.="</ul>
		</div>
	</div>";
	return $text;
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
	global $s, $inp, $par, $cUsername, $arrParam;
	$kodeMenu = getField("select kodeMenu from mst_category where kodeCategory='$par[kodeCategory]'");		
	$fileIcon = $_FILES["iconMenu"]["tmp_name"];
	$fileIcon_name = $_FILES["iconMenu"]["name"];
	if(($fileIcon!="") and ($fileIcon!="none")){						
		fileUpload($fileIcon,$fileIcon_name,$fFile);			
		$iconMenu = "ico-".$kodeMenu.".".getExtension($fileIcon_name);
		fileRename($fFile, $fileIcon_name, $iconMenu);			
	}
	if(empty($iconMenu)) $iconMenu = getField("select iconMenu from app_menu where kodeMenu='$kodeMenu'");

	$sql="update app_menu set namaMenu='$inp[namaCategory]', iconMenu='$iconMenu', urutanMenu='".setAngka($inp[urutanCategory])."', statusMenu='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeMenu='$kodeMenu'";
	db($sql);

	$sql="update mst_category set kodeCategory='$inp[kodeCategory]', kodeInduk='$inp[kodeInduk]', namaCategory='$inp[namaCategory]', keteranganCategory='$inp[keteranganCategory]', urutanCategory='".setAngka($inp[urutanCategory])."', statusCategory='$inp[statusCategory]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeCategory='$par[kodeCategory]'";
	db($sql);				

	$sql="update mst_data set kodeCategory='$inp[kodeCategory]' where kodeCategory='$par[kodeCategory]'";
	db($sql);

	$sql="update mst_category set kodeInduk='$inp[kodeCategory]' where kodeInduk='$par[kodeCategory]'";
	db($sql);

	$sql="update app_parameter set nilaiParameter='$inp[kodeCategory]' where nilaiParameter='$par[kodeCategory]'";
	db($sql);
	// echo $sql;
	// die();
	echo "<script>alert('UPDATE DATA BERHASIL');closeBox();reloadPage();</script>";
}

function lihat(){

	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$cVac,$cyear,$m,$arrParam;

	$modul = getField("select kodeModul from app_modul order by urutanModul asc limit 1");
	$par[modul] = empty($par[modul]) ? $modul : $par[modul];
	$par[divisi] = isset($par["divisi"]) ? $par["divisi"] : "";
	$cols=4;	
	if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
		$cols=5;	
	}


	$text = table($cols, array(($cols-1),$cols));

	$text.="<div class=\"pageheader\">

	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

	".getBread()."

	<span class=\"pagedesc\">&nbsp;</span>

</div>    

<div id=\"contentwrapper\" class=\"contentwrapper\">

	<form action=\"\" method=\"post\" id = \"form\" class=\"stdform\" onsubmit=\"return false;\">

		<div id=\"pos_l\" style=\"float:left;\">

			<p>					

				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>

	".comboData("select * from app_modul order by urutanModul","kodeModul","namaModul","par[modul]","",$par[modul],"onchange=\"document.getElementById('form').submit();\"","210px;","chosen-select")."
			</p>

		</div>	
		



	</form>

	<br clear=\"all\" />

	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

		<thead>

			<tr>
				<th width=\"20\">No.</th>
				
				<th width=\"100\">Kode</th>
				
				<th width=\"*\">Nama Kategori</th>
				<th width=\"50\">Status</th>

				
				
				";if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"50\">Kontrol</th>";
				$text.="


			</thead>

			<tbody></tbody>
			</table>

		</div>";
		$sekarang = date('Y-m-d');
		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=DATA MANUAL ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}

		return $text;

	}

	

	function lData(){

		global $s,$par,$fManual,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;	
		// global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;	
		if($_GET[json]==1){
			header("Content-type: application/json");
		}

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	// echo $sLimit;
		

		$filters= " where t1.statusCategory = 't'";

		if (!empty($_GET['fSearch']))

			$filters.= " and (				

		lower(t1.namaCategory) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				


		)";

		
		

		$arrOrder = array(	

			"t1.namaCategory",
			"t1.kodeCategory",
			"t1.namaCategory",
			"t1.statusCategory",
			"",




			);


		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

		if(!empty($par[modul])){
			$filters .= " AND t1.kodeModul ='$par[modul]'";
		}

		$sql = " SELECT * FROM mst_category t1 join app_modul t2 on t1.kodeModul = t2.kodeModul $filters order by $orderBy $sLimit ";
		// echo $sql;

		$res=db($sql);

		

		$json = array(

			"iTotalRecords" => mysql_num_rows($res),

			"iTotalDisplayRecords" => getField("SELECT COUNT(*) FROM mst_category t1 join app_modul t2 on t1.kodeModul = t2.kodeModul $filters"),
			

			"aaData" => array(),

			);





		

		$no=intval($_GET['iDisplayStart']);

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		// $par[kodeModul] =$c;

		while($r=mysql_fetch_array($res)){
			$no++;

			$r[status] = $r[statusCategory] == "t" ? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
			$controlKebutuhan = "";
			if (isset($menuAccess[$s]["edit"])) 
				$controlKebutuhan.= "<a onclick=\"openBox('popup.php?par[mode]=edit&par[kodeCategory]=$r[kodeCategory]&par[kodeModul]=$par[modul]". getPar($par, "mode,idp") . "',825,400);\"  href=\"#\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
			if(isset($menuAccess[$s]["delete"])) 
				$controlKebutuhan.="<a href=\"#Delete\" onclick=\"del('$r[kodeCategory]','".getPar($par,"mode,kodeCategory")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			}

			$r[namaCategory] = "<a href=\"?par[mode]=det&par[kodeCategory]=$r[kodeCategory]".getPar($par,"mode")."\" class=\"detil\">$r[namaCategory]</a>";

			$data=array(

				"<div align=\"center\">".$no.".</div>",				

				"<div align=\"left\">$r[kodeCategory]</div>",

				"<div align=\"left\">$r[namaCategory]</div>",

				"<div align=\"center\">$r[status]</div>",	

				"<div align=\"center\">$controlKebutuhan</div>",		

				

				);





			$json['aaData'][]=$data;


		}

		if($par[mode] == "xls"){
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=DATA MANUAL ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}

		return json_encode($json);

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

	function detail(){
		global $p,$m,$s,$inp,$par,$arrTitle,$menuAccess;
		$kodeCategory = getField("select kodeInduk from mst_category where kodeCategory='$par[kodeCategory]'");
		
		$text.="<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
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


// 	function xls(){		
// 	global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID,$areaCheck;
// 	require_once 'plugins/PHPExcel.php';
// 	$sekarang = date('Y-m-d');
	
// 	$objPHPExcel = new PHPExcel();				
// 	$objPHPExcel->getProperties()->setCreator($cName)
// 	->setLastModifiedBy($cName)
// 	->setTitle($arrTitle["".$_GET[p].""]);
// 	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
// 	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
// 	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
// 	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
// 	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);

// 	$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');		
// 	$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');		
// 	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
// 	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
// 	$objPHPExcel->getActiveSheet()->setCellValue('A1', "REKAP MASTER DATA");
// 	$objPHPExcel->getActiveSheet()->setCellValue('A2', "TANGGAL : ".date('Y-m-d H:i:s'));

	
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getFont()->setBold(true);	
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
// 	$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
// 	$objPHPExcel->getActiveSheet()->setCellValue('B4', "MODUL");
// 	$objPHPExcel->getActiveSheet()->setCellValue('C4', "KODE");
// 	$objPHPExcel->getActiveSheet()->setCellValue('D4', "NAMA KATEGORI");
// 	$objPHPExcel->getActiveSheet()->setCellValue('E4', "STATUS");
	
// 	$rows=5;
		
// 	$sql = " SELECT * FROM mst_category t1 join app_modul t2 on t1.kodeModul = t2.kodeModul ";

// 	$res=db($sql);
// 	while($r=mysql_fetch_array($res)){			
// 		$no++;
// 		$r[status] = $r[statusCategory] == "t" ? "Aktif" : "Tidak Aktif";			
							
// 		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		
// 		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
// 		$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
// 		$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[namaModul]);
// 		$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[kodeCategory]);
// 		$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaCategory]);
// 		$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[status]);

		
		
// 		$rows++;
// 	}
// 	$rows--;
// 	$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// 	$objPHPExcel->getActiveSheet()->getStyle('A1:E'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
// 	$objPHPExcel->getActiveSheet()->getStyle('A4:E'.$rows)->getAlignment()->setWrapText(true);						
	
// 	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
// 	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
// 	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
// 	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
// 	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
// 	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
// 	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
// 	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
// 	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);
	
// 	$objPHPExcel->getActiveSheet()->setTitle("DATA MASTER DATA");
// 	$objPHPExcel->setActiveSheetIndex(0);
	
// 	// Save Excel file
// 	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// 	$objWriter->save($fExport."DATA MASTER DATA ".$sekarang.".xls");
// }	



function form(){
	global $c, $s,$inp,$par,$fFile,$arrTitle,$menuAccess;		
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
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
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



function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode]){


		case "lst":

		$text=lData();

		break;	

		case "delManual":
		$text = hapusManual();
		break;

		case "delIco":				
		if(isset($menuAccess[$s]["edit"])) $text = hapusIcon(); else $text = lihat();
		break;

		case "edit":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
		break;

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