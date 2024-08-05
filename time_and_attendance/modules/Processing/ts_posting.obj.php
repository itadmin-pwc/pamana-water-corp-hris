<?
class TSPostingObj extends dateDiff {
	var $pdFrom;
	var $pdTo;
	var $Group;
	var $arrHolidays			= array();
	var $arrUTExempEmpList		= array();
	var $arrAbsentExempEmpList	= array();
	var $arrTardyExempEmpList	= array();
	var $arrFlexExempEmpList	= array();
	var $getOTExemptEmp			= array();
	var $arrLeaveAppTypes		= array();
	var $arrHDMatrix			= array();
	var $arrempListWithPay		= array();	
	function PostTS() {
		$Trns = $this->beginTranI();//begin transaction
		$this->getholiday();
		$this->getUTExemptEmp();
		$this->getAbsentExemptEmp();
		$this->getTardyExemptEmp();
		$this->getFlexExemptEmp();
		$this->getOTExemptEmp();
		$this->getLeaveAppTypes();
		$this->HalfDayMatrix();
		$this->getOTList();
		$sqlUpdateTS = "";
		$sqlDeductions = "";
		$sqlOT = "";
		$sqlCWW = "";
		$sqlUpdateSat = "";
		//Clear Deductions And OT
		if ($Trns) {
			$this->Repost();
		}
		if ($Trns) {
			$this->clearCWW();
		}
		
		if ($Trns) {
			$Trns = $this->ClearViolations();
		}
		$arrBrnCode = $arrEmpOTs = $arrEmpDeds = $arrEmpCWW = array();
		$getCompInfo = $this->getCompany($_SESSION['company_code']);
		
		foreach($this->getEmpList() as $valTSList)
		{
			//balik dito nga
			// if($valTSList['empNo'] == '010000065' && $valTSList['tsDate'] == '2023-11-11' || $valTSList['empNo'] == '010000065' && $valTSList['tsDate'] == '2023-10-18') {
			// 	echo 'Sat Pay' . $valTSList['satPayTag'] . " - " . $valTSList['tsDate'] . ' - ' . $valTSList['dayType'] . '<br>' . '<br>'; 
			// 	var_dump($valTSList) . '<br>' . '<br>';
			// }
			//var_dump($valTSList) . '<br>' . '<br>';

			$dedTag=false;
			$hrsOt = $hrsND = $hrsregND = $halfDayLeavePay = 0 ;
			
			$SchedHrsWork = $this->getSchedHrsWork($valTSList);

			if (!in_array($valTSList['empBrnCode'],$arrBrnCode)) 
			{
				$arrBrnCode[] = $valTSList['empBrnCode'];
			}
			if ($valTSList['crossDay'] =='Y' && $valTSList['dayType']!='02') {
//				$timeIn = "{$valTSList['tsDate']} {$valTSList['timeIn']}";
				if ($valTSList['timeIn'] != "") {
					if ($this->IsLeaveAppType($valTSList['tsAppTypeCd'])) { //apptype is an leavetype
						//Compute hours worked for half day Leave
						if(in_array($valTSList['tsAppTypeCd'],array(12))){//halfday leave AM
							$halfDayLeavePay = 5;
							$timeIn = ((float)str_replace(":",".",$valTSList['lunchIn'])>(float)str_replace(":",".",$valTSList['shftLunchIn'])) ? "{$valTSList['tsDate']} {$valTSList['lunchIn']}":"{$valTSList['tsDate']} {$valTSList['shftLunchIn']}";
							$tIn = ((float)str_replace(":",".",$valTSList['lunchIn'])>(float)str_replace(":",".",$valTSList['shftLunchIn'])) ? "{$valTSList['lunchIn']}":"{$valTSList['shftLunchIn']}";
						} elseif(in_array($valTSList['tsAppTypeCd'],array(14))){
							$timeIn = ((float)str_replace(":",".",$valTSList['lunchIn'])>(float)str_replace(":",".",$valTSList['shftLunchIn'])) ? "{$valTSList['tsDate']} {$valTSList['lunchIn']}":"{$valTSList['tsDate']} {$valTSList['shftLunchIn']}";	
							$tIn = ((float)str_replace(":",".",$valTSList['lunchIn'])>(float)str_replace(":",".",$valTSList['shftLunchIn'])) ? "{$valTSList['lunchIn']}":"{$valTSList['shftLunchIn']}";
						}
					} else {
						$timeIn = ((float)str_replace(":",".",$valTSList['timeIn'])>(float)str_replace(":",".",$valTSList['shftTimeIn'])) ? "{$valTSList['tsDate']} {$valTSList['timeIn']}":"{$valTSList['tsDate']} {$valTSList['shftTimeIn']}";						
						$tIn = ((float)str_replace(":",".",$valTSList['timeIn'])>(float)str_replace(":",".",$valTSList['shftTimeIn'])) ? "{$valTSList['timeIn']}":"{$valTSList['shftTimeIn']}";						
					}
//					$timeIn = ((float)str_replace(":",".",$valTSList['timeIn'])>(float)str_replace(":",".",$valTSList['shftTimeIn'])) ? "{$valTSList['tsDate']} {$valTSList['timeIn']}":"{$valTSList['tsDate']} {$valTSList['shftTimeIn']}";						
					//try
					if ($valTSList['empBrnCode']!=='0001' && $SchedHrsWork<1 && $valTSList['CWWTag']=='') {
						$timeout = $this->DateAdd($valTSList['tsDate']) . " {$valTSList['lunchOut']}";
						$tOut = "{$valTSList['lunchOut']}";
					} else {
						if ((float)str_replace(":",".",$valTSList['timeOut'])>20) {
							$timeout = "{$valTSList['tsDate']} {$valTSList['timeOut']}";
							$tOut = "{$valTSList['timeOut']}";
						} else {
							$timeout = ((float)str_replace(":",".",$valTSList['shftTimeOut'])>(float)str_replace(":",".",$valTSList['timeOut'])) ? $this->DateAdd($valTSList['tsDate']) . " {$valTSList['timeOut']}":$this->DateAdd($valTSList['tsDate']) . " {$valTSList['shftTimeOut']}";
							$tOut = ((float)str_replace(":",".",$valTSList['shftTimeOut'])>(float)str_replace(":",".",$valTSList['timeOut'])) ? "{$valTSList['timeOut']}":"{$valTSList['shftTimeOut']}";
						}
					}
				} else {
					$timeIn ="{$valTSList['tsDate']} 00:00";
					$timeout = "{$valTSList['tsDate']} 00:00";
					$tIn ="00:00";
					$tOut = "00:00";
				}

				$hrsWork = round($this->calDiff($tIn,$tOut,'m')/60,2) + $halfDayLeavePay;
				//$hrsWrk = round($this->calDiff($timeIn,$timeout,'m')/60,2) + $halfDayLeavePay;

				if ($valTSList['lunchOut'] != $valTSList['lunchIn'] && $valTSList['lunchOut'] != '' && $valTSList['lunchIn'] != '' && !in_array($valTSList['tsAppTypeCd'],array(12,14))) {
					$shfthrsLunch = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['shftLunchOut']}","{$valTSList['tsDate']} {$valTSList['shftLunchIn']}",'m')/60,2);
					$shfthrsLunch = ($shfthrsLunch<1 && $shfthrsLunch!=0.5) ? 1: $shfthrsLunch;
					//original code [tardy lunch should start at 12:00 PM not lunch out] - 08/06/2023
					//$hrsLunch = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['lunchOut']}","{$valTSList['tsDate']} {$valTSList['lunchIn']}",'m')/60,2);
					$hrsLunch = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['shftLunchOut']}","{$valTSList['tsDate']} {$valTSList['lunchIn']}",'m')/60,2);
					if ($hrsLunch>$shfthrsLunch) {
						if ($shfthrsLunch == 0.5)
							$hrsWrk = $hrsWrk-($hrsLunch-$shfthrsLunch);
						else
							$hrsWrk = $hrsWrk-$hrsLunch;
						if ($hrsLunch>$shfthrsLunch){
							$hrsTardy += $hrsLunch-$shfthrsLunch;
						}
					}else{
						if ($hrsLunch>0 && $shfthrsLunch!=0.5){
							$hrsWrk = $hrsWrk-$shfthrsLunch;
						}
					}
				}
				
			$hrsWrk = ($hrsWrk <= $SchedHrsWork) ? $SchedHrsWork:$hrsWrk;//alejohrswork
			$hrsND 	= 0;
			//$hrsTard =round($this->calDiff("{$valTSList['tsDate']} {$valTSList['shftTimeIn']}","{$valTSList['tsDate']} {$valTSList['timeIn']}",'m')/60,2);
			$hrsTardy = ((float)str_replace(":",".",$valTSList['timeIn'])>(float)str_replace(":",".",$valTSList['shftTimeIn'])) ? round($this->calDiff("{$valTSList['tsDate']} {$valTSList['shftTimeIn']}","{$valTSList['tsDate']} {$valTSList['timeIn']}",'m')/60,2):0;
			//$hrsTardy = ($hrsTardy > 0) ? $hrsTardy:0;
			//$hrsTardy = $hrsTardy;

			if($valTSList['CWWTag']=='Y'  &&  $hrsWrk<=9 && $valTSList['otCrossTag']!='Y'){
				//old original
				if ((float)str_replace(":",".",$valTSList['timeOut'])>(float)str_replace(":",".",$tIn)  && (float)str_replace(":",".",$valTSList['timeOut'])<24) {
					$hrsUT = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['timeOut']}",$this->DateAdd($valTSList['tsDate']) ." {$valTSList['shftTimeOut']}",'m')/60,2);

				} else {
				//	$hrsUT = ((float)str_replace(":",".",$valTSList['timeOut'])<(float)str_replace(":",".",$valTSList['shftTimeOut'])) ? round($this->calDiff("{$valTSList['tsDate']} {$valTSList['timeOut']}","{$valTSList['tsDate']} {$valTSList['shftTimeOut']}",'m')/60,2):0;
				}
				$hrsWrk =($hrsWrk)-$hrsUT -$hrsTardy;
				//old original
			}
			if($valTSList['CWWTag'] !='Y' &&  $hrsWrk<=8 && $valTSList['otCrossTag']!='Y'){
				if ((float)str_replace(":",".",$valTSList['timeOut'])>(float)str_replace(":",".",$tIn)  && (float)str_replace(":",".",$valTSList['timeOut'])<24) {
					$hrsUT = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['timeOut']}",$this->DateAdd($valTSList['tsDate']) ." {$valTSList['shftTimeOut']}",'m')/60,2);

				} else {
					$hrsUT = ((float)str_replace(":",".",$valTSList['timeOut'])<(float)str_replace(":",".",$valTSList['shftTimeOut'])) ? round($this->calDiff("{$valTSList['tsDate']} {$valTSList['timeOut']}","{$valTSList['tsDate']} {$valTSList['shftTimeOut']}",'m')/60,2):0;
				}
							$hrsWrk =($hrsWrk)-$hrsUT -$hrsTardy;
			}

			if($valTSList['hrs8Deduct']=='Y') {
				$hrsWrk = $SchedHrsWork;
				$hrsTardy = 0;
				$hrsUT = 0;
			} 


				/*if (($hrsUT+$hrsWrk) > $SchedHrsWork && $hrsUT>0) {
					if ($hrsWrk<$SchedHrsWork)
						$hrsUT = $SchedHrsWork - $hrsWrk;
				}*/
  				$tothours = round($hrsTardy+$hrsWrk+$hrsUT,2);					
				/*if ($tothours < $SchedHrsWork && $hrsTardy>0) {
					$hrsTardy = 0;
				}*/
				
				if ($valTSList['otIn'] != '' && $valTSList['otOut']!='') {
					if ($valTSList['empBrnCode']!=='0001' && $SchedHrsWork<1  && $valTSList['CWWTag']=='')
						$OTOut = ((float)str_replace(":",".",$valTSList['otOut'])<(float)str_replace(":",".",$valTSList['lunchOut'])) ? $valTSList['otOut']:$valTSList['lunchOut'];
					else
						$OTOut = ((float)str_replace(":",".",$valTSList['otOut'])<(float)str_replace(":",".",$valTSList['timeOut'])) ? $valTSList['otOut']:$valTSList['timeOut'];
						
					if ($valTSList['otCrossTag']=='Y') {
						$var = $this->calDiff("{$valTSList['tsDate']} {$valTSList['otIn']}",$this->DateAdd($valTSList['tsDate'])." $OTOut",'m')/60;
						$OtHrs = number_format(floor($var * 100) / 100, 2, '.', '');
					} else {
						$var = $this->calDiff("{$valTSList['tsDate']} {$valTSList['otIn']}","{$valTSList['tsDate']} $OTOut",'m')/60;
						$OtHrs = number_format(floor($var * 100) / 100, 2, '.', '');
					}
//					$hrsOt = (($hrsWrk-$SchedHrsWork)<$OtHrs) ? $hrsWrk-$SchedHrsWork:$OtHrs;
					$hrsOt = $OtHrs;
			
				}
				if ($hrsWrk>4) {
					if ($valTSList['empBrnCode']!=='0001' && $SchedHrsWork<1  && $valTSList['CWWTag']=='')
						$tOut = $valTSList['lunchOut'];
					else
						$tOut = $valTSList['timeOut'];
					
						if ((float)str_replace(":",".",$tOut)<6) {
							if((float)str_replace(":",".",$valTSList['shftTimeOut']) !=0){
								$dtTo = ((float)str_replace(":",".",$tOut)<(float)str_replace(":",".",$valTSList['shftTimeOut'])) ? $this->DateAdd($valTSList['tsDate'])." {$tOut}":$this->DateAdd($valTSList['tsDate'])." {$valTSList['shftTimeOut']}";
								
							}else{
								$dtTo = $this->DateAdd($valTSList['tsDate'])." {$tOut}";
							}
							
						} else {
							if ((float)str_replace(":",".",$tOut)>(float)str_replace(":",".",$valTSList['timeIn'])  && (float)str_replace(":",".",$tOut)<24) {
								$dtTo = $valTSList['tsDate']." {$tOut}";
							} else {
								$dtTo = $this->DateAdd($valTSList['tsDate'])." 06:00";
							}
						}
						if ((float)str_replace(":",".",$valTSList['timeIn'])<24) {
							$dtFr = ((float)str_replace(":",".",$valTSList['timeIn'])<=22) ? "{$valTSList['tsDate']} 22:00":"{$valTSList['tsDate']} {$valTSList['timeIn']}"; 
						} else {
							$dtFr = $this->DateAdd($valTSList['tsDate'])." 00:00"; 
						}
					
						if ((float)str_replace(":",".",$valTSList['timeIn'])<=22) {
						
							$hrsND += round($this->calDiff("{$valTSList['tsDate']} 22:00",$dtTo,'m')/60,2);
							
						} else {
							
							$hrsND += round($this->calDiff($dtFr,$dtTo,'m')/60,2);
						}
						if ($valTSList['dayType'] == '01')
							$hrsregND = $hrsND;
					}
					$hrsND = ($hrsND > 0) ? $hrsND:0;

					if (!in_array($valTSList['dayType'],array('01'))) {
						$hrsWrk = $hrsTardy = $hrsUT = 0;
					}
					
				if ($this->IsLeaveAppType($valTSList['tsAppTypeCd'])) 
				{ //apptype is an leavetype
				
					//Compute hours worked for half day Leave
					if(in_array($valTSList['tsAppTypeCd'],array(12))) {//halfday leave PM
						$hrsWrk = $hrsWrk+4;
											
					} elseif(in_array($valTSList['tsAppTypeCd'],array('04','05','06','07','18','22'))) {//compute hours work for leave w/ pay
						$hrsWrk = $SchedHrsWork;
						$hrsOt = $hrsND = $hrsregND = 0;
					} elseif (in_array($valTSList['tsAppTypeCd'], array(16,17,08,11,19,20))) { //compute hours work for leave w/o pay
						$hrsWrk = $hrsOt = $hrsND = $hrsregND = $hrsUT = 0;
					} elseif (in_array($valTSList['tsAppTypeCd'],array(21))) {//compute hours work for leave combi
						$hrsWrk = 4;
					}
			}
		
			}//end of crossDay
			else {
				
				//echo $valTSList['empNo'] == '010000065' ? $valTSList['empNo'] . ' - ' . $valTSList['tsDate'] . '<br>' : '';
				$time = $this->computehrsWork($valTSList);
				//echo $valTSList['empNo'] == '010000065' ? var_dump($time) . '<br>' . '<br>' : '';
				
				$hrsWrk 	= ((float)$time['hrsWork']<0) ? 0: (float)$time['hrsWork'];
				// echo $valTSList['empNo'] == '010000065' ? $hrsWrk . '<br>' : '';
				// echo $valTSList['empNo'] == '010000065' ? 'timeIn : '.$valTSList['timeIn'] . '<br>' : '';
				// echo $valTSList['empNo'] == '010000065' ? 'timeOut :  '.$valTSList['timeOut'] . '<br>' : '';
				// echo $valTSList['empNo'] == '010000065' ? 'DayType : ' . $valTSList['dayType'] . '<br>' : '';
				//echo $valTSList['empNo'] == '010000065' ? 'OT : ' . $valTSList['hrsOT'] . '<br><br><br>' : '';

				if($valTSList['dayType'] == '03') {
					$hrsWrk = round($this->calDiff("{$valTSList['otIn']}","{$valTSList['otOut']}",'m')/60,2);
					if($hrsWrk > 8)
						$hrsWrk = $hrsWrk - 1;
				}
				//echo $valTSList['empNo'] == '010000065' ? $hrsWrk . '<br>' : '';

				$hrsTardy 	= ($valTSList['dayType'] == '01') ? $time['hrsTardy']:0;
				//echo $valTSList['empNo'] == '010000065' ? $hrsTardy . '<br>' . '<br>' : '';

				//round($this->calDiff("{$valTSList['tsDate']} {$valTSList['lunchOut']}","{$valTSList['tsDate']} {$valTSList['lunchIn']}",'m')/60,2)
						
				if ($valTSList['dayType'] == '01')
					$hrsregND 	= $time['hrsregND'];
					
				$hrsOt 		= $time['hrsOT'];
				$hrsND 		= $time['hrsND'];
				// if ($valTSList['empNo']=='010000065' && date('m/d/Y',strtotime($valTSList['tsDate']))=='03/31/2024') {
				// 	echo "OT = {$time['hrsOT']}\n" . '<br>';	
				// 	echo "ND: " . $hrsND;
				// }	
				
				$hrsUT = ($time['hrsUT'] >= $SchedHrsWork) ? 0: $time['hrsUT'];
				$hrsUT = ($hrsUT < 0) ? 0:$hrsUT;

				if (($hrsUT+$hrsWrk) > $SchedHrsWork && $hrsUT>0) {
					if ($hrsWrk<$SchedHrsWork)
						$hrsUT = $SchedHrsWork - $hrsWrk - $hrsTardy;
					else
						$hrsWrk = $SchedHrsWork - $hrsUT;

				}

			  	$tothours = round($hrsTardy+$hrsWrk+$hrsUT,2);
				if ($tothours < $SchedHrsWork && $hrsTardy>0 && !in_array($valTSList['tsAppTypeCd'],array(14,15))) {
					$hrsTardy = $hrsTardy;					

				} 		
				
				//edited by Nhomer
				//old codes
				//$hrsTardy = ($hrsTardy>$SchedHrsWork) ? 0: $hrsTardy;

				$hrsTardy = ((float)$hrsTardy>(float)$SchedHrsWork) ? $SchedHrsWork: $hrsTardy;

				if ($hrsWrk>0) {
					if ($SchedHrsWork<=8) {
						if(!in_array($valTSList['tsAppTypeCd'],array(12,13,14,15,21))) 
							$hrsWrk = $SchedHrsWork - $hrsTardy - $hrsUT;

					} else {

						if(!in_array($valTSList['tsAppTypeCd'],array(12,13,14,15,21))) 
							$hrsWrk = $SchedHrsWork - $hrsTardy - $hrsUT;
					}
				}
			}
			if ($this->checkExemp($valTSList['empNo'],'UT')) {	
				$hrsWrk += $hrsUT;
				$hrsUT = 0;
			}
			if ($this->checkExemp($valTSList['empNo'],'Tardy')) {	
				$hrsWrk += $hrsTardy;
				$hrsTardy = 0;
			}
			if ($this->checkExemp($valTSList['empNo'],'Absent')) {
				if($valTSList['dayType']=='01')
					$hrsWrk = $SchedHrsWork;
			}
			
			$otTag = "";
			//OT
				
			if ($hrsWrk == 0 && $valTSList['dayType'] == '01') {
				$hrsND = $hrsregND = 0;
			}

			// if ($valTSList['empNo']=='010000065' && date('m/d/Y',strtotime($valTSList['tsDate']))=='03/31/2024') {
			// 	echo "OT = {$time['hrsOT']}\n" . '<br>';	
			// 	echo "ND: " . $hrsND;
			// }	

			$hrsND 		= ($hrsND>8) ? 8:$hrsND;
			$hrsND 		= ($hrsND<$hrsregND)? $hrsND:$hrsND-$hrsregND;

			//orig code 08/06/2023
			//$hrsLunch = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['lunchOut']}","{$valTSList['tsDate']} {$valTSList['lunchIn']}",'m')/60,2);
			$hrsLunch = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['shftLunchOut']}","{$valTSList['tsDate']} {$valTSList['lunchIn']}",'m')/60,2);
			$shfthrsLunch = round($this->calDiff("{$valTSList['tsDate']} {$valTSList['shftLunchOut']}","{$valTSList['tsDate']} {$valTSList['shftLunchIn']}",'m')/60,2);
			$shfthrsLunch = ($shfthrsLunch<1 && $shfthrsLunch!=0.5) ? 1: $shfthrsLunch;
									
			$hrsregND 	= ($hrsregND >= 5 && $shfthrsLunch!=0.5) ? $hrsregND:$hrsregND;

			if ($hrsND > 0 || $hrsregND > 0) {
				if ($this->checkExemp($valTSList['empNo'],'OT')) {
					$hrsND = $hrsregND = 0;
				}
			}

			
			// if($valTSList['empNo'] == '010000065') {
			// 	echo var_dump($valTSList);
			// 	echo '<br><br>';
			// }
			//update remove ot minus tardy hrs code 31324
			//dito check ang OT
			if ($hrsOt >= 0.50) {
				if (in_array($valTSList['dayType'],array('03','04','07')) && $SchedHrsWork==3.5) {
					$hrsOt = $hrsOt + 5.5;
				}
				if (!$this->checkExemp($valTSList['empNo'],'OT')) {
						
					if ($hrsTardy>0 && $valTSList['crossDay']!='Y') {
						if ($hrsTardy<=$hrsOt && $hrsOt >0 ) {
							//$hrsOt = $hrsOt; - $hrsTardy; // 31324 orig ot less tardy
							$hrsWrk = $hrsWrk + $hrsTardy;
							//$hrsTardy = 0; //31324 orig no tardy if paid via ot
						} else {
							$hrsWrk = $hrsWrk + $hrsOt;
							//$hrsTardy = $hrsTardy - $hrsOt; //31324 orig tardy paid via ot
							$hrsOt = 0;
						}
					}
					if ($hrsTardy>0 || $hrsUT > 0 && $valTSList['crossDay']=='Y') {
						$hrsWrk = $hrsWrk-$hrsUT-$hrsTardy;
						// if($hrsOt>=($hrsUT+$hrsTardy)){ // 31324 orig ot less tardy
						// 	//$hrsOt=$hrsOt-$hrsUT-$hrsTardy;
						// 	//$hrsTardy =0;
						// 	//$hrsUT=0;
						// 	$hrsWrk = $hrsWrk-$hrsUT-$hrsTardy;
						// }else{
						// 	$hrsWrk = $hrsWrk-$hrsUT-$hrsTardy;
						// }
					}
					if ($hrsOt>0) {
						$sat=date("N",strtotime($valTSList['tsDate']));
						if ($hrsOt>8 && $valTSList['dayType']!='01') {
							$hrsOTLe8 = 8;
							$hrsOTGt8 = $hrsOt-8;

							//01-04-2023
							// if($sat==6 && $valTSList['CWWTag']=='Y') {
							// 	$hrsOTLe8 = $hrsOt;
							// 	$hrsOTGt8 = 0;
							// }else{
							// 	$hrsOTLe8 = 8;
							// 	$hrsOTGt8 = $hrsOt-8;
							// }
							
						} else {
							if ($hrsOt<=8) {
								//alejo ot halfday duty deduct 1 hr for break
								if ($valTSList['CWWTag']=='' && $sat==6  && $hrsOt>=5 ){
									$hrsOTLe8 = $hrsOt;
								}else{
									
						// if($valTSList['empNo'] == '010000065' && $valTSList['tsDate'] == '2024-02-04') {
						// 	echo var_dump($valTSList);
						// 	echo '<br><br>';
						// }
									$hrsOTLe8 = $hrsOt;//original code
									if($hrsOt < 8 && $valTSList['CWWTag']=='Y' && $valTSList['dayType'] == '02') {
										$hrsOTLe8 = $hrsOt;
									}
								}
									$hrsOTGt8 = 0;
							} else {
								if($valTSList['shiftCode'] == '01' && $sat==6) {
									//minus break
									$hrsOTLe8 = $hrsOt;
									$hrsOTGt8 = 0;
									// if($valTSList['empNo'] == '010000073') {
									// 	echo $hrsOt . '<br>';
									// 	echo $valTSList['shiftCode'] . '<br>';
									// 	echo $sat . '<br>';
									// 	echo $time['hrsWork'] . " - " . $valTSList['tsDate'] . ' - ' . $valTSList['dayType'] . '<br>' . '<br>'; 
									// 	var_dump($valTSList) . '<br>' . '<br>';
									// 	echo $valTSList['otIn'] . '<br>' . '<br>';
									// 	echo $valTSList['otOut'] . '<br>' . '<br>';
									// 	echo $valTSList['otOut'] . '<br>' . '<br>';
									// 	echo $SchedHrsWork;
									// }
								}else{

									// if($valTSList['empNo'] == '010000073') {
									// 	echo $hrsOt . '<br>';
									// 	echo $valTSList['shiftCode'] . '<br>';
									// 	echo $sat . '<br>';
									// 	echo $time['hrsWork'] . " - " . $valTSList['tsDate'] . ' - ' . $valTSList['dayType'] . '<br>' . '<br>'; 
									// 	var_dump($valTSList) . '<br>' . '<br>';
									// 	echo $valTSList['otIn'] . '<br>' . '<br>';
									// 	echo $valTSList['otOut'] . '<br>' . '<br>';
									// 	echo $valTSList['otOut'] . '<br>' . '<br>';
									// 	echo $SchedHrsWork;
									// }

									$hrsOTLe8 = 8;
									//$hrsOTGt8 = ($hrsOt-8)-$hrsTardy-$hrsUT;
									$hrsOTGt8 = ($hrsOt-8);
									$hrsTardy = 0;
								}
							}
						}
							$otTag = ",otTag='Y'"; 
							if ($Trns) {				
								$sqlOT="Insert into tblTK_Overtime (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8,hrsRegNDLe8)  values ('{$_SESSION['company_code']}','{$valTSList['empNo']}','{$valTSList['tsDate']}','{$valTSList['dayType']}','$hrsOTLe8','$hrsOTGt8','$hrsND','$hrsregND')\n";
								$Trns = $this->execQryI($sqlOT);
							}						
						}
	
				} else {
					$hrsOt = 0;
				}
			} elseif ($hrsND > 0 || $hrsregND > 0) {
				if ($Trns) {				
					$sqlOT ="Insert into tblTK_Overtime (compCode, empNo, tsDate, dayType, hrsOTLe8, hrsOTGt8, hrsNDLe8,hrsRegNDLe8)  values ('{$_SESSION['company_code']}','{$valTSList['empNo']}','{$valTSList['tsDate']}','{$valTSList['dayType']}',0,0,'$hrsND','$hrsregND')\n";			
					$Trns = $this->execQryI($sqlOT);
				} 
				$otTag = ",otTag='Y'";
			}

			if ($valTSList['CWWTag']=='Y' && $this->IsLeaveAppType($valTSList['tsAppTypeCd']) && $valTSList['deductTag']=='Y') {
				$hrsTardy += 1;
			}
			

			if ($hrsTardy !=0) {
				$cwwhrstardy =$hrsTardy;
				$fieldDed=",dedTag='Y'";
				
				$hrsTardy = round($hrsTardy, 2);
				$minTardy = round($hrsTardy * 60, 0);
				//echo $hrsTardy . ' == ' . $minTardy . '<br><br>';
				if ($Trns) {
					//12-08-2023 Add Managers remaining late time here
					$isManager = $this->getRecCount("SELECT * FROM tbltk_managersattendance WHERE empNo='{$valTSList['empNo']}'");
					if ($isManager > 0) {
						// Retrieve the current payroll period
						$current = $this->getTblData("tblPayPeriod", "payGrp='1' and payCat = '3' and pdStat IN ('O','')");
					
						// Determine if we need to reset the immunity
						if ($current["pdNumber"] % 2 != 0) {
							// If it's an odd period number (i.e., 1, 3, 5, ...), reset immunity
							$LateRemainingInMins = 300;
						} else {
							// Fetch the remaining immunity from the previous period
							$previousPeriod = $current["pdNumber"] - 1;
							$lateRecord = $this->getTblData("tbltk_managersattendanceLateRecord", "empNo='{$valTSList['empNo']}' and period='{$previousPeriod}'");
							if ($lateRecord) {
								$LateRemainingInMins = $lateRecord['LateRemainingInMins'];
							} else {
								$LateRemainingInMins = 300;
							}
						}
					
						// Compute late minutes after immunity
						$LateUsedInMins = min($hrsTardy, $LateRemainingInMins);
						$remainingLate = max(0, $hrsTardy - $LateRemainingInMins);
						$LateRemainingInMins = max(0, $LateRemainingInMins - $hrsTardy);
					
						// Insert or update the late immunity record for the current period
						$lateRecordCurrent = $this->getRecCount("SELECT * FROM tbltk_managersattendanceLateRecord WHERE empNo='{$valTSList['empNo']}' and period='{$current["pdNumber"]}'");
						if ($lateRecordCurrent > 0) {
							$sqlUpdateLateRecord = "UPDATE tbltk_managersattendanceLateRecord SET LateRemainingInMins={$LateRemainingInMins}, LateUsedInMins=LateUsedInMins+{$LateUsedInMins} WHERE empNo='{$valTSList['empNo']}' and period='{$current["pdNumber"]}'";
							$this->execQryI($sqlUpdateLateRecord);
						} else {
							$sqlInsertLateRecord = "INSERT INTO tbltk_managersattendanceLateRecord (empNo, LateRemainingInMins, LateUsedInMins, period) VALUES ('{$valTSList['empNo']}', {$LateRemainingInMins}, {$LateUsedInMins}, '{$current["pdNumber"]}')";
							$this->execQryI($sqlInsertLateRecord);
						}
					
						// Insert deduction if there are remaining late minutes
						if ($remainingLate > 0) {
							$sqlDeductions = "INSERT INTO tblTK_Deductions (compCode, empNo, tsDate, hrsTardy, minTardy) VALUES ('{$_SESSION['company_code']}','{$valTSList['empNo']}','{$valTSList['tsDate']}','$remainingLate', '$minTardy')";
							$this->execQryI($sqlDeductions);
						}
					
						// Update the manager's attendance
						$sqlManagerAtt = "UPDATE tbltk_managersattendance SET LateRemainingInMins=LateRemainingInMins-{$hrsTardy}, LateUsedInMins=LateUsedInMins+{$LateUsedInMins} WHERE empNo='{$valTSList['empNo']}'";
						$this->execQryI($sqlManagerAtt);
					} else {
						$sqlDeductions = "Insert into tblTK_Deductions (compCode, empNo, tsDate, hrsTardy, minTardy) values ('{$_SESSION['company_code']}','{$valTSList['empNo']}','{$valTSList['tsDate']}','$hrsTardy', '$minTardy');";
						$Trns = $this->execQryI($sqlDeductions);
					}
					//12-08-2023
				}
			}

			if($hrsUT != 0) {
				$cwwhrsut = $hrsUT;
				$fieldDed=",dedTag='Y'";
				$hrsUT = round($hrsUT, 2);
				$minUT = round($hrsUT * 60, 0);

				if ($Trns) {
					$recordCount = $this->getRecCount("SELECT * FROM tblTK_Deductions WHERE empNo='{$valTSList['empNo']}' AND tsDate='{$valTSList['tsDate']}'");
					if($recordCount > 0) {
						$sqlDeductions = "UPDATE tblTK_Deductions SET hrsUT = '{$hrsUT}', minUT = '{$minUT}' WHERE empNo='{$valTSList['empNo']}' AND tsDate='{$valTSList['tsDate']}'";
						$Trns = $this->execQryI($sqlDeductions);
					}else{
						$sqlDeductions = "Insert into tblTK_Deductions (compCode, empNo, tsDate, hrsUT, minUT) values ('{$_SESSION['company_code']}','{$valTSList['empNo']}','{$valTSList['tsDate']}','$hrsUT','$minUT');";
						$Trns = $this->execQryI($sqlDeductions);
					}
				}
			}

			$hrsWrk = ($hrsWrk < 0) ? 0:$hrsWrk;
			
			if ($valTSList['CWWTag']=='Y') {
				$CWWHrswrk =0;

				$cwhrsWrk = ($cwwhrstardy > 0) ? $hrsWrk+$cwwhrstardy:$hrsWrk;
				$cwhrsWrk += ($cwwhrsut > 0) ? $hrsWrk+$cwwhrsut:$hrsWrk;
				$dayCode = date('N',strtotime($valTSList['tsDate']));
					//old code
				

				if ($SchedHrsWork<=10)
					$CWWHrswrk = ($cwhrsWrk > 8) ? $cwhrsWrk-8:0;
				else
					$CWWHrswrk =0;

				$cwhrsWrk=0;
				$sqlCWW = "Insert into tblTK_HrsWorkedRepository (compCode, empNo, tsDate, dayCode, hrsWorked) values ('{$_SESSION['company_code']}','{$valTSList['empNo']}','".date('Y-m-d',strtotime($valTSList['tsDate']))."','$dayCode','$CWWHrswrk');";
				if ($Trns) {
					$Trns = $this->execQryI($sqlCWW);
				} 				
				if ($dayCode == 6) {
					$sqlUpdateSat = "update tblTK_HrsWorkedRepository set satDate='{$valTSList['tsDate']}' where empNo='{$valTSList['empNo']}' and tsDate<='{$valTSList['tsDate']}' and satDate is null;";
					if ($Trns) {
						$Trns = $this->execQryI($sqlUpdateSat);
					}
				}

			}
			
			///ALEJO						
			$sqlUpdateTS = " Update tblTK_Timesheet set hrsWorked='$hrsWrk',hrsRequired='$SchedHrsWork' $fieldDed $otTag where empNo='{$valTSList['empNo']}' AND tsDate = '{$valTSList['tsDate']}' AND compCode='{$_SESSION['company_code']}'; \n";
			// if ($valTSList['empNo']=='010000065')
			// 	echo "$sqlUpdateTS\n";
			
			if ($Trns) {
				$Trns = $this->execQryI($sqlUpdateTS);
			}
			unset($fieldDed,$fieldAppType,$legalPayTag);	
		}

		if ($Trns) {
			// $Trns = $this->updateTSsatDate();
			$this->updateTSsatDate();
		} 

		$sqlLegHold = "";
		$arrEmpWithPay = $this->getEmployeeListWithHolidayPay();
		foreach($this->getempLegalHolidays() as $valHol) {
			if ((float)$valHol['hrsOT']> 0 || (float)$valHol['hrsOT'] != "") {
				$datehol =date('Y-m-d',strtotime($valHol['tsDate']));
				$empno =$valHol['empNo'];
				$sqlLegHold =" Update tblTK_timesheet set legalPayTag='Y' where empNo='$empno' and tsDate='$datehol'";
				//$sqlLegHold .=" Update tblTK_timesheet set legalPayTag='Y' where empNo='$empno' and tsDate='$datehol'; \n";
			} else {
				/*if (in_array($valHol['empNo'],$arrEmpWithPay)) {
					$datehol =date('Y-m-d',strtotime($valHol['tsDate']));
					$sqlLegHold .=" Update tblTK_timesheet set legalPayTag='Y' where empNo='{$valHol['empNo']}' and tsDate='$datehol'; \n";
				}else {*/ 
					if ($this->checkExemp($valHol['empNo'],'Absent')) {
						$datehol =date('Y-m-d',strtotime($valHol['tsDate']));
						$empno =$valHol['empNo'];
						$sqlLegHold =" Update tblTK_timesheet set legalPayTag='Y' where empNo='$empno' and tsDate='$datehol'";
						//$sqlLegHold .=" Update tblTK_timesheet set legalPayTag='Y' where empNo='$empno' and tsDate='$datehol'; \n";
					} else {
						$datehol =date('Y-m-d',strtotime($valHol['tsDate']));
						$empno =$valHol['empNo'];
						$legalTag = $this->getEmpTimesheet($empno,$datehol,$valHol['dayType']);
							$sqlLegHold =" Update tblTK_timesheet set legalPayTag='$legalTag' where empNo='$empno' and tsDate='$datehol'";
						//$sqlLegHold .=" Update tblTK_timesheet set legalPayTag='$legalTag' where empNo='$empno' and tsDate='$datehol';\n";
					}
				///}
			}

			$this->execQryI($sqlLegHold);
		}
		
		if ($sqlLegHold != "") {
			if ($Trns) {
				//$Trns = $this->execMultiQryI($sqlLegHold);
			} 
		} 		
		
		//convert hrsOT to amtOT
		$sqlUpdateOTtable = "";
		foreach($this->getOTforComputation() as $valOT) {
			$emp_allowance = $this->getEmpAllowance($_SESSION['company_code'], $valOT['empNo']);

			$allowance = 0;
			if($emp_allowance['allowAmt'] != '') {
				$allowance = $emp_allowance['allowAmt'];
			}

			//$DailyWithAllowance = round((((float)$valOT['empMrate'] + $allowance) * 12) / (float)$getCompInfo['compDaysInYear'], 2);
			$DailyWithAllowance = (((float)$valOT['empMrate'] + $allowance) * 12) / (float)$getCompInfo['compDaysInYear'];
			$HourWithAllowance = $DailyWithAllowance / 8;

			// echo "Emp No : " . $valOT['empNo'] . '<br>';
			// echo "tsDate : " . $valOT['tsDate'] . '<br>';
			// echo "Monthly : " . $valOT['empMrate'] . '<br>';
			// echo "Allowance : " . $allowance . '<br>';
			// echo "Daily With Allowance : " . $DailyWithAllowance . '<br>';
			// echo "Hour With Allowance : " . $HourWithAllowance . '<br>';

			$amtOTLe8 = round((float)$valOT['hrsOTLe8'] * (float)$valOT['otPrem8'] * $HourWithAllowance, 2);
			$amtOTGt8 = round((float)$valOT['hrsOTGt8'] * (float)$valOT['otPremOvr8'] *$HourWithAllowance, 2);
			// echo "amtOTLe8 : " . $amtOTLe8 . '<br>';
			// echo "amtOTGt8 : " . $amtOTGt8 . '<br>';

			// $amtOTLe8 = round((float)$valOT['hrsOTLe8'] * (float)$valOT['otPrem8'] * ((float)$valOT['empDrate']/8),2);
			// $amtOTGt8 = round((float)$valOT['hrsOTGt8'] * (float)$valOT['otPremOvr8'] * ((float)$valOT['empDrate']/8),2);
			
			/*if ($valOT['daytype']=="07" && $valOT['hrsOTGt8'] !="" ) {
				$amtOTGt8 = round((float)$valOT['hrsOTGt8'] * (float)$valOT['otPremOvr8'] * ((float)$valOT['empDrate']/8),2);
			}else{
			$amtOTGt8 = round((float)$valOT['hrsOTGt8'] * (float)$valOT['otPremOvr8'] * ((float)$valOT['empDrate']/8),2);
			}*/
			$amtRegNDLe8 = round((float)$valOT['hrsRegNDLe8'] * (float)$valOT['ndreg'] * $HourWithAllowance,2);
			$amtNDLe8 = round((float)($valOT['hrsNDLe8']) * (float)$valOT['ndPrem8'] * $HourWithAllowance,2);
			// echo "amtRegNDLe8 : " . $amtRegNDLe8 . '<br>';
			// echo "amtNDLe8 : " . $amtNDLe8 . '<br><br><br>';
			// echo "amtRegNDLe8 : " . $amtRegNDLe8 . "</br>";
			// echo "amtNDLe8 : " . $amtNDLe8 . "</br></br></br>";

			$sqlUpdateOTtable = " Update tblTK_Overtime set amtOTLe8='$amtOTLe8',amtOTGt8='$amtOTGt8',amtNDLe8='$amtNDLe8',amtRegNDLe8='$amtRegNDLe8' where compCode='{$_SESSION['company_code']}' AND empNo='{$valOT['empNo']}' AND tsDate='{$valOT['tsDate']}'";	
			
			if ($sqlUpdateOTtable != "") {
				if ($Trns) {
					$Trns = $this->execQryI($sqlUpdateOTtable);
				} 
			}	
		}
		
		if ($sqlUpdateOTtable != "") {
			if ($Trns) {
				//$Trns = $this->execMultiQryI($sqlUpdateOTtable);
			} 
		}		
		
		
		//convert hrstardy and hrsUT to amt
		$sqlUpdateDedtable = "";
		foreach($this->getDedforComputation() as $valDed) {
			$amtUT="";
			//orig tardy compute
			// $amtTardy = round((float)$valDed['hrsTardy'] * ((float)$valDed['empDrate']/8), 2);
			// $amtUT = round((float)$valDed['hrsUT'] * ((float)$valDed['empDrate']/8), 2);

			//03-14-2024 manually compute daily rate  //start
			$emp_allowance = $this->getEmpAllowance($_SESSION['company_code'], $valDed['empNo']);

			$allowance = 0;
			if($emp_allowance['allowAmt'] != '') {
				$allowance = $emp_allowance['allowAmt'];
			}

			$DailyWithAllowance = (((float)$valDed['empMrate'] + $allowance) * 12) / (float)$getCompInfo['compDaysInYear'];
			//03-14-2024 manually compute daily rate //end

			// $amtTardy = round((float)$valDed['hrsTardy'] * ((float)$DailyWithAllowance/8), 2);
			// $amtUT = round((float)$valDed['hrsUT'] * ((float)$DailyWithAllowance/8), 2);
			$amtTardy = round((($DailyWithAllowance/8)/60)*$valDed['minTardy'], 2);
			$amtUT = round(((($DailyWithAllowance/8)/60)*$valDed['minUT']), 2);
			// echo $valDed['empNo'] . ' - ';
			// echo $amtTardy . ' - ';
			// echo $amtUT . '<br><br>';
			$sqlUpdateDedtable = " Update tblTK_Deductions set amtTardy='$amtTardy', amtUT='$amtUT' where compCode='{$_SESSION['company_code']}' AND empNo='{$valDed['empNo']}' AND tsDate='{$valDed['tsDate']}';\n";	
			$this->execQryI($sqlUpdateDedtable);
		}
		if ($sqlUpdateDedtable != "") {
			if ($Trns) {
				//$Trns = $this->execQryI($sqlUpdateDedtable);
			} 
		}		
		


		
		if(!$Trns){
			$Trns = $this->rollbackTranI();//rollback transaction
			return false;
		}
		else{
			$Trns = $this->commitTranI();//commit transaction
			return true;	
		}		
	}

	function GetPayPeriod() {
		$sqlpayPd = "Select pdFrmDate,pdToDate from tblPayPeriod where compCode='{$_SESSION['company_code']}' and payCat=3 and payGrp='{$this->Group}' AND pdTSStat='O'";
		$res = $this->getSqlAssocI($this->execQryI($sqlpayPd));
		$this->pdFrom = date('Y-m-d',strtotime($res['pdFrmDate']));
		$this->pdTo = date('Y-m-d',strtotime($res['pdToDate']));
	}

	function getEmpList() {
		$sqlEmpList = "SELECT tblTK_Timesheet.empNo,DATE_FORMAT(tblTK_Timesheet.tsDate,'%Y-%m-%d') as tsDate, 
                      tblTK_Timesheet.dayType, tblTK_Timesheet.shftTimeIn, tblTK_Timesheet.otCrossTag, 
                      tblTK_Timesheet.shftLunchOut, tblTK_Timesheet.shftLunchIn, tblTK_Timesheet.shftBreakOut, 
                      tblTK_Timesheet.shftBreakIn, tblTK_Timesheet.shftTimeOut, tblTK_Timesheet.tsAppTypeCd, 
                      tblTK_Timesheet.timeIn, tblTK_Timesheet.lunchOut, tblTK_Timesheet.lunchIn, 
                      tblTK_Timesheet.breakOut, tblTK_Timesheet.breakIn, tblTK_Timesheet.timeOut, tblTK_Timesheet.otIn, 
                      tblTK_Timesheet.otOut, tblTK_Timesheet.hrsWorked, tblTK_Timesheet.legalPayTag, 
                      tblTK_Timesheet.attendType, tblTK_Timesheet.brnchCd AS brnCode, tblTK_Timesheet.crossDay, 
                      tblTK_Timesheet.dedTag, tblTK_Timesheet.otTag, tblEmpMast.empPayType, tblEmpMast.empBrnCode, 
                      tblEmpMast.empDiv, tblTK_Timesheet.hrs8Deduct, tblTK_Timesheet.obTag, tblTK_Timesheet.csTag, 
                      tblTK_Timesheet.crdTag, tblTK_Timesheet.editReason, 
					  tblTK_EmpShift.CWWTag, tblTK_EmpShift.shiftCode
					  FROM tbltk_timesheet LEFT OUTER JOIN
                      tblEmpMast ON tblTK_Timesheet.compcode = tblEmpMast.compCode AND 
                      tblTK_Timesheet.empNo = tblEmpMast.empNo LEFT OUTER JOIN
                      tblTK_EmpShift ON tblEmpMast.empNo = tblTK_EmpShift.empNo
					  WHERE tblTK_Timesheet.compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND  empPayGrp='{$this->Group}'
											AND empBrnCode IN ((Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y')) order by tblTK_Timesheet.tsDate
						";
						//echo var_dump($this->getArrResI($this->execQryI($sqlEmpList)));
		return $this->getArrResI($this->execQryI($sqlEmpList));		
	}
	
	
	function getholiday() {
		$sqlHoliday = "Select holidayDate, brnCode, dayType from tblHolidayCalendar where compCode='{$_SESSION['company_code']}' AND holidayStat='A' AND holidaydate BETWEEN '{this->pdFrom}' AND '{this->pdTo}'";
		$this->arrHolidays = $this->getArrResI($this->execQryI($sqlHoliday));		
	}
	function checkHolidayDate($tsDate,$RDTag,$brnCode) {
		if ($RDTag=='Y') 
			$dayType='02';
		else
			$dayType='01';
		foreach($this->arrHolidays as $valHol) {
			if (date('Y-m-d',strtotime($valHol['holidayDate'])) == date('Y-m-d',strtotime($tsDate)) && ($valHol['brnCode'] =='0' || $valHol['brnCode']==$brnCode)) {
				if ($RDTag == 'Y') {
					if ($valHol['dayType']=='03')
						$dayType='05';
					elseif ($valHol['dayType']=='04')
						$dayType='06';
					elseif ($valHol['dayType']=='07')
						$dayType="08";
				} else {
					$dayType=$valHol['dayType'];
				}
			}
		}
		return $dayType;
	}
	
	function DateAdd($date) {
		$month = date('m',strtotime($date));
		$day = date('d',strtotime($date));
		$year = date('Y',strtotime($date));
		$Maxdays = cal_days_in_month(CAL_GREGORIAN,$month,$year);
		$day++;
        if ($day>$Maxdays){
			$day = 1;
			$month++;
			if ($month>12) {
				$month = 1;
				$year++;
			} 
		}
		return date('Y-m-d',strtotime("$month/$day/$year"));
	}	
	
	function getUTExemptEmp() {
		$sqlUTExempt = "SELECT tblEmpMast.empNo FROM tblTK_RankLevelTimeExempt INNER JOIN tblEmpMast ON tblTK_RankLevelTimeExempt.compCode = tblEmpMast.compCode AND tblTK_RankLevelTimeExempt.exemptLevelCd = tblEmpMast.empLevel AND  tblTK_RankLevelTimeExempt.exemptRankCd = tblEmpMast.empRank
						WHERE (tblTK_RankLevelTimeExempt.utHrsExempt = 'Y') AND tblTK_RankLevelTimeExempt.compCode='{$_SESSION['company_code']}'";
		$this->arrUTExempEmpList = $this->getArrResI($this->execQryI($sqlUTExempt));				
	}
	function getAbsentExemptEmp() {
		$sqlAbsentExempt = "SELECT tblEmpMast.empNo FROM tblTK_RankLevelTimeExempt INNER JOIN tblEmpMast ON tblTK_RankLevelTimeExempt.compCode = tblEmpMast.compCode AND tblTK_RankLevelTimeExempt.exemptLevelCd = tblEmpMast.empLevel AND  tblTK_RankLevelTimeExempt.exemptRankCd = tblEmpMast.empRank
						WHERE (tblTK_RankLevelTimeExempt.absentExempt = 'Y') AND tblTK_RankLevelTimeExempt.compCode='{$_SESSION['company_code']}'";
		$this->arrAbsentExempEmpList = $this->getArrResI($this->execQryI($sqlAbsentExempt));				
	}
	function getTardyExemptEmp() {
		$sqlTardyExempt = "SELECT tblEmpMast.empNo FROM tblTK_RankLevelTimeExempt INNER JOIN tblEmpMast ON tblTK_RankLevelTimeExempt.compCode = tblEmpMast.compCode AND tblTK_RankLevelTimeExempt.exemptLevelCd = tblEmpMast.empLevel AND  tblTK_RankLevelTimeExempt.exemptRankCd = tblEmpMast.empRank
						WHERE (tblTK_RankLevelTimeExempt.trdHrsExempt = 'Y') AND tblTK_RankLevelTimeExempt.compCode='{$_SESSION['company_code']}'";
		$this->arrTardyExempEmpList = $this->getArrResI($this->execQryI($sqlTardyExempt));				
	}
	function getFlexExemptEmp() {
		/*$sqlFlexExempt = "SELECT tblEmpMast.empNo FROM tblTK_RankLevelTimeExempt INNER JOIN tblEmpMast ON tblTK_RankLevelTimeExempt.compCode = tblEmpMast.compCode AND tblTK_RankLevelTimeExempt.exemptLevelCd = tblEmpMast.empLevel AND  tblTK_RankLevelTimeExempt.exemptRankCd = tblEmpMast.empRank
						WHERE (tblTK_RankLevelTimeExempt.flexiExempt = 'Y') AND tblTK_RankLevelTimeExempt.compCode='{$_SESSION['company_code']}'";*/
						
		$sqlFlexExempt = "SELECT tblEmpMast.empNo FROM tbltk_empflex INNER JOIN tblEmpMast ON tbltk_empflex.compCode = tblEmpMast.compCode AND tbltk_empflex.empNo = tblEmpMast.empNo 
						WHERE tbltk_empflex.empNo = tblEmpMast.empNo  AND tbltk_empflex.compCode='{$_SESSION['company_code']}'";
		$this->arrFlexExempEmpList = $this->getArrResI($this->execQryI($sqlFlexExempt));				
	}	
	function getOTExemptEmp() {
		$sqlOTExempt = "SELECT tblEmpMast.empNo FROM tblTK_RankLevelTimeExempt INNER JOIN tblEmpMast ON tblTK_RankLevelTimeExempt.compCode = tblEmpMast.compCode AND tblTK_RankLevelTimeExempt.exemptLevelCd = tblEmpMast.empLevel AND  tblTK_RankLevelTimeExempt.exemptRankCd = tblEmpMast.empRank
						WHERE (tblTK_RankLevelTimeExempt.otExempt = 'Y') AND tblTK_RankLevelTimeExempt.compCode='{$_SESSION['company_code']}'";
		$this->arrOTExempEmpList = $this->getArrResI($this->execQryI($sqlOTExempt));				
	}

	function checkExemp($empNo,$type) {
		$res = false;
		switch($type) {
			case 'UT':
				foreach($this->arrUTExempEmpList as $val) {
					if ($empNo==$val['empNo']) {
						$res = true;
					}
				}
			break;
			case 'Absent':
				foreach($this->arrAbsentExempEmpList as $val) {
					if ($empNo==$val['empNo']) {
						$res = true;
					}
				}
			break;
			case 'Tardy':
				foreach($this->arrTardyExempEmpList as $val) {
					if ($empNo==$val['empNo']) {
						$res = true;
					}
				}
			break;						
			case 'Flex':
				foreach($this->arrFlexExempEmpList as $val) {
					if ($empNo==$val['empNo']) {
						$res = true;
					}
				}
			break;
			case 'OT':
				foreach($this->arrOTExempEmpList as $val) {
					if ($empNo==$val['empNo']) {
						$res = true;
					}
				}
			break;
											
		}
		return $res;
	}

	function computehrsWork($arr) 
	{
		//balik dito
		// if($arr['empNo'] == '010000073') {
		// 	echo $arr['empNo'] . '<br>';
		// 	echo $arr['timeIn'] . '<br>';
		// 	echo $arr['timeOut'] . '<br>';
		// 	echo $arr['otIn'] . '<br>';
		// 	echo $arr['otOut'] . '<br><br>';
		// }
		$SchedHrsWork = $this->getSchedHrsWork($arr);
		$time['In'] = $SchedtimeIn;
		$time['hrsOT'] = $time['hrsND'] = $time['hrsUT'] = $time['hrsTardy'] = $time['hrsregND'] = 0;
		//Test Lang
		//$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$arr['timeIn']}","{$arr['tsDate']} {$arr['timeOut']}",'m')/60,2);
		//1/3/2023
		// if($arr['empNo'] == '010000065' && $arr['tsDate'] == '2023-10-25') {
		// 	echo $time['hrsWork'] . " - " . $arr['tsDate'] . ' - ' . $arr['dayType'] . '<br>' . '<br>'; 
		// 	var_dump($arr) . '<br>' . '<br>';
		// }
		if ($arr['dayType']=='01')
		{ //regular day
			if ($this->IsLeaveAppType($arr['tsAppTypeCd'])) 
			{ //apptype is an leavetype
				//Compute hours worked for half day Leave
				if(in_array($arr['tsAppTypeCd'],array(13,15))) 
				{//halfday leave PM
					$arrSched = $this->getHalfDaySched($arr);	
					if ((float)str_replace(":",".",$arrSched['In'])<(float)str_replace(":",".",$arr['timeIn'])) {
					 	$time['In'] = $arr['timeIn'];
						$time['hrsTardy'] = round($this->calDiff("{$arr['tsDate']} {$arrSched['In']}","{$arr['tsDate']} {$arr['timeIn']}",'m')/60,2);	
					} else {
						$time['In'] = $arrSched['In'];
					}
					//if(in_array($arr['tsAppTypeCd'],array(15))){ 
				
					if ((float)str_replace(":",".",$arrSched['Out'])>(float)str_replace(":",".",$arr['lunchOut'])) {
					 	$time['Out'] = $arr['lunchOut'];
						$time['hrsUT'] = round($this->calDiff("{$arr['tsDate']} {$arr['lunchOut']}","{$arr['tsDate']} {$arrSched['Out']}",'m')/60,2);
					} else {
						$time['Out'] = $arrSched['Out'];
					}
					//}

					if ($arr['obTag'] == '') {
						$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
					} else {
						if ($arr['hrs8Deduct'] == '') { 
							$time['Out'] = $arr['timeOut'];
							$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$time['Out']}",'m')/60,2);			
						} else {
							$time['hrsWork']	= 4;					
							$time['hrsTardy']	= 0;
							$time['hrsUT']		= 0;							
						}
					}
/*					if ($arr['empNo'] == '280000309')
						echo "{$arr['tsDate']} {$time['hrsWork']}\n";*/
						
						
					if ($arr['tsAppTypeCd']==13 && $arr['obTag'] == '') {
						$time['hrsWork'] =  $time['hrsWork'] + ($SchedHrsWork - $time['hrsWork']) - $time['hrsTardy'] - $time['hrsUT'];					
					}elseif ($arr['tsAppTypeCd']==15) {
						$time['hrsWork'] =  $time['hrsWork']  - $time['hrsTardy'] - $time['hrsUT'];					
					}elseif ($arr['tsAppTypeCd']==13 && $arr['obTag'] == 'Y' && $arr['hrs8Deduct'] == '') {
						$hdhrwk=$time['hrsWork']+4;
						$time['hrsWork'] = ($hdhrwk>$SchedHrsWork)? $SchedHrsWork: $hdhrwk;
					}
/*					if ($arr['empNo'] == '280000309')
						echo "{$arr['tsDate']} {$time['hrsWork']}\n";*/
				} 
				elseif (in_array($arr['tsAppTypeCd'],array(12,14))) {//halfday leave AM
				
									$arrSched = $this->getHalfDaySched($arr);	
					if ((float)str_replace(":",".",$arrSched['In'])<(float)str_replace(":",".",$arr['lunchIn'])) {
					 	$time['In'] = $arr['lunchIn'];
						$time['hrsTardy'] = round($this->calDiff("{$arr['tsDate']} {$arrSched['In']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);	
					} else {
						$time['In'] = $arrSched['In'];
					}

					if ($arr['otCrossTag']=='Y') {// if OT is cross day
							$tOut = $this->DateAdd($arr['tsDate'])." {$time['Out']}";					
					} else {
						if ((float)str_replace(":",".",$arrSched['Out'])>(float)str_replace(":",".",$arr['timeOut'])) {
							$time['Out'] = $arr['timeOut'];
							$time['hrsUT'] = round($this->calDiff("{$arr['tsDate']} {$arr['timeOut']}","{$arr['tsDate']} {$arrSched['Out']}",'m')/60,2);
						} else {
							$time['Out'] = $arrSched['Out'];
						}
						$tOut = "{$arr['tsDate']} {$time['Out']}";

					}
					if ($arr['obTag'] == '') {
						$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$time['In']}","$tOut",'m')/60,2);
						
					} else {
						if ($arr['hrs8Deduct'] == '') {
							$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$time['In']}","$tOut",'m')/60,2);			
						} else {
							$time['hrsWork'] 	= 4;					
							$time['hrsTardy']	= 0;
							$time['hrsUT']		= 0;							
						}
					}
					if ($arr['tsAppTypeCd']==12) {
						$time['hrsWork'] = $time['hrsWork'] + ($SchedHrsWork - $time['hrsWork']) - $time['hrsTardy'] - $time['hrsUT'];					
					}

					//Compute ND for regular Day halfday AM
					if ((float)str_replace(":",".",$time['Out'])>22 )  {
						$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
						$time['hrsregND'] = $time['hrsND'];
					} elseif((float)str_replace(":",".",$time['In'])<=6) {
						// $time['hrsND'] = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} 06:00",'m')/60,2);

						$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
						$time['hrsregND'] = $time['hrsND'];
					}
										
				} elseif(in_array($arr['tsAppTypeCd'],array('04','05','06','07','18','22'))) {//compute hours work for leave w/ pay
					$time['hrsWork'] = $SchedHrsWork;
				} elseif (in_array($arr['tsAppTypeCd'],array(16,17,08,11,19,20))) {//compute hours work for leave w/o pay
					$time['hrsWork'] = 0;
				} elseif (in_array($arr['tsAppTypeCd'],array(21))) {//compute hours work for leave combi
					$time['hrsWork'] = 4;
				}
				//End of half day leave
				
				if($arr['tsAppTypeCd']==12) {//compute OT for half day leave w/ pay
					if ($arr['otIn']!='' && $arr['otOut']!='') {
						$OTOut = ((float)str_replace(":",".",$arr['otOut'])<(float)str_replace(":",".",$arr['timeOut'])) ? $arr['otOut']:$arr['timeOut'];
						$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$time['Out'])) ? $time['Out']:$arr['otIn'];						
						if ($arr['otCrossTag']=='Y') {// if OT is cross day
							$var = $this->calDiff("{$arr['tsDate']} $OTIn",$this->DateAdd($arr['tsDate'])." $OTOut",'m')/60;
						} else {
							$var = $this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} $OTOut",'m')/60;
						}
						
						//$time['hrsOT'] = (($time['hrsWork']-$SchedHrsWork)<$OtHrs) ? $time['hrsWork']-$SchedHrsWork:$OtHrs;
						$time['hrsOT'] = number_format(floor($var * 100) / 100, 2, '.', '');
						// if($arr['empNo'] == '010000073') {
						// 	echo $arr['empNo'] . '<br>';
						// 	echo $arr['timeIn'] . '<br>';
						// 	echo $arr['timeOut'] . '<br>';
						// 	echo $arr['otIn'] . '<br>';
						// 	echo $arr['otOut'] . '<br><br>';
						// 	echo $time['hrsOT'] . '<br>';
						// }
						
						//compute ND for OT
						if (((float)str_replace(":",".",$time['Out'])>22 && (float)str_replace(":",".",$arr['otOut'])>22) || $arr['otCrossTag']=='Y')  {
							$time['hrsND'] += $time['hrsOT'] - round($this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} 22:00",'m')/60,2);
						}
					}
				}
				
			} else { //if apptype is not leave type
			if ($this->checkExemp($arr['empNo'],'Flex')) {
				//$hrsTardy = 0;
				//$hrsWrk='11';
			$time['In'] = $arr['timeIn'];
			$time['Out'] = $arr['timeOut'];
			$cday=$arr['crossDay'];
			$SchedHrsWork=round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2);
			$hrsWrk = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
			if ((float)str_replace(":",".",$arr['shftTimeIn'])<(float)str_replace(":",".",$arr['timeIn'])) {
					 	//$time['In'] = $arr['timeIn'];
						$time['hrsTardy'] = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['timeIn']}",'m')/60,2);	
					}
					
					if($hrsWrk<$SchedHrsWork){
						if ((float)str_replace(":",".",$arr['shftTimeIn'])<=(float)str_replace(":",".",$arr['timeIn']) && (float)str_replace(":",".",$arr['timeOut'])<(float)str_replace(":",".",$arr['shftTimeOut'] )){
					$time['hrsUT'] =$SchedHrsWork-$hrsWrk;
							}
							elseif($time['hrsTardy']==0){
								$time['hrsUT'] =$SchedHrsWork-$hrsWrk;
							}
					}

//ND

	if ($cday=='Y' && (float)str_replace(":",".",$time['Out'])>22 || (float)str_replace(":",".",$time['Out'])<=6 && $arr['timeOut']!=""){
					
				
					$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00",$this->DateAdd($arr['tsDate'])."{$time['Out']}",'m')/60,2);
					
				$time['hrsregND'] = $time['hrsND'];
					}elseif ((float)str_replace(":",".",$time['Out'])>22 || (float)str_replace(":",".",$time['Out'])<=6 && $arr['timeOut']!="") {
						 
					$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
						$time['hrsregND'] = $time['hrsND'];
				}				
if ($cday!='Y'){$hrsWrk=$hrsWrk;}else {
	//$SchedHrsWork=($SchedHrsWork>9) ? 9:$SchedHrsWork;
	$hrsWrk=($hrsWrk>9) ? 9:$hrsWrk;}

			}
			else{
				//dito tignan
					if ((float)str_replace(":",".",$arr['shftTimeIn'])<(float)str_replace(":",".",$arr['timeIn'])) {
					 	$time['In'] = $arr['timeIn'];
						$time['hrsTardy'] = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['timeIn']}",'m')/60,2);	
					} else {
						$time['In'] = ($arr['timeIn'] == "" ) ? "00:00":$arr['shftTimeIn'];
					}

					if ((float)str_replace(":",".",$arr['shftTimeOut'])>(float)str_replace(":",".",$arr['timeOut']) && $arr['otCrossTag']!='Y' && $arr['timeOut'] !="") {
					 	
						if ($arr['crossDay']!='Y') {
							if ($arr['empBrnCode']!=='0001' && $SchedHrsWork<1  && $arr['CWWTag']=='') {
								$time['Out'] = $arr['lunchOut'];
								$time['hrsUT'] = round($this->calDiff("{$arr['tsDate']} {$arr['timeOut']}","{$arr['tsDate']} {$arr['shftLunchOut']}",'m')/60,2);
							} else {
								$time['Out'] = $arr['timeOut'];
								$time['hrsUT'] = round($this->calDiff("{$arr['tsDate']} {$arr['timeOut']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2);
							} 						
						} else {
							if ($arr['empBrnCode']!=='0001' && $SchedHrsWork<1  && $arr['CWWTag']=='') {
								$time['Out'] = $arr['lunchOut'];
								$time['hrsUT'] = round($this->calDiff("{$arr['tsDate']} {$arr['timeOut']}",$this->DateAdd($arr['tsDate'])." {$arr['shftLunchOut']}",'m')/60,2);
							} else {
								$time['Out'] = $arr['timeOut'];
								
								$time['hrsUT'] = round($this->calDiff("{$arr['tsDate']} {$arr['timeOut']}",$this->DateAdd($arr['tsDate'])." {$arr['shftTimeOut']}",'m')/60,2);
							} 					
						}						
						
					} else {
						
						if ($arr['empBrnCode']!=='0001' && $SchedHrsWork<1  && $arr['CWWTag']=='') 
							$time['Out'] = ($arr['lunchOut'] == "" ) ? "00:00":$arr['shftLunchOut'];
						else 
							$time['Out'] = ($arr['timeOut'] == "" ) ? "00:00":$arr['shftTimeOut'];
							$time['Out'] = ($arr['timeOut'] ==$arr['timeIn']) ? "00:00":$time['Out'];
					}			
				
			}//flex
				//if apptype is OB
				//balik dito
				if ($arr['obTag']=='Y') {
				
				// if($arr['empNo'] == '010000065' && $arr['tsDate'] == '2023-10-25' || $arr['empNo'] == '010000065' && $arr['tsDate'] == '2023-10-21') {
				// 	echo $time['hrsWork'] . " - " . $arr['tsDate'] . ' - ' . $arr['dayType'] . '<br>' . '<br>'; 
				// 	var_dump($arr) . '<br>' . '<br>';
				// }

					//check check
					if($arr['hrs8Deduct']=='Y') {
						//derek
						$time['hrsWork'] 	= $SchedHrsWork;
						$time['hrsTardy']	= 0;
						$time['hrsUT']		= 0;
					} else {
						$time['hrsWork'] = $SchedHrsWork;
						if (((float)$time['hrsTardy']+(float)$time['hrsUT']) == $SchedHrsWork){
							$time['hrsWork'] = 0;
						}
						else{
							$time['hrsWork'] = $SchedHrsWork - $time['hrsTardy'] - $time['hrsUT'];
						}
					}

					// if($arr['empNo'] == '010000065' && $arr['tsDate'] == '2023-11-11') {
					// 	echo $arr['empNo'] == '010000065' ? $arr['empNo'] . ' - ' . $arr['tsDate'] . '<br>' : '';
						
					// 	echo $arr['empNo'] == '010000065' ? $arr['timeIn'] . '<br>' : '';
					// 	echo $arr['empNo'] == '010000065' ? $arr['timeOut'] . '<br>' : '';
					// 	echo $arr['empNo'] == '010000065' ? $arr['dayType'] . '<br>' : '';
					// }

				} 
				//else {
					$shfthrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['shftLunchOut']}","{$arr['tsDate']} {$arr['shftLunchIn']}",'m')/60,2);
					$shfthrsLunch = ($shfthrsLunch<1 && $shfthrsLunch!=0.5) ? 1: $shfthrsLunch;
					
					if(($arr['editReason']==FAIL_LCHOUT) || ($arr['editReason']==FAIL_LCHIN) || ($arr['editReason']==FAIL_LCHINOUT) || ($arr['editReason']==FAIL_SKIPLUNCH))
						$hrsLunch = $shfthrsLunch;
					else{
						//orig code
						//$hrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['lunchOut']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);
						$hrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['shftLunchOut']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);
					}
					
					if ($arr['empBrnCode']!=='0001' && $SchedHrsWork<1  && $arr['CWWTag']=='') {
						$hrsLunch = 0;
					} 
					
					if(($arr['editReason']==FAIL_LOGOUT) && ($arr['lunchOut']!="" && $arr['timeOut']=="")) {
						$hrsWrk = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$arr['lunchOut']}",'m')/60,2);

					} else {
						if ((float)str_replace(":",".",$time['In'])>0) {
							if ((float)str_replace(":",".",$arr['shftTimeOut'])>=17 && (float)str_replace(":",".",$arr['timeOut'])<=7 && $arr['timeOut']!="") {
								$hrsWrk = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2);								
							}
							else{
								$hrsWrk = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$time['Out']}",'m')/60,2);	
							}
						} elseif ((float)str_replace(":",".",$time['In'])==0) {
							$hrsWrk = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$time['Out']}",'m')/60,2);								
						}
					}

					// if ($arr['empNo'] == '010000000')
					// 	echo $arr['empNo'] . " == " . $arr['tsDate'] . " == " . $hrsWrk . "<br/><br/>";
				
/*						if($arr['empNo']=='117000012' && date('Y-m-d',strtotime($arr['tsDate']))=='11/24/2013')
							echo "{$arr['tsDate']} $hrsWrk\n";
*/					
					if ($shfthrsLunch!=0.5)
						$hrsLunch = ($hrsLunch<1) ? 1:$hrsLunch;	

					//conpute hours work 

					if ($arr['lunchOut'] != $arr['lunchIn'] && $arr['lunchOut'] != '' && $arr['lunchIn'] != '') {
						if ($hrsLunch>$shfthrsLunch) {
							if ($shfthrsLunch == 0.5) {
								$time['hrsWork'] = $hrsWrk-($hrsLunch-$shfthrsLunch);
							} else {
								$time['hrsWork'] = $hrsWrk-$hrsLunch;
							}
							if ($hrsLunch>$shfthrsLunch){
								$time['hrsTardy'] += $hrsLunch-$shfthrsLunch;
							}
						}
						else {
							if ($hrsLunch>0 && $shfthrsLunch!=0.5){
								$time['hrsWork'] = $hrsWrk-$shfthrsLunch;
							}
							else{
								$time['hrsWork'] = $hrsWrk;
							}
						}
					} 
					else {
						$time['hrsWork'] = ($hrsWrk >= 5) ? $hrsWrk-$shfthrsLunch:$hrsWrk;
					}


					$time['hrsWork'] = ($time['hrsWork'] < 0) ? 0:$time['hrsWork'];
				
					if($arr['hrs8Deduct']!='Y') {
						if ($arr['breakOut'] !='' && $arr['breakIn'] !='') {
							$brkTime = round($this->calDiff("{$arr['tsDate']} {$arr['breakOut']}","{$arr['tsDate']} {$arr['breakIn']}",'m')/60,2);
							if ($brkTime>0.25) {
								$time['hrsWork'] -= $brkTime-0.25;
								$time['hrsTardy'] += $brkTime-0.25;
									
							}
							if ($brkTime>0 && $brkTime<0.83) {
								$this->AddViolation($arr['empNo'],$arr['tsDate'],'08');
							}						
						}
					}
				

				if ($hrsLunch>0 && $hrsLunch<0.33) {
					$this->AddViolation($arr['empNo'],$arr['tsDate'],'07');
				}
//					if ((float)str_replace(":",".",$valTSList['timeIn'])>=22) 
				//compute ND 
				if ($this->checkExemp($arr['empNo'],'Flex')) {
				// if ((float)str_replace(":",".",$time['Out'])>22 || (float)str_replace(":",".",$time['Out'])<=6 && $arr['timeOut']!="")  {
				
				// 	$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00",$this->DateAdd($arr['tsDate'])."{$time['Out']}",'m')/60,2);
				// 	$time['hrsregND'] = $time['hrsND'];
				// }
				
			}else{
				if ((float)str_replace(":",".",$time['Out'])>22 || (float)str_replace(":",".",$time['Out'])<=6 && $arr['timeOut']!="")  {
					if ((float)str_replace(":",".",$arr['shftTimeOut'])>=22 && (float)str_replace(":",".",$arr['timeOut'])<=7) {
						$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2);
						//$time['hrsND'] ='1';
					} else {
						$time['Out'] = $arr['timeOut'];
						//$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
						$time['hrsND'] =round($this->calDiff("{$arr['tsDate']} 22:00",$this->DateAdd($arr['tsDate'])."{$time['Out']}",'m')/60,2);

					}
					$time['hrsregND'] = $time['hrsND'];
				}elseif((float)str_replace(":",".",$time['In'])<=6) {
					$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} 06:00",'m')/60,2);
					//$time['hrsND'] ='3';
					$time['hrsregND'] = $time['hrsND'];
			}
			}
				//compute OT
					if ($arr['otIn']!='' && $arr['otOut']!='') {	//new code with flextime add by alejo
					$dayCode = date('N',strtotime($arr['tsDate']));
					//if ($arr['timeOut'] != "")
						if ($this->checkExemp($arr['empNo'],'Flex')) {
							$OTOut = $arr['otOut'];
						}else{
							if ($SchedHrsWork!=3.5) {
								$OTOut = $arr['otOut'];
								//01/03/2023
								// $OTOut = ((float)str_replace(":",".",$arr['otOut'])<(float)str_replace(":",".",$arr['timeOut'])) ? $arr['otOut']:$arr['timeOut'];
								// if($arr['empNo'] == '010000065' && $arr['tsDate'] == '2023-11-11') {
								// 	echo $OTOut . '<br>';
								// 	echo $time['hrsWork'] . " - " . $arr['tsDate'] . ' - ' . $arr['dayType'] . '<br>' . '<br>'; 
								// 	var_dump($arr) . '<br>' . '<br>';
								// 	echo $arr['otIn'] . '<br>' . '<br>';
								// 	echo $arr['otOut'] . '<br>' . '<br>';
								// 	echo $arr['otOut'] . '<br>' . '<br>';
								// 	echo $SchedHrsWork;
								// }
							}
							else{
								$OTOut = ((float)str_replace(":",".",$arr['otOut'])<(float)str_replace(":",".",$arr['lunchOut'])) ? $arr['otOut']:$arr['lunchOut'];
								//$OTOut = $arr['otOut'];
						}	
					}
					if ($dayCode==6 && $arr['CWWTag']=='Y') {
						$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$arr['timeIn'])) ? $arr['timeIn']:$arr['otIn'];				
						
					}else{
						if ($this->checkExemp($arr['empNo'],'Flex')) {
							$OTIn = $arr['otIn'];
							}else{
						$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$time['Out'])) ? $time['Out']:$arr['otIn'];//origcode
						}						
					}
						
					if ($arr['otCrossTag']=='Y') {  //if OT is cross day
						//$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$time['Out'])) ? $time['Out']:$arr['otIn'];						

						$var = $this->calDiff("{$arr['tsDate']} $OTIn",$this->DateAdd($arr['tsDate'])." $OTOut",'m')/60;
						$OtHrs = number_format(floor($var * 100) / 100, 2, '.', '');
						$time['hrsND'] = $OtHrs - round($this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} 22:00",'m')/60,2);

					} else {
						$var = $this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} $OTOut",'m')/60;
						$OtHrs = number_format(floor($var * 100) / 100, 2, '.', '');
						if (((float)str_replace(":",".",$arr['timeOut'])>22 || (float)str_replace(":",".",$arr['otOut'])>22))  {
							$time['hrsND'] += $OtHrs - round($this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} 22:00",'m')/60,2);
						}

						//hanap ot dito
						// if($arr['empNo'] == '010000073') {
						// 	echo "" . '<br>';
						// 	echo $OTOut . '<br>';
						// 	echo $time['hrsWork'] . " - " . $arr['tsDate'] . ' - ' . $arr['dayType'] . '<br>' . '<br>'; 
						// 	var_dump($arr) . '<br>' . '<br>';
						// 	echo $arr['otIn'] . '<br>' . '<br>';
						// 	echo $arr['otOut'] . '<br>' . '<br>';
						// 	echo $OtHrs . '<br>' . '<br>';
						// 	echo $SchedHrsWork;
						// }
					}
					
					//test ito if OT break gawin after 5 hrs 07/25/2024 ($dayCode==6 && $arr['CWWTag']=='Y' && $OtHrs >=5)
					if ($dayCode==6 && $arr['CWWTag']=='Y' && $OtHrs >=6){
						$OtHrs--;
					}
											
					$time['hrsOT'] = $OtHrs;

				}//new code with flextime add by alejo
				
			}

		// if	day type is not regular day
		} else {
			
			$time['hrsTardy'] 	= 0;
			$time['hrsUT']		= 0;

			if ($arr['otIn']!='' && $arr['otOut']!='' && $arr['timeIn']!='' && $arr['timeOut']!='') 
			{// with OT Application			
				if (in_array($arr['dayType'],array('02','05','06'))) 
				{
				
/*					$OTOut = $arr['otOut'];
					$OTIn = $arr['otIn'];*/
					$OTOut = ((float)str_replace(":",".",$arr['otOut'])<(float)str_replace(":",".",$arr['timeOut'])) ? $arr['otOut']:$arr['timeOut'];	
					$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$arr['timeIn'])) ? $arr['timeIn']:$arr['otIn'];				
					//edited by nhomer original code
					//$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$arr['otIn']}","{$arr['tsDate']} {$arr['otOut']}",'m')/60,2);
						
					if ($arr['otCrossTag']=='Y') {
						$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} {$arr['otIn']}",$this->DateAdd($arr['tsDate'])." {$arr['otOut']}",'m')/60,2);	
					}
					else{
						$time['hrsWork'] = round($this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} $OTOut",'m')/60,2);	
					}
				} 
				else 
				{
					$OTOut = ((float)str_replace(":",".",$arr['otOut'])<(float)str_replace(":",".",$arr['timeOut'])) ? $arr['otOut']:$arr['timeOut'];
					//if ($arr['empBrnCode'] != '0001' && $arr['empDiv']==7 && !in_array($arr['dayType'],array('02','05','06'))) {
					if (in_array($arr['empDiv'],array('2','3','4','5','6','7','10')) && !in_array($arr['dayType'],array('02','05','06'))) {
						$OTIn = ((float)str_replace(":",".",$arr['timeIn'])<(float)str_replace(":",".",$arr['shftTimeIn'])) ? $arr['shftTimeIn']:$arr['timeIn'];
					} else { 
						$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$arr['timeIn'])) ? $arr['timeIn']:$arr['otIn'];
					}
						if ($arr['otCrossTag']=='Y') {
							$time['hrsWork']+= round($this->calDiff("{$arr['tsDate']} $OTIn",$this->DateAdd($arr['tsDate'])." $OTOut",'m')/60,2);
							
							
						} else {
							$time['hrsWork']+= round($this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} $OTOut",'m')/60,2);
						}
						
						
				}
				
				
				if ($arr['breakOut'] !='' && $arr['breakIn'] !='') 
				{
					$brkTime = round($this->calDiff("{$arr['tsDate']} {$arr['breakOut']}","{$arr['tsDate']} {$arr['breakIn']}",'m')/60,2);
					if ($brkTime>0.25) 
					{
						$time['hrsWork'] -= $brkTime-0.25;
					}
				}
				
				if ($arr['lunchOut'] != $arr['lunchIn'] && $arr['lunchOut'] !='' && $arr['lunchIn'] !='') 
				{
					//orig code
					//$hrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['lunchOut']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);
					$hrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['shftLunchOut']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);
					$shfthrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['shftLunchOut']}","{$arr['tsDate']} {$arr['shftLunchIn']}",'m')/60,2);
					$shfthrsLunch = ($shfthrsLunch<1 && $shfthrsLunch!=0.5) ? 1: $shfthrsLunch;
					$hrsWrk = $time['hrsWork'];
					if ($hrsLunch>$shfthrsLunch) 
					{
					
				
						if ($shfthrsLunch == 0.5) {
							$time['hrsWork'] = $hrsWrk-($hrsLunch-$shfthrsLunch);
							
						} else {
							$time['hrsWork'] = $hrsWrk-$hrsLunch;
							
						}						
						$time['hrsTardy'] += $hrsLunch-$shfthrsLunch;
					} 
					else 
					{
						if ($hrsLunch>0 && $shfthrsLunch!=0.5)
							$time['hrsWork'] = $time['hrsWork']-$shfthrsLunch;
						else
							$time['hrsWork'] = $time['hrsWork'];
					}
				} 
				else 
				{
					//test ito if OT break gawin after 5 hrs 07/25/2024 $time['hrsWork'] = ($time['hrsWork']>=5) ? $time['hrsWork']-1:$time['hrsWork'];
					$time['hrsWork'] = ($time['hrsWork']>=6) ? $time['hrsWork']-1:$time['hrsWork'];
				}	
				
				
				//if apptype is OB
				if ($arr['obTag']=='Y' && $arr['hrs8Deduct']=='Y') 
				{
					$time['hrsWork'] 	= 8;
				}

				//if ($arr['empBrnCode'] != '0001' && $arr['empDiv']==7 && !in_array($arr['dayType'],array('02','05','06'))) {
				if (in_array($arr['empDiv'],array('1','2','3','4','5','6','7','8','10')) && !in_array($arr['dayType'],array('02','05','06'))) {
					if ($this->checkEmpOT($arr['empNo'],date("Y-m-d",strtotime($arr['tsDate'])))) {
						$time['hrsOT'] = $time['hrsWork'];	
					} else {
						if ($time['hrsWork'] <= 8) {
							$time['hrsOT'] = $time['hrsWork'];	
						} else {
					
							$time['hrsOT'] = 8 - $time['hrsTardy'];
						}	
					}
				} else {
					/*if($time['hrsWork']>=5){ alejo
						$time['hrsOT'] = $time['hrsWork']-1;
					}else{$time['hrsOT'] = $time['hrsWork'];}*/
					$time['hrsOT'] = $time['hrsWork'];
				}
				
				//compute ND	
				if (((float)str_replace(":",".",$arr['timeOut'])>22 && (float)str_replace(":",".",$arr['otOut'])>22))  {
					
					$time['hrsND'] += round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} $OTOut",'m')/60,2);
					// if($arr['empNo'] == '010000073') {
					// 	echo $arr['empNo'] == '010000073' ? $arr['empNo'] . ' - ' . $arr['tsDate'] . '<br>' : '';
							
					// 	echo $arr['empNo'] == '010000073' ? $arr['timeIn'] . '<br>' : '';
					// 	echo $arr['empNo'] == '010000073' ? $arr['timeOut'] . '<br>' : '';
					// 	echo $arr['empNo'] == '010000073' ? $arr['dayType'] . '<br>' : '';
					// 	echo $arr['empNo'] == '010000073' ? 'ND: ' . $time['hrsND'] . '<br>' : '';
					// }

				}elseif ((float)str_replace(":",".",$arr['timeOut'])<22 &&(float)str_replace(":",".",$arr['otOut'])<22 && $arr['otCrossTag']=='Y')  {
					$dtTo = ((float)str_replace(":",".",$arr['timeOut'])<(float)str_replace(":",".",$arr['otOut'])) ? $this->DateAdd($arr['tsDate'])." {$arr['timeOut']}":$this->DateAdd($arr['tsDate'])."  {$arr['otOut']}";
					$time['hrsND'] += round($this->calDiff("{$arr['tsDate']} 22:00",$dtTo,'m')/60,2);
				} 

				// if((float)str_replace(":",".",$arr['timeOut'])>22 || (float)str_replace(":",".",$arr['otOut'])<=6 && $arr['timeOut']!="") {
					
				// }
				$time['hrsWork'] = 0;
				
			} else { // without OT Application
				
				//if employee is store based and store operatiom
				//if ($arr['empBrnCode'] != '0001' && $arr['empDiv']==7 && !in_array($arr['dayType'],array('02','05','06'))) {
				if (in_array($arr['empDiv'],array('1','2','3','4','5','6','7','8','10')) && !in_array($arr['dayType'],array('02','05','06'))) {	
					if ((float)str_replace(":",".",$arr['shftTimeIn'])<(float)str_replace(":",".",$arr['timeIn'])) {
					 	$time['In'] = $arr['timeIn'];
					} else {
						$time['In'] = $arr['shftTimeIn'];
					}

					if ((float)str_replace(":",".",$arr['shftTimeOut'])>(float)str_replace(":",".",$arr['timeOut']) && $arr['otCrossTag']!='Y') {
						$time['Out'] = $arr['timeOut'];
						
					} else {
						$time['Out'] = $arr['shftTimeOut'];
					}					

					$hrsWrk = round($this->calDiff("{$arr['tsDate']} {$time['In']}","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
												
					if ($arr['lunchOut'] != $arr['lunchIn']) 
					{
						//orig code
						//$hrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['lunchOut']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);
						$hrsLunch = round($this->calDiff("{$arr['tsDate']} {$arr['shftLunchOut']}","{$arr['tsDate']} {$arr['lunchIn']}",'m')/60,2);
						if ($hrsLunch>1) 
						{
							$time['hrsWork'] = $hrsWrk-($hrsLunch-1);
							$time['hrsTardy'] += $hrsLunch-1;
						} 
						else 
						{
							if ($hrsLunch>0){
								if($hrsWrk>8){
									$time['hrsWork'] =  $hrsWrk-1;
								}
								else{
									$time['hrsWork'] = $hrsWrk;
								} //alejo on workon holiday wo OT app
							}
							else
								$time['hrsWork'] = $hrsWrk;
						}
					} 
					else 
					{
						$time['hrsWork'] = ($hrsWrk>=5) ? $hrsWrk-1:$hrsWrk;
					}						

					if ($time['hrsWork'] <= 8) {
						$time['hrsOT'] = $time['hrsWork'];	
					} else {
						$time['hrsOT'] = 8;
					}
					//compute ND 
					if ((float)str_replace(":",".",$time['Out'])>22)  {
						$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} {$time['Out']}",'m')/60,2);
					}
					//if OT is cross day
					if ($arr['otIn']!='' && $arr['otOut']!='') {
						$OTOut = ((float)str_replace(":",".",$arr['otOut'])<(float)str_replace(":",".",$arr['timeOut'])) ? $arr['otOut']:$arr['timeOut'];
						$OTIn = ((float)str_replace(":",".",$arr['otIn'])<(float)str_replace(":",".",$time['Out'])) ? $time['Out']:$arr['otIn'];						
						
						if ($arr['otCrossTag']=='Y' || $arr['crossDay']=='Y') {
							$var = $this->calDiff("{$arr['tsDate']} {$arr['otIn']}",$this->DateAdd($arr['tsDate'])." {$arr['otOut']}",'m')/60;
							$OtHrs = number_format(floor($var * 100) / 100, 2, '.', '');
							if ((float)str_replace(":",".",$OTOut)>22)  {
								if ((float)str_replace(":",".",$time['Out'])>22)
									$time['hrsND'] += round($this->calDiff("{$arr['tsDate']} $OTIn",$this->DateAdd($arr['tsDate'])." $OTOut",'m')/60,2);
								else
									$time['hrsND'] += round($this->calDiff("{$arr['tsDate']} 22:00",$this->DateAdd($arr['tsDate'])." $OTOut",'m')/60,2);
								}
						} else {
							$var = $this->calDiff("{$arr['tsDate']} {$arr['otIn']}","{$arr['tsDate']} {$arr['otOut']}",'m')/60;
							$OtHrs = number_format(floor($var * 100) / 100, 2, '.', '');
							if ((float)str_replace(":",".",$OTOut)>22)  {
								if ((float)str_replace(":",".",$time['Out'])>22)
									$time['hrsND'] += round($this->calDiff("{$arr['tsDate']} $OTIn","{$arr['tsDate']} $OTOut",'m')/60,2);
								else
									$time['hrsND'] += round($this->calDiff("{$arr['tsDate']} 22:00","{$arr['tsDate']} $OTOut",'m')/60,2);
								
							} 
						}
						$time['hrsOT'] += $OtHrs;
					} 
					
					//compute ND
/*					elseif ((float)str_replace(":",".",$arr['timeOut'])<22 &&(float)str_replace(":",".",$arr['otOut'])<22 && $arr['otCrossTag']=='Y')  {
						$dtTo = ((float)str_replace(":",".",$arr['timeOut'])<(float)str_replace(":",".",$arr['otOut'])) ? $this->DateAdd($arr['tsDate'])." {$arr['timeOut']}":$this->DateAdd($arr['tsDate'])."  {$arr['otOut']}";
						$time['hrsND'] = round($this->calDiff("{$arr['tsDate']} 22:00",$dtTo,'m')/60,2);
					}
*/

					$time['hrsWork'] = 0;

				} else {
					$time['hrsWork']=0;
				}
			}
		}
		if ((float)$time['hrsUT']>0) {
			$this->AddViolation($arr['empNo'],$arr['tsDate'],'EQT');
		}
		if ((float)$time['hrsTardy']>0) {
			$this->AddViolation($arr['empNo'],$arr['tsDate'],'TDA');
		}
		if ($arr['editReason'] != "")
			$this->AddViolation($arr['empNo'],$arr['tsDate'],$arr['editReason']);		

		return $time;
	}
	function IsLeaveAppType($AppType) {
		$res = false;
		foreach($this->arrLeaveAppTypes as $valType) {
			if ($AppType==$valType['tsAppTypeCd']) {
				$res = true;
			}
		}
		return $res;
	}
	
	function getSchedHrsWork($arr) {
		if ((float)str_replace(":",".",$arr['shftTimeOut'])==0) {
			if($arr['crossDay']=="Y"){
				//$tIn = "{$arr['tsDate']} {$arr['shftTimeIn']}";
				$tIn =(float)str_replace(":",".",$arr['shftTimeIn']);
				if ($arr['empBrnCode']!=='0001') {
					$tOut = $this->DateAdd($arr['tsDate']) . " {$arr['shftLunchOut']}";
					//$hrsWork = round($this->calDiff($tIn,$tOut,'m')/60,2);
					$cmpthrs=(float)(24-(float)$tIn);
					$hrsWork =(float) $cmpthrs-1;
					
				}else{
					$cmpthrs=(float)(24-(float)$tIn);
					$hrsWork =(float) $cmpthrs-1;
					//$tOut = $this->DateAdd($arr['tsDate']) . " {$arr['shftTimeOut']}";
					
					//$hrsWork = round($this->calDiff($tIn,$tOut,'m')/60,2)-1;
				}
			}
			else{
				//$hrsWork = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftLunchOut']}",'m')/60,2);
				$hrsWork = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftLunchOut']}",'m')/60,2);
			}
		} else {
			if($arr['crossDay']=="Y"){
			//	if ((float)str_replace(":",".",$arr['shftTimeIn'])==0){
				//	$tIn = "{$arr['tsDate']} {$arr['timeIn']}";
				//}else{
					$tIn = "{$arr['tsDate']} {$arr['shftTimeIn']}";
					//}
				
				$tOut = $this->DateAdd($arr['tsDate']) . " {$arr['shftTimeOut']}";//OLD CODE
				//$tOut = "{$arr['tsDate']}  {$arr['shftTimeOut']}";//EDITED BY ALEJO
				if ($arr['otCrossTag']!="Y"){
				//$hrsWork = round($this->calDiff($tIn,$tOut,'m')/60,2)-1;
				$hrsWork =round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}",$tOut,'m')/60,2)-1;
				}else{
				$hrsWork =round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2)-1;}
			}
			else{

				//$hrsWork = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2)-1;
				//alejocode && $sat==6 
				$sat=date("N",strtotime($arr['tsDate']));
				if ($arr['empBrnCode'] !== 0001 && $arr['CWWTag']=='' && $sat==6 ){ 

				$hrsWork = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2);
				if ($hrsWork>=5){
						$hrsWork= $hrsWork-1;
				}else{
						$hrsWork= $hrsWork;

				}
				
				}else{
					//original code
					$hrsWork = round($this->calDiff("{$arr['tsDate']} {$arr['shftTimeIn']}","{$arr['tsDate']} {$arr['shftTimeOut']}",'m')/60,2)-1;
					//original code
				}

			}
		}

		$hrsWork = ($hrsWork==7) ? 8:$hrsWork;
		return $hrsWork;


	}
	function clearCWW() {
		$sqlCWW ="delete from tblTK_HrsWorkedRepository where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y'));";
		return $this->execMultiQryI($sqlCWW);							
	
	}
	
	function getLeaveAppTypes() {
		$sqlLeaveAppTypes = "Select tsAppTypeCd from tblTK_AppTypes where compCode='{$_SESSION['company_code']}' AND leaveTag='Y'";
		$this->arrLeaveAppTypes = $this->getArrResI($this->execQryI($sqlLeaveAppTypes));
	}
	
	function Repost() {
		$sqlClearDedAndOTAndViolations = " Delete from tblTK_Deductions where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y')); \n";
		
		$sqlClearDedAndOTAndViolations .= " Delete from tblTK_Overtime where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y'));";
		$sqlClearDedAndOTAndViolations .="Update tbltk_timesheet set satPaytag=NULL where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y'));";


		/*$sqlClearDedAndOTAndViolations .= " Delete from tblTK_EmpViolations where compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' 
						AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
							AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')) AND process='Posting';";*/							
		return $this->execMultiQryI($sqlClearDedAndOTAndViolations);
	}

	function ClearViolations(){
		$sqlClearViolations = "Delete from tblTK_EmpViolations WHERE compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND empNo IN ( Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode=  '{$_SESSION['company_code']}' AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y')) AND process='Posting' ";	
		return $this->execQryI($sqlClearViolations);
	}
	
	function getOTforComputation() {
		$sqlOT= "SELECT tblTK_Overtime.empNo, tblTK_Overtime.tsDate, tblTK_Overtime.dayType, tblTK_Overtime.hrsOTLe8, tblTK_Overtime.hrsOTGt8, tblTK_Overtime.hrsNDLe8, tblEmpMast.empDrate, tblEmpMast.empMrate, tblOtPrem.otPrem8, tblOtPrem.otPremOvr8, tblOtPrem.ndPrem8,hrsRegNDLe8,ndreg
				FROM tblTK_Overtime INNER JOIN tblEmpMast ON tblTK_Overtime.compCode = tblEmpMast.compCode AND tblTK_Overtime.empNo = tblEmpMast.empNo INNER JOIN tblOtPrem ON tblTK_Overtime.dayType = tblOtPrem.dayType
				Where tblTK_Overtime.compCode = '{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
						AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' 
					AND compCode='{$_SESSION['company_code']}' AND postTag='Y') AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' ";	
		return $this->getArrResI($this->execQryI($sqlOT));		
	}

	function getDedforComputation() {
		$sqlDed= "SELECT tblEmpMast.empNo, tblEmpMast.empMrate, tblTK_Deductions.empNo, tblTK_Deductions.tsDate, tblTK_Deductions.minTardy, tblTK_Deductions.minUT, 
                      tblTK_Deductions.amtTardy, tblTK_Deductions.amtUT
				FROM tblEmpMast INNER JOIN tblTK_Deductions ON tblEmpMast.compCode = tblTK_Deductions.compCode AND tblEmpMast.empNo = tblTK_Deductions.empNo
				Where tblEmpMast.compCode = '{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' 
						AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' 
					AND compCode='{$_SESSION['company_code']}' AND postTag='Y') AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' ";	
		return $this->getArrResI($this->execQryI($sqlDed));		
	}
	
	function getEmpTimesheet($empNo,$tsDate,$dayType) {
	
		$res = "";
		// $sqlTScur = "SELECT empNo, hrsWorked, tsDate , tsAppTypeCd, attendType,dayType,otIn,otOut,otTag,satPayTag FROM tblTK_Timesheet WHERE (tsDate < '$tsDate') AND compCode='{$_SESSION['company_code']}' and empNo='$empNo' and dayType not in('02') order by tsDate desc ";

	$sqlTScur = "	SELECT
tbltk_timesheet.empNo,
tbltk_timesheet.hrsWorked,
tbltk_timesheet.tsDate,
tbltk_timesheet.tsAppTypeCd,
tbltk_timesheet.attendType,
tbltk_timesheet.dayType,
tbltk_timesheet.otIn,
tbltk_timesheet.otOut,
tbltk_timesheet.otTag,
tbltk_timesheet.satPayTag,
tbltk_empshift.CWWTag,
tbltk_timesheet.brnchCd
FROM
tbltk_timesheet
INNER JOIN tbltk_empshift ON tbltk_timesheet.empNo = tbltk_empshift.empNo
WHERE (tsDate < '$tsDate') AND tbltk_timesheet.compCode='{$_SESSION['company_code']}' and tbltk_timesheet.empNo='$empNo' and tbltk_timesheet.dayType not in('02')
order by tsDate desc";
	/* && in_array($daytype, array('03','07'))	if ($empNo == '040001617' && date('Y-m-d',strtotime($tsDate)) == '3/23/2013') {
			echo "wil $sqlTScur\n";
		}*/

		$arrTSCur = $this->getArrResI($this->execQryI($sqlTScur));
		foreach($arrTSCur as $valCur) {
		$dayCode = date('N',strtotime($valCur['tsDate']));
			if ($valCur['dayType'] =="01") {
				if ($valCur['tsAppTypeCd']=='') {
					if (in_array((float)$valCur['hrsWorked'],array('','0'))) {
						$cww=$valCur['CWWTag'];
						if ($dayCode==6 && $cww=="Y"){
						
						}else{
							if ($valCur['brnchCd'] !== "0001" ){
								if ($dayCode==6 ){
						
						}else{
							if (in_array((float)$valCur['hrsWorked'],array('','0'))) {
								if ($valCur['tsDate'] == '2020-12-29 00:00:00') {

									//paid legal holiday for head office employees
									$res = 'Y';
								break;
								}else{
								$res = 'N';
								break;

								}
								

							}else{
								$res = 'Y';
								break;
							}

						}

							}
							else{
						$res = 'N';
						break;
						}	
						}
						
					} else {
						$res = "Y";
						break;
					}

				} else {

					
					if (in_array($valCur['tsAppTypeCd'],array(16,17,08,11,19,20))) {
						$res = "N";
						break;
					} else {
						$res = "Y";

						break;
					}
				}
			} elseif (in_array($valCur['dayType'],array("04","05","06"))) {
				if ($valCur['otIn'] !="" && $valCur['otOut'] !="") {
					$res = "Y";
					break;
				} 				
			} elseif ($valCur['dayType']=="05") {
				$res = "Y";
				break;
			}elseif ($valCur['dayType']=="07") {
					//if ($valCur['otTag']=="Y") {
					$res = "Y";
					break;					
				//}
			}elseif ($valHist['dayType']=="08") {
				//if ($valCur['otTag']=="Y") {
					$res = "Y";
					break;	
				//}
				} elseif ($valCur['dayType']=="03") {
				if ($valCur['otTag']=="Y") {
					$res = "Y";
				
					break;	
				}
			} elseif ($valCur['dayType']=="02") {
				if ($valCur['otIn'] !="" && $valCur['otOut'] !="") {
					$res = "Y";
					break;
				}else{
					$res = "Y";
					break;	
				}
			} else {
				if ($valCur['otIn'] !="" && $valCur['otOut'] !="") {
					$res = "Y";
					break;
				} else {
					if ($valCur['dayType']!="02") {
						$res = "N";
						break;
					}
				}
			}
		}
		if ($dayType == 5 && $res == "N") {
			$hrsWorked = 0;
			foreach($arrTSCur as $valTSCur) {
				if ($valTSCur['hrsWorked'] > 0) {
					$res = "Y";
					break;
				} 
			}
		}
		if ($res=="") {
			// $sqlTShist = "SELECT empNo, hrsWorked, tsDate, tsAppTypeCd, attendType,dayType,otIn,otOut,satPayTag FROM tblTK_Timesheethist WHERE      (tsDate <'$tsDate') AND compCode='{$_SESSION['company_code']}'  and empNo='$empNo' and dayType not in('02') order by tsDate desc limit 15";
				$sqlTShist = "	SELECT
				tblTK_Timesheethist.empNo,
				tblTK_Timesheethist.hrsWorked,
				tblTK_Timesheethist.tsDate,
				tblTK_Timesheethist.tsAppTypeCd,
				tblTK_Timesheethist.attendType,
				tblTK_Timesheethist.dayType,
				tblTK_Timesheethist.otIn,
				tblTK_Timesheethist.otOut,
				tblTK_Timesheethist.otTag,
				tblTK_Timesheethist.satPayTag,
				tbltk_empshift.CWWTag,
				tblTK_Timesheethist.brnchCd
				FROM
				tblTK_Timesheethist
				INNER JOIN tbltk_empshift ON tblTK_Timesheethist.empNo = tbltk_empshift.empNo
				WHERE (tsDate < '$tsDate') AND tblTK_Timesheethist.compCode='{$_SESSION['company_code']}' and tblTK_Timesheethist.empNo='$empNo' and tblTK_Timesheethist.dayType not in('02')
				order by tsDate desc limit 15";

			$arrTSHist = $this->getArrResI($this->execQryI($sqlTShist));
			foreach ($arrTSHist as $valHist) {
		$dayCode = date('N',strtotime($valHist['tsDate']));
			if ($valHist['dayType'] =="01") {
				if ($valHist['tsAppTypeCd']=='') {
					if (in_array((float)$valHist['hrsWorked'],array('','0'))) {
						$cww=$valHist['CWWTag'];
						if ($dayCode==6 && $cww=="Y"){
						
						}else{
									if ($valHist['brnchCd'] !== "0001" ){
								if ($dayCode==6 ){
						
						}else{
							if (in_array((float)$valHist['hrsWorked'],array('','0'))) {
								if ($valHist['tsDate'] == '2020-12-29 00:00:00') {

									//paid legal holiday for head office employees
									$res = 'Y';
								break;
								}else{
								$res = 'N';
								break;

								}
								

							}else{
								$res = 'Y';
								break;
							}

						}

							}
						$res = 'N';
						break;	
						}
						
					} else {
						$res = "Y";
						break;
					}

				} else {

					
					if (in_array($valHist['tsAppTypeCd'],array(16,17,08,11,19,20))) {
						$res = "N";
						break;
					} else {
						$res = "Y";

						break;
					}
				}
			} elseif (in_array($valHist['dayType'],array("04","05","06"))) {
				if ($valHist['otIn'] !="" && $valHist['otOut'] !="") {
					$res = "Y";
					break;
				} 	

				if ($valHist['tsDate'] == '2020-12-31 00:00:00') {
									//paid legal holiday for head office employees
									$res = 'Y';
								break;
					}			
			} elseif ($valHist['dayType']=="05") {
				$res = "Y";
				break;
			}elseif ($valHist['dayType']=="07") {
					//if ($valCur['otTag']=="Y") {
					$res = "Y";
					break;					
				//}
			}elseif ($valHist['dayType']=="08") {
				//if ($valCur['otTag']=="Y") {
					$res = "Y";
					break;	
				//}
				} elseif ($valHist['dayType']=="03") {
				if ($valHist['otTag']=="Y") {
					$res = "Y";
				
					break;	
				}
			} elseif ($valHist['dayType']=="02") {
				if ($valHist['otIn'] !="" && $valHist['otOut'] !="") {
					$res = "Y";
					break;
				}else{
					$res = "Y";
					break;	
				}
			} else {
				if ($valHist['otIn'] !="" && $valHist['otOut'] !="") {
					$res = "Y";
					break;
				} else {
					if ($valHist['dayType']!="02") {
						$res = "N";
						break;
					}
				}
			}
			}
			if ($dayType == 5 && $res == "N") {
				$hrsWorked = 0;
				foreach($arrTSHist as $valTSHist) {
					if ($valTSHist['hrsWorked'] > 0) {
						$res = "Y";
						break;
					} 
				}
			}
		}
	
		return $res;
		
	}
	
	
	function checkErrorTag() {
		$sqlError = "Select count(empNo) as ctr from tblTK_Timesheet  WHERE tblTK_Timesheet.compCode='{$_SESSION['company_code']}' AND tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND checkTag='Y' AND empNo IN (Select empNo from tblEmpMast where compCode='{$_SESSION['company_code']}' AND empPayGrp='{$this->Group}' AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y'))";
		return $this->getSqlAssocI($this->execQryI($sqlError));
	}
	
	function HalfDayMatrix() {
		$sql = "Select * from tblTK_HalfDayMatrix";
		$this->arrHDMatrix =  $this->getArrResI($this->execQryI($sql));
	}

	function getHalfDaySched($arr) {
		$arrSched = array();
		foreach($this->arrHDMatrix as $val) {
			if ((float)str_replace(":",".",$val['ShiftIN']) == (float)str_replace(":",".",$arr['shftTimeIn']) && (float)str_replace(":",".",$val['ShiftOUT']) == (float)str_replace(":",".",$arr['shftTimeOut'])) {
				if (in_array($arr['tsAppTypeCd'],array(12,14))) {
					$arrSched['In'] = $val['PMIN'];
					$arrSched['Out'] = $val['PMOUT'];
				}else {
					$arrSched['In'] = $val['AMIN'];
					$arrSched['Out'] = $val['AMOUT'];
				}
			}
		}
		return $arrSched;
	}

	function AddViolation($empNo,$tsDate,$violationCode) {
		$proc = "Posting";
		$sqlAddViolation = "Insert into tblTK_EmpViolations ( compCode, empNo, violationCd, tsDate, dateAdded,process) values ('{$_SESSION['company_code']}','$empNo','$violationCode','$tsDate','".date('m-d-Y')."','$proc');";
		$this->execQryI($sqlAddViolation);	
	}	

	function getOTList() {
		$sqlOT = "SELECT otDate, empNo, otIn, otOut,crossTag FROM tblTK_OTApp 
						where compCode='{$_SESSION['company_code']}' AND otStat='A' AND otDate BETWEEN '{$this->pdFrom}' AND '{$this->pdTo}'
							AND empNo IN (Select empNo from tblEmpMast where empPayGrp='{$this->Group}' AND compCode='{$_SESSION['company_code']}' 
											AND empBrnCode IN (Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y'))						
						order by tblTK_OTApp.empNo,otDate";
		$this->arrOTList = $this->getArrResI($this->execQryI($sqlOT));
		// die(var_dump($this->arrOTList));
	}

	function checkEmpOT ($empNo,$tsDate) {
		$var = false;
		foreach($this->arrOTList as $val) {
			if (date("Y-m-d",strtotime($val['otDate'])) == $tsDate && $val['empNo'] == $empNo)	 {
				$var = true;
			}
		}
		return $var;
	}	

	function getempLegalHolidays() {
		$sqlEmpLegalHolidays = "Select tk.empNo,tk.tsDate,tk.dayType,hrsOtle8 as hrsOT,tk.otTag from tblTK_Timesheet tk 
										inner join tblEmpMast emp on tk.empNo=emp.empNo 
										left join tblTK_overtime ot on tk.empNo=ot.empNo and tk.tsDate=ot.tsDate
										where tk.daytype IN ('03','05','07','08') and tk.compCode='{$_SESSION['company_code']}' AND tk.tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND  empPayGrp='{$this->Group}'
												AND brnchCd IN ((Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y'))"	;
		return $this->getArrResI($this->execQryI($sqlEmpLegalHolidays));
	}
	
	function getEmployeeListWithHolidayPay() {
		$sqlEmpListWithPay = "Select empNo from tblTK_ManagersAttendance";	
		$arr= array();
		$res = $this->getArrResI($this->execQryI($sqlEmpListWithPay));
		foreach($res as $val) {
			$arr[] = $val['empNo'];
		}
		return $arr;
	}

	function updateTSsatDate(){
		//OLD CODE
		// $sqlupdateTSsatDate= "update tbltk_timesheet tk inner join view_HrsWorkedRepository hr on tk.empNo=hr.empno and tk.tsDate=hr.satDate  set satPayTag='Y' where hr.hrsWorked>0 and  tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND  tk.empNo in (select empNo from tblEmpmast where empPayGrp='{$this->Group}' AND brnchCd IN ((Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND processTag='Y' and dayCode<6)))";
		// return $this->execQryI($sqlupdateTSsatDate);

//ALEJO UPDATE
		$satqry="select tk.empNo,tk.tsDate,sum(hr.hrsWorked) as hrs from tbltk_timesheet tk inner join view_HrsWorkedRepository hr on tk.empNo=hr.empno and tk.tsDate=hr.satDate   where hr.hrsWorked>0 and  tsDate between '{$this->pdFrom}' AND '{$this->pdTo}' AND  tk.empNo in (select empNo from tblEmpmast where empPayGrp='{$this->Group}' AND brnchCd IN ((Select brnCode from tblTK_UserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}' AND postTag='Y' and dayCode<6)))		GROUP BY tk.empNo,tk.tsDate";
	
		$ressat = $this->getArrResI($this->execQryI($satqry));
		
			foreach ($ressat as $satlist) {
			if ($satlist['hrs']>=4){
			$paytagsat='Y';
			}else{
				if($satlist['tsDate']=="2021-01-02 00:00:00"){
					$paytagsat='Y';
				}else{
					$paytagsat='NULL';
				}	
			}

			$sqlupdateTSsatDate="update tblTK_Timesheet set satPaytag='$paytagsat' where empNo='{$satlist['empNo']}' and tsDate='".date('Y-m-d',strtotime($satlist['tsDate'])). "'";
			$this->execQryI($sqlupdateTSsatDate);
		}
	}

	function getEmpAllowance($compCode,$empNo){

		$query = "SELECT * FROM tblallowance 
						   WHERE compCode = '{$compCode}'
						   AND   empNo    = '".trim($empNo)."'
						   AND   allowCode=3";

		return $this->getSqlAssocI($this->execQryI($query));
	}

	function getCompany($compCode){
		$qry = "SELECT * FROM tblCompany WHERE compStat = 'A' AND compCode={$compCode}";
		return $this->getSqlAssocI($this->execQryI($qry));
	}

	function getRecCount($query)
	{
		$result = $this->execQryI($query);
		return $this->getRecCountI($result);
	}

	function getTblData($tbl, $cond)
	{
		$qryTblInfo = "Select * from " . $tbl . " where " . $cond;
		$resTblInfo = $this->execQryI($qryTblInfo);
		return $this->getSqlAssocI($resTblInfo);
	}

	function setUserBranch(){
		$this->execQryI("Update tblTK_UserBranch set postTag=Null where empNo='".$_SESSION['employee_number']."'");
		for($i=0;$i<=(int)$_GET['chCtr'];$i++) {
			if ($_GET["chkBrnCode$i"] !="") {
				$arrStr = $_GET["chkBrnCode$i"];
				$qry = "Update tblTK_UserBranch set postTag='Y' where brnCode='".$arrStr."' and empNo='".$_SESSION['employee_number']."';";
				$this->execQryI($qry);
			}
		}
	}
}

?>