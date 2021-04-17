<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
		
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_skala where idPangkat='$par[idPangkat]' and idSkala='$par[idSkala]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPangkat,idSkala")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_skala set idPangkat='$inp[idPangkat]', idSkala='$inp[idSkala]', nilaiSkala='".setAngka($inp[nilaiSkala])."', keteranganSkala='$inp[keteranganSkala]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPangkat='$par[idPangkat]' and idSkala='$par[idSkala]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idSkala=getField("select idSkala from pay_skala order by idSkala desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_skala (idPangkat, idSkala, nilaiSkala, keteranganSkala, createBy, createTime) values ('$inp[idPangkat]', '$inp[idSkala]', '".setAngka($inp[nilaiSkala])."', '$inp[keteranganSkala]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		echo
		db($sql);
		echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_skala where idSkala='$par[idSkala]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusKacamata] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idPangkat]","anda harus mengisi pangkat");
		setValidation("is_null","inp[idPangkat]","anda harus mengisi pangkat");
		$text = getValidation();

		$text.="<style>
					#inp_idPangkat__chosen {min-width: 360px; width: auto;}
					#inp_idSkala__chosen {min-width: 360px; width: auto;}
				</style>
				<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">Pangkat</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='S09' order by urutanData","kodeData","namaData","inp[idPangkat]"," ", $r[idPangkat],"","360px","chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Skala</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='SG' order by urutanData","kodeData","namaData","inp[idSkala]"," ", $r[idSkala],"","360px","chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiSkala]\" name=\"inp[nilaiSkala]\"  value=\"".getAngka($r[nilaiSkala])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganSkala]\" name=\"inp[keteranganSkala]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:350px;\">$r[keteranganSkala]</textarea>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idSkalaKacamata")."',725,450);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Pangkat</th>
					<th width=\"100\">Skala</th>
					<th width=\"100\">Nilai</th>
					<th width=\"150\">Keterangan</th>
					";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(keteranganSkala) like '%".strtolower($par[filter])."%'
		)";
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select * from pay_skala";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>".$arrMaster["$r[idPangkat]"]."</td>
					<td align=\"center\">".$arrMaster["$r[idSkala]"]."</td>				
					<td align=\"right\">".getAngka($r[nilaiSkala])."</td>	
					<td>".nl2br($r[keteranganSkala])."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idPangkat]=$r[idPangkat]&par[idSkala]=$r[idSkala]".getPar($par,"mode,idPangkat,idSkala")."',725,450);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idPangkat]=$r[idPangkat]&par[idSkala]=$r[idSkala]".getPar($par,"mode,idPangkat,idSkala")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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