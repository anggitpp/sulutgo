<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";	
	
	function hapus(){
		global $db,$s,$inp,$par,$cUsername;							
		$sql="delete from plt_pertanyaan where idPertanyaan='$par[idPertanyaan]'";
		db($sql);
		$sql="delete from plt_pertanyaan_jawaban where idPertanyaan='$par[idPertanyaan]'";
		db($sql);
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idPertanyaan")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$det,$detail,$cUsername;
		repField();			
		
		$sql="update plt_pertanyaan set idKategori='$inp[idKategori]', detailPertanyaan='$inp[detailPertanyaan]', tipePertanyaan='$inp[tipePertanyaan]', statusPertanyaan='$inp[statusPertanyaan]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idPertanyaan='$par[idPertanyaan]'";
		db($sql);		
		
		db("delete from plt_pertanyaan_jawaban where idPertanyaan='$par[idPertanyaan]'");		
		if(in_array($inp[tipePertanyaan], array("a", "i"))){
			$sql="insert into plt_pertanyaan_jawaban (idPertanyaan, keteranganJawaban, bobotJawaban, createBy, createTime) values ('$par[idPertanyaan]', '$det[keteranganJawaban]', '".setAngka($det[bobotJawaban])."', '$cUsername', '".date('Y-m-d H:i:s')."')";				
			db($sql);
		}else{
			if(is_array($detail)){
				ksort($detail);
				reset($detail);			
				while(list($idJawaban,$valJawaban)=each($detail)){
					list($detailJawaban, $keteranganJawaban, $bobotJawaban) = explode("\t", $valJawaban);				
					$sql="insert into plt_pertanyaan_jawaban (idPertanyaan, idJawaban, detailJawaban, keteranganJawaban, bobotJawaban, createBy, createTime) values ('$par[idPertanyaan]', '$idJawaban', '$detailJawaban', '$keteranganJawaban', '".setAngka($bobotJawaban)."', '$cUsername', '".date('Y-m-d H:i:s')."')";				
					db($sql);
				}
			}
		}
		
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idPertanyaan")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$det,$detail,$cUsername;	
		repField();
		$idPertanyaan = getField("select idPertanyaan from plt_pertanyaan order by idPertanyaan desc limit 1")+1;		
		
		$sql="insert into plt_pertanyaan (idPertanyaan, idEvaluasi, idKategori, detailPertanyaan, tipePertanyaan, statusPertanyaan, createBy, createTime) values ('$idPertanyaan', '$par[idEvaluasi]', '$inp[idKategori]', '$inp[detailPertanyaan]', '$inp[tipePertanyaan]', '$inp[statusPertanyaan]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);
		
		if(in_array($inp[tipePertanyaan], array("a", "i"))){
			$sql="insert into plt_pertanyaan_jawaban (idPertanyaan, keteranganJawaban, bobotJawaban, createBy, createTime) values ('$idPertanyaan', '$det[keteranganJawaban]', '".setAngka($det[bobotJawaban])."', '$cUsername', '".date('Y-m-d H:i:s')."')";				
			db($sql);
		}else{
			if(is_array($detail)){
				ksort($detail);
				reset($detail);			
				while(list($idJawaban,$valJawaban)=each($detail)){
					list($detailJawaban, $keteranganJawaban, $bobotJawaban) = explode("\t", $valJawaban);				
					$sql="insert into plt_pertanyaan_jawaban (idPertanyaan, idJawaban, detailJawaban, keteranganJawaban, bobotJawaban, createBy, createTime) values ('$idPertanyaan', '$idJawaban', '$detailJawaban', '$keteranganJawaban', '".setAngka($bobotJawaban)."', '$cUsername', '".date('Y-m-d H:i:s')."')";
					db($sql);
				}
			}
		}
				
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idPertanyaan")."';</script>";
	}
	
	function form(){
		global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
		$sql="select * from plt_pertanyaan where idPertanyaan='$par[idPertanyaan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		if(empty($r[idPertanyaan])) $r[idPertanyaan] = getField("select idPertanyaan from plt_pertanyaan order by idPertanyaan desc limit 1")+1;
		if(empty($r[idKategori])) $r[idKategori] = $par[idKategori];		
		
		if(!is_array($detail)) $detail=arrayQuery("select idJawaban,concat(detailJawaban, '\t', keteranganJawaban, '\t', bobotJawaban) from plt_pertanyaan_jawaban where idPertanyaan='$par[idPertanyaan]'");
		
		
		$r[detailPertanyaan] = empty($inp[detailPertanyaan]) ? $r[detailPertanyaan] : $inp[detailPertanyaan];
		$r[idKategori] = empty($inp[idKategori]) ? $r[idKategori] : $inp[idKategori];
		$r[tipePertanyaan] = empty($inp[tipePertanyaan]) ? $r[tipePertanyaan] : $inp[tipePertanyaan];
		$r[statusPertanyaan] = empty($inp[statusPertanyaan]) ? $r[statusPertanyaan] : $inp[statusPertanyaan];		
		
		$false =  $r[statusPertanyaan] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";
		
		$area = $r[tipePertanyaan] == "a" ? "checked=\"checked\"" : "";
		$check =  $r[tipePertanyaan] == "c" ? "checked=\"checked\"" : "";		
		$input =  $r[tipePertanyaan] == "i" ? "checked=\"checked\"" : "";
		$radio =  (empty($area) && empty($check) && empty($input)) ? "checked=\"checked\"" : "";
		
		$titleJawaban = "PILIHAN GANDA";
		if($r[tipePertanyaan] == "c") $titleJawaban = "CHECKBOX";
		if($r[tipePertanyaan] == "a") $titleJawaban = "TEXTAREA";
		if($r[tipePertanyaan] == "i") $titleJawaban = "FREE INPUT";
		
		$cat = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[5]."' order by urutanData limit 1");
		$status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");		
		
		if(empty($fileTemp))
		$deleteFile = "<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>";
		
		setValidation("is_null","inp[detailPertanyaan]","anda harus mengisi pertanyaan");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");
		$text = getValidation();

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?".getPar($par)."#detail\" enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Jenis</label>
						<span class=\"field\" style=\"margin-left:150px; border:0px;\">								
							".getField("select namaEvaluasi from dta_evaluasi where idEvaluasi='".$par[idEvaluasi]."'")."
						</span>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Pertanyaan</label>
						<div class=\"field\" style=\"margin-left:150px;\">								
							<input type=\"text\" id=\"inp[detailPertanyaan]\" name=\"inp[detailPertanyaan]\"  value=\"$r[detailPertanyaan]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Kategori</label>
						<div class=\"field\" style=\"margin-left:150px;\">								
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[50]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "360px","chosen-select")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Tipe Pertanyaan</label>
						<div class=\"fradio\" style=\"margin-left:150px;\">								
							<input type=\"radio\" id=\"radio\" name=\"inp[tipePertanyaan]\" value=\"r\" onclick=\"document.form.submit();\" $radio /> <span class=\"sradio\">Pilihan Ganda</span>
							<input type=\"radio\" id=\"area\" name=\"inp[tipePertanyaan]\" value=\"a\" onclick=\"document.form.submit();\" $area /> <span class=\"sradio\">Textarea</span><br>
							<input type=\"radio\" id=\"check\" name=\"inp[tipePertanyaan]\" value=\"c\" onclick=\"document.form.submit();\" $check /> <span class=\"sradio\" style=\"margin-right:44px;\">Checkbox</span>							
							<input type=\"radio\" id=\"input\" name=\"inp[tipePertanyaan]\" value=\"i\" onclick=\"document.form.submit();\" $input /> <span class=\"sradio\">Free Input</span>
						</div>
					</p>	
					<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Status</label>
						<div class=\"fradio\" style=\"margin-left:150px;\">								
							<input type=\"radio\" id=\"true\" name=\"inp[statusPertanyaan]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusPertanyaan]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>
						</div>
					</p>
					<br clear=\"all\">					
					<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
					<legend style=\"padding:10px; margin-left:20px;\"><h4>".$titleJawaban."</h4></legend>";
			

			if(in_array($r[tipePertanyaan], array("a", "i"))){
				$sql="select * from plt_pertanyaan_jawaban where idPertanyaan='$par[idPertanyaan]'";
				$res=db($sql);
				$r=mysql_fetch_array($res);
				
				$text.="<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Keterangan</label>
						<div class=\"field\" style=\"margin-left:150px;\">								
							<input type=\"text\" id=\"det[keteranganJawaban]\" name=\"det[keteranganJawaban]\"  value=\"$r[keteranganJawaban]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\" />
						</div>
					</p>
					<p>
						<label class=\"l-input-small\" style=\"width:150px;\">Bobot</label>
						<div class=\"field\" style=\"margin-left:150px;\">								
							<input type=\"text\" id=\"det[bobotJawaban]\" name=\"det[bobotJawaban]\"  value=\"".getAngka($r[bobotJawaban])."\" class=\"mediuminput\" style=\"width:100px; text-align:right;\" onkeyup=\"cekAngka(this);\"/>
						</div>
					</p>";
			}else{
				$text.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th>Jawaban</th>
								<th>Keterangan</th>
								<th width=\"100\">Bobot</th>
								<th width=\"75\">Control</th>
							</tr>
						</thead>
						<tbody>";				
									
										
					$detail=is_array($detail)?$detail:array();
					if(!empty($det[detailJawaban]) && !empty($inp[save_detail])){
						unset($detail["$inp[tempDetail]"]);
						if(!empty($det[detailJawaban])) $detail["$det[idJawaban]"]=$det[detailJawaban]."\t".$det[keteranganJawaban]."\t".$det[bobotJawaban];
					}
					unset($detail["$inp[delete_detail]"]);										
					
					$no=1;
					$dta = array();
					if(is_array($detail)){
						ksort($detail);
						reset($detail);
						while(list($idJawaban,$valDetail)=each($detail)){
							$dta[$no]= $valDetail;
							$no++;
						}
					}
					$detail = $dta;
					
					$no=1;
					if(is_array($detail)){
						ksort($detail);
						reset($detail);
						while(list($idJawaban,$valDetail)=each($detail)){					
							list($detailJawaban, $keteranganJawaban, $bobotJawaban) = explode("\t", $valDetail);
							
							$text.="<input type=\"hidden\" id=\"detail[".$idJawaban."]\" name=\"detail[".$idJawaban."]\"  value=\"".$valDetail."\">";
							$text.=$inp[edit_detail] == $idJawaban ?
							"<tr>
								<td><input type=\"hidden\" id=\"det[idJawaban]\" name=\"det[idJawaban]\"  value=\"$no\">$no.</td>
								<td><input type=\"text\" id=\"det[detailJawaban]\" name=\"det[detailJawaban]\" value=\"$detailJawaban\"  class=\"mediuminput\" maxlength=\"150\" style=\"width:98%\" /></td>
								<td><input type=\"text\" id=\"det[keteranganJawaban]\" name=\"det[keteranganJawaban]\" value=\"$keteranganJawaban\" class=\"mediuminput\" maxlength=\"150\" style=\"width:98%\" /></td>
								<td align=\"center\"><input type=\"text\" id=\"det[bobotJawaban]\" name=\"det[bobotJawaban]\"  value=\"".getAngka($bobotJawaban)."\" class=\"mediuminput\" style=\"width:90%; text-align:right;\" onkeyup=\"cekAngka(this);\" /></td>
								<td><input type=\"submit\" class=\"add\" name=\"inp[save_detail]\" value=\"Simpan\" style=\"float:right\" onclick=\"return validation(document.form)\"/></td>
							</tr>":
							"<tr>
							<td>$no.</td>
							<td>$detailJawaban</td>
							<td>$keteranganJawaban</td>
							<td align=\"right\">".getAngka($bobotJawaban)."</td>
							<td align=\"center\">
								<input type=\"submit\" class=\"edit\" name=\"inp[edit_detail]\" value=\"$idJawaban\"/>
								<input type=\"submit\" class=\"delete\" name=\"inp[delete_detail]\" value=\"$idJawaban\"/>
							</td>
							</tr>";							
							$no++;
						}
					}
					
					if(empty($inp[edit_detail]))
					$text.="<tr>
						<td><input type=\"hidden\" id=\"det[idJawaban]\" name=\"det[idJawaban]\"  value=\"$no\">$no.</td>
						<td><input type=\"text\" id=\"det[detailJawaban]\" name=\"det[detailJawaban]\"  class=\"mediuminput\" maxlength=\"150\" style=\"width:98%\" /></td>
						<td><input type=\"text\" id=\"det[keteranganJawaban]\" name=\"det[keteranganJawaban]\"  class=\"mediuminput\" maxlength=\"150\" style=\"width:98%\" /></td>
						<td align=\"center\"><input type=\"text\" id=\"det[bobotJawaban]\" name=\"det[bobotJawaban]\"  class=\"mediuminput\" style=\"width:90%; text-align:right;\" onkeyup=\"cekAngka(this);\" /></td>
						<td><input type=\"submit\" class=\"add\" name=\"inp[save_detail]\" value=\"Simpan\" style=\"float:right\" onclick=\"return validation(document.form)\"/></td>
					</tr>";
					
					$text.="</tbody>
				</table>";
			}
				
		$text.="</fieldset>
				</div>
				<p>					
					<input type=\"hidden\" id=\"_submit\" name=\"_submit\"  value=\"\">					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\" onclick=\"return chk('".getPar($par,"mode")."');\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode,idPertanyaan")."';\"/>					
				</p>
			</form>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$det,$detail,$arrTitle,$arrParameter,$fileTemp,$fFile,$menuAccess;				
		$sql="select * from dta_evaluasi where idEvaluasi='$par[idEvaluasi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);	

  	$cols = 6;		

		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;

		$text = table($cols, array(($cols-2),($cols-1),$cols));

		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">				
				<div style=\"top:10px; right:35px; position:absolute\">
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode, idEvaluasi")."';\"/>
				</div>
				<div id=\"general\" style=\"margin-top:20px;\">	
									<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
				
					<legend style=\"padding:10px; margin-left:20px;\"><h4>EVALUASI</h4></legend>
						<form class=\"stdform\">
						<table style=\"width:100%\">
						<tr>
						<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Jenis</label>
							<span class=\"field\" style=\"margin-left:150px;\">$r[namaEvaluasi]&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Sub</label>
							<span class=\"field\" style=\"margin-left:150px;\">$r[subEvaluasi]&nbsp;</span>
						</p>
						</td>
						<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Tujuan</label>
							<span class=\"field\" style=\"margin-left:150px;\">$r[tujuanEvaluasi]&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\" style=\"width:150px;\">Metode</label>
							<span class=\"field\" style=\"margin-left:150px;\">$r[metodeEvaluasi]&nbsp;</span>
						</p>
						</td>
						</tr>
						</table>
						</form>
						</fieldset>
					<legend style=\"padding:10px; border-bottom:1px solid #c0c0c0;\"><h4>PERTANYAAN</h4></legend>

			<form action=\"\" method=\"post\" id = \"form\" class=\"stdform\" onsubmit=\"return false;\">
					<div id=\"pos_l\" style=\"position:absolute; left:0; margin-left:30px;\">
						<p>					

				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$fSearch."\" style=\"width:130px;\"/>
				".comboData("SELECT * from mst_data where statusData='t' and kodeCategory='PL06'", "kodeData", "namaData", "bSearch", "All", "$bSearch", "", "250px","chosen-select");
				$text.="&nbsp;&nbsp;
				

			</p>
					</div>
					<div id=\"pos_r\" style=\"position:absolute; right:0; margin-right:35px;\">";
					if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idPertanyaan")."\" class=\"btn btn1 btn_document\" ><span>Tambah Data</span></a>";
					$text.="</div>			
					</form>
					<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\" style=\"margin-top:64px;\">
						<thead>
							<tr>
								<th width=\"20\">No.</th>
								<th>Pertanyaan</th>
								<th width=\"200\">Kategori</th>
								<th width=\"125\">Tipe Pertanyaan</th>
								<th width=\"50\">Status</th>";
					if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
					$text.="</tr>
						</thead>
				<tbody></tbody>
				
				</table>
				</div>";
		return $text;
	}



    function lData(){

      global $s,$par,$fRencana,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m,$fRencana;  
        // global $s,$inp,$par,$arrTitle,$fFile,$menuAccess,$cUsername,$sUser;  
   

      if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')

        $sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
      // echo $sLimit;

   $arrTipe = array("r" => "Pilihan Ganda", "c" => "Checkbox", "a" => "Textarea", "i" => "Free Input");

				

						if (!empty($_GET['fSearch']))

			$filter= " and (				

				lower(t1.detailPertanyaan) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'				

				or lower(t2.namaData) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'


			)";



	if (!empty($_GET['bSearch']))

			$filter= " and t1.idKategori = ".$_GET['bSearch']."";



      $arrOrder = array(  
        "t1.detailPertanyaan",
        "",
        "",
        "",
        "",
        "",
        );




      $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];

		$sql="select t1.*, t2.namaData from plt_pertanyaan t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.idEvaluasi='$par[idEvaluasi]' $filter order by t1.idPertanyaan $sLimit";
        // echo $sql;

      $res=db($sql);



      $json = array(

        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("select count(*) from plt_pertanyaan t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.idEvaluasi='$par[idEvaluasi]' ".$filter.""),
        "aaData" => array(),
        );



      $no=intval($_GET['iDisplayStart']);
      while($r=mysql_fetch_array($res)){
        $no++;
            
		$controlShift="";
			if(isset($menuAccess[$s]["edit"])) 		

				$controlShift.="<a href=\"?par[mode]=edit&par[idPertanyaan]=$r[idPertanyaan]".getPar($par,"mode,idPertanyaan")."\" title=\"Edit Data\" class=\"edit\" ><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"])) 		
				$controlShift.="<a href=\"?par[mode]=del&par[idPertanyaan]=$r[idPertanyaan]".getPar($par,"mode,idPertanyaan")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
      
       
         $data=array(						
				"<div align=\"center\">".$no."</div>",
				"<div align=\"left\" style=\"font-size:12px;\">".$r[detailPertanyaan]."</a></div>",
				"<div align=\"center\">".$r[namaData]."</div>",
				"<div align=\"center\">".$arrTipe["$r[tipePertanyaan]"]."</div>",
				"<div align=\"center\">".$statusPertanyaan."</div>",
				"<div align=\"center\">".$controlShift."</div>",
			);




        $json['aaData'][]=$data;


      }

      if($par[mode] == "xls"){
        xls();      
        $text.="<iframe src=\"download.php?d=exp&f=DATA DOKUMEN ".$sekarang.".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
      }

      return json_encode($json);

    }


	function lihat(){
		global $db,$s,$inp,$par,$_submit,$arrTitle,$arrParameter,$menuAccess;		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."
				</div>    
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<div style=\"padding-bottom:10px;\">
			</div>
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<input type=\"hidden\" name=\"_submit\" value=\"t\">			
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" placeholder=\"Search\" /></td>				
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
					<th>Jenis</th>					
					<th>Tujuan</th>
					<th>Metode</th>
					<th style=\"width:75px;\">Jumlah</th>
					<th style=\"width:150px;\">Update</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where statusEvaluasi='t'";
		if(!empty($par[filter]))			
		$filter.= " and (
			lower(namaEvaluasi) like '%".strtolower($par[filter])."%'
			or lower(subEvaluasi) like '%".strtolower($par[filter])."%'
			or lower(tujuanEvaluasi) like '%".strtolower($par[filter])."%'
			or lower(metodeEvaluasi) like '%".strtolower($par[filter])."%'
		)";
		
		$sql="select * from dta_evaluasi $filter order by idEvaluasi";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){					
			$no++;			
			list($cntPertanyaan, $updateTime) = explode("\t", getField("select concat(count(idPertanyaan), '\t', max(createTime)) from plt_pertanyaan where idEvaluasi='$r[idEvaluasi]'"));
			
			if(empty($updateTime)) $updateTime = $r[createTime];
			if(empty($cntPertanyaan)) $cntPertanyaan = 0;
			
			list($tanggalUpdate, $waktuUpdate) = explode(" ", $updateTime);
			$text.="<tr>
					<td>$no.</td>			
					<td><a href=\"?par[mode]=det&par[idEvaluasi]=$r[idEvaluasi]".getPar($par,"mode,idEvaluasi,filter")."\">$r[namaEvaluasi]</a></td>					
					<td>$r[tujuanEvaluasi]</td>
					<td>$r[metodeEvaluasi]</td>
					<td align=\"center\">".getAngka($cntPertanyaan)."</td>
					<td align=\"center\">".getTanggal($tanggalUpdate)." ".substr($waktuUpdate,0,5)."</td>					
					</tr>";			
		}	
		
		$text.="</tbody>
			</table>
			</div>";			
		return $text;
	}

	
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess;
		switch($par[mode]){						
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;							
			case "add":
				if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : tambah(); else $text = lihat();
			break;
			case "lst":
				$text=lData();
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