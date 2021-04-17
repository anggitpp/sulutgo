<?php
ob_start();
include "global.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ENTERPRISE RESOURCES PLANNING</title>
    <link href="favicon.ico" rel="shortcut icon" />  
    <link rel="stylesheet" href="styles/styles.css" type="text/css" />    
    <script type="text/javascript" src="scripts/jquery.js"></script>
    <script type="text/javascript" src="scripts/custom.js"></script>    
    <script type="text/javascript" src="scripts/cookie.js"></script>
    <script type="text/javascript" src="scripts/color.js"></script>
    <script type="text/javascript" src="scripts/data.js"></script>
    <script type="text/javascript" src="scripts/uniform.js"></script>
    <script type="text/javascript" src="scripts/time.js"></script>
    <script type="text/javascript" src="scripts/chosen.js"></script>
    <script type="text/javascript" src="scripts/general.js"></script>
    <script type="text/javascript" src="scripts/tables.js"></script>
    <script type="text/javascript" src="scripts/tinybox.js"></script>
    <script type="text/javascript" src="scripts/tinybox.js"></script>	    
    <script type="text/javascript" src="scripts/autoNumeric.js"></script>    
    <script type="text/javascript" src="scripts/jquery.chained.min.js"></script>    
    <script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>    
    <script type="text/javascript" src="scripts/jquery.redirect.min.js"></script>    
    <script type="text/javascript" src="scripts/jquery.validate.min.js"></script>    
    <script type="text/javascript" src="scripts/datatablesExt.fnReloadAjax.js"></script>    

  </head>

  <body class="withvernav-nobg">
    <?php
    valPar($par, "", 5);
    ?>
    <div class="bodywrapper">       

      <?php
      if (is_file($arrScript[$s])) {
        echo "<script type=\"text/javascript\" src=\"" . $arrScript[$s] . "\"></script>";
      } else {
        echo "<script type=\"text/javascript\" src=\"sources/js/default.js\"></script>";
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
	  
      if (empty($c))
        include "sources/index.php";
      else
        include is_file($arrSource[$s]) ? $arrSource[$s] : (is_dir($srcDir) ? $srcDir . "/" . $srcDirPath . "_index.php" : "sources/index.php");

      if (function_exists('getContent'))
        echo getContent($par);
      ?>
      <!-- centercontent -->


    </div><!--bodywrapper-->

  </body>

</html>
