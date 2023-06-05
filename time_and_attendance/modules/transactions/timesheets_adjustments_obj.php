<?
class timesheetAdjustmentsObj extends commonObj{
	var $get;
	
	var $session;
	
	function __construct($method,$sessionVars){
		$this->get = $method;
		$this->session = $sessionVars;
	}	
	
	function getDayType() {
		$qry = "SELECT * FROM tblDayType WHERE dayStat = 'A'";
		return $this->getArrRes($this->execQry($qry));
	}
	
	function getTimeSheetDayType($where){
		if($where!=''){
			$where=$where;	
		}
		else{
			$where='';	
		}
		$qry = "SELECT dayType, tsDate FROM tblTK_Timesheet $where
				Union
				SELECT dayType, tsDate FROM tblTK_Timesheethist $where
				";
		//$qry = "Select dayType from tblTK_Timesheet $where";
		return $this->getSqlAssoc($this->execQry($qry));	
	}
	
	function getOpenPeriod($where){
		if($where!=''){
			$where=$where;	
		}
		else{
			$where='';	
		}
		$qry = "SELECT compCode, pdStat, date_format(pdPayable,'%m/%d/%Y') AS pdPayable, 
					pdSeries,payGrp,payCat,pdYear,pdNumber,pdFrmDate,pdToDate 
				FROM tblPayPeriod 
				WHERE pdTSStat = 'O' 
					$where";
		return $this->getSqlAssoc($this->execQry($qry));	
	}
	
	function getPaySeries($where){
		if($where!=''){
			$where = $where;	
		}	
		else{
			$where='';	
		}
		$qry = "Select pdSeries from tblPayPeriod 
					where pdStat = 'H'
						$where";
		return $this->getSqlAssoc($this->execQry($qry));	
	}
	
	function processHrs(){
		$daytype = $this->get['hdnDayType']; 
		$empno = $this->get['txtAddEmpNo'];
		$tsdate = $this->get['txtTSDate'];
		$allowance = $this->get['cmbAllowance'];
		$advances = $this->get['cmbAdvances'];
		$empInfo = $this->getEmpInfo($empno);
		
		if($daytype=="01"){
			$premium01 = $this->getOtPremArt($daytype);
			if($this->get['txtHrsReg']!=''){
				$regPay = $this->calculateRegRate($this->get['txtHrsReg'],$empInfo['empHrate']);		
			}
			if($this->get['txtHrsOTNG8']!=''){
				$hrsOTNG8 = $this->calculateOTRate($this->get['txtHrsOTNG8'],$premium01['otPrem8'],$empInfo['empHrate']);
			}
			if($this->get['txtOTG8']!=''){
				$hrsOTG8 = $this->calculateOTRate($this->get['txtOTG8'],$premium01['otPremOvr8'],$empInfo['empHrate']);
			}
			if($this->get['txtHrsND']!=''){
				$hrsND = $this->calculateOTRate($this->get['txtHrsND'],$premium01['ndPrem8'],$empInfo['empHrate']);
			}
			if($this->get['txtHrsNDG8']!=''){
				$hrsNDG8 = $this->calculateOTRate($this->get['txtHrsNDG8'],$premium01['ndPremOvr8'],$empInfo['empHrate']);
			}
			
			if($allowance=="Y"){
				$empAllow = $this->getAllowances($empno);
				foreach($empAllow as $valAllow){
					if($valAllow['allowCode']==7){
						$ecola = (($valAllow['allowAmt']/8)*$this->get['txtHrsReg']);
					}
					if($valAllow['allowCode']==10){
						$ctpa = (($valAllow['allowAmt']/8)*$this->get['txtHrsReg']);
					}
				}
					
			}
			if($advances=="Y"){
				$empAdvances = $this->calculateAdvances($this->get['txtHrsReg'],$empno);
			}
			

			$ndTotal = $hrsND + $hrsNDG8;
			$otTotal = $hrsOTNG8 + $hrsOTG8;
			echo "$('txtBasicAmnt').value='".number_format($regPay,2)."';";
			echo "$('txtTSAmntDate').value='$tsdate';";
			echo "$('txtNDAmnt').value='".number_format($ndTotal,2)."';";	
			echo "$('txtOTAmnt').value='".number_format($otTotal,2)."';";	
			echo "$('txtECOLAAmnt').value='".number_format($ecola,2)."';";	
			echo "$('txtCTPAAmnt').value='".number_format($ctpa,2)."';";	
			echo "$('txtAdvancesAmnt').value='".number_format($empAdvances,2)."';";
			echo "$('btnSaveAmnt').disabled=false;";
			if(($regPay=="" || $regPay=="0.00") && ($ndTotal=="" || $ndTotal=="0.00") && ($otTotal=="" || $otTotal=="0.00") && ($ecola=="" || $ecola=="0.00") && ($ctpa=="" ||  $ctpa=="0.00") && ($empAdvances=="" || $empAdvances=="0.00")){
				echo "$('btnSaveAmnt').disabled=true;";
				echo "$('txtBasicAmnt').disabled=true;";
				echo "$('txtTSAmntDate').disabled=true;";
				echo "$('txtNDAmnt').disabled=true;";
				echo "$('txtOTAmnt').disabled=true;";
				echo "$('txtECOLAAmnt').disabled=true;";
				echo "$('txtCTPAAmnt').disabled=true;";
				echo "$('txtAdvancesAmnt').disabled=true;";
			}
			else{
				echo "$('btnSaveAmnt').disabled=false;";	
				echo "$('txtBasicAmnt').disabled=false; $('txtBasicAmnt').readOnly=true;";
				echo "$('txtTSAmntDate').disabled=false; $('txtTSAmntDate').readOnly=true;";
				echo "$('txtNDAmnt').disabled=false; $('txtNDAmnt').readOnly=true;";
				echo "$('txtOTAmnt').disabled=false; $('txtOTAmnt').readOnly=true;";
				echo "$('txtECOLAAmnt').disabled=false; $('txtECOLAAmnt').readOnly=true;";
				echo "$('txtCTPAAmnt').disabled=false; $('txtCTPAAmnt').readOnly=true;";
				echo "$('txtAdvancesAmnt').disabled=false; $('txtAdvancesAmnt').readOnly=true;";
				echo "$('cmbStatAmnt').disabled=false; $('cmbStatAmnt').value='O';";
			}
		}
		
		if($daytype!="01"){
			$premium02 = $this->getOtPremArt($daytype);
//			if($this->get['txtHrsReg']!=''){
//				$regPay = $this->calculateRegRate($this->get['txtHrsReg'],$empInfo['empHrate']);		
//			}
			if($this->get['txtHrsOTNG8']!=''){
				$hrsOTNG8 = $this->calculateOTRate($this->get['txtHrsOTNG8'],$premium02['otPrem8'],$empInfo['empHrate']);
			}
			if($this->get['txtOTG8']!=''){
				$hrsOTG8 = $this->calculateOTRate($this->get['txtOTG8'],$premium02['otPremOvr8'],$empInfo['empHrate']);
			}
			if($this->get['txtHrsND']!=''){
				$hrsND = $this->calculateOTRate($this->get['txtHrsND'],$premium02['ndPrem8'],$empInfo['empHrate']);
			}
			if($this->get['txtHrsNDG8']!=''){
				$hrsNDG8 = $this->calculateOTRate($this->get['txtHrsNDG8'],$premium02['ndPremOvr8'],$empInfo['empHrate']);
			}
			
			if($allowance=="Y"){
				$empAllow = $this->getAllowances($empno);
				foreach($empAllow as $valAllow){
					if($valAllow['allowCode']==7){
						$ecola = (($valAllow['allowAmt']/8)*$this->get['txtHrsReg']);
					}
					if($valAllow['allowCode']==10){
						$ctpa = (($valAllow['allowAmt']/8)*$this->get['txtHrsReg']);
					}
				}
					
			}
			if($advances=="Y"){
				$empAdvances = $this->calculateAdvances($this->get['txtHrsReg'],$empno);
			}
			

			$ndTotal = $hrsND + $hrsNDG8;
			$otTotal = $hrsOTNG8 + $hrsOTG8;
			echo "$('txtTSAmntDate').value='$tsdate';";
			echo "$('txtNDAmnt').value='".number_format($ndTotal,2)."';";	
			echo "$('txtOTAmnt').value='".number_format($otTotal,2)."';";	
			echo "$('txtECOLAAmnt').value='".number_format($ecola,2)."';";	
			echo "$('txtCTPAAmnt').value='".number_format($ctpa,2)."';";	
			echo "$('txtAdvancesAmnt').value='".number_format($empAdvances,2)."';";
			if(($ndTotal=="" || $ndTotal=="0.00") && ($otTotal=="" || $otTotal=="0.00") && ($ecola=="" || $ecola=="0.00") && ($ctpa=="" ||  $ctpa=="0.00") && ($empAdvances=="" || $empAdvances=="0.00")){
				echo "$('btnSaveAmnt').disabled=true;";
			}
			else{
				echo "$('btnSaveAmnt').disabled=false;";	
				echo "$('txtBasicAmnt').disabled=true;";
				echo "$('txtTSAmntDate').disabled=false; $('txtTSAmntDate').readOnly=true;";
				echo "$('txtNDAmnt').disabled=false; $('txtNDAmnt').readOnly=true;";
				echo "$('txtOTAmnt').disabled=false; $('txtOTAmnt').readOnly=true;";
				echo "$('txtECOLAAmnt').disabled=false; $('txtECOLAAmnt').readOnly=true;";
				echo "$('txtCTPAAmnt').disabled=false; $('txtCTPAAmnt').readOnly=true;";
				echo "$('txtAdvancesAmnt').disabled=false; $('txtAdvancesAmnt').readOnly=true;";
				echo "$('cmbStatAmnt').disabled=false; $('cmbStatAmnt').value='O';";
			}
		}
		
	}
	
	function processAdjustments(){
		$Trns = $this->beginTran();
		$insertQry = "Insert into tblTK_TimesheetAdjustment
		 			  	(compcode,empNo,tsDate,dayType,payGrp,payCat,pdYear,pdNumber,entryTag,includeAllowTag,includeAdvTag,
						hrsReg,hrsOtLe8,hrsOtGt8,hrsNd,hrsNdGt8,adjBasic,adjOt,adjNd,adjHp,adjEcola,adjCtpa,adjAdv,tsStat,
						userAdded,dateAdded)
					  Values('".$_SESSION['company_code']."','".$this->get['txtAddEmpNo']."',
					  	'".($this->get['txtTSDate']==""?$this->get['txtTSAmntDate']:$this->get['txtTSDate'])."',
					  	'".$this->get['hdnDayType']."','".$this->get['hdnPayGrp']."','".$this->get['hdnPayCat']."',
						'".$this->get['hdnPDYear']."','".$this->get['hdnPDNumber']."','".$this->get['cmbAdjustmentType']."',
						'".$this->get['cmbAllowance']."','".$this->get['cmbAdvances']."',
						'".($this->get['txtHrsReg']=="."?"":$this->get['txtHrsReg'])."',
						'".($this->get['txtHrsOTNG8']=="."?"":$this->get['txtHrsOTNG8'])."',
						'".($this->get['txtOTG8']=="."?"":$this->get['txtOTG8'])."',
						'".($this->get['txtHrsND']=="."?"":$this->get['txtHrsND'])."',
						'".($this->get['txtHrsNDG8']=="."?"":$this->get['txtHrsNDG8'])."',
						'".($this->get['txtBasicAmnt']==""?"0.00":str_replace(",","",$this->get['txtBasicAmnt']))."',
						'".($this->get['txtOTAmnt']==""?"0.00":str_replace(",","",$this->get['txtOTAmnt']))."',
						'".($this->get['txtNDAmnt']==""?"0.00":str_replace(",","",$this->get['txtNDAmnt']))."',
						'".($this->get['txtHPAmnt']==""?"0.00":str_replace(",","",$this->get['txtHPAmnt']))."',
						'".($this->get['txtECOLAAmnt']==""?"0.00":str_replace(",","",$this->get['txtECOLAAmnt']))."',
						'".($this->get['txtCTPAAmnt']==""?"0.00":str_replace(",","",$this->get['txtCTPAAmnt']))."',
						'".($this->get['txtAdvancesAmnt']==""?"0.00":str_replace(",","",$this->get['txtAdvancesAmnt']))."',
						'".$this->get['cmbStatAmnt']."','".$this->session['employee_number']."','".date("Y-m-d")."')";	
		if ($Trns) {
			$Trns = $this->execQry($insertQry);
		} 
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;	
		}
		else{
			$Trns = $this->commitTran();
			return true;	
		}
	}
	
	function updateAdjustments(){
		$Trns = $this->beginTran();
		$updateQry = "Update tblTK_TimesheetAdjustment 
					  set tsDate='".($this->get['txtTSDate']==""?$this->get['txtTSAmntDate']:$this->get['txtTSDate'])."',
					  	dayType='".$this->get['hdnDayType']."', includeAllowTag='".$this->get['cmbAllowance']."', 
						includeAdvTag='".$this->get['cmbAdvances']."', 
						hrsReg='".($this->get['txtHrsReg']=="."?"":$this->get['txtHrsReg'])."',
						hrsOtLe8='".($this->get['txtHrsOTNG8']=="."?"":$this->get['txtHrsOTNG8'])."', 
						hrsOtGt8='".($this->get['txtOTG8']=="."?"":$this->get['txtOTG8'])."',  
						hrsNd='".($this->get['txtHrsND']=="."?"":$this->get['txtHrsND'])."', 
						hrsNdGt8='".($this->get['txtHrsNDG8']=="."?"":$this->get['txtHrsNDG8'])."',
						adjBasic='".($this->get['txtBasicAmnt']==""?"0.00":str_replace(",","",$this->get['txtBasicAmnt']))."', 
						adjOt='".($this->get['txtOTAmnt']==""?"0.00":str_replace(",","",$this->get['txtOTAmnt']))."',
						adjNd='".($this->get['txtNDAmnt']==""?"0.00":str_replace(",","",$this->get['txtNDAmnt']))."',
						adjHp='".($this->get['txtHPAmnt']==""?"0.00":str_replace(",","",$this->get['txtHPAmnt']))."',
						adjEcola='".($this->get['txtECOLAAmnt']==""?"0.00":str_replace(",","",$this->get['txtECOLAAmnt']))."',
						adjCtpa='".($this->get['txtCTPAAmnt']==""?"0.00":str_replace(",","",$this->get['txtCTPAAmnt']))."',
						adjAdv='".($this->get['txtAdvancesAmnt']==""?"0.00":str_replace(",","",$this->get['txtAdvancesAmnt']))."',
						tsStat='".$this->get['cmbStatAmnt']."', userUpdated='".$this->session['employee_number']."',
						dateUpdated='".date("Y-m-d")."', entryTag='".$this->get['cmbAdjustmentType']."'
					  where seqNo='".$this->get['hdnSeqNo']."'";	
		if($Trns){
			$Trns = $this->execQry($updateQry);	
		}
		if(!$Trns){
			$Trns = $this->rollbackTran();
			return false;	
		}
		else{
			$Trns = $this->commitTran();
			return true;
		}
	}
	
	function calculateRegRate($hrs,$drate){
		$adjustedRegRate = $hrs * $drate;
		return $adjustedRegRate;	
	}
	
	function calculateOTRate($hrs,$premium,$drate){
		$hrsRate = $hrs * $premium;
		$adjustedOTRate = (float)$hrsRate * (float)$drate;
		return $adjustedOTRate;
	}
	
	function calculateAdvances($hrs,$empno){
		$qry = "Select allowAmt from tblAllowance where allowStat='A' and empNo='{$empno}' and allowCode='2'";
		$resQry = $this->getSqlAssoc($this->execQry($qry));
		$allowAmnt = (($resQry['allowAmt']/26)/8);
		return $empAllowance = $allowAmnt * $hrs;
	}
	
	function getAllowances($empno){
		$qry = "Select allowAmt,allowCode from tblAllowance where allowStat='A' and empNo='{$empno}' and allowCode in ('7','10')";
		$resQry = $this->getArrRes($this->execQry($qry));
		return $resQry;		
	}
		
}
?>