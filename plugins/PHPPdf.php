<?php
require('PHPPdf/fpdf.php');

class JSPDF extends FPDF {
	var $javascript;
	var $n_js;

	function IncludeJS($script) {
		$this->javascript=$script;
	}

	function _putjavascript() {
		$this->_newobj();
		$this->n_js=$this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
		$this->_out('>>');
		$this->_out('endobj');
		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS '.$this->_textstring($this->javascript));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putresources() {
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}

	function _putcatalog() {
		parent::_putcatalog();
		if (!empty($this->javascript)) {
			$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
		}
	}
}

class PDF extends JSPDF
	{
	var $widths;
	var $aligns;

	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	function SetAligns($a)
	{
		//Set the array of column alignments
		$this->aligns=$a;
	}

	function Row($data, $border=true)
	{		
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++){
			list($value, $flag) = explode("\t", $data[$i]);
			$nb=max($nb,$this->NbLines($this->widths[$i],$value));
		}
		$h=4*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			list($value, $flag) = explode("\t", $data[$i]);

			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
								
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			
			//Draw the border
			
			if ($border == true && $flag != "n"){
				if($f == "n"){			
					$this->Rect($tx,$y,($tw + $w),$h);
				}else if($flag == "f"){								
					if($i == 0)
						$this->Cell($w,$h,'','LT');
					else if($i == count($data)-1)
						$this->Cell($w,$h,'','TR');
					else
						$this->Cell($w,$h,'','T');
					
					$this->Ln();
					$this->SetXY($x, $y);					
				}else if($flag == "l"){													
					$this->Cell($w,$h,'','LT');					
					$this->Ln();
					$this->SetXY($x, $y);					
				}else if($flag == "r"){													
					$this->Cell($w,$h,'','RT');					
					$this->Ln();
					$this->SetXY($x, $y);					
				}else{
					$this->Rect($x,$y,$w,$h);
				}
			}
			
			//Print the text
			list($value, $flag) = explode("\t", $data[$i]);			
			if($flag == "b") $this->SetFont('','B');
			$this->MultiCell($w,4,$value,0,$a);
			$this->SetFont('');
			
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
						
			$f = $flag;
			$tx = $x;
			$tw = $w;
		}
		//Go to the next line
		$this->Ln($h);
	}

	function Rows($data, $border=true)
	{	
		for($r=0;$r<count($data);$r++){
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data[$r]);$i++){
				list($value, $flag) = explode("\t", $data[$r][$i]);
				$nb=max($nb,$this->NbLines($this->widths[$i],$value));
			}
			$h=6*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data[$r]);$i++)
			{
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
				
				if($r==0 && $i==0){
					$xb=$this->GetX();
					$yb=$this->GetY();	
				}
				
				//Save the current position		
				$x=$this->GetX();
				$y=$this->GetY();					
				
				//Print the text
				list($value, $flag) = explode("\t", $data[$r][$i]);			
				if($flag == "b") $this->SetFont('','B');
				$this->MultiCell($w,6,$value,0,$a);
				$this->SetFont('');
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);
				if($r==0) $wb+=$w;
			}
			$hb+=$h;
			$this->Ln($h);
		}
		//Draw the border
		if ($border == true) $this->Rect($xb,$yb,$wb,$hb);	
	}

	function Cols($data, $span=0)
	{
		
		for($r=0;$r<count($data);$r++){
			$nb=0;
			for($i=0;$i<count($data[$r]);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$r][$i]));
			$h=6*$nb;
			$hb+=$h;
		}
		
		for($r=0;$r<count($data);$r++){
			//Calculate the height of the row
			$nb=0;
			for($i=0;$i<count($data[$r]);$i++)
				$nb=max($nb,$this->NbLines($this->widths[$i],$data[$r][$i]));
			$h=6*$nb;
			//Issue a page break first if needed
			$this->CheckPageBreak($h);
			//Draw the cells of the row
			for($i=0;$i<count($data[$r]);$i++)
			{
				$w=$this->widths[$i];
				$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
							
				//Save the current position		
				$x=$this->GetX();
				$y=$this->GetY();					
				
				if($i!=$span){
					$xb=$this->GetX();
					$yb=$this->GetY();
					$wb=$w;
				}else{
					$wb+=$w;
				}
				
				//Print the text
				$this->MultiCell($w,6,$data[$r][$i],0,$a);
				$this->SetFont('');
				//Put the position to the right of the cell
				$this->SetXY($x+$w,$y);	
							
				if($r==0 && $i!=($span-1)) $this->Rect($xb,$yb,$wb,$hb);
			}
			$this->Ln($h);
		}
		//Draw the border	
		
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
	
	function AutoPrint($dialog=false)
	{
		//Open the print dialog or start printing immediately on the standard printer
		$param=($dialog ? 'true' : 'false');
		$script="print($param);";
		$this->IncludeJS($script);
	}

	function AutoPrintToPrinter($server, $printer, $dialog=false)
	{
		//Print on a shared printer (requires at least Acrobat 6)
		$script = "var pp = getPrintParams();";
		if($dialog)
			$script .= "pp.interactive = pp.constants.interactionLevel.full;";
		else
			$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
		$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
		$script .= "print(pp);";
		$this->IncludeJS($script);
	}
}
?>
