<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$emp = new Emp();
$cutil = new Common();
$ui = new UIHelper();

$dFile = "files/emp/adm/fam/";

if ($_GET["json"] == 'excel') {
	list($fSearch, $rank, $location, $div_id, $subDept, $unitId) = explode("~", $_GET["params"]);
	$res = $emp->exportToExcelForm($fSearch, $rank, $div_id, $location, $subDept, $unitId);
	$uie = new UiHelperExtra();
	$uie->exportXLS($res, array("Data Pegawai",
		"Jabatan : " . (empty($rank) ? "ALL" : $cutil->getMstDataDesc($rank)),
		"Lokasi : " . (empty($location) ? "ALL" : $cutil->getMstDataDesc($location)),
		"Divisi : " . (empty($div_id) ? "ALL" : $cutil->getMstDataDesc($div_id)),
		"Departemen : " . (empty($subDept) ? "ALL" : $cutil->getMstDataDesc($subDept)),
		"Group : " . (empty($unitId) ? "ALL" : $cutil->getMstDataDesc($unitId))
		), "laporan_data_pegawai_" . strtolower($cutil->getMstDataDesc($par[empType])) . "_" . uniqid() . ".xlsx");
}


function uploadBukti($id) {
	global $s, $inp, $par, $dFile;
	$fileUpload = $_FILES["file_sk"]["tmp_name"];
	$fileUpload_name = $_FILES["file_sk"]["name"];
	if (($fileUpload != "") and ( $fileUpload != "none")) {
		fileUpload($fileUpload, $fileUpload_name, $dFile);
		$foto_file = "family-" . time() . "." . getExtension($fileUpload_name);
		fileRename($dFile, $fileUpload_name, $foto_file);
	}
	if (empty($foto_file))
		$foto_file = getField("select file_sk from emp_pcontract where id ='$id'");

	return $foto_file;
}

function pegawai() {
global $s, $id, $inp, $par, $arrParameter,$db;
$data = getField("select concat(t1.reg_no,'\t',t1.join_date,'\t',t1.pos_name,'\t',t2.namaData,'\t',t3.namaData) FROM dta_pegawai t1 LEFT JOIN mst_data t2 ON t1.`rank` = t2.`kodeData` LEFT JOIN mst_data t3 ON t1.`cat` = t3.`kodeData` WHERE t1.id = '$par[idPegawai]'");

return $data;

}

function hapusFoto() {
	global $s, $inp, $par, $dFile, $cUsername;

	$foto_file = getField("select file_sk from emp_pcontract where id='$par[id]'");
	if (file_exists($dFile . $foto_file) and $foto_file != "")
		unlink($dFile . $foto_file);

	$sql = "update emp_pcontract set file_sk='' where id='$par[id]'";
	db($sql);

	echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}
function tambah(){

	global $s, $inp, $par, $cUsername, $arrProduk, $cUsername;
	repField();
	$id = getField("select id from emp_pcontract order by id desc limit 1") + 1;
	$inp[file_sk] = uploadBukti($id);
	$inp[sk_date] = setTanggal($inp[sk_date]);	
	$inp[start_date] = setTanggal($inp[start_date]);	
	$inp[end_date] = setTanggal($inp[end_date]);	

	$sql = "insert into emp_pcontract (id, parent_id, sk_no, subject, sk_date, start_date, end_date, file_sk, remark,loc1, status, cre_date, cre_by) values ('$id', '$inp[parent_id]', '$inp[sk_no]', '$inp[subject]', '$inp[sk_date]', '$inp[start_date]', '$inp[end_date]', '$inp[file_sk]', '$inp[remark]', '$inp[loc1]', '$inp[status]', '".date('Y-m-d H:i:s')."', '$cUsername')";
	
	db($sql);

	
	echo "<script>alert('TAMBAH DATA BERHASIL');window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
}

function ubah(){

	global $s, $inp, $par, $cUsername, $arrProduk;
	repField();
	
	$inp[sk_date] = setTanggal($inp[sk_date]);
	$inp[start_date] = setTanggal($inp[start_date]);	
	$inp[end_date] = setTanggal($inp[end_date]);	
	$file_sk = uploadBukti($par[id]);
	$sql = "update emp_pcontract set file_sk='$file_sk', parent_id='$inp[parent_id]',sk_no='$inp[sk_no]',subject='$inp[subject]', sk_date='$inp[sk_date]', start_date='$inp[start_date]',end_date='$inp[end_date]', status='$inp[status]',  remark='$inp[remark]',loc1='$inp[loc1]',  upd_by = '$cUsername', upd_date = '".date('Y-m-d H:i:s')."' where id='$par[id]'";
	// echo $sql;
	// die();
	db($sql);

	echo "<script>alert('UPDATE DATA BERHASIL')</script>";
	echo "<script>window.location='?par[mode]=edit&par[id]=$id" . getPar($par, "mode") . "';</script>";
	
}
function hapus(){
	global $s,$inp,$par,$cUsername;
	$sql="delete from emp_pcontract where id='$par[id]'";
	db($sql);


	echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
}	

function lihat(){
	global $s,$inp,$par,$arrParameter,$arrParam,$arrTitle,$menuAccess,$arrColor, $areaCheck, $cutil;		

	$cols = 10;
	// $cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
	$text = table($cols, array($cols-1, $cols));

	// $par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");

	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	" . empLocHeader() . "
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<table style=\"width:100%\">
			<tr>
			<td style=\"width:50%; text-align:left; vertical-align:top;\">
					<table>
					<tr>
					<td style=\"vertical-align:top; padding-top:2px;\"><input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/></td>
					<td style=\"vertical-align:top;\" id=\"bView\">
						<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
						document.getElementById('bView').style.display = 'none';
						document.getElementById('bHide').style.display = 'table-cell';
						document.getElementById('dFilter').style.visibility = 'visible';							
						document.getElementById('fSet').style.height = 'auto';
					\" />
					</td>
					<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
					<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 8px;\"_search btn-small\" style=\"display:none\" onclick=\"
						document.getElementById('bView').style.display = 'table-cell';
						document.getElementById('bHide').style.display = 'none';
						document.getElementById('dFilter').style.visibility = 'collapse';							
						document.getElementById('fSet').style.height = '0px';
					\" />					
					</td>		
					</tr>
					</table>					
					<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px;\">
					<div id=\"dFilter\" style=\"visibility:collapse;\">
					<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">STATUS PEGAWAI</label>
							<div class=\"field\" style=\"margin-left:200px;\">
			 					".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S04' ORDER BY urutanData", "kodeData", "namaData", "lSearch", "--- STATUS PEGAWAI ---", $_GET['lSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">JABATAN</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S09' ORDER BY urutanData", "kodeData", "namaData", "bSearch", "--JABATAN--", $_GET['bSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">LOKASI</label>
							<div class=\"field\" style=\"margin-left:200px;\">
								".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'S06' AND kodeData IN ($areaCheck) ORDER BY urutanData", "kodeData", "namaData", "tSearch", "--LOKASI--", $_GET['tSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[38])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">
			 					".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X04' ORDER BY urutanData", "kodeData", "namaData", "dirSearch", "--".strtoupper($arrParameter[38])."--", $_GET['dirSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">
			 					".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "pSearch", "--".strtoupper($arrParameter[39])."--", $_GET['pSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">
			 					".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' ORDER BY urutanData", "kodeData", "namaData", "mSearch", "--".strtoupper($arrParameter[40])."--", $_GET['mSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:200px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[58])."</label>
							<div class=\"field\" style=\"margin-left:200px;\">
			 					".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' ORDER BY urutanData", "kodeData", "namaData", "gSearch", "--".strtoupper($arrParameter[58])."--", $_GET['gSearch'], "", "250px", "chosen-select")."
							</div>
						</p>
						
					</div>
					</fieldset>
			</div>				
			</td>
			<td style=\"width:50%; text-align:right; vertical-align:top;\">
				<a href=\"#\" id=\"btnExpExcel\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>&nbsp";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
				$text.="</td>
			</tr>
			</table>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th>Pegawai</th>
						<th width=\"75\">NPP</th>
						<th width=\"200\">Nomor SK</th>
						<th width=\"100\">Perihal</th>
						<th width=\"100\">Tanggal SK</th>
						<th width=\"100\">Tgl Berlaku</th>
						<th width=\"100\">Tgl Berakhir</th>
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
					var fSearch = jQuery(\"#fSearch\").val();
					var bSearch = jQuery(\"#bSearch\").val();
					var tSearch = jQuery(\"#tSearch\").val();
					var dirSearch = jQuery(\"#dirSearch\").val();
					var pSearch = jQuery(\"#pSearch\").val();
					var mSearch = jQuery(\"#mSearch\").val();
					var gSearch = jQuery(\"#gSearch\").val();
					var lSearch = jQuery(\"#lSearch\").val();

					window.open(sajax + '&json=excel&params=' + fSearch + '~' + bSearch + '~' + tSearch + '~' + dirSearch + '~' + pSearch + '~' + mSearch + '~' + gSearch + '~' + lSearch, '_blank');
				});

				
			});
		</script>";

		return $text;
	}

	function lData(){
		global $s,$par,$menuAccess,$arrParameter,$arrParam, $areaCheck;
		// if(!empty($arrParam[$s]))		
		// 	$par[empType] = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' and urutanData='".$arrParam[$s]."'");

		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
			$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);

		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");			
		$sWhere= " where t2.status='".$status."'";
		// if(!empty($par[empType]))
		// 	$sWhere .= " and cat='".$par[empType]."'";


		if (!empty($_GET['fSearch']))
				$sWhere.= " and (				
					lower(t2.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					or lower(t2.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					or lower(t2.pos_name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
					)";
					if(!empty($_GET['bSearch']))
						$sWhere.=" and t2.rank = '".$_GET['bSearch']."'";
					if(!empty($_GET['dirSearch']))
						$sWhere.=" and t2.dir_id = '".$_GET['dirSearch']."'";
					if(!empty($_GET['pSearch']))
						$sWhere.=" and t2.div_id = '".$_GET['pSearch']."'";
					if(!empty($_GET['tSearch']))
						$sWhere.=" and t2.location = '".$_GET['tSearch']."'";
					if(!empty($_GET['mSearch']))
						$sWhere.=" and t2.dept_id = '".$_GET['mSearch']."'";
					if(!empty($_GET['gSearch']))
						$sWhere.=" and t2.unit_id = '".$_GET['gSearch']."'";
					if(!empty($_GET['lSearch']))
						$sWhere.=" and t2.cat = '".$_GET['lSearch']."'";

					if(!empty($par[empType]))
						$sWhere .= " AND location IN ($areaCheck)";
					// else
					// 	$sWhere .= " AND (location IS NULL OR location = 0)";

					$arrOrder = array(	
						"t2.name",
						"t2.name",
						"t2.reg_no",
						"t1.sk_no",
						"t1.sk_subject",	
						"t1.sk_cat",
						"t1.sk_type",
						"t1.sk_date",
					);
					$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
			$sql="select t1.*, t2.name as namaPegawai,t1.id as idKontrak,t2.reg_no from emp_pcontract t1 join dta_pegawai t2 on t1.parent_id = t2.id $sWhere order by $orderBy $sLimit";
				// echo $sql;
				// echo "select count(*) from dta_pegawai $sWhere";
				$res=db($sql);

				$json = array(
					"iTotalRecords" => mysql_num_rows($res),
					"iTotalDisplayRecords" => getField("select count(*) from emp_pcontract t1 join dta_pegawai t2 on t1.parent_id = t2.id $sWhere"),
					"aaData" => array(),
				);

				$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

				$no=intval($_GET['iDisplayStart']);
				while($r=mysql_fetch_array($res)){
					
					$no++;
				

					$controlEmp="";

					if(empty($r[file_sk])){
						$download = " - ";
					}else{
					$download = "<a href=\"download.php?d=empcontract&f=$r[idKontrak]\"><img src=\"".getIcon($r[file_sk])."\" align=\"center\" style=\"vertical-align:middle; margin-top: 7px;\" ></a>";
					}	

					if(isset($menuAccess[$s]["edit"]))
						$controlEmp.="<a href=\"?par[mode]=edit&par[id]=$r[idKontrak]".getPar($par,"mode")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";				
					if(isset($menuAccess[$s]["delete"]))
						$controlEmp.="<a href=\"?par[mode]=del&par[id]=$r[idKontrak]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\" ><span>Delete</span></a>";				

					$data=array(
						"<div align=\"center\">".$no.".</div>",				
						"<div align=\"left\">".strtoupper($r[namaPegawai])."</div>",
						"<div align=\"center\">".$r[reg_no]."</div>",
						"<div align=\"left\">".$r[sk_no]."</div>",
						"<div align=\"left\">".$r[subject]."</div>",
						"<div align=\"center\">".getTanggal($r[sk_date])."</div>",
						"<div align=\"center\">".getTanggal($r[start_date])."</div>",
						"<div align=\"center\">".getTanggal($r[end_date])."</div>",
						"<div align=\"center\">".$download."</div>",
						"<div align=\"center\">".$controlEmp."</div>",
					);


					$json['aaData'][]=$data;
				}
				return json_encode($json);
			}


function form(){

	global $s,$inp,$par,$fFile,$arrTitle,$menuAccess, $dFile, $tab, $cUsername, $kodeGroup, $Member, $member_id;

	$sql="select * from emp_pcontract where id='$par[id]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);

	$sql_="select * from dta_pegawai where id='$r[parent_id]'";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);		

	$arrMaster = arrayQuery("select kodeData, namaData from mst_data");						

	$false =  $r[status] == "0" ? "checked=\"checked\"" : "";
	$true =  empty($false) ? "checked=\"checked\"" : "";

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
					<label class=\"l-input-small\">Nomor SK</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[sk_no]\" name=\"inp[sk_no]\" class=\"smallinput\" value=\"$r[sk_no]\" />	
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Perihal</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[subject]\" name=\"inp[subject]\" class=\"smallinput\" value=\"$r[subject]\" />	
					</div>
				</p>

				<p>
					<label class=\"l-input-small\">Tanggal SK</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[sk_date]\" name=\"inp[sk_date]\"  value=\"".getTanggal($r[sk_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				<table style=\"width:100%\">
				<tr>
				<td style=\"width:50%\">
				<p>
					<label class=\"l-input-small2\">Tanggal Berlaku</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[start_date]\" name=\"inp[start_date]\"  value=\"".getTanggal($r[start_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				</td>
				<td style=\"width:50%\">
				<p>
					<label class=\"l-input-small\">Berakhir</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[end_date]\" name=\"inp[end_date]\"  value=\"".getTanggal($r[end_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
					</div>
				</p>
				</td>
				</tr>
				</table>
				<p>
					<label class=\"l-input-small\">Lokasi Kerja</label>
					<div class=\"field\">
						" . comboData("select * from mst_data where kodeCategory='S06' and statusData='t' order by namaData", "kodeData", "namaData", "inp[loc1]", " ", $r[loc1], "", "225px","chosen-select") . "
					
					</div>
				</p>
				
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
						$text.=empty($r[file_sk])?
						"<input type=\"text\" id=\"fotoTemp\" name=\"fotoTemp\" class=\"input\" style=\"width:295px;\" maxlength=\"100\" />
						<div class=\"fakeupload\">
							<input type=\"file\" id=\"file_sk\" name=\"file_sk\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTemp.value = this.value;\" />
						</div>":
						"<img src=\"".getIcon($r[file_sk])."\" align=\"left\" height=\"20\" style=\"padding-right:5px; padding-bottom:5px;\">
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
				<p>
 <label class=\"l-input-small\" >Status</label>
 <div class=\"field\">     
  <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"1\" $true /> <span class=\"sradio\">Aktif</span>
  <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"0\" $false /> <span class=\"sradio\">Tidak Aktif</span>       
 
</div>
</p>

				</fieldset>
				
				
		</form>	
	
	</div>
	


	";
	return $text;

}

			function getContent($par){
				global $s,$_submit,$menuAccess;
				switch($par[mode]){
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