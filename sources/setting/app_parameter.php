<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
		
	function chk(){
		global $inp,$par;
		if(getField("select kodeParameter from app_parameter where statusParameter='f' and kodeParameter='$par[kodeParameter]'"))
		return "sorry, data has been use";
	}
	
	function hapus(){
		global $s,$inp,$par,$cUsername;
		$sql="delete from app_parameter where kodeParameter='$par[kodeParameter]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,kodeParameter")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$cUsername;		
		repField();
		$sql="update app_parameter set namaParameter='$inp[namaParameter]', nilaiParameter='$inp[nilaiParameter]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeParameter='$par[kodeParameter]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$cUsername;		
		$kodeParameter=getField("select kodeParameter from app_parameter order by kodeParameter desc limit 1")+1;		
		
		repField();
		$sql="insert into app_parameter (kodeParameter, namaParameter, nilaiParameter, statusParameter, createBy, createTime) values ('$kodeParameter', '$inp[namaParameter]', '$inp[nilaiParameter]', 't', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from app_parameter where kodeParameter='$par[kodeParameter]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);									
				
		setValidation("is_null","inp[namaParameter]","you must fill name");		
		setValidation("is_null","inp[nilaiParameter]","you must fill value");		
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
						<label class=\"l-input-small\">Name</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaParameter]\" name=\"inp[namaParameter]\" value=\"$r[namaParameter]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Value</label>
						<div class=\"field\">
							<textarea id=\"inp[nilaiParameter]\" name=\"inp[nilaiParameter]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[nilaiParameter]</textarea>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeParameter")."',725,300);\"><span>Add Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"250\">Name</th>
					<th>Value</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (
			lower(namaParameter) like '%".strtolower($par[filter])."%'
			or lower(nilaiParameter) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from app_parameter $filter order by nilaiParameter";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaParameter]</td>
					<td>$r[nilaiParameter]</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeParameter]=$r[kodeParameter]".getPar($par,"mode,kodeParameter")."',725,300);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeParameter]','".getPar($par,"mode,kodeParameter")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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