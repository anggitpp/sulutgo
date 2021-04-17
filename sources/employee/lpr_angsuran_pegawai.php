<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
function lihat(){
	global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;		
	// if(empty($par[tahun])) $par[tahun]=date('Y');	
	// if(empty($par[bulan])) $par[bulan]=date('m');	
	if(empty($par[tipePinjaman])){
		 $koperasi = "checked=\"checked\"";		
	}else if($par[tipePinjaman] == "t"){
		$koperasi = "checked=\"checked\"";
	}else{
		 $perusahaan = "checked=\"checked\"";	
	}
	if(empty($par[tipePinjaman])) $par[tipePinjaman] = "t";
		
	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."

</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form action=\"\" id=\"form\" method=\"post\" class=\"stdform\">
		<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" style=\"width:200px;\" value=\"$par[filter]\" class=\"mediuminput\" />
				<td>".comboMonth("par[bulan]", $par[bulan], "", "", "All")."</td>
				<td>".comboYear("par[tahun]", $par[tahun], "","","","All")."</td>
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
				<input type=\"radio\" id=\"true\" name=\"par[tipePinjaman]\" onchange=\"document.getElementById('form').submit();\" value=\"t\" $koperasi /> <span class=\"sradio\">Koperasi</span>
				<input type=\"radio\" id=\"false\" name=\"par[tipePinjaman]\" onchange=\"document.getElementById('form').submit();\" value=\"f\" $perusahaan /> <span class=\"sradio\">Perusahaan</span> 
			</p>
		</div>
		<div id=\"pos_r\"><a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a></div>
		</form>
		<br clear=\"all\" />
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"*\">Nama</th>
					<th width=\"100\">NPP</th>
					<th width=\"100\">Tanggal</th>
					<th width=\"100\">Nomor</th>
					<th width=\"100\">Nilai</th>
					<th width=\"100\">Status</th>
					</tr>
				</thead>
				<tbody>";

		//if(!empty($par[filter]))	
					$filter.="where  (
					lower(t2.name) like '%".strtolower($par[filter])."%'
					or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
					or lower(t1.nomorPinjaman) like '%".strtolower($par[filter])."%'			
					)";

					if(!empty($par[tahun])){
						$filter.=" and year(tanggalPinjaman) = '$par[tahun]'";
					}

					if(!empty($par[bulan])){
						$filter.=" and month(tanggalPinjaman) = '$par[bulan]'";
					}

					if(!empty($par[tipePinjaman]))
						$filter.=" and tipePinjaman = '$par[tipePinjaman]'";
					

					$sql="select * from ess_pinjaman t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter order by nomorPinjaman";
					$res=db($sql);		
					while($r=mysql_fetch_array($res)){
						$no++;

						$statusPinjaman = getField("select count(*) from ess_angsuran where statusAngsuran='f' and idPinjaman='$r[idPinjaman]'") > 0 ?
							"<img src=\"styles/images/f.png\" title=\"Belum Lunas\">":
							"<img src=\"styles/images/t.png\" title=\"Sudah Lunas\">";

						$text.="<tr>
						<td>$no.</td>
						
							<td>$r[name]</td>					
							<td>$r[reg_no]</td>
									
							<td align=\"center\">".getTanggal($r[tanggalPinjaman])."</td>	
							<td>$r[nomorPinjaman]</td>			
							<td align=\"right\">".getAngka($r[nilaiPinjaman])."</td>
							<td align=\"center\">$statusPinjaman</td>					
							
							</tr>";				
						}	

						$text.="</tbody>
					</table>
				</div>";
				$sekarang = date('Y-m-d');
				if($par[mode] == "xls"){      
					xls();      
					$text.="<iframe src=\"download.php?d=exp&f=LAPORAN PINJAMAN PEGAWAI ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
				}

				return $text;
			}		

			function xls(){   
				global $db,$par,$arrTitle,$arrIcon,$cName,$menuAccess,$fExport,$cUsername,$s,$cID;
				require_once 'plugins/PHPExcel.php';
				$sekarang = date('Y-m-d');

				$objPHPExcel = new PHPExcel();        
				$objPHPExcel->getProperties()->setCreator($cName)
				->setLastModifiedBy($cName)
				->setTitle($arrTitle["".$_GET[p].""]);
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

				$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');    
				$objPHPExcel->getActiveSheet()->mergeCells('A2:G2');    
				$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

				$objPHPExcel->getActiveSheet()->setCellValue('A1', "LAPORAN PINJAMAN PEGAWAI");


				$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold(true);  
				$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


				$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
				$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
				$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
				$objPHPExcel->getActiveSheet()->setCellValue('D4', 'TANGGAL');
				$objPHPExcel->getActiveSheet()->setCellValue('E4', 'NOMOR');
				$objPHPExcel->getActiveSheet()->setCellValue('F4', 'NILAI');
				$objPHPExcel->getActiveSheet()->setCellValue('G4', 'STATUS');


				$rows=5;    
				$filter.="where  (
					lower(t2.name) like '%".strtolower($par[filter])."%'
					or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
					or lower(t1.nomorPinjaman) like '%".strtolower($par[filter])."%'			
					)";

					if(!empty($par[tahun])){
						$filter.=" and year(tanggalPinjaman) = '$par[tahun]'";
					}

					if(!empty($par[bulan])){
						$filter.=" and month(tanggalPinjaman) = '$par[bulan]'";
					}

					if(!empty($par[tipePinjaman]))
						$filter.=" and tipePinjaman = '$par[tipePinjaman]'";
					

					$sql="select * from ess_pinjaman t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter order by nomorPinjaman";
				$res=db($sql);

				while($r=mysql_fetch_array($res)){      
					$no++;
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    
    // $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':B'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

    				$statusPinjaman = getField("select count(*) from ess_angsuran where statusAngsuran='f' and idPinjaman='$r[idPinjaman]'") > 0 ?
							"Belum Lunas" : "Sudah Lunas";
	    

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[name]);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[tanggalPinjaman]);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $r[nomorPinjaman]);

					$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getAngka($r[nilaiPinjaman]));
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$rows, $statusPinjaman);
					$rows++;
    // $rows = $rows -1;
				}
				$rows--;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

				$objPHPExcel->getActiveSheet()->getStyle('A4:G'.$rows)->getAlignment()->setWrapText(true);            

				$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

				$objPHPExcel->getActiveSheet()->setTitle("LAPORAN PINJAMAN PEGAWAI");
				$objPHPExcel->setActiveSheetIndex(0);

  // Save Excel file
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save($fExport."LAPORAN PINJAMAN PEGAWAI ".$sekarang.".xls");
			}
		function getContent($par){
			global $s,$_submit,$menuAccess;
			switch($par[mode]){
				case "cek":
				$text = cek();
				break;
				case "get":
				$text = gPegawai();
				break;
				case "peg":
				$text = pegawai();
				break;
				case "pas":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formPas() : ubahPas(); else $text = lihat();
				break;
				case "delPic":				
				if(isset($menuAccess[$s]["edit"])) $text = hapusPic(); else $text = lihat();
				break;
				case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
				break;
				case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
				break;
				case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
				break;
				default:
				$text = lihat();
				break;
			}
			return $text;
		}	
		?>