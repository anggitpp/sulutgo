<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function hapus(){
		global $s,$inp,$par,$cUsername;				
		$sql="delete from dta_mesin where idMesin='$par[idMesin]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idMesin")."';</script>";
	}
		
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField();				
		$sql="update dta_mesin set snMesin='$inp[snMesin]', namaMesin='$inp[namaMesin]', alamatMesin='$inp[alamatMesin]', portMesin='$inp[portMesin]', lokasiMesin='$inp[lokasiMesin]', keteranganMesin='$inp[keteranganMesin]', statusMesin='$inp[statusMesin]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idMesin='$par[idMesin]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$cUsername;				
		repField();		
		$idMesin = getField("select idMesin from dta_mesin order by idMesin desc limit 1")+1;		
		
		$sql="insert into dta_mesin (idMesin, snMesin, namaMesin, alamatMesin, portMesin, lokasiMesin, keteranganMesin, statusMesin, createBy, createTime) values ('$idMesin', '$inp[snMesin]', '$inp[namaMesin]', '$inp[alamatMesin]', '$inp[portMesin]', '$inp[lokasiMesin]', '$inp[keteranganMesin]', '$inp[statusMesin]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);		
		
		echo "<script>closeBox();reloadPage();</script>";
	}
		
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from dta_mesin where idMesin='$par[idMesin]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
				
		$false =  $r[statusMesin] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";		

		setValidation("is_null","inp[snMesin]","anda harus mengisi sn");
		setValidation("is_null","inp[namaMesin]","anda harus mengisi nama");
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
						<label class=\"l-input-small\">SN</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[snMesin]\" name=\"inp[snMesin]\"  value=\"$r[snMesin]\" class=\"mediuminput\" style=\"width:100px;\" maxlength=\"30\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nama</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaMesin]\" name=\"inp[namaMesin]\"  value=\"$r[namaMesin]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">IP</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[alamatMesin]\" name=\"inp[alamatMesin]\"  value=\"$r[alamatMesin]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Port</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[portMesin]\" name=\"inp[portMesin]\"  value=\"$r[portMesin]\" class=\"mediuminput\" style=\"width:100px;\" onkeyup=\"checkPhone(this);\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Lokasi</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[lokasiMesin]\" name=\"inp[lokasiMesin]\"  value=\"$r[lokasiMesin]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganMesin]\" name=\"inp[keteranganMesin]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[keteranganMesin]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusMesin]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusMesin]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>
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
				<td>Search : </td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>				
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idMesin")."',875,450);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">SN</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"150\">IP</th>
					<th style=\"min-width:150px;\">Lokasi</th>					
					<th style=\"min-width:150px;\">Keterangan</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$filter = "where namaMesin is not null";
		if(!empty($par[filter]))			
		$filter.= " and (
			lower(snMesin) like '%".strtolower($par[filter])."%'
			or lower(namaMesin) like '%".strtolower($par[filter])."%'
			or lower(alamatMesin) like '%".strtolower($par[filter])."%'
			or lower(lokasiMesin) like '%".strtolower($par[filter])."%'
			or lower(keteranganMesin) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from dta_mesin $filter order by idMesin";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;
			$statusMesin = $r[statusMesin] == "t"?
			"<img src=\"styles/images/t.png\" title='Aktif'>":
			"<img src=\"styles/images/f.png\" title='Tidak Aktif'>";								
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[snMesin]</td>
					<td>$r[namaMesin]</td>
					<td>$r[alamatMesin]:$r[portMesin]</td>
					<td>$r[lokasiMesin]</td>
					<td>".nl2br($r[keteranganMesin])."</td>
					<td align=\"center\">$statusMesin</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idMesin]=$r[idMesin]".getPar($par,"mode,idMesin")."',875,450);\"><span>Edit</span></a>";				
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idMesin]=$r[idMesin]".getPar($par,"mode,idMesin")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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