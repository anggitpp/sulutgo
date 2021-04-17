<?php
global $s, $par, $menuAccess, $arrTitle;
?>

<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
		<thead>
			<tr>
				<th width="20">NO</th>
				<th width="350">BAB - PASAL</th>
				<th>KETERANGAN</th>
				
				<?php
				// If current user has access to edit or delete on the current menu, then append 'KONTROL' header
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
					?>
					<th width="80">KONTROL</th>
					<?php 
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql = "
			SELECT 
			t1.kodeData kodeBab, t1.namaData namaBab, t1.keteranganData keteranganBab, 
			t2.kodeData kodePasal, t2.namaData namaPasal, t2.keteranganData keteranganPasal 
			FROM mst_data t1 
			LEFT JOIN mst_data t2 
			ON t2.kodeInduk = t1.kodeData 
			WHERE t1.kodeCategory = 'PR01' 
			ORDER BY t1.urutanData
			";
			$res = db($sql);
			$kodeBab = 0;
			$noInduk = 0;
			$noAnak = 0;
			while($r = mysql_fetch_assoc($res)){
				if($kodeBab != $r[kodeBab]){
					$noInduk++;
					$noAnak = 0;
					$kodeBab = $r[kodeBab];
					echo "
					<tr>
						<td>$noInduk</td>
						<td>$r[namaBab]</td>
						<td>$r[keteranganBab]</td>
						<td></td>
					</tr>
					";
				}
				$noAnak++;
				echo "
				<tr>
					<td></td>
					<td style='padding-left:20px;'>$noAnak. $r[namaPasal]</td>
					<td>$r[keteranganPasal]</td>";

					if(isset($menuAccess[$s]['edit'])) {
						echo "<td align='center'><a class='add' href='?par[mode]=det&par[kodePasal]=$r[kodePasal]".getPar($par,"mode")."'><span>Tambah Data</span></a></td>";
					}
					echo "
				</tr>";
			}
			?>
		</tbody>
	</table>
</div>