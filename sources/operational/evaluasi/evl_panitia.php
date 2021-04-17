<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
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

function update(){
	global $db,$s,$inp,$par,$det,$cUsername;
	repField("saranPeserta");

	$sql="update plt_pelatihan_peserta set saranPeserta='$inp[saranPeserta]' where idPelatihan='$par[idPelatihan]' and idPegawai='$par[idPegawai]'";
	db($sql);		

	$idEvaluasi = getField("select idEvaluasi from plt_pelatihan where idPelatihan='$par[idPelatihan]'");
	db("delete from plt_pertanyaan_evaluasi where idPelatihan='$par[idPelatihan]' and idPegawai='$par[idPegawai]'");
	if(is_array($det)){	
		while (list($idPertanyaan) = each($det)){
			$tipeJawaban = getField("select tipePertanyaan from plt_pertanyaan where idPertanyaan='$idPertanyaan'");
			$sql="insert into plt_pertanyaan_evaluasi (idPertanyaan, idPegawai, idPelatihan, idEvaluasi, tipeJawaban, evaluasiJawaban, createBy, createTime) values ('$idPertanyaan', '$par[idPegawai]', '$par[idPelatihan]', '$idEvaluasi', '$tipeJawaban', '".implode("\t", $det[$idPertanyaan])."', '$cUsername', '".date('Y-m-d H:i:s')."')";
			db($sql);
		}
	}

	echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idPegawai")."';</script>";
}

function form(){
	global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
	include "plugins/mces.jsp";

	$sql="select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
	$res=db($sql);
	$r=mysql_fetch_array($res);					
	$idEvaluasi = $r[idEvaluasi];

	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread(ucwords($par[mode]." data"))."								
</div>
<div class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  >
		<div id=\"general\" style=\"margin-top:20px;\">					
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>INFORMASI</h4></legend>
				<p>
					<label class=\"l-input-small\" style=\"width:150px;\">Pelatihan</label>
					<span class=\"field\" style=\"margin-left:150px;\">$r[judulPelatihan]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\" style=\"width:150px;\">Nama Pegawai</label>
					<span class=\"field\" style=\"margin-left:150px;\">".getField("select name from emp where id='".$par[idPegawai]."'")."&nbsp;</span>
				</p>
			</fieldset>";

			$sql="select * from plt_pertanyaan t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.idEvaluasi='".$idEvaluasi."' and t1.statusPertanyaan='t'";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$arrKategori["$r[idKategori]"] = $r[urutanData]."\t".$r[namaData];
			}

			$text.="<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
			<legend style=\"padding:10px; margin-left:20px;\"><h4>PERTANYAAN</h4></legend>
			<div style=\"float:right; margin-right:20px;\">
				<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode, idPegawai")."';\"/> <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
			</div>";

			if(is_array($arrKategori)){
				$text.="<ul class=\"hornav\">";
				
				$tab = 1;
				asort($arrKategori);
				reset($arrKategori);				
				while (list($idKategori, $valKategori) = each($arrKategori)){
					list($urutanKategori, $namaKategori) = explode("\t", $valKategori);
					$current = $tab == 1 ? "class=\"current\"" : "";
					$text.="<li ".$current." style=\"margin-bottom:10px;\"><a href=\"#tab_".$urutanKategori."\">".$namaKategori."</a></li>";
					$tab++;
				}
				$text.="</ul>";					
				
				$tab = 1;
				asort($arrKategori);
				reset($arrKategori);
				while (list($idKategori, $valKategori) = each($arrKategori)){
					$display = $tab == 1 ? "display: block;" : "display: none;";
					
					list($urutanKategori, $namaKategori) = explode("\t", $valKategori);
					$text.="<div id=\"tab_".$urutanKategori."\" class=\"subcontent\" style=\"border:0px; clear:both; ".$display."\">";
					
					$sql="select * from plt_pertanyaan where idEvaluasi='".$idEvaluasi."' and idKategori='".$idKategori."' and statusPertanyaan='t' order by idPertanyaan";
					$res=db($sql);
					$no=1;
					while($r=mysql_fetch_array($res)){		
						$tipePertanyaan = $r[tipePertanyaan];
						$evaluasiJawaban = getField("select evaluasiJawaban from plt_pertanyaan_evaluasi where idPertanyaan='$r[idPertanyaan]' and idPegawai='$par[idPegawai]'");
						
						$text.="<table style=\"width:100%; margin-bottom:20px;\" cellpadding=\"0\" cellspacing=\"0\">
						<tr>
							<td width=\"30\">$no.</td>
							<td>$r[detailPertanyaan]</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>";

								$idJawaban = 0;
								if(in_array($tipePertanyaan, array("a", "i"))){
									$text.="<textarea id=\"det[".$r[idPertanyaan]."][".$idJawaban."]\" name=\"det[".$r[idPertanyaan]."][".$idJawaban."]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:500px;\">".$evaluasiJawaban."</textarea>";
								}else{
									$arrJawaban = explode("\t", $evaluasiJawaban);
									$type = $tipePertanyaan == "c" ? "checkbox" : "radio";							
									$text.="<div class=\"fradio\">";							
									$sql_="select * from plt_pertanyaan_jawaban where idPertanyaan='".$r[idPertanyaan]."'";
									$res_=db($sql_);
									while($r_=mysql_fetch_array($res_)){
										$checked = in_array($r_[idJawaban], $arrJawaban) ? "checked=\"checked\"" : "";
										$idJawaban = $tipePertanyaan == "c" ? $r_[idJawaban] : $idJawaban;
										$text.="<input type=\"".$type."\" id=\"det[".$r_[idPertanyaan]."][".$r_[idJawaban]."][".$r_[idJawaban]."]\" name=\"det[".$r_[idPertanyaan]."][".$idJawaban."]\" value=\"$r_[idJawaban]\" $checked /> <span class=\"sradio\">$r_[detailJawaban]</span>";
									}
									$text.="</div>";
								}

								$text.="</td>
							</tr>
						</table>";
						
						$no++;						
					}					
					
					$text.="</div>";					
					$tab++;
				}
				
			}

			$text.="</fieldset>
			
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
				<legend style=\"padding:10px; margin-left:20px;\"><h4>MASUKAN & SARAN</h4></legend>
				<textarea id=\"mce1\" name=\"inp[saranPeserta]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:100%;\">".getField("select saranPeserta from plt_pelatihan_peserta where idPegawai='".$par[idPegawai]."'")."</textarea>
			</fieldset>
			
		</div>				
	</form>";

	return $text;
}

function lihat(){
	global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;
	if(empty($_submit) && empty($par[tahunPelatihan])) $par[tahunPelatihan] = date('Y');
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
					<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\" /></td>
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
				<th rowspan=\"2\" style=\"vertical-align:middle\" max-width=\"20\">No.</th>					
				<th rowspan=\"2\" style=\"vertical-align:middle\">Pelatihan</th>					
				<th colspan=\"2\" style=\"vertical-align:middle; max-width:150px;\">Pelaksanaan</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\">Lokasi</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\">Vendor</th>
				<th rowspan=\"2\" style=\"vertical-align:middle\">PIC</th>
				<th colspan=\"2\" style=\"vertical-align:middle; max-width:100px;\">Jumlah</th>
			</tr>
			<tr>
				<th style=\"vertical-align:middle; max-width:75px;\">Mulai</th>
				<th style=\"vertical-align:middle; max-width:75px;\">Selesai</th>
				<th style=\"vertical-align:middle; max-width:50px;\">Peserta</th>
				<th style=\"vertical-align:middle; max-width:50px;\">Evaluasi</th>					
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
			or lower(t1.lokasiPelatihan) like '%".strtolower($par[filter])."%'
			or lower(t1.namaPic) like '%".strtolower($par[filter])."%'
			or lower(t2.namaVendor) like '%".strtolower($par[filter])."%'
			)";

			$sql="select t1.*, case when t1.pelaksanaanPelatihan='e' then t2.namaVendor else 'Internal' end as namaVendor from (
			select p1.*, case when p1.pelaksanaanPelatihan='e' then p2.namaTrainer else p1.namaPegawai end as namaPic from (
			select d1.*, d2.name as namaPegawai from plt_pelatihan d1 left join emp d2 on (d1.idPegawai=d2.id)
			) as p1 left join dta_trainer p2 on (p1.idTrainer=p2.idTrainer)
			) as t1 left join dta_vendor t2 on (t1.idVendor=t2.kodeVendor) $filter order by t1.idPelatihan";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){					
				$no++;			
				$cntPeserta = getField("select count(idPegawai) from plt_pelatihan_peserta where idPelatihan='$r[idPelatihan]'");
				$cntEvaluasi = count(arrayQuery("select idPegawai from plt_pertanyaan_evaluasi where idPelatihan='$r[idPelatihan]' group by idPegawai"));
				$text.="<tr>
				<td align=\"center\">$no.</td>			
				<td><a href=\"?par[mode]=det&par[idPelatihan]=$r[idPelatihan]".getPar($par,"mode,idPelatihan")."\">$r[judulPelatihan]</a></td>
				<td align=\"center\">".getTanggal($r[mulaiPelatihan])."</td>
				<td align=\"center\">".getTanggal($r[selesaiPelatihan])."</td>
				<td>$r[lokasiPelatihan]</td>
				<td>$r[namaVendor]</td>
				<td>$r[namaPegawai]</td>
				<td align=\"center\">".getAngka($cntPeserta)."</td>
				<td align=\"center\">".getAngka($cntEvaluasi)."</td>
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
			<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
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
							$getJabatan = getField("SELECT pos_name FROM emp_phist where parent_id = '$r[id]' ");
							if(!empty($getJabatan)){

								$jabatan = getField("SELECT namaData FROM mst_data where kodeData = '$getJabatan'");

							}else{
								$jabatan = "-";
							};
							
							$statusEvaluasi = $cntEvaluasi > 0 ?
							"<img src=\"styles/images/t.png\" title='Sudah Dievaluasi'>":
							"<img src=\"styles/images/f.png\" title='Belum Dievaluasi'>";
							$text.="<tr>
							<td>$no.</td>
							<td>".strtoupper($r[name])."</td>
							<td>$jabatan</td>
							<td>$r[posisiPeserta]</td>
							<td align=\"right\">".getAngka($r[umurPeserta])." Tahun</td>
							<td align=\"center\">
								<a href=\"?par[mode]=eval&par[idPegawai]=$r[idPegawai]".getPar($par,"mode,idPegawai")."\" title=\"Evaluasi\">".$statusEvaluasi."</a>
							</td>
						</tr>";			
						$no++;
					}
					
					if($no == 1)
						$text.="<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";

				$text.="</tbody>
			</table>
		</fieldset>
	</div>				
</form>";

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