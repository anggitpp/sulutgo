<?php

/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($_GET["mode"]) {
  case "add":
    if (isset($menuAccess[$s]["add"]))
      include 'tupoksi_edit.php';
    else
      include 'tupoksi_view.php';
    break;
  case "edit":
    if (isset($menuAccess[$s]["edit"]))
      include 'tupoksi_edit.php';
    else 
      include 'tupoksi_view.php';
    break;
  case "del":
    $empc = new EmpTup();
    $empc->id = $_GET["id"];
    $empc->desparent();
	include 'tupoksi_view.php';
    break;
  default :
    include 'tupoksi_view.php';
    break;
}
?>

