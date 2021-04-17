<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
		
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_pph where idPPh='$par[idPPh]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPPh")."';</script>";
	}
	
	function pindah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		db("delete from pay_pph where tahunPPh='$par[tahunPPh]'");
		$sql="select * from pay_pph where tahunPPh='$inp[tahunPPh]'";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
			$idPPh=getField("select idPPh from pay_pph order by idPPh desc limit 1")+1;
			$sql="insert into pay_pph (idPPh, idPerkawinan, tahunPPh, nilaiPPh, keteranganPPh, createBy, createTime) values ('$idPPh', '$r[idPerkawinan]', '".setAngka($par[tahunPPh])."', '".setAngka($r[nilaiPPh])."', '$r[keteranganPPh]', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_pph set idPerkawinan='$inp[idPerkawinan]', tahunPPh='".setAngka($inp[tahunPPh])."', nilaiPPh='".setAngka($inp[nilaiPPh])."', keteranganPPh='$inp[keteranganPPh]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPPh='$par[idPPh]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idPPh=getField("select idPPh from pay_pph order by idPPh desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_pph (idPPh, idPerkawinan, tahunPPh, nilaiPPh, keteranganPPh, createBy, createTime) values ('$idPPh', '$inp[idPerkawinan]', '".setAngka($inp[tahunPPh])."', '".setAngka($inp[nilaiPPh])."', '$inp[keteranganPPh]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function formCopy(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		$tahunPPh = $par[tahunPPh]-1;
		
		setValidation("is_null","inp[tahunPPh]","anda harus mengisi tahun");		
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
							".comboYear("inp[tahunPPh]", $tahunPPh)."
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
		
		$sql="select * from pay_pph where idPPh='$par[idPPh]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		if(empty($r[tahunPPh])) $r[tahunPPh] = $par[tahunPPh];
		
		setValidation("is_null","inp[idPerkawinan]","anda harus mengisi status perkawinan");		
		setValidation("is_null","inp[tahunPPh]","anda harus mengisi tahun");
		setValidation("is_null","inp[nilaiPPh]","anda harus mengisi nilai");		
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
						<label class=\"l-input-small\">Status Perkawinan</label>
						<div class=\"field\">
							".comboData("select kodeData, concat(namaData, ' --', keteranganData) as namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[9]."' order by urutanData","kodeData","namaData","inp[idPerkawinan]"," ",$r[idPerkawinan],"", "310px")."
						</div>
					</p>			
					<p>
						<label class=\"l-input-small\">Tahun</label>
						<div class=\"field\">
							".comboYear("inp[tahunPPh]", $r[tahunPPh])."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiPPh]\" name=\"inp[nilaiPPh]\"  value=\"".getAngka($r[nilaiPPh])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganPPh]\" name=\"inp[keteranganPPh]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keteranganPPh]</textarea>
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
		if(empty($par[tahunPPh])) $par[tahunPPh] = date("Y");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				".comboYear("par[tahunPPh]", $par[tahunPPh])."
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=copy".getPar($par,"mode,idPPh")."',675,250);\"><span>Ambil Arsip</span></a> 
		<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idPPh")."',675,400);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Status Perkawinan</th>					
					<th width=\"150\">Nilai</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		$filter=" where t1.tahunPPh='$par[tahunPPh]'";
		if(!empty($par[filter]))			
		$filter.=" and (			
			lower(t1.keteranganPPh) like '%".strtolower($par[filter])."%'
			or lower(t2.namaData) like '%".strtolower($par[filter])."%'			
		)";
		
		$sql="select t1.*, t2.namaData as namaPerkawinan from pay_pph t1 join mst_data t2 on (t1.idPerkawinan=t2.kodeData) $filter order by t2.urutanData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaPerkawinan]</td>
					<td align=\"right\">".getAngka($r[nilaiPPh])."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idPPh]=$r[idPPh]".getPar($par,"mode,idPPh")."',675,400);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idPPh]=$r[idPPh]".getPar($par,"mode,idPPh")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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