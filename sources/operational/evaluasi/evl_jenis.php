<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function hapus(){
		global $s,$inp,$par,$cUsername;				
		$sql="delete from dta_evaluasi where idEvaluasi='$par[idEvaluasi]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idEvaluasi")."';</script>";
	}
		
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField();				
		$sql="update dta_evaluasi set namaEvaluasi='$inp[namaEvaluasi]', subEvaluasi='$inp[subEvaluasi]', tujuanEvaluasi='$inp[tujuanEvaluasi]', metodeEvaluasi='$inp[metodeEvaluasi]', statusEvaluasi='$inp[statusEvaluasi]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idEvaluasi='$par[idEvaluasi]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$cUsername;				
		repField();		
		$idEvaluasi = getField("select idEvaluasi from dta_evaluasi order by idEvaluasi desc limit 1")+1;		
		
		$sql="insert into dta_evaluasi (idEvaluasi, namaEvaluasi, subEvaluasi, tujuanEvaluasi, metodeEvaluasi, statusEvaluasi, createBy, createTime) values ('$idEvaluasi', '$inp[namaEvaluasi]', '$inp[subEvaluasi]', '$inp[tujuanEvaluasi]', '$inp[metodeEvaluasi]', '$inp[statusEvaluasi]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);		
		
		echo "<script>closeBox();reloadPage();</script>";
	}
		
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from dta_evaluasi where idEvaluasi='$par[idEvaluasi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
				
		$false =  $r[statusEvaluasi] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";		
		
		setValidation("is_null","inp[namaEvaluasi]","anda harus mengisi jenis");		
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Jenis</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaEvaluasi]\" name=\"inp[namaEvaluasi]\"  value=\"$r[namaEvaluasi]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Sub</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[subEvaluasi]\" name=\"inp[subEvaluasi]\"  value=\"$r[subEvaluasi]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Tujuan</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[tujuanEvaluasi]\" name=\"inp[tujuanEvaluasi]\"  value=\"$r[tujuanEvaluasi]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Metode</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[metodeEvaluasi]\" name=\"inp[metodeEvaluasi]\"  value=\"$r[metodeEvaluasi]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusEvaluasi]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusEvaluasi]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>
						</div>
					</p>
				</div>				
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
				</p>
			</form>	
			</div>";
		return $text;
	}

	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\" /></td>				
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idEvaluasi")."',875,450);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th style=\"min-width:150px;\">Jenis</th>
					<th width=\"150\">Created</th>
					<th style=\"min-width:150px;\">Metode</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$filter = "where namaEvaluasi is not null";
		if(!empty($par[filter]))			
		$filter.= " and (
			lower(namaEvaluasi) like '%".strtolower($par[filter])."%'
			or lower(subEvaluasi) like '%".strtolower($par[filter])."%'
			or lower(tujuanEvaluasi) like '%".strtolower($par[filter])."%'
			or lower(metodeEvaluasi) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from dta_evaluasi $filter order by idEvaluasi";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$statusEvaluasi = $r[statusEvaluasi] == "t"?
			"<img src=\"styles/images/t.png\" title='Aktif'>":
			"<img src=\"styles/images/f.png\" title='Tidak Aktif'>";								
			list($tanggalCreate, $waktuCreate) = explode(" ", $r[createTime]);
			$text.="<tr>
					<td>$no.</td>					
					<td>$r[namaEvaluasi]</td>
					<td align=\"center\">".getTanggal($tanggalCreate)." ".substr($waktuCreate,0,5)."</td>
					<td>$r[metodeEvaluasi]</td>
					<td align=\"center\">$statusEvaluasi</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idEvaluasi]=$r[idEvaluasi]".getPar($par,"mode,idEvaluasi")."',875,450);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idEvaluasi]=$r[idEvaluasi]".getPar($par,"mode,idEvaluasi")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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