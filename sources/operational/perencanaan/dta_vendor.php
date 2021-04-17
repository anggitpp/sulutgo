<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fFile = "images/vendor/";
$dFile = "files/vendor/";
$fFile2 = "files/export/";

$path_import = "files/imports/";
$file_log = "files/logs/data_vendor.log";
$file_template = "files/templates/template-vendor.xlsx";

function getContent($par)
{
    global $s, $_submit, $menuAccess, $fFile, $dFile, $cUsername;

    switch ($par['mode']) {

        case "lst":
            $text = lData();
            break;

        case "cek":
            $text = cek();
            break;

        case "kta":
            $text = kota();
            break;

        case "geo":
            $text = getField("select namaData from mst_data where kodeData='$par[kodeKota]'");
            break;

        case "delProduk":
            if (isset($menuAccess[$s]["delete"])) $text = hapusProduk(); else $text = lihat();
            break;

        case "editProduk":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formProduk() : ubahProduk(); else $text = lihat();
            break;

        case "addProduk":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formProduk() : tambahProduk(); else $text = lihat();
            break;

        case "delBank":
            if (isset($menuAccess[$s]["delete"])) $text = hapusBank(); else $text = lihat();
            break;

        case "editBank":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formBank() : ubahBank(); else $text = lihat();
            break;

        case "addBank":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formBank() : tambahBank(); else $text = lihat();
            break;

        case "delContact":
            if (isset($menuAccess[$s]["delete"])) $text = hapusContact(); else $text = lihat();
            break;

        case "editContact":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formContact() : ubahContact(); else $text = lihat();
            break;

        case "addContact":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formContact() : tambahContact(); else $text = lihat();
            break;

        case "delAddress":
            if (isset($menuAccess[$s]["delete"])) $text = hapusAddress(); else $text = lihat();
            break;

        case "editAddress":
            if (isset($menuAccess[$s]["edit"])) $text = empty($_submit) ? formAddress() : ubahAddress(); else $text = lihat();
            break;

        case "addAddress":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? formAddress() : tambahAddress(); else $text = lihat();
            break;

        case "update":
            if (isset($menuAccess[$s]["edit"])) $text = ubah("update");
            break;

        case "delNpwp":
            if (isset($menuAccess[$s]["edit"])) $text = hapusNpwp(); else $text = lihat();
            break;

        case "delId":
            if (isset($menuAccess[$s]["edit"])) $text = hapusId(); else $text = lihat();
            break;

        case "delTdp":
            if (isset($menuAccess[$s]["edit"])) $text = hapusTdp(); else $text = lihat();
            break;

        case "delSiup":
            if (isset($menuAccess[$s]["edit"])) $text = hapusSiup(); else $text = lihat();
            break;

        case "delLogo":
            if (isset($menuAccess[$s]["edit"])) $text = hapusLogo(); else $text = lihat();
            break;

        case "del":
            if (isset($menuAccess[$s]["delete"])) $text = hapus(); else $text = lihat();
            break;

        case "edit":
            if (isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form() : ubah(); else $text = lihat();
            break;

        case "add":
            $text = isset($menuAccess[$s]["add"]) ? tambah() : lihat();
            break;

        case "det":
            $text = detail();
            break;

        default:
            $sql = "select * from dta_vendor where namaVendor='' and createBy='$cUsername'";
            $res = db($sql);
            while ($r = mysql_fetch_array($res)) {

                if (file_exists($fFile . $r[logoVendor]) and $r[logoVendor] != "") unlink($fFile . $r[logoVendor]);

                $sql_ = "select * from dta_vendor_identity where kodeVendor='$r[kodeVendor]'";
                $res_ = db($sql_);
                $r_ = mysql_fetch_array($res_);
                if (file_exists($dFile . $r_[siupIdentity_file]) and $r_[siupIdentity_file] != "") unlink($dFile . $r_[siupIdentity_file]);
                if (file_exists($dFile . $r_[tdpIdentity_file]) and $r_[tdpIdentity_file] != "") unlink($dFile . $r_[tdpIdentity_file]);
                if (file_exists($dFile . $r_[idIdentity_file]) and $r_[idIdentity_file] != "") unlink($dFile . $r_[idIdentity_file]);
                if (file_exists($dFile . $r_[npwpIdentity_file]) and $r_[npwpIdentity_file] != "") unlink($dFile . $r_[npwpIdentity_file]);

                db("delete from dta_vendor where kodeVendor='$r[kodeVendor]'");
                db("delete from dta_vendor_address where kodeVendor='$r[kodeVendor]'");
                db("delete from dta_vendor_info where kodeVendor='$r[kodeVendor]'");
                db("delete from dta_vendor_identity where kodeVendor='$r[kodeVendor]'");
                db("delete from dta_vendor_contact where kodeVendor='$r[kodeVendor]'");
                db("delete from dta_vendor_bank where kodeVendor='$r[kodeVendor]'");
                db("delete from dta_vendor_produk where kodeVendor='$r[kodeVendor]'");
            }

            $text = lihat();
            break;

        case "import":
            formImport("Import Vendor");
            break;

        case "decode":
            echo uploadXLS();
            break;

        case "insert_import":
            if (isset($menuAccess[$s]["add"]))
                insertByImport();
            else
                echo "Membutuhkan akses!";
            break;

    }

    return $text;
}

function lihat()
{
    global $s, $par, $arrTitle, $menuAccess;

    $text = "
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">$arrTitle[$s]</h1>
		" . getBread() . "
		<span class=\"pagedesc\">&nbsp;</span>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
				<p>
					<input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"$_GET[fSearch]\" style=\"width:200px;\"/> 
					<input type=\"button\" id=\"sFilter\" value=\"+\" class=\"btn btn_search btn-small\" onclick=\"showFilter()\" />
					<input type=\"button\" style=\"display:none;\" id=\"hFilter\" value=\"-\" class=\"btn btn_search btn-small\" onclick=\"hideFilter()\" />  
				</p>
			</div>
			<div id=\"pos_r\">";

    if (isset($menuAccess[$s]["add"]))
        $text .= "<a id=\"import\" class=\"btn btn1 btn_inboxi\"><span>Import</span></a>";

    $text .= "&nbsp;<a href=\"#\" id=\"btnExport\" class=\"btn btn1 btn_inboxi\"><span>Export</span></a>";

    if (isset($menuAccess[$s]["add"]))
        $text .= "&emsp;<a href=\"?par[mode]=add" . getPar($par, "mode, kodeVendor") . "\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>";

    $text .= "</div>
			</form>
		
			<fieldset id=\"form_filter\" style=\"display:none; clear:both;\">
				<form id=\"form\" class=\"stdform\">
					<table width=\"100%\">
						<tr>
							<td width=\"50%\">
								<p>
									<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Kategori Vendor</label>
									<div class=\"field\" style=\"margin-left:200px;\">
										" . comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='MKV' and statusData='t' order by namaData", "kodeData", "namaData", "bSearch", "All Kategori Vendor", "$bSearch", "", "220px", "chosen-select", "") . "
										<style>
                                         #bSearch_chosen{min-width:250px;}
										</style>
									</div>
								</p>    
								<p>
									<label class=\"l-input-medium\" style=\"width:153px; text-align:left; padding-left:10px;\">Kategori Produk</label>
									<div class=\"field\" style=\"margin-left:200px;\">
										" . comboData("select * from dta_vendor_produk  order by kodeProduk", "kodeProduk", "pelatihanProduk", "mSearch", "All Kategori Produk", "$mSearch", "", "220px", "chosen-select", "") . "
									</div>
									<style>
                                         #mSearch_chosen{min-width:250px;}
									</style>
								</p>
								<p>
									<label class=\"l-input-small\" style=\"width:153px; text-align:left; padding-left:10px;\">Kota</label>
									<div class=\"field\" style=\"margin-left:200px;\">
										" . comboData("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory='S03' and statusData='t' order by namaData", "kodeData", "namaData", "aSearch", "All Kota", "$aSearch", "onchange=\"getSubKategori('" . getPar($par, "mode, aSearch") . "');\"", "220px", "chosen-select", "") . "
										<style>
                                        #aSearch_chosen{min-width:250px;}
										</style>
									</div>
								</p>
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
			
			<br clear=\"all\" />
			
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th width=\"100\">Nomor</th>
						<th>Nama Vendor</th>
						<th width=\"150\">Alias</th>
						<th width=\"150\">Kota</th>
						<th width=\"150\">Telepon</th>
						<th width=\"50\">Status</th>
						<th width=\"75\">Kontrol</th>
					</tr>
				</thead>
				<tbody>	
				</tbody>
			</table>
			
		</form>
	</div>
	<script>
		
        function showFilter() {
            jQuery('#form_filter').show(1)
            jQuery('#sFilter').hide(1)
            jQuery('#hFilter').show(1)
        }
        
        function hideFilter() {
            jQuery('#form_filter').hide(1)
            jQuery('#sFilter').show(1)
            jQuery('#hFilter').hide(1)
        }
        
        jQuery(\"#btnExport\").live('click', function(e) {
			e.preventDefault()
			window.location.href=\"?par[mode]=xls\"+\"" . getPar($par, "mode") . "\"+\"&fSearch=\"+jQuery(\"#fSearch\").val()
		})
        
        jQuery(\"#import\").click(function () {
            openBox(`popup.php?par[mode]=import" . getPar($par, "mode") . "`, 700, 250)
        })
        
    </script>";

    $cols = 8;
    $text .= table($cols, array($cols, ($cols - 1)));

    if ($par['mode'] == "xls") {
        xls();
        $text .= "<iframe src=\"download.php?d=exp&f=exp-" . $arrTitle[$s] . ".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
    }

    return $text;
}

function lData()
{

    global $par;

    if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1')
        $sLimit = "limit " . intval($_GET['iDisplayStart']) . ", " . intval($_GET['iDisplayLength']);

    $sWhere = " WHERE t1.namaVendor!=''";

    if (!empty($_GET['fSearch']))
        $sWhere .= " and (				
		lower(teleponVendor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'	
		or lower(aliasVendor) like '%" . mysql_real_escape_string(strtolower($_GET['fSearch'])) . "%'
		)";

    if (!empty($_GET['bSearch']))
        $sWhere .= " and t1.kategoriVendor = '$_GET[bSearch]'";
    /*
            if (!empty($_GET['aSearch']))
                $sWhere.= " and t1.kodeKota = '$_GET[aSearch]'";*/

    $arrOrder = array(
        "aliasVendor",
        "aliasVendor",
        "aliasVendor",
        "aliasVendor"
    );

    $orderBy = $arrOrder["" . $_GET[iSortCol_0] . ""] . " " . $_GET[sSortDir_0];
    $sql = "select t1.*, t2.namaData from dta_vendor t1 left join mst_data t2 on (t1.kodeKota=t2.kodeData) $sWhere order by $orderBy $sLimit";

    $res = db($sql);
    $json = array(
        "iTotalRecords" => mysql_num_rows($res),
        "iTotalDisplayRecords" => getField("select count(*) from dta_vendor t1 left join mst_data t2 on (t1.kodeKota=t2.kodeData) $sWhere"),
        "aaData" => array(),
    );

    $no = intval($_GET['iDisplayStart']);
    while ($r = mysql_fetch_array($res)) {
        $no++;
        if ($r[statusVendor] == "p")
            $statusVendor = "<img src=\"styles/images/p.png\" title='Prospect'>";
        else if ($r[statusVendor] == "t")
            $statusVendor = "<img src=\"styles/images/t.png\" title='Active'>";
        else
            $statusVendor = "<img src=\"styles/images/f.png\" title='Not Active'>";

        $namaVendor = empty($r[titleVendor]) ? $r[namaVendor] : $r[namaVendor] . ", " . $r[titleVendor];

        $data = array(
            "<div align=\"center\">$no</div>",
            "<div align=\"center\">$r[nomorVendor]</div>",
            "<div align=\"left\">$namaVendor</div>",
            "<div align=\"left\">$r[aliasVendor]</div>",
            "<div align=\"left\">$r[namaData]</div>",
            "<div align=\"left\">$r[teleponVendor]</div>",
            "<div align=\"center\">$statusVendor</div>",
            "
				<div align=\"center\">
					<a href=\"?par[mode]=det&par[kodeVendor]=$r[kodeVendor]" . getPar($par, "mode,kodeVendor") . "\" title=\"Detail Data\" class=\"detail\"><span>Detail</span></a>

					<a href=\"?par[mode]=edit&par[kodeVendor]=$r[kodeVendor]" . getPar($par, "mode,kodeVendor") . "\" title=\"Edit Data\" class=\"edit\"><span>Edit</span></a>

					<a href=\"?par[mode]=del&par[kodeVendor]=$r[kodeVendor]" . getPar($par, "mode,kodeVendor") . "\" onclick=\"return confirm('are you sure to delete data ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>
				</div>
				",
        );
        $json['aaData'][] = $data;
    }
    return json_encode($json);
}

function xls()
{

    global $s, $par, $arrTitle, $fFile2;

    $direktori = $fFile2;
    $namaFile = "exp-" . $arrTitle[$s] . ".xls";
    $judul = "" . $arrTitle[$s] . "";
    $field = array("no", "nama vendor", "alias", "kota", "telepon", "status");

    $filter = "where t1.namaVendor!=''";
    if ($par[jenis] == "Nama Vendor") {
        $filter .= " and (lower(t1.namaVendor) like '%" . strtolower($par[filter]) . "%' or lower(t1.aliasVendor) like '%" . strtolower($par[filter]) . "%')";
    } else if ($par[jenis] == "Alamat") {
        $filter .= " and lower(t1.alamatVendor) like '%" . strtolower($par[filter]) . "%'";
    } else if ($par[jenis] == "Telepon") {
        $filter .= " and lower(t1.teleponVendor) like '%" . strtolower($par[filter]) . "%'";
    } else if ($par[jenis] == "Kota") {
        $filter .= " and lower(t2.namaData) like '%" . strtolower($par[filter]) . "%'";
    } else {
        $filter .= " and (
		lower(t1.namaVendor) like '%" . strtolower($par[filter]) . "%'
		or lower(t1.aliasVendor) like '%" . strtolower($par[filter]) . "%'
		or lower(t1.teleponVendor) like '%" . strtolower($par[filter]) . "%'
		or lower(t2.namaData) like '%" . strtolower($par[filter]) . "%'				
		)";
    }

    $res = db("SELECT t1.*, t2.namaData FROM `dta_vendor` t1 left join mst_data t2 on (t1.kodeKota=t2.kodeData) $filter order by t1.namaVendor");
    $no = 0;

    while ($r = mysql_fetch_array($res)) {
        if ($r[statusVendor] == "p")
            $statusVendor = "Prospect'";
        else if ($r[statusVendor] == "t")
            $statusVendor = "Active";
        else
            $statusVendor = "Not Active";

        $namaVendor = empty($r[titleVendor]) ? $r[namaVendor] : $r[namaVendor] . ", " . $r[titleVendor];
        $no++;

        $data[] = array($no . "\t center",
            $namaVendor . "\t left",
            $r[aliasVendor] . "\t left",
            $r[namaData] . "\t left",
            $r[teleponVendor] . "\t left",
            $statusVendor . "\t center");
    }
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);
}

function cek()
{
    global $db, $inp, $par;
    if (getField("select nomorVendor from dta_vendor where nomorVendor='$inp[nomorVendor]' and kodeVendor!='$par[kodeVendor]'"))
        return "sorry, account no. \" $inp[nomorVendor] \" already exist";
}

function kota()
{
    global $db, $s, $id, $inp, $par, $arrParameter;
    $data = arrayQuery("select concat(kodeData, '\t', namaData) from mst_data where statusData='t' and kodeInduk='$par[kodePropinsi]' and kodeCategory='" . $arrParameter[4] . "' order by namaData");
    return implode("\n", $data);
}

function hapusProduk()
{
    global $db, $s, $inp, $par, $cUsername;
    $sql = "delete from dta_vendor_produk where kodeVendor='$par[kodeVendor]' and kodeProduk='$par[kodeProduk]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=6" . getPar($par, "mode,kodeProduk") . "';</script>";
}

function ubahProduk()
{
    global $db, $s, $inp, $par, $cUsername;
    repField(array("materiProduk"));
    $sql = "update dta_vendor_produk set kodeKategori='$inp[kodeProduk]', pelatihanProduk='$inp[pelatihanProduk]', durasiProduk='$inp[durasiProduk]', pesertaProduk='$inp[pesertaProduk]', biayaProduk='" . setAngka($inp[biayaProduk]) . "', materiProduk='$inp[materiProduk]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]' and kodeProduk='$par[kodeProduk]'";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN');window.parent.location='index.php?par[mode]=edit&tab=6" . getPar($par, "mode,kodeProduk") . "';</script>";
}

function tambahProduk()
{
    global $db, $s, $inp, $par, $detail, $cUsername;
    $kodeProduk = getField("select kodeProduk from dta_vendor_produk where kodeVendor='$par[kodeVendor]' order by kodeProduk desc limit 1") + 1;

    repField(array("materiProduk"));
    $sql = "insert into dta_vendor_produk (kodeVendor, kodeProduk, kodeKategori, pelatihanProduk, durasiProduk, pesertaProduk, biayaProduk, materiProduk, createBy, createTime) values ('$par[kodeVendor]', '$kodeProduk', '$inp[kodeKategori]', '$inp[pelatihanProduk]', '$inp[durasiProduk]', '$inp[pesertaProduk]', '" . setAngka($inp[biayaProduk]) . "', '$inp[materiProduk]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);

    echo "<script>alert('DATA BERHASIL DISIMPAN'); parent.window.location='index.php?par[mode]=edit&tab=6" . getPar($par, "mode, kodeProduk") . "';</script>";
}

function hapusBank()
{
    global $db, $s, $inp, $par, $cUsername;
    $sql = "delete from dta_vendor_bank where kodeVendor='$par[kodeVendor]' and kodeBank='$par[kodeBank]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=5" . getPar($par, "mode,kodeBank") . "';</script>";
}

function ubahBank()
{
    global $db, $s, $inp, $par, $cUsername;

    repField();
    $sql = "update dta_vendor_bank set namaBank='$inp[namaBank]', rekeningBank='$inp[rekeningBank]', pemilikBank='$inp[pemilikBank]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]' and kodeBank='$par[kodeBank]'";
    db($sql);
    echo "<script>alert('DATA BERHASIL DISIMPAN');window.parent.location='index.php?par[mode]=edit&tab=5" . getPar($par, "mode,kodeBank") . "';</script>";
}

function tambahBank()
{
    global $db, $s, $inp, $par, $cUsername;
    $kodeBank = getField("select kodeBank from dta_vendor_bank where kodeVendor='$par[kodeVendor]' order by kodeBank desc limit 1") + 1;

    repField();
    $sql = "insert into dta_vendor_bank (kodeVendor, kodeBank, namaBank, rekeningBank, pemilikBank, createBy, createTime) values ('$par[kodeVendor]', '$kodeBank', '$inp[namaBank]', '$inp[rekeningBank]', '$inp[pemilikBank]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>alert('DATA BERHASIL DISIMPAN');window.parent.location='index.php?par[mode]=edit&tab=5" . getPar($par, "mode,kodeBank") . "';</script>";
}

function hapusContact()
{
    global $db, $s, $inp, $par, $cUsername;
    $sql = "delete from dta_vendor_contact where kodeVendor='$par[kodeVendor]' and kodeContact='$par[kodeContact]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=4" . getPar($par, "mode,kodeContact") . "';</script>";
}

function ubahContact()
{
    global $db, $s, $inp, $par, $cUsername;

    repField();
    $sql = "update dta_vendor_contact set namaContact='$inp[namaContact]', jabatanContact='$inp[jabatanContact]', emailContact='$inp[emailContact]', teleponContact='$inp[teleponContact]', kantorContact='$inp[kantorContact]', faxContact='$inp[faxContact]', keteranganContact='$inp[keteranganContact]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]' and kodeContact='$par[kodeContact]'";
    db($sql);
    echo "<script>alert('DATA BERHASIL DISIMPAN');window.parent.location='index.php?par[mode]=edit&tab=4" . getPar($par, "mode,kodeContact") . "';</script>";
}

function tambahContact()
{
    global $db, $s, $inp, $par, $cUsername;
    $kodeContact = getField("select kodeContact from dta_vendor_contact where kodeVendor='$par[kodeVendor]' order by kodeContact desc limit 1") + 1;

    repField();
    $sql = "insert into dta_vendor_contact (kodeVendor, kodeContact, namaContact, jabatanContact, emailContact, teleponContact, kantorContact, faxContact, keteranganContact, createBy, createTime) values ('$par[kodeVendor]', '$kodeContact', '$inp[namaContact]', '$inp[jabatanContact]', '$inp[emailContact]', '$inp[teleponContact]', '$inp[kantorContact]', '$inp[faxContact]', '$inp[keteranganContact]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>alert('DATA BERHASIL DISIMPAN');window.parent.location='index.php?par[mode]=edit&tab=4" . getPar($par, "mode,kodeContact") . "';</script>";
}

function hapusAddress()
{
    global $db, $s, $inp, $par, $cUsername;
    $sql = "delete from dta_vendor_address where kodeVendor='$par[kodeVendor]' and kodeAddress='$par[kodeAddress]'";
    db($sql);
    echo "<script>window.location='?par[mode]=edit&tab=1" . getPar($par, "mode,kodeAddress") . "';</script>";
}

function ubahAddress()
{
    global $db, $s, $inp, $par, $cUsername;
    repField();

    $sql = "update dta_vendor_address set kodePropinsi='$inp[kodePropinsi]', kodeKota='$inp[kodeKota]', kategoriAddress='$inp[kategoriAddress]', alamatAddress='$inp[alamatAddress]', teleponAddress='$inp[teleponAddress]', faxAddress='$inp[faxAddress]', latitudeAddress='$inp[latitudeAddress]', longitudeAddress='$inp[longitudeAddress]', keteranganAddress='$inp[keteranganAddress]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]' and kodeAddress='$par[kodeAddress]'";
    db($sql);
    echo "<script>alert('DATA BERHASIL DISIMPAN');window.parent.location='index.php?par[mode]=edit&tab=1" . getPar($par, "mode,kodeAddress") . "';</script>";
}

function tambahAddress()
{
    global $db, $s, $inp, $par, $cUsername;

    $kodeAddress = getField("select kodeAddress from dta_vendor_address where kodeVendor='$par[kodeVendor]' order by kodeAddress desc limit 1") + 1;

    repField();

    $sql = "insert into dta_vendor_address (kodeVendor, kodeAddress, kodePropinsi, kodeKota, kategoriAddress, alamatAddress, teleponAddress, faxAddress, latitudeAddress, longitudeAddress, keteranganAddress, createBy, createTime) values ('$par[kodeVendor]', '$kodeAddress', '$inp[kodePropinsi]', '$inp[kodeKota]', '$inp[kategoriAddress]', '$inp[alamatAddress]', '$inp[teleponAddress]', '$inp[faxAddress]', '$inp[latitudeAddress]', '$inp[longitudeAddress]', '$inp[keteranganAddress]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    echo "<script>alert('DATA BERHASIL DISIMPAN');parent.window.location='index.php?par[mode]=edit&tab=1" . getPar($par, "mode,kodeAddress") . "';</script>";
}

function uploadNpwp($kodeVendor)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["npwpIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["npwpIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $npwpIdentity_file = "npwp-" . $kodeVendor . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $npwpIdentity_file);
    }
    if (empty($npwpIdentity_file)) $npwpIdentity_file = getField("select npwpIdentity_file from dta_vendor_identity where kodeVendor='$kodeVendor'");

    return $npwpIdentity_file;
}

function uploadId($kodeVendor)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["idIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["idIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $idIdentity_file = "id-" . $kodeVendor . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $idIdentity_file);
    }
    if (empty($idIdentity_file)) $idIdentity_file = getField("select idIdentity_file from dta_vendor_identity where kodeVendor='$kodeVendor'");

    return $idIdentity_file;
}

function uploadTdp($kodeVendor)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["tdpIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["tdpIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $tdpIdentity_file = "tdp-" . $kodeVendor . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $tdpIdentity_file);
    }
    if (empty($tdpIdentity_file)) $tdpIdentity_file = getField("select tdpIdentity_file from dta_vendor_identity where kodeVendor='$kodeVendor'");

    return $tdpIdentity_file;
}

function uploadSiup($kodeVendor)
{
    global $db, $s, $inp, $par, $dFile;
    $fileUpload = $_FILES["siupIdentity_file"]["tmp_name"];
    $fileUpload_name = $_FILES["siupIdentity_file"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $dFile);
        $siupIdentity_file = "siup-" . $kodeVendor . "." . getExtension($fileUpload_name);
        fileRename($dFile, $fileUpload_name, $siupIdentity_file);
    }
    if (empty($siupIdentity_file)) $siupIdentity_file = getField("select siupIdentity_file from dta_vendor_identity where kodeVendor='$kodeVendor'");

    return $siupIdentity_file;
}

function uploadLogo($kodeVendor)
{
    global $db, $s, $inp, $par, $fFile;
    $fileUpload = $_FILES["logoVendor"]["tmp_name"];
    $fileUpload_name = $_FILES["logoVendor"]["name"];
    if (($fileUpload != "") and ($fileUpload != "none")) {
        fileUpload($fileUpload, $fileUpload_name, $fFile);
        $logoVendor = "logo-" . $kodeVendor . "." . getExtension($fileUpload_name);
        fileRename($fFile, $fileUpload_name, $logoVendor);
    }
    if (empty($logoVendor)) $logoVendor = getField("select logoVendor from dta_vendor where kodeVendor='$kodeVendor'");

    return $logoVendor;
}

function hapusNpwp()
{
    global $db, $s, $inp, $par, $dFile, $cUsername;

    $npwpIdentity_file = getField("select npwpIdentity_file from dta_vendor_identity where kodeVendor='$par[kodeVendor]'");
    if (file_exists($dFile . $npwpIdentity_file) and $npwpIdentity_file != "") unlink($dFile . $npwpIdentity_file);

    $sql = "update dta_vendor_identity set npwpIdentity_file='' where kodeVendor='$par[kodeVendor]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusId()
{
    global $db, $s, $inp, $par, $dFile, $cUsername;

    $idIdentity_file = getField("select idIdentity_file from dta_vendor_identity where kodeVendor='$par[kodeVendor]'");
    if (file_exists($dFile . $idIdentity_file) and $idIdentity_file != "") unlink($dFile . $idIdentity_file);

    $sql = "update dta_vendor_identity set idIdentity_file='' where kodeVendor='$par[kodeVendor]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusTdp()
{
    global $db, $s, $inp, $par, $dFile, $cUsername;

    $tdpIdentity_file = getField("select tdpIdentity_file from dta_vendor_identity where kodeVendor='$par[kodeVendor]'");
    if (file_exists($dFile . $tdpIdentity_file) and $tdpIdentity_file != "") unlink($dFile . $tdpIdentity_file);

    $sql = "update dta_vendor_identity set tdpIdentity_file='' where kodeVendor='$par[kodeVendor]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusSiup()
{
    global $db, $s, $inp, $par, $dFile, $cUsername;

    $siupIdentity_file = getField("select siupIdentity_file from dta_vendor_identity where kodeVendor='$par[kodeVendor]'");
    if (file_exists($dFile . $siupIdentity_file) and $siupIdentity_file != "") unlink($dFile . $siupIdentity_file);

    $sql = "update dta_vendor_identity set siupIdentity_file='' where kodeVendor='$par[kodeVendor]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit&tab=3" . getPar($par, "mode") . "';</script>";
}

function hapusLogo()
{
    global $db, $s, $inp, $par, $fFile, $cUsername;

    $logoVendor = getField("select logoVendor from dta_vendor where kodeVendor='$par[kodeVendor]'");
    if (file_exists($fFile . $logoVendor) and $logoVendor != "") unlink($fFile . $logoVendor);

    $sql = "update dta_vendor set logoVendor='' where kodeVendor='$par[kodeVendor]'";
    db($sql);

    echo "<script>window.location='?par[mode]=edit" . getPar($par, "mode") . "';</script>";
}

function hapus()
{
    global $db, $s, $inp, $par, $fFile, $dFile, $cUsername;

    $logoVendor = getField("select logoVendor from dta_vendor where kodeVendor='$par[kodeVendor]'");
    if (file_exists($fFile . $logoVendor) and $logoVendor != "") unlink($fFile . $logoVendor);

    $sql = "select * from dta_vendor_identity where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);
    if (file_exists($dFile . $r[siupIdentity_file]) and $r[siupIdentity_file] != "") unlink($dFile . $r[siupIdentity_file]);
    if (file_exists($dFile . $r[tdpIdentity_file]) and $r[tdpIdentity_file] != "") unlink($dFile . $r[tdpIdentity_file]);
    if (file_exists($dFile . $r[idIdentity_file]) and $r[idIdentity_file] != "") unlink($dFile . $r[idIdentity_file]);
    if (file_exists($dFile . $r[npwpIdentity_file]) and $r[npwpIdentity_file] != "") unlink($dFile . $r[npwpIdentity_file]);

    $sql = "delete from dta_vendor where kodeVendor='$par[kodeVendor]'";
    db($sql);
    $sql = "delete from dta_vendor_address where kodeVendor='$par[kodeVendor]'";
    db($sql);
    $sql = "delete from dta_vendor_info where kodeVendor='$par[kodeVendor]'";
    db($sql);
    $sql = "delete from dta_vendor_identity where kodeVendor='$par[kodeVendor]'";
    db($sql);
    $sql = "delete from dta_vendor_contact where kodeVendor='$par[kodeVendor]'";
    db($sql);
    $sql = "delete from dta_vendor_bank where kodeVendor='$par[kodeVendor]'";
    db($sql);
    $sql = "delete from dta_vendor_produk where kodeVendor='$par[kodeVendor]'";
    db($sql);

    echo "<script>window.location='?" . getPar($par, "mode,kodeVendor") . "';</script>";
}

function ubah($update = "")
{
    global $db, $s, $inp, $par, $cUsername;

    $logoVendor = uploadLogo($par[kodeVendor]);
    $siupIdentity_file = uploadSiup($par[kodeVendor]);
    $tdpIdentity_file = uploadTdp($par[kodeVendor]);
    $idIdentity_file = uploadId($par[kodeVendor]);
    $npwpIdentity_file = uploadNpwp($par[kodeVendor]);
    repField();

    $sql = "update dta_vendor set kodePropinsi='$inp[kodePropinsi]', kodeKota='$inp[kodeKota]', nomorVendor='$inp[nomorVendor]', titleVendor='$inp[titleVendor]', kategoriVendor='$inp[kategoriVendor]', namaVendor='$inp[namaVendor]', aliasVendor='$inp[aliasVendor]', alamatVendor='$inp[alamatVendor]', teleponVendor='$inp[teleponVendor]', faxVendor='$inp[faxVendor]', emailVendor='$inp[emailVendor]', webVendor='$inp[webVendor]', logoVendor='$logoVendor', statusVendor='$inp[statusVendor]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]'";
    db($sql);


    # dta_vendor_info
    $sql = getField("select kodeInfo from dta_vendor_info where kodeVendor='$par[kodeVendor]'") ?
        "update dta_vendor_info set pendirianInfo='$inp[pendirianInfo]', pendirianInfo_tanggal='" . setTanggal($inp[pendirianInfo_tanggal]) . "', izinInfo='$inp[izinInfo]', izinInfo_tanggal='" . setTanggal($inp[izinInfo_tanggal]) . "', peringkatInfo='$inp[peringkatInfo]', akreditasiInfo='$inp[akreditasiInfo]', dikmenInfo='$inp[dikmenInfo]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]'" :

        "insert into dta_vendor_info (kodeVendor, kodeInfo, pendirianInfo, pendirianInfo_tanggal, izinInfo, izinInfo_tanggal, peringkatInfo, akreditasiInfo, dikmenInfo, createBy, createTime) values ('$kodeVendor', '$kodeInfo', '$inp[pendirianInfo]', '" . setTanggal($inp[pendirianInfo_tanggal]) . "', '$inp[izinInfo]', '" . setTanggal($inp[izinInfo_tanggal]) . "', '$inp[peringkatInfo]', '$inp[akreditasiInfo]', '$inp[dikmenInfo]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";

    db($sql);

    # dta_vendor_identity
    $sql = getField("select kodeIdentity from dta_vendor_identity where kodeVendor='$par[kodeVendor]'") ?

        "update dta_vendor_identity set siupIdentity='$inp[siupIdentity]', siupIdentity_file='$siupIdentity_file', tdpIdentity='$inp[tdpIdentity]', tdpIdentity_file='$tdpIdentity_file', idIdentity='$inp[idIdentity]', idIdentity_file='$idIdentity_file', npwpIdentity='$inp[npwpIdentity]', npwpIdentity_file='$npwpIdentity_file', alamatIdentity='$inp[alamatIdentity]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeVendor='$par[kodeVendor]'" :

        "insert into dta_vendor_identity (kodeVendor, kodeIdentity, siupIdentity, siupIdentity_file, tdpIdentity, tdpIdentity_file, idIdentity, idIdentity_file, npwpIdentity, npwpIdentity_file, alamatIdentity, createBy, createTime) values ('$par[kodeVendor]', '$par[kodeVendor]', '$inp[siupIdentity]', '$siupIdentity_file', '$inp[tdpIdentity]', '$tdpIdentity_file', '$inp[idIdentity]', '$idIdentity_file', '$inp[npwpIdentity]', '$npwpIdentity_file', '$inp[alamatIdentity]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";

    db($sql);

    if (empty($update)) {
        echo "<script>alert('Data berhasil diubah');</script>";
        echo "<script>window.location='?" . getPar($par, "mode,kodeVendor") . "';</script>";
    }

}

function tambah()
{
    global $s, $inp, $par, $cUsername;

    $kodeMenu = $s;

    $kodeVendor = getField("select kodeVendor from dta_vendor order by kodeVendor desc limit 1") + 1;
    $nomorVendor = "V" . str_pad($kodeVendor, 3, "0", STR_PAD_LEFT);
    $logoVendor = uploadLogo($kodeVendor);
    repField();

    $sql = "insert into dta_vendor (kodeVendor, kodeMenu, kodePropinsi, kodeKota, nomorVendor, titleVendor, namaVendor, kategoriVendor, aliasVendor, alamatVendor, teleponVendor, faxVendor, emailVendor, webVendor, logoVendor, statusVendor, createBy, createTime) values ('$kodeVendor', '$kodeMenu', '0', '0', '$nomorVendor', '$inp[titleVendor]', '$inp[namaVendor]','$inp[kategoriVendor]', '$inp[aliasVendor]', '$inp[alamatVendor]', '$inp[teleponVendor]', '$inp[faxVendor]', '$inp[emailVendor]', '$inp[webVendor]', '$logoVendor', '$inp[statusVendor]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);

    $kodeInfo = $kodeVendor;
    $sql = "insert into dta_vendor_info (kodeVendor, kodeInfo, pendirianInfo, pendirianInfo_tanggal, izinInfo, izinInfo_tanggal, peringkatInfo, akreditasiInfo, dikmenInfo, createBy, createTime) values ('$kodeVendor', '$kodeInfo', '$inp[pendirianInfo]', '" . setTanggal($inp[pendirianInfo_tanggal]) . "', '$inp[izinInfo]', '" . setTanggal($inp[izinInfo_tanggal]) . "', '$inp[peringkatInfo]', '$inp[akreditasiInfo]', '$inp[dikmenInfo]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);

    $kodeIdentity = $kodeVendor;
    $sql = "insert into dta_vendor_identity (kodeVendor, kodeIdentity, siupIdentity, tdpIdentity, idIdentity, npwpIdentity, alamatIdentity, createBy, createTime) values ('$kodeVendor', '$kodeIdentity', '$inp[siupIdentity]', '$inp[tdpIdentity]', '$inp[idIdentity]', '$inp[npwpIdentity]', '$inp[alamatIdentity]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
    db($sql);
    // echo "<script>alert('Data berhasil disimpan');</script>";

    echo "<script>window.location='?par[mode]=edit&par[kodeVendor]=$kodeVendor" . getPar($par, "mode,kodeVendor") . "';</script>";
}

function formProduk()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;
    include "plugins/mce.jsp";

    $sql = "select * from dta_vendor_produk where kodeVendor='$par[kodeVendor]' and kodeProduk='$par[kodeProduk]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[seriDetail]", "anda harus mengisi no. seri");
    setValidation("is_null", "beliDetail", "anda harus mengisi tanggal beli");
    $text = getValidation();

    $text .= "
    <style>
        .chosen-container {
            min-width: 200px !important;; 
        }
    </style>";

    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Produk</h1>
		" . getBread(ucwords("Produk")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">	
				<p>
					<label class=\"l-input-small\">Pelatihan</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[pelatihanProduk]\" name=\"inp[pelatihanProduk]\"  size=\"50\" value=\"$r[pelatihanProduk]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">Kategori</label>
					<div class=\"field\">
						" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[43] . "' order by namaData", "kodeData", "namaData", "inp[kodeKategori]", " ", $r[kodeKategori], "", "410px", "chosen-select") . "
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">Durasi</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[durasiProduk]\" name=\"inp[durasiProduk]\"  size=\"50\" value=\"$r[durasiProduk]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">Peserta</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[pesertaProduk]\" name=\"inp[pesertaProduk]\"  size=\"50\" value=\"$r[pesertaProduk]\" class=\"mediuminput\" style=\"width:400px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">Biaya</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[biayaProduk]\" name=\"inp[biayaProduk]\"  size=\"50\" value=\"" . getAngka($r[biayaProduk]) . "\" class=\"mediuminput\" style=\"width:150px; text-align:right\" onkeyup=\"cekAngka(this);\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">Materi</label>
					<div class=\"field\">
						<textarea id=\"mce1\" name=\"inp[materiProduk]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[materiProduk]</textarea>
					</div>	
				</p>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formBank()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

    $sql = "select * from dta_vendor_bank where kodeVendor='$par[kodeVendor]' and kodeBank='$par[kodeBank]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[namaBank]", "anda harus mengisi bank name");
    setValidation("is_null", "inp[rekeningBank]", "anda harus mengisi account no.");
    setValidation("is_null", "inp[pemilikBank]", "anda harus mengisi account name");
    $text = getValidation();

    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Banking</h1>
		" . getBread(ucwords(str_replace("Bank", "", $par[mode]) . " banking")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">	
				<p>
					<label class=\"l-input-small\">Bank Name</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaBank]\" name=\"inp[namaBank]\"  size=\"50\" value=\"$r[namaBank]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<label class=\"l-input-small\">No Akun</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[rekeningBank]\" name=\"inp[rekeningBank]\"  size=\"50\" value=\"$r[rekeningBank]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"50\"/>
					</div>	
				</p>								
				<p>
					<label class=\"l-input-small\">Account Name</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[pemilikBank]\" name=\"inp[pemilikBank]\"  size=\"50\" value=\"$r[pemilikBank]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>	
				</p>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formContact()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

    $sql = "select * from dta_vendor_contact where kodeVendor='$par[kodeVendor]' and kodeContact='$par[kodeContact]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    setValidation("is_null", "inp[jabatanContact]", "anda harus mengisi jabatan");
    setValidation("is_null", "inp[namaContact]", "anda harus mengisi nama");
    $text = getValidation();

    $text .= "<div class=\"centercontent contentpopup\">
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">Contact</h1>
		" . getBread(ucwords(str_replace("Contact", "", $par[mode]) . " contact")) . "
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
		<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">												
				<p>
					<label class=\"l-input-small\">Jabatan</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[jabatanContact]\" name=\"inp[jabatanContact]\"  size=\"50\" value=\"$r[jabatanContact]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Nama</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[namaContact]\" name=\"inp[namaContact]\"  size=\"50\" value=\"$r[namaContact]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Email</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[emailContact]\" name=\"inp[emailContact]\"  value=\"$r[emailContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Handphone</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[teleponContact]\" name=\"inp[teleponContact]\"  value=\"$r[teleponContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
					</div>
				</p>					
				<p>
					<label class=\"l-input-small\">Tlp. Kantor</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[kantorContact]\" name=\"inp[kantorContact]\"  value=\"$r[kantorContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Fax</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[faxContact]\" name=\"inp[faxContact]\"  value=\"$r[faxContact]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Keterangan</label>
					<div class=\"field\">
						<textarea id=\"inp[keteranganContact]\" name=\"inp[keteranganContact]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganContact]</textarea>
					</div>
				</p>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
				</p>
			</div>
		</form>	
	</div>";
    return $text;
}

function formAddress()
{
    global $db, $s, $inp, $par, $arrTitle, $arrParameter, $menuAccess;

    $sql = "select * from dta_vendor_address where kodeVendor='$par[kodeVendor]' and kodeAddress='$par[kodeAddress]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[latitudeAddress])) $r[latitudeAddress] = "-6.264563";
    if (empty($r[longitudeAddress])) $r[longitudeAddress] = "106.766342";

    setValidation("is_null", "inp[alamatAddress]", "anda harus mengisi alamat");
    $text = getValidation();

    $text .= "<script type=\"text/javascript\" src=\"https://maps.googleapis.com/maps/api/js?key=" . api_key_map() . "\"></script>
	<div class=\"centercontent contentpopup\">
		<div class=\"pageheader\">
			<h1 class=\"pagetitle\">Alamat</h1>
			" . getBread(ucwords(str_replace("Address", "", $par[mode]) . " address")) . "
			<ul class=\"hornav\">
				<li class=\"current\"><a href=\"#detail\" onclick=\"document.getElementById('dMap').style.visibility = 'collapse';\">Detail</a></li>
				<li><a href=\"#map\" onclick=\"document.getElementById('dMap').style.visibility = 'visible';\">Map</a></li>
			</ul>
		</div>
		<style>
                .chosen-container {
                    min-width: 210px;
                }
            </style>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"detail\" class=\"subcontent\">									
					<p>
						<label class=\"l-input-small\">Kategori</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[kategoriAddress]\" name=\"inp[kategoriAddress]\"  size=\"50\" value=\"$r[kategoriAddress]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>	
					</p>								
					<p>
						<label class=\"l-input-small\">Alamat</label>
						<div class=\"field\">
							<textarea id=\"inp[alamatAddress]\" name=\"inp[alamatAddress]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[alamatAddress]</textarea>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Propinsi</label>
						<div class=\"field\">
							" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[3] . "' order by namaData", "kodeData", "namaData", "inp[kodePropinsi]", " ", $r[kodePropinsi], "onchange=\"getKota('" . getPar($par, "mode,kodePropinsi") . "');\"", "210px", "chosen-select") . "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Kota</label>
						<div class=\"field\">
							" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[4] . "' and kodeInduk='$r[kodePropinsi]' order by namaData", "kodeData", "namaData", "inp[kodeKota]", " ", $r[kodeKota], "onchange=\"setGeocode('" . getPar($par, "mode,kodeKota") . "')\"", "210px", "chosen-select") . "
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Telepon</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[teleponAddress]\" name=\"inp[teleponAddress]\"  value=\"$r[teleponAddress]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Fax</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[faxAddress]\" name=\"inp[faxAddress]\"  value=\"$r[faxAddress]\" class=\"mediuminput\" style=\"width:200px;\" maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
						</div>
					</p>
					<p>
						<label class=\"l-input-small\">Diskripsi</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganAddress]\" name=\"inp[keteranganAddress]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganAddress]</textarea>
						</div>
					</p>					
				</div>
				<div id=\"map\" class=\"subcontent\" style=\"display:none;\">					
					
				</div>								

				<table width=\"100%\" id=\"dMap\" style=\"visibility:collapse;\">
					<tr>
						<td>
							<p>
								<div id=\"mapCanvas\" style=\"width: 100%; height: 200px; border: 1px solid #a3a3a3; margin: 10px 0 5px 0px;\"></div>
								<div id=\"map_canvas\"></div>
								<div style=\"display:none\" id=\"markerStatus\"><i>Click and drag the marker</i></div>
								<div style=\"display:none\" id=\"info\"></div>
								<div style=\"display:none\" id=\"address\"></div>						
							</p>
							<p>
								<label>Latitude</label>
								<input type=\"text\" id=\"inp[latitudeAddress]\"  name=\"inp[latitudeAddress]\" class=\"smallinput\" value=\"$r[latitudeAddress]\" />
							</p>
							<p>
								<label>Longitude</label>
								<input type=\"text\" id=\"inp[longitudeAddress]\" name=\"inp[longitudeAddress]\" class=\"smallinput\" value=\"$r[longitudeAddress]\" />
							</p>
							<script>initialize();</script>
						</td>
					</tr>
				</table>
				
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Save\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
				</p>
				
			</div>
		</form>	
	</div>";
    return $text;
}

function form()
{

    global $db, $s, $inp, $par, $tab, $arrTitle, $fFile, $dFile, $arrParameter, $menuAccess;

    $sql = "select * from dta_vendor where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[kodeTipe])) $r[kodeTipe] = $par[kodeTipe];

    $false = $r[statusVendor] == "f" ? "checked=\"checked\"" : "";
    $true = empty($false) ? "checked=\"checked\"" : "";

    setValidation("is_null", "inp[nomorVendor]", "you must fill no. akun");
    setValidation("is_null", "inp[namaVendor]", "you must fill nama pelanggan");
    setValidation("is_null", "inp[alamatVendor]", "you must fill alamat");
    $text = getValidation();

    $dAddress = " style=\"display: none;\"";
    $dInformation = " style=\"display: none;\"";
    $dIdentity = " style=\"display: none;\"";
    $dContact = " style=\"display: none;\"";
    $dBanking = " style=\"display: none;\"";
    $dGeneral = " style=\"display: none;\"";
    $dProduk = " style=\"display: none;\"";
    $dTrainer = " style=\"display: none;\"";

    if ($tab == 1) {
        $tAddress = "class=\"current\"";
        $dAddress = " style=\"display: block;\"";
    } else if ($tab == 2) {
        $tInformation = "class=\"current\"";
        $dInformation = " style=\"display: block;\"";
    } else if ($tab == 3) {
        $tIdentity = "class=\"current\"";
        $dIdentity = " style=\"display: block;\"";
    } else if ($tab == 4) {
        $tContact = "class=\"current\"";
        $dContact = " style=\"display: block;\"";
    } else if ($tab == 5) {
        $tBanking = "class=\"current\"";
        $dBanking = " style=\"display: block;\"";
    } else if ($tab == 6) {
        $tProduk = "class=\"current\"";
        $dProduk = " style=\"display: block;\"";
    } else if ($tab == 7) {
        $tTrainer = "class=\"current\"";
        $dTrainer = " style=\"display: block;\"";
    } else {
        $tGeneral = "class=\"current\"";
        $dGeneral = " style=\"display: block;\"";
    }

    $lastUpdate = $r[updateTime] == "0000-00-00 00:00:00" ? $r[createTime] : $r[updateTime];
    list($tanggalUpdate, $waktuUpdate) = explode(" ", $lastUpdate);
    $tanggalUpdate = getTanggal($tanggalUpdate, "t");
    $waktuUpdate = substr($waktuUpdate, 0, 5);

    $mode = empty($r[nomorVendor]) ? "add" : "edit";

    $text .= "
	<style>
		.fieldB .chosen-container {
			min-width: 5rem !important;
		}
		.fieldA .chosen-container,
		.fieldC .chosen-container {
			width: 60% !important;
		}
	</style>
	<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread(ucwords($mode . " data")) . "
		<ul class=\"hornav\">
			<li $tGeneral><a href=\"#general\">Umum</a></li>
			<li $tAddress><a href=\"#address\">Alamat</a></li>
			<li $tInformation><a href=\"#information\">Informasi</a></li>
			<li $tIdentity><a href=\"#identity\">Identitas</a></li>
			<li $tContact><a href=\"#contact\">Kontak</a></li>
			<li $tBanking><a href=\"#banking\">Bank</a></li>
			<li $tProduk><a href=\"#produk\">Produk</a></li>
			<li $tTrainer><a href=\"#trainer\">Trainer</a></li>
		</ul>
	</div>
	<div id=\"contentwrapper\" class=\"contentwrapper\">
	<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
		<div style=\"position:absolute; right:20px; top:14px;\">
			<input type=\"submit\" class=\"submit radius2\" name=\"btnSave\" value=\"Simpan\" onclick=\"return chk('" . getPar($par, "mode") . "');\"/>
			<input type=\"button\" class=\"cancel radius2\" value=\"Kembali\" onclick=\"window.location='?" . getPar($par, "mode, kodeVendor") . "';\"/>
		</div>";

    # TAB GENERAL
    $text .= "<div id=\"general\" class=\"subcontent\" $dGeneral >	
		<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
			<tr>
				<td width=\"50%\">
					<p>
						<label class=\"l-input-small2\">No Akun</label>
						<div class=\"fieldA\">
							<input type=\"text\" id=\"inp[nomorVendor]\" name=\"inp[nomorVendor]\"  value=\"$r[nomorVendor]\" class=\"mediuminput\" style=\"width:150px;\" maxlength=\"30\"/>
						</div>
					</p>
				</td>	
				<td width=\"50%\">

					<p>
						<label class=\"l-input-small3\">Kategori Vendor</label>
						<div class=\"fieldC\">
							" . comboData("select * from mst_data where statusData='t' and kodeCategory='MKV' order by urutanData", "kodeData", "namaData", "inp[kategoriVendor]", " ", $r[kategoriVendor], "", "225px", "chosen-select") . "
						</div>
					</p>	
				</td>
			</tr>
		</table>

		<p>
			<label class=\"l-input-small\">Nama Vendor</label>
			<div class=\"fieldB\">
				" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[42] . "' order by urutanData", "namaData", "namaData", "inp[titleVendor]", " ", $r[titleVendor], "", "75px", "chosen-select") . "
				<input type=\"text\" id=\"inp[namaVendor]\" name=\"inp[namaVendor]\"  value=\"$r[namaVendor]\" class=\"mediuminput\" style=\"width:325px;\" maxlength=\"150\"/>							
			</div>
		</p>	


		<p>
			<label class=\"l-input-small\">Alias</label>
			<div class=\"fieldB\">
				<input type=\"text\" id=\"inp[aliasVendor]\" name=\"inp[aliasVendor]\"  value=\"$r[aliasVendor]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"150\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Logo</label>
			<div class=\"fieldB\">";
    $text .= empty($r[logoVendor]) ?
        "<input type=\"text\" id=\"fileTemp\" name=\"fileTemp\" class=\"input\" style=\"width:250px;\" maxlength=\"100\" />
				<div class=\"fakeupload\" style=\"width:300px;\">
					<input type=\"file\" id=\"logoVendor\" name=\"logoVendor\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp.value = this.value;\" />
				</div>" :
        "<img src=\"" . $fFile . "/" . $r[logoVendor] . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\">
				<input type=\"file\" id=\"logoVendor\" name=\"logoVendor\" style=\"display:none;\" />
				<a href=\"?par[mode]=delLogo" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this logo?')\" class=\"action delete\"><span>Delete</span></a>
				<br clear=\"all\">";
    $text .= "</div>
			</p>
			<p>
				<label class=\"l-input-small\">Alamat</label>
				<div class=\"fieldB\">
					<textarea id=\"inp[alamatVendor]\" name=\"inp[alamatVendor]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[alamatVendor]</textarea>
				</div>
			</p>
			<table style=\"width:100%\">
				<tr>
					<td style=\"width:50%\">										
						<p>
							<label class=\"l-input-small2\">Propinsi</label>
							<div class=\"fieldA\">
								" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[3] . "' order by namaData", "kodeData", "namaData", "inp[kodePropinsi]", " ", $r[kodePropinsi], "onchange=\"getKota('" . getPar($par, "mode,kodePropinsi") . "');\"", "260px", "chosen-select") . "
							</div>
						</p>						
						<p>
							<label class=\"l-input-small2\">Telepon</label>
							<div class=\"fieldA\">
								<input type=\"text\" id=\"inp[teleponVendor]\" name=\"inp[teleponVendor]\"  value=\"$r[teleponVendor]\" class=\"mediuminput\"  maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small2\">Email</label>
							<div class=\"fieldA\">
								<input type=\"text\" id=\"inp[emailVendor]\" name=\"inp[emailVendor]\"  value=\"$r[emailVendor]\" class=\"mediuminput\"  maxlength=\"50\"/>
							</div>
						</p>
						<p>
							<label class=\"l-input-small2\">Status</label>
							<div class=\"fieldA\" style='width:100%;'>											
								<input type=\"radio\" id=\"true\" name=\"inp[statusVendor]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
								<input type=\"radio\" id=\"false\" name=\"inp[statusVendor]\" value=\"f\" $false /> <span class=\"sradio\">Not Active</span>					
							</div>
						</p>
					</td>
					<td style=\"width:50%\">
						<p>
							<label class=\"l-input-small3\">Kota</label>
							<div class=\"fieldC\">
								" . comboData("select * from mst_data where statusData='t' and kodeCategory='" . $arrParameter[4] . "' and kodeInduk='$r[kodePropinsi]' order by namaData", "kodeData", "namaData", "inp[kodeKota]", " ", $r[kodeKota], "", "260px", "chosen-select") . "
							</div>
						</p>
						<p>
							<label class=\"l-input-small3\">Fax</label>
							<div class=\"fieldC\">
								<input type=\"text\" id=\"inp[faxVendor]\" name=\"inp[faxVendor]\"  value=\"$r[faxVendor]\" class=\"mediuminput\"  maxlength=\"50\" onkeyup=\"cekPhone(this);\"/>
							</div>
						</p>							
						<p>
							<label class=\"l-input-small3\">Website</label>
							<div class=\"fieldC\">
								<input type=\"text\" id=\"inp[webVendor]\" name=\"inp[webVendor]\" value=\"$r[webVendor]\" class=\"mediuminput\"  maxlength=\"50\"/>
							</div>
						</p>";
    if ($mode == "edit")
        $text .= "<p>
						<label class=\"l-input-small3\">Last Update</label>
						<span class=\"fieldC\">" . $tanggalUpdate . " @ " . $waktuUpdate . "&nbsp;
						</span>
					</p>";
    $text .= "</td>
				</tr>
			</table>																		
		</div>";

    # TAB ADDRESS
    $text .= "<div id=\"address\" class=\"subcontent\" $dAddress >";
    if (isset($menuAccess[$s]["add"]))
        $text .= "

		<h3 style='position:absolute; top:25px;'>Alamat Vendor</h3>
		<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addAddress" . getPar($par, "mode,kodeAddress") . "',825,550);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Alamat</span></a>";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>
				<th width=\"200\">Kategori</th>	
				<th>Alamat</th>
				<th width=\"200\">Kota</th>	
				<th width=\"150\">Telepon</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "</tr>
			</thead>
			<tbody>";

    $sql = "select * from dta_vendor_address t1 join mst_data t2 on (t1.kodeKota=t2.kodeData) where t1.kodeVendor='$par[kodeVendor]' order by t1.kodeAddress";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
					<td>$no.</td>
					<td>$r[kategoriAddress]</td>
					<td>$r[alamatAddress]</td>
					<td>$r[namaData]</td>
					<td>$r[teleponAddress]</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editAddress&par[kodeAddress]=$r[kodeAddress]" . getPar($par, "mode,kodeAddress") . "',825,550);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delAddress&par[kodeAddress]=$r[kodeAddress]" . getPar($par, "mode,kodeAddress") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "</td>";
        }
        $text .= "</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "</tr>";
    }

    $text .= "</tbody>
			</table>
		</div>";

    # TAB INFORMATION
    $sql = "select * from dta_vendor_info where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $text .= "<div id=\"information\" class=\"subcontent\" $dInformation >						
		<p>
			<label class=\"l-input-small\">Nomor Pendirian Lembaga</label>
			<div class=\"field\">
				<input type=\"text\" id=\"inp[pendirianInfo]\" name=\"inp[pendirianInfo]\"  value=\"$r[pendirianInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Tanggal SK Pendirian</label>
			<div class=\"field\">
				<input type=\"text\" id=\"pendirianInfo_tanggal\" name=\"inp[pendirianInfo_tanggal]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[pendirianInfo_tanggal]) . "\" class=\"vsmallinput hasDatePicker\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor SK Izin Operational</label>
			<div class=\"field\">
				<input type=\"text\" id=\"inp[izinInfo]\" name=\"inp[izinInfo]\"  value=\"$r[izinInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Tanggal SK Izin Operational</label>
			<div class=\"field\">
				<input type=\"text\" id=\"izinInfo_tanggal\" name=\"inp[izinInfo_tanggal]\" size=\"10\" maxlength=\"10\" value=\"" . getTanggal($r[izinInfo_tanggal]) . "\" class=\"vsmallinput hasDatePicker\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Peringkat Akreditasi</label>
			<div class=\"field\">
				<input type=\"text\" id=\"inp[peringkatInfo]\" name=\"inp[peringkatInfo]\"  value=\"$r[peringkatInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor SK Akreditasi</label>
			<div class=\"field\">
				<input type=\"text\" id=\"inp[akreditasiInfo]\" name=\"inp[akreditasiInfo]\"  value=\"$r[akreditasiInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
			</div>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor SK Dikmen</label>
			<div class=\"field\">
				<input type=\"text\" id=\"inp[dikmenInfo]\" name=\"inp[dikmenInfo]\"  value=\"$r[dikmenInfo]\" class=\"mediuminput\" style=\"width:300px;\" maxlength=\"50\"/>
			</div>
		</p>
	</div>";

    # TAB IDENTITY
    $sql = "select * from dta_vendor_identity where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $text .= "<div id=\"identity\" class=\"subcontent\" $dIdentity >
	<table width=\"100%\">
		<tr>
			<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
				<p>
					<label class=\"l-input-small\">SIUP</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[siupIdentity]\" name=\"inp[siupIdentity]\"  value=\"$r[siupIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">TDP</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[tdpIdentity]\" name=\"inp[tdpIdentity]\"  value=\"$r[tdpIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">ID</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[idIdentity]\" name=\"inp[idIdentity]\"  value=\"$r[idIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">NPWP</label>
					<div class=\"field\">
						<input type=\"text\" id=\"inp[npwpIdentity]\" name=\"inp[npwpIdentity]\"  value=\"$r[npwpIdentity]\" class=\"mediuminput\" style=\"width:250px;\" maxlength=\"50\"/>
					</div>
				</p>
				<p>
					<label class=\"l-input-small\">Alamat</label>
					<div class=\"field\">
						<textarea id=\"inp[alamatIdentity]\" name=\"inp[alamatIdentity]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:400px;\">$r[alamatIdentity]</textarea>
					</div>
				</p>
			</td>
			<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"fieldB\">";
    $text .= empty($r[siupIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_siup\" name=\"fileTemp_siup\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
						<div class=\"fakeupload\" style=\"width:300px;\">
							<input type=\"file\" id=\"siupIdentity_file\" name=\"siupIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_siup.value = this.value;\" />
						</div>" :
        "<a href=\"download.php?d=vendor&f=siup.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[siupIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
						<input type=\"file\" id=\"siupIdentity_file\" name=\"siupIdentity_file\" style=\"display:none;\" />
						<a href=\"?par[mode]=delSiup" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
						<br clear=\"all\">";
    $text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"fieldB\">";
    $text .= empty($r[tdpIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_tdp\" name=\"fileTemp_tdp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
							<div class=\"fakeupload\" style=\"width:300px;\">
								<input type=\"file\" id=\"tdpIdentity_file\" name=\"tdpIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_tdp.value = this.value;\" />
							</div>" :
        "<a href=\"download.php?d=vendor&f=tdp.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[tdpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
							<input type=\"file\" id=\"tdpIdentity_file\" name=\"tdpIdentity_file\" style=\"display:none;\" />
							<a href=\"?par[mode]=delTdp" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
							<br clear=\"all\">";
    $text .= "</div>
						</p>
						<p>
							<label class=\"l-input-small\">File</label>
							<div class=\"fieldB\">";
    $text .= empty($r[idIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_id\" name=\"fileTemp_id\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
								<div class=\"fakeupload\" style=\"width:300px;\">
									<input type=\"file\" id=\"idIdentity_file\" name=\"idIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_id.value = this.value;\" />
								</div>" :
        "<a href=\"download.php?d=vendor&f=id.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[idIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
								<input type=\"file\" id=\"idIdentity_file\" name=\"idIdentity_file\" style=\"display:none;\" />
								<a href=\"?par[mode]=delId" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
								<br clear=\"all\">";
    $text .= "</div>
							</p>
							<p>
								<label class=\"l-input-small\">File</label>
								<div class=\"fieldB\">";
    $text .= empty($r[npwpIdentity_file]) ?
        "<input type=\"text\" id=\"fileTemp_npwp\" name=\"fileTemp_npwp\" class=\"input\" style=\"width:235px;\" maxlength=\"100\" />
									<div class=\"fakeupload\" style=\"width:300px;\">
										<input type=\"file\" id=\"npwpIdentity_file\" name=\"npwpIdentity_file\" class=\"realupload\" size=\"50\" onchange=\"this.form.fileTemp_npwp.value = this.value;\" />
									</div>" :
        "<a href=\"download.php?d=vendor&f=npwp.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[npwpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>
									<input type=\"file\" id=\"npwpIdentity_file\" name=\"npwpIdentity_file\" style=\"display:none;\" />
									<a href=\"?par[mode]=delNpwp" . getPar($par, "mode") . "\" onclick=\"return confirm('are you sure to delete this file?')\" class=\"action delete\"><span>Delete</span></a>
									<br clear=\"all\">";
    $text .= "</div>
								</p>
							</td>
						</tr>
					</table>
				</div>";

    # TAB CONTACT
    $text .= "<div id=\"contact\" class=\"subcontent\" $dContact >";
    if (isset($menuAccess[$s]["add"]))
        $text .= "
				<h3 style='position:absolute; top:25px;'>Kontak</h3>
				<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addContact" . getPar($par, "mode,kodeCatatan") . "',725,500);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Kontak</span></a>";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th style=\"min-width:175px;\">Jabatan</th>
						<th style=\"min-width:175px;\">Nama</th>
						<th width=\"150\">Email</th>
						<th width=\"100\">Handphone</th>
						<th width=\"100\">Tlp. Kantor</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "</tr>
					</thead>
					<tbody>";

    $sql = "select * from dta_vendor_contact where kodeVendor='$par[kodeVendor]' order by kodeContact";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
                    <td>$no.</td>
                    <td>$r[jabatanContact]</td>
                    <td>$r[namaContact]</td>
                    <td>$r[emailContact]</td>
                    <td>$r[teleponContact]</td>
                    <td>$r[kantorContact]</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editContact&par[kodeContact]=$r[kodeContact]" . getPar($par, "mode,kodeContact") . "',725,500);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delContact&par[kodeContact]=$r[kodeContact]" . getPar($par, "mode,kodeContact") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "</td>";
        }
        $text .= "</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
                    <td>&nbsp;</td>								
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>								
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "</tr>";
    }

    $text .= "</tbody>
					</table>
				</div>";

    # TAB BANKING
    $text .= "<div id=\"banking\" class=\"subcontent\" $dBanking >";
    if (isset($menuAccess[$s]["add"]))
        $text .= "
				<h3 style='position:absolute; top:25px;'>Bank</h3>
				<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addBank" . getPar($par, "mode,kodeIdentity") . "',725,300);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Bank</span></a>";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th width=\"20\">No.</th>
						<th>Nama Bank</th>
						<th width=\"150\">No Akun</th>							
						<th>Nama Akun</th>";

    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th width=\"50\">Kontrol</th>";
    $text .= "</tr>
					</thead>
					<tbody>";

    $sql = "SELECT * FROM `dta_vendor_bank` WHERE kodeVendor = '$par[kodeVendor]' order by kodeBank";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
                    <td>$no.</td>
                    <td>$r[namaBank]</td>
                    <td>$r[rekeningBank]</td>
                    <td>$r[pemilikBank]</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editBank&par[kodeBank]=$r[kodeBank]" . getPar($par, "mode,kodeBank") . "',725,300);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delBank&par[kodeBank]=$r[kodeBank]" . getPar($par, "mode,kodeBank") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "</td>";
        }
        $text .= "</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "</tr>";
    }

    $text .= "</tbody>
					</table>
				</div>";

    # TAB PRODUK
    $text .= "<div id=\"produk\" class=\"subcontent\" $dProduk >";
    if (isset($menuAccess[$s]["add"]))
        $text .= "
				<h3 style='position:absolute; top:25px;'>Produk</h3>
				<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=addProduk" . getPar($par, "mode,kodeKategori") . "',1000,575);\" style=\"float:right; margin-bottom:10px;\"><span>Tambah Produk</span></a>";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th style=\"vertical-align:middle;\" width=\"20\">No.</th>
						<th style=\"vertical-align:middle;\">Produk</th>
						<th style=\"vertical-align:middle;\">Durasai</th>
						<th style=\"vertical-align:middle;\">Peserta</th>
						<th style=\"vertical-align:middle;\" width=\"150\">Biaya</th>";
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) $text .= "<th   style=\"vertical-align:middle;\" width=\"50\">Control</th>";
    $text .= "</tr>
					</thead>
					<tbody>";

    $res = db("SELECT * FROM `dta_vendor_produk` WHERE kodeVendor = '$par[kodeVendor]' order by kodeProduk");
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
							<td>$no.</td>
							<td>$r[pelatihanProduk]</td>
							<td>$r[durasiProduk]</td>
							<td>$r[pesertaProduk]</td>
							<td align=\"right\">" . getAngka($r[biayaProduk]) . "</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
            $text .= "<td align=\"center\">";
            if (isset($menuAccess[$s]["edit"])) $text .= "<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"update('" . getPar($par, "mode") . "'); openBox('popup.php?par[mode]=editProduk&par[kodeProduk]=$r[kodeProduk]" . getPar($par, "mode,kodeProduk") . "',1000,575);\"><span>Edit</span></a>";
            if (isset($menuAccess[$s]["delete"])) $text .= "<a href=\"?par[mode]=delProduk&par[kodeProduk]=$r[kodeProduk]" . getPar($par, "mode,kodeProduk") . "\" onclick=\"update('" . getPar($par, "mode") . "'); return confirm('anda yakin akan menghapus data ini ?');\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
            $text .= "</td>";
        }
        $text .= "</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
							<td>&nbsp;</td>							
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "</tr>";
    }

    $text .= "</tbody>
					</table>
				</div>";

    # TAB TRAINER
    $text .= "<div id=\"trainer\" class=\"subcontent\" $dTrainer >";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
				<thead>
					<tr>
						<th style=\"vertical-align:middle;\" width=\"20\">No.</th>
						<th style=\"vertical-align:middle;\">Nama</th>
						<th style=\"vertical-align:middle;\" width=\"200\">No. HP</th>
						<th style=\"vertical-align:middle;\" width=\"200\">Email</th>
						<th style=\"vertical-align:middle;\" width=\"50\">Status</th>";
    $text .= "</tr>
					</thead>
					<tbody>";
    $sql = "select * from dta_trainer where idVendor='$par[kodeVendor]' order by namaTrainer";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $statusTrainer = $r[statusTrainer] == "t" ?
            "<img src=\"styles/images/t.png\" title='Active'>" :
            "<img src=\"styles/images/f.png\" title='Not Active'>";

        $text .= "<tr>
							<td>$no.</td>			
							<td>$r[namaTrainer]</td>							
							<td>$r[handphoneTrainer]</td>
							<td>$r[emailTrainer]</td>
							<td align=\"center\">$statusTrainer</td>
						</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>												
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>";
    }

    $text .= "</tbody>
			</table>
		</div>";

    $text .= "</form>";
    return $text;
}

function detail()
{

    global $db, $s, $inp, $par, $tab, $arrTitle, $fFile, $dFile, $arrParameter, $menuAccess;

    $sql = "select * from dta_vendor where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    if (empty($r[kodeTipe])) $r[kodeTipe] = $par[kodeTipe];

    if ($r[statusVendor] == "p")
        $statusVendor = "Prospect";
    else if ($r[statusVendor] == "t")
        $statusVendor = "Active";
    else
        $statusVendor = "Not Active";

    $dAddress = " style=\"display: none;\"";
    $dInformation = " style=\"display: none;\"";
    $dIdentity = " style=\"display: none;\"";
    $dContact = " style=\"display: none;\"";
    $dBanking = " style=\"display: none;\"";
    $dProduk = " style=\"display: none;\"";
    $dTrainer = " style=\"display: none;\"";
    $dGeneral = " style=\"display: none;\"";

    if ($tab == 1) {
        $tAddress = "class=\"current\"";
        $dAddress = " style=\"display: block;\"";
    } else if ($tab == 2) {
        $tInformation = "class=\"current\"";
        $dInformation = " style=\"display: block;\"";
    } else if ($tab == 3) {
        $tIdentity = "class=\"current\"";
        $dIdentity = " style=\"display: block;\"";
    } else if ($tab == 4) {
        $tContact = "class=\"current\"";
        $dContact = " style=\"display: block;\"";
    } else if ($tab == 5) {
        $tBanking = "class=\"current\"";
        $dBanking = " style=\"display: block;\"";
    } else if ($tab == 6) {
        $tProduk = "class=\"current\"";
        $dProduk = " style=\"display: block;\"";
    } else if ($tab == 7) {
        $tTrainer = "class=\"current\"";
        $dTrainer = " style=\"display: block;\"";
    } else {
        $tGeneral = "class=\"current\"";
        $dGeneral = " style=\"display: block;\"";
    }

    $lastUpdate = $r[updateTime] == "0000-00-00 00:00:00" ? $r[createTime] : $r[updateTime];
    list($tanggalUpdate, $waktuUpdate) = explode(" ", $lastUpdate);
    $tanggalUpdate = getTanggal($tanggalUpdate, "t");
    $waktuUpdate = substr($waktuUpdate, 0, 5);

    $text .= "<div class=\"pageheader\">
		<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
		" . getBread(ucwords("detail data")) . "
		<ul class=\"hornav\">
			<li $tGeneral><a href=\"#general\">General</a></li>
			<li $tAddress><a href=\"#address\">Address</a></li>
			<li $tInformation><a href=\"#information\">Information</a></li>
			<li $tIdentity><a href=\"#identity\">Identity</a></li>
			<li $tContact><a href=\"#contact\">Contact</a></li>
			<li $tBanking><a href=\"#banking\">Banking</a></li>
			<li $tProduk><a href=\"#produk\">Produk</a></li>
			<li $tTrainer><a href=\"#trainer\">Trainer</a></li>
		</div>
		<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
				<div style=\"top:70px; right:35px; position:absolute\">
					<input type=\"button\" class=\"cancel radius2\" style=\"float:right;\" value=\"Back\" onclick=\"window.location='?" . getPar($par, "mode, kodeVendor") . "';\"/>
				</div>";

    # TAB GENERAL
    $namaVendor = empty($r[titleVendor]) ? $r[namaVendor] : $r[namaVendor] . ", " . $r[titleVendor];
    $text .= "<div id=\"general\" class=\"subcontent\" $dGeneral >					
				<p>
					<label class=\"l-input-small\">No Akun</label>
					<span class=\"field\">$r[nomorVendor]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Nama Vendor</label>
					<span class=\"field\">$namaVendor&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Alias</label>
					<span class=\"field\">$r[aliasVendor]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Logo</label>
					<div class=\"field\">";
    $text .= empty($r[logoVendor]) ? "" :
        "<img src=\"" . $fFile . "/" . $r[logoVendor] . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\">
						<br clear=\"all\">";
    $text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">Alamat</label>
						<span class=\"field\">" . nl2br($r[alamatVendor]) . "&nbsp;</span>
					</p>
					<table style=\"width:100%\">
						<tr>
							<td style=\"width:50%\">										
								<p>
									<label class=\"l-input-small\">Propinsi</label>
									<span class=\"field\">" . getField("select namaData from mst_data where kodeData='$r[kodePropinsi]'") . "&nbsp;</span>
								</p>						
								<p>
									<label class=\"l-input-small\">Telepon</label>
									<span class=\"field\">$r[teleponVendor]&nbsp;</span>
								</p>
								<p>
									<label class=\"l-input-small\">Email</label>
									<span class=\"field\">$r[emailVendor]&nbsp;</span>
								</p>
								<p>
									<label class=\"l-input-small\">Status</label>
									<span class=\"field\">$statusVendor&nbsp;</span>
								</p>
							</td>
							<td style=\"width:50%\">
								<p>
									<label class=\"l-input-small\">Kota</label>
									<span class=\"field\">" . getField("select namaData from mst_data where kodeData='$r[kodeKota]'") . "&nbsp;</span>
								</p>
								<p>
									<label class=\"l-input-small\">Fax</label>
									<span class=\"field\">$r[faxVendor]&nbsp;</span>
								</p>							
								<p>
									<label class=\"l-input-small\">Website</label>
									<span class=\"field\">$r[webVendor]&nbsp;</span>
								</p>						
								<p>
									<label class=\"l-input-small\">Website</label>
									<span class=\"fieldC\">" . $tanggalUpdate . " @ " . $waktuUpdate . "&nbsp;
									</span>
								</p>
							</td>
						</tr>
					</table>																		
				</div>";

    # TAB ADDRESS
    $text .= "<div id=\"address\" class=\"subcontent\" $dAddress >
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th width=\"200\">Kategori</th>	<th>Alamat</th>
							<th width=\"200\">Kota</th>	<th width=\"150\">Telepon</th>
						</tr>
					</thead>
					<tbody>";

    $sql = "select * from dta_vendor_address t1 join mst_data t2 on (t1.kodeKota=t2.kodeData) where t1.kodeVendor='$par[kodeVendor]' order by t1.kodeAddress";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
							<td>$no.</td>
							<td>$r[kategoriAddress]</td>
							<td>$r[alamatAddress]</td>
							<td>$r[namaData]</td>
							<td>$r[teleponAddress]</td>
						</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>";
    }

    $text .= "</tbody>
			</table>
		</div>";

    # TAB INFORMATION
    $sql = "select * from dta_vendor_info where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $text .= "<div id=\"information\" class=\"subcontent\" $dInformation >						
		<p>
			<label class=\"l-input-small\">Nomor Pendirian Lembaga</label>
			<span class=\"field\">$r[pendirianInfo]&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Tanggal SK Pendirian</label>
			<span class=\"field\">" . getTanggal($r[pendirianInfo_tanggal], "t") . "&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor SK Izin Operational</label>
			<span class=\"field\">$r[izinInfo]&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Tanggal SK Izin Operational</label>
			<span class=\"field\">" . getTanggal($r[izinInfo_tanggal], "t") . "&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Peringkat Akreditasi</label>
			<span class=\"field\">$r[peringkatInfo]&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor SK Akreditasi</label>
			<span class=\"field\">$r[akreditasiInfo]&nbsp;</span>
		</p>
		<p>
			<label class=\"l-input-small\">Nomor SK Dikmen</label>
			<span class=\"field\">$r[dikmenInfo]&nbsp;</span>
		</p>
	</div>";

    # TAB IDENTITY
    $sql = "select * from dta_vendor_identity where kodeVendor='$par[kodeVendor]'";
    $res = db($sql);
    $r = mysql_fetch_array($res);

    $text .= "<div id=\"identity\" class=\"subcontent\" $dIdentity >
	<table width=\"100%\">
		<tr>
			<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
				<p>
					<label class=\"l-input-small\">SIUP</label>
					<span class=\"field\">$r[siupIdentity]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">TDP</label>
					<span class=\"field\">$r[tdpIdentity]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">ID</label>
					<span class=\"field\">$r[idIdentity]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">NPWP</label>
					<span class=\"field\">$r[npwpIdentity]&nbsp;</span>
				</p>
				<p>
					<label class=\"l-input-small\">Alamat</label>
					<span class=\"field\">" . nl2br($r[alamatIdentity]) . "&nbsp;</span>
				</p>
			</td>
			<td width=\"50%\" nowrap=\"nowrap\" style=\"vertical-align:top\">
				<p>
					<label class=\"l-input-small\">File</label>
					<div class=\"field\">";
    $text .= empty($r[siupIdentity_file]) ? "" :
        "<a href=\"download.php?d=vendor&f=siup.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[siupIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "</div>
					</p>
					<p>
						<label class=\"l-input-small\">File</label>
						<div class=\"field\">";
    $text .= empty($r[tdpIdentity_file]) ? "" :
        "<a href=\"download.php?d=vendor&f=tdp.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[tdpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "</div>
						</p>
						<p>
							<label class=\"l-input-small\">File</label>
							<div class=\"field\">";
    $text .= empty($r[idIdentity_file]) ? "" :
        "<a href=\"download.php?d=vendor&f=id.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[idIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "</div>
							</p>
							<p>
								<label class=\"l-input-small\">File</label>
								<div class=\"field\">";
    $text .= empty($r[npwpIdentity_file]) ? "" :
        "<a href=\"download.php?d=vendor&f=id.$r[kodeVendor]\"><img src=\"" . getIcon($dFile . "/" . $r[npwpIdentity_file]) . "\" align=\"left\" style=\"padding-right:5px; padding-bottom:5px; max-width:50px; max-height:50px;\"></a>";
    $text .= "</div>
								</p>
							</td>
						</tr>
					</table>
				</div>";

    # TAB CONTACT
    $text .= "<div id=\"contact\" class=\"subcontent\" $dContact >
				<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
					<thead>
						<tr>
							<th width=\"20\">No.</th>
							<th style=\"min-width:175px;\">Posisi</th>
							<th style=\"min-width:175px;\">Nama</th>
							<th width=\"150\">Email</th>
							<th width=\"100\">HP</th>
							<th width=\"100\">Tlp Kantor</th>
						</tr>
					</thead>
					<tbody>";

    $sql = "select * from dta_vendor_contact where kodeVendor='$par[kodeVendor]' order by kodeContact";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
							<td>$no.</td>
							<td>$r[jabatanContact]</td>
							<td>$r[namaContact]</td>
							<td>$r[emailContact]</td>
							<td>$r[teleponContact]</td>
							<td>$r[kantorContact]</td>
						</td>
					</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
					<td>&nbsp;</td>								
					<td>&nbsp;</td>
					<td>&nbsp;</td>								
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>";
    }

    $text .= "</tbody>
		</table>
	</div>";

    # TAB BANKING
    $text .= "<div id=\"banking\" class=\"subcontent\" $dBanking >
	<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
		<thead>
			<tr>
				<th width=\"20\">No.</th>
				<th>Nama Bank</th>
				<th width=\"150\">No Akun</th>							
				<th>Nama Akun</th>
			</tr>
		</thead>
		<tbody>";

    $sql = "select * from dta_vendor_bank t1 join mst_data t2 on (t1.kodeBank=t2.kodeData) where t1.kodeVendor='$par[kodeVendor]' order by t1.kodeBank";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
				<td>$no.</td>
				<td>$r[namaBank]</td>
				<td>$r[rekeningBank]</td>
				<td>$r[pemilikBank]</td>
			</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>";
    }

    $text .= "</tbody>
</table>
</div>";

    # TAB PRODUK
    $text .= "<div id=\"produk\" class=\"subcontent\" $dProduk >
<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
	<thead>
		<tr>
			<th style=\"vertical-align:middle;\" width=\"20\">No.</th>
			<th style=\"vertical-align:middle;\">Produk</th>
			<th style=\"vertical-align:middle;\">Durasai</th>
			<th style=\"vertical-align:middle;\">Peserta</th>
			<th style=\"vertical-align:middle;\" width=\"150\">Biaya</th>
		</tr>
	</thead>
	<tbody>";

    $sql = "select * from dta_vendor_produk where kodeVendor='$par[kodeVendor]' order by kodeProduk";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $text .= "<tr>
			<td>$no.</td>
			<td>$r[pelatihanProduk]</td>
			<td>$r[durasiProduk]</td>
			<td>$r[pesertaProduk]</td>
			<td align=\"right\">" . getAngka($r[biayaProduk]) . "</td>
		</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>
		<td>&nbsp;</td>							
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>";
        if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
            $text .= "<td>&nbsp;</td>";
        $text .= "</tr>";
    }

    $text .= "</tbody>
</table>
</div>";

    # TAB TRAINER
    $text .= "<div id=\"trainer\" class=\"subcontent\" $dTrainer >";
    $text .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\">
<thead>
	<tr>
		<th style=\"vertical-align:middle;\" width=\"20\">No.</th>
		<th style=\"vertical-align:middle;\">Nama</th>
		<th style=\"vertical-align:middle;\" width=\"200\">No. HP</th>
		<th style=\"vertical-align:middle;\" width=\"200\">Email</th>
		<th style=\"vertical-align:middle;\" width=\"50\">Status</th>";
    $text .= "</tr>
	</thead>
	<tbody>";
    $sql = "select * from dta_trainer where idVendor='$par[kodeVendor]' order by namaTrainer";
    $res = db($sql);
    $no = 1;
    while ($r = mysql_fetch_array($res)) {
        $statusTrainer = $r[statusTrainer] == "t" ?
            "<img src=\"styles/images/t.png\" title='Active'>" :
            "<img src=\"styles/images/f.png\" title='Not Active'>";

        $text .= "<tr>
			<td>$no.</td>			
			<td>$r[namaTrainer]</td>							
			<td>$r[handphoneTrainer]</td>
			<td>$r[emailTrainer]</td>
			<td align=\"center\">$statusTrainer</td>
		</tr>";
        $no++;
    }

    if ($no == 1) {
        $text .= "<tr>												
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>";
    }

    $text .= "</tbody>
</table>
</div>";

    $text .= "</form>";
    return $text;
}

function insertByImport()
{

    global $inp, $file_log, $cID;

    $log = fopen($file_log, "a+");

    $data_size = $inp['size'];
    $data_position = $inp['position'];
    $data = json_decode($inp['data']);

    // FINISH HIM :v
    if ($data_position == $data_size) {

        fwrite($log, "\nFINISH \t\t: " . date("d/m/Y H:i:s") . "\n");
        fclose($log);

        return;
    }

    $number = $data[0];
    $name = $data[1];
    $category_id = $data[2];
    $type = $data[3];
    $address = $data[4];
    $province_id = $data[5];
    $city_id = $data[6];
    $phone = $data[7];
    $fax = $data[8];
    $email = $data[9];
    $website = $data[10];

    $code = getField("SELECT `nomorVendor` FROM `dta_vendor` ORDER BY `nomorVendor` DESC LIMIT 1");
    $code = $code ? str_replace("V", "", $code) : 0;
    $code = "V" . str_pad($code + 1, 3, "0", STR_PAD_LEFT);

    $sql = "INSERT INTO `dta_vendor` SET
        `kodeMenu` = '0',
        `kodePropinsi` = '$province_id',
        `kodeKota` = '$city_id',
        `nomorVendor` = '$code',
        `kategoriVendor` = '$category_id',
        `titleVendor` = '$type',
        `namaVendor` = '$name',
        `aliasVendor` = '$name',
        `alamatVendor` = '$address',
        `teleponVendor` = '$phone',
        `faxVendor` = '$fax',
        `emailVendor` = '$email',
        `webVendor` = '$website',
        `statusVendor` = 't',
        `createTime` = '". date('Y-m-d H:i:s') ."',
        `createBy` = '$cID'
    ";

    if (db($sql)) {

        fwrite($log, "INSERT \t\t: " . date("d/m/Y H:i:s") . " \tNo: $number\n");
        fclose($log);

        return;
    }

    fwrite($log, "FAILED \t\t: " . date("d/m/Y H:i:s") . " \tNo: $number\n");
    fclose($log);
}