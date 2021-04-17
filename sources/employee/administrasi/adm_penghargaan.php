<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dFile = "files/emp/adm/penghargaan/";
$fFile = "files/export/";
$fLog = "files/logPenghargaan.log";

function setFile(){
	global $fFile,$fLog,$cID;

	if(!in_array(strtolower(substr($_FILES[fileData][name],-3)), array("xls")) && !in_array(strtolower(substr($_FILES[fileData][name],-4)), array("xlsx"))){
		return "file harus dalam format .xls atau .xlsx";
	}else{
		$fileUpload = $_FILES[fileData][tmp_name];
		$fileUpload_name = $_FILES[fileData][name];
		if(($fileUpload!="") and ($fileUpload!="none")){                       
			fileUpload($fileUpload,$fileUpload_name,$fFile);
			$fileData = md5($cID."-".date("Y-m-d H:i:s")).".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $fileData);

			if(file_exists($fLog))unlink($fLog);
			$fileName = fopen($fLog, "a+");
			fwrite($fileName, "START : ".date("Y-m-d H:i:s")."\r\n\r\n");
			fclose($fileName);

			return "fileData".$fileData;
		}
	}

}

function setData(){
	global $par,$fFile,$fLog;

	$inputFileName = $fFile.$par[fileData];
	require_once ('plugins/PHPExcel/IOFactory.php');           

	try {
		$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFileName);
	} catch(Exception $e) {
		die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
	}

	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();     

	$arrMaster = arrayQuery("select lower(trim(namaData)), kodeData from mst_data where kodeCategory IN('S18', 'S19')");

	$result=$par[rowData].". ";
	if($par[rowData] <= $highestRow){  
		$rowData = $sheet->rangeToArray('A' . $par[rowData] . ':I' . $par[rowData], NULL, TRUE, TRUE);             
		$dta = $rowData[0];
		$tRow = 6;

		if(!in_array(trim(strtolower($dta[1])), array("","NPP PEGAWAI"))){          
			$parentId = getField("select id from emp where reg_no = '".$dta[1]."'");
			
            $fileName = fopen($fLog, "a+");

			$idPenghargaan = getField("SELECT id from emp_reward order by id desc limit 1")+1;
			$sql = "insert into emp_reward set id = '$idPenghargaan', parent_id = '$parentId', rwd_no = '".$dta[3]."', rwd_subject = '".$dta[4]."', rwd_agency = '".$dta[5]."',  rwd_year = '".$dta[6]."', rwd_cat = '".$arrMaster[trim(strtolower($dta[7]))]."', rwd_type = '".$arrMaster[trim(strtolower($dta[8]))]."', cre_by = 'migrasi', cre_date = '".date('Y-m-d H:i:s')."'";
			db($sql);

			fwrite($fileName, "OK : ".$dta[3]."\t".$dta[2]."\r\n");

			fclose($fileName);
			sleep(1);

			$tRow++;
		}
        
        $rowData = $par[rowData] - 5;
        $highestRow = $highestRow - 5;
        $progresData = getAngka($rowData/$highestRow * 100);  
        
		return $progresData."\t(".$progresData."%) ".getAngka($rowData)." of ".getAngka($highestRow)."\t".$result;
	}
}

function endProses(){
	global $fLog;

	$fileName = fopen($fLog, "a+");    
	fwrite($fileName, "\r\nEND : ".date("Y-m-d H:i:s"));
	fclose($fileName);     
	sleep(1);

	return "import data selesai : ".getTanggal(date('Y-m-d'),"t").", ".date('H:i');
}

function formUpload(){
	global $s,$par,$arrTitle;    
	$text.="
	<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
			".getBread(ucwords("import data"))."
			<span class=\"pagedesc\">&nbsp;</span>                 
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form class=\"stdform\" enctype=\"multipart/form-data\">   
				<div id=\"formInput\" class=\"subcontent\">
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">
							<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:290px;\" maxlength=\"100\" />
							<div class=\"fakeupload\">
								<input type=\"file\" id=\"fileData\" name=\"fileData\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
							</div>
						</div>
					</p>                 
					<p>
						<div style=\"float:right; margin-top:10px; margin-right:150px;\"><a href=\"download.php?d=tmpPenghargaan\" class=\"detil\">* download template.xls</a></div>                
					</p>
				</div>
				<div id=\"prosesImg\" align=\"center\" style=\"display:none; position:absolute; left:50%; top:50%;\">                      
					<img src=\"styles/images/loaders/loader6.gif\">
				</div>
				<div id=\"progresBar\" class=\"progress\" style=\"display:none;\">                     
					<strong>Progress</strong> <span id=\"progresCnt\">(0%) </span>
					<div class=\"bar2\"><div id=\"persenBar\" class=\"value orangebar\" style=\"width: 0%; height:20px;\"></div></div>
				</div>                 
				<span id=\"progresRes\"></span>
				<div id=\"progresEnd\" class=\"progress\" style=\"margin-top:30px; display:none;\">                
					<a href=\"download.php?d=logPenghargaan\" class=\"btn btn1 btn_inboxi\"><span>Download Result</span></a>               
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"window.parent.location='index.php?".getPar($par,"mode")."';\"/>
				</div>
				<br clear=\"all\">
				<p style=\"position: absolute; right: 20px; top: 10px;\">
					<input type=\"button\" class=\"btnSubmit radius2\" name=\"btnSimpan\" value=\"Upload\" onclick=\"setProses('".getPar($par,"mode")."');\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"closeBox();\"/>
				</p>
			</form>
		</div>
	</div>";

	return $text;
}

function pegawai() {
	global $par;

	$data = getField("select concat(t1.reg_no,'\t', DATE_FORMAT(t1.join_date, '%d/%m/%Y'),'\t',t1.pos_name,'\t',t2.namaData,'\t',t3.namaData) FROM dta_pegawai t1 LEFT JOIN mst_data t2 ON t1.`rank` = t2.`kodeData` LEFT JOIN mst_data t3 ON t1.`cat` = t3.`kodeData` WHERE t1.id = '$par[idPegawai]'");

	return $data;
}


function uploadBukti($id) {
	global $dFile;

	$fileUpload = $_FILES["rwd_filename"]["tmp_name"];
	$fileUpload_name = $_FILES["rwd_filename"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $dFile);
		$foto_file = "reward-" . time() . "." . getExtension($fileUpload_name);
		fileRename($dFile, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select rwd_filename from emp_reward where id ='$id'");

	return $foto_file;
}

function hapusFoto() {
	global $par, $dFile;

	$foto_file = getField("select rwd_filename from emp_reward where id='$par[id]'");
	if (file_exists($dFile . $foto_file) and $foto_file != "")
		unlink($dFile . $foto_file);

	$sql = "update emp_reward set rwd_filename='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}
function tambah(){
	global $inp, $par, $cUsername, $cUsername;
	
	repField();

	$id = getField("select id from emp_reward order by id desc limit 1") + 1;
	$inp[rwd_filename] = uploadBukti($id);
	$sql = "insert into emp_reward (id, parent_id, rwd_no, rwd_subject, rwd_filename, remark,rwd_agency,rwd_year,rwd_cat,rwd_type,cre_date, cre_by) values ('$id', '$inp[parent_id]', '$inp[rwd_no]', '$inp[rwd_subject]', '$inp[rwd_filename]', '$inp[remark]', '$inp[rwd_agency]','$inp[rwd_year]','$inp[rwd_cat]','$inp[rwd_type]', '".date('Y-m-d H:i:s')."', '$cUsername')";
	db($sql);
	
	echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah(){
	global $inp, $par, $cUsername;

	repField();
	
	$rwd_filename = uploadBukti($par[id]);
	$sql = "update emp_reward set rwd_filename='$rwd_filename', parent_id='$inp[parent_id]',rwd_no='$inp[rwd_no]',rwd_subject='$inp[rwd_subject]',  remark='$inp[remark]',rwd_agency='$inp[rwd_agency]',rwd_year='$inp[rwd_year]',rwd_cat='$inp[rwd_cat]',rwd_type='$inp[rwd_type]',  upd_by = '$cUsername', upd_date = '".date('Y-m-d H:i:s')."' where id='$par[id]'";
	db($sql);

	echo "<script>alert('UPDATE DATA BERHASIL');window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
	
}
function hapus(){
	global $par;

	$sql="delete from emp_reward where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}	

function lihat(){
	global $s, $par, $arrTitle, $menuAccess;		

	$cols = 11;
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
				<a href=\"#Upload\" class=\"btn btn1 btn_inboxo\" onclick=\"openBox('popup.php?par[mode]=upl".getPar($par,"mode")."',725,250);\"><span>Import Data</span></a>
				<a href=\"#\"id=\"btnExpExcel\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>&nbsp";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
				$text.="
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
					</td>
					<td width=\"50%\">
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
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'AK' ORDER BY namaData", "kodeData", "namaData", "gSearch", "- Semua Grade -", $_GET['gSearch'], "onchange=\"\"", "250px", "chosen-select")."
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
					<th width=\"20\">No.</th>
					<th>Pegawai</th>
					<th width=\"75\">NPP</th>
					<th width=\"200\">Nomor Penghargaan</th>
					<th width=\"100\">Perihal</th>
					<th width=\"100\">Penerbit</th>
					<th width=\"100\">Kategori</th>
					<th width=\"100\">Tipe</th>
					<th width=\"100\">Tahun</th>
					<th width=\"50\">File</th>
					<th width=\"125\">Kontrol</th>
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
				
				window.location='?par[mode]=xls&par[aSearch]='+aSearch+'&par[bSearch]='+bSearch+'&par[cSearch]='+cSearch+'&par[dSearch]='+dSearch+'&par[eSearch]='+eSearch+'&par[fSearch]='+fSearch+'&par[gSearch]='+gSearch+'".getPar($par,"mode, aSearch, bSearch, cSearch, dSearch, eSearch, fSearch, gSearch")."';
			});
		});
	</script>";

	if($par[mode] == "xls"){			
        xls();			
        $text.="<iframe src=\"download.php?d=exp&f=".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

	return $text;
}

function lData(){
	global $s, $par, $menuAccess, $arrParameter;
	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

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
		"t1.rwd_no",
		"t1.sk_subject",	
		"t1.sk_cat",
		"t1.sk_type",
		"t1.sk_date",
	);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
	
	$sql="select t1.id, t1.rwd_no, t1.rwd_subject, t1.rwd_agency, t1.rwd_cat, t1.rwd_type, t1.rwd_year, t1.rwd_filename, t2.name as namaPegawai, t2.reg_no from emp_reward t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $sWhere order by $orderBy $sLimit";
	$res=db($sql);

	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(t1.id) from emp_reward t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $sWhere"),
		"aaData" => array(),
	);

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){
		$no++;
		$controlEmp="";

		$download = empty($r[rwd_filename]) ? " - " : "<a href=\"download.php?d=emprwd&f=$r[id]\"><img src=\"".getIcon($r[rwd_filename])."\" align=\"center\" style=\"vertical-align:middle; margin-top: 7px;\" ></a>";

		if(isset($menuAccess[$s]["edit"]))
			$controlEmp.="<a href=\"?par[mode]=edit&par[id]=$r[id]".getPar($par,"mode")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";				
		if(isset($menuAccess[$s]["delete"]))
			$controlEmp.="<a href=\"?par[mode]=del&par[id]=$r[id]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\" ><span>Delete</span></a>";				

		$data=array(
			"<div align=\"center\">".$no.".</div>",				
			"<div align=\"left\">".strtoupper($r[namaPegawai])."</div>",
			"<div align=\"center\">".$r[reg_no]."</div>",
			"<div align=\"left\">".$r[rwd_no]."</div>",
			"<div align=\"left\">".$r[rwd_subject]."</div>",
			"<div align=\"left\">".$r[rwd_agency]."</div>",
			"<div align=\"center\">".$arrMaster[$r[rwd_cat]]."</div>",
			"<div align=\"center\">".$arrMaster[$r[rwd_type]]."</div>",
			"<div align=\"center\">".$r[rwd_year]."</div>",
			"<div align=\"center\">".$download."</div>",
			"<div align=\"center\">".$controlEmp."</div>",
		);


		$json['aaData'][]=$data;
	}
	return json_encode($json);
}


function form(){
	global $s, $par, $arrTitle, $tab;

	$sql="select * from emp_reward where id='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$sql_="select * from dta_pegawai where id='$r[parent_id]'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);		

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");						

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."	
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div style=\"top:10px; right:25px; position:absolute\">
				<input type=\"hidden\" id=\"inp[tab]\" name=\"inp[tab]\" class=\"mediuminput\" value=\"$tab\" style=\"width:395px;\" maxlength=\"125\"/>					
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Simpan\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, id") . "';\"/>
			</div>
			<fieldset>
			<table style=\"width:100%\">
				<tr>
				<td style=\"width:50%\">
					<p>
						<label class=\"l-input-small2\">Nama Pegawai</label>
						" . comboData("select * from dta_pegawai order by name", "id", "name", "inp[parent_id]", " ", $r[parent_id], "onchange=\"changePegawai(this.value,'".getPar($par,"mode,parent_id")."')\"", "225px","chosen-select") . "
					</p>
					<p>
						<label class=\"l-input-small2\">Pangkat</label>
						<span class=\"field\" id =\"pangkat\">".$arrMaster[$r_[rank]]."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small2\">Jabatan</label>
						<span class=\"field\" id =\"jabatan\">".$r_[pos_name]."&nbsp;</span>
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
				<legend> DATA KELUARGA </legend>
				<p>
					<label class=\"l-input-small\">Nomor Penghargaan</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[rwd_no]\" name=\"inp[rwd_no]\" class=\"smallinput\" value=\"$r[rwd_no]\" />	
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Perihal</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[rwd_subject]\" name=\"inp[rwd_subject]\" class=\"smallinput\" value=\"$r[rwd_subject]\" />	
					</div>
				</p>
				<table style=\"width:100%\">
					<tr>
					<td style=\"width:70%\">
						<p>
							<label class=\"l-input-medium\">Penerbit</label>
							<div class=\"fieldB\">
								<input type=\"text\" id=\"inp[rwd_agency]\" name=\"inp[rwd_agency]\" class=\"smallinput\" value=\"$r[rwd_agency]\" />	
							</div>
						</p>
					</td>
					<td style=\"width:30%\">
						<p>
							<label class=\"l-input-small\">Tahun</label>
							<div class=\"fieldB\">
								<input type=\"text\" id=\"inp[rwd_year]\" name=\"inp[rwd_year]\" class=\"smallinput\" value=\"$r[rwd_year]\" />	
							</div>
						</p>
					</td>
					</tr>
				</table>
				<table style=\"width:100%\">
					<tr>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small2\">Kategori</label>
							<div class=\"field\">
								" . comboData("select * from mst_data where kodeCategory='S18' and statusData='t' order by namaData", "kodeData", "namaData", "inp[rwd_cat]", " ", $r[rwd_cat], "", "225px","chosen-select") . "		
							</div>
						</p>
					</td>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small\">Tipe</label>
							<div class=\"field\">
								" . comboData("select * from mst_data where kodeCategory='S19' and statusData='t' order by namaData", "kodeData", "namaData", "inp[rwd_type]", " ", $r[rwd_type], "", "225px","chosen-select") . "	
							</div>
						</p>
					</td>
					</tr>
				</table>
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[rwd_filename])?
						"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
						<div class=\"fakeupload\">
							<input type=\"file\" id=\"rwd_filename\" name=\"rwd_filename\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[rwd_filename])."\" align=\"left\" height=\"20\" style=\"padding-right:5px; padding-bottom:5px;\">
						<a href=\"?par[mode]=delFoto".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
						$text.="
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[remark]\" name=\"inp[remark]\"  rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:60%;\">$r[remark]</textarea>
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);	
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);	

	$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN DATA REWARD');
	// $objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulan])." ".$par[tahun]);

	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('A4:J4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->setCellValue('A4','NO.');
    $objPHPExcel->getActiveSheet()->setCellValue('B4','NPP PEGAWAI');
    $objPHPExcel->getActiveSheet()->setCellValue('C4','NAMA PEGAWAI');
    $objPHPExcel->getActiveSheet()->setCellValue('D4','NOMOR PENGHARGAAN');
    $objPHPExcel->getActiveSheet()->setCellValue('E4','PERIHAL');
    $objPHPExcel->getActiveSheet()->setCellValue('F4','PENERBIT');
    $objPHPExcel->getActiveSheet()->setCellValue('G4','TAHUN');
	$objPHPExcel->getActiveSheet()->setCellValue('H4','KATEGORI');
	$objPHPExcel->getActiveSheet()->setCellValue('I4','TIPE');
	$objPHPExcel->getActiveSheet()->setCellValue('J4','KETERANGAN');
	
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

	$sql="SELECT t2.name as namaPegawai, t2.reg_no, t1.* from emp_reward t1 join emp t2 on t1.parent_id = t2.id join emp_phist t3 on (t2.id = t3.parent_id AND t3.status = '1') $sWhere order by t2.name";
    $res = db($sql);
    $no=0;
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data where kodeCategory IN ('S18', 'S19')");
    while ($r = mysql_fetch_array($res)) {
		$no++;

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);				
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[reg_no]);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[namaPegawai]));
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[rwd_no]);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[rwd_subject]);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[rwd_agency]);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $r[rwd_year]);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$rows, $arrMaster[$r[rwd_cat]]);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$rows, $arrMaster[$r[rwd_type]]);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$rows, $r[remark]);
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':J'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

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

    $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->getStyle('A1:J'.$rows)->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle('A6:J'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

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