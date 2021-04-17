<?php 
	include "global.php";		
	include is_file($arrSource[$s]) ? $arrSource[$s] : "sources/index.php";	
	echo getContent($par);	
?>