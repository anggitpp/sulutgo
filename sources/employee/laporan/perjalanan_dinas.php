<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$fExport = "files/export/";
function lihat(){
	global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;		
	if(empty($par[tahun])) $par[tahun]=date('Y');					
	$text.="<div class=\"pageheader\">
	<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
	".getBread()."

</div>    
<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form action=\"\" method=\"post\" class=\"stdform\">
		<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
				<td>".comboYear("par[tahun]", $par[tahun])."</td>
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
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
					<th width=\"100\">Nomor</th>
					<th width=\"100\">Tanggal</th>
					<th width=\"100\">Kategori</th>
					</tr>
				</thead>
				<tbody>";

		//if(!empty($par[filter]))	
					$filter.="where  (
					lower(t2.name) like '%".strtolower($par[filter])."%'
					or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
					or lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'			
					)";

					if(!empty($par[tahun])){
						$filter.=" and year(tanggalDinas) = '$par[tahun]'";
					}
					

					$sql="select * from ess_dinas t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter order by nomorDinas";
					$res=db($sql);		
					while($r=mysql_fetch_array($res)){
						$no++;

						$text.="<tr>
						<td>$no.</td>
						
							<td>$r[name]</td>					
							<td>$r[reg_no]</td>
							<td>$r[nomorDinas]</td>					
							<td align=\"center\">".getTanggal($r[tanggalDinas])."</td>	
							<td>".getField("Select namaData from mst_data where kodeData = '$r[idKategori]'")."</td>					
							
							</tr>";				
						}	

						$text.="</tbody>
					</table>
				</div>";
				$sekarang = date('Y-m-d');
				if($par[mode] == "xls"){      
					xls();      
					$text.="<iframe src=\"download.php?d=exp&f=LAPORAN PERJALANAN DINAS ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);

				$objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');    
				$objPHPExcel->getActiveSheet()->mergeCells('A2:Q2');    
				$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

				$objPHPExcel->getActiveSheet()->setCellValue('A1', "LAPORAN PERJALANAN DINAS");


				$objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getFont()->setBold(true);  
				$objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:Q4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


				$objPHPExcel->getActiveSheet()->setCellValue('A4', 'No.');
				$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NAMA');
				$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NPP');
				$objPHPExcel->getActiveSheet()->setCellValue('D4', 'JABATAN');
				$objPHPExcel->getActiveSheet()->setCellValue('E4', 'TGL KEBERANGKATAN');
				$objPHPExcel->getActiveSheet()->setCellValue('F4', 'TGL KEPULANGAN');
				$objPHPExcel->getActiveSheet()->setCellValue('G4', 'NOMOR');
				$objPHPExcel->getActiveSheet()->setCellValue('H4', 'KOTA AWAL');
				$objPHPExcel->getActiveSheet()->setCellValue('I4', 'KOTA TUJUAN');
				$objPHPExcel->getActiveSheet()->setCellValue('J4', 'JENIS TRANSPORTASI');
				$objPHPExcel->getActiveSheet()->setCellValue('K4', 'AGENDA PERJALANAN DINAS');
				$objPHPExcel->getActiveSheet()->setCellValue('L4', 'JUMLAH HARI');
				$objPHPExcel->getActiveSheet()->setCellValue('M4', 'UANG SAKU');
				$objPHPExcel->getActiveSheet()->setCellValue('N4', 'UANG MAKAN');
				$objPHPExcel->getActiveSheet()->setCellValue('O4', 'UANG PELENGKAP');
				$objPHPExcel->getActiveSheet()->setCellValue('P4', 'TRANSPORT TUJUAN');
				$objPHPExcel->getActiveSheet()->setCellValue('Q4', 'SUB TOTAL');


				$rows=5;    
				$filter.="where  (
				lower(t2.name) like '%".strtolower($par[filter])."%'
				or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
				or lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'			
				)";

				if(!empty($par[tahun])){
					$filter.=" and year(tanggalDinas) = '$par[tahun]'";
				}


				$sql="select * from ess_dinas t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter order by nomorDinas";
				$res=db($sql);

				while($r=mysql_fetch_array($res)){      
					$no++;
					
					$objPHPExcel->getActiveSheet()->getStyle('A'.$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    
    // $objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':B'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);      

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$rows, $r[name]);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, $r[reg_no]);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[nomorDinas]);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, getTanggal($r[tanggalDinas]));
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, getField("Select namaData from mst_data where kodeData = '$r[idKategori]'"));
    // $rows = $rows + $no__anakan;

					$rows++;
    // $rows = $rows -1;
				}
				$rows--;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':Q'.$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('F4:F'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('G4:G'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('H4:H'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('I4:I'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('J4:J'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('K4:K'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('L4:L'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('M4:M'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('N4:N'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('O4:O'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('P4:P'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('Q4:Q'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle('A1:Q'.$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

				$objPHPExcel->getActiveSheet()->getStyle('A4:Q'.$rows)->getAlignment()->setWrapText(true);            

				$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(100);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(100);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);
				$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 4);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.2);
				$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

				$objPHPExcel->getActiveSheet()->setTitle("LAPORAN PERJALANAN DINAS");
				$objPHPExcel->setActiveSheetIndex(0);

  // Save Excel file
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				$objWriter->save($fExport."LAPORAN PERJALANAN DINAS ".$sekarang.".xls");
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