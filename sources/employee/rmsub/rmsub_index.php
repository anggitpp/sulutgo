<?php

/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
//echo "MODE " . $_GET['mode'] . " -> access add: " . isset($menuAccess[$s]["add"]);
switch ($_GET["mode"]) {
  case "add":
    if (isset($menuAccess[$s]["add"]))
      include 'rmsub_edit.php';
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
      die();
    }
    break;
  case "edit":
    if (isset($menuAccess[$s]["edit"]))
      include 'rmsub_edit.php';
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
      die();
    }
    break;
  case "del":
    $empc = new EmpRmb();
    $empc->id = $_GET["id"];
    $empc->destroy();
    break;
  default :
    include 'rmsub_view.php';
    break;
}
?>

