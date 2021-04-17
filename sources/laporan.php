<?php		
	function lihat(){
		global $c,$p,$s,$inp,$par,$arrTitle,$menuAccess,$arrMenu,$arrMenu_site,$cntMenu;					
		if(empty($arrTitle[$s])) $arrTitle[$s] = "Laporan";
		$text.="<div class=\"pageheader\">
				<h1 class=\"pagetitle\">".$arrTitle[$s]."</h1>
				<div style=\"margin-top: 10px;\">
				".getBread()."					
				</div>
				</div>
				<div id=\"contentwrapper\" class=\"contentwrapper\">
				<br clear=\"all\" />
				<ol style=\"margin-left:20px;\">";
		$no=1;
		
		$arrMenu[$s] = empty($_GET[s]) ? $arrMenu_site[$p][0] : $arrMenu[$s];
		if (is_array($arrMenu[$s])) {
			  asort($arrMenu[$s]);
			  reset($arrMenu[$s]);
			  while (list($keyMenu, $valMenu) = each($arrMenu[$s])) {
				list($urutanMenu, $kodeMenu, $namaMenu, $iconMenu, $targetMenu) = explode("\t", $valMenu);				
				if ($cntMenu[$kodeMenu] > 0){
					$text.="<li style=\"list-style: number; font-size:14px; font-weight:bold; margin-bottom:10px;\"><a href=\"index.php?c=" . $c . "&p=" . $p . "&m=" . $kodeMenu . "&s=" . $kodeMenu."\">$namaMenu</a></li>";
					$no++;
				}
			  }
		}
		$text.="</ol>
			</div>";
		return $text;
	}	
	
	function getContent($par){
		global $s,$_submit,$menuAccess,$cUsername;
		switch($par[mode]){			
			default:
				$text = lihat();
			break;
		}
		return $text;
	}	
?>