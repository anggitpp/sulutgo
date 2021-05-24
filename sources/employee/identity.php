<?php
session_start();
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";

$_SESSION["curr_emp_id"] = (isset($_GET["empid"]) ? $_GET["empid"] : $_SESSION["curr_emp_id"] );
if(!empty($par[idPegawai])) $_SESSION["curr_emp_id"] = $par[idPegawai];

if (empty($_SESSION["curr_emp_id"])) {
    echo 
  "<script>
    alert(\"Silakan memilih Pegawai terlebih dahulu...\");
    window.location.href=\"".APP_URL . "/?c=3&p=8&m=79&s=82\";
  </script>";
//  header("Location: " . APP_URL . "/index.php?c=3&p=8&m=79&s=82");
}
$emp = new Emp();
$emp->id = $_SESSION["curr_emp_id"];
$emp = $emp->getById();

if($par[mode]=="print"){
	pdf();
}

$empc = new EmpPhist();
$empc->parentId = $emp->id;
$empc = $empc->getActiveParentId();

$cutil = new Common();
$ui = new UIHelper();
?>
</style>  <div class="pageheader">
  <h1 class="pagetitle"><?php echo $s == 279 ? "Data Pegawai <input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?".getPar($par,"mode,idPegawai")."';\" style=\"float:right; margin-top:-10px;\" />" : $arrTitle[getField("select kodeInduk from app_menu where kodeMenu='$s'")] . " - " . $arrTitle[$s] ?></h1>
  <?= getBread(" NPP " . $emp->regNo) ?>
  <span class="pagedesc">&nbsp;</span>
</div>
<div id="contentwrapper" class="contentwrapper">
  <form class="stdform" >
  <div style="position: absolute;right: 20px;top: 50px;"><a href="#" class="btn btn1 btn_document" title="Cetak Form" onclick="openBox('ajax.php?par[mode]=print&par[id]=<?= $emp->id.getPar($par,"mode,id") ?>',1200,600);" ><span>Print Data</span></a></div>
    <table style="width: 100%" >
      <tr>
        <td style="width: 15%; padding-right: 10px; padding-top: 5px;">
          <img alt="<?= $emp->regNo ?>" width="100%" height="200px" src="<?= APP_URL . "/files/emp/pic/" . (empty($emp->picFilename) ? "nophoto.jpg" : $emp->picFilename) ?>" >
        </td>
        <td style="width: 80%;vertical-align: top;">
          <div class="widgetbox">
            <!--<div style="margin-bottom:0px;" class="title"><h3>Data Diri Pegawai</h3></div>-->           
            <table style="width: 100%;padding-top: -5px;" >
              <tr>
                <td style="padding-right: 5px;">
                  <?php
				  echo $ui->createPLabelSpanDisplay("Nama Lengkap", $emp->name);
                  echo $ui->createPLabelSpanDisplay("Nama Panggilan", $emp->alias);
                  echo $ui->createPLabelSpanDisplay("Tempat Lahir", $cutil->getDescription("select namaData description from mst_data WHERE kodeData='$emp->birthPlace'", "description"));
                  echo $ui->createPLabelSpanDisplay("No. KTP", $emp->ktpNo);
                  echo $ui->createPLabelSpanDisplay("Berlaku s/d", $emp->ktpLifetime == "t" ? "Seumur Hidup" : getTanggal($emp->ktpValid));
                  echo $ui->createPLabelSpanDisplay("Jenis Kelamin", ($emp->gender == "F" ? "Perempuan" : "Laki-Laki"));
                  ?>
                </td>
                <td style="width:350px;">
                  <?php
                  echo $ui->createPLabelSpanDisplay("ID", $emp->regNo);
                  echo $ui->createPLabelSpanDisplay("NPP", $emp->kode);
                  echo $ui->createPLabelSpanDisplay("Tgl. Lahir", getTanggal($emp->birthDate));
                  echo $ui->createPLabelSpanDisplay("File KTP", "<a href=\"#\" onclick=\"openBox('view.php?doc=ktp&id=$emp->id',1000,500);\"><img src=\"" . getIcon($emp->ktpFilename) . "\" align=\"left\" style=\"vertical-align:middle\" ></a>", "", "fieldB");
                  ?>
                </td>
              </tr>           
            </table>									
          </div>
        </td>
      </tr>
    </table>
	
	<table style="width: 100%" >
	 <tr>
		<td>
		  <?php echo $ui->createPLabelSpanDisplay("Alamat sesuai KTP", nl2br($emp->ktpAddress)); ?>
		</td>
		<td style="width: 350px;">&nbsp;</td>
	  </tr>
	  <tr>
		<td>
		  <?php echo $ui->createPLabelSpanDisplay("Propinsi KTP", $cutil->getDescription("select namaData description from mst_data WHERE kodeData='$emp->ktpProv'", "description")); ?>
		</td>
		<td>
		  <?php echo $ui->createPLabelSpanDisplay("Kab/Kota KTP", $cutil->getDescription("select namaData description from mst_data WHERE kodeData='$emp->ktpCity'", "description")); ?>
		</td>
	  </tr>
	  <tr>
		<td>
		  <?php echo $ui->createPLabelSpanDisplay("Alamat Domisili", nl2br($emp->domAddress)); ?>
		</td>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td>
		  <?php echo $ui->createPLabelSpanDisplay("Propinsi Domisili", $cutil->getDescription("select namaData description from mst_data WHERE kodeData='$emp->domProv'", "description")); ?>
		</td>
		<td>
		  <?php echo $ui->createPLabelSpanDisplay("Kab/Kota Domisili", $cutil->getDescription("select namaData description from mst_data WHERE kodeData='$emp->domCity'", "description")); ?>
		</td>
	  </tr>              
	  <tr>
		<td>
		  <?php
		  echo $ui->createPLabelSpanDisplay("Telp. Rumah", $emp->phoneNo);
		  echo $ui->createPLabelSpanDisplay("Email", $emp->email);
		  echo $ui->createPLabelSpanDisplay("Status", $cutil->getDescription("select concat(namaData,' - ',keteranganData) description from mst_data WHERE kodeData='$emp->marital'", "description"));
		  echo $ui->createPLabelSpanDisplay("No. NPWP", $emp->npwpNo);
		  ?>
		</td>
		<td>
		  <?php
		  echo $ui->createPLabelSpanDisplay("Nomor HP", $emp->cellNo);
		  ?>
		</td>
	  </tr>
	</table>
	
	<div class="contenttitle2"><h3>POSISI SAAT INI</h3></div>		  
          <table style="width:100%;">
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Jabatan", $empc->posName);?></td>
				<td style="width: 350px;">&nbsp;</td>
			</tr>
            <tr>
              <td>
				<?php
					echo $ui->createPLabelSpanDisplay("Pangkat", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->rank'", "description"));
					#echo $ui->createPLabelSpanDisplay("Nomor SK", $empc->skNo);
				?>
                
              </td>
              <td>
				<?php
					echo $ui->createPLabelSpanDisplay("Grade", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->grade'", "description"));
					#echo $ui->createPLabelSpanDisplay("Tanggal", $empc->skDate);
				?>
              </td>
            </tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Direktorat", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->dirId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Divisi", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->divId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Bagian", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->deptId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Unit", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->unitId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Lokasi Kerja", $cutil->getDescription("select namaData as description from mst_data WHERE kodeData='$empc->location'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Mulai", getTanggal($empc->startDate));?></td>
				<td><?php echo $ui->createPLabelSpanDisplay("Berhenti", getTanggal($empc->endDate));?></td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Atasan", $cutil->getDescription("select concat(reg_no,' - ',name) as description from emp WHERE status=535 and id='$empc->leaderId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Tata Usaha", $cutil->getDescription("select concat(reg_no,' - ',name) as description from emp WHERE status=535 and id='$empc->administrationId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Pengganti", $cutil->getDescription("select concat(reg_no,' - ',name) as description from emp WHERE status=535 and id='$empc->replacementId'", "description"));?></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $ui->createPLabelSpanDisplay("Keterangan", $empc->remark);?></td>
				<td>&nbsp;</td>
			</tr>
          </table>		  		 
  </form>
</div>

<?php
function pdf(){
		global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
		require_once 'plugins/PHPPdf.php';
		
		$arrMaster = arrayQuery("select kodeData, namaData from mst_data");
		$arrName = arrayQuery("select id, name from emp");

		$sql="select *,(CASE WHEN gender = 'M' THEN 'Laki-Laki' ELSE (CASE WHEN gender = 'F' THEN 'Perempuan' ELSE '' END) END) as gender from dta_pegawai where id = '$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		$sql__="select * from emp_char where parent_id = '$par[id]'";
		$res__=db($sql__);
		$r__=mysql_fetch_array($res__);
		
		$pdf = new PDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetLeftMargin(15);
		
		
		$pdf->Cell(50,20,$botom,0,0,'L');
		if(!empty($r[pic_filename])){
		$gambar = "files/emp/pic/".$r[pic_filename];
		$pdf->Image($gambar,15,10,40);
		}else{
		$gambar = "files/emp/pic/nophoto.jpg";
		$pdf->Image($gambar,15,10,40);
		}
		$pdf->Cell(40,6,' ',0,0,'L');
		$pdf->Ln(40);
		$pdf->Ln();
		
		$pdf->Ln();
		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);

		$pdf->Cell(180,8,'DATA PRIBADI',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);

				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		

		$pdf->Cell(35,6,'NAMA LENGKAP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,strtoupper($r[name]),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'NPP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,strtoupper($r[reg_no]),0,0);
$pdf->SetFont('Arial','',10);

		$pdf->Ln(7);	

		$pdf->Cell(35,6,'NAMA PANGGILAN',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,strtoupper($r[alias]),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'KTP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,strtoupper($r[ktp_no]),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);	
		// $pdf->Cell(0,6,' ',0,0,'L');
		$pdf->Cell(35,6,'TEMPAT LAHIR',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[birth_place]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'TANGGAL LAHIR',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,getTanggal($r[birth_date],'t'),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		//$pdf->Cell(40,6,' ',0,0,'L');
		$pdf->Cell(35,6,'JENIS KELAMIN',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[gender],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'BERLAKU S/D',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,getTanggal($r[ktp_valid],'t'),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		//$pdf->Cell(40,6,' ',0,0,'L');
		$pdf->Cell(35,6,'ALAMAT KTP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[ktp_address],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		//$pdf->Cell(40,6,' ',0,0,'L');
		$pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[ktp_prov]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'KOTA',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[ktp_city]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		//$pdf->Cell(40,6,' ',0,0,'L');
		$pdf->Cell(35,6,'ALAMAT DOMISILI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[dom_address],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		$pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[dom_prov]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'KOTA',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[dom_city]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		$pdf->Cell(35,6,'TELP RUMAH',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[phone_no],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'NOMOR HP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[cell_no],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);
		$sql="select * from emp_char where parent_id = '$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);

		$pdf->Cell(35,6,'KARAKTER PRIBADI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[characteristic],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);
		$pdf->Cell(35,6,'HOBI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[hobby],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);
		$pdf->Cell(35,6,'KEAHLIAN KHUSUS',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[abilities],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);
		$pdf->Cell(35,6,'ORGANISASI SOSIAL',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[organization],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);
		$pdf->Ln();
		$pdf->Ln();	

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'POSISI',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,40,30,30,20,30,20));
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));

		$pdf->Row(array("NO.\tb","POSISI\tb","JABATAN\tb","LOKASI\tb","TAHUN\tb","NOMOR SK\tb","STATUS\tb"));
		$pdf->SetWidths(array(10, 40, 30, 30, 20, 30, 20));
		$pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

		$sql = "select * from emp_phist where parent_id='$par[id]' order by year(start_date) desc";
		$res=db($sql);
		$no=0;
		while ($r=mysql_fetch_array($res)) {
			$r[status] = $r[status] == 1 ? "Aktif" : "Tidak Aktif";
			$tahun = substr($r[start_date], 0, 4).($r[end_date] == null || $r[end_date] == "" ? " - current " : " - ".substr($r[end_date],0 ,4));
			$no++;
			$no = $no.".";
			$pdf->Row(array($no."\tu",$r[pos_name]."\tu",$arrMaster[$r[rank]]."\tu",$arrMaster[$r[location]]."\tu",$tahun."\tu",$r[sk_no]."\tu",$r[status]."\tu"));
			$total += $r[nilai];
		}

	

	$pdf->Ln();
	$pdf->Ln();





		$kontakexist = getField("select count(id) from emp_contact where parent_id = '$par[id]'");
		if($kontakexist!=0){
		$pdf->AddPage();

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'KONTAK',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		
		$sql="select * from emp_contact where parent_id = '$par[id]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);

		$pdf->Cell(80,6,'SERUMAH',0,0,'C','true');
		$pdf->Cell(23,6,' ',0,0,'L','');
		$pdf->Cell(80,6,'BEDA RUMAH',0,0,'C','true');
		$pdf->Ln(10);		

		$pdf->Cell(35,6,'NAMA',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,strtoupper($r[sr_nama]),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'NAMA',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,strtoupper($r[br_nama]),0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		
		// $pdf->Cell(0,6,' ',0,0,'L');
		$pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[sr_hub],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[br_hub],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		$pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[sr_phone],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[br_phone],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);


		$pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[sr_address],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$r[br_address],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		$pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[sr_prov]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[br_prov]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln(7);

		$pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[sr_city]],0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(60,6,' ',0,0,'C');
		$pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
		$pdf->Cell(3,6,' ',0,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,6,$arrMaster[$r[br_city]],0,0);
		$pdf->SetFont('Arial','',10);
	$pdf->Ln();
		$pdf->Ln();

		$familyexist = getField("select count(id) from emp_family where parent_id = '$par[id]'");
		if($familyexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'KELUARGA',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,80,30,60));
    $pdf->SetAligns(array('L','L','L'));

    $pdf->Row(array("NO.\tb","NAMA\tb","HUBUNGAN\tb","TTL\tb"));
    $pdf->SetWidths(array(10, 80, 30, 60));
    $pdf->SetAligns(array('L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_family where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[name]."\tu",$arrMaster[$r[rel]]."\tu",$arrMaster[$r[birth_place]]." ".getTanggal($r[birth_date])."\tu"));
       $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();
		

		}

		

		//REKENING

		$bankexist = getField("select count(id) from emp_bank where parent_id = '$par[id]'");
		if($bankexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'REKENING BANK',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,60,30,30,50));
    $pdf->SetAligns(array('L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NAMA BANK\tb","NO REKENING\tb","CABANG\tb","REMARK\tb"));
    $pdf->SetWidths(array(10,60,30,30,50));
    $pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_bank where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[bank_id]]."\tu",$r[branch]."\tu",$r[account_no]."\tu",$r[remark]."\tu"));
       $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();

		//KARIR

		$karirexist = getField("select count(id) from emp_career where parent_id = '$par[id]'");
		if($karirexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT KARIR',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,30,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR SK\tb","PERIHAL\tb","KATEGORI\tb","TIPE\tb","TANGGAL\tb"));
    $pdf->SetWidths(array(10,30,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_career where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[sk_subject]."\tu",$arrMaster[$r[sk_cat]]."\tu",$arrMaster[$r[sk_type]]."\tu",$r[sk_date]."\tu"));
       // $total += $r[nilai];
    }
    
		}


		//KONTRAK

		$kontrakexist = getField("select count(id) from emp_pcontract where parent_id = '$par[id]'");

		if($kontrakexist!=0){
			$pdf->AddPage();

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT KONTRAK',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,30,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR SK\tb","PERIHAL\tb","TGL SK\tb","TGL BERLAKU\tb","TGL BERAKHIR\tb"));
    $pdf->SetWidths(array(10,30,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_pcontract where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[subject]."\tu",$r[sk_date]."\tu",$r[start_date]."\tu",$r[end_date]."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();


		//PELATIHAN

		$trainingexist = getField("select count(id) from emp_training where parent_id = '$par[id]'");
		if($trainingexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT PELATIHAN',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,40,50,30,30,20));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR SERTIFIKAT\tb","PERIHAL\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
    $pdf->SetWidths(array(10,40,50,30,30,20));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_training where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[trn_no]."\tu",$r[trn_subject]."\tu",$arrMaster[$r[trn_cat]]."\tu",$arrMaster[$r[trn_type]]."\tu",$r[trn_year]."\tu"));
       // $total += $r[nilai];
    }
    
		}
$pdf->Ln();
		$pdf->Ln();
		//PENGHARGAAN

		$rewardexist = getField("select count(id) from emp_reward where parent_id = '$par[id]'");
		if($rewardexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT PENGHARGAAN',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,45,30,30,25,20,20));
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR PENGHARGAAN\tb","PERIHAL\tb","PENERBIT\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
    $pdf->SetWidths(array(10,45,30,30,25,20,20));
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_reward where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[rwd_no]."\tu",$r[rwd_subject]."\tu",$r[rwd_agency]."\tu",$arrMaster[$r[rwd_cat]]."\tu",$arrMaster[$r[rwd_type]]."\tu",$r[rwd_year]."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();

		//PERINGATAN

		$punishexist = getField("select count(id) from emp_punish where parent_id = '$par[id]'");
		if($punishexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT PERINGATAN',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR PERINGATAN\tb","PERIHAL\tb","PENERBIT\tb","TIPE\tb","TAHUN\tb"));
    $pdf->SetWidths(array(10,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_punish where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[pnh_no]."\tu",$r[pnh_subject]."\tu",$r[pnh_agency]."\tu",$arrMaster[$r[pnh_type]]."\tu",$r[pnh_year]."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();

		//PENDIDIKAN

		$eduexist = getField("select count(id) from emp_edu where parent_id = '$par[id]'");
		if($eduexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT PENDIDIKAN',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,30,50,35,30,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","TINGKATAN\tb","NAMA LEMBAGA\tb","JURUSAN\tb","KOTA\tb","TAHUN\tb"));
    $pdf->SetWidths(array(10,30,50,35,30,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_edu where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[edu_type]]."\tu",$r[edu_name]."\tu",$arrMaster[$r[edu_dept]]."\tu",$arrMaster[$r[edu_city]]."\tu",$r[edu_year]."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();

		//KERJA

		$workexist = getField("select count(id) from emp_pwork where parent_id = '$par[id]'");
		if($workexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT KERJA',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,60,50,35,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","PERUSAHAAN\tb","JABATAN\tb","BAGIAN\tb","TAHUN\tb"));
    $pdf->SetWidths(array(10,60,50,35,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select *,concat(year(start_date),' - ',year(end_date)) as edu_years from emp_pwork where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[company_name]."\tu",$r[position]."\tu",$r[dept]."\tu",$r[edu_years]."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();

		//KESEHATAN

		$healthexist = getField("select count(id) from emp_health where parent_id = '$par[id]'");
		if($healthexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'RIWAYAT KESEHATAN',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,30,50,40,50));
    $pdf->SetAligns(array('L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","TANGGAL\tb","NAMA TEMPAT\tb","DOKTER\tb","KETERANGAN\tb"));
    $pdf->SetWidths(array(10,30,50,40,50));
    $pdf->SetAligns(array('L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_health where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",getTanggal($r[hlt_date])."\tu",$r[hlt_place]."\tu",$r[hlt_doctor]."\tu",$r[hlt_remark]."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();

		//ASET

		$healthexist = getField("select count(id) from emp_asset where parent_id = '$par[id]'");
		if($healthexist!=0){

		$pdf->SetFont('Arial','B',13);
		$pdf->setFillColor(0,0,0);
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell(180,8,'PINJAMAN ASET',0,0,'C','#000000');
		$pdf->SetTextColor(0,0,0);				
		$pdf->setFillColor(230,230,230);
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','',10);

		$pdf->SetWidths(array(10,30,30,30,50,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","ASET\tb","NO. SERI\tb","KATEGORI\tb","TIPE\tb","TANGGAL\tb"));
    $pdf->SetWidths(array(10,30,30,30,50,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
		$pdf->SetFont('Arial','',8);

    $sql = "select * from emp_asset where parent_id='$par[id]'";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[ast_name]."\tu",$r[ast_no]."\tu",$r[ast_usage]."\tu",$arrMaster[$r[ast_type]]."\tu",getTanggal($r[ast_date])."\tu"));
       // $total += $r[nilai];
    }
    
		}

		$pdf->Ln();
		$pdf->Ln();


		// $pdf->SetFont('Arial','B',13);
		// $pdf->Cell(180,6,'PENGALAMAN PRIBADI',0,0,'C');
		// $pdf->Ln(10);	
		// $pdf->SetFont('Arial','',10);
		// $sql_="select *,concat(year(start_date),' - ',year(end_date))  dtRange from emp_pwork where parent_id = '$par[id]'";
		// $res_=db($sql_);
		// while($r_=mysql_fetch_array($res_)){	
		// $pdf->Cell(40,6,$r_[dtRange],0,0,'L');
		// $pdf->Cell(5,6,$r_[position].' pada '.$r_[company_name],0,0,'L');
		
		// $pdf->Ln(5);		
		// }
		// $pdf->Ln(10);		
		// $pdf->SetFont('Arial','B',13);
		// $pdf->Cell(180,6,'PENDIDIKAN FORMAL',0,0,'C');
		// $pdf->Ln(10);	
		// $pdf->SetFont('Arial','',10);
		// $sql_="select * from emp_edu where parent_id = '$par[id]'";
		// $res_=db($sql_);
		// while($r_=mysql_fetch_array($res_)){	
		// $pdf->Cell(40,6,'Lulus Tahun '.$r_[edu_year],0,0,'L');
		// $pdf->Cell(5,6,$r_[edu_name],0,0,'L');
		// $pdf->Ln(5);		
		// }
		// $pdf->Ln(10);		

		// $pdf->SetFont('Arial','B',13);
		// $pdf->Cell(180,6,'PRESTASI',0,0,'C');
		// $pdf->Ln(10);	
		// $pdf->SetFont('Arial','',10);

		// $sql_="select * from emp_reward where parent_id = '$par[id]'";
		// $res_=db($sql_);
		// while($r_=mysql_fetch_array($res_)){	
		// $pdf->Cell(40,6,$arrMaster[$r_[rwd_type]],0,0,'L');
		// $pdf->Cell(5,6,$r_[rwd_subject],0,0,'L');
		// $pdf->Ln(5);		
		// }

		// $pdf->SetFont('Arial','B',13);
		// $pdf->Cell(180,6,'KEAHLIAN KHUSUS',0,0,'C');
		// $pdf->Ln(10);	
		// $pdf->SetFont('Arial','',10);
		// $pdf->Cell(180,6,$r__[abilities],0,0,'L');
		// $pdf->Ln(10);

		// $pdf->SetFont('Arial','B',13);
		// $pdf->Cell(180,6,'ORGANISASI SOSIAL',0,0,'C');
		// $pdf->Ln(10);	
		// $pdf->SetFont('Arial','',10);
		// $pdf->Cell(180,6,$r__[organization],0,0,'L');
			
		
		
		
		$pdf->Output();	
	}
?>