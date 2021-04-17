<?php

/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($_GET["mode"]) {
  case "printCV":
    pdf();
  break;
  case "viewGambar":
    echo "<img src=\"$par[url]\">";
    break;


  case "add":
    if (isset($menuAccess[$s]["add"]))
      include 'form_edit.php';
    else
      include 'form_view.php';
    break;
  case "edit":
    if (isset($menuAccess[$s]["edit"]))
      include 'form_edit.php';
    else
      include 'form_view.php';
    break;
  case "jabedit":
    if (isset($menuAccess[$s]["edit"]))
      include COMMON_DIR . "dlgempjab.php";
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  case "jabadd":
    if (isset($menuAccess[$s]["add"])) {
      include COMMON_DIR . "dlgempjab.php";
    } else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  case "fasedit":
    if (isset($menuAccess[$s]["edit"]))
      include COMMON_DIR . "dlgempfas.php";
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  case "fasadd":
    if (isset($menuAccess[$s]["add"])) {
      include COMMON_DIR . "dlgempfas.php";
    } else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  default :
    include 'form_edit.php';
    break;
}

// function formatDate($date){
//   list($tahun,$bulan,$tanggal)=explode("-", $date);
//   $get = $tanggal."-".$bulan."-".$tahun;

//   return $get;
// }

//     function pdf(){
//         global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
//         require_once 'plugins/PHPPdf.php';
        
//         $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
//         $arrName = arrayQuery("select id, name from emp");

//         $sql="select *,(CASE WHEN gender = 'M' THEN 'Laki-Laki' ELSE (CASE WHEN gender = 'F' THEN 'Perempuan' ELSE '' END) END) as gender from emp where id = '$par[id]'";
//         $res=db($sql);
//         $r=mysql_fetch_array($res);

//         $r_[join_date] = $r[join_date];
//         $r_[leave_date] = $r[leave_date];
//         $sql__="select * from emp_char where parent_id = '$par[id]'";
//         $res__=db($sql__);
//         $r__=mysql_fetch_array($res__);
        
//         $pdf = new PDF('P','mm','A4');
//         $pdf->AddPage();
//         $pdf->SetLeftMargin(15);
        
        
//         $pdf->Cell(30,20,'',0,0,'L');
//         if(!empty($r[pic_filename])){
//     $gambar = "files/emp/pic/".$r[pic_filename];
    
//         $pdf->Image($gambar, 155,40,35);
//         }else{
//         $gambar = "files/emp/pic/nophoto.jpg";
//         $pdf->Image($gambar,155,40,40);
//         }
//         $pdf->Cell(40,6,'',0,0,'L');
//         // $pdf->Ln(1); 
        

//         $pdf->Ln();
//         $pdf->SetFont('Arial','B',12);
//         $pdf->SetTextColor(0,0,0);
//         $pdf->SetFont('Arial','B',8);
                
//         $pdf->setFillColor(230,230,230);
//         // $pdf->Ln(6); 
//         $pdf->SetFont('Arial','B',12); 
//         $pdf->Cell(60,6,' ',0,0,'L');
//         $pdf->Cell(25,6,'',0,0,'L');
//         $pdf->Cell(80,6,' ',0,0,'C');
//         $pdf->SetFont('Arial','B',12);
//         $pdf->Cell(3,6,"RAHASIA",0,0,'L');
//         $pdf->Ln(7);
//         $pdf->Ln();
//         $pdf->SetFont('Arial','B',12); 
//         $pdf->Cell(60,6,' ',0,0,'L');
//         $pdf->Cell(25,6,'BIODATA KARYAWAN',0,0,'L');
//         $pdf->Cell(80,6,' ',0,0,'C');
//         $pdf->SetFont('Arial','B',12);
//         $pdf->Cell(3,6,"",0,0,'L');
//         $pdf->Ln();
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(20,6,' ',0,0,'L');
//         // $pdf->Cell(25,6,'NPP',0,0,'L','true');
//         // $pdf->Cell(1,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(3,6,$r[reg_no],0,0,'L');
//         // $pdf->Ln(7);

//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(20,6,' ',0,0,'L');
//         // $pdf->Cell(25,6,'JABATAN',0,0,'L','true');
//         // $pdf->Cell(1,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(3,6,$r[pos_name],0,0,'L');
//         // $pdf->Ln(7);

//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(20,6,' ',0,0,'L');
//         // $pdf->Cell(25,6,'PANGKAT',0,0,'L','true');
//         // $pdf->Cell(1,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(3,6,$arrMaster[$r[rank]],0,0,'L');
//         // $pdf->Ln(7);

//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(20,6,' ',0,0,'L');
//         // $pdf->Cell(25,6,'GRADE',0,0,'L','true');
//         // $pdf->Cell(1,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(3,6,$arrMaster[$r[grade]],0,0,'L');
//         // $pdf->Ln(7); 

//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(20,6,' ',0,0,'L');
//         // $pdf->Cell(25,6,'SKALA',0,0,'L','true');
//         // $pdf->Cell(1,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(3,6,$arrMaster[$r[skala]],0,0,'L');
//         $pdf->SetFont('Arial','B',8);
//         $pdf->Line(15,40,195,40);
//         $pdf->Line(15,40,15,100);
//         $pdf->Line(195,40,195,100);
//         $pdf->Line(15,100,195,100);  
//         $pdf->Ln(4);
//         $gelar="";
//         if(!empty($r[depan]))
//             $gelar=$r[depan]." ".$r[belakang];

//         $r[name] =explode(",",$r[name]);


//         $cekGelar="-";
//         if(!empty($r[depan]))
//             $cekGelar=getField("SELECT srtf_name from emp_training where parent_id='$par[id]' and perihal='AAMAI'");

//         $pdf->Ln();
//         $pdf->SetLeftMargin(20);
//         $pdf->SetWidths(array(40,90));
//         $pdf->SetAligns(array('L')); 
//         $pdf->Row(array("NAMA\tb","".$r[name][0]." \t"));
//         $pdf->Row(array("NPP\tb","".$r[reg_no]."\t"));
//         $pdf->Row(array("GELAR DEPAN\tb","".$r[depan]."\t"));
//         $pdf->Row(array("GELAR BELAKANG\tb","".$r[belakang]."\t"));

//         $pdf->Ln(25);
        
        
        
//         $pdf->Ln(20);
//         $pdf->SetFont('Arial','',10);
//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));

//         $pdf->Row(array("DATA PRIBADI\tb"));
//         $pdf->SetFont('Arial','',8);
//         $pdf->SetWidths(array(45,45,45,45));
//         $pdf->SetAligns(array('L'));

//         $pdf->Row(array("TEMPAT LAHIR\tb","".$arrMaster[$r[birth_place]]."\t","STATUS\tb",$arrMaster[$r[cat]]."\t"));
//         $pdf->Row(array("TANGGAL LAHIR\tb","".getTanggal($r[birth_date],'t')."\t","NO.KTP\tb","".$r[ktp_no]."\t"));
//         $pdf->Row(array("JENIS KELAMIN\tb","".$r[gender]."\t","EMAIL\tb","".$r[email]."\t"));
//         $pdf->Row(array("AGAMA\tb","".$arrMaster[$r[religion]]."\t","FACEBOOK\tb","".$r[facebook]."\t"));
//         $pdf->Row(array("NOMOR HP\tb","".$r[cell_no]."\t","INSTAGRAM\tb","".$r[instagram]."\t"));
//         // $pdf->Row(array("TEMPAT LAHIR\tb","".$arrMaster[$r[birth_place]]."\tb","STATUS\tb","\tb"));

//         // $pdf->SetFont('Arial','B',13);
//         // $pdf->setFillColor(0,0,0);
//         // $pdf->SetTextColor(255,255,255);

//         // $pdf->Cell(180,8,'DATA PRIBADI',0,0,'C','#000000');
//         // $pdf->SetTextColor(0,0,0);

                
//         // $pdf->setFillColor(230,230,230);
//         // $pdf->Ln(15);    
//         // $pdf->SetFont('Arial','',8);

        

    

        
//         // $pdf->Cell(35,6,'TEMPAT LAHIR',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[birth_place]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'STATUS',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // //$pdf->Cell(40,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'TANGGAL LAHIR',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,getTanggal($r[birth_date],'t'),0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'NO.KTP',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[ktp_no],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // //$pdf->Cell(40,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'JENIS KELAMIN',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[gender],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'EMAIL',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[email],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // //$pdf->Cell(40,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'AGAMA',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
    
//         // $pdf->Ln(7);

//         // //$pdf->Cell(40,6,' ',0,0,'L');
//         // // $pdf->Cell(35,6,'ALAMAT DOMISILI',0,0,'L','true');
//         // // $pdf->Cell(3,6,' ',0,0,'C');
//         // // $pdf->SetFont('Arial','',8);
//         // // $pdf->Cell(5,6,$r[dom_address],0,0);
//         // // $pdf->SetFont('Arial','',8);
//         // // $pdf->Ln(7);

//         // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[dom_prov]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'FACEBOOK',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);
//         // // $pdf->Cell(60,6,' ',0,0,'C');
        
        
//         // // $pdf->Ln(7);

//         // // $pdf->Cell(35,6,'TELP RUMAH',0,0,'L','true');
//         // // $pdf->Cell(3,6,' ',0,0,'C');
//         // // $pdf->SetFont('Arial','',8);
//         // // $pdf->Cell(5,6,$r[phone_no],0,0);
//         // // $pdf->SetFont('Arial','',8);
//         // // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'NOMOR HP',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[cell_no],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'INSTAGRAM',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'instagraam@inta.com',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);
//         $pdf->Ln(7);
//         $sql="select * from emp_char where parent_id = '$par[id]'";
//         $res=db($sql);
//         $r=mysql_fetch_array($res);

//         // $pdf->Cell(35,6,'KARAKTER PRIBADI',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[characteristic],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);
//         // $pdf->Cell(35,6,'HOBI',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[hobby],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);
//         // $pdf->Cell(35,6,'KEAHLIAN KHUSUS',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[abilities],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);
//         // $pdf->Cell(35,6,'ORGANISASI SOSIAL',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[organization],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("POSISI SAAT INI\tb"));
//         $pdf->SetFont('Arial','',8);
//         $sql="SELECT * from emp_phist where parent_id = '$par[id]' and status='1' order by start_date desc";
//         $res=db($sql);
//         $getUnit =getField("SELECT location from dta_pegawai where parent_id='$par[id]'");
//         $r=mysql_fetch_array($res);
//         $pdf->SetWidths(array(45,45,45,45));
//         $pdf->SetAligns(array('L'));
//   //    $r_[leave_date] = !empty($r_[leave_date]) ? formatDate($r_[leave_date]) : "current";
//         // $r_[masaKerjaEfektif] = formatDate($r_[join_date])." - ".$r_[leave_date];

        
//         $pdf->Row(array("JABATAN\tb","".strtoupper($r[pos_name])."\t","MKE\tb",formatDate($r_[join_date])."\t"));
//     //  $r_[leave_date] = !empty($r_[leave_date]) ? $r_[leave_date] : "current";
//         // $r[mkePeriode] = substr($r_[join_date], 0, 4)." - ".$r_[leave_date];
//         $mkePeriode = getField("SELECT replace(
//                 case when coalesce(leave_date,NULL) IS NULL or leave_date='0000-00-00' or leave_date='' THEN
//                 CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
//                 ELSE
//                 CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
//                 END,' 0 bln','') masaKerja from dta_pegawai where parent_id='$par[id]'");
     
//         $pdf->Row(array("UNIT KERJA\tb",$arrMaster[$getUnit]."\t","MKE PERIODE\tb","".$mkePeriode."\t"));
//     //  $r_[end_date] = !empty($r[end_date]) ? formatDate($r[end_date]) : "current";
//         // $r[tglMasaKerjaJabatan] = formatDate($r[start_date])." - ".$r_[end_date];
        
//         $arrData = arrayQuery("select kodeData, namaData from mst_data");
//         $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
//         $getDateMKJs=getField("select min(start_date) from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."' and start_date!='0000-00-00' and start_date is not null");
//         if(empty($getDateMKJs)) $getDateMKJs = $r[start_date];
//         if(in_array($getDateMKJs, array("", "0000-00-00"))) $getDateMKJs = $r[join_date];
//         if($getDateMKJs < $r[join_date]) $getDateMKJs = $r[join_date];

//         $pdf->Row(array("GRADE / TJ\tb","".$arrMaster[$r[grade]]."\t","MKJ\tb","".formatDate($getDateMKJs)."\t"));
//     //  $r_[end_date] = !empty($r[end_date]) ? substr($r[end_date],0,4) : "current";
//         // $r[mkjPeriode] = substr($r[start_date],0,4)." - ".$r_[end_date];
//         $start_date=getField("select start_date from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."'  and status='1' and start_date is not null");
//         // if(empty($start_date)) $start_date = $r[start_date];
//         // if(in_array($start_date, array("", "0000-00-00"))) $start_date = $r[join_date];
//         // if($start_date < $r[join_date]) $start_date = $r[join_date];
        
//         // $end_date = $r[end_date];
//         // if(in_array($end_date, array("", "0000-00-00"))) $end_date = date("Y-m-d");
//         // if($end_date > date("Y-m-d")) $end_date = date("Y-m-d");
//         // $dMKJP = selisihHari($start_date, $end_date);
//         // $yMKJP = getAngka(floor($dMKJP/ 365));
//         // $mMKJP = getAngka(floor(($dMKJP % 365) / 30));

//   //    $mkjPPeriode = empty($mMKJP) ? "" : $yMKJP." thn ".$mMKJP." bln";       
//         $arrData = arrayQuery("select kodeData, namaData from mst_data");
//         $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
//         $start_date=getField("select min(start_date) from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."' and start_date!='0000-00-00' and start_date is not null");
//         if(empty($start_date)) $start_date = $r[start_date];
//         if(in_array($start_date, array("", "0000-00-00"))) $start_date = $r[join_date];
//         if($start_date < $r[join_date]) $start_date = $r[join_date];
        
//         $end_date = $r[end_date];
//         if(in_array($end_date, array("", "0000-00-00"))) $end_date = date("Y-m-d");
//         if($end_date > date("Y-m-d")) $end_date = date("Y-m-d");
//         $dMKJ = selisihHari($start_date, $end_date);
//         $yMKJ = getAngka(floor($dMKJ/ 365));
//         $mMKJ = getAngka(floor(($dMKJ % 365) / 30));
        
//         $grade = $arrData[$r[grade]];
//         if(is_array($arrGrade)){
//             reset($arrGrade);
//             while(list($id, $val) = each($arrGrade)){
//                 if (preg_match("/\b".$val."\b/i", $grade))
//                     $grade = "";
//             }
//         }
//         if (preg_match("/\bnon\b/i", $grade))
//             $grade = "";
        
//         $mkjPeriode = empty($grade) ? "" : $yMKJ." thn ".$mMKJ." bln";      

//         $pdf->Row(array("SG\tb","".$arrMaster[$r[skala]]."\t","MKJ PERIODE\tb","".$mkjPeriode."\t"));

        


    

//         // $pdf->Cell(35,6,'JABATAN',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,strtoupper($r[pos_name]),0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'MKE',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $r_[leave_date] = !empty($r_[leave_date]) ? $r_[leave_date] : "current";
//         // $r_[masaKerjaEfektif] = $r_[join_date]." - ".$r_[leave_date];
//         // $pdf->Cell(5,6,$r_[masaKerjaEfektif],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7); 

//         // $pdf->Cell(35,6,'UNIT KERJA',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'MKE PERIODE',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $r_[leave_date] = !empty($r_[leave_date]) ? substr($r_[leave_date], 0, 4) : "current";
//      //     $r[mkePeriode] = substr($r_[join_date], 0, 4)." - ".$r_[leave_date];
//         // $pdf->Cell(5,6,$r[mkePeriode],0,0);
//         // $pdf->SetFont('Arial','',8);
        
//         // $pdf->Ln(7); 

//         // $pdf->Cell(35,6,'GRADE / TJ',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[grade]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'MKJ',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $r_[end_date] = !empty($r[end_date]) ? $r[end_date] : "current";
//      //     $r[tglMasaKerjaJabatan] = $r[start_date]." - ".$r_[end_date];
//         // $pdf->Cell(5,6,$r[tglMasaKerjaJabatan],0,0);
//         // $pdf->SetFont('Arial','',8);
            
//         // $pdf->Ln(7); 

//         // $pdf->Cell(35,6,'MKJ',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $r_[end_date] = !empty($r[end_date]) ? $r[end_date] : "current";
//      //     $r[tglMasaKerjaJabatan] = $r[start_date]." - ".$r_[end_date];
//         // $pdf->Cell(5,6,$r[tglMasaKerjaJabatan],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'MKJ PERIODE',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $r_[end_date] = !empty($r[end_date]) ? substr($r[end_date],0,4) : "current";
//      //  $r[mkjPeriode] = substr($r[start_date],0,4)." - ".$r_[end_date];
//         // $pdf->Cell(5,6,$r[mkjPeriode],0,0);
//         // $pdf->SetFont('Arial','',8);
            
//         // $pdf->Ln(7); 

    
//         // // $pdf->Cell(0,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'TMT MENJABAT',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'TANGGAL GRADE TJ',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // // $pdf->Cell(0,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'TMT MENJABAT PER',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'GRADE TJ PERIODE',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7); 

//         // // $pdf->Cell(0,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'TMT SG',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'TANGGAL PENSIUN',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // // $pdf->Cell(0,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'TMT SG PERIODE',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,'',0,0);
//         // $pdf->SetFont('Arial','',8);
//         $pdf->Ln(0);    

        

        
        

//         // }

//         $pdf->AddPage();

        

//         //POSISI

//         $posisiexist = getField("select count(id) from emp_phist where parent_id = '$par[id]'");
//         // if($posisiexist!=0){

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT JABATAN\tb"));
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,25,45,70,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","NOMOR SK\tb","TGL EFEKTIF\tb","POSISI\tb","KATEGORI\tb"));
//     $pdf->SetWidths(array(10,25,45,70,30));
//     $pdf->SetAligns(array('L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_phist where parent_id='$par[id]' order by sk_date desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//       $r[end_date] = !empty($r[end_date]) ? formatDate($r[end_date]) : "current";
//       $r[tglEfektif] = formatDate($r[start_date])." - ".$r[end_date];
//        $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[tglEfektif]."\tu",$r[pos_name]."\tu",$arrMaster[$r[kategori_id]]."\tu"));
//        // $total += $r[nilai];
//     }

//     $pdf->Ln();
//     //KARIR

//     $karirexist = getField("select count(id) from emp_career where parent_id = '$par[id]'");
//     // if($karirexist!=0){

//     $pdf->SetWidths(array(180));
//     $pdf->SetAligns(array('L'));
//     $pdf->SetFont('Arial','',10);
//     $pdf->Row(array("RIWAYAT TUGAS\tb"));   
//     $pdf->SetFont('Arial','',8);

//     $pdf->SetWidths(array(10,30,30,80,30));
// $pdf->SetAligns(array('L','L','L','L','L','L'));

// $pdf->Row(array("NO.\tb","NOMOR SK\tb","TANGGAL\tb","PERIHAL\tb","TIPE\tb",));


//     if($karirexist!=0){
//         $pdf->SetWidths(array(10,30,30,80,30));
// $pdf->SetAligns(array('L','L','L','L','L','L'));
//     $pdf->SetFont('Arial','',8);
// $sql = "select * from emp_career where parent_id='$par[id]' order by sk_date desc";
// $res=db($sql);
// $no=0;
// while ($r=mysql_fetch_array($res)) {
//   $no++;
//   $no = $no.".";
//    $pdf->Row(array($no."\tu",$r[sk_no]."\tu",formatDate($r[sk_date])."\tu",$r[sk_subject]."\tu",$arrMaster[$r[sk_type]]."\tu"));
//    // $total += $r[nilai];
// }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"."\tu"));
// }

// $pdf->Ln();
//     // }

// //PELATIHAN

//         $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1860' order by idKategori,createDate asc ");
//         // if($trainingexist!=0){

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT PENJENJANGAN\tb"));    
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,40,105,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","PENJENJANGAN\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
//     if($trainingexist!=0){
//     $pdf->SetWidths(array(10,40,105,25));
//     $pdf->SetAligns(array('L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1860' order by idKategori,createDate asc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }

// $pdf->Ln();
//     // }

// //PELATIHAN

//         $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1861' order by idKategori,createDate asc ");
//         // if($trainingexist!=0){

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT MODUL\tb"));   
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,40,30,75,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","MODUL\tb","SERTIFIKASI\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
//     if($trainingexist!=0){
//     $pdf->SetWidths(array(10,40,30,75,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1861' order by idKategori,createDate asc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$arrMaster[$r[idKategori]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }

// $pdf->Ln();
//     // }

// //PELATIHAN

//         $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1862' order by idKategori,createDate asc ");
//         // if($trainingexist!=0){

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("SERTIFIKASI PROFESI\tb")); 
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,40,30,75,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","SERTIFIKASI/GELAR\tb","SERTIFIKASI\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
//     if($trainingexist!=0){
//     $pdf->SetWidths(array(10,40,30,75,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1862' order by idKategori,createDate asc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$arrMaster[$r[idKategori]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }

// $pdf->Ln();
//     // }

// //PELATIHAN

//         $trainingexist = getField("select count(id) from emp_training where parent_id = '$par[id]' ");
//         // if($trainingexist!=0){

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT PELATIHAN\tb"));   
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,90,30,30,20));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","PERIHAL\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
//     if($trainingexist!=0){
//     $pdf->SetWidths(array(10,90,30,30,20));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_training where parent_id='$par[id]' order by trn_year desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[trn_subject]."\tu",$arrMaster[$r[trn_cat]]."\tu",$arrMaster[$r[trn_type]]."\tu",$r[trn_year]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    

// $pdf->Ln();
//     // }

// //PELATIHAN

//         $trainingexist = getField("select count(id) from emp_training where parent_id = '$par[id]' ");
//         // if($trainingexist!=0){

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT PELATIHAN\tb"));   
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,90,30,30,20));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","PERIHAL\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
//     if($trainingexist!=0){
//     $pdf->SetWidths(array(10,90,30,30,20));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_training where parent_id='$par[id]' order by trn_year desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[trn_subject]."\tu",$arrMaster[$r[trn_cat]]."\tu",$arrMaster[$r[trn_type]]."\tu",$r[trn_year]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    
    
    
//         // }
//         $pdf->Ln();

//         //PENGHARGAAN

//         $rewardexist = getField("select count(id) from emp_reward where parent_id = '$par[id]'");
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT PENGHARGAAN\tb")); 
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,45,30,30,25,20,20));
//     $pdf->SetAligns(array('L','L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","NOMOR PENGHARGAAN\tb","PERIHAL\tb","PENERBIT\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
//     if($rewardexist!=0){
//     $pdf->SetWidths(array(10,45,30,30,25,20,20));
//     $pdf->SetAligns(array('L','L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_reward where parent_id='$par[id]' order by rwd_year desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[rwd_no]."\tu",$r[rwd_subject]."\tu",$r[rwd_agency]."\tu",$arrMaster[$r[rwd_cat]]."\tu",$arrMaster[$r[rwd_type]]."\tu",$r[rwd_year]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    
//         // }

//         $pdf->Ln();
    
//         // }

// //PENDIDIKAN

//         $eduexist = getField("select count(id) from emp_edu where parent_id = '$par[id]'");
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT PENDIDIKAN\tb"));  

//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,30,50,35,30,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","TINGKATAN\tb","NAMA LEMBAGA\tb","JURUSAN\tb","KOTA\tb","TAHUN\tb"));
//     if($eduexist!=0){
//     $pdf->SetWidths(array(10,30,50,35,30,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_edu where parent_id='$par[id]' order by edu_year desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$arrMaster[$r[edu_type]]."\tu",$r[edu_name]."\tu",$arrMaster[$r[edu_dept]]."\tu",$arrMaster[$r[edu_city]]."\tu",$r[edu_year]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    
//         // }

//         $pdf->Ln();
    


// //KESEHATAN

//         $healthexist = getField("select count(id) from emp_health where parent_id = '$par[id]'");
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT KESEHATAN\tb"));   
//         // $pdf->Ln(15);    
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,30,50,40,50));
//     $pdf->SetAligns(array('L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","TANGGAL\tb","NAMA TEMPAT\tb","DOKTER\tb","KETERANGAN\tb"));
//     if($healthexist!=0){
//     $pdf->SetWidths(array(10,30,50,40,50));
//     $pdf->SetAligns(array('L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_health where parent_id='$par[id]' order by hlt_date desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",getTanggal($r[hlt_date])."\tu",$r[hlt_place]."\tu",$r[hlt_doctor]."\tu",$r[hlt_remark]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    
    
//         // }

//         $pdf->Ln();


// $familyexist = getField("select count(id) from emp_family where parent_id = '$par[id]'");
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("KELUARGA\tb"));        
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,80,30,60));
//     $pdf->SetAligns(array('L','L','L'));

//         $pdf->Row(array("NO.\tb","NAMA\tb","HUBUNGAN\tb","TTL\tb"));
//         if($familyexist!=0){
//     $pdf->SetWidths(array(10, 80, 30, 60));
//     $pdf->SetAligns(array('L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_family where parent_id='$par[id]' order by birth_date desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[name]."\tu",$arrMaster[$r[rel]]."\tu",$arrMaster[$r[birth_place]]." ".getTanggal($r[birth_date])."\tu"));
//        $total += $r[nilai];
//         }
//     }else{
//             $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('C'));
//             $pdf->Row(array("-- data kosong --"));
//         }
    
//         // }

//         $pdf->Ln();
//         $pdf->AddPage();
    

// $kontakexist = getField("select count(id) from emp_contact where parent_id = '$par[id]' ");
//         // if($kontakexist!=0){
//             // $pdf->AddPage();

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("INFORMASI ALAMAT DAN NO.TELP\tb"));
//         $pdf->SetFont('Arial','',8);

        
//         $sql="select * from emp_contact where parent_id = '$par[id]' order by cre_date desc";
//         $res=db($sql);
//         $r=mysql_fetch_array($res);

//         $pdf->SetWidths(array(90,90));
//         $pdf->SetAligns(array('L'));

//         $pdf->Row(array("SERUMAH\tb","BEDA RUMAH\tb"));         
        
//         $pdf->SetWidths(array(45,45,45,45));
//         $pdf->SetAligns(array('L'));
//         $pdf->Row(array("NAMA\tb","".strtoupper($r[sr_nama])."\t","NAMA\tb","".strtoupper($r[br_nama])."\t"));
//         $pdf->Row(array("HUBUNGAN\tb","".$r[sr_hub]."\t","HUBUNGAN\tb","".$r[br_hub]."\t"));
//         $pdf->Row(array("NO. TELP\tb","".$r[sr_phone]."\t","NO. TELP\tb","".$r[br_hub]."\t"));
//         $pdf->Row(array("ALAMAT\tb","".$r[sr_address]."\t","ALAMAT\tb","".$r[br_address]."\t"));
//         $pdf->Row(array("PROVINSI\tb","".$r[sr_prov]."\t","PROVINSI\tb","".$r[br_prov]."\t"));
//         $pdf->Row(array("KAB/KOTA\tb","".$r[sr_city]."\t","KAB/KOTA\tb","".$r[br_city]."\t"));
//      //    $pdf->Ln(7);
//         // $pdf->Cell(35,6,'NAMA',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,strtoupper($r[sr_nama]),0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'NAMA',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,strtoupper($r[br_nama]),0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

        
//         // // $pdf->Cell(0,6,' ',0,0,'L');
//         // $pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[sr_hub],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[br_hub],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // $pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[sr_phone],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[br_phone],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);


//         // $pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[sr_address],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$r[br_address],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[sr_prov]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[br_prov]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Ln(7);

//         // $pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[sr_city]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(60,6,' ',0,0,'C');
//         // $pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
//         // $pdf->Cell(3,6,' ',0,0,'C');
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(5,6,$arrMaster[$r[br_city]],0,0);
//         // $pdf->SetFont('Arial','',8);
//         $pdf->Ln();
        

// //PERINGATAN

//         $punishexist = getField("select count(id) from emp_punish where parent_id = '$par[id]'");
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("RIWAYAT PERINGATAN\tb"));  
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,50,30,30,30,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","NOMOR PERINGATAN\tb","PERIHAL\tb","PENERBIT\tb","TIPE\tb","TAHUN\tb"));
//     if($punishexist!=0){
//     $pdf->SetWidths(array(10,50,30,30,30,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_punish where parent_id='$par[id]' order by pnh_year desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[pnh_no]."\tu",$r[pnh_subject]."\tu",$r[pnh_agency]."\tu",$arrMaster[$r[pnh_type]]."\tu",$r[pnh_year]."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    
//         // }

//         $pdf->Ln();
        
// //ASET

//         $healthexist = getField("select count(id) from emp_asset where parent_id = '$par[id]'");
        

//         $pdf->SetWidths(array(180));
//         $pdf->SetAligns(array('L'));
//         $pdf->SetFont('Arial','',10);
//         $pdf->Row(array("PINJAMAN ASET\tb"));   
//         $pdf->SetFont('Arial','',8);

//         $pdf->SetWidths(array(10,30,30,30,50,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//     $pdf->Row(array("NO.\tb","ASET\tb","NO. SERI\tb","KATEGORI\tb","TIPE\tb","TANGGAL\tb"));
//     if($healthexist!=0){
//     $pdf->SetWidths(array(10,30,30,30,50,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//         $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_asset where parent_id='$par[id]' order ast_date desc";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[ast_name]."\tu",$r[ast_no]."\tu",$r[ast_usage]."\tu",$arrMaster[$r[ast_type]]."\tu",getTanggal($r[ast_date])."\tu"));
//        // $total += $r[nilai];
//     }
// }else{
//     $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//     $pdf->Row(array("-- data kosong --"));
// }
    
//         // }

//         $pdf->Ln();
        
// //      //KONTRAK

// //      $kontrakexist = getField("select count(id) from emp_pcontract where parent_id = '$par[id]'");

// //      // if($kontrakexist!=0){
// //          // $pdf->AddPage();

// //      $pdf->SetFont('Arial','B',13);
// //      $pdf->setFillColor(0,0,0);
// //      $pdf->SetTextColor(255,255,255);
// //      $pdf->Cell(180,8,'RIWAYAT KONTRAK',0,0,'C','#000000');
// //      $pdf->SetTextColor(0,0,0);              
// //      $pdf->setFillColor(230,230,230);
// //      $pdf->Ln(15);   
// //      $pdf->SetFont('Arial','',8);

// //      $pdf->SetWidths(array(10,30,50,30,30,30,30));
// //     $pdf->SetAligns(array('L','L','L','L','L','L'));

// //  $pdf->Row(array("NO.\tb","NOMOR SK\tb","PERIHAL\tb","TGL SK\tb","TGL BERLAKU\tb","TGL BERAKHIR\tb"));
// //  if($kontrakexist!=0){
// //      $pdf->SetWidths(array(10,30,50,30,30,30,30));
// //     $pdf->SetAligns(array('L','L','L','L','L','L'));
// //      $pdf->SetFont('Arial','',8);

// //     $sql = "select * from emp_pcontract where parent_id='$par[id]'";
// //     $res=db($sql);
// //     $no=0;
// //     while ($r=mysql_fetch_array($res)) {
// //       $no++;
// //       $no = $no.".";
// //        $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[subject]."\tu",$r[sk_date]."\tu",$r[start_date]."\tu",$r[end_date]."\tu"));
// //        // $total += $r[nilai];
// //  }
// // }else{
// //  $pdf->SetWidths(array(180));
// // $pdf->SetAligns(array('C'));
// //  $pdf->Row(array("-- data kosong --"));
// // }
    
// //      // }

// //      $pdf->Ln();
// //      $pdf->Ln();

        

        

        


        

// //      $bankexist = getField("select count(id) from emp_bank where parent_id = '$par[id]'");
// //      // if($bankexist!=0){

// //      $pdf->SetFont('Arial','B',13);
// //      $pdf->setFillColor(0,0,0);
// //      $pdf->SetTextColor(255,255,255);
// //      $pdf->Cell(180,8,'REKENING BANK',0,0,'C','#000000');
// //      $pdf->SetTextColor(0,0,0);              
// //      $pdf->setFillColor(230,230,230);
// //      $pdf->Ln(15);   
// //      $pdf->SetFont('Arial','',8);

// //      $pdf->SetWidths(array(10,60,30,30,50));
// //     $pdf->SetAligns(array('L','L','L','L','L'));

// //  $pdf->Row(array("NO.\tb","NAMA BANK\tb","NO REKENING\tb","CABANG\tb","REMARK\tb"));
// //  if($bankexist!=0){
// //     $pdf->SetWidths(array(10,60,30,30,50));
// //     $pdf->SetAligns(array('L','L','L','L','L'));
// //      $pdf->SetFont('Arial','',8);

// //     $sql = "select * from emp_bank where parent_id='$par[id]'";
// //     $res=db($sql);
// //     $no=0;
// //     while ($r=mysql_fetch_array($res)) {
// //       $no++;
// //       $no = $no.".";
// //        $pdf->Row(array($no."\tu",$arrMaster[$r[bank_id]]."\tu",$r[branch]."\tu",$r[account_no]."\tu",$r[remark]."\tu"));
// //        $total += $r[nilai];
// //  }
// // }else{
// //  $pdf->SetWidths(array(180));
// // $pdf->SetAligns(array('C'));
// //  $pdf->Row(array("-- data kosong --"));
// // }
    
// //      // }

// //      $pdf->Ln();
// //      $pdf->Ln();


        

        

        

//         //KERJA

// //      $workexist = getField("select count(id) from emp_pwork where parent_id = '$par[id]'");
        

// //      $pdf->SetFont('Arial','B',13);
// //      $pdf->setFillColor(0,0,0);
// //      $pdf->SetTextColor(255,255,255);
// //      $pdf->Cell(180,8,'RIWAYAT KERJA',0,0,'C','#000000');
// //      $pdf->SetTextColor(0,0,0);              
// //      $pdf->setFillColor(230,230,230);
// //      $pdf->Ln(15);   
// //      $pdf->SetFont('Arial','',8);

// //      $pdf->SetWidths(array(10,60,50,35,25));
// //     $pdf->SetAligns(array('L','L','L','L','L','L'));

// //  $pdf->Row(array("NO.\tb","PERUSAHAAN\tb","JABATAN\tb","BAGIAN\tb","TAHUN\tb"));
// //  if($workexist!=0){
// //     $pdf->SetWidths(array(10,60,50,35,25));
// //     $pdf->SetAligns(array('L','L','L','L','L','L'));
// //      $pdf->SetFont('Arial','',8);

// //     $sql = "select *,concat(year(start_date),' - ',year(end_date)) as edu_years from emp_pwork where parent_id='$par[id]'";
// //     $res=db($sql);
// //     $no=0;
// //     while ($r=mysql_fetch_array($res)) {
// //       $no++;
// //       $no = $no.".";
// //        $pdf->Row(array($no."\tu",$r[company_name]."\tu",$r[position]."\tu",$r[dept]."\tu",$r[edu_years]."\tu"));
// //        // $total += $r[nilai];
// //  }
// // }else{
// //  $pdf->SetWidths(array(180));
// // $pdf->SetAligns(array('C'));
// //  $pdf->Row(array("-- data kosong --"));
// // }
    
// //      // }

// //      $pdf->Ln();
// //      $pdf->Ln();

        

        

//         //REKENING

    

//         // $pdf->SetFont('Arial','B',13);
//         // $pdf->Cell(180,6,'PENGALAMAN PRIBADI',0,0,'C');
//         // $pdf->Ln(10);    
//         // $pdf->SetFont('Arial','',8);
//         // $sql_="select *,concat(year(start_date),' - ',year(end_date))  dtRange from emp_pwork where parent_id = '$par[id]'";
//         // $res_=db($sql_);
//         // while($r_=mysql_fetch_array($res_)){ 
//         // $pdf->Cell(40,6,$r_[dtRange],0,0,'L');
//         // $pdf->Cell(5,6,$r_[position].' pada '.$r_[company_name],0,0,'L');
        
//         // $pdf->Ln(5);     
//         // }
//         // $pdf->Ln(10);        
//         // $pdf->SetFont('Arial','B',13);
//         // $pdf->Cell(180,6,'PENDIDIKAN FORMAL',0,0,'C');
//         // $pdf->Ln(10);    
//         // $pdf->SetFont('Arial','',8);
//         // $sql_="select * from emp_edu where parent_id = '$par[id]'";
//         // $res_=db($sql_);
//         // while($r_=mysql_fetch_array($res_)){ 
//         // $pdf->Cell(40,6,'Lulus Tahun '.$r_[edu_year],0,0,'L');
//         // $pdf->Cell(5,6,$r_[edu_name],0,0,'L');
//         // $pdf->Ln(5);     
//         // }
//         // $pdf->Ln(10);        

//         // $pdf->SetFont('Arial','B',13);
//         // $pdf->Cell(180,6,'PRESTASI',0,0,'C');
//         // $pdf->Ln(10);    
//         // $pdf->SetFont('Arial','',8);

//         // $sql_="select * from emp_reward where parent_id = '$par[id]'";
//         // $res_=db($sql_);
//         // while($r_=mysql_fetch_array($res_)){ 
//         // $pdf->Cell(40,6,$arrMaster[$r_[rwd_type]],0,0,'L');
//         // $pdf->Cell(5,6,$r_[rwd_subject],0,0,'L');
//         // $pdf->Ln(5);     
//         // }

//         // $pdf->SetFont('Arial','B',13);
//         // $pdf->Cell(180,6,'KEAHLIAN KHUSUS',0,0,'C');
//         // $pdf->Ln(10);    
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(180,6,$r__[abilities],0,0,'L');
//         // $pdf->Ln(10);

//         // $pdf->SetFont('Arial','B',13);
//         // $pdf->Cell(180,6,'ORGANISASI SOSIAL',0,0,'C');
//         // $pdf->Ln(10);    
//         // $pdf->SetFont('Arial','',8);
//         // $pdf->Cell(180,6,$r__[organization],0,0,'L');
            
        
        
        
//         $pdf->Output(); 
//     }
function pdf(){
    global $db,$s,$inp,$par,$fFile,$arrTitle,$arrParam;
    require_once 'plugins/PHPPdf.php';
    
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
    $arrName = arrayQuery("select id, name from emp");

    $sql="select *,(CASE WHEN gender = 'M' THEN 'Laki-Laki' ELSE (CASE WHEN gender = 'F' THEN 'Perempuan' ELSE '' END) END) as gender from emp where id = '$par[id]'";
    $res=db($sql);
    $r=mysql_fetch_array($res);

    $r_[join_date] = $r[join_date];
    $r_[leave_date] = $r[leave_date];
    $sql__="select * from emp_char where parent_id = '$par[id]'";
    $res__=db($sql__);
    $r__=mysql_fetch_array($res__);
    
    $pdf = new PDF('P','mm','A4');
    $pdf->AddPage();
    $pdf->SetLeftMargin(15);
    
    
    $pdf->Cell(30,20,'',0,0,'L');
    if(!empty($r[pic_filename])){
    $gambar = "files/emp/pic/".$r[pic_filename];
    
    $pdf->Image($gambar, 155,40,35);
    }else{
    $gambar = "files/emp/pic/nophoto.jpg";
    $pdf->Image($gambar,155,40,40);
    }
    $pdf->Cell(40,6,'',0,0,'L');
    // $pdf->Ln(1); 
    

    $pdf->Ln();
    $pdf->SetFont('Arial','B',12);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','B',8);
        
    $pdf->setFillColor(230,230,230);
    // $pdf->Ln(6); 
    $pdf->SetFont('Arial','B',12); 
    $pdf->Cell(60,6,' ',0,0,'L');
    $pdf->Cell(25,6,'',0,0,'L');
    $pdf->Cell(80,6,' ',0,0,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(3,6,"RAHASIA",0,0,'L');
    $pdf->Ln(7);
    $pdf->Ln();
    $pdf->SetFont('Arial','B',12); 
    $pdf->Cell(60,6,' ',0,0,'L');
    $pdf->Cell(25,6,'BIODATA KARYAWAN',0,0,'L');
    $pdf->Cell(80,6,' ',0,0,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(3,6,"",0,0,'L');
    $pdf->Ln();
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'NPP',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$r[reg_no],0,0,'L');
    // $pdf->Ln(7);

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'JABATAN',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$r[pos_name],0,0,'L');
    // $pdf->Ln(7);

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'PANGKAT',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$arrMaster[$r[rank]],0,0,'L');
    // $pdf->Ln(7);

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'GRADE',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$arrMaster[$r[grade]],0,0,'L');
    // $pdf->Ln(7); 

    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(20,6,' ',0,0,'L');
    // $pdf->Cell(25,6,'SKALA',0,0,'L','true');
    // $pdf->Cell(1,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(3,6,$arrMaster[$r[skala]],0,0,'L');
    $pdf->SetFont('Arial','B',8);
    $pdf->Line(15,40,195,40);
    $pdf->Line(15,40,15,100);
    $pdf->Line(195,40,195,100);
    $pdf->Line(15,100,195,100);  
    $pdf->Ln(4);
    $gelar="";
    if(!empty($r[depan]))
      $gelar=$r[depan]." ".$r[belakang];

    $r[name] =explode(",",$r[name]);


    $cekGelar="-";
    if(!empty($r[depan]))
      $cekGelar=getField("SELECT srtf_name from emp_training where parent_id='$par[id]' and perihal='AAMAI'");

    $pdf->Ln();
    $pdf->SetLeftMargin(20);
    $pdf->SetWidths(array(40,90));
      $pdf->SetAligns(array('L')); 
      $pdf->Row(array("NAMA\tb","".$r[name][0]." \t"));
      $pdf->Row(array("NPP\tb","".$r[reg_no]."\t"));
      $pdf->Row(array("GELAR DEPAN\tb","".$r[depan]."\t"));
      $pdf->Row(array("GELAR BELAKANG\tb","".$r[belakang]."\t"));

    $pdf->Ln(25);
    
    
    $pdf->SetLeftMargin(15);
    $pdf->Ln(20);
    $pdf->SetFont('Arial','',10);
    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));

    $pdf->Row(array("DATA PRIBADI\tb"));
    $pdf->SetFont('Arial','',8);
    $pdf->SetWidths(array(45,45,45,45));
      $pdf->SetAligns(array('L'));

      $pdf->Row(array("TEMPAT LAHIR\tb","".$arrMaster[$r[birth_place]]."\t","STATUS\tb",$arrMaster[$r[cat]]."\t"));
      $pdf->Row(array("TANGGAL LAHIR\tb","".getTanggal($r[birth_date],'t')."\t","NO.KTP\tb","".$r[ktp_no]."\t"));
      $pdf->Row(array("JENIS KELAMIN\tb","".$r[gender]."\t","EMAIL\tb","".$r[email]."\t"));
      $pdf->Row(array("AGAMA\tb","".$arrMaster[$r[religion]]."\t","FACEBOOK\tb","".$r[facebook]."\t"));
      $pdf->Row(array("NOMOR HP\tb","".$r[cell_no]."\t","INSTAGRAM\tb","".$r[instagram]."\t"));
      // $pdf->Row(array("TEMPAT LAHIR\tb","".$arrMaster[$r[birth_place]]."\tb","STATUS\tb","\tb"));

    // $pdf->SetFont('Arial','B',13);
    // $pdf->setFillColor(0,0,0);
    // $pdf->SetTextColor(255,255,255);

    // $pdf->Cell(180,8,'DATA PRIBADI',0,0,'C','#000000');
    // $pdf->SetTextColor(0,0,0);

        
    // $pdf->setFillColor(230,230,230);
    // $pdf->Ln(15);  
    // $pdf->SetFont('Arial','',8);

    

  

    
    // $pdf->Cell(35,6,'TEMPAT LAHIR',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[birth_place]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'STATUS',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TANGGAL LAHIR',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,getTanggal($r[birth_date],'t'),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NO.KTP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[ktp_no],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'JENIS KELAMIN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[gender],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'EMAIL',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[email],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'AGAMA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
  
    // $pdf->Ln(7);

    // //$pdf->Cell(40,6,' ',0,0,'L');
    // // $pdf->Cell(35,6,'ALAMAT DOMISILI',0,0,'L','true');
    // // $pdf->Cell(3,6,' ',0,0,'C');
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Cell(5,6,$r[dom_address],0,0);
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Ln(7);

    // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[dom_prov]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'FACEBOOK',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // // $pdf->Cell(60,6,' ',0,0,'C');
    
    
    // // $pdf->Ln(7);

    // // $pdf->Cell(35,6,'TELP RUMAH',0,0,'L','true');
    // // $pdf->Cell(3,6,' ',0,0,'C');
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Cell(5,6,$r[phone_no],0,0);
    // // $pdf->SetFont('Arial','',8);
    // // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NOMOR HP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[cell_no],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'INSTAGRAM',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'instagraam@inta.com',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    $pdf->Ln(7);
    $sql="select * from emp_char where parent_id = '$par[id]'";
    $res=db($sql);
    $r=mysql_fetch_array($res);

    // $pdf->Cell(35,6,'KARAKTER PRIBADI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[characteristic],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // $pdf->Cell(35,6,'HOBI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[hobby],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // $pdf->Cell(35,6,'KEAHLIAN KHUSUS',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[abilities],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    // $pdf->Cell(35,6,'ORGANISASI SOSIAL',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[organization],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("POSISI SAAT INI\tb"));
    $pdf->SetFont('Arial','',8);
    $sql="SELECT * from emp_phist where parent_id = '$par[id]' and status='1' order by start_date desc";
    $res=db($sql);
    $getUnit =getField("SELECT location from dta_pegawai where parent_id='$par[id]'");
    $r=mysql_fetch_array($res);
    $pdf->SetWidths(array(45,45,45,45));
      $pdf->SetAligns(array('L'));
  //    $r_[leave_date] = !empty($r_[leave_date]) ? formatDate($r_[leave_date]) : "current";
    // $r_[masaKerjaEfektif] = formatDate($r_[join_date])." - ".$r_[leave_date];

    
      $pdf->Row(array("JABATAN\tb","".strtoupper($r[pos_name])."\t","MKE\tb",formatDate($r_[join_date])."\t"));
    //  $r_[leave_date] = !empty($r_[leave_date]) ? $r_[leave_date] : "current";
      // $r[mkePeriode] = substr($r_[join_date], 0, 4)." - ".$r_[leave_date];
      $mkePeriode = getField("SELECT replace(
        case when coalesce(leave_date,NULL) IS NULL or leave_date='0000-00-00' or leave_date='' THEN
        CONCAT(TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ', TIMESTAMPDIFF(MONTH, join_date,  CURRENT_DATE ) % 12, ' bln')
        ELSE
        CONCAT(TIMESTAMPDIFF(YEAR,  join_date, leave_date),' thn ', TIMESTAMPDIFF(MONTH, join_date,  leave_date) % 12, ' bln')
        END,' 0 bln','') masaKerja from dta_pegawai where parent_id='$par[id]'");
   
      $pdf->Row(array("UNIT KERJA\tb",$arrMaster[$getUnit]."\t","MKE PERIODE\tb","".$mkePeriode."\t"));
    //  $r_[end_date] = !empty($r[end_date]) ? formatDate($r[end_date]) : "current";
      // $r[tglMasaKerjaJabatan] = formatDate($r[start_date])." - ".$r_[end_date];
    
      $arrData = arrayQuery("select kodeData, namaData from mst_data");
    $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
      $getDateMKJs=getField("select min(start_date) from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."' and start_date!='0000-00-00' and start_date is not null");
    if(empty($getDateMKJs)) $getDateMKJs = $r[start_date];
    if(in_array($getDateMKJs, array("", "0000-00-00"))) $getDateMKJs = $r[join_date];
    if($getDateMKJs < $r[join_date]) $getDateMKJs = $r[join_date];

      $pdf->Row(array("GRADE / TJ\tb","".$arrMaster[$r[grade]]."\t","MKJ\tb","".formatDate($getDateMKJs)."\t"));
    //  $r_[end_date] = !empty($r[end_date]) ? substr($r[end_date],0,4) : "current";
      // $r[mkjPeriode] = substr($r[start_date],0,4)." - ".$r_[end_date];
      $start_date=getField("select start_date from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."'  and status='1' and start_date is not null");
    // if(empty($start_date)) $start_date = $r[start_date];
    // if(in_array($start_date, array("", "0000-00-00"))) $start_date = $r[join_date];
    // if($start_date < $r[join_date]) $start_date = $r[join_date];
    
    // $end_date = $r[end_date];
    // if(in_array($end_date, array("", "0000-00-00"))) $end_date = date("Y-m-d");
    // if($end_date > date("Y-m-d")) $end_date = date("Y-m-d");
    // $dMKJP = selisihHari($start_date, $end_date);
    // $yMKJP = getAngka(floor($dMKJP/ 365));
    // $mMKJP = getAngka(floor(($dMKJP % 365) / 30));

  //    $mkjPPeriode = empty($mMKJP) ? "" : $yMKJP." thn ".$mMKJP." bln";     
      $arrData = arrayQuery("select kodeData, namaData from mst_data");
    $arrGrade = explode(",", getField("select nilaiParameter from pay_parameter where kodeParameter='MKJ'"));
      $start_date=getField("select min(start_date) from emp_phist where parent_id='$par[id]' and grade='".$r[grade]."' and start_date!='0000-00-00' and start_date is not null");
    if(empty($start_date)) $start_date = $r[start_date];
    if(in_array($start_date, array("", "0000-00-00"))) $start_date = $r[join_date];
    if($start_date < $r[join_date]) $start_date = $r[join_date];
    
    $end_date = $r[end_date];
    if(in_array($end_date, array("", "0000-00-00"))) $end_date = date("Y-m-d");
    if($end_date > date("Y-m-d")) $end_date = date("Y-m-d");
    $dMKJ = selisihHari($start_date, $end_date);
    $yMKJ = getAngka(floor($dMKJ/ 365));
    $mMKJ = getAngka(floor(($dMKJ % 365) / 30));
    
    $grade = $arrData[$r[grade]];
    if(is_array($arrGrade)){
      reset($arrGrade);
      while(list($id, $val) = each($arrGrade)){
        if (preg_match("/\b".$val."\b/i", $grade))
          $grade = "";
      }
    }
    if (preg_match("/\bnon\b/i", $grade))
      $grade = "";
    
    $mkjPeriode = empty($grade) ? "" : $yMKJ." thn ".$mMKJ." bln";    

      $pdf->Row(array("SG\tb","".$arrMaster[$r[skala]]."\t","MKJ PERIODE\tb","".$mkjPeriode."\t"));

      


    

    // $pdf->Cell(35,6,'JABATAN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,strtoupper($r[pos_name]),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[leave_date] = !empty($r_[leave_date]) ? $r_[leave_date] : "current";
    // $r_[masaKerjaEfektif] = $r_[join_date]." - ".$r_[leave_date];
    // $pdf->Cell(5,6,$r_[masaKerjaEfektif],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7); 

    // $pdf->Cell(35,6,'UNIT KERJA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKE PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[leave_date] = !empty($r_[leave_date]) ? substr($r_[leave_date], 0, 4) : "current";
   //   $r[mkePeriode] = substr($r_[join_date], 0, 4)." - ".$r_[leave_date];
    // $pdf->Cell(5,6,$r[mkePeriode],0,0);
    // $pdf->SetFont('Arial','',8);
    
    // $pdf->Ln(7); 

    // $pdf->Cell(35,6,'GRADE / TJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[grade]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[end_date] = !empty($r[end_date]) ? $r[end_date] : "current";
   //   $r[tglMasaKerjaJabatan] = $r[start_date]." - ".$r_[end_date];
    // $pdf->Cell(5,6,$r[tglMasaKerjaJabatan],0,0);
    // $pdf->SetFont('Arial','',8);
      
    // $pdf->Ln(7); 

    // $pdf->Cell(35,6,'MKJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[end_date] = !empty($r[end_date]) ? $r[end_date] : "current";
   //   $r[tglMasaKerjaJabatan] = $r[start_date]." - ".$r_[end_date];
    // $pdf->Cell(5,6,$r[tglMasaKerjaJabatan],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'MKJ PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $r_[end_date] = !empty($r[end_date]) ? substr($r[end_date],0,4) : "current";
   //  $r[mkjPeriode] = substr($r[start_date],0,4)." - ".$r_[end_date];
    // $pdf->Cell(5,6,$r[mkjPeriode],0,0);
    // $pdf->SetFont('Arial','',8);
      
    // $pdf->Ln(7); 

  
    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT MENJABAT',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'TANGGAL GRADE TJ',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT MENJABAT PER',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'GRADE TJ PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7); 

    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT SG',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'TANGGAL PENSIUN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'TMT SG PERIODE',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,'',0,0);
    // $pdf->SetFont('Arial','',8);
    $pdf->Ln(0);  

    

    
    

    // }

    $pdf->AddPage();

    

    //POSISI

    $posisiexist = getField("select count(id) from emp_phist where parent_id = '$par[id]'");
    // if($posisiexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT JABATAN\tb"));
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,25,45,70,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

    $pdf->Row(array("NO.\tb","NOMOR SK\tb","TGL EFEKTIF\tb","POSISI\tb","KATEGORI\tb"));
  $pdf->SetWidths(array(10,25,45,70,30));
    $pdf->SetAligns(array('L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_phist where parent_id='$par[id]' order by sk_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
    $no = $no.".";
    $r[end_date] = !empty($r[end_date]) ? formatDate($r[end_date]) : "current";
    $r[tglEfektif] = formatDate($r[start_date])." - ".$r[end_date];
       $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[tglEfektif]."\tu",$r[pos_name]."\tu",$arrMaster[$r[kategori_id]]."\tu"));
       // $total += $r[nilai];
  }

  $pdf->Ln();
  //KARIR

  $karirexist = getField("select count(id) from emp_career where parent_id = '$par[id]'");
  // if($karirexist!=0){

  $pdf->SetWidths(array(180));
    $pdf->SetAligns(array('L'));
    $pdf->SetFont('Arial','',10);
  $pdf->Row(array("RIWAYAT TUGAS\tb")); 
  $pdf->SetFont('Arial','',8);

  $pdf->SetWidths(array(10,30,30,80,30));
$pdf->SetAligns(array('L','L','L','L','L','L'));

$pdf->Row(array("NO.\tb","NOMOR SK\tb","TANGGAL\tb","PERIHAL\tb","TIPE\tb",));


  if($karirexist!=0){
    $pdf->SetWidths(array(10,30,30,80,30));
$pdf->SetAligns(array('L','L','L','L','L','L'));
  $pdf->SetFont('Arial','',8);
$sql = "select * from emp_career where parent_id='$par[id]' order by sk_date desc";
$res=db($sql);
$no=0;
while ($r=mysql_fetch_array($res)) {
  $no++;
  $no = $no.".";
   $pdf->Row(array($no."\tu",$r[sk_no]."\tu",formatDate($r[sk_date])."\tu",$r[sk_subject]."\tu",$arrMaster[$r[sk_type]]."\tu"));
   // $total += $r[nilai];
}
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"."\tu"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1860' order by idKategori,createDate asc ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PENJENJANGAN\tb"));  
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,40,105,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","PENJENJANGAN\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,40,105,25));
    $pdf->SetAligns(array('L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1860' order by idKategori,createDate asc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1861' order by idKategori,createDate asc ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT MODUL\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","MODUL\tb","SERTIFIKASI\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1861' order by idKategori,createDate asc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$arrMaster[$r[idKategori]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1862' order by idKategori,createDate asc ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("SERTIFIKASI PROFESI\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","SERTIFIKASI/GELAR\tb","SERTIFIKASI\tb","PENYELENGGARAAN\tb","TAHUN LULUS\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,40,30,75,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_sertifikasi where idPegawai='$par[id]' and idJenis='1862' order by idKategori,createDate asc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[idJenis]]."\tu",$arrMaster[$r[idKategori]]."\tu",$r[penyelenggara]."\tu",$r[tahun]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}

$pdf->Ln();
  // }

//PELATIHAN

    $trainingexist = getField("select count(id) from emp_training where parent_id = '$par[id]' ");
    // if($trainingexist!=0){

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PELATIHAN\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,90,30,30,20));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","PERIHAL\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
  if($trainingexist!=0){
    $pdf->SetWidths(array(10,90,30,30,20));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_training where parent_id='$par[id]' order by trn_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[trn_subject]."\tu",$arrMaster[$r[trn_cat]]."\tu",$arrMaster[$r[trn_type]]."\tu",$r[trn_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
  
    
    // }
    $pdf->Ln();

    //PENGHARGAAN

    $rewardexist = getField("select count(id) from emp_reward where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PENGHARGAAN\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,45,30,30,25,20,20));
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","NOMOR PENGHARGAAN\tb","PERIHAL\tb","PENERBIT\tb","KATEGORI\tb","TIPE\tb","TAHUN\tb"));
  if($rewardexist!=0){
    $pdf->SetWidths(array(10,45,30,30,25,20,20));
    $pdf->SetAligns(array('L','L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_reward where parent_id='$par[id]' order by rwd_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[rwd_no]."\tu",$r[rwd_subject]."\tu",$r[rwd_agency]."\tu",$arrMaster[$r[rwd_cat]]."\tu",$arrMaster[$r[rwd_type]]."\tu",$r[rwd_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
  
    // }

//PENDIDIKAN

    $eduexist = getField("select count(id) from emp_edu where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PENDIDIKAN\tb"));  

    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,30,50,35,30,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","TINGKATAN\tb","NAMA LEMBAGA\tb","JURUSAN\tb","KOTA\tb","TAHUN\tb"));
  if($eduexist!=0){
    $pdf->SetWidths(array(10,30,50,35,30,25));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_edu where parent_id='$par[id]' order by edu_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$arrMaster[$r[edu_type]]."\tu",$r[edu_name]."\tu",$arrMaster[$r[edu_dept]]."\tu",$arrMaster[$r[edu_city]]."\tu",$r[edu_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
  


//KESEHATAN

    $healthexist = getField("select count(id) from emp_health where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT KESEHATAN\tb")); 
    // $pdf->Ln(15);  
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,30,50,40,50));
    $pdf->SetAligns(array('L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","TANGGAL\tb","NAMA TEMPAT\tb","DOKTER\tb","KETERANGAN\tb"));
  if($healthexist!=0){
    $pdf->SetWidths(array(10,30,50,40,50));
    $pdf->SetAligns(array('L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_health where parent_id='$par[id]' order by hlt_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",getTanggal($r[hlt_date])."\tu",$r[hlt_place]."\tu",$r[hlt_doctor]."\tu",$r[hlt_remark]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    
    // }

    $pdf->Ln();


$familyexist = getField("select count(id) from emp_family where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("KELUARGA\tb"));    
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,80,30,60));
    $pdf->SetAligns(array('L','L','L'));

    $pdf->Row(array("NO.\tb","NAMA\tb","HUBUNGAN\tb","TTL\tb"));
    if($familyexist!=0){
    $pdf->SetWidths(array(10, 80, 30, 60));
    $pdf->SetAligns(array('L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_family where parent_id='$par[id]' order by birth_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[name]."\tu",$arrMaster[$r[rel]]."\tu",$arrMaster[$r[birth_place]]." ".getTanggal($r[birth_date])."\tu"));
       $total += $r[nilai];
    }
  }else{
      $pdf->SetWidths(array(180));
    $pdf->SetAligns(array('C'));
      $pdf->Row(array("-- data kosong --"));
    }
    
    // }

    $pdf->Ln();
    $pdf->AddPage();
  

$kontakexist = getField("select count(id) from emp_contact where parent_id = '$par[id]' ");
    // if($kontakexist!=0){
      // $pdf->AddPage();

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("KONTAK\tb"));
    $pdf->SetFont('Arial','',8);

    
    $sql="select * from emp_contact where parent_id = '$par[id]' order by cre_date desc";
    $res=db($sql);
    $r=mysql_fetch_array($res);

    $pdf->SetWidths(array(90,90));
      $pdf->SetAligns(array('L'));

    $pdf->Row(array("SERUMAH\tb","BEDA RUMAH\tb"));     
     
    $pdf->SetWidths(array(45,45,45,45));
      $pdf->SetAligns(array('L'));
      $pdf->Row(array("NAMA\tb","".strtoupper($r[sr_nama])."\t","NAMA\tb","".strtoupper($r[br_nama])."\t"));
      $pdf->Row(array("HUBUNGAN\tb","".$r[sr_hub]."\t","HUBUNGAN\tb","".$r[br_hub]."\t"));
      $pdf->Row(array("NO. TELP\tb","".$r[sr_phone]."\t","NO. TELP\tb","".$r[br_phone]."\t"));
      $pdf->Row(array("ALAMAT\tb","".$r[sr_address]."\t","ALAMAT\tb","".$r[br_address]."\t"));
      $pdf->Row(array("PROVINSI\tb","".$arrMaster[$r[sr_prov]]."\t","PROVINSI\tb","".$arrMaster[$r[br_prov]]."\t"));
      $pdf->Row(array("KAB/KOTA\tb","".$arrMaster[$r[sr_city]]."\t","KAB/KOTA\tb","".$arrMaster[$r[br_city]]."\t"));
   //    $pdf->Ln(7);
    // $pdf->Cell(35,6,'NAMA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,strtoupper($r[sr_nama]),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NAMA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,strtoupper($r[br_nama]),0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    
    // // $pdf->Cell(0,6,' ',0,0,'L');
    // $pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[sr_hub],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'HUBUNGAN',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[br_hub],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // $pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[sr_phone],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'NO. TELP',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[br_phone],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);


    // $pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[sr_address],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'ALAMAT',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$r[br_address],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[sr_prov]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'PROVINSI',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[br_prov]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Ln(7);

    // $pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[sr_city]],0,0);
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(60,6,' ',0,0,'C');
    // $pdf->Cell(35,6,'KAB/KOTA',0,0,'L','true');
    // $pdf->Cell(3,6,' ',0,0,'C');
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(5,6,$arrMaster[$r[br_city]],0,0);
    // $pdf->SetFont('Arial','',8);
    $pdf->Ln();
    

//PERINGATAN

    $punishexist = getField("select count(id) from emp_punish where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("RIWAYAT PERINGATAN\tb"));  
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","NOMOR PERINGATAN\tb","PERIHAL\tb","PENERBIT\tb","TIPE\tb","TAHUN\tb"));
  if($punishexist!=0){
    $pdf->SetWidths(array(10,50,30,30,30,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_punish where parent_id='$par[id]' order by pnh_year desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[pnh_no]."\tu",$r[pnh_subject]."\tu",$r[pnh_agency]."\tu",$arrMaster[$r[pnh_type]]."\tu",$r[pnh_year]."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
    
//ASET

    $healthexist = getField("select count(id) from emp_asset where parent_id = '$par[id]'");
    

    $pdf->SetWidths(array(180));
      $pdf->SetAligns(array('L'));
      $pdf->SetFont('Arial','',10);
    $pdf->Row(array("PINJAMAN ASET\tb")); 
    $pdf->SetFont('Arial','',8);

    $pdf->SetWidths(array(10,30,30,30,50,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));

  $pdf->Row(array("NO.\tb","ASET\tb","NO. SERI\tb","KATEGORI\tb","TIPE\tb","TANGGAL\tb"));
  if($healthexist!=0){
    $pdf->SetWidths(array(10,30,30,30,50,30));
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->SetFont('Arial','',8);

    $sql = "select * from emp_asset where parent_id='$par[id]' order ast_date desc";
    $res=db($sql);
    $no=0;
    while ($r=mysql_fetch_array($res)) {
      $no++;
      $no = $no.".";
       $pdf->Row(array($no."\tu",$r[ast_name]."\tu",$r[ast_no]."\tu",$r[ast_usage]."\tu",$arrMaster[$r[ast_type]]."\tu",getTanggal($r[ast_date])."\tu"));
       // $total += $r[nilai];
  }
}else{
  $pdf->SetWidths(array(180));
$pdf->SetAligns(array('C'));
  $pdf->Row(array("-- data kosong --"));
}
    
    // }

    $pdf->Ln();
    
//    //KONTRAK

//    $kontrakexist = getField("select count(id) from emp_pcontract where parent_id = '$par[id]'");

//    // if($kontrakexist!=0){
//      // $pdf->AddPage();

//    $pdf->SetFont('Arial','B',13);
//    $pdf->setFillColor(0,0,0);
//    $pdf->SetTextColor(255,255,255);
//    $pdf->Cell(180,8,'RIWAYAT KONTRAK',0,0,'C','#000000');
//    $pdf->SetTextColor(0,0,0);        
//    $pdf->setFillColor(230,230,230);
//    $pdf->Ln(15); 
//    $pdf->SetFont('Arial','',8);

//    $pdf->SetWidths(array(10,30,50,30,30,30,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//  $pdf->Row(array("NO.\tb","NOMOR SK\tb","PERIHAL\tb","TGL SK\tb","TGL BERLAKU\tb","TGL BERAKHIR\tb"));
//  if($kontrakexist!=0){
//      $pdf->SetWidths(array(10,30,50,30,30,30,30));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//    $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_pcontract where parent_id='$par[id]'";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[sk_no]."\tu",$r[subject]."\tu",$r[sk_date]."\tu",$r[start_date]."\tu",$r[end_date]."\tu"));
//        // $total += $r[nilai];
//  }
// }else{
//  $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//  $pdf->Row(array("-- data kosong --"));
// }
    
//    // }

//    $pdf->Ln();
//    $pdf->Ln();

    

    

    


    

//    $bankexist = getField("select count(id) from emp_bank where parent_id = '$par[id]'");
//    // if($bankexist!=0){

//    $pdf->SetFont('Arial','B',13);
//    $pdf->setFillColor(0,0,0);
//    $pdf->SetTextColor(255,255,255);
//    $pdf->Cell(180,8,'REKENING BANK',0,0,'C','#000000');
//    $pdf->SetTextColor(0,0,0);        
//    $pdf->setFillColor(230,230,230);
//    $pdf->Ln(15); 
//    $pdf->SetFont('Arial','',8);

//    $pdf->SetWidths(array(10,60,30,30,50));
//     $pdf->SetAligns(array('L','L','L','L','L'));

//  $pdf->Row(array("NO.\tb","NAMA BANK\tb","NO REKENING\tb","CABANG\tb","REMARK\tb"));
//  if($bankexist!=0){
//     $pdf->SetWidths(array(10,60,30,30,50));
//     $pdf->SetAligns(array('L','L','L','L','L'));
//    $pdf->SetFont('Arial','',8);

//     $sql = "select * from emp_bank where parent_id='$par[id]'";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$arrMaster[$r[bank_id]]."\tu",$r[branch]."\tu",$r[account_no]."\tu",$r[remark]."\tu"));
//        $total += $r[nilai];
//  }
// }else{
//  $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//  $pdf->Row(array("-- data kosong --"));
// }
    
//    // }

//    $pdf->Ln();
//    $pdf->Ln();


    

    

    

    //KERJA

//    $workexist = getField("select count(id) from emp_pwork where parent_id = '$par[id]'");
    

//    $pdf->SetFont('Arial','B',13);
//    $pdf->setFillColor(0,0,0);
//    $pdf->SetTextColor(255,255,255);
//    $pdf->Cell(180,8,'RIWAYAT KERJA',0,0,'C','#000000');
//    $pdf->SetTextColor(0,0,0);        
//    $pdf->setFillColor(230,230,230);
//    $pdf->Ln(15); 
//    $pdf->SetFont('Arial','',8);

//    $pdf->SetWidths(array(10,60,50,35,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));

//  $pdf->Row(array("NO.\tb","PERUSAHAAN\tb","JABATAN\tb","BAGIAN\tb","TAHUN\tb"));
//  if($workexist!=0){
//     $pdf->SetWidths(array(10,60,50,35,25));
//     $pdf->SetAligns(array('L','L','L','L','L','L'));
//    $pdf->SetFont('Arial','',8);

//     $sql = "select *,concat(year(start_date),' - ',year(end_date)) as edu_years from emp_pwork where parent_id='$par[id]'";
//     $res=db($sql);
//     $no=0;
//     while ($r=mysql_fetch_array($res)) {
//       $no++;
//       $no = $no.".";
//        $pdf->Row(array($no."\tu",$r[company_name]."\tu",$r[position]."\tu",$r[dept]."\tu",$r[edu_years]."\tu"));
//        // $total += $r[nilai];
//  }
// }else{
//  $pdf->SetWidths(array(180));
// $pdf->SetAligns(array('C'));
//  $pdf->Row(array("-- data kosong --"));
// }
    
//    // }

//    $pdf->Ln();
//    $pdf->Ln();

    

    

    //REKENING

  

    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'PENGALAMAN PRIBADI',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);
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
    // $pdf->SetFont('Arial','',8);
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
    // $pdf->SetFont('Arial','',8);

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
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(180,6,$r__[abilities],0,0,'L');
    // $pdf->Ln(10);

    // $pdf->SetFont('Arial','B',13);
    // $pdf->Cell(180,6,'ORGANISASI SOSIAL',0,0,'C');
    // $pdf->Ln(10);  
    // $pdf->SetFont('Arial','',8);
    // $pdf->Cell(180,6,$r__[organization],0,0,'L');
      
    
    
    
    $pdf->Output(); 
  }
  
?>

