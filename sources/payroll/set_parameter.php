<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
			
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_parameter where idParameter='$par[idParameter]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idParameter")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_parameter set kodeParameter='$inp[kodeParameter]', namaParameter='$inp[namaParameter]', nilaiParameter='$inp[nilaiParameter]', keteranganParameter='$inp[keteranganParameter]', statusParameter='$inp[statusParameter]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idParameter='$par[idParameter]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idParameter=getField("select idParameter from pay_parameter order by idParameter desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_parameter (idParameter, kodeParameter, namaParameter, nilaiParameter, keteranganParameter, statusParameter, createBy, createTime) values ('$idParameter', '$inp[kodeParameter]', '$inp[namaParameter]', '$inp[nilaiParameter]', '$inp[keteranganParameter]', '$inp[statusParameter]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;

		$sql="select * from pay_parameter where idParameter='$par[idParameter]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusParameter] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[kodeParameter]","anda harus mengisi kode");
		setValidation("is_null","inp[namaParameter]","anda harus mengisi parameter");
		setValidation("is_null","inp[nilaiParameter]","anda harus mengisi value");
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
						<label class=\"l-input-small\">Kode</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[kodeParameter]\" name=\"inp[kodeParameter]\" value=\"$r[kodeParameter]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"30\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Parameter</label>
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
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganParameter]\" name=\"inp[keteranganParameter]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganParameter]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusParameter]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusParameter]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>
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
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idParameter")."',725,425);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Parameter</th>
					<th width=\"100\">Kode</th>
					<th>Keterangan</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (
			lower(namaParameter) like '%".strtolower($par[filter])."%'
			or lower(kodeParameter) like '%".strtolower($par[filter])."%'
			or lower(keteranganParameter) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from pay_parameter $filter order by namaParameter";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$statusParameter = $r[statusParameter] == "t"?
			"<img src=\"styles/images/t.png\">":
			"<img src=\"styles/images/f.png\">";
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaParameter]</td>
					<td>$r[kodeParameter]</td>
					<td>".nl2br($r[keteranganParameter])."</td>
					<td align=\"center\">$statusParameter</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idParameter]=$r[idParameter]".getPar($par,"mode,idParameter")."',725,425);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.=$r[statusParameter] == "f" ?
				"<a href=\"?par[mode]=del&par[idParameter]=$r[idParameter]".getPar($par,"mode,idParameter")."\" onclick=\"confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>":
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