<?
class transferredObj extends commonObj {
	var $get;
	var $session;

	function __construct($method,$sessionVars){
		$this->get = $method;
	}
	
	function getListTransEmp() {
		$payGrp = $this->getProcGrp();
		$qryTransEmp = "Select payroll_company..tblTransferredEmployees.*,tblEmpMast.empPayGrp,tblEmpMast.empBrnCode 
		from payroll_company..tblTransferredEmployees
		Inner Join tblEmpMast on payroll_company..tblTransferredEmployees.empNo=tblEmpMast.empNo 
		where payroll_company..tblTransferredEmployees.status='Q' 
		and tblEmpMast.empPayGrp<>'{$payGrp}' 
		and tblEmpMast.empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' 
		and empNo='{$_SESSION['employee_number']}')
		order by payroll_company..tblTransferredEmployees.empLastName,payroll_company..tblTransferredEmployees.empFirstName";
		return $this->getArrRes($this->execQry($qryTransEmp));
	}
	
	function getCompanies() {
		$sqlCompanies = "Select compCode,compName from payroll_company..tblCompany where compStat='A' order by compName";
		return $this->getArrRes($this->execQry($sqlCompanies));
	}
	
	function getcompBranches($compCode) {
		switch($compCode) {
			case 1:
				$sqlBranches = "Select brnCode,brnDesc from pgjr_payroll..tblBranch where brnStat='A' order by brnDesc";
			break;	
			case 2:
				$sqlBranches = "Select brnCode,brnDesc from pg_payroll..tblBranch where brnStat='A' order by brnDesc";
			break;	
			case 4:
				$sqlBranches = "Select brnCode,brnDesc from DFClark_payroll..tblBranch where brnStat='A' order by brnDesc";
			break;				
			case 5:
				$sqlBranches = "Select brnCode,brnDesc from DFSubic_payroll..tblBranch where brnStat='A' order by brnDesc";
			break;
			case 3:
				$sqlBranches = "Select brnCode,brnDesc from LUSITANO..tblBranch where brnStat='A' order by brnDesc";
			break;
			case 13:
				$sqlBranches = "Select brnCode,brnDesc from PG_SUBIC..tblBranch where brnStat='A' order by brnDesc";
			break;
			default:
				$sqlBranches = "Select brnCode,brnDesc from tblBranch where brnStat='A' order by brnDesc";
			break;
		}
		return $this->getArrRes($this->execQry($sqlBranches));
	}
	
	function getTransEmpInfo($empNo) {
		$sqlTransEmpInfo = "SELECT tblEmpMast.empNo, tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblBranch.brnDesc, tblPosition.posDesc, tblEmpMast.empPayType, tblEmpMast.empMrate, tblEmpMast.empDrate, tblEmpMast.empSssNo,tblEmpMast.empBrnCode FROM tblEmpMast INNER JOIN tblBranch ON tblEmpMast.compCode = tblBranch.compCode AND tblEmpMast.empBrnCode = tblBranch.brnCode LEFT OUTER JOIN tblPosition ON tblEmpMast.compCode = tblPosition.compCode AND tblEmpMast.empPosId = tblPosition.posCode where empNo='$empNo' and empstat <> 'User' and empNo not in (Select empNo from payroll_company..tblTransferredEmployees where Year(dateAdded)='".date('Y')."') and empStat='RG'";
		return $this->getSqlAssoc($this->execQry($sqlTransEmpInfo));
	}
	
	function AddTransEmp($arr) {
		$arrEmpInfo = $this->getTransEmpInfo($arr['txtempNo']);
//		//$arrnewBranch = $this->getEmpBranchArt($_SESSION['company_code'],$arr['cmbBranch']);
		$arrOldComp = $this->getTransCompany($_SESSION['company_code']);
		$arrNewComp = $this->getTransCompany($arr['cmbCompany']);		
		$arrnewBranch = $this->getNewBranch($arrNewComp['db'],$arr['cmbBranch']);
		$newBranchCode = $arrnewBranch['brnCode'];
		$newBranch = $arrnewBranch['brnDesc'];
		$newBranchGroup = $arrnewBranch['brnDefGrp'];
		$newLocCode = $newBranchCode;
		$new_CompCode = $arr['cmbCompany'];
		
		if ($arrNewComp['db'] != "") {
				$Trns = $this->beginTran();
				$sqlAddTransEmp = "Insert into payroll_company..tblTransferredEmployees (
						empNo, empFirstName, 
						empLastName, empMidName, 
						company_old, company_new, 
						branch_old, branch_new, 
						position_old, salary_old, 
						sssNo, dateAdded, 
						status, old_compCode, 
						old_brnCode, empPayType_old,
						new_compCode,new_brnCode
						) values 
						(
						'{$arrEmpInfo['empNo']}', '{$arrEmpInfo['empFirstName']}',
						'{$arrEmpInfo['empLastName']}', '{$arrEmpInfo['empMidName']}',
						'{$arrOldComp['compName']}', 
						'{$arrNewComp['compName']}',
						'{$arrEmpInfo['brnDesc']}', '$newBranch',
						'{$arrEmpInfo['posDesc']}', '{$arrEmpInfo['empMrate']}',
						'{$arrEmpInfo['empSssNo']}', '".date('m/d/Y')."',
						'Q', '".$_SESSION['company_code']."',
						'{$arrEmpInfo['empBrnCode']}', '{$arrEmpInfo['empPayType']}',
						'$new_CompCode','$newBranchCode'
						);\n
						";
				if ($Trns) {
					  $Trns = $this->execQry($sqlAddTransEmp);
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
		else {
			return false;	
		}				 			
	}
					
	function releaseTrans(){
		$Trns = $this->beginTran();
		$sqlTransEmp="";
		for($i=0; $i<(int)$this->get['chCtr']; $i++){
			if($this->get["chTran$i"]!=""){
			  	$empVal = $this->getEmpData($this->get["chTran$i"]);
				  $arrOldComp = $this->getTransCompany($empVal['old_compCode']);
				  $arrNewComp = $this->getTransCompany($empVal['new_compCode']);		
				  $resChecks = $this->checkEmployeeExist($empVal['empNo'],$arrNewComp['db'],"tblEmpMast_New");
				  $arrnewBranch = $this->getNewBranch($arrNewComp['db'],$empVal['new_brnCode']);
				  $newBranchCode = $arrnewBranch['brnCode'];
				  $newBranch = $arrnewBranch['brnDesc'];
				  $newBranchGroup = $arrnewBranch['brnDefGrp'];
				  $newLocCode = $newBranchCode;
				  $new_CompCode = $empVal['new_compCode'];
				  
				  if($resChecks){
					  //get data of employee
					  $resData = $this->checkEmployeeExist($empVal['empNo'],"","tblEmpMast");					  
	  
					 //Update EmpMast_new
					 $sqlEmpmast .= "Update ".$arrNewComp['db']."tblEmpMast_new set empLastName='".$resData['empLastName']."',
								  empFirstName='".$resData['empFirstName']."',empMidName='".$resData['empMidName']."',
								  empLocCode='".$newLocCode."',empBrnCode='".$newBranchCode."',
								  empDiv='".$resData['empDiv']."',empDepCode='".$resData['empDepCode']."',
								  empSecCode='".$resData['empSecCode']."',empPosId='".$resData['empPosId']."',
								  dateHired='".$resData['dateHired']."',empStat='RG',
								  empRestDay='".$resData['empRestDay']."',empTeu='".$resData['empTeu']."',
								  empTin='".$resData['empTin']."',empSssNo='".$resData['empSssNo']."',
								  empPagibig='".$resData['empPagibig']."',empBankCd='".$resData['empBankCd']."',
								  empAcctNo='".$resData['empAcctNo']."',empPayGrp='".$newBranchGroup."',
								  empPayType='".$resData['empPayType']."',empPayCat='".$resData['empPayCat']."',
								  empWageTag='".$resData['empWageTag']."',empPrevTag='Y',
								  empAddr1='".$resData['empAddr1']."',empAddr2='".$resData['empAddr2']."',
								  empAddr3='".$resData['empAddr3']."',empMarStat='".$resData['empMarStat']."',
								  empSex='".$resData['empSex']."',empBday='".$resData['empBday']."',
								  empReligion='".$resData['empReligion']."',empMrate='".$resData['empMrate']."',
								  empDrate='".$resData['empDrate']."',empHrate='".$resData['empHrate']."',
								  empOtherInfo='".$resData['empOtherInfo']."',empNickName='".$resData['empNickName']."',
								  empBplace='".$resData['empBplace']."',
								  empWeight='".$resData['empWeight']."',empCitizenCd='".$resData['empCitizenCd']."',
								  empBloodType='".$resData['empBloodType']."',empEndDate='".$resData['empEndDate']."',
								  empLevel='".$resData['empLevel']."',empSubSection='".$resData['empSubSection']."',
								  empCityCd='".$resData['empCityCd']."',empSpouseName='".$resData['empSpouseName']."',
								  empBuildDesc='".$resData['empBuildDesc']."',empComplexDesc='".$resData['empComplexDesc']."',
								  empEyeColorDesc='".$resData['empEyeColorDesc']."',empHairDesc='".$resData['empHairDesc']."',
								  empPhicNo='".$resData['empPhicNo']."',empAbsencesTag='".$resData['empAbsencesTag']."',
								  empLatesTag='".$resData['empLatesTag']."',empUtTag='".$resData['empUtTag']."',
								  empOtTag='".$resData['empOtTag']."',id='".$resData['id']."',empdateadded='".$resData['empdateadded']."',
								  empProvinceCd='".$resData['empProvinceCd']."',empECPerson='".$resData['empECPerson']."',
								  empECNumber='".$resData['empECNumber']."',empMunicipalityCd='".$resData['empMunicipalityCd']."',
								  stat='H',employmentTag='".$resData['employmentTag']."',
								  empRank='".$resData['empRank']."'  where empNo='".$resData['empNo']."' ;\n";	
								  				
									$sqlUpdateEmpMast .= "Update tblEmpMast set empStat='IN' where empNo='{$empVal['empNo']}'; \n";	
								
									//update transferredEmpMastNew
									$sqlUpdateEmpMastNew .= "Update tblEmpMast_new set empStat='IN' where empNo='{$empVal['empNo']}'; \n";	
									
//									$sqlTransEmp .= "Update PAYROLL_COMPANY..tblTransferredEmployees set status='T' where empNo='{$empVal['empNo']}' and status='Q' and userTransferred='{$_SESSION['user_id']}' and dateTransferred='".date('m/d/Y')."'; \n";	
									$sqlTransEmp .= "Update PAYROLL_COMPANY..tblTransferredEmployees set status='T'
									,userTransferred='{$_SESSION['user_id']}',dateTransferred='".date('m/d/Y')."' 
									where empNo='{$empVal['empNo']}' and status='Q'; \n";	

									
									if($this->deleteExistingRecord($empVal['empNo'],$arrNewComp['db'])){
										$this->transferOtherInfo($empVal['empNo'],$arrNewComp['db'],$new_CompCode);		
										$this->setLastPayEmp($empVal['empNo'],$arrNewComp['db']);
									}		 
				  }
				  else{
					  //$empID = $this->getEmpID($arrNewComp['db'],$new_CompCode);
					  $empID = $this->getEmpID($arrNewComp['db'],'1');
				  //Transfer EmpMast
				$sqlEmpmast .= "Insert Into ".$arrNewComp['db']."tblEmpMast_new (
								compCode, empNo,
								empLastName, empFirstName,
								empMidName, empLocCode,
								empBrnCode, empDiv,
								empDepCode, empSecCode,
								empPosId, dateHired,
								empStat, empRestDay,
								empTeu, empTin,
								empSssNo, empPagibig,
								empBankCd, empAcctNo,
								empPayGrp, empPayType,
								empPayCat, empWageTag,
								empPrevTag, empAddr1,
								empAddr2, empAddr3,
								empMarStat, empSex,
								empBday, empReligion,
								empMrate, empDrate,
								empHrate, empOtherInfo,
								empNickName, empBplace,
								empHeight, empWeight,
								empCitizenCd, empBloodType,
								empEndDate, empLevel,
								empSubSection, empCityCd,
								empSpouseName, empBuildDesc,
								empComplexDesc, empEyeColorDesc,
								empHairDesc, empPhicNo,
								empAbsencesTag, empLatesTag,
								empUtTag, empOtTag,id,empdateadded,
								empProvinceCd, empECPerson,
								empECNumber, empMunicipalityCd,
								employmentTag, empRank
						  )
							SELECT
								$new_CompCode as compCode, empNo,
								empLastName, empFirstName,
								empMidName, $newLocCode,
								$newBranchCode, empDiv,
								empDepCode, empSecCode,
								empPosId, dateHired,
								'RG', empRestDay,
								empTeu, empTin,
								empSssNo, empPagibig,
								empBankCd, empAcctNo,
								$newBranchGroup, empPayType,
								empPayCat, empWageTag,
								'Y', empAddr1,
								empAddr2, empAddr3,
								empMarStat, empSex,
								empBday, empReligion,
								empMrate, empDrate,
								empHrate, empOtherInfo,
								empNickName, empBplace,
								empHeight, empWeight,
								empCitizenCd, empBloodType,
								empEndDate, empLevel,
								empSubSection, empCityCd,
								empSpouseName, empBuildDesc,
								empComplexDesc, empEyeColorDesc,
								empHairDesc, empPhicNo,
								empAbsencesTag, empLatesTag,
								empUtTag, empOtTag,$empID,'".date('m/d/Y')."',
								empProvinceCd, empECPerson,
								empECNumber, empMunicipalityCd,
								employmentTag, empRank   							  
							 FROM tblEmpMast where empNo = '{$empVal['empNo']}'; \n";
							 
						$sqlUpdateEmpMast .= "Update tblEmpMast set empStat='IN' where empNo='{$empVal['empNo']}'; \n";	
					
						//update transferredEmpMastNew
						$sqlUpdateEmpMastNew .= "Update tblEmpMast_new set empStat='IN' where empNo='{$empVal['empNo']}'; \n";	
						
						$sqlTransEmp .= "Update PAYROLL_COMPANY..tblTransferredEmployees set status='T'
						,userTransferred='{$_SESSION['user_id']}',dateTransferred='".date('m/d/Y')."' 
						where empNo='{$empVal['empNo']}' and status='Q'; \n";	
						
						if($this->deleteExistingRecord($empVal['empNo'],$arrNewComp['db'])){
							$this->transferOtherInfo($empVal['empNo'],$arrNewComp['db'],$new_CompCode);		
							$this->setLastPayEmp($empVal['empNo'],$arrNewComp['db']);
						}		 
				  }	
			}											
		}
		
		if ($sqlTransEmp!="") {
			$Trns = $this->execQry($sqlTransEmp);
		}		
		
		if ($sqlUpdateEmpMastNew!="") {
			$Trns = $this->execQry($sqlUpdateEmpMastNew);
		}
		
		if ($sqlUpdateEmpMast!="") {
			$Trns = $this->execQry($sqlUpdateEmpMast);
		}
				
		if ($sqlEmpmast!="") {
			$Trns = $this->execQry($sqlEmpmast);
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
		
////				   }
////		
////				//Temp Prev Earnings Table from YTD Datahist
////				$sqlPrevearningYTD = "
////							   Insert into payroll_company..tblPrevEmployer (
////								compCode, empNo,
////								prevEmplr, empAddr1,
////								empAddr2, empAddr3,
////								emplrTin, prevEarnings,
////								prevTaxes, yearCd,
////								grossNonTax, prevBasic,
////								prevAdvances, dateAdded,
////								nonTaxSss
////							   )
////							   SELECT
////								tblYtdDataHist.compCode, tblYtdDataHist.empNo,
////								tblCompany.compName, tblCompany.compAddr1, 
////								tblCompany.compAddr2, tblCompany.compAddr3, 
////								tblCompany.compTin, tblYtdDataHist.YtdTaxable,
////								tblYtdDataHist.YtdTax, tblYtdDataHist.pdYear,
////								YtdGross-YtdTaxable, YtdBasic,
////								sprtAdvance, '".date('m/d/Y')."',
////								YtdGovDed
////							   FROM tblYtdDataHist INNER JOIN
////								tblCompany ON tblYtdDataHist.compCode = tblCompany.compCode
////							   WHERE (tblYtdDataHist.empNo = '{$arrEmpInfo['empNo']}')		 
////					 ";
////					 if ($Trns) {
////							$Trns =  $this->execQry($sqlPrevearningYTD);
////					 }
////					 
////					 //Temp Prev Earnings Table from PrevEmployer table
////					 $sqlPrevearningPrevEmployer = "
////						 Insert into payroll_company..tblPrevEmployer (
////							compCode, empNo,
////							prevEmplr, empAddr1,
////							empAddr2, empAddr3,
////							emplrTin, prevEarnings,
////							prevTaxes, yearCd,
////							grossNonTax, prevBasic,
////							prevAdvances, dateAdded,
////							nonTaxSss, tax13th
////						 )				 
////						Select 
////							compCode, empNo,
////							prevEmplr, empAddr1,
////							empAddr2, empAddr3,
////							emplrTin, prevEarnings,
////							prevTaxes, yearCd,
////							grossNonTax, prevBasic,
////							prevAdvances, dateAdded,
////							nonTaxSss, nonTax13th
////						From tblPrevEmployer where empNo = '{$arrEmpInfo['empNo']}' and yearCd = '".date('Y')."'
////					 ";
////					 if ($Trns) {
////							$Trns =  $this->execQry($sqlPrevearningPrevEmployer);
////					 }				 
////					 
////					 
////					//LoansHdr
////					 $sqlLoansHdr = "Insert Into payroll_company..tblEmpLoans (
////									compCode, empNo,
////									lonTypeCd, lonRefNo,
////									lonAmt, lonWidInterst,
////									lonGranted, lonStart,
////									lonEnd, lonSked,
////									lonNoPaymnts, lonDedAmt1,
////									lonDedAmt2, lonPayments, 
////									lonPaymentNo, lonCurbal,
////									lonLastPay,
////									mmsNo, InvoiceNo		 
////								)
////								SELECT
////									$new_CompCode as compCode, empNo,
////									lonTypeCd, lonRefNo,
////									lonAmt, lonWidInterst,
////									lonGranted, lonStart,
////									lonEnd, lonSked,
////									lonNoPaymnts, lonDedAmt1,
////									lonDedAmt2, lonPayments, 
////									lonPaymentNo, lonCurbal,
////									lonLastPay,
////									mmsNo, InvoiceNo
////								 FROM tblEmpLoans where lonStat='O' and empNo='{$arrEmpInfo['empNo']}'			 
////					 ";
////					 if ($Trns) {
////							$Trns = $this->execQry($sqlLoansHdr);
////					 }	
////					 
////					//LoanDtls	  		
////					$sqlLoanDtls = "
////					Insert into payroll_company..tblEmpLoansDtlHist (
////								compCode, empNo,
////								lonTypeCd, lonRefNo,
////								trnAmountD, ActualAmt				
////					)
////							SELECT tblEmpLoans.compCode, tblEmpLoansDtlHist.empNo,
////								tblEmpLoansDtlHist.lonTypeCd, tblEmpLoansDtlHist.lonRefNo,
////								SUM(tblEmpLoansDtlHist.trnAmountD) AS trnAmountD,
////								SUM(tblEmpLoansDtlHist.ActualAmt) AS ActualAmt
////							FROM tblEmpLoans INNER JOIN tblEmpLoansDtlHist 
////							ON tblEmpLoans.compCode = tblEmpLoansDtlHist.compCode 
////							AND tblEmpLoans.empNo = tblEmpLoansDtlHist.empNo 
////							AND tblEmpLoans.lonTypeCd = tblEmpLoansDtlHist.lonTypeCd
////							AND tblEmpLoans.lonRefNo = tblEmpLoansDtlHist.lonRefNo
////							WHERE (tblEmpLoans.lonStat = 'O') and tblEmpLoans.empNo='{$arrEmpInfo['empNo']}' group by tblEmpLoans.compCode, tblEmpLoansDtlHist.empNo,
////								tblEmpLoansDtlHist.lonTypeCd, tblEmpLoansDtlHist.lonRefNo
////					";
////					if ($Trns) {
////						  $Trns = $this->execQry($sqlLoanDtls);
////					}
////					
	
//	function transferEmployee(){
//		$Trns = $this->beginTran();								
//			//update transferred EmpMast
//			$sqlUpdateEmpMast = "Update tblEmpMast set empStat='IN' where empNo='{$arrEmpInfo['empNo']}'";	
//			if ($Trns) {
//				$Trns = $this->execQry($sqlUpdateEmpMast);
//			}
//		
//			//update transferredEmpMastNew
//			$sqlUpdateEmpMastNew = "Update tblEmpMast_new set empStat='IN' where empNo='{$arrEmpInfo['empNo']}'";	
//			if ($Trns) {
//				$Trns = $this->execQry($sqlUpdateEmpMastNew);
//			}
//
//			if(!$Trns){
//				$Trns = $this->rollbackTran();
//				return false;
//			}
//			else{
//				$Trns = $this->commitTran();							
//			}
//			return true;
//	}
	
	
	function transferOtherInfo($empno,$todb,$tocomp){
		$Trns = $this->beginTran();
		//transfer previous employeer
		$sqlPreviousEmployeer = "Insert into ".$todb."tblPrevEmployer (compCode, empNo, prevEmplr, empAddr1, empAddr2, empAddr3, emplrTin, 
					 prevEarnings, prevTaxes, prevStat, grossNonTax, nonTax13th, empBasic_Curr, empBasic_Prev, empTypeTag, nonTaxSss, tax13th, 
					 yearCd, taxPerMonth, taxDeducted, userAdded, dateAdded, prevBasic, prevAdvances, prevBasicRE, prevAdvancesRE) 
					 SELECT $tocomp, empNo, prevEmplr, empAddr1, empAddr2, empAddr3, emplrTin, prevEarnings, prevTaxes, prevStat, grossNonTax,
					 nonTax13th, empBasic_Curr, empBasic_Prev, empTypeTag, nonTaxSss, tax13th, yearCd, taxPerMonth, taxDeducted, userAdded, 
					 dateAdded, prevBasic, prevAdvances, prevBasicRE, prevAdvancesRE
					 FROM tblPrevEmployer
					 WHERE empNo='".$empno."' ;\n";
		if($sqlPreviousEmployeer!=""){
			$Trns = $this->execQry($sqlPreviousEmployeer);		
		}	
			
		//transfer ytd data history
		$sqlYtdDataHist = "Insert into ".$todb."tblPrevEmployer (compCode, empNo, prevEmplr, empAddr1, empAddr2, empAddr3, emplrTin,
					 prevEarnings, prevTaxes, yearCd, grossNonTax, prevBasic, prevAdvances, dateAdded, nonTaxSss) 
					 SELECT $tocomp, tblYtdDataHist.empNo, tblCompany.compName, tblCompany.compAddr1, tblCompany.compAddr2,
					 tblCompany.compAddr3, tblCompany.compTin, tblYtdDataHist.YtdTaxable, tblYtdDataHist.YtdTax, tblYtdDataHist.pdYear, 
					 YtdGross-YtdTaxable, YtdBasic, sprtAdvance, '".date('m/d/Y')."', YtdGovDed 
					 FROM tblYtdDataHist 
					 INNER JOIN tblCompany ON tblYtdDataHist.compCode = tblCompany.compCode
					 WHERE (tblYtdDataHist.empNo = '".$empno."') ;\n";
		if($sqlYtdDataHist!=""){
			$Trns = $this->execQry($sqlYtdDataHist);
		}					   
		
		//transfer allowance
		$sqlAllowance = "Insert into ".$todb."tblAllowance_New (compCode, empNo, allowCode, allowAmt, allowSked, allowTaxTag, 
					allowPayTag, allowStart, allowEnd, allowStat, sprtPS, allowTag, dateAdded, userAdded) 
					SELECT $tocomp, empNo, allowCode, allowAmt, allowSked, allowTaxTag, allowPayTag, allowStart, allowEnd,
					allowStat, sprtPS, allowTag, ".date('m/d/Y').", ".$_SESSION['user_id']." 
					FROM tblAllowance 
					WHERE empNo='".$empno."' ;\n";
		if($sqlAllowance!=""){
			$Trns = $this->execQry($sqlAllowance);	
		}			
		
		//transfer employee loan
		$sqlEmpLoans ="Insert into ".$todb."tblEmpLoans (compCode, empNo, lonTypeCd, lonRefNo, lonAmt, lonWidInterst, lonGranted, 
					lonStart, lonEnd, lonSked, lonNoPaymnts, lonDedAmt1, lonDedAmt2, lonPayments, lonPaymentNo, lonCurbal, lonLastPay, 
					mmsNo, InvoiceNo, lonStat) 
					SELECT $tocomp, empNo, lonTypeCd, lonRefNo, lonAmt, lonWidInterst, lonGranted, lonStart, lonEnd, lonSked, 
					lonNoPaymnts, lonDedAmt1, lonDedAmt2, lonPayments, lonPaymentNo, lonCurbal, lonLastPay, mmsNo, InvoiceNo, lonStat 
					FROM tblEmpLoans 
					WHERE lonStat='O' and empNo='".$empno."' ;\n";	
		if($sqlEmpLoans!=""){
			$Trns = $this->execQry($sqlEmpLoans);	
		}			
		
		//transfer loan details
		$sqlEmpLoanDetails = "Insert into ".$todb."tblEmpLoansDtlHist (compCode, empNo, lonTypeCd, lonRefNo, trnAmountD, ActualAmt)
					SELECT $tocomp, tblEmpLoansDtlHist.empNo, tblEmpLoansDtlHist.lonTypeCd, tblEmpLoansDtlHist.lonRefNo,
					SUM(tblEmpLoansDtlHist.trnAmountD) AS trnAmountD, SUM(tblEmpLoansDtlHist.ActualAmt) AS ActualAmt 
					FROM tblEmpLoans 
					INNER JOIN tblEmpLoansDtlHist ON tblEmpLoans.compCode = tblEmpLoansDtlHist.compCode 
					AND tblEmpLoans.empNo = tblEmpLoansDtlHist.empNo 
					AND tblEmpLoans.lonTypeCd = tblEmpLoansDtlHist.lonTypeCd 
					AND tblEmpLoans.lonRefNo = tblEmpLoansDtlHist.lonRefNo 
					WHERE (tblEmpLoans.lonStat = 'O') and tblEmpLoans.empNo='".$empno."' 
					GROUP BY tblEmpLoans.compCode,tblEmpLoansDtlHist.empNo,tblEmpLoansDtlHist.lonTypeCd,tblEmpLoansDtlHist.lonRefNo ;\n";
		if($sqlEmpLoanDetails!=""){
			$Trns = $this->execQry($sqlEmpLoanDetails);	
		}			
		
		//transfer contact mast
		$sqlTransferContactMast= "Insert into ".$todb."tblContactMast (compCode, empNo, contactCd, contactName) 
					SELECT $tocomp, empNo, contactCd, contactName 
					FROM tblContactMast 
					WHERE empNo='".$empno."' ;\n";
		if($sqlTransferContactMast!=""){
			$Trns = $this->execQry($sqlTransferContactMast);	
		}
		
		//transfer performance
		$sqlTransferPerformance = "Insert into ".$todb."tblPerformance (performanceFrom, performanceTo, performanceNumerical,
					performanceAdjective, performancePurpose, empNo, compCode, date_Added, user_Added, old_empDrate,new_empDrate,remarks) 
					SELECT performanceFrom, performanceTo, performanceNumerical, performanceAdjective,performancePurpose,
					empNo,$tocomp,date_Added,user_Added,old_empDrate, new_empDrate,remarks 
					FROM tblPerformance 
					WHERE empNo='".$empno."' ;\n";
		if($sqlTransferPerformance!=""){
			$Trns = $this->execQry($sqlTransferPerformance);	
		}						
		
		//transfer trainings
		$sqlTransferTrainings ="Insert into ".$todb."tblTrainings (trainingFrom, trainingTo, trainingTitle, trainingCost, trainingBond,
					effectiveFrom, effectiveTo, empNo, compCode, date_Added, user_Added) 
					SELECT trainingFrom, trainingTo,trainingTitle,trainingCost,trainingBond,effectiveFrom, 
					effectiveTo,empNo,$tocomp,date_Added, user_Added 
					FROM tblTrainings 
					WHERE empNo='".$empno."' ;\n";
		if($sqlTransferTrainings!=""){
			$Trns = $this->execQry($sqlTransferTrainings);	
		}			
		
		//transfer disciplinary action
		$sqlDisciplinaryAction = "Insert into ".$todb."tblDisciplinaryAction (date_commit, date_serve, article_id, section_id, offense,
					sanction, suspensionFrom, suspensionTo, empNo, catCode) 
					Select date_commit, date_serve, article_id, section_id, offense, sanction, suspensionFrom, suspensionTo, empNo, catCode 
					from tblDisciplinaryAction
					where empNo='".$empno."' ;\n";
		if($sqlDisciplinaryAction!=""){
			$Trns = $this->execQry($sqlDisciplinaryAction);	
		}			
		
		//transfer educational background
		$sqlEducationalBackground = "Insert into ".$todb."tblEducationalBackground (type, schoolId, dateStarted, dateCompleted, empNo,
					catCode, licenseNumber, licenseName, dateIssued, dateExpired) 
					SELECT type, schoolId, dateStarted, dateCompleted, empNo, catCode, licenseNumber, licenseName, dateIssued, dateExpired 
					FROM tblEducationalBackground 
					WHERE empNo='".$empno."' ;\n";
		if($sqlEducationalBackground!=""){
			$Trns = $this->execQry($sqlEducationalBackground);	
		}			
		
		//transfer employment background
		$sqlEmploymentBackground = "Insert into ".$todb."tblEmployeeDataHistory (companyName, employeePosition, startDate,
					endDate, empNo, catCode) 
					SELECT companyName, employeePosition, startDate, endDate, empNo, catCode 
					FROM tblEmployeeDataHistory 
					WHERE empNo='".$empno."' ;\n";
		if($sqlEmploymentBackground!=""){
			$Trns = $this->execQry($sqlEmploymentBackground);	
		}	

		//transfer customer number
		$sqlCustomerNumber = "Insert into ".$todb."tblCustomerNo (custNo, empNo) 
					Select custNo, empNo 
					from tblCustomerNo
					where empNo='".$empno."' ;\n";
		if($sqlCustomerNumber!=""){
			$Trns = $this->execQry($sqlCustomerNumber);	
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
	
	function setLastPayEmp($empNo,$toDb){
		$Trns = $this->beginTran();
			$sqlUpdateLastPayEmp = "Update ".$toDb."tblLastPayEmp set reHire='Y' where empNo='".$empNo."' and reHire is Null ;\n";		
		if($Trns){
			$Trns = $this->execQry($sqlUpdateLastPayEmp);	
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
	
	function deleteExistingRecord($empNo,$toDb){
		$Trns = $this->beginTran();

		//Delete EmpMast
		$sqlDeleteEmpMast = "Delete from ".$toDb."tblEmpMast where empNo='".$empNo."' ;\n";
		if($sqlDeleteEmpMast!=""){
			$Trns = $this->execQry($sqlDeleteEmpMast);	
		}	
		
		//Delete previous employeer
		$sqlDeletePreviousEmployeer = "Delete from ".$toDb."tblPrevEmployer where empNo='".$empNo."' ;\n";
		if($sqlDeletePreviousEmployeer!=""){
			$Trns = $this->execQry($sqlDeletePreviousEmployeer);	
		}
		
		//Delete allowance
		$sqlDeleteAllowance = "Delete from ".$toDb."tblAllowance where empNo='".$empNo."' ;\n";	
		if($sqlDeleteAllowance!=""){
			$Trns = $this->execQry($sqlDeleteAllowance);
		}
		
		//Delete allowance new
		$sqlDeleteAllowanceNew = "Delete from ".$toDb."tblAllowance_New where empNo='".$empNo."' ;\n";	
		if($sqlDeleteAllowanceNew!=""){
			$Trns = $this->execQry($sqlDeleteAllowanceNew);
		}

		//Delete Emp Loans
		$sqlDeleteEmpLoans = "Delete from ".$toDb."tblEmpLoans where empNo='".$empNo."' ;\n";	
		if($sqlDeleteEmpLoans!=""){
			$Trns = $this->execQry($sqlDeleteEmpLoans);
		}

		//Delete emp loan details
		$sqlDeleteEmpLoanDetails ="Delete from ".$toDb."tblEmpLoansDtlHist  where empNo='".$empNo."' ;\n";
		if($sqlDeleteEmpLoanDetails!=""){
			$Trns = $this->execQry($sqlDeleteEmpLoanDetails);	
		}

		//Delete contact mast
		$sqlDeleteContactMast = "Delete from ".$toDb."tblContactMast where empNo='".$empNo."' ;\n";	
		if($sqlDeleteContactMast!=""){
			$Trns = $this->execQry($sqlDeleteContactMast);	
		}	

		//Delete performance
		$sqlDeletePerformance = "Delete from ".$toDb."tblPerformance where empNo='".$empNo."' ;\n";
		if($sqlDeletePerformance!=""){
			$Trns = $this->execQry($sqlDeletePerformance);	
		}

		//Delete trainings
		$sqlDeleteTrainings ="Delete from ".$toDb."tblTrainings where empNo='".$empNo."' ;\n";
		if($sqlDeleteTrainings!=""){
			$Trns = $this->execQry($sqlDeleteTrainings);	
		}

		//Delete disciplinary action
		$sqlDeleteDisciplinaryAction ="Delete from ".$toDb."tblDisciplinaryAction where empNo='".$empNo."' ;\n";
		if($sqlDeleteDisciplinaryAction!=""){
			$Trns = $this->execQry($sqlDeleteDisciplinaryAction);	
		}

		//Delete educational background
		$sqlDeleteEducationalBackground ="Delete from ".$toDb."tblEducationalBackground where empNo='".$empNo."' ;\n";
		if($sqlDeleteEducationalBackground!=""){
			$Trns = $this->execQry($sqlDeleteEducationalBackground);	
		}
	
		//Delete employment background
		$sqlDeleteEmploymentBackground ="Delete from ".$toDb."tblEmployeeDataHistory where empNo='".$empNo."' ;\n";
		if($sqlDeleteEmploymentBackground!=""){
			$Trns = $this->execQry($sqlDeleteEmploymentBackground);	
		}
		
		//Delete customer number
		$sqlDeleteCustomerNumber = "Delete from ".$toDb."tblCustomerNo where empNo='".$empNo."' ;\n";
		if($sqlDeleteCustomerNumber!=""){
			$Trns = $this->execQry($sqlDeleteCustomerNumber);	
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
	
	function getTransCompany($compCode) {
		switch($compCode) {
//			case 2:
//				$compName = "PUREGOLD PRICE CLUB, INC.";
//				$db = "TEST_PROD_GEN..";
//			break;	
			case 1:
				$compName = "PUREGOLD JUNIOR SUPERMARKET, INC.";
				$db = "pgjr_payroll..";
			break;	
			case 2:
				$compName = "PUREGOLD PRICE CLUB, INC.";
				$db = "pg_payroll..";
			break;	
			case 4:
				$compName = "PUREGOLD DUTY FREE  CLARK INC.";
				$db = "DFClark_payroll..";
			break;				
			case 5:
				$compName = "PUREGOLD DUTY FREE SUBIC INC.";
				$db = "DFSubic_payroll..";
			break;
			case 3:
				$compName = "LUSITANO INC.";
				$db = "LUSITANO..";
			break;
			case 13:
				$compName = "PPCI SUBIC INC.";
				$db = "PG_SUBIC..";
			break;
			default:
				$compName = "";
				$db = "";
			break;
		}
		$arr['compName'] = $compName;
		$arr['db'] = $db;
		return $arr;
	}
	
	function getEmpID($db,$compCode) {
		$sqlID="Select id from ".$db."tblEmpID where compCode='$compCode'";
		$res = $this->getSqlAssoc($this->execQry($sqlID));
		$ID = $res['id'] + 1;
		$sqlUpdateID = "Update ".$db."tblEmpID set id='$ID' where compCode='$compCode' ";
		$this->execQry($sqlUpdateID);
		return $ID;
	}	
	
	function delTransEmp($seqNo) {
		$Trns = $this->beginTran();	
//		$arrEmpInfo = $this->getTransEmpInfoDeletion($empNo);
//		$arrOldComp = $this->getTransCompany($arrEmpInfo['new_compCode']);
//		if ($arrOldComp['db'] != "") {
		
			//delete empMast_new
//			$sqlDelEmpMast_new = "Delete from ".$arrOldComp['db']."tblEmpMast_new where empNo='$empNo' and stat is null";
//			if ($Trns) {
//				  $Trns = $this->execQry($sqlDelEmpMast_new);
//			}
			
			//delete Prev earnings
//			$sqlDeletePrevEarnings = "Delete from payroll_company..tblPrevEmployer where empNo='$empNo' and prevStat is null";
//			if ($Trns) {
//				  $Trns = $this->execQry($sqlDeletePrevEarnings);
//			}
//			
//			//delete LoansHdr
//			$sqlDeleteLoansHdr = "Delete from payroll_company..tblEmpLoans where empNo='$empNo' and lonStat is null";
//			if ($Trns) {
//				  $Trns = $this->execQry($sqlDeleteLoansHdr);
//			}
//			
//			//delete LoansDtls
//			$sqlDeleteLoanDtls = "Delete from payroll_company..tblEmpLoansDtlHist where empNo='$empNo' and dedStat is null";
//			
//			if ($Trns) {
//				  $Trns = $this->execQry($sqlDeleteLoanDtls);
//			}
//			
			//delete Trans Emp Info
			$sqlDeleteTransEmpInfo = "Delete from payroll_company..tblTransferredEmployees where seqNo = '$seqNo' and status='Q'";
			if ($Trns) {
				  $Trns = $this->execQry($sqlDeleteTransEmpInfo);
			}	
					
			if(!$Trns){
				$Trns = $this->rollbackTran();
				return false;
			}
			else{
				$Trns = $this->commitTran();
				return true;	
			}
//		} else {
//			return false;	
//		}
	}
	
	function getTransEmpInfoDeletion($empNo) {
		$sqlTransEmpDateInfo = "Select * from payroll_company..tblTransferredEmployees where empNo='$empNo' and status='Q'";	
		return $this->getSqlAssoc($this->execQry($sqlTransEmpDateInfo));
		
	}
	
	function checkEmployeeExist($employeeCode,$companyDestination=NULL,$tableName){
		if($companyDestination!=""){
			$dest=$companyDestination;	
		}
		else{
			$dest="";	
		}
		$sqlCheckEmployeeExist = "Select * from ".$dest.$tableName." where empNo='$employeeCode'";
		if($this->getRecCount($this->execQry($sqlCheckEmployeeExist))>0){
			return $this->getSqlAssoc($this->execQry($sqlCheckEmployeeExist));	
		}
		else{
			return false;	
		}	
	}
	
	function getEmpData($empNo){
		$sqlEmpDateInfo = "Select * from payroll_company..tblTransferredEmployees where empNo='$empNo' and status='Q'";	
		return $this->getSqlAssoc($this->execQry($sqlEmpDateInfo));
	}
	
	function getNewBranch($compDest,$branch){
		$qry = "SELECT * FROM ".$compDest."tblBranch
					     WHERE brnCode = '$branch' 
						 AND brnStat = 'A'";
		$res = $this->execQry($qry);
		return $this->getSqlAssoc($res);
	}
}
?>