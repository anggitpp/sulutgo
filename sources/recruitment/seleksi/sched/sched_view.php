<?php
$loc = preg_replace("/&cyear=\d+/", "", preg_replace("/&cmonth=\d+/", "", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"]))));
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
$cyear = isset($_GET["cyear"]) ? $_GET["cyear"] : date("Y");
$cmonth = isset($_GET["cmonth"]) ? $_GET["cmonth"] : date("n") - 1;
$cutil = new Common();
$arrColors = array("601"=>"#FF33FF","602"=>"#ba8b26", "603"=>"#0020e0", "604"=>"#e00020", "605"=>"#5fe000", "606"=>"#128a92", "607"=>"#851712");
$ui = new UIHelper();
if ($_GET["events"] == 1) {
  header("Content-type: application/json");
  $sql = "
  SELECT * from rec_selection_appl t1

  WHERE " . $cyear . ($cmonth + 1) . "=concat(year(t1.sel_date),month(t1.sel_date))
  GROUP BY t1.sel_date, t1.phase_id
  ORDER BY t1.phase_id
  ";
 // echo $sql;
  $res = $cutil->executeSQL($sql);
  $ret = array();
  $c = 0;
  $arrMaster = arrayQuery("select kodeData, namaData from mst_data");
  foreach ($res as $r) {
    $ret[$c] = array(
      "id" => $r["id"],
      "title" => "",
      "start" => $r["sel_date"],
      "end" => $r["sel_date"],
      "color" => $arrColors[$r["phase_id"]],
      "data" => array(
        "id" => $r["id"],
        "phaseName" => $arrMaster[$r["phase_id"]],
        "phaseId" => $r["phase_id"],
        "phaseDate" => $r["sel_date"],
        "phaseDates" => getTanggal($r["sel_date"]),

        )
      );
    $c++;
    $file = getField("SELECT id FROM rec_selection_appl_file where parent_id='$r[id]'");

    if ($r["phase_id"] == 603 AND !empty($file) AND $r["sel_status"] == 601) {
      $ret[$c] = array(
        "id" => $r["id"],
        "title" => "",
        "start" => $r["sel_date"],
        "end" => $r["sel_date"],
        "color" => $arrColors[601],
        "data" => array(
          "id" => $r["id"],
          "phaseName" => "Psikogram",
          "phaseId" => 601,
          "phaseDate" => $r["sel_date"],
          "phaseDates" => getTanggal($r["sel_date"]),

          )
        );
      $c++;
    }
  }
  echo json_encode($ret);
  die();
}
// if ($_GET["info"] == 1) {
//   header("Content-type: application/json");
//   $sql = "
//     SELECT t1.id,
//     t6.subject, 
//     t6.pos_available posAvailable,
//     t1.phase_date startDate,
//     t1.phase_time phaseTime,
//     t3.namaData phaseName,
//     GROUP_CONCAT(t5.`name` ORDER BY t5.name SEPARATOR '~\t') participant,
//     GROUP_CONCAT(t7.`tanggal`,' - ', t8.`namaData` ORDER BY t7.tanggal desc SEPARATOR '~\t') aktifitas,
//     t1.phase_loc location, t1.phase_remark remark,
//     COUNT(t4.id) applCount
//     FROM rec_selection_phase t1
//     JOIN rec_selection t2 ON t1.parent_id=t2.id
//     JOIN mst_data t3 ON t3.kodeData=t1.phase_id
//     LEFT JOIN rec_selection_appl t4 ON t4.parent_id=t1.parent_id AND t4.phase_id=t1.phase_id
//     JOIN rec_applicant t5 ON t4.appl_id=t5.id
//     JOIN rec_plan t6 on t6.id=t1.plan_id
//     LEFT JOIN rec_selection_appl_aktivitas t7 on t4.id = t7.parent_id
//     LEFT JOIN mst_data t8 on t7.kategori = t8.kodeData
//     WHERE t1.id='" . $_GET["id"] . "'
//     GROUP BY t1.parent_id,t1.phase_id
//     ORDER BY t1.phase_sort
//     ";

//   $res = $cutil->executeSQL($sql);
//   $ret = array();
//   foreach ($res as $r) {
//     $p = 1;
//     $arrParticipant = explode("~\t", $r["participant"]);
//     $participant = "";
//     foreach ($arrParticipant as $ap) {
//       $participant.=$p . ". " . $ap . "<br/>";
//       $p++;
//     }
//     $a = 1;
//     $arrAktifitas = explode("~\t", $r["aktifitas"]);
//     $aktifitas = "";
//     foreach ($arrAktifitas as $aa) {
//       $aktifitas.=$a . ". " . $aa . "<br/>";
//       $a++;
//     }
//     $ret[] = array(
//         "id" => $r["id"],
//         "subject" => $r["subject"],
//         "posName" => $r["posAvailable"],
//         "phaseName" => $r["phaseName"],
//         "startDate" => $r["startDate"],
//         "phaseTime" => $r["phaseTime"],
//         "location" => $r["location"],
//         "remark" => $r["remark"],
//         "participant" => $participant,
//         "aktifitas" => $aktifitas);
//   }
//   echo json_encode($ret);
//   die();
// }
?>
<link rel="stylesheet" type="text/css" href="scripts/quickpage/quickpage.css"/>
<script src="scripts/jquery.quick.pagination.min.js"></script>
<div class="pageheader">
  <h1 class="pagetitle"><?php echo $arrTitle[$s] ?></h1>
  <?= getBread() ?>
  <span class="pagedesc">&nbsp;</span>
</div>

<div id="contentwrapper" class="contentwrapper">
  <div style="position: absolute;right: 20px;top:20px;">
    PANGGILAN : <img src="images/panggilan.PNG" style="width:80px;height: 10px"> 
    PSIKOTEST : <img src="images/psikotest.PNG" style="width:80px;height: 10px"> 
    PSIKOGRAM : <img src="images/psikogram.PNG" style="width:80px;height: 10px"> 
    WAWANCARA USER : <img src="images/wawancarauser.PNG" style="width:80px;height: 10px"> 
    WAWANCARA HR : <img src="images/wawancarahr.PNG" style="width:80px;height: 10px"> 
    MCU : <img src="images/MCU.PNG" style="width:80px;height: 10px">
  </div>
  <form class="stdform" method="post" name="form" id="form">     

    <script type="text/javascript" src="scripts/calendar.js"></script>
    <script type="text/javascript">
      jQuery(function () {
        jQuery('#calendar').fullCalendar({
          month: <?= $cmonth ?>,
          year: <?= $cyear ?>,
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
            url: sajax + "&events=1",
            cache: true
          },
          eventMouseover: function (calEvent, jsEvent) {
            var tooltip = '<div class="tooltipevent" style="padding:0 5px; position:absolute; z-index:10000; font-size:10px; background:#fff; color:#666; border:solid 1px #ccc; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px;">' + calEvent.data.phaseName + '</div>';

            jQuery("body").append(tooltip);
            jQuery(this).mouseover(function (e) {
              jQuery(this).css('z-index', 10000);
              jQuery('.tooltipevent').fadeIn('500');
              jQuery('.tooltipevent').fadeTo('10', 1.9);
            }).mousemove(function (e) {
              jQuery('.tooltipevent').css('top', e.pageY + 10);
              jQuery('.tooltipevent').css('left', e.pageX + 20);
            });
          },
          eventMouseout: function (calEvent, jsEvent) {
            jQuery(this).css('z-index', 8);
            jQuery('.tooltipevent').remove();
          },
          eventClick: function (calEvent, jsEvent, view) {
           var date = jQuery('#calendar').fullCalendar('getDate');
           var bulanKegiatan = date.getMonth()+1;
           var tahunKegiatan = date.getFullYear();
           if (calEvent.data.phaseId != 601) {
           window.location = '?c=2&p=6&m=792&s=792&par[idPhase]='+calEvent.data.phaseId+'&par[tanggalData]='+calEvent.data.phaseDates; 
           }
           
         },
       });

        jQuery('.fc-button-prev span').click(function () {

          var date = jQuery('#calendar').fullCalendar('getDate');
          var bulanKegiatan = date.getMonth() == 0 ? 11 : date.getMonth() - 1;
          var tahunKegiatan = date.getMonth() == 0 ? date.getFullYear() - 1 : date.getFullYear();
          window.location = '<?= $loc ?>' + '&cmonth=' + bulanKegiatan + '&cyear=' + tahunKegiatan;
        });

        jQuery('.fc-button-next span').click(function () {

          var date = jQuery('#calendar').fullCalendar('getDate');
          var bulanKegiatan = date.getMonth() == 12 ? 0 : date.getMonth() + 1;
          var tahunKegiatan = date.getMonth() == 12 ? date.getFullYear() + 1 : date.getFullYear();
          window.location = '<?= $loc ?>' + '&cmonth=' + bulanKegiatan + '&cyear=' + tahunKegiatan;
        });
      });
    </script>
    <div id="calendar"></div>
    


  </div>


  <script type="text/javascript">
    jQuery(document).ready(function () {
      jQuery.ajax({url: sajax, dataType: 'json', data: {mode: "view", events: 1, "cmonth": <?= $cmonth ?>, "cyear": <?= $cyear ?>}, success: function (data) {
        jQuery(data).each(function () {
          var contentTmpl = "<div style=\"width:10px; height:10px; background:" + this.color + "; margin-top:4px; margin-right:5px; float:left;\">&nbsp;</div>";
          contentTmpl += "<a href=\"#info\" onclick=\"getInfo(" + this.id + ");\" >" + this.data.phaseName + "</a>";
          contentTmpl += "<div style=\"font-size:10px; color:#888;\">" + this.data.startDate + "</div>";
          
        });
        jQuery("ul.pagination1").quickPagination({pageSize: "5"});
      }
    });
      <?php
      if (isset($_GET["minfo"])) {
        ?>
        getInfo(<?= $_GET["minfo"] ?>);
        sajax=removeParameter(sajax,"minfo");
        <?php
      }
      ?>

    });

  </script>