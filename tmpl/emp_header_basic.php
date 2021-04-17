<?php

$sql="select pic_filename, name, reg_no, CONCAT('(',TIMESTAMPDIFF(YEAR,  join_date, CURRENT_DATE ),' thn ',')') as masaKerja, join_date from emp where id='".$_SESSION["curr_emp_id"]."'";
$res=db($sql);
$r=mysql_fetch_array($res);

$sql_="select location, pos_name from emp_phist where parent_id='".$_SESSION["curr_emp_id"]."' and status='1'";
$res_=db($sql_);
$r_=mysql_fetch_array($res_);

$arrMaster = arrayQuery("select kodeData, namaData from mst_data");

echo"
<form class=\"stdform\" >
<fieldset>
  <table style=\"width: 100%;margin-top:10px;\">
    <tr>
      <td rowspan=\"3\" style=\"width: 10%; padding-left: 10px; padding-right: 10px; padding-top: 5px;\">
        <img alt=\"".$r["reg_no"]."\" width=\"100%\" height=\"100px\" src=\"files/emp/pic/" . ($r["pic_filename"] == "" ? "nophoto.jpg" : $r["pic_filename"])."\" class=\"pasphoto\">
      </td>
      <td style=\"width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;\">
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">NAMA</label>
			<span class=\"field\">".$r["name"]."&nbsp;</span>
		</p>
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">NPP</label>
			<span class=\"field\">".$r["reg_no"]."&nbsp;</span>
		</p>
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">JABATAN</label>
			<span class=\"field\">$r_[pos_name]&nbsp;</span>
		</p>
        
      </td>
      <td style=\"width: 45%;vertical-align: top; padding-top:8px; padding-left: 5px; padding-right: 10px;\">
        <p>
            &nbsp;
        </p>
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">MASA KERJA</label>
			<span class=\"field\">".$r[masaKerja]." ".getTanggal($r[join_date], "t")."&nbsp;</span>
		</p>
		
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">LOKASI KERJA</label>
			<span class=\"field\">".getField("select namaData from mst_data where kodeData='".$r_[location]."'")."&nbsp;</span>
		</p>
      </td>
    </tr>    
  </table>
  </fieldset>
</form>";



?>