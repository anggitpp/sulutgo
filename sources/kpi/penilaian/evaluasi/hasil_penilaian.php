<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dFile = "files/kinerja_individu/";

global $s, $inp, $par, $_submit, $menuAccess, $getBulan;
$par[idPeriode] = empty($par[idPeriode]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1") : $par[idPeriode];
$getBulan = queryAssoc("SELECT * FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk = $par[idPeriode] order by urutanData asc");

function getContent($par)
{
    global $s, $inp, $par, $_submit, $menuAccess;

    switch($par[mode])
    {   
        case "lst":
        $text = lData();
        break;
        
        case "getPosisi":
        $text = getPosisi();
        break;
        
        case "getBulanRealisasi":
        $text = getBulanRealisasi();
        break;
        
        case "detail":
        $text = detail();
        break;
        
        default:
        $text = lihat();
        break;
        
        case "edit":
        if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
        break;
        
        case "delFile":
        if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
        break;
        
        case "formSaran":
        $text = empty($_submit) ? formSaran() : simpanSaran(); 
        break;
    }
    return $text;
}

function simpanSaran()
{
    global $s, $id, $inp, $par, $arrParameter,$db;
    
    
    db("update pen_realisasi_individu set saran = '$inp[saran]' where id_realisasi = $par[id_realisasi]");
    echo "<script>closeBox();</script>";
    echo "<script>alert('Data berhasil disimpan!')</script>";
    echo "<script>reloadPage();</script>";
}

function formSaran()
{
    global $s, $id, $inp, $par, $arrParameter,$db;
    
    $saran = getField("select saran from pen_realisasi_individu where id_realisasi = $par[id_realisasi]");
    
    $text .= "

	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Saran</h1>
	</div>
    <div id=\"contentwrapper\" class=\"contentwrapper\">
        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
            <textarea class=\"mediuminput\" id=\"inp[saran]\" name=\"inp[saran]\" style=\"margin-left:-5px; height: 100px; width:100%;\">$saran</textarea>
        	<p style=\"position: absolute;top:10px;right: 20px;\">
				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
			</p>
        <form>
    </di>
    ";
    
    return $text;
}

function getPosisi(){
    global $s, $id, $inp, $par, $arrParameter,$db;
    $getData = queryAssoc("SELECT idKode , subKode FROM pen_setting_kode WHERE kodeTipe = $par[idTipe] and statusKode = 't' order by idKode asc");
    echo json_encode($getData);
}

function getBulanRealisasi(){
    global $s, $id, $inp, $par, $arrParameter,$db;
    $getData = queryAssoc("SELECT kodeData , namaData FROM mst_data WHERE kodeInduk = $par[idTahun] order by kodeData asc");
    echo json_encode($getData);
}

function lihat()
{
	global $s,$inp,$par,$arrTitle,$menuAccess,$cID,$cTipeAkses,$cKodeAkses, $getBulan, $arrParameter;
    
    
    $totalBulan = count($getBulan);
	$cols = 5 + $totalBulan;
    for($i=0;$i<=$totalBulan;$i++)
    {
        $dt[$i] = $cols - $i;
    }
    $dta = array(5,4); 
    $noSort = array_merge($dt,  $dta);
	$text = table($cols, $noSort,"lst","true","","dataList","");
	$text .= "

	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
	</div>
    
	<div id=\"contentwrapper\" class=\"contentwrapper\">
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
        
		    <form id=\"formPeriode\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
    			<div id=\"pos_l\" style=\"float:left;\">
    				<p>         
    					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\" onkeyup=\"\"/>
                        ".comboData("SELECT kodeTipe , namaTipe FROM pen_tipe where statusTipe = 't' order by urutanTipe asc","kodeTipe","namaTipe","combo1","- Semua Kategori -",$combo1,"onchange=\"getPosisi(this.value,'".getPar($par,"mode")."');\"", "200px", "chosen-select")."
                        
    				    <input type=\"button\" id=\"sFilter\" value=\"+\" class=\"btn btn_search btn-small\" onclick=\"showFilter()\" />
                        <input type=\"button\" style=\"display:none;\" id=\"hFilter\" value=\"-\" class=\"btn btn_search btn-small\" onclick=\"hideFilter()\" />
                    </p>
    			</div> 
    			<div id=\"pos_r\" style=\"float:right;\">
                    <p>		
        				".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"document.getElementById('formPeriode').submit();\"", "200px") . "
                    </p>
    			</div>
            </form>
            
            
            <br clear=\"all\" />
        
            <div id=\"form_filter\" style=\"display:none;\">
            
            <fieldset>
                <form id=\"form\" class=\"stdform\">
                    <table width=\"100%\">
                        <tr>
                            <td width=\"50%\">
                                <p>
                					<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Posisi</label>
                					<div class=\"field\" style=\"margin-left:200px;\">
                						".comboData("SELECT idKode , subKode FROM pen_setting_kode where statusKode = 't' and kodeTipe = $combo1 order by idKode asc","idKode","subKode","combo2","- Semua Posisi -",$combo2,"", "200px", "chosen-select")."
            			                 <style>
                                         #combo2_chosen{min-width:250px;}
                                         </style>
                                    </div>
                				</p>    
                				<p>
                					<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Penilaian</label>
                					<div class=\"field\" style=\"margin-left:200px;\">
                						".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT'","kodeData","namaData","combo3","- Semua Penilaian -",$combo3,"onchange=\"getBulanRealisasi(this.value,'".getPar($par,"mode")."');\"", "200px", "chosen-select")."
            			                 <style>
                                         #combo3_chosen{min-width:250px;}
                                         </style>
                                    </div>
                				</p>
                                <p>
                					<label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Realisasi</label>
                					<div class=\"field\" style=\"margin-left:200px;\">
                						".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDB' and kodeInduk='$combo3'","kodeData","namaData","combo4","- Semua Realisasi -",$combo4,"", "200px", "chosen-select")."
                					    <style>
                                        #combo4_chosen{min-width:250px;}
                                        </style>
                                    </div>
                				</p>
                                
                            </td>
                            <td width=\"50%\"></td>
                        </tr>
                    </table>
                </form>
        	</fieldset>
		    <br clear=\"all\"/>
        </div>
        
        <div style=\"overflow-x:scroll;\">
    		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\" >
    			<thead>
    				<tr>
    					<th style=\"vertical-align:middle; min-width:20px;\" rowspan=\"2\">no</th>
                        <th style=\"vertical-align:middle; min-width:200px;\" rowspan=\"2\">nama</th>
                        <th style=\"vertical-align:middle; min-width:100px;\" rowspan=\"2\">NPP</th>
                        <th style=\"vertical-align:middle; min-width:200px;\" rowspan=\"2\">$arrParameter[38]</th>
    					<th style=\"vertical-align:middle; min-width:200px;\" rowspan=\"2\">jabatan</th>
                        <th style=\"vertical-align:middle;\" colspan=\"$totalBulan\">nilai</th>
    				</tr>
                    <tr>
                        ";
                        foreach($getBulan as $bln)
                        {
                            $text.="<th style=\"vertical-align:middle; min-width:75px;\">$bln[kodeMaster]</th>";
                        }
                        $text.="
    				</tr>
    			</thead>
    			<tbody></tbody>
    
    		</table>
        </div>
	</div>
    
	";
	if($par[mode] == "xlsSimpananPokok"){            
		xlsSimpananPokok();          
		$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s]." Simpanan Pokok - ".time())).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
	}
	$text.="
	<script>
		jQuery(\"#btnExportSimpananPokok\").live('click', function(e){
			e.preventDefault();
			window.location.href=\"?par[mode]=xlsSimpananPokok\"+ \"".getPar($par,"mode","tanggal")."\"+ \"&par[tanggal]=\" + jQuery(\"#tSearch\").val();
		});
	</script>
	";

	return $text;
}

function lData()
{
	global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m,$cID,$cTipeAkses,$cKodeAkses, $getBulan;

	if($_GET[json] == 1){
		header("Content-type: application/json");
	}

	if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1'){
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
	}

	$sWhere = " WHERE b.tipePenilaian != 0 AND b.kodePenilaian IS NOT NULL";
    
    if (!empty($_GET['combo1'])) $sWhere.= " and b.tipePenilaian = '$_GET[combo1]'";
    if (!empty($_GET['combo2'])) $sWhere.= " and b.kodePenilaian = '$_GET[combo2]'";
    if (!empty($_GET['combo3'])) $sWhere.= " and b.tahunPenilaian = '$_GET[combo3]'";
    if (!empty($_GET['combo4'])) $sWhere.= " and b.bulanPenilaian = '$_GET[combo4]'";
 
	if (!empty($_GET['fSearch'])){
		$sWhere.= " and (     
                		lower(a.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
                		)";
	}
    
	$arrOrder = array("a.name","a.name","a.reg_no");

	$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

	$sql = "
            SELECT * FROM emp AS a
            JOIN pen_pegawai AS b ON (b.idPegawai = a.id)
            $sWhere order by $orderBy $sLimit
           ";
           
    //echo $sql;die;

	$res = db($sql);

	$json = array(
		"iTotalRecords" => mysql_num_rows($res),
		"iTotalDisplayRecords" => getField("select count(a.id) from emp AS a
                                            JOIN pen_pegawai AS b ON (b.idPegawai = a.id)
                                	        $sWhere "),
		"aaData" => array()
		);

	$no = intval($_GET['iDisplayStart']);
    
	while($r = mysql_fetch_array($res)){
		$no++;
        
		$data1 = array(
			"<div align=\"center\">".$no."</div>",
			"<div align=\"left\">".strtoupper($r[name])."</div>",
            "<div align=\"center\">".$r[reg_no]."</div>",
            "<div align=\"left\">".getField("select namaTipe from pen_tipe where kodeTipe = $r[tipePenilaian]")."</div>",
            "<div align=\"left\">".getField("select subKode from pen_setting_kode where idKode = $r[kodePenilaian]")."</div>"
            );
        
        
        $no_arr=0;
        foreach($getBulan as $bln)
        {
            $no_arr++;
            $hasil = getField("select nilai from pen_hasil where id_periode = $r[tahunPenilaian] and id_bulan = $bln[kodeData] and id_pegawai = $r[idPegawai]");
            
            $getMax = queryAssoc("SELECT MAX(nilaiMax),a.* FROM pen_setting_konversi AS a  where a.idPeriode = $r[tahunPenilaian]");
            $max = $getMax[0];
            
            $tombol = "<a href=\"?par[mode]=detail&par[idPenPegawai]=$r[id]&par[idPegawai]=$r[idPegawai]&par[tipePenilaian]=$r[tipePenilaian]&par[kodePenilaian]=$r[kodePenilaian]&par[tahunPenilaian]=$r[tahunPenilaian]&par[bulanPenilaian]=$r[bulanPenilaian]".getPar($par,"mode,idPenPegawai,idPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,bulanPenilaian,idPeriode")."\"><strong>$hasil</strong></a>";
            
            if(($hasil >= $max['nilaiMax']))
            {
                $data2[$no_arr] = "<div align=\"center\" style=\"background-color:".$max[warnaKonversi].";\">$tombol</div>"; 
            }
            else
            {
                if(!empty($hasil))
                {
                    $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = $r[tahunPenilaian] and $hasil BETWEEN nilaiMin AND nilaiMax");
                    $konv = $getKonversi[0];
                    $data2[$no_arr] = "<div align=\"center\" style=\"background-color:".$konv[warnaKonversi].";\">$tombol</div>";
                }
                else
                {
                    $data2[$no_arr] = "<div align=\"center\">-</div>";
                }
                
            }
            
            
        }       
                    
        $dataX = array_merge($data1,$data2);
        
		$json['aaData'][] = $dataX;
	}

	return json_encode($json);
}

function hapusFile(){
    global $s,$inp,$par,$dFile,$cUsername;
    
    $FileTarget = getField("select file_upload from pen_realisasi_individu where id_realisasi='$par[id_realisasi]'");
    if(file_exists($dFile.$FileTarget) and $FileTarget!="") unlink($dFile.$FileTarget);

    $sql="update pen_realisasi_individu set file_upload='' where id_realisasi='$par[id_realisasi]'";
    db($sql);

    echo "<script>closeBox();reloadPage();</script>";
}

function simpan()
{
    global $s,$inp,$par,$cUsername,$cID,$dFile;
    
    if(empty($par['id_realisasi']))
    {
        $fileIcon = $_FILES["FileTarget"]["tmp_name"];
        $fileIcon_name = $_FILES["FileTarget"]["name"];
        if(($fileIcon!="") and ($fileIcon!="none"))
        {
            fileUpload($fileIcon,$fileIcon_name,$dFile);
            $FileTarget = time().".".getExtension($fileIcon_name);
            fileRename($dFile, $fileIcon_name, $FileTarget);
        }
        else
        {
            $FileTarget = "";
        }  
        $id_realisasi= getLastId('pen_realisasi_individu', 'id_realisasi');
        db("insert into pen_realisasi_individu 
         (id_realisasi,
         id_pegawai,
         id_pen_pegawai,
         id_sasaran,
         id_tahun,
         id_bulan,
         realisasi,
         file_upload,
         keterangan,
         create_by, 
         create_date) 
         values 
         ($id_realisasi,
         '$par[idPegawai]',
         '$par[idPenPegawai]',
         '$par[id_sasaran]',
         '$par[tahunPenilaian]',
         '$par[bulanPenilaian]',
         '$inp[realisasi]',
         '$FileTarget',
         '$inp[keterangan]',
         '$cID', 
         '".date("Y-m-d H:i:s")."')");
    }
    else
    {
        $fileIcon = $_FILES["FileTarget"]["tmp_name"];
        $fileIcon_name = $_FILES["FileTarget"]["name"];
        if(($fileIcon!="") and ($fileIcon!="none"))
        {
            fileUpload($fileIcon,$fileIcon_name,$dFile);
            $FileTarget = time().".".getExtension($fileIcon_name);
            fileRename($dFile, $fileIcon_name, $FileTarget);
            
            $updateFile = "file_upload = '$FileTarget',";
        }
        else
        {
            $updateFile = "";
        }
        db("
            update pen_realisasi_individu set 
            realisasi = '$inp[realisasi]',
            keterangan = '$inp[keterangan]',
            $updateFile
            update_by = '$cID',
            update_date = '".date("Y-m-d H:i:s")."'
            where 
            id_realisasi = $par[id_realisasi]
            ");
    }
    
    
    
    echo "<script>closeBox();alert('Data Berhasil Disimpan!');reloadPage();</script>";
}

function form(){

    global $s,$inp,$par,$tab,$arrTitle,$arrParameter,$menuAccess,$dFile;

    //debugVar($par);die;

    $sql="select * from pen_realisasi_individu where id_realisasi='$par[id_realisasi]'";

    $res=db($sql);

    $r=mysql_fetch_array($res);


    setValidation("is_null","inp[targetIndividu]","anda harus mengisi target");

    setValidation("is_null","inp[satuanIndividu]","anda harus mengisi satuan");

    $text = getValidation();        

    

    $text.= "<div class=\"centercontent contentpopup\">

    <div class=\"pageheader\">

        <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

        ".getBread(ucwords("nilai"))."                              

    </div>              

    <div id=\"contentwrapper\" class=\"contentwrapper\">                

        <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\"> 

            <div style=\"position:absolute; top:0px; right:0px; margin-top:10px; margin-right:20px;\">

                <input type=\"hidden\" id=\"tab\" name=\"tab\" value=\"".$par[idAspek]."\">
                
                <input type=\"hidden\" id=\"tab\" name=\"inp[id_realisasi]\" value=\"".$r[id_realisasi]."\">

                <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>

                <input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>

            </div>  
            
            <br />
            
            ";
            $getSasasaran = queryAssoc("select * from pen_sasaran_obyektif where idSasaran = $par[id_sasaran]");
            $sas = $getSasasaran[0];
            $text.="
            
            <fieldset style=\"padding:10px; border-radius: 10px;\">                     
                
                <legend>Kinerja</legend>

                <table width=\"100%\">

                    <tr>
                        
                        <td width=\"100%\" style=\"varti-align:top\">
                            
                            <p>
                                
                                <label class=\"l-input-small\">Sasaran</label>
                                
                                <span class=\"field\">
                                    
                                    $sas[uraianSasaran]
                                    
                                </span>
                                
                            </p>
                            
                            <p>
                                
                                <label class=\"l-input-small\">Pencapaian Target</label>
                                
                                <span class=\"field\">
                                    
                                    $sas[targetSasaran] 
                                    $sas[targetSasaran2]
                                    
                                </span>
                                
                            </p>
                            
                            <p>
                                
                                <label class=\"l-input-small\">Keterangan</label>
                                
                                <span class=\"field\">
                                    
                                    $sas[keteranganSasaran]
                                    
                                </span>
                                
                            </p>    
                            
                            <p>
                                
                                <label class=\"l-input-small\">Scoring</label>
                                
                                <span class=\"field\">
                                    
                                    $sas[scoringSasaran]
                                    
                                </span>
                                
                            </p>
                            
                            <p>
                                
                                <label class=\"l-input-small\">Measurement</label>
                                
                                <span class=\"field\">
                                    
                                    $sas[measurementSasaran]
                                    
                                </span>
                                
                            </p>                    
                            
                        </td>
                        
                    </tr>

                </table>

            </fieldset> 
            
            
            <br />
            
            
            <fieldset style=\"padding:10px; border-radius: 10px;\">                     
                
                <legend>Realisasi</legend>

                <table width=\"100%\">

                    <tr>
                        
                        <td width=\"100%\" style=\"varti-align:top\">
                            
                            <p>
                                
                                <label class=\"l-input-small\">Realisasi</label>
                                
                                <div class=\"field\">          
                                    <input type=\"text\" id=\"inp[realisasi]\" name=\"inp[realisasi]\"  value=\"$r[realisasi]\" class=\"mediuminput\" style=\"width:238px;\"/>
                                </div>
                                
                            </p>
                            
                            <p>
                                <label class=\"l-input-small\">File</label>
                                <div class=\"field\">          
                                    ";
                                    if($r[file_upload] != ""){
                                        $text.= "<a href=\"#Preview\" title=\"Preview File\" onclick=\"openBox('view.php?&par[tipe]=file_realisasi_individu&par[id_realisasi]=$r[id_realisasi]',400,400);\"><img style=\"width:30px;\" src=\"".getIcon($r[file_upload])."\"></a>
                                        <a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('are you sure to delete file ?')\" class=\"action delete\"><span>Delete</span></a>
                                        <br clear=\"all\">";
                                        
                                    }else{
                                        $text.= "<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:238px;\" maxlength=\"100\" />
                                        <div class=\"fakeupload\" style=\"width:306px;\">
                                            <input type=\"file\" id=\"FileTarget\" name=\"FileTarget\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
                                        </div>";
                                    }
                                    $text.="
                                </div> 
                            </p>
                            
                            <p>
                                
                                <label class=\"l-input-small\">Keterangan</label>
                                
                                <div class=\"field\">          
                                    <textarea name=\"inp[keterangan]\" style=\"width:500px; height:60px;\" id=\"inp[keterangan]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >".$r['keterangan']."</textarea>
                                </div> 
                                
                            </p>                        
                            
                        </td>
                        
                    </tr>

                </table>

            </fieldset>             

        </form>

    </div>";

    return $text;

}

function detail()
{
    global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$dirKtp,$dirUsaha;
    
    echo "<div class=\"pageheader\">
    
            <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
            
            ".getBread(ucwords($par[mode]." data"))."								
            
            </div>
    
    <form class=\"stdform\">
    
        <div style=\"padding:20px; margin-top:-30px;\">				
        
            <fieldset style=\"padding:10px; border-radius: 10px;\">						
            
            <legend style=\"padding:10px; margin-left:20px;\"><h4>PEGAWAI</h4></legend>";
            
            $_SESSION["curr_emp_id"] = $par[idPegawai];	
            $_SESSION["kodePenilaian"] = $par[kodePenilaian];	
            
            require_once "tmpl/__emp_header__penilaian.php";
            
            echo "</fieldset>
        </div>
    </form>";
    
    $text.="
    <script>
        jQuery( document ).ready(function() {
            hideMenu();
        });
    </script>
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
        <div style=\"position:absolute; top:0px; right:0px; margin-top:10px; margin-right:20px;\">					

			<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\"  onclick=\"window.location='?".getPar($par,"mode,idPegawai,idPenPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,bulanPenilaian")."';\"/>

		</div>
";
$par[bulanPenilaian] = empty($par[bulanPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by urutanData asc limit 1") : $par[bulanPenilaian];

$getPen = queryAssoc("select * from pen_pegawai where id=$par[idPenPegawai]");
$pen = $getPen[0]; 
$text.="
<form id=\"formFilter\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
    
    <input type=\"hidden\" name=\"par[mode]\" value=\"$par[mode]\">
    <input type=\"hidden\" name=\"par[idPegawai]\" value=\"$par[idPegawai]\">
    <input type=\"hidden\" name=\"par[idPenPegawai]\" value=\"$par[idPenPegawai]\">
    <input type=\"hidden\" name=\"par[tipePenilaian]\" value=\"$par[tipePenilaian]\">
    <input type=\"hidden\" name=\"par[kodePenilaian]\" value=\"$par[kodePenilaian]\">
    <input type=\"hidden\" name=\"par[tahunPenilaian]\" value=\"$par[tahunPenilaian]\">
    <input type=\"hidden\" name=\"par[bulanPenilaian]\" value=\"$par[bulanPenilaian]\">
    <input type=\"hidden\" name=\"par[idPeriode]\" value=\"$par[idPeriode]\">
    
    <fieldset>
        <table style=\"width: 100%;\">
            <tr>
                <td style=\"width: 50%;\">
                    <p>
                        <label style=\"width:150px\" class=\"l-input-medium\">PERIODE TAHUN</label>
                        <span class=\"field\">".getField("select namaData from mst_data where kodeData = $pen[tahunPenilaian]")."</span>
                    </p>
                    <p>
                        <label style=\"width:150px\" class=\"l-input-medium\">PERIODE PENILAIAN</label>
                        <span class=\"field\">".getTanggal($pen[periodeStart])." s/d ".getTanggal($pen[periodeEnd])."</span>
                    </p>
                </td>
                <td style=\"width: 50%;\">
                    <p>
                        <label style=\"width:150px\" class=\"l-input-medium\">PERIODE REALISASI</label>
                        <div class=\"field\">".comboData("SELECT namaData,kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by urutanData asc","kodeData","namaData","par[bulanPenilaian]","",$par['bulanPenilaian'],"onchange=\"document.getElementById('formFilter').submit();\"","210px;","chosen-select")."</div>
                    </p>
                </td>
                <tr>
                </table>
            </fieldset>
        </form>
        <br />
        
        ";
        
        $par[idPenilaian] = $par[kodePenilaian];
        
        $sql="select * from pen_setting_aspek where idPeriode='$par[tahunPenilaian]'";

        $res=db($sql);

        while($r=mysql_fetch_array($res)){

            $arrAspek["$r[idAspek]"] = $r[urutanAspek]."\t".$r[namaAspek];

        }

        

        $text.="<ul class=\"hornav\">";             

        $idx=0;

        if(is_array($arrAspek)){

          asort($arrAspek);

          reset($arrAspek);

          while (list($idAspek, $valAspek) = each($arrAspek)){

            list($urutanAspek, $namaAspek) = explode("\t", $valAspek);          

            if(empty($tab)) $tab = $idAspek;

            $current = $tab == $idAspek ? "class=\"current\"" : "";

            $text.="<li ".$current."><a href=\"#id_".$idAspek."\"  onclick=\"document.getElementById('tab').value=".$idAspek.";\">$namaAspek</a></li>";                 

            $idx++;         

        }

    }       

    $text.="</ul>
    
    <div id=\"pos_r\" style=\"float:right; margin-top:-30px; margin-right:20px;\">
        <!-- <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a> -->
    </div>
    
    <input type=\"hidden\" id=\"tab\" name=\"tab\" value=\"".$tab."\">";

    

    $idx=0;

    if(is_array($arrAspek)){

      asort($arrAspek);

      reset($arrAspek);

      while (list($idAspek, $valAspek) = each($arrAspek)){

        list($urutanAspek, $namaAspek) = explode("\t", $valAspek);          

        $display = $tab == $idAspek ? "" : "style=\"display:none;\"";

        

        if(empty($par["idPrespektif_".$idAspek])) $par["idPrespektif_".$idAspek] = getField("select idPrespektif from pen_setting_prespektif where kodePrespektif='".$idAspek."' limit 1");

        if(empty($par["idIndikator_".$idAspek])) $par["idIndikator_".$idAspek] = getField("select kodeIndikator from pen_setting_prespektif_indikator where idPrespektif='".$par["idPrespektif_".$idAspek]."' and statusIndikator='t' limit 1");

        

        $text.="<div id=\"id_".$idAspek."\" class=\"subcontent\" ".$display.">

        <form class=\"stdform\">

            
          
          <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">

            <thead>

                <tr>

                    <th width=\"20\" style=\"vertical-align:middle;\">No.</th>                           

                    <th style=\"vertical-align:middle;\">Sasaran KPI</th>     
                    
                    <th style=\"vertical-align:middle;\">Bobot</th>
                    
                    <th style=\"vertical-align:middle;\">Pencapaian Target<br />(S/d bulan desember 2019)</th>
                    
                    <!-- <th style=\"vertical-align:middle;\">target<br />kinerja</th> -->
                    
                    <th style=\"vertical-align:middle;\">target<br />proporsional<br />S/D bln lap</th>
                    
                    <th style=\"vertical-align:middle;\">realisasi<br />S/D bln lap</th>
                    
                    <th style=\"vertical-align:middle;\">pencapaian thd target</th>
                    
                    <th style=\"vertical-align:middle;\">pencapaian target<br />s/d bln berjalan</th>
                    
                    <!-- <th style=\"vertical-align:middle;\">nilai<br />predikat<br />pencapaian</th> -->
                    
                    <th style=\"vertical-align:middle;\">Nilai</th>
                    
                    <th style=\"vertical-align:middle;\">Dok</th>
                    
                    <th style=\"vertical-align:middle;\">WOM</th>
                    
                    <th style=\"vertical-align:middle;\">Saran</th>
                    
                </tr>

            </thead>
            
            <tbody>
                ";
                $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
                $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = $par[kodePenilaian] and idAspek = $idAspek and idTipe = $idTipe and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");
                foreach($getPerspektif as $prs)
                {
                    $text.="
                    <tr style=\"background-color:#d9d9d9;\">
                        <td colspan=13><strong>".strtoupper($prs[namaPrespektif])."</strong></td>
                    </tr>
                    ";
                    $getIndikator=queryAssoc("select * from pen_setting_prespektif_indikator where idPrespektif=$prs[idPrespektif] order by urutanIndikator asc");
                    $noInd=0;
                    foreach($getIndikator as $ind)
                    {
                        $noInd++;
                        $text.="
                        <tr style=\"background-color:#f2f2f2;\">
                            <td colspan=13 style=\"font-size:8pt;\"><strong>$noInd. ".strtoupper($ind[uraianIndikator])."</strong></td>
                        </tr>
                        ";
                        $getObj=queryAssoc("select * from pen_sasaran_obyektif where idPrespektif = $prs[idPrespektif] and idIndikator = $ind[kodeIndikator] order by idSasaran asc");
                        $no=0;
                        foreach($getObj as $obj)
                        {
                            $no++;
                            
                            
                            $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
                            $rea = $realisasi[0];
                            $FileTarget = empty($rea[file_upload]) ? "-" : "<a href=\"#Preview\" title=\"Preview File\" onclick=\"openBox('view.php?&par[tipe]=file_realisasi_individu&par[id_realisasi]=$rea[id_realisasi]',900,500);\"><img style=\"width:30px;\" src=\"".getIcon($rea[file_upload])."\"></a>";
                            
                            $nilai = empty($rea) ? 0 : $rea[nilai];
                            
                            $nilai1 = empty($rea) ? "" : "-";
                            
                            $nilai2 = empty($rea) ? "" : "-";
                            
                            $nilai3 = empty($rea) ? "" : "-";
                            
                            $nilai4 = empty($rea) ? "" : "-";
                            
                            $nilai5 = empty($rea) ? "" : "-";
                            
                            $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = $par[tahunPenilaian] AND $nilai BETWEEN nilaiMin AND nilaiMax");
                            $konv = $getKonversi[0];
                            
                            $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran]");
                            $sas=$getSasaran[0];
                            
                            $text.="
                            <tr>
                                <td align=\"center\">$no - $rea[id_realisasi]</td>

                                <td>$obj[uraianSasaran]</td>
                                
                                <td align=\"center\">$sas[bobotIndividu]%</td>                  
                                
                                <td>$obj[targetSasaran] $obj[targetSasaran2]</td>
                                
                                <!-- <td align=\"center\">$sas[keteranganTargetIndividu]</td> -->
                                
                                <td align=\"center\">$nilai1</td>
                                
                                <td align=\"center\">$nilai2</td>
                                
                                <td align=\"center\">$nilai3</td>
                                
                                <td align=\"center\">$nilai4</td>
                                
                                <!-- <td align=\"center\">$nilai5</td> -->
                                
                                <td align=\"center\">".(($nilai==0)?" ":$nilai)."</td>
                                
                                <td align=\"center\">$FileTarget</td>
                                
                                <td align=\"center\" ><span style=\"background-color:".$konv[warnaKonversi].";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                                
                                <td align=\"center\">
                                    <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=formSaran&par[id_realisasi]=$rea[id_realisasi]".getPar($par,"mode,id_realisasi")."', 600, 200 )\" class=\"edit\"></a>
                                </td>
                                
                            </tr>
                            ";
                        }
                    }
                }
                $text.="
            </tbody>
        </table>

    </div>";

    $idx++;

}

}

$text.="   
</div>";
return $text;
}

?>