
<?php
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created	: 	04/07/2011
		Description		;	Object for the List of Reminders for User
	*/

	class remObj extends commonObj 
	{
	
		function listReminders()
		{
			$qryListReminders = "Select * from tblReminder where compCode='".$_SESSION["company_code"]."' and status='A'";
			$resListReminders = $this->execQry($qryListReminders);
			return $arrListReminders = $this->getArrRes($resListReminders);
		}
		
		function otherQryRem()
		{
			//Get List of Employees that should be tagged as Minimum Wage
			$payGrp = $this->getProcGrp();
			if($payGrp!="")
				$where = " and empPayGrp='".$payGrp."'";
			else
				$where = "";
				
			$qryList = "SELECT     count(*) as Cnt
						FROM       tblEmpMast empMast INNER JOIN
									tblBranch brnch ON empMast.empBrnCode = brnch.brnCode AND empMast.empDrate <= brnch.minWage
						WHERE     (empMast.compCode = '".$_SESSION["company_code"]."') AND (brnch.compCode = '".$_SESSION["company_code"]."') 
								  AND (empMast.empWageTag = 'N') ".$where.";";
			$arrcntRec = $this->getSqlAssoc($this->execQry($qryList));
			
			
			//Check PAF List
			$arrPafList = array("tblPAF_Others","tblPAF_EmpStatus","tblPAF_Branch","tblPAF_Position", "tblPAF_PayrollRelated", "tblPAF_Allowance");
			foreach($arrPafList as $arrPafList_val)
			{
				$qryCountPaf = "SELECT count(*) as PafCount
								FROM tblEmpMast
								Where compCode = '".$_SESSION["company_code"]."'  ".$where." 
									   and empNo in (Select empNo from ".$arrPafList_val.");";
				$arrCountPaf = $this->getSqlAssoc($this->execQry($qryCountPaf));
				$countPAf+= $arrCountPaf["PafCount"];
			}

			//Check New Employees
			$qryNewEmp = "SELECT count(*) as EmpnewCnt FROM tblEmpMast_new 
			     			WHERE compCode = '{$_SESSION['company_code']}' 
								AND empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' and empNo='{$_SESSION['employee_number']}')
								AND (stat='H' or stat is null)
								AND empPayCat<>0  ".$where."";
			$arrcntRecNewEmp = $this->getSqlAssoc($this->execQry($qryNewEmp));
			
			//Check Employees with Prev. Employer
							
			/*
			$qryPrevEmp = "SELECT count(*) as EmpPrevCnt FROM tblEmpMast
			     			WHERE compCode = '{$_SESSION['company_code']}' 
								and empStat in ('IN') and empPrevTag<>'Y'";
			$arrcntPrevEmp = $this->getSqlAssoc($this->execQry($qryPrevEmp));
			*/

			if($where!="")
				$payGrps = ($payGrp!=2?"2":"1");
			else
				$payGrps = " 1 and 2";
			
			if($arrcntRecNewEmp["EmpnewCnt"]>0) 
				$arrReminder.= "Don't forget to Release New Employees.<br>There are <u>".$arrcntRecNewEmp["EmpnewCnt"]." New Employee(s)</u> to be  release under Group ".$payGrps."."."+";
			
			
			if(($countPAf>0) and ($_SESSION["user_level"]!=3))
				$arrReminder.= "Don't forget to release/post PAF.<br>There are <u>".$countPAf." PAF records</u> to be  release/post under Group ".$payGrp."."."+";
			
			
			
			if($arrcntRec["Cnt"]>0)
				$arrReminder.= "There are Employee(s) that need to be tagged as Minimum Wage Earner. <br> Refer to Module Personal Profile -> Employee Min. Wage Update."."+";
			
			
			//if($arrcntPrevEmp ["EmpPrevCnt"]>0)
				//$arrReminder.= "There are Employee(s) that need to be tagged with Previous Employer. <br> Kindly Check the List of Transfer Employees and at the same time Check his/her FMS Data."."+";
			

			return $arrReminder;
						
		}
	}
?>