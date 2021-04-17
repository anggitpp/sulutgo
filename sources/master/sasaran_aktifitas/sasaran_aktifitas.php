

<?php

if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

$fFile = "files/xls/";
$fFileE = "files/export/";

function xls()

{        

    global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrParam,$fFileE,$mode;

    

    $direktori = $fFileE;

    $namaFile = "REPORT SASARAN AKTIFITAS.xls";

    $judul = "DATA SASARAN AKTIFITAS";

    $field = array("no",  "Sasaran", "Keterangan", "Layanan","Toilet",  "status");

    

    // $status = getField("select kodeData from mst_data where statusData='t' and kodeCategory='".$arrParameter[6]."' order by urutanData limit 1");           
    // $sWhere= " where t2.status='".$status."'";



    $sWhere = " where id_sasaran  is not null ";

    if (!empty($par['filter'])) $sWhere.= " and layanan ='$par[filter]'";

    if (!empty($par['filter2'])) $sWhere.= " and subyek ='$par[filter2]'";

    $sql = "SELECT t1.*  FROM aktifitas_sasaran t1   $sWhere order by t1.urut asc";
    
    $res=db($sql);
    
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $arrStatus =array('t' =>'Aktif' , 'f' => 'Tidak Aktif');
    $no = 0;

    while($r=mysql_fetch_array($res))

    {

        $no++;

        $data[] = array($no ."\t center" , 

            $r[sasaran] ."\t left", 

            $r[keterangan] ."\t center", 

            $arrMaster[$r[layanan]] ."\t left", 

            $arrMaster[$r[subyek]] ."\t left", 

            $arrStatus[$r[status]] ."\t left");

    }
    
    exportXLS($direktori, $namaFile, $judul, 6, $field, $data);

}
function getContent($par)

{

    global $s, $inp, $par, $_submit, $_submit2, $_submit4, $menuAccess;



    switch($par[mode])

    {   

        case "lst":

        $text = lData();

        break;

        case 'getSubObyek':
        getSubObyek();
        break;

        case "form":

        if(isset($menuAccess[$s]["add"])) $text = empty($_submit) ? form($par[id_sekolah]) : simpan(); else $text = form();

        break;



        case "delete":

        $text = delete();

        break;



        case "getkota":

        getkota();

        break;



        default:

        $text = lihat();

        break;

    }

    return $text;

}


function getSubObyek() {

    global $s, $id, $inp, $par, $arrParameter,$db;

    $getData = queryAssoc("SELECT kodeData, namaData FROM mst_data WHERE  kodeInduk = '$par[subobyek]' order by namaData asc");

    echo  json_encode($getData);

}
function getkota() {

    global $s, $id, $inp, $par, $arrParameter,$db;

    $getData = queryAssoc("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S03' and kodeInduk = $par[provinsi] order by namaData asc");

    echo json_encode($getData);

}





function simpan(){

    global $s,$inp,$par,$arrTitle,$menuAccess,$cUsername,$cID;



    $lastID = getField("SELECT id_sasaran FROM aktifitas_sasaran ORDER BY id_sasaran DESC LIMIT 1") + 1;

    

    if(!empty($par[id_sasaran])){

        $sql = "UPDATE aktifitas_sasaran SET layanan = '$inp[layanan]',subyek = '$inp[subyek]',sasaran = '$inp[sasaran]',keterangan = '$inp[keterangan]',urut = '$inp[urut]',kode = '$inp[kode]',status = '$inp[status]', update_date = '".date('Y-m-d H:i:s')."', update_by = '$cID' WHERE id_sasaran = '$par[id_sasaran]'";

    }else{

        $sql = "INSERT INTO aktifitas_sasaran (id_sasaran,layanan,subyek, sasaran, keterangan,urut,kode,status,create_date,create_by) VALUES ('$lastID','$inp[layanan]','$inp[subyek]','$inp[sasaran]','$inp[keterangan]','$inp[urut]','$inp[kode]','$inp[status]','".date('Y-m-d H:i:s')."','$cID')";

    }


    // var_dump($sql);
    // die();
    db($sql);

    echo "<script>closeBox();</script>";

    echo "<script>alert('Data berhasil disimpan!')</script>";

    echo "<script>window.location='index.php?".getPar($par,"mode, id_sasaran")."';</script>";

}


function dropdown($nama, $variable, $sel, $conditional = "") {

    $html = "<select id=\"$nama\" name=\"$nama\" $java $conditional>";
    
    foreach ($variable as $key => $value) {

        $html.= $key == $sel ? "<option value=\"$key\" selected>$value</option>" : "<option value=\"$key\">$value</option>";

    }

    $html.= "</select>";

    return $html;
}


function form(){

    global $s,$inp,$par,$arrTitle,$menuAccess,$arrParam,$array_model;

    $whereCombo2="";
    if(!empty($par[filter]))
        $whereCombo2.="and kodeInduk='$par[filter]'";

    $sql = db("SELECT * FROM aktifitas_sasaran WHERE id_sasaran ='$par[id_sasaran]'");

    $r = mysql_fetch_array($sql);
    $r['layanan'] = empty($r['layanan']) ? $par['filter'] : $r['layanan'];

    $r['subyek'] = empty($r['subyek']) ? $par['filter2'] : $r['subyek'];

    if(!isset($par[id_sasaran])){
        $r['kode'] = getKode();
        $r['urut'] = getField("SELECT `urut` FROM `aktifitas_sasaran`  ORDER BY `urut` DESC LIMIT 1")+1;
    }
    

    $r['status'] = empty($r['status']) ? "t" : $r['status'];

    $text .="

    <style>

        #inp_kodeRekening__chosen{

        min-width:250px;

    }

</style>

<div class=\"pageheader\">

    <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

</div>

<div id=\"contentwrapper\" class=\"contentwrapper\">

    <br>

    

    <form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">
      <fieldset>

        <legend>Lokasi</legend>
        <div style=\"position:absolute; right:20px; top:14px;\">

            <input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Simpan\"/>
            <input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?".getPar($par,"mode,id_sasaran")."'\"/>

        </div>
        <p>

            <label class=\"l-input-small\">Layanan</label>

            <div class=\"field\">

                ".comboData("SELECT * FROM mst_data WHERE kodeCategory='SVR'  order by namaData","kodeData", "namaData","inp[layanan]"," - Pilih layanan -",$r[layanan],"onchange=\"changeObyek(this.value,'".getPar($par,"mode")."');\"", "310px","chosen-select")."

            </div>

        </p>

        <p>

            <label class=\"l-input-small\">Obyek</label>

            <div class=\"field\">

                ".comboData("SELECT * FROM mst_data WHERE kodeCategory='OBY' $whereCombo2 order by namaData","kodeData", "namaData","inp[subyek]","- Pilih Obyek -",$r[subyek],"", "310px","chosen-select")."

            </div>

        </p>

    </fieldset>
    <fieldset>

        <legend>Sasaran</legend>
        <p>

            <label class=\"l-input-small\">Sasaran</label>

            <div class=\"field\">

                <input type=\"text\" id=\"inp[sasaran]\" name=\"inp[sasaran]\" class=\"longinput\" value=\"". $r['sasaran'] ."\" />

            </div>

        </p>
        
        <p>

            <label class=\"l-input-small\">Keterangan</label>

            <div class=\"field\">

                <textarea rows=\"5\"  class=\"longinput\" name=\"inp[keterangan]\">".$r[keterangan]."</textarea>

            </div>

        </p>

        <table style=\"width: 100%;\">

            <tr>

                <td style=\"width: 50%;\">

                    <label class=\"l-input-small2\">Order</label>

                    <div class=\"field\">

                        <input type=\"text\" id=\"inp[urut]\" name=\"inp[urut]\" class=\"\" value=\"". $r['urut'] ."\" style=\"width: 50%;\"/>

                    </div>

                </td>

                <td style=\"width: 50%;\">

                    <label class=\"l-input-small2\">Kode</label>

                    <div class=\"field\">

                        <input type=\"text\" id=\"inp[kode]\" name=\"inp[kode]\" class=\"\" value=\"". $r['kode'] ."\" style=\"width: 42%;\" readonly/>

                    </div>

                </td>

            </tr>

        </table>


        <p>

           <label class=\"l-input-small\">Status</label>

           <div class=\"field\">

              <div class=\"fradio\">

                 <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"t\" class=\"metode1\" style=\"width:300px;\" ".($r[status] == 't' ? "checked" : '')."/> Aktif



                 <input type=\"radio\" id=\"inp[status]\" name=\"inp[status]\" value=\"f\" class=\"metode2\" style=\"width:300px;\" ".($r[status] == 'f' ? "checked" : '')."/> Tidak Aktif

             </div>

         </div>

     </p>

 </div>



</fieldset>

<br>



<br>



</form>

</div>
<script>

    jQuery(document).ready(function(){

        position = jQuery(\"#inp\\\[model\\\]\").val();
        checking(position);
        
        jQuery(\"#inp\\\[model\\\]\").change(function() {

            position = jQuery(this).val();

            checking(position);

        });

});

function checking(position) {

    switch(position) {

        case '1':
        jQuery(\"#parameter\").fadeIn(0);
        break;

        case '2':
        jQuery(\"#parameter\").fadeOut(0);
        break;

        default:
        jQuery(\"#parameter\").fadeOut(0);
        break;

    }

}

</script>
";



return $text;

}

function getKode() {
    global $mode;

    $getBefore = getField("SELECT count(`kode`) FROM `aktifitas_sasaran`  ORDER BY `kode` DESC LIMIT 1");

    // list( ,$getBefore)  = explode("$mode", $getBefore);

    $result = $getBefore + 1;

    if($result < 10) $return    = $mode.'SA'."00".$result;
    if($result >= 10) $return    = $mode.'SA'."0".$result;
    if($result >= 100) $return  = $mode.'SA'.$result;

    return  $return;
}

function delete(){

    global $s, $inp, $par, $_submit, $_submit2, $_submit3, $_submit4, $menuAccess;


    $cek = getField("SELECT * from mst_aktifitas where id_sub_ruang='$par[id_sasaran]'");

    if($cek){
        echo "<script>closeBox();</script>";

        echo "<script>alert('sorry, data has been use')</script>";        
    }else{
        $sql = "DELETE FROM aktifitas_sasaran WHERE id_sasaran = '$par[id_sasaran]'";

        db($sql);

        echo "<script>closeBox();</script>";

        echo "<script>alert('Data telah dihapus!')</script>";    
    }

    echo "<script>window.location.href='index.php?".getPar($par,"mode, id_sasaran")."';</script>";

}



function lihat()

{

    global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;

    // $getKode = getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

    // $array_model = array("Checklist", "Parameter", "Isian");

    
    if ($_GET["json"] == 1) {

        header("Content-type: application/json");

        if(!isset($par['filter']))
        $par['filter']=getField("SELECT kodeData from mst_data where kodeCategory='SVR' order by kodeData ASC");

        if(!isset($par['filter2']))
        $par['filter2']=getField("SELECT kodeData from mst_data where kodeCategory='OBY' and kodeInduk='$par[filter]' order by kodeData ASC");

        $sWhere = " where id_sasaran  is not null ";

        if (!empty($par['filter'])) $sWhere.= " and layanan ='$par[filter]'";

        if (!empty($par['filter2'])) $sWhere.= " and subyek ='$par[filter2]'";

        $sql = "SELECT t1.*  FROM aktifitas_sasaran t1   $sWhere order by t1.urut asc";

        $res = db($sql);
        
        $ret = array(); 
        
        while ($r = mysql_fetch_assoc($res))
        {

            // $r[id_sub_ruang]=getField("SELECT namaData FROM mst_data WHERE kodeData = '$r[id_sub_ruang]'");

            // $r[model] = $array_model[$r[model]];

            // $r[layanan] = getField("SELECT namaData FROM mst_data WHERE kodeMaster = '$r[id_sekolah]'");


            $r[status] = $r[status] == 't' ? "<img src=\"".APP_URL."/styles/images/t.png\" title='Aktif'>" : "<img src=\"".APP_URL."/styles/images/f.png\" title='Tidak Aktif'>";


            $ret[] = $r;
            
        }
        
        echo json_encode(array("sEcho" => 1, "aaData" => $ret));
        
        exit();
        
    }

    echo "
    <script src=\"sources/js/default.js\"></script>
    
    <div class=\"pageheader\">

        <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
        
        " . getBread() . "
        
        <span class=\"pagedesc\">&nbsp;</span>

    </div>";
    
    echo "
    <div id=\"contentwrapper\" class=\"contentwrapper\">

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">

            <thead>

                <tr>

                    <th width=\"20\" style=\"vertical-align:middle;\">No</th>
                    
                    <th style=\"vertical-align:middle;\">SASARAN</th>
                    
                    <th width=\"250\" style=\"vertical-align:middle;\">KETERANGAN</th>
                    
                    <th width=\"50\" style=\"vertical-align:middle;\">URUT</th>

                    
                    <th width=\"60\" style=\"vertical-align:middle;\">STATUS</th>";

                    if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {

                        echo "<th width=\"20\" style=\"vertical-align: middle\">Kontrol</th>";
                        
                    }

                    echo "

                </tr>

            </thead>

        </table>

    </div>";
    
    echo "
    <script type=\"text/javascript\">

        jQuery(document).ready(function () {

            ot = jQuery(\"#datatable\").dataTable({

                \"sScrollY\": \"100%\",

                \"aLengthMenu\": [[20, 35, 70, -1], [20, 35, 70, \"All\"]],

                \"bSort\": true,

                \"bSorting\": [[ 3, \"desc\" ]],

                \"bFilter\": true,

                \"iDisplayStart\": 0,

                \"iDisplayLength\": 20,

                \"sPaginationType\": \"full_numbers\",

                \"sAjaxSource\": \"ajax.php?json=1".getPar($par, 'filterGroup')."\",

                \"aoColumns\": [
                
                {\"mData\": null, \"sWidth\": \"20px\", \"bSortable\": false, \"sClass\": \"alignCenter\"},			
                
                {\"mData\": \"sasaran\", \"bSortable\": true},
                
                {\"mData\": \"keterangan\", \"bSortable\": true, \"sClass\": \"alignLeft\"},
                
                {\"mData\": \"urut\", \"bSortable\": true, \"sClass\": \"alignCenter\"},
                
                {\"mData\": \"status\", \"bSortable\": true, \"sClass\": \"alignCenter\"},

                {\"mData\": null,\"sWidth\": \"80px\", \"bSortable\": false, \"sClass\": \"alignCenter\", \"fnRender\": function(o){

                    var ret = '';
                    
                    ";
                    
                    if(isset($menuAccess[$s]['edit'])) {

                        echo "ret += '<a  href=\"?par[mode]=form&par[id_sasaran]=' + o.aData['id_sasaran'] + '".getPar($par,"mode, id_sasaran")."\" class=\"edit\" title=\"Edit Data\"></a>';";
                        
                    } 
                    
                    if(isset($menuAccess[$s]['delete'])) {

                        echo "ret += '<a href=\"?par[mode]=delete&par[id_sasaran]=' + o.aData['id_sasaran'] + '".getPar($par, "mode, id_sasaran")."\" class=\"delete\" title=\"Delete Data\" onclick=\"return confirm(\'Apakah anda ingin menghapus data ini?\')\"></a>'; ";
                        
                    }
                    
                    echo "

                    return ret;

                }}

                ],
                
                \"aaSorting\": [[3, \"asc\"]],
                
                \"fnInitComplete\": function (oSettings) {

                    oSettings.oLanguage.sZeroRecords = \"No data available\";
                    
                }, \"sDom\": \"<'top'f>rt<'bottom'lip><'clear'>\",
                
                \"fnRowCallback\": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                    jQuery(\"td:first\", nRow).html((iDisplayIndexFull + 1) + \".\");
                    
                    return nRow;
                    
                },
                
                \"bProcessing\": true,
                
                \"oLanguage\": {

                    \"sProcessing\": '<img src=\"".APP_URL."/styles/images/loader.gif\" />'
                    
                }
                
            });


jQuery(\"#datatable_wrapper #datatable_filter\").css(\"float\", \"left\").css(\"position\", \"relative\").css(\"margin-left\", \"14px\").css(\"font-size\", \"14px\");

jQuery(\"#datatable_wrapper #datatable_filter > label > img\").css(\"margin-top\", \"8px\");

jQuery(\"#datatable_wrapper #datatable_filter\").append(`".comboData("SELECT * from mst_data  where kodeCategory='SVR' order by namaData","kodeData","namaData","bSearch","- Layanan -","$par[filter]","","210px;","chosen-select", "")."`);

jQuery(\"#datatable_wrapper #datatable_filter\").append(`".comboData("SELECT * from mst_data  where kodeCategory='OBY' and kodeInduk='$par[filter]' order by namaData","kodeData","namaData","mSearch","- Seluruh Obyek -","$par[filter2]","","210px;","chosen-select", "")."`);                        

";

$getPar = getPar($par, 'mode');

echo "

jQuery(\"#datatable_wrapper .top\").append(\"<div id='right_panel' class='dataTables_filter' style='float:right; top: 0px; right: 0px'>\");

jQuery(\"#datatable_wrapper #right_panel\").append(\"&nbsp;&nbsp;<a href='index.php?par[mode]=form".getPar($par,"mode")."' id='tambahData' class='btn btn1 btn_document'><span>Tambah Data</span></a>&nbsp;&nbsp;&nbsp;\");

jQuery(\"#datatable_wrapper #right_panel\").append('<a href=\"?par[mode]=xls".getPar($par, "mode")."\" id=\"btnExport\" class=\"btn btn1 btn_inboxo\"><span>Export</span></a>');
});

</script>

<script type=\"text/javascript\">

    function joinTable(){
        jQuery(\".dataTables_scrollHeadInner > table\").css(\"border-bottom\",\"0\").css(\"padding-bottom\",\"0\").css(\"margin-bottom\",\"0\");
        jQuery(\".dataTables_scrollBody > table\").css(\"border-top\",\"0\").css(\"margin-top\",\"-5px\");
    }

    jQuery(document).ready(function () {

        jQuery(\"#bSearch\").change(function(){

            var filter = jQuery(\"#bSearch\").val();

            window.location='?par[filter]='+filter+'".getPar($par,'mode, filter')."';

        });

});

jQuery(document).ready(function () {

    jQuery(\"#mSearch\").change(function(){

        var filter2 = jQuery(\"#mSearch\").val();

        window.location='?par[filter2]='+filter2+'".getPar($par,'mode, filter2')."';

    });

});

</script>";

if($par[mode] == "xls"){            
    xls();          
    echo "<iframe src=\"download.php?d=exp&f=REPORT SASARAN AKTIFITAS.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iAKTIFITASe>";
}

}



// function lData()

// {

//     global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;

//     $getKode=getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

//     $array_model = array("Checklist", "Parameter", "Isian");

//     if($_GET[json] == 1){

//         header("Content-type: application/json");

//     }



//     if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1'){

//         $sLimit = "limit ".intval($_GET['iDisplayStart']).", ".intval($_GET['iDisplayLength']);



//     }



//     $sWhere = " where id_sasaran is not null and kodeInduk='$getKode'";

//     if (!empty($_GET['tSearch'])) $sWhere.= " and id_ruang = $_GET[tSearch] ";

//     if (!empty($_GET['fSearch'])){

//         $sWhere .= " and (     

//             lower(aktifitas) like '%".mysql_real_escape_string(strtolower($_GET['fSearch']))."%'

//             )";

//     }



// $arrOrder = array("aktifitas","id_ruang","status");



// $orderBy = $arrOrder["".$_GET[iSortCol_0].""]." ".$_GET[sSortDir_0];



// $sql = "SELECT mst_aktifitas.*,mst_data.namaData FROM mst_aktifitas join mst_data on(mst_aktifitas.id_ruang=mst_data.kodeData) $sWhere order by $orderBy";

//     // echo $sql;



// $res = db($sql);



// $json = array(

//     "iTotalRecords" => mysql_num_rows($res),

//     "iTotalDisplayRecords" => getField("SELECT count(*) FROM mst_aktifitas join mst_data on(mst_aktifitas.id_ruang=mst_data.kodeData) $sWhere "),

//     "aaData" => array()

//     );



// $no = intval($_GET['iDisplayStart']);



// while($r = mysql_fetch_array($res)){

//     $no++;

//     $r[model] = $array_model[$r[model]];

//     $r[status] = $r[status] == 't' ? "<img src=\"".APP_URL."/styles/images/t.png\" title='Aktif'>" : "<img src=\"".APP_URL."/styles/images/f.png\" title='Tidak Aktif'>";

//     $r[layanan] = getField("SELECT namaData FROM mst_data WHERE kodeMaster = '$r[id_sekolah]'");

//     $data = array(

//         "<div align=\"center\">".$no."</div>",

//         "<div align=\"left\">".$r[aktifitas]."</div>",

//         "<div align=\"left\">".$r[model]."</div>",

//         "<div align=\"left\">".$r[namaData]."</div>",

//         "<div align=\"center\">".$r[status]."</div>",

//         "<div align=\"center\">

//         <a href='index.php?par[mode]=form&par[id_sasaran]=$r[id_sasaran]".getPar($par,"mode")."' class='edit' title='Edit Data'></a>



//         <a href='index.php?par[mode]=delete&par[id_sasaran]=$r[id_sasaran]".getPar($par,"mode")."' onclick=\"return confirm('Anda yakin akan menghapus data ini ?')\" class='delete' title='Delete Data'></a>

//     </div>"

//     );



//     $json['aaData'][] = $data;

// }   

// return json_encode($json);

// }

// fungsi lihat 


//     global $s,$inp,$par,$arrTitle,$menuAccess;

//     $getKode=getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

//     $cols = 6;     



//     $text = table($cols, array($cols,($cols-7),($cols-6),($cols-5),($cols-4),($cols-3),($cols-2),($cols-1)));

//     $mSearch = empty($mSearch) ? date('Y') : $mSearch;

//     $text .= "



//     <div class=\"pageheader\">

//         <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>

//         ".getBread()."

//         <span class=\"pagedesc\">&nbsp;</span>

//     </div>

//     <div id=\"contentwrapper\" class=\"contentwrapper\">



//         <form action=\"\" method=\"post\" id = \"form\" class=\"stdform\" onsubmit=\"return false;\">

//             <div id=\"pos_l\" style=\"float:left;\">



//                 <p>         

//                     <input type=\"text\" id=\"fSearch\" name=\"fSearch\" value=\"".$par[filterData]."\" style=\"width:200px;\" onkeyup=\"getTotal('".getPar($par,"mode")."');\"/>

//                     ".comboData("SELECT * from mst_data  where kodeCategory='OBY' and kodeInduk='$getKode' order by namaData","kodeData","namaData","tSearch","- Obyek -",$tSearch,"","180px;","chosen-select")."
//                 </p>



//             </div> 

//             <div id=\"pos_r\" style=\"float:right;\">

//                 ";

//                 if(isset($menuAccess[$s]["add"])) $text.="

//                     <a href=\"index.php?par[mode]=form".getPar($par,"mode")."\" id=\"tambahData\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>

//                 ";



//                 $text.="

//             </div>

//             ";

//             $text .="

//         </form>

//         <br clear=\"all\" />

//         <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dataList\">

//             <thead>

//                 <tr>

//                     <th width=\"20px\">NO</th>

//                     <th width=\"*\">Aktifitas</th>

//                     <th width=\"100px\">Model</th>

//                     <th width=\"100px\">Obyek</th>

//                     <th width=\"20px\">Status</th>

//                     <th width=\"50px\">KONTROL</th>

//                 </tr>

//             </thead>

//             <tbody></tbody>

//         </table>

//     </div>

//     ";

//     if($par[mode] == "xlsSetoran"){            

//         xlsSetoran();          

//         $text.="<iframe src=\"download.php?d=exp&f=".ucwords(strtolower($arrTitle[$s]." Setoran - ".time())).".xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";

//     }

//     $text.="

//     <script>

//         jQuery(\"#btnExportSetoran\").live('click', function(e){

//             e.preventDefault();

//             window.location.href=\"?par[mode]=xlsSetoran\"+ \"".getPar($par,"mode")."\";

//         });

// </script>

// ";



// return $text;

?>