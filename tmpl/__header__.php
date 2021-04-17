
<script type="text/javascript">
  function addAttributeRequired(elmid) {
  var elm = jQuery('#' + elmid).parent().parent();
          var elmLbl = elm.children("label");
          if (elmLbl.html() != null) {
  var elmLblText = elmLbl.html();
          if (elmLblText.indexOf('&nbsp;&nbsp;<span class="required">*)</span>') < 0) {
  elmLbl.html(elmLblText + '&nbsp;&nbsp;<span class="required">*)</span>');
  }
  }
  }

  jQuery(document).ready(function(){
  // jQuery('select, input:checkbox').uniform();
<?php if ($__validate["formid"] != "") { ?>
  <?php if (count($__validate["items"]) > 0) { ?>
    <?php foreach ($__validate["items"] as $key => $val) { ?>
        addAttributeRequired('<?= $key ?>');
    <?php } ?>
  <?php } ?>

    jQuery("#<?= $__validate["formid"] ?>").validate({
  <?php
  if (count($__validate["items"]) > 0) {
    $delim = "";
    ?>
      rules: {
    <?php foreach ($__validate["items"] as $key => $val) { ?>
      <?= $delim ?>"<?= $key ?>": "<?= $val["rule"] ?>"
      <?php
      $delim = ", ";
    }
    ?>
      },
  <?php } ?>
  <?php
  if (count($__validate["items"]) > 0) {
    $delim = "";
    ?>
      messages: {
    <?php foreach ($__validate["items"] as $key => $val) { ?>
      <?= $delim ?>"<?= $key ?>": "<?= $val["msg"] ?>"
      <?php
      $delim = ", ";
    }
    ?>
      }
  <?php } ?>
    });
<?php } ?>
  });</script>