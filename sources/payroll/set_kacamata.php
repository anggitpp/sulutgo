<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
		
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_kacamata where idKacamata='$par[idKacamata]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idKacamata")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_kacamata set idKategori='$inp[idKategori]',idJabatan='$inp[idJabatan]', idTipe='$inp[idTipe]', nilaiKacamata='".setAngka($inp[nilaiKacamata])."', maxKacamata='".setAngka($inp[maxKacamata])."', batasKacamata='".setAngka($inp[batasKacamata])."', keteranganKacamata='$inp[keteranganKacamata]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idKacamata='$par[idKacamata]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idKacamata=getField("select idKacamata from pay_kacamata order by idKacamata desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_kacamata (idKacamata, idKategori, idTipe, idJabatan, nilaiKacamata, maxKacamata, batasKacamata, keteranganKacamata, createBy, createTime) values ('$idKacamata', '$inp[idKategori]', '$inp[idTipe]','$inp[idJabatan]', '".setAngka($inp[nilaiKacamata])."', '".setAngka($inp[maxKacamata])."', '".setAngka($inp[batasKacamata])."', '$inp[keteranganKacamata]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_kacamata where idKacamata='$par[idKacamata]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusKacamata] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");
		setValidation("is_null","inp[idTipe]","anda harus mengisi tipe");
		setValidation("is_null","inp[nilaiKacamata]","anda harus mengisi nilai");
		setValidation("is_null","inp[maxKacamata]","anda harus mengisi max klaim");
		setValidation("is_null","inp[batasKacamata]","anda harus mengisi batas");
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
						<label class=\"l-input-small\">Kategori</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[17]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "360px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tipe</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[18]."' order by urutanData","kodeData","namaData","inp[idTipe]"," ",$r[idTipe],"", "360px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Pangkat</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData","kodeData","namaData","inp[idJabatan]"," ",$r[idJabatan],"", "360px","chosen-select")."
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiKacamata]\" name=\"inp[nilaiKacamata]\"  value=\"".getAngka($r[nilaiKacamata])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Max</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[maxKacamata]\" name=\"inp[maxKacamata]\"  value=\"".getAngka($r[maxKacamata])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" /> klaim
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Batas</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[batasKacamata]\" name=\"inp[batasKacamata]\"  value=\"".getAngka($r[batasKacamata])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" /> kali
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganKacamata]\" name=\"inp[keteranganKacamata]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:350px;\">$r[keteranganKacamata]</textarea>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idKacamata")."',725,450);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Kategori</th>
					<th>Tipe</th>
					<th>Jabatan</th>
					<th width=\"150\">Nilai</th>
					<th width=\"100\">Max</th>
					<th width=\"100\">Batas</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(t1.keteranganKacamata) like '%".strtolower($par[filter])."%'
			or lower(t2.namaData) like '%".strtolower($par[filter])."%'
			or lower(t3.namaData) like '%".strtolower($par[filter])."%'
		)";
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select t1.*, t2.namaData as namaKategori, t3.namaData as namaTipe from pay_kacamata t1 join mst_data t2 join mst_data t3 on (t1.idKategori=t2.kodeData and t1.idTipe=t3.kodeData) $filter order by t2.namaData, t3.namaData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaKategori]</td>
					<td>$r[namaTipe]</td>					
					<td>".$arrMaster[$r[idJabatan]]."</td>					
					<td align=\"right\">".getAngka($r[nilaiKacamata])."</td>
					<td align=\"right\">".getAngka($r[maxKacamata])." klaim</td>
					<td align=\"right\">".getAngka($r[batasKacamata])." kali</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idKacamata]=$r[idKacamata]".getPar($par,"mode,idKacamata")."',725,450);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idKacamata]=$r[idKacamata]".getPar($par,"mode,idKacamata")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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