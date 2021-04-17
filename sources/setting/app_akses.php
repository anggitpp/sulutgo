<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";

/*
$arrSite = arrayQuery("select kodeSite, namaSite from app_site where statusSite='t' order by urutanSite");
$sql = "select * from app_menu where statusMenu='t' order by kodeMenu";
$res = db($sql);
while ($r = mysql_fetch_array($res)) {  
  if (empty($r[kodeInduk]))
  $arrParent["$r[kodeSite]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

  $arrMenu["$r[kodeInduk]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];  
}
*/

function ubah() {
  global $s,$db, $inp, $par, $group, $cUsername, $cGroup, $kodeModul;
  repField(); 

  $arrMenu = arrayQuery("select kodeMenu, kodeMenu from app_group_menu where kodeGroup='$cGroup' AND kodeMenu IN (SELECT kodeMenu FROM app_menu WHERE kodeModul = '$kodeModul')");
  if (is_array($arrMenu)) {    
    while (list($kodeMenu) = each($arrMenu)) {
		db("delete from app_group_menu where kodeGroup='$par[kodeGroup]' and kodeMenu='$kodeMenu'");
	}
  }
  
  if (is_array($group)){ 
    while (list($statusGroup) = each($group)) {
      if (is_array($group[$statusGroup])) {
        while (list($kodeMenu) = each($group[$statusGroup])) {		 
          $sql = "insert into app_group_menu (kodeGroup, kodeMenu, statusGroup, createBy, createTime) values ('$par[kodeGroup]', '$kodeMenu', '$statusGroup', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
          db($sql);
        }
      }
    }
  }
  echo "<script>closeBox();reloadPage();</script>";
}

function form() {
  global $s,$db, $inp, $par, $arrParent, $arrSite, $arrMenu, $arrTitle, $menuAccess, $cUsername, $sUser, $cntMenu, $kodeGroup;

  $sql = "select * from app_group where kodeGroup='$par[kodeGroup]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);

  if(empty($kodeGroup)) $kodeGroup = $par[kodeGroup];
  $arrGroup = arrayQuery("select kodeMenu,statusGroup,kodeGroup from app_group_menu where kodeGroup='$kodeGroup'");  

  $false = $r[statusGroup] == "f" ? "checked=\"checked\"" : "";
  $true = empty($false) ? "checked=\"checked\"" : "";
  
  $text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">" . $arrTitle[$s] . " : ".$r[namaGroup]."</h1>
					" . getBread(ucwords($par[mode] . " data")) . "
					<ul class=\"hornav\">";
  
  $no=1;
  if (is_array($arrSite)) {
    reset($arrSite);
    while (list($kodeSite, $namaSite) = each($arrSite)) {
	  $current = $no == 1 ? "class=\"current\"" : "";
      $text.="<li $current><a href=\"#$kodeSite\">$namaSite</a></li>";
	  $no++;
    }
  }
  $text.="</ul>
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" enctype=\"multipart/form-data\">	";


  $no=1;
  if (is_array($arrSite)) {
    reset($arrSite);
    while (list($kodeSite, $namaSite) = each($arrSite)) {
	  $display = $no == 1 ? "" : "style=\"display: none;\"";
      $text.="<div id=\"$kodeSite\" class=\"subcontent\"  $display>
							<div style=\"height:250px;overflow-y:auto;width:100%; border:solid 1px #ccc;\">
							<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">					
								<tbody>";

      if (is_array($arrParent[$kodeSite])) {
        asort($arrParent[$kodeSite]);
        reset($arrParent[$kodeSite]);
        while (list($kMenu, $vMenu) = each($arrParent[$kodeSite])) {
          list($urutanMenu, $kodeMenu, $namaMenu, $iconMenu, $kodeTarget, $aksesMenu) = explode("\t", $vMenu);

          $aksesView = substr($aksesMenu, 0, 1);
          $aksesAdd = substr($aksesMenu, 1, 1);
          $aksesEdit = substr($aksesMenu, 2, 1);
          $aksesDelete = substr($aksesMenu, 3, 1);

          #MAZTE-ADD
          if (strlen($aksesMenu > 4)) {
            $aksesAppr1 = substr($aksesMenu, 4, 1);
            $aksesAppr2 = substr($aksesMenu, 5, 1);
            $aksesAppr3 = substr($aksesMenu, 6, 1);
          }
          #END-MAZTE ADD

          $view = $arrGroup[$kodeMenu]["view"] ? "checked=\"checked\"" : "";
          $add = $arrGroup[$kodeMenu]["add"] ? "checked=\"checked\"" : "";
          $edit = $arrGroup[$kodeMenu]["edit"] ? "checked=\"checked\"" : "";
          $delete = $arrGroup[$kodeMenu]["delete"] ? "checked=\"checked\"" : "";

          #MAZTE-ADD
          if (strlen($aksesMenu > 4)) {
            $appr1 = $arrGroup[$kodeMenu]["apprlv1"] ? "checked=\"checked\"" : "";
            $appr2 = $arrGroup[$kodeMenu]["apprlv2"] ? "checked=\"checked\"" : "";
            $appr3 = $arrGroup[$kodeMenu]["apprlv3"] ? "checked=\"checked\"" : "";
          }

		  if ($cntMenu[$kodeMenu] > 0 || $cUsername == $sUser){
			  $text.= "<tr>
													<td style=\"padding-left:5px; padding-top:5px; padding-bottom:5px; padding-left:15px;border-bottom:1px solid #ecebeb;\"><strong>$namaMenu</strong></td>";
			  if (!empty($kodeTarget)) {
				if ($aksesView)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[view][$kodeMenu]\" name=\"group[view][$kodeMenu]\" value=\"$kodeMenu\" $view /> View</td>";
				if ($aksesAdd)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[add][$kodeMenu]\" name=\"group[add][$kodeMenu]\" value=\"$kodeMenu\" $add /> Add</td>";
				if ($aksesEdit)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[edit][$kodeMenu]\" name=\"group[edit][$kodeMenu]\" value=\"$kodeMenu\" $edit /> Edit</td>";
				if ($aksesDelete)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[delete][$kodeMenu]\" name=\"group[delete][$kodeMenu]\" value=\"$kodeMenu\" $delete /> Delete</td>";
				#MAZTE-ADD
				if ($aksesAppr1)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[apprlv1][$kodeMenu]\" name=\"group[apprlv1][$kodeMenu]\" value=\"$kodeMenu\" $appr1 /> Approval Lv1</td>";
				if ($aksesAppr2)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[apprlv2][$kodeMenu]\" name=\"group[apprlv2][$kodeMenu]\" value=\"$kodeMenu\" $appr2 /> Approval Lv2</td>";
				if ($aksesAppr3)
				  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[apprlv3][$kodeMenu]\" name=\"group[apprlv3][$kodeMenu]\" value=\"$kodeMenu\" $appr3 /> Approval Lv3</td>";
				#END MAZTE-ADD      
			  }
			  $text.="</tr>";

			  if (is_array($arrMenu[$kodeMenu])) {
				asort($arrMenu[$kodeMenu]);
				reset($arrMenu[$kodeMenu]);
				while (list($kMenu1, $vMenu1) = each($arrMenu[$kodeMenu])) {
				  list($urutanMenu1, $kodeMenu1, $namaMenu1, $iconMenu1, $kodeTarget1, $aksesMenu1) = explode("\t", $vMenu1);

				  $aksesView1 = substr($aksesMenu1, 0, 1);
				  $aksesAdd1 = substr($aksesMenu1, 1, 1);
				  $aksesEdit1 = substr($aksesMenu1, 2, 1);
				  $aksesDelete1 = substr($aksesMenu1, 3, 1);
				  #MAZTE-ADD
				  if (strlen($aksesMenu1 > 4)) {
					$aksesAppr1 = substr($aksesMenu1, 4, 1);
					$aksesAppr2 = substr($aksesMenu1, 5, 1);
					$aksesAppr3 = substr($aksesMenu1, 6, 1);
				  }
				  #END-MAZTE ADD
				  $view1 = $arrGroup[$kodeMenu1]["view"] ? "checked=\"checked\"" : "";
				  $add1 = $arrGroup[$kodeMenu1]["add"] ? "checked=\"checked\"" : "";
				  $edit1 = $arrGroup[$kodeMenu1]["edit"] ? "checked=\"checked\"" : "";
				  $delete1 = $arrGroup[$kodeMenu1]["delete"] ? "checked=\"checked\"" : "";

				  #MAZTE-ADD
				  if (strlen($aksesMenu1 > 4)) {
					$appr1 = $arrGroup[$kodeMenu1]["apprlv1"] ? "checked=\"checked\"" : "";
					$appr2 = $arrGroup[$kodeMenu1]["apprlv2"] ? "checked=\"checked\"" : "";
					$appr3 = $arrGroup[$kodeMenu1]["apprlv3"] ? "checked=\"checked\"" : "";
				  }
				  #END MAZTE-ADD

				  
				  if ($cntMenu[$kodeMenu1] > 0 || $cUsername == $sUser){
					  $text.= "<tr>
																<td style=\"padding-left:30px; padding-top:5px; padding-bottom:5px;border-bottom:1px solid #ecebeb;\"><i>&middot; $namaMenu1</i></td>";
					  if (!empty($kodeTarget1)) {
						if ($aksesView1)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[view][$kodeMenu1]\" name=\"group[view][$kodeMenu1]\" value=\"$kodeMenu1\" $view1 /> View</td>";
						if ($aksesAdd1)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[add][$kodeMenu1]\" name=\"group[add][$kodeMenu1]\" value=\"$kodeMenu1\" $add1 /> Add</td>";
						if ($aksesEdit1)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[edit][$kodeMenu1]\" name=\"group[edit][$kodeMenu1]\" value=\"$kodeMenu1\" $edit1 /> Edit</td>";
						if ($aksesDelete1)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[delete][$kodeMenu1]\" name=\"group[delete][$kodeMenu1]\" value=\"$kodeMenu1\" $delete1 /> Delete</td>";
						#MAZTE-ADD
						if ($aksesAppr1)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[apprlv1][$kodeMenu1]\" name=\"group[apprlv1][$kodeMenu1]\" value=\"$kodeMenu1\" $appr1 /> Approval Lv1</td>";
						if ($aksesAppr2)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[apprlv2][$kodeMenu1]\" name=\"group[apprlv2][$kodeMenu1]\" value=\"$kodeMenu1\" $appr2 /> Approval Lv2</td>";
						if ($aksesAppr3)
						  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[apprlv3][$kodeMenu1]\" name=\"group[apprlv3][$kodeMenu1]\" value=\"$kodeMenu1\" $appr3 /> Approval Lv3</td>";
						#END MAZTE-ADD
					  }
					  $text.="</tr>";
					  if (is_array($arrMenu[$kodeMenu1])) {
						asort($arrMenu[$kodeMenu1]);
						reset($arrMenu[$kodeMenu1]);
						while (list($kMenu2, $vMenu2) = each($arrMenu[$kodeMenu1])) {
						  list($urutanMenu2, $kodeMenu2, $namaMenu2, $iconMenu2, $kodeTarget2, $aksesMenu2) = explode("\t", $vMenu2);

						  $aksesView2 = substr($aksesMenu2, 0, 1);
						  $aksesAdd2 = substr($aksesMenu2, 1, 1);
						  $aksesEdit2 = substr($aksesMenu2, 2, 1);
						  $aksesDelete2 = substr($aksesMenu2, 3, 1);

						  $view2 = $arrGroup[$kodeMenu2]["view"] ? "checked=\"checked\"" : "";
						  $add2 = $arrGroup[$kodeMenu2]["add"] ? "checked=\"checked\"" : "";
						  $edit2 = $arrGroup[$kodeMenu2]["edit"] ? "checked=\"checked\"" : "";
						  $delete2 = $arrGroup[$kodeMenu2]["delete"] ? "checked=\"checked\"" : "";

						  if ($cntMenu[$kodeMenu2] > 0 || $cUsername == $sUser){
						  $text.= "<tr>
																		<td style=\"padding-left:55px; padding-top:5px; padding-bottom:5px;border-bottom:1px solid #ecebeb;\">&middot; $namaMenu2</td>";
						  if (!empty($kodeTarget2)) {
							if ($aksesView2)
							  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[view][$kodeMenu2]\" name=\"group[view][$kodeMenu2]\" value=\"$kodeMenu2\" $view2 /> View</td>";
							if ($aksesAdd2)
							  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[add][$kodeMenu2]\" name=\"group[add][$kodeMenu2]\" value=\"$kodeMenu2\" $add2 /> Add</td>";
							if ($aksesEdit2)
							  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"75\"><input type=\"checkbox\" id=\"group[edit][$kodeMenu2]\" name=\"group[edit][$kodeMenu2]\" value=\"$kodeMenu2\" $edit2 /> Edit</td>";
							if ($aksesDelete2)
							  $text.="<td style=\"padding:5px;border-bottom:1px solid #ecebeb;\" width=\"100\"><input type=\"checkbox\" id=\"group[delete][$kodeMenu2]\" name=\"group[delete][$kodeMenu2]\" value=\"$kodeMenu2\" $delete2 /> Delete</td>";
						  }
						  $text.="</tr>";
						  }
						}
					  }
					}
				}
			  }
			}
		}
      }

      $text.="</tbody>
							</table>
						   </div>
						</div>";
	  $no++;
    }
  }
  $text.="<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Sinkronisasi\" onclick=\"openBox('popup.php?par[mode]=copy&par[kodeGroup]=$r[kodeGroup]" . getPar($par, "mode,kodeGroup") . "',800,300);\" style=\"float:right;\"/>
				</p>
			</form>	
			</div>";
  return $text;
}

function lihat() {
  global $s,$db, $inp, $par, $arrTitle, $menuAccess, $cUsername, $sUser, $sGroup, $kodeModul;
  $text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
				" . getBread() . "
				
			</div>    
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form action=\"\" method=\"post\" class=\"stdform\">
			<div id=\"pos_l\" style=\"float:left;\">
			<p>
				<span>Search : </span>
				<input type=\"text\" id=\"par[filter]\" name=\"par[filter]\" size=\"50\" value=\"$par[filter]\" class=\"mediuminput\" />
				<input type=\"submit\" value=\"GO\" class=\"btn btn_search btn-small\"/> 
			</p>
			</div>			
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Nama</th>
					<th>Deskripsi</th>
					<th width=\"50\">Status</th>";
  if (isset($menuAccess[$s]["edit"])) $text.="<th width=\"50\">Kontrol</th>";
  $text.="</tr>
			</thead>
			<tbody>";
  
  $filter="where t2.kodeModul=".$kodeModul."";
  $filte.=" and (
			lower(t1.namaGroup) like '%" . strtolower($par[filter]) . "%'
			or lower(t1.keteranganGroup) like '%" . strtolower($par[filter]) . "%'
		)";
  if ($cUsername != $sUser)
    $filter.= " and lower(t1.namaGroup) != '$sGroup' ";

  $sql = "select * from app_group t1 join app_group_modul t2 on (t1.kodeGroup=t2.kodeGroup) $filter order by t1.namaGroup";
  $res = db($sql);
  while ($r = mysql_fetch_array($res)) {
    $no++;
    $statusGroup = $r[statusGroup] == "t" ?
            "<img src=\"styles/images/t.png\" title='Active'>" :
            "<img src=\"styles/images/f.png\" title='Not Active'>";
    $text.="<tr>
					<td>$no.</td>
					<td>$r[namaGroup]</td>
					<td>$r[keteranganGroup]</td>				
					<td align=\"center\">$statusGroup</td>";
    if (isset($menuAccess[$s]["edit"])) $text.="<td align=\"center\">
				<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeGroup]=$r[kodeGroup]" . getPar($par, "mode,kodeGroup") . "',1000,500);\"><span>Edit</span></a>
			</td>";    
    $text.="</tr>";
  }

  $text.="</tbody>
			</table>
			</div>";
  return $text;
}
function sinkronisasi(){
	global $s,$db,$inp,$par,$kodeModul,$arrTitle,$menuAccess;	
	setValidation("is_null","inp[kodeGroup]","anda harus mengisi sinkron to");		
	$text = getValidation();
	
	$text.="<script>
				function setGroup(){
					if(validation(document.form)){
						kodeGroup = document.getElementById('inp[kodeGroup]').value;
						window.parent.location = '?par[mode]=edit&kodeGroup=' + kodeGroup + '".getPar($par,"mode")."';
					}
					return false;
				}
			</script>
			<div class=\"centercontent contentpopup\">
			<div class=\"pageheader\">
				<h1 class=\"pagetitle\">Sinkronisasi User Group</h1>
				".getBread(ucwords("sinkronisasi user group"))."
			</div>
			<div id=\"contentwrapper\" class=\"contentwrapper\">
			<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1".getPar($par)."\" onsubmit=\"return setGroup();\" enctype=\"multipart/form-data\">	
			<div id=\"general\" class=\"subcontent\">
				<p>
					<label class=\"l-input-small\">".getField("select namaGroup from app_group where kodeGroup='$par[kodeGroup]'")."</label>
					<div class=\"field\">Sinkron to 
						".comboData("select * from app_group t1 join app_group_modul t2 on (t1.kodeGroup=t2.kodeGroup) where t2.kodeModul='".$kodeModul."' and t1.kodeGroup!='$par[kodeGroup]' order by t1.namaGroup","kodeGroup","namaGroup","inp[kodeGroup]"," ",$inp[kodeGroup],"", "360px")."
					</div>
				</p>	
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"GO\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
				</p>
			</div>
		</form>	
		</div>";
	return $text;
}
function getContent($par) {
  global $s,$db, $_submit, $menuAccess;
  switch ($par[mode]) {        
	case "copy":
		$text = sinkronisasi();
	break;
    case "edit":
      if (isset($menuAccess[$s]["edit"]))
        $text = empty($_submit) ? form() : ubah();
      else
        $text = lihat();
      break;    
    default:
      $text = lihat();
      break;
  }
  return $text;
}

?>