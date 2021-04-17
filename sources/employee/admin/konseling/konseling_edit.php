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

    if (isset($_GET['idKonseling'])) {
        $filter .= " AND t1.idKonseling = '".$_GET['idKonseling']."'";
    }

    $sql = "
	SELECT
	t1.idKonseling, t1.nomorKonseling, t2.name, t2.pos_name,
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

$r[userKonselor] = empty($r[userKonselor]) ? $cUsername : $r[userKonselor];
$r[namaUser] = getField("SELECT namaUser FROM app_user WHERE username = '$r[userKonselor]'");
$resEmp = getField("SELECT CONCAT(name, 'tabsplit\t', pos_name) FROM dta_pegawai WHERE id = '$r[idPegawai]'");
list($r[namaPegawai], $r[jabatanPegawai]) = explode("tabsplit\t", $resEmp);
$r[nomorKonseling] = empty($r[nomorKonseling]) ? str_pad($r[idKonseling], 2, '0', STR_PAD_LEFT).'/KNS/'.getRomawi(date('m', strtotime($r[createDate]))).'/'.date('Y', strtotime($r[createDate])) : $r[nomorKonseling];

setValidation('is_null', 'displayNamaPegawai', 'anda belum mengisi nama pegawai');
setValidation('is_null', 'idPegawai', 'nama pegawai tidak ditemukan di data pegawai');
setValidation('is_null', 'inp[tanggalKonseling]', 'anda belum mengisi tanggal');
setValidation('is_null', 'inp[idKategori]', 'anda belum mengisi kategori');
echo getValidation();
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
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data" onsubmit="return validation(this);">
		<p style="position: absolute; right: 20px; top: 10px;">
			<input type="submit" class="submit radius2" name="btnSimpan" value="Save"/>
			<input type="button" class="cancel radius2" value="Cancel" onclick="window.location = '?<?= getPar(); ?>';"/>
		</p>

		<p>
			<label class="l-input-small">Nomor</label>
			<div class="field">
				<input type="text" id="inp[nomorKonseling]" name="inp[nomorKonseling]" class="vsmallinput" value="<?php echo $r[nomorKonseling] ?>" readonly>
			</div>
		</p>

		<p>
			<label class="l-input-small">Nama Pegawai</label>
			<div class="field">
				<input type="hidden" id="idPegawai" name="inp[idPegawai]" value="<?php echo $r[idPegawai] != '0' ? $r[idPegawai] : '' ?>">
				<input type="text" id="displayNamaPegawai" name="displayNamaPegawai" class="smallinput lookupEmp" value="<?php echo $r[namaPegawai] ?>">
			</div>
		</p>

		<p>
			<label class="l-input-small">Jabatan</label>
			<div class="field">
				<input type="text" id="displayJabatanPegawai" name="displayJabatanPegawai" class="smallinput" value="<?php echo $r[jabatanPegawai] ?>" readonly>
			</div>
		</p>

		<p>
			<label class="l-input-small">Tanggal</label>
			<div class="field">
				<input type="text" id="inp[tanggalKonseling]" name="inp[tanggalKonseling]" class="vsmallinput hasDatePicker" value="<?php echo getTanggal($r[tanggalKonseling]) ?>">
			</div>
		</p>

		<p>
			<label class="l-input-small">Waktu</label>
			<div class="field">
				<input type="text" id="waktuMulai" name="inp[waktuMulai]" class="vsmallinput hasTimePicker" style="background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;" value="<?php echo $r[waktuMulai] ?>">
				&nbsp;&nbsp;s.d&nbsp;&nbsp;
				<input type="text" id="waktuSelesai" name="inp[waktuSelesai]" class="vsmallinput hasTimePicker" style="background: url(styles/images/icons/time.png) no-repeat left; padding-left:30px; width:50px;" value="<?php echo $r[waktuSelesai] ?>">
			</div>
		</p>

		<p>
			<label class="l-input-small">Perihal</label>
			<div class="field">
				<textarea name="inp[perihalKonseling]" id="inp[perihalKonseling]" class="smallinput" style="height: 50px;"><?php echo $r[perihalKonseling] ?></textarea>
			</div>
		</p>

		<p>
			<label class="l-input-small">Catatan</label>
			<div class="field">
				<textarea name="inp[catatanKonseling]" id="inp[catatanKonseling]" class="smallinput" style="height: 50px;"><?php echo $r[catatanKonseling] ?></textarea>
			</div>
		</p>

		<p>
			<label class="l-input-small">Kategori</label>
			<div class="field">
				<?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'ADM01' AND statusData = 't' ORDER BY urutanData", 'kodeData', 'namaData', 'inp[idKategori]', ' ', $r[idKategori], '', '225px'); ?>
			</div>
		</p>

		<p>
			<label class="l-input-small">Konselor</label>
			<div class="field">
				<input type="hidden" id="userKonselor" name="inp[userKonselor]" value="<?php echo $r[userKonselor] ?>">
				<input type="text" id="displayNamaKonselor" name="displayNamaKonselor" class="smallinput lookupKonselor" value="<?php echo $r[namaUser] ?>">
			</div>
		</p>

		<div class="widgetbox">
			<div class="title" style="margin-bottom:0px;"><h3>HISTORI KONSELING</h3></div>
		</div>
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dtHistory">
			<thead>
				<tr>
					<th rowspan="2" width="20" style="vertical-align: middle;">NO</th>
					<th rowspan="2" width="120" style="vertical-align: middle;">TANGGAL</th>
					<th colspan="2" style="vertical-align: middle;">WAKTU</th>
					<th rowspan="2" style="vertical-align: middle;">KATEGORI</th>
					<th rowspan="2" style="vertical-align: middle;">POKOK PEMBAHASAN</th>
				</tr>
				<tr>
					<th style="vertical-align: middle;">MULAI</th>
					<th style="vertical-align: middle;">SELESAI</th>
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
		tmplRow += "<tr>";
		tmplRow += "	<td align=\"right\">" + nextNum + ".</td>";
		tmplRow += "	<td align=\"center\">" + data.tanggalKonseling + "</td>";
		tmplRow += "	<td align=\"center\">" + data.waktuMulai + "</td>";
		tmplRow += "	<td align=\"center\">" + data.waktuSelesai + "</td>";
		tmplRow += "	<td>" + data.namaKategori + "</td>";
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
