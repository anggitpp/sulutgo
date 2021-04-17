<?php
	include "global.php";		
	
	if(!empty($username) && !empty($password)){
		$username=mysql_real_escape_string($username);
		$password=mysql_real_escape_string($password);
	
		$sql = "select * from app_user where username='$username' and password='".encodePass($password)."' and statusUser='t'";
		$res=db($sql);
		$r= mysql_fetch_array($res);
		if($r[password]!=""){
			setcookie("cUsername",$r[username]);
			setcookie("cPassword",$r[password]);
			setcookie("cGroup",$r[kodeGroup]);		
			setcookie("cNama",$r[namaUser]);
			setcookie("cFoto",$r[fotoUser]);
			setcookie("cID",$r[idPegawai]);
			setcookie("cUser",$r[id]);
			
			db("update app_user set loginUser='".date('Y-m-d H:i:s')."' where username='$username'");
			header('Location: main.php');
		}else{
			$message="username / password was wrong";
		}
	}else{
		$message="you must fill username & password";
	}

	
	$kodeInfo=1;
	$sql="select * from app_info where kodeInfo='$kodeInfo'";
	$res=db($sql);
	$r=mysql_fetch_array($res);	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $r[keteranganInfo]; ?></title>
	<link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="images/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="images/favicon/favicon-16x16.png">
	<link rel="manifest" href="images/favicon/site.webmanifest">
	<link rel="mask-icon" href="images/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

	<link href="favicon.ico" rel="shortcut icon" />  
    <link rel="stylesheet" href="styles/styles.css" type="text/css" />    
    <script type="text/javascript" src="scripts/jquery.js"></script>	
    <script type="text/javascript" src="scripts/custom.js"></script>
    <script type="text/javascript" src="scripts/cookie.js"></script>
    <script type="text/javascript" src="scripts/data.js"></script>
    <script type="text/javascript" src="scripts/uniform.js"></script>
	<script type="text/javascript" src="scripts/time.js"></script>
	<script type="text/javascript" src="scripts/chosen.js"></script>    
    <script type="text/javascript" src="scripts/tinybox.js"></script>
	<script type="text/javascript">
		function message(){
			<?php
			if(!empty($lgn))
				echo "alert('".$message."')";
			?>	
		}
	</script>
<body class="loginpage" onload="message()";>
	<center>
		<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" style="background:url('images/info/bfront.png');background-size: cover;background-repeat: no-repeat;background-position: center center;">
		<tr><td height="385" align="left" valign="top">&nbsp;</td></tr>
		<tr>
		<td height="160" valign="top" style='background: transparent linear-gradient(to bottom, #777777 5%, #3e3d3d 31%, #343434 100%) repeat scroll 0% 0%;opacity: 0.9; filter: alpha(opacity=90);'>
			<div style="position:normal;margin-top:50px;">
			<table cellpadding="3" cellspacing="2" border="0">
				<tr>
					<td align="right" style="padding-right:30px;font-weight:bold;color:#fff;width:630px;"> 
					<font size="4"><?php echo $r[namaInfo]; ?></font><br>
					<font size="2"><?php echo $r[keteranganInfo]; ?></font><br>
					</td>
					<td class="note" style="border-left:1px solid #fff;width:20px">&nbsp;</td>
					<td class="note" style="padding-top:-20px;">
                    <div class="loginbox">
                      <form id="login" action="login.php" method="post">       
                        <div class="username">
							<div class="usernameinner">
								<input type="text" name="username" id="username" placeholder="Username" />
							</div>
						</div>                
						<div class="password">
							<div class="passwordinner">
								<input type="password" name="password" id="password" placeholder="Password" />
							</div>
						</div>
						<input type="hidden" id="lgn" name="lgn" value="t">
						<button>Login</button>  
                    </form> 
                    </div> 
                    <div style='font-size:10px;float:left;margin-left:-140px;color:#ccc;'>If you are not registered, please contact the administrator - Forgot Password</div>
					</td>
				</tr>
				</table>				
				</div>
		</td>
		</tr>
		<tr><td height="4" style='background:#fe0000;'></td></tr>
		<tr>
			<td  valign="top" style='background:#fff;'>
			<div style='font-family:arial;font-size:10px;margin:10px 0 0 30px;color:#b9b9b9;'>
			<img src='images/info/logo-1.png' align='left' style='margin-right:20px;'>
			</div>
             <div style='align:right'>
            <img src='images/info/support.png' align='right' style='margin-right:20px;'>
            </div>
			</td>
		</tr>
		</table>
		</center>		
		</body>
		</html>