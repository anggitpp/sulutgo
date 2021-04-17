<?php
	include "global.php";			
	$url = APP_URL."/";
	if($doc == "dokumen_pelatihan"){
		$sql="select file from perencanaan_dokumen where id_pdokumen='$par[id_pdokumen]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/rencana/dokumen/".$r[file];		
		$eFile = getExtension($r[file]);		
	}

	if($doc == "rencana_pelatihan"){
		$sql="select filePelatihan from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/rencana/".$r[filePelatihan];		
		$eFile = getExtension($r[filePelatihan]);		
	}

	if($doc == "file_sk"){
		$sql="select * from kepegawaian_perubahan_status where id_perubahan='$par[id_perubahan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/kepegawaian/perubahan/sk/".$r[file];		
		$eFile = getExtension($r[file]);		
	}
	
	if($doc == "ktp"){
		$sql="select * from emp where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/ktp/".$r[ktp_filename];		
		$eFile = getExtension($r[ktp_filename]);		
	}	
	
	if($doc == "family"){
		$sql="select * from emp_family where id='$id'";
			// echo $sql;
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/rel/".$r[rel_filename];		
		$eFile = getExtension($r[rel_filename]);		
	}
	
	if($doc == "career"){
		$sql="select * from emp_career where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		$fFile = "files/emp/career/".$r[sk_filename];		
		$eFile = getExtension($r[sk_filename]);	
	}
	
	if($doc == "contract"){
		$sql="select * from emp_pcontract where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/contract/".$r[file_sk];	
		$eFile = getExtension($r[file_sk]);
	}
	
	if($doc == "empReward"){
		$sql = "SELECT filename FROM emp_reward WHERE id='$id'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$fFile = "files/emp/reward/".$r[filename];
		$eFile = getExtension($r[filename]);
	}

	if($doc == "empPunish"){
		$sql = "SELECT filename FROM emp_punish WHERE id = '$id'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$fFile = "files/emp/punish/".$r[filename];
		$eFile = getExtension($r[filename]);
	}

	if($doc == "empFamily"){
		$sql = "SELECT rel_filename FROM emp_family WHERE id='$id'";
		$res = db($sql);
		$r = mysql_fetch_array($res);

		$fFile = "files/emp/family/".$r[rel_filename];
		$eFile = getExtension($r[rel_filename]);
	}

	if($doc == "empTraining"){
		$sql="select filename from emp_training where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/training/".$r[filename];	
		$eFile = getExtension($r[filename]);
	}
	
	if($doc == "reward"){
		$sql="select * from emp_reward where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/rwd/".$r[rwd_filename];	
		$eFile = getExtension($r[rwd_filename]);
	}

	if($doc == "sel"){
		$sql="select * from rec_selection_appl_file where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/recruit/selection/".$r[filename];	
		$eFile = getExtension($r[filename]);
	}

	if($doc == "skPelamar"){
		$sql="select * from rec_selection_sk where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/recruit/sk/".$r[sk_filename];	
		$eFile = getExtension($r[sk_filename]);
	}
	
	if($doc == "punish"){
		$sql="select * from emp_punish where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/pnh/".$r[pnh_filename];	
		$eFile = getExtension($r[pnh_filename]);
	}
	
	if($doc == "edu"){
		$sql="select * from emp_edu where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/edu/".$r[edu_filename];	
		$eFile = getExtension($r[edu_filename]);
	}

	if($doc == "sto"){
		$sql="select * from emp_struktur where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/sto/".$r[file];	
		$eFile = getExtension($r[file]);
	}
	
	if($doc == "work"){
		$sql="select * from emp_pwork where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/edu/".$r[filename];	
		$eFile = getExtension($r[filename]);
	}
	
	if($doc == "health"){
		$sql="select * from emp_health where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/hlt/".$r[hlt_filename];	
		$eFile = getExtension($r[hlt_filename]);
	}
	
	if($doc == "asset"){
		$sql="select * from emp_asset where id='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/emp/ast/".$r[ast_filename];	
		$eFile = getExtension($r[ast_filename]);
	}
	
	if($doc == "dokumen"){
		$sql="select * from plt_pelatihan_dokumen where idDokumen='$id' AND idPelatihan = '$par[idPelatihan]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$jFile = $r[judulDokumen];
		$kFile = nl2br($r[keteranganDokumen]);
		$fFile = "files/dokumen/".$r[fileDokumen];
		$eFile = getExtension($r[fileDokumen]);
	}
	
	if($doc == "mt_dokumen"){
		$sql="select * from mt_dokumen where idDokumen='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
		
		$jFile = $r[judulDokumen];
		$kFile = nl2br($r[keteranganDokumen]);
		$fFile = "files/trainee/dokumen/".$r[fileDokumen];
		$eFile = getExtension($r[fileDokumen]);
	}

	if($doc == "manual"){
		$sql="select * from app_menu where kodeMenu='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);				
			
		$fFile = "files/menu/".$r[fileMenu];		
		$eFile = getExtension($r[fileMenu]);	
		
		if(!is_file($fFile)){
			echo "maaf, panduan belum tersedia.";
			die();
		}
	}
	
	if($doc == "fileMenu"){
		$sql="select * from app_menu where kodeMenu='$id'";
		// echo $sql;
		$res=db($sql);
		$r=mysql_fetch_array($res);				
			
		$fFile = "files/FileMenu/".$r[fileMenu];		
		$eFile = getExtension($r[fileMenu]);	
		
		if(!is_file($fFile)){
			echo "maaf, panduan belum tersedia.";
			die();
		}
	}
	
	if($doc == "mt_penilaian"){
		$sql="select * from mt_penilaian where idPenilaian='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/trainee/penilaian/".$r[filePenilaian];
		$eFile = getExtension($r[filePenilaian]);
	}

	if($doc == "koreksiAbsen"){
		$sql="select * from att_koreksi where idKoreksi='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/koreksi/".$r[fileKoreksi];
		$eFile = getExtension($r[fileKoreksi]);
	}

	if($doc == "fileCuti"){
		$sql="select * from att_cuti where idCuti='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/cuti/".$r[fileCuti];
		$eFile = getExtension($r[fileCuti]);
	}

	if($doc == "fileHadir"){
		$sql="select * from att_hadir where idHadir='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/hadir/".$r[fileHadir];
		$eFile = getExtension($r[fileHadir]);
	}

	if($doc == "fileLembur"){
		$sql="select * from att_lembur where idLembur='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/lembur/".$r[fileLembur];
		$eFile = getExtension($r[fileLembur]);
	}

	if($doc == "filePinjaman"){
		$sql="select * from ess_pinjaman where idPinjaman='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/pinjaman/".$r[filePinjaman];
		$eFile = getExtension($r[filePinjaman]);
	}

	if($doc == "fileDinas"){
		$sql="select * from ess_dinas where idDinas='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/dinas/".$r[fileDinas];
		$eFile = getExtension($r[fileDinas]);
	}
	
	if($doc == "mt_resume"){
		$sql="select * from mt_tugas where idTugas='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/trainee/tugas/".$r[resumeTugas];
		$eFile = getExtension($r[resumeTugas]);
	}
	
	if($doc == "mt_presentasi"){
		$sql="select * from mt_tugas where idTugas='$id'";
		$res=db($sql);
		$r=mysql_fetch_array($res);
					
		$fFile = "files/trainee/tugas/".$r[presentasiTugas];
		$eFile = getExtension($r[presentasiTugas]);
	}
    
    if($par[tipe]=="file_realisasi_individu")
    {
        $sql="select * from pen_realisasi_individu where id_realisasi='$par[id_realisasi]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/kinerja_individu/".$r[file_upload];	
		$eFile = getExtension($r[file_upload]);
    }
    
    if($par[tipe]=="file_pen_kode")
    {
        $sql="select * from pen_setting_kode where idKode='$par[idKode]'";
		$res=db($sql);
		$r=mysql_fetch_array($res);		
		
		$fFile = "files/penilaian/setting/kode/".$r[skKode];	
		$eFile = getExtension($r[skKode]);
    }
	
	if(in_array(strtolower($eFile), array("pdf","jpg","png","gif","jpeg"))){
		if(in_array($eFile, array("pdf")))
			echo "<embed src=\"".$fFile."\" width=\"100%\" height=\"475\"/>";
		else
			echo "<img src=\"".$fFile."\" width=\"100%\"/>";
	
		if(!empty($jFile)) echo "<p style=\"margin-top: 15px; text-align: center;\"><strong>".$jFile."</strong></p><br>";
		if(!empty($kFile)) echo $kFile."<hr><br>";			
			
		exit();
	}
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
    <script type="text/javascript" src="scripts/gdocs.js"></script> 
	<script type="text/javascript" src="scripts/tinybox.js"></script>
	<script type="text/javascript"> 	
		$(document).ready(function() {
			$('a.embed').gdocsViewer();
		});
	</script> 
</head>
<body class="withvernav-nobg">
	<div style="position: absolute; top: 50%; left:50%; transform: translate(-50%, -50%); z-index: -1;">
		<img src="styles/images/loader.gif" alt="">
	</div>
	<input type="hidden" id="_index" name="_index" value="popup" />
	<div class="bodywrapper">
		<a href="<?php echo $url.$fFile;?>" class="embed"></a>
	</div>
</body>
</html>