<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

switch ($par['mode']) {

    case "datas":
        datas();
        break;

    case "detail":
        detail();
        break;

    default:
        index();
        break;
}

function index()
{

    global $s, $arrTitle, $par;

    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
        <?= getBread() ?>
    </div>

    <div id="contentwrapper" class="contentwrapper">

        <br clear="all"/>

        <div id="calendar"></div>

    </div>

    <script src="scripts/calendar.js"></script>
    <script>
        jQuery(function () {

            jQuery('#calendar').fullCalendar({
                year: <?= date('Y') ?>,
                month: <?= date('m') - 1 ?>,
                date: <?= date('d') ?>,
                header: {
                    left: 'month',
                    center: 'title',
                    right: 'prev, next'
                },
                buttonText: {
                    prev: '&laquo;',
                    next: '&raquo;',
                    prevYear: '&nbsp;&lt;&lt;&nbsp;',
                    nextYear: '&nbsp;&gt;&gt;&nbsp;',
                    today: 'today',
                    month: 'month',
                    week: 'week',
                    day: 'day'
                },
                events: {
                    url: 'ajax.php?<?= getPar($par, "mode") . "&par[mode]=datas" ?>',
                    cache: true
                },
                eventClick: function (data) {
                    openBox('popup.php?<?= getPar($par, "mode, id") ?>&par[mode]=detail&par[id]=' + data.id, '800', '550')
                }
            })

        })
    </script>
    <?php
}

function datas()
{

    global $par, $start, $end;

    $start = date('Y-m-d', $start);
    $end = date('Y-m-d', $end);

    $datas = [];
    $res = db("SELECT * FROM `plt_pelatihan` WHERE `mulaiPelatihan` BETWEEN '$start' AND '$end'");

    while ($row = mysql_fetch_assoc($res)) {

        $datas[] = [
            'id' => $row['idPelatihan'],
            'title' => "$row[judulPelatihan]",
            'start' => "$row[mulaiPelatihan]",
            'end' => "$row[selesaiPelatihan]"
        ];

    }

    echo json_encode($datas);
}

function detail()
{

    global $s, $arrTitle, $par;

    $res = db("SELECT * FROM `plt_pelatihan` WHERE `idPelatihan` = '$par[id]'");
    $row = mysql_fetch_assoc($res);

    ?>
    <div class="pageheader">
        <h1 class="pagetitle">DETIL</h1>
    </div>

    <br>

    <div class="contentwrapper">

        <form id="form" action="" method="post" class="stdform">

            <fieldset style="padding: 1rem;">

                <legend>&emsp;PELATIHAN&emsp;</legend>

                <p>
                    <label class="l-input-small">Judul Pelatihan</label>
                    <span class="field">
					&nbsp;<?= $row['judulPelatihan'] ?>
					</span>
                </p>

                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%;">
                            <p>
                                <label class="l-input-small2">Tanggal Mulai</label>
                                <span class="field">
								&nbsp;<?= getTanggal($row['mulaiPelatihan']) ?>
								</span>
                            </p>
                        </td>
                        <td style="width: 50%;">
                            <p>
                                <label class="l-input-small2">Tanggal Selesai</label>
                                <span class="field">
								&nbsp;<?= getTanggal($row['selesaiPelatihan']) ?>
								</span>
                            </p>
                        </td>
                    </tr>
                </table>

                <table style='width:100%;'>
                    <tr>
                        <td style='width:50%;'>
                            <p>
                                <label class="l-input-small2">Sub</label>
                                <span class="field">
								&nbsp;<?= $row['subPelatihan'] ?>
								</span>
                            </p>
                        </td>
                        <td style='width:50%;'>
                            <p>
                                <label class="l-input-small2">Kode</label>
                                <span class="field">
								&nbsp;<?= $row['kodePelatihan'] ?>
								</span>
                            </p>
                        </td>
                    </tr>
                </table>

                <p>
                    <label class="l-input-small">Kategori</label>
                    <span class="field">
                    &nbsp;<?= namaData($row['idTraining']) ?>
                    </span>
                </p>

                <p>
                    <label class="l-input-small">Training</label>
                    <span class="field">
                    &nbsp;<?= namaData($row['idKategori']) ?>
                    </span>
                </p>

                <p>
                    <label class="l-input-small">Level</label>
                    <span class="field">
                    &nbsp;<?= namaData($row['idDepartemen']) ?>
                    </span>
                </p>

                <p>
                    <label class="l-input-small">Jumlah Peserta</label>
                    <span class="field">
					&nbsp;<?= getAngka($row['pesertaPelatihan']) ?>
					</span>
                </p>

                <p>
                    <label class="l-input-small">Pelaksanaan</label>
                    <span class="field">
					&nbsp;<?= $row['pelaksanaanPelatihan'] == 'e' ? 'Eksternal' : 'Internal' ?>
					</span>
                </p>

                <p>
                    <label class="l-input-small">Vendor</label>
                    <span class="field">
					&nbsp;<?= getField("SELECT namaVendor FROM `dta_vendor` WHERE `kodeVendor` = '$row[idVendor]'") ?>
					</span>
                </p>

                <p>
                    <label class="l-input-small">Koordinator</label>
                    <span class="field">
					&nbsp;<?= getField("SELECT UPPER(`namaTrainer`) AS `namaTrainer` FROM dta_trainer WHERE `idTrainer` = '$row[idTrainer]'") ?>
					</span>
                </p>

                <p>
                    <label class="l-input-small">Lokasi</label>
                    <span class="field">
					&nbsp;<?= $row['lokasiPelatihan'] ?>
					</span>
                </p>

            </fieldset>

        </form>

    </div>

<?php
}