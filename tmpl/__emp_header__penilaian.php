<?php
/*
$htemp = new Emp();
$htemp->id = $_SESSION["curr_emp_id"];
$htemps = $htemp->getByIdHeader();
foreach ($htemps as $htemp) {
  $htemp = $htemp;
}
*/

$sql="select * from emp where id='".$_SESSION["curr_emp_id"]."'";
$res=db($sql);
$r=mysql_fetch_array($res);

$sql_="select * from emp_phist where parent_id='".$_SESSION["curr_emp_id"]."' and status='1'";
$res_=db($sql_);
$r_=mysql_fetch_array($res_);

$sql__="select * from pen_pegawai t1 join pen_tipe t2 on t1.tipePenilaian = t2.kodeTipe join pen_setting_kode t3 on t1.kodePenilaian = t3.idKode where idPegawai='".$_SESSION["curr_emp_id"]."' AND t1.kodePenilaian = '".$_SESSION["kodePenilaian"]."' order by t3.tanggalMulai";
// echo $sql__;
$res__=db($sql__);
$r__=mysql_fetch_array($res__);

$query = db("select * from dta_pegawai where id = ".$_SESSION["curr_emp_id"]."");
$r___ = mysql_fetch_array($query);

// $cutil = new Common();
// $ui = new UIHelper();
?>

<form class="stdform" >
  <table style="width: 100%;margin-top:10px;">
    <tr>
      <td rowspan="3" style="width: 10%; padding-left: 10px; padding-right: 10px; padding-top: 5px;">
        <img alt="<?= $htemp["regNo"] ?>" width="120"  src="<?=  ($r___["pic_filename"] == "" ? "files/emp/pic/nophoto.jpg" : "images/foto/".$r___["pic_filename"]) ?>" class='pasphoto'>
      </td>
      <td style="width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;">
		<p>
			<label style="width:150px" class="l-input-small">NAMA</label>
			<span class="field"><?= $r["name"]?>&nbsp;</span>
		</p>
		<p>
			<label style="width:150px" class="l-input-small">NIK</label>
			<span class="field"><?= $r["reg_no"]?>&nbsp;</span>
		</p>

		<p>
			<label style="width:150px" class="l-input-small">TIPE PENILAIAN</label>
			<span class="field"><?= $r__["namaTipe"]?>&nbsp;</span>
		</p>
		<p>
			<label style="width:150px" class="l-input-small">KODE PENILAIAN</label>
			<span class="field"><?= $r__["subKode"]?>&nbsp;</span>
		</p>
        
      </td>
      <td style="width: 45%;vertical-align: top; padding-top:8px; padding-left: 5px; padding-right: 10px;">
		<p>
			<label style="width:150px" class="l-input-small">NPWP</label>
			<span class="field"><?= $r["npwp_no"]?>&nbsp;</span>
		</p>
		<p>
			<label style="width:150px" class="l-input-small">PANGKAT/GRADE</label>
			<span class="field"><?= getField("select namaData from mst_data where kodeData='".$r_[rank]."'")." / ".getField("select namaData from mst_data where kodeData='".$r_[grade]."'")?>&nbsp;</span>
		</p>
		<p>
			<label style="width:150px" class="l-input-small">LOKASI</label>
			<span class="field"><?= getField("select namaData from mst_data where kodeData='".$r_[location]."'")?>&nbsp;</span>
		</p>
		<p>
			<label style="width:150px" class="l-input-small">JABATAN</label>
			<span class="field"><?= $r_["pos_name"]?>&nbsp;</span>
		</p>
      </td>
    </tr>    
  </table>
</form>