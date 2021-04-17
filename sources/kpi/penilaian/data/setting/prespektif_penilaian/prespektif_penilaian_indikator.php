<?php 
global $s, $par, $menuAccess, $arrTitle, $json;
$sql="select * from pen_setting_prespektif where idPrespektif='$par[idPrespektif]'";
$res=db($sql);
$r=mysql_fetch_array($res);
$par[kodeAspek] = $r['kodeAspek'];
$par[kodePrespektif] = $r['kodePrespektif'];
?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>	
	<span class="pagedesc">&nbsp;</span>							
</div>
<div class="contentwrapper">
    <span style="position: absolute; top: .8rem; right: 1.2rem;">
		<input type="button" class="cancel radius2" value="Kembali" onclick="window.location='?<?= getPar($par, "mode, idPrespektif,idAspek"); ?>';"/>
	</span>
	<br />
	<div style="padding:10px; margin-top:-50px;">
    	<fieldset style="padding:10px; border-radius: 10px;">						
    	<legend style="padding:10px; margin-left:20px;"><h4>PENILAIAN</h4></legend>
    		<form class="stdform">
    			<p>
    				<label class="l-input-small">Penilaian</label>
    				<span class="field">
    					<?= getField("SELECT concat(namaKode, ' -- ', subKode) from pen_setting_kode where idKode='$r[idKode]'");?>
    				</span>
    			</p>
                <p>
    				<label class="l-input-small">Aspek</label>
    				<span class="field">
    					<?= getField("SELECT namaAspek from pen_setting_aspek where idAspek='$par[idAspek]'");?>
    				</span>
    			</p>
    			<p>
    				<label class="l-input-small">Prespektif</label>
    				<span class="field">
    					<?= $r[namaPrespektif];?>
    				</span>
    			</p>
    		</form>
    	</fieldset>
	</div>
	<div style="position:absolute; right:25px; margin-top: 10px;">
		<?php
			if(isset($menuAccess[$s]["add"])) echo "<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=addIndikator".getPar($par,"mode,kodeIndikator,indukIndikator")."',750,350);\"><span>Tambah Data</span></a>";
		?>
	</div>
	<div class="widgetbox">
		<div class="title" style="margin-top:30px; margin-bottom:0px;"><h3>INDIKATOR</h3></div>
	</div>	
	<table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick" id="datatable">
		<thead>
			<tr>
				<th width="20" style="vertical-align: middle">NO.</th>
				<th style="vertical-align: middle">Uraian</th>
				<th width="100" style="vertical-align: middle">Urut</th>
				<th width="100" style="vertical-align: middle">Status</th>
				<?php 
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']) ){
					?>
					<th width="80" style="vertical-align: middle">KONTROL</th>
					<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			$no=1;
			$sql="select * from pen_setting_prespektif_indikator where idPrespektif='$par[idPrespektif]' and levelIndikator=1";
			$res=db($sql);
			while($r=mysql_fetch_array($res)){
				$statusIndikator = $r[statusIndikator] == "t" ?
				"<img src=\"styles/images/t.png\" >" : "<img src=\"styles/images/f.png\" >";
				echo "<tr>
					<td>$no.</td>
					<td>$r[uraianIndikator]</td>
					<td align=\"center\">".getAngka($r[urutanIndikator])."</td>
					<td align=\"center\">$statusIndikator</td>";
				if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete']) || isset($menuAccess[$s]["add"])){
					echo "<td align=\"center\">";
						if(isset($menuAccess[$s]["add"])) echo "<a href=\"#Add\" title=\"Add Data\" class=\"add\"  onclick=\"openBox('popup.php?par[mode]=addIndikator&par[indukIndikator]=$r[kodeIndikator]".getPar($par,"mode,indukIndikator")."',875,450);\"><span>Edit</span></a>";			
						if(isset($menuAccess[$s]["edit"])) echo "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editIndikator&par[kodeIndikator]=$r[kodeIndikator]".getPar($par,"mode,kodeIndikator,indukIndikator")."',875,450);\"><span>Edit</span></a>";				
						if(isset($menuAccess[$s]["delete"])) echo "<a href=\"?par[mode]=delIndikator&par[kodeIndikator]=$r[kodeIndikator]".getPar($par,"mode,kodeIndikator,indukIndikator")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";	
					echo "</td>";
				}
				echo "</tr>";
				
				$sql_="select * from pen_setting_prespektif_indikator where idPrespektif='$par[idPrespektif]' and indukIndikator='$r[kodeIndikator]'";
				$res_=db($sql_);
				$num=1;
				while($r_=mysql_fetch_array($res_)){
					$statusIndikator_ = $r_[statusIndikator] == "t" ?
					"<img src=\"styles/images/t.png\" >" : "<img src=\"styles/images/f.png\" >";
					echo "<tr>
						<td>&nbsp;</td>
						<td>".strtolower(numToAlpha($num)).". $r_[uraianIndikator]</td>
						<td align=\"center\">".getAngka($r_[urutanIndikator])."</td>
						<td align=\"center\">$statusIndikator</td>";
					if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])){
						echo "<td align=\"center\">";							
							if(isset($menuAccess[$s]["edit"])) echo "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=editIndikator&par[kodeIndikator]=$r_[kodeIndikator]".getPar($par,"mode,kodeIndikator,indukIndikator")."',875,450);\"><span>Edit</span></a>";				
							if(isset($menuAccess[$s]["delete"])) echo "<a href=\"?par[mode]=delIndikator&par[kodeIndikator]=$r_[kodeIndikator]".getPar($par,"mode,kodeIndikator,indukIndikator")."\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";	
						echo "</td>";
					}
					echo "</tr>";
					$num++;
				}
				
				$no++;
			}
			
			if($no == 1)
				echo "<tr>
					<td colspan=\"5\">&nbsp;</td>
				</tr>";
			
		?>
		</tbody>
	</table>
</div>