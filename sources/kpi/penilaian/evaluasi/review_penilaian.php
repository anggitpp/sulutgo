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
        
        case "deleteTindakan":
    		$text = deleteTindakan();
		break;
        
		default:
		  $text = lihat();
		break;
        
        case "viewData":
            $text = viewData(); 
		break;
        
        case "edit":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
		break;
        
        case "delFile":
            if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
        break;
        
        case "addAktivitas":
            if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formAktifitas() : simpanAktifitas(); else $text = lihat();
        break;
        
        case "formCatatan":
            if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formCatatan() : simpanCatatan(); else $text = lihat();
        break;
	}
	return $text;
}

function viewData()
{
    global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$dirKtp,$dirUsaha;
    
    //debugVar($par);die;
    
    echo "<div class=\"pageheader\">
    
            <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>								
            
            </div>
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
            <br />
            ";
            $text.=getDetailCompetency($par[idAspek], $par);
            $text.="
    </div>";
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

function deleteTindakan(){
    global $s,$inp,$par,$dFile,$cUsername;
    

    $sql="delete from pen_eva_tindakan where id_tindakan='$par[id_tindakan]'";
    db($sql);

    echo "<script>alert('Data Berhasil dihapus!');</script>";
    echo "<script>window.location='?par[mode]=detail".getPar($par,"mode,id_tindakan")."';</script>";
}

function simpanAktifitas()
{
    global $s,$inp,$par,$cUsername,$cID,$dFile;
    
    //debugVar($par);die;
    
    if(empty($par['id_tindakan']))
    {
        
        
        $id_tindakan = getLastId('pen_eva_tindakan', 'id_tindakan');
        
        db("insert into pen_eva_tindakan 
                                       (id_tindakan,
                                        id_pegawai,
                                        id_pen_pegawai,
                                        aktifitas,
                                        rencana,
                                        target,
                                        keterangan,
                                        create_by, 
                                        create_date) 
                                values 
                                        ($id_tindakan,
                                        '$par[idPegawai]',
                                        '$par[idPenPegawai]',
                                        '$inp[aktifitas]',
                                        '$inp[rencana]',
                                        '$inp[target]',
                                        '$inp[keterangan]',
                                        '$cID', 
                                        '".date("Y-m-d H:i:s")."')");
    }
    else
    {
        db("
            update pen_eva_tindakan set 
                                      aktifitas = '$inp[aktifitas]',
                                      target = '$inp[target]',
                                      rencana = '$inp[rencana]',
                                      keterangan = '$inp[keterangan]',
                                      update_by = '$cID',
                                      update_date = '".date("Y-m-d H:i:s")."'
                                where 
                                      id_tindakan = $par[id_tindakan]
        ");
    }
    
    
 
    echo "<script>closeBox();alert('Data Berhasil Disimpan!');reloadPage();</script>";
}


function simpanCatatan()
{
    global $s,$inp,$par,$cUsername,$cID,$dFile;
    
    //debugVar($par);die;
    
    if(empty($par['id_catatan']))
    {
        $id_catatan = getLastId('pen_eva_catatan', 'id_catatan');
        db("insert into pen_eva_catatan
                                       (id_catatan,
                                        id_pegawai,
                                        id_pen_pegawai,
                                        $par[tipe],
                                        create_by, 
                                        create_date) 
                                values 
                                        ($id_catatan,
                                        '$par[idPegawai]',
                                        '$par[idPenPegawai]',
                                        '".$inp[$par[tipe]]."',
                                        '$cID', 
                                        '".date("Y-m-d H:i:s")."')");

    }
    else
    {
        db("
            update pen_eva_catatan set 
                                      $par[tipe] = '".$inp[$par[tipe]]."',
                                      update_by = '$cID',
                                      update_date = '".date("Y-m-d H:i:s")."'
                                where 
                                      id_catatan = $par[id_catatan]
        ");
    }
    
    
 
    echo "<script>closeBox();alert('Data Berhasil Disimpan!');reloadPage();</script>";
}

function formAktifitas(){

	global $s,$inp,$par,$tab,$arrTitle,$arrParameter,$menuAccess,$dFile;

	//debugVar($par);die;

	$sql="select * from pen_eva_tindakan where id_tindakan='$par[id_tindakan]'";

	$res=db($sql);

	$r=mysql_fetch_array($res);

//
//	setValidation("is_null","inp[targetIndividu]","anda harus mengisi target");
//
//	setValidation("is_null","inp[satuanIndividu]","anda harus mengisi satuan");
//
//	$text = getValidation();		

	

    $text.= "<div class=\"centercontent contentpopup\">

			<div class=\"pageheader\">

				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

				".getBread(ucwords("aktifitas"))."								

			</div>				

			<div id=\"contentwrapper\" class=\"contentwrapper\">				

			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
            
            <br />
            
            <div style=\"position:absolute; top:0px; right:0px; margin-top:10px; margin-right:20px;\">

				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>

				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>

			</div>
			
            <fieldset style=\"padding:10px; border-radius: 10px;\">		

				<table width=\"100%\">

    				<tr>
    
    					<td width=\"100%\" style=\"varti-align:top\">
                        
                            <p>
    
    							<label class=\"l-input-small\">Aktifitas</label>
    
    							<div class=\"field\">          
                                    <textarea name=\"inp[aktifitas]\" style=\"width:300px; height:40px;\" id=\"inp[aktifitas]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >".$r['aktifitas']."</textarea>
                                </div> 
    
    						</p>
    
    						<p>
    
    							<label class=\"l-input-small\">Rencana Jadwal</label>
    
    							<div class=\"field\">          
                                    <input type=\"text\" id=\"inp[rencana]\" name=\"inp[rencana]\"  value=\"$r[rencana]\" class=\"mediuminput\" style=\"width:300px;\"/>
                                </div>
    
    						</p>
                            
                            <p>
    
    							<label class=\"l-input-small\">Target Hasil</label>
    
    							<div class=\"field\">          
                                    <textarea name=\"inp[target]\" style=\"width:300px; height:40px;\" id=\"inp[target]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >".$r['target']."</textarea>
                                </div> 
    
    						</p>
                            
                            <p>
    
    							<label class=\"l-input-small\">Keterangan</label>
    
    							<div class=\"field\">          
                                    <textarea name=\"inp[keterangan]\" style=\"width:300px; height:40px;\" id=\"inp[keterangan]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >".$r['keterangan']."</textarea>
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

function formCatatan(){

	global $s,$inp,$par,$tab,$arrTitle,$arrParameter,$menuAccess,$dFile;

	//debugVar($par);die;

	$sql="select * from pen_eva_catatan where id_catatan='$par[id_catatan]'";

	$res=db($sql);

	$r=mysql_fetch_array($res);

//
//	setValidation("is_null","inp[targetIndividu]","anda harus mengisi target");
//
//	setValidation("is_null","inp[satuanIndividu]","anda harus mengisi satuan");
//
//	$text = getValidation();		

	

    $text.= "<div class=\"centercontent contentpopup\">

			<div class=\"pageheader\">

				<h1 class=\"pagetitle\">Catatan ".ucfirst($par[tipe])."</h1>

				".getBread("Catatan ".ucfirst($par[tipe])."")."								

			</div>				

			<div id=\"contentwrapper\" class=\"contentwrapper\">				

			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
            
            <br />
            
            <div style=\"position:absolute; top:0px; right:0px; margin-top:10px; margin-right:20px;\">

				<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>

				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>

			</div>
			
            <fieldset style=\"padding:10px; border-radius: 10px;\">		

				<table width=\"100%\">

    				<tr>
    
    					<td width=\"100%\" style=\"varti-align:top\">
                        
                            <p>
    
    							<label class=\"l-input-small\">Catatan</label>
    
    							<div class=\"field\">          
                                    <textarea name=\"inp[$par[tipe]]\" style=\"width:300px; height:40px;\" id=\"inp[$par[tipe]]\" size=\"10\" maxlength=\"500\" class=\"vsmallinput\" >".$r[$par[tipe]]."</textarea>
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
    
    $par[bulanPenilaian] = empty($par[bulanPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk = $par[idPeriode] order by urutanData asc limit 1") : $par[bulanPenilaian];

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
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
            <div style=\"position:absolute; top:0px; right:0px; margin-top:10px; margin-right:20px;\">					

    			<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\"  onclick=\"window.location='?".getPar($par,"mode,idPegawai,idPenPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,bulanPenilaian")."';\"/>
    
    		</div>
            
            ";
            $getPen = queryAssoc("select * from pen_pegawai where id=$par[idPenPegawai]");
            $pen = $getPen[0]; 
            $text.="
            <form id=\"formFilter\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
                
                <input type=\"hidden\" name=\"par[mode]\" value=\"$par[mode]\">
                <input type=\"hidden\" name=\"par[idPenPegawai]\" value=\"$par[idPenPegawai]\">
                <input type=\"hidden\" name=\"par[idPegawai]\" value=\"$par[idPegawai]\">
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
            <div class=\"widgetbox\">
    			<div class=\"title\" style=\"margin-bottom: 10px\"><h3>Pencapaian Kinerja Saat Ini</h3></div>
                
                
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">

					<thead>

						<tr>

							<th width=\"20\">No</th>							

							<th>Sasaran Obyektif</th>
                            
                            <th>Bobot</th>
                            
                            <th>Nilai</th>
                            
                            <th>WOM</th>
                            
                            
                        </tr>
                    
                    </thead>
                    
                    <tbody>
                        
                        ";
                        $no=0;
                        $getAspek = queryAssoc("select * from pen_setting_aspek where idPeriode=$par[tahunPenilaian]");
                        foreach($getAspek as $asp)
                        {
                            $no++;
                            
                            $nilai = getNilai($asp[idAspek], $par, $par[bulanPenilaian]);
                            $konv = getWom($nilai, $par[tahunPenilaian]);
                            $text.="
                            <tr>
                                <td align=\"center\">$no</td>
                                <td><a href=\"#\" onclick=\"openBox('popup.php?par[mode]=viewData&par[idPenPegawai]=$par[idPenPegawai]&par[idPegawai]=$par[idPegawai]&par[tipePenilaian]=$par[tipePenilaian]&par[kodePenilaian]=$par[kodePenilaian]&par[tahunPenilaian]=$par[tahunPenilaian]&par[bulanPenilaian]=$par[bulanPenilaian]&par[idPeriode]=$par[idPeriode]&par[idAspek]=$asp[idAspek]".getPar($par,"mode")."', 1000, 500 )\"><strong>$asp[namaAspek]</strong></a></td>
                                <td align=\"center\">$asp[bobotAspek]%</td>
                                <td align=\"center\">$nilai</td>
                                <td align=\"center\" style=\"background-color:".$konv[warna].";\"><strong>".$konv[uraian]."</strong></td>
                                
                            </tr>
                            ";
                            $tnilai = $tnilai + $nilai;
                            $bobot = $bobot + $asp[bobotAspek];
                        }
                        
                        
                        
                        $yudisium = getField("select nilai from pen_hasil WHERE id_periode = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_pegawai = $par[idPegawai]");
                        $yudisium = empty($yudisium) ? 0 : $yudisium;
                        
                        $konv = getWom($yudisium, $par[tahunPenilaian]);
                        
                        $text.="
                        
                        <tr>
                            <td align=\"center\" colspan=\"2\"><strong>TOTAL </strong></td>
                            <td align=\"center\">$bobot%</td>
                            <td align=\"center\">$yudisium</td>
                            <td align=\"center\" style=\"background-color:".$konv[warna].";\"><strong>".$konv[uraian]."</strong></td>
                            
                        </tr>
                        
                    </tbody>
                    
                </table>
                
    		</div>
            
            
            
            <div class=\"widgetbox\">
    			<div class=\"title\" style=\"margin-bottom: 10px\"><h3>KENDALA, HAMBATAN DAN AKTIVITAS TINDAK LANJUT</h3></div>
                
                <ul class=\"hornav\">
                    <li class=\"current\"><a href=\"#tabTeknis\">Kendala Teknis</a></li>
                    <li><a href=\"#tabNonTeknis\">Non Teknis</a></li>
                </ul>
                
                <div id=\"tabTeknis\" class=\"subcontent\" style=\"display: block;\">
                
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">

    					<thead>
    
    						<tr>
    							<th width=\"20\">No</th>							
    
    							<th>KENDALA DAN HAMBATAN TEKNIS</th>
                                
                                <th>TINDAK LANJUT YANG DISEPAKATI</th>
                            </tr>
                        
                        </thead>
                        
                        <tbody>
                            ";
                            $getRes = queryAssoc("select * from pen_cmc where kategori = 987 and peserta = $par[idPegawai] and isue = 1");
                            $no=0;
                            if($getRes)
                            {
                                foreach($getRes as $act)
                                {
                                    $no++;
                                    $text.="
                                    <tr>
            							<td align=\"center\">$no</td>
                                        <td>$act[keterangan]</td>
                                        <td>$act[uraian]</td>
                                    </tr>
                                    ";
                                }
                            }
                            else
                            {
                                $text.="
                                    <tr>
            							<td align=\"center\">-</td>
                                        <td align=\"center\">-</td>
                                        <td align=\"center\">-</td>
                                    </tr>
                                    ";
                            }
                            
                            $text.="
                            
                        
                        </tbody>
                    
                    </table>
                
                </div>
                
                <div id=\"tabNonTeknis\" class=\"subcontent\" style=\"display: none;\">
                
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">

    					<thead>
    
    						<tr>
    							<th width=\"20\">No</th>							
    
    							<th>KENDALA DAN HAMBATAN TEKNIS</th>
                                
                                <th>TINDAK LANJUT YANG DISEPAKATI</th>
                            </tr>
                        
                        </thead>
                        
                        <tbody>
                        
                            ";
                            $getRes = queryAssoc("select * from pen_cmc where kategori = 987 and peserta = $par[idPegawai] and isue = 0");
                            $no=0;
                            if($getRes)
                            {
                                foreach($getRes as $act)
                                {
                                    $no++;
                                    $text.="
                                    <tr>
            							<td align=\"center\">$no</td>
                                        <td>$act[keterangan]</td>
                                        <td>$act[uraian]</td>
                                    </tr>
                                    ";
                                }   
                            }
                            else
                            {
                                $text.="
                                    <tr>
            							<td align=\"center\">-</td>
                                        <td align=\"center\">-</td>
                                        <td align=\"center\">-</td>
                                    </tr>
                                    ";
                            }
                            
                            $text.="
                        
                        </tbody>
                    
                    </table>
                
                </div>
                
            </div>
            
            
            
            
            
            
            
            
            <div class=\"widgetbox\">
    			<div class=\"title\" style=\"margin-bottom: 10px\"><h3>KESEPAKATAN ATIVITAS TINDAK LANJUT</h3></div>
                <a href=\"#\" onclick=\"openBox('popup.php?par[mode]=addAktivitas". getPar($par, "mode,id_project") . "',700,400);\" class=\"btn btn1 btn_document\" style=\"position:relative; float:right; right:0;  margin-top:-50px; \"><span>Tambah</span></a>
            
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">

					<thead>

						<tr>
							<th width=\"20\">No</th>							

							<th>AKTIFITAS PERIODE MENDATANG</th>
                            
                            <th>JADWAL</th>
                            
                            <th>HASIL YANG DIHARAPKAN</th>
                            
                            <th>KETERANGAN</th>
                            
                            <th>KONTROL</th>
                        </tr>
                    
                    </thead>
                    
                    <tbody>
                    ";
                    $getTind = queryAssoc("select * from pen_eva_tindakan where id_pegawai = $par[idPegawai] and id_pen_pegawai = $par[idPenPegawai]");
                    if($getTind)
                    {
                        $no=0;
                        foreach($getTind as $tind)
                        {
                            $no++;
                            $text.="
                            <tr>
    							<td align=\"center\">$no</td>
                                <td>$tind[aktifitas]</td>
                                <td>$tind[rencana]</td>
                                <td>$tind[target]</td>
                                <td>$tind[keterangan]</td>
                                ";
                                if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
                
        							$text.="<td align=\"center\" >";				
        
        							if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=addAktivitas&par[id_tindakan]=$tind[id_tindakan]".getPar($par,"mode,id_tindakan")."',700,400);\"><span>Edit</span></a>";
                                    
                                    if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=deleteTindakan&par[id_tindakan]=$tind[id_tindakan]".getPar($par,"mode,id_tindakan")."\" onclick=\"return confirm('Anda yakin ingin menghapus data?');\" title=\"Hapus Data\" class=\"delete\"><span>Hapus</span></a>";				
        
        							$text.="</td>";
        
        						}
                                $text.="
                            </tr>
                            ";
                        }
                    }
                    else
                    {
                        $text.="
                        <tr>
							<td align=\"center\">-</td>
                            <td align=\"center\">-</td>
                            <td align=\"center\">-</td>
                            <td align=\"center\">-</td>
                            <td align=\"center\">-</td>
                            <td align=\"center\">-</td>
                        </tr>
                        ";
                    }
                    
                    $text.="
                        
                    
                    </tbody>
                
                </table>
            
            </div>
            
            <br />
            
            ";
            $getCatatan = queryAssoc("select * from pen_eva_catatan where id_pegawai = $par[idPegawai] and id_pen_pegawai = $par[idPenPegawai]");
            $catatan = $getCatatan[0];
            
            $cttPegawai = empty($catatan['pegawai']) ? "-" : $catatan['pegawai'];
            $cttAtasan = empty($catatan['atasan']) ? "-" : $catatan['atasan'];
            $text.="
            <table width=\"100%\">

				<tr>

					<td width=\"48%\" style=\"varti-align:top\">
                        
                        <fieldset>
                            <legend>Catatan/Komentar dari Pegawai - <a style=\"color:red !important;\" href=\"#Edit\" title=\"Edit Data\"  onclick=\"openBox('popup.php?par[mode]=formCatatan&par[id_catatan]=$catatan[id_catatan]&par[tipe]=pegawai".getPar($par,"mode,id_catatan")."',700,220);\"><span class=\"edit\" >Edit</span></a></legend>
                            $cttPegawai
                        </fieldset>

					</td>
                    
                    <td width=\"4%\" style=\"varti-align:top\">&nbsp</td>
                    
                    <td width=\"48%\" style=\"varti-align:top\">
                        
                        <fieldset>
                            <legend>Catatan/Komentar dari Atasan - <a style=\"color:red !important;\" href=\"#Edit\" title=\"Edit Data\"  onclick=\"openBox('popup.php?par[mode]=formCatatan&par[id_catatan]=$catatan[id_catatan]&par[tipe]=atasan".getPar($par,"mode,id_catatan")."',700,220);\"><span class=\"edit\" >Edit</span></a></legend>
                            $cttAtasan
                        </fieldset

					</td>	

				</tr>

			</table>
            
            
            
            
             
        </form>
    </div>";
    return $text;
}



?>