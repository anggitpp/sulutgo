<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
ini_set('memory_limit', '1024M');
function detail()
{
    global $db, $s, $inp, $par, $arrTitle, $idPayroll;

    $sql = "select * from emp where id='" . $par[idPegawai] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
	$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
	
   $sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and (
		concat(year(start_date), lpad(month(start_date),2,'0')) <= '".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'
		or start_date is null
	) order by start_date desc limit 1";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

    $namaPegawai = $r[name];
    $cat = $r[cat];

    $text .= "<div class=\"pageheader\">
				<h1 class=\"pagetitle\">
					Slip Gaji
					<div style=\"float:right;\">" . getBulan($par[bulanProses]) . " " . $par[tahunProses] . "</div>
				</h1>
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">				
					<table width=\"100%\">
					<tr>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NAMA</label>
							<span class=\"field\">".$r[name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">ID</label>
							<span class=\"field\">".$r[reg_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">JABATAN</label>
							<span class=\"field\">".$r_[pos_name]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">PANGKAT / GRADE</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[rank]."'")."&nbsp;/&nbsp;".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")."&nbsp;</span>
						</p>
					</td>
					<td width=\"50%\">
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">SKALA</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[skala]."'")."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">STATUS</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r[marital]."'")."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">NO BPJS TK</label>
							<span class=\"field\">".$r[bpjs_no]."&nbsp;</span>
						</p>
						<p>
							<label style=\"width:150px\" class=\"l-input-small\">LOKASI</label>
							<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[location]."'")."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">PENERIMAAN</th>							
							<th width=\"50%\" style=\"text-align:left;\">POTONGAN</th>
						</tr>
					</thead>
					<tbody>";

    //$idProses = getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."' ");
    //$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='".$par[idPegawai]."' and t1.idProses='".$idProses."' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";

    //$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
    //$pph21 = $r[cat] != 532 ? array() : arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
    //$arrNot = array_merge($slipLain, $pph21);

    $sql = "select * from pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . " t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='" . $par[idPegawai] . "' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
		$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
		
        //if (!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $arrNot)) 
		{
            if ($tipeKomponen != $r[tipeKomponen]) $urutanKomponen = 1;
            $arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
            $tipeKomponen = $r[tipeKomponen];
            $urutanKomponen++;
        }

        //if (in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p") $catatanProses = $cat != 532 ? "" : "PPH 21 Rp. " . getAngka($r[nilaiProses]);
    }
    $cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));

    for ($i = 1; $i <= max($cntKomponen); $i++) {
        $text .= "<tr>
							<td style=\"padding:3px 20px;\">";
        $text .= empty($arrKomponen["t"][$i]["namaKomponen"]) ? "&nbsp;" :
            "<span style=\"float:left; width:30px;\">" . $i . ".</span>
							<span style=\"float:left;\">" . $arrKomponen["t"][$i]["namaKomponen"] . "</span>
							<span style=\"float:right; text-align:right; width:125px;\">" . getAngka($arrKomponen["t"][$i]["nilaiProses"]) . "</span>
							<span style=\"float:right;\">Rp.</span>";
        $text .= "</td>
							<td style=\"padding:3px 20px;\">";
        $text .= empty($arrKomponen["p"][$i]["namaKomponen"]) ? "&nbsp;" :
            "<span style=\"float:left; width:30px;\">" . $i . ".</span>
							<span style=\"float:left;\">" . $arrKomponen["p"][$i]["namaKomponen"] . "</span>
							<span style=\"float:right; text-align:right; width:125px;\">" . getAngka($arrKomponen["p"][$i]["nilaiProses"]) . "</span>
							<span style=\"float:right;\">Rp.</span>";
        $text .= "</td>
						</tr>";

        $totKomponen["t"] += $arrKomponen["t"][$i]["nilaiProses"];
        $totKomponen["p"] += $arrKomponen["p"][$i]["nilaiProses"];
    }

    $text .= "<tr>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">" . getAngka($totKomponen["t"]) . "</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
						<td style=\"padding:3px 20px;\">
						<span style=\"float:left; width:30px;\">&nbsp;</span>
						<span style=\"float:left;\"><strong>TOTAL</strong></span>
						<span style=\"float:right; text-align:right; width:125px;\">" . getAngka($totKomponen["p"]) . "</span>
						<span style=\"float:right;\">Rp.</span>
						</td>
					</tr>";

    $text .= "</tbody>
					</table>
					
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<tbody>
						<tr>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd; border-right:0px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left;\"><strong>THP</strong></span>
								<span style=\"float:right; text-align:right; width:125px;\">" . getAngka($totKomponen["t"] - $totKomponen["p"]) . "</span>
								<span style=\"float:right;\">Rp.</span>
							</td>
							<td width=\"50%\" style=\"padding:3px 20px; border-top:1px solid #ddd;\">&nbsp;</td>
						</tr>
						<tr>
							<td colspan=\"2\" style=\"padding:3px 20px;\">
								<span style=\"float:left; width:30px;\">&nbsp;</span>
								<span style=\"float:left; width:203px;\"><strong>Terbilang</strong></span>
								<span>" . trim(terbilang($totKomponen["t"] - $totKomponen["p"])) . " Rupiah</span>
							</td>
						</tr>
					</tbody>
					</table>";

    $sql = "select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status in ('1', 't')";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>							
							<th width=\"50%\" style=\"text-align:left;\">Ditransfer ke :</th>							
							<th width=\"50%\" style=\"text-align:left;\">Catatan :</th>
						</tr>
					</thead>
					<tbody>
						<tr>
						<td style=\"padding:3px 20px; height:75px;\">
							Rek. " . $r[namaBank] . " " . $r[branch] . "&nbsp;<br>
							No. Acc " . $r[account_no] . "&nbsp;<br>
							a.n. " . $namaPegawai . "
						</td>
						<td style=\"padding:3px 20px; height:75px;\">" . nl2br(getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'")) . "&nbsp;" . $catatanProses . "</td>
						</tr>
					</tbody>
					</table>
					
				</div>
				</form>
			</div>";

    return $text;
}

function lihat()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess, $cUsername, $idPayroll;
    if (empty($par[bulanProses])) $par[bulanProses] = date('m');
    if (empty($par[tahunProses])) $par[tahunProses] = date('Y');

    $queryCabang = "SELECT t1.kodeData id, t1.namaData description, t1.kodeInduk from mst_data t1 JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk where t2.kodeCategory='X04' order by t1.urutanData";

    $text .= "<div class=\"pageheader\">
				<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
				" . getBread() . "
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">
			Lokasi Kerja : " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[7] . "' order by urutanData", "kodeData", "namaData", "par[idLokasi]", "All", $par[idLokasi], "onchange=\"document.getElementById('form').submit();\"", "310px") . " 
			<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
			" . comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"") . " " . comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"") . "
			<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
			</div>
			<div style=\"float:right;\">
				Status Pegawai : " . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[5] . "' order by urutanData", "kodeData", "namaData", "par[idStatus]", "ALL", $par[idStatus], "onchange=\"document.getElementById('form').submit();\"") . "			
				" . comboData($queryCabang, "id", "description", "par[idCabang]", " - ALL CABANG - ", $par[idCabang], "onchange=\"document.getElementById('form').submit();\"") . "
				<a href=\"#\" class=\"btn btn1 btn_edit\" onclick=\"openBox('ajax.php?par[mode]=all" . getPar($par, "mode") . "',925,550);\"><span>Print All</span></a>
			</div>
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>" . comboArray("par[search]", array("All", "Nama", "ID"), $par[search]) . "</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
				</tr>
			</table>
			</div>			
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">ID</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th style=\"width:100px;\">Gaji</th>
					<th width=\"50\">Detail</th>
					<th width=\"50\">Cetak</th>
				</tr>
			</thead>
			<tbody>";

    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[6] . "' order by urutanData limit 1");
    $bulanKeluar = $par[bulanProses] == 1 ? 12 : $par[bulanProses] - 1;
    $tahunKeluar = $par[bulanProses] == 1 ? $par[tahunProses] - 1 : $par[tahunProses];
    $tanggalKeluar = $tahunKeluar . "-" . str_pad($bulanKeluar, 2, "0", STR_PAD_LEFT) . "-15";

    $filter = "where (t1.status='" . $status . "' or t1.leave_date > '" . $tanggalKeluar . "') and t2.status=1";

    if (!empty($par[idLokasi]))
        $filter .= " and t2.location='" . $par[idLokasi] . "'";

    if (!empty($par[idStatus]))
        $filter .= " and t1.cat=" . $par[idStatus] . "";

    if (!empty($par[idCabang]))
        $filter .= " and t2.div_id=" . $par[idCabang] . "";

    if ($par[search] == "Nama")
        $filter .= " and lower(t1.name) like '%" . strtolower($par[filter]) . "%'";
    else if ($par[search] == "ID")
        $filter .= " and lower(t1.reg_no) like '%" . strtolower($par[filter]) . "%'";
    else
        $filter .= " and (
					lower(t1.name) like '%" . strtolower($par[filter]) . "%'
					or lower(t1.reg_no) like '%" . strtolower($par[filter]) . "%'
				)";


    if ($idProses = getField("select idProses from pay_proses where detailProses='pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . "' ")) {
        db("delete from pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . " where idKomponen in (10,12) and flagProses=0");

        //$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t2.realisasiKomponen='t' and t1.idProses='".$idProses."' order by idDetail";

        //$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
        //$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");

        $sql = "select * from pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . " t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) where t2.realisasiKomponen='t' order by idDetail";
        $res = db($sql);
        while ($r = mysql_fetch_array($res)) {
            //if (!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)) {
                //if ($r[cat] != 532 || !in_array($r[idKomponen], $pph21))
                    $arrGaji["$r[idPegawai]"] += $r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;
            //}
        }
    }

    $arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
    $sql = "select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id) $filter order by name";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        if (isset($arrGaji["$r[id]"])) {
            $no++;
            $text .= "<tr>
							<td>$no.</td>
							<td>" . strtoupper($r[name]) . "</td>
							<td>$r[kode]</td>
							<td>$r[pos_name]</td>
							<td>" . $arrDivisi["$r[div_id]"] . "</td>
							<td align=\"right\">" . getAngka($arrGaji["$r[id]"]) . "</td>
							<td align=\"center\"><a href=\"#\" title=\"Detail Data\" class=\"detail\" onclick=\"openBox('popup.php?par[mode]=det&par[idPegawai]=$r[id]" . getPar($par, "mode,idPegawai") . "',925,550);\"><span>Detail</span></a></td>
							<td align=\"center\"><a onclick=\"openBox('ajax.php?par[mode]=print&par[idPegawai]=$r[id]" . getPar($par, "mode,idPegawai") . "',925,500);\" href=\"#\" title=\"Detail Data\" class=\"print\" target=\"print\"><span>Print</span></a></td>

						</tr>";
        }
    }
    $text .= "</tbody>			
			</table>
			</div>
			<iframe name=\"print\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";

    if ($par[mode] == "print") pdf();

    return $text;
}

function pdf()
{
    global $db, $s, $inp, $par, $fFile, $arrTitle, $arrParam, $idPayroll;
    require_once 'plugins/PHPPdf.php';

    $sql = "select * from emp where id='" . $par[idPegawai] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
	$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
	
   $sql_="select * from emp_phist where parent_id='".$par[idPegawai]."' and (
		concat(year(start_date), lpad(month(start_date),2,'0')) <= '".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'
		or start_date is null
	) order by start_date desc limit 1";
	$res_=db($sql_);
	$r_=mysql_fetch_array($res_);

    $status1 = getField("select namaData from mst_data where kodeData = '$r[marital]'");
    $status2 = getField("select keteranganData from mst_data where kodeData = '$r[marital]'");
    $kawin = $status1 . ", " . $status2;

    $namaPegawai = $r[name];
    $cat = $r[cat];

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetLeftMargin(5);

    $pdf->Image("images/info/logo-1.png", $pdf->GetX() + 152.5, $pdf->GetY() + 1, 45, 10);
    //$pdf->Image("images/info/bgslip.png", $pdf->GetX() + 35.5, $pdf->GetY() + 50, 120, 50);

    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(200, 7, 'SLIP GAJI', 0, 0, 'L');
    $pdf->Ln(5);
    $pdf->Cell(200, 7, strtoupper(getBulan($par[bulanProses])) . " " . $par[tahunProses], 0, 0, 'L');
    $pdf->Ln();

    $pdf->Cell(200, 1, '', 'B');
    $pdf->Ln(1.25);
    $pdf->Cell(200, 1, '', 'T');
    $pdf->Ln();
    $pdf->Cell(200, 1, '', 'T');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', '8');
    $pdf->SetWidths(array(30, 5, 65, 10, 30, 5, 65));
    $pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L', 'L'));
    $pdf->Row(array("NAMA\tb", ":", $r[name], "", "SKALA\tb", ":", getField("select namaData from mst_data where kodeData='".$r_[skala]."'")), false);
    $pdf->Row(array("ID\tb", ':', $r[reg_no], '', "STATUS\tb", ":", getField("select namaData from mst_data where kodeData='".$r[marital]."'")), false);
    $pdf->Row(array("JABATAN\tb", ":", $r_[pos_name], "", "NO BPJS TK\tb", ":", $r[bpjs_no]), false);
    $pdf->Row(array("PANGKAT / GRADE\tb", ":", getField("select namaData from mst_data where kodeData='".$r_[rank]."'")." / ".getField("select namaData from mst_data where kodeData='".$r_[grade]."'"), "", "LOKASI\tb", ":", getField("select namaData from mst_data where kodeData='".$r_[location]."'")), false);
    $pdf->Ln();


    $pdf->SetWidths(array(100, 100));
    $pdf->SetAligns(array('L', 'L'));
    $pdf->Row(array("PENERIMAAN\tb", "POTONGAN\tb"));

    //$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
    //$pph21 = $r[cat] != 532 ? array() : arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
    //$arrNot = array_merge($slipLain, $pph21);

    $sql = "select * from pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . " t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idPegawai='" . $par[idPegawai] . "' and t2.realisasiKomponen='t' order by t2.tipeKomponen desc, t2.urutanKomponen";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
		$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
        //if (!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $arrNot)) 
		{
            if ($tipeKomponen != $r[tipeKomponen]) $urutanKomponen = 1;
            $arrKomponen["$r[tipeKomponen]"][$urutanKomponen] = $r;
            $tipeKomponen = $r[tipeKomponen];
            $urutanKomponen++;
        }

        //if (in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p") $catatanProses = $cat != 532 ? "" : "PPH 21 Rp. " . getAngka($r[nilaiProses]);
    }
    $cntKomponen = array(count($arrKomponen["t"]), count($arrKomponen["p"]));

    $pdf->SetFont('Arial', '', '8');
    $pdf->SetWidths(array(5, 60, 10, 25, 5, 60, 10, 25));
    $pdf->SetAligns(array('C', 'L', 'C', 'R', 'C', 'L', 'C', 'R'));

    for ($i = 1; $i <= max($cntKomponen); $i++) {

        $noT = empty($arrKomponen["t"][$i]["namaKomponen"]) ? "" : $i . ".";
        $noP = empty($arrKomponen["p"][$i]["namaKomponen"]) ? "" : $i . ".";
        $rpT = empty($arrKomponen["t"][$i]["namaKomponen"]) ? "" : "Rp.";
        $rpP = empty($arrKomponen["p"][$i]["namaKomponen"]) ? "" : "Rp.";
        $nilaiT = empty($arrKomponen["t"][$i]["namaKomponen"]) ? "" : getAngka($arrKomponen["t"][$i]["nilaiProses"]);
        $nilaiP = empty($arrKomponen["p"][$i]["namaKomponen"]) ? "" : getAngka($arrKomponen["p"][$i]["nilaiProses"]);

        $pdf->Row(array(
                $noT . "\tf", $arrKomponen["t"][$i]["namaKomponen"] . "\tf", $rpT . "\tf", $nilaiT . "\tr",
                $noP . "\tf", $arrKomponen["p"][$i]["namaKomponen"] . "\tf", $rpP . "\tf", $nilaiP . "\tf")
        );

        $totKomponen["t"] += $arrKomponen["t"][$i]["nilaiProses"];
        $totKomponen["p"] += $arrKomponen["p"][$i]["nilaiProses"];
    }

    $pdf->Row(array(
            "\tf", "TOTAL\tf", "Rp.\tf", getAngka($totKomponen["t"]) . "\tr",
            "\tf", "TOTAL\tf", "Rp.\tf", getAngka($totKomponen["p"]) . "\tf")
    );
    $pdf->Cell(200, 1, '', 'T');
    $pdf->Ln(5);

    $pdf->Row(array(
            "\tf", "THP\tf", "Rp.\tf", getAngka($totKomponen["t"] - $totKomponen["p"]) . "\tf",
            "\tf", "\tf", "\tf", "\tf")
    );

    $pdf->SetWidths(array(5, 20, 175));
    $pdf->SetAligns(array('C', 'L', 'L'));
    $pdf->SetFont('Arial', '', '7');
    $pdf->Row(array(
            "\tf", "Terbilang\tf", ": " . terbilang($totKomponen["t"] - $totKomponen["p"]) . " Rupiah\tf")
    );
    $pdf->Cell(200, 1, '', 'T');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', '8');


    $sql = "select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$par[idPegawai]' and status in ('1', 't')";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    $rekeningGaji = "Rek. " . $r[namaBank] . " " . $r[branch] . "\nNo. Acc " . $r[account_no] . "\na.n. " . $namaPegawai . "";

    $pdf->SetWidths(array(100, 100));
    $pdf->SetAligns(array('L', 'L'));
    $pdf->Row(array("Ditransfer ke :\tb", "Catatan :\tb"));
    $pdf->Row(array($rekeningGaji, getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'") . " " . $catatanProses));


    $pdf->Ln();
    $pdf->SetFont('Arial', 'BI', 9);
    $pdf->Cell(200, 7, '*Slip gaji ini dicetak menggunakan Human Resource Information System', 0, 0, 'L');
    $pdf->Ln(4);
    $pdf->Cell(200, 7, 'Sehingga tidak memerlukan tanda tangan.', 0, 0, 'L');

    // $pdf->AutoPrint(true);
    $pdf->Output();
}

function cetak()
{
    global $db, $s, $inp, $par, $fFile, $arrTitle, $arrParam, $arrParameter, $cUsername, $idPayroll;
    require_once 'plugins/PHPPdf.php';

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetLeftMargin(5);

    $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='" . $arrParameter[6] . "' order by urutanData limit 1");
    $bulanKeluar = $par[bulanProses] == 1 ? 12 : $par[bulanProses] - 1;
    $tahunKeluar = $par[bulanProses] == 1 ? $par[tahunProses] - 1 : $par[tahunProses];
    $tanggalKeluar = $tahunKeluar . "-" . str_pad($bulanKeluar, 2, "0", STR_PAD_LEFT) . "-15";

    $filter = "where (t1.status='" . $status . "' or t1.leave_date > '" . $tanggalKeluar . "') and t2.status=1";

    if (!empty($par[idLokasi]))
        $filter .= " and t2.location='" . $par[idLokasi] . "'";

    if (!empty($par[idStatus]))
        $filte .= " and t1.cat=" . $par[idStatus] . "";

    if ($par[search] == "Nama")
        $filter .= " and lower(t1.name) like '%" . strtolower($par[filter]) . "%'";
    else if ($par[search] == "ID")
        $filter .= " and lower(t1.reg_no) like '%" . strtolower($par[filter]) . "%'";
    else
        $filter .= " and (
				lower(t1.name) like '%" . strtolower($par[filter]) . "%'
				or lower(t1.reg_no) like '%" . strtolower($par[filter]) . "%'
			)";


    if ($idProses = getField("select idProses from pay_proses where detailProses='pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . "' ")) {
        //$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
        //$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");

        $sql = "select * from pay_proses_" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . " t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) where t2.realisasiKomponen='t' order by t1.idPegawai, t2.tipeKomponen desc, t2.urutanKomponen";
        $res = db($sql);
        while ($r = mysql_fetch_array($res)) {
			$iuranPensiun = $r[join_date] < "2008-01-01" ? "PPMP" : "PPIP";
			$r[namaKomponen] = str_replace("PPIP/PPMP",$iuranPensiun,$r[namaKomponen]);
            //if (!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)) 
			{
                //if ($r[cat] != 532 || !in_array($r[idKomponen], $pph21)) 
				{
                    $arrGaji["$r[idPegawai]"] += $r[tipeKomponen] == "t" ? $r[nilaiProses] : $r[nilaiProses] * -1;

                    if ($tipeKomponen != $r[tipeKomponen]) $urutanKomponen = 1;
                    $arrKomponen["$r[idPegawai]"]["$r[tipeKomponen]"][$urutanKomponen] = $r;
                    $tipeKomponen = $r[tipeKomponen];
                    $urutanKomponen++;
                }
            }

            //if (in_array($r[idKomponen], $pph21) && $r[tipeKomponen] == "p")
            //    $catatanProses["$r[idPegawai]"] = $r[cat] != 532 ? "" : "PPH 21 Rp. " . getAngka($r[nilaiProses]);
        }
    }

    $arrLokasi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='S06'");
    $arrStatus = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='S25'");
    $arrPangkat = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='S09'");
    $arrGrade = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='S10'");
    $arrSkala = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='SG'");

    $sql = "select t1.*, t2.pos_name, t2.rank, t2.grade, t2.location, t2.skala from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and (
		concat(year(t2.start_date), lpad(month(t2.start_date),2,'0')) <= '" . $par[tahunProses] . str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT) . "'
		or t2.start_date is null
	)) $filter order by name";
    $res = db($sql);
    while ($r = mysql_fetch_array($res)) {
        if (isset($arrGaji["$r[id]"])) {
            $pdf->AddPage();
            $pdf->Image("images/info/logo-1.png", $pdf->GetX() + 152.5, $pdf->GetY() + 1, 45, 10);
            //$pdf->Image("images/info/bgslip.png", $pdf->GetX() + 35.5, $pdf->GetY() + 50, 120, 50);

            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(200, 7, 'SLIP GAJI', 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(200, 7, strtoupper(getBulan($par[bulanProses])) . " " . $par[tahunProses], 0, 0, 'L');
            $pdf->Ln();

            $pdf->Cell(200, 1, '', 'B');
            $pdf->Ln(1.25);
            $pdf->Cell(200, 1, '', 'T');
            $pdf->Ln();
            $pdf->Cell(200, 1, '', 'T');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', '8');
            $pdf->SetWidths(array(30, 5, 65, 10, 30, 5, 65));
            $pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L', 'L'));
			$pdf->Row(array("NAMA\tb", ":", $r[name], "", "SKALA\tb", ":", $arrSkala["" . $r[skala] . ""]), false);
			$pdf->Row(array("ID\tb", ':', $r[reg_no], '', "STATUS\tb", ":", $arrStatus["" . $r[marital] . ""]), false);
			$pdf->Row(array("JABATAN\tb", ":", $r[pos_name], "", "NO BPJS TK\tb", ":", $r[bpjs_no]), false);
			$pdf->Row(array("PANGKAT / GRADE\tb", ":", $arrPangkat["" . $r[rank] . ""] . " / " . $arrGrade["" . $r[grade] . ""], "", "LOKASI\tb", ":", $arrLokasi["" . $r[location] . ""]), false);
            $pdf->Ln();

            $pdf->SetWidths(array(100, 100));
            $pdf->SetAligns(array('L', 'L'));
            $pdf->Row(array("PENERIMAAN\tb", "POTONGAN\tb"));


            $pdf->SetFont('Arial', '', '8');
            $pdf->SetWidths(array(5, 60, 10, 25, 5, 60, 10, 25));
            $pdf->SetAligns(array('C', 'L', 'C', 'R', 'C', 'L', 'C', 'R'));

            $cntKomponen = array(count($arrKomponen["$r[id]"]["t"]), count($arrKomponen["$r[id]"]["p"]));
            for ($i = 1; $i <= max($cntKomponen); $i++) {

                $noT = empty($arrKomponen["$r[id]"]["t"][$i]["namaKomponen"]) ? "" : $i . ".";
                $noP = empty($arrKomponen["$r[id]"]["p"][$i]["namaKomponen"]) ? "" : $i . ".";
                $rpT = empty($arrKomponen["$r[id]"]["t"][$i]["namaKomponen"]) ? "" : "Rp.";
                $rpP = empty($arrKomponen["$r[id]"]["p"][$i]["namaKomponen"]) ? "" : "Rp.";
                $nilaiT = empty($arrKomponen["$r[id]"]["t"][$i]["namaKomponen"]) ? "" : getAngka($arrKomponen["$r[id]"]["t"][$i]["nilaiProses"]);
                $nilaiP = empty($arrKomponen["$r[id]"]["p"][$i]["namaKomponen"]) ? "" : getAngka($arrKomponen["$r[id]"]["p"][$i]["nilaiProses"]);

                $pdf->Row(array(
                        $noT . "\tf", $arrKomponen["$r[id]"]["t"][$i]["namaKomponen"] . "\tf", $rpT . "\tf", $nilaiT . "\tr",
                        $noP . "\tf", $arrKomponen["$r[id]"]["p"][$i]["namaKomponen"] . "\tf", $rpP . "\tf", $nilaiP . "\tf")
                );

                $totKomponen["$r[id]"]["t"] += $arrKomponen["$r[id]"]["t"][$i]["nilaiProses"];
                $totKomponen["$r[id]"]["p"] += $arrKomponen["$r[id]"]["p"][$i]["nilaiProses"];
            }

            $pdf->Row(array(
                    "\tf", "TOTAL\tf", "Rp.\tf", getAngka($totKomponen["$r[id]"]["t"]) . "\tr",
                    "\tf", "TOTAL\tf", "Rp.\tf", getAngka($totKomponen["$r[id]"]["p"]) . "\tf")
            );
            $pdf->Cell(200, 1, '', 'T');
            $pdf->Ln(5);

            $pdf->Row(array(
                    "\tf", "THP\tf", "Rp.\tf", getAngka($totKomponen["$r[id]"]["t"] - $totKomponen["$r[id]"]["p"]) . "\tf",
                    "\tf", "\tf", "\tf", "\tf")
            );

            $pdf->SetWidths(array(5, 20, 175));
            $pdf->SetAligns(array('C', 'L', 'L'));
            $pdf->SetFont('Arial', '', '7');
            $pdf->Row(array(
                    "\tf", "Terbilang\tf", ": " . terbilang($totKomponen["$r[id]"]["t"] - $totKomponen["$r[id]"]["p"]) . " Rupiah\tf")
            );
            $pdf->Cell(200, 1, '', 'T');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', '', '8');

            $sql_ = "select t1.*, t2.namaData as namaBank from emp_bank t1 join mst_data t2 on (t1.bank_id=t2.kodeData) where t1.parent_id='$r[id]' and status in ('1', 't')";
            $res_ = db($sql_);
            $r_ = mysql_fetch_array($res_);
            $rekeningGaji = "Rek. " . $r_[namaBank] . " " . $r_[branch] . "\nNo. Acc " . $r_[account_no] . "\na.n. " . $r[name] . "";
            $pdf->SetWidths(array(100, 100));
            $pdf->SetAligns(array('L', 'L'));
            $pdf->Row(array("Ditransfer ke :\tb", "Catatan :\tb"));
            $pdf->Row(array($rekeningGaji, getField("select keteranganCatatan from pay_catatan where idPegawai='$par[idPegawai]'") . " " . $catatanProses["$r[id]"]));


            $pdf->Ln();
            $pdf->SetFont('Arial', 'BI', 9);
            $pdf->Cell(200, 7, '*Slip gaji ini dicetak menggunakan Human Resource Information System', 0, 0, 'L');
            $pdf->Ln(4);
            $pdf->Cell(200, 7, 'Sehingga tidak memerlukan tanda tangan.', 0, 0, 'L');
        }
    }

    // $pdf->AutoPrint(true);
    $pdf->Output();
}

function getContent($par)
{
    global $db, $s, $_submit, $menuAccess;
    switch ($par[mode]) {
        case "det":
            $text = detail();
            break;
        case "all":
            $text = cetak();
            break;
        default:
            $text = lihat();
            break;
    }
    return $text;
}

?>
