<?php
session_start();
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$_SESSION["curr_emp_id"] = empty($_SESSION["curr_emp_id"]) ? $cUser : $_SESSION["curr_emp_id"];

function ubah()
{
    global $inp, $cUser;

    $cekID = getField("select id from emp_char where parent_id = '" . $_SESSION['curr_emp_id'] . "'");
    if ($cekID) {
        $sql = "update emp_char set characteristic = '$inp[characteristic]', hobby = '$inp[hobby]', abilities = '$inp[abilities]', organization = '$inp[organization]', update_by = '$cUser', update_date = '" . date('Y-m-d H:i:s') . "' where id = '$cekID'";
        db($sql);
    } else {
        $id = getLastId("emp_bank", "id");
        $sql = "insert into emp_char set id = '$id', parent_id = '" . $_SESSION['curr_emp_id'] . "', characteristic = '$inp[characteristic]', hobby = '$inp[hobby]', abilities = '$inp[abilities]', organization = '$inp[organization]', create_by = '$cUser', create_date = '" . date('Y-m-d H:i:s') . "'";
        db($sql);
    }

    echo "<script>alert('DATA BERHASIL DISIMPAN');reloadPage();</script>";
}

function form()
{
    global $s, $par, $arrTitle, $ui;
    ?>
    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <br clear="all" />
        <?php include './tmpl/emp_header_basic.php';
            $sql = "SELECT * from emp_char where parent_id ='$_SESSION[curr_emp_id]'";
            $res = db($sql);
            $r = mysql_fetch_assoc($res);
            ?>
        <form method="post" class="stdform" action="?_submit=1<?= getPar($par) ?>">
            <p class="btnSave">
                <input type="submit" class="submit radius2" name="btnSimpan" value="Simpan" />
            </p>
            <br clear="all" />
            <table style="width:100%">
                <tr>
                    <td style="width:50%">
                        <p><?= $ui->createTextArea("Karakter Pribadi", "inp[characteristic]", $r[characteristic], "", "t") ?></p>
                        <p><?= $ui->createTextArea("Keahlian Khusus", "inp[abilities]", $r[abilities], "", "t") ?></p>
                    </td>
                    <td style="width:50%">
                        <p><?= $ui->createTextArea("Hobi", "inp[hobby]", $r[hobby], "", "t") ?></p>
                        <p><?= $ui->createTextArea("Organisasi Sosial", "inp[organization]", $r[organization], "", "t") ?></p>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}

function getContent($par)
{
    global $_submit;
    switch ($par[mode]) {
        default:
            $text = empty($_submit) ? form() : ubah();
            break;
    }
    return $text;
}
?>