

<?php

if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

$fExport = "files/export/";

$fFile = "files/xls/";

$fFileE = "files/export/";

$mode = $arrParam[$s];
function xls()

{        

    global $db,$s,$inp,$par,$arrTitle,$arrParameter,$cNama,$fFile,$menuAccess,$arrParam,$fFileE;

    

    $direktori = $fFileE;

    $namaFile = "REPORT MASTER AKTIFITAS.xls";

    $judul = "DATA MASTER AKTIFITAS ";

    $field = array("no",  "Aktifitas", "Model", "Satuan", "Obyek", "Subyek","Urutan","Status");

    $getKode = getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

    $array_model = array("Checklist", "Parameter", "Isian");

    $where="";
    if(!empty($par[filter]))
       $where.="where t1.id_setting is not null and t2.kodeInduk='$getKode' and t1.id_ruang = '$par[filter]'";

    $sql = "SELECT t1.*,t2.namaData as obyek FROM mst_aktifitas t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) $where order by id_sub_ruang,urutan asc";
    
    $res=db($sql);
    
    $arrMaster = arrayQuery("select kodeData, namaData from mst_data");

    $no = 0;

    while($r=mysql_fetch_array($res))

    {
        
        $r[id_sub_ruang] = getField("SELECT sasaran FROM aktifitas_sasaran WHERE id_sasaran = '$r[id_sub_ruang]'");
        $r[satuan]       = getField("SELECT namaData from mst_data where kodeData='$r[satuan]'");
        
        if($r[model] == '1'){
            $r[model]        = $array_model[$r[model]]."<br/> <small>".$r[model_par1]." sd ".$r[model_par2]."</small>";
        }else{
            $r[model]        = $array_model[$r[model]];
        }

        $no++;
      
        $data[] = array($no ."\t center" , 

                        $r[aktifitas] ."\t left", 

                        $r[model] ."\t left", 

                        $r[satuan] ."\t left", 

                        $r[obyek] ."\t left",

                        $r[id_sub_ruang] ."\t left",

                        $r[urutan] ."\t center",

                        $r[status] ."\t center");
      
    }
    
    exportXLS($direktori, $namaFile, $judul, 8, $field, $data);

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

        case "detail":

        detail();

        break;

        case "view":

        view();

        break;



        default:

        $text = lihat();

        break;

    }

    return $text;

}


function getSubObyek() {

    global $s, $id, $inp, $par, $arrParameter,$db;

    $getData = queryAssoc("SELECT id_sasaran, sasaran FROM aktifitas_sasaran WHERE  subyek = '$par[subobyek]' order by sasaran asc");

    echo  json_encode($getData);

}
function getkota() {

    global $s, $id, $inp, $par, $arrParameter,$db;

    $getData = queryAssoc("SELECT kodeData, namaData FROM mst_data WHERE kodeCategory = 'S03' and kodeInduk = $par[provinsi] order by namaData asc");

    echo json_encode($getData);

}





function simpan(){

    global $s,$inp,$par,$arrTitle,$menuAccess,$cUsername,$cID;



    $lastID = getField("SELECT id_setting FROM mst_aktifitas ORDER BY id_setting DESC LIMIT 1") + 1;

    

    if(!empty($par[id_setting])){

        $sql = "UPDATE mst_aktifitas SET `id_setting` = '$lastID',`id_ruang` = '$inp[id_ruang]',`id_sub_ruang` = '$inp[id_sub_ruang]',`aktifitas` = '$inp[aktifitas]',`kategori` = '$inp[kategori]',`tipe` = '$inp[tipe]',`kode` = '$inp[kode]',`satuan` = '$inp[satuan]',`urutan` = '$inp[urutan]',`model` = '$inp[model]',`model_note` = '$inp[model_note]',`model_par1` = '$inp[model1]',`model_par2`= '$inp[model2]',`keterangan` = '$inp[keterangan]',`status` = '$inp[status]',`interval` = '$inp[interval]', `update_time` = '".date('Y-m-d H:i:s')."', `update_by` = '$cID' WHERE `id_setting` = '$inp[id_setting]'";

    }else{

        $sql = "INSERT INTO mst_aktifitas (`id_setting`,`id_ruang`,`id_sub_ruang`, `aktifitas`,`kategori`,`tipe`,`kode`,`satuan`,`interval`,`urutan`,`model`,`model_note`,`model_par1`,`model_par2`,`keterangan`,`status`,`create_time`,`create_by`) VALUES ('$lastID','$inp[id_ruang]','$inp[id_sub_ruang]','$inp[aktifitas]','$inp[kategori]','$inp[tipe]','$inp[kode]','$inp[satuan]','$inp[interval]','$inp[urutan]','$inp[model]','$inp[model_note]','$inp[model1]','$inp[model2]','$inp[keterangan]','$inp[status]','".date('Y-m-d H:i:s')."','$cID')";

    }


    // var_dump($sql);
    // die();
    db($sql);

    echo "<script>closeBox();</script>";

    echo "<script>alert('Data berhasil disimpan!')</script>";

    echo "<script>window.location='index.php?par[mode]=detail".getPar($par,"mode, id_setting,id_sub_ruang")."';</script>";

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
    $getKode=getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");
    $array_model = array("Checklist", "Parameter", "Isian");

    $sql = db("SELECT * FROM mst_aktifitas WHERE id_setting ='$par[id_setting]'");

    $r = mysql_fetch_array($sql);

    if(isset($par[id_sub_ruang]))
        $r['id_sub_ruang']=$par[id_sub_ruang];

    $whereCombo2="";
    if(!empty($par[filter]))
        $whereCombo2.="and subyek='$par[filter]'";

    $urutan=$r[urutan];
    if(!isset($par[id_setting]))
        $urutan=getField("SELECT urutan from mst_aktifitas where id_ruang='$par[filter]' and id_sub_ruang='$par[id_sub_ruang]' order by urutan desc") + 1;

    $r['id_ruang'] = empty($r['id_ruang']) ? $par['filter'] : $r['id_ruang'];

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
            <input type=\"button\" class=\"cancel radius2\" value=\"Batal\" onclick=\"window.location='?par[mode]=detail".getPar($par,"mode,id_setting,id_sub_ruang")."'\"/>

        </div>
        <p>

            <label class=\"l-input-small\">Obyek</label>

            <div class=\"field\">

                ".comboData("SELECT * FROM mst_data WHERE kodeCategory='OBY' and kodeInduk='$getKode' order by namaData","kodeData", "namaData","inp[id_ruang]"," - Pilih Obyek -",$r[id_ruang],"onchange=\"changeObyek(this.value,'".getPar($par,"mode")."');\"", "310px","chosen-select")."

            </div>

        </p>

        <p>

            <label class=\"l-input-small\">Sub Obyek</label>

            <div class=\"field\">

                ".comboData("SELECT * FROM aktifitas_sasaran  where layanan='$getKode' $whereCombo2 order by sasaran","id_sasaran", "sasaran","inp[id_sub_ruang]","- Pilih Sub Obyek -",$r[id_sub_ruang],"", "310px","chosen-select")."

            </div>

        </p>



    </fieldset>
    <fieldset>

        <legend>Aktifitas</legend>

        

        <div>

           <p>

            <label class=\"l-input-small\">Aktifitas</label>

            <div class=\"field\">

                <input type=\"hidden\" id=\"inp[id_setting]\" name=\"inp[id_setting]\"  value=\"".$r[id_setting]."\" class=\"longinput\" >



                <input type=\"text\" id=\"inp[aktifitas]\" name=\"inp[aktifitas]\"  value=\"".$r[aktifitas]."\" class=\"longinput\" >

            </div>

        </p>
        <p>

            <label class=\"l-input-small\">Kategori</label>

            <div class=\"field\">

                ".comboData("SELECT * FROM mst_data WHERE kodeCategory='KKK' and kodeInduk='$getKode'  order by namaData","kodeData", "namaData","inp[kategori]"," - Pilih Kategori -",$r[kategori],"", "310px","chosen-select")."

            </div>

        </p>
        <p>  

            <label class=\"l-input-small\">Kontrol Model</label>

            <div class=\"field\">

                <div class=\"sradio\" style=\"padding-top:5px;padding-left:8px;\">

                    ".dropdown("inp[model]", $array_model, $r['model'], "style='width:310px;'")."

                </div>

            </div>

        </p>
        <div id=\"parameter\"  style=\"display: none;\">
        <style>
            #inp_satuan__chosen{
                min-width:100px;
            }
        </style>
            <p>

                <label class=\"l-input-small\">Parameter</label>

                <div class=\"field\">
                <span id=\"parameter3\"  style=\"display: none;\" >	
                	<input type=\"text\" id=\"inp[model_note]\" name=\"inp[model_note]\" style=\"width: 297px;\" value=\"". $r['model_note'] ."\" />
                </span>
                <span id=\"parameter2\"  style=\"display: none;\" >
                   
                  	<input type=\"text\" id=\"inp[model1]\" name=\"inp[model1]\" style=\"width: 115px;\" value=\"". $r['model_par1'] ."\" />
               		 &emsp; s.d &emsp;
                    <input type=\"text\" id=\"inp[model2]\" name=\"inp[model2]\" style=\"width: 115px;\" value=\"". $r['model_par2'] ."\" />
                </span>
                </div>

            </p>

             <p>

                <label class=\"l-input-small\">Satuan</label>

                <div class=\"field\">

                    ".comboData("SELECT * FROM mst_data WHERE kodeCategory='LKS' order by namaData","kodeData", "namaData","inp[satuan]","- Pilih Satuan -",$r['satuan'],"", "100px","chosen-select")."

                </div>

            </p>

        </div>


       

        <p>

            <label class=\"l-input-small\">Keterangan</label>

            <div class=\"field\">

                <textarea rows=\"3\"  class=\"longinput\" name=\"inp[keterangan]\">".$r[keterangan]."</textarea>

            </div>

        </p>

        <table width=\"100%\">
            <tr>
                <td width=\"50%\">
                    <p>

                        <label class=\"l-input-small\" style=\"width:210px;\">Tipe</label>

                        <div class=\"field\">

                            <input type=\"text\" id=\"inp[tipe]\" name=\"inp[tipe]\" class=\"smallinput\" value=\"". $r['tipe'] ."\" />

                        </div>

                    </p>
                </td>
                <td width=\"50%\">
                    <p>

                        <label class=\"l-input-small\" style=\"width:120px;\">Kode</label>

                        <div class=\"field\">

                            <input type=\"text\" id=\"inp[kode]\" name=\"inp[kode]\" class=\"smallinput\" value=\"". $r['kode'] ."\" />

                        </div>

                    </p>
                </td>
            </tr>
        </table>
        <p>

            <label class=\"l-input-small\">Interval</label>

            <div class=\"field\">

                ".comboData("SELECT * FROM mst_data WHERE kodeCategory='ISK'   order by namaData","kodeData", "namaData","inp[interval]"," - Pilih interval -",$r[interval],"", "310px","chosen-select")."

            </div>

        </p>
        <p>

            <label class=\"l-input-small\" >Urutan</label>

            <div class=\"field\">

                <input type=\"text\" id=\"inp[urutan]\" name=\"inp[urutan]\" class=\"vsmallinput\" value=\"". $urutan ."\" style=\"width:30px; text-align:right;\" />

            </div>

        </p>


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
        jQuery(\"#parameter2\").fadeIn(0);
        jQuery(\"#parameter3\").fadeOut(0);
        break;

        case '2':
        jQuery(\"#parameter\").fadeIn(0);
        jQuery(\"#parameter3\").fadeIn(0);
        jQuery(\"#parameter2\").fadeOut(0);
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



function delete(){

    global $s, $inp, $par, $_submit, $_submit2, $_submit3, $_submit4, $menuAccess;



    $sql = "DELETE FROM mst_aktifitas WHERE id_setting = '$par[id_setting]'";

    db($sql);

    echo "<script>closeBox();</script>";

    echo "<script>alert('Data telah dihapus!')</script>";

    echo "<script>window.location.href='index.php?par[mode]=detail".getPar($par,"mode, id_setting,id_sub_ruang")."';</script>";

}


// function view()
// {

//     global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m,$mode;

//     $getKode = getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

//     $array_model = array("Checklist", "Parameter", "Isian");

//     // if(!isset($par[filter]))
//     //     $par[filter]=getField("SELECT kodeData from mst_data where kodeCategory='OBY' and kodeInduk='$getKode' order by kodeData ASC");

//     echo "
//     <script src=\"sources/js/default.js\"></script>
    
//     <div class=\"pageheader\">

//         <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
        
//         " . getBread() . "
        
//         <span class=\"pagedesc\">&nbsp;</span>

//     </div>";
    
//     echo "
//     <div id=\"contentwrapper\" class=\"contentwrapper\">

//         <form action=\"\" method=\"POST\" id=\"MyForm\" class=\"stdform\" autocomplete=\"off\">

//             <div id=\"pos_l\" style=\"float:left;\">

                

//             </div>  
            
//             <div id=\"pos_r\" style=\"float:right;\">

//             <a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>

            

//          </div>

//      </form>



//      <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">

//         <thead>

//             <tr>

//                 <th width=\"20\" style=\"vertical-align:middle;\">No</th>

//                 <th style=\"vertical-align:middle;\">OBYEK</th>

//                 <th width=\"70\" style=\"vertical-align:middle;\">AKTIFITAS</th>

//                 ";

//                 echo "

//             </tr>

//         </thead>
//         <tbody>
//             ";
//             // $where1="";
//             // if(!empty($par[filter]))
//             //     $where1.="and subyek = '$par[filter]'";
//             $no1=0;
//             $sqll=db("SELECT * from mst_data where kodeInduk='$getKode' and kodeCategory='OBY' and kodeData='$par[filtergo]' order by urutanData asc");
//             while ($rr = mysql_fetch_assoc($sqll))
//             {
//                 $no++;
//                 echo "
//                 <tr>
//                     <td>$no. </td>
//                     <td >$rr[namaData]</td>
//                     <td style=\"vertical-align:middle; text-align:center;\"><a href=\"?par[mode]=detail&par[filter]=$rr[kodeData]".getPar($par, "mode")."\" class=\"detail\" title=\"View Data\" ></a></td>
//                 </tr>
//                 ";
//                 // $sWhere = " where t1.id_setting is not null and t2.kodeInduk='$getKode' and id_sub_ruang='$rr[id_sasaran]'";

//             // if (!empty($par['filter'])) $sWhere.= " and t1.id_ruang = $par[filter]";

//                 $sql = "SELECT * from aktifitas_sasaran t1 where layanan='$getKode' and subyek = '$rr[kodeData]' order by urut asc";

//                 $res = db($sql);

//                 $ret = array(); 
//                 $no1=0;
//                 while ($r = mysql_fetch_assoc($res))
//                 {
//                     $no1++;
//                     // $r[id_sub_ruang] = getField("SELECT sasaran FROM aktifitas_sasaran WHERE id_sasaran = '$r[id_sub_ruang]'");
//                     // $r[satuan]       = getField("SELECT namaData from mst_data where kodeData='$r[satuan]'");
                    
//                     // if($r[model] == '1'){
//                     //     $r[model]        = $array_model[$r[model]]."<br/> <small>".$r[model_par1]." sd ".$r[model_par2]."</small>";
//                     // }else{
//                     //     $r[model]        = $array_model[$r[model]];
//                     // }
//             // $r[layanan] = "-";


//                     // $r[status] = $r[status] == 't' ? "<img src=\"".APP_URL."/styles/images/t.png\" title='Aktif'>" : "<img src=\"".APP_URL."/styles/images/f.png\" title='Tidak Aktif'>";

//                     $getTotal = getField("SELECT count(*) FROM mst_aktifitas t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) left join mst_data t3 on(t1.kategori=t3.kodeData)   where t1.id_setting is not null and t2.kodeInduk='$getKode' and id_sub_ruang='$r[id_sasaran]' order by urutan asc");
//                     echo "

//                     <tr>
//                         <td style=\"vertical-align:middle; text-align:left;\"  ></td>
//                         <td style=\"vertical-align:middle; text-align:left;\"  >$no1. $r[sasaran]</td>
//                         <td style=\"vertical-align:middle; text-align:right; \">".getAngka($getTotal)."</td>
                        
//                     </tr>
//                     ";

//                     $getTotalAll += $getTotal;

//                 }
//             }
//             echo "
//             </tbody
//             <tfoot>
//                     <tr>
//                         <td colspan=\"2\" style=\"text-align:right;\">Total</td>
//                         <td style=\"vertical-align:middle; text-align:right; \">".getAngka($getTotalAll)."</td>
                        
//                     </tr>
//             </tfoot>
//         </table>

//         <br />
//              <table style=\"width:100%\">
//                 <tr style=\"width:100%\">
//                     <td style=\"width:50%; vertical-align:top; padding-left:15px; padding-right:15px;\"\">
//                     <div style=\"border: 1px solid #CCC; margin: 4px 4px 4px 0px; align-item: center;\">
//                     <div style=\"padding: 8px; border-bottom: 1px solid #ccc; text-align: center;\">

//                     <b>JENIS AKTIFITAS</b>


//                     </div>
//                         <style>
//                         #chartdiv1 {
//                                     width   : 100%;
//                                     height  : 400px;
//                                 }                                       
//                         </style>
//                         <script type=\"text/javascript\">
//                                     var chart1 = AmCharts.makeChart(\"chartdiv1\",
//                                         {
//                                             \"type\": \"pie\",
//                                             \"balloonText\": \"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>\",
//                                             \"labelText\": \"[[title]]<br> [[percents]]%\",
//                                             \"titleField\": \"category\",
//                                             \"valueField\": \"column-1\",
//                                             \"fontSize\": 8,
//                                             \"allLabels\": [],
//                                             \"balloon\": {},
//                                             \"legend\": {
//                                                 \"enabled\": true,
//                                                 \"align\": \"center\",
//                                                 \"markerType\": \"circle\"
//                                             },
//                                             \"titles\": [],
//                                             \"dataProvider\": [
//                                                  ";
//                                             // $area    = empty($par['area']) ? "" : "AND `id_area` = '$par[area]'";
//                                             $getID =getField("SELECT kodeData from mst_data where kodeMaster='$mode'");
//                                             $sql   =db("SELECT * FROM `mst_data` WHERE `kodeCategory` = 'KKK' and kodeInduk='$getID' order by urutanData limit 10");
//                                             while ($r=mysql_fetch_array($sql)) {
                        
//                                             echo "
//                                                 {
//                                                     \"category\": \"".$r[namaData]."\",
//                                                     \"column-1\": ".getField("SELECT count(*) from aktifitas_setting t1 left join mst_aktifitas t2 on(t1.id_sub_ruang=t2.id_sub_ruang and t1.aktifitas=t2.aktifitas) where t2.kategori='$r[kodeData]'")."
//                                                 },
//                                             ";
//                                             }
//                                             echo "
                                                
//                                             ]
//                                         }
//                                     );
//                         </script>
//                     <div id=\"chartdiv1\"></div>
//                     </div>
                    
//                 </tr>
//             </table>
//             <br />
//              <table style=\"width:100%\">
//                 <tr>
//                     <td style=\"width:100%\">
//                            <style>
//                     #chartdiv3 {
//                             width   : 100%;
//                             height  : 400px;
//                         }                                       
//                     </style>
//                     <script type=\"text/javascript\">
//                         var chart3 = AmCharts.makeChart(\"chartdiv3\",
//                         {
//                             \"type\": \"serial\",
//                             \"categoryField\": \"category\",
//                             \"startDuration\": 1,
//                             \"categoryAxis\": {
//                                 \"gridPosition\": \"start\"
//                             },
//                             \"trendLines\": [],
//                             \"graphs\": [
//                                 {
//                                     \"balloonText\": \"[[title]] of [[category]]:[[value]]\",
//                                     \"fillAlphas\": 1,
//                                     \"id\": \"AmGraph-1\",
//                                     \"title\": \"OBYEK\",
//                                     \"type\": \"column\",
//                                     \"valueField\": \"column-1\"
//                                 }
//                             ],
//                             \"guides\": [],
//                             \"valueAxes\": [
//                                 {
//                                     \"id\": \"ValueAxis-1\",
//                                     \"stackType\": \"regular\",
//                                     \"title\": \"TOTAL AKTIFITAS\"
//                                 }
//                             ],
//                             \"allLabels\": [],
//                             \"balloon\": {},
//                             \"legend\": {
//                                 \"enabled\": true,
//                                 \"useGraphSettings\": true
//                             },
//                             \"titles\": [
//                                 {
//                                     \"id\": \"Title-1\",
//                                     \"size\": 15,
//                                     \"text\": \"TOTAL AKTIFITAS\"
//                                 }
//                             ],
//                             \"dataProvider\": [
//                                 ";
//                                 $sql3="SELECT * from mst_data where kodeInduk='$getKode'and kodeCategory='OBY' order by urutanData asc";
//                                 $res3=db($sql3);
//                                 while ($r3=mysql_fetch_array($res3)) {
                                    
//                                 echo "
//                                 {
//                                     \"category\": \"$r3[namaData]\",
//                                     \"column-1\": ".getField("SELECT count(*) FROM mst_aktifitas t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) left join mst_data t3 on(t1.kategori=t3.kodeData)   where t1.id_setting is not null and t2.kodeInduk='$getKode' and id_ruang='$r3[kodeData]' order by urutan asc")."
//                                 },
//                                 ";
//                                 }
//                                 echo "
//                             ]
//                         }
//                         );
//                     </script>
//                     <div id=\"chartdiv3\"></div>
//                     </td>
//                 <tr>
//              </table>

//     </div>
    
    
//     ";

// if($par[mode] == "xls"){            
//     xls();          
//     echo "<iframe src=\"download.php?d=exp&f=REPORT MASTER AKTIFITAS.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
// }
// }


function lihat()

{

    global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m,$mode;

    $getKode = getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

    $array_model = array("Checklist", "Parameter", "Isian");

    // if(!isset($par[filter]))
    //     $par[filter]=getField("SELECT kodeData from mst_data where kodeCategory='OBY' and kodeInduk='$getKode' order by kodeData ASC");

    echo "
    <script src=\"sources/js/default.js\"></script>
    
    <div class=\"pageheader\">

        <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
        
        " . getBread() . "
        
        <span class=\"pagedesc\">&nbsp;</span>

    </div>";
    
    echo "
    <div id=\"contentwrapper\" class=\"contentwrapper\">

        <form action=\"\" method=\"POST\" id=\"MyForm\" class=\"stdform\" autocomplete=\"off\">

            <div id=\"pos_l\" style=\"float:left;\">

                

            </div>  
            
            <div id=\"pos_r\" style=\"float:right;\">

            <a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>

            

         </div>

     </form>



     <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">

        <thead>

            <tr>

                <th width=\"20\" style=\"vertical-align:middle;\">No</th>

                <th style=\"vertical-align:middle;\">OBYEK</th>

                <th width=\"70\" style=\"vertical-align:middle;\">KATEGORI</th>

                <th width=\"70\" style=\"vertical-align:middle;\">AKTIFITAS</th>

                <th width=\"50\" style=\"vertical-align:middle;\">VIEW</th>
                ";

                echo "

            </tr>

        </thead>
        <tbody>
            ";
            // $where1="";
            // if(!empty($par[filter]))
            //     $where1.="and subyek = '$par[filter]'";
            $no1=0;
            $sqll=db("SELECT * from mst_data where kodeInduk='$getKode'and kodeCategory='OBY' order by urutanData asc");
            while ($rr = mysql_fetch_assoc($sqll))
            {
                $no++;

                $getTotalKat = getField("SELECT count(*) from aktifitas_sasaran t1 where layanan='$getKode' and subyek = '$rr[kodeData]' order by urut asc");
                $getTotalAkt = getField("SELECT count(*) FROM mst_aktifitas t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) left join mst_data t3 on(t1.kategori=t3.kodeData) where t1.id_setting is not null and t2.kodeInduk='$getKode'  and id_ruang='$rr[kodeData]'");
                echo "
                <tr>
                    <td>$no. </td>
                    <td>$rr[namaData]</td>
                    <td align=\"right\">".getAngka($getTotalKat)."</td>
                    <td align=\"right\">".getAngka($getTotalAkt)."</td>
                    <td style=\"vertical-align:middle; text-align:center;\"><a href=\"?par[mode]=detail&par[filter]=$rr[kodeData]".getPar($par, "mode")."\" class=\"detail\" title=\"View Data\" ></a></td>
                    
                </tr>
                ";
               
               $getTotalKatAll += $getTotalKat;
               $getTotalAktAll += $getTotalAkt;
            }
            echo "
            </tbody
            <tfoot>
                <tr>
                    <td colspan=\"2\" style=\"text-align:right;\">Total</td>
                    
                    <td align=\"right\">".getAngka($getTotalKatAll)."</td>
                    <td align=\"right\">".getAngka($getTotalAktAll)."</td>
                    <td style=\"vertical-align:middle; text-align:center;\"></td>
                    
                </tr>
            </tfoot>

        </table>

        <br />
             <table style=\"width:100%\">
                <tr style=\"width:100%\">
                    <td style=\"width:50%; vertical-align:top; padding-left:15px; padding-right:15px;\"\">
                    <div style=\"border: 1px solid #CCC; margin: 4px 4px 4px 0px; align-item: center;\">
                    <div style=\"padding: 8px; border-bottom: 1px solid #ccc; text-align: center;\">

                    <b>JENIS AKTIFITAS</b>


                    </div>
                        <style>
                        #chartdiv1 {
                                    width   : 100%;
                                    height  : 400px;
                                }                                       
                        </style>
                        <script type=\"text/javascript\">
                                    var chart1 = AmCharts.makeChart(\"chartdiv1\",
                                        {
                                            \"type\": \"pie\",
                                            \"balloonText\": \"[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>\",
                                            \"labelText\": \"[[title]]<br> [[percents]]%\",
                                            \"titleField\": \"category\",
                                            \"valueField\": \"column-1\",
                                            \"fontSize\": 8,
                                            \"allLabels\": [],
                                            \"balloon\": {},
                                            \"legend\": {
                                                \"enabled\": true,
                                                \"align\": \"center\",
                                                \"markerType\": \"circle\"
                                            },
                                            \"titles\": [],
                                            \"dataProvider\": [
                                                 ";
                                            // $area    = empty($par['area']) ? "" : "AND `id_area` = '$par[area]'";
                                            $getID =getField("SELECT kodeData from mst_data where kodeMaster='$mode'");
                                            $sql   =db("SELECT * FROM `mst_data` WHERE `kodeCategory` = 'KKK' and kodeInduk='$getID' order by urutanData limit 10");
                                            while ($r=mysql_fetch_array($sql)) {
                        
                                            echo "
                                                {
                                                    \"category\": \"".$r[namaData]."\",
                                                    \"column-1\": ".getField("SELECT count(*) from aktifitas_setting t1 left join mst_aktifitas t2 on(t1.id_sub_ruang=t2.id_sub_ruang and t1.aktifitas=t2.aktifitas) where t2.kategori='$r[kodeData]'")."
                                                },
                                            ";
                                            }
                                            echo "
                                                
                                            ]
                                        }
                                    );
                        </script>
                    <div id=\"chartdiv1\"></div>
                    </div>
                    
                </tr>
            </table>
            <br />
             <table style=\"width:100%\">
                <tr>
                    <td style=\"width:100%\">
                           <style>
                    #chartdiv3 {
                            width   : 100%;
                            height  : 400px;
                        }                                       
                    </style>
                    <script type=\"text/javascript\">
                        var chart3 = AmCharts.makeChart(\"chartdiv3\",
                        {
                            \"type\": \"serial\",
                            \"categoryField\": \"category\",
                            \"startDuration\": 1,
                            \"categoryAxis\": {
                                \"gridPosition\": \"start\"
                            },
                            \"trendLines\": [],
                            \"graphs\": [
                                {
                                    \"balloonText\": \"[[title]] of [[category]]:[[value]]\",
                                    \"fillAlphas\": 1,
                                    \"id\": \"AmGraph-1\",
                                    \"title\": \"OBYEK\",
                                    \"type\": \"column\",
                                    \"valueField\": \"column-1\"
                                }
                            ],
                            \"guides\": [],
                            \"valueAxes\": [
                                {
                                    \"id\": \"ValueAxis-1\",
                                    \"stackType\": \"regular\",
                                    \"title\": \"TOTAL AKTIFITAS\"
                                }
                            ],
                            \"allLabels\": [],
                            \"balloon\": {},
                            \"legend\": {
                                \"enabled\": true,
                                \"useGraphSettings\": true
                            },
                            \"titles\": [
                                {
                                    \"id\": \"Title-1\",
                                    \"size\": 15,
                                    \"text\": \"TOTAL AKTIFITAS\"
                                }
                            ],
                            \"dataProvider\": [
                                ";
                                $sql3="SELECT * from mst_data where kodeInduk='$getKode'and kodeCategory='OBY' order by urutanData asc";
                                $res3=db($sql3);
                                while ($r3=mysql_fetch_array($res3)) {
                                    
                                echo "
                                {
                                    \"category\": \"$r3[namaData]\",
                                    \"column-1\": ".getField("SELECT count(*) FROM mst_aktifitas t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) left join mst_data t3 on(t1.kategori=t3.kodeData)   where t1.id_setting is not null and t2.kodeInduk='$getKode' and id_ruang='$r3[kodeData]' order by urutan asc")."
                                },
                                ";
                                }
                                echo "
                            ]
                        }
                        );
                    </script>
                    <div id=\"chartdiv3\"></div>
                    </td>
                <tr>
             </table>

    </div>
    
    
    ";

if($par[mode] == "xls"){            
    xls();          
    echo "<iframe src=\"download.php?d=exp&f=REPORT MASTER AKTIFITAS.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
}

}

function detail()

{

    global $s,$par,$fFile,$menuAccess,$cUsername,$sUser,$sGroup,$arrTitle,$arrParam,$m;

    $getKode = getField("SELECT kodeData from mst_data where kodeMaster='".getField("SELECT parameterMenu from app_menu where kodemenu='$s'")."'");

    $array_model = array("Checklist", "Parameter", "Isian");

    if(!isset($par[filter]))
        $par[filter]=getField("SELECT kodeData from mst_data where kodeCategory='OBY' and kodeInduk='$getKode' order by kodeData ASC");

    

    echo "
    <script src=\"sources/js/default.js\"></script>
    
    <div class=\"pageheader\">

        <h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
        
        " . getBread() . "
        
        <span class=\"pagedesc\">&nbsp;</span>

    </div>";
    
    echo "
    <div id=\"contentwrapper\" class=\"contentwrapper\">

        <form action=\"\" method=\"POST\" id=\"MyForm\" class=\"stdform\" autocomplete=\"off\">

            <div id=\"pos_l\" style=\"float:left;\">

                " .comboData("SELECT * from mst_data  where kodeCategory='OBY' and kodeInduk='$getKode' order by namaData","kodeData","namaData","bSearch","- Pilih Obyek -","$par[filter]","","210px;","chosen-select", "") ."

            </div>  
            
            <div id=\"pos_r\" style=\"float:right;\">

            <a href=\"?par[mode]=xls".getPar($par,"mode")."\" class=\"btn btn1 btn_inboxi\"><span>Export Data</span></a>

             <a href=\"index.php?par[mode]=form".getPar($par,"mode")."\" id=\"tambahData\" class=\"btn btn1 btn_document\"><span>Tambah Data</span></a>

         </div>

     </form>



     <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"datatable\">

        <thead>

            <tr>

                <th width=\"20\" style=\"vertical-align:middle;\">No</th>

                <th style=\"vertical-align:middle;\">AKTIFITAS</th>

                <th width=\"100\" style=\"vertical-align:middle;\">KATEGORI</th>

                <th width=\"70\" style=\"vertical-align:middle;\">MODEL</th>

                <th width=\"50\" style=\"vertical-align:middle;\">SATUAN</th>

                <th width=\"50\" style=\"vertical-align:middle;\">INTERVAL</th>

                <th width=\"40\" style=\"vertical-align:middle;\">ORDER</th>

                <th width=\"20\" style=\"vertical-align:middle;\">S</th>";

                if(isset($menuAccess[$s]['edit']) || isset($menuAccess[$s]['delete'])) {

                    echo "<th width=\"20\" style=\"vertical-align: middle\">Kontrol</th>";

                }

                echo "

            </tr>

        </thead>
        <tbody>
            ";
            

            $where1="";
            if(!empty($par[filter]))
                $where1.="and subyek = '$par[filter]'";
            
            $where2="";
            if(isset($par[filter2]))
                $where2="and id_sasaran='$par[filter2]'";

            $no1=0;
            $sqll=db("SELECT * from aktifitas_sasaran t1 where layanan='$getKode' $where1 $where2 order by urut asc");
            while ($rr = mysql_fetch_assoc($sqll))
            {
                $no++;
                echo "
                <tr>
                    <td>$no. </td>
                    <td colspan=\"6\">$rr[sasaran]</td>
                    <td style=\"text-align:center;\"><a href='index.php?par[mode]=form&par[id_sub_ruang]=$rr[id_sasaran]".getPar($par,"mode,id_sub_ruang")."' class='add' title='Edit Data'></a></td>
                    <td></td>
                </tr>
                ";
                $sWhere = " where t1.id_setting is not null and t2.kodeInduk='$getKode' and id_sub_ruang='$rr[id_sasaran]'";

            // if (!empty($par['filter'])) $sWhere.= " and t1.id_ruang = $par[filter]";

                $sql = "SELECT t1.*,t2.namaData as obyek,t3.namaData as kategori_akrifitas FROM mst_aktifitas t1 join mst_data t2 on(t1.id_ruang=t2.kodeData) left join mst_data t3 on(t1.kategori=t3.kodeData)   $sWhere order by urutan asc";

                $res = db($sql);

                $ret = array(); 
                $no1=0;
                while ($r = mysql_fetch_assoc($res))
                {
                    $no1++;
                    $r[id_sub_ruang] = getField("SELECT sasaran FROM aktifitas_sasaran WHERE id_sasaran = '$r[id_sub_ruang]'");
                    $r[satuan]       = getField("SELECT namaData from mst_data where kodeData='$r[satuan]'");
                    $r[interval]       = getField("SELECT namaData from mst_data where kodeData='$r[interval]'");
                    
                    if($r[model] == '1'){
                        $r[model]        = $array_model[$r[model]]."<br/> <small>".$r[model_par1]." sd ".$r[model_par2]."</small>";
                    }else if($r[model] == '2'){
                        $r[model]        = $array_model[$r[model]]."<br/> <small>".$r[model_note]."</small>";
                    }else{
                        $r[model]        = $array_model[$r[model]];
                    }
            // $r[layanan] = "-";


                    $r[status] = $r[status] == 't' ? "<img src=\"".APP_URL."/styles/images/t.png\" title='Aktif'>" : "<img src=\"".APP_URL."/styles/images/f.png\" title='Tidak Aktif'>";

                    echo "

                    <tr>
                        <td></td>
                        <td style=\"vertical-align:middle;\">$no1. $r[aktifitas]</td>
                        <td style=\"vertical-align:middle;\">$r[kategori_akrifitas]</td>
                        <td style=\"vertical-align:middle;\">$r[model]</td>
                        <td style=\"vertical-align:middle;\">$r[satuan]</td>
                        <td style=\"vertical-align:middle;\">$r[interval]</td>
                        <td style=\"text-align:center; vertical-align:middle;\">$r[urutan]</td>
                        <td style=\"text-align:center; vertical-align:middle;\">$r[status]</td>
                        <td style=\"text-align:center; vertical-align:middle;\">
                            <a href='index.php?par[mode]=form&par[id_setting]=$r[id_setting]".getPar($par,"mode,id_setting")."' class='edit' title='Edit Data'></a>
                            <a href='index.php?par[mode]=delete&par[id_setting]=$r[id_setting]".getPar($par,"mode,id_setting")."' onclick=\"return confirm('Anda yakin akan menghapus data ini ?')\" class='delete' title='Delete Data'></a>

                        </td>
                    </tr>
                    ";
                }
            }
            echo "
            </tbody

        </table>

    </div>
    
    
    <script type=\"text/javascript\">

        function joinTable(){
            jQuery(\".dataTables_scrollHeadInner > table\").css(\"border-bottom\",\"0\").css(\"padding-bottom\",\"0\").css(\"margin-bottom\",\"0\");
            jQuery(\".dataTables_scrollBody > table\").css(\"border-top\",\"0\").css(\"margin-top\",\"-5px\");
        }

        jQuery(document).ready(function () {

            jQuery(\"#bSearch\").change(function(){

                var filter = jQuery(\"#bSearch\").val();

                window.location='?par[filter]='+filter+'".getPar($par,'filter,filter2')."';

            });

});

</script>";

if($par[mode] == "xls"){            
    xls();          
    echo "<iframe src=\"download.php?d=exp&f=REPORT MASTER AKTIFITAS.xls\" frameborder=\"0\" width=\"0\" height=\"0\"></iframe>";
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



//     $sWhere = " where id_setting is not null and kodeInduk='$getKode'";

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

//         <a href='index.php?par[mode]=form&par[id_setting]=$r[id_setting]".getPar($par,"mode")."' class='edit' title='Edit Data'></a>



//         <a href='index.php?par[mode]=delete&par[id_setting]=$r[id_setting]".getPar($par,"mode")."' onclick=\"return confirm('Anda yakin akan menghapus data ini ?')\" class='delete' title='Delete Data'></a>

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