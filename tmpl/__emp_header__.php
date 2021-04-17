<?php
$htemp = new Emp();
$htemp->id = $_SESSION["curr_emp_id"];
$htemps = $htemp->getByIdHeader();
foreach ($htemps as $htemp) {
  $htemp = $htemp;
}

$cutil = new Common();
$ui = new UIHelper();
?>

<form class="stdform" >
  <table style="width: 100%;margin-top:10px;">
    <tr>
      <td rowspan="3" style="width: 10%; padding-left: 10px; padding-right: 10px; padding-top: 5px;">
        <img alt="<?= $htemp["regNo"] ?>" width="100%" height="100px" src="<?= "files/emp/pic/" . ($htemp["picFilename"] == "" ? "nophoto.jpg" : $htemp["picFilename"]) ?>" class='pasphoto'>
      </td>
      <td style="width: 40%;vertical-align: top; padding-left: 5px; padding-right: 10px;">
        <?php
        echo $ui->createPLabelSpanDisplay("Nama ", $htemp["name"]);
        echo $ui->createPLabelSpanDisplay("NIK", $htemp["regNo"]);
        echo $ui->createPLabelSpanDisplay("Jabatan", $htemp["jabatan"]);
        ?>
      </td>
      <td style="width: 40%;vertical-align: top; padding-top:8px; padding-left: 5px; padding-right: 10px;">
        <?php
        echo "<p>&nbsp;<br/></p>";
        echo $ui->createPLabelSpanDisplay("Status", $htemp["category"]);
        echo $ui->createPLabelSpanDisplay("Unit", $htemp["unit"]);
        ?>

      </td>
    </tr>    
  </table>
</form>