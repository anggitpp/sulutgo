<?php

/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($_GET["mode"]) {
  default :
    include 'sched_view.php';
    break;
}
?>

