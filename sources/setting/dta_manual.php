<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/manual/";	
		
	function hapusFile(){
		global $s,$inp,$par,$fFile,$cUsername;
		$fileManual = getField("select fileManual from dta_manual where idManual='$par[idManual]'");
		if(file_exists($fFile.$fileManual) and $fileManual!="")unlink($fFile.$fileManual);
		
		$sql="update dta_manual set fileManual='' where idManual='$par[idManual]'";
		db($sql);

		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
	}
	
	function hapus(){
		global $s,$inp,$par,$fFile,$cUsername;
		$fileManual = getField("select fileManual from dta_manual where idManual='$par[idManual]'");
		if(file_exists($fFile.$fileManual) and $fileManual!="")unlink($fFile.$fileManual);
		
		$sql="delete from dta_manual where idManual='$par[idManual]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idManual")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$acc,$fFile,$cUsername;			
		
		$fileIcon = $_FILES["fileManual"]["tmp_name"];
		$fileIcon_name = $_FILES["fileManual"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$fileManual = "manual-".$par[idManual].".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $fileManual);			
		}
		if(empty($fileManual)) $fileManual = getField("select fileManual from dta_manual where idManual='$par[idManual]'");		
		repField();
		
		$sql="update dta_manual set judulManual='$inp[judulManual]', keteranganManual='$inp[keteranganManual]', fileManual='$fileManual', statusManual='$inp[statusManual]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idManual='$par[idManual]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idManual")."';</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$acc,$fFile,$cUsername;		
		$idManual=getField("select idManual from dta_manual order by idManual desc")+1;		
		
		$fileIcon = $_FILES["fileManual"]["tmp_name"];
		$fileIcon_name = $_FILES["fileManual"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$fileManual = "manual-".$idManual.".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $fileManual);			
		}				
		repField("keteranganManual");
		
		$sql="insert into dta_manual (idManual, judulManual, keteranganManual, fileManual, statusManual, createBy, createTime) values ('$idManual', '$inp[judulManual]', '$inp[keteranganManual]', '$fileManual', '$inp[statusManual]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idManual")."';</script>";
	}
	
	function form(){
		global $s,$inp,$par,$fFile,$arrModul,$arrTitle,$menuAccess;		
		$sql="select * from dta_manual where idManual='$par[idManual]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$false =  $r[statusManual] == "f" ? "checked=\"checked\"" : "";
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		
		setValidation("is_null","inp[judulManual]","anda harus mengisi judul");
		setValidation("is_null","inp[keteranganManual]","anda haru mengisi keterangan");
		setValidation("is_null","fileManual","anda haru mengisi file");
		$text = getValidation();	

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Judul</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[judulManual]\" name=\"inp[judulManual]\"  value=\"$r[judulManual]\" class=\"mediuminput\" maxlength=\"150\" style=\"width:350px;\"/>
						</div>
					</p>									
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"keteranganManual\" name=\"inp[keteranganManual]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganManual]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
							$text.=empty($r[fileManual])?
								"<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"fileManual\" name=\"fileManual\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
								</div>":
								"<a href=\"".$fFile."".$r[fileManual]."\"><img src=\"".getIcon($r[fileManual])."\" style=\"padding-right:5px; padding-top:10px;\"></a>
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusManual]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusManual]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>							
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"window.location='?".getPar($par,"mode,idManual")."';\"/>
					</p>
				</div>
			</form>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$fFile;
		
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idManual")."\" class=\"btn btn1 btn_document\"><span>Add Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Judul</th>					
					<th width=\"50\">File</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"75\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
				
		$filter ="where idManual is not null";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(judulManual) like '%".strtolower($par[filter])."%'				
		)";
	
		$sql="select * from dta_manual $filter order by idManual";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$statusManual = $r[statusManual] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";	

			$fileManual = empty($r[fileManual]) ? "" : "<a href=\"".$fFile."".$r[fileManual]."\"><img src=\"".getIcon($r[fileManual])."\"></a>";
			$text.="<tr>
					<td>$no.</td>
					<td>$r[judulManual]</td>					
					<td align=\"center\">$fileManual</td>
					<td align=\"center\">$statusManual</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";									
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idManual]=$r[idManual]".getPar($par,"mode,idManual")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idManual]=$r[idManual]".getPar($par,"mode,idManual")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
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