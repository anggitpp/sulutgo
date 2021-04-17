<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fSK = "files/emp/sk/";
$fPKWT = "files/emp/PKWT/";

function uploadSK($id){
    global $fSK;
    $fileUpload = $_FILES["file_sk"]["tmp_name"];
    $fileUpload_name = $_FILES["file_sk"]["name"];
    if(($fileUpload!="") and ($fileUpload!="none")){	
        fileUpload($fileUpload,$fileUpload_name,$fSK);			
        $file_sk = "fileSK-".$id.".".getExtension($fileUpload_name);
        fileRename($fSK, $fileUpload_name, $file_sk);			
    }
    if(empty($file_sk)) $file_sk = getField("select file_sk from emp_phist where id='$id'");
    
    return $file_sk;
}

function uploadPKWT($id){
    global $fPKWT;
    $fileUpload = $_FILES["file_pkwt"]["tmp_name"];
    $fileUpload_name = $_FILES["file_pkwt"]["name"];
    if(($fileUpload!="") and ($fileUpload!="none")){	
        fileUpload($fileUpload,$fileUpload_name,$fPKWT);			
        $file_pkwt = "filePKWT-".$id.".".getExtension($fileUpload_name);
        fileRename($fPKWT, $fileUpload_name, $file_pkwt);			
    }
    if(empty($file_pkwt)) $file_pkwt = getField("select file_pkwt from emp_phist where id='$id'");
    
    return $file_pkwt;
}

function pegawai() {
	global $s, $id, $inp, $par, $arrParameter,$db;
	$data = getField("select concat(t1.reg_no,'\t',t1.join_date,'\t',t1.pos_name,'\t',t2.namaData,'\t',t3.namaData) FROM dta_pegawai t1
	LEFT JOIN mst_data t2 ON t1.`rank` = t2.`kodeData` 
	LEFT JOIN mst_data t3 ON t1.`cat` = t3.`kodeData` WHERE t1.id = '$par[idPegawai]'");
	return $data;	
}
function anakan() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodeInduk]'  order by namaData");
    return implode("\n", $data);
}
function directorate() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    where t2.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function divisi() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    where t3.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function departemen() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
    where t4.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}
function unit() {
    global $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(t1.kodeData, '\t', t1.namaData) from mst_data t1
    JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
    JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
    JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
    JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk
    where t5.kodeCategory='X03' and t1.kodeInduk ='$par[kodeInduk]' order by t1.namaData");
    return implode("\n", $data);
}

function hapusFileSK(){
    global $par,$fSK;					
    $file_sk = getField("select file_sk from emp_file where id='$par[idFile]'");
    if(file_exists($fSK.$file_sk) and $file_sk!="")unlink($fSK.$file_sk);
    
    $sql="update emp_phist set file_sk='' where id='$par[idFile]'";
    db($sql);		
    echo "<script>window.location='?par[mode]=edit".getPar($par,"mode, idFile")."';</script>";
}

function hapusFilePKWT(){
    global $par,$fPKWT;					
    $file_pkwt = getField("select file_pkwt from emp_file where id='$par[idFile]'");
    if(file_exists($fPKWT.$file_pkwt) and $file_pkwt!="")unlink($fPKWT.$file_pkwt);
    
    $sql="update emp_phist set file_pkwt='' where id='$par[idFile]'";
    db($sql);		
    echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
}

function hapus(){
    global $db,$s,$inp,$par,$fKTP, $fPIC, $cUsername;						
    $sql="delete from emp_phist where id='$par[id]'";
    db($sql);
	echo "<script>alert('DATA BERHASIL DIHAPUS');window.location='?".getPar($par,"mode,id")."';</script>";
}

function tambah(){
    global $s, $db, $inp, $par, $cUsername, $arrParam;
  
    $inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
    $inp[sk_date] = setTanggal($inp[sk_date]);
    
	$idPhist = getField("select id from emp_phist order by id desc limit 1")+1;
	$fileSK = uploadSK($idPhist);
    $filePKWT = uploadPKWT($idPhist);
    $sql="insert into emp_phist (id, parent_id, pos_name, rank, grade, sk_no, sk_date, start_date, end_date, location, status, remark, top_id, dir_id, div_id, dept_id, unit_id, prov_id, city_id, manager_id, leader_id, administration_id, replacement_id, replacement2_id, lembur, payroll_id, shift_id, group_id, proses_id, company_id, kategori, perdin, obat, area, file_sk, file_pkwt, cre_by, cre_date) values ('$idPhist', '$inp[parent_id]', '$inp[pos_name]', '$inp[rank]', '$inp[grade]', '$inp[sk_no]', '$inp[sk_date]', '$inp[start_date]', '$inp[end_date]', '$inp[location]', '$inp[status]', '$inp[remark]', '$inp[top_id]',  '$inp[dir_id]', '$inp[div_id]', '$inp[dept_id]', '$inp[unit_id]', '$inp[prov_id]', '$inp[city_id]', '$inp[manager_id]', '$inp[leader_id]', '$inp[administration_id]', '$inp[replacement_id]', '$inp[replacement2_id]', '$inp[lembur]', '$inp[payroll_id]', '$inp[shift_id]', '$inp[group_id]', '$inp[proses_id]', '$inp[company_id]', '$inp[kategori]', '$inp[perdin]', '$inp[obat]', '$inp[area]', '$fileSK', '$filePKWT', '$cUsername', '".date('Y-m-d H:i:s')."')";
	db($sql);
	
	if($inp[status] == 1){
		$sql = "update emp_phist set status = '0' where parent_id = '$inp[parent_id]' AND id != '$idPhist'";
		db($sql);
		// echo $sql;
	}
	// die();
    
    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?".getPar($par,"mode,id")."';</script>";
}

function ubah(){
    global $s, $db, $inp, $par, $cUsername, $arrParam;

	$inp[start_date] = setTanggal($inp[start_date]);
    $inp[end_date] = setTanggal($inp[end_date]);
	$inp[sk_date] = setTanggal($inp[sk_date]);
	
	$fileSK = uploadSK($par[id]);
    $filePKWT = uploadPKWT($par[id]);
    
    $sql = "update emp_phist set pos_name = '$inp[pos_name]', area = '$inp[area]', rank = '$inp[rank]', grade = '$inp[grade]', sk_no = '$inp[sk_no]', sk_date = '$inp[sk_date]', start_date = '$inp[start_date]', end_date = '$inp[end_date]', location = '$inp[location]', remark = '$inp[remark]',top_id = '$inp[top_id]', dir_id = '$inp[dir_id]', div_id = '$inp[div_id]', dept_id = '$inp[dept_id]', unit_id = '$inp[unit_id]', prov_id = '$inp[prov_id]', city_id = '$inp[city_id]', manager_id = '$inp[manager_id]', leader_id = '$inp[leader_id]', administration_id = '$inp[administration_id]', replacement_id = '$inp[replacement_id]', replacement2_id = '$inp[replacement2_id]', lembur = '$inp[lembur]', payroll_id = '$inp[payroll_id]', shift_id = '$inp[shift_id]', kategori = '$inp[kategori]', group_id = '$inp[group_id]', proses_id = '$inp[proses_id]', company_id = '$inp[company_id]', perdin = '$inp[perdin]', obat = '$inp[obat]', status = '$inp[status]', file_sk = '$fileSK', file_pkwt = '$filePKWT', upd_by = '$cUsername', upd_date = '".date('Y-m-d H:i:s')."' where id = '$par[id]' ";
	db($sql);

	if($inp[status] == 1){
		$sql = "update emp_phist set status = '0' where parent_id = '$inp[parent_id]' AND id != '$par[id]'";
		db($sql);
	}
    

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.location='?".getPar($par,"mode,id")."';</script>";
}

function form(){
	global $s,$db,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$kodeModul,$sGroup,$m,$arrParam;

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $sql="select * from emp_phist where id='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$false = $r[status] == 0 ? "checked=\"checked\"" : "";
	$true = empty($false) ? "checked=\"checked\"" : "";

	$sql_="select * from emp where id='$r[parent_id]'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);	

    setValidation("is_null","inp[pos_name]","anda harus mengisi Jabatan pada tab Posisi");
    setValidation("is_null","inp[rank]","anda harus mengisi Pangkat pada tab Posisi");
    setValidation("is_null","inp[location]","anda harus mengisi Lokasi Kerja pada tab Posisi");
    setValidation("is_null","inp[start_date]","anda harus mengisi Mulai pada tab Posisi");
    setValidation("is_null","inp[dir_id]","anda harus mengisi Perusahaan pada tab Posisi > Organisasi");  
	
	$text = getValidation();

    $text.="<div class=\"pageheader\">
    <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
    " . getBread(ucwords($mode . " data")) . "
</div>
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
	<p style=\"position:absolute;top:5px;right:5px;\"> 
        <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" />
        <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode") . "';\"/>
    </p>
	<style>
    .chosen-container{ min-width:250px; }
    #inp_dir_id__chosen{ min-width:350px;}
    #inp_top_id__chosen{ min-width:350px;}
    #inp_div_id__chosen{ min-width:350px;}
    #inp_dept_id__chosen{ min-width:350px;}
    #inp_unit_id__chosen{ min-width:350px;}
    #inp_prov_id__chosen{ min-width:350px;}
    #inp_city_id__chosen{ min-width:350px;}
    #inp_manager_id__chosen{ min-width:350px;}
    #inp_leader_id__chosen{ min-width:350px;}
    #inp_administration_id__chosen{ min-width:350px;}
    #inp_replacement_id__chosen{ min-width:350px;}
    #inp_replacement2_id__chosen{ min-width:350px;}
    #inp_payroll_id__chosen{ min-width:350px;}
    #inp_kategori__chosen{ min-width:350px;}
    #inp_group_id__chosen{ min-width:350px;}
    #inp_proses_id__chosen{ min-width:350px;}
    #inp_shift_id__chosen{ min-width:350px;}
    #inp_company_id__chosen{ min-width:350px;}
    #inp_perdin__chosen{ min-width:350px;}
    #inp_obat__chosen{ min-width:350px;}
	</style>
	";
	
	$text.="
	<fieldset>
		<table style=\"width:100%\">
			<tr>
				<td style=\"width:50%\">
					<p>
						<label class=\"l-input-small2\">Nama Pegawai</label>
						" . comboData("select * from dta_pegawai order by name", "id", "name", "inp[parent_id]", " ", $r[parent_id], "onchange=\"changePegawai(this.value,'".getPar($par,"mode,parent_id")."');\"", "225px","chosen-select") . "
					</p>
					<p>
						<label class=\"l-input-small2\">Pangkat</label>
						<span class=\"field\" id =\"pangkat\">".$arrMaster[$r[rank]]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small2\">Jabatan</label>
						<span class=\"field\" id =\"jabatan\">".$r[pos_name]."&nbsp;</span>
					</p>
				</td>
				<td style=\"width:50%\">
					<p>
						<label class=\"l-input-small\">NPP</label>
						<span class=\"field\" id =\"nik\">".$r_[reg_no]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\" id =\"cat\">".$arrMaster[$r_[cat]]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Mulai Kerja</label>
						<span class=\"field\" id =\"mulaiKerja\">".getTanggal($r_[join_date])."&nbsp;</span>
					</p>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend> DATA JABATAN </legend>

		<table style=\"width:100%\">
			<tr>
				<td style=\"width:50%\">
					<p>           
						<label class=\"l-input-small2\" >Posisi <span class=\"required\">*)</span></label>
						<div class=\"field\">           
							<input type=\"text\" id=\"inp[pos_name]\" name=\"inp[pos_name]\"  value=\"$r[pos_name]\" class=\"mediuminput\" style=\"width:250px;\" />            
						</div>          
					</p>
					<p>           
						<label class=\"l-input-small2\" >Jabatan <span class=\"required\">*)</span></label>
						<div class=\"field\">           
							".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S09' order by namaData", "id", "description", "inp[rank]", " ", $r[rank], "onchange=\"getAnakan('rank','grade','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
						</div>          
					</p>

					<p>           
						<label class=\"l-input-small2\" >Nomor SK</label>
						<div class=\"field\">           
							<input type=\"text\" id=\"inp[sk_no]\" name=\"inp[sk_no]\"  value=\"$r[sk_no]\" class=\"mediuminput\" style=\"width:250px;\" />            
						</div>          
					</p>
					<p>
						<label class=\"l-input-small2\">Mulai <span class=\"required\">*)</span></label>
						<div class=\"field\">
							<input type=\"text\" id=\"start_date\" name=\"inp[start_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[start_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>           
						<label class=\"l-input-small2\" >Keterangan</label>
						<div class=\"field\">           
							<input type=\"text\" id=\"inp[remark]\" name=\"inp[remark]\"  value=\"$r[remark]\" class=\"mediuminput\" style=\"width:250px;\" />            
						</div>          
					</p>
					<p>
                        <label class=\"l-input-small2\">File SK</label>
                        <div class=\"field\">";
                            $text.=empty($r[file_sk])?
                            "<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
                            <div class=\"fakeupload\" style=\"width:450px;\">
                            <input type=\"file\" id=\"file_sk\" name=\"file_sk\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
                            </div>":
                            "<a href=\"download.php?d=empsk&f=$r[id]\"><img src=\"".getIcon($r[file_sk])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
                            <input type=\"file\" id=\"file_sk\" name=\"fileCuti\" style=\"display:none;\" />
                            <a href=\"?par[mode]=delFileSK&par[idFile]=$r[id]".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
                            <br clear=\"all\">";
                            $text.="
                        </div>
                    </p>
					<p>
						<label class=\"l-input-small2\" >Status</label>
						<div class=\"field\">     	
							<input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"0\" $false /> <span class=\"sradio\">Tidak Aktif</span>
							<input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"1\" $true /> <span class=\"sradio\">Aktif</span> 
						</div>
					</p>
					
				</td>
				<td style=\"width:50%\">
					<p>
					&nbsp;
					</p>
					<p>           
						<label class=\"l-input-small\" >Golongan</label>
						<div class=\"field\">           
							".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S10' and kodeInduk = '$r[rank]' order by namaData", "id", "description", "inp[grade]", " ", $r[grade], "", "250px","chosen-select")."            
						</div>          
					</p>	            
					<p>
						<label class=\"l-input-small\">Tanggal SK</label>
						<div class=\"field\">
							<input type=\"text\" id=\"sk_date\" name=\"inp[sk_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[sk_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Selesai</label>
						<div class=\"field\">
							<input type=\"text\" id=\"end_date\" name=\"inp[end_date]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[end_date]) . "\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>
					<p>           
						<label class=\"l-input-small\" >Lokasi Kerja <span class=\"required\">*)</span></label>
						<div class=\"field\">           
							".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S06' order by namaData", "id", "description", "inp[location]", " ", $r[location], "", "250px","chosen-select")."            
						</div>          
					</p>
					<p>           
						<label class=\"l-input-small\" >Area Kerja</label>
						<div class=\"field\">           
							".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'AK' order by namaData", "id", "description", "inp[area]", " ", $r[area], "", "250px","chosen-select")."            
						</div>          
					</p>       
					<p>
                        <label class=\"l-input-small\">File PKWT</label>
                        <div class=\"field\">";
                            $text.=empty($r[file_pkwt])?
                            "<input type=\"text\" id=\"fileTemp2\" name=\"fileTemp2\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
                            <div class=\"fakeupload\" style=\"width:300px;\">
                            <input type=\"file\" id=\"file_pkwt\" name=\"file_pkwt\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp2.value = this.value;\" />
                            </div>":
                            "<a href=\"download.php?d=emppkwt&f=$r[id]\"><img src=\"".getIcon($r[file_pkwt])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
                            <input type=\"file\" id=\"file_pkwt\" name=\"fileCuti\" style=\"display:none;\" />
                            <a href=\"?par[mode]=delFilePKWT&par[idFile]=$r[id]".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
                            <br clear=\"all\">";
                            $text.="
                        </div>
                    </p>       
				</td>
			</tr>
		</table>

	<ul class=\"editornav\">
	<li class=\"current\"><a href=\"#organisasi\">ORGANISASI</a></li>
	<li><a href=\"#lokasi\">LOKASI</a></li>
	<li><a href=\"#struktur\">STRUKTUR</a></li>
	<li><a href=\"#setting\">SETTING</a></li>        
			
	</ul>
    

	<div id =\"organisasi\" class=\"subcontent1\">
	<br>
		<p>           
			<label class=\"l-input-small\" >Perusahaan <span class=\"required\">*)</span></label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X03' order by kodeInduk,urutanData", "id", "description", "inp[top_id]", " ", $r[top_id], "onchange=\"getAnakan('top_id','dir_id','directorate','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Cabang</label>
			<div class=\"field\">           
				".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
				JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
				where t2.kodeCategory='X03' and t1.kodeInduk = '$r[top_id]' order by t1.urutanData", "id", "description", "inp[dir_id]", " ", $r[dir_id], "onchange=\"getAnakan('dir_id','div_id','divisi','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Divisi</label>
			<div class=\"field\">           
				".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
				JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
				JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
				where t3.kodeCategory='X03' and t1.kodeInduk = '$r[dir_id]' order by t1.urutanData", "id", "description", "inp[div_id]", " ", $r[div_id], "onchange=\"getAnakan('div_id','dept_id','departemen','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Departemen</label>
			<div class=\"field\">           
				".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
				JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
				JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
				JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
				where t4.kodeCategory='X03' and t1.kodeInduk = '$r[div_id]' order by t1.urutanData", "id", "description", "inp[dept_id]", " ", $r[dept_id], "onchange=\"getAnakan('dept_id','unit_id','unit','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Unit</label>
			<div class=\"field\">           
				".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
				JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
				JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
				JOIN mst_data t4 ON t4.kodeData=t3.kodeInduk
				JOIN mst_data t5 ON t5.kodeData=t4.kodeInduk
				where t5.kodeCategory='X03' and t1.kodeInduk = '$r[dept_id]' order by t1.urutanData", "id", "description", "inp[unit_id]", " ", $r[unit_id], "", "250px","chosen-select")."            
			</div>          
		</p>
	</div>
	
	<div id =\"lokasi\" class=\"subcontent1\" style=\"margin:0;display:none;\">
	<br>
		<p>           
			<label class=\"l-input-small\" >Provinsi</label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S02' order by namaData", "id", "description", "inp[prov_id]", " ", $r[prov_id], "onchange=\"getAnakan('prov_id','city_id','anakan','".getPar($par,"mode")."');\"", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Kota</label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description from mst_data where kodeCategory = 'S03' and kodeInduk = '$r[prov_id]' order by namaData", "id", "description", "inp[city_id]", " ", $r[city_id], "", "250px","chosen-select")." 
			</div>          
		</p>
	</div>

	<div id =\"struktur\" class=\"subcontent1\" style=\"margin:0;display:none;\">
	<br>
		<p>           
			<label class=\"l-input-small\" >Manajer</label>
			<div class=\"field\">           
				".comboData("select id, concat(reg_no, ' - ', name) description from emp WHERE status=535 order by name", "id", "description", "inp[manager_id]", " ", $r[manager_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
		<label class=\"l-input-small\" >Atasan</label>
		<div class=\"field\">           
			".comboData("select id, concat(reg_no , ' - ', name) description from emp WHERE status=535 order by name", "id", "description", "inp[leader_id]", " ", $r[leader_id], "", "250px","chosen-select")."            
		</div>          
		</p>
		
		<p>           
			<label class=\"l-input-small\" >Tata Usaha</label>
			<div class=\"field\">           
				".comboData("select id, concat(reg_no , ' - ', name) description from emp WHERE status=535 order by name", "id", "description", "inp[administration_id]", " ", $r[administration_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Pengganti 1</label>
			<div class=\"field\">           
				".comboData("select id, concat(reg_no , ' - ', name) description from emp WHERE status=535 order by name", "id", "description", "inp[replacement_id]", " ", $r[replacement_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Pengganti 2</label>
			<div class=\"field\">           
				".comboData("select id, concat(reg_no , ' - ', name) description from emp WHERE status=535 order by name", "id", "description", "inp[replacement2_id]", " ", $r[replacement2_id], "", "250px","chosen-select")."            
			</div>          
		</p>
	</div>
	
	<div id =\"setting\" class=\"subcontent1\" style=\"margin:0;display:none;\">
	<br>
		<p>
			<label class=\"l-input-small\">Hak Lembur</label>
			<div class=\"field\">
				<input type=\"radio\" id=\"new\" name=\"inp[lembur]\" value=\"1\" $true /> <span class=\"sradio\">Ya</span>
				<input type=\"radio\" id=\"rep\" name=\"inp[lembur]\" value=\"0\" $false /> <span class=\"sradio\">Tidak</span>
			</div>
		</p>
		<p>           
			<label class=\"l-input-small\" >Jenis Payroll</label>
			<div class=\"field\">           
				".comboData("select idJenis id, namaJenis description from pay_jenis where statusJenis='t' order by namaJenis", "id", "description", "inp[payroll_id]", " ", $r[payroll_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Location Process</label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[7]."' order by urutanData", "id", "description", "inp[group_id]", " ", $r[group_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Group Process</label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[49]."' order by kodeInduk,urutanData", "id", "description", "inp[proses_id]", " ", $r[proses_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Shift Kerja</label>
			<div class=\"field\">           
				".comboData("select idShift id, namaShift description from dta_shift where statusShift = 't' order by namaShift", "id", "description", "inp[shift_id]", " ", $r[shift_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Perusahaan</label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='".$arrParameter[47]."' order by kodeInduk,urutanData", "id", "description", "inp[company_id]", " ", $r[company_id], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Kategori</label>
			<div class=\"field\">           
				".comboData("select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='KT' order by kodeInduk,urutanData", "id", "description", "inp[kategori]", " ", $r[kategori], "", "250px","chosen-select")."            
			</div>          
		</p>";
		$r[perdin] = empty($r[perdin]) ? getField("select kodeData from mst_data where kodeCategory = 'PJG' order by urutanData DESC LIMIT 1") : $r[perdin];
		$r[obat] = empty($r[obat]) ? getField("select kodeData from mst_data where kodeCategory = 'GRI' order by urutanData DESC LIMIT 1") : $r[obat];
		$text.="
		<p>           
			<label class=\"l-input-small\" >Perjalanan Dinas</label>
			<div class=\"field\">           
				".comboData("select kodeData id, concat(namaData, ' ', '(', keteranganData, ')') description, kodeInduk from mst_data  where kodeCategory='PJG' order by kodeInduk,urutanData", "id", "description", "inp[perdin]", " ", $r[perdin], "", "250px","chosen-select")."            
			</div>          
		</p>
		<p>           
			<label class=\"l-input-small\" >Gol. Pengobatan</label>
			<div class=\"field\">           
				".comboData("select kodeData id, concat(namaData, ' ', '(', keteranganData, ')') description, kodeInduk from mst_data  where kodeCategory='GRI' order by kodeInduk,urutanData", "id", "description", "inp[obat]", " ", $r[obat], "", "250px","chosen-select")."            
			</div>          
		</p>
	</div>
	</fieldset>
</div>";

return $text;

}

function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter;

    $cols = 9;		

    $text = table($cols, array(($cols-3),($cols-2),($cols-1),$cols));
    
	$arrData = arrayQuery("select t2.kodeKomponen, t2.namaKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t' group by 1 order by t2.tipeKomponen desc, t2.urutanKomponen");
	$arrDetail = arrayQuery("select t2.kodeKomponen, t2.idKomponen, t2.namaKomponen from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen");
    
	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	
	".getBread()."
    </div>    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    

    
    <div id=\"pos_l\" style=\"float:left;\">

    <p>					
        <input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>

        <input type=\"button\" id=\"sFilter\" value=\"+\" class=\"btn btn_search btn-small\" onclick=\"showFilter()\" />

        <input type=\"button\" style=\"display:none;\" id=\"hFilter\" value=\"-\" class=\"btn btn_search btn-small\" onclick=\"hideFilter()\" />

        

    </p>

    

    <script>

        function showFilter()

        {

            jQuery('#form_filter').show('slow');

            jQuery('#sFilter').hide();

            jQuery('#hFilter').show();

        }

        

        function hideFilter()

        {

            jQuery('#form_filter').hide('slow');

            jQuery('#sFilter').show();

            jQuery('#hFilter').hide();

        }

    </script>

    

</div>



<div id=\"pos_r\" style=\"float:right; margin-top:5px;\">
<a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>&nbsp";
if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
$text.="</td>

</div>	

    



</form>

<fieldset id=\"form_filter\" style=\"display:none;\">

<form id=\"form\" class=\"stdform\">

    <table width=\"100%\">

        <tr>

            <td width=\"50%\">               

                <p>

                    <label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Lokasi</label>

                    <div class=\"field\" style=\"margin-left:200px;\">

                        ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' ORDER BY urutanData", "kodeData", "namaData", "combo1", "- Semua Lokasi -", $_GET['combo1'], "onchange=\"\"", "250px", "chosen-select")."

                         <style>

                         #combo1_chosen{min-width:250px;}

                         </style>

                    </div>

                </p>

                
                <p>

                <label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Pangkat</label>

                <div class=\"field\" style=\"margin-left:200px;\">

                    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY namaData", "kodeData", "namaData", "combo3", "- Semua Pangkat -", $_GET['combo3'], "onchange=\"\"", "250px", "chosen-select")."

                    <style>

                    #combo3_chosen{min-width:250px;}

                    </style>

                </div>

            </p>

                <p>

                    <label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Departemen</label>

                    <div class=\"field\" style=\"margin-left:200px;\">

                        ".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
                        JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
                        JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
                        where t3.kodeCategory='X04' order by t1.urutanData", "id", "description", "combo5", "- Semua Departemen -", $_GET['combo5'], "", "250px", "chosen-select")."

                        <style>

                        #combo5_chosen{min-width:250px;}

                        </style>

                    </div>

                </p>

            </td>

            <td width=\"50%\">

            <p>

            <label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Jenis</label>

            <div class=\"field\" style=\"margin-left:200px;\">

                ".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","combo2","- Semua Jenis -",$_GET['combo2'],"onchange=\"\"","210px;","chosen-select")."

                 <style>

                 #combo2_chosen{min-width:250px;}

                 </style>

            </div>

        </p> 
        

              
                <p>

                    <label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Grade</label>

                    <div class=\"field\" style=\"margin-left:200px;\">

                        ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S10' ORDER BY namaData", "kodeData", "namaData", "combo4", "- Semua Grade -", $_GET['combo4'], "onchange=\"\"", "250px", "chosen-select")."

                        <style>

                        #combo4_chosen{min-width:250px;}

                        </style>

                    </div>

                </p>

                <p>

                    <label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Jabatan Pegawai </label>

                    <div class=\"field\" style=\"margin-left:200px;\">

                        ".comboData("SELECT DISTINCT(pos_name) AS posisi FROM emp_phist ORDER BY pos_name ASC", "posisi", "posisi", "combo6", "- Semua Jabatan -", $_GET['combo6'], "onchange=\"\"", "250px", "chosen-select")."

                        <style>

                        #combo6_chosen{min-width:250px;}

                        </style>

                    </div>

                </p>

              

            </td>

        </tr>

    </table>

    

    

</form>

</fieldset>
";
    $days = cal_days_in_month(CAL_GREGORIAN, $par[bulanMakan], $par[tahunMakan]);
    $text.="

    <br clear=\"all\" />

    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
    <thead>
    <tr>
    <th style=\"vertical-align:middle\" width=\"20\"  style=\"\">No.</th>
    <th width=\"*\" style=\"vertical-align:middle \" >NAMA</th>
    <th width=\"80\" style=\"vertical-align:middle \" >NPP</th>
    <th width=\"150\" style=\"vertical-align:middle \" >Jabatan</th>
    <th width=\"150\" style=\"vertical-align:middle \" >Rank</th>
    <th width=\"80\" style=\"vertical-align:middle \" >Lokasi</th>
    <th width=\"80\" style=\"vertical-align:middle \" >Tahun</th>
    <th width=\"50\" style=\"vertical-align:middle \" >Nomor SK</th>
    <th width=\"100\" style=\"vertical-align:middle \" >Control</th>
    </tr>
	</thead>
	
    <tbody></tbody>

	</table>
    </div>";
    
    if($par[mode] == "xls"){			
        xls();			
        $tanggal = date('Y-m-d');
        $text.="<iframe src=\"download.php?d=exp&f=".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }		
    
    return $text;
}

function lData(){

    global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle, $arrParameter, $arrUrutan, $areaCheck, $arrParam;	

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
    $sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

    if (!empty($_GET['fSearch']))

    $sWhere.= " and (			

    lower(name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'	

    or lower(reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'

    )";

    if(!empty($_GET['combo1'])) $sWhere.=" and t2.location='".$_GET['combo1']."'";	
    if(!empty($_GET['combo2'])) $sWhere.=" and t2.payroll_id='".$_GET['combo2']."'";	
    if(!empty($_GET['combo3'])) $sWhere.=" and t2.rank='".$_GET['combo3']."'";	
    if(!empty($_GET['combo4'])) $sWhere.=" and t2.grade='".$_GET['combo4']."'";	
    if(!empty($_GET['combo5'])) $sWhere.=" and t2.dept_id='".$_GET['combo5']."'";	
    if(!empty($_GET['combo6'])) $sWhere.=" and t2.pos_name='".$_GET['combo6']."'";

    $arrOrder = array(	
    "name",
    "name",
    "reg_no",
    "dept_id",
    "pos_name",
    "birth_date",
    "join_date"
    );

   
    $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
   
    $sql = " SELECT t1.id as idPosisi, t2.name, t2.reg_no, t1.pos_name, t1.rank, t1.location, t1.start_date, t1.end_date, t1.sk_no from emp_phist t1 join emp t2 on t1.parent_id = t2.id where t2.status = '535' $sWhere order by t2.name $sLimit";
    
    $res=db($sql);

    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("select count(distinct(t1.id)) from emp t1 left join emp_phist t2 on (t1.id = t2.parent_id and t2.status = 1) $sWhere"),
        "aaData" => array(),
    );
	$no=intval($_GET['iDisplayStart']);
	
    $arrMaster = arrayQuery("Select kodeData, namaData from mst_data");
    $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
    
    while($r=mysql_fetch_array($res)){
        $no++;
        
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
			$controlKebutuhan = "";
            if(isset($menuAccess[$s]["edit"]))
            $controlKebutuhan.= "<a href=\"?par[mode]=edit&par[id]=$r[idPosisi]".getPar($par,"mode,idPosisi")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
            
            
			if(isset($menuAccess[$s]["delete"]))
            $controlKebutuhan.= "<a href=\"?par[mode]=del&par[id]=$r[idPosisi]".getPar($par,"mode,idPosisi")."\" onclick=\"return confirm('anda yakin akan menghapus data ini ?')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";

        }
      
        $data=array(
            "<div align=\"center\">".$no.".</div>",				
            "<div align=\"left\">$r[name]</div>",
            "<div align=\"center\">$r[reg_no]</div>",
            "<div align=\"left\">".$r[pos_name]."</div>",
            "<div align=\"left\">".$arrMaster[$r[rank]]."</div>",
            "<div align=\"center\">".getTanggal($r[birth_date])."</div>",
            "<div align=\"right\">".$r[masaKerja]."</div>",
            "<div align=\"center\"><a href=\"?c=3&p=8&m=282&s=292&empid=$r[idPegawai]\" title=\"Detail Data\" class=\"detail\" ><span>Detail</span></a></div>",
            "<div align=\"center\">".$controlKebutuhan."</div>",
        );

        $json['aaData'][]=$data;
    }

    return json_encode($json);
}	

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){			
        case "anakan":
        $text = anakan();
		break;
		case "pegawai":
        $text = pegawai();
		break;
		case "directorate":
        $text = directorate();
		break;
		case "divisi":
        $text = divisi();
		break;
		case "departemen":
        $text = departemen();
		break;
		case "unit":
        $text = unit();
        break;
        case "lst":
		$text=lData();
		break;
		case "delFileSK":
            $text = hapusFileSK();
        break;
        case "delFilePKWT":
            $text = hapusFilePKWT();
        break;
        case "edit":
        if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
        break;
        case "add":
        if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
        break;
        case "del":
		if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
		break;
        default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>