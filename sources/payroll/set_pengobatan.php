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
		
		$data["namaJabatan"] = $r_[pos_name];
		$data["namaDivisi"] = getField("select namaData from mst_data where kodeData='".$r_[div_id]."'");
		
		$data["idPengganti"] = $r_[replacement_id];
		$data["idAtasan"] = $r_[leader_id];
		
		list($data[nikPengganti], $data[namaPengganti]) = explode("\t", getField("select concat(reg_no, '\t', name) from emp where id='".$r_[replacement_id]."'"));
		list($data[nikAtasan], $data[namaAtasan]) = explode("\t", getField("select concat(reg_no, '\t', name) from emp where id='".$r_[leader_id]."'"));
		
		return json_encode($data);
	}	
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;
		$sql="delete from pay_pengobatan where id='$par[id]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
	}
	
	function ubah(){
		global $db,$s,$inp,$par,$cUsername;		
		repField();
		$inp[nilai] = setAngka($inp[nilai]);
		$sql="update pay_pengobatan set idPegawai='$inp[idPegawai]', nilai='$inp[nilai]', keterangan='$inp[keterangan]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where id='$par[id]'";
		db($sql);
		echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$par[id]".getPar($par,"mode,id")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername;		
		$id=getField("select id from pay_pengobatan order by id desc limit 1")+1;		
		$inp[nilai] = setAngka($inp[nilai]);
		repField();
		$sql="insert into pay_pengobatan (id, idPegawai, nilai, keterangan, createBy, createTime) values ('$id', '$inp[idPegawai]', '$inp[nilai]', '$inp[keterangan]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?par[mode]=edit&par[id]=$id".getPar($par,"mode,id")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from pay_pengobatan where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$false =  $r[statusPengobatan] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";	
		
		setValidation("is_null","inp[idPangkat]","anda harus mengisi pangkat");
		setValidation("is_null","inp[idGrade]","anda harus mengisi grade");
		setValidation("is_null","inp[lajangPengobatan]","anda harus mengisi nilai lajang");
		setValidation("is_null","inp[keluargaPengobatan]","anda harus mengisi nilai keluarga");
		$text = getValidation();


		$sql_="select
		id as idPegawai,
		reg_no as nikPegawai,
		name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");

		$text.="
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
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
		<label class=\"l-input-small\">Jabatan</label>
		<div class=\"field\">								
		<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$r_[namaJabatan]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
		</div>
		</p>
		<p>
		<label class=\"l-input-small\">Divisi</label>
		<div class=\"field\">								
		<input type=\"text\" id=\"inp[namaDivisi]\" name=\"inp[namaDivisi]\"  value=\"$r_[namaDivisi]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
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
					<p style=\"position:absolute;right:20px;top:10px;\">
						<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
						<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,id")."';\"/>
					</p>
				
			</form>	
			</div>
			";
		return $text;
	}

	function pegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID,$cGroup, $areaCheck;
		$par[idPegawai] = $cID;		
		$text.="
		<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Daftar Pegawai</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
		</div>    
		" . empLocHeader() . "
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
		<th style=\"min-width:150px;\">Divisi</th>
		<th width=\"50\">Kontrol</th>
		</tr>
		</thead>
		<tbody>";
		if($cGroup != 1)
		$filter = "where reg_no is not null and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')";
		else
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
		
		$filter .= " AND t2.location IN ($areaCheck)";
		
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
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,id")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th  width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Jabatan</th>
					<th width=\"100\">Divisi</th>
					<th width=\"100\">Nilai</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th  width=\"50\">Control</th>";
				$text.="</tr>
				
			</thead>
			<tbody>";
		
		if(!empty($par[filter]))			
		$filter.="where (			
			lower(t2.name) like '%".strtolower($par[filter])."%'
		)";
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$sql="select *,t1.id from pay_pengobatan t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter order by t1.id";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;					
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>					
					<td>".strtoupper($r[pos_name])."</td>
					<td>".$arrMaster[$r[div_id]]."</td>
					<td align=\"right\">".getAngka($r[nilai])."</td>";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\">";				
				if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode,id")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
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