<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
		
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_pkp where idPkp='$par[idPkp]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPkp")."';</script>";
	}
	
	function pindah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		db("delete from pay_pkp where tahunPkp='$par[tahunPkp]'");
		$sql="select * from pay_pkp where tahunPkp='$inp[tahunPkp]'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
			$idPkp=getField("select idPkp from pay_pkp order by idPkp desc limit 1")+1;
			$sql="insert into pay_pkp (idPkp, tahunPkp, awalPkp, akhirPkp, persenPkp, keteranganPkp, createBy, createTime) values ('$idPkp', '".setAngka($par[tahunPkp])."', '".setAngka($r[awalPkp])."', '".setAngka($r[akhirPkp])."', '".setAngka($r[persenPkp])."', '$r[keteranganPkp]', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_pkp set tahunPkp='".setAngka($inp[tahunPkp])."', awalPkp='".setAngka($inp[awalPkp])."', akhirPkp='".setAngka($inp[akhirPkp])."', persenPkp='".setAngka($inp[persenPkp])."', keteranganPkp='$inp[keteranganPkp]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPkp='$par[idPkp]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idPkp=getField("select idPkp from pay_pkp order by idPkp desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_pkp (idPkp, tahunPkp, awalPkp, akhirPkp, persenPkp, keteranganPkp, createBy, createTime) values ('$idPkp', '".setAngka($inp[tahunPkp])."', '".setAngka($inp[awalPkp])."', '".setAngka($inp[akhirPkp])."', '".setAngka($inp[persenPkp])."', '$inp[keteranganPkp]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function formCopy(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$tahunPkp = $par[tahunPkp]-1;
		
		setValidation("is_null","inp[tahunPkp]","anda harus mengisi tahun");		
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
						<label class=\"l-input-small\">Tahun</label>
						<div class=\"field\">
							".comboYear("inp[tahunPkp]", $tahunPkp)."
							<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Copy Data\" style=\"margin-left:50px;\"/> 
							<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
						</div>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_pkp where idPkp='$par[idPkp]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		if(empty($r[tahunPkp])) $r[tahunPkp] = $par[tahunPkp];
				
		setValidation("is_null","inp[awalPkp]","anda harus mengisi nilai");		
		setValidation("is_null","inp[akhirPkp]","anda harus mengisi nilai");		
		setValidation("is_null","inp[tahunPkp]","anda harus mengisi tahun");		
		setValidation("is_null","inp[persenPkp]","anda harus mengisi potongan");		
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
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[awalPkp]\" name=\"inp[awalPkp]\"  value=\"".getAngka($r[awalPkp])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
							 s.d 
							<input type=\"text\" id=\"inp[akhirPkp]\" name=\"inp[akhirPkp]\"  value=\"".getAngka($r[akhirPkp])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tahun</label>
						<div class=\"field\">
							".comboYear("inp[tahunPkp]", $r[tahunPkp])."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Potongan</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[persenPkp]\" name=\"inp[persenPkp]\"  value=\"".getAngka($r[persenPkp])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" /> %
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganPkp]\" name=\"inp[keteranganPkp]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keteranganPkp]</textarea>
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
		if(empty($par[tahunPkp])) $par[tahunPkp] = date("Y");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				".comboYear("par[tahunPkp]", $par[tahunPkp])."
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=copy".getPar($par,"mode,idPkp")."',675,250);\"><span>Ambil Arsip</span></a> 
		<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idPkp")."',675,400);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"200\">Nilai</th>					
					<th width=\"100\">Potongan</th>
					<th>Keterangan</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$filter=" where tahunPkp='$par[tahunPkp]'";
		if(!empty($par[filter]))			
		$filter.=" and (			
			lower(keteranganPkp) like '%".strtolower($par[filter])."%'			
		)";
		
		$sql="select * from pay_pkp $filter order by awalPkp";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$nilaiPkp = "";
			if($r[awalPkp] < 1){
				$nilaiPkp = "< ".getAngka($r[akhirPkp]);
			}else if($r[akhirPkp] < 1){
				$nilaiPkp = "> ".getAngka($r[awalPkp]);
			}else{
				$nilaiPkp = getAngka($r[awalPkp])." s.d ".getAngka($r[akhirPkp]);
			}
			
			$text.="<tr>
					<td>$no.</td>
					<td align=\"left\">".$nilaiPkp."</td>
					<td align=\"center\">".getAngka($r[persenPkp])." %</td>
					<td>$r[keteranganPkp]</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idPkp]=$r[idPkp]".getPar($par,"mode,idPkp")."',675,400);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idPkp]=$r[idPkp]".getPar($par,"mode,idPkp")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
			case "copy":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formCopy() : pindah(); else $text = lihat();
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