<?php
if (isset($_POST['btnSimpan'])) {
    updateData();
    die();
}

$sql = "SELECT * FROM sdm_konseling WHERE idKonseling = '$par[idKonseling]'";
$res = db($sql);
$r = mysql_fetch_assoc($res);
$par[idPegawai] = !empty($r[idPegawai]) && !isset($par[idPegawai]) ? $r[idPegawai] : $par[idPegawai];
if ($json == 1) {
    header('Content-type: application/json');

    $filter = "WHERE t1.idKonseling IS NOT NULL AND t1.idPegawai = '$par[idPegawai]'";

    $sql = "
	SELECT
	t1.idKonseling, t1.idPegawai, t1.nomorKonseling, t2.name, t2.pos_name, t2.pic_filename,
	t1.tanggalKonseling, t1.waktuMulai, t1.waktuSelesai, t1.perihalKonseling, t1.catatanKonseling,
	t3.namaData namaKategori, t4.namaUser
	FROM sdm_konseling t1
	JOIN dta_pegawai t2 ON t2.id = t1.idPegawai
	JOIN mst_data t3 ON t3.kodeData = t1.idKategori
	JOIN app_user t4 ON t4.username = t1.userKonselor
	$filter
	ORDER BY t1.tanggalKonseling DESC
	";

    $ret = array();
    $res = db($sql);
    while ($r = mysql_fetch_assoc($res)) {
        $r[tanggalKonseling] = getTanggal($r[tanggalKonseling]);
        $ret[] = $r;
    }

    echo json_encode($ret);
    exit();
}

$r[namaKategori] = getField("SELECT namaData FROM mst_data WHERE kodeData = '$r[idKategori]'");
$r[userKonselor] = empty($r[userKonselor]) ? $cUsername : $r[userKonselor];
$r[namaUser] = getField("SELECT namaUser FROM app_user WHERE username = '$r[userKonselor]'");
$resEmp = getField("SELECT CONCAT(name, 'tabsplit\t', pos_name, 'tabsplit\t', pic_filename) FROM dta_pegawai WHERE id = '$r[idPegawai]'");
list($r[namaPegawai], $r[jabatanPegawai], $r[pic_filename]) = explode("tabsplit\t", $resEmp);
$r[nomorKonseling] = empty($r[nomorKonseling]) ? str_pad($r[idKonseling], 2, '0', STR_PAD_LEFT).'/KNS/'.getRomawi(date('m', strtotime($r[createDate]))).'/'.date('Y', strtotime($r[createDate])) : $r[nomorKonseling];
?>
<style>
.container { width: 800px; margin: 0 auto; }
.autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-no-suggestion { padding: 2px 5px;}
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: bold; color: #000; }
.autocomplete-group { padding: 2px 5px; }
.autocomplete-group strong { font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }
</style>
<script src="scripts/jquery.autocomplete.min.js" charset="utf-8"></script>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread(); ?>
	<span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper" id="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
		<p style="position: absolute; right: 20px; top: 10px;">
			<input type="button" class="cancel radius2" value="Cancel" onclick="window.location = '?<?= getPar(); ?>';"/>
		</p>
		<div class="three_fourth">
			<p>
				<label class="l-input-small">Nomor</label>
				<span class="field">
					<?php echo $r[nomorKonseling] ?>&nbsp;
				</span>
			</p>

			<p>
				<label class="l-input-small">Nama Pegawai</label>
				<span class="field">
					<?php echo $r[namaPegawai] ?>&nbsp;
				</span>
			</p>

			<p>
				<label class="l-input-small">Jabatan</label>
				<span class="field">
					<?php echo $r[jabatanPegawai] ?>
				</span>
			</p>
			<p>
				<label class="l-input-small">Tanggal</label>
				<span class="field">
					<?php echo getTanggal($r[tanggalKonseling]) ?>
				</span>
			</p>

			<p>
				<label class="l-input-small">Waktu</label>
				<span class="field">
					<?php echo $r[waktuMulai] ?>
					&nbsp;&nbsp;s.d&nbsp;&nbsp;
					<?php echo $r[waktuSelesai] ?>
				</span>
			</p>

			<p>
				<label class="l-input-small">Perihal</label>
				<span class="field">
					<?php echo $r[perihalKonseling] ?>
				</span>
			</p>

			<p>
				<label class="l-input-small">Catatan</label>
				<span class="field">
					<?php echo $r[catatanKonseling] ?>
				</span>
			</p>

			<p>
				<label class="l-input-small">Kategori</label>
				<span class="field">
					<?php echo $r[namaKategori] ?>
				</span>
			</p>

			<p>
				<label class="l-input-small">Konselor</label>
				<span class="field">
					<?php echo $r[namaUser] ?>
				</span>
			</p>
		</div>
		<div class="one_fourth" style="margin-right: 0;">
			<img alt="<?= $r['namaPegawai'] ?>" width="80%" height="200px" src="<?= 'files/emp/pic/'.($r['pic_filename'] == '' ? 'nophoto.jpg' : $r['pic_filename']) ?>" class='pasphoto'>
		</div>
		<br clear="all">

		<div class="widgetbox">
			<div class="title" style="margin-bottom:0px;"><h3>HISTORI KONSELING</h3></div>
		</div>
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dtHistory">
			<thead>
				<tr>
					<th width="20" style="vertical-align: middle;">NO</th>
					<th width="200" style="vertical-align: middle;">NOMOR</th>
					<th width="120" style="vertical-align: middle;">TANGGAL</th>
					<th width="100" style="vertical-align: middle;">KATEGORI</th>
					<th style="vertical-align: middle;">PERIHAL</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</form>
</div>
<script type="text/javascript">
jQuery(".lookupKonselor").autocomplete({
	serviceUrl: 'ajax.php?par[mode]=getUserList<?= getPar() ?>',
	onSelect: function (data) {
		jQuery("#userKonselor").val(data.data);
	}
});

jQuery(".lookupEmp").autocomplete({
	serviceUrl: 'ajax.php?par[mode]=getEmpList<?= getPar() ?>',
	onSelect: function (data) {
		var str = data.data.split("tabsplit\t");
		if(jQuery("#idPegawai").val() != str[0]){
			jQuery("#idPegawai").val(str[0]);
			jQuery("#displayJabatanPegawai").val(str[1]);

			jQuery.ajax({
				url: 'ajax.php?json=1&par[idPegawai]=' + str[0] + '<?php echo getPar($par, 'idKonseling,idPegawai') ?>',
				type: 'GET',
				dataType: 'json',
			}).done(function(result) {
				jQuery("#dtHistory tbody").empty();
				var i = 0;
				for(i; i < result.length; i++) {
					var obj = result[i];

					appendNewRow(obj);
				}

				if(i == 0)
				appendNewRow(null);
			});
		}
	}
});

jQuery(document).ready(function(){
	jQuery.ajax({
		url: 'ajax.php?json=1&par[idPegawai]=<?php echo $par[idPegawai].getPar($par, 'idKonseling,idPegawai') ?>',
		type: 'GET',
		dataType: 'json',
	}).done(function(result) {
		jQuery("#dtHistory tbody").empty();
		var i = 0;
		for(i; i < result.length; i++) {
			var obj = result[i];

			appendNewRow(obj);
		}

		if(i == 0)
		appendNewRow(null);
	});
});

function appendNewRow(data){
	var maxNum = jQuery("#dtHistory tbody tr").length;
	var nextNum = maxNum + 1;
	if(data == null){
		jQuery("#dtHistory tbody").append('<tr id="trEmpty"><td colspan="6">No data available in table</td></tr>');
	}else{
		var tmplRow = "";
		if("<?php echo $par[idKonseling] ?>" == data.idKonseling)
		tmplRow += "<tr style=\"background-color: #E2E4FF;\">";
		else
		tmplRow += "<tr>";
		tmplRow += "	<td align=\"right\">" + nextNum + ".</td>";
		tmplRow += "	<td align=\"center\"><a href=\"?par[mode]=det&par[idKonseling]=" + data.idKonseling + "&par[idPegawai]=" + data.idPegawai + "<?php echo getPar(); ?>\">" + data.nomorKonseling + "</a></td>";
		tmplRow += "	<td align=\"center\">" + data.tanggalKonseling + "</td>";
		tmplRow += "	<td align=\"center\">" + data.namaKategori + "</td>";
		tmplRow += "	<td>" + data.perihalKonseling + "</td>";
		tmplRow += "</tr>";

		jQuery("#dtHistory tbody").append(tmplRow);
	}
}

appendNewRow(null);
</script>
<?php
function updateData()
{
    global $inp, $par, $cUsername;
    repField();

    $sql = "UPDATE sdm_konseling SET nomorKonseling = '$inp[nomorKonseling]', idPegawai = '$inp[idPegawai]', tanggalKonseling = '".setTanggal($inp[tanggalKonseling])."', waktuMulai = '$inp[waktuMulai]', waktuSelesai = '$inp[waktuSelesai]', perihalKonseling = '$inp[perihalKonseling]', catatanKonseling = '$inp[catatanKonseling]', idKategori = '$inp[idKategori]', userKonselor = '$inp[userKonselor]', updateBy = '$cUsername', updateDate = '".date('Y-m-d H:i:s')."' WHERE idKonseling = '$par[idKonseling]'";
    db($sql);

    echo "
	<script>
	alert('DATA TELAH DISIMPAN');
	window.location = '?par[mode]=edit".getPar($par, 'mode')."';
	</script>";
}
?>
