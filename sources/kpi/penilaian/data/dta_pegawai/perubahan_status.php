<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fFile = "files/upload/";

function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode])
    {
        case "appr":
		if(isset($menuAccess[$s]["apprlv1"])) $text = empty($_submit) ? form_approve() : simpan_approve(); else $text = lihat();
		break;
        
		case "delete":
		if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
		break;

		case "edit":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
		break;

		case "add":
		if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
		break;
        
        case "lst":
		$text=lData();
		break;  

		default:
		$text = lihat();
		break;
	}
	return $text;
}

function simpan_approve()
{
    global $s,$inp,$par,$arrTitle,$menuAccess,$cID;
    
    db("update emp_perubahan_status set 
                                     approval_date = '".setTanggal($inp[approval_date])."',
                                     approval_by = '".$cID."',
                                     approval_keterangan = '".$inp[approval_keterangan]."',
                                     approval_status = '".$inp[approval_status]."'
                                where
                                     id_perubahan = $par[id_perubahan]
                                     ");
    if($inp[approval_status] == 't')
    {
        $getData = queryAssoc("select * from emp_perubahan_status where id_perubahan = $par[id_perubahan]");
        $id = $getData[0][id_pegawai];
        $status = $getData[0][perubahan_status];
        db("update emp set status = '$status' where id = $id");
    }
                                     
    echo "<script>closeBox();</script>";
    echo "<script>alert('Data Berhasil disimpan!');</script>";
    echo "<script>reloadPage();</script>";
}

function form_approve()
{
	global $s,$inp,$par,$arrTitle,$menuAccess,$cID;

	$sql="select * from emp_perubahan_status where id_perubahan = $par[id_perubahan]";
	$res=db($sql);
	$r = mysql_fetch_array($res);

	$pending =  $r[approval_status] == "p" ? "checked=\"checked\"" : "";
	$tolak =  $r[approval_status] == "f" ? "checked=\"checked\"" : "";
	$setuju =  empty($pending) && empty($tolak) ? "checked=\"checked\"" : "";

	$r[approval_date] = empty($r[approval_date]) ? date('Y-m-d') : $r[approval_date];
    
    if (empty($r[approval_by])) {
		$r[approval_by] = getField("select namaUser from app_user where id='$cID'");
	}else{
		$r[approval_by] = getField("select namaUser from  app_user where id='$r[approval_by]'");
	}

	$text.="

	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Approval</h1>
		<br>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"pos_r\" style=\"position:absolute;top: 10px; right: 20px\">
				<p>
					";
                    if($r[approval_status] != "t")
                    {
                        $text.="
                        <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return save('".getPar($par,"mode")."');\"/>
					    <input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
                        ";
                    } 
                    $text.="
			     </p>
			</div>
			<fieldset>
                <p>
    				<label class=\"l-input-small\" style=\"padding-left:10px;\">Tanggal</label>
    				<div class=\"field\">
       					<input type=\"text\" id=\"inp[approval_date]\" name=\"inp[approval_date]\"  value=\"".getTanggal($r[approval_date])."\" class=\"hasDatePicker\" maxlength=\"150\"/>
    				</div>
    			</p>
    			<p>
    				<label class=\"l-input-small\" style=\"padding-left:10px;\">Nama</label>
    				<div class=\"field\">
    					<input type=\"text\" readonly id=\"inp[approval_by]\" style=\"width:300px;\"name=\"inp[approval_by]\" size=\"10\" maxlength=\"150\" value=\"$r[approval_by]\" class=\"vsmallinput\"/>
    				</div>
    			</p>
    			<p>
    				<label class=\"l-input-small\" style=\"padding-left:10px;\">Keterangan</label>
    				<div class=\"field\">
    					<textarea name=\"inp[approval_keterangan]\" style=\"width:300px;\" id=\"inp[approval_keterangan]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >$r[approval_keterangan]</textarea>
    				</div>
    			</p>
    			<p>
    				<label class=\"l-input-small\" style=\"padding-left:10px;\">Status</label>
    				<div class=\"fradio\">
    					<input type=\"radio\" id=\"setuju\" name=\"inp[approval_status]\" value=\"t\" $setuju /> <span class=\"sradio\">Setuju</span>
    					<input type=\"radio\" id=\"pending\" name=\"inp[approval_status]\" value=\"p\" $pending /> <span class=\"sradio\">Pending</span>
    					<input type=\"radio\" id=\"tolak\" name=\"inp[approval_status]\" value=\"f\" $tolak /> <span class=\"sradio\">Tolak</span>							
    				</div>
    			</p>
            </fieldset>
		</form>	
	</div>";

	return $text;

}

function hapus()
{
    global $s, $id, $inp, $par, $arrParameter,$db,$fFile;
    
    $file_sk = getField("select file_sk from emp_perubahan_status where id_perubahan = $par[id_perubahan]");
    
    db("delete from emp_perubahan_status where id_perubahan = $par[id_perubahan]");
    
    unlink($fFile.$file_sk);
    
    echo "<script>alert('Data Berhasil dihapus!');</script>";
    echo "<script>window.location='?".getPar($par,"mode,id_agen")."';</script>";
}

function uploadFile($inpFileName, $dirFoto)
{
    $filePicTmp = $_FILES[$inpFileName]["tmp_name"];
    $filePicName = $_FILES[$inpFileName]["name"];
    if($filePicTmp != "" && $filePicTmp != "none")
    {
        $fpicName = strtotime(date("Y-m-d H:i:s")). "." .getExtension($filePicName);
        if(move_uploaded_file($filePicTmp, $dirFoto . $fpicName)){
            return $fpicName;
        }
    }else{
        return false;
    }
}

function simpan()
{
    global $s,$par,$inp,$menuAccess,$arrTitle,$cID,$cIdPegawai,$fFile;
    
    if($par['mode'] == "add")
    {
        $id = getLastId("emp_perubahan_status", "id_perubahan");
        $file_sk = uploadFile('file_sk', $fFile);
        
        db("insert into emp_perubahan_status set id_perubahan = $id,
                                                 tanggal = '".setTanggal($inp[tanggal])."',
                                                 id_pegawai = '".$inp[id_pegawai]."',
                                                 keterangan = '".$inp[keterangan]."',
                                                 perubahan_status = '".$inp[perubahan_status]."',
                                                 file_sk = '".$file_sk."',
                                                 create_date = '".date("Y-m-d H:i:s")."',
                                                 create_by = '".$cID."'
                                                 ");
    }
    
    if($par['mode'] == "edit")
    {
        $file_sk = $_FILES['file_sk']['tmp_name'];
        if(!empty($file_sk)) $file = uploadFile('file_sk', $fFile);
        
        db("update emp_perubahan_status set 
                                             tanggal = '".setTanggal($inp[tanggal])."',
                                             id_pegawai = '".$inp[id_pegawai]."',
                                             keterangan = '".$inp[keterangan]."',
                                             perubahan_status = '".$inp[perubahan_status]."',
                                             ".((!empty($file_sk)) ? "file_sk = '$file'," : "")."
                                             update_date = '".date("Y-m-d H:i:s")."',
                                             update_by = '".$cID."'
                                        where
                                             id_perubahan = $par[id_perubahan]
                                             ");
    }
    
    echo "<script>alert('Data berhasil disimpan!')</script>";
	echo "<script>window.location='?".getPar($par,"mode")."';</script>";
}

function form()
{
    global $s,$par,$menuAccess,$arrTitle, $cIdPegawai, $fFile;
    
    $sql = "select * from emp_perubahan_status where id_perubahan = '$par[id_perubahan]'";
    $res = db($sql);
    $r   = mysql_fetch_array($res);
    
    $r[tanggal] = empty($r[tanggal]) ? date('Y-m-d') : $r[tanggal];
    
    setValidation("is_null", "inp[id_pegawai]", "Field Pegawai tidak boleh kosong");
    $text .= getValidation();
    
    $text.="
    <div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread(ucwords($par[mode]." data"))."	
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
			<div style=\"position:absolute; right:20px; top:14px;\">
                
                ";
                if($r[approval_status] != "t")
                {
                    $text.="
                    <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
				    <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Batal\" onclick=\"window.location='index.php?".getPar($par,"mode")."';\"/>
                    ";
                }
                else
                {
                    $text.="
				    <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Kembali\" onclick=\"window.location='index.php?".getPar($par,"mode")."';\"/>
                    ";
                } 
                $text.="
            
				
            </div>
            <fieldset>
				<legend>Perubahan Status</legend>
				<p>
					<label class=\"l-input-small\">Tanggal</label>
					<div class=\"field\">
						<input type=\"text\" name=\"inp[tanggal]\"  value=\"".getTanggal($r[tanggal])."\" class=\"hasDatePicker\" style=\"width:220px;\"/>
					</div>
				</p>
                <p>
					<label class=\"l-input-small\">Pegawai</label>
					<div class=\"field\">
						".comboData("select id, name from dta_pegawai order by name asc", "id", "name", "inp[id_pegawai]", "- Pilih Pegawai -", $r[id_pegawai], "", "250px", "chosen-select")."
					</div>
				</p>
                <p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea name=\"inp[keterangan]\" style='width:500px;'>$r[keterangan]</textarea>
					</div>
				</p>
                <p>
					<label class=\"l-input-small\">Status</label>
					<div class=\"field\">
						";
                        $arr = array('t'=>"Aktif", 'f'=>"Tidak Aktif");
                        $text.="
                        ".comboKey("inp[perubahan_status]", $arr, $r[perubahan_status], "", "250px")."
					</div>
				</p>
                <p>
    				<label class=\"l-input-small\">File SK</label>
    				<div class=\"field\">
                        ";
    					$text.=
                        empty($r[file_sk])
                        ?
    						"<input type=\"text\" id=\"fotoTempKtp\" name=\"fotoTempKtp\" class=\"input\" style=\"width:300px;\" maxlength=\"100\" />
    						<div class=\"fakeupload\" style=\"margin-left:5px;\">
    							<input type=\"file\" id=\"file_sk\" name=\"file_sk\" class=\"realupload\" size=\"50\" onchange=\"this.form.fotoTempKtp.value = this.value;\" />
    						</div>"
                        :
    						"
    						<a href=\"?par[mode]=deleteFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
    						<br clear=\"all\">";
    				    $text.="
                    </div>
    			</p>
            </fieldset>
        </form>
    </div>
    ";
    return $text;
}

function lData()
{
	global $s,$par,$menuAccess, $cIdPegawai;
    
	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit="limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);


	$sWhere = " WHERE a.id_perubahan is not null";

	if (!empty($_GET['fSearch']))
		$sWhere.= " and (				
    	lower(b.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
        or
        lower(b.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
    	)";
        
	$arrOrder = array(	
		"a.tanggal",
        "b.reg_no",
		"b.name"
		);

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql="
    SELECT * FROM emp_perubahan_status AS a
    JOIN emp AS b ON (b.id = a.id_pegawai)
	$sWhere order by $orderBy $sLimit";
    
	$res=db($sql);
    
	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("SELECT count(id_perubahan) FROM emp_perubahan_status AS a
                                            JOIN emp AS b ON (b.id = a.id_pegawai)
                                            $sWhere"),
		"aaData" => array(),
		);

	$no=intval($_GET['iDisplayStart']);
	while($r=mysql_fetch_array($res)){
		$no++;
        
        if($r[perubahan_status] == "t") $r[perubahan_status] = "Aktif";
        if($r[perubahan_status] == "f") $r[perubahan_status] = "Tidak Aktif"; 
        
        if($r[approval_status] == "t") $appr = "<img src=\"styles/images/t.png\" title='setuju'>";
		if($r[approval_status] == "f") $appr = "<img src=\"styles/images/f.png\" title='tolak'>";
		if($r[approval_status] == "p") $appr = "<img src=\"styles/images/o.png\" title='Pending'>";
        if($r[approval_status] == "")  $appr = "<img src=\"styles/images/p.png\" title='belum diproses'>";
        
        $tombol = isset($menuAccess[$s]["apprlv1"]) ? "<a href=\"#\" onclick=\"openBox('popup.php?par[mode]=appr&par[id_perubahan]=$r[id_perubahan]".getPar($par, "mode,id_perubahan")."',550,300);\">".$appr."</a>" : $appr;
        
		$data=array(
			"<div align=\"center\">$no</div>",				
			"<div align=\"center\">".getTanggal($r[tanggal])."</div>",
			"<div align=\"center\">$r[reg_no]</div>",
			"<div align=\"left\">$r[name]</div>",
			"<div align=\"center\">$r[perubahan_status]</div>",
			"<div align=\"center\">$tombol</div>",
            "<div align=\"left\">$r[keterangan]</div>",
			"
			<div align=\"center\">
				<a href='?par[mode]=edit&par[id_perubahan]=$r[id_perubahan]".getPar($par,"mode,id_perubahan")."' class='edit' title='Edit Data'></a>
                ".(($r[approval_status] != "t") 
                    ? 
                    "
                    <a href='?par[mode]=delete&par[id_perubahan]=$r[id_perubahan]".getPar($par,"mode,id_perubahan")."' class='delete' title='Hapus Data' onclick=\"return confirm('are you sure to delete data ?');\"></a>
                    " 
                    : 
                    "-")."
			</div>
			",
			);
		$json['aaData'][]=$data;
	}
	return json_encode($json);
}

function lihat(){
	global $s,$inp,$par,$arrTitle,$menuAccess,$arrColor;
    
	$cols = 8;  
	$text = table($cols, array($cols,($cols-1),($cols-2),($cols-3)));
    
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
	</div> 

	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" id=\"form\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p>					
					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$_GET['fSearch']."\" style=\"width:200px;\"/>
				</p>
			</div>

			<div id=\"pos_r\" style=\"float:right; margin-top:15px;\">";
				if(isset($menuAccess[$s]["add"])) {
					$text.="
					<a href=\"index.php?par[mode]=add".getPar($par,"mode")."\" id=\"\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>
					";
				}
				$text.="
			</div>	
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"100\">Tanggal</th>
					<th width=\"100\">NPP</th>
					<th width=\"200\">nama</th>
					<th width=\"100\">status</th>
					<th width=\"50\">approval</th>
                    <th width=\"50\">keterangan</th>
					<th width=\"70\">Kontrol</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
    </div>
	";
	if($par[mode] == "xls"){			
		xls();			
		$text.="<iframe src=\"download.php?d=exp&f=exp-".$arrTitle[$s].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}

	$text.="
	<script>
		jQuery(\"#btnExport\").live('click', function(e){
			e.preventDefault();
			window.location.href=\"?par[mode]=xls\"+\"".getPar($par,"mode")."\"+\"&fSearch=\"+jQuery(\"#fSearch\").val();
		});
	</script>
	";
	return $text;
}

?>