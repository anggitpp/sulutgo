<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
		
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_skala where id='$par[id]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_skala set idGolongan='$inp[idGolongan]', minSkala='".setAngka($inp[minSkala])."', midSkala='".setAngka($inp[midSkala])."', maxSkala='".setAngka($inp[maxSkala])."', rawatSkala='".setAngka($inp[rawatSkala])."', ketSkala='$inp[ketSkala]', updateBy='$cUsername', updateDate='".date('Y-m-d H:i:s')."' where id='$par[id]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$id=getField("select id from pay_skala order by id desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_skala (id, idGolongan, maxSkala, minSkala, midSkala, rawatSkala, ketSkala, createBy, createDate) values ('$id', '$inp[idGolongan]', '".setAngka($inp[maxSkala])."', '".setAngka($inp[minSkala])."', '".setAngka($inp[midSkala])."', '".setAngka($inp[rawatSkala])."', '$inp[ketSkala]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		echo
		db($sql);
		echo "<script>alert('DATA BERHASIL DISIMPAN');closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_skala where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusKacamata] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idGolongan]","anda harus mengisi kategori");
		
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
						<label class=\"l-input-small\">Golongan</label>
						<div class=\"field\">
							".comboKey("inp[idGolongan]",array("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16"),$r[idGolongan])."
						</div>
					</p>
										
					<p>
						<label class=\"l-input-small\">Min</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[minSkala]\" name=\"inp[minSkala]\"  value=\"".getAngka($r[minSkala])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Mid</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[midSkala]\" name=\"inp[midSkala]\"  value=\"".getAngka($r[midSkala])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Max</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[maxSkala]\" name=\"inp[maxSkala]\"  value=\"".getAngka($r[maxSkala])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Rawat Jalan</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[rawatSkala]\" name=\"inp[rawatSkala]\"  value=\"".getAngka($r[rawatSkala])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[ketSkala]\" name=\"inp[ketSkala]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:350px;\">$r[ketSkala]</textarea>
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
					<th>Golongan</th>
					<th width=\"100\">Min</th>
					<th width=\"100\">Mid</th>
					<th width=\"100\">Max</th>
					<th width=\"150\">Keterangan</th>
					";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(t1.ketSkala) like '%".strtolower($par[filter])."%'
			or lower(t2.namaData) like '%".strtolower($par[filter])."%'
		)";
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select * from pay_skala";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[idGolongan]</td>
					<td align=\"right\">".getAngka($r[minSkala])."</td>					
					<td align=\"right\">".getAngka($r[midSkala])."</td>					
					<td align=\"right\">".getAngka($r[maxSkala])."</td>		
					<td>$r[ketSkala]</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode,id")."',725,450);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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