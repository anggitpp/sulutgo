<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function tambah(){
	global $inp, $par, $cUsername, $cUsername;
	
	repField();

	$inp[tanggal_buat] = setTanggal($inp[tanggal_buat]);
	$inp[tanggal_lahir] = setTanggal($inp[tanggal_lahir]);
	$inp[tanggal_masuk] = setTanggal($inp[tanggal_masuk]);
	$inp[tanggal_berlaku] = setTanggal($inp[tanggal_berlaku]);

	$inp[s_gaji_pokok] = setAngka($inp[s_gaji_pokok]);
	$inp[s_tunjangan_jabatan] = setAngka($inp[s_tunjangan_jabatan]);
	$inp[s_tunjangan_operasional] = setAngka($inp[s_tunjangan_operasional]);
	$inp[s_tunjangan_kehadiran] = setAngka($inp[s_tunjangan_kehadiran]);
	$inp[s_tunjangan_lain] = setAngka($inp[s_tunjangan_lain]);
	$inp[s_tunjangan_khusus] = setAngka($inp[s_tunjangan_khusus]);
	$inp[s_lain_lain] = setAngka($inp[s_lain_lain]);
	$inp[s_tunai_tetap] = setAngka($inp[s_tunai_tetap]);
	$inp[d_gaji_pokok] = setAngka($inp[d_gaji_pokok]);
	$inp[d_tunjangan_jabatan] = setAngka($inp[d_tunjangan_jabatan]);
	$inp[d_tunjangan_operasional] = setAngka($inp[d_tunjangan_operasional]);
	$inp[d_tunjangan_kehadiran] = setAngka($inp[d_tunjangan_kehadiran]);
	$inp[d_tunjangan_lain] = setAngka($inp[d_tunjangan_lain]);
	$inp[d_tunjangan_khusus] = setAngka($inp[d_tunjangan_khusus]);
	$inp[d_lain_lain] = setAngka($inp[d_lain_lain]);
	$inp[d_tunai_tetap] = setAngka($inp[d_tunai_tetap]);
	
	$id = getField("select id_par from emp_par order by id_par desc limit 1") + 1;
	$sql = "insert into emp_par set id_par = '$id', nama_perusahaan = '$inp[nama_perusahaan]', tipe_permintaan = '$inp[tipe_permintaan]', tanggal_buat = '$inp[tanggal_buat]', nama = '$inp[nama]', nik = '$inp[nik]', tanggal_lahir = '$inp[tanggal_lahir]', gender = '$inp[gender]', tanggal_masuk = '$inp[tanggal_masuk]', term_sebelum = '$inp[term_sebelum]', term_usulan = '$inp[term_usulan]', status_kawin = '$inp[status_kawin]', status_rekrutmen = '$inp[status_rekrutmen]', s_jabatan = '$inp[s_jabatan]', s_tingkatan = '$inp[s_tingkatan]', s_posisi_atasan = '$inp[s_posisi_atasan]', s_lokasi_kerja = '$inp[s_lokasi_kerja]', s_divisi = '$inp[s_divisi]', d_jabatan = '$inp[d_jabatan]', d_tingkatan = '$inp[d_tingkatan]', d_posisi_atasan = '$inp[d_posisi_atasan]', d_lokasi_kerja = '$inp[d_lokasi_kerja]', d_divisi = '$inp[d_divisi]', status_karyawan = '$inp[status_karyawan]', keterangan = '$inp[keterangan]', s_gaji_pokok = '$inp[s_gaji_pokok]', s_tunjangan_jabatan = '$inp[s_tunjangan_jabatan]', s_tunjangan_operasional = '$inp[s_tunjangan_operasional]', s_tunjangan_kehadiran = '$inp[s_tunjangan_kehadiran]', s_tunjangan_lain = '$inp[s_tunjangan_lain]', s_tunjangan_khusus = '$inp[s_tunjangan_khusus]', s_lain_lain = '$inp[s_lain_lain]', s_tunai_tetap = '$inp[s_tunai_tetap]',  d_gaji_pokok = '$inp[d_gaji_pokok]', d_tunjangan_jabatan = '$inp[d_tunjangan_jabatan]', d_tunjangan_operasional = '$inp[d_tunjangan_operasional]', d_tunjangan_kehadiran = '$inp[d_tunjangan_kehadiran]', d_tunjangan_lain = '$inp[d_tunjangan_lain]', d_tunjangan_khusus = '$inp[d_tunjangan_khusus]', d_lain_lain = '$inp[d_lain_lain]', d_tunai_tetap = '$inp[d_tunai_tetap]', tanggal_berlaku = '$inp[tanggal_berlaku]', create_by = '$cUsername', create_date = '".date('Y-m-d H:i:s')."'";
	db($sql);
	
	echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah(){
	global $inp, $par, $cUsername;

	repField();

	$inp[tanggal_buat] = setTanggal($inp[tanggal_buat]);
	$inp[tanggal_lahir] = setTanggal($inp[tanggal_lahir]);
	$inp[tanggal_masuk] = setTanggal($inp[tanggal_masuk]);
	$inp[tanggal_berlaku] = setTanggal($inp[tanggal_berlaku]);

	$inp[s_gaji_pokok] = setAngka($inp[s_gaji_pokok]);
	$inp[s_tunjangan_jabatan] = setAngka($inp[s_tunjangan_jabatan]);
	$inp[s_tunjangan_operasional] = setAngka($inp[s_tunjangan_operasional]);
	$inp[s_tunjangan_kehadiran] = setAngka($inp[s_tunjangan_kehadiran]);
	$inp[s_tunjangan_lain] = setAngka($inp[s_tunjangan_lain]);
	$inp[s_tunjangan_khusus] = setAngka($inp[s_tunjangan_khusus]);
	$inp[s_lain_lain] = setAngka($inp[s_lain_lain]);
	$inp[s_tunai_tetap] = setAngka($inp[s_tunai_tetap]);
	$inp[d_gaji_pokok] = setAngka($inp[d_gaji_pokok]);
	$inp[d_tunjangan_jabatan] = setAngka($inp[d_tunjangan_jabatan]);
	$inp[d_tunjangan_operasional] = setAngka($inp[d_tunjangan_operasional]);
	$inp[d_tunjangan_kehadiran] = setAngka($inp[d_tunjangan_kehadiran]);
	$inp[d_tunjangan_lain] = setAngka($inp[d_tunjangan_lain]);
	$inp[d_tunjangan_khusus] = setAngka($inp[d_tunjangan_khusus]);
	$inp[d_lain_lain] = setAngka($inp[d_lain_lain]);
	$inp[d_tunai_tetap] = setAngka($inp[d_tunai_tetap]);
	
	$sql = "update emp_par set nama_perusahaan = '$inp[nama_perusahaan]', tipe_permintaan = '$inp[tipe_permintaan]', tanggal_buat = '$inp[tanggal_buat]', nama = '$inp[nama]', nik = '$inp[nik]', tanggal_lahir = '$inp[tanggal_lahir]', gender = '$inp[gender]', tanggal_masuk = '$inp[tanggal_masuk]', term_sebelum = '$inp[term_sebelum]', term_usulan = '$inp[term_usulan]', status_kawin = '$inp[status_kawin]', status_rekrutmen = '$inp[status_rekrutmen]', s_jabatan = '$inp[s_jabatan]', s_tingkatan = '$inp[s_tingkatan]', s_posisi_atasan = '$inp[s_posisi_atasan]', s_lokasi_kerja = '$inp[s_lokasi_kerja]', s_divisi = '$inp[s_divisi]', d_jabatan = '$inp[d_jabatan]', d_tingkatan = '$inp[d_tingkatan]', d_posisi_atasan = '$inp[d_posisi_atasan]', d_lokasi_kerja = '$inp[d_lokasi_kerja]', d_divisi = '$inp[d_divisi]', status_karyawan = '$inp[status_karyawan]', keterangan = '$inp[keterangan]', s_gaji_pokok = '$inp[s_gaji_pokok]', s_tunjangan_jabatan = '$inp[s_tunjangan_jabatan]', s_tunjangan_operasional = '$inp[s_tunjangan_operasional]', s_tunjangan_kehadiran = '$inp[s_tunjangan_kehadiran]', s_tunjangan_lain = '$inp[s_tunjangan_lain]', s_tunjangan_khusus = '$inp[s_tunjangan_khusus]', s_lain_lain = '$inp[s_lain_lain]', s_tunai_tetap = '$inp[s_tunai_tetap]',  d_gaji_pokok = '$inp[d_gaji_pokok]', d_tunjangan_jabatan = '$inp[d_tunjangan_jabatan]', d_tunjangan_operasional = '$inp[d_tunjangan_operasional]', d_tunjangan_kehadiran = '$inp[d_tunjangan_kehadiran]', d_tunjangan_lain = '$inp[d_tunjangan_lain]', d_tunjangan_khusus = '$inp[d_tunjangan_khusus]', d_lain_lain = '$inp[d_lain_lain]', d_tunai_tetap = '$inp[d_tunai_tetap]', tanggal_berlaku = '$inp[tanggal_berlaku]', update_by = '$cUsername', update_date = '".date('Y-m-d H:i:s')."' where id_par = '$par[id]'";
	db($sql);

	echo "<script>alert('UPDATE DATA BERHASIL');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus(){
	global $par;

	$sql="delete from emp_par where id_par='$par[id]'";
	db($sql);

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}	

function lihat(){
	global $s, $par, $arrTitle, $menuAccess;		

	$cols = 7;
	$text = table($cols, array($cols-1, $cols));

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p>					
					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
					<input type=\"button\" id=\"sFilter\" value=\"+\" class=\"btn btn_search btn-small\" onclick=\"showFilter()\" />
					<input type=\"button\" style=\"display:none;\" id=\"hFilter\" value=\"-\" class=\"btn btn_search btn-small\" onclick=\"hideFilter()\" /> 
				</p>
			</div>
			<div id=\"pos_r\" style=\"float:right; margin-top:5px;\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
				$text.="
			</div>	
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"75\">NPP</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">Tipe</th>
					<th width=\"100\">Dibuat</th>
					<th width=\"100\">Berlaku</th>
					<th width=\"100\">Kontrol</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<script type=\"text/javascript\">
		jQuery(document).ready(function(){
			jQuery(\"#btnExpExcel\").click(function (e) {
				e.preventDefault();
				
				var fSearch = jQuery(\"#fSearch\").val();
								
				window.location='?par[mode]=xls&par[fSearch]='+fSearch+'".getPar($par,"mode, fSearch")."';
			});
		});
	</script>";

	return $text;
}

function lData(){
	global $s, $par, $menuAccess, $arrParameter;

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
	$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

	$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
	$sWhere= " where t2.status='".$status."'";

	if (!empty($_GET['fSearch']))
		$sWhere.= " and (			
		lower(t2.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'	
		or lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
		)";

    if(!empty($_GET['aSearch'])) $sWhere.=" and t3.location='".$_GET['aSearch']."'";	
    if(!empty($_GET['bSearch'])) $sWhere.=" and t3.payroll_id='".$_GET['bSearch']."'";	
    if(!empty($_GET['cSearch'])) $sWhere.=" and t3.rank='".$_GET['cSearch']."'";	
    if(!empty($_GET['dSearch'])) $sWhere.=" and t3.grade='".$_GET['dSearch']."'";	
    if(!empty($_GET['eSearch'])) $sWhere.=" and t3.dept_id='".$_GET['eSearch']."'";	
    if(!empty($_GET['gSearch'])) $sWhere.=" and t3.area='".$_GET['gSearch']."'";

	$arrOrder = array(	
		"t2.name",
		"t2.name",
		"t2.reg_no",
		"t1.bank_id",
		"t1.account_no",	
		"t1.branch",
		"t2.join_date",
	);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
	$sql="select nik, nama, tipe_permintaan, tanggal_buat, tanggal_berlaku from emp_par order by id_par $sLimit";
	$res=db($sql);

	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(id_par) from emp_par"),
		"aaData" => array(),
	);

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory = 'TR'");

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){	
		$no++;
		$controlEmp="";

		if(isset($menuAccess[$s]["edit"]))
			$controlEmp.="<a href=\"?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";				
		if(isset($menuAccess[$s]["delete"]))
			$controlEmp.="<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\" ><span>Delete</span></a>";				

		$data=array(
			"<div align=\"center\">".$no.".</div>",				
			"<div align=\"center\">".$r[nik]."</div>",
			"<div align=\"left\">".strtoupper($r[nama])."</div>",			
			"<div align=\"left\">".$arrMaster[$r[tipe_permintaan]]."</div>",
			"<div align=\"center\">".getTanggal($r[tanggal_buat])."</div>",
			"<div align=\"center\">".getTanggal($r[tanggal_berlaku])."</div>",			
			"<div align=\"center\">".$controlEmp."</div>",
		);

		$json['aaData'][]=$data;
	}
	return json_encode($json);
}


function form(){
	global $s, $par, $arrTitle;

	$sql = "select * from emp_par where id_par = '$par[id]'";
	$res = db($sql);
	$r = mysql_fetch_array($res);

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."	
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div style=\"top:10px; right:25px; position:absolute\">				
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Simpan\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id") . "';\"/>
			</div>
            <fieldset>
                <legend> <b>GENERAL </b></legend>
				<table style=\"width:100%\">
					<tr>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Nama Perusahaan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[nama_perusahaan]\" name=\"inp[nama_perusahaan]\" class=\"mediuminput\" value=\"$r[nama_perusahaan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tipe Permintaan</label>
                            <div class=\"field\">
                                ".comboData("select * from mst_data where kodeCategory='TR' and statusData='t' order by namaData", "kodeData", "namaData", "inp[tipe_permintaan]", " ", $r[tipe_permintaan], "", "300px","chosen-select")."
                            </div>
                        </p>
					</td>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Tanggal Dibuat</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[tanggal_buat]\" name=\"inp[tanggal_buat]\" value=\"".getTanggal($r[tanggal_buat])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
                            </div>
                        </p>
					</td>
					</tr>
				</table>
			</fieldset>
			<br clear=\"all\"/>
			<fieldset>
				<legend> <b>DATA PEGAWAI </b></legend>
				<table style=\"width:100%\">
					<tr>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Nama Karyawan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[nama]\" name=\"inp[nama]\" class=\"mediuminput\" value=\"$r[nama]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">NPP Karyawan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[nik]\" name=\"inp[nik]\" class=\"mediuminput\" value=\"$r[nik]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tanggal Lahir</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[tanggal_lahir]\" name=\"inp[tanggal_lahir]\" value=\"".getTanggal($r[tanggal_lahir])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Jenis Kelamin</label>
                            <div class=\"field\">     
                                <input type=\"radio\" id=\"male\" name=\"inp[gender]\" value=\"l\" $male /> <span class=\"sradio\">Pria</span>
                                <input type=\"radio\" id=\"female\" name=\"inp[gender]\" value=\"p\" $female /> <span class=\"sradio\">Wanita</span>       
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Status Rekrutmen</label>
                            <div class=\"field\">
                                ".comboData("select * from mst_data where kodeCategory='SR' and statusData='t' order by namaData", "kodeData", "namaData", "inp[status_rekrutmen]", " ", $r[status_rekrutmen], "", "300px","chosen-select")."
                            </div>
                        </p>
					</td>
					<td style=\"width:50%\">
                        <p>
                            <label class=\"l-input-small2\">Tanggal Bergabung</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[tanggal_masuk]\" name=\"inp[tanggal_masuk]\" value=\"".getTanggal($r[tanggal_masuk])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Term Sebelumnya</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[term_sebelum]\" name=\"inp[term_sebelum]\" class=\"mediuminput\" value=\"$r[term_sebelum]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Term Usulan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[term_usulan]\" name=\"inp[term_usulan]\" class=\"mediuminput\" value=\"$r[term_usulan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Status Perkawinan</label>
                            <div class=\"field\">
                                ".comboData("select * from mst_data where kodeCategory='S08' and statusData='t' order by namaData", "kodeData", "namaData", "inp[status_kawin]", " ", $r[status_kawin], "", "300px","chosen-select")."
                            </div>
                        </p>
					</td>
					</tr>
				</table>
            </fieldset>
            <br clear=\"all\"/>
            <fieldset>
                <legend> <b>DATA POSISI </b></legend>
                <table style=\"width:100%\">
                    <tr>
                    <td style=\"width:50%\">
                        <div class=\"widgetbox\">
                            <div class=\"title\"><h3>SAAT INI</h3></div>
                        </div>
                        <p>
                            <label class=\"l-input-small2\">Jabatan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[s_jabatan]\" name=\"inp[s_jabatan]\" class=\"mediuminput\" value=\"$r[s_jabatan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Kel. Jab/Tingkatan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[s_tingkatan]\" name=\"inp[s_tingkatan]\" class=\"mediuminput\" value=\"$r[s_tingkatan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Posisi Atasan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[s_posisi_atasan]\" name=\"inp[s_posisi_atasan]\" class=\"mediuminput\" value=\"$r[s_posisi_atasan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Lokasi Kerja</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[s_lokasi_kerja]\" name=\"inp[s_lokasi_kerja]\" class=\"mediuminput\" value=\"$r[s_lokasi_kerja]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Divisi/Departemen</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[s_divisi]\" name=\"inp[s_divisi]\" class=\"mediuminput\" value=\"$r[s_divisi]\" />	
                            </div>
                        </p>
                    </td>
                    <td style=\"width:50%\">
                        <div class=\"widgetbox\">
                            <div class=\"title\"><h3>YANG DIUSULKAN</h3></div>
                        </div>
                        <p>
                            <label class=\"l-input-small2\">Jabatan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[d_jabatan]\" name=\"inp[d_jabatan]\" class=\"mediuminput\" value=\"$r[d_jabatan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Kel. Jab/Tingkatan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[d_tingkatan]\" name=\"inp[d_tingkatan]\" class=\"mediuminput\" value=\"$r[d_tingkatan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Posisi Atasan</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[d_posisi_atasan]\" name=\"inp[d_posisi_atasan]\" class=\"mediuminput\" value=\"$r[d_posisi_atasan]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Lokasi Kerja</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[d_lokasi_kerja]\" name=\"inp[d_lokasi_kerja]\" class=\"mediuminput\" value=\"$r[d_lokasi_kerja]\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Divisi/Departemen</label>
                            <div class=\"field\">
                                <input type=\"text\" id=\"inp[d_divisi]\" name=\"inp[d_divisi]\" class=\"mediuminput\" value=\"$r[d_divisi]\" />	
                            </div>
                        </p>
                    </td>
                    </tr>
                </table>
                <p>
                    <label class=\"l-input-small\">Status Karyawan</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[status_karyawan]\" name=\"inp[status_karyawan]\" class=\"mediuminput\" value=\"$r[status_karyawan]\" />	
                    </div>
                </p>
                <p>
                    <label class=\"l-input-small\">Keterangan, Alasan</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\" class=\"mediuminput\" value=\"$r[keterangan]\" />	
                    </div>
                </p>
            </fieldset>
            <br clear=\"all\"/>
            <fieldset>
                <legend> <b>DATA GAJI </b></legend>
                <table style=\"width:100%\">
                    <tr>
                    <td style=\"width:50%\">
                        <div class=\"widgetbox\">
                            <div class=\"title\"><h3>SAAT INI</h3></div>
                        </div>
                        <p>
                            <label class=\"l-input-small2\">Gaji Pokok</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_gaji_pokok]\" name=\"inp[s_gaji_pokok]\" class=\"mediuminput\" value=\"".getAngka($r[s_gaji_pokok])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Jabatan</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_tunjangan_jabatan]\" name=\"inp[s_tunjangan_jabatan]\" class=\"mediuminput\" value=\"".getAngka($r[s_tunjangan_jabatan])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Operasional</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_tunjangan_operasional]\" name=\"inp[s_tunjangan_operasional]\" class=\"mediuminput\" value=\"".getAngka($r[s_tunjangan_operasional])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Kehadiran</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_tunjangan_kehadiran]\" name=\"inp[s_tunjangan_kehadiran]\" class=\"mediuminput\" value=\"".getAngka($r[s_tunjangan_kehadiran])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Lain-lain</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_tunjangan_lain]\" name=\"inp[s_tunjangan_lain]\" class=\"mediuminput\" value=\"".getAngka($r[s_tunjangan_lain])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Khusus</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_tunjangan_khusus]\" name=\"inp[s_tunjangan_khusus]\" class=\"mediuminput\" value=\"".getAngka($r[s_tunjangan_khusus])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Lain-Lain</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_lain_lain]\" name=\"inp[s_lain_lain]\" class=\"mediuminput\" value=\"".getAngka($r[s_lain_lain])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunai Tetap</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[s_tunai_tetap]\" name=\"inp[s_tunai_tetap]\" class=\"mediuminput\" value=\"".getAngka($r[s_tunai_tetap])."\" />	
                            </div>
                        </p>
                    </td>
                    <td style=\"width:50%\">
                        <div class=\"widgetbox\">
                            <div class=\"title\"><h3>YANG DIUSULKAN</h3></div>
                        </div>
                        <p>
                            <label class=\"l-input-small2\">Gaji Pokok</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_gaji_pokok]\" name=\"inp[d_gaji_pokok]\" class=\"mediuminput\" value=\"".getAngka($r[d_gaji_pokok])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Jabatan</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_tunjangan_jabatan]\" name=\"inp[d_tunjangan_jabatan]\" class=\"mediuminput\" value=\"".getAngka($r[d_tunjangan_jabatan])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Operasional</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_tunjangan_operasional]\" name=\"inp[d_tunjangan_operasional]\" class=\"mediuminput\" value=\"".getAngka($r[d_tunjangan_operasional])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Kehadiran</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_tunjangan_kehadiran]\" name=\"inp[d_tunjangan_kehadiran]\" class=\"mediuminput\" value=\"".getAngka($r[d_tunjangan_kehadiran])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Lain-lain</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_tunjangan_lain]\" name=\"inp[d_tunjangan_lain]\" class=\"mediuminput\" value=\"".getAngka($r[d_tunjangan_lain])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunjangan Khusus</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_tunjangan_khusus]\" name=\"inp[d_tunjangan_khusus]\" class=\"mediuminput\" value=\"".getAngka($r[d_tunjangan_khusus])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Lain-Lain</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_lain_lain]\" name=\"inp[d_lain_lain]\" class=\"mediuminput\" value=\"".getAngka($r[d_lain_lain])."\" />	
                            </div>
                        </p>
                        <p>
                            <label class=\"l-input-small2\">Tunai Tetap</label>
                            <div class=\"field\">
                                <input onkeyup=\"cekAngka(this);\" type=\"text\" id=\"inp[d_tunai_tetap]\" name=\"inp[d_tunai_tetap]\" class=\"mediuminput\" value=\"".getAngka($r[d_tunai_tetap])."\" />	
                            </div>
                        </p>
                    </td>
                    </tr>
                </table>
                <p>
                    <label class=\"l-input-small\">Tanggal Berlaku</label>
                    <div class=\"field\">
                        <input type=\"text\" id=\"inp[tanggal_berlaku]\" name=\"inp[tanggal_berlaku]\" value=\"".getTanggal($r[tanggal_berlaku])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
                    </div>
                </p>
            </fieldset>
		</form>	
	</div>";

	return $text;
}

function xls(){		
	global $s, $arrTitle, $arrParameter, $cNama, $fFile, $par;
    require_once 'plugins/PHPExcel.php';
    
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);	

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN DATA BANK');
	// $objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulan])." ".$par[tahun]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->setCellValue('A4','NO.');
    $objPHPExcel->getActiveSheet()->setCellValue('B4','NPP PEGAWAI');
    $objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA PEGAWAI');
    $objPHPExcel->getActiveSheet()->setCellValue('D4','BANK');
    $objPHPExcel->getActiveSheet()->setCellValue('E4','CABANG');
    $objPHPExcel->getActiveSheet()->setCellValue('F4','NO. REKENING');
    $objPHPExcel->getActiveSheet()->setCellValue('G4','ATAS NAMA');
	
	$rows = 5;

    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
	$sWhere= " where t2.status='".$status."'";

	if (!empty($par['fSearch']))
		$sWhere.= " and (			
		lower(t2.name) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'	
		or lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
		)";

    if(!empty($par['aSearch'])) $sWhere.=" and t3.location='".$par['aSearch']."'";	
    if(!empty($par['bSearch'])) $sWhere.=" and t3.payroll_id='".$par['bSearch']."'";	
    if(!empty($par['cSearch'])) $sWhere.=" and t3.rank='".$par['cSearch']."'";	
    if(!empty($par['dSearch'])) $sWhere.=" and t3.grade='".$par['dSearch']."'";	
    if(!empty($par['eSearch'])) $sWhere.=" and t3.dept_id='".$par['eSearch']."'";	
    if(!empty($par['gSearch'])) $sWhere.=" and t3.area='".$par['gSearch']."'";

	$sql="SELECT t2.name as namaPegawai, t2.reg_no, t1.bank_id, t1.branch, t1.account_no, t1.account_name from emp_bank t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $sWhere order by t2.name";
    $res = db($sql);
    $no=0;
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('S13')");
    while ($r = mysql_fetch_array($res)) {
		$no++;
		
		$r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Perempuan";
		
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[reg_no]);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[namaPegawai]));
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrMaster[$r[bank_id]]);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[branch]);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F'.$rows, $r[account_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[account_name]);
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $rows++;							
    }

    $rows--;
    $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle('A6:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

    $objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower($arrTitle[$s])));
    $objPHPExcel->setActiveSheetIndex(0);

            // Save Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($fFile.ucwords(strtolower($arrTitle[$s])).".xls");
}

function getContent($par){
	global $s,$_submit,$menuAccess;

	switch($par[mode]){
		case "end":
            if(isset($menuAccess[$s]["add"])) $text = endProses();
        break;
        case "dat":
            if(isset($menuAccess[$s]["add"])) $text = setData();
        break;
        case "tab":
            if(isset($menuAccess[$s]["add"])) $text = setFile();
        break;
        case "upl":
            $text = isset($menuAccess[$s]["add"]) ? formUpload() : lihat();
        break;
		case "lst":
			$text=lData();
		break;	
		case "del":
			$text=hapus();
		break;
		case "pegawai":
			$text = pegawai();
		break;
		case "add":
			if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
		break;	
		case "edit":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
		break;
		case "delFoto":
			if (isset($menuAccess[$s]["edit"])) $text = hapusFoto(); else $text = lihat();
		break;
		default:
			$text = lihat();
		break;
	}

	return $text;
}	
?>