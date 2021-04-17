<?php
require "app.php";

session_start();

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

//error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("memory_limit", "256M");
ini_set('max_execution_time', 7200);
ini_set('post_max_size', '20M');
ini_set('upload_max_filesize', '20M');
date_default_timezone_set("Asia/Jakarta");

include "app/Helpers/helper.mysql.php";
require("plugins/PHPMailer/PHPMailerAutoload.php");
require("global.custome.php");

/*
 * Mazte Added
 */

define('DS', DIRECTORY_SEPARATOR);
define('APP_URL', "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
define('HOME_DIR', dirname(__FILE__));
define('MODEL_DIR', HOME_DIR . DS . "sources/" . DS . "models" . DS);
define('COMMON_DIR', HOME_DIR . DS . "sources" . DS . "common" . DS);

require_once MODEL_DIR . "/_init.models.php";
/*
 * End Mazte Added
 */

$db['host'] = env('DB_HOST') . ":" . env('DB_PORT');
$db['name'] = env('DB_DATABASE');
$db['user'] = env('DB_USERNAME');
$db['pass'] = env('DB_PASSWORD');

$sUser = "hambaallah";
$sGroup = "Hamba Allah";
$arrColor = array("#ba8b26", "#0020e0", "#e00020", "#5fe000", "#128a92", "#851712", "#394a67", "#a41899");

$cUsername = $_SESSION[cUsername];
$cPassword = $_SESSION[cPassword];
$cGroup = $_SESSION[cGroup];
$cNama = $_SESSION[cNama];
$cFoto = $_SESSION[cFoto];
$cID = $_SESSION[cID];
$cUser = $_SESSION[cUser];

$ui = new UIHelper();

regAccess();
logAccess();

if (getUser()) {
    $areaCheck = "SELECT kodeArea FROM app_user_area WHERE username = '$cUsername'";
    /*  setcookie("cSession", date('Y-m-d H:i:s'));
    $nSession = date('Y-m-d H:i:s');
    if (!empty($cSession) && abs(selisihMenit($cSession, $nSession)) > 10) {
      echo "<script>
      alert('sesi berakhir, silahkan login kembali');
      parent.window.location='logout.php';
    </script>";

  }   */
}


$inp[page] = 5;
$kodeModul = $c;
$kodeSite = $p;
$menuAccess = arrayQuery("select t1.kodeMenu,t1.statusGroup,t2.username from app_group_menu t1 join app_user t2 on (t1.kodeGroup = t2.kodeGroup) where t2.username='$cUsername'");


$filter = " and t1.kodeModul='" . $kodeModul . "'";
$arrSite = arrayQuery("select t1.kodeSite, t1.namaSite from app_site t1 join app_menu t2 join app_group_menu t3 join app_user t4 on (t1.kodeSite=t2.kodeSite and t2.kodeMenu=t3.kodeMenu and t3.kodeGroup=t4.kodeGroup) where t1.statusSite='t' and t4.username='$cUsername' " . $filter . " order by t1.urutanSite");
$arrParameter = arrayQuery("select kodeParameter, nilaiParameter from app_parameter order by kodeParameter");

$sql = "select * from app_menu t1 join app_modul t2 on (t1.kodeModul=t2.kodeModul) where t1.statusMenu='t' " . $filter . " order by t1.kodeMenu";
$res = db($sql);
while ($r = mysql_fetch_array($res)) {
    if (empty($r[kodeInduk]))
        $arrParent["$r[kodeSite]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

    if (empty($r[kodeInduk]))
        $arrInduk["$r[kodeSite]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

    $arrMenu["$r[kodeInduk]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

    $arrMenu_site["$r[kodeSite]"]["$r[kodeInduk]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

    $arrTarget = explode("/", $r[targetMenu]);
    $script = $arrTarget[count($arrTarget) - 1];
    unset($arrTarget[count($arrTarget) - 1]);
    $folder = implode("/", $arrTarget);

    $arrSource["$r[kodeMenu]"] = "sources/" . $r[targetMenu] . ".php";
    $arrScript["$r[kodeMenu]"] = "sources/" . $folder . "/js/" . $script . ".js";
    $arrTitle["$r[kodeMenu]"] = $r[namaMenu];
    $arrFlag["$r[kodeMenu]"] = $r[kodeSite];
    $arrParam["$r[kodeMenu]"] = $r[parameterMenu];
    $arrUrutan["$r[kodeMenu]"] = $r[urutanMenu];
    $arrTop["$r[kodeMenu]"] = $r[kodeSite] . "\t" . $r[kodeInduk];
}

if (is_array($arrInduk)) {
    asort($arrInduk);
    reset($arrInduk);
    while (list($kodeSite) = each($arrInduk)) {
        if (is_array($arrInduk[$kodeSite])) {
            asort($arrInduk[$kodeSite]);
            reset($arrInduk[$kodeSite]);
            while (list($kodeInduk) = each($arrInduk[$kodeSite])) {
                $cntMenu["s" . $kodeSite] += isset($menuAccess[$kodeInduk]["view"]) ? 1 : 0;
                $cntMenu[$kodeInduk] += isset($menuAccess[$kodeInduk]["view"]) ? 1 : 0;
                if (is_array($arrMenu[$kodeInduk])) {
                    asort($arrMenu[$kodeInduk]);
                    reset($arrMenu[$kodeInduk]);
                    while (list($kodeMenu) = each($arrMenu[$kodeInduk])) {
                        $cntMenu["s" . $kodeSite] += isset($menuAccess[$kodeMenu]["view"]) ? 1 : 0;
                        $cntMenu[$kodeInduk] += isset($menuAccess[$kodeMenu]["view"]) ? 1 : 0;
                        $cntMenu[$kodeMenu] += isset($menuAccess[$kodeMenu]["view"]) ? 1 : 0;
                        if (is_array($arrMenu[$kodeMenu])) {
                            asort($arrMenu[$kodeMenu]);
                            reset($arrMenu[$kodeMenu]);
                            while (list($kodeMenu1) = each($arrMenu[$kodeMenu])) {
                                $cntMenu["s" . $kodeSite] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                                $cntMenu[$kodeInduk] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                                $cntMenu[$kodeMenu] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                                $cntMenu[$kodeMenu1] += isset($menuAccess[$kodeMenu1]["view"]) ? 1 : 0;
                            }
                        }
                    }
                }
            }
        }
    }
}


if (is_array($arrInduk)) {
    arsort($arrInduk);
    reset($arrInduk);
    while (list($kodeSite) = each($arrInduk)) {
        if (is_array($arrInduk[$kodeSite])) {
            arsort($arrInduk[$kodeSite]);
            reset($arrInduk[$kodeSite]);
            while (list($keyInduk, $valInduk) = each($arrInduk[$kodeSite])) {
                list($urutanInduk, $kodeInduk, $namaInduk, $iconInduk) = explode("\t", $valInduk);
                if ($cntMenu[$kodeInduk] > 0) {
                    $arrDef[$kodeSite][$keyInduk] = $kodeInduk . "\t" . $kodeInduk;
                    $arrKey[$kodeSite] = $kodeInduk . "\t" . $kodeInduk;
                    if (is_array($arrMenu[$kodeInduk])) {
                        arsort($arrMenu[$kodeInduk]);
                        reset($arrMenu[$kodeInduk]);
                        while (list($keyMenu, $valMenu) = each($arrMenu[$kodeInduk])) {
                            list($urutanMenu, $kodeMenu, $namaMenu, $iconMenu) = explode("\t", $valMenu);
                            if ($cntMenu[$kodeMenu] > 0) {
                                $arrDef[$kodeSite][$keyInduk] = $kodeMenu . "\t" . $kodeMenu;
                                $arrKey[$kodeSite] = $kodeInduk . "\t" . $kodeMenu;
                                if (is_array($arrMenu[$kodeMenu])) {
                                    arsort($arrMenu[$kodeMenu]);
                                    reset($arrMenu[$kodeMenu]);
                                    while (list($keyMenu1, $valMenu1) = each($arrMenu[$kodeMenu])) {
                                        list($urutanMenu1, $kodeMenu1, $namaMenu1, $iconMenu1) = explode("\t", $valMenu1);
                                        if ($cntMenu[$kodeMenu1] > 0) {
                                            $arrDef[$kodeSite][$keyInduk] = $kodeMenu . "\t" . $kodeMenu1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}


if (empty($m))
    list($m, $s) = explode("\t", $arrDef[$c][$p]);
if (empty($m))
    $m = $s = $p;

$arrMaps = array(
    "01" => "1", #Aceh
    "02" => "17", #Bali
    "03" => "3", #Bengkulu
    "04" => "13", #DKI Jakarta
    "05" => "4", #Jambi
    "07" => "14", #Jawa Tengah
    "08" => "15", #Jawa Timur
    "10" => "16", #Daerah Istimewa Yogyakarta
    "11" => "20", #Kalimantan Barat
    "12" => "21", #Kalimantan Selatan
    "13" => "22", #Kalimantan Tengah
    "14" => "23", #Kalimantan Timur
    "15" => "8", #Lampung
    "17" => "18", #Nusa Tenggara Barat
    "18" => "19", #Nusa Tenggara Timur
    "21" => "27", #Sulawesi Tengah
    "22" => "26", #Sulawesi Tenggara
    "24" => "6", #Sumatera Barat
    "26" => "2", #Sumatera Utara
    "28" => "30", #Maluku
    "29" => "31", #Maluku Utara
    "30" => "12", #Jawa Barat
    "31" => "28", #Sulawesi Utara
    "32" => "7", #Sumatera Selatan
    "33" => "11", #Banten
    "34" => "24", #Gorontalo
    "35" => "9", #Kepulauan Bangka Belitung
    "36" => "32", #Papua
    "37" => "5", #Riau
    "38" => "25", #Sulawesi Selatan
    "39" => "33", #Papua Barat
    "40" => "10", #Kepulauan Riau
    "41" => "29", #Sulawesi Barat
);

function empLocHeader()
{
    return "
  <div class=\"notibar announcement\" style=\"background-color: #FFD863; border-color: #FFD863; color: #000; margin: 0 20px 30px 20px;\">
    <a class=\"close\"></a>
    <p><b>Informasi : </b>Pastikan setiap pegawai sudah disetting lokasi kerja</p>
  </div>
  <script type=\"text/javascript\">
    jQuery(document).ready(function() {

      function hidePanel() {     
        jQuery(\"a.close\").click();
      }
      
      setTimeout(hidePanel, 5000);
    });
  </script>
  ";
}

function convertMinsToHours($time, $format = '%02d:%02d')
{
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function getLastId($table, $key)
{
    return getField("select $key from $table order by $key desc limit 1") + 1;
}

function getBread($mode = "")
{
    global $c, $p, $s, $m, $par, $arrTitle;
    if (!empty($par[kodeCategory]))
        $cat = "&par[mode]=det&par[kodeCategory]=$par[kodeCategory]";

    $text .= "<ul class=\"breadcrumbs\">
  <li><a href=\"main.php\" target=\"_top\">Home</a></li>";
    $text .= getTopBread($s);
    $text .= empty($mode) ? "<li>" . $arrTitle[$s] . "</li>" : "<li><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $m . "&s=" . $s . $cat . "\" target=\"_top\">" . $arrTitle[$s] . "</a></li>
  <li>" . $mode . "</li>";
    $text .= "</ul>";
    return $text;
}

function getTopBread($kodeMenu)
{
    global $c, $p, $m, $par, $arrTitle, $arrSite, $arrTop, $arrKey, $arrDef;
    list($kodeSite, $kodeInduk) = explode("\t", $arrTop[$kodeMenu]);

    if (empty($arrTitle[$kodeInduk]))
        list($kodeMenu, $kodeMenu1) = explode("\t", $arrKey[$kodeSite]);
    else
        list($kodeMenu, $kodeMenu1) = explode("\t", $arrDef[$kodeSite][$kodeInduk]);

    if (!empty($kodeInduk))
        $result = getTopBread($kodeInduk);

    if (in_array($m, array(5, 18, 39, 71, 88)) && !empty($arrTitle[$kodeInduk])) # hack master data
        $kodeMenu = $kodeInduk;

    if (empty($arrSite[$c]))
        $result .= empty($arrTitle[$kodeInduk]) ? "" : "<li><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeInduk . "&s=" . $kodeMenu . "\" target=\"_top\">" . $arrTitle[$kodeInduk] . "</a></li>";
    else
        $result .= empty($arrTitle[$kodeInduk]) ? "<li><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu1 . "\" target=\"_top\">" . $arrSite[$c] . "</a></li>" : "<li><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeInduk . "&s=" . $kodeMenu . "\" target=\"_top\">" . $arrTitle[$kodeInduk] . "</a></li>";

    return $result;
}

function logAccess()
{
    global $c, $p, $s, $par, $_submit, $cUsername;
    $aktivitasLog = isset($par[mode]) ? $par[mode] : "open page";
    $aktivitasLog = $par[mode] == "det" ? "view detail" : $aktivitasLog;
    $aktivitasLog = $par[mode] == "del" ? "delete data" : $aktivitasLog;
    $aktivitasLog = ($par[mode] == "add" && !empty($_submit)) ? "input data" : $aktivitasLog;
    $aktivitasLog = ($par[mode] == "edit" && !empty($_submit)) ? "edit data" : $aktivitasLog;
    $aktivitasLog = ($par[mode] == "det" && !empty($_submit)) ? "update data" : $aktivitasLog;

    if (!empty($cUsername) && in_array($aktivitasLog, array("open page", "view detail", "input data", "edit data", "delete data", "update data"))) {
        $kodeLog = getField("select kodeLog from log_access order by kodeLog desc limit 1") + 1;
        $kodeModul = empty($c) ? 0 : $c;
        $kodeSite = empty($p) ? 0 : $p;
        $kodeMenu = empty($s) ? 0 : $s;
        $sql = "insert into log_access (kodeLog, kodeModul, kodeSite, kodeMenu, aktivitasLog, createBy, createTime) values ('$kodeLog', '$kodeModul', '$kodeSite', '$kodeMenu', '$aktivitasLog', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
        db($sql);
    }

    $createDate = date("Y-m-d", dateMin("d", 10, mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"))));
    if (getField("select count(*) from log_access where date(createTime) < '" . $createDate . "'")) {
        $sql = "delete from log_access where date(createTime) < '" . $createDate . "'";
        db($sql);
    }
}

function regAccess($order = 'egpcs')
{
    if (!function_exists('register_global_array')) {

        function register_global_array(array $superglobal)
        {
            foreach ($superglobal as $varname => $value) {
                global $$varname;
                $$varname = $value;
            }
        }
    }

    $order = explode("\r\n", trim(chunk_split($order, 1)));
    foreach ($order as $k) {
        switch (strtolower($k)) {
            case 'e':
                register_global_array($_ENV);
                break;
            case 'g':
                register_global_array($_GET);
                break;
            case 'p':
                register_global_array($_POST);
                break;
            case 'c':
                register_global_array($_COOKIE);
                break;
            case 's':
                register_global_array($_SERVER);
                break;
        }
    }
}

function db($sql)
{
    global $conn, $db;
    if (!isset($conn)) {
        $conn = mysql_connect("$db[host]", "$db[user]", "$db[pass]") or die("server is currently offline");
        mysql_select_db("$db[name]");
    }
    if (!$result = mysql_query($sql, $conn)) {
        // echo "$sql<br>Proses ke database gagal<br>";
        // mysql_error($conn);
    }
    return $result;
}


function debug($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}


function resizeImage($path, $file, $widthValue, $heightValue)
{
    #CREATED BY BHOTINK
    $ext = getExtension($path . $file);

    if ($ext == "jpg" || $ext == "jpeg") $orig_image = imagecreatefromjpeg($path . $file);
    if ($ext == "png") $orig_image = imagecreatefrompng($path . $file);
    if ($ext == "gif") $orig_image = imagecreatefromgif($path . $file);

    list($width, $height) = getimagesize($path . $file);
    $width_orig = $width;
    $height_orig = $height;

    $widthResize = $widthValue;
    $heightResize = $heightValue;

    $destination_image = imagecreatetruecolor($widthResize, $heightResize);
    imagecopyresampled($destination_image, $orig_image, 0, 0, 0, 0, $widthResize, $heightResize, $width_orig, $height_orig);
    unlink($path . $file);
    imagejpeg($destination_image, $path . $file, 100);

    return $file;
}

function sendSMS($to, $message)
{
    if (!empty($to) && !empty($message)) {
        $userkey = "3xrq20";
        $passkey = "sinergics";
        $xmlResponse = simplexml_load_file("https://reguler.zenziva.net/apps/smsapi.php?userkey=" . $userkey . "&passkey=" . $passkey . "&nohp=" . $to . "&pesan=" . urlencode($message));
        $jsonResponse = json_encode($xmlResponse);
        $response = json_decode($jsonResponse);

        return $response->message;
    }
}

function sendMail($address, $subject, $message)
{
    $mail = new PHPMailer;
    // $mail->isSMTP();
    $mail->SMTPDebug = 0;

    $mail->Debugoutput = 'html';
    $mail->Host = 'mail.sinergics.net';

    $mail->Port = 465;
    $mail->SMTPSecure = 'ssl';

    $mail->SMTPAuth = true;
    $mail->Username = "sulutgo@sinergics.net";
    $mail->Password = "3Msa?1tg7&t{";

    $mail->setFrom('sulutgo@sinergics.net', 'noreply');
    //$mail->addReplyTo('noreply@mdspustaka.com', 'noreply');

    $mail->addAddress($address);
    $mail->Subject = $subject;
    $mail->msgHTML($message);
    if (!$mail->send()) {
        return "Mailer Error: " . $mail->ErrorInfo;
    } else {
        return "DONE";
    }
}

function repField($ex = array())
{
    global $inp;
    if (is_array($inp)) {
        while (list($id, $nilai) = each($inp)) {
            $inp[$id] = in_array($id, $ex) ? $nilai : mysql_real_escape_string($nilai);
            // $inp[$id] = in_array($id, $ex) ? $nilai : $nilai;
        }
    }
    return $inp;
}

function getUser()
{
    global $cUsername, $cPassword;
    $cUsername = mysql_real_escape_string($cUsername);
    $cPassword = mysql_real_escape_string($cPassword);
    if ($cUsername == "" or $cPassword == "") {
        return false;
    } else {
        if (!getField("select username from app_user where username='$cUsername' and password='$cPassword' and statusUser='t'")) {
            return false;
        }
    }
    return true;
}

function getField($sql)
{
    $res = db($sql);
    $r = mysql_fetch_row($res);
    return $r[0];
}

function getSizeFile($file)
{
    $bytes = filesize($file);
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function dbpage($sql)
{
    global $inp, $par;
    if ($par[hal] == "") {
        $par[hal] = 0;
        $next = $par[hal] + 1;
    } else {
        $prev = $par[hal] - 1;
        $next = $par[hal] + 1;
    }
    $hal_sql = $par[hal] * $inp[page];
    $res = db($sql);
    $jumlah = mysql_num_rows($res);
    $jml = ceil($jumlah / $inp[page]);
    $jmlakhir = $jml - 1;
    $str_list .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr>";
    if ($jumlah > $inp[page]) {
        if ($par[hal] == 0) {
            $str_list .= "<td style=\"padding:3px;\" align=center>" . comboPage($jml, getPar($par, "hal")) . "</td>";
            $str_list .= "<td style=\"padding:3px;\" width=20 align=right nowrap><a href=\"?par[hal]=$next" . getPar($par, "hal") . "\" title=\"Next Page\"  class=\"tmb\"><span>&rsaquo;</span></a></td><td width=20 align=right nowrap><a href=\"?par[hal]=$jmlakhir" . getPar($par, "hal") . "\" title=\"Last Page\"  class=\"tmb\"><span>&raquo;</span></a></td>";
        } else {
            $str_list .= "<td style=\"padding:3px;\" width=20 align=left nowrap><a href=\"?par[hal]=0" . getPar($par, "hal") . "\" title=\"First Page\"  class=\"tmb\"><span>&laquo;</span></a></td>
        <td style=\"padding:3px;\" width=20 align=left nowrap><a href=\"?par[hal]=$prev" . getPar($par, "hal") . "\" title=\"Previous Page\"  class=\"tmb\"><span>&lsaquo;</span></a></td>";
            $str_list .= "<td style=\"padding:3px;\" align=center>" . comboPage($jml, getPar($par, "hal")) . "</td>";
            if ($par[hal] < ($jml - 1)) {
                $str_list .= "<td style=\"padding:3px;\" width=20 align=right nowrap><a href=\"?par[hal]=$next" . getPar($par, "hal") . "\" title=\"Next Page\"  class=\"tmb\"><span>&rsaquo;</span></a></td>
          <td style=\"padding:3px;\" width=20 align=right nowra><a href=\"?par[hal]=$jmlakhir" . getPar($par, "hal") . "\" title=\"Last Page\"  class=\"tmb\"><span>&raquo;</span></td>";
            }
        }
    }
    $str_list .= "</tr></table>";
    $res = db($sql . " limit " . $inp[page] . " offset " . $hal_sql);
    if (mysql_num_rows($res) == 0 && $par[hal] > 0)
        echo "<script>window.location='?par[hal]=" . ($par[hal] - 1) . "" . getPar($par, "hal") . "';</script>";

    return array("view" => "<form>
     <table align=\"center\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">						
      <tr>		
       <td>$str_list</td>
     </tr>
   </table>
 </form>", "result" => $res);
}

function api_key_map()
{
    return "AIzaSyC6LP62m34rPrT-h_VHnSQG7Arafti_wOI";
}

function comboPage($jml, $filter)
{
    global $inp, $par;
    $txt .= "<select name=par[hal] onChange=\"javascript:page('mainFrame',this,0)\">";
    for ($i = 1; $i <= $jml; $i++) {
        if ($par[hal] == ($i - 1)) {
            $txt .= "<option value=\"?par[hal]=" . ($i - 1) . "" . getPar($par, "hal") . "\" selected>Page $i</option>";
        } else {
            $txt .= "<option value=\"?par[hal]=" . ($i - 1) . "" . getPar($par, "hal") . "\">Page $i</option>";
        }
    }
    $txt .= "</select>";
    return $txt;
}

function setValidation($param, $nm_obj, $message = "", $rg_awal = "0", $rg_akhir = "0")
{
    global $rule;
    switch ($param) {
        case "is_null":
            $rule .= "t10_checkisi(document.getElementById('$nm_obj'),\"$message\");\n";
            break;
        case "is_mail":
            $rule .= "t10_checkmail(document.getElementById('$nm_obj'),\"$message\");\n";
            break;
        case "is_date":
            $rule .= "t10_checkvaliddate(document.getElementById('$nm_obj[0]'),document.getElementById('$nm_obj[1]'),document.getElementById('$nm_obj[2]'));\n";
            break;
        case "is_num":
            $rule .= "t10_checknum(document.getElementById('$nm_obj'),\"$message\");\n";
            break;
        case "is_range":
            $rule .= "t10_checkrange(document.getElementById('$nm_obj'),\"$message\",$rg_awal,$rg_akhir);\n";
            break;
    }
}

function getValidation()
{
    global $rule;
    $text = "<script language=javascript src=\"scripts/validation.js\"></script>
    <script language=\"javascript\">
      var valid;
      function validation(var_nama_form) {
       var nm_form=var_nama_form;
       valid=true;
       " . $rule . "
       if (valid){
         return true;
       }else{
         return false;
       }
     }
   </script>";
    return $text;
}

function getTable($table)
{
    $sql = "show columns from $table";
    $res = db($sql);
    $field = array();
    while ($r = mysql_fetch_array($res)) {
        $field["$r[0]"] = "";
    }
    return $field;
}

function table($cols = 0, $nosort = array(), $mode = "lst", $paging = "true", $scroll = "", $tabel = "dataList", $param = "", $get = "")
{
    global $par;
    $result = "<script type=\"text/javascript\">		
  jQuery(document).ready(function() {												
   var oTable = jQuery('#" . $tabel . "').dataTable( {
    'bProcessing': true,
    'bServerSide': true,
    'sAjaxSource': 'ajax.php?par[mode]=" . $mode . "" . $param . "" . getPar($par, "mode") . "',
    'sPaginationType': 'full_numbers',
    'bFilter': false,
    'aLengthMenu': [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, \"All\"]],";

    if ($scroll == "hv") {
        $result .= "'sScrollY': '225px',   
      'sScrollX': '100%',";
    }

    if ($scroll == "h") {
        $result .= "'sScrollX': '100%',";
    }

    if ($scroll == "v") {
        $result .= "'sScrollY': '300px',";
    }

    if ($paging == "false") {
        $result .= "'bPaginate': false,
      'bInfo': false,";
    }

    if (!empty($cols)) {
        $result .= "'aoColumns': [";
        for ($i = 1; $i <= $cols; $i++)
            $result .= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
        $result .= "],";
    } else {
        $result .= "'bSort': false,";
    }

    $result .= "'iDisplayStart':parseInt(jQuery('#_page').val()),
    'iDisplayLength':parseInt(jQuery('#_len').val()),
    'sDom': 'frtlip',
    'oLanguage':{						
     'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
     'sInfoFiltered': '<span></span>',
     'sProcessing': '<img src=\"styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
   },

   'fnDrawCallback': function () {				
     jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
     jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));";

    if (!empty($get))
        $result .= "arr = jQuery('#chk').val().split('\t');					
    for(i=0; i< arr.length; i++){						
      jQuery('#id_' + arr[i] + '').prop('checked', true );
    }
    ";

    $result .= "},

    'fnServerParams': function (aoData) {											
      aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
      aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
      aoData.push({ 'name': 'cSearch', 'value': jQuery('#cSearch').val() });
      aoData.push({ 'name': 'dSearch', 'value': jQuery('#dSearch').val() });
      aoData.push({ 'name': 'eSearch', 'value': jQuery('#eSearch').val() });
      aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
      aoData.push({ 'name': 'gSearch', 'value': jQuery('#gSearch').val() });
      aoData.push({ 'name': 'hSearch', 'value': jQuery('#hSearch').val() });
      aoData.push({ 'name': 'iSearch', 'value': jQuery('#iSearch').val() });
      aoData.push({ 'name': 'jSearch', 'value': jQuery('#jSearch').val() });
      aoData.push({ 'name': 'kSearch', 'value': jQuery('#kSearch').val() });
      aoData.push({ 'name': 'lSearch', 'value': jQuery('#lSearch').val() });
      aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
      aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
      aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
      aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
      aoData.push({ 'name': 'zSearch', 'value': jQuery('#zSearch').val() });
      aoData.push({ 'name': 'dirSearch', 'value': jQuery('#dirSearch').val() });

      aoData.push({ 'name': 'combo1', 'value': jQuery('#combo1').val() });
      aoData.push({ 'name': 'combo2', 'value': jQuery('#combo2').val() });
      aoData.push({ 'name': 'combo3', 'value': jQuery('#combo3').val() });
      aoData.push({ 'name': 'combo4', 'value': jQuery('#combo4').val() });
      aoData.push({ 'name': 'combo5', 'value': jQuery('#combo5').val() });
      aoData.push({ 'name': 'combo6', 'value': jQuery('#combo6').val() });
      aoData.push({ 'name': 'combo7', 'value': jQuery('#combo7').val() });
      aoData.push({ 'name': 'combo8', 'value': jQuery('#combo8').val() });
      aoData.push({ 'name': 'combo9', 'value': jQuery('#combo9').val() });
      aoData.push({ 'name': 'combo10', 'value': jQuery('#combo10').val() });
    },			
  });
  
  jQuery('#search').keyup(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#aSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });	

  jQuery('#bSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#cSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#dSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });	

  jQuery('#eSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });	

  jQuery('#fSearch').keyup(function(){
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#gSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  }); 

  jQuery('#hSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  }); 

  jQuery('#iSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  }); 

  jQuery('#jSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  }); 

  jQuery('#lSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#mSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#pSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });	

  jQuery('#sSearch').keyup(function(){
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });

  jQuery('#tSearch').change(function(){	
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });			

  jQuery('#zSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  });									

  jQuery('#dirSearch').change(function(){ 
    oTable.fnPageChange(0);
    oTable.fnReloadAjax();
  }); 

  jQuery('#combo1').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo2').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 });

 jQuery('#combo3').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo4').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 });

 jQuery('#combo5').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo6').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo7').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo8').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo9').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#combo10').change(function(){ 
   oTable.fnPageChange(0);
   oTable.fnReloadAjax();
 }); 

 jQuery('#fSearch').attr('placeholder', 'search ...');
 jQuery('#fSearch').attr('style', 'background: url(\"styles/images/filter.png\") no-repeat; width:200px; padding-left:30px;');
 
 jQuery('#search').attr('placeholder', 'search ...');
 jQuery('#search').attr('style', 'background: url(\"styles/images/filter.png\") no-repeat; width:200px; padding-left:30px;');
 ";

    $result .= "});						
</script>";
    return $result;
}

function table2($cols = 0, $nosort = array(), $mode = "lst", $paging = "true", $scroll = "", $tabel = "dataList", $param = "", $get = "")
{
    global $par;
    $result = "<script type=\"text/javascript\">		
  jQuery(document).ready(function() {												
   var oTable = jQuery('#" . $tabel . "').dataTable( {
    'bProcessing': true,
    'bServerSide': true,
    'sAjaxSource': 'ajax.php?par[mode]=" . $mode . "" . $param . "" . getPar($par, "mode") . "',
    'sPaginationType': 'full_numbers',
    'bFilter': false,
    'aLengthMenu': [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, \"All\"]],";

    if ($scroll == "hv") {
        $result .= "'sScrollY': '225px',   
      'sScrollX': '100%',";
    }

    if ($scroll == "h") {
        $result .= "'sScrollX': '100%',";
    }

    if ($scroll == "v") {
        $result .= "'sScrollY': '300px',";
    }

    if ($paging == "false") {
        $result .= "'bPaginate': false,
      'bInfo': false,";
    }

    if (!empty($cols)) {
        $result .= "'aoColumns': [";
        for ($i = 1; $i <= $cols; $i++)
            $result .= (in_array($i, $nosort) || $i == 1) ? "{'bSortable': false}," : "null,";
        $result .= "],";
    } else {
        $result .= "'bSort': false,";
    }

    $result .= "'iDisplayStart':parseInt(jQuery('#_page').val()),
    'iDisplayLength':parseInt(jQuery('#_len').val()),
    'sDom': 'frtlip',
    'oLanguage':{						
     'sInfo': 'showing <span>_START_</span> to <span>_END_</span> of <span>_TOTAL_</span> entries',
     'sInfoFiltered': '<span></span>',
     'sProcessing': '<img src=\"styles/images/loader.gif\" style=\"position:absolute; left:50%; top:50px;\"/>',
   },

   'fnDrawCallback': function () {				
     jQuery('#_page').val(parseInt(this.fnPagingInfo().iStart));
     jQuery('#_len').val(parseInt(this.fnPagingInfo().iLength));";

    if (!empty($get))
        $result .= "arr = jQuery('#chk').val().split('\t');					
    for(i=0; i< arr.length; i++){						
      jQuery('#id_' + arr[i] + '').prop('checked', true );
    }
    ";

    $result .= "},

    'fnServerParams': function (aoData) {											
     aoData.push({ 'name': 'aSearch', 'value': jQuery('#aSearch').val() });
     aoData.push({ 'name': 'bSearch', 'value': jQuery('#bSearch').val() });
     aoData.push({ 'name': 'cSearch', 'value': jQuery('#cSearch').val() });
     aoData.push({ 'name': 'dSearch', 'value': jQuery('#dSearch').val() });
     aoData.push({ 'name': 'eSearch', 'value': jQuery('#eSearch').val() });
     aoData.push({ 'name': 'fSearch', 'value': jQuery('#fSearch').val() });
     aoData.push({ 'name': 'gSearch', 'value': jQuery('#gSearch').val() });
     aoData.push({ 'name': 'hSearch', 'value': jQuery('#hSearch').val() });
     aoData.push({ 'name': 'iSearch', 'value': jQuery('#iSearch').val() });
     aoData.push({ 'name': 'jSearch', 'value': jQuery('#jSearch').val() });
     aoData.push({ 'name': 'kSearch', 'value': jQuery('#kSearch').val() });
     aoData.push({ 'name': 'lSearch', 'value': jQuery('#lSearch').val() });
     aoData.push({ 'name': 'mSearch', 'value': jQuery('#mSearch').val() });
     aoData.push({ 'name': 'pSearch', 'value': jQuery('#pSearch').val() });
     aoData.push({ 'name': 'sSearch', 'value': jQuery('#sSearch').val() });
     aoData.push({ 'name': 'tSearch', 'value': jQuery('#tSearch').val() });
     aoData.push({ 'name': 'zSearch', 'value': jQuery('#zSearch').val() });
     aoData.push({ 'name': 'dirSearch', 'value': jQuery('#dirSearch').val() });
   },			
 });

 jQuery('#aSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});	

jQuery('#bSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#cSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#dSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});	

jQuery('#eSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});	

jQuery('#fSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#gSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#hSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#iSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#jSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#lSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#mSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#pSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});	

jQuery('#sSearch').keyup(function(){
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});

jQuery('#tSearch').change(function(){	
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});			

jQuery('#zSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
});									

jQuery('#dirSearch').change(function(){ 
  oTable.fnPageChange(0);
  oTable.fnReloadAjax();
}); 

jQuery('#fSearch').attr('placeholder', 'search ...');
jQuery('#fSearch').attr('style', 'background: url(\"styles/images/filter.png\") no-repeat; width:200px; padding-left:30px;');
";

    $result .= "});						
</script>";
    return $result;
}

function getPar($par, $ha_nama = "")
{
    global $c, $p, $m, $s, $_p, $_l;
    if (strlen($c))
        $var .= "&c=$c";
    if (strlen($p))
        $var .= "&p=$p";
    if (strlen($m))
        $var .= "&m=$m";
    if (strlen($s))
        $var .= "&s=$s";
    if (strlen($_p))
        $var .= "&_p=$_p";
    if (strlen($_l))
        $var .= "&_l=$_l";
    if (is_array($par)) {
        while (list($nama, $nilai) = each($par)) {
            if (strpos(",$ha_nama,", "$nama,") == 0) {
                if (!empty($nilai))
                    $var .= "&par[$nama]=$nilai";
            }
        }
    }
    return $var;
}

function setPar($par, $ha_nama = "")
{
    if (is_array($par)) {
        while (list($nama, $nilai) = each($par)) {
            if (strpos(",$ha_nama,", "$nama,") == 0) {
                if (!empty($nilai))
                    $var .= "<input type=\"hidden\" id=\"par[$nama]\" name=\"par[$nama]\" value=\"$nilai\"/>";
            }
        }
    }
    return $var;
}

function valPar($par, $ha_nama = "", $length = "")
{
    global $c, $p, $m, $s, $_p, $_l, $arrParameter;
    if (strlen($c))
        $var .= "&c=$c";
    if (strlen($p))
        $var .= "&p=$p";
    if (strlen($m))
        $var .= "&m=$m";
    if (strlen($s))
        $var .= "&s=$s";
    if (is_array($par)) {
        while (list($nama, $nilai) = each($par)) {
            if (strpos(",$ha_nama,", "$nama,") == 0) {
                if (!empty($nilai))
                    $var .= "&par[$nama]=$nilai";
            }
        }
    }

    if (empty($_p))
        $_p = 0;
    if (empty($_l))
        $_l = $arrParameter[1];
    if (!empty($length))
        $_l = $length;
    echo "<input type=\"hidden\" id=\"_par\" name=\"_par\" value=\"$var\"/>
  <input type=\"hidden\" id=\"_page\" name=\"_page\" value=\"$_p\" />
  <input type=\"hidden\" id=\"_len\" name=\"_len\" value=\"$_l\" />";
}

function arrayQuery($sql)
{
    $arr_y = array();
    $res_item = db($sql);
    while ($r = mysql_fetch_row($res_item)) {
        $jumlah = count($r);
        if ($jumlah == 1) {
            $arr_y[] = $r[0];
        } elseif ($jumlah == 2) {
            $arr_y["$r[0]"] = $r[1];
        } elseif ($jumlah == 3) {
            $arr_y["$r[0]"]["$r[1]"] = $r[2];
        } elseif ($jumlah == 4) {
            $arr_y["$r[0]"]["$r[1]"]["$r[2]"] = $r[3];
        } elseif ($jumlah == 5) {
            $arr_y["$r[0]"]["$r[1]"]["$r[2]"]["$r[3]"] = $r[4];
        } elseif ($jumlah == 6) {
            $arr_y["$r[0]"]["$r[1]"]["$r[2]"]["$r[3]"]["$r[4]"] = $r[5];
        }
    }
    return $arr_y;
}

function comboArray($nama, $arr_nilai, $sel, $java = "", $width = "")
{
    $style = $width == "" ? "" : "style=\"width:$width%\"";
    $text = "<select id=\"$nama\" name=\"$nama\" $java $style>";
    ksort($arr_nilai);
    reset($arr_nilai);
    while (list($key, $nilai) = each($arr_nilai)) {
        if ($nilai == $sel) {
            $text .= "<option value='$nilai' selected>$nilai</option>";
        } else {
            $text .= "<option value='$nilai'>$nilai</option>";
        }
    }
    $text .= "</select>";
    return $text;
}

function comboKey($nama, $arr_nilai, $sel, $java = "", $width = "")
{
    $style = $width == "" ? "" : "style=\"width:$width\"";
    $text = "<select id=\"$nama\" name=\"$nama\" $java $style>";
    #ksort($arr_nilai);
    #reset($arr_nilai);
    while (list($key, $nilai) = each($arr_nilai)) {
        if ($key == $sel) {
            $text .= "<option value='$key' selected>$nilai</option>";
        } else {
            $text .= "<option value='$key'>$nilai</option>";
        }
    }
    $text .= "</select>";
    return $text;
}

function selisihHari($d1, $d2)
{
    list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
    list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
    list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

    list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
    list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
    list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

    if (empty($jamAwal)) $jamAwal = 0;
    if (empty($menitAwal)) $menitAwal = 0;
    if (empty($detikAwal)) $detikAwal = 0;

    if (empty($jamAkhir)) $jamAkhir = 0;
    if (empty($menitAkhir)) $menitAkhir = 0;
    if (empty($detikAkhir)) $detikAkhir = 0;

    $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
    $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
    return dateDiff("d", $dAwal, $dAkhir);
}


function comboYear($nama, $sel, $range = "", $java = "", $width = "", $all = "", $awal = "", $akhir = "")
{
    $style = "style=\"width:80px;\"";
    $text = "<select id=\"$nama\" name=\"$nama\" $java $style>";
    $range = $range == "" ? 5 : $range;
    if (empty($awal))
        $awal = empty($sel) ? date('Y') - $range : $sel - $range;
    if (empty($akhir))
        $akhir = empty($sel) ? date('Y') + $range : $sel + $range;
    if (!empty($all))
        $text .= empty($sel) ? "<option value=\"\" selected>All</option>" : "<option value=\"\">All</option>";
    for ($nilai = $awal; $nilai <= $akhir; $nilai++) {
        if ($nilai == $sel) {
            $text .= "<option value=\"$nilai\" selected>$nilai</option>";
        } else {
            $text .= "<option value=\"$nilai\">$nilai</option>";
        }
    }
    $text .= "</select>";
    return $text;
}

function comboMonth($nama, $sel, $java = "", $width = "", $all = "")
{
    $style = $width == "" ? "" : "style=\"width:$width%\"";
    $text = "<select id=\"$nama\" name=\"$nama\" $java $style>";
    if (!empty($all))
        $text .= empty($sel) ? "<option value=\"\" selected>All</option>" : "<option value=\"\">All</option>";
    for ($nilai = 1; $nilai <= 12; $nilai++) {
        $bulan = str_pad($nilai, 2, "0", STR_PAD_LEFT);
        if ($nilai == $sel) {
            $text .= "<option value=\"$bulan\" selected>" . getBulan($bulan) . "</option>";
        } else {
            $text .= "<option value=\"$bulan\">" . getBulan($bulan) . "</option>";
        }
    }
    $text .= "</select>";
    return $text;
}

function comboData($sql, $key, $val, $nama, $option = "All", $nilai = "", $java = "", $width = "", $class = "", $disabled = "")
{
    $width = $width == "" ? "" : "width:$width;";
    $disabled = $disabled == "" ? "" : "disabled";
    $txt = "<select id=\"$nama\" name=\"$nama\" class=\"$class\" style=\"height:32px; $width\" $java $disabled>";

    $result = db("$sql");
    $jml = mysql_num_rows($result);

    if ($option == " ") {
        $txt .= "<option value=\"\">$option</option>";
    }

    if (strlen($option) > 2) {
        $txt .= "<option value=\"\">$option</option>";
    }

    for ($i = 0; $i < $jml; $i++) {
        $r = mysql_fetch_array($result);
        if (trim($r[$key]) == trim($nilai)) {
            $txt .= "<option value=\"$r[$key]\" selected>$r[$val]</option>";
        } else {
            $txt .= "<option value=\"$r[$key]\">$r[$val]</option>";
        }
    }
    $txt .= "</select>";
    return $txt;
}

function floorDec($input, $decimals)
{
    return round($input - (5 / pow(10, $decimals + 1)), $decimals);
}

function setAngka($nilai)
{
    $nilai = $nilai == "" ? "0" : $nilai;
    $hasil = substr($nilai, 0, 1) == "." ? "0" . $nilai : $nilai;
    $hasil = str_replace(",", "", $hasil);
    return $hasil;
}

function getAngka($nilai = 0, $digit = 0)
{
    return number_format($nilai, $digit);
}

function ceilAngka($number, $significance = 1)
{
    return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
}

function namaData($kodeData)
{
    $text = getField("select namaData from mst_data where kodeData = '$kodeData'");
    return $text;
}

function namaMaster($kodedata)
{
    $text = getField("select namaData from mst_data where kodeData = '$kodedata'");
    return $text;
}

function numToAlpha($data)
{
    $data = $data - 1;
    $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $alpha_flip = array_flip($alphabet);
    if ($data <= 25) {
        return strtoupper($alphabet[$data]);
    } elseif ($data > 25) {
        $dividend = ($data + 1);
        $alpha = '';
        $modulo;
        while ($dividend > 0) {
            $modulo = ($dividend - 1) % 26;
            $alpha = $alphabet[$modulo] . $alpha;
            $dividend = floor((($dividend - $modulo) / 26));
        }
        return strtoupper($alpha);
    }
}

function setTanggal($tanggal)
{
    if (empty($tanggal))
        $hasil = "0000-00-00";
    $arr = explode("/", $tanggal);
    $hasil = $arr[2] . "-" . $arr[1] . "-" . $arr[0];
    return $hasil;
}

function getBulan($bulan, $str = "")
{
    $arr = array(
        "1" => "Januari", "2" => "Februari", "3" => "Maret", "4" => "April", "5" => "Mei", "6" => "Juni", "7" => "Juli",
        "8" => "Agustus", "9" => "September", "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli",
        "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
    );
    $sub = array(
        "1" => "Jan", "2" => "Feb", "3" => "Mar", "4" => "Apr", "5" => "May", "6" => "Jun", "7" => "Jul",
        "8" => "Aug", "9" => "Sep", "01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "May", "06" => "Jun", "07" => "Jul", "08" => "Aug", "09" => "Sep", "10" => "Oct", "11" => "Nov", "12" => "Dec"
    );
    $hasil = $str ? $sub["$bulan"] : $arr["$bulan"];
    return $hasil;
}

function getRomawi($val)
{
    $arr = array("", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
    $value = intval($val);
    $hasil = $arr[$value];
    return $hasil;
}

function getTanggal($tanggal, $format = "")
{
    $arr = explode("-", $tanggal);
    if ($format == "") {
        $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : $arr[2] . "/" . $arr[1] . "/" . $arr[0];
    } else {
        $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : "$arr[2] " . getBulan($arr[1]) . " $arr[0]";
    }
    return $hasil;
}

function getTanggal2($date, $format)
{
    $middle = strtotime($date);
    $new_date = date($format, $middle);

    return $new_date;
}

function formatDate($date)
{
    list($tahun, $bulan, $tanggal) = explode("-", $date);
    $get = $tanggal . "-" . $bulan . "-" . $tahun;

    return $get;
}

function getHistory($table, $lid, $id, $cby = "created_by", $cdate = "created_date", $uby = "updated_by", $udate = "updated_date")
{
    global $arrParam;
    $sql = db("SELECT $cby, $cdate, $uby, $udate from $table where $lid = $id");
    $r = mysql_fetch_assoc($sql);
    $text = "
  <br>
  <fieldset>
    <legend><b>HISTORY</b></legend>
    <p>
      <table width=\"100%\">
        <tr>
          <td>
            <label class=\"l-input-small\">Create By</label>
            <span class=\"field\">
              &nbsp;" . $r[$cby] . "
            </span>
          </td>
          <td>
            <label class=\"l-input-small\">Update By</label>
            <span class=\"field\">
              &nbsp;" . $r[$uby] . "
            </span>
          </td>
        </tr>
      </table>
    </p>
    <p>
      <table width=\"100%\">
        <tr>
          <td>
            <label class=\"l-input-small\">Create Date</label>
            <span class=\"field\">
              &nbsp;" . $r[$cdate] . "
            </span>
          </td>
          <td>
            <label class=\"l-input-small\">Update Date</label>
            <span class=\"field\">
              &nbsp;" . $r[$udate] . "
            </span>
          </td>
        </tr>
      </table>
    </p>
  </fieldset>
  ";

    return $text;
}

function getHari($date)
{
    $arrHari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
    $dayW = date('w', strtotime($date));

    return $arrHari[$dayW];
}

function getTgl($tanggal, $format = "")
{
    $arr = explode("-", $tanggal);
    if ($format == "") {
        $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : $arr[2] . "-" . $arr[1] . "-" . $arr[0];
    } else {
        $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : "$arr[2] " . getBulan($arr[1]) . " $arr[0]";
    }
    return $hasil;
}


function getWaktu($waktu, $format = "")
{
    $arr_ = explode(" ", $waktu);
    $tanggal = $arr_[0];
    $jam = $arr_[1];
    $arr = explode("-", $tanggal);
    if ($format == "") {
        $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : $arr[2] . "/" . $arr[1] . "/" . $arr[0];
    } else {
        $hasil = ($tanggal == "" or $tanggal == "0000-00-00") ? "" : "$arr[2] " . getBulan($arr[1]) . " $arr[0]";
    }
    return $hasil ? $hasil . " @ " . substr($jam, 0, 5) : "";
}

function columnXLS($col)
{
    $arr = array(
        1 => "A",
        2 => "B",
        3 => "C",
        4 => "D",
        5 => "E",
        6 => "F",
        7 => "G",
        8 => "H",
        9 => "I",
        10 => "J",
        11 => "K",
        12 => "L",
        13 => "M",
        14 => "N",
        15 => "O",
        16 => "P",
        17 => "Q",
        18 => "R",
        19 => "S",
        20 => "T",
        21 => "U",
        22 => "V",
        23 => "W",
        24 => "X",
        25 => "Y",
        26 => "Z",
        27 => "AA",
        28 => "AB",
        29 => "AC",
        30 => "AD",
        31 => "AE",
        32 => "AF",
        33 => "AG",
        34 => "AH",
        35 => "AI",
        36 => "AJ",
        37 => "AK",
        38 => "AL",
        39 => "AM",
        40 => "AN",
        41 => "AO",
        42 => "AP",
        43 => "AQ",
        44 => "AR",
        45 => "AS",
        46 => "AT",
        47 => "AU",
        48 => "AV",
        49 => "AW",
        50 => "AX",
        51 => "AY",
        52 => "AZ"
    );

    return $arr[$col];
}

function exportXLS($direktori = "files/export/", $namaFile, $judul, $totalField, $field = array(), $data = array(), $fieldTotal = false, $mergeFieldTotal = 1, $dataTotal = array())
{
    global $s, $arrTitle, $cNama;

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator($cNama)->setLastModifiedBy($cNama)->setTitle($arrTitle[$s]);

    $objPHPExcel->getActiveSheet()->setTitle(substr($judul, 0, 29));
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getActiveSheet()->setCellValue('A1', $judul);
    $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->mergeCells('A1:' . columnXLS($totalField) . '1');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->getStyle('A3:' . columnXLS($totalField) . '3')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $objPHPExcel->getActiveSheet()->getStyle('A4:' . columnXLS($totalField) . '4')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $objPHPExcel->getActiveSheet()->getStyle('A3:A4')->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    $col = 0;
    foreach ($field as $f1 => $f2) {
        $col++;

        if ($col == 1) $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($col))->setWidth(5);
        else $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($col))->setWidth(40);

        if (!is_array($f2)) {
            $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($col) . '3', strtoupper($f2));
            $objPHPExcel->getActiveSheet()->mergeCells(columnXLS($col) . '3:' . columnXLS($col) . '4');
        } else {
            $plus = count($f2) - 1;

            $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($col) . '3', strtoupper($f1));
            $objPHPExcel->getActiveSheet()->mergeCells(columnXLS($col) . '3:' . columnXLS($col + $plus) . '3');
            $objPHPExcel->getActiveSheet()->getStyle(columnXLS($col) . '3:' . columnXLS($col + $plus) . '3')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $noCld = $col - 1;
            foreach ($f2 as $f3) {
                $noCld++;
                $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($noCld) . '4', strtoupper($f3));
                $objPHPExcel->getActiveSheet()->getStyle(columnXLS($noCld) . '4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                $objPHPExcel->getActiveSheet()->getStyle(columnXLS($noCld) . '4')->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle(columnXLS($noCld) . '4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getColumnDimension(columnXLS($noCld))->setWidth(40);
            }

            $col = $col + $plus;
        }

        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($col) . '3:' . columnXLS($col) . '4')->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }

    $rows = 4;

    foreach ($data as $key) {
        $rows++;

        $objPHPExcel->getActiveSheet()->getStyle('A' . $rows)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $rows . ':' . columnXLS($totalField) . $rows)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle(columnXLS($totalField) . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $no = 0;
        foreach ($key as $val) {
            $dt = explode("\t", $val);

            $val = $dt[0];
            $align = str_replace(" ", "", $dt[1]);

            $no++;
            $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($no) . $rows, $val);

            if ($align == "left") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            if ($align == "center") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            if ($align == "right") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $objPHPExcel->getActiveSheet()->getStyle(columnXLS($no) . $rows)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        }
    }

    if ($fieldTotal == true) {
        $rowsTotal = $rows + 1;

        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowsTotal)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $rowsTotal . ':' . columnXLS($totalField) . $rowsTotal)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowsTotal . ':' . columnXLS($mergeFieldTotal) . $rowsTotal);

        $noTotal = 0;
        foreach ($dataTotal as $dtt) {
            $ddd = explode("\t", $dtt);

            $dtt = $ddd[0];
            $alg = str_replace(" ", "", $ddd[1]);

            $noTotal++;

            if ($noTotal == 1) {
                $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValue("A" . $rowsTotal, $dtt);
                $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                if ($alg == "left") $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                if ($alg == "center") $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if ($alg == "right") $objPHPExcel->getActiveSheet()->getStyle("A" . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            } else {
                $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValue(columnXLS($mergeFieldTotal) . $rowsTotal, $dtt);
                $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

                if ($alg == "left") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                if ($alg == "center") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                if ($alg == "right") $objPHPExcel->getActiveSheet()->getStyle(columnXLS($mergeFieldTotal) . $rowsTotal)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }

            $mergeFieldTotal++;
        }
    }


    $objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(90);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.325);
    $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.325);

    // Save Excel file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save($direktori . $namaFile);
}

function dtaPelatihan($titlePelatihan = "PELATIHAN")
{
    global $par;

    $sql = "select * from plt_pelatihan where idPelatihan='$par[idPelatihan]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);


    $value = getField("SELECT SUM(nilaiRab) FROM plt_pelatihan_rab WHERE idPelatihan = '$par[idPelatihan]'");
    $value += getField("SELECT SUM(u_saku + u_inap + u_makan + u_cuci + u_jalan + u_tiket) FROM budget_pelatihan WHERE `id_pelatihan_perencanaan` = '$par[idPelatihan]'");

    $totalBiaya = $value;

    $pelaksanaanPelatihan = $r[pelaksanaanPelatihan] == "e" ? "Eksternal" : "Internal";

    $text = "
  <div style=\"position:absolute; top:0; right:0; margin-top:125px; z-index:9; margin-right:40px;\">
    <input id=\"bView\" type=\"button\" value=\"+ View\" class=\"btn btn_search btn-small\" onclick=\"document.getElementById('bView').style.display = 'none'; document.getElementById('bHide').style.display = 'block'; document.getElementById('dView').style.display = 'block';\" />
    <input id=\"bHide\" type=\"button\" value=\"- Hide\" class=\"btn btn_search btn-small\" style=\"display:none\" onclick=\"document.getElementById('bView').style.display = 'block'; document.getElementById('bHide').style.display = 'none'; document.getElementById('dView').style.display = 'none'; \" />
</div>
<fieldset id=\"fSet\" style=\"padding:10px; border-radius: 10px;\">
  <legend style=\"padding:10px; margin-left:20px;\"><h4>" . $titlePelatihan . "</h4></legend>
  <p>
    <label class=\"l-input-small\" style=\"width:150px;\">Pelatihan</label>
    <span class=\"field\" style=\"margin-left:150px;\"><a href='#' onclick=\"openBox('popup.php?par[mode]=detail2&par[idPelatihan]=$r[idPelatihan]" . getPar($par, "mode, idPelatihan") . "',940,550)\" style='color:blue;'>$r[judulPelatihan]</a>&nbsp;</span>
  </p>
  <table style=\"width:100%; margin-top:-5px; margin-bottom:-5px;\" cellpadding=\"0\" cellspacing=\"0\">
   <tr>
     <td style=\"width:50%\">
      <p>
       <label class=\"l-input-small\" style=\"width:150px;\">Sub</label>
       <span class=\"field\" style=\"margin-left:150px;\">$r[subPelatihan]&nbsp;</span>
     </p>
   </td>
   <td style=\"width:50%\">
    <p>
     <label class=\"l-input-small\" style=\"width:150px;\">Kode</label>
     <span class=\"field\" style=\"margin-left:150px;\">$r[kodePelatihan]&nbsp;</span>
   </p>
 </td>
</tr>
</table>
<div id=\"dView\" style=\"display:none;\">
 <p>
  <label class=\"l-input-small\" style=\"width:150px;\">Jumlah Peserta</label>
  <span class=\"field\" style=\"margin-left:150px;\">" . getAngka($r[pesertaPelatihan]) . " Orang&nbsp;</span>
</p>
<p>
  <label class=\"l-input-small\" style=\"width:150px;\">Pelaksanaan</label>
  <span class=\"field\" style=\"margin-left:150px;\">" . $pelaksanaanPelatihan . "&nbsp;</span>
</p>";
    if ($r[pelaksanaanPelatihan] == "e")
        $text .= "<p>
<label class=\"l-input-small\" style=\"width:150px;\">Vendor</label>
<span class=\"field\" style=\"margin-left:150px;\">" . getField("select namaVendor from dta_vendor where kodeVendor='" . $r[idVendor] . "'") . "&nbsp;</span>
</p>";
    $text .= "<p>
<label class=\"l-input-small\" style=\"width:150px;\">Lokasi</label>
<span class=\"field\" style=\"margin-left:150px;\">$r[lokasiPelatihan]&nbsp;</span>
</p>
<p>
  <label class=\"l-input-small\" style=\"width:150px;\">Biaya</label>
  <span class=\"field\" style=\"margin-left:150px;\">Rp. " . getAngka($totalBiaya) . "&nbsp;</span>
</p>";
    if ($r[pelaksanaanPelatihan] == "e")
        $text .= "<p>
<label class=\"l-input-small\" style=\"width:150px;\">Trainer</label>
<span class=\"field\" style=\"margin-left:150px;\">" . getField("select namaTrainer from dta_trainer where idTrainer='" . $r[idTrainer] . "'") . "&nbsp;</span>
</p>";
    else
        $text .= "<p>
<label class=\"l-input-small\" style=\"width:150px;\">Penanggung Jawab</label>
<span class=\"field\" style=\"margin-left:150px;\">" . getField("select name from emp where id='" . $r[idPegawai] . "'") . "&nbsp;</span>
</p>";
    $text .= "</div>
</fieldset>
<br clear=\"all\">";
    return $text;
}

function dateDiff($per, $d1, $d2)
{
    $d = $d2 - $d1;
    switch ($per) {
        case "yyyy":
            $d /= 12;
        case "m":
            $d *= 12 * 7 / 365.25;
        case "ww":
            $d /= 7;
        case "d":
            $d /= 24;
        case "h":
            $d /= 60;
        case "n":
            $d /= 60;
    }
    return round($d) > 0 ? round($d) : round($d) * -1;
}

function dateAdd($per, $n, $d)
{
    switch ($per) {
        case "yyyy":
            $n *= 12;
        case "m":
            $d = mktime(
                date("H", $d),
                date("i", $d),
                date("s", $d),
                date("n", $d) + $n,
                date("j", $d),
                date("Y", $d)
            );
            $n = 0;
            break;
        case "ww":
            $n *= 7;
        case "d":
            $n *= 24;
        case "h":
            $n *= 60;
        case "n":
            $n *= 60;
    }
    return $d + $n;
}

function dateMin($per, $n, $d)
{
    switch ($per) {
        case "yyyy":
            $n *= 12;
        case "m":
            $d = mktime(
                date("H", $d),
                date("i", $d),
                date("s", $d),
                date("n", $d) - $n,
                date("j", $d),
                date("Y", $d)
            );
            $n = 0;
            break;
        case "ww":
            $n *= 7;
        case "d":
            $n *= 24;
        case "h":
            $n *= 60;
        case "n":
            $n *= 60;
    }
    return $d - $n;
}

function sumTime($times)
{

    // loop throught all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    $result = sprintf('%02d:%02d', $hours, $minutes);
    if ($result == "00:00") $result = "";
    return $result;
}

function selisihTahun($d1, $d2)
{
    list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
    list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
    list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

    list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
    list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
    list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

    if (empty($jamAwal)) $jamAwal = 0;
    if (empty($menitAwal)) $menitAwal = 0;
    if (empty($detikAwal)) $detikAwal = 0;

    if (empty($jamAkhir)) $jamAkhir = 0;
    if (empty($menitAkhir)) $menitAkhir = 0;
    if (empty($detikAkhir)) $detikAkhir = 0;

    $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
    $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
    return dateDiff("yyyy", $dAwal, $dAkhir);
}

function selisihBulan($d1, $d2)
{
    list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
    list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
    list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

    list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
    list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
    list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

    if (empty($jamAwal)) $jamAwal = 0;
    if (empty($menitAwal)) $menitAwal = 0;
    if (empty($detikAwal)) $detikAwal = 0;

    if (empty($jamAkhir)) $jamAkhir = 0;
    if (empty($menitAkhir)) $menitAkhir = 0;
    if (empty($detikAkhir)) $detikAkhir = 0;

    $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
    $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
    return dateDiff("m", $dAwal, $dAkhir);
}

function selisihJam($d1, $d2)
{
    list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
    list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
    list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

    list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
    list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
    list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

    $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
    $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
    return dateDiff("h", $dAwal, $dAkhir);
}

function selisihMenit($d1, $d2)
{
    list($tanggalAwal, $waktuAwal) = explode(" ", $d1);
    list($tahunAwal, $bulanAwal, $hariAwal) = explode("-", $tanggalAwal);
    list($jamAwal, $menitAwal, $detikAwal) = explode(":", $waktuAwal);

    list($tanggalAkhir, $waktuAkhir) = explode(" ", $d2);
    list($tahunAkhir, $bulanAkhir, $hariAkhir) = explode("-", $tanggalAkhir);
    list($jamAkhir, $menitAkhir, $detikAkhir) = explode(":", $waktuAkhir);

    $dAwal = mktime($jamAwal, $menitAwal, $detikAwal, $bulanAwal, $hariAwal, $tahunAwal);
    $dAkhir = mktime($jamAkhir, $menitAkhir, $detikAkhir, $bulanAkhir, $hariAkhir, $tahunAkhir);
    return dateDiff("n", $dAwal, $dAkhir);
}

function selisihMenit2($d1, $d2)
{

    list($jamAwal, $menitAwal) = explode(":", $d1);


    list($jamAkhir, $menitAkhir) = explode(":", $d2);

    $dAwal = mktime($jamAwal, $menitAwal);
    $dAkhir = mktime($jamAkhir, $menitAkhir);
    return dateDiff("n", $dAwal, $dAkhir);
}

function uploadFiles($id, $name, $folder_target, $format)
{
    $fileUpload = $_FILES["$name"]["tmp_name"];
    $fileUpload_name = $_FILES["$name"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $folder_target);
        $file = $format . $id . "." . getExtension($fileUpload_name);
        fileRename($folder_target, $fileUpload_name, $file);
    }
    return $file;
}

function fileUpload($userfile, $userfile_name, $dir)
{
    if ($userfile != "") {
        if (!is_dir("$dir/")) {
            mkdir("$dir", 0755);
        }

        if (!copy($userfile, "$dir/$userfile_name")) {
            echo "error tuh";
        }
    }
}

function getExtension($str)
{
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }

    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

function getIcon($file, $folder = "")
{
    $ext = getExtension($file);
    $file = "styles/images/extensions/" . $ext . ".png";
    $icon = is_file($folder . $file) ? $file : "styles/images/extensions/file.png";
    return $icon;
}

function fileRename($folder, $oldfile, $newfile)
{
    if (!rename($folder . $oldfile, $folder . $newfile)) {
        if (copy($folder . $oldfile, $folder . $newfile)) {
            unlink($folder . $oldfile);
        }
    }
}

function fileMove($nfile, $ofolder, $nfolder)
{
    if (!is_dir("$nfolder/")) {
        mkdir("$nfolder", 0755);
    }
    if (!rename($ofolder . $nfile, $nfolder . $nfile)) {
        if (copy($ofolder . $nfile, $nfolder . $nfile)) {
            unlink($ofolder . $nfile);
        }
    }
}

function arraySuffle($list)
{
    if (!is_array($list))
        return $list;

    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key)
        $random = $list[$key];

    return $random;
}

function terbilang($x)
{
    $t = explode(".", $x);
    return $t[1] > 0 ? numToString($t[0]) . " Koma" . numToString($t[1]) : numToString($t[0]);
}

function numToString($x)
{
    $abil = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    if ($x < 12)
        return " " . $abil[$x];
    elseif ($x < 20)
        return numToString($x - 10) . " Belas";
    elseif ($x < 100)
        return numToString($x / 10) . " Puluh" . numToString($x % 10);
    elseif ($x < 200)
        return " Seratus" . numToString($x - 100);
    elseif ($x < 1000)
        return numToString($x / 100) . " Ratus" . numToString($x % 100);
    elseif ($x < 2000)
        return " Seribu" . numToString($x - 1000);
    elseif ($x < 1000000)
        return numToString($x / 1000) . " Ribu" . numToString($x % 1000);
    elseif ($x < 1000000000)
        return numToString($x / 1000000) . " Juta" . numToString($x % 1000000);
}

function formatKode($kodeParameter, $fieldNama, $tableNama, $tanggalDefault = "")
{
    global $inp, $par;

    list($tanggal, $bulan, $tahun) = explode("/", $tanggalDefault);
    if (empty($bulan))
        $bulan = date('m');
    if (empty($tahun))
        $tahun = date('Y');

    $param = getField("select nilaiParameter from app_parameter where kodeParameter='$kodeParameter'");
    $param = str_replace("YYYY", $tahun, $param);
    $param = str_replace("YY", substr($tahun, 2, 2), $param);
    $param = str_replace("MM", $bulan, $param);


    if (substr($param, 0, 1) != "C" && substr($param, -1) != "C") {
        $count = "C";
        $no = 1;
    }

    $arr = explode("C", $param);
    if (is_array($arr)) {
        while (list($k, $cnt) = each($arr)) {
            if (empty($cnt)) {
                $count .= "C";
                $no++;
            }
        }
    }

    list($prefix, $sufix) = explode($count, $param);

    if (is_array($fieldNama) && is_array($tableNama)) {
        $sql = "select replace(replace(fldNama, '" . $sufix . "', ''), '" . $prefix . "', '') from (";
        while (list($k, $tblNama) = each($tableNama)) {
            if ($k > 0)
                $sql .= " union ";
            $sql .= "select " . $fieldNama[$k] . " as fldNama from " . $tblNama . " where " . $fieldNama[$k] . " like '" . str_replace($count, "%", $param) . "'";
        }
        $sql .= ") as t order by 1 desc limit 1";
        $counter = getField($sql);
    } else {
        $counter = getField("select replace(replace(" . $fieldNama . ", '" . $sufix . "', ''), '" . $prefix . "', '') from " . $tableNama . " where " . $fieldNama . " like '" . str_replace($count, "%", $param) . "' order by 1 desc limit 1");
    }

    $counter = str_pad($counter + 1, $no, "0", STR_PAD_LEFT);

    $param = str_replace($count, $counter, $param);
    return $param;
}

function encodePass($password)
{
    $password = mysql_real_escape_string($password);
    $set = "UzFuM3JHMV9DNV9EM1ZsMHAzUg==";
    $def = "8eb98b33c777a27ab57a35ee1dc3a389";

    return md5($def . $set . md5($password) . $set . $def . $set . $def);
}

function debugVar($arr)
{
    if (is_array($arr)) {
        echo '<pre>';
        print_r($arr);
        echo '<pre>';
    } else {
        if (!empty($arr)) {
            echo $arr;
        } else {
            echo "<strong>-- Empty Variable --</strong>";
        }

    }

}

function queryAssoc($sql)
{
    $getResult = mysql_query($sql);

    if (!$getResult) {
        echo $sql;
        echo "<br />";
        echo "This query is fail to connect database!";
        echo "<br />";
        die;
    } else {
        if (!empty($getResult)) {
            while ($result = mysql_fetch_assoc($getResult)) {
                $arr[] = $result;
            }
            return $arr;
        }
    }
}

function dateNow()
{
    return date('Y-m-d H:i:s');
}

function getDetailCompetency($idAspek, $par)
{
    $text .= "
  <table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" class=\"stdtable stdtablequick dataTable\">
    <thead>
      <tr>
        <th style=\"vertical-align:middle;\">" . getField("select namaAspek from pen_setting_aspek where idAspek=$idAspek and idPeriode = $par[idPeriode]") . "</th>
        <th style=\"vertical-align:middle;\">komponen KPI</th>
        <th style=\"vertical-align:middle;\" rowspan=\"3\">Bobot</th>
        <th style=\"vertical-align:middle;\" colspan=\"5\">Rating</th>
        <th style=\"vertical-align:middle;\" rowspan=\"3\">Bobot</th>
        <th style=\"vertical-align:middle;\" rowspan=\"3\">nilai terbobot (%)</th>
        <th style=\"vertical-align:middle;\" rowspan=\"3\">nilai terbobot</th>
        <tr>
          <tr>
            <th style=\"vertical-align:middle;\" rowspan=\"3\" colspan=\"2\">sasaran Kinerja KPI</th>
            <th style=\"vertical-align:middle;\">1</th>
            <th style=\"vertical-align:middle;\">2</th>
            <th style=\"vertical-align:middle;\">3</th>
            <th style=\"vertical-align:middle;\">4</th>
            <th style=\"vertical-align:middle;\">5</th>
            <tr>
              <tr>
                <th style=\"vertical-align:middle;\">100%</th>
                <th style=\"vertical-align:middle;\" colspan=\"8\">100%</th>
                <tr>
                </thead>
                <tbody>
                  ";
    $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[kodePenilaian]");
    $getPerspektif = queryAssoc("select * from pen_setting_prespektif where idKode = $par[kodePenilaian] and idAspek = $idAspek and idTipe = $idTipe  and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");
    if ($getPerspektif) {
        foreach ($getPerspektif as $pers) {
            $getObj = queryAssoc("select 
                                                * 
                        from pen_sasaran_obyektif 
                        where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");

            if (!empty($getObj)) {
                $text .= "
                        <tr>
                          <td style=\"vertical-align:middle;\" rowspan=\"" . (count($getObj) + 1) . "\"><strong>$pers[namaPrespektif]</strong></td>
                        </tr>
                        ";
            }

            foreach ($getObj as $obj) {
                $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $par[bulanPenilaian] and id_sasaran = $obj[idSasaran]");
                $rea = $realisasi[0];
                $nilai = empty($rea) ? 0 : $rea[nilai];

                $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
                $sas = $getSasaran[0];

                $nilai1 = $sas[bobotIndividu] . ",0";
                $nilai2 = $sas[bobotIndividu] * $nilai;
                $nilai3 = $nilai2 / 100;

                $text .= "
                        <tr>
                          <td style=\"vertical-align:middle;\" >$obj[uraianSasaran]</td>
                          <td align=\"center\">$sas[bobotIndividu]%</td>
                          <td align=\"center\">" . ($nilai == 1 ? "<strong>x</strong>" : "") . "</td>
                          <td align=\"center\">" . ($nilai == 2 ? "<strong>x</strong>" : "") . "</td>
                          <td align=\"center\">" . ($nilai == 3 ? "<strong>x</strong>" : "") . "</td>
                          <td align=\"center\">" . ($nilai == 4 ? "<strong>x</strong>" : "") . "</td>
                          <td align=\"center\">" . ($nilai == 5 ? "<strong>x</strong>" : "") . "</td>
                          <td align=\"center\">$nilai1</td>
                          <td align=\"center\">$nilai2.0</td>
                          <td align=\"center\">$nilai3</td>
                        </tr>
                        ";

                $t_bobot_1[$aspek[idAspek]] = $t_bobot_1[$aspek[idAspek]] + $sas[bobotIndividu];
                $t_bobot_3[$aspek[idAspek]][] = $nilai3;
            }


        }

        $text .= "
                    <tr>
                      <td align=\"center\" colspan=\"2\">&nbsp;</td>
                      <td align=\"center\" ><strong>" . $t_bobot_1[$aspek[idAspek]] . "%</strong></td>
                      <td align=\"center\" colspan=\"5\">&nbsp;</td>
                      <td align=\"center\"><strong>" . $t_bobot_1[$aspek[idAspek]] . "%</strong></td>
                      <td align=\"center\"></td>
                      <td align=\"center\"><strong>" . array_sum($t_bobot_3[$aspek[idAspek]]) . "</strong></td>
                    </tr>
                    ";

        $yudisium = $yudisium + array_sum($t_bobot_3[$aspek[idAspek]]);
    }
    $text .= "
                </tbody>
              </table>

              <br />
              ";
    return $text;
}

function getIdSasaran($idAspek, $idKode)
{
    $get = queryAssoc("SELECT c.idSasaran FROM pen_setting_aspek AS a
                JOIN pen_setting_prespektif AS b ON (b.idAspek = a.idAspek)
                JOIN pen_sasaran_obyektif AS c ON (c.idPrespektif = b.idPrespektif)
                WHERE a.idAspek = $idAspek AND b.idKode = $idKode");
    foreach ($get as $bbb) {
        $arrIdSasaran[] = $bbb[idSasaran];
    }
    $idSasaran = implode(",", $arrIdSasaran);
    return $idSasaran;
}

function getNilai($idAspek, $par, $bulanPenilaian)
{
    $idTipe = getField("select kodeTipe from pen_setting_kode where idKode ='$par[kodePenilaian]'");
    $getPerspektif = queryAssoc("select * from pen_setting_prespektif where idKode = '$par[kodePenilaian]' and idAspek = '$idAspek' and idTipe = '$idTipe' and idPrespektif in (select idPrespektif from pen_setting_prespektif_indikator) order by urut asc");


    if ($getPerspektif) {
        foreach ($getPerspektif as $pers) {
            $getObj = queryAssoc("select 
                                        * 
                    from pen_sasaran_obyektif 
                    where idPrespektif=$pers[idPrespektif] and idPeriode = $par[tahunPenilaian] order by idSasaran asc");
            if ($getObj) {
                foreach ($getObj as $obj) {
                    $realisasi = queryAssoc("select * from pen_realisasi_individu where id_pegawai=$par[idPegawai] and id_tahun = $par[tahunPenilaian] and id_bulan = $bulanPenilaian and id_sasaran = $obj[idSasaran]");
                    $rea = $realisasi[0];
                    $nilai = empty($rea) ? 0 : $rea[nilai];

                    $getSasaran = queryAssoc("select * from pen_sasaran_individu where idSasaran = $obj[idSasaran] and idPegawai = $par[idPegawai]");
                    $sas = $getSasaran[0];

                    $nilai1 = $sas[bobotIndividu];
                    $nilai2 = $sas[bobotIndividu] * $nilai;
                    $nilai3 = $nilai2 / 100;

                    $t_bobot_1[$aspek[idAspek]] = $t_bobot_1[$aspek[idAspek]] + $sas[bobotIndividu];
                    $t_bobot_3[$aspek[idAspek]][] = $nilai3;
                }
            }
        }
        $yudisium = $yudisium + array_sum($t_bobot_3[$aspek[idAspek]]);
    }
    return $yudisium;
}

function getWom($nilai, $tahun)
{
    $getKonversi = queryAssoc("select * from pen_setting_konversi where idPeriode=$tahun AND $nilai BETWEEN nilaiMin AND nilaiMax");
    if (!empty($getKonversi)) {
        $konv = $getKonversi[0];
        $cek = queryAssoc("SELECT nilaiMax, warnaKonversi, uraianKonversi FROM pen_setting_konversi WHERE idPeriode=$tahun LIMIT 1");
        $data[warna] = ($nilai >= $cek[0][nilaiMax]) ? $cek[0][warnaKonversi] : $konv[warnaKonversi];
        $data[uraian] = ($nilai >= $cek[0][nilaiMax]) ? $cek[0][uraianKonversi] : $konv[uraianKonversi];
    } else {
        if ($nilai >= 5) {
            $data[warna] = "#46AB46";
            $data[uraian] = "A";
        } else {
            $data[warna] = "#FF0000";
            $data[uraian] = "E";
        }

    }

    return $data;
}


function getKPIKonversiNilai($id, $persentase): int
{

    $obyektif = KPIMasterObyektif::find($id);

    foreach ($obyektif->rating->detil as $item) {

        if ($persentase >= $item->minimal && $persentase <= $item->maksimal) {
            return $item->nilai;
        }

    }

    return 0;
}

function getKPIKonversiWarna($id, $persentase): string
{

    $obyektif = KPIMasterObyektif::find($id);

    foreach ($obyektif->rating->detil as $item) {

        if ($persentase >= $item->minimal && $persentase <= $item->maksimal) {
            return $item->warna;
        }

    }

    return "#000";
}


function getWOMWithColor($period_id, $value): array
{

    $ratings = KPIMasterNilaiHasil::where('idPeriode', $period_id)->get();

    foreach ($ratings as $rating) {

        if ($value >= $rating->nilaiMin && $value <= $rating->nilaiMax) {

            return [
                'tag' => $rating->uraianKonversi,
                'color' => $rating->warnaKonversi,
                'mention' => $rating->penjelasanKonversi
            ];
        }

    }

    return [
        'tag' => 'X',
        'color' => '#000',
        'mention' => '-'
    ];
}