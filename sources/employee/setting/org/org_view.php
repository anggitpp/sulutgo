<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	
	function chk(){
		global $db,$inp,$par;
		if(getField("select kodeData from mst_data where kodeInduk='$par[kodeData]' and statusData='t'"))
		return "sorry, data has been use";
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;		
		$sql="update mst_data set kodeInduk='".getField("select kodeInduk from mst_data where kodeData='$par[kodeData]'")."' where kodeInduk='$par[kodeData]'";
		db($sql);
		
		$sql="delete from mst_data where kodeData='$par[kodeData]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,kodeData,kodeInduk")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername;					
		repField();
		$kodeCategory = (empty($par[kodeInduk]) || $inp[tipeData] == "t") ? "X03" : getField("select concat('X0', right(kodeCategory,1)+1) from mst_data where kodeData='$par[kodeInduk]'");
		
		$sql="update mst_data set kodeCategory='$kodeCategory', namaData='$inp[namaData]', keteranganData='$inp[keteranganData]', urutanData='".setAngka($inp[urutanData])."', statusData='$inp[statusData]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where kodeData='$par[kodeData]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$acc,$arrAkses,$fFile,$cUsername;				
		$kodeData=getField("select kodeData from mst_data order by kodeData desc limit 1")+1;		
		$kodeCategory = (empty($par[kodeInduk]) || $inp[tipeData] == "t") ? "X03" : getField("select concat('X0', right(kodeCategory,1)+1) from mst_data where kodeData='$par[kodeInduk]'");
		
		repField();		
		$sql="insert into mst_data (kodeData, kodeInduk, kodeCategory, namaData, keteranganData, urutanData, statusData, createBy, createTime) values ('$kodeData', '$par[kodeInduk]', '$kodeCategory', '$inp[namaData]', '$inp[keteranganData]', '".setAngka($inp[urutanData])."', '$inp[statusData]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>closeBox();reloadPage();</script>";
	}		
	
	function form(){
		global $db,$s,$inp,$par,$fFile,$arrSite,$arrAkses,$arrTitle,$menuAccess;
		$kodeCategory = (empty($par[kodeInduk]) || $inp[tipeData] == "t") ? "X03" : getField("select concat('X0', right(kodeCategory,1)+1) from mst_data where kodeData='$par[kodeInduk]'");
		
		$sql="select * from mst_data where kodeData='$par[kodeData]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
				
		if(empty($r[kodeInduk])) $r[kodeInduk] = $par[kodeInduk];
		if(empty($r[urutanData])) $r[urutanData] = getField("select urutanData from mst_data where kodeInduk='$par[kodeInduk]' and kodeCategory='".$kodeCategory."' order by urutanData desc limit 1") + 1;
				
		$false =  $r[statusData] == "f" ? "checked=\"checked\"" : "";
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		$ya =  ($r[kodeCategory] == "X03" || empty($r[kodeInduk])) ? "checked=\"checked\"" : "";
		$tidak =  empty($ya) ? "checked=\"checked\"" : "";
		
		setValidation("is_null","inp[namaData]","you must fill menu");
		setValidation("is_null","inp[urutanData]","you must fill order");
		$text = getValidation();	

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">";
				
			if(!empty($r[kodeInduk]))
			$text.="<p>
						<label class=\"l-input-small\">Parent</label>
						<span class=\"field\">
							".getField("select namaData from mst_data where kodeData='$r[kodeInduk]'")."&nbsp;
						</span>
					</p>";				
			$text.="<p>
						<label class=\"l-input-small\">Struktur</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaData]\" name=\"inp[namaData]\"  value=\"$r[namaData]\" class=\"mediuminput\" maxlength=\"150\"/>
						</div>
					</p>										
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganData]\" name=\"inp[keteranganData]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganData]</textarea>
						</div>
					</p>													
					<p>
						<label class=\"l-input-small\">Order</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[urutanData]\" name=\"inp[urutanData]\"  value=\"".getAngka($r[urutanData])."\" class=\"mediuminput\" style=\"width:50px; text-align:right;\" onkeyup=\"cekAngka(this);\" />
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusData]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusData]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>							
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Struktur Baru</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"ya\" name=\"inp[tipeData]\" value=\"t\" $ya /> <span class=\"sradio\">Ya</span>
							<input type=\"radio\" id=\"tidak\" name=\"inp[tipeData]\" value=\"f\" $tidak/> <span class=\"sradio\">Tidak</span>				
						</div>
					</p>
					<p>
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$kodeCategory;				
	    $text = table(0, array(), "lst", "false");
				
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>";
				if(getField("select count(*) from mst_data where statusData='t' and kodeCategory='X03'"))
		$text.=" ".comboData("select * from mst_data where statusData='t' and kodeCategory='X03' order by kodeData","kodeData","namaData","par[kodeInduk]","",$par[kodeInduk],"onchange=\"window.location='?par[kodeInduk]=' + document.getElementById('par[kodeInduk]').value + '".getPar($par,"kodeInduk")."';\"", "250px;")."";
		$text.="</p>
			</div>
			<div id=\"pos_r\">";		
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" style=\"margin-left:5px;\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,kodeData,kodeInduk")."',825,525);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Data</th>
					<th width=\"50\">Order</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["add"]) || isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"75\">Control</th>";
		$text.="</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
		return $text;
	}	
	
	function row($arrData, $kodeInduk, $no, $levelData, $levelMax){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrColor,$json;	
  		if(is_array($arrData[$kodeInduk])){
			while(list($kodeData,$r)=each($arrData[$kodeInduk])){
				$no++;
				$statusData = $r[statusData] == "t"?
				"<img src=\"styles/images/t.png\" title='Active'>":
				"<img src=\"styles/images/f.png\" title='Not Active'>";				
				$paddingData = 30 + (($levelData - 1) * 15)."px";
				
				$controData="";
		
				if(isset($menuAccess[$s]["add"]) && $levelData < $levelMax)
				$controData.="<a href=\"#Add\" title=\"Tambah Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r[kodeData]".getPar($par,"mode,kodeInduk")."',825,525);\"><span>Add</span></a>";
		
				if(isset($menuAccess[$s]["edit"]))
				$controData.=" <a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeData]=$r[kodeData]".getPar($par,"mode,kodeData")."',825,525);\"><span>Edit</span></a>";
				
				if(isset($menuAccess[$s]["delete"]))
				$controData.=" <a href=\"#Delete\" onclick=\"del('$r[kodeData]','".getPar($par,"mode,kodeData")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				
				$data=array(
					"<div align=\"center\">".$no.".</div>",				
					"<div align=\"left\" style=\"padding-left:$paddingData;\">".$r[namaData]."</div>",
					"<div align=\"center\"><font color=\"".$arrColor[$levelData]."\">".getAngka($r[urutanData])."</font></div>",
					"<div align=\"center\">".$statusData."</div>",								
					"<div align=\"center\">".$controData."</div>",
				);
				$json['aaData'][]=$data;
				
				if($r[kodeCategory] != "X03" || empty($r[kodeInduk]))
				list($json, $no)=row($arrData, $kodeData, $no, ($levelData + 1), $levelMax);		
			}
		}
		return array($json, $no);
	}
	
	function lData(){
		global $s,$par,$menuAccess,$json;
		if(empty($par[kodeInduk])) $par[kodeInduk] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='X03' order by kodeData");		
		$levelMax = 5;
		
		$sWhere= " where kodeCategory in ('X03','X04', 'X05', 'X06', 'X07', 'X08')";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (	
				lower(namaData) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
			
		$sql="select * from mst_data $sWhere order by kodeInduk, urutanData";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from mst_data $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$arrData["$r[kodeInduk]"]["$r[kodeData]"] = $r;
		}
		
		$sql_="select * from mst_data $sWhere and kodeData='".$par[kodeInduk]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		if(mysql_num_rows($res_)){


			$no++;
			$statusData = $r_[statusData] == "t"?
			"<img src=\"styles/images/t.png\" title='Active'>":
			"<img src=\"styles/images/f.png\" title='Not Active'>";			
			
			$controData="";
		
			if(isset($menuAccess[$s]["add"]) && $r_[levelData] < $levelMax)
			$controData.="<a href=\"#Add\" title=\"Tambah Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r_[kodeData]".getPar($par,"mode,kodeInduk")."',825,525);\"><span>Add</span></a>";
	
			if(isset($menuAccess[$s]["edit"]))
			$controData.=" <a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeData]=$r_[kodeData]".getPar($par,"mode,kodeData")."',825,525);\"><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"]))
			$controData.=" <a href=\"#Delete\" onclick=\"del('$r_[kodeData]','".getPar($par,"mode,kodeData")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"left\" style=\"padding-left:15px;\">".$r_[namaData]."</div>",
				"<div align=\"center\"><font color=\"".$arrColor[$levelData]."\">".getAngka($r_[urutanData])."$r_[levelData]</font> </div>",
				"<div align=\"center\">".$statusData."</div>",								
				"<div align=\"center\">".$controData."</div>",
			);
			$json['aaData'][]=$data;			
		}
		
		$levelData =2;
		if(is_array($arrData["$par[kodeInduk]"])){
			while(list($kodeData,$r)=each($arrData["$par[kodeInduk]"])){
				$no++;
				$statusData = $r[statusData] == "t"?
				"<img src=\"styles/images/t.png\" title='Active'>":
				"<img src=\"styles/images/f.png\" title='Not Active'>";				
				
				$controData="";
		
				if(isset($menuAccess[$s]["add"]) && $levelData < $levelMax)
				$controData.="<a href=\"#Add\" title=\"Tambah Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=add&par[kodeInduk]=$r[kodeData]".getPar($par,"mode,kodeInduk")."',825,525);\"><span>Add</span></a>";
		
				if(isset($menuAccess[$s]["edit"]))
				$controData.=" <a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeData]=$r[kodeData]".getPar($par,"mode,kodeData")."',825,525);\"><span>Edit</span></a>";
				
				if(isset($menuAccess[$s]["delete"]))
				$controData.=" <a href=\"#Delete\" onclick=\"del('$r[kodeData]','".getPar($par,"mode,kodeData")."')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				
				$data=array(
					"<div align=\"center\">".$no.".</div>",				
					"<div align=\"left\" style=\"padding-left:30px;\">".$r[namaData]."</div>",
					"<div align=\"center\"><font color=\"".$arrColor[$levelData]."\">".getAngka($r[urutanData])."</font></div>",
					"<div align=\"center\">".$statusData."</div>",								
					"<div align=\"center\">".$controData."</div>",
				);
				$json['aaData'][]=$data;
				
				if($r[kodeCategory] != "X03" || empty($r[kodeInduk]))
				list($json, $no)=row($arrData, $kodeData, $no, ($levelData + 1), $levelMax);								
			}
		}	
		
		
		return json_encode($json);
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;		
		switch($par[mode]){
			case "lst":
				$text=lData();
			break;
			
			case "chk":
				$text = chk();
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