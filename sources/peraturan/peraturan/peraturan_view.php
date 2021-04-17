<?php
global $s, $par, $menuAccess, $arrTitle;
?>

<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<form class="stdform">
		<fieldset>
			<legend>BAB - PASAL</legend>
			<p>
				<label class="l-input-small">BAB</label>
				<span class="field"><?= getField("select t2.namaData from mst_data t1 join mst_data t2 on t2.kodeData = t1.kodeInduk WHERE t1.kodeData = '$par[kodePasal]'")?>&nbsp;</span>
			</p>


			<p>
				<label class="l-input-small">Keterangan</label>
				<span class="field"><?= getField("select t2.keteranganData from mst_data t1 join mst_data t2 on t2.kodeData = t1.kodeInduk WHERE t1.kodeData = '$par[kodePasal]'")?>&nbsp;</span>
			</p>
			<p>
				<label class="l-input-small">Pasal</label>
				<span class="field"><?= getField("select t1.namaData from mst_data t1 join mst_data t2 on t2.kodeData = t1.kodeInduk WHERE t1.kodeData = '$par[kodePasal]'")?>&nbsp;</span>
			</p>
			<p>
				<label class="l-input-small">Keterangan</label>
				<span class="field"><?= getField("select t1.keteranganData from mst_data t1 join mst_data t2 on t2.kodeData = t1.kodeInduk WHERE t1.kodeData = '$par[kodePasal]'")?>&nbsp;</span>
			</p>
		</fieldset>
	</form>
	<div class="widgetbox">
		<div class="title">
			<h3>Penjelasan Pasal</h3>
		</div>
	</div>

	<a href="#" class="btn btn1 btn_document" onclick="openBox('popup.php?par[mode]=add<?= getPar($par,'mode'); ?>',800,350);" style="position:absolute; right:20px; top:275px;"><span>TAMBAH</span></a>

	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="dyntable">
		<thead>
			<tr>
				<th width="20">NO</th>
				<th>Ayat</th>
				<th width="80">Order</th>
				<th width="80">Status</th>
				<th width="100">Kontrol</th>
			</tr>
		</thead>

		<tbody>
			<?php
			$sql = "
			SELECT 
			*
			FROM per_pasal_ayat t1 where t1.kodePasal='$par[kodePasal]' order by t1.urutanAyat";


			$res = db($sql);
			$no = 0;
	// Common database result looping
			while($r = mysql_fetch_assoc($res)){
				$no++;

				$statusAyat = ""; 
				switch ($r[statusAyat]) {
					case 't':
					$statusAyat = "<img src=\"styles/images/t.png\" title=\"Tampil\" />";
					break;

					default:
					$statusAyat = "<img src=\"styles/images/f.png\" title=\"Tidak Tampil\" />";
					break;
				}
				echo "<tr>
				<td>$no</td>
				<td>$r[namaAyat]</td>
				<td align='right'>$r[urutanAyat]</td>
				<td align='center'>$statusAyat</td>
				<td align='center'>
					<a href='#' onclick=\"openBox('popup.php?par[mode]=edit&par[idAyat]=$r[idAyat]".getPar($par,'mode')."',800,350);\" class='edit'><span>Edit Data</span></a>

				    

				    <a href=\"?par[mode]=del&par[idAyat]=$r[idAyat]".getPar($par,"mode,idAyat")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>

				</td>
			</tr>";
		}
		?>
	</tbody>
</table>
</div>