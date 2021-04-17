<?php
global $s, $par, $menuAccess, $arrTitle;
$infoKode = getField("SELECT CONCAT(namaKode, '~', kodeAspek, '~', kodePrespektif) FROM pen_setting_kode WHERE idKode = '$par[idKode]'");
list($namaKode, $kodeAspek, $kodePrespektif) = explode("~", $infoKode);
$arrAspek = arrayQuery("SELECT idAspek, namaAspek FROM pen_setting_aspek WHERE kodeAspek = '$kodeAspek' ORDER BY urutanAspek");

$keys = array_keys($arrAspek);
if(!isset($par[tab]))
	$par[tab] = $keys[0];
?>
<script type="text/javascript">
	function changeTotalSubyek(idAspek){
		var table = jQuery("#dtAspek_" + idAspek + " > tbody > tr");
		jQuery("#displayTotalSubyek_" + idAspek).html(table.length + " Pertanyaan &nbsp;");
	}
</script>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<div style="margin-top: 10px">
		<?php echo getBread(ucwords($par[mode]." data")) ?>
	</div>
	<span class="pagedesc">&nbsp;</span>
</div>
<div id="contentwrapper" class="contentwrapper">
	<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" enctype="multipart/form-data">
		<a href="#dlg" onclick="openBox('popup.php?par[mode]=ambil<?= getPar($par, "mode") ?>', 600, 250);" class="btn btn1 btn_inboxi" style="position: absolute; right: 20px; top: 10px"><span>Ambil Data</span></a>
		<p>
			<label class="l-input-small">Kode Penilaian</label>
			<span class="field">
				<?=  $namaKode ?>  &nbsp;
			</span>
		</p>

		<p>
			<label class="l-input-small">Tipe</label>
			<span class="field">
				<?= getField("SELECT namaTipe FROM pen_tipe WHERE kodeTipe = '$par[kodeTipe]'") ?> &nbsp;
			</span>
		</p>
		
		<br clear="all">

		<ul class="hornav" style="margin:10px 0px !important;">
			<?php
			foreach($arrAspek as $key => $val){
				if($key == $par[tab])
					echo "<li class=\"current\"><a href=\"#tab_$key\">$val</a></li>";
				else
					echo "<li><a href=\"#tab_$key\">$val</a></li>";
			}
			?>
		</ul>

		<?php
		foreach($arrAspek as $key => $val){
			?>
			<div class="subcontent" id="tab_<?= $key ?>" style="border-radius:0; display: <?= $key != $par[tab] ? "none" : "block" ?>;">
				<table width="100%">
					<tr>
						<td>
							<p>
								<label class="l-input-small">Total Subyek</label>
								<span class="field" id="displayTotalSubyek_<?= $key ?>">
									0 Pertanyaan&nbsp;
								</span>
							</p>
						</td>
						<td align="right" width="25%">
							<a href="#add" onclick="openBox('popup.php?par[mode]=addSubyek&amp;par[tab]=<?= $key ?>&amp;par[idAspek]=<?= $key.getPar($par, "mode,tab,idAspek"); ?>', 800, 575);" class="btn btn1 btn_document"><span>Tambah Data</span></a>
						</td>
					</tr>
				</table>
				<table id="dtAspek_<?= $key ?>" class="stdtable" style="margin-top: 10px">
					<thead>
						<tr>
							<th width="20">NO</th>
							<th>PERTANYAAN</th>
							<th width="100">20</th>
							<th width="100">40</th>
							<th width="100">60</th>
							<th width="100">80</th>
							<th width="80">KONTROL</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sql = "SELECT * FROM pen_sasaran WHERE kodeTipe = '$par[kodeTipe]' AND idAspek = '$key'";
						$res = db($sql);
						$no = 0;
						while($r = mysql_fetch_array($res)){
							$no++;
							$arrPilihan = arrayQuery("SELECT namaPilihan FROM pen_sasaran_detail WHERE idSasaran = '$r[idSasaran]' ORDER BY bobotPilihan");
							?>
							<tr>
								<td width="20" align="right"><?= $no ?>.</td>
								<td><?= $r[namaSasaran] ?></td>
								<td width="100"><?= $arrPilihan[0] ?></td>
								<td width="100"><?= $arrPilihan[1] ?></td>
								<td width="100"><?= $arrPilihan[2] ?></td>
								<td width="100"><?= $arrPilihan[3] ?></td>
								<td width="80" align="center">
									<a href="#edit" onclick="openBox('popup.php?par[mode]=editSubyek&amp;par[tab]=<?= $key ?>&amp;par[idAspek]=<?= $key ?>&amp;par[idSasaran]=<?= $r[idSasaran].getPar($par, "mode,tab") ?>',  800, 575);" class="edit" title="Edit Data"><span>Edit Data</span></a>
									<a href="?par[mode]=delSubyek&amp;par[tab]=<?= $key ?>&amp;par[idSasaran]=<?= $r[idSasaran].getPar($par, "mode,tab") ?>" onclick="return confirm('are you sure to delete data ?');" class="delete" title="Delete Data"><span>Delete Data</span></a>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<script type="text/javascript">
				changeTotalSubyek('<?= $key ?>');
			</script>
			<?php
		}
		?>
	</form>
</div>