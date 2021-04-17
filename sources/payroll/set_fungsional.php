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
		$sql="delete from pay_fungsional where idFungsional='$par[idFungsional]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idFungsional")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_fungsional set idPangkat='$inp[idPangkat]', idGrade='$inp[idGrade]', pusatFungsional='".setAngka($inp[pusatFungsional])."', cabangFungsional='".setAngka($inp[cabangFungsional])."', keteranganFungsional='$inp[keteranganFungsional]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idFungsional='$par[idFungsional]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idFungsional=getField("select idFungsional from pay_fungsional order by idFungsional desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_fungsional (idFungsional, idPangkat, idGrade, pusatFungsional, cabangFungsional, keteranganFungsional, createBy, createTime) values ('$idFungsional', '$inp[idPangkat]', '$inp[idGrade]', '".setAngka($inp[pusatFungsional])."', '".setAngka($inp[cabangFungsional])."', '$inp[keteranganFungsional]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_fungsional where idFungsional='$par[idFungsional]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusFungsional] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idPangkat]","anda harus mengisi pangkat");
		setValidation("is_null","inp[idGrade]","anda harus mengisi grade");
		setValidation("is_null","inp[pusatFungsional]","anda harus mengisi nilai kantor pusat");
		setValidation("is_null","inp[cabangFungsional]","anda harus mengisi nilai kantor cabang");
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
						<label class=\"l-input-small\">Kantor Pusat</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[pusatFungsional]\" name=\"inp[pusatFungsional]\"  value=\"".getAngka($r[pusatFungsional])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Kantor Cabang</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[cabangFungsional]\" name=\"inp[cabangFungsional]\"  value=\"".getAngka($r[cabangFungsional])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganFungsional]\" name=\"inp[keteranganFungsional]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keteranganFungsional]</textarea>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idFungsional")."',725,400);\"><span>Tambah Data</span></a>";
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
					<th width=\"150\">Kantor Pusat</th>
					<th width=\"150\">Kantor Cabang</th>
				</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(t1.keteranganFungsional) like '%".strtolower($par[filter])."%'
			or lower(t2.namaData) like '%".strtolower($par[filter])."%'
			or lower(t3.namaData) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select t1.*, t2.namaData as namaPangkat, t3.namaData as namaGrade from pay_fungsional t1 join mst_data t2 join mst_data t3 on (t1.idPangkat=t2.kodeData and t1.idGrade=t3.kodeData) $filter order by t2.namaData, t3.namaData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaPangkat]</td>
					<td>$r[namaGrade]</td>					
					<td align=\"right\">".getAngka($r[pusatFungsional])."</td>
					<td align=\"right\">".getAngka($r[cabangFungsional])."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idFungsional]=$r[idFungsional]".getPar($par,"mode,idFungsional")."',725,400);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idFungsional]=$r[idFungsional]".getPar($par,"mode,idFungsional")."\" onclick=\"confirm('anda yakin akan menghapus data ini?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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