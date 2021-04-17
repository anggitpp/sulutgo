        <?php
        if(!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";          
        $fFile = "files/upload/";
        $fExport = "files/export/";
        $fLog = "files/Rencana Tahunan.log";


        function lihat(){
            global $s,$inp,$par,$arrTitle,$arrParam,$arrParameter,$menuAccess,$cIdPegawai;

            $arrMinggu = array(0,3,3,3,3,3,3,3,3,3,3,3,3);


            if(empty($par[tahunRencana])) $par[tahunRencana] = date('Y');
            

            $text.="<div class=\"pageheader\">
            <h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
            ".getBread()."

        </div>    
            <div id=\"contentwrapper\" class=\"contentwrapper\">
                <form id=\"form\" action=\"\" method=\"post\" class=\"stdform\">
                    <div style=\"position:absolute; right:0; top:0; margin-top:10px; margin-right:20px;\">              
                        ".comboYear("par[tahunRencana]", $par[tahunRencana], "", "onchange=\"document.getElementById('form').submit();\"")."
                    </div>
                    
                </form>
                
                    <br clear=\"all\" />
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dynscroll\">
                        <thead>
                            <tr>
                                <th rowspan=\"3\" width=\"20\" style=\"vertical-align:middle;\">No.</th>
                                <th rowspan=\"3\" style=\"min-width:350px; vertical-align:middle;\">nama</th>
                                <th rowspan=\"3\" style=\"min-width:300px; vertical-align:middle;\">jabatan</th>
                                <th colspan=\"36\" style=\"min-width:150px; vertical-align:middle;\">Tahun ".$par[tahunRencana]."</th>
                                <th rowspan=\"3\" style=\"min-width:50px; vertical-align:middle;\">Total</th>
                            </tr>
                            <tr>";
                                for($i=1; $i<=12; $i++){                
                                    $text.="<th style=\"min-width:20px;\" colspan=\"".$arrMinggu[$i]."\">".getBulan($i)."</th>";
                                }
                                $text.="</tr>
                                <tr>";
                                    for($i=1; $i<=12; $i++){
                                        
                                            $text.="<th style=\"min-width:20px;\">C</th>";
                                            $text.="<th style=\"min-width:20px;\">M</th>";
                                            $text.="<th style=\"min-width:20px;\">C</th>";
                                        
                                    }
                                    $text.="</tr>
                                </thead>
                                <tbody>";

                                            $kodeCouching = getField("SELECT kodeData FROM mst_data WHERE kodeMaster = 'ECH'");

                                            $sWhere = "WHERE f.atasan_langsung ='$cIdPegawai'";
                                            $sql="select a.id as idCMC, a.peserta as pesertaCMC, a.tanggal as tanggalCMC,a.judul as bahasan, b.name as peserta, c.reg_no,c.name as atasan,d.idPegawai,f.*,e.parent_id,e.pos_name,g.name as peserta2,h.id_cmc,h.id_pegawai as idpeg
                                                from pen_cmc as a
                                                right outer join emp as b on(a.peserta = b.id)
                                                right outer join emp as c on(a.atasan = c.id)
                                                right outer join app_user as d on(a.atasan = d.idPegawai OR a.peserta = d.idPegawai)
                                                right outer join emp_phist as e on(a.peserta = e.parent_id)
                                                right outer join pen_pegawai as f on(b.id = f.idPegawai or c.id = f.idPegawai) 
                                                RIGHT OUTER JOIN emp as g on(g.id = f.idPegawai)
 												LEFT OUTER JOIN pen_cmc_peserta as h on(h.id_pegawai = f.idPegawai)
                                           		$sWhere
                                                 group by peserta";
                                    $res=db($sql);
                                    $no=0;
                                    while($r=mysql_fetch_array($res)){
                                    $no++;
                                    list($tahun, $bulan, $tanggal) = explode("-", $r[tanggalCMC]);

                                    $text.="<tr>
                                            <td align=\"center\">".$no.".</td>
                                            <td>".$r[peserta2]."</td>
                                            <td>".$r[pos_name]."</td>";
                                            $idField=5;
                                            $totsamping = 0;
                                            $totalcoaching = 0;
                                            $totalmentoring = 0;
                                            $totalcounseling = 0;
                                            for($i=1; $i<=12; $i++){
                                                $coaching = getField("SELECT COUNT(*) from pen_cmc where peserta ='$r[pesertaCMC]' and month(tanggal) = $i and year(tanggal)='$par[tahunRencana]' and kategori='987'");
                                                $mentoring = getField("SELECT COUNT(*) from pen_cmc as a join pen_cmc_peserta as b on (a.id = b.id_cmc) where b.id_pegawai ='$r[idpeg]' and month(tanggal) = $i and year(tanggal)='$par[tahunRencana]' and kategori='988'");
                                                $counseling = getField("SELECT COUNT(*) from pen_cmc where peserta ='$r[pesertaCMC]' and month(tanggal) = $i and year(tanggal)='$par[tahunRencana]' and kategori='989'");
                                                 
                                                $coaching1 = getField("SELECT COUNT(*) from pen_cmc where month(tanggal) = $i and year(tanggal)='$par[tahunRencana]' and kategori='987'");
                                                $mentoring1 = getField("SELECT COUNT(*) from pen_cmc as a join pen_cmc_peserta as b on (a.id = b.id_cmc) where month(tanggal) = $i and year(tanggal)='$par[tahunRencana]' and kategori='988'");
                                                $counseling1 = getField("SELECT COUNT(*) from pen_cmc where month(tanggal) = $i and year(tanggal)='$par[tahunRencana]' and kategori='989'");

                                                if($coaching =="0"){
                                                    $coaching ="";
                                                }
                                                if($mentoring =="0"){
                                                    $mentoring ="";
                                                }
                                                if($counseling =="0"){
                                                    $counseling ="";
                                                }


                                        
                                                $text.="<td align=\"center\">$coaching</td>";
                                                $text.="<td align=\"center\">$mentoring</td>";
                                                $text.="<td align=\"center\">$counseling</td>";

                                                $totsamping+= $coaching + $mentoring + $counseling;
                                                $totalcoaching+=  $coaching1;
                                                $totalmentoring+= $mentoring1;
                                                $totalcounseling+= $counseling1;
                                            }           

                                            $text.="<td align=\"center\">".$totsamping."</td>
                                        </tr>";         
                                    }


                            $text.="</tbody>
                        </table>
                    </div>";

                
                return $text;
            }           


        function getContent($par){
            global $s,$_submit,$menuAccess;
            switch($par[mode]){                     
                case "end":
                if(isset($menuAccess[$s]["add"])) $text = endProses();
                break;
                case "dat":
                if(isset($menuAccess[$s]["add"])) $text = setData();
                break;
                case "tab":
                if(isset($menuAccess[$s]["add"])) $text = setFile();
                break;

                case "upl":
                $text = isset($menuAccess[$s]["add"]) ? form() : lihat();
                break;          
                default:
                $text = lihat();
                break;
            }
            return $text;
        }   
        ?>