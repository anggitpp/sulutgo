<?php

include "global.php";
###############################################################
# File Download 1.31
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
###############################################################
# Sample call:
#    download.php?f=phptutorial.zip
#
# Sample call (browser will try to save with new file name):
#    download.php?f=phptutorial.zip&fc=php123tutorial.zip
###############################################################
// Allow direct file download (hotlinking)?
// Empty - allow hotlinking
// If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
define('ALLOWED_REFERRER', '');

// Download folder, i.e. folder where you keep all files for download.
// MUST end with slash (i.e. "/" )
define('BASE_DIR', 'files');

// log downloads?  true/false
define('LOG_DOWNLOADS', true);

// log file name
define('LOG_FILE', 'downloads.log');

// Allowed extensions list in format 'extension' => 'mime type'
// If myme type is set to empty string then script will try to detect mime type 
// itself, which would only work if you have Mimetype or Fileinfo extensions
// installed on server.
$allowed_ext = array(
    // archives
    'zip' => 'application/zip',
    // documents
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.ms-powerpoint',
    'csv' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    // text
    'txt' => 'text/plain',
    'log' => 'text/x-log',
    // executables
    'exe' => 'application/octet-stream',
    // images
    'gif' => 'image/gif',
    'png' => 'image/png',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'tif' => 'image/tif',
    'ico' => 'image/x-icon',
    // audio
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/x-wav',
    // video
    'mpeg' => 'video/mpeg',
    'mpg' => 'video/mpeg',
    'mpe' => 'video/mpeg',
    'mov' => 'video/quicktime',
    'avi' => 'video/x-msvideo'
);


####################################################################
###  DO NOT CHANGE BELOW
####################################################################
// If hotlinking not allowed then make hackers think there are some server problems
if (
    ALLOWED_REFERRER !== '' && (!isset($_SERVER['HTTP_REFERER']) || strpos(strtoupper($_SERVER['HTTP_REFERER']), strtoupper(ALLOWED_REFERRER)) === false)
) {
    die("Internal server error. Please contact system administrator.");
}

// Make sure program execution doesn't time out
// Set maximum script execution time in seconds (0 means no limit)
set_time_limit(0);

if (!isset($_GET['d']) || empty($_GET['d'])) {
    die("Please specify file name for download.");
}


// Nullbyte hack fix
if (strpos($_GET['d'], "\0") !== FALSE)
    die('');
if (strpos($_GET['f'], "\0") !== FALSE)
    die('');

// Get real file name.
// Remove any path info to avoid hacking by adding relative path, etc.

if ($_GET['d'] == "exp") {
    $fname = basename($_GET['f']);
    $dfile = $_GET['f'];
} else if ($_GET['d'] == "logAbsen") {
    $fname = basename("Upload Absen.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logPegawai") {
    $fname = basename("logPegawai.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logKeluarga") {
    $fname = basename("logKeluarga.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logKontak") {
    $fname = basename("logKontak.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logPelatihan") {
    $fname = basename("logPelatihan.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logPenghargaan") {
    $fname = basename("logPenghargaan.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logPeringatan") {
    $fname = basename("logPeringatan.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logBank") {
    $fname = basename("logBank.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logPendidikan") {
    $fname = basename("logPendidikan.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logKerja") {
    $fname = basename("logKerja.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logKesehatan") {
    $fname = basename("logKesehatan.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logAset") {
    $fname = basename("logAset.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logJenis") {
    $fname = basename("logJenis.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logJabatan") {
    $fname = basename("logJabatan.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "fmtPokok") {
    $fname = basename("Format Update.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "logPokok") {
    $fname = basename("Upload Update.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "fmtAbsen") {
    $fname = basename("Format Absensi.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "fmtJadwal") {
    $fname = basename("Format Jadwal.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "logMaster") {
    $fname = basename("Upload Master.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logManual") {
    $fname = basename("Upload Manual.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logService") {
    $fname = basename("Upload Service.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "logLembur") {
    $fname = basename("Upload Lembur.log");
    $dfile = date('Y-m-d H:i:s') . ".log";
} else if ($_GET['d'] == "fmtManual") {
    $fname = basename("Format Manual.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "fmtUpload") {
    $fname = basename("Format Upload.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "tmpPegawai") {
    $fname = basename("Format Upload Pegawai.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpKeluarga") {
    $fname = basename("Format Upload Keluarga.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpKontak") {
    $fname = basename("Format Upload Kontak.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpPenghargaan") {
    $fname = basename("Format Upload Penghargaan.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpPeringatan") {
    $fname = basename("Format Upload Peringatan.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpBank") {
    $fname = basename("Format Upload Bank.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpPelatihan") {
    $fname = basename("Format Upload Pelatihan.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpPendidikan") {
    $fname = basename("Format Upload Pendidikan.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpKGD") {
    $fname = basename("Format Upload KGD.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "tmpJenis") {
    $fname = basename("Format Upload Jenis.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "tmpKesehatan") {
    $fname = basename("Format Upload Kesehatan.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpAset") {
    $fname = basename("Format Upload Aset.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpKerja") {
    $fname = basename("Format Upload Kerja.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "tmpJabatan") {
    $fname = basename("Format Upload Jabatan.xls");
    $dfile = "template.xls";
} else if ($_GET['d'] == "fmtUploadPR") {
    $fname = basename("Format Upload PR.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "fmtService") {
    $fname = basename("Format Service.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "fmtLembur") {
    $fname = basename("Format Lembur.xlsx");
    $dfile = "template.xlsx";
} else if ($_GET['d'] == "komponen") {
    $sql = "select * from dta_komponen where idKomponen='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileKomponen']);
    $dfile = $r['fileKomponen'];
} else if ($_GET['d'] == "pokok") {
    $sql = "select * from pay_pokok where idPokok='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filePokok']);
    $dfile = $r['filePokok'];
} else if ($_GET['d'] == "fileMenu") {
    $sql = "select * from app_menu where kodeMenu='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    // echo $sql." ".$r[fileMenu];

    $fname = basename($r['fileMenu']);
    $dfile = $r['fileMenu'];
} else if ($_GET['d'] == "melekat") {
    $sql = "select * from pay_melekat where idMelekat='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileMelekat']);
    $dfile = $r['fileMelekat'];
} else if ($_GET['d'] == "koreksi") {
    $sql = "select * from pay_koreksi where idKoreksi='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileKoreksi']);
    $dfile = $r['fileKoreksi'];
} else if ($_GET['d'] == "essKoreksi") {
    $sql = "select fileKoreksi from att_koreksi where idKoreksi='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileKoreksi']);
    $dfile = $r['fileKoreksi'];
} //MODUL RECRUITMENT
else if ($_GET['d'] == "recPendidikan") {
    $sql = "select * from rec_applicant_edu where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['edu_filename']);
    $dfile = $r['edu_filename'];
} else if ($_GET['d'] == "recSK") {
    $sql = "select * from rec_applicant_placement where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['sk_file']);
    $dfile = $r['sk_file'];
} else if ($_GET['d'] == "recPengalaman") {
    $sql = "select * from rec_applicant_pwork where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "recPelatihan") {
    $sql = "select * from rec_applicant_ptraining where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "recFile") {
    $sql = "select * from rec_applicant_pfile where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "recSelection") {
    $sql = "select sel_filename from rec_selection_appl where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['sel_filename']);
    $dfile = $r['sel_filename'];
} else if ($_GET['d'] == "sakit") {
    $sql = "select * from att_sakit where idSakit='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileSakit']);
    $dfile = $r['fileSakit'];
} else if ($_GET['d'] == "catatan") {
    $sql = "select * from catatan_sistem where idCatatan='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['File']);
    $dfile = $r['File'];
} else if ($_GET['d'] == "essHadir") {
    $sql = "select * from att_hadir where idHadir='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileHadir']);
    $dfile = $r['fileHadir'];
} else if ($_GET['d'] == "essCuti") {
    $sql = "select * from att_cuti where idCuti='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileCuti']);
    $dfile = $r['fileCuti'];
} else if ($_GET['d'] == "essLembur") {
    $sql = "select * from att_lembur where idLembur='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileLembur']);
    $dfile = $r['fileLembur'];
} else if ($_GET['d'] == "kas") {
    $sql = "select * from ess_kas where idKas='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileKas']);
    $dfile = $r['fileKas'];
} else if ($_GET['d'] == "bukti") {
    $sql = "select * from rawatinap_klaim where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['buktiKlaim']);
    $dfile = $r['buktiKlaim'];
} else if ($_GET['d'] == "sto") {
    $sql = "select * from emp_struktur where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['file']);
    $dfile = $r['file'];
} else if ($_GET['d'] == "tiket") {
    $sql = "select * from ess_tiket where idTiket='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileTiket']);
    $dfile = $r['fileTiket'];
} else if ($_GET['d'] == "dinas") {
    $sql = "select * from ess_dinas where idDinas='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['fileDinas']);
    $dfile = $r['fileDinas'];
} else if ($_GET['d'] == "kacamata") {
    $sql = "select * from emp_rmb_files where parent_id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "kesehatan") {
    $sql = "select * from emp_rmb_files where parent_id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "pinjaman") {
    $sql = "select * from ess_pinjaman where idPinjaman='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filePinjaman']);
    $dfile = $r['filePinjaman'];
} else if ($_GET['d'] == "empktp") {
    $sql = "select ktp_filename from emp where id='" . $_GET['f'] . "'";
    // echo $sql;
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['ktp_filename']);
    $dfile = $r['ktp_filename'];
} else if ($_GET['d'] == "empFamily") {
    $sql = "select rel_filename from emp_family where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['rel_filename']);
    $dfile = $r['rel_filename'];
} else if ($_GET['d'] == "empFile") {
    $sql = "select filename from emp_file where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empBank") {
    $sql = "select filename from emp_bank where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empcareer") {
    $sql = "select sk_filename from emp_career where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['sk_filename']);
    $dfile = $r['sk_filename'];
} else if ($_GET['d'] == "empcontract") {
    $sql = "select file_sk from emp_pcontract where id='" . $_GET['f'] . "'";
    // echo $sql;
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['file_sk']);
    $dfile = $r['file_sk'];
} else if ($_GET['d'] == "emppwork") {
    $sql = "select filename from emp_pwork where id='" . $_GET['f'] . "'";
    // echo $sql;
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empTraining") {
    $sql = "select filename from emp_training where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empsk") {
    $sql = "select file_sk filename from emp_phist where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "emppkwt") {
    $sql = "select file_pkwt filename from emp_phist where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empReward") {
    $sql = "select filename from emp_reward where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empPunish") {
    $sql = "select filename from emp_punish where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empHealth") {
    $sql = "select filename from emp_health where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empEdu") {
    $sql = "select filename from emp_edu where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empAsset") {
    $sql = "select filename from emp_asset where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "emprmb") {
    $sql = "select filename filename from emp_rmb_files where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empWork") {
    $sql = "select filename filename from emp_pwork where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empDoc") {
    $sql = "select filename from emp_info_doc where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empInfo") {
    $sql = "select filename from emp_info_news where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];
} else if ($_GET['d'] == "empPengumuman") {
    $sql = "select filename from emp_pengumuman where id='" . $_GET['f'] . "'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $fname = basename($r['filename']);
    $dfile = $r['filename'];

} else if ($_GET['d'] == "dokumen_penilaian") {

    $res = db("SELECT `fileDokumen` FROM `pen_dokumen` WHERE `kodeDokumen` = '$_GET[f]'");
    $row = mysql_fetch_assoc($res);

    $fname = basename($row['fileDokumen']);
    $dfile = $row['fileDokumen'];

} else {
    die('');
}

// Check if the file exists
// Check in subfolders too
function find_file($dirname, $fname, &$file_path)
{
    $dir = opendir($dirname);

    while ($file = readdir($dir)) {
        if (empty($file_path) && $file != '.' && $file != '..') {
            if (is_dir($dirname . '/' . $file)) {
                find_file($dirname . '/' . $file, $fname, $file_path);
            } else {
                if (file_exists($dirname . '/' . $fname)) {
                    $file_path = $dirname . '/' . $fname;
                    return;
                }
            }
        }
    }
}

// find_file
// get full file path (including subfolders)
$file_path = '';
find_file(BASE_DIR, $fname, $file_path);

if (!is_file($file_path)) {
    die("File does not exist. Make sure you specified correct file name.");
}

// file size in bytes
$fsize = filesize($file_path);

// file extension
$fext = strtolower(substr(strrchr($fname, "."), 1));

// check if allowed extension
if (!array_key_exists($fext, $allowed_ext)) {
    die("Not allowed file type.");
}

// get mime type
if ($allowed_ext[$fext] == '') {
    $mtype = '';
    // mime type is not set, get from server settings
    if (function_exists('mime_content_type')) {
        $mtype = mime_content_type($file_path);
    } else if (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME); // return mime type
        $mtype = finfo_file($finfo, $file_path);
        finfo_close($finfo);
    }
    if ($mtype == '') {
        $mtype = "application/force-download";
    }
} else {
    // get mime type defined by admin
    $mtype = $allowed_ext[$fext];
}

// Browser will try to save file with this filename, regardless original filename.
// You can override it if needed.

if (!isset($_GET['fc']) || empty($_GET['fc'])) {
    $asfname = $fname;
} else {
    // remove some bad chars
    $asfname = str_replace(array('"', "'", '\\', '/'), '', $_GET['fc']);
    if ($asfname === '')
        $asfname = 'NoName';
}

$downloadFile = empty($dfile) ? "unknown" : $dfile;

// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: $mtype");
header("Content-Disposition: attachment; filename=\"$downloadFile\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);

// download
// @readfile($file_path);
$file = @fopen($file_path, "rb");
if ($file) {
    while (!feof($file)) {
        print(fread($file, 1024 * 8));
        flush();
        if (connection_status() != 0) {
            @fclose($file);
            die();
        }
    }
    @fclose($file);
}

// log downloads
if (!LOG_DOWNLOADS)
    die();
