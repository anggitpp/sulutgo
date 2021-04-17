<?php
$resEmp = getField("SELECT CONCAT(name, 'tabsplit\t', reg_no, 'tabsplit\t', pos_name, 'tabsplit\t', div_id, 'tabsplit\t', dept_id, 'tabsplit\t', unit_id) FROM dta_pegawai WHERE id = '$par[idPegawai]'");
list($namaPegawai, $nikPegawai, $jabatanPegawai, $divId, $deptId, $unitId) = explode("tabsplit\t", $resEmp);
?>
<div class="pageheader">
	<h1 class="pagetitle"><?php echo $arrTitle[$s] ?>&nbsp;&raquo;&nbsp;Detail</h1>
	<div style="margin-top: 10px;">
		<?php echo getBread("Detail") ?>
	</div>
	<span class="pagedesc">&nbsp;</span>
</div>
<div class="contentwrapper" id="contentwrapper">
	<form id="form" action="" method="post" class="stdform">
		<div style="position:absolute; right:0; margin-top: -75px; margin-right:20px;">
			<a href="?par[mode]=export<?php echo getPar($par, "mode") ?>" class="btn btn1 btn_inboxo" title="Export Data"><span>Export Data</span></a>
			<input type="button" class="cancel radius2" value="Back" onclick="window.location = '?<?php echo getPar($par, "mode,idPegawai"); ?>';"/>
		</div>	

		<div class="widgetbox">
			<div class="title" style="margin-top:10px; margin-bottom:0px;"><h3>DATA PEGAWAI</h3></div>
		</div>

		<p>
			<label class="l-input-small">Nama</label>
			<span class="field">
				<?php echo $namaPegawai ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small">NPP</label>
			<span class="field">
				<?php echo $nikPegawai ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small">Jabatan</label>
			<span class="field">
				<?php echo $jabatanPegawai ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small"><?php echo $arrParameter[39] ?></label>
			<span class="field">
				<?php echo getField("SELECT namaData FROM mst_data WHERE kodeData = '$divId'") ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small"><?php echo $arrParameter[40] ?></label>
			<span class="field">
				<?php echo getField("SELECT namaData FROM mst_data WHERE kodeData = '$deptId'") ?>&nbsp;
			</span>
		</p>
		<p>
			<label class="l-input-small"><?php echo $arrParameter[41] ?></label>
			<span class="field">
				<?php echo getField("SELECT namaData FROM mst_data WHERE kodeData = '$unitId'") ?>&nbsp;
			</span>
		</p>

		<div class="widgetbox">
			<div class="title" style="margin-top:10px; margin-bottom:0px;"><h3>DETAIL CUTI</h3></div>
		</div>

		<p>
			<label class="l-input-small">Periode Cuti</label>
			<span class="field">
				<?php echo $par[tanggalMulai] ?>&nbsp;s/d&nbsp;<?php echo $par[tanggalSelesai] ?>
			</span>
		</p>
		<br clear="all">
		<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
			<thead>
				<tr>
					<th rowspan="2" width="20" style="vertical-align: middle;">NO.</th>
					<th rowspan="2" width="200" style="vertical-align: middle;">NOMOR CUTI</th>
					<th rowspan="2" style="vertical-align: middle;">TIPE CUTI</th>
					<th rowspan="2" style="vertical-align: middle;"width="250">KETERANGAN CUTI</th>
					<th colspan="2" width="120">TANGGAL CUTI</th>
				</tr>
				<tr>
					<th width="120">MULAI</th>
					<th width="120">SELESAI</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$_sql = "
				SELECT 
				t1.*, t2.namaCuti
				FROM att_cuti t1 
				JOIN dta_cuti t2 
				ON t2.idCuti = t1.idTipe
				WHERE 
				t1.idPegawai = '$par[idPegawai]' 
				AND t1.persetujuanCuti = 't' AND t1.sdmCuti = 't' 
				AND (t1.mulaiCuti BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."' OR t1.selesaiCuti BETWEEN '".setTanggal($par[tanggalMulai])."' AND '".setTanggal($par[tanggalSelesai])."')";
				$_res = db($_sql);
				$_no = 0;
				while($_r = mysql_fetch_assoc($_res)){
					$_no++;
					?>
					<tr>
						<td width="20" align="right"><?php echo $_no ?>.</td>
						<td width="200"><?php echo $_r['nomorCuti'] ?></td>
						<td><?php echo $_r['namaCuti'] ?></td>
						<td><?php echo $_r['keteranganCuti'] ?></td>
						<td align="center" width="120"><?php echo getTanggal($_r['mulaiCuti']) ?></td>
						<td align="center" width="120"><?php echo getTanggal($_r['selesaiCuti']) ?></td>
					</tr>
					<?php
				}

				if($_no == 0){
					?>
					<tr>
						<td colspan="6">No data available</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</form>
</div>
