<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
global $p, $s, $m, $menuAccess, $arrTitle, $par, $_submit, $cUsername;
$mode = $arrParam[$s];

$arrData = arrayQuery("select kodeData, namaData from mst_data");
$array_status = array('t' => "Aktif", 'f' => "Tidak aktif");
$dpFile = "files/template_file/";
function lihat(){
	global $s,$inp,$par,$arrParameter,$arrParam,$arrTitle,$menuAccess,$arrColor, $areaCheck, $cutil,$dpFile,$mode;	

	$cols = 6;
	$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
	$text = table($cols, array($cols-1, $cols));
	
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div style=\"position:absolute; top: 15px; right: 20px;\">

				

			</div>
		";
			$text.="<table style=\"width:100%\">
			<tr>
				<td style=\"width:50%; text-align:left; vertical-align:top;\">
					<table>
						<tr>
							<td style=\"vertical-align:top; padding-top:2px;\">
								<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[fSearch]."\" style=\"width:200px;\"/>
								<input type=\"hidden\" id=\"par[id]\" name=\"par[id]\" size=\"20\" style=\"width:200px;\"  value=\"$par[id]\" class=\"mediuminput\" />
								
							</td>
								
						</tr>
					</table>					
					<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px;\">
							
					</fieldset>
				</div>				
			</td>
			<td style=\"width:50%; text-align:right; vertical-align:top;\">
				<a href=\"?par[mode]=tambah".getPar($par,"mode, id_pegawai")."\" class=\"btn btn1 btn_document\"><span>Tambah</span></a>";
				$text.="</td>
			</tr>
		</table>
	</form>
	<br clear=\"all\" />
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
		<thead>
			<tr>
				<th width=\"20\">NO</th>
				<th width=\"*\">Nama</th>
				<th width=\"50\">File</th>
				<th width=\"300\">Target</th>
				<th width=\"50\">Status</th>"
				;
				if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"75\">KONTROL</th>";
				$text.="</tr>
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
				var aSearch = jQuery(\"#aSearch\").val();
				var dirSearch = jQuery(\"#dirSearch\").val();
				var pSearch = jQuery(\"#pSearch\").val();
				var mSearch = jQuery(\"#mSearch\").val();
				var gSearch = jQuery(\"#gSearch\").val();
				var lSearch = jQuery(\"#lSearch\").val();

				window.open(sajax + '&json=excel&params=' + fSearch + '~' + bSearch + '~' + tSearch + '~' + aSearch + '~' + dirSearch + '~' + pSearch + '~' + mSearch + '~' + gSearch + '~' + lSearch, '_blank');
			});


});
</script>";
return $text;
}


function lData(){
global $s,$par,$menuAccess,$arrParameter,$arrParam, $areaCheck,$dpFile,$array_status,$mode;



if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
	$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);


$sWhere= "WHERE id_template IS NOT NULL";

if (!empty($_GET['fSearch']))
	$sWhere.= " and (				
		lower(template) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
		
		)";

$arrOrder = array(	
	"template",
	);
$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];


$sql="SELECT * from absen_template $sWhere order by $orderBy $sLimit";
				// var_dump($sql);
$res=db($sql);

$json = array(
	"iTotalRecords" => mysql_num_rows($res),
	"iTotalDisplayRecords" => getField("SELECT COUNT(*) FROM `absen_template` $sWhere "),
	"aaData" => array(),
	);

$arrData = arrayQuery("select kodeData, namaData from mst_data");

$no=intval($_GET['iDisplayStart']);
while($r=mysql_fetch_array($res)){
	$no++;


    $r[status]= $r[status] == 't' ? '<img src="styles/images/t.png" title="Aktif">' : '<img src="styles/images/f.png" title="Aktif">';

    // $foto="<a href=\"?par[mode]=viewFoto&par[id_aset]=$r[id_aset]".getPar($par, "mode")."\" title=\"Jumlah Pejabat\">".getField("SELECT count(*) from aset_foto where id_aset ='$r[id_aset]'")."</a>"; 

	$controlEmp="";

	if(isset($menuAccess[$s]["edit"]))
		$controlEmp.="<a href=\"?par[mode]=edit&par[id_template]=$r[id_template]".getPar($par,"mode, id_template")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				
	
	if(isset($menuAccess[$s]["delete"]))
		$controlEmp.="<a href=\"?par[mode]=del&par[id_template]=$r[id_template]".getPar($par, "mode,id_template")."\" class=\"delete\" title=\"Delete Data\" onclick=\"return confirm(`Apakah anda ingin menghapus data ini?`)\"></a>";				

	$data=array(
		"<div align=\"center\">".$no.".</div>",				
		"<div align=\"left\">".$r[template]."</div>",
		"<div align=\"center\"><a href=\"#\" onclick=\"openBox('view.php?doc=manual&par[id_template]=$r[id_template]".getPar($par,"mode,id_template")."',825,525);\"><img src=\"".getIcon($r[file_template])."\" width=\"20px\"></a></div>",
		"<div align=\"left\">".$r[file_target]."</div>",
		"<div align=\"center\">".$r[status]."</div>",
		"<div align=\"center\">".$controlEmp."</div>",
		);


	$json['aaData'][]=$data;
}
return json_encode($json);
}





function form() {

global $p, $s, $m, $menuAccess, $arrTitle, $par, $inp, $cID, $_submit, $mode, $array_status,$array_shift,$dpFile,$arr_impact,$arr_posisi,$array_status;

if(isset($_submit)) {

     if($par['mode'] == 'tambah') {
     		repField();
     		$id_template = getField("select id_template from absen_template order by id_template desc limit 1")+1;		
			$fileFile = $_FILES["file"]["tmp_name"];
			$fileFile_name = $_FILES["file"]["name"];
			if(($fileFile!="") and ($fileFile!="none")){						
			fileUpload($fileFile,$fileFile_name,$dpFile);			
			$file = $id_template."-".$inp[file_target].".".getExtension($fileFile_name);
			fileRename($dpFile, $fileFile_name, $file);			
			}

			$sql = "INSERT INTO `absen_template` 

			(`id_template`, `template`, `file_target`, `file_template`,`keterangan`,`status`,`create_by`, `create_date`)

			VALUES 

			('$id_template', '$inp[template]','$inp[file_target]','$file','$inp[keterangan]','$inp[status]','$cID','".date('Y-m-d H:i:s')."')";

			if(db($sql)) {
				// echo "Berhasil";

				// var_dump($sql);
				// die();

			    echo "<script>alert('Data berhasil disimpan!')</script>";

			    echo "<script>window.location='index.php?".getPar($par,"mode,id_template")."';</script>";

			} else { 
				echo "Gagal";
				var_dump($sql);
				die();
			    echo "<script type=\"text/javascript\">alert('data gagal disimpan!');</script>";

			}
     }else{
 		repField();
		$fileFile = $_FILES["file"]["tmp_name"];
		$fileFile_name = $_FILES["file"]["name"];
		if(($fileFile!="") and ($fileFile!="none")){						
		fileUpload($fileFile,$fileFile_name,$dpFile);			
		$file = $par[id_template]."-".$inp[file_target].".".getExtension($fileFile_name);
		fileRename($dpFile, $fileFile_name, $file);			
		}
		$sql = "UPDATE 
        
        `absen_template` 
        
        SET 

        `template`   			= '$inp[template]', 
        `file_target`          	= '$inp[file_target]',
        `file_template`       	= '$file', 
        `status`        		= '$inp[status]',
        `keterangan`        	= '$inp[keterangan]',
        `update_by`     		= '$cID', 
        `update_date`   		= '".date('Y-m-d H:i:s')."' 
        
        WHERE `id_template` = '$par[id_template]'";

        if(db($sql)) {
            
            echo "<script>alert('Data berhasil disimpan!')</script>";

			echo "<script>window.location='index.php?par[mode]=edit&par[id_template]=$par[id_template]".getPar($par,"mode,id_template")."';</script>";
            
        } else {

        	// var_dump($sql);
        	// die();
            
            echo "<script type=\"text/javascript\">alert('Data gagal Diupdate!');</script>";
            
        }

     }

 }

$res = db("SELECT * FROM `absen_template` WHERE `id_template` = '$par[id_template]'");
$row = mysql_fetch_assoc($res);


// setValidation("is_null", "inp[sk]", "Anda belum mengisi isi field SK");
// setValidation("is_null", "inp[tanggal]", "Anda belum mengisi isi field tanggal");
// setValidation("is_null", "inp[keterangan]", "Anda belum mengisi isi field Keterangan");

$text.= getValidation();

$text.= "
<script src=\"sources/js/default.js\"></script>

 <div class=\"pageheader\">

        <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

        " . getBread() . "

        <span class=\"pagedesc\">&nbsp;</span>

    </div>";

$text.= "
<style>

    .chosen-container {

        width: 200px !important;   
        
    }

</style>

<div id=\"contentwrapper\" class=\"contentwrapper\">

    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  enctype=\"multipart/form-data\" onsubmit=\"return validation(document.form);\">

        <div style=\"position:absolute; top: 15px; right: 20px;\">

            <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" />
            <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,'mode,id_template')."'\" />
        </div>

        <fieldset>

            <legend style=\"padding:10px; margin-left:20px;\">Master Template</legend>	


             <p>

                <label class=\"l-input-small\">Nama Template</label>

                <div class=\"field\">

                    <input type=\"text\" id=\"inp[template]\" name=\"inp[template]\" class=\"\" value=\"". $row['template'] ."\" />

                </div>

            </p>

              <p>

                <label class=\"l-input-small\">File Target</label>

                <div class=\"field\">

                    <input type=\"text\" id=\"inp[file_target]\" name=\"inp[file_target]\" class=\"\" value=\"". $row['file_target'] ."\" />

                </div>

            </p>



           <p>
			<label class=\"l-input-small\">File</label>
			<div class=\"field\">
				";
				if($row[file_template] != ""){
					$text.="<img src=\"".getIcon($row[file_template])."\" width=\"30px\">
					<a href=\"?par[mode]=hapusFoto&par[id_template]=".$row[id_template].getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete image ?')\" class=\"action delete\"><span>Delete</span></a>
					<br clear=\"all\">";

				}else{
					$text.="<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:445px;\" maxlength=\"100\" />
					<div class=\"fakeupload\" style=\"width:476px;\">
						<input type=\"file\" id=\"file\" name=\"file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
					</div>";
				}
				
				$text.="

			</div>
			</p>

            <p>

                <label class=\"l-input-small\">Keterangan</label>

                <div class=\"field\">

                    <input type=\"text\" id=\"inp[keterangan]\" name=\"inp[keterangan]\" class=\"\" value=\"". $row['keterangan'] ."\" />

                </div>

            </p>

               <p>
                    <label class=\"l-input-small\">Status</label>
                    <div class=\"field\">
                        <div class=\"sradio\" style=\"padding-top:5px;padding-left:8px;\">
                            ";

                            $checked = "checked";

                            foreach ($array_status as $key => $value) {

                                if(isset($row['status'])) $checked = ($row['status'] == $key) ? "checked" : "";

                            $text.="
                            <input type=\"radio\" name=\"inp[status]\" value=\"$key\" $checked > <span style=\"padding-right:10px;\">$value</span>
                            ";

                            $checked = "";
                            } 
                            
                            $text.="

                        </div>
                    </div>
                </p>


            
    	</fieldset>
    </form>
</div>
";

return $text;

}



function hapus() {

    global $par,$dpFile;

    $fileTemplate = getField("select file_template from absen_template where id_template='$par[id_template]'");
	if(file_exists($dpFile.$fileTemplate) and $fileTemplate!="")unlink($dpFile.$fileTemplate);

    $sql = "DELETE FROM `absen_template` WHERE `id_template` = '$par[id_template]' ";

    if(db($sql)) {

        $text= "<script type=\"text/javascript\">alert('Data Telah Berhasil Dihapus'); window.location='?".getPar($par, "mode, id_template")."';</script>";

    } else {

        $text= "<script type=\"text/javascript\">alert(Data Gagal Dihapus!'); window.location='?".getPar($par, "mode, id_template")."';</script>";

    }

    return $text;

}



function hapusFoto(){
    global $s,$par,$dpFile;

    $foto_file = getField("select file_template from absen_template where id_template='$par[id_template]'");
    if (file_exists($dpFile . $foto_file) and $foto_file != "")
        unlink($dpFile . $foto_file);

    $sql="UPDATE absen_template set file_template='' WHERE id_template = '$par[id_template]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit".getPar($par, "mode")."';</script>";
}
function getContent($par){
	global $s,$_submit,$menuAccess;
	switch($par[mode]){
		
		case 'tambah':
	    if(isset($menuAccess[$s]["add"])) $text= form(); else  $text=lihat();
	    break;


		case 'edit':
	    if(isset($menuAccess[$s]["add"])) $text= form(); else  $text=lihat();
	    break;


	    case 'del':
	    if(isset($menuAccess[$s]["edit"])) $text=hapus(); else echo "access denied";
	    break;

	
	    case "hapusFoto":
        	if(isset($menuAccess[$s]["delete"])) $text = hapusFoto(); else $text = detail();
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
?>