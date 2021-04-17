<?php

$chmod = $_GET["chkmode"];
$currId = $_GET["id"];
$currNik = $_GET["nik"];
$addFilter = "";
if (!empty($currId)) {
  $addFilter = " AND id<>$currId";
}
if (!empty($currNik)) {
  $addFilter .= " AND reg_no<>$currNik";
}
switch ($chmod) {
  case "nik":
    $currVal = $_GET["regNo"];
    $sql = "SELECT count(id) total FROM emp WHERE upper(reg_no)=upper('$currVal') $addFilter ";
    break;
  case "email":
    $currVal = $_GET["email"];
    $sql = "SELECT count(id) total FROM emp WHERE upper(email)=upper('$currVal') $addFilter ";
    break;
  case "hp":
    $currVal = $_GET["cellNo"];
    $sql = "SELECT count(id) total FROM emp WHERE upper(cell_no)=upper('$currVal') $addFilter ";
    break;	
  case "nama":
    $currVal = $_GET["name"];
    $sql = "SELECT count(id) total FROM emp WHERE upper(name)=upper('$currVal') $addFilter ";
    break;
  case "ktp":
    $currVal = $_GET["ktpNo"];
    $sql = "SELECT count(id) total FROM emp WHERE upper(ktp_no)=upper('$currVal') $addFilter ";
    break;
  default:
    $sql = "";
    echo "false";
    exit();
    break;
}

$cutil = new Common();
$total = $cutil->getDescription($sql, "total");
//echo 'SQL: ' . $sql;
if ($total == 0) {
  echo "true";
} else {
  echo "false";
}
?>
