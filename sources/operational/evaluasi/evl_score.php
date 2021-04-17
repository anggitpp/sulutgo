<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";			

function lihat(){
	global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;
	if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');

	$arrKategori = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[50]."' order by urutanData");

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."
</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<div style=\"padding-bottom:10px;\">
	</div>
	<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
		<input type=\"hidden\" name=\"_submit\" value=\"t\">
		<div style=\"position:absolute; top:0; right:0; margin-top:10px; margin-right:20px;\">Periode : ".comboYear("par[tahunPelatihan]", $par[tahunPelatihan], "", "onchange=\"document.getElementById('form').submit();\"","", "All")."</div>
		<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\"/></td>
					<td>".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[43]."' order by namaData","kodeData","namaData","par[idKategori]","All",$par[idKategori],"","200px","chosen-select")."</td>
					<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
		</div>				
	</form>
	<br clear=\"all\" />
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
		<thead>
			<tr>
				<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"20\">No.</th>					
				<th rowspan=\"2\" style=\"vertical-align:middle\">Pelatihan</th>					
				<th colspan=\"".(count($arrKategori) + 1)."\" style=\"vertical-align:middle;\">Nilai</th>					
				<th rowspan=\"2\" style=\"vertical-align:middle;\"  width=\"50\">View</th>
			</tr>
			<tr>";
				if(is_array($arrKategori)){				
					reset($arrKategori);
					while(list($idKategori,$namaKategori)=each($arrKategori)){
						$text.="<th style=\"vertical-align:middle;\" width=\"100\">".$namaKategori."</th>";
					}
				}
				$text.="<th style=\"vertical-align:middle;\" width=\"100\">Akhir</th>
			</tr>
		</thead>
		<tbody>";

			$filter = "where t1.idPelatihan is not null and t1.statusPelatihan='t'";
			if(!empty($par[tahunPelatihan]))
				$filter.= " and ".$par[tahunPelatihan]." between year(t1.mulaiPelatihan) and year(t1.selesaiPelatihan)";

			if(!empty($par[idKategori]))
				$filter.=" and t1.idKategori='".$par[idKategori]."'";

			if(!empty($par[filter]))		
				$filter.= " and (
			lower(t1.judulPelatihan) like '%".strtolower($par[filter])."%'
			)";

			$dtaKategori = arrayQuery("select t1.idPelatihan, t3.idKategori, t3.idKategori from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi) $filter order by t1.idPelatihan");
			$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) $filter order by t1.idPelatihan");

			$sql="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) $filter order by t1.idPelatihan";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrJawaban = explode("\t", $r[evaluasiJawaban]);
				$nilaiPelatihan = 0;
				if(is_array($arrJawaban)){
					reset($arrJawaban);
					while(list($i,$d)=each($arrJawaban)){
						$idJawaban = isset($dtaJawaban["$r[idPertanyaan]"][$d]) ? $d : 0;
						$bobotJawaban = $dtaJawaban["$r[idPertanyaan]"][$idJawaban];
						$nilaiPelatihan+= $bobotJawaban * 100 / count($dtaJawaban["$r[idPertanyaan]"]);
					}
				}			
				$arrNilai["$r[idPelatihan]"]["$r[idKategori]"]+= $nilaiPelatihan;
				$cntKategori["$r[idPelatihan]"]["$r[idKategori]"]=$r[idKategori];
			}	

			$sql="select * from plt_pelatihan t1 $filter order by t1.idPelatihan";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){					
				$no++;						
				$cntEvaluasi = count(arrayQuery("select idPegawai from plt_pertanyaan_evaluasi where idPelatihan='$r[idPelatihan]' group by idPegawai"));
				$text.="<tr>
				<td align=\"center\">$no.</td>			
				<td>$r[judulPelatihan]</td>";				
				$totalPelatihan = 0;
				if(is_array($arrKategori)){				
					reset($arrKategori);
					while(list($idKategori,$namaKategori)=each($arrKategori)){
						$nilaiPelatihan = in_array($idKategori, $dtaKategori["$r[idPelatihan]"]) ? round($arrNilai["$r[idPelatihan]"][$idKategori] / $cntEvaluasi / count($cntKategori["$r[idPelatihan]"])) : "-";
						$text.="<td align=\"center\">".$nilaiPelatihan."</td>";
						if(in_array($idKategori, $dtaKategori["$r[idPelatihan]"]))
							$totalPelatihan+= round($arrNilai["$r[idPelatihan]"][$idKategori] / $cntEvaluasi / count($cntKategori["$r[idPelatihan]"]));
					}
				}
				$text.="<td align=\"center\">".round($totalPelatihan / count($dtaKategori["$r[idPelatihan]"]))."</td>
				<td align=\"center\"><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\" class=\"detail\" title=\"Detail Data\"><span>Detail</span></a></td>
			</tr>";	
		}
		
		$text.="</tbody>
	</table>
</div>";	

return $text;
}	

function detail(){
	global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
	$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);		
	$idEvaluasi = $r[idEvaluasi];		

	$arrKategori = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='".$arrParameter[50]."' order by urutanData");

	$dtaJawaban = arrayQuery("select t3.idPertanyaan, t4.idJawaban, t4.bobotJawaban from plt_pelatihan t1 join dta_evaluasi t2 join plt_pertanyaan t3 join plt_pertanyaan_jawaban t4 on (t1.idEvaluasi=t2.idEvaluasi and t2.idEvaluasi=t3.idEvaluasi and t3.idPertanyaan=t4.idPertanyaan) where t1.idPelatihan='$par[idPelatihan]'");

	$sql="select t2.*, t3.idKategori from plt_pelatihan t1 join plt_pertanyaan_evaluasi t2 join plt_pertanyaan t3 on (t1.idPelatihan=t2.idPelatihan and t2.idPertanyaan=t3.idPertanyaan) where t1.idPelatihan='$par[idPelatihan]' order by t1.idPelatihan";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$arrJawaban = explode("\t", $r[evaluasiJawaban]);
		$nilaiPelatihan = 0;
		if(is_array($arrJawaban)){
			reset($arrJawaban);
			while(list($i,$d)=each($arrJawaban)){
				$idJawaban = isset($dtaJawaban["$r[idPertanyaan]"][$d]) ? $d : 0;
				$bobotJawaban = $dtaJawaban["$r[idPertanyaan]"][$idJawaban];
				$nilaiPelatihan+= $bobotJawaban * 100 / count($dtaJawaban["$r[idPertanyaan]"]);
			}
		}			
		$arrNilai["$r[idPegawai]"]+= $nilaiPelatihan;
		$sumKategori["$r[idKategori]"]+= $nilaiPelatihan;
		$cntKategori["$r[idKategori]"]=$r[idKategori];
	}

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread(ucwords($par[mode]." data"))."								
</div>
<div class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  >	
		<div style=\"top:10px; right:35px; position:absolute\">					
			<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idPelatihan")."';\"/>
		</div>
		<div id=\"general\" style=\"margin-top:20px;\">					
			".dtaPelatihan()."					
			<fieldset style=\"padding:10px; border-radius: 10px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>KATEGORI</h4></legend>
				<div id=\"divKategori\" align=\"center\"></div>
				<script type=\"text/javascript\">
					var kategoriChart ='<chart numberScaleValue=\"1000,1000,1000\" numberScaleUnit=\"Ribu, Juta, Miliar\" useRoundEdges=\"1\" showBorder=\"1\" bgColor=\"F7F7F7, E9E9E9\">";

					$cntEvaluasi = count(arrayQuery("select idPegawai from plt_pertanyaan_evaluasi where idPelatihan='$par[idPelatihan]' group by idPegawai"));
					if(is_array($arrKategori)){
						reset($arrKategori);
						while(list($idKategori, $namaKategori) = each($arrKategori)){									
							$nilaiPelatihan = round($sumKategori[$idKategori] / $cntEvaluasi / count($cntKategori));
							$text.="<set label=\"".$namaKategori."\" value=\"".$nilaiPelatihan."\"/> ";					
						}
					}

					$text.="</chart>';
					var chart = new FusionCharts(\"Column3D\", \"chartKategori\", \"100%\", 250);
					chart.setXMLData( kategoriChart );
					chart.render(\"divKategori\");
				</script>
			</fieldset>					
			<fieldset style=\"padding:10px; border-radius: 10px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>MASUKAN & SARAN</h4></legend>
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th>Masukan & Saran</th>
							<th>Peserta</th>
						</tr>
					</thead>
					<tbody>";
						
						$sql="select * from plt_pelatihan_peserta t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idPelatihan='$par[idPelatihan]' and trim(t1.saranPeserta)!='' order by t2.name";
						$res=db($sql);
						$no=1;
						while($r=mysql_fetch_array($res)){
							$cntEvaluasi = getField("select count(*) from plt_pertanyaan_evaluasi where idPelatihan='$par[idPelatihan]' and idPegawai='$r[idPegawai]'");
							$statusEvaluasi = $cntEvaluasi > 0 ?
							"<img src=\"styles/images/t.png\" title='Sudah Dievaluasi'>":
							"<img src=\"styles/images/f.png\" title='Belum Dievaluasi'>";
							$text.="<tr>
							<td>$no.</td>
							<td>$r[saranPeserta]</td>
							<td>".strtoupper($r[name])."</td>
						</tr>";
						$no++;
					}
					
					if($no == 1)
						$text.="<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";

				$text.="</tbody>
			</table>
		</fieldset>

		<fieldset style=\"padding:10px; border-radius: 10px;\">
			<legend style=\"padding:10px; margin-left:20px;\"><h4>PESERTA</h4></legend>
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th>Nama</th>
						<th width=\"200\">Jabatan</th>
						<th width=\"200\">Posisi</th>
						<th width=\"75\">Umur</th>
						<th width=\"50\">Evaluasi</th>
					</tr>
				</thead>
				<tbody>";											

					$sql="select * from plt_pelatihan_peserta t1 join emp t2 on (t1.idPegawai=t2.id) where t1.idPelatihan='$par[idPelatihan]' order by t2.name";
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){
						$cntEvaluasi = getField("select count(*) from plt_pertanyaan_evaluasi where idPelatihan='$par[idPelatihan]' and idPegawai='$r[idPegawai]'");
						if($cntEvaluasi > 0){
							$nilaiPelatihan = ($arrNilai["$r[idPegawai]"] / $cntEvaluasi);
							
							$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($r[name])."</td>
							<td>$r[jabatanPeserta]</td>
							<td>$r[posisiPeserta]</td>
							<td align=\"right\">".getAngka($r[umurPeserta])." Tahun</td>
							<td align=\"center\">".round($nilaiPelatihan)."</td>
						</tr>";
						$totalPelatihan+= $nilaiPelatihan;
						$no++;
					}
				}

				$text.="<tr>
				<td>&nbsp;</td>							
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align=\"center\" colspan=\"2\"><strong>NILAI RATA-RATA<strong></td>
				<td align=\"center\">".round($totalPelatihan / ($no -1))."</td>
			</tr>";

			$text.="</tbody>
		</table>
	</fieldset>

</div>				
</form>";

return $text;
}

function detail2(){
  global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam;
  $sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  if (empty($r[idPelatihan])) {
    $r[idPelatihan] = getField('select idPelatihan from plt_pelatihan order by idPelatihan desc limit 1') + 1;
  }
  if (empty($r[idKategori])) {
    $r[idKategori] = $par[idKategori];
  }
  if (!is_array($detail)) {
    $detail = arrayQuery("select idDetail,concat(keteranganDetail, '\t', DATE_FORMAT(date(mulaiDetail),'%d/%m/%Y'), '\t', substring(time(mulaiDetail),1,5), '\t', DATE_FORMAT(date(selesaiDetail),'%d/%m/%Y'), '\t', substring(time(selesaiDetail),1,5)) from plt_pelatihan_detail where idPelatihan='$par[idPelatihan]'");
  }

  $kodeModul = getField("select kodeModul from app_modul where folderModul = 'katalog'");

  $r[idPegawai] = empty($inp[idPegawai]) ? $r[idPegawai] : $inp[idPegawai];
  $r[idVendor] = empty($inp[idVendor]) ? $r[idVendor] : $inp[idVendor];
  $r[idKategori] = empty($inp[idKategori]) ? $r[idKategori] : $inp[idKategori];
  $r[idDepartemen] = empty($inp[idDepartemen]) ? $r[idDepartemen] : $inp[idDepartemen];
  $r[kodePelatihan] = empty($inp[kodePelatihan]) ? $r[kodePelatihan] : $inp[kodePelatihan];
  $r[judulPelatihan] = empty($inp[judulPelatihan]) ? $r[judulPelatihan] : $inp[judulPelatihan];
  $r[subPelatihan] = empty($inp[subPelatihan]) ? $r[subPelatihan] : $inp[subPelatihan];
  $r[pesertaPelatihan] = empty($inp[pesertaPelatihan]) ? $r[pesertaPelatihan] : setAngka($inp[pesertaPelatihan]);
  $r[pelaksanaanPelatihan] = empty($inp[pelaksanaanPelatihan]) ? $r[pelaksanaanPelatihan] : $inp[pelaksanaanPelatihan];
  $r[lokasiPelatihan] = empty($inp[lokasiPelatihan]) ? $r[lokasiPelatihan] : $inp[lokasiPelatihan];
  $r[biayaPelatihan] = empty($inp[biayaPelatihan]) ? $r[biayaPelatihan] : setAngka($inp[biayaPelatihan]);
  $r[filePelatihan] = empty($fileTemp) ? $r[filePelatihan] : upload($r[idPelatihan]);

  $eksternal = $r[pelaksanaanPelatihan] == 'e' ? 'checked="checked"' : '';
  $internal = empty($eksternal) ? 'checked="checked"' : '';
  $cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
  $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");

  $text .="
  <style>
        #inp_kodeRekening__chosen{
    min-width:250px;
  }
</style>
<div class=\"centercontent contentpopup\">
  <div class=\"pageheader\">
    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
    ".getBread(ucwords("import data"))."
    <span class=\"pagedesc\">&nbsp;</span> 
  </div>
  <div id=\"contentwrapper\" class=\"contentwrapper\">
    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
    <div style=\"position:absolute; right:20px; top:14px;\">
        <input type=\"button\" class=\"cancel radius2\" style=\"float:right\" value=\"Close\" onclick=\"closeBox();\"/>
      </div>
      <!--<fieldset>
        <legend>KATALOG PROGRAM</legend>
        <p>
          <label class=\"l-input-small\">Modul</label>
          <span class=\"field\">
            &nbsp;".getField("SELECT keterangan from app_site WHERE kodeSite = '$r[modul_pelatihan]'")."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Kategori Level</label>
          <span class=\"field\">
            &nbsp;".getField("SELECT namaMenu FROM app_menu WHERE kodeMenu = '$r[kategori_level_pelatihan]'")."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Program</label>
          <span class=\"field\">
            &nbsp;".getField("SELECT program FROM ctg_program WHERE id_program = '$r[program_pelatihan]'")."
          </span>
        </p>
      </fieldset>
      <br>-->
      <fieldset>
        <legend>PELATIHAN</legend>
        <p>
          <label class=\"l-input-small\">Judul Pelatihan</label>
          <span class=\"field\">
            &nbsp;".$r[judulPelatihan]."
          </span>
        </p>
        <table style='width:100%;'>
          <tr>
            <td style='width:50%;'>
              <p>
                <label class=\"l-input-small2\">Tanggal Mulai</label>
                <span class=\"field\">
                  &nbsp;".getTanggal($r[mulaiPelatihan])."
                </span>
              </p>
            </td>
            <td style='width:50%;'>
              <p>
                <label class=\"l-input-small2\">Tanggal Selesai</label>
                <span class=\"field\">
                  &nbsp;".getTanggal($r[selesaiPelatihan])."
                </span>
              </p>
            </td>
          </tr>
        </table>
        <table style='width:100%;'>
          <tr>
            <td style='width:50%;'>
              <p>
                <label class=\"l-input-small2\">Sub</label>
                <span class=\"field\">
                  &nbsp;".$r[subPelatihan]."
                </span>
              </p>
            </td>
            <td style='width:50%;'>
              <p>
                <label class=\"l-input-small2\">Kode</label>
                <span class=\"field\">
                  &nbsp;".$r[kodePelatihan]."
                </span>
              </p>
            </td>
          </tr>
        </table>
        <p>
          <label class=\"l-input-small\">Kategori</label>
          <span class=\"field\">
            &nbsp;".namaData($r[idKategori])."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Level</label>
          <span class=\"field\">
            &nbsp;".namaData($r[idDepartemen])."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Jumlah Peserta</label>
          <span class=\"field\">
            &nbsp;".getAngka($r[pesertaPelatihan])."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Pelaksanaan</label>
          <span class=\"field\">
            &nbsp;".($r[pelaksanaanPelatihan]=='e'?'Eksternal' : 'Internal')."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Vendor</label>
          <span class=\"field\">
            &nbsp;".getField("SELECT namaVendor from dta_vendor where kodeVendor = '$r[idVendor]'")."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Koordinator</label>
          <span class=\"field\">
            &nbsp;".getField("SELECT upper(namaTrainer) as namaTrainer from dta_trainer where idTrainer = '$r[idTrainer]'")."
          </span>
        </p>
        <p>
          <label class=\"l-input-small\">Lokasi</label>
          <span class=\"field\">
            &nbsp;".$r[lokasiPelatihan]."
          </span>
        </p>
      </fieldset>
    </form>
  </div>
</div>";
return $text;
}

function getContent($par){
	global $db,$s,$_submit,$menuAccess;
	switch($par[mode]){	
		case "detail2":
		$text = detail2();
		break;

		case "popup_detail":
		include "program_kategori_popup_detail.php";
		break;

		case "eval":
		if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : update(); else $text = lihat();
		break;
		case "det":
		$text = detail();
		break;
		default:
		$text = lihat();
		break;
	}
	return $text;
}	
?>