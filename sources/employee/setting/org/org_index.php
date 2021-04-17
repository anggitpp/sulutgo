<?php

/* PERMANENT ROUTER */
if (!isset($menuAccess[$s]["view"]))
  echo "<script>logout();</script>";
switch ($_GET["mode"]) {
  case "add":
    if (isset($menuAccess[$s]["add"]))
      include 'org_edit.php';
    else {
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
      die();
    }
    break;
  case "edit":
    if (isset($menuAccess[$s]["edit"]))
      include 'org_edit.php';
    else {
//      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])));
      $loc = str_replace("popup", "index", preg_replace("/&id=\d+/", "", preg_replace("/&lv=\d+/", "", preg_replace("/&pid=\d+/", "", preg_replace("/&mode=\w+/", "", $_SERVER["REQUEST_URI"])))));
      $_SESSION["entity_id"] = "";
      echo "<script>window.parent.location='$loc';</script>";
      die();
    }
    break;
  case "del":
    try {
      header("Content-Type: application/json");
      $empc = new MstData();
      $empc->kodeData = $_GET["id"];
      $ret;
      $ds = $empc->countChildren($_GET["id"]);
      if ($ds > 0) {
        $ret = array("success" => 0, "message" => "Data sudah digunakan.");
      } else if ($ds == -1) {
        $ret = array("success" => -1, "message" => "Proses hapus gagal.");
      } else {
        $ret = array("success" => 1);
        $empc->destroy();
      }
    } catch (Exception $ex) {
      
    }
    echo json_encode($ret);
    break;
  default :
    include 'org_view.php';
    break;
}
?>

