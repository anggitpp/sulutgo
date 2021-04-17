<?php
	session_start();
	setcookie("cUsername","");
	setcookie("cPassword","");
	setcookie("cGroup","");	
	setcookie("cNama","");
	setcookie("cFoto","");	
	setcookie("cSession","");
	setcookie("cID","");
	
	session_destroy();
	header('Location: login.php');
?>