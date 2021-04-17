<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
			
	function gKomponen(){
		global $db,$s,$inp,$par,$cUsername,$arrParameter;
		$sql="select idKomponen, concat(namaKomponen, ' (', kodeKomponen, ')') as namaKomponen from dta_komponen where statusKomponen='t' and tipeKomponen='".$par[tipeKomponen]."' group by kodeKomponen order by urutanKomponen";
		$res = db($sql);
		while($r = mysql_fetch_array($res)){
			$data[] = $r;
		}
		
		return json_encode($data);
	}		
	
	function hapusDetail(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_formula_detail where idDetail='$par[idDetail]'";
		db($sql);
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idDetail")."';</script>";
	}
	
	function ubahDetail(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$inp[idKomponen] = $inp[tipeDetail] == "u" ? 0 : $inp[idKomponen];
		$sql="update pay_formula_detail set idKomponen='$inp[idKomponen]', tipeDetail='$inp[tipeDetail]', nilaiDetail='".setAngka($inp[nilaiDetail])."', operasi1Detail='$inp[operasi1Detail]', operasi2Detail='$inp[operasi2Detail]', keteranganDetail='$inp[keteranganDetail]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idDetail='$par[idDetail]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambahDetail(){
		global $db,$s,$inp,$par,$cUsername;		
		$idDetail=getField("select idDetail from pay_formula_detail order by idDetail desc limit 1")+1;		
		
		repField();
		$inp[idKomponen] = $inp[tipeDetail] == "u" ? 0 : $inp[idKomponen];
		$sql="insert into pay_formula_detail (idDetail, idFormula, idKomponen, tipeDetail, nilaiDetail, operasi1Detail, operasi2Detail, keteranganDetail, createBy, createTime) values ('$idDetail', '$par[idFormula]', '$inp[idKomponen]', '$inp[tipeDetail]', '".setAngka($inp[nilaiDetail])."', '$inp[operasi1Detail]', '$inp[operasi2Detail]', '$inp[keteranganDetail]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_formula where idFormula='$par[idFormula]'";
		db($sql);
		$sql="delete from pay_formula_detail where idFormula='$par[idFormula]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,idFormula")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_formula set idKomponen='$inp[idKomponen]', namaFormula='$inp[namaFormula]', keteranganFormula='$inp[keteranganFormula]', statusFormula='$inp[statusFormula]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idFormula='$par[idFormula]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$idFormula=getField("select idFormula from pay_formula order by idFormula desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_formula (idFormula, idKomponen, namaFormula, keteranganFormula, statusFormula, createBy, createTime) values ('$idFormula', '$inp[idKomponen]', '$inp[namaFormula]', '$inp[keteranganFormula]', '$inp[statusFormula]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function formDetail(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;

		$sql="select * from pay_formula_detail where idDetail='$par[idDetail]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		if(empty($r[tipeDetail])) $r[tipeDetail] = "t";
		
		$umr =  $r[tipeDetail] == "u" ? "checked=\"checked\"" : "";
		$potongan =  $r[tipeDetail] == "p" ? "checked=\"checked\"" : "";		
		$penerimaan =  (empty($umr) && empty($potongan)) ? "checked=\"checked\"" : "";	
		
		$display = $r[tipeDetail] == "u" ? "none" : "block"; 
		
		setValidation("is_null","inp[operasi1Detail]","anda harus mengisi operator");
		setValidation("is_null","inp[nilaiDetail]","anda harus mengisi nilai");
		setValidation("is_null","inp[operasi2Detail]","anda harus mengisi operator");
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">Formula : ".getField("select namaFormula from pay_formula where idFormula='$par[idFormula]'")."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Tipe</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"penerimaan\" name=\"inp[tipeDetail]\" value=\"t\" onclick=\"document.getElementById('_idKomponen').style.display = 'block'; getKomponen('".getPar($par,"mode")."');\" $penerimaan /> <span class=\"sradio\">Penerimaan</span>
							<input type=\"radio\" id=\"potongan\" name=\"inp[tipeDetail]\" value=\"p\" onclick=\"document.getElementById('_idKomponen').style.display = 'block'; getKomponen('".getPar($par,"mode")."');\" $potongan /> <span class=\"sradio\">Potongan</span>
							<input type=\"radio\" id=\"umr\" name=\"inp[tipeDetail]\" value=\"u\" onclick=\"document.getElementById('_idKomponen').style.display = 'none';\" $umr /> <span class=\"sradio\">UMR</span>
						</div>
					</p>
					<div id=\"_idKomponen\" style=\"display:$display\">
					<p>
						<label class=\"l-input-small\">Komponen</label>
						<div class=\"field\">
							".comboData("select idKomponen, concat(namaKomponen, ' (', kodeKomponen, ')') as namaKomponen from dta_komponen where tipeKomponen='$r[tipeDetail]' and statusKomponen='t' group by kodeKomponen order by urutanKomponen","idKomponen","namaKomponen","inp[idKomponen]","",$r[idKomponen],"", "300px")."
						</div>
					</p>	
					</div>
					<p>
						<label class=\"l-input-small\">Operator</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[operasi1Detail]\" name=\"inp[operasi1Detail]\" value=\"$r[operasi1Detail]\" class=\"mediuminput\" style=\"text-align:center; width:30px;\" maxlength=\"1\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilaiDetail]\" name=\"inp[nilaiDetail]\"  value=\"".$r[nilaiDetail]."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Operator</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[operasi2Detail]\" name=\"inp[operasi2Detail]\" value=\"$r[operasi2Detail]\" class=\"mediuminput\" style=\"text-align:center; width:30px;\" maxlength=\"1\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganDetail]\" name=\"inp[keteranganDetail]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganDetail]</textarea>
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
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;

		$sql="select * from pay_formula where idFormula='$par[idFormula]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusFormula] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
				
		setValidation("is_null","inp[namaFormula]","anda harus mengisi nama");
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
						<label class=\"l-input-small\">Nama</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaFormula]\" name=\"inp[namaFormula]\" value=\"$r[namaFormula]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>
					</p>													
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganFormula]\" name=\"inp[keteranganFormula]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganFormula]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Nilai Minimal</label>
						<div class=\"field\">
							".comboData("select * from dta_komponen where statusKomponen='t' group by kodeKomponen order by urutanKomponen","idKomponen","namaKomponen","inp[idKomponen]"," ",$r[idKomponen],"", "360px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusFormula]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusFormula]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idFormula")."',725,350);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Nama</th>
					<th width=\"100\">Komponen</th>					
					<th width=\"50\">Setting</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (
			lower(namaFormula) like '%".strtolower($par[filter])."%'			
			or lower(keteranganFormula) like '%".strtolower($par[filter])."%'
		)";
		
		$arrDetail = arrayQuery("select t1.idFormula, count(t2.idDetail) from pay_formula t1 join pay_formula_detail t2 on (t1.idFormula=t2.idFormula) $filter group by 1");
		
		$sql="select * from pay_formula $filter order by namaFormula";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$statusFormula = $r[statusFormula] == "t"?
			"<img src=\"styles/images/t.png\">":
			"<img src=\"styles/images/f.png\">";
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaFormula]</td>											
					<td align=\"center\">".getAngka($arrDetail["$r[idFormula]"])."</td>
					<td align=\"center\"><a href=\"?par[mode]=det&par[idFormula]=$r[idFormula]".getPar($par,"mode,idFormula")."\" title=\"Setting Komponen\" class=\"detail\" ><span>Setting</span></a></td>
					<td align=\"center\">$statusFormula</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idFormula]=$r[idFormula]".getPar($par,"mode,idFormula")."',725,350);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[idFormula]=$r[idFormula]".getPar($par,"mode,idFormula")."\" onclick=\"confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Formula : ".getField("select namaFormula from pay_formula where idFormula='$par[idFormula]'")."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
				".setPar($par, "filter")."
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>
			<div id=\"pos_r\"><a href=\"?".getPar($par,"mode,idFormula")."\" class=\"btn btn1 btn_list\"><span>List Komponen</span></a> ";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=addDet".getPar($par,"mode,idDetail")."',725,450);\"><span>Tambah Data</span></a>";
		$text.="
				</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Komponen</th>
					<th width=\"100\">Operator 1</th>					
					<th width=\"100\">Nilai</th>
					<th width=\"100\">Operator 2</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody>";
		
		
		$filter="where t1.idFormula='$par[idFormula]'";
		if(!empty($par[filter]))			
		$filter.=" and (
			lower(t2.namaKomponen) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from pay_formula_detail t1 left join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) $filter order by t1.idDetail";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;						
			$r[namaKomponen] = $r[tipeDetail] == "u" ? "UMR" : $r[namaKomponen];
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaKomponen] ($r[kodeKomponen])</td>											
					<td align=\"center\">$r[operasi1Detail]</td>
					<td align=\"right\">".$r[nilaiDetail]."</td>
					<td align=\"center\">$r[operasi2Detail]</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editDet&par[idDetail]=$r[idDetail]".getPar($par,"mode,idDetail")."',725,450);\"><span>Edit</span></a>";
				if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=delDet&par[idDetail]=$r[idDetail]".getPar($par,"mode,idDetail")."\" onclick=\"confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
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
				$text = gKomponen();
			break;
			case "delDet":
				if(isset($menuAccess[$s]["delete"])) $text = hapusDetail(); else $text = lihat();
			break;
			case "editDet":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formDetail() : ubahDetail(); else $text = lihat();
			break;
			case "addDet":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formDetail() : tambahDetail(); else $text = lihat();
			break;
			case "det":
				$text = detail();
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