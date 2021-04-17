<?php
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";

$_SESSION["entity_id"] = "";
$_SESSION["curr_emp_id"] = "";
$_SESSION["parent_id"] = "";
$kodeInduk = isset($_SESSION["pg_org"]) ? $_SESSION["pg_org"] : isset($_POST["kodeInduk"]) ? $_POST["kodeInduk"] : "";
//$kodeInduk = ;
$emp = new MstData();
$cutil = new Common();
?>
<div class="pageheader">
  <h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
  <?= getBread() ?>
  <span class="pagedesc">&nbsp;</span>
</div>

<script language="javascript">
</script>


<div id="contentwrapper" class="contentwrapper">
  <form class="stdform" method="post" action="" id="form1" name="form">
    <div id="pos_r"><?php echo $arrParameter[38]?>:
      <?php
      $sql = "select kodeData id, namaData description, kodeInduk from mst_data  where kodeCategory='X04' order by urutanData";
      echo $cutil->generateSelectWithEmptyOptionId($sql, "id", "description", "kodeInduk", "kodeInduk", $kodeInduk, "<option value=''>ALL</option>", " class='single-deselect-td'");
      ?>
    </div>
  </form>
  <br clear="all" />
  <!-- table list data -->
  <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
    <thead>
      <tr>
        <!--<th width="40px">No.</th>-->
<!--        <th>Direktorat/Divisi/Unit</th>-->
        <!--<th>Direktorat</th>-->
        <th style="width: 200px;"><?php echo $arrParameter[38]?> - <?php echo $arrParameter[39]?></th>
        <th><?php echo $arrParameter[40]?> - <?php echo $arrParameter[41]?></th>
        <!--<th</th>-->
        <!--<th  width="70px">Lahir</th>-->
        <!--<th style="width: 40px;">Order</th>-->
        <th  style="width: 40px;">Status</th>
        <th style="width: 100px;">Control</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $purl = preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]));
      $dcat = "X04"; #kode direktorat
      $d0 = $emp->getAllByCat($dcat, $kodeInduk);
      foreach ($d0 as $rt) {
        echo "<tr>";
//        echo "<td></td>";
//        echo "<td colspan=\"6\">" . strtoupper($rt->namaData) . "</td>";
//        echo "<td></td>";
        echo "<td>" . strtoupper($rt->namaData) . "</td>";
        echo "<td></td>";
//        echo "<td style='text-align:right;'></td>";
        echo "<td style='text-align:center;'></td>";
        echo "<td style='text-align:center;'>"
//        .(isset($menuAccess[$s]["add"]) ? "<a href=\"$purl&mode=add&lv=2&pid=$rt->kodeData'\" title=\"Tambah Divisi\" class=\"add\"  onclick=\"openBox(',825,450);\"><span>Add</span></a>" : "") 
        . "</td>";
        echo "</tr>";
        $dc = $emp->getAllByParentTupoksi($rt->kodeData);
        $no = 0;
        foreach ($dc as $rc) {
          $no++;
          echo "<tr>";
//          echo "<td></td>";
          echo "<td>&nbsp;&nbsp;$no.&nbsp;&nbsp;&nbsp;&nbsp;" . strtoupper($rc->namaData) . "</td>";
          echo "<td></td>";
//          echo "<td style='text-align:right;'>$rc->urutanData</td>";
          echo "<td style='text-align:center;'>" . ($rc->statusData > 0  ? "<img src=\"styles/images/t.png\" title='Active'>" : "<img src=\"styles/images/f.png\" title='Not Active'>") . "</td>";
          echo "<td style='text-align:center;'>"
//          .(isset($menuAccess[$s]["add"]) ? "<a href=\"#Add\" title=\"Tambah Bagian\" class=\"add\"  onclick=\"openBox('$purl&mode=add&lv=3&pid=$rc->kodeData',825,450);\"><span>Add</span></a>" : "")
          . (isset($menuAccess[$s]["edit"]) ? "<a href=\"$purl&mode=edit&lv=2&id=$rc->kodeData\" title=\"Edit Tupoksi\" class=\"edit\"><span>Edit</span></a>" : "")
         . (isset($menuAccess[$s]["delete"]) ? "<a href=\"#Del\" title=\"Delete Data\" class=\"delete\"  onclick=\"removeRow(" . $rc->kodeData . ")\"><span>Delete</span></a>" : "")
          . "</td>";
          echo "</tr>";
          $nb = "a";
          $du = $emp->getAllByParentTupoksi($rc->kodeData);
          foreach ($du as $ru) {
            echo "<tr>";
//            echo "<td></td>";
            echo "<td></td>";
            echo "<td>$nb.  " . $ru->namaData . "</td>";
//            echo "<td style='text-align:right;'>$ru->urutanData</td>";
            echo "<td style='text-align:center;'>" . ($ru->statusData > 0  ? "<img src=\"styles/images/t.png\" title='Active'>" : "<img src=\"styles/images/f.png\" title='Not Active'>") . "</td>";
            echo "<td style='text-align:center;'>"
//            . (isset($menuAccess[$s]["add"]) ? "<a href=\"#Add\" title=\"Tambah Unit\" class=\"add\"  onclick=\"openBox('$purl&mode=add&lv=4&pid=$ru->kodeData',825,450);\"><span>Add</span></a>" : "")
            . (isset($menuAccess[$s]["edit"]) ? "<a href=\"$purl&mode=edit&lv=3&id=$ru->kodeData\" title=\"Edit Tupoksi\" class=\"edit\" \"><span>Edit</span></a>" : "")
            . (isset($menuAccess[$s]["delete"]) ? "<a href=\"#Del\" title=\"Delete Data\" class=\"delete\"  onclick=\"removeRow(" . $ru->kodeData . ")\"><span>Delete</span></a>" : "")
            . "</td>";
            echo "</tr>";
            $nb++;
            $du = $emp->getAllByParentTupoksi($ru->kodeData);
            foreach ($du as $ru) {
              echo "<tr>";
//              echo "<td></td>";
              echo "<td></td>";
              echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;-" . $ru->namaData . "</td>";
//              echo "<td style='text-align:right;'>$ru->urutanData</td>";
              echo "<td style='text-align:center;'>" . ($ru->statusData > 0 ? "<img src=\"styles/images/t.png\" title='Active'>" : "<img src=\"styles/images/f.png\" title='Not Active'>") . "</td>";
              echo "<td style='text-align:center;'>"
//              .(isset($menuAccess[$s]["add"]) ? "<a href=\"#Add\" title=\"Add Data\" class=\"add\"  onclick=\"openBox('$purl&mode=add&lv=2&pid=$ru->kodeInduk',825,450);\"><span>Add</span></a>" : "")
              . (isset($menuAccess[$s]["edit"]) ? "<a href=\"$purl&mode=edit&lv=4&id=$ru->kodeData\" title=\"Edit Tupoksi\" class=\"edit\" \"><span>Edit</span></a>" : "")
              . (isset($menuAccess[$s]["delete"]) ? "<a href=\"#Del\" title=\"Delete Data\" class=\"delete\"  onclick=\"removeRow(" . $ru->kodeData . ")\"><span>Delete</span></a>" : "")
              . "</td>";
              echo "</tr>";
              $nb++;
            }
          }
        }
      }
      ?>
    </tbody>
  </table>
</div>
<script type="text/javascript">
  jQuery(document).ready(function () {
//    jQuery('.single-deselect-td').chosen({allow_single_deselect: true, width: '100%', search_contains: true});
//    jQuery('.single-deselect-40').chosen({allow_single_deselect: true, width: '40%', search_contains: true});
//    jQuery('.single-deselect').chosen({allow_single_deselect: true, width: '90%', search_contains: true});
    jQuery("#kodeInduk").bind("change", function () {
      jQuery("#form1").submit();
    });
  });
  function removeRow(id) {
    if (confirm("Are you sure to delete data ?")) {    
	  window.location='<?php echo $purl;?>&mode=del&id=' + id;
    }
  }
</script>