<?php 
global $s, $par, $menuAccess, $arrTitle, $json;

if(empty($par['idTipe'])) $par['idTipe'] = getField("select kodeTipe from pen_tipe where kodeTipe in (SELECT DISTINCT(kodeTipe) FROM pen_setting_kode) order by kodeTipe asc limit 1");
if(empty($par['idKode'])) $par['idKode'] = getField("select idKode from pen_setting_kode where kodeTipe = $par[idTipe] AND statusKode = 't' order by idKode ASC limit 1");


?>
<div class="pageheader">
	<h1 class="pagetitle"><?= $arrTitle[$s] ?></h1>
	<?= getBread() ?>								
</div>
<div class="contentwrapper">
    <div id="pos_l" style="float:left;">
        <form id="formFilter" method="post"  action="?<?= getPar($par, "mode, idTipe, idKode") ?>" onsubmit="return validation(document.form);">
            <?= comboData("SELECT kodeTipe, namaTipe FROM pen_tipe WHERE statusTipe='t' order by namaTipe asc", "kodeTipe", "namaTipe", "par[idTipe]", "", $par[idTipe], "onchange=\"getKode(this.value,'".getPar($par,"mode")."');\"", "250px", ""); ?>
            <?= comboData("SELECT idKode, subKode FROM pen_setting_kode WHERE kodeTipe = '$par[idTipe]' AND statusKode = 't' order by subKode ASC", "idKode", "subKode", "par[idKode]", "", $par[idKode], "", "250px", ""); ?>
            <input type="submit" value="GO" class="btn btn_search btn-small">
        </form>
    </div>
    
    <div id="pos_r" style="float:right; margin-top: 15px;">
        <a href="#" onclick="openBox('popup.php?par[mode]=dlg<?= getPar($par, "mode,filterTanggal,kodePrespektif") ?>', 600,250)" class="btn btn1 btn_inboxi"><span>Ambil Data</span></a>
    </div>
    
    <br clear="all" />
    <br />
    
    <form class="stdform" style="margin-top: -15px;">
        <table class="stdtable stdtablequick" cellpadding="0" cellspacing="0" border="0">
    		<thead>
    			<tr>
                    <th width="20" style="vertical-align: middle">No</th>
    				<th width="250" style="vertical-align: middle">Prespektif</th>
                    <th width="100" style="vertical-align: middle">Kode</th>
    				<th style="vertical-align: middle">KPI</th>
    				<th width="100" style="vertical-align: middle">Indikator</th>
                    <th width="80" style="vertical-align: middle">KONTROL</th>
    			</tr>
    		</thead>
            <tbody>
                <?php
                $getAspek = queryAssoc("select * from pen_setting_aspek order by urutanAspek");
                if($getAspek)
                {
                    foreach($getAspek as $asp)
                    {
                        ?>
                        <tr style="background-color:#d9d9d9;">
                            <td colspan="5"><strong><?= $asp[namaAspek] ?></strong></td>
                            <td style="text-align: center;">
                                <a href="#Add" title="Tambah Data" class="add" onclick="openBox('popup.php?par[mode]=add&par[idAspek]=<?= $asp[idAspek].getPar($par,"mode,idAspek") ?>',800,600);"><span>Add</span></a>
                            </td>
                        </tr>
                        <?php
                        $getPrespektif = queryAssoc("select * from pen_setting_prespektif where idAspek = $asp[idAspek] and idTipe = $par[idTipe] and idKode = $par[idKode] order by urut");
                        if($getPrespektif)
                        {
                            $no=0;
                            foreach($getPrespektif as $prs)
                            {
                                $no++;
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?= $no ?></td>
                                    <td style="text-align: left;"><?= $prs[namaPrespektif] ?></td>
                                    <td style="text-align: center;"><?= $prs[kodeNama] ?></td>
                                    <td style="text-align: left;"><?= $prs[kpiPrespektif] ?></td>
                                    <td style="text-align: center;">
                                        <a href="?par[mode]=det&par[idAspek]=<?= $asp[idAspek] ?>&par[idPrespektif]=<?= $prs[idPrespektif].getPar($par,"mode,idPrespektif") ?>"><?= getField("select count(kodeIndikator) from pen_setting_prespektif_indikator where idPrespektif=$prs[idPrespektif]") ?></a>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="#Edit" title="Edit Data" class="edit" onclick="openBox('popup.php?par[mode]=edit&par[idPrespektif]=<?= $prs[idPrespektif].getPar($par,"mode,idPrespektif") ?>',800,600);"><span>Edit</span></a>
                                        <?php
                                        $cekData = getField("select idSasaran from pen_sasaran_obyektif where idPrespektif = $prs[idPrespektif] limit 1");
                                        if(empty($cekData))
                                        {
                                            ?>
                                            <a href="?par[mode]=del&par[idPrespektif]=<?= $prs[idPrespektif].getPar($par,"mode,idPrespektif") ?>" class="delete" onclick="return confirm('are you sure to delete data ?')"><span>Delete</span></a>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                }
                ?>
            </tbody>
       </table>
   </form>
</div>
<script>
function getKode(idTipe, getPar)
{
    parameter=document.getElementById("par[idKode]");
    for(var i=parameter.options.length-1;i>=0;i--){
		parameter.remove(i);
	}
    
    jQuery.post("ajax.php?par[mode]=getKode&par[idTipeJS]="+idTipe+getPar+"").done(function(result)
    {
        data = jQuery.parseJSON(result);
        for(var i=0; i<data.length; i++){ jQuery("#par\\[idKode\\]").append('<option value="'+data[i].idKode+'">'+data[i].subKode+'</option>'); }
        //jQuery("#combo5").trigger("chosen:updated");
    });
}

jQuery("#btnAmbil").live("click", function(e){
		e.preventDefault();
		openBox('popup.php?par[mode]=dlg&par[kodePrespektif]=' + kodePrespektif + '<?= getPar($par, "mode,filterTanggal,kodePrespektif") ?>', 600,250);
	});
</script>