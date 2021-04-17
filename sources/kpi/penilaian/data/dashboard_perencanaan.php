<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fPokok = "files/pokok/";

function lihat(){
	global $db,$c,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cUsername, $areaCheck;		
    
    $par[idPeriode] = empty($par[idPeriode]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc limit 1") : $par[idPeriode];

	$text.="
    <div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    	".getBread()."				
    </div>    
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
    
    	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
    		<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px; \">		
                ".comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"document.getElementById('form').submit();\"", "200px") ."
    		</div>				
        </form>	
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Jumlah Posisi per Divisi</h3></div>
    	</div>
        
        ";
        $warna = array("murky-green", "purple-orchid", "moon-raker", "camarone", "citrus", "allports", "light-blue");
        $getData = queryAssoc("SELECT * FROM pen_tipe WHERE statusTipe = 't' order by urutanTipe asc");
        $no = -1;
        foreach($getData as $data)
        {
            $no++;
            $text.="
            <a href=\"index.php?c=25&p=85&m=988&s=988&par[tipe]=$data[kodeTipe]\">
            <div class=\"dashboard-box $warna[$no]\" style=\"width:23%; float:left; margin-right:20px; margin-bottom:20px;\">
                <div class=\"dashboard-box-header\">
                    <p class=\"dashboard-box-title\">".substr($data[namaTipe],0,23)."</p>
                </div>
                <div class=\"dashboard-box-content\">
                    <p class=\"dashboard-box-number\"><font style=\"font-size:22pt;\">".getAngka(getField("select count(idKode) from pen_setting_kode where kodeTipe = '$data[kodeTipe]'"))."</font></p>
                </div>
            </div>
            </a>
            ";
        }
        $text.="
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom:0px;\"><h3>Monitoring Data</h3></div>
    	</div>
       
       ";
       $getAspek = queryAssoc("select * from pen_setting_aspek order by urutanAspek");
       $text.="
       <div style=\"overflow-x:scroll;\">
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">
    		<thead>
    			<tr>
    				<th width=\"20\"  style=\"vertical-align: middle\" rowspan=\"4\">NO</th>
    				<th width=\"300\" style=\"vertical-align: middle\" rowspan=\"4\">Tipe penilaian</th>
                    <th width=\"\" style=\"vertical-align: middle\" colspan=\"10\">Jumlah data setting</th>
    			</tr>
                <tr>
    				<th width=\"100\"  style=\"vertical-align: middle\" rowspan=\"3\">Posisi</th>
                    <th width=\"\"  style=\"vertical-align: middle\" colspan=\"4\">Sasaran obyektif</th>
                    <th width=\"\"  style=\"vertical-align: middle\" colspan=\"4\">Sasaran Individu</th>
    			</tr>
                <tr>
                    ";
                    foreach($getAspek as $asp) { $text.="<th style=\"vertical-align: middle\" colspan=\"2\">$asp[namaAspek]</th>"; }
                    foreach($getAspek as $asp) { $text.="<th style=\"vertical-align: middle\" colspan=\"2\">$asp[namaAspek]</th>"; }
                    $text.="
    			</tr>
                <tr>
                    ";
                    foreach($getAspek as $asp) 
                    { 
                        $text.="
                        <th style=\"vertical-align: middle\">Jml Posisi</th>
                        <th style=\"vertical-align: middle\">Jml SO</th>"; 
                    }
                    foreach($getAspek as $asp) 
                    { 
                        $text.="
                        <th style=\"vertical-align: middle\">Jml Posisi</th>
                        <th style=\"vertical-align: middle\">Jml SI</th>"; 
                    }
                    
                    $text.="
    			</tr>
    		</thead>
            <tbody>
                ";
                $no=0;
                foreach($getData as $data)
                {
                    $no++; 
                    $total1 = getField("select count(idKode) from pen_setting_kode where kodeTipe = '$data[kodeTipe]'"); 
                    $t1 +=  $total1;      
                    $text.="
                    <tr>
                        <td style=\"text-align:center;\">$no</td>
                        <td style=\"text-align:left;\">$data[namaTipe]</td>
                        <td style=\"text-align:center;\"><a href=\"index.php?c=25&p=85&m=988&s=988&par[tipe]=$data[kodeTipe]\">".getAngka($total1)."</a></td>
                        ";
                        foreach($getAspek as $asp) 
                        {
                            $getKode = queryAssoc("select a.*, (select count(*) from pen_sasaran_obyektif where idKode = a.idKode and idPeriode = $par[idPeriode]) as total from pen_setting_kode as a where a.kodeTipe = '$data[kodeTipe]'");
                            $tComp[$asp[idAspek]] = 0;
                            foreach($getKode as $kode)
                            {
                                $a = getField("select idSasaran from pen_sasaran_obyektif where idPrespektif in (select idPrespektif from pen_setting_prespektif where idAspek = '$asp[idAspek]' and idKode = $kode[idKode]) and idPeriode = $par[idPeriode]");
                                if($a) $tComp[$asp[idAspek]]++;
                            }
                            $bisnisTotal[$asp[idAspek]] = getField("SELECT COUNT(idSasaran) FROM pen_sasaran_obyektif AS a
                                            JOIN pen_setting_prespektif AS b ON (b.idPrespektif = a.idPrespektif)
                                            WHERE a.idPeriode = $par[idPeriode] and b.idTipe = $data[kodeTipe] AND b.idAspek = '$asp[idAspek]'");
                            
                            $text.="
                            <td style=\"text-align:center;\">".$tComp[$asp[idAspek]]."</td>
                            <td style=\"text-align:center;\">".$bisnisTotal[$asp[idAspek]]."</td>
                            ";
                        }
                        $t2 += $tComp[1];
                        $t3 += $bisnisTotal[1];
                        $t4 += $tComp[2];
                        $t5 += $bisnisTotal[2];
                        
                        $getIndividu = queryAssoc("SELECT 
                                      *,
                                      (select count(*) from pen_sasaran_individu where idPegawai = t1.idPegawai and idPeriode = $par[idPeriode]) as total  
                                    FROM
                                      pen_pegawai t1 
                                      JOIN dta_pegawai t2  ON (t1.idPegawai = t2.id)  
                                    where
                                      t1.tipePenilaian='".$data['kodeTipe']."'
                                     ");
                        $tInd = 0;
                        foreach($getIndividu as $idnv)
                        {
                            if($idnv[total]>0) $tInd++;
                        }
                        
                        foreach($getAspek as $asp) 
                        {
                            $bisnisTotal2[$asp[idAspek]] = getField("SELECT COUNT(a.idSasaran) FROM pen_sasaran_obyektif AS a
                                            JOIN pen_setting_prespektif AS b ON (b.idPrespektif = a.idPrespektif)
                                            JOIN pen_sasaran_individu as c ON (c.idSasaran = a.idSasaran)
                                            WHERE a.idPeriode = $par[idPeriode] and b.idTipe = $data[kodeTipe] AND b.idAspek = '$asp[idAspek]'");
                            $text.="
                            <td style=\"text-align:center;\">".$tInd."</td>
                            <td style=\"text-align:center;\">".getAngka($bisnisTotal2[$asp[idAspek]])."</td>
                            ";
                        }
                        
                        $t6 += $tInd;
                        $t7 += $bisnisTotal2[1];
                        $t8 = $t6;
                        $t9 += $bisnisTotal2[2];
                        $text.="
                    </tr>
                    ";
                }
                $text.="
            </tbody>
            <tfoot>
                <tr>
                    <td style=\"text-align:right;\" colspan=\"2\"><strong>Total</strong></td>
                    <td style=\"text-align:center;\">$t1</td>
                    <td style=\"text-align:center;\">$t2</td>
                    <td style=\"text-align:center;\">$t3</td>
                    <td style=\"text-align:center;\">$t4</td>
                    <td style=\"text-align:center;\">$t5</td>
                    <td style=\"text-align:center;\">$t6</td>
                    <td style=\"text-align:center;\">$t7</td>
                    <td style=\"text-align:center;\">$t8</td>
                    <td style=\"text-align:center;\">$t9</td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
    ";

	return $text;
}	

function getContent($par){
	global $db,$s,$_submit,$menuAccess;		
	switch($par[mode]){				
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>