<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
	$fFile = "files/export/";
	if(empty($par[idJenis])) $par[idJenis] = getField("select idJenis from pay_jenis where statusJenis='t' order by idJenis limit 1");
	
	$sql="select t2.* from pay_jenis_komponen t1 join dta_komponen t2 on (t1.idKomponen=t2.idKomponen) where t1.idJenis='".$par[idJenis]."' and t2.statusKomponen='t'";
	$res=db($sql);
	while($r=mysql_fetch_array($res)){
		$tipeKomponen = substr(strtolower($r[kodeKomponen]),0,2);
		if($tipeKomponen == "lm") $r[tipeKomponen] = "lm";
		if($tipeKomponen == "ar") $r[tipeKomponen] = "ar";
		if($tipeKomponen == "pn") $r[tipeKomponen] = "pn";
		if($tipeKomponen == "em") $r[tipeKomponen] = "em";
		$arrKomponen["$r[tipeKomponen]"]["$r[idKomponen]"]=strlen($r[urutanKomponen]).$r[urutanKomponen]."\t".$r[namaKomponen];
		$cntKomponen++;
	}
	
	function lihat(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess,$arrParameter,$arrKomponen,$cntKomponen, $areaCheck;
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');				
		if(empty($fSearch)) $fSearch = empty($_POST[fSearch]) ? $_GET[fSearch] : $_POST[fSearch]; 
		
		$cols = $cntKomponen + 13;
		$nosort = array();
		for($i=1; $i<=$cols; $i++) $nosort[]=$i;
		$text = table($cols, $nosort, "lst", "false", "h", "dataList");
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
				<div style=\"position: absolute; right: 20px; top: 10px; vertical-align:top; padding-top:2px; width: 600px;\">
						<div style=\"position:absolute; right: 0px;\">
							<table>
								<tr>
									<td>
										Lokasi Proses : ".comboData("select * from mst_data where statusData='t' and kodeCategory='X03' order by urutanData","kodeData","namaData","par[idLokasi]","All",$par[idLokasi],"onchange=\"document.getElementById('form').submit();\"", "120px")."
									</td>
									<td style=\"vertical-align:top;\" id=\"bView\">
										<input type=\"button\" value=\"+\" style=\"font-size:26px; padding:0 6px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'none';
										document.getElementById('bHide').style.display = 'table-cell';
										document.getElementById('dFilter').style.visibility = 'visible';							
										document.getElementById('fSet').style.height = 'auto';
										document.getElementById('fSet').style.padding = '10px';
										\">
									</td>
									<td style=\"vertical-align:top; display:none;\" id=\"bHide\">
										<input type=\"button\" value=\"-\" style=\"font-size:26px; padding:0 9px;\" class=\"btn btn_search btn-small\" onclick=\"
										document.getElementById('bView').style.display = 'table-cell';
										document.getElementById('bHide').style.display = 'none';
										document.getElementById('dFilter').style.visibility = 'collapse';							
										document.getElementById('fSet').style.height = '0px';
										document.getElementById('fSet').style.padding = '0px';
										\">					
									</td>
									<td>
										<input type=\"button\" value=\"&lsaquo;\" class=\"btn btn_search btn-small\" style=\"margin-right:5px;\" onclick=\"prevDate();\"/>
										".comboMonth("par[bulanProses]", $par[bulanProses], "onchange=\"document.getElementById('form').submit();\"")." ".comboYear("par[tahunProses]", $par[tahunProses], "", "onchange=\"document.getElementById('form').submit();\"")."
										<input type=\"button\" value=\"&rsaquo;\" class=\"btn btn_search btn-small\" onclick=\"nextDate();\"/>
									</td>
								</tr>
							</table>
						</div>
						<fieldset id=\"fSet\" style=\"padding:0px; border: 0px; height:0px; box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12); background: #fff;position: absolute; left: 0; right: 0px; top: 40px; z-index: 800;\">
							<div id=\"dFilter\" style=\"visibility:collapse;\">
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[39]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X05' ORDER BY urutanData", "kodeData", "namaData", "par[divId]", "--".strtoupper($arrParameter[39])."--", $par[divId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[40]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X06' AND kodeInduk = '$par[divId]' ORDER BY urutanData", "kodeData", "namaData", "par[deptId]", "--".strtoupper($arrParameter[40])."--", $par[deptId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:150px; text-align:left; padding-left:10px;\">".strtoupper($arrParameter[41]) . "</label>
									<div class=\"field\" style=\"margin-left:150px;\">
									    ".comboData("SELECT kodeData, namaData FROM mst_data WHERE statusData='t' AND kodeCategory = 'X07' AND kodeInduk = '$par[deptId]' ORDER BY urutanData", "kodeData", "namaData", "par[unitId]", "--".strtoupper($arrParameter[41])."--", $par[unitId], "onchange=\"document.getElementById('form').submit();\"", "250px", "chosen-select") . "
									</div>
								</p>
							</div>
						</fieldset>
				</div>	
				<div id=\"pos_l\" style=\"float:left;\">
					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$fSearch."\" style=\"width:200px;\"/>
				</div>				
			<div id=\"pos_r\" style=\"float:right; margin-top:10px; margin-bottom:10px;\">
				".comboData("select * from pay_jenis where statusJenis='t' order by idJenis","idJenis","namaJenis","par[idJenis]","",$par[idJenis], "onchange=\"document.getElementById('form').submit();\"", "90%", "chosen-select")."
				<a href=\"#\" onclick=\"window.location='?par[mode]=xls&fSearch=' + document.getElementById('fSearch').value + '".getPar($par,"mode")."';\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>
			</div>
			</form>			
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th rowspan=\"2\" style=\"vertical-align:middle\" width=\"20\">No.</th>					
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">NPP</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:250px;\">Nama</th>
					<th rowspan=\"2\"  style=\"vertical-align:middle; min-width:250px;\">$arrParameter[39]</th>
					<th rowspan=\"2\"  style=\"vertical-align:middle; min-width:250px;\">$arrParameter[40]</th>					
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:250px;\">Jabatan</th>";
			if(is_array($arrKomponen["t"])){
			  asort($arrKomponen["t"]);
			  reset($arrKomponen["t"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["t"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$text.="<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">".$namaKomponen."</th>";
			  }
			}			
			$text.="<th style=\"vertical-align:middle; min-width:125px;\">Total Gaji</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">Keterangan</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">Usia</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">Rekening</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">Tanggal Masuk</th>
					<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">Tanggal Lahir</th>";
			if(is_array($arrKomponen["lm"])){
			  asort($arrKomponen["lm"]);
			  reset($arrKomponen["lm"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["lm"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$text.="<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">".$namaKomponen."</th>";
			  }
			}
			if(count($arrKomponen["p"]) > 0)
				$text.="<th style=\"vertical-align:middle\" colspan=\"".count($arrKomponen["p"])."\">Pemotongan</th>";
			if(count($arrKomponen["pn"]) > 0) 
				$text.="<th style=\"vertical-align:middle\" colspan=\"".count($arrKomponen["pn"])."\">Non Proses</th>";			
			if(count($arrKomponen["ar"]) > 0) 
				$text.="<th style=\"vertical-align:middle\" colspan=\"".count($arrKomponen["ar"])."\">AR. DKM</th>";
			
			if(is_array($arrKomponen["em"])){
			  asort($arrKomponen["em"]);
			  reset($arrKomponen["em"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["em"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$text.="<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">".$namaKomponen."</th>";
			  }
			}
			$text.="<th rowspan=\"2\" style=\"vertical-align:middle; min-width:125px;\">Total</th>
				</tr>
				<tr>
					<th style=\"vertical-align:middle; min-width:125px;\">".getBulan($par[bulanProses])." ".$par[tahunProses]."</th>";
			if(is_array($arrKomponen["p"])){
			  asort($arrKomponen["p"]);
			  reset($arrKomponen["p"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["p"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$text.="<th style=\"vertical-align:middle; min-width:125px;\">".$namaKomponen."</th>";
			  }
			}
			if(is_array($arrKomponen["pn"])){
			  asort($arrKomponen["pn"]);
			  reset($arrKomponen["pn"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["pn"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$text.="<th style=\"vertical-align:middle; min-width:125px;\">".$namaKomponen."</th>";
			  }
			}
			if(is_array($arrKomponen["ar"])){
			  asort($arrKomponen["ar"]);
			  reset($arrKomponen["ar"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["ar"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$text.="<th style=\"vertical-align:middle; min-width:125px;\">".$namaKomponen."</th>";
			  }
			}
			$text.="</tr>
			</thead>
			<tbody></tbody>			
			</table>
			</div>";
			
		if($par[mode] == "xls"){			
			xls();			
			$text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower("Laporan Rekapitulasi Gaji")).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
		}		
			
		return $text;
	}
	
	function xls(){		
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrKomponen, $areaCheck;
		require_once 'plugins/PHPExcel.php';
		
		$objPHPExcel = new PHPExcel();				
		$objPHPExcel->getProperties()->setCreator($cNama)
							 ->setLastModifiedBy($cNama)
							 ->setTitle($arrTitle[$s]);
				
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
		
		$cols=7;
		if(is_array($arrKomponen["t"])){
		  asort($arrKomponen["t"]);
		  reset($arrKomponen["t"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["t"])){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
			$cols++;
		  }
		}
		
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
		$cols++;
		
		if(is_array($arrKomponen["lm"])){
		  asort($arrKomponen["lm"]);
		  reset($arrKomponen["lm"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["lm"])){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
			$cols++;
		  }
		}		
		if(is_array($arrKomponen["p"])){
		  asort($arrKomponen["p"]);
		  reset($arrKomponen["p"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["p"])){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
			$cols++;
		  }
		}
		if(is_array($arrKomponen["pn"])){
		  asort($arrKomponen["pn"]);
		  reset($arrKomponen["pn"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["pn"])){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
			$cols++;
		  }
		}
		if(is_array($arrKomponen["ar"])){
		  asort($arrKomponen["ar"]);
		  reset($arrKomponen["ar"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["ar"])){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
			$cols++;
		  }
		}
		if(is_array($arrKomponen["em"])){
		  asort($arrKomponen["em"]);
		  reset($arrKomponen["em"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["em"])){
			$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);
			$cols++;
		  }
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension(numToAlpha($cols))->setWidth(20);		
				
		$objPHPExcel->getActiveSheet()->getRowDimension('4')->setRowHeight(25);		
		
		$objPHPExcel->getActiveSheet()->mergeCells('A1:'.numToAlpha($cols).'1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:'.numToAlpha($cols).'2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:'.numToAlpha($cols).'3');
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN REKAPITULASI GAJI KARYAWAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', getBulan($par[bulanProses])." ".$par[tahunProses]);
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:'.numToAlpha($cols).'4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A5:'.numToAlpha($cols).'5')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A4', 'NO.');
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'NPP');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', 'NAMA');
		$objPHPExcel->getActiveSheet()->setCellValue('D4', 'DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('E4', 'SUB DEPARTEMEN');
		$objPHPExcel->getActiveSheet()->setCellValue('F4', 'JABATAN');
		
		$objPHPExcel->getActiveSheet()->mergeCells('A4:A5');
		$objPHPExcel->getActiveSheet()->mergeCells('B4:B5');
		$objPHPExcel->getActiveSheet()->mergeCells('C4:C5');
		$objPHPExcel->getActiveSheet()->mergeCells('D4:D5');
		$objPHPExcel->getActiveSheet()->mergeCells('E4:E5');
		
		$cols=7;
		if(is_array($arrKomponen["t"])){
		  asort($arrKomponen["t"]);
		  reset($arrKomponen["t"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["t"])){
			list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', $namaKomponen);	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
			$cols++;
		  }
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TOTAL GAJI');	
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', strtoupper(getBulan($par[bulanProses])." ".$par[tahunProses]));	
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'KETERANGAN');	
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'USIA');	
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'REKENING');	
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TANGGAL MASUK');	
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TANGGAL LAHIR');	
		$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		$cols++;
		
		if(is_array($arrKomponen["lm"])){
		  asort($arrKomponen["lm"]);
		  reset($arrKomponen["lm"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["lm"])){
			list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', $namaKomponen);	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
			$cols++;
		  }
		}
		
		if(count($arrKomponen["p"]) > 0){
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'PEMOTONGAN');	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols+count($arrKomponen["p"])-1).'4');
			if(is_array($arrKomponen["p"])){
			  asort($arrKomponen["p"]);
			  reset($arrKomponen["p"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["p"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', $namaKomponen);				
				$cols++;
			  }
			}
		}
		
		if(count($arrKomponen["pn"]) > 0){
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'NON PROSES');	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols+count($arrKomponen["pn"])-1).'4');
			if(is_array($arrKomponen["pn"])){
			  asort($arrKomponen["pn"]);
			  reset($arrKomponen["pn"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["pn"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', $namaKomponen);				
				$cols++;
			  }
			}
		}
		
		if(count($arrKomponen["ar"]) > 0){
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'AR. DKM');	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols+count($arrKomponen["ar"])-1).'4');
			if(is_array($arrKomponen["ar"])){
			  asort($arrKomponen["ar"]);
			  reset($arrKomponen["ar"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["ar"])){
				list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'5', $namaKomponen);				
				$cols++;
			  }
			}
		}
		
		if(is_array($arrKomponen["em"])){
		  asort($arrKomponen["em"]);
		  reset($arrKomponen["em"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["em"])){
			list($urutanKomponen, $namaKomponen) = explode("\t", $valKomponen);
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', $namaKomponen);	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
			$cols++;
		  }
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).'4', 'TOTAL');	
			$objPHPExcel->getActiveSheet()->mergeCells(numToAlpha($cols).'4:'.numToAlpha($cols).'5');
		
		$rows = 6;
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		$sWhere= " where t1.id is not null and t1.status='".$status."' AND t1.group_id is not null and t1.status = '535'";		
		if(!empty($par[idLokasi]))
			$sWhere.= " and t1.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";	
		if(!empty($par[idJenis]))
			$sWhere.= " and t1.payroll_id='".$par[idJenis]."'";	
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				
				or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t1.pos_name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.namaData) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
		
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
				
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					if($r[cat] == 531 || !in_array($r[idKomponen], $pph21))
					$arrNilai["$r[idPegawai]"]["$r[idKomponen]"]=$r[nilaiProses];
				}
			}
		}
				
		$arrRekening =  arrayQuery("select parent_id, account_no from emp_bank where status='1'");
		$arrSub = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X06' order by urutanData");
		$sql="select t1.*, t2.namaData from dta_pegawai t1 left join mst_data t2 on (t1.div_id=t2.kodeData) $sWhere order by t1.name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){	
			$no++;
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$rows, $no);			
			$objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$rows, $r[reg_no], PHPExcel_Cell_DataType::TYPE_STRING);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$rows, strtoupper($r[name]));				
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$rows, $r[namaData]);			
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$rows, $arrSub["$r[dept_id]"]);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$rows, $r[pos_name]);
			
			$cols=7;
			$arrPenerimaan["$r[id]"] = 0;
			if(is_array($arrKomponen["t"])){
			  asort($arrKomponen["t"]);
			  reset($arrKomponen["t"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["t"])){				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrNilai["$r[id]"][$idKomponen]);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$cols++;
				
				$arrPenerimaan["$r[id]"]+=$arrNilai["$r[id]"][$idKomponen];
				$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
				$arrGaji["$r[id]"]+=$arrNilai["$r[id]"][$idKomponen];
				$totGaji+=$arrNilai["$r[id]"][$idKomponen];
			  }
			}			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrPenerimaan["$r[id]"]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$cols++;
			$cols++;
			
			$bulanPegawai = selisihBulan($r[birth_date], $par[tahunProses]."-".$par[bulanProses]."-01");
			$umurPegawai = floor($bulanPegawai / 12)." Thn ".($bulanPegawai % 12)." Bln";
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $umurPegawai);
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrRekening["$r[id]"], PHPExcel_Cell_DataType::TYPE_STRING);
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getTanggal($r[join_date]), PHPExcel_Cell_DataType::TYPE_STRING);
			$cols++;
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, getTanggal($r[birth_date]), PHPExcel_Cell_DataType::TYPE_STRING);
			$cols++;
			
			if(is_array($arrKomponen["lm"])){
			  asort($arrKomponen["lm"]);
			  reset($arrKomponen["lm"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["lm"])){				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrNilai["$r[id]"][$idKomponen]);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$cols++;
				
				$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
				$arrGaji["$r[id]"]+=$arrNilai["$r[id]"][$idKomponen];
				$totGaji+=$arrNilai["$r[id]"][$idKomponen];
			  }
			}	
			
			if(is_array($arrKomponen["p"])){
			  asort($arrKomponen["p"]);
			  reset($arrKomponen["p"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["p"])){				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrNilai["$r[id]"][$idKomponen]);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$cols++;
				
				$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
				$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
				$totGaji-=$arrNilai["$r[id]"][$idKomponen];
			  }
			}	
			
			if(is_array($arrKomponen["pn"])){
			  asort($arrKomponen["pn"]);
			  reset($arrKomponen["pn"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["pn"])){				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrNilai["$r[id]"][$idKomponen]);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$cols++;
				
				$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
				$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
				$totGaji-=$arrNilai["$r[id]"][$idKomponen];
			  }
			}	

			if(is_array($arrKomponen["ar"])){
			  asort($arrKomponen["ar"]);
			  reset($arrKomponen["ar"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["ar"])){				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrNilai["$r[id]"][$idKomponen]);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$cols++;
				
				$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
				$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
				$totGaji-=$arrNilai["$r[id]"][$idKomponen];
			  }
			}	
			
			if(is_array($arrKomponen["em"])){
			  asort($arrKomponen["em"]);
			  reset($arrKomponen["em"]);
			  while(list($idKomponen, $valKomponen) = each($arrKomponen["em"])){				
				$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrNilai["$r[id]"][$idKomponen]);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
				$cols++;
				
				$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
				$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
				$totGaji-=$arrNilai["$r[id]"][$idKomponen];
			  }
			}				
			
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrGaji["$r[id]"]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
			
			$rows++;			
		}
		
				
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rows)->getFont()->setBold(true);	
		$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$rows, 'TOTAL');
		
		$cols=7;
		$totPenerimaan = 0;
		if(is_array($arrKomponen["t"])){
		  asort($arrKomponen["t"]);
		  reset($arrKomponen["t"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["t"])){				
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrTotal[$idKomponen]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$totPenerimaan+=$arrTotal[$idKomponen];
			$cols++;
		  }
		}			
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $totPenerimaan);
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$cols++;
		$cols++;
		$cols++;		
		$cols++;		
		$cols++;				
		$cols++;
		
		if(is_array($arrKomponen["lm"])){
		  asort($arrKomponen["lm"]);
		  reset($arrKomponen["lm"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["lm"])){				
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrTotal[$idKomponen]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$cols++;
		  }
		}	
		
		if(is_array($arrKomponen["p"])){
		  asort($arrKomponen["p"]);
		  reset($arrKomponen["p"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["p"])){				
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrTotal[$idKomponen]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$cols++;
		  }
		}	
		
		if(is_array($arrKomponen["pn"])){
		  asort($arrKomponen["pn"]);
		  reset($arrKomponen["pn"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["pn"])){				
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrTotal[$idKomponen]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$cols++;
		  }
		}	

		if(is_array($arrKomponen["ar"])){
		  asort($arrKomponen["ar"]);
		  reset($arrKomponen["ar"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["ar"])){				
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrTotal[$idKomponen]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$cols++;
		  }
		}	
		
		if(is_array($arrKomponen["em"])){
		  asort($arrKomponen["em"]);
		  reset($arrKomponen["em"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["em"])){				
			$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $arrTotal[$idKomponen]);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
			$cols++;
		  }
		}				
		
		$objPHPExcel->getActiveSheet()->setCellValue(numToAlpha($cols).$rows, $totGaji);
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':'.numToAlpha($cols).$rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$rows.':'.numToAlpha($cols).$rows)->getNumberFormat()->setFormatCode('[>0]#,##0;[Red][<0]-#,##0;#,##0;');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rows.':'.numToAlpha($cols).$rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
		
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('A4:A'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('C4:C'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('D4:D'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E4:E'.$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$cols=7;
		if(is_array($arrKomponen["t"])){
		  asort($arrKomponen["t"]);
		  reset($arrKomponen["t"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["t"])){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		  }
		}
		
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$cols++;
		
		if(is_array($arrKomponen["lm"])){
		  asort($arrKomponen["lm"]);
		  reset($arrKomponen["lm"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["lm"])){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		  }
		}		
		if(is_array($arrKomponen["p"])){
		  asort($arrKomponen["p"]);
		  reset($arrKomponen["p"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["p"])){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		  }
		}
		if(is_array($arrKomponen["pn"])){
		  asort($arrKomponen["pn"]);
		  reset($arrKomponen["pn"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["pn"])){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		  }
		}
		if(is_array($arrKomponen["ar"])){
		  asort($arrKomponen["ar"]);
		  reset($arrKomponen["ar"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["ar"])){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		  }
		}
		if(is_array($arrKomponen["em"])){
		  asort($arrKomponen["em"]);
		  reset($arrKomponen["em"]);
		  while(list($idKomponen, $valKomponen) = each($arrKomponen["em"])){
			$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$cols++;
		  }
		}
		$objPHPExcel->getActiveSheet()->getStyle(numToAlpha($cols).'4:'.numToAlpha($cols).$rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getAlignment()->setWrapText(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.numToAlpha($cols).$rows)->getFont()->setName('Arial');
		$objPHPExcel->getActiveSheet()->getStyle('A6:'.numToAlpha($cols).$rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		
		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
		$objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);	
		
		$objPHPExcel->getActiveSheet()->setTitle(ucwords(strtolower("Laporan Rekapitulasi Gaji")));
		$objPHPExcel->setActiveSheetIndex(0);
		
		// Save Excel file
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($fFile.ucwords(strtolower("Laporan Rekapitulasi Gaji")).".xls");
	}
	
	function lData(){
		global $s,$par,$fFile,$menuAccess,$arrParameter,$arrKomponen, $areaCheck;				
		if(empty($par[bulanProses])) $par[bulanProses] = date('m');
		if(empty($par[tahunProses])) $par[tahunProses] = date('Y');						
		
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");
		
		$sWhere= " where t1.id is not null and t1.status='".$status."' AND t1.group_id is not null and t1.status = '535'";		
		if(!empty($par[idLokasi]))
			$sWhere.= " and t1.group_id='".$par[idLokasi]."'";
		if(!empty($par[divId]))
			$sWhere.= " and t1.div_id='".$par[divId]."'";
		if(!empty($par[deptId]))
			$sWhere.= " and t1.dept_id='".$par[deptId]."'";
		if(!empty($par[unitId]))
			$sWhere.= " and t1.unit_id='".$par[unitId]."'";	
		if(!empty($par[idJenis]))
			$sWhere.= " and t1.payroll_id='".$par[idJenis]."'";	
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (				
				lower(t1.name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				
				or lower(t1.reg_no) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t1.pos_name) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.namaData) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
		
		if(getField("select idProses from pay_proses where detailProses='pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)."'")){
			$slipLain = arrayQuery("select t1.idKomponen from dta_komponen t1 join app_menu t2 on (t1.kodeKomponen=t2.parameterMenu) where t2.targetMenu='payroll/pay_slip_lain'");
			$pph21 = arrayQuery("select idKomponen from dta_komponen where flagKomponen='4'");
				
			$sql="select * from pay_proses_".$par[tahunProses].str_pad($par[bulanProses], 2, "0", STR_PAD_LEFT)." t1 join dta_komponen t2 join emp t3 on (t1.idKomponen=t2.idKomponen and t1.idPegawai=t3.id) order by idDetail";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				if(!in_array($r[idKomponen], array(165)) && !in_array($r[idKomponen], $slipLain)){
					if($r[cat] == 531 || !in_array($r[idKomponen], $pph21))
					$arrNilai["$r[idPegawai]"]["$r[idKomponen]"]=$r[nilaiProses];
				}
			}
		}
		
		$arrRekening =  arrayQuery("select parent_id, account_no from emp_bank where status='1'");
		$arrSub = arrayQuery("select kodeData, namaData from mst_data where statusData='t' and kodeCategory='X06' order by urutanData");
		$sql="select t1.*, t2.namaData from dta_pegawai t1 left join mst_data t2 on (t1.div_id=t2.kodeData) $sWhere order by t1.name";
		$res=db($sql);		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_pegawai t1 left join mst_data t2 on (t1.div_id=t2.kodeData) $sWhere"),
			"aaData" => array(),
		);
				
		while($r=mysql_fetch_array($res)){	
			if(is_array($arrNilai["$r[id]"])){
				$no++;
				
				$data=array();
				$data[]="<div align=\"center\">".$no.".</div>";
				$data[]="<div align=\"left\">".$r[reg_no]."</div>";
				$data[]="<div align=\"left\">".strtoupper($r[name])."</div>";
				$data[]="<div align=\"left\">".$r[namaData]."</div>";
				$data[]="<div align=\"left\">".$arrSub["$r[dept_id]"]."</div>";
				$data[]="<div align=\"left\">".$r[pos_name]."</div>";
				
				
				$arrPenerimaan["$r[id]"] = 0;
				if(is_array($arrKomponen["t"])){
				  asort($arrKomponen["t"]);
				  reset($arrKomponen["t"]);
				  while(list($idKomponen) = each($arrKomponen["t"])){
					$data[]="<div align=\"right\">".getAngka($arrNilai["$r[id]"][$idKomponen])."</div>";				
					$arrPenerimaan["$r[id]"]+=$arrNilai["$r[id]"][$idKomponen];				
					$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
					$arrGaji["$r[id]"]+=$arrNilai["$r[id]"][$idKomponen];
					$totGaji+=$arrNilai["$r[id]"][$idKomponen];
				  }
				}
				$bulanPegawai = selisihBulan($r[birth_date], $par[tahunProses]."-".$par[bulanProses]."-01");
				$umurPegawai = floor($bulanPegawai / 12)." Thn ".($bulanPegawai % 12)." Bln";
				
				$data[]="<div align=\"right\">".getAngka($arrPenerimaan["$r[id]"])."</div>";
				$data[]="<div align=\"right\"></div>";
				$data[]="<div align=\"right\">".$umurPegawai."</div>";
				$data[]="<div align=\"left\">".$arrRekening["$r[id]"]."</div>";
				$data[]="<div align=\"center\">".getTanggal($r[join_date])."</div>";
				$data[]="<div align=\"center\">".getTanggal($r[birth_date])."</div>";
				
				if(is_array($arrKomponen["lm"])){
				  asort($arrKomponen["lm"]);
				  reset($arrKomponen["lm"]);
				  while(list($idKomponen) = each($arrKomponen["lm"])){
					$data[]="<div align=\"right\">".getAngka($arrNilai["$r[id]"][$idKomponen])."</div>";				
					$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
					$arrGaji["$r[id]"]+=$arrNilai["$r[id]"][$idKomponen];
					$totGaji+=$arrNilai["$r[id]"][$idKomponen];
				  }
				}
				
				if(is_array($arrKomponen["p"])){
				  asort($arrKomponen["p"]);
				  reset($arrKomponen["p"]);
				  while(list($idKomponen) = each($arrKomponen["p"])){
					$data[]="<div align=\"right\">".getAngka($arrNilai["$r[id]"][$idKomponen])."</div>";
					$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
					$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
					$totGaji-=$arrNilai["$r[id]"][$idKomponen];
				  }
				}
				
				if(is_array($arrKomponen["pn"])){
				  asort($arrKomponen["pn"]);
				  reset($arrKomponen["pn"]);
				  while(list($idKomponen) = each($arrKomponen["pn"])){
					$data[]="<div align=\"right\">".getAngka($arrNilai["$r[id]"][$idKomponen])."</div>";				
					$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
					$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
					$totGaji-=$arrNilai["$r[id]"][$idKomponen];
				  }
				}
				
				if(is_array($arrKomponen["ar"])){
				  asort($arrKomponen["ar"]);
				  reset($arrKomponen["ar"]);
				  while(list($idKomponen) = each($arrKomponen["ar"])){
					$data[]="<div align=\"right\">".getAngka($arrNilai["$r[id]"][$idKomponen])."</div>";				
					$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
					$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
					$totGaji-=$arrNilai["$r[id]"][$idKomponen];
				  }
				}	
				if(is_array($arrKomponen["em"])){
				  asort($arrKomponen["em"]);
				  reset($arrKomponen["em"]);
				  while(list($idKomponen) = each($arrKomponen["em"])){
					$data[]="<div align=\"right\">".getAngka($arrNilai["$r[id]"][$idKomponen])."</div>";		
					$arrTotal[$idKomponen]+=$arrNilai["$r[id]"][$idKomponen];
					$arrGaji["$r[id]"]-=$arrNilai["$r[id]"][$idKomponen];
					$totGaji-=$arrNilai["$r[id]"][$idKomponen];
				  }
				}
				
				$data[]="<div align=\"right\">".getAngka($arrGaji["$r[id]"])."</div>";
				
				$json['aaData'][]=$data;
			}
		}
		
		$data=array();
		$data[]="<div align=\"center\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\"><strong>TOTAL</strong></div>";
		
		$totPenerimaan = 0;
		if(is_array($arrKomponen["t"])){
		  asort($arrKomponen["t"]);
		  reset($arrKomponen["t"]);
		  while(list($idKomponen) = each($arrKomponen["t"])){
			$data[]="<div align=\"right\">".getAngka($arrTotal[$idKomponen])."</div>";				
			$totPenerimaan+=$arrTotal[$idKomponen];
		  }
		}
		$bulanPegawai = selisihBulan($r[birth_date], $par[tahunProses]."-".$par[bulanProses]."-01");
		$umurPegawai = floor($bulanPegawai / 12)." Thn ".($bulanPegawai % 12)." Bln";
		
		$data[]="<div align=\"right\">".getAngka($arrPenerimaan["$r[id]"])."</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		$data[]="<div align=\"left\">&nbsp;</div>";
		
		if(is_array($arrKomponen["lm"])){
		  asort($arrKomponen["lm"]);
		  reset($arrKomponen["lm"]);
		  while(list($idKomponen) = each($arrKomponen["lm"])){
			$data[]="<div align=\"right\">".getAngka($arrTotal[$idKomponen])."</div>";				
		  }
		}
		
		if(is_array($arrKomponen["p"])){
		  asort($arrKomponen["p"]);
		  reset($arrKomponen["p"]);
		  while(list($idKomponen) = each($arrKomponen["p"])){
			$data[]="<div align=\"right\">".getAngka($arrTotal[$idKomponen])."</div>";				
		  }
		}
		
		if(is_array($arrKomponen["pn"])){
		  asort($arrKomponen["pn"]);
		  reset($arrKomponen["pn"]);
		  while(list($idKomponen) = each($arrKomponen["pn"])){
			$data[]="<div align=\"right\">".getAngka($arrTotal[$idKomponen])."</div>";				
		  }
		}
		
		if(is_array($arrKomponen["ar"])){
		  asort($arrKomponen["ar"]);
		  reset($arrKomponen["ar"]);
		  while(list($idKomponen) = each($arrKomponen["ar"])){
			$data[]="<div align=\"right\">".getAngka($arrTotal[$idKomponen])."</div>";				
		  }
		}	
		if(is_array($arrKomponen["em"])){
		  asort($arrKomponen["em"]);
		  reset($arrKomponen["em"]);
		  while(list($idKomponen) = each($arrKomponen["em"])){
			$data[]="<div align=\"right\">".getAngka($arrTotal[$idKomponen])."</div>";				
		  }
		}
		
		$data[]="<div align=\"right\">".getAngka($totGaji)."</div>";
		
		$json['aaData'][]=$data;
		
		return json_encode($json);
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){
			case "lst":
				$text = lData();
			break;
			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>
