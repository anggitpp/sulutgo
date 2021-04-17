<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/pengumuman/";	
		
	function hapusFile(){
		global $s,$inp,$par,$fFile,$cUsername;
		$filePengumuman = getField("select filePengumuman from dta_pengumuman where idPengumuman='$par[idPengumuman]'");
		if(file_exists($fFile.$filePengumuman) and $filePengumuman!="")unlink($fFile.$filePengumuman);
		
		$sql="update dta_pengumuman set filePengumuman='' where idPengumuman='$par[idPengumuman]'";
		db($sql);

		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."'</script>";
	}
	
	function hapus(){
		global $s,$inp,$par,$fFile,$cUsername;
		$filePengumuman = getField("select filePengumuman from dta_pengumuman where idPengumuman='$par[idPengumuman]'");
		if(file_exists($fFile.$filePengumuman) and $filePengumuman!="")unlink($fFile.$filePengumuman);
		
		$sql="delete from dta_pengumuman where idPengumuman='$par[idPengumuman]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPengumuman")."';</script>";
	}
	
	function ubah(){
		global $s,$inp,$par,$acc,$fFile,$cUsername;			
		
		$fileIcon = $_FILES["filePengumuman"]["tmp_name"];
		$fileIcon_name = $_FILES["filePengumuman"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$filePengumuman = "doc-".$par[idPengumuman].".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $filePengumuman);			
		}
		if(empty($filePengumuman)) $filePengumuman = getField("select filePengumuman from dta_pengumuman where idPengumuman='$par[idPengumuman]'");		
		repField();
		
		$sql="update dta_pengumuman set tanggalPengumuman='".setTanggal($inp[tanggalPengumuman])."', judulPengumuman='$inp[judulPengumuman]', sumberPengumuman='$inp[sumberPengumuman]', resumePengumuman='$inp[resumePengumuman]', detailPengumuman='$inp[detailPengumuman]', filePengumuman='$filePengumuman', statusPengumuman='$inp[statusPengumuman]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPengumuman='$par[idPengumuman]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idPengumuman")."';</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$acc,$fFile,$cUsername;		
		$idPengumuman=getField("select idPengumuman from dta_pengumuman order by idPengumuman desc")+1;		
		
		$fileIcon = $_FILES["filePengumuman"]["tmp_name"];
		$fileIcon_name = $_FILES["filePengumuman"]["name"];
		if(($fileIcon!="") and ($fileIcon!="none")){						
			fileUpload($fileIcon,$fileIcon_name,$fFile);			
			$filePengumuman = "doc-".$idPengumuman.".".getExtension($fileIcon_name);
			fileRename($fFile, $fileIcon_name, $filePengumuman);			
		}				
		repField("detailPengumuman");
		
		$sql="insert into dta_pengumuman (idPengumuman, tanggalPengumuman, judulPengumuman, sumberPengumuman, resumePengumuman, detailPengumuman, filePengumuman, statusPengumuman, createBy, createTime) values ('$idPengumuman', '".setTanggal($inp[tanggalPengumuman])."', '$inp[judulPengumuman]', '$inp[sumberPengumuman]', '$inp[resumePengumuman]', '$inp[detailPengumuman]', '$filePengumuman', '$inp[statusPengumuman]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPengumuman")."';</script>";
	}
	
	function form(){
		global $s,$inp,$par,$fFile,$arrModul,$arrTitle,$menuAccess;
		include "plugins/mce.jsp";
		$sql="select * from dta_pengumuman where idPengumuman='$par[idPengumuman]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);									
		if(empty($r[tanggalPengumuman])) $r[tanggalPengumuman] = date("Y-m-d");
		
		$false =  $r[statusPengumuman] == "f" ? "checked=\"checked\"" : "";
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		
		setValidation("is_null","inp[judulPengumuman]","anda harus mengisi judul");
		setValidation("is_null","inp[sumberPengumuman]","anda haru mengisi sumber");
		setValidation("is_null","inp[resumePengumuman]","anda haru mengisi ringkasan");
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
							<input type=\"text\" id=\"inp[judulPengumuman]\" name=\"inp[judulPengumuman]\"  value=\"$r[judulPengumuman]\" class=\"mediuminput\" maxlength=\"150\" style=\"width:350px;\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[tanggalPengumuman]\" name=\"inp[tanggalPengumuman]\"  value=\"".getTanggal($r[tanggalPengumuman])."\" style=\"width: 335px\" class=\"hasDatePicker\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Sumber</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[sumberPengumuman]\" name=\"inp[sumberPengumuman]\"  value=\"$r[sumberPengumuman]\" class=\"mediuminput\" maxlength=\"150\" style=\"width:350px;\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Ringkasan</label>
						<div class=\"field\">
							<textarea id=\"inp[resumePengumuman]\" name=\"inp[resumePengumuman]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[resumePengumuman]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Detail</label>
						<div class=\"field\">
							<textarea id=\"mce1\" name=\"inp[detailPengumuman]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[detailPengumuman]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
							$text.=empty($r[filePengumuman])?
								"<input type=\"text\" id=\"iconTemp\" name=\"iconTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
								<div class=\"fakeupload\">
									<input type=\"file\" id=\"filePengumuman\" name=\"filePengumuman\" class=\"realupload\" size=\"50\" onchange=\"this.form.iconTemp.value = this.value;\" />
								</div>":
								"<a href=\"".$fFile."".$r[filePengumuman]."\"><img src=\"".getIcon($r[filePengumuman])."\" style=\"padding-right:5px; padding-top:10px;\"></a>
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
						$text.="</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusPengumuman]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusPengumuman]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>							
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"window.location='?".getPar($par,"mode,idPengumuman")."';\"/>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idPengumuman")."\" class=\"btn btn1 btn_document\"><span>Add Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Judul</th>
					<th width=\"75\">Tanggal</th>
					<th width=\"50\">File</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"75\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
				
		$filter ="where idPengumuman is not null";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(judulPengumuman) like '%".strtolower($par[filter])."%'				
		)";
	
		$sql="select * from dta_pengumuman $filter order by tanggalPengumuman desc";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$statusPengumuman = $r[statusPengumuman] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";	

			$filePengumuman = empty($r[filePengumuman]) ? "" : "<a href=\"".$fFile."".$r[filePengumuman]."\"><img src=\"".getIcon($r[filePengumuman])."\"></a>";
			$text.="<tr>
					<td>$no.</td>
					<td>$r[judulPengumuman]</td>
					<td align=\"center\">".getTanggal($r[tanggalPengumuman])."</td>					
					<td align=\"center\">$filePengumuman</td>
					<td align=\"center\">$statusPengumuman</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";									
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[idPengumuman]=$r[idPengumuman]".getPar($par,"mode,idPengumuman")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idPengumuman]=$r[idPengumuman]".getPar($par,"mode,idPengumuman")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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