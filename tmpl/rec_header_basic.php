<?php

$sql="select t1.pic_filename, name, gender, CONCAT(TIMESTAMPDIFF(YEAR,  birth_date, CURRENT_DATE ), ' thn') as umur, t2.edu_type, edu_dept from rec_applicant t1 LEFT OUTER JOIN rec_applicant_edu t2 on t1.id = t2.parent_id where t1.id='".$_SESSION["curr_rec_id"]."'";
$res=db($sql);
$r=mysql_fetch_array($res);

$arrMasterData = arrayQuery("SELECT kodeData, namaData FROM mst_data where kodeCategory IN ('R11', 'R13')");

$r[gender] = $r[gender] == "M" ? "Laki-Laki" : "Perempuan";

echo"
<form class=\"stdform\" >
<fieldset>
  <table style=\"width: 100%; margin:10px 0px 10px 0px;\">
    <tr>
      <td rowspan=\"3\" style=\"width: 10%; padding-left: 10px; padding-right: 20px; padding-top: 5px;\">
		<img alt=\"".$r["gender"]."\" width=\"100%\" height=\"100px\" src=\"files/recruit/pic/" . ($r["pic_filename"] == "" ? "nophoto.jpg" : $r["pic_filename"])."\" class=\"pasphoto\"> 
      </td>
      <td style=\"width: 45%;vertical-align: top; padding-left: 5px; padding-right: 10px;\">
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">NAMA</label>
			<span class=\"field\">".$r["name"]."&nbsp;</span>
		</p>
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">GENDER</label>
			<span class=\"field\">".$r["gender"]."&nbsp;</span>
		</p>
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">UMUR</label>
			<span class=\"field\">$r[umur]&nbsp;</span>
		</p>
        
      </td>
      <td style=\"width: 45%;vertical-align: top; padding-top:8px; padding-left: 5px; padding-right: 10px;\">
        <p>
            &nbsp;
        </p>
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">PENDIDIKAN</label>
			<span class=\"field\">".$arrMasterData[$r[edu_type]]."&nbsp;</span>
		</p>
		
		<p>
			<label style=\"width:150px\" class=\"l-input-small\">JURUSAN</label>
            <span class=\"field\">".$arrMasterData[$r[edu_dept]]."&nbsp;</span>
		</p>
      </td>
    </tr>    
  </table>

  </fieldset>
</form>";



?>