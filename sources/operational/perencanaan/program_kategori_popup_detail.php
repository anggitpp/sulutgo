<?php
global $s, $par, $menuAccess, $arrTitle, $dFile;

$sql = "SELECT * FROM ctg_program WHERE id_program = '$par[id_program]'";
$res = db($sql);
$r = mysql_fetch_array($res);

$namaModul = getField("SELECT keterangan FROM app_site WHERE kodeSite='$r[id_modul]'");
$namaKategori = getField("SELECT keterangan FROM app_menu WHERE kodeMenu='$r[id_kategori]'");

?>

<div class="pageheader">
  <h1 class="pagetitle">Katalog Program</h1>
  <span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper" id="contentwrapper">
  <form action="?<?=getPar($par);?>" method="post" id="form" class="stdform" enctype="multipart/form-data">
  <div style="position:absolute; right:20px; top:14px;">
      <input type="button" class="cancel radius2" style="float:right" value="Close" onclick="closeBox();"/>
  </div>
    <!--<fieldset style="padding:10px; border-radius: 10px; margin-bottom: 20px;">
      <legend> KATALOG </legend>
      <p>
        <label class="l-input-small">Modul</label>
        <span class="field"><?=$namaModul?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Kategori</label>
        <span class="field"><?=$namaKategori?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Program</label>
        <span class="field"><?=$r[program]?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Durasi</label>
        <span class="field"><?=$r[durasi]?> Hari</span>
      </p>
    </fieldset>-->
    <fieldset style="padding:10px; border-radius: 10px; margin-bottom: 20px;">
      <legend> PROGRAM </legend>
      <p>
        <label class="l-input-small">Nama Program</label>
        <span class="field"><?=$r[program]?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Kode</label>
        <span class="field"><?=$r[kode]?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Tujuan</label>
        <span class="field"><?=$r[tujuan]?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Peserta</label>
        <span class="field"><?=$r[peserta]?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Durasi</label>
        <span class="field"><?=$r[durasi]?> Hari</span>
      </p>
      <p>
        <label class="l-input-small">Metodologi</label>
        <span class="field"><?=$r[metodologi]?>&nbsp;</span>
      </p>
      <p>
        <label class="l-input-small">Uraian</label>
        <span class="field"><?=$r[uraian]?>&nbsp;</span>
      </p>
    </fieldset>
  </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery("#form").submit(function(e) {
    if (validation(document.form)) {} else {
      e.preventDefault();
      return false;
    }
  });
});

function onBack() {
  window.location = "?<?=getPar($par, "mode, id_program")?>";
}
</script>