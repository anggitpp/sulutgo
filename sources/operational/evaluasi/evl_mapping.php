<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	

	function ubah(){

		global $s,$inp,$par,$cUsername;

		repField();				

		$sql="update plt_pelatihan set idEvaluasi='$inp[idEvaluasi]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPelatihan='$par[idPelatihan]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess, $loaded;

		if (!isset($loaded)) {
			echo "<script>window.location='?". getPar($par) ."&loaded'</script>";
			return;
		}

		$res=db("select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'");
		$r=mysql_fetch_array($res);
			
		setValidation("is_null","inp[idEvaluasi]","anda harus mengisi evaluasi");		
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
						<label class=\"l-input-small\">Pelatihan</label>
						<span class=\"field\" style=\"border:0px;\">$r[judulPelatihan]&nbsp;</span>
					</p>					
					<p>
						<label class=\"l-input-small\">Evaluasi</label>
						<div class=\"field\">
							".comboData("select * from dta_evaluasi where statusEvaluasi='t' order by namaEvaluasi","idEvaluasi","namaEvaluasi","inp[idEvaluasi]"," ",$r[idEvaluasi],"", "410px","chosen-select")."
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
		global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;
		if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div style=\"padding-bottom:10px;\">
			</div>
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<input type=\"hidden\" name=\"_submit\" value=\"t\">
			<div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"","", "All")."</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\"/></td>
				<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by namaData","kodeData","namaData","par[idKategori]","All",$par[idKategori],"","200px","chosen-select")."</td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>			
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>					
					<th>Pelatihan</th>					
					<th>Kategori</th>
					<th>Evaluasi</th>";
		if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody>";
				
		$filter = "where s1.idPelatihan is not null and s1.statusPelatihan='t'";
		if(!empty($par[tahunPelatihan]))
			$filter.= " and ".$par[tahunPelatihan]." between year(s1.mulaiPelatihan) and year(s1.selesaiPelatihan)";
		
		if(!empty($par[idKategori]))
			$filter.=" and s1.idKategori='".$par[idKategori]."'";
		
		if(!empty($par[filter]))		
		$filter.= " and (
			lower(s1.judulPelatihan) like '%".strtolower($par[filter])."%'
			or lower(s1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
			or lower(s1.namaData) like '%".strtolower($par[filter])."%'
			or lower(s2.namaEvaluasi) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select s1.*, s2.namaEvaluasi from (
			select t1.*, t2.namaData as namaKategori from plt_pelatihan t1 join mst_data t2 on (t1.idKategori=t2.kodeData)
		) as s1 left join dta_evaluasi s2 on (s1.idEvaluasi=s2.idEvaluasi) $filter order by s1.idPelatihan";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){						
			$no++;
				
			$text.="<tr>
					<td>$no.</td>			
					<td>$r[judulPelatihan]</td>									
					<td>$r[namaKategori]</td>
					<td>$r[namaEvaluasi]</td>";			
			if(isset($menuAccess[$s]["edit"])){
				$text.="<td align=\"center\">
					<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."',875,350);\"><span>Edit</span></a>
				</td>";
			}
			$text.="</tr>";							
		}	
		
		$text.="</tbody>
			</table>
			</div>";
		
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s])).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}	
			
		return $text;
	}	
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){	
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>