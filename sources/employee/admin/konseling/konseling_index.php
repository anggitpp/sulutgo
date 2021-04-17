<?php

if (!isset($menuAccess[$s]['view'])) {
    echo '<script type="text/javascript">logout();</script>';
}

switch ($par[mode]) {
    case 'add':
    if (isset($menuAccess[$s]['add'])) {
        addNewRow();
    } else {
        include 'konseling_list.php';
    }
    break;

    case 'edit':
    if (isset($menuAccess[$s]['edit'])) {
        include 'konseling_edit.php';
    } else {
        include 'konseling_list.php';
    }
    break;

    case "del":
    if(isset($menuAccess[$s]['delete']))
      hapusData();
    else
      include "konseling_list.php";
    break;

    case 'det':
    include 'konseling_det.php';
    break;

    case 'getEmpList':
    getEmpList();
    break;

    case 'getUserList':
    getUserList();
    break;

    default:
    clearEmptyRow();
    include 'konseling_list.php';
    break;
}

function clearEmptyRow()
{
    global $cUsername;
    db("DELETE FROM sdm_konseling WHERE idPegawai IS NULL OR idPegawai = 0 AND createBy = '$cUsername'");
}

function hapusData()
{
    global $par, $cUsername;
    db("DELETE FROM sdm_konseling WHERE idKonseling = '$par[idKonseling]'");

    echo "
    <script>
      alert('HAPUS DATA BERHASIL');
      window.location = '?".getPar($par, "mode,idKonseling")."';
    </script>
    ";
}

function addNewRow()
{
    global $cUsername;

    $nextId = getField('SELECT idKonseling FROM sdm_konseling ORDER BY idKonseling DESC LIMIT 1') + 1;
    $sql = "INSERT INTO sdm_konseling (idKonseling, createBy, createDate) VALUES ('$nextId', '$cUsername', '".date('Y-m-d H:i:s')."');";
    db($sql);

    echo "
	<script type=\"text/javascript\">
	window.location = '?par[mode]=edit&par[idKonseling]=$nextId".getPar()."';
	</script>
	";
}

function getEmpList()
{
    header('Content-type: application/json');
    $searchVal = $_GET['query'];
    $searchVal = strtoupper($searchVal);
    $filter = "WHERE id is not null AND (UPPER(name) LIKE '$searchVal%')";
    $sql = "
	SELECT name, CONCAT(id, 'tabsplit\t', pos_name) id
	FROM dta_pegawai
	$filter
	ORDER BY name";
    $res = db($sql);
    $ret = array();
    while ($r = mysql_fetch_array($res)) {
        $ret[] = array('value' => $r[name], 'data' => $r[id]);
    }
    echo json_encode(array('query' => 'Unit', 'suggestions' => $ret));
}

function getUserList()
{
    header('Content-type: application/json');
    $searchVal = $_GET['query'];
    $searchVal = strtoupper($searchVal);
    $filter = "WHERE username is not null AND (UPPER(username) LIKE '$searchVal%' OR UPPER(namaUser) LIKE '$searchVal%')";
    $sql = "
	SELECT username, namaUser
	FROM app_user
	$filter
	ORDER BY namaUser";
    $res = db($sql);
    $ret = array();
    while ($r = mysql_fetch_array($res)) {
        $ret[] = array('value' => $r[namaUser], 'data' => $r[username]);
    }
    echo json_encode(array('query' => 'Unit', 'suggestions' => $ret));
}
