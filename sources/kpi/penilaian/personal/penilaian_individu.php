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
        
	}
	return $text;
}

function lihat()
{
    global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$dirKtp,$dirUsaha,$cIdPegawai;
    
    $par[idPegawai] = empty($_SESSION['idPegawai']) ? $cIdPegawai : $_SESSION['idPegawai'];
    
    $par[tahunPenilaian] = $par[idPeriode] = getField("SELECT kodeData FROM mst_data WHERE kodeCategory = 'PRDT' LIMIT 1");
    $getPen = queryAssoc("select * from pen_pegawai where tahunPenilaian = $par[tahunPenilaian] and idPegawai = $par[idPegawai]");
    $pen = $getPen[0];
    $par[idPenPegawai] = $pen[id];
    $par[kodePenilaian] = $pen[kodePenilaian];
    
    
    
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
                            
                            <th style=\"vertical-align:middle;\">Nilai</th>
                            
                            <th style=\"vertical-align:middle;\">File</th>
                            
                            <th style=\"vertical-align:middle;\">WOM</th>";

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
                                
                                $filedWarna = "wom_".$nilai;
                                $warna = $obj[$filedWarna];
                                
                                $text.="
                                <tr>
                                    <td align=\"center\">$no</td>

    								<td>$obj[uraianSasaran] </td>					
    
    								<td align=\"left\">$obj[targetSasaran] $obj[targetSasaran2]</td>
    
    								<td align=\"left\">$sas[satuanIndividu] $rea[realisasi] $sas[satuanIndividu2]</td>
                                    
                                    <td align=\"center\">".(($nilai==0)?" ":$nilai)."</td>
                                    
                                    <td align=\"center\">$FileTarget</td>
                                    
                                    <td align=\"center\">".( (empty($rea[realisasi]) or preg_match("/ /i", $rea[realisasi]))? " ": "<span style=\"background-color:".$warna.";\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>")." </td>
                                    
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