<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	
	function hapus(){
		global $s,$inp,$par,$cUsername;				
		$sql="delete from dta_libur where idLibur='$par[idLibur]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=det".getPar($par,"mode,idLibur")."';</script>";
	}
		
	function ubah(){
		global $s,$inp,$par,$cUsername;
		repField();				
		$sql="update dta_libur set idKategori='$inp[idKategori]', namaLibur='$inp[namaLibur]', mulaiLibur='".setTanggal($inp[mulaiLibur])."', selesaiLibur='".setTanggal($inp[selesaiLibur])."', keteranganLibur='$inp[keteranganLibur]', statusLibur='$inp[statusLibur]', updateBy='$cUsername', updateTime='".date('Y-m-d H:i:s')."' where idLibur='$par[idLibur]'";
		db($sql);
		
		echo "<script>closeBox();reloadPage();</script>";
	}
	
	function tambah(){
		global $s,$inp,$par,$cUsername;				
		repField();		
		$idLibur = getField("select idLibur from dta_libur order by idLibur desc limit 1")+1;		
		
		$sql="insert into dta_libur (idLibur, idKategori, namaLibur, mulaiLibur, selesaiLibur, keteranganLibur, statusLibur, createBy, createTime) values ('$idLibur', '$inp[idKategori]', '$inp[namaLibur]', '".setTanggal($inp[mulaiLibur])."', '".setTanggal($inp[selesaiLibur])."', '$inp[keteranganLibur]', '$inp[statusLibur]', '$cUsername', '".date('Y-m-d H:i:s')."')";
		db($sql);		
		
		echo "<script>closeBox();reloadPage();</script>";
	}
		
	function form(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		
		$sql="select * from dta_libur where idLibur='$par[idLibur]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
				
		$false =  $r[statusLibur] == "f" ? "checked=\"checked\"" : "";		
		$true =  empty($false) ? "checked=\"checked\"" : "";		

		setValidation("is_null","inp[namaLibur]","anda harus mengisi judul");
		setValidation("is_null","inp[idKategori]","anda harus mengisi kategori");
		setValidation("is_null","mulaiLibur","anda harus mengisi tanggal mulai");
		setValidation("is_null","selesaiLibur","anda harus mengisi tanggal selesai");
		$text = getValidation();

		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">										
					<p>
						<label class=\"l-input-small\">Judul</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaLibur]\" name=\"inp[namaLibur]\"  value=\"$r[namaLibur]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Kategori</label>
						<div class=\"field\">
							".comboData("select * from mst_data where statusData='t' and kodeCategory='".$arrParameter[15]."' order by urutanData","kodeData","namaData","inp[idKategori]"," ",$r[idKategori],"", "410px")."
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Tanggal</label>
						<div class=\"field\">
							<input type=\"text\" id=\"mulaiLibur\" name=\"inp[mulaiLibur]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[mulaiLibur])."\" class=\"vsmallinput hasDatePicker\"/> s.d  
							<input type=\"text\" id=\"selesaiLibur\" name=\"inp[selesaiLibur]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[selesaiLibur])."\" class=\"vsmallinput hasDatePicker\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganLibur]\" name=\"inp[keteranganLibur]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[keteranganLibur]</textarea>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusLibur]\" value=\"t\" $true /> <span class=\"sradio\">Aktif</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusLibur]\" value=\"f\" $false /> <span class=\"sradio\">Tidak Aktif</span>
						</div>
					</p>
				</div>				
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"closeBox();\"/>
				</p>
			</form>	
			</div>";
		return $text;
	}

	function detail(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;
		if(empty($par[tahunLibur])) $par[tahunLibur] = date('Y');
		
		$cols = 7;
		$cols = (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) ? $cols : $cols-1;
		$text = table($cols, array($cols-1, $cols));
		
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
			<div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">			
			Tahun : ".comboYear("par[tahunLibur]", $par[tahunLibur], "", "onchange=\"document.getElementById('form').submit();\"")."			
			".setPar($par,"tahunLibur")."
			</div>
			</form>						
			<form action=\"\" method=\"post\" class=\"stdform\" onsubmit=\"return false;\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>					
				<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\"/>
			</p>
			</div>
			<div id=\"pos_r\">
			<a href=\"?".getPar($par,"mode,idLibur")."\" class=\"btn btn1 btn_calendar\" ><span>Calendar</span></a> ";
		if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idLibur")."',875,450);\"><span>Tambah Data</span></a>";
		$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th width=\"150\">Tanggal</th>
					<th style=\"min-width:150px;\">Judul</th>										
					<th style=\"min-width:150px;\">Kategori</th>
					<th style=\"min-width:150px;\">Keterangan</th>
					<th width=\"50\">Status</th>";
				if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text.="<th width=\"50\">Kontrol</th>";
		$text.="</tr>
			</thead>
			<tbody></tbody>
			</table>
			</div>";
		return $text;
	}		
	
	function lihat(){
		global $s,$inp,$par,$arrTitle,$arrParameter,$menuAccess;		
		
		if(strlen($par[bulanLibur]) < 1)$par[bulanLibur] = date('n')-1;
		if(strlen($par[tahunLibur]) < 1)$par[tahunLibur] = date('Y');		
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread()."					
				</div>
				<br clear=\"all\">
				<div id=\"contentwrapper\" class=\"contentwrapper\">
					<div class=\"one_half last dashboard_left\">";			
								
				$arr = explode("/",$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
				$url="https://";
				for($i=0; $i<(count($arr)-1); $i++){
					$url.=$arr[$i]."/";
				}
				$url.="calendar/jwl_libur.php?".getPar($par)."";
				$arrData = json_decode(file_get_contents($url), true);

				$text.="<script type=\"text/javascript\" src=\"scripts/calendar.js\"></script>
						<script type=\"text/javascript\">
						jQuery(function () {								
							jQuery('#calendar').fullCalendar({
								month: $par[bulanLibur],
								year: $par[tahunLibur],
							
								buttonText: {
									prev: '&laquo;',
									next: '&raquo;',
									prevYear: '&nbsp;&lt;&lt;&nbsp;',
									nextYear: '&nbsp;&gt;&gt;&nbsp;',
									today: 'today',
									month: 'month',
									week: 'week',
									day: 'day'
								},

								header: {
									left: 'title',
									right: 'prev,next',															
								},
								
								events: {
									url: '$url',
									cache: true
								},								
																
								eventMouseover: function(calEvent, jsEvent) {									
									var tooltip = '<div class=\"tooltipevent\" style=\"padding:0 5px; position:absolute; z-index:10000; font-size:10px; background:#fff; color:#666; border:solid 1px #ccc; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;\">' + calEvent.data.namaLibur +'</div>';
									
									jQuery(\"body\").append(tooltip);
									jQuery(this).mouseover(function(e) {
										jQuery(this).css('z-index', 10000);
										jQuery('.tooltipevent').fadeIn('500');
										jQuery('.tooltipevent').fadeTo('10', 1.9);
									}).mousemove(function(e) {
										jQuery('.tooltipevent').css('top', e.pageY + 10);
										jQuery('.tooltipevent').css('left', e.pageX + 20);
									});
								},
								
								eventMouseout: function(calEvent, jsEvent) {
									jQuery(this).css('z-index', 8);
									jQuery('.tooltipevent').remove();
								},
								
								eventClick: function (calEvent, jsEvent, view) {								
									window.location = '?par[idLibur]=' + calEvent.id + '".getPar($par,"idLibur")."';
								},
							});
							
							jQuery('.fc-button-prev span').click(function(){
							   document.getElementById('daftarLibur').style.display = 'none';
							   var date = jQuery('#calendar').fullCalendar('getDate');
							   var bulanLibur = date.getMonth() == 0 ? 11 : date.getMonth()-1;
							   var tahunLibur = date.getMonth() == 0 ? date.getFullYear()-1 : date.getFullYear();
							   window.location = '?par[bulanLibur]=' + bulanLibur + '&par[tahunLibur]=' + tahunLibur + '".getPar($par,"bulanLibur,tahunLibur,idLibur")."';
							});

							jQuery('.fc-button-next span').click(function(){
							   document.getElementById('daftarLibur').style.display = 'none';
							   var date = jQuery('#calendar').fullCalendar('getDate');
							   var bulanLibur = date.getMonth() == 12 ? 0 : date.getMonth()+1;
							   var tahunLibur = date.getMonth() == 12 ? date.getFullYear()+1 : date.getFullYear();
							   window.location = '?par[bulanLibur]=' + bulanLibur + '&par[tahunLibur]=' + tahunLibur + '".getPar($par,"bulanLibur,tahunLibur,idLibur")."';
							});
						});
					</script>";
					
			$text.="<div id=\"calendar\"></div>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:30px; margin-bottom:5px;\">
							<span style=\"float:left;\"><h3>Daftar Libur</h3></span>
							<input type=\"button\" class=\"cancel radius2\" value=\"VIEW ALL\" onclick=\"window.location='?par[mode]=det".getPar($par,"mode,idLibur")."';\"  style=\"float:right; margin-top:-15px;\"/>
						</div>
						<div id=\"daftarLibur\" class=\"widgetcontent userlistwidget nopadding\">
							<ul>";
							$no=1;
							if(is_array($arrData)){						
								while(list($i, $r)=each($arrData)){
									$data=$r[data];
									list($mulaiLibur) = explode(" ", $data[mulaiLibur]);
									list($selesaiLibur) = explode(" ", $data[selesaiLibur]);
									$text.="<li>
										<div style=\"width:10px; height:10px; background:$r[color]; margin-top:4px; margin-right:5px; float:left;\">&nbsp;</div>
										<a href=\"?par[idLibur]=$data[idLibur]".getPar($par,"idLibur")."\">$data[namaLibur]</a>
										<div style=\"font-size:10px; color:#888;\">".getTanggal($mulaiLibur)." s.d ".getTanggal($selesaiLibur)."</div>
									</li>";
								}
							}	
					$text.="</ul>
						</div>
					</div>
				</div>
				<div class=\"one_half last dashboard_right\" style=\"margin-left:20px;\">					
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:5px;\"><h3>Keterangan</h3></div>";
						
					if(isset($menuAccess[$s]["add"])) $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add".getPar($par,"mode,idLibur")."',875,450);\" style=\"position:absolute; right:0; top:0; margin-top:0px;\"><span>Tambah Data</span></a>";
						
					if(empty($par[idLibur])) $par[idLibur] = getField("select idLibur from dta_libur where ".$par[tahunLibur].($par[bulanLibur]+1)." between concat(year(mulaiLibur),month(mulaiLibur)) and concat(year(selesaiLibur),month(selesaiLibur)) order by mulaiLibur limit 1");
					
					$sql="select * from dta_libur t1 join mst_data t2 on (t1.idKategori=t2.kodeData) where t1.idLibur='$par[idLibur]'";
					$res=db($sql);
					$r=mysql_fetch_array($res);										
					
					$tanggalLibur = $r[mulaiLibur] == $r[selesaiLibur] ? getTanggal($r[mulaiLibur]) : getTanggal($r[mulaiLibur])." s.d ".getTanggal($r[selesaiLibur]);
					
					$text.="<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\">					
							<p>
								<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Judul</label>
								<span class=\"field\" style=\"margin-left:140px;\">$r[namaLibur]&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Kategori</label>
								<span class=\"field\" style=\"margin-left:140px;\">$r[namaData]&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Tanggal</label>
								<span class=\"field\" style=\"margin-left:140px;\">$tanggalLibur&nbsp;</span>
							</p>
							<p>
								<label class=\"l-input-small\" style=\"text-align:left; padding-left:10px;\">Keterangan</label>
								<span class=\"field\" style=\"margin-left:140px;\">".nl2br($r[keteranganLibur])."&nbsp;</span>
							</p>					
						</form>
					</div>
				</div>
			</div>";				
		return $text;
	}
	
	function lData(){
		global $s,$par,$menuAccess;		
		if(empty($par[tahunLibur])) $par[tahunLibur] = date('Y');
		
		if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
		$sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);
				
		$sWhere= "where t1.namaLibur is not null and ".$par[tahunLibur]." between year(t1.mulaiLibur) and year(t1.selesaiLibur)";
		if (!empty($_GET['fSearch']))
			$sWhere.= " and (	
				lower(t1.namaLibur) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t1.keteranganLibur) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
				or lower(t2.namaData) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'
			)";
			
		$arrOrder = array(	
			"t1.mulaiLibur",
			"t1.mulaiLibur",
			"t1.namaLibur",
			"t2.namaData",	
			"t1.keteranganLibur",
		);
		$orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];
		$sql="select * from dta_libur t1 join mst_data t2 on (t1.idKategori=t2.kodeData) $sWhere order by $orderBy $sLimit";
		$res=db($sql);
		
		$json = array(
			"iTotalRecords" => mysql_num_rows($res),
			"iTotalDisplayRecords" => getField("select count(*) from dta_libur t1 join mst_data t2 on (t1.idKategori=t2.kodeData) $sWhere"),
			"aaData" => array(),
		);
		
		$no=intval($_GET['iDisplayStart']);
		while($r=mysql_fetch_array($res)){
			$no++;
			$tanggalLibur = $r[mulaiLibur] == $r[selesaiLibur] ? getTanggal($r[mulaiLibur]) : getTanggal($r[mulaiLibur])." s.d ".getTanggal($r[selesaiLibur]);
			
			$statusLibur=$r[statusLibur] == "t" ?
					"<img src=\"styles/images/t.png\" title=\"Active\">":
					"<img src=\"styles/images/f.png\" title=\"Not Active\">";
			
			$controlLibur="";
			
			if(isset($menuAccess[$s]["edit"]))
			$controlLibur.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[idLibur]=$r[idLibur]".getPar($par,"mode,idLibur")."',875,450);\"><span>Edit</span></a>";
			
			if(isset($menuAccess[$s]["delete"]))
			$controlLibur.=" <a href=\"?par[mode]=del&par[idLibur]=$r[idLibur]".getPar($par,"mode,idLibur")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
			
			$data=array(
				"<div align=\"center\">".$no.".</div>",				
				"<div align=\"center\">".$tanggalLibur."</div>",
				"<div align=\"left\">".$r[namaLibur]."</div>",
				"<div align=\"left\">".$r[namaData]."</div>",
				"<div align=\"left\">".nl2br($r[keteranganLibur])."</div>",
				"<div align=\"center\">".$statusLibur."</div>",								
				"<div align=\"center\">".$controlLibur."</div>",
			);
		
		
			$json['aaData'][]=$data;
		}
		return json_encode($json);
	}
	
	function getContent($par){
		global $s,$_submit,$menuAccess;
		switch($par[mode]){			
			case "lst":
				$text=lData();
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