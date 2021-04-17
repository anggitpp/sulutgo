<?php
if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

function getContent($par)
{
	global $s, $inp, $par, $_submit, $menuAccess;

	switch($par[mode])
	{   
		default:
		  $text = lihat();
		break;
	}
	return $text;
}

function lihat()
{
    global $s,$inp,$par,$arrTitle,$menuAccess,$cID,$cTipeAkses,$cKodeAkses, $getBulan,$cIdPegawai;
    
    $par[idPegawai] = empty($_SESSION['idPegawai']) ? $cIdPegawai : $_SESSION['idPegawai'];
    
    $sql="select * from dta_pegawai where id='".$par[idPegawai]."'";
    $res=db($sql);
    $r=mysql_fetch_array($res);
    
    $text .="
    
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
		".getBread()."
	</div>
    
    <div id=\"contentwrapper\" class=\"contentwrapper\">
        <fieldset>
            <legend>Personal</legend>
            
            <form id=\"form\" class=\"stdform\">
                <table width=\"100%\">
                    <tr>
                        <td width=\"200px\" style=\"vertical-align:top;\">
                            <img width=\"190\" src=\"".($r["pic_filename"] == "" ? "files/emp/pic/nophoto.jpg" : "images/foto/".$r["pic_filename"])."\">
                        </td>
                        <td style=\"vertical-align:top;\">
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Nama Pegawai</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    $r[name] &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">NPP</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    $r[reg_no] &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Tanggal Lahir</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    ".getTanggal($r['birth_date'])." &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Gender</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    ".($r[gender] == "M" ? "Laki-laki" : "Perempuan")." &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Direktorat</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    - &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Divisi</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    - &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Jabatan</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    $r[pos_name] &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Pangkat</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    ".getField("select namaData from mst_data where kodeData='".$r[rank]."'")." &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Grade</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    ".getField("select namaData from mst_data where kodeData='".$r[grade]."'")." &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Skala</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    - &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Lokasi Kerja</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    - &nbsp;
                                </span>
            				</p>
                            <p>
            					<label class=\"l-input-large\" style=\"width:153px; text-align:left; padding-left:10px;\">Keterangan</label>
            					<span class=\"field\" style=\"margin-left:100px;\">
                                    - &nbsp;
                                </span>
            				</p>
                            
                        </td>
                    </tr>
                </table>
            </form>
            
        </fieldset>
        
        <div class=\"widgetbox\">
    		<div class=\"title\" style=\"margin-bottom: 10px\"><h3>RIWAYAT POSISI PENILAIAN</h3></div>
        </div>
        
    	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">
    		<thead>
    			<tr>
    				<th width=\"20\"  style=\"vertical-align: middle\">NO</th>
    				<th width=\"100\" style=\"vertical-align: middle\">TAHUN</th>
                    <th width=\"200\" style=\"vertical-align: middle\">Periode</th>
    				<th width=\"200\" style=\"vertical-align: middle\">TIPE PENILAIAN</th>
    				<th width=\"200\" style=\"vertical-align: middle\">KODE PENILAIAN</th>	
    			</tr>
    		</thead>
    		<tbody>
            ";
            $sql = "SELECT * FROM pen_pegawai t1 left join pen_tipe t2 on (t1.tipePenilaian=t2.kodeTipe) left join pen_setting_kode t3 on t1.kodePenilaian = t3.idKode where t1.idPegawai='".$par[idPegawai]."'
                	ORDER BY t1.tahunPenilaian";
                	$ret = array();
                	$res = db($sql);
                    $no=0;
                	while($r = mysql_fetch_array($res))
                    {
                        $no++;
                		$r[tahunPenilaian] = getField("select namaData from mst_data where kodeData = $r[tahunPenilaian]");
                        $r[periode] = getTanggal($r['periodeStart'])." s/d ".getTanggal($r['periodeEnd']);
                        $text.="
                        <tr>
                            <td style=\"text-align: center\">$no</td>
                            <td style=\"text-align: center\">$r[tahunPenilaian]</td>
                            <td style=\"text-align: center\">$r[periode]</td>
                            <td style=\"text-align: left\">$r[namaTipe]</td>
                            <td style=\"text-align: left\">$r[subKode]</td>
                        </tr>
                        ";
                	}
            $text.="
    		</tbody>
    	</table>
        
    </div>
    
    ";
    
    return $text;
}

?>