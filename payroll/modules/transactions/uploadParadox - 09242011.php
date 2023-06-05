<?



$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();

$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
$getBrnchGrp = $inqTSObj->getBranchGroup($compCode);

$pdFrmDate = $_GET["dtFrm"];
$pdToDate = $_GET["dtTo"];
$pdNumber = $_GET["pdNum"];
$pdYear = $_GET["pdYear"];
$error_log = 0;




function Space($num)
{
	$sp = '';
	
	for($i=0; $i<$num; $i++)
		$sp .= ' ';

	return $sp;
}

function qryRemarks($db) {
	$qryGetRem = "SELECT [OT RATE CODE] as otRateCode, [EMPLOYEE ID#] as empNo,[DATE] as tsDate FROM [t_templ_ho]";
	return $db->Execute($qryGetRem);
}
function getRemarks($tsDate,$empNo,$resGetRem)
{
	$finalRem = "";
	$ctr=1;
	while(!$resGetRem->EOF){
		if ($resGetRem->fields['empNo']==$empNo && date('m/d/Y',strtotime($resGetRem->fields['tsDate']))==date('m/d/Y',strtotime($tsDate))) {
			if($ctr == 1){
				$finalRem = $resGetRem->fields['otRateCode'];	
			}
			else{
				$finalRem = $resGetRem->fields['otRateCode']."-".$finalRem;	
			}
			$ctr++;
		}
		$resGetRem->MoveNext();
	}
	
	return $finalRem;
	
}	


/*Process of Extracting the TS*/
	$Trns = $inqTSObj->beginTran();
			
			
	/*Delete Content of tblTsParadox*/
	$qryDelTsParadox = "Delete from tblTsParadox 
						where tsDate between '".$pdFrmDate."' AND '".$pdToDate."'
						and empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayGrp='".$_SESSION["pay_group"]."')
						";
	$Trns = $inqTSObj->execQry($qryDelTsParadox);

	if ($handle = opendir("D:/wamp/www/TIMESHEETS/HYPER_TIMESHEETS/".$_SESSION["company_code"]."-".$_SESSION["pay_group"]."-".$pdNumber."-".$pdYear."")) 
	{
		/* This is the correct way to loop over the directory. */
		
		while (false !== ($file = readdir($handle))) 
		{ 
			if ($file != "." && $file != ".." && strlen(trim(strpos($file,'.mdb')))>0) 
			{ 
				$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("D:/wamp/www/TIMESHEETS/HYPER_TIMESHEETS/".$_SESSION["company_code"]."-".$_SESSION["pay_group"]."-".$pdNumber."-".$pdYear."/".$file);
				$db->Connect($dsn,'','');
				$txtFile_OutPut = $ernTranHeader_BAdj = $earnTranDtl_BAdj = $ernTranHeader_OTAdj = $earnTranDtl_OTAdj = $ernTranHeader_AllowAdj = $earnTranDtl_AllowAdj = "";
				$cntrlNo = $cntrlNo_OtAdj = $cntrlNo_AllowAdj = 1;
				
				$qryGetParadoxData = "SELECT [EMPLOYEE ID#] as empNo,[DATE] as tsDate,
										  SUM( [DAYS ABSENT]) as hrsAbsnt,SUM( [TARDY HRS]) as hrsTrdy,SUM( [UT HRS]) as hrsUt, SUM([OT HOURS]) as hrsOtLe8, 
										  SUM([OT EXCESS HOURS]) as hrsOtGt8,SUM( [OT ND]) as hrsNd, SUM([INCOME AMOUNT]) as othAdj
										  FROM [t_templ_ho]
										  WHERE [DATE] BETWEEN #{$pdFrmDate}# AND #{$pdToDate}# 
										  GROUP BY  [EMPLOYEE ID#],[DATE] ORDER BY [EMPLOYEE ID#],[DATE];";
				
				$resGetParadoxData = $db->Execute($qryGetParadoxData);
				if($resGetParadoxData->recordCount() > 0)
				{
					$arrEmp = array();
					$arrEmp_NotWithinCO = array();
					$test_cnt = 1;
					$resGetRem = qryRemarks($db);
					while(!$resGetParadoxData->EOF)
					{
						if($tmp_emp!=$resGetParadoxData->fields['empNo'])
							$ctr = 1;
						
						$tmp_emp = $resGetParadoxData->fields['empNo'];
						
						$tmpOtLe8Hrs = (float)$resGetParadoxData->fields['hrsOtLe8'];
						$tmpOtGt8hrs = (float)$resGetParadoxData->fields['hrsOtGt8'];
						$tmpNdLe8Hrs = (float)$resGetParadoxData->fields['hrsNd'];
						$tmpOthAdj 	 = (float)$resGetParadoxData->fields['othAdj'];
						
						$tmpNdGt8Hrs = 0;
						if($tmpOtLe8Hrs > 8)
						{
							$excessOtLe8Hrs = $tmpOtLe8Hrs-8;
							$tmpOtLe8Hrs = $tmpOtLe8Hrs-$excessOtLe8Hrs;
							$tmpOtGt8hrs = $tmpOtGt8hrs+$excessOtLe8Hrs;
						}
						
						if($tmpNdLe8Hrs > 8)
						{
							$excessNdLe8Hrs = $tmpNdLe8Hrs-8;
							$tmpNdLe8Hrs = $tmpNdLe8Hrs-$excessNdLe8Hrs;
							$tmpNdGt8Hrs = $tmpNdGt8Hrs+$excessNdLe8Hrs;
						}
						$qryToNewTsParadaox.= " INSERT INTO tblTsParadox (compCode,empNo,tsDate,hrsAbsent,hrsTardy,hrsUt,hrsOtLe8,hrsOtGt8,hrsNdLe8,hrsNdGt8,tsRemarks)
												VALUES
												('{$_SESSION['company_code']}',
												 '{$resGetParadoxData->fields['empNo']}',
												 '{$resGetParadoxData->fields['tsDate']}',
												 '".sprintf("%01.2f",(float)$resGetParadoxData->fields['hrsAbsnt'])."',
												 '".sprintf("%01.2f",(float)$resGetParadoxData->fields['hrsTrdy'])."',
												 '".sprintf("%01.2f",(float)$resGetParadoxData->fields['hrsUt'])."',
												 '".sprintf("%01.2f",$tmpOtLe8Hrs)."','".sprintf("%01.2f",$tmpOtGt8hrs)."',
												 '".sprintf("%01.2f",$tmpNdLe8Hrs)."',
												 '".sprintf("%01.2f",$tmpNdGt8Hrs)."',
												 '".getRemarks($resGetParadoxData->fields['tsDate'],$resGetParadoxData->fields['empNo'],$resGetRem)."-".substr($file,0,strlen($file)-23)."');\n";
						
						unset($tmpOtHrs,$excessOtHrs,$tmpOtGt8hrs,$tmpNdLe8Hrs,$tmpNdGt8Hrs,$tmpOthAdj,$empCntRD);
						$i++;
						
						$test_cnt++;
						$testEmp = $resGetParadoxData->fields['empNo'];
						$resGetParadoxData->MoveNext();
					}//End of While loop
					if(($Trns)&&($qryToNewTsParadaox!=""))
						$Trns = $inqTSObj->execQry($qryToNewTsParadaox);

					unset($qryToNewTsParadaox);
				}//End of If Statement if($resGetParadoxData->recordCount() > 0)
				
				/*Employees With B-Adj/OT-Adj/Allowance-Adj Code*/
//				$earnTran_refNo_BAdj = "BADJ-".substr($file,0,strlen($file)-23).'-'.date("m/d/y");
//				$earnTran_refNo_OTAdj = "OTADJ-".substr($file,0,strlen($file)-23).'-'.date("m/d/y");
//				$earnTran_refNo_AllowAdj = "ALLOWADJ-".substr($file,0,strlen($file)-23).'-'.date("m/d/y");

				$earnTran_refNo_BAdj = "BADJ-".substr($file,0,strlen($file)-23).'-'.$pdYear.'-'.$pdNumber.'-'.$_SESSION["pay_group"];
				$earnTran_refNo_OTAdj = "OTADJ-".substr($file,0,strlen($file)-23).'-'.$pdYear.'-'.$pdNumber.'-'.$_SESSION["pay_group"];
				$earnTran_refNo_AllowAdj = "ALLOWADJ-".substr($file,0,strlen($file)-23).'-'.$pdYear.'-'.$pdNumber.'-'.$_SESSION["pay_group"];

				
				
				$qryGetParadoxData_NWCutOff = "SELECT [EMPLOYEE ID#] as empNo,[DATE] as tsDate,
								  SUM( [DAYS ABSENT]) as hrsAbsnt,SUM( [TARDY HRS]) as hrsTrdy,SUM( [UT HRS]) as hrsUt, SUM([OT HOURS]) as hrsOtLe8, 
								  SUM([OT EXCESS HOURS]) as hrsOtGt8,SUM( [OT ND]) as hrsNd, SUM([INCOME AMOUNT]) as othAdj, [INCOME TYPE] as incType, [ALLOW TYPE] as allowType, [ALLOWANCE AMOUNT] as allowAmount
								  FROM [t_templ_ho]
								  GROUP BY  [EMPLOYEE ID#],[DATE],[INCOME TYPE],[ALLOW TYPE],[ALLOWANCE AMOUNT] ORDER BY [ALLOW TYPE]";
				$resGetParadoxData_NWCutOff = $db->Execute($qryGetParadoxData_NWCutOff);
				if($resGetParadoxData_NWCutOff->recordCount() > 0)
				{
					/*Delete Content of tblEarnTranHeader and tblEarnTranDtl*/
					$qryEarnTranheader.= "Delete from tblEarnTranHeader where compCode='".$_SESSION["company_code"]."' 
											and refNo in ('".$earnTran_refNo_BAdj."','".$earnTran_refNo_OTAdj."')
											and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and earnStat='A';";
					
					$qryEarnTranheader.= "Delete from tblEarnTranDtl where compCode='".$_SESSION["company_code"]."' 
											and refNo in ('".$earnTran_refNo_BAdj."','".$earnTran_refNo_OTAdj."','".$earnTran_refNo_AllowAdj."')
											and payGrp='".$_SESSION["pay_group"]."'  and earnStat='A';";
					
					$qryEarnTranheader.= "Delete from tblEarnTranHeader where compCode='".$_SESSION["company_code"]."' 
											and refNo like ('".$earnTran_refNo_AllowAdj."%')
											and pdYear='".$pdYear."' and pdNumber='".$pdNumber."' and earnStat='A';";
					
					$qryEarnTranheader.= "Delete from tblEarnTranDtl where compCode='".$_SESSION["company_code"]."' 
											and refNo like ('".$earnTran_refNo_AllowAdj."%')
											and payGrp='".$_SESSION["pay_group"]."'  and earnStat='A';";
											
					$cntrlNo = 1;
					$cntrlNo_Allow = 1;
					while(!$resGetParadoxData_NWCutOff->EOF)
					{
						$tmpOthAdj 	 = (float)$resGetParadoxData_NWCutOff->fields['othAdj'];
						$tmpAllowAdj 	 = (float)$resGetParadoxData_NWCutOff->fields['allowAmount'];
						
						if(((($tmpOthAdj!=0) || ($tmpOthAdj!="")) && (($resGetParadoxData_NWCutOff->fields['incType']=='B-ADJ') || ($resGetParadoxData_NWCutOff->fields['incType']=='BASIC'))))
						{
							$earnTranDtl_BAdj.=  "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,payGrp,payCat,earnStat,trnTaxCd)
											  values('".$_SESSION["company_code"]."','".$earnTran_refNo_BAdj."','".$resGetParadoxData_NWCutOff->fields['empNo']."','".$cntrlNo."','".ADJ_BASIC."','".$tmpOthAdj ."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_BASIC_TAXCD."');";
							$cntrlNo++;
						}
						
						if(((($tmpOthAdj!=0) || ($tmpOthAdj!="")) && ($resGetParadoxData_NWCutOff->fields['incType']=='OT-ADJ')))
						{
							$earnTranDtl_OTAdj.=  "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,payGrp,payCat,earnStat,trnTaxCd)
											  values('".$_SESSION["company_code"]."','".$earnTran_refNo_OTAdj."','".$resGetParadoxData_NWCutOff->fields['empNo']."','".$cntrlNo_OtAdj."','".ADJ_OT."','".$tmpOthAdj ."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','".ADJ_OT_TAXCD."');";
							$cntrlNo_OtAdj++;
						}
						
						if(($tmpAllowAdj!=0) || ($tmpAllowAdj!="") )
						{
							$arr_AllowType = $inqTSObj->getEquivAllwCode($resGetParadoxData_NWCutOff->fields['allowType']);
							$allow_refNo = $earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"];
							
							$earnTranDtl_AllowAdj.=  "Insert into tblEarnTranDtl(compCode, refNo, empNo, trnCntrlNo, trnCode, trnAmount,payGrp,payCat,earnStat,trnTaxCd)
											  values('".$_SESSION["company_code"]."','".$allow_refNo."','".$resGetParadoxData_NWCutOff->fields['empNo']."','".$cntrlNo_AllowAdj."','".$arr_AllowType["trnCode"]."','".$tmpAllowAdj ."','".$_SESSION["pay_group"]."','".$_SESSION["pay_category"]."','A','N');";
							$cntrlNo_AllowAdj++;
							
							if($earnTranAllow_refNo!=$allow_refNo)
							{
								$ernTranHeader_AllowAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
											 values('".$_SESSION["company_code"]."','".$earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"]."','".$arr_AllowType["trnCode"]."','Allow-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
							}
								
								$earnTranAllow_refNo = $earnTran_refNo_AllowAdj."-".$arr_AllowType["trnCode"];
						}
						
						$resGetParadoxData_NWCutOff->MoveNext();
					} 
					
					if($earnTranDtl_BAdj!="")
						$ernTranHeader_BAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
										 values('".$_SESSION["company_code"]."','".$earnTran_refNo_BAdj."','".ADJ_BASIC."','B-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
					
					if($earnTranDtl_OTAdj!="")
						$ernTranHeader_OTAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
										 values('".$_SESSION["company_code"]."','".$earnTran_refNo_OTAdj."','".ADJ_OT."','OT-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
					
					/*if($earnTranDtl_AllowAdj!="")
						$ernTranHeader_AllowAdj.= "Insert into tblEarnTranHeader(compCode,refNo, trnCode, earnRem,earnStat, pdYear, pdNumber)
										 values('".$_SESSION["company_code"]."','".$earnTran_refNo_AllowAdj."','".ADJ_OT."','OT-Adj on Hyper TS','A','".$pdYear."','".$pdNumber."');";
					*/	
					
					$execQueries.= 	$ernTranHeader_BAdj.$ernTranHeader_OTAdj.$ernTranHeader_AllowAdj.$earnTranDtl_BAdj.$earnTranDtl_OTAdj.$earnTranDtl_AllowAdj;
					
					
					//Transactions with no B-Adj Adjustment
					$txtfile_title = "List of Transactions Not Within the Cut Off\r\n";


					$header = strtoupper("EMP. NO.").Space(11).strtoupper("TRAN. DATE").Space(11).strtoupper("HRS. ABSENT").Space(11).strtoupper("HRS. TARDY").Space(11).strtoupper("HRS. UT").Space(11).strtoupper("HRS. OTLE8").Space(11).strtoupper("HRS. OTGT8").Space(11).strtoupper("HRS. ND").Space(11).strtoupper("INCOME TYPE").Space(11).strtoupper("OTH. ADJ").Space(11);
					$qryGetParadoxData_NWCutOff_noBadj = "SELECT [EMPLOYEE ID#] as empNo,[DATE] as tsDate,
									  SUM( [DAYS ABSENT]) as hrsAbsnt,SUM( [TARDY HRS]) as hrsTrdy,SUM( [UT HRS]) as hrsUt, SUM([OT HOURS]) as hrsOtLe8, 
									  SUM([OT EXCESS HOURS]) as hrsOtGt8,SUM( [OT ND]) as hrsNd, SUM([INCOME AMOUNT]) as othAdj, [INCOME TYPE] as incType
									  FROM [t_templ_ho]
									  WHERE  [DATE] NOT BETWEEN #{$pdFrmDate}# AND #{$pdToDate}#    AND [INCOME TYPE] is null
									  GROUP BY  [EMPLOYEE ID#],[DATE],[INCOME TYPE] ORDER BY [DATE]";
					
					$resGetParadoxData_NWCutOff_noBadj = $db->Execute($qryGetParadoxData_NWCutOff_noBadj);
					if($resGetParadoxData_NWCutOff_noBadj->recordCount() > 0)
					{
						$txtFile_OutPut = "BRANCH : ".substr($file,0,strlen($file)-23)."\r\n";
						$error_log = 1;
						while(!$resGetParadoxData_NWCutOff_noBadj->EOF)
						{
						$txtFile_OutPut.=trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["empNo"], 0, 15)).Space(19-strlen($resGetParadoxData_NWCutOff_noBadj->fields["empNo"])).
		($resGetParadoxData_NWCutOff_noBadj->fields["tsDate"]!=""?trim(substr(date("m-d-Y", strtotime($resGetParadoxData_NWCutOff_noBadj->fields["tsDate"])), 0, 15)):Space(21)).Space(21-strlen(date("m-d-Y", strtotime($resGetParadoxData_NWCutOff_noBadj->fields["tsDate"])))).($resGetParadoxData_NWCutOff_noBadj->fields["hrsAbsnt"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["hrsAbsnt"], 0, 15)).Space(22-strlen($resGetParadoxData_NWCutOff_noBadj->fields["hrsAbsnt"])):Space(22)).($resGetParadoxData_NWCutOff_noBadj->fields["hrsTrdy"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["hrsTrdy"], 0, 15)).Space(21-strlen($resGetParadoxData_NWCutOff_noBadj->fields["hrsTrdy"])):Space(21)).($resGetParadoxData_NWCutOff_noBadj->fields["hrsUt"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["hrsUt"], 0, 15)).Space(18-strlen($resGetParadoxData_NWCutOff_noBadj->fields["hrsUt"])):Space(18)).($resGetParadoxData_NWCutOff_noBadj->fields["hrsOtLe8"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["hrsOtLe8"], 0, 15)).Space(21-strlen($resGetParadoxData_NWCutOff_noBadj->fields["hrsOtLe8"])):Space(21)).($resGetParadoxData_NWCutOff_noBadj->fields["hrsOtGt8"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["hrsOtGt8"], 0, 15)).Space(21-strlen($resGetParadoxData_NWCutOff_noBadj->fields["hrsOtGt8"])):Space(21)).($resGetParadoxData_NWCutOff_noBadj->fields["hrsNd"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["hrsNd"], 0, 15)).Space(18-strlen($resGetParadoxData_NWCutOff_noBadj->fields["hrsNd"])):Space(18)).($resGetParadoxData_NWCutOff_noBadj->fields["incType"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["incType"], 0, 15)).Space(22-strlen($resGetParadoxData_NWCutOff_noBadj->fields["incType"])):Space(22)).($resGetParadoxData_NWCutOff_noBadj->fields["othAdj"]!=""?trim(substr($resGetParadoxData_NWCutOff_noBadj->fields["othAdj"], 0, 15)).Space(19-strlen($resGetParadoxData_NWCutOff_noBadj->fields["othAdj"])):Space(19)).
		"\r\n";
						$resGetParadoxData_NWCutOff_noBadj->MoveNext();
						}
					}	
				
					if($error_log==1)
					{
						$output_err.=$txtfile_title."\r\n".$header."\r\n".$txtFile_OutPut;
						
						if(file_exists($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-ERROR-'.$_SESSION["pay_group"].'-'.$pdNum.'-'.$pdYear.'.txt'))
						{
							//unlink($_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '/'.session_id().'-ERROR.txt');
						}
				
						$inqTSObj->WriteFile(session_id().'-ERROR-'.$_SESSION["pay_group"].'-'.$pdNum.'-'.$pdYear.'.txt', $_SERVER['DOCUMENT_ROOT']. DOWNLOAD_PATH . '', $output_err);
						$noError = 1;
						
					}
					
				}	// End of ($resGetParadoxData_NWCutOff->recordCount() > 0)
				
					
			} // End of  if ($file != "." && $file != "..") 
		} // End of while (false !== ($file = readdir($handle)))
		
		
/*		if(($Trns)&&($ins_Statement!=""))
			$Trns = $inqTSObj->execQry($ins_Statement);
*/		
		if(($Trns)&&($qryEarnTranheader!=""))
				$Trns = $inqTSObj->execQry($qryEarnTranheader);
				
		if(($Trns)&&($execQueries!=""))
			$Trns = $inqTSObj->execQry($execQueries);
		
		$qryUpdateEarnTran = "Update tblEarnTranDtl set payCat='".EXEC."' where empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".EXEC."' and empPayGrp='".$_SESSION["pay_group"]."'); ";
		$qryUpdateEarnTran.= "Update tblEarnTranDtl set payCat='".CONFI."' where empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".CONFI."' and empPayGrp='".$_SESSION["pay_group"]."'); ";
		$qryUpdateEarnTran.= "Update tblEarnTranDtl set payCat='".NONCONFI."' where empNo in (Select empNo from tblEmpMast where compCode='".$_SESSION["company_code"]."' and empPayCat='".NONCONFI."' and empPayGrp='".$_SESSION["pay_group"]."'); ";
		
		if(($Trns)&&($qryUpdateEarnTran!=""))
			$Trns = $inqTSObj->execQry($qryUpdateEarnTran);
		


		if(!$Trns){
			$Trns = $inqTSObj->rollbackTran();
			if($error_log!=0)
				echo "31";
			else
				echo "3";
		}
		else{
			$Trns = $inqTSObj->commitTran();
			if($error_log!=0)
				echo "41";
			else
				echo "4";
		}	
		$db->Close();
		closedir($handle); 
	} // End of if ($handle = opendir('C:/wamp/www/PG-HRIS-SYSTEM/GRP2_TIMESHEETS')) 
/* End of Process of Extracting the TS*/



?>