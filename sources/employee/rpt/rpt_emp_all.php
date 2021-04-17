<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "files/export/";

function lihat(){
	global $s, $par, $arrTitle;		

	$cols = 44;
    $text = table($cols, "", "lst", "true", "h");
    
    $_GET['kSearch'] = empty($_GET['kSearch']) ? date('Y-m-d') : $_GET['kSearch'];

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
				<script>
					function showFilter(){
						jQuery('#form_filter').show('slow');
						jQuery('#sFilter').hide();
						jQuery('#hFilter').show();
					}            
					function hideFilter(){
						jQuery('#form_filter').hide('slow');
						jQuery('#sFilter').show();
						jQuery('#hFilter').hide();
					}
				</script>
			</div>
			<div id=\"pos_r\" style=\"float:right; margin-top:5px;\">
				<a href=\"#\"id=\"btnExpExcel\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>	
		</form>
		<fieldset id=\"form_filter\" style=\"display:none;\">
		<form id=\"form\" class=\"stdform\">
			<table width=\"100%\">
				<tr>
                    <td width=\"50%\"> 
                        <p>
							<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Status Pegawai</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S05' ORDER BY urutanData", "kodeData", "namaData", "hSearch", "- Semua Status -", $_GET['hSearch'], "onchange=\"\"", "250px", "chosen-select")."
								<style>
									#hSearch_chosen{min-width:250px;}
								</style>
							</div>
						</p>              
						<p>
							<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Lokasi</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' ORDER BY urutanData", "kodeData", "namaData", "aSearch", "- Semua Lokasi -", $_GET['aSearch'], "onchange=\"\"", "250px", "chosen-select")."
								<style>
									#aSearch_chosen{min-width:250px;}
								</style>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Pangkat</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY namaData", "kodeData", "namaData", "cSearch", "- Semua Pangkat -", $_GET['cSearch'], "onchange=\"\"", "250px", "chosen-select")."
								<style>
									#cSearch_chosen{min-width:250px;}
								</style>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Departemen</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("select t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1
								JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk
								JOIN mst_data t3 ON t3.kodeData=t2.kodeInduk
								where t3.kodeCategory='X04' order by t1.urutanData", "id", "description", "eSearch", "- Semua Departemen -", $_GET['eSearch'], "", "250px", "chosen-select")."
								<style>
									#eSearch_chosen{min-width:250px;}
								</style>
							</div>
                        </p>
                        <p>
                            <label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Periode Masuk</label>
                            <div class=\"field\" style=\"margin-left:200px;\">
                                <input type=\"text\" id=\"jSearch\" name=\"jSearch\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($_GET['jSearch']) . "\" class=\"vsmallinput hasDatePicker\"/> s/d 
                                <input type=\"text\" id=\"kSearch\" name=\"kSearch\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($_GET['kSearch']) . "\" class=\"vsmallinput hasDatePicker\"/>
                            </div>
                        </p>
					</td>
                    <td width=\"50%\">
                        <p>
                            <label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Tipe Pegawai</label>
                            <div class=\"field\" style=\"margin-left:200px;\">
                                ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S04' ORDER BY urutanData", "kodeData", "namaData", "iSearch", "- Semua Tipe -", $_GET['iSearch'], "onchange=\"\"", "250px", "chosen-select")."
                                <style>
                                    #iSearch_chosen{min-width:250px;}
                                </style>
                            </div>
                        </p> 
						<p>
							<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Jenis</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","bSearch","- Semua Jenis -",$_GET['bSearch'],"onchange=\"\"","210px;","chosen-select")."
								<style>
									#bSearch_chosen{min-width:250px;}
								</style>
							</div>
						</p> 
						<p>
							<label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Grade</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S10' ORDER BY namaData", "kodeData", "namaData", "dSearch", "- Semua Grade -", $_GET['dSearch'], "onchange=\"\"", "250px", "chosen-select")."
								<style>
									#dSearch_chosen{min-width:250px;}
								</style>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Area Kerja</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'AK' ORDER BY namaData", "kodeData", "namaData", "gSearch", "- Semua Area -", $_GET['gSearch'], "onchange=\"\"", "250px", "chosen-select")."
								<style>
									#gSearch_chosen{min-width:250px;}
								</style>
							</div>
						</p>
					</td>
				</tr>
			</table>
		</form>
		</fieldset>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
            <thead>
                <tr>
                    <th style=\"min-width:20px;\">No.</th>
                    <th style=\"min-width:200px;\">NAMA LENGKAP</th>
                    <th style=\"min-width:100px;\">NIK</th>
                    <th style=\"min-width:100px;\">STATUS PEGAWAI</th>
                    <th style=\"min-width:100px;\">LOKASI</th>
                    <th style=\"min-width:100px;\">JABATAN</th>
                    <th style=\"min-width:100px;\">GOLONGAN</th>
                    <th style=\"min-width:100px;\">CABANG</th>
                    <th style=\"min-width:100px;\">DIVISI</th>
                    <th style=\"min-width:100px;\">DEPARTEMEN</th>
                    <th style=\"min-width:100px;\">UNIT</th>
                    <th style=\"min-width:100px;\">JENIS KELAMIN</th>
                    <th style=\"min-width:100px;\">TEMPAT LAHIR</th>
                    <th style=\"min-width:100px;\">TANGGAL LAHIR</th>
                    <th style=\"min-width:100px;\">USIA</th>
                    <th style=\"min-width:100px;\">NO KTP</th>
                    <th style=\"min-width:100px;\">AGAMA</th>
                    <th style=\"min-width:100px;\">PENDIDIKAN TERAKHIR</th>
                    <th style=\"min-width:100px;\">JURUSAN</th>
                    <th style=\"min-width:100px;\">NO REKENING</th>
                    <th style=\"min-width:100px;\">ALAMAT DOMISILI</th>
                    <th style=\"min-width:100px;\">PROPINSI</th>
                    <th style=\"min-width:100px;\">KOTA</th>
                    <th style=\"min-width:100px;\">ALAMAT KTP</th>
                    <th style=\"min-width:100px;\">PROPINSI</th>
                    <th style=\"min-width:100px;\">KOTA</th>
                    <th style=\"min-width:100px;\">NO TELP</th>
                    <th style=\"min-width:100px;\">NO HP</th>
                    <th style=\"min-width:100px;\">EMAIL</th>
                    <th style=\"min-width:100px;\">STATUS PERKAWINAN</th>
                    <th style=\"min-width:100px;\">JUMLAH ANAK</th>
                    <th style=\"min-width:100px;\">NAMA PASANGAN</th>
                    <th style=\"min-width:100px;\">ANAK 1</th>
                    <th style=\"min-width:100px;\">ANAK 2</th>
                    <th style=\"min-width:100px;\">ANAK 3</th>
                    <th style=\"min-width:100px;\">AYAH</th>
                    <th style=\"min-width:100px;\">IBU</th>
                    <th style=\"min-width:100px;\">GOL. DARAH</th>
                    <th style=\"min-width:100px;\">NO NPWP</th>
                    <th style=\"min-width:100px;\">NO BPJS TK</th>
                    <th style=\"min-width:100px;\">NO BPJS KESEHATAN</th>
                    <th style=\"min-width:100px;\">MULAI BEKERJA</th>
                    <th style=\"min-width:100px;\">LAMA KERJA</th>
                    <th style=\"min-width:100px;\">SALARY</th>
                </tr>  
        </thead>
			<tbody></tbody>
		</table>
	</div>
	<script type=\"text/javascript\">
		jQuery(document).ready(function(){
			jQuery(\"#btnExpExcel\").click(function (e) {
				e.preventDefault();
				
				var aSearch = jQuery(\"#aSearch\").val();
				var bSearch = jQuery(\"#bSearch\").val();
				var cSearch = jQuery(\"#cSearch\").val();
				var dSearch = jQuery(\"#dSearch\").val();
				var eSearch = jQuery(\"#eSearch\").val();
				var fSearch = jQuery(\"#fSearch\").val();
				var gSearch = jQuery(\"#gSearch\").val();
				var hSearch = jQuery(\"#hSearch\").val();
				var iSearch = jQuery(\"#iSearch\").val();
				var jSearch = jQuery(\"#jSearch\").val();
				
				window.location='?par[mode]=xls&par[aSearch]='+aSearch+'&par[bSearch]='+bSearch+'&par[cSearch]='+cSearch+'&par[dSearch]='+dSearch+'&par[eSearch]='+eSearch+'&par[fSearch]='+fSearch+'&par[gSearch]='+gSearch+'&par[hSearch]='+hSearch+'&par[iSearch]='+iSearch+'&par[jSearch]='+jSearch+'".getPar($par,"mode, aSearch, bSearch, cSearch, dSearch, eSearch, fSearch, gSearch, hSearch, iSearch, jSearch")."';
			});
		});
	</script>";

	if($par[mode] == "xls"){			
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=LAPORAN ALL PEGAWAI.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}

	return $text;
}

function lData(){
    global $arrParameter;
    
    $arrMaster= arrayQuery("select kodeData, namaData from mst_data");
	
	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

	// $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
	$sWhere= " where t1.id is not null";

	if (!empty($_GET['fSearch']))
		$sWhere.= " and ( 
		lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'	
		or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
		)";

	if(!empty($_GET['aSearch'])) $sWhere.=" and t2.location='".$_GET['aSearch']."'";	
	if(!empty($_GET['bSearch'])) $sWhere.=" and t2.payroll_id='".$_GET['bSearch']."'";	
	if(!empty($_GET['cSearch'])) $sWhere.=" and t2.rank='".$_GET['cSearch']."'";	
	if(!empty($_GET['dSearch'])) $sWhere.=" and t2.grade='".$_GET['dSearch']."'";	
	if(!empty($_GET['eSearch'])) $sWhere.=" and t2.dept_id='".$_GET['eSearch']."'";	
	if(!empty($_GET['gSearch'])) $sWhere.=" and t2.area='".$_GET['gSearch']."'";
	if(!empty($_GET['hSearch'])) $sWhere.=" and t1.status='".$_GET['hSearch']."'";
    if(!empty($_GET['iSearch'])) $sWhere.=" and t1.cat='".$_GET['iSearch']."'";
    if(!empty($_GET['jSearch'])) $sWhere.=" and t1.join_date BETWEEN '".setTanggal($_GET['jSearch'])."' AND '".setTanggal($_GET['kSearch'])."'";

	$arrOrder = array(	
		"t2.name",
		"t2.name",
		"t2.reg_no",
		"t1.trn_no",
		"t1.sk_subject",	
		"t1.sk_cat",
		"t1.sk_type",
		"t1.sk_date",
	);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
	$sql="select t1.id, t1.name, t1.reg_no, t1.cat, t2.location, t2.rank, t2.grade, t2.dir_id, t2.div_id, t2.dept_id, t2.unit_id, t1.gender, t1.birth_place, t1.birth_date, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn') as umur, t1.ktp_no, t1.religion, (SELECT concat(x2.namaData, '\t', x1.edu_dept) FROM emp_edu x1 JOIN mst_data x2 ON x1.edu_type=x2.kodeData WHERE x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) eduName, t3.account_no, t1.dom_address, t1.dom_prov, t1.dom_city, t1.ktp_address, t1.ktp_prov, t1.ktp_city, t1.cell_no, t1.phone_no, t1.email, t1.marital, t1.blood_type, t1.npwp_no, t1.bpjs_no, t1.bpjs_no_ks, t1.join_date, replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    when leave_date = '0000-00-00' THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') masaKerja, t4.nilaiPokok from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_bank t3 on (t1.id = t3.parent_id) left join pay_pokok t4 on t1.id = t4.idPegawai $sWhere group by t1.id order by t1.name $sLimit";
	$res=db($sql);

	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(distinct(t1.id)) from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_bank t3 on (t1.id = t3.parent_id) left join pay_pokok t4 on t1.id = t4.idPegawai $sWhere"),
		"aaData" => array(),
	);

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){		
        $no++;		
        
        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-laki";
        list($r[eduType], $r[eduDept]) = explode("\t", $r[eduName]);

        $r[totalAnak] = getField("(SELECT COUNT(*) FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666')");
        $r[namaPasangan] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel NOT IN ('666', '667', '668') LIMIT 1)");
        $r[namaAnak1] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666' LIMIT 0,1)");
        $r[namaAnak2] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666' LIMIT 1,2)");
        $r[namaAnak3] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666' LIMIT 2,3)");
        $r[namaBapak] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '668')");
        $r[namaIbu] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '667')");

		$data=array(
			"<div align=\"center\">".$no.".</div>",				
			"<div align=\"left\">".strtoupper($r[name])."</div>",
			"<div align=\"center\">".$r[reg_no]."</div>",
			"<div align=\"left\">".$arrMaster[$r[cat]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[location]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[rank]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[grade]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[dir_id]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[div_id]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[dept_id]]."</div>",
			"<div align=\"left\">".$arrMaster[$r[unit_id]]."</div>",
            "<div align=\"left\">".$r[gender]."</div>",
            "<div align=\"left\">".$arrMaster[$r[birth_place]]."</div>",
            "<div align=\"center\">".getTanggal($r[birth_date])."</div>",
            "<div align=\"right\">".$r[umur]."</div>",
            "<div align=\"center\">".$r[ktp_no]."</div>",
            "<div align=\"left\">".$arrMaster[$r[religion]]."</div>",
            "<div align=\"left\">".$r[eduType]."</div>",
            "<div align=\"left\">".$arrMaster[$r[eduDept]]."</div>",
            "<div align=\"right\">".$r[account_no]."</div>",
            "<div align=\"left\">".$r[dom_address]."</div>",
            "<div align=\"left\">".$arrMaster[$r[dom_prov]]."</div>",
            "<div align=\"left\">".$arrMaster[$r[dom_city]]."</div>",
            "<div align=\"left\">".$r[ktp_address]."</div>",
            "<div align=\"left\">".$arrMaster[$r[ktp_prov]]."</div>",
            "<div align=\"left\">".$arrMaster[$r[ktp_city]]."</div>",
            "<div align=\"right\">".$r[phone_no]."</div>",
            "<div align=\"right\">".$r[cell_no]."</div>",
            "<div align=\"left\">".$r[email]."</div>",
            "<div align=\"left\">".$arrMaster[$r[marital]]."</div>",
            "<div align=\"left\">".$r[totalAnak]."</div>",
            "<div align=\"left\">".$r[namaPasangan]."</div>",
            "<div align=\"left\">".$r[namaAnak1]."</div>",
            "<div align=\"left\">".$r[namaAnak2]."</div>",
            "<div align=\"left\">".$r[namaAnak3]."</div>",
            "<div align=\"left\">".$r[namaBapak]."</div>",
            "<div align=\"left\">".$r[namaIbu]."</div>",
            "<div align=\"left\">".$r[blood_type]."</div>",
            "<div align=\"right\">".$r[npwp_no]."</div>",
            "<div align=\"right\">".$r[bpjs_no]."</div>",
            "<div align=\"right\">".$r[bpjs_no_ks]."</div>",
            "<div align=\"center\">".getTanggal($r[join_date])."</div>",
            "<div align=\"right\">".$r[masaKerja]."</div>",
            "<div align=\"right\">".getAngka($r[nilaiPokok])."</div>",

		);

		$json['aaData'][]=$data;
	}

	return json_encode($json);
}

function xls(){		
    global $s, $arrTitle, $arrParameter, $cNama, $fFile, $par;
    
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    require_once 'plugins/PHPExcel.php';
    
	$objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)
	->setLastModifiedBy($cNama)
	->setTitle($arrTitle[$s]);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(40);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(40);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AN')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AO')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AP')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('AQ')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('AR')->setWidth(20);

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:AR1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:AR2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:AR3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN DATA KERJA');

	$objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:AR4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->setCellValue('A4','NO.');
    $objPHPExcel->getActiveSheet()->setCellValue('B4','NAMA LENGKAP');
    $objPHPExcel->getActiveSheet()->setCellValue('C4','NIK');
    $objPHPExcel->getActiveSheet()->setCellValue('D4','STATUS PEGAWAI');
    $objPHPExcel->getActiveSheet()->setCellValue('E4','LOKASI');
    $objPHPExcel->getActiveSheet()->setCellValue('F4','JABATAN');
    $objPHPExcel->getActiveSheet()->setCellValue('G4','GOLONGAN');
	$objPHPExcel->getActiveSheet()->setCellValue('H4','CABANG');
	$objPHPExcel->getActiveSheet()->setCellValue('I4','DIVISI');
	$objPHPExcel->getActiveSheet()->setCellValue('J4','DEPARTEMEN');
	$objPHPExcel->getActiveSheet()->setCellValue('K4','UNIT');
	$objPHPExcel->getActiveSheet()->setCellValue('L4','JENIS KELAMIN');
	$objPHPExcel->getActiveSheet()->setCellValue('M4','TEMPAT LAHIR');
	$objPHPExcel->getActiveSheet()->setCellValue('N4','TANGGAL LAHIR');
	$objPHPExcel->getActiveSheet()->setCellValue('O4','USIA');
	$objPHPExcel->getActiveSheet()->setCellValue('P4','NO KTP');
	$objPHPExcel->getActiveSheet()->setCellValue('Q4','AGAMA');
	$objPHPExcel->getActiveSheet()->setCellValue('R4','PENDIDIKAN TERAKHIR');
	$objPHPExcel->getActiveSheet()->setCellValue('S4','JURUSAN');
	$objPHPExcel->getActiveSheet()->setCellValue('T4','NO REKENING');
	$objPHPExcel->getActiveSheet()->setCellValue('U4','ALAMAT DOMISILI');
	$objPHPExcel->getActiveSheet()->setCellValue('V4','PROPINSI');
	$objPHPExcel->getActiveSheet()->setCellValue('W4','KOTA');
	$objPHPExcel->getActiveSheet()->setCellValue('X4','ALAMAT KTP');
	$objPHPExcel->getActiveSheet()->setCellValue('Y4','PROPINSI');
	$objPHPExcel->getActiveSheet()->setCellValue('Z4','KOTA');
	$objPHPExcel->getActiveSheet()->setCellValue('AA4','NO TELP');
	$objPHPExcel->getActiveSheet()->setCellValue('AB4','NO HP');
	$objPHPExcel->getActiveSheet()->setCellValue('AC4','EMAIL');
	$objPHPExcel->getActiveSheet()->setCellValue('AD4','STATUS PERKAWINAN');
	$objPHPExcel->getActiveSheet()->setCellValue('AE4','JUMLAH ANAK');
	$objPHPExcel->getActiveSheet()->setCellValue('AF4','NAMA PASANGAN');
	$objPHPExcel->getActiveSheet()->setCellValue('AG4','ANAK 1');
	$objPHPExcel->getActiveSheet()->setCellValue('AH4','ANAK 2');
	$objPHPExcel->getActiveSheet()->setCellValue('AI4','ANAK 3');
	$objPHPExcel->getActiveSheet()->setCellValue('AJ4','AYAH');
	$objPHPExcel->getActiveSheet()->setCellValue('AK4','IBU');
	$objPHPExcel->getActiveSheet()->setCellValue('AL4','GOL. DARAH');
	$objPHPExcel->getActiveSheet()->setCellValue('AM4','NO. NPWP');
	$objPHPExcel->getActiveSheet()->setCellValue('AN4','NO. BPJS TK');
	$objPHPExcel->getActiveSheet()->setCellValue('AO4','NO. BPJS KESEHATAN');
	$objPHPExcel->getActiveSheet()->setCellValue('AP4','MULAI BEKERJA');
	$objPHPExcel->getActiveSheet()->setCellValue('AQ4','LAMA BEKERJA');
	$objPHPExcel->getActiveSheet()->setCellValue('AR4','SALARY');

	$rows = 5;

    // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
	$sWhere= " where t1.id is not null";

	if (!empty($par['fSearch']))
		$sWhere.= " and (			
		lower(t1.name) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'	
		or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($par['fSearch']))."%'
		)";

    if(!empty($par['aSearch'])) $sWhere.=" and t2.location='".$par['aSearch']."'";	
    if(!empty($par['bSearch'])) $sWhere.=" and t2.payroll_id='".$par['bSearch']."'";	
    if(!empty($par['cSearch'])) $sWhere.=" and t2.rank='".$par['cSearch']."'";	
    if(!empty($par['dSearch'])) $sWhere.=" and t2.grade='".$par['dSearch']."'";	
    if(!empty($par['eSearch'])) $sWhere.=" and t2.dept_id='".$par['eSearch']."'";	
    if(!empty($par['gSearch'])) $sWhere.=" and t2.area='".$par['gSearch']."'";
    if(!empty($par['hSearch'])) $sWhere.=" and t1.status='".$par['hSearch']."'";
    if(!empty($par['iSearch'])) $sWhere.=" and t1.cat='".$par['iSearch']."'";

	$sql="select t1.id, t1.name, t1.reg_no, t1.cat, t2.location, t2.rank, t2.grade, t2.dir_id, t2.div_id, t2.dept_id, t2.unit_id, t1.gender, t1.birth_place, t1.birth_date, CONCAT(TIMESTAMPDIFF(YEAR, t1.birth_date, CURRENT_DATE ),' thn') as umur, t1.ktp_no, t1.religion, (SELECT concat(x2.namaData, '\t', x1.edu_dept) FROM emp_edu x1 JOIN mst_data x2 ON x1.edu_type=x2.kodeData WHERE x1.parent_id=t1.id ORDER BY x1.edu_type DESC LIMIT 1) eduName, t3.account_no, t1.dom_address, t1.dom_prov, t1.dom_city, t1.ktp_address, t1.ktp_prov, t1.ktp_city, t1.cell_no, t1.phone_no, t1.email, t1.marital, t1.blood_type, t1.npwp_no, t1.bpjs_no, t1.bpjs_no_ks, t1.join_date, replace(
    case when coalesce(leave_date,NULL) IS NULL THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    when leave_date = '0000-00-00' THEN
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  CURRENT_DATE ) % 12, ' bln')
    ELSE
    CONCAT(TIMESTAMPDIFF(YEAR,  t1.join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, t1.join_date,  t1.leave_date) % 12, ' bln')
    END,' 0 bln','') masaKerja, t4.nilaiPokok from emp t1 join emp_phist t2 on (t1.id = t2.parent_id AND t2.status = '1') left join emp_bank t3 on (t1.id = t3.parent_id) left join pay_pokok t4 on t1.id = t4.idPegawai $sWhere group by t1.id order by t1.name";
    $res=db($sql);
    $no=intval($_GET['iDisplayStart']);
    while($r=mysql_fetch_array($res)){		
        $no++;		
        
        $r[gender] = $r[gender] == "F" ? "Perempuan" : "Laki-laki";
        list($r[eduType], $r[eduDept]) = explode("\t", $r[eduName]);

        $r[totalAnak] = getField("(SELECT COUNT(*) FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666')");
        $r[namaPasangan] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel NOT IN ('666', '667', '668') LIMIT 1)");
        $r[namaAnak1] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666' LIMIT 0,1)");
        $r[namaAnak2] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666' LIMIT 1,2)");
        $r[namaAnak3] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '666' LIMIT 2,3)");
        $r[namaBapak] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '668')");
        $r[namaIbu] = getField("(SELECT name FROM emp_family x1 WHERE x1.parent_id = '".$r[id]."' AND x1.rel = '667')");

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, strtoupper($r[name]));
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $arrMaster[$r[cat]]);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrMaster[$r[location]]);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $arrMaster[$r[rank]]);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $arrMaster[$r[grade]]);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $arrMaster[$r[dir_id]]);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $arrMaster[$r[div_id]]);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $arrMaster[$r[dept_id]]);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$rows, $arrMaster[$r[unit_id]]);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$rows, $r[gender]);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$rows, $arrMaster[$r[birth_place]]);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$rows, getTanggal($r[birth_date]));
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$rows, $r[umur]);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('P'.$rows, $r[ktp_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('Q'.$rows, $arrMaster[$r[religion]]);
        $objPHPExcel->getActiveSheet()->setCellValue('R'.$rows, $r[eduType]);
        $objPHPExcel->getActiveSheet()->setCellValue('S'.$rows, $arrMaster[$r[eduDept]]);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('T'.$rows, $r[account_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('U'.$rows, $r[dom_address]);
        $objPHPExcel->getActiveSheet()->setCellValue('V'.$rows, $arrMaster[$r[dom_prov]]);
        $objPHPExcel->getActiveSheet()->setCellValue('W'.$rows, $arrMaster[$r[dom_city]]);
        $objPHPExcel->getActiveSheet()->setCellValue('X'.$rows, $r[ktp_address]);
        $objPHPExcel->getActiveSheet()->setCellValue('Y'.$rows, $arrMaster[$r[ktp_prov]]);
        $objPHPExcel->getActiveSheet()->setCellValue('Z'.$rows, $arrMaster[$r[ktp_city]]);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('AA'.$rows, $r[phone_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('AB'.$rows, $r[cell_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('AC'.$rows, $r[email]);
        $objPHPExcel->getActiveSheet()->setCellValue('AD'.$rows, $arrMaster[$r[marital]]);
        $objPHPExcel->getActiveSheet()->setCellValue('AE'.$rows, $r[totalAnak]);
        $objPHPExcel->getActiveSheet()->setCellValue('AF'.$rows, $r[namaPasangan]);
        $objPHPExcel->getActiveSheet()->setCellValue('AG'.$rows, $r[namaAnak1]);
        $objPHPExcel->getActiveSheet()->setCellValue('AH'.$rows, $r[namaAnak2]);
        $objPHPExcel->getActiveSheet()->setCellValue('AI'.$rows, $r[namaAnak3]);
        $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$rows, $r[namaBapak]);
        $objPHPExcel->getActiveSheet()->setCellValue('AK'.$rows, $r[namaIbu]);
        $objPHPExcel->getActiveSheet()->setCellValue('AL'.$rows, $r[blood_type]);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('AM'.$rows, $r[npwp_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('AN'.$rows, $r[bpjs_no],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('AO'.$rows, $r[bpjs_no_ks],PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('AP'.$rows, getTanggal($r[join_date]));
        $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$rows, $r[masaKerja]);
        $objPHPExcel->getActiveSheet()->setCellValue('AR'.$rows, getAngka($r[nilaiPokok]));

        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':AR'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

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
    $objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('L4:L'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('M4:M'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('N4:N'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('O4:O'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('P4:P'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('Q4:Q'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('R4:R'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('S4:S'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('T4:T'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('U4:U'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('V4:V'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('W4:W'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('X4:X'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('Y4:Y'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('Z4:Z'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AA4:AA'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AB4:AB'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AC4:AC'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AD4:AD'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AE4:AE'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AF4:AF'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AG4:AG'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AH4:AH'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AI4:AI'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AJ4:AJ'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AK4:AK'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AL4:AL'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AM4:AM'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AN4:AN'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AO4:AO'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AP4:AP'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AQ4:AQ'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('AR4:AR'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->getStyle('A1:AR'.$rows)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:AR'.$rows)->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle('A6:AR'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

    $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	

    $objPHPExcel->getActiveSheet()->setTitle("LAPORAN ALL PEGAWAI");
    $objPHPExcel->setActiveSheetIndex(0);

            // Save Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($fFile."LAPORAN ALL PEGAWAI.xls");
}

function getContent($par){
	global $s;

	switch($par[mode]){
		case "lst":
			$text=lData();
		break;	
		default:
			$text = lihat();
		break;
	}

	return $text;
}	
?>