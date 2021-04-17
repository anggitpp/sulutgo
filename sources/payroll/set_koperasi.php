<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	

	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_koperasi where id='$par[id]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$sql="update pay_koperasi set idPangkat='$inp[idPangkat]', nilai='".setAngka($inp[nilai])."', keterangan='$inp[keterangan]', updateBy='$cUsername', updateDate='".date('Y-m-d H:i:s')."' where id='$par[id]'";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$id=getField("select id from pay_koperasi order by id desc limit 1")+1;		
		
		repField();
		$sql="insert into pay_koperasi (id, idPangkat,  nilai, keterangan, createBy, createDate) values ('$id', '$inp[idPangkat]', '".setAngka($inp[nilai])."', '$inp[keterangan]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_koperasi where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusTunjangan] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idPangkat]","anda harus mengisi pangkat");
		
		setValidation("is_null","inp[nilai]","anda harus mengisi nilai kantor pusat");
		
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
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[11]."' order by urutanData","kodeData","namaData","inp[idPangkat]"," ",$r[idPangkat],"", "360px")."
						</div>
					</p>
							
					<p>
						<label class=\"l-input-small\">Nilai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[nilai]\" name=\"inp[nilai]\"  value=\"".getAngka($r[nilai])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keterangan]\" name=\"inp[keterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:55px; width:300px;\">$r[keterangan]</textarea>
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,id")."',725,400);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th  width=\"20\">No.</th>
					<th >Pangkat</th>
					
					<th width=\"150\">Nilai</th>
					";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Control</th>";
		$text.="</tr>
				
					
					
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(t1.keteranganTunjangan) like '%".strtolower($par[filter])."%'
			or lower(t2.namaData) like '%".strtolower($par[filter])."%'
			
		)";
		
		$sql="select t1.*, t2.namaData as namaPangkat from pay_koperasi t1 join mst_data t2 on (t1.idPangkat=t2.kodeData) $filter order by t2.namaData";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>$r[namaPangkat]</td>
									
					<td align=\"right\">".getAngka($r[nilai])."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode,id")."',725,400);\"><span>Edit</span></a>";
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