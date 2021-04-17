<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$dFile = "files/kinerja_individu/";

function getContent($par)
{
	global $s, $inp, $par, $_submit, $menuAccess;

	switch($par[mode])
	{   
	    case "detail":
            $text = detail(); 
		break;
        
        case "edit":
			if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : simpan(); else $text = lihat();
		break;
        
        case "delFile":
            if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
        break;
        
		default:
		  $text = lihat();
		break;
        
	}
	return $text;
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
    
    $nilai = getNilaiSasaran($par[id_sasaran], $inp[realisasi]);
    
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
                                        tanggal_realisasi,
                                        nilai,
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
                                        
                                        '".setTanggal($inp[tanggal_realisasi])."',
                                        '$nilai',
                                        
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
                                      nilai = '$nilai',
                                      tanggal_realisasi = '".setTanggal($inp[tanggal_realisasi])."',
                                      keterangan = '$inp[keterangan]',
                                      $updateFile
                                      update_by = '$cID',
                                      update_date = '".date("Y-m-d H:i:s")."'
                                where 
                                      id_realisasi = $par[id_realisasi]
        ");
    }
    
    
    /////////////////////////////////////////////// insert hasil /////////////////////////////////////////////////////////
    
    $getAspek = queryAssoc("select * from pen_setting_aspek");
    if($getAspek)
    {
        foreach($getAspek as $aspek)
        {
            $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
            $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = '$par[kodePenilaian]' and idAspek = '$aspek[idAspek]' and idTipe = $idTipe order by urut asc");
            if($getPerspektif)
            {
                foreach($getPerspektif as $pers)
                {
                    $getObj=queryAssoc("select 
                                                * 
                                                from pen_sasaran_obyektif 
                                                where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
                    
                    /////////////////////////////////////////////// insert hasil detail /////////////////////////////////////////////////////////
                    
                    $totalObj = getField("SELECT COUNT(idIndividu) FROM pen_sasaran_individu WHERE idPegawai = $par[idPegawai] AND idPeriode = $par[tahunPenilaian] AND idSasaran IN (SELECT idSasaran FROM pen_sasaran_obyektif WHERE idPrespektif = $pers[idPrespektif])");
                    $getNilaiPrespektif = getField("SELECT ROUND( SUM(nilai)/$totalObj , 2 ) FROM pen_realisasi_individu WHERE id_pegawai = $par[idPegawai] AND id_tahun = $par[tahunPenilaian] AND id_bulan = $par[bulanPenilaian] AND id_sasaran IN (SELECT idSasaran FROM pen_sasaran_obyektif WHERE idPrespektif = $pers[idPrespektif])");
                    $nilaiPrespektif = (!empty($getNilaiPrespektif)) ? $getNilaiPrespektif : 0 ;
                    
                    db("delete from pen_hasil_detail where id_pegawai = $par[idPegawai] and id_periode = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_prespektif = $pers[idPrespektif]");
                    $id_hasil = getLastId('pen_hasil_detail', 'id_hasil');
                    
                    db("insert into pen_hasil_detail 
                                           (id_hasil,
                                            id_prespektif,
                                            id_periode,
                                            id_bulan,
                                            id_pegawai,
                                            nilai) 
                                    values 
                                            ($id_hasil,
                                            '$pers[idPrespektif]',
                                            '$par[tahunPenilaian]',
                                            '$par[bulanPenilaian]',
                                            '$par[idPegawai]',
                                            '$nilaiPrespektif')");
                    
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    
                    //foreach($getObj as $obj)
//                    {
//                        $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
//                        $rea = $realisasi[0];
//                        $nilai = empty($rea) ? 0 : $rea[nilai];
//                        
//                        $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
//                        $sas=$getSasaran[0];
//                        
//                        $nilai1 = $sas[bobotIndividu] .",0";
//                        $nilai2 = $sas[bobotIndividu] * $nilai;
//                        $nilai3 = $nilai2/100;
//                        
//                        $t_bobot_1[$aspek[idAspek]] = $t_bobot_1[$aspek[idAspek]] + $sas[bobotIndividu];
//                        $t_bobot_3[$aspek[idAspek]][] = $nilai3;
//                    }
                    
                }
                
                //$yudisium = $yudisium + array_sum($t_bobot_3[$aspek[idAspek]]);
            }
        }
    }
    
    //$y1 = getField("SELECT IFNULL ( ROUND( AVG(nilai),1) ,0) FROM pen_hasil_detail AS a
//                    JOIN pen_setting_prespektif AS b ON (b.idPrespektif = a.id_prespektif) 
//                    WHERE id_pegawai = $par[idPegawai] AND id_periode = $par[tahunPenilaian] AND id_bulan = $par[bulanPenilaian] and nilai != '0' and idAspek = '1'");
//    $y2 = getField("SELECT IFNULL ( ROUND( AVG(nilai),1) ,0) FROM pen_hasil_detail AS a
//                    JOIN pen_setting_prespektif AS b ON (b.idPrespektif = a.id_prespektif) 
//                    WHERE id_pegawai = $par[idPegawai] AND id_periode = $par[tahunPenilaian] AND id_bulan = $par[bulanPenilaian] and nilai != '0' and idAspek = '2'");
//    
    $y1 = getNilai(1, $par, $par[bulanPenilaian]);
    $y2 = getNilai(2, $par, $par[bulanPenilaian]);
    
    $b1 = getField("select bobotAspek from pen_setting_aspek where idAspek = '1'");     
    $b2 = getField("select bobotAspek from pen_setting_aspek where idAspek = '2'");                
                    
    $yudisium = ($y1 * ($b1/100)) + ($y2 * ($b2/100));
    $yudisium = round($yudisium,2);
    
    $idHasil = getField("select id_hasil from pen_hasil where id_periode = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_pegawai = $par[idPegawai]");
    if(empty($idHasil))
    {
        $id_hasil = getLastId('pen_hasil', 'id_hasil');
        db("insert into pen_hasil 
                               (id_hasil,
                                id_periode,
                                id_bulan,
                                id_pegawai,
                                nilai,
                                create_by, 
                                create_date) 
                        values 
                                ($id_hasil,
                                '$par[tahunPenilaian]',
                                '$par[bulanPenilaian]',
                                '$par[idPegawai]',
                                '$yudisium',
                                '$cID', 
                                '".date("Y-m-d H:i:s")."')");
    }
    else
    {
        db("
            update pen_hasil set 
                              nilai = '$yudisium'
                        where 
                              id_hasil = $idHasil
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
			
                <legend>Sasaran Obyektif</legend>

				<table width=\"100%\">

    				<tr>
    
    					<td width=\"100%\" style=\"varti-align:top\">
    
                            
                            
    						<p>
    
    							<label class=\"l-input-small\">Sasaran</label>
    
    							<span class=\"field\">
    
    								$sas[uraianSasaran]&nbsp;
    
    							</span>
    
    						</p>
                            
                            <p>
    
    							<label class=\"l-input-small\">Pencapaian Target</label>
    
    							<span class=\"field\">
    
    								$sas[targetSasaran] 
                                    $sas[targetSasaran2]
                                    &nbsp;
    							</span>
    
    						</p>
                            
                            <p>
    
    							<label class=\"l-input-small\">Keterangan</label>
    
    							<span class=\"field\">
    
    								$sas[keteranganSasaran]
                                    &nbsp;
    							</span>
    
    						</p>	
                            
                            <p>
    
    							<label class=\"l-input-small\">Scoring</label>
    
    							<span class=\"field\">
    
    								$sas[scoringSasaran]
                                    &nbsp;
    							</span>
    
    						</p>
                            
                            <p>
    
    							<label class=\"l-input-small\">Measurement</label>
    
    							<span class=\"field\">
    
    								$sas[measurementSasaran]
                                    &nbsp;
    							</span>
    
    						</p>					
    
    					</td>
    
    				</tr>

				</table>

			</fieldset>	
            
            
            <br />
            
            
            <fieldset style=\"padding:10px; border-radius: 10px;\">						
			
                <legend>Sasaran Individu</legend>

				<table width=\"100%\">

    				<tr>
    
    					<td width=\"100%\" style=\"varti-align:top\">
                        
                            <p>
    
    							<label class=\"l-input-small\">Penilaian</label>
    
    							<span class=\"field\">
    
    								".getField("select namaData from mst_data where kodeData = $par[tahunPenilaian]")." <strong>&nbsp;&nbsp;&nbsp;>&nbsp;&nbsp;&nbsp;</strong> ".getField("select namaData from mst_data where kodeData = $par[bulanPenilaian]")."
    
    							</span>
    
    						</p>   
                            
                            ";
                            $cek = queryAssoc("select * from pen_sasaran_individu where idIndividu = $par[idIndividu]");
                            $cekBreak = $cek[0][settingIndividu];
                            if($cekBreak == "t")
                            {
                                $nilai = getField("select nilai from pen_sasaran_individu_breakdown where idIndividu = $par[idIndividu] and kode = $par[bulanPenilaian]");
                                $text.="
                                <p>
    
        							<label class=\"l-input-small\">Target</label>
                                    
        							<span class=\"field\">
        
        								".getAngka($nilai)."
        
        							</span>
        
        						</p>
                                ";
                            }
                            $text.="
    
    						<p>
    
    							<label class=\"l-input-small\">Realisasi</label>
    
    							<div class=\"field\">          
                                    ".$cek[0][targetIndividu]." <input type=\"text\" id=\"inp[realisasi]\" name=\"inp[realisasi]\"  value=\"$r[realisasi]\" class=\"mediuminput\" style=\"width:70px;\"/> ".$cek[0][keteranganTargetIndividu]."
                                </div>
    
    						</p>
                            
                            <p>
    
    							<label class=\"l-input-small\">Tanggal Realisasi</label>
    
    							<div class=\"field\">          
                                    <input type=\"text\" id=\"inp[tanggal_realisasi]\" name=\"inp[tanggal_realisasi]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r['tanggal_realisasi'])."\" class=\"vsmallinput hasDatePicker\"/>
                                </div>
    
    						</p>
                            
                            <p>
                                <label class=\"l-input-small\">Bukti Pencapaian </label>
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

				<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,bulanPenilaian,idPenPegawai")."';\"/>

			</div>
            
            ";
            
            $par[bulanPenilaian] = empty($par[bulanPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by kodeData asc limit 1") : $par[bulanPenilaian];
            
            $getPen = queryAssoc("select * from pen_pegawai where id='$par[idPenPegawai]'");
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
                    			<span class=\"field\">".getField("select namaData from mst_data where kodeData = $par[tahunPenilaian]")."</span>
                    		</p>
                            <p>
                    			<label style=\"width:150px\" class=\"l-input-medium\">PERIODE PENILAIAN</label>
                    			<span class=\"field\">".getTanggal($pen[periodeStart])." s/d ".getTanggal($pen[periodeEnd])."</span>
                    		</p>
                        </td>
                        <td style=\"width: 50%;\">
                            <p>
                    			<label style=\"width:150px\" class=\"l-input-medium\">PERIODE REALISASI</label>
                    			<div class=\"field\">".comboData("SELECT namaData,kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by kodeData asc","kodeData","namaData","par[bulanPenilaian]","",$par['bulanPenilaian'],"onchange=\"document.getElementById('formFilter').submit();\"","210px;","chosen-select")."</div>
                    		</p>
                        </td>
                    <tr>
                </table>
            </fieldset>
            </form>
            <br />
            
            ";
        
        $par[idPenilaian] = $par[kodePenilaian];
        
        $sql="select * from pen_setting_aspek ";

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

							<th width=\"20\" style=\"vertical-align:middle;\">No</th>							

							<th style=\"vertical-align:middle;\">Komponen kpi</th>		

							<th style=\"vertical-align:middle;\">Target<br />Sasaran</th>
                            
                            <th style=\"vertical-align:middle;\">Realisasi</th>
                            
                            <th style=\"vertical-align:middle;\">Rating</th>
                            
                            <th style=\"vertical-align:middle;\">File</th>
                            
                            <th style=\"vertical-align:middle;\">WOM</th>";

					if(isset($menuAccess[$s]["edit"])) $text.="<th width=\"50\" style=\"vertical-align:middle;\">Kontrol</th>";

					$text.="</tr>

					</thead>
                    
                    <tbody>
                    ";
                    $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
                    $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = '$par[kodePenilaian]' and idAspek = '$idAspek' and idTipe = '$idTipe' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");
                    foreach($getPerspektif as $prs)
                    {
                        $text.="
                        <tr style=\"background-color:#d9d9d9;\">
                            <td colspan=8><strong>".strtoupper($prs[namaPrespektif])."</strong></td>
                        </tr>
                        ";
                        $getIndikator=queryAssoc("select * from pen_setting_prespektif_indikator where idPrespektif=$prs[idPrespektif] order by urutanIndikator asc");
                        $noInd=0;
                        foreach($getIndikator as $ind)
                        {
                            $noInd++;
                            $text.="
                            <tr style=\"background-color:#f2f2f2;\">
                                <td colspan=8 style=\"font-size:8pt;\"><strong>$noInd. ".strtoupper($ind[uraianIndikator])."</strong></td>
                            </tr>
                            ";
                            $getObj=queryAssoc("select * from pen_sasaran_obyektif where idPrespektif = $prs[idPrespektif] and idIndikator = $ind[kodeIndikator] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
                            $no=0;
                            foreach($getObj as $obj)
                            {
                                $no++;
                                
                                $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
                                $rea = $realisasi[0];
                                $FileTarget = empty($rea[file_upload]) ? " " : "<a href=\"#Preview\" title=\"Preview File\" onclick=\"openBox('view.php?&par[tipe]=file_realisasi_individu&par[id_realisasi]=$rea[id_realisasi]',900,500);\"><img style=\" height:20px;\" src=\"".getIcon($rea[file_upload])."\"></a>";
                                $nilai = empty($rea) ? 0 : $rea[nilai];
                                $nilai = empty($nilai) ? 0 : $nilai;
                                
                                $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = $par[tahunPenilaian] and $nilai BETWEEN nilaiMin AND nilaiMax");
                                $konv = $getKonversi[0];
                                
                                $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPeriode = $par[tahunPenilaian] and idPegawai = $par[idPegawai]");
                                $sas=$getSasaran[0];
                                
                                if($rea[realisasi] >= $obj[nilaiSasaran_1a] and $rea[realisasi] <= $obj[nilaiSasaran_1b]) $warna = $obj[wom_5];
                                if($rea[realisasi] >= $obj[nilaiSasaran_2a] and $rea[realisasi] <= $obj[nilaiSasaran_2b]) $warna = $obj[wom_4];
                                if($rea[realisasi] >= $obj[nilaiSasaran_3a] and $rea[realisasi] <= $obj[nilaiSasaran_3b]) $warna = $obj[wom_3];
                                if($rea[realisasi] >= $obj[nilaiSasaran_4a] and $rea[realisasi] <= $obj[nilaiSasaran_4b]) $warna = $obj[wom_2];
                                if($rea[realisasi] >= $obj[nilaiSasaran_5a] and $rea[realisasi] <= $obj[nilaiSasaran_5b]) $warna = $obj[wom_1];
                                
                                $text.="
                                <tr>
                                    <td align=\"center\">$no</td>

    								<td>$obj[uraianSasaran] </td>					
    
    								<td align=\"left\">$obj[targetSasaran] $obj[targetSasaran2]</td>
    
    								<td align=\"left\">".(empty($rea[realisasi]) ? "" : "$sas[satuanIndividu] $rea[realisasi] $sas[satuanIndividu2]")."</td>
                                    
                                    <td align=\"center\">".(($nilai==0)?" ":$nilai)."</td>
                                    
                                    <td align=\"center\">$FileTarget</td>
                                    
                                    <td align=\"center\">".( $rea[realisasi] == "" ? " ": "<span style=\"background-color:".$warna.";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>")." </td>
                                    
                                    ";
                                    if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
                    
            							$text.="<td align=\"center\" >";				
            
            							if(isset($menuAccess[$s]["edit"])) $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[id_sasaran]=$obj[idSasaran]&par[id_realisasi]=$rea[id_realisasi]&par[idIndividu]=$sas[idIndividu]".getPar($par,"mode,id_realisasi,idIndividu,idSasaran")."',800,600);\"><span>Edit</span></a>";				
            
            							$text.="</td>";
            
            						}
                                    $text.="
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

function lihat()
{
    global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$dirKtp,$dirUsaha,$cIdPegawai;
    
    $par[idPegawai] = empty($_SESSION['idPegawai']) ? $cIdPegawai : $_SESSION['idPegawai'];
    
    $par[tahunPenilaian] = getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDT' LIMIT 1");
    $par[idPeriode] = empty($par[idPeriode]) ? $par[tahunPenilaian] : $par[idPeriode];
    $getPen = queryAssoc("select * from pen_pegawai where tahunPenilaian = $par[tahunPenilaian] and idPegawai = $par[idPegawai]");
    $pen = $getPen[0];
    
    $par[idPenPegawai] = $pen[id];
    $par[kodePenilaian] = $pen[kodePenilaian];
    
    //debugVar($par);die;
    
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
    
        <div style=\"position:absolute; top:0px; right:0px; margin-top:10px; margin-right:20px;\"></div>
        
        ";
        
        $par[bulanPenilaian] = empty($par[bulanPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by kodeData asc limit 1") : $par[bulanPenilaian];
        
        $getPen = queryAssoc("select * from pen_pegawai where id='$par[idPenPegawai]'");
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
            
            
            <div class=\"widgetbox\">
        		<div class=\"title\" style=\"margin-bottom: 10px\"><h3>HASIL PENILAIAN</h3></div>
                <div style=\"float:right; margin-top:-50px; margin-right:10px;\">".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc","kodeData","namaData","par[idPeriode]","",$par['idPeriode'],"onchange=\"document.getElementById('formFilter').submit();\"","210px;","chosen-select")."</div>
            </div>
        </form>
        
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">
    		<thead>
    			<tr>
    				<th width=\"20\"  style=\"vertical-align: middle\">NO</th>
    				<th width=\"200\" style=\"vertical-align: middle\">BULAN</th>
                    ";
                    $getAspek = queryAssoc("select * from pen_setting_aspek");
                    foreach($getAspek as $aspek)
                    {
                        $text.="
                        <th  style=\"vertical-align: middle\">$aspek[namaAspek]</th>
                        ";
                    }
                    $text.="
                    
    				<th width=\"100\" style=\"vertical-align: middle\">NILAI</th>
    				<th width=\"100\" style=\"vertical-align: middle\">WOM</th>	
                    <th width=\"70\" style=\"vertical-align: middle\">Kontrol</th>	
    			</tr>
    		</thead>
            <tbody>
            ";
            $getBulan = queryAssoc("SELECT * FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk = $par[idPeriode]");
            $no=0;
            foreach($getBulan as $bln)
            {
                $no++;
                $text.="
                <tr>
                    <td style=\"text-align:center;\">$no</td>
                    <td style=\"text-align:left;\">$bln[namaData]</td>
                    ";
                    $total = 0;
                    foreach($getAspek as $aspek)
                    {
                        $text.="
                        <td style=\"text-align:center;\">".getNilai($aspek[idAspek], $par, $bln[kodeData])."</td>
                        ";
                        $total = $total + getNilai($aspek[idAspek], $par, $bln[kodeData]);
                    }
                    
                    $yudisium = getField("select nilai from pen_hasil WHERE id_periode = $par[idPeriode] and id_bulan = $bln[kodeData] and id_pegawai = $par[idPegawai]");
                    $yudisium = empty($yudisium) ? 0 : $yudisium;
                    
                    $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = $par[idPeriode] and $yudisium BETWEEN nilaiMin AND nilaiMax");
                    $konv = $getKonversi[0];
                    
                    $getMax = queryAssoc("SELECT MAX(nilaiMax),a.* FROM pen_setting_konversi AS a  where a.idPeriode = $par[idPeriode]");
                    $max = $getMax[0];
                    
                    $warna  = ($yudisium >= $max['nilaiMax']) ? $max[warnaKonversi] : $konv[warnaKonversi];
                    $uraian  = ($yudisium >= $max['nilaiMax']) ? $max[uraianKonversi] : $konv[uraianKonversi];
                    
                    $text.="
                    <td style=\"text-align:center;\"><strong>$yudisium</strong></td>
                    <td style=\"text-align:center;\"><div align=\"center\" style=\"background-color:".$warna.";\">&nbsp;&nbsp;&nbsp; <strong>$uraian</strong> &nbsp;&nbsp;&nbsp;</div></td>
                    <td style=\"text-align:center;\"><a href=\"?par[mode]=detail&par[idPenPegawai]=$par[idPenPegawai]&par[idPegawai]=$par[idPegawai]&par[tipePenilaian]=$par[idPenPegawai]&par[kodePenilaian]=$par[kodePenilaian]&par[tahunPenilaian]=$par[idPeriode]&par[bulanPenilaian]=$bln[kodeData]".getPar($par,"mode,idPenPegawai,idPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,bulanPenilaian,idPeriode")."\" title=\"Realisasi\" class=\"edit\"><span>Realisasi</span></a></td>
                </tr>
                ";
            }
            $text.="
            </tbody>
        </table>
        
    </div>";
    return $text;
}

/*
function getNilai($idAspek, $par, $bulanPenilaian)
{
    $getAspek = queryAssoc("select * from pen_setting_aspek where idAspek = $idAspek");
    if($getAspek)
    {
        foreach($getAspek as $aspek)
        {
            
            $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
            $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = $par[kodePenilaian] and idAspek = $aspek[idAspek] and idTipe = $idTipe order by urut asc");
            if($getPerspektif)
            {
                foreach($getPerspektif as $pers)
                {
                    $getObj=queryAssoc("select 
                                                * 
                                                from pen_sasaran_obyektif 
                                                where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
                    
                    
                    
                    foreach($getObj as $obj)
                    {
                        $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $bulanPenilaian and id_sasaran = $obj[idSasaran]");
                        $rea = $realisasi[0];
                        $nilai = empty($rea) ? 0 : $rea[nilai];
                        
                        $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
                        $sas=$getSasaran[0];
                        
                        $nilai1 = $sas[bobotIndividu] .",0";
                        $nilai2 = $sas[bobotIndividu] * $nilai;
                        $nilai3 = $nilai2/100;
                            
                         $t_bobot_1[$aspek[idAspek]] = $t_bobot_1[$aspek[idAspek]] + $sas[bobotIndividu];
                         $t_bobot_3[$aspek[idAspek]][] = $nilai3;
                    }
                    
                    
                    
                }
                
                $yudisium = $yudisium + array_sum($t_bobot_3[$aspek[idAspek]]);
            }
        }
    }
    
    return $yudisium;
}
*/
?>