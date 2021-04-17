<?php
global $s, $par, $menuAccess, $arrTitle;

if (isset($_POST["btnSimpan"])) {
	updateData();
	die();
}

$sql = "SELECT * FROM pen_pegawai WHERE id = '$par[id]'";
$res = db($sql);
$r = mysql_fetch_array($res);

$r[tahunPenilaian] = empty($r[tahunPenilaian]) ? getField("SELECT kodeData FROM mst_data WHERE kodeCategory='PRDT' AND namaData='".date('Y')."'") : $r[tahunPenilaian];

$namaPegawai = getField("SELECT name FROM dta_pegawai WHERE id = '$r[idPegawai]'");
$namaPegawai = strtolower($namaPegawai);
$namaPegawai = ucwords($namaPegawai);

setValidation("is_null","inp[tipePenilaian]","anda harus mengisi Tipe Penilaian");
setValidation("is_null","inp[kodePenilaian]","anda harus mengisi Kode Penilaian");
setValidation("is_null","inp[bulanPenilaian]","anda harus mengisi Bulan Penilaian");
setValidation("is_null","inp[TahunPenilaian]","anda harus mengisi Tahun Penilaian");
setValidation("is_null","inp[periodeStart]","anda harus mengisi Periode");
setValidation("is_null","inp[periodeEnd]","anda harus mengisi Periode");
echo getValidation();
?>
<div class="contentpopup" style="margin-left: 0px">
	<div class="pageheader">
		<h1 class="pagetitle">Tipe Penilaian &raquo; <?= $namaPegawai ?></h1>
		<div style="margin-top: 10px">
			<?php echo getBread(ucwords($par[mode]." data")) ?>
		</div>
		<span class="pagedesc">&nbsp;</span>
	</div>
	<div id="contentwrapper" class="contentwrapper">
		<form id="form" name="form" method="post" class="stdform" action="?<?= getPar($par) ?>" onsubmit="return validation(document.form);" enctype="multipart/form-data">
			<fieldset>
    			<p>
    				<label class="l-input-small" style="width: 150px">Tipe Penilaian</label>
    				<div class="field">
    					<?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t'", "kodeTipe", "namaTipe", "inp[tipePenilaian]", " ", $r[tipePenilaian], "onchange=\"getKode('" . getPar($par, "mode,kodeTipe") . "');\"", "220px");?>
    				</div>
    			</p>
    			<p>
    				<label class="l-input-small" style="width: 150px">Kode Penilaian</label>
    				<div class="field">
    					<?= comboData("SELECT idKode, subKode FROM pen_setting_kode WHERE statusKode='t' AND kodeTipe = '$r[tipePenilaian]'", "idKode", "subKode", "inp[kodePenilaian]", " ", $r[kodePenilaian], "", "220px");?>
    				</div>
    			</p>
    			<p>
    				<label class="l-input-small" style="width: 150px">Tahun</label>
    				<div class="field">
                    <?= comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='PRDT'", "kodeData", "namaData", "inp[tahunPenilaian]", " ", $r[tahunPenilaian], "onchange=\"getBulan('" . getPar($par, "mode") . "');\"", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Periode</label>
    				<div class="field">
                        <input type="text" id="inp[periodeStart]" name="inp[periodeStart]" size="10" maxlength="10" value="<?= getTanggal($r[periodeStart]) ?>" class="vsmallinput hasDatePicker"/>
    				    &nbsp;s/d&nbsp;
                        <input type="text" id="inp[periodeEnd]" name="inp[periodeEnd]" size="10" maxlength="10" value="<?= getTanggal($r[periodeEnd]) ?>" class="vsmallinput hasDatePicker"/>
                    </div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Keterangan</label>
    				<div class="field" >
                        <textarea name="inp[keterangan]" id="inp[keterangan]"><?= $r[keterangan] ?></textarea>
    				</div>
    			</p>
            </fieldset>
            
            <br />
            
            <fieldset>
                <legend>Penilaian</legend>
    			<p>
    				<label class="l-input-small" style="width: 150px">Atasan Langsung</label>
    				<div class="field">
    					<?= comboData("select id, name from emp where id != $par[idPegawai] order by name asc", "id", "name", "inp[atasan_langsung]", " ", $r[atasan_langsung], "", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Atasan dari Atasan</label>
    				<div class="field">
    					<?= comboData("select id, name from emp where id != $par[idPegawai] order by name asc", "id", "name", "inp[atasan_dari_atasan]", " ", $r[atasan_dari_atasan], "", "220px");?>
    				</div>
    			</p>
            </fieldset>
            
            <br />
            
            <fieldset>
                <legend>Struktur Organisasi</legend>
    			<p>
    				<label class="l-input-small" style="width: 150px">Periode</label>
    				<div class="field">
    					<?= comboData("select idSperiode, periode from str_periode order by idSperiode asc", "idSperiode", "periode", "inp[str_periode]", " ", $r[str_periode], "", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Kategori</label>
    				<div class="field">
    					<?= comboData("SELECT kodeData, namaData FROM str_org WHERE kodeCategory='X04' order by namaData asc", "kodeData", "namaData", "inp[str_kategori]", " ", $r[str_kategori], "onchange=\"getLvl_1('" . getPar($par, "mode") . "');\"", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Level 1</label>
    				<div class="field">
    					<?= comboData("SELECT kodeData, namaData FROM str_org WHERE kodeInduk='$r[str_kategori]' order by namaData asc", "kodeData", "namaData", "inp[str_lv1]", " ", $r[str_lv1], "onchange=\"getLvl_2('" . getPar($par, "mode") . "');\"", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Level 2</label>
    				<div class="field">
    					<?= comboData("SELECT kodeData, namaData FROM str_org WHERE kodeInduk='$r[str_lv1]' order by namaData asc", "kodeData", "namaData", "inp[str_lv2]", " ", $r[str_lv2], "onchange=\"getLvl_3('" . getPar($par, "mode") . "');\"", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Level 3</label>
    				<div class="field">
    					<?= comboData("SELECT kodeData, namaData FROM str_org WHERE kodeInduk='$r[str_lv2]' order by namaData asc", "kodeData", "namaData", "inp[str_lv3]", " ", $r[str_lv3], "onchange=\"getLvl_4('" . getPar($par, "mode") . "');\"", "220px");?>
    				</div>
    			</p>
                <p>
    				<label class="l-input-small" style="width: 150px">Level 4</label>
    				<div class="field">
    					<?= comboData("SELECT kodeData, namaData FROM str_org WHERE kodeInduk='$r[str_lv3]' order by namaData asc", "kodeData", "namaData", "inp[str_lv4]", " ", $r[str_lv4], "", "220px");?>
    				</div>
    			</p>
                
            </fieldset>
            
			<p style="position:absolute;top:10px;right: 5px;">
				<input type="submit" class="submit radius2" name="btnSimpan" value="Simpan"/>
				<input type="button" class="cancel radius2" value="Batal" onclick="closeBox();"/>
			</p>
		</form>
	</div>
</div>
<?php
function updateData(){
	global $s,$inp,$par,$detail,$cUsername;			
	repField();
	
	

	if($par[mode] == "edit"){
		$sql="update pen_pegawai set  tipePenilaian = '$inp[tipePenilaian]',kodePenilaian = '$inp[kodePenilaian]',tahunPenilaian = '$inp[tahunPenilaian]',bulanPenilaian = '$inp[bulanPenilaian]', keterangan= '$inp[keterangan]', periodeStart='".setTanggal($inp[periodeStart])."',periodeEnd='".setTanggal($inp[periodeEnd])."', atasan_langsung = '$inp[atasan_langsung]', atasan_dari_atasan = '$inp[atasan_dari_atasan]', str_periode = '$inp[str_periode]', str_kategori = '$inp[str_kategori]', str_lv1 = '$inp[str_lv1]', str_lv2 = '$inp[str_lv2]', str_lv3 = '$inp[str_lv3]', str_lv4 = '$inp[str_lv4]', updateBy='$cUsername', updateDate='".date('Y-m-d H:i:s')."' where id = '$par[id]'";
	}else{
		$nextId = getField("SELECT id FROM pen_pegawai ORDER BY id DESC LIMIT 1")+1;
		$sql = "INSERT INTO pen_pegawai (id, idPegawai, tipePenilaian, kodePenilaian, tahunPenilaian, bulanPenilaian, keterangan, periodeStart, periodeEnd, atasan_langsung, atasan_dari_atasan, str_periode, str_kategori, str_lv1, str_lv2, str_lv3, str_lv4, createBy, createDate) VALUES ('$nextId', '$par[idPegawai]', '$inp[tipePenilaian]','$inp[kodePenilaian]', '$inp[tahunPenilaian]','$inp[bulanPenilaian]','$inp[keterangan]','".setTanggal($inp[periodeStart])."','".setTanggal($inp[periodeEnd])."', '$inp[atasan_langsung]', '$inp[atasan_dari_atasan]', '$inp[str_periode]', '$inp[str_kategori]', '$inp[str_lv1]', '$inp[str_lv2]', '$inp[str_lv3]', '$inp[str_lv4]', '$cUsername', '".date("Y-m-d H:i:s")."')";
	}
	
	db($sql);

	echo "
	<script>
		closeBox(); alert('Data berhasil disimpan'); reloadPage();
	</script>";
}
?>
<script type="text/javascript">

function getLvl_1(getPar){
	str_kategori = document.getElementById('inp[str_kategori]');
	str_lv1 = document.getElementById('inp[str_lv1]');
    str_lv2 = document.getElementById('inp[str_lv2]');
    str_lv3 = document.getElementById('inp[str_lv3]');
    str_lv4 = document.getElementById('inp[str_lv4]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=str_lv1.options.length-1; i>=0; i--){
				str_lv1.remove(i);
			}
            for(var i=str_lv2.options.length-1; i>=0; i--){
				str_lv2.remove(i);
			}
            for(var i=str_lv3.options.length-1; i>=0; i--){
				str_lv3.remove(i);
			}
            for(var i=str_lv4.options.length-1; i>=0; i--){
				str_lv4.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				str_lv1.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) str_lv1.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=getLvl_1&par[str_kategori]="+ str_kategori.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getLvl_2(getPar){
	str_lv1 = document.getElementById('inp[str_lv1]');
    str_lv2 = document.getElementById('inp[str_lv2]');
    str_lv3 = document.getElementById('inp[str_lv3]');
    str_lv4 = document.getElementById('inp[str_lv4]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
            for(var i=str_lv2.options.length-1; i>=0; i--){
				str_lv2.remove(i);
			}
            for(var i=str_lv3.options.length-1; i>=0; i--){
				str_lv3.remove(i);
			}
            for(var i=str_lv4.options.length-1; i>=0; i--){
				str_lv4.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				str_lv2.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) str_lv2.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=getLvl_2&par[str_lv1]="+ str_lv1.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getLvl_3(getPar){
    str_lv2 = document.getElementById('inp[str_lv2]');
    str_lv3 = document.getElementById('inp[str_lv3]');
    str_lv4 = document.getElementById('inp[str_lv4]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
            for(var i=str_lv3.options.length-1; i>=0; i--){
				str_lv3.remove(i);
			}
            for(var i=str_lv4.options.length-1; i>=0; i--){
				str_lv4.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				str_lv3.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) str_lv3.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=getLvl_3&par[str_lv2]="+ str_lv2.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getLvl_4(getPar){
    str_lv3 = document.getElementById('inp[str_lv3]');
    str_lv4 = document.getElementById('inp[str_lv4]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){		
            for(var i=str_lv4.options.length-1; i>=0; i--){
				str_lv4.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				str_lv4.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) str_lv4.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=getLvl_4&par[str_lv3]="+ str_lv3.value + getPar, true);
	xmlHttp.send(null);
	return false;
}

function getKode(getPar){
	tipePenilaian = document.getElementById('inp[tipePenilaian]');
	kodePenilaian = document.getElementById('inp[kodePenilaian]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=kodePenilaian.options.length-1; i>=0; i--){
				kodePenilaian.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				kodePenilaian.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) kodePenilaian.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=kode&par[tipePenilaian]="+ tipePenilaian.value + getPar, true);
	console.log("ajax.php?par[mode]=kode&par[tipePenilaian]="+ tipePenilaian.value + getPar);
	xmlHttp.send(null);
	return false;
}

function getBulan(getPar){
	tahunPenilaian = document.getElementById('inp[tahunPenilaian]');
	bulanPenilaian = document.getElementById('inp[bulanPenilaian]');
	var xmlHttp = getXMLHttp();		
	xmlHttp.onreadystatechange = function(){	
		if(xmlHttp.readyState == 4 && xmlHttp.status==200){			
			for(var i=bulanPenilaian.options.length-1; i>=0; i--){
				bulanPenilaian.remove(i);
			}
			if(xmlHttp.responseText){
				var arr = xmlHttp.responseText.split("\n");						
				var opt = document.createElement("OPTION");
				opt.value = "";		
				opt.text = "";
				bulanPenilaian.options.add(opt);
				for(var i=0; i<arr.length; i++){							
					var opt = document.createElement("OPTION");
					var val = arr[i].split("\t");
					opt.value = val[0];		 
					opt.text = val[1];
					if(opt.value) bulanPenilaian.options.add(opt);
				}
			}
		}
	}
	xmlHttp.open("GET", "ajax.php?par[mode]=bulan&par[tahunPenilaian]="+ tahunPenilaian.value + getPar, true);
	console.log("ajax.php?par[mode]=bulan&par[tahunPenilaian]="+ tahunPenilaian.value + getPar);
	xmlHttp.send(null);
	return false;
}


</script>