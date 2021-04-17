<?php
global $s, $par, $arrTitle;

if (!isset($par[kodeTipe])) $par[kodeTipe] = getField("SELECT kodeTipe FROM pen_tipe WHERE statusTipe='t' LIMIT 1");
if (!isset($par[idKode])) $par[idKode] = getField("SELECT idKode FROM pen_setting_kode WHERE kodeTipe = $par[kodeTipe] and statusKode='t' LIMIT 1");
if (!isset($par[idPeriode])) $par[idPeriode] = getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData DESC LIMIT 1");

$sql = "select * from pen_setting_kode where idKode='$par[idKode]'";
$res = db($sql);
$r = mysql_fetch_array($res);

?>
<div class="pageheader">
    <h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
    <?= getBread() ?>
    <span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper">
    <form id="form" name="form" method="post" action="?<?= getPar($par, "idKode,kodeTipe") ?>" class="stdform">
        <p>
            <label class="l-input-small">Penilaian&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t'", "kodeTipe", "namaTipe", "par[kodeTipe]", "", $par[kodeTipe], "onchange=\"this.form.submit();\"", "300px"); ?>
        </p>
        <p>
            <label class="l-input-small">Kode Penilaian&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <?= comboData("SELECT idKode, subKode FROM pen_setting_kode WHERE kodeTipe = $par[kodeTipe] and statusKode='t'", "idKode", "subKode", "par[idKode]", "", $par[idKode], "onchange=\"this.form.submit();\"", "300px"); ?>
        </p>
        <p>
            <label class="l-input-small">Periode&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
            <?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT' and statusData='t' order by kodeData asc", "kodeData", "namaData", "par[idPeriode]", "", $par[idPeriode], "onchange=\"this.form.submit();\"", "110px"); ?>
        </p>

        <br clear="all">
        <div style="margin-top:-20px;">
            <fieldset style="padding:10px; border-radius: 10px;">
                <legend style="padding:10px; margin-left:20px;"><h4>PENILAIAN</h4></legend>
                <p>
                    <label class="l-input-small">Kode</label>
                    <span class="field">
					<?= $r[namaKode]; ?> &nbsp;
				</span>
                </p>
                <p>
                    <label class="l-input-small">Penilaian</label>
                    <span class="field">
					<?= $r[subKode]; ?> &nbsp;
				</span>
                </p>
                <p>
                    <label class="l-input-small">Keterangan</label>
                    <span class="field">
					<?= nl2br($r[keteranganKode]); ?> &nbsp;
				</span>
                </p>
                <p>
                    <label class="l-input-small">Surat Keputusan</label>
                    <span class="field">
					<?php
                    if (empty($r[skKode])) {
                        ?>
                        &nbsp;
                        <?php
                    } else {
                        echo "<a href=\"#Preview\" title=\"Preview File\" onclick=\"openBox('view.php?&par[tipe]=file_pen_kode&par[idKode]=$r[idKode]',900,500);\"><img style=\" height:20px;\" src=\"" . getIcon($r[skKode]) . "\"></a>";
                    }
                    ?>
				</span>
                </p>
            </fieldset>
        </div>
        <div class="widgetbox" style="margin-bottom: -10px">
            <div class="title"><h3>Aspek penilaian</h3></div>
        </div>
        <table class="stdtable">
            <thead>
            <tr>
                <th>No</th>
                <th>Aspek Penilaian</th>
                <th>Bobot</th>
                <th>Nilai</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT t1.idAspek, t1.namaAspek, t1.bobotAspek, t2.nilaiDetail FROM pen_setting_aspek t1 LEFT JOIN pen_setting_aspek_detail t2 ON (t2.idAspek = t1.idAspek) WHERE t1.statusAspek = 't' and t1.idPeriode = '$par[idPeriode]'";
            $res = db($sql);
            $no = 0;
            $totalAspek = 0;
            $subTotal = 0;
            $subTotalAspek = 0;
            while ($r = mysql_fetch_array($res)) {
                $no++;
                $totalAspek++;
                $subTotal += $r[nilaiDetail];
                ?>
                <tr>
                    <td width="20" align="right"><?= $no ?></td>
                    <td><?= $r[namaAspek] ?></td>
                    <td align="right"><?= getAngka($r[bobotAspek]) ?>%</td>
                    <td align="right"><?= getAngka($r[nilaiDetail]) ?></td>
                </tr>
                <?php
                $idTipe = getField("select kodeTipe from pen_setting_kode where idKode = $par[idKode]");
                $getPrespektif = queryAssoc("select * from pen_setting_prespektif where idKode = $par[idKode] and idAspek = $r[idAspek]  and idTipe = $idTipe order by urut asc");
                if ($getPrespektif) {
                    $subTotalBobot[$r[idAspek]] = 0;
                    foreach ($getPrespektif as $prespektif) {
                        ?>
                        <tr>
                            <td width="20" align="right"></td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $prespektif[namaPrespektif] ?></td>
                            <td align="right"><?= $prespektif[bobot] ?> %</td>
                            <td></td>
                        </tr>
                        <?php
                        $subTotalBobot[$r[idAspek]] += $prespektif[bobot];
                    }
                    ?>
                    <tr>
                        <td width="20" align="right"></td>
                        <td align="right"><strong>Sub-Total</strong></td>
                        <td align="right"><?= $subTotalBobot[$r[idAspek]] ?> %</td>
                        <td></td>
                    </tr>
                    <?php
                }
            }
            $avgAspek = $subTotal / $no;
            ?>
            </tbody>
            <tfooter>
                <tr>
                    <td colspan="3" align="right">Nilai Rata-Rata</td>
                    <td align="right"><?= getAngka($avgAspek, 2) ?></td>
                </tr>
            </tfooter>
        </table>

        <div class="widgetbox" style="margin-bottom: -10px">
            <div class="title"><h3>NILAI KONVERSI</h3></div>
        </div>
        <table class="stdtable">
            <thead>
            <tr>
                <th width="20">No</th>
                <th width="120">Nilai</th>
                <th width="80">Kode</th>
                <th>Penjelasan</th>
                <th width="80">WOM</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT CONCAT(nilaiMin, ' - ', nilaiMax) nilaiKonversi, uraianKonversi, penjelasanKonversi, warnaKonversi FROM pen_setting_konversi WHERE idPeriode = '$par[idPeriode]' AND statusKonversi = 't'";
            $res = db($sql);
            $no = 0;
            while ($r = mysql_fetch_array($res)) {
                $no++;
                ?>
                <tr>
                    <td width="20" align="right"><?= $no ?>.</td>
                    <td width="120" align="center">
                        <b><?= $r[nilaiKonversi] ?></b>
                    </td>
                    <td width="250" align="center"><?= $r[uraianKonversi] ?></td>
                    <td><?= $r[penjelasanKonversi] ?></td>
                    <td width="120" align="center">
							<span style="background-color: <?= $r[warnaKonversi] ?>">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</span>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </form>
</div>
<script type="text/javascript">
    jQuery("#par\\[kodeTipe\\]").live("change", function (e) {


        setCookie(jQuery("#par\\[kodeTipe\\]").val());
    });

    function createCookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        } else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function setCookie(elem) {
        createCookie("tipePenilaian", elem);
    }
</script>