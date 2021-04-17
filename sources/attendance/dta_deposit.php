<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		

	function gPegawai(){
		global $db,$s,$inp,$par;
		$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$data["idPegawai"] = $r[id];
		$data["nikPegawai"] = $r[reg_no];
		$data["namaPegawai"] = strtoupper($r[name]);
		
		$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);

		
		return json_encode($data);
	}
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;				
		$sql="delete from dta_deposit where idDeposit='$par[idDeposit]'";
		db($sql);		
		echo "<script>window.location='?".getPar($par,"mode,idDeposit")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;
		repField();
		
		list($d,$m,$Y) = explode("/", $inp[tanggalDeposit]);
		$expiredDeposit = date("Y-m-d", dateAdd("m", 1, mktime(0,0,0,$m,$d,$Y)));
		
		$sql="update dta_deposit set idPegawai='$inp[idPegawai]', idTipe='$inp[idTipe]', tanggalDeposit='".setTanggal($inp[tanggalDeposit])."', expiredDeposit='".setTanggal($inp[expiredDeposit])."', keteranganDeposit='$inp[keteranganDeposit]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idDeposit='$par[idDeposit]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDeposit")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();				
		$idDeposit = getField("select idDeposit from dta_deposit order by idDeposit desc limit 1")+1;		
		
		$sql="insert into dta_deposit (idDeposit, idPegawai, idTipe, tanggalDeposit, expiredDeposit, keteranganDeposit, statusDeposit, createBy, createTime) values ('$idDeposit', '$inp[idPegawai]', '$inp[idTipe]', '".setTanggal($inp[tanggalDeposit])."', '".setTanggal($inp[expiredDeposit])."', '$inp[keteranganDeposit]', 't', '$cUsername', '".date("Y-m-d H:i:s")."')";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,idDeposit")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from dta_deposit where idDeposit='$par[idDeposit]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		if(empty($r[tanggalDeposit])) $r[tanggalDeposit] = date('Y-m-d');		
		if(empty($r[expiredDeposit])) $r[expiredDeposit] = date("Y-m-d", dateAdd("m", 1, mktime(0,0,0,date('m'),date('d'),date('Y'))));
		$sql_="select id as idPegawai, reg_no as nikPegawai, name as namaPegawai from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		setValidation("is_null","inp[idPegawai]","anda harus mengisi nik");
		setValidation("is_null","inp[idTipe]","anda harus mengisi tipe");
		setValidation("is_null","tanggalDeposit","anda harus mengisi tanggal");				
		$text = getValidation();
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top\">						
						<p>
							<label class=\"l-input-small\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$r_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."');\"/>
								<input type=\"button\" class=\"cancel radius2\" value=\"...\" onclick=\"openBox('popup.php?par[mode]=peg".getPar($par,"mode,filter")."',1000,525);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$r_[namaPegawai]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[57]."' order by urutanData","kodeData","namaData","inp[idTipe]"," ",$r[idTipe],"", "310px","chosen-select")."
							</div>
						</p>
					</td>
					<td width=\"55%\" style=\"vertical-align:top\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalDeposit\" name=\"inp[tanggalDeposit]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalDeposit])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Expired</label>
							<div class=\"field\">
								<input type=\"text\" id=\"expiredDeposit\" name=\"inp[expiredDeposit]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[expiredDeposit])."\" class=\"vsmallinput hasDatePicker\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[keteranganDeposit]\" name=\"inp[keteranganDeposit]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[keteranganDeposit]</textarea>
							</div>
						</p>
					</td>
					</tr>
					</table>
					
					</div>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>									
				</p>
			</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck;
		$par[idPegawai] = $cID;
		//if(empty($par[bulanData])) $par[bulanData]=date('m');
		if(empty($par[tahunData])) $par[tahunData]=date('Y');
		
		$cols = 8;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		$text = table($cols, array($cols-1, $cols));			
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: " . ($isUsePeriod ? "-3px" : "20px" ) . "; top: " . ($isUsePeriod ? "0px" : "10px" ) . "; vertical-align:top; padding-top:2px; width: 500px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Kerja : ".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[7]."' AND kodeData IN ( $areaCheck ) order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
									</td>
									<td style=\"vertical-align:top;\" id=\"bView\">
										<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'none';
										document.getElementById('bHide').style.display = 'table-cell';
										document.getElementById('dFilter').style.visibility = 'visible';							
										document.getElementById('fSet').style.height = 'auto';
										document.getElementById('fSet').style.padding = '10px';
										\">
									</td>
									<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
										<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 9px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'table-cell';
										document.getElementById('bHide').style.display = 'none';
										document.getElementById('dFilter').style.visibility = 'collapse';							
										document.getElementById('fSet').style.height = '0px';
										document.getElementById('fSet').style.padding = '0px';
										\">					
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
				</div>
				<div id=\"pos_l\" style=\"float:left;\">
					<p>					
						<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
						".comboMonth("mSearch", $par[bulanData], "", "", "t")." ".comboYear("tSearch", $par[tahunData])."
					</p>
				</div>	
			</form>			
			<div id=\"pos_r\">";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idDeposit")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\" style=\"vertical-align:middle;\">No.</th>
					<th width=\"75\" style=\"vertical-align:middle;\">Tanggal</th>
					<th width=\"100\" style=\"vertical-align:middle;\">NPP</th>
					<th style=\"min-width:150px; vertical-align:middle;\">Nama</th>					
					<th width=\"150\" style=\"vertical-align:middle;\">Tipe</th>					
					<th width=\"75\" style=\"vertical-align:middle;\">Expired</th>					
					<th width=\"50\" style=\"vertical-align:middle;\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\" style=\"vertical-align:middle;\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
		return $text;
	}		
		
	function pegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID;
		$par[idPegawai] = $cID;		
		$text.="<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Daftar Pegawai</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>
					<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\" />
					<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\" />
				</td>
				</tr>
			</table>
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Gedung</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where reg_no is not null";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		
		
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
		$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrDivisi["$r[div_id]"]."</td>
					<td align=\"center\">
						<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>
					</td>
				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>
		</div>";
		return $text;
	}
	
	function lData(){
		global $s,$par,$menuAccess,$cID, $areaCheck, $cGroup,$arrParameter;		
		$par[idPegawai] = $cID;		
		$today = date("Y-m-d");
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= "where year(t1.tanggalDeposit)='".$_GET['tSearch']."'";
		
		if(!empty($_GET['mSearch']))
		$sWhere.=" and month(t1.tanggalDeposit)='".$_GET['mSearch']."'";
		
		if(!empty($par[idLokasi]))
			$sWhere.= " and location='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and unit_id='".$par[unitId]."'";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
			
		$arrOrder = array(	
			"t1.tanggalDeposit",
			"t1.tanggalDeposit",
			"t2.reg_no",	
			"t2.name",			
			"t1.idTipe",
			"t1.expiredDeposit",
			"t1.statusDeposit",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select * from dta_deposit t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_deposit t1 left join dta_pegawai t2 on (t1.idPegawai=t2.id) $sWhere"),
			"aaData" => array(),
		);
		
		
		$arrTipe = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[57]."'");
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;

			$statusDeposit = $r[statusDeposit] == "t"? "<img src=\"styles/images/t.png\" title=\"Aktif\">" : "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">";
			$statusDeposit = $r[expiredDeposit] < $today ? "<img src=\"styles/images/f.png\" title=\"Tidak Aktif\">" : $statusDeposit;
			
			$controlDeposit="";			
			if(isset($menuAccess[$s]["edit"]))
			$controlDeposit.="<a href=\"#\" onclick=\"window.location='?par[mode]=edit&par[idDeposit]=$r[idDeposit]&par[tahunData]=' + document.getElementById('tSearch').value +'&par[filterData]=' + document.getElementById('fSearch').value +'".getPar($par,"mode,idDeposit,filterData,tahunData")."';\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"]))
			$controlDeposit.=" <a href=\"#".getPar($par,"mode,idDeposit,filterData,tahunData")."\" onclick=\"
			if(confirm('are you sure to delete data ?')){
				window.location='?par[mode]=del&par[idDeposit]=$r[idDeposit]&par[tahunData]=' + document.getElementById('tSearch').value +'&par[filterData]=' + document.getElementById('fSearch').value +'".getPar($par,"mode,idDeposit,filterData,tahunData")."';
			}
			\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",
				"<div align=\"center\">".getTanggal($r[tanggalDeposit])."</div>",
				"<div align=\"left\">".$r[reg_no]."</div>",
				"<div align=\"left\">".strtoupper($r[name])."</div>",				
				"<div align=\"left\">".$arrTipe["".$r[idTipe].""]."</div>",												
				"<div align=\"center\">".getTanggal($r[expiredDeposit])."</div>",
				"<div align=\"center\">".$statusDeposit."</div>",
				"<div align=\"center\">".$controlDeposit."</div>",
				
			);
		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "lst":
				$text=lData();
			break;
									
			case "get":
				$text = gPegawai();
			break;
			case "peg":
				$text = pegawai();
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