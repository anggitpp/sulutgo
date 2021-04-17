<?php
	if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";		
	$fFile = "files/dinas/";
	
	function gNomor(){
		global $db,$s,$inp,$par;
		$prefix="RPJ";
		$date=empty($_GET[tanggalDinas]) ? $inp[tanggalDinas] : $_GET[tanggalDinas];
		$date=empty($date) ? date('d/m/Y') : $date;
		list($tanggal, $bulan, $tahun) = explode("/", $date);
		
		$nomor=getField("select nomorDinas from ess_dinas where month(tanggalDinas)='$bulan' and year(tanggalDinas)='$tahun' order by nomorDinas desc limit 1");
		list($count) = explode("/", $nomor);
		return str_pad(($count + 1), 3, "0", STR_PAD_LEFT)."/".$prefix."-".getRomawi($bulan)."/".$tahun;
	}

	function hapusFile(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		$buktiKlaim = getField("select buktiKlaim from rawatjalan_klaim where id='$par[id]'");
		if(file_exists($fFile.$buktiKlaim) and $buktiKlaim!="")unlink($fFile.$buktiKlaim);
		
		$sql="update rawatjalan_klaim set buktiKlaim='' where id='$par[id]'";
		db($sql);		
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	

	function hubungan(){
		global $db, $par;
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$hubungan = getField("select rel from emp_family where id = '$par[idPasien]'");
		$hubungan = $arrMaster[$hubungan];
		$umur = getField("select TIMESTAMPDIFF(YEAR, birth_date, CURRENT_DATE ) empAge from emp_family where id = '$par[idPasien]'");

		echo $hubungan."\t".$umur;
	}
	
	function gPegawai(){
		global $db,$s,$inp,$par;
		$sql="select * from emp where reg_no='".$par[nikPegawai]."'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$data["idPegawai"] = $r[id];
		$data["nikPegawai"] = $r[reg_no];
		$data["namaPegawai"] = strtoupper($r[name]);
		
		$sql_="select * from emp_phist where parent_id='".$r[id]."' and status='1'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);

		$getPokok = getField("select nilaiPokok from pay_pokok where idPegawai = '$r[id]'");
		$getNilai = getField("select nilai from rawatjalan_plafon where idJenis = '$r[marital]' AND tahun = '".date('Y')."'");
		$getJumlahPengambilan = getField("select sum(pengambilan) from rawatjalan_klaim where idPegawai = '$r[id]' AND year(tanggalKlaim) = '".date('Y')."'");

		$data["batasNilai"] = $getPokok * $getNilai - $getJumlahPengambilan;
		$data["namaJabatan"] = $r_[pos_name];
		$data["namaGolongan"] = getField("select namaData from mst_data where kodeData='".$r_[obat]."'");


		
		return json_encode($data);
	}
		
	function uploadFile($idDinas){
		global $db,$s,$inp,$par,$fFile;		
		$fileUpload = $_FILES["buktiKlaim"]["tmp_name"];
		$fileUpload_name = $_FILES["buktiKlaim"]["name"];
		if(($fileUpload!="") and ($fileUpload!="none")){	
			fileUpload($fileUpload,$fileUpload_name,$fFile);			
			$buktiKlaim = "bukti-".$idDinas.".".getExtension($fileUpload_name);
			fileRename($fFile, $fileUpload_name, $buktiKlaim);			
		}		
		
		return $buktiKlaim;
	}
	

	function hapus(){
		global $db,$s,$inp,$par,$fFile,$cUsername;					
		// $fileDinas = getField("select fileDinas from ess_dinas where idDinas='$par[idDinas]'");
		// if(file_exists($fFile.$fileDinas) and $fileDinas!="")unlink($fFile.$fileDinas);
		
		$sql="delete from rawatjalan_klaim where id='$par[id]'";
		db($sql);
		$sql="delete from rawatjalan_klaim_detail where idKlaim='$par[id]'";
		db($sql);
		echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
	}

	function sdm(){
		global $db,$s,$inp,$par,$cUsername;
		repField();				
		$sql="update rawatjalan_klaim set sdmKeterangan = '$inp[sdmKeterangan]',sdmKlaim = '$inp[sdmKlaim]', sdmBy='$cUsername', sdmDate='".date('Y-m-d H:i:s')."' where id='$par[id]'";
		db($sql);

		
		echo "<script>window.location='?".getPar($par,"mode,idSakit")."';</script>";
	}
	
	function approve(){
		global $db,$s,$inp,$par,$cUsername, $cUsername;
		repField();				

		$sql="update rawatjalan_klaim set apprKlaim = '$inp[apprKlaim]',approveKeterangan = '$inp[approveKeterangan]', approveBy='$cUsername', approveDate='".date('Y-m-d H:i:s')."' where id='$par[id]'";
		db($sql);
		
		echo "<script>window.location='?".getPar($par,"mode,id")."';</script>";
	}
		
	function ubah(){
		global $db,$s,$inp,$par,$cUsername, $det;
		repField();		
				
		$sql="update rawatjalan_klaim set idPegawai='$inp[idPegawai]',idPasien='$inp[idPasien]',tempatKlaim='$inp[tempatKlaim]', nomor='$inp[nomor]', tanggal='".setTanggal($inp[tanggal])."',  tanggalKlaim='".setTanggal($inp[tanggalKlaim])."', keterangan='$inp[keterangan]', batasNilai = '$inp[batasNilai]',pengambilan = '$inp[pengambilan]', sisaNilai = '$inp[sisaNilai]',updateBy='$cUsername', updateDate='".date('Y-m-d H:i:s')."' where id='$par[id]'";
		// echo $sql;
		db($sql);

		// die();
		echo "<script>window.location='?par[mode]=edit".getPar($par,"mode")."';</script>";
	}
	
	function tambah(){
		global $db,$s,$inp,$par,$cUsername,$arrParam, $cID;	
		repField();				
		$id = getField("select id from rawatjalan_klaim order by id desc limit 1")+1;		
		
		$sql="insert into rawatjalan_klaim (id, idPegawai,createBy, createDate) values ('$id', '$cID', '$cUsername', '".date('Y-m-d H:i:s')."')";
		

		db($sql);
		
		echo "<script>window.location='?par[mode]=edit&par[id]=$id".getPar($par,"mode,idDinas")."';</script>";
	}
		
	function form(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $arrParam;
		
		$sql="select * from rawatjalan_klaim where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);	

		$true = $r[apprKlaim] == "t" ? "checked=\"checked\"" : "";
		$false = $r[apprKlaim] == "f" ? "checked=\"checked\"" : "";
		$revisi = $r[apprKlaim] == "r" ? "checked=\"checked\"" : "";
		
		$sTrue = $r[sdmKlaim] == "t" ? "checked=\"checked\"" : "";
		$sFalse = $r[sdmKlaim] == "f" ? "checked=\"checked\"" : "";
		$sRevisi = $r[sdmKlaim] == "r" ? "checked=\"checked\"" : "";

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

		$r[hubunganPasien] = getField("select rel from emp_family where id = '$r[idPasien]'");

		
		if(empty($r[nomor])) $r[nomor] = gNomor();
		if(empty($r[tanggal])) $r[tanggal] = date('Y-m-d');		

		$text = getValidation();

		// if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;								
		
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			marital,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$row_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$row_[idPegawai]."' and status='1'";
		// echo $sql__;
		$res__=db($sql__);
		$row__=mysql_fetch_array($res__);
		$row__[namaJabatan] = $row__[pos_name];
		$row__[namaGolongan] = getField("select namaData from mst_data where kodeData='".$row__[obat]."'");

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		if($arrMaster[$row__[grade]] < 4){
			$arrMaster[$row__[grade]] = 4;
		}
		$getSkala = getField("select rawatSkala from pay_skala where idGolongan = '".$arrMaster[$row__[grade]]."'");
		// echo "select rawatSkala from pay_skala where idGolongan = '".$arrMaster[$row__[grade]]."'";

		$getNilai = getField("select nilai from rawatjalan_plafon where idJenis = '$row_[marital]' AND tahun = '".date('Y')."'");
		$getJumlahPengambilan = getField("select sum(pengambilan) from rawatjalan_klaim where idPegawai = '$row_[idPegawai]' AND year(tanggalKlaim) = '".date('Y')."'");

		// echo "select nilai from rawatjalan_plafon where idJenis = '$row_[marital]' AND tahun = '".date('Y')."'";

		$getNilai = str_replace(",", ".", $getNilai);

		// echo $getSkala."<br>";
		// echo $getNilai."<br>";
		// echo $getJumlahPengambilan."<br>";

		$r[batasNilai] = ($getSkala * $getNilai) - $getJumlahPengambilan;
				
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\"  enctype=\"multipart/form-data\">	
				<div id=\"general\" style=\"margin-top:20px;\">
				<fieldset>
					<legend>DATA PEGAWAI</legend>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<div class=\"field\">
								<input type=\"text\" id=\"inp[nomor]\" name=\"inp[nomor]\"  value=\"$r[nomor]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"30\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<div class=\"field\">								
								<input type=\"hidden\" id=\"inp[idPegawai]\" name=\"inp[idPegawai]\"  value=\"$r[idPegawai]\" readonly=\"readonly\"/>
								<input type=\"text\" id=\"inp[nikPegawai]\" name=\"inp[nikPegawai]\"  value=\"$row_[nikPegawai]\" class=\"mediuminput\" style=\"width:100px;\" onchange=\"getPegawai('".getPar($par,"mode,nikPegawai")."');\"/>
								
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaPegawai]\" name=\"inp[namaPegawai]\"  value=\"$row_[namaPegawai]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p></td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggal\" name=\"inp[tanggal]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggal])."\" class=\"vsmallinput hasDatePicker\" onchange=\"getNomor('".getPar($par,"mode")."');\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaJabatan]\" name=\"inp[namaJabatan]\"  value=\"$row__[namaJabatan]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Golongan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[namaGolongan]\" name=\"inp[namaGolongan]\"  value=\"$row__[namaGolongan]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
								<input type=\"hidden\" id=\"\" name=\"inp[idGolongan]\"  value=\"$row__[obat]\" class=\"mediuminput\" style=\"width:300px;\" readonly=\"readonly\" />
							</div>
						</p>
					</td>
					</tr>
					</table>
					</fieldset>
					<br clear=\"all\"/>
					<fieldset>
					<legend>
						DATA KLAIM
					</legend>
					<table style=\"width:100%\">
						<tr>
						<td style=\"width:45%\">
							
							<p>
							<label class=\"l-input-small\">Tanggal</label>
							<div class=\"field\">
								<input type=\"text\" id=\"tanggalKlaim\" name=\"inp[tanggalKlaim]\" size=\"10\" maxlength=\"10\" value=\"".getTanggal($r[tanggalKlaim])."\" class=\"vsmallinput hasDatePicker\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Tempat Inap</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[tempatKlaim]\" name=\"inp[tempatKlaim]\"  value=\"$r[tempatKlaim]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Batas Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[batasNilai]\" name=\"inp[batasNilai]\"  value=\"$r[batasNilai]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Pengambilan</label>
							<div class=\"field\">								
								<input type=\"text\" onkeyup=\"hitungSisa(this);\" id=\"inp[pengambilan]\" name=\"inp[pengambilan]\"  value=\"$r[pengambilan]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Sisa Nilai</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"inp[sisaNilai]\" name=\"inp[sisaNilai]\"  value=\"$r[sisaNilai]\" class=\"mediuminput\" style=\"width:200px;\" />
							</div>
						</p>
						</td>
						<td style=\"width:55%\">
						<p>
							<label class=\"l-input-small\">Nama Pasien</label>
							<div class=\"field\">								
								
								".comboData("select * from emp_family where parent_id='$r[idPegawai]' and rel NOT IN(667,668)  order by name","id","name","inp[idPasien]"," ",$r[idPasien],"onchange=\"getHubungan(this.value,'".getPar($par,"mode,kodePosisi")."')\"","310px;","")."



							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Hubungan</label>
							<div class=\"field\">								
								<input type=\"text\" id=\"hubunganPasien\" name=\"inp[hubunganPasien]\"  value=\"".$arrMaster[$r[hubunganPasien]]."\" class=\"mediuminput\" style=\"width:300px;\" readonly/>

							</div>
						</p>";
						// $hubungan = getField("select rel from emp_family where id = '$r[idPasien]'");
						// $umur = getField("select TIMESTAMPDIFF(YEAR, birth_date, CURRENT_DATE ) empAge from emp_family where id = '$r[idPasien]'");
						// if($hubungan = 666 && $umur > 23){
						// 	$displayBukti = "style = \"display:block;\"";
						// }else{
						// 	$displayBukti = "style = \"display:none;\"";
						// }
						// $text.="
						// <div id = \"bukti\" $displayBukti>
						// <p>
						// 	<label class=\"l-input-small\">Bukti</label>
						// 	<div class=\"field\">";								
						// 		$text.=empty($r[buktiKlaim])?
						// 			"<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
						// 			<div class=\"fakeupload\" style=\"width:300px;\">
						// 				<input type=\"file\" id=\"buktiKlaim\" name=\"buktiKlaim\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
						// 			</div>":
						// 			"<a href=\"download.php?d=bukti&f=$par[id]\"><img src=\"".getIcon($fFile."/".$r[buktiKlaim])."\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
						// 			<input type=\"file\" id=\"buktiKlaim\" name=\"buktiKlaim\" style=\"display:none;\" />
						// 			<a href=\"?par[mode]=delFile".getPar($par,"mode")."\" onclick=\"return confirm('anda yakin akan menghapus file ini?')\" class=\"action delete\"><span>Delete</span></a>
						// 			<br clear=\"all\">";
						// 	$text.="</div>
						// </p>
						// </div>
						$text.="
						<p>
						<span class=\"required\"> *Kosongkan kolom Nama Pasien dan Hubungan jika klaim untuk Pekerja.</span>
						</p>
						</td>
					</table>
					</fieldset>
					<br clear=\"all\"/>
					<fieldset>
					<legend> KETERANGAN </legend>
					
						
								<textarea id=\"inp[keterangan]\" name=\"inp[keterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:90%;\">$r[keterangan]</textarea>
					
					</fieldset>
					<br clear=\"\">";
if($par[mode] == "app")
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[apprKlaim]\" value=\"t\" $true /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[apprKlaim]\" value=\"f\" $false /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[apprKlaim]\" value=\"r\" $revisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[approveKeterangan]\" name=\"inp[approveKeterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[approveKeterangan]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
					
			if($par[mode] == "sdm")
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Status</label>
							<div class=\"fradio\">
								<input type=\"radio\" id=\"true\" name=\"inp[sdmKlaim]\" value=\"t\" $sTrue /> <span class=\"sradio\">Disetujui</span>
								<input type=\"radio\" id=\"false\" name=\"inp[sdmKlaim]\" value=\"f\" $sFalse /> <span class=\"sradio\">Ditolak</span>
								<input type=\"radio\" id=\"revisi\" name=\"inp[sdmKlaim]\" value=\"r\" $sRevisi /> <span class=\"sradio\">Diperbaiki</span>
							</div>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<div class=\"field\">
								<textarea id=\"inp[sdmKeterangan]\" name=\"inp[sdmKeterangan]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:300px;\">$r[sdmKeterangan]</textarea>
							</div>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>
				
				</div>";
				$text.="
				<p style=\"position:absolute; top:5px;right:10px;\">					
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\"/>					
				</p>
			</form>";
		return $text;
	}

	function lihat(){
	global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID, $areaCheck,$arrParam;
	$par[idPegawai] = $cID;
	if(empty($par[tahun])) $par[tahun]=date('Y');		
	$text.="
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
		<span class=\"pagedesc\">&nbsp;</span>
	</div>";
	// require_once "tmpl/__emp_header__.php";		
	$text.="<div id=\"contentwrapper\" class=\"contentwrapper\">			
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<table>
					<tr>
						<td>Search : </td>				
						<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
						<td>".comboYear("par[tahun]", $par[tahun])."</td>
						<td><input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> </td>
					</tr>
				</table>
			</div>
			<div id=\"pos_r\">";
				if(isset($menuAccess[$s]["add"])) $text.="<a href=\"?par[mode]=add".getPar($par,"mode,idDinas")."\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";
				$text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
				<thead>
					<tr>
						<th rowspan=\"2\" width=\"20\">No.</th>
						<th rowspan=\"2\" style=\"min-width:100px;\">Nama</th>
						<th rowspan=\"2\" width=\"100\">Tanggal</th>
						<th rowspan=\"2\" width=\"100\">Nomor</th>
						<th rowspan=\"2\" width=\"100\">Nilai</th>
						<th colspan=\"2\" width=\"50\">Approval</th>
						<th rowspan=\"2\" width=\"50\">Kontrol</th>";
						$text.="</tr>
						<tr>
							<th width=\"50\">Atasan</th>
							<th width=\"50\">SDM</th>
						</tr>
					</thead>
					<tbody>";

		$filter = "where year(t1.tanggal)='$par[tahun]'";
		if(!empty($cID)) $filter.= " and t1.idPegawai='".$cID."'";
		// if(!empty($par[filter]))		
		// $filter.= " and (
		// 	lower(t1.nomorDinas) like '%".strtolower($par[filter])."%'
		// 	or lower(t1.namaDinas) like '%".strtolower($par[filter])."%'
		// 	or lower(t2.reg_no) like '%".strtolower($par[filter])."%'
		// 	or lower(t2.name) like '%".strtolower($par[filter])."%'		
		// )";
		
		// $arrKategori = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='".$arrParameter[19]."'");
		
		$sql="select *,t1.id as idK from rawatjalan_klaim t1 join dta_pegawai t2 on t1.idPegawai = t2.id $filter";
		// echo $sql;
		$res=db($sql);
		while($r=mysql_fetch_array($res)){			
			$no++;

			if(empty($r[apprKlaim])) $r[apprKlaim] = "p";
			$apprKlaim = $r[apprKlaim] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$apprKlaim = $r[apprKlaim] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $apprKlaim;
			$apprKlaim = $r[apprKlaim] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $apprKlaim;			

			if(empty($r[sdmKlaim])) $r[sdmKlaim] = "p";
			$sdmKlaim = $r[sdmKlaim] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$sdmKlaim = $r[sdmKlaim] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $sdmKlaim;
			$sdmKlaim = $r[sdmKlaim] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $sdmKlaim;				

			if(empty($r[pembayaranDinas])) $r[pembayaranDinas] = "p";
			$pembayaranDinas = $r[pembayaranDinas] == "t"? "<img src=\"styles/images/t.png\" title=\"Disetujui\">" : "<img src=\"styles/images/p.png\" title=\"Belum Diproses\">";
			$pembayaranDinas = $r[pembayaranDinas] == "f"? "<img src=\"styles/images/f.png\" title=\"Ditolak\">" : $pembayaranDinas;
			$pembayaranDinas = $r[pembayaranDinas] == "r"? "<img src=\"styles/images/o.png\" title=\"Diperbaiki\">" : $pembayaranDinas;

			$persetujuanLink = isset($menuAccess[$s]["apprlv1"]) ? "?par[mode]=app&par[id]=$r[idK]".getPar($par,"mode,id") : "#";			
			$sdmLink = (isset($menuAccess[$s]["apprlv2"]) && $r[apprKlaim] == "t") ? "?par[mode]=sdm&par[id]=$r[idK]".getPar($par,"mode,id") : "#";

			$text.="<tr>
			<td>$no.</td>
			<td>".strtoupper($r[name])."</td>
			<td align=\"center\">".getTanggal($r[tanggal])."</td>
			<td align=\"center\">".$r[nomor]."</td>
			<td align=\"right\">".getAngka($r[pengambilan])."</td>
			<td align=\"center\"><a href=\"".$persetujuanLink."\" title=\"Detail Data\">$apprKlaim</a></td>
			<td align=\"center\"><a href=\"".$sdmLink."\" title=\"Detail Data\">$sdmKlaim</a></td>
			
			
			";
			if(isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])){
				$text.="<td align=\"center\"><a href=\"?par[mode]=det&par[id]=$r[idK]".getPar($par,"mode,id")."\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>";		
				if(in_array($r[persetujuanDinas], array(0,2)))
					if(isset($menuAccess[$s]["edit"])&&$r[persetujuanDinas]!='t') $text.="<a href=\"?par[mode]=edit&par[id]=$r[idK]".getPar($par,"mode,id")."\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>";				

				if(in_array($r[persetujuanDinas], array(0)))
					if(isset($menuAccess[$s]["delete"])) $text.="<a href=\"?par[mode]=del&par[id]=$r[idK]".getPar($par,"mode,id")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
				$text.="</td>";
			}
			$text.="</tr>";				
		}	

		$text.="</tbody>
	</table>
</div>";
return $text;
}		

	
	function detailApproval(){
		global $db,$s,$inp,$par,$arrTitle,$menuAccess;
		
		$sql="select * from ess_dinas where idDinas='$par[idDinas]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);			

		$titleField = $par[mode] == "detSdm" ? "Approval SDM" : "Approval Atasan";
		$persetujuanField = $par[mode] == "detSdm" ? "sdmDinas" : "persetujuanDinas";
		$catatanField = $par[mode] == "detSdm" ? "noteDinas" : "catatanDinas";
		$timeField = $par[mode] == "detSdm" ? "sdmTime" : "approveTime";
		$userField = $par[mode] == "detSdm" ? "sdmBy" : "approveBy";
		
		$titleField = $par[mode] == "detPay" ? "Pembayaran" : $titleField;
		$persetujuanField = $par[mode] == "detPay" ? "pembayaranDinas" : $persetujuanField;
		$catatanField = $par[mode] == "detPay" ? "deskripsiDinas" : $catatanField;
		$timeField = $par[mode] == "detPay" ? "paidTime" : $timeField;
		$userField = $par[mode] == "detPay" ? "paidBy" : $userField;
		
		list($dateField) = explode(" ", $r[$timeField]);
				
		$persetujuanDinas = "Belum Diproses";
		$persetujuanDinas = $r[$persetujuanField] == "t" ? "Disetujui" : $persetujuanDinas;
		$persetujuanDinas = $r[$persetujuanField] == "f" ? "Ditolak" : $persetujuanDinas;	
		$persetujuanDinas = $r[$persetujuanField] == "r" ? "Diperbaiki" : $persetujuanDinas;	
		
		$text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$titleField."</h1>
					".getBread(ucwords($par[mode]." data"))."
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\"  class=\"stdform\">	
				<div id=\"general\" class=\"subcontent\">
					<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($dateField,"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from ".$db['setting'].".app_user where username='".$r[$userField]."' ")."&nbsp;</span>
						</p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<span class=\"field\">".$persetujuanDinas."&nbsp;</span>
					</p>
					<p>
						<label class=\"l-input-small\">Keterangan</label>
						<span class=\"field\">".nl2br($r[$catatanField])."&nbsp;</span>
					</p>				
					<p>						
						<input type=\"button\" class=\"cancel radius2\" value=\"Close\" onclick=\"closeBox();\"/>
					</p>
				</div>
			</form>	
			</div>";
		return $text;
	}
	
	function detail(){
		global $db,$s,$inp,$par,$arrTitle,$arrParameter,$menuAccess,$cID;
		
		$sql="select * from rawatjalan_klaim where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);					
		
		if(empty($r[nomor])) $r[nomor] = gNomor();
		if(empty($r[tanggalDinas])) $r[tanggalDinas] = date('Y-m-d');

		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
				
		if(!empty($cID) && empty($r[idPegawai])) $r[idPegawai] = $cID;
		$sql_="select
			id as idPegawai,
			reg_no as nikPegawai,
			name as namaPegawai
		from emp where id='".$r[idPegawai]."'";
		$res_=db($sql_);
		$r_=mysql_fetch_array($res_);
		
		$sql__="select * from emp_phist where parent_id='".$r_[idPegawai]."' and status='1'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		$r_[namaJabatan] = $r__[pos_name];
		$r_[namaGolongan] = getField("select namaData from mst_data where kodeData='".$r__[obat]."'");
		
		$text.="<div class=\"pageheader\">
					<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
					".getBread(ucwords($par[mode]." data"))."								
				</div>
				<div class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" class=\"stdform\">	
				<div id=\"general\" style=\"margin-top:20px;\">
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Nomor</label>
							<span class=\"field\">".$r[nomor]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">NPP</label>
							<span class=\"field\">".$r_[nikPegawai]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".$r_[namaPegawai]."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggal],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Jabatan</label>
							<span class=\"field\">".$r_[namaJabatan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Golongan</label>
							<span class=\"field\">".$r_[namaGolongan]."&nbsp;</span>
						</p>
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>DATA KLAIM</h3></div>
					</div>
					<table width=\"100%\">
					<tr>
					<td width=\"45%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[tanggalKlaim],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Tempat Inap</label>
							<span class=\"field\">".$r[tempatKlaim]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Batas Nilai</label>
							<span class=\"field\">".$r[batasNilai]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Pengambilan</label>
							<span class=\"field\">".$r[pengambilan]."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Sisa Nilai</label>
							<span class=\"field\">".$r[sisaNilai]."&nbsp;</span>
						</p>		
						
					</td>
					<td width=\"55%\" style=\"vertical-align:top;\">
						<p>
							<label class=\"l-input-small\">Nama Pasien</label>
							<span class=\"field\">".getField("select name from emp_family where id = '$r[idPasien]'")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Hubungan</label>
							<span class=\"field\">".$arrMaster[getField("select rel from emp_family where id = '$r[idPasien]'")]."&nbsp;</span>
						</p>						
					</td>
					</tr>
					</table>
					<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>KETERANGAN</h3></div>
					</div>
					<p>
					$r[keterangan]
					</p>

					
					";

			$sql="select * from rawatjalan_klaim where id='$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);

			$apprKlaim = "Belum Diproses";
			$apprKlaim = $r[apprKlaim] == "t" ? "Disetujui" : $apprKlaim;
			$apprKlaim = $r[apprKlaim] == "f" ? "Ditolak" : $apprKlaim;	
			$apprKlaim = $r[apprKlaim] == "r" ? "Diperbaiki" : $apprKlaim;	
			
			list($r[approveDate]) = explode(" ", $r[approveDate]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL ATASAN</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[approveDate],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from app_user where username='$r[approveBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$apprKlaim."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[approveKeterangan])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
			$sdmKlaim = "Belum Diproses";
			$sdmKlaim = $r[sdmKlaim] == "t" ? "Disetujui" : $sdmKlaim;
			$sdmKlaim = $r[sdmKlaim] == "f" ? "Ditolak" : $sdmKlaim;	
			$sdmKlaim = $r[sdmKlaim] == "r" ? "Diperbaiki" : $sdmKlaim;	
			
			list($r[sdmDate]) = explode(" ", $r[sdmDate]);
			
			$text.="<div class=\"widgetbox\">
						<div class=\"title\" style=\"margin-top:10px; margin-bottom:0px;\"><h3>APPROVAL SDM</h3></div>
					</div>			
					<table width=\"100%\">
					<tr>
					<td width=\"45%\">
						<p>
							<label class=\"l-input-small\">Tanggal</label>
							<span class=\"field\">".getTanggal($r[sdmDate],"t")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Nama</label>
							<span class=\"field\">".getField("select namaUser from app_user where username='$r[sdmBy]' ")."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Status</label>
							<span class=\"field\">".$sdmKlaim."&nbsp;</span>
						</p>
						<p>
							<label class=\"l-input-small\">Keterangan</label>
							<span class=\"field\">".nl2br($r[sdmKeterangan])."&nbsp;</span>
						</p>
					</td>
					<td width=\"55%\">&nbsp;</td>
					</tr>
					</table>";
			
		
			$text.="</div>
				<p>					
					<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,id")."';\" style=\"float:right;\"/>		
				</p>
			</form>";
		return $text;
	}
	
	function pegawai(){
		global $db,$s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess;		
		$text.="<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Daftar Pegawai</h1>
				".getBread()."
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<table>
				<tr>
				<td>Search : </td>
				<td>".comboArray("par[search]", array("All", "Nama", "NPP"), $par[search])."</td>
				<td><input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" style=\"width:250px;\" value=\"$par[filter]\" class=\"mediuminput\" /></td>
				<td>
					<input type=\"hidden\" id=\"par[mode]\" name=\"par[mode]\" value=\"$par[mode]\" />
					<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\" />
				</td>
				</tr>
			</table>
			</div>
			<div id=\"\" style=\"float:right;\">
			".comboData("select t1.kodeData id, concat(t2.namaData, ' - ', t1.namaData) description, t1.kodeInduk from mst_data t1

                       JOIN mst_data t2 ON t2.kodeData=t1.kodeInduk

                       where t2.kodeCategory='X04' order by t1.urutanData","id","description","par[divisi]"," ",$par[divisi],"onchange=\"document.getElementById('form').submit();\"", "500px","chosen-select")."
			</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th style=\"min-width:150px;\">Nama</th>
					<th width=\"100\">NPP</th>
					<th style=\"min-width:150px;\">Jabatan</th>					
					<th style=\"min-width:150px;\">Divisi</th>
					<th width=\"50\">Kontrol</th>
				</tr>
			</thead>
			<tbody>";
		
		$filter = "where reg_no is not null AND t2.obat !=''";
		
		if($par[search] == "Nama")
			$filter.= " and lower(t1.name) like '%".strtolower($par[filter])."%'";
		else if($par[search] == "NPP")
			$filter.= " and lower(t1.reg_no) like '%".strtolower($par[filter])."%'";
		else
			$filter.= " and (
				lower(t1.name) like '%".strtolower($par[filter])."%'
				or lower(t1.reg_no) like '%".strtolower($par[filter])."%'
			)";		

		if(!empty($par[divisi]))
			$filter.=" and t2.div_id = '$par[divisi]'";		
		
		
		$arrDivisi = arrayQuery("select kodeData, namaData from mst_data where kodeCategory='X05'");
		$sql="select t1.*, t2.pos_name, t2.div_id from emp t1 left join emp_phist t2 on (t1.id=t2.parent_id and t2.status=1) $filter order by name";
		$res=db($sql);
		while($r=mysql_fetch_array($res)){
			$no++;
			
			$text.="<tr>
					<td>$no.</td>
					<td>".strtoupper($r[name])."</td>
					<td>$r[reg_no]</td>
					<td>$r[pos_name]</td>
					<td>".$arrDivisi["$r[div_id]"]."</td>
					<td align=\"center\">
						<a href=\"#\" title=\"Pilih Data\" class=\"check\" onclick=\"setPegawai('".$r[reg_no]."', '".getPar($par, "mode, nikPegawai")."')\"><span>Detail</span></a>
					</td>
				</tr>";
		}	
		
		$text.="</tbody>
			</table>
			</div>
		</div>";
		return $text;
	}
	
	function getContent($par){
		global $db,$s,$_submit,$menuAccess, $cUsername;
		switch($par[mode]){
			case "no":
				$text = gNomor();
			break;
			case "get":
				$text = gPegawai();
			break;
			case "hubungan":
				$text = hubungan();
			break;			
			case "peg":
				$text = pegawai();
			break;
			
			case "det":
				$text = detail();
			break;
			case "detAts":
				$text = detailApproval();
			break;
			case "detSdm":
				$text = detailApproval();
			break;
			case "detPay":
				$text = detailApproval();
			break;
			case "sdm":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : sdm(); else $text = lihat();
			break;
			case "app":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : approve(); else $text = lihat();
			break;	
			
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
			break;
			case "del":
				if(isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
			break;
			case "edit":
				if(isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
			break;
			case "add":
				$text = tambah();
			break;
			case "delFile":
				if(isset($menuAccess[$s]["edit"])) $text = hapusFile(); else $text = lihat();
			break;
			default:
				$text = lihat();
				$sql = "select * from rawatjalan_klaim where tanggalKlaim = '0000-00-00' OR tanggalKlaim is null AND nomor is null AND createBy = '$cUsername'";
				echo $ql;
				$res = db($sql);
				while ($r = mysql_fetch_array($res)) {
					db("delete from rawatjalan_klaim where id = '$r[id]'");
					db("delete from rawatjalan_klaim_detail where idKlaim = '$r[id]'");
				}
			break;
		}
		return $text;
	}	
?>