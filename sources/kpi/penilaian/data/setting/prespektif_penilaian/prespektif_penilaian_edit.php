<?php
global $s, $par, $menuAccess, $arrTitle;


if (isset($_POST["btnSimpan"])) {
	switch ($par[mode]) {
		case "add":
		insertData();
		die();
		break;

		case "edit":
		updateData();
		die();
		break;
	}
}

if($sData==1){
	header("Content-type: application/json");

	$sql = "SELECT * FROM pen_setting_aspek where kodeAspek='$par[kodeAspek]' and statusAspek='t'";
	$ret = array();
	$res = db($sql);
	while($r = mysql_fetch_array($res)){
		$ret[] = $r;
	}

	echo json_encode($ret);
	exit();
}


$arrStatus = array("t" => "Aktif", "f" => "Tidak Aktif");
$sql  = "SELECT * FROM pen_setting_prespektif WHERE idPrespektif = '$par[idPrespektif]'";
$res = db($sql);
$r = mysql_fetch_array($res);


$r[kodePrespektif] = empty($r[kodePrespektif]) ? $par[kodePrespektif] : $r[kodePrespektif];
$r[kodeAspek] = empty($r[kodeAspek]) ? $par[kodeAspek] : $r[kodeAspek];
// echo "SELECT idAspek, namaAspek FROM pen_setting_aspek WHERE statusAspek = 't' where kodeAspek = '$r[kodeAspek]'";

?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords($par[mode]." data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<fieldset>
				<legend> PRESPEKTIF INDIKATOR </legend>
    			<p>
    				<label class="l-input-small">Prespektif</label>
    				<div class="field">
    					<input type="text" class="mediuminput" id="inp[namaPrespektif]" name="inp[namaPrespektif]" value="<?= $r[namaPrespektif] ?>" style="width: 90%"/>
    				</div>
    			</p>
    			<p>
    				<label class="l-input-small">KPI</label>
    				<div class="field">
    					<textarea class="mediuminput" id="inp[kpiPrespektif]" name="inp[kpiPrespektif]" style="height:100px; width: 90%;"><?= $r[kpiPrespektif] ?></textarea>
    				</div>
    			</p>
    			<p>
    				<label class="l-input-small">Keterangan</label>
    				<div class="field">
    					<textarea class="mediuminput" id="inp[keteranganPrespektif]" name="inp[keteranganPrespektif]" style="height: 100px; width: 90%"><?= $r[keteranganPrespektif] ?></textarea>
    				</div>
    			</p>
    			<p>
    				<label class="l-input-small">Kode</label>
    				<div class="field">
    					<input type="text" class="mediuminput" id="inp[kodeNama]" name="inp[kodeNama]" value="<?= $r[kodeNama] ?>" style="width: 80px"/>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small">Bobot</label>
    				<div class="field">
    					<input type="text" class="mediuminput" id="inp[bobot]" name="inp[bobot]" value="<?= $r[bobot] ?>" style="width: 80px"/> %
    				</div>
    			</p>
                <p>
    				<label class="l-input-small">Urutan</label>
    				<div class="field">
    					<input type="text" class="mediuminput" id="inp[urut]" name="inp[urut]" value="<?= $r[urut] ?>" style="width: 30px"/>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small">Status</label>
    				<div class="field fradio">
    					<?php
    					foreach($arrStatus as $key => $value){
    						$checked = $r[status] == $key ? "checked=\"checked\"" : "";
    						?>
    						<input type="radio" <?= $checked ?> name="inp[status]" id="<?= $key ?>" value="<?= $key ?>" > <?= $value ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    						<?php 
    					} 
    					?>
    				</div>
    			</p>
    			<p style="position:absolute;top:10px;right:20px;">
    				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
    				<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
    			</p>
			</fieldset>
		</form>
	</div>
</div>
<script>
	function setPrespektif(){
		kodePrespektif=document.getElementById("inp[kodePrespektif]");
		
		var xmlHttp = getXMLHttp();
		xmlHttp.onreadystatechange = function(){
			if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
				if(xmlHttp.responseText){						
					response = xmlHttp.responseText.trim();
					if(response){
						var data = JSON.parse(response);
						
						for(var i=kodePrespektif.options.length-1;i>=0;i--){
							kodePrespektif.remove(i);
						}
												
						for (i = 0; i < data.length; ++i){					
							var opt = document.createElement("OPTION");					
							opt.value = data[i]["idAspek"]
							opt.text = data[i]["namaAspek"];		
							if(opt.value) kodePrespektif.options.add(opt);
						}
					}												
				}
			}
		}
		xmlHttp.open("GET", "ajax.php?<?= getPar($par, "kodeAspek"); ?>&sData=1&par[kodeAspek]=" + jQuery("#inp\\[kodeAspek\\]").val(), true);
		xmlHttp.send(null);
		return false;
	}
</script>
<?php
function insertData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$nextId = getField("SELECT idPrespektif FROM pen_setting_prespektif ORDER BY idPrespektif DESC LIMIT 1")+1;
	$sql="INSERT INTO pen_setting_prespektif (idPrespektif, idTipe, idKode, idAspek, kodeNama, bobot, namaPrespektif, kpiPrespektif, keteranganPrespektif, status, urut, createBy, createDate) VALUES ('$nextId', '$par[idTipe]', '$par[idKode]', '$par[idAspek]', '$inp[kodeNama]', '$inp[bobot]', '$inp[namaPrespektif]', '$inp[kpiPrespektif]', '$inp[keteranganPrespektif]', '$inp[status]', '$inp[urut]', '$cUsername', '".date("Y-m-d H:i:s")."');";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox(); 
        alert('Data berhasil disimpan!');
		parent.window.location='index.php?".getPar($par, "mode,idPrespektif")."';
	</script>";
}

function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();

	$sql="UPDATE pen_setting_prespektif SET kodeNama = '$inp[kodeNama]', bobot = '$inp[bobot]', namaPrespektif = '$inp[namaPrespektif]', kpiPrespektif = '$inp[kpiPrespektif]', keteranganPrespektif = '$inp[keteranganPrespektif]', status = '$inp[status]', urut = '$inp[urut]', updateBy = '$cUsername', updateDate = '".date("Y-m-d H:i:s")."' WHERE idPrespektif = '$par[idPrespektif]'";
	// echo $sql;
	db($sql);

	echo "
	<script>
		closeBox();
        alert('Data berhasil disimpan!');
		parent.window.location='index.php?".getPar($par, "mode,idPrespektif")."';
	</script>";
}