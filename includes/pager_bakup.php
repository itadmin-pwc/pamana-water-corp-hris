<?
class AjaxPager extends dbHandler  {//pager class extends connection
	
	var $_limit;           
	
	private $_intOffset;   
	
	private $_intMaxRec;   
	
	private $imgFirstDis; 
	
	private $imgFirstEn;
	
	private $imgPrevDis;
	
	private $imgPrevEn;

	private $imgNextDis;
	
	private $imgNextEn;
	
	private $imgLastDis;

	private $imgLastEn;
	
	private $imgIndicator;
	
	private $imgIndicatorDis;
	
	private $_imgPath;
	
	function __construct($limit,$imgPath){
		
		$this->_imgPath = (!empty($imgPath) || $imgPath != '' || $imgPath != 0) ? $imgPath : '';
		$this->_limit = $limit;
		$this->imgFirstDis = $this->_imgPath."page-first-disabled.gif";
		$this->imgFirstEn  = $this->_imgPath."page-first.gif";
		$this->imgPrevDis  = $this->_imgPath."page-prev-disabled.gif";
		$this->imgPrevEn   = $this->_imgPath."page-prev.gif";
		$this->imgNextDis  = $this->_imgPath."page-next-disabled.gif";
		$this->imgNextEn   = $this->_imgPath."page-next.gif";
		$this->imgLastDis  = $this->_imgPath."page-last-disabled.gif";
		$this->imgLastEn   = $this->_imgPath."page-last.gif";
		$this->imgIndicator = $this->_imgPath."refresh.gif";
		$this->imgIndicatorDis     = $this->_imgPath."refresh_disabled.png";
	}
		
	function _getMaxRec($qryMaxRec){

		$this->_intMaxRec =  mysql_num_rows($qryMaxRec);
		return $this->_intMaxRec;
	}	
	
	private function _computeExcess(){
		
		return  ceil($this->_intMaxRec%$this->_limit);
	}
	
	private function _computeLastRec(){

		if($this->_computeExcess() == 0){
			if($this->_intMaxRec == 0){
				return 0;
			}
			else{
				return $this->_intMaxRec-$this->_limit;	
			}
		}
		else{
			return $this->_intMaxRec-$this->_computeExcess();
		}
	}
	
	function _actFirst(){
		
		$this->_intOffset = 0;
		return $this->_intOffset;
	}
	
	function _actPrev($offSet){
		
		$this->_intOffset = $offSet-$this->_limit;
		
		if($this->_intOffset == 0){
			$this->_intOffset = 0;
		}
		return $this->_intOffset;
	}
	
	function _actNext($offSet){
		
		$this->_intOffset = $offSet+$this->_limit;
		
		if(($this->_intOffset > $this->_intMaxRec) || ($this->_intOffset == $this->_intMaxRec)){
			$this->_intOffset = $this->_computeLastRec();
		}
		return $this->_intOffset; 
	}
	
	function _actLast(){
		
		$this->_intOffset = $this->_computeLastRec();
		return $this->_intOffset; 
	}
	
	function _viewPagerButton($url,$element,$offSet="",$isSearch, $txtSrch,$cmbSrch,$extra){
		
		$isSearch = (empty($isSearch)) ? 0 : $isSearch;
		
		echo "<TABLE border=\"0\" cellpassding=\"0\" cellspacing=\"0\" height=\"30\" align=\"right\">\n";
			echo "\t<TR>\n";
				echo "\t\t<TD width=\"40\">\n";
						if($offSet == 0){
							echo "\t\t\t<IMG src=\"$this->imgFirstDis\" title=\"First\" style=\"position:relative;top:3px;\">\n";
							echo "\t\t\t<IMG src=\"$this->imgPrevDis\" title=\"Previous\" style=\"position:relative;top:3px;\">\n";
						}
						else{
							echo "\t\t\t<IMG src=\"$this->imgFirstEn\" title=\"First\" id=\"btnFisrt\" onclick=\"pager('$url','$element','First',$offSet,'$isSearch',$txtSrch,$cmbSrch,'$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
							echo "<IMG src=\"$this->imgPrevEn\"  title=\"Previous\" id=\"btnPreb\" onclick=\"pager('$url','$element','Prev',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
						}
				echo "\t\t</TD>\n";
				echo "\t\t<TD width=\"30\" align=\"center\" >\n";
					if($this->_intMaxRec > $this->_limit || $isSearch == "1"){
						echo "\t\t\t<img id=\"indicator2\" src=\"$this->imgIndicator\" title=\"REFRESH\" onclick=\"pager('$url','$element','refresh',0,0,'','','$extra','$this->_imgPath')\" >\n";
					}
					else{
						echo "\t\t\t<img id=\"indicator2\" src=\"$this->imgIndicatorDis\" title=\"DISABLED\">\n";
					}
				echo "\t\t</TD>\n";
				echo "\t\t<TD width=\"40\">\n";			

						if((int)$offSet == (int)$this->_computeLastRec()){
							
							echo "\t\t\t<IMG src=\"$this->imgNextDis\" title=\"Last\" style=\"position:relative;top:3px;\">\n";
							echo "\t\t\t<IMG src=\"$this->imgLastDis\" title=\"Next\" style=\"position:relative;top:3px;\">\n";
						}
						else{
							echo "\t\t\t<IMG src=\"$this->imgNextEn\"  title=\"Next\" id=\"btnNext\" onclick=\"pager('$url','$element','Next',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
							echo "\t\t\t<IMG src=\"$this->imgLastEn\" title=\"Last\" id=\"btnLast\" onclick=\"pager('$url','$element','Last',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
						}
				echo "\t\t</TD>\n";
				echo "\t\t<TD width=\"150\">\n";	
						echo $this->_recCounter();
				echo "\t\t</TD>\n";
			echo "\t<TR>\n";
		echo "</TABLE>\n";
	}	
		
	private function _recCounter(){
		
		$from = ($this->_intMaxRec == 0) ? $this->_intOffset : $this->_intOffset+1;
		
		$to = $this->_intOffset+$this->_limit;
		if($to > $this->_intMaxRec){
			$to = $this->_intMaxRec;
		}
		return  "<font size=\"1\" color=\"#707070\">Showing " . $from . " to " . $to . " of " . $this->_intMaxRec ."</font>";
	}
	
	function _watToDo($action,$intOffset,$isSearch){
		
		if(empty($intOffset)){
			$this->_intOffset = 0;
		}

		switch ($action){
			case 'First':
				if($isSearch == 1){
					$this->_intOffset = $this->_actFirst();	
				}
				else{
					$this->_intOffset = $this->_actFirst();	
				}
			break;
			case 'Prev':
				if($isSearch == 1){
					$this->_intOffset = $this->_actPrev($intOffset);	
				}
				else{
					$this->_intOffset = $this->_actPrev($intOffset);
				}
			break;
			case 'Next':
				if($isSearch == 1){
					$this->_intOffset = $this->_actNext($intOffset);	
				}
				else{
					$this->_intOffset = $this->_actNext($intOffset);	
				}
					
			break;
			case 'Last':
				if($isSearch == 1){
					$this->_intOffset = $this->_actLast();	
				}
				else{
					$this->_intOffset = $this->_actLast();
				}
			break;
			case 'Search':
				$this->_intOffset = $intOffset;
			break;
			default://delete'

				if($intOffset==$this->_intMaxRec){
					$this->_intOffset = ($this->_intMaxRec == 0) ? 0 : $this->_intOffset = $this->_computeLastRec();
				}
				
				else{
					$this->_intOffset = $intOffset;
				}
		}	
		return $this->_intOffset;
	}
}//end of pager class
?>