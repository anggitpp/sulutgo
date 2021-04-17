<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			
$fFile = "files/kacamata/";

function gNomor(){
	global $db,$s,$inp,$par;
	$prefix="RKC";
	$date=empty($_GET[rmb_date]) ? $inp[rmb_date] : $_GET[rmb_date];
	$date=empty($date) ? date('d/m/Y') : $date;
	list($tanggal, $bulan, $tahun) = explode("/", $date);

	$nomor=getField("select rmb_no from emp_rmb where month(rmb_date)='$bulan' and year(rmb_date)='$tahun' and rmb_no like '%$prefix%' order by rmb_no desc limit 1");
	list($count) = explode("/", $nomor);
	return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
}

function gPegawai(){
	global $db,$s,$inp,$par;
	$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$data["parent_id"] = $r[id];
	$data["nikPegawai"] = $r[reg_no];
	$data["namaPegawai"] = strtoupper($r[name]);

	$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$data["namaJabatan"] = getField("select namaData from mst_data where kodeData='".$r_[rank]."'");
	$data["namaDivisi"] = getField("select namaData from mst_data where kodeData='".$r_[div_id]."'");

	return json_encode($data);
}

function gPlafon(){
	global $db,$s,$inp,$par;

	$idJabatan = getField("select rank from dta_pegawai where id = '$par[parent_id]'");

	list($rmb_day, $rmb_month, $rmb_year) = explode("/", $par[rmb_date]);

	$sql="select * from pay_kacamata where idKategori='".$par[rmb_cat]."' and idTipe='".$par[rmb_type]."' and idJabatan = '$idJabatan'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$data[rmb_limit] = getAngka($r[nilaiKacamata]);

	$rmb_val = getField("select sum(rmb_val) from emp_rmb where parent_id='".$par[parent_id]."' and rmb_cat='".$par[rmb_cat]."' and rmb_type='".$par[rmb_type]."' and year(rmb_date)='".$rmb_year."' and status='1' and rmb_jenis='m'");

	$data[rmb_balance] = getAngka($r[nilaiKacamata] - $rmb_val);

	return json_encode($data);
}

function upload($parent_id){
	global $db,$s,$inp,$par,$fFile;		
	$fileUpload = $_FILES["fileKacamata"]["tmp_name"];
	$fileUpload_name = $_FILES["fileKacamata"]["name"];
	if(($fileUpload!="") and ($fileUpload!="none")){	
		fileUpload($fileUpload,$fileUpload_name,$fFile);			
		$fileKacamata = "doc-".$parent_id.".".getExtension($fileUpload_name);
		fileRename($fFile, $fileUpload_name, $fileKacamata);			
	}		

	return $fileKacamata;
}

function hapusFile(){
	global $db,$s,$inp,$par,$fFile,$cUsername;					
	$filename = getField("select filename from emp_rmb_files where parent_id='$par[id]'");
	if(file_exists($fFile.$filename) and $filename!="")unlink($fFile.$filename);

	$sql="delete from emp_rmb_files where parent_id='$par[id]'";
	db($sql);		
	echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
}

function hapus(){
	global $db,$s,$inp,$par,$cUsername;				
	$sql="delete from emp_rmb where id='$par[id]'";
	db($sql);	

	$sql="delete from emp_rmb_files where parent_id='$par[id]'";
	db($sql);

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}

function approve(){
	global $db,$s,$inp,$par,$cUsername;
	repField();				
	$filename=upload($par[id]);

	$sql="update emp_rmb set parent_id='$inp[parent_id]', rmb_no='$inp[rmb_no]', rmb_date='".setTanggal($inp[rmb_date])."', rmb_cat='$inp[rmb_cat]', rmb_type='$inp[rmb_type]', rmb_val='".setAngka($inp[rmb_val])."', remark='$inp[remark]', apr_remark='$inp[apr_remark]', status='$inp[status]', apr_by='$cUsername', apr_date='".date('Y-m-d')."' where id='$par[id]'";
	db($sql);

	if(!empty($filename)){
		$sql="insert into emp_rmb_files (parent_id, filename, upd_by, upd_date) values ('$par[id]', '$filename', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
	}

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}

function ubah(){
	global $db,$s,$inp,$par,$cUsername;
	repField();				
	$filename=upload($par[id]);

	$sql="update emp_rmb set parent_id='$inp[parent_id]', rmb_no='$inp[rmb_no]', rmb_date='".setTanggal($inp[rmb_date])."', rmb_cat='$inp[rmb_cat]', rmb_type='$inp[rmb_type]', rmb_val='".setAngka($inp[rmb_val])."', remark='$inp[remark]', upd_by='$cUsername', upd_date='".date('Y-m-d H:i:s')."' where id='$par[id]'";
	db($sql);

	if(!empty($filename)){
		$sql="insert into emp_rmb_files (parent_id, filename, upd_by, upd_date) values ('$par[id]', '$filename', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
	}

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}

function tambah(){
	global $db,$s,$inp,$par,$cUsername;				
	repField();			
	$cek = getField("select id from emp_rmb where rmb_date ='".setTanggal($inp[rmb_date])."' AND parent_id = '$inp[parent_id]' AND status !='1'");
		if($cek){
			echo "<script>alert('TAMBAH DATA GAGAL, DATA PEGAWAI UNTUK HARI INI SUDAH ADA');</script>";
		}else{		
	$id = getField("select id from emp_rmb order by id desc limit 1")+1;		
	$filename=upload($id);

	$sql="insert into emp_rmb (id, parent_id, rmb_jenis, rmb_no, rmb_date, rmb_cat, rmb_type, rmb_val, remark, status, cre_by, cre_date) values ('$id', '$inp[parent_id]', 'm', '$inp[rmb_no]', '".setTanggal($inp[rmb_date])."', '$inp[rmb_cat]', '$inp[rmb_type]', '".setAngka($inp[rmb_val])."', '$inp[remark]', '0', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);

	if(!empty($filename)){
		$sql="insert into emp_rmb_files (parent_id, filename, cre_by, cre_date) values ('$id', '$filename', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
	}

	echo "<script>alert('TAMBAH DATA BERHASIL');</script>";

	}

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}

function form(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername;

	$sql="select * from emp_rmb where id='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					

	$true = $r[status] == "1" ? "checked=\"checked\"" : "";
	$false = $r[status] == "2" ? "checked=\"checked\"" : "";
	$revisi = $r[status] == "3" ? "checked=\"checked\"" : "";

	if(empty($r[rmb_no])) $r[rmb_no] = gNomor();
	if(empty($r[rmb_date])) $r[rmb_date] = date('Y-m-d');		

	setValidation("is_null","inp[rmb_no]","anda harus mengisi nomor");
	setValidation("is_null","inp[parent_id]","anda harus mengisi nik");
	setValidation("is_null","rmb_date","anda harus mengisi tanggal");
	setValidation("is_null","inp[rmb_cat]","anda harus mengisi kategori");
	setValidation("is_null","inp[rmb_type]","anda harus mengisi tipe");
	setValidation("is_null","inp[rmb_val]","anda harus mengisi nilai");
	$text = getValidation();

	$sql_="select
	id as parent_id,
	reg_no as nikPegawai,
	name as namaPegawai
	from emp where id='".$r[parent_id]."'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

	$sql__="select * from emp_phist where parent_id='".$r_[parent_id]."' and status='1'";
	$res__=db($sql__);
	$r__=mysql_fetch_array($res__);
	$r_[namaJabatan] = $r__[pos_name];
	$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");

	list($rmb_year) = explode("-", $r[rmb_date]);
	$sql__="select * from pay_kacamata where idKategori='".$r[rmb_cat]."' and idTipe='".$r[rmb_type]."'";
	$res__=db($sql__);
	$r__=mysql_fetch_array($res__);
	
	$r[rmb_limit] = $r__[nilaiKacamata];						
	$r[rmb_balance] = $r__[nilaiKacamata] - getField("select sum(rmb_val) from emp_rmb where parent_id='".$r[parent_id]."' and rmb_cat='".$r[rmb_cat]."' and rmb_type='".$r[rmb_type]."' and year(rmb_date)='".$rmb_year."' and rmb_date<='".$r[rmb_date]."' and status='1' and rmb_jenis='m' and id!='".$par[id]."'");

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
	".getBread(ucwords($par[mode]." data"))."								
</div>
<div class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return save('".getPar($par, "mode")."')\" enctype=\"multipart/form-data\">	
		<div id=\"general\" style=\"margin-top:20px;\">
			<table width=\"100%\">
				<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[rmb_no]\" name=\"inp[rmb_no]\"  value=\"$r[rmb_no]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[parent_id]\" name=\"inp[parent_id]\"  value=\"$r[parent_id]\" readonly=\"readonly\"/>
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
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"rmb_date\" name=\"inp[rmb_date]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[rmb_date])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
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
					</td>
				</tr>
			</table>
			<div class=\"widgetbox\">
				<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM KACAMATA</h3></div>
			</div>
			<table width=\"100%\">
				<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Kategori</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[17]."' order by urutanData","kodeData","namaData","inp[rmb_cat]"," ",$r[rmb_cat],"onchange=\"getPlafon('".getPar($par,"mode,parent_id,rmb_date,rmb_cat,rmb_type")."');\"", "310px")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<div class=\"field\">
								".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[18]."' order by urutanData","kodeData","namaData","inp[rmb_type]"," ",$r[rmb_type],"onchange=\"getPlafon('".getPar($par,"mode,parent_id,rmb_date,rmb_cat,rmb_type")."');\"", "310px")."
							</div>
						</p>			
						<p>
							<label class=\"l-input-small\">Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[rmb_val]\" name=\"inp[rmb_val]\"  value=\"".getAngka($r[rmb_val])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" onkeyup=\"cekAngka(this);\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Document</label>
							<div class=\"field\">";
								$filename = getField("select filename from emp_rmb_files where parent_id='$par[id]'");
								$text.=empty($filename)?
								"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
								<div class=\"fakeupload\" style=\"width:300px;\">
									<input type=\"file\" id=\"fileKacamata\" name=\"fileKacamata\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
								</div>":
								"<a href=\"download.php?d=kacamata&f=$par[id]\"><img src=\"".getIcon($fFile."/".$filename)."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
								<input type=\"file\" id=\"fileKacamata\" name=\"fileKacamata\" style=\"display:none;\" />
								<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
								$text.="</div>
							</p>
						</td>
						<td width=\"55%\" style=\"vertical-align:top;\">
							<p>
								<label class=\"l-input-small\">Balance</label>
								<div class=\"field\">								
									<input type=\"text\" id=\"inp[rmb_balance]\" name=\"inp[rmb_balance]\" value=\"".getAngka($r[rmb_balance])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly=\"readonly\" />
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Limit</label>
								<div class=\"field\">								
									<input type=\"text\" id=\"inp[rmb_limit]\" name=\"inp[rmb_limit]\" value=\"".getAngka($r[rmb_limit])."\" class=\"mediuminput\" style=\"text-align:right; width:120px;\" readonly=\"readonly\" />
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<div class=\"field\">
									<textarea id=\"inp[remark]\" name=\"inp[remark]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:415px;\">$r[remark]</textarea>
								</div>
							</p>						
						</td>
					</tr>
				</table>";

				if($par[mode] == "app"){
					$apr_by = empty($r[apr_by]) ? $cUsername : $r[apr_by];
					list($r[apr_date]) = explode(" ",$r[apr_date]);
					if(empty($r[apr_date]) || $r[apr_date] == "0000-00-00") $r[apr_date] = date('Y-m-d');
					$text.="<div class=\"widgetbox\">
					<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL</h3></div>
				</div>			
				<table width=\"100%\">
					<tr>
						<td width=\"45%\">
							<p>
								<label class=\"l-input-small\">Tanggal</label>
								<div class=\"field\">
									<input type=\"text\" id=\"apr_date\" name=\"inp[apr_date]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[apr_date])."\" class=\"vsmallinput hasDatePicker\"/>
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Nama</label>
								<div class=\"field\">								
									<input type=\"text\" id=\"inp[apr_by]\" name=\"inp[apr_by]\"  value=\"".getField("select namaUser from ".$db['setting'].".app_user where username='$apr_by' ")."\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Status</label>
								<div class=\"fradio\">
									<input type=\"radio\" id=\"true\" name=\"inp[status]\" value=\"1\" $true /> <span class=\"sradio\">Disetujui</span>
									<input type=\"radio\" id=\"false\" name=\"inp[status]\" value=\"2\" $false /> <span class=\"sradio\">Ditolak</span>
									
								</div>
							</p>
							<p>
								<label class=\"l-input-small\">Keterangan</label>
								<div class=\"field\">
									<textarea id=\"inp[apr_remark]\" name=\"inp[apr_remark]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[apr_remark]</textarea>
								</div>
							</p>
						</td>
						<td width=\"55%\">&nbsp;</td>
					</tr>
				</table>";
			}
			$text.="</div>
			<p>					
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,parent_id")."';\"/>					
			</p>
		</form>";
		return $text;
	}

	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID,$areaCheck, $cGroup;
		$par[idPegawai] = $cID;				
		if(empty($par[tahun])) $par[tahun]=date('Y');				
		$text="
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">".$arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")]." - ".$arrTitle[$s]."</h1>
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
							<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
							<td>".comboYear("par[tahun]", $par[tahun])."</td>
							<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
						</tr>
					</table>
				</div>
				<div id=\"pos_r\">";
					if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,id")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
					$text.="</div>
				</form>
				<br clear=\"all\" />
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
					<thead>
						<tr>
							<th rowspan=\"2\" width=\"20\">No.</th>	
							<th rowspan=\"2\" style=\"min-width:100px;\">Nama</th>
							<th rowspan=\"2\" width=\"100\">NPP</th>
							<th rowspan=\"2\" style=\"min-width:100px;\">Nomor</th>
							<th rowspan=\"2\" width=\"75\">Tanggal</th>
							<th rowspan=\"2\" style=\"min-width:100px;\">Kategori</th>
							<th rowspan=\"2\" width=\"100\">Nilai</th>
							<th colspan=\"2\" width=\"100\">Approval</th>
							<th colspan=\"2\" width=\"100\">Bayar</th>
							<th rowspan=\"2\" width=\"50\">Detail</th>";
							if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th rowspan=\"2\" width=\"50\">Kontrol</th>";
							$text.="</tr>
							<tr>
								<th width=\"50\">Atasan</th>
								<th width=\"50\">SDM</th>
								<th width=\"50\">Bayar</th>
								<th width=\"50\">Cetak</th>
							</tr>
						</thead>
						<tbody>";

							$filter = "where year(t1.rmb_date)='$par[tahun]' and rmb_jenis='m' ".($cGroup != "1" && $cGroup != "20" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");		
							if(!empty($par[filter]))
								$filter.= " and (
							lower(t1.rmb_no) like '%".strtolower($par[filter])."%'
							or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
							or lower(t2.name) like '%".strtolower($par[filter])."%'
							)";

							$arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[17]."'");
							$arrTipe = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[18]."'");

							$filter .= " AND t2.location IN ($areaCheck)";

							$sql="select t1.*, t2.name, t2.reg_no from emp_rmb t1 left join dta_pegawai t2 on (t1.parent_id=t2.id) $filter order by t1.rmb_no";
							$res=db($sql);
							while($r=mysql_fetch_array($res)){			
								$no++;

								if(empty($r[status])) $r[status] = 0;
								$status = $r[status] == "1"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
								$status = $r[status] == "2"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $status;
								$status = $r[status] == "3"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $status;

								if(empty($r[sdm_status])) $r[sdm_status] = 0;
								$sdm_status = $r[sdm_status] == "1"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
								$sdm_status = $r[sdm_status] == "2"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdm_status;
								$sdm_status = $r[sdm_status] == "3"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdm_status;

								if(empty($r[pay_status])) $r[pay_status] = 0;
								$pay_status = $r[pay_status] == "1"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
								$pay_status = $r[pay_status] == "2"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $pay_status;			
								$pay_status = $r[pay_status] == "3"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $pay_status;

								$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[id]=$r[id]".getPar($par,"mode,id") : "#";

								$text.="<tr>
								<td>$no.</td>
								<td>".strtoupper($r[name])."</td>
								<td>$r[reg_no]</td>
								<td>$r[rmb_no]</td>					
								<td align=\"center\">".getTanggal($r[rmb_date])."</td>
								<td>".$arrKategori["$r[rmb_cat]"]." - ".$arrTipe["$r[rmb_type]"]."</td>
								<td align=\"right\">".getAngka($r[rmb_val])."</td>
								<td align=\"center\"><a href=\"".$persetujuanLink."\">$status</a></td>
								<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detSdm&par[id]=$r[id]".getPar($par,"mode,id")."',750,425);\" >$sdm_status</a></td>
								<td align=\"center\"><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=detPay&par[id]=$r[id]".getPar($par,"mode,id")."',750,425);\" >$pay_status</a></td>
								<td align=\"center\">";
									if($r[pay_status] == 1)
										$text.="<a href=\"ajax.php?par[mode]=print&par[id]=$r[id]".getPar($par,"mode,id")."\" title=\"Print Data\" class=\"print\" target=\"print\"><span>Print</span></a>";
									$text.="&nbsp;</td>
									<td align=\"center\">
										<a href=\"?par[mode]=det&par[id]=$r[id]".getPar($par,"mode,id")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>
									</td>";
									if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
										$text.="<td align=\"center\">";				
										if(isset($menuAccess[$s]["edit"])&&$r[status]!='1') $text.="<a href=\"?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode,id")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
										if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
										$text.="</td>";
									}
									$text.="</tr>";				
								}	

								$text.="</tbody>
							</table>
						</div><iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";

						if($par[mode] == "print") pdf();
						return $text;
					}		

					function pdf(){
						global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
						require_once 'plugins/PHPPdf.php';

						$sql_="select * from pay_profile limit 1";
						$res_=db($sql_);
						$r_=mysql_fetch_array($res_);

						$sql="select * from emp_rmb t1 join dta_pegawai t2 on (t1.parent_id=t2.id) where t1.id='".$par[id]."'";
						$res=db($sql);
						$r=mysql_fetch_array($res);


						$pdf = new PDF('P','mm','A4');
						$pdf->AddPage();
						$pdf->SetLeftMargin(5);

						$pdf->Ln();		
						$pdf->SetFont('Arial','B',11);					
						$pdf->Cell(100,7,'BANTUAN KACAMATA',0,0,'L');		
						$pdf->Ln();		

						$pdf->Cell(200,1,'','B');
						$pdf->Ln(1.25);
						$pdf->Cell(200,1,'','T');
						$pdf->Ln();
						$pdf->Cell(200,1,'','T');
						$pdf->Ln(5);

						$pdf->SetFont('Arial','','8');
						$pdf->SetWidths(array(90, 10, 30, 5, 65));	
						$pdf->SetAligns(array('L','L','L','L','L'));
						$pdf->Row(array($r_[namaProfile]."\tb","","Tanggal\tb",":",getTanggal($r[pay_date],"t")), false);
						$pdf->Row(array($r_[alamatProfile],"","Nomor\tb",":",$r[rmb_no]), false);

						$pdf->Ln();
						$pdf->SetWidths(array(5, 20, 5, 60, 10, 30, 5, 60, 5));
						$pdf->SetAligns(array('L','L','L','L','L','L','L'));
						$pdf->Row(array("\tf", "NAMA\tf",":\tf",$r[name]."\tf","\tf","KATEGORI\tf",":\tf",getField("select namaData from mst_data where kodeData='".$r[rmb_cat]."'")."\tf", "\tf"));
						$pdf->Row(array("\tf", "NPP\tf",":\tf",$r[reg_no]."\tf","\tf","NILAI\tf",":\tf","Rp. ".getAngka($r[rmb_val])."\tf", "\tf"));

						$pdf->SetWidths(array(5, 20, 5, 165, 5));
						$pdf->SetAligns(array('L','L','L','L','L'));
						$pdf->Row(array("\tf", "TERBILANG\tf",":\tf",terbilang($r[rmb_val])." Rupiah\tf", "\tf"));

						$pdf->Cell(200,1,'','T');		
						$pdf->Ln(5);


						$pdf->SetWidths(array(100, 50, 50));
						$pdf->SetAligns(array('L','C','C'));
						$pdf->Row(array("KETERANGAN\tb","PENERIMA\tb","KASIR\tb"));
						$pdf->Row(array($r[pay_remark], "\n\n(".$r[name].")", "\n\n(".getField("select namaUser from ".$db['setting'].".app_user where username='".$r[pay_by]."'").")"));

						$pdf->AutoPrint(true);
						$pdf->Output();
					}

					function detailApproval(){
						global $db,$s,$inp,$par,$arrTitle,$menuAccess;

						$sql="select * from emp_rmb where id='$par[id]'";
						$res=db($sql);
						$r=mysql_fetch_array($res);			

						$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
						$persetujuanField = $par[mode] == "detSdm" ? "sdm_status" : "status";
						$catatanField = $par[mode] == "detSdm" ? "sdm_remark" : "remark";
						$timeField = $par[mode] == "detSdm" ? "sdm_date" : "apr_date";
						$userField = $par[mode] == "detSdm" ? "sdm_by" : "apr_by";

						$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
						$persetujuanField = $par[mode] == "detPay" ? "pay_status" : $persetujuanField;
						$catatanField = $par[mode] == "detPay" ? "pay_remark" : $catatanField;
						$timeField = $par[mode] == "detPay" ? "pay_date" : $timeField;
						$userField = $par[mode] == "detPay" ? "pay_by" : $userField;

						list($dateField) = explode(" ", $r[$timeField]);

						$persetujuanPinjaman = "Belum Diproses";
						$persetujuanPinjaman = $r[$persetujuanField] == "1" ? "Disetujui" : $persetujuanPinjaman;
						$persetujuanPinjaman = $r[$persetujuanField] == "2" ? "Ditolak" : $persetujuanPinjaman;	
						$persetujuanPinjaman = $r[$persetujuanField] == "3" ? "Diperbaiki" : $persetujuanPinjaman;	



						$text.="<div class=\"centercontent contentpopup\">
						<div class=\"pageheader\">
							<h1 class=\"pagetitle\">".$titleField."</h1>
							".getBread(ucwords($par[mode]." data"))."
						</div>
						<div id=\"contentwrapper\" class=\"contentwrapper\">
							<form id=\"form\" name=\"form\"  class=\"stdform\">	
								<div id=\"general\" class=\"subcontent\">
									<p>
										<label class=\"l-input-small\">Tanggal</label>
										<span class=\"field\">".getTanggal($dateField,"t")."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Nama</label>
										<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='".$r[$userField]."' ")."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Status</label>
										<span class=\"field\">".$persetujuanPinjaman."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Keterangan</label>
										<span class=\"field\">".nl2br($r[$catatanField])."&nbsp;</span>
									</p>				
									<p>						
										<input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\"/>
									</p>
								</div>
							</form>	
						</div>";
						return $text;
					}


					function detail(){
						global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;

						$sql="select * from emp_rmb where id='$par[id]'";
						$res=db($sql);
						$r=mysql_fetch_array($res);					

						$true = $r[status] == "1" ? "checked=\"checked\"" : "";
						$false = $r[status] == "2" ? "checked=\"checked\"" : "";
						$revisi = $r[status] == "3" ? "checked=\"checked\"" : "";

						if(empty($r[rmb_no])) $r[rmb_no] = gNomor();
						if(empty($r[rmb_date])) $r[rmb_date] = date('Y-m-d');		

						setValidation("is_null","inp[rmb_no]","anda harus mengisi nomor");
						setValidation("is_null","inp[parent_id]","anda harus mengisi nik");
						setValidation("is_null","rmb_date","anda harus mengisi tanggal");
						setValidation("is_null","inp[rmb_cat]","anda harus mengisi kategori");
						setValidation("is_null","inp[rmb_type]","anda harus mengisi tipe");
						setValidation("is_null","inp[rmb_val]","anda harus mengisi nilai");
						$text = getValidation();

						$sql_="select
						id as parent_id,
						reg_no as nikPegawai,
						name as namaPegawai
						from emp where id='".$r[parent_id]."'";
						$res_=db($sql_);
						$r_=mysql_fetch_array($res_);

						$sql__="select * from emp_phist where parent_id='".$r_[parent_id]."' and status='1'";
						$res__=db($sql__);
						$r__=mysql_fetch_array($res__);
						$r_[namaJabatan] = $r__[pos_name];
						$r_[namaDivisi] = getField("select namaData from mst_data where kodeData='".$r__[div_id]."'");

						list($rmb_year) = explode("-", $r[rmb_date]);
						$sql__="select * from pay_kacamata where idKategori='".$r[rmb_cat]."' and idTipe='".$r[rmb_type]."'";
						$res__=db($sql__);
						$r__=mysql_fetch_array($res__);

						$r[rmb_limit] = $r__[nilaiKacamata];						
						$r[rmb_balance] = $r__[nilaiKacamata] - getField("select sum(rmb_val) from emp_rmb where parent_id='".$r[parent_id]."' and rmb_cat='".$r[rmb_cat]."' and rmb_type='".$r[rmb_type]."' and year(rmb_date)='".$rmb_year."' and rmb_date<='".$r[rmb_date]."' and status='1' and rmb_jenis='m' and id!='".$par[id]."'");

						$text.="<div class=\"pageheader\">
						<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
						".getBread(ucwords($par[mode]." data"))."								
					</div>
					<div class=\"contentwrapper\">
						<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return save('".getPar($par, "mode")."')\" enctype=\"multipart/form-data\">	
							<div id=\"general\" style=\"margin-top:20px;\">
								<table width=\"100%\">
									<tr>
										<td width=\"45%\">
											<p>
												<label class=\"l-input-small\">Nomor</label>
												<span class=\"field\">".$r[rmb_no]."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">NPP</label>
												<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Nama</label>
												<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
											</p>
										</td>
										<td width=\"55%\">
											<p>
												<label class=\"l-input-small\">Tanggal</label>
												<span class=\"field\">".getTanggal($r[rmb_date],"t")."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Jabatan</label>
												<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Divisi</label>
												<span class=\"field\">".$r_[namaDivisi]."&nbsp;</span>
											</p>
										</td>
									</tr>
								</table>
								<div class=\"widgetbox\">
									<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM KACAMATA</h3></div>
								</div>
								<table width=\"100%\">
									<tr>
										<td width=\"45%\" style=\"vertical-align:top;\">
											<p>
												<label class=\"l-input-small\">Kategori</label>
												<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[rmb_cat]."'")."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Tipe</label>
												<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[rmb_type]."'")."&nbsp;</span>
											</p>			
											<p>
												<label class=\"l-input-small\">Nilai</label>
												<span class=\"field\">".getAngka($r[rmb_val])."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Document</label>
												<div class=\"field\">";
													$filename = getField("select filename from emp_rmb_files where parent_id='$par[id]'");
													$text.=empty($filename)?
													"&nbsp;":
													"<a href=\"download.php?d=kacamata&f=$par[id]\"><img src=\"".getIcon($fFile."/".$filename)."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a><br clear=\"all\">";
													$text.="</div>
												</p>
											</td>
											<td width=\"55%\" style=\"vertical-align:top;\">
												<p>
													<label class=\"l-input-small\">Balance</label>
													<span class=\"field\">".getAngka($r[rmb_balance])."&nbsp;</span>
												</p>
												<p>
													<label class=\"l-input-small\">Limit</label>
													<span class=\"field\">".getAngka($r[rmb_limit])."&nbsp;</span>
												</p>
												<p>
													<label class=\"l-input-small\">Keterangan</label>
													<span class=\"field\">".nl2br($r[remark])."&nbsp;</span>
												</p>						
											</td>
										</tr>
									</table>";

									$status = "Belum Diproses";
									$status = $r[status] == "1" ? "Disetujui" : $status;
									$status = $r[status] == "2" ? "Ditolak" : $status;	
									$status = $r[status] == "3" ? "Diperbaiki" : $status;	

									$text.="<div class=\"widgetbox\">
									<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
								</div>			
								<table width=\"100%\">
									<tr>
										<td width=\"45%\">
											<p>
												<label class=\"l-input-small\">Tanggal</label>
												<span class=\"field\">".getTanggal($r[apr_date],"t")."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Nama</label>
												<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[apr_by]'")."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Status</label>
												<span class=\"field\">".$status."&nbsp;</span>
											</p>
											<p>
												<label class=\"l-input-small\">Keterangan</label>
												<span class=\"field\">".nl2br($r[apr_remark])."&nbsp;</span>
											</p>
										</td>
										<td width=\"55%\">&nbsp;</td>
									</tr>
								</table>";


								$status = "Belum Diproses";
								$status = $r[sdm_status] == "1" ? "Disetujui" : $status;
								$status = $r[sdm_status] == "2" ? "Ditolak" : $status;	
								$status = $r[sdm_status] == "3" ? "Diperbaiki" : $status;	

								$text.="<div class=\"widgetbox\">
								<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
							</div>			
							<table width=\"100%\">
								<tr>
									<td width=\"45%\">
										<p>
											<label class=\"l-input-small\">Tanggal</label>
											<span class=\"field\">".getTanggal($r[sdm_date],"t")."&nbsp;</span>
										</p>
										<p>
											<label class=\"l-input-small\">Nama</label>
											<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[sdm_by]'")."&nbsp;</span>
										</p>
										<p>
											<label class=\"l-input-small\">Status</label>
											<span class=\"field\">".$status."&nbsp;</span>
										</p>
										<p>
											<label class=\"l-input-small\">Keterangan</label>
											<span class=\"field\">".nl2br($r[sdm_remark])."&nbsp;</span>
										</p>
									</td>
									<td width=\"55%\">&nbsp;</td>
								</tr>
							</table>";

							$status = "Belum Diproses";
							$status = $r[pay_status] == "1" ? "Disetujui" : $status;
							$status = $r[pay_status] == "2" ? "Ditolak" : $status;	
							$status = $r[pay_status] == "3" ? "Diperbaiki" : $status;	

							$text.="<div class=\"widgetbox\">
							<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>PEMBAYARAN</h3></div>
						</div>			
						<table width=\"100%\">
							<tr>
								<td width=\"45%\">
									<p>
										<label class=\"l-input-small\">Tanggal</label>
										<span class=\"field\">".getTanggal($r[pay_date],"t")."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Nama</label>
										<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='$r[pay_by]'")."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Status</label>
										<span class=\"field\">".$status."&nbsp;</span>
									</p>
									<p>
										<label class=\"l-input-small\">Keterangan</label>
										<span class=\"field\">".nl2br($r[pay_remark])."&nbsp;</span>
									</p>
								</td>
								<td width=\"55%\">&nbsp;</td>
							</tr>
						</table>";

						$text.="</div>
						<p>					
							<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right;\"/>		
						</p>
					</form>";
					return $text;
				}


				function pegawai(){
					global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cID,$areaCheck;
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

									$filter = "where reg_no is not null ".($cGroup != "1" && $cGroup != "20" ? " and (leader_id='".$par[idPegawai]."' or administration_id='".$par[idPegawai]."')" : "");

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

				function getContent($par){
					global $db,$s,$_submit,$menuAccess;
					switch($par[mode]){
						case "no":
						$text = gNomor();
						break;
						case "get":
						$text = gPegawai();
						break;
						case "plf":
						$text = gPlafon();
						break;
						case "peg":
						$text = pegawai();
						break;
						case "delFile":
						if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
						break;

						case "det":
						$text = detail();
						break;
						case "detAts":
						$text = detailApproval();
						break;
						case "detSdm":
						$text = detailApproval();
						break;
						case "detPay":
						$text = detailApproval();
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
						case "app":
						if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
						break;
						default:
						$text = lihat();
						break;
					}
					return $text;
				}	
				?>