<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	
	function gGrade(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		$sql="select * from mst_data where statusData='t' and kodeInduk='".$par[idPangkat]."' and kodeCategory='".$arrParameter[12]."' order by urutanData";
		$res = db($sql);
		while($r = mysql_fetch_array($res)){
			$data[] = $r;
		}
		
		return json_encode($data);
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_pengobatan where idPengobatan='$par[idPengobatan]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idPengobatan")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_pengobatan set idPangkat='$inp[idPangkat]', idGrade='$inp[idGrade]', lajangPengobatan='".setAngka($inp[lajangPengobatan])."', keluargaPengobatan='".setAngka($inp[keluargaPengobatan])."', keteranganPengobatan='$inp[keteranganPengobatan]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPengobatan='$par[idPengobatan]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idPengobatan=getField("select idPengobatan from pay_pengobatan order by idPengobatan desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_pengobatan (idPengobatan, idPangkat, idGrade, lajangPengobatan, keluargaPengobatan, keteranganPengobatan, createBy, createTime) values ('$idPengobatan', '$inp[idPangkat]', '$inp[idGrade]', '".setAngka($inp[lajangPengobatan])."', '".setAngka($inp[keluargaPengobatan])."', '$inp[keteranganPengobatan]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_pengobatan where idPengobatan='$par[idPengobatan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusPengobatan] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idPangkat]","anda harus mengisi pangkat");
		setValidation("is_null","inp[idGrade]","anda harus mengisi grade");
		setValidation("is_null","inp[lajangPengobatan]","anda harus mengisi nilai lajang");
		setValidation("is_null","inp[keluargaPengobatan]","anda harus mengisi nilai keluarga");
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
						<label class=\"l-input-small\">Pangkat</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData","kodeData","namaData","inp[idPangkat]"," ",$r[idPangkat],"onchange=\"getGrade('".getPar($par,"mode")."');\"", "360px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Grade</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeInduk='".$r[idPangkat]."' and kodeCategory='".$arrParameter[12]."' order by urutanData","kodeData","namaData","inp[idGrade]"," ",$r[idGrade],"", "360px")."
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Lajang</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[lajangPengobatan]\" name=\"inp[lajangPengobatan]\"  value=\"".getAngka($r[lajangPengobatan])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keluarga</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[keluargaPengobatan]\" name=\"inp[keluargaPengobatan]\"  value=\"".getAngka($r[keluargaPengobatan])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganPengobatan]\" name=\"inp[keteranganPengobatan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keteranganPengobatan]</textarea>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idPengobatan")."',725,400);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th rowspan=\"2\" width=\"20\">No.</th>
					<th rowspan=\"2\">Pangkat</th>
					<th rowspan=\"2\">Grade</th>
					<th colspan=\"2\" width=\"300\">Nilai</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Control</th>";
		$text.="</tr>
				<tr>
					<th width=\"150\">Lajang</th>
					<th width=\"150\">Keluarga</th>
				</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(t1.keteranganPengobatan) like '%".strtolower($par[filter])."%'
			or lower(t2.namaData) like '%".strtolower($par[filter])."%'
			or lower(t3.namaData) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select t1.*, t2.namaData as namaPangkat, t3.namaData as namaGrade from pay_pengobatan t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) $filter order by t2.namaData, t3.namaData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaPangkat]</td>
					<td>$r[namaGrade]</td>					
					<td align=\"right\">".getAngka($r[lajangPengobatan])."</td>
					<td align=\"right\">".getAngka($r[keluargaPengobatan])."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idPengobatan]=$r[idPengobatan]".getPar($par,"mode,idPengobatan")."',725,400);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idPengobatan]=$r[idPengobatan]".getPar($par,"mode,idPengobatan")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
			case "get":
				$text = gGrade();
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