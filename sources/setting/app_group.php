<?php
if (!isset($menuAccess[$s]["view"])) echo "<script>logout();</script>";
$arrSite = arrayQuery("select kodeSite, namaSite from app_site where statusSite='t' order by urutanSite");
$sql = "select * from app_menu where statusMenu='t' order by kodeMenu";
$res = db($sql);
while ($r = mysql_fetch_array($res)) {  
  if (empty($r[kodeInduk]))
  $arrParent["$r[kodeSite]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];

  $arrMenu["$r[kodeInduk]"]["$r[kodeMenu]"] = $r[kodeSite] . strlen($r[urutanMenu]) . $r[urutanMenu] . "\t" . $r[kodeMenu] . "\t" . $r[namaMenu] . "\t" . $r[iconMenu] . "\t" . $r[targetMenu] . "\t" . $r[aksesMenu];  
}

function chk() {
  global $inp, $par;
  if (getField("select kodeGroup from app_user where kodeGroup='$par[kodeGroup]'"))
    return "sorry, data has been use";
}

function hapus() {
  global $s, $inp, $par, $cUsername;
  $sql = "delete from app_group where kodeGroup='$par[kodeGroup]'";
  db($sql);
  $sql = "delete from app_group_modul where kodeGroup='$par[kodeGroup]'";
  db($sql);
  $sql = "delete from app_group_menu where kodeGroup='$par[kodeGroup]'";
  db($sql);
  echo "<script>window.location='?" . getPar($par, "mode,kodeGroup") . "';</script>";
}

function ubah() {
  global $s, $inp, $par, $det, $cUsername;
  repField();
  $sql = "update app_group set namaGroup='$inp[namaGroup]', keteranganGroup='$inp[keteranganGroup]', statusGroup='$inp[statusGroup]', updateBy='$cUsername', updateTime='" . date('Y-m-d H:i:s') . "' where kodeGroup='$par[kodeGroup]'";
  db($sql);

  if (is_array($det)) {
	db("delete from app_group_modul where kodeGroup='$par[kodeGroup]'");
    while (list($kodeModul) = each($det)) {     
		$sql = "insert into app_group_modul (kodeGroup, kodeModul, createBy, createTime) values ('$par[kodeGroup]', '$kodeModul', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
		db($sql);      
    }
  }  

  echo "<script>closeBox();reloadPage();</script>";
}

function tambah() {
  global $s, $inp, $par, $det, $cUsername;
  $kodeGroup = getField("select kodeGroup from app_group order by kodeGroup desc limit 1") + 1;

  repField();
  $sql = "insert into app_group (kodeGroup, namaGroup, keteranganGroup, statusGroup, createBy, createTime) values ('$kodeGroup', '$inp[namaGroup]', '$inp[keteranganGroup]', '$inp[statusGroup]', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
  db($sql);

  if (is_array($det)) {
    while (list($kodeModul) = each($det)) {     
		$sql = "insert into app_group_modul (kodeGroup, kodeModul, createBy, createTime) values ('$kodeGroup', '$kodeModul', '$cUsername', '" . date('Y-m-d H:i:s') . "')";
		db($sql);      
    }
  }

  echo "<script>closeBox();reloadPage();</script>";
}

function form() {
  global $s, $inp, $par, $arrParent, $arrSite, $arrMenu, $arrTitle, $menuAccess, $cUsername, $sUser;

  $sql = "select * from app_group where kodeGroup='$par[kodeGroup]'";
  $res = db($sql);
  $r = mysql_fetch_array($res);
  
  $false = $r[statusGroup] == "f" ? "checked=\"checked\"" : "";
  $true = empty($false) ? "checked=\"checked\"" : "";

  setValidation("is_null", "inp[namaGroup]", "you must fill name");
  $text = getValidation();

  $text.="<div class=\"centercontent contentpopup\">
				<div class=\"pageheader\">
					<h1 class=\"pagetitle\">" . $arrTitle[$s] . "</h1>
					" . getBread(ucwords($par[mode] . " data")) . "					
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<form id=\"form\" name=\"form\" method=\"post\" class=\"stdform\" action=\"?_submit=1" . getPar($par) . "\" onsubmit=\"return validation(document.form);\" enctype=\"multipart/form-data\">	
				<div id=\"general\" class=\"subcontent\">					
					<p>
						<label class=\"l-input-small\">Nama</label>
						<div class=\"field\">
							<input type=\"text\" id=\"inp[namaGroup]\" name=\"inp[namaGroup]\"  value=\"$r[namaGroup]\" class=\"mediuminput\" style=\"width:350px;\" maxlength=\"150\"/>
						</div>
					</p>					
					<p>
						<label class=\"l-input-small\">Deskrisi</label>
						<div class=\"field\">
							<textarea id=\"inp[keteranganGroup]\" name=\"inp[keteranganGroup]\" rows=\"3\" cols=\"50\" class=\"longinput\" style=\"height:50px; width:350px;\">$r[keteranganGroup]</textarea>
						</div>
					</p>
					<p>
  <label class=\"l-input-small\">Akses Modul</label>
  <div class=\"field\" style=\"margin-left:175px;\">";

    $det = arrayQuery("select kodeModul, kodeGroup from app_group_modul where kodeGroup='$par[kodeGroup]'");
    $arrkatModul = arrayQuery("select kodeData,namaData from mst_data where kodeCategory ='BE'");
    // var_dump($arrkatModul);
    $sql_="select * from app_modul where statusModul='t'"; 
    $res_=db($sql_);
    while($r_=mysql_fetch_array($res_)){
      $arrNamaModul[$r_[kategoriModul]][$r_[kodeModul]] = $r_[namaModul];         
    }
    krsort($arrNamaModul);

    foreach ($arrNamaModul as $katmodul => $isiModul) {
        $text.= "<fieldset><legend>$arrkatModul[$katmodul]</legend>";
      foreach ($isiModul as $kodeModul => $namaModul) {
        $checked = isset($det["$kodeModul"]) ? "checked=\"checked\"" : "";
        $text.="<input type=\"checkbox\" id=\"det[".$kodeModul."]\" name=\"det[".$kodeModul."]\" value=\"".$kodeModul."\" $checked /> ".$namaModul."<br>";

      }
      $text.="</fieldset>";
    }

        // $checked = isset($det["$r_[kodeModul]"]) ? "checked=\"checked\"" : "";
        //   $text.="<input type=\"checkbox\" id=\"det[".$r_[kodeModul]."]\" name=\"det[".$r_[kodeModul]."]\" value=\"".$r_[kodeModul]."\" $checked /> ".$r_[namaModul]."<br>";
    $text.="</div>
  </p>
					<p>
						<label class=\"l-input-small\">Status</label>
						<div class=\"fradio\">
							<input type=\"radio\" id=\"true\" name=\"inp[statusGroup]\" value=\"t\" $true /> <span class=\"sradio\">Active</span>
							<input type=\"radio\" id=\"false\" name=\"inp[statusGroup]\" value=\"f\" $false onclick=\"sts();\"/> <span class=\"sradio\">Not Active</span>
							<input type=\"hidden\" id=\"count\" name=\"count\" value=\"" . getField("select count(*) from app_user where kodeGroup='$par[kodeGroup]'") . "\">
						</div>
					</p>					
				</div>
				<p>
					<input type=\"submit\" class=\"submit radius2\" name=\"btnSimpan\" value=\"Save\"/>
					<input type=\"button\" class=\"cancel radius2\" value=\"Cancel\" onclick=\"closeBox();\"/>
				</p>
			</form>	
			</div>";
  return $text;
}

function lihat() {
  global $s, $inp, $par, $arrTitle, $menuAccess, $cUsername, $sUser, $sGroup;
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
			<div id=\"pos_r\">";
  if (isset($menuAccess[$s]["add"]))
    $text.="<a href=\"#Add\" class=\"btn btn1 btn_document\" onclick=\"openBox('popup.php?par[mode]=add" . getPar($par, "mode,kodeGroup") . "',800,500);\"><span>Add Data</span></a>";
  $text.="</div>
			</form>
			<br clear=\"all\" />
			<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"stdtable stdtablequick\" id=\"dyntable\">
			<thead>
				<tr>
					<th width=\"20\">No.</th>
					<th>Nama</th>
					<th>Deskripsi</th>
					<th width=\"50\">Status</th>";
  if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"]))
    $text.="<th width=\"50\">Kontrol</th>";
  $text.="</tr>
			</thead>
			<tbody>";

  //if(!empty($par[filter]))			
  $filter.="where (
			lower(namaGroup) like '%" . strtolower($par[filter]) . "%'
			or lower(keteranganGroup) like '%" . strtolower($par[filter]) . "%'
		)";
  if ($cUsername != $sUser)
    $filter.= " and lower(namaGroup) != '$sGroup' ";

  $sql = "select * from app_group $filter order by namaGroup";
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
    if (isset($menuAccess[$s]["edit"]) || isset($menuAccess[$s]["delete"])) {
      $text.="<td align=\"center\">";
      if (isset($menuAccess[$s]["edit"]))
        $text.="<a href=\"#Edit\" title=\"Edit Data\" class=\"edit\"  onclick=\"openBox('popup.php?par[mode]=edit&par[kodeGroup]=$r[kodeGroup]" . getPar($par, "mode,kodeGroup") . "',800,500);\"><span>Edit</span></a>";
      if (isset($menuAccess[$s]["delete"]))
        $text.="<a href=\"#Delete\" onclick=\"del('$r[kodeGroup]','" . getPar($par, "mode,kodeGroup") . "')\" title=\"Delete Data\" class=\"delete\"><span>Delete</span></a>";
      $text.="</td>";
    }
    $text.="</tr>";
  }

  $text.="</tbody>
			</table>
			</div>";
  return $text;
}

function getContent($par) {
  global $s, $_submit, $menuAccess;
  switch ($par[mode]) {
    case "chk":
      $text = chk();
      break;
    case "del":
      if (isset($menuAccess[$s]["delete"]))
        $text = hapus();
      else
        $text = lihat();
      break;
    case "edit":
      if (isset($menuAccess[$s]["edit"]))
        $text = empty($_submit) ? form() : ubah();
      else
        $text = lihat();
      break;
    case "add":
      if (isset($menuAccess[$s]["add"]))
        $text = empty($_submit) ? form() : tambah();
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