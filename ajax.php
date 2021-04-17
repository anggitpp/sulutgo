<?php

include "global.php";
if (!getUser()) {
  echo "<script>window.location='logout.php'</script>";
  exit();
}
if (!empty($_GET["t"]) && $_GET["t"] == "common" && !empty($_GET["f"])) {
  include COMMON_DIR . $_GET["f"] . ".php";
  exit();
}
$srcDir = $arrSource[$s];
if (strpos($srcDir, "?") > -1) {
  $srcDir = substr($srcDir, 0, strpos($srcDir, "?"));
  $query_str = parse_url($arrSource[$s], PHP_URL_QUERY);
  parse_str($query_str, $query_params);
  foreach ($query_params as $key => $value) {
    $$key = str_replace(".php", "", $value);
    global $$key;
  }
}
$srcDir = str_replace(".php", "", $srcDir);
$srcDirPath = substr($srcDir, (strripos($srcDir, "/") + 1));

include is_file($arrSource[$s]) ? $arrSource[$s] : (is_dir($srcDir) ? $srcDir . "/" . $srcDirPath . "_index.php" : "sources/index.php");

if (function_exists('getContent'))
  echo getContent($par);

ob_end_flush();
?>