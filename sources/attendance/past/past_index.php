<?php

/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($_GET["mode"]) {
  case "add":
    if (isset($menuAccess[$s]["add"]))
      include 'past_edit.php';
    else
      include 'past_view.php';
    break;
  case "edit":
    if (isset($menuAccess[$s]["edit"]))
      include 'past_edit.php';
    else
      include 'past_view.php';
    break;
  case "jabedit":
    if (isset($menuAccess[$s]["edit"]))
      include COMMON_DIR . "dlgempjab.php";
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  case "jabadd":
    if (isset($menuAccess[$s]["edit"])) {
      include COMMON_DIR . "dlgempjab.php";
    } else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  case "fasedit":
    if (isset($menuAccess[$s]["edit"]))
      include COMMON_DIR . "dlgempfas.php";
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  case "fasadd":
    if (isset($menuAccess[$s]["add"])) {
      include COMMON_DIR . "dlgempfas.php";
    } else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
    }
    break;
  default :
    include 'past_view.php';
    break;
}
?>

