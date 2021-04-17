<?php
if(
	!function_exists("mysql_connect") && 
	!function_exists("mysql_select_db") && 
	!function_exists("mysql_query") &&
	!function_exists("mysql_fetch_row") &&
	!function_exists("mysql_fetch_array") &&
	!function_exists("mysql_fetch_assoc") &&
	!function_exists("mysql_escape_string") &&
	!function_exists("mysql_real_escape_string") &&
	!function_exists("mysql_num_rows"))
{
	$_host 	= "";
	$_user 	= "";
	$_pass 	= "";

	function mysql_connect($host, $user, $pass){
		global $_host, $_user, $_pass;
		$_host = $host;
		$_user = $user;
		$_pass = $pass;

		return TRUE;
	}

	function mysql_select_db($db){
		global $conn, $_host, $_user, $_pass;

		$conn = new mysqli($_host, $_user, $_pass, $db);
		if($conn->connect_errno){
			die($conn->connect_error);
		}
	}

	function mysql_query($query, $conn){
		return $conn->query($query);
	}

	function mysql_fetch_row($result){
		return $result->fetch_row();
	}

	function mysql_fetch_array($result){
		return $result->fetch_array();
	}

	function mysql_fetch_assoc($result){
		return $result->fetch_assoc();
	}

	function mysql_escape_string($string){
		$string = addslashes($string);

		$string = str_replace(array('%', '_'), array('\\%', '\\_'), $string);
		return $string;
	}

	function mysql_real_escape_string($string){
		return mysql_escape_string($string);
	}

	function mysql_num_rows($result){
		return $result->num_rows;
	}
}
?>