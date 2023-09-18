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
	
	private $get;
	
	function __construct($limit,$imgPath){
		$this->get = $_GET;
		$this->_imgPath = (!empty($imgPath) || $imgPath != '' || $imgPath != 0) ? $imgPath : '';
		$this->_limit = $limit;
		$this->imgFirstDis           = $this->_imgPath."page-first-disabled.png";
		$this->imgFirstEn            = $this->_imgPath."page-first.png";
		$this->imgPrevDis            = $this->_imgPath."page-prev-disabled.png";
		$this->imgPrevEn             = $this->_imgPath."page-prev.png";
		$this->imgNextDis            = $this->_imgPath."page-next-disabled.png";
		$this->imgNextEn             = $this->_imgPath."page-next.png";
		$this->imgLastDis            = $this->_imgPath."page-last-disabled.png";
		$this->imgLastEn             = $this->_imgPath."page-last.png";
		$this->imgIndicator          = $this->_imgPath."refresh.png";
		$this->imgIndicatorDis       = $this->_imgPath."refresh_disabled.png";
	}
		
	function _getMaxRec($qryMaxRec){

		$this->_intMaxRec =  $this->getRecCount($qryMaxRec);
		return $this->_intMaxRec;
	}	
	
	private private function _computeExcess(){
		
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
	
	private function _actFirst(){
		
		$this->_intOffset = 0;
		return $this->_intOffset;
	}
	
	private function _actPrev($offSet){
		
		$this->_intOffset = $offSet-$this->_limit;
		
		if($this->_intOffset == 0){
			$this->_intOffset = 0;
		}
		return $this->_intOffset;
	}
	
	private function _actNext($offSet){
		
		$this->_intOffset = $offSet+$this->_limit;
		
		if(($this->_intOffset > $this->_intMaxRec) || ($this->_intOffset == $this->_intMaxRec)){
			$this->_intOffset = $this->_computeLastRec();
		}
		return $this->_intOffset; 
	}
	
	private function _actLast(){
		
		$this->_intOffset = $this->_computeLastRec();
		return $this->_intOffset; 
	}
	
	private function _actSearchPage(){
		
		$pageTot = ceil( $this->_intMaxRec / $this->_limit);
		if((int)$this->get['page'] == 1){
			$this->_intOffset = 0;
			return $this->_intOffset;
		}
		if((int)$this->get['page'] <= 0){
			$this->_intOffset = 0;
			return $this->_intOffset;			
		}
		if($this->get['page'] > 1){
			if((int)$this->get['page'] > $pageTot){
				$this->_intOffset = $this->_computeLastRec();
			}
			else{
				$this->_intOffset = (((int)$this->get['page']-1)*$this->_limit);
			}
			return $this->_intOffset;
		}
	}
	
	private function getPageNum(){
		$pageTot = ceil( $this->_intMaxRec / $this->_limit);
		if($this->_intMaxRec == 0){
			return 0;
		}
		else{
			if(!empty($_GET['page'])){
				if((int)$this->get['page'] > $pageTot){
					return $pageTot;
				}
				elseif ((int)$this->get['page'] <= 0){
					return 1;
				}
				else{
					return $_GET['page'];
				}
			}
			else{
				return ($this->_intOffset/$this->_limit)+1;
			}
		}
	}
	
	private function getTotPage(){
		 return ceil( $this->_intMaxRec / $this->_limit);
	}
	
	function _viewPagerButton($url,$element,$offSet="",$isSearch, $txtSrch,$cmbSrch,$extra){
		
		$isSearch = (empty($isSearch)) ? 0 : $isSearch;
		
		echo "<TABLE border=\"0\" cellpassding=\"0\" cellspacing=\"0\" height=\"30\" align=\"left\">\n";
			echo "\t<TR>\n";
				echo "\t\t<TD align=\"right\">\n";
						if($offSet == 0){
							echo "\t\t\t<IMG src=\"$this->imgFirstDis\" title=\"First\" style=\"position:relative;top:3px;\">\n";
							echo "\t\t\t<IMG src=\"$this->imgPrevDis\" title=\"Previous\" style=\"position:relative;top:3px;\">\n";
						}
						else{
							echo "\t\t\t<IMG src=\"$this->imgFirstEn\" title=\"First\" id=\"btnFisrt\" onclick=\"pager('$url','$element','First',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
							echo "<IMG src=\"$this->imgPrevEn\"  title=\"Previous\" id=\"btnPreb\" onclick=\"pager('$url','$element','Prev',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
						}
				echo "<font style=\"font:normal normal 15px Verdana, Arial, Helvetica, sans-serif;color : silver;position : relative;top: -1px;\">|</font>";
				echo "\t\t</TD>\n";
				echo "\t\t<TD  align=\"left\" >\n";
						if($this->_intMaxRec > $this->_limit && $isSearch != "1"){
							$disabled = "";
						}
						else{
							$disabled = "disabled";
						}
						echo "\t\t\t<font size=\"1\" color=\"#000000\">Page <input type=\"text\" name=\"srchPage\" id=\"srchPage\" size=\"1\" style=\"font-size:9px;text-align:center;\" onkeyup=\"pager('$url','$element','getPage',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra&page='+this.value,'$this->_imgPath',event)\" value=\"{$this->getPageNum()}\" $disabled> of {$this-> getTotPage()}</font>";
				echo "<font style=\"font:normal normal 15px Verdana, Arial, Helvetica, sans-serif;color : silver;position : relative;top: -1px;\">|</font>";
				echo "\t\t</TD>\n";
				echo "\t\t<TD >\n";			

						if((int)$offSet == (int)$this->_computeLastRec()){
							
							echo "\t\t\t<IMG src=\"$this->imgNextDis\" title=\"Last\" style=\"position:relative;top:3px;\">\n";
							echo "\t\t\t<IMG src=\"$this->imgLastDis\" title=\"Next\" style=\"position:relative;top:3px;\">\n";
						}
						else{
							echo "\t\t\t<IMG src=\"$this->imgNextEn\"  title=\"Next\" id=\"btnNext\" onclick=\"pager('$url','$element','Next',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
							echo "\t\t\t<IMG src=\"$this->imgLastEn\" title=\"Last\" id=\"btnLast\" onclick=\"pager('$url','$element','Last',$offSet,'$isSearch','$txtSrch','$cmbSrch','$extra','$this->_imgPath')\" style=\"position:relative;top:3px;\">\n";
						}
				echo "<font style=\"font:normal normal 15px Verdana, Arial, Helvetica, sans-serif;color : silver;position : relative;top: -1px;\">|</font>";
				echo "\t\t</TD>\n";
				echo "\t\t<TD align=\"left\" width=\"50\">\n";
					if($this->_intMaxRec > $this->_limit || $isSearch == "1"){
						echo "\t\t\t<img style=\"position:relative;top:2px;left:2px\" id=\"indicator2\" src=\"$this->imgIndicator\" title=\"REFRESH\" onclick=\"pager('$url','$element','refresh',0,0,'','','$extra','$this->_imgPath')\" >\n";
					}
					else{
						echo "\t\t\t<img id=\"indicator2\" style=\"position:relative;top:2px;left:2px\" src=\"$this->imgIndicatorDis\" title=\"DISABLED\">\n";
					}
				echo "<font style=\"font:normal normal 15px Verdana, Arial, Helvetica, sans-serif;color : silver;position : relative;top: -1px;\">|</font>";
				echo "\t\t</TD>\n";
				echo "\t\t<TD >\n";	
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
		return  "<font size=\"1\" color=\"#000000\">( Displaying Record " . $from . " - " . $to . " of " . $this->_intMaxRec ." )</font>";
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
			case 'getPage':
				$this->_intOffset = $this->_actSearchPage();
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