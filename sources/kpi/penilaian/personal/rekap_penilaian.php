<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function getContent($par)
{
	global $s, $inp, $par, $_submit, $menuAccess;

	switch($par[mode])
	{   
		default:
		  $text = lihat();
		break;
        
        case "detail":
    		$text = detail();
    	break;
        
        case "xls":
    		$text = xls();
    	break;
        
	}
    
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
    $par[tipePenilaian] = $pen[tipePenilaian];
    
    
    echo "<div class=\"pageheader\">
    
            <h1 class=\"pagetitle\">".$arrTitle[$s]." xx</h1>
            
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
                        $bobot = getField("select bobotAspek from pen_setting_aspek where idAspek = '$aspek[idAspek]'");  
                        $nilai = getNilai($aspek[idAspek], $par, $bln[kodeData]);
                        $text.="
                        <td style=\"text-align:center;\">".getNilai($aspek[idAspek], $par, $bln[kodeData])."</td>
                        ";
                        $total = $total + ($nilai * ($bobot/100));
                    }
                    
                    $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = '$par[idPeriode]' and $total BETWEEN nilaiMin AND nilaiMax");
                    $konv = $getKonversi[0];
                    
                    $getMax = queryAssoc("SELECT MAX(nilaiMax),a.* FROM pen_setting_konversi AS a  where a.idPeriode = $par[idPeriode]");
                    $max = $getMax[0];
                    
                    $warna  = ($total >= $max['nilaiMax']) ? $max[warnaKonversi] : $konv[warnaKonversi];
                    
                    $text.="
                    <td style=\"text-align:center;\"><strong>$total</strong></td>
                    <td style=\"text-align:center;\"><div align=\"center\" style=\"background-color:".$warna.";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
                    <td style=\"text-align:center;\"><a href=\"?par[mode]=detail&par[idPenPegawai]=$par[idPenPegawai]&par[idPegawai]=$par[idPegawai]&par[tipePenilaian]=$par[tipePenilaian]&par[kodePenilaian]=$par[kodePenilaian]&par[tahunPenilaian]=$par[idPeriode]&par[bulanPenilaian]=$bln[kodeData]".getPar($par,"mode,idPenPegawai,idPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,bulanPenilaian,idPeriode")."\" title=\"Realisasi\" class=\"detail\"><span>Realisasi</span></a></td>
                </tr>
                ";
            }
            $text.="
            </tbody>
        </table>
        
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
            $par[bulanPenilaian] = empty($par[bulanPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by kodeData asc limit 1") : $par[bulanPenilaian];
            
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
                        			<div class=\"field\">".comboData("SELECT namaData,kodeData FROM mst_data WHERE kodeCategory = 'PRDB' AND kodeInduk ='$par[tahunPenilaian]' order by kodeData asc","kodeData","namaData","par[bulanPenilaian]","",$par['bulanPenilaian'],"onchange=\"document.getElementById('formFilter').submit();\"","210px;","chosen-select")."</div>
                        		</p>
                            </td>
                        <tr>
                    </table>
                </fieldset>
            </form>
            <br />
            <br />
            <div class=\"widgetbox\" style=\"position: relative;\">
                <div style=\"margin-bottom:-10px;\">
                    <h3>Detil Penilaian Bulan ".getField("SELECT namaData FROM mst_data WHERE kodeData = '$par[bulanPenilaian]'")."</h3>
                </div>
            </div>
            
            <div id=\"pos_r\" style=\"float:right; margin-top:-28px;\">
                <a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>
            </div>
            <hr>
            
            <br />
            ";
            $getAspek = queryAssoc("select * from pen_setting_aspek");
            if($getAspek)
            {
                foreach($getAspek as $aspek)
                {
                    $text.="
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" class=\"stdtable stdtablequick dataTable\">
                        <thead>
                            <tr>
                                <th style=\"vertical-align:middle;\">$aspek[namaAspek]</th>
                                <th style=\"vertical-align:middle;\">komponen KPI</th>
                                <th style=\"vertical-align:middle;\" rowspan=\"3\">Bobot</th>
                                <th style=\"vertical-align:middle;\" colspan=\"5\">Rating</th>
                                <th style=\"vertical-align:middle;\" rowspan=\"3\">Bobot</th>
                                <th style=\"vertical-align:middle;\" rowspan=\"3\">nilai terbobot (%)</th>
                                <th style=\"vertical-align:middle;\" rowspan=\"3\">nilai terbobot</th>
                            <tr>
                            <tr>
                                <th style=\"vertical-align:middle;\" rowspan=\"3\" colspan=\"2\">sasaran Kinerja KPI</th>
                                <th style=\"vertical-align:middle;\">1</th>
                                <th style=\"vertical-align:middle;\">2</th>
                                <th style=\"vertical-align:middle;\">3</th>
                                <th style=\"vertical-align:middle;\">4</th>
                                <th style=\"vertical-align:middle;\">5</th>
                            <tr>
                            <tr>
                                <th style=\"vertical-align:middle;\">100%</th>
                                <th style=\"vertical-align:middle;\" colspan=\"8\">100%</th>
                            <tr>
                        </thead>
                        <tbody>
                            ";
                            $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
                            $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = '$par[kodePenilaian]' and idAspek = '$aspek[idAspek]' and idTipe = '$idTipe' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");
                            if($getPerspektif)
                            {
                                foreach($getPerspektif as $pers)
                                {
                                    $getObj=queryAssoc("select 
                                                                * 
                                                                from pen_sasaran_obyektif 
                                                                where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
                                    
                                    if(!empty($getObj))
                                    {
                                        $text.="
                                        <tr>
                                            <td style=\"vertical-align:middle;\" rowspan=\"".(count($getObj)+1)."\"><strong>$pers[namaPrespektif]</strong></td>
                                        </tr>
                                        ";
                                    }
                                    
                                    foreach($getObj as $obj)
                                    {
                                        $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
                                        $rea = $realisasi[0];
                                        $nilai = empty($rea) ? 0 : $rea[nilai];
                                        
                                        $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
                                        $sas=$getSasaran[0];
                                        
                                        $nilai1 = $sas[bobotIndividu] .",0";
                                        $nilai2 = $sas[bobotIndividu] * $nilai;
                                        $nilai3 = $nilai2/100;
                                
                                        $text.="
                                            <tr>
                                                <td style=\"vertical-align:middle;\" >$obj[uraianSasaran]</td>
                                                <td align=\"center\">$sas[bobotIndividu]%</td>
                                                <td align=\"center\">".($nilai==1 ? "<strong>x</strong>" : "")."</td>
                                                <td align=\"center\">".($nilai==2 ? "<strong>x</strong>" : "")."</td>
                                                <td align=\"center\">".($nilai==3 ? "<strong>x</strong>" : "")."</td>
                                                <td align=\"center\">".($nilai==4 ? "<strong>x</strong>" : "")."</td>
                                                <td align=\"center\">".($nilai==5 ? "<strong>x</strong>" : "")."</td>
                                                <td align=\"center\">$nilai1</td>
                                                <td align=\"center\">$nilai2.0</td>
                                                <td align=\"center\">$nilai3</td>
                                            </tr>
                                            ";
                                            
                                         $t_bobot_1[$aspek[idAspek]] = $t_bobot_1[$aspek[idAspek]] + $sas[bobotIndividu];
                                         $t_bobot_3[$aspek[idAspek]][] = $nilai3;
                                    }
                                    
                                    
                                    
                                }
                                
                                $text.="
                                    <tr>
                                        <td align=\"center\" colspan=\"2\">&nbsp;</td>
                                        <td align=\"center\" ><strong>".$t_bobot_1[$aspek[idAspek]]."%</strong></td>
                                        <td align=\"center\" colspan=\"5\">&nbsp;</td>
                                        <td align=\"center\"><strong>".$t_bobot_1[$aspek[idAspek]]."%</strong></td>
                                        <td align=\"center\"></td>
                                        <td align=\"center\"><strong>".array_sum($t_bobot_3[$aspek[idAspek]])."</strong></td>
                                    </tr>
                                    ";
                                
                                //$yudisium = $yudisium + array_sum($t_bobot_3[$aspek[idAspek]]);
                            }
                            $text.="
                        </tbody>
                    </table>
                    
                    <br />
                    ";
                }
                
                $yudisium = getField("select nilai from pen_hasil WHERE id_periode = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_pegawai = $par[idPegawai]");
                $yudisium = empty($yudisium) ? 0 : $yudisium;
                
                $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = $pen[tahunPenilaian] and $yudisium BETWEEN nilaiMin AND nilaiMax");
                $konv = $getKonversi[0];
                
                $getMax = queryAssoc("SELECT MAX(nilaiMax),a.* FROM pen_setting_konversi AS a  where a.idPeriode = $pen[tahunPenilaian]");
                $max = $getMax[0];
                
                $warna  = ($yudisium >= $max['nilaiMax']) ? $max[warnaKonversi] : $konv[warnaKonversi];
                $uraian = ($yudisium >= $max['nilaiMax']) ? $max[uraianKonversi] : $konv[uraianKonversi];
                
                $text.="
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" class=\"stdtable stdtablequick dataTable\">
                    <tbody>
                        <tr>
                            <td style=\"vertical-align:middle;\" rowspan=\"4\" width=\"80%\" align=\"center\"><h3>YUDISIUM</h3></td>
                        </tr>
                        <tr>
                            <td style=\"vertical-align:middle;\" align=\"center\"><strong>NILAI KINERJA</strong></td>
                        </tr>
                        <tr>
                            <td style=\"vertical-align:middle; \" align=\"center\"><strong>$yudisium</strong></td>
                        </tr>
                        <tr>
                            <td style=\"vertical-align:middle; background-color:".$warna.";\" align=\"center\"><strong>".$uraian."</strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <br />
                
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" class=\"stdtable stdtablequick dataTable\">
                    <thead>
                        <tr>
                            <th>Klasifikasi</th>
                            <th>Score</th>
                            <th>predikat</th>
                            <th>WOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        ";
                        $getKonv = queryAssoc("select * from pen_setting_konversi where idPeriode = $pen[tahunPenilaian] order by idKonversi asc");
                        foreach($getKonv as $konv)
                        {
                            $text.="
                            <tr>
                                <td><strong>$konv[penjelasanKonversi]</strong></td>
                                <td align=\"center\"><strong>$konv[nilaiMin] - $konv[nilaiMax]</strong></td>
                                <td align=\"center\"><strong>$konv[uraianKonversi]</strong></td>
                                <td align=\"center\"><span style=\"background-color:$konv[warnaKonversi];\">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span></td>
                            </tr>
                            ";
                        }
                        $text.="
                    </tbody>
                </table>
                ";
            }
            $text.="
    </div>";
    
    if($_GET[export] == "xls"){			
    	xls();			
    	$text.="<iframe src=\"download.php?d=exp&f=penilaian_$par[idPegawai]".$par[idPeriode].".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }
    
    $text.="
    <script>
    	jQuery(\"#btnExport\").live('click', function(e){
    		e.preventDefault();
    		window.location.href=\"?par[mode]=detail&par[idPenPegawai]=$par[idPenPegawai]&par[idPegawai]=$par[idPegawai]&par[tipePenilaian]=$par[tipePenilaian]&par[kodePenilaian]=$par[kodePenilaian]&par[tahunPenilaian]=$par[tahunPenilaian]&par[bulanPenilaian]=$par[bulanPenilaian]&par[idPeriode]=$par[tahunPenilaian]\"+\"".getPar($par,"mode,idPenPegawai,idPegawai,tipePenilaian,kodePenilaian,tahunPenilaian,,bulanPenilaian,idPeriode")."\"+\"&export=xls\"
    	});
    </script>
    ";
    return $text;
}

function xls()
{
    global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess, $areaCheck, $arrParam;
    
    require_once 'plugins/PHPExcel.php';
    $objPHPExcel = new PHPExcel();				
	$objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);	
	
    $nama = getField("select name from emp where id = $par[idPegawai]");
    $title = "rekap ".getField("select namaData from mst_data where kodeData = $par[idPeriode]")." - ".getField("select namaData from mst_data where kodeData = $par[bulanPenilaian]")." : ".$nama;
    
	$objPHPExcel->getActiveSheet()->setCellValue('B2', strtoupper($title));
	$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('B2:L2')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->setCellValue('L2', date("d/m/Y H:i:s"));	
    $objPHPExcel->getActiveSheet()->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
	$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);
    
    $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(60);
    $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(60);
    $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension("K")->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension("L")->setWidth(25);
    
    $objPHPExcel->getActiveSheet()->setCellValue('B4', "BUSINESS PERFORMANCE");
    
    $objPHPExcel->getActiveSheet()->setCellValue('C4', "KOMPONEN KPI");
    
    $objPHPExcel->getActiveSheet()->setCellValue('D4', "BOBOT");
    $objPHPExcel->getActiveSheet()->mergeCells("D4:D5");
    
    $objPHPExcel->getActiveSheet()->setCellValue('E4', "RATING");
    $objPHPExcel->getActiveSheet()->mergeCells("E4:I4");
    
    $objPHPExcel->getActiveSheet()->setCellValue('J4', "BOBOT");
    $objPHPExcel->getActiveSheet()->mergeCells("J4:J5");
    
    $objPHPExcel->getActiveSheet()->setCellValue('K4', "NILAI TERBOBOT (%)");
    $objPHPExcel->getActiveSheet()->mergeCells("K4:K5");
    
    $objPHPExcel->getActiveSheet()->setCellValue('L4', "BOBOT TERBOBOT");
    $objPHPExcel->getActiveSheet()->mergeCells("L4:L5");
    
    
    $objPHPExcel->getActiveSheet()->getStyle('B4:L4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('E5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('F5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('G5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('H5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('C4:C6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('D4:D6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('I4:I6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('J4:J6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('K4:K6')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('L4:L5')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B4:L4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B5:L5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('B6:L6')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $objPHPExcel->getActiveSheet()->setCellValue('B5', "SASARAN KINERJA KPI");
    $objPHPExcel->getActiveSheet()->mergeCells("B5:C6");
    
    $objPHPExcel->getActiveSheet()->setCellValue('E5', "1");
    $objPHPExcel->getActiveSheet()->setCellValue('F5', "2");
    $objPHPExcel->getActiveSheet()->setCellValue('G5', "3");
    $objPHPExcel->getActiveSheet()->setCellValue('H5', "4");
    $objPHPExcel->getActiveSheet()->setCellValue('I5', "5");
    
    $objPHPExcel->getActiveSheet()->setCellValue('D6', "100%");
    $objPHPExcel->getActiveSheet()->setCellValue('E6', "100%");
    $objPHPExcel->getActiveSheet()->mergeCells("E6:L6");
    
    $objPHPExcel->getActiveSheet()->getStyle('B4:L6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B4:L6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('B4:L6')->getFont()->setBold(true);
    
    $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = '$par[kodePenilaian]'");
    $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = '$par[kodePenilaian]' and idAspek = 1 and idTipe = '$idTipe' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");
    if($getPerspektif)
    {
        $col = 7;
        foreach($getPerspektif as $pers)
        {
            $col++;
            $col=$col-1;
            $getObj=queryAssoc("select 
                                        * 
                                        from pen_sasaran_obyektif 
                                        where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
            
            
            
            if(!empty($getObj)) $objPHPExcel->getActiveSheet()->setCellValue("B$col", "$pers[namaPrespektif]");
            
            foreach($getObj as $obj)
            {   
                $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
                $rea = $realisasi[0];
                $nilai = empty($rea) ? 0 : $rea[nilai];
                
                $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
                $sas=$getSasaran[0];
                
                $nilai1 = $sas[bobotIndividu] ;
                $nilai2 = $sas[bobotIndividu] * $nilai;
                $nilai3 = $nilai2/100;
        
                $objPHPExcel->getActiveSheet()->setCellValue("C$col", "$obj[uraianSasaran]");
                $objPHPExcel->getActiveSheet()->setCellValue("D$col", "$sas[bobotIndividu]%");
                $objPHPExcel->getActiveSheet()->setCellValue("E$col", "".($nilai==1 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("F$col", "".($nilai==2 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("G$col", "".($nilai==3 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("H$col", "".($nilai==4 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("I$col", "".($nilai==5 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("J$col", "$nilai1%");
                $objPHPExcel->getActiveSheet()->setCellValue("K$col", "$nilai2");
                $objPHPExcel->getActiveSheet()->setCellValue("L$col", "$nilai3");
                    
                 $t_bobot_1[$aspek[idAspek]] = $t_bobot_1[$aspek[idAspek]] + $sas[bobotIndividu];
                 $t_bobot_3[$aspek[idAspek]][] = $nilai3;
                 $col++;
            }
            
            $objPHPExcel->getActiveSheet()->getStyle("D6:L$col")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            
          }
          
          $objPHPExcel->getActiveSheet()->setCellValue("D$col", "".$t_bobot_1[$aspek[idAspek]]."%");
          $objPHPExcel->getActiveSheet()->setCellValue("J$col", "".$t_bobot_1[$aspek[idAspek]]."%");
          $objPHPExcel->getActiveSheet()->setCellValue("L$col", "".array_sum($t_bobot_3[$aspek[idAspek]])."");
          
          $objPHPExcel->getActiveSheet()->getStyle("L6:K$col")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          $objPHPExcel->getActiveSheet()->getStyle("B".($col-1).":L".($col-1)."")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          $objPHPExcel->getActiveSheet()->getStyle("B$col:L$col")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          
        
          $yudisium = $yudisium + array_sum($t_bobot_3[$aspek[idAspek]]);
    }
    
    $objPHPExcel->getActiveSheet()->getStyle("B4:B$col")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    
    
    $col3 = $col+3;
    $col4 = $col3+1;
    $col5 = $col4+1;
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col3", "SOFT COMPETENCY");
    
    $objPHPExcel->getActiveSheet()->setCellValue("C$col3", "KOMPONEN KPI");
    
    $objPHPExcel->getActiveSheet()->setCellValue("D$col3", "BOBOT");
    $objPHPExcel->getActiveSheet()->mergeCells("D$col3:D$col4");
    
    $objPHPExcel->getActiveSheet()->setCellValue("E$col3", "RATING");
    $objPHPExcel->getActiveSheet()->mergeCells("E$col3:I$col3");
    
    $objPHPExcel->getActiveSheet()->setCellValue("J$col3", "BOBOT");
    $objPHPExcel->getActiveSheet()->mergeCells("J$col3:J$col4");
    
    $objPHPExcel->getActiveSheet()->setCellValue("K$col3", "NILAI TERBOBOT (%)");
    $objPHPExcel->getActiveSheet()->mergeCells("K$col3:K$col4");
    
    $objPHPExcel->getActiveSheet()->setCellValue("L$col3", "BOBOT TERBOBOT");
    $objPHPExcel->getActiveSheet()->mergeCells("L$col3:L$col4");

    $objPHPExcel->getActiveSheet()->getStyle("B$col3:L$col3")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col3")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("E$col4")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("F$col4")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("G$col4")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("H$col4")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("C$col3:C$col5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("D$col3:D$col5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("I$col3:I$col5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("J$col3:J$col5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("K$col3:K$col5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("L$col3:L$col5")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col3:L$col3")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col4:L$col4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col5:L$col5")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col4", "SASARAN KINERJA KPI");
    $objPHPExcel->getActiveSheet()->mergeCells("B$col4:C$col5");
    
    $objPHPExcel->getActiveSheet()->setCellValue("E$col4", "1");
    $objPHPExcel->getActiveSheet()->setCellValue("F$col4", "2");
    $objPHPExcel->getActiveSheet()->setCellValue("G$col4", "3");
    $objPHPExcel->getActiveSheet()->setCellValue("H$col4", "4");
    $objPHPExcel->getActiveSheet()->setCellValue("I$col4", "5");
    
    $objPHPExcel->getActiveSheet()->setCellValue("D$col5", "100%");
    $objPHPExcel->getActiveSheet()->setCellValue("E$col5", "100%");
    $objPHPExcel->getActiveSheet()->mergeCells("E$col5:L$col5");
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col3:L$col5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col3:L$col5")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col3:L$col5")->getFont()->setBold(true);
    
    $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
    $getPerspektif=queryAssoc("select * from pen_setting_prespektif where idKode = '$par[kodePenilaian]' and idAspek = 2 and idTipe = '$idTipe' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");
    if($getPerspektif)
    {
        $col = $col5+1;
        foreach($getPerspektif as $pers)
        {
            $col++;
            $col=$col-1;
            $getObj=queryAssoc("select 
                                        * 
                                        from pen_sasaran_obyektif 
                                        where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
            
            
            
            if(!empty($getObj)) $objPHPExcel->getActiveSheet()->setCellValue("B$col", "$pers[namaPrespektif]");
            
            foreach($getObj as $obj)
            {   
                $realisasi_a = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
                $rea_a = $realisasi_a[0];
                $nilai_a = empty($rea_a) ? 0 : $rea_a[nilai];
                
                $getSasaran_a = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
                $sas_a=$getSasaran_a[0];
                
                $nilai1_a = $sas_a[bobotIndividu] ;
                $nilai2_a = $sas_a[bobotIndividu] * $nilai_a;
                $nilai3_a = $nilai2_a/100;
        
                $objPHPExcel->getActiveSheet()->setCellValue("C$col", "$obj[uraianSasaran]");
                $objPHPExcel->getActiveSheet()->setCellValue("D$col", "$sas_a[bobotIndividu]%");
                $objPHPExcel->getActiveSheet()->setCellValue("E$col", "".($nilai_a==1 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("F$col", "".($nilai_a==2 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("G$col", "".($nilai_a==3 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("H$col", "".($nilai_a==4 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("I$col", "".($nilai_a==5 ? "x" : "")."");
                $objPHPExcel->getActiveSheet()->setCellValue("J$col", "$nilai1_a%");
                $objPHPExcel->getActiveSheet()->setCellValue("K$col", "$nilai2_a");
                $objPHPExcel->getActiveSheet()->setCellValue("L$col", "$nilai3_a");
                    
                 $t_bobot_1_a[$aspek[idAspek]] = $t_bobot_1_a[$aspek[idAspek]] + $sas_a[bobotIndividu];
                 $t_bobot_3_a[$aspek[idAspek]][] = $nilai3_a;
                 $col++;
            }
            
            $objPHPExcel->getActiveSheet()->getStyle("D5:L$col")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            
          }
          
          $objPHPExcel->getActiveSheet()->setCellValue("D$col", "".$t_bobot_1_a[$aspek[idAspek]]."%");
          $objPHPExcel->getActiveSheet()->setCellValue("J$col", "".$t_bobot_1_a[$aspek[idAspek]]."%");
          $objPHPExcel->getActiveSheet()->setCellValue("L$col", "".array_sum($t_bobot_3_a[$aspek[idAspek]])."");
          
          $objPHPExcel->getActiveSheet()->getStyle("L5:L$col")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          $objPHPExcel->getActiveSheet()->getStyle("B".($col-1).":L".($col-1)."")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
          $objPHPExcel->getActiveSheet()->getStyle("B$col:L$col")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        
          $yudisium = $yudisium + array_sum($t_bobot_3_a[$aspek[idAspek]]);
    }
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col3:B$col")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
    $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode = $par[idPeriode] and $yudisium BETWEEN nilaiMin AND nilaiMax");
    $konv = $getKonversi[0];
    $getMax = queryAssoc("SELECT MAX(nilaiMax),a.* FROM pen_setting_konversi AS a  where a.idPeriode = $par[idPeriode]");
    $max = $getMax[0];
    $uraian = ($yudisium >= $max['nilaiMax']) ? $max[uraianKonversi] : $konv[uraianKonversi];
    $wom = ($yudisium >= $max['nilaiMax']) ? $max[warnaKonversi] : $konv[warnaKonversi];
    $warna = str_replace("#", "", $wom);
    
    $col_y = $col + 3;
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_y:L$col_y")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("J$col_y:J".($col_y+2)."")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("L$col_y:L".($col_y+2)."")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_y:L".($col_y+2)."")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col_y", "YUDISIUM");
    $objPHPExcel->getActiveSheet()->mergeCells("B$col_y:J".($col_y+2)."");
    
    $objPHPExcel->getActiveSheet()->setCellValue("K$col_y", "NILAI KINERJA");
    $objPHPExcel->getActiveSheet()->mergeCells("K$col_y:L$col_y");
    
    
    
    $objPHPExcel->getActiveSheet()->setCellValue("K".($col_y+1)."", $yudisium);
    $objPHPExcel->getActiveSheet()->mergeCells("K".($col_y+1).":L".($col_y+1)."");
    
    $objPHPExcel->getActiveSheet()->setCellValue("K".($col_y+2)."", "$uraian");
    $objPHPExcel->getActiveSheet()->mergeCells("K".($col_y+2).":L".($col_y+2)."");
    $objPHPExcel->getActiveSheet()->getStyle("K".($col_y+2).":L".($col_y+2)."")->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => "$warna")
            )
        )
    );
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_y:L".($col_y+2)."")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_y:L".($col_y+2)."")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_y:L".($col_y+2)."")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_y:L".($col_y+2)."")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $col_w = $col_y+5;
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col_w", "KLASIFIKASI");
    $objPHPExcel->getActiveSheet()->mergeCells("B$col_w:C$col_w");
    
    $objPHPExcel->getActiveSheet()->setCellValue("D$col_w", "SCORE");
    $objPHPExcel->getActiveSheet()->mergeCells("D$col_w:E$col_w");
    
    $objPHPExcel->getActiveSheet()->setCellValue("F$col_w", "PREDIKAT");
    $objPHPExcel->getActiveSheet()->mergeCells("F$col_w:G$col_w");
    
    $objPHPExcel->getActiveSheet()->setCellValue("H$col_w", "WOM");
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_w:H$col_w")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_w:H$col_w")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_w:H$col_w")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_w:H$col_w")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_w")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("C$col_w")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("E$col_w")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("G$col_w")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("H$col_w")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $getKonv = queryAssoc("select * from pen_setting_konversi where idPeriode = $par[idPeriode] order by idKonversi asc");
    $col_s = $col_w;
    foreach($getKonv as $konv)
    {
        $col_w++;
        $objPHPExcel->getActiveSheet()->setCellValue("B$col_w", $konv[penjelasanKonversi]);
        $objPHPExcel->getActiveSheet()->mergeCells("B$col_w:C$col_w");
        
        $objPHPExcel->getActiveSheet()->setCellValue("D$col_w", "$konv[nilaiMin] - $konv[nilaiMax]");
        $objPHPExcel->getActiveSheet()->mergeCells("D$col_w:E$col_w");
        $objPHPExcel->getActiveSheet()->getStyle("D$col_w")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $objPHPExcel->getActiveSheet()->setCellValue("F$col_w", $konv[uraianKonversi]);
        $objPHPExcel->getActiveSheet()->mergeCells("F$col_w:G$col_w");
        $objPHPExcel->getActiveSheet()->getStyle("F$col_w")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        $warna = str_replace("#", "", $konv[warnaKonversi]);
        $objPHPExcel->getActiveSheet()->getStyle("H$col_w")->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => "$warna")
                )
            )
        );
    }
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_s:B$col_w")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("H$col_s:H$col_w")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_w:H$col_w")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $col_p = $col_w+3;
    $col_p1 = $col_p+5;
    
    $getPen = queryAssoc("select * from pen_pegawai where idPegawai = $par[idPegawai] and tipePenilaian = $par[tipePenilaian] and kodePenilaian = $par[kodePenilaian] and tahunPenilaian = $par[idPeriode] limit 1");
    $pen = $getPen[0];
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col_p", "PEGAWAI");
    $objPHPExcel->getActiveSheet()->setCellValue("C$col_p", "ATASAN LANGSUNG");
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col_p1", "( $nama )");
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->setCellValue("C$col_p1", "( ".getField("select name from emp where id = $pen[atasan_langsung]")." )");
    $objPHPExcel->getActiveSheet()->getStyle("C$col_p1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p:C$col_p")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p:C$col_p")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p:C$col_p")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p:C$col_p")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p:B$col_p1")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p:B$col_p1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("C$col_p:C$col_p1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_p1:C$col_p1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $col_a = $col_p1+3;
    $col_a1 = $col_a+5;
    
    $peg = queryAssoc("select * from dta_pegawai where id=$par[idPegawai] limit 1");
    $pgw = $peg[0];
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col_a", "PIMPINAN DIVISI");
    $objPHPExcel->getActiveSheet()->mergeCells("B$col_a:C$col_a");
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a:C$col_a")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a:C$col_a")->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a:C$col_a")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a:C$col_a")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a:B$col_a1")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("C$col_a:C$col_a1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a1:C$col_a1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
    $objPHPExcel->getActiveSheet()->setCellValue("B$col_a1", "( ".getField("select keteranganData from mst_data where kodeData = $pgw[div_id]")." )");
    $objPHPExcel->getActiveSheet()->mergeCells("B$col_a1:C$col_a1");
    $objPHPExcel->getActiveSheet()->getStyle("B$col_a1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
	$objPHPExcel->getActiveSheet()->setTitle("PENILAIAN");
	$objPHPExcel->setActiveSheetIndex(0);
	
	// Save Excel file
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save("files/export/penilaian_$par[idPegawai]".$par[idPeriode].".xls");
}

?>