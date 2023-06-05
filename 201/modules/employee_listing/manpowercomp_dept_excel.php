<?
################### INCLUDE FILE #################
	session_start();
	ini_set('include_path','d:\wamp\php\PEAR');
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("common_obj.php");
	require_once 'Spreadsheet/Excel/Writer.php';
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$workbook = new Spreadsheet_Excel_Writer();
	
	//Set Font Style
	$headerFormat = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'left',
						  num_format=>0));
	$headerFormat->setFontFamily('Calibri'); 
	
	$headerFormat_branch = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'green',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'left',
						  num_format=>0));
	$headerFormat_branch->setFontFamily('Calibri'); 
	
	$headerFormat_rank = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'red',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'center',
						  num_format=>0));
	$headerFormat_rank->setFontFamily('Calibri'); 
	
	$headerFormat_empstat = $workbook->addFormat(array('Size' => 10,
                                      'Color' => 'blue',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'center',
						  num_format=>0));
	$headerFormat_empstat->setFontFamily('Calibri'); 
	
	$headerFormat_div = $workbook->addFormat(array('Size' => 9,
                                      'Color' => 'blue',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'left',
						  num_format=>0));
	$headerFormat_div->setFontFamily('Calibri'); 
	
	$headerFormat_dept = $workbook->addFormat(array('Size' => 8,
                                      'Color' => 'black',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'left',
						  num_format=>0));
	$headerFormat_dept->setFontFamily('Calibri'); 

	$headerFormat_grand = $workbook->addFormat(array('Size' => 11,
                                      'Color' => 'red',
                                      'bold'=> 1,
									  'border' => 0,
									  'Align' => 'left',
						  num_format=>0));
	$headerFormat_grand ->setFontFamily('Calibri'); 
	
	
	$filename = "MAN POWER HEAD COUNT BY DEPT.xls";
	
	//Column titles
	$workbook->send($filename);
	$worksheet=&$workbook->addWorksheet("Employee Listing");
	$worksheet->setLandscape();
	
	
	//Variables
	$Year = ($_GET['payPd'] !="") ? $_GET['payPd'] : date('Y');
	$arrCompInfo = $inqTSObj->getCompany($_SESSION["company_code"]);	
	$arrBranch = $inqTSObj->getEmpBranchArt($_SESSION["company_code"],$empBrnCode );
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	$empBrnCode = $_GET['empBrnCode'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empRank = $_GET['empRank'];
	$empStatus = $_GET['empStatus'];
	$monthfr =  $_GET["monthfr"];
	$monthto =  $_GET["monthto"];
	
	if(($monthfr!="") && ($monthfr!=""))
		$disDate =  " AS OF ".date("m/d/Y", strtotime($monthfr))." - ".date("m/d/Y", strtotime($monthto));
	
	//Functions
	function getRankType_ManPower($empRank="")
	{
		extract($GLOBALS);
		$whereRank = ($empRank!="0"?" and rankCode='".$empRank."'":"");
		$qryRank =	"SELECT  rankCode, rankDesc FROM tblRankType 
					WHERE  compCode='".$_SESSION["company_code"]."' and rankCode<>'5' $whereRank";
		
		$rsRank = $inqTSObj->execQry($qryRank);
		
		if($empRank!="0")
			return $inqTSObj->getSqlAssoc($rsRank);
		else
			return $inqTSObj->getArrRes($rsRank);
		
	}
	
	function getDistinctDivCode($arrEmp, $branchCode)
	{
		foreach($arrEmp as $arrEmp_val)
		{
			if(($disDiv!=$arrEmp_val["empDiv"]) && ($arrEmp_val["empBrnCode"]==$branchCode))
				$divCodeDesc.= $arrEmp_val["empDiv"]."*".$arrEmp_val["divDesc"]."=";
				
			$disDiv = $arrEmp_val["empDiv"];
		}
		
		return $divCodeDesc;
	}
	
	function getDistinctDeptCode($arrEmp, $branchCode, $divCode)
	{
		foreach($arrEmp as $arrEmp_val)
		{
			if($arrEmp_val["empBrnCode"]==$branchCode)
			{
				if($arrEmp_val["empDiv"]==$divCode)
				{
					if($arrEmp_val["empDepCode"]!=$disDepCode)
					{	
						$depCodeDesc.= $arrEmp_val["empDepCode"]."*".$arrEmp_val["deptDesc"]."=";
					}	
					$disDepCode = $arrEmp_val["empDepCode"];
				}
			}
		}		
		return $depCodeDesc;
	}
	
	//Query
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($empRank>"" && $empRank>0) {$empRank1 = " AND (empRank = '{$empRank}')";} else {$empRank1 = "";}
	if (($empStatus!="" ) && ($empStatus!="0")) {$empStatus1 = " AND (employmentTag = '{$empStatus}')";} else {$empStatus1 = "";}
	if (($monthfr!="" ) && ($monthto!="")) {$empDateHired = " AND (dateHired between '".date("m/d/Y", strtotime($monthfr))."' and '".date("m/d/Y", strtotime($monthto))."')";} else {$empDateHired = "";}
		
	
	
	$sqlEmp = "SELECT empNo, empBrnCode, brnDesc, empDiv, div.deptdesc as divDesc, empDepCode, dept.deptDesc as deptDesc, empLastName, empFirstName, empMidName, empStat, empRank 
				FROM tblEmpMast empMast, tblBranch branch, tblDepartment div, tblDepartment dept
				WHERE 
				(empMast.compCode = '".$_SESSION["company_code"]."')   
				$empDiv1 $empDept1 $empBrnCode1 $empRank1 $empStatus1 $empDateHired AND
				branch.compCode='".$_SESSION["company_code"]."' and empMast.empBrnCode=branch.brnCode AND 
				div.compCode='".$_SESSION["company_code"]."' and empMast.empDiv = div.divCode AND div.deptLevel='1' AND
				dept.compCode='".$_SESSION["company_code"]."' and empMast.empDiv=dept.divCode and empMast.empDepCode=dept.deptCode and dept.deptLevel='2' and empStat='RG'
				order by brnDesc, divDesc, deptDesc,  empLastName, empFirstName, empMidName ";   
	$resEmp = $inqTSObj->execQry($sqlEmp);	
	$arrEmp = $inqTSObj->getArrRes($resEmp);
	
	if($inqTSObj->getRecCount($resEmp)>0)
	{
		//Set Header
		$worksheet->setColumn(0,0,60);
		for($ctrCol=1; $ctrCol<=20; $ctrCol++)
		{
			$worksheet->setColumn(0,$ctrCol,15);
		}
		
		
		
		$worksheet->freezePanes(array(6,0));
		
		
		
		$worksheet->write(0,0,$arrCompInfo["compName"],$headerFormat);
		$worksheet->write(1,0,'MANPOWER HEAD COUNT BY DEPT.'.$disDate,$headerFormat);
		$worksheet->write(2,0,"RUN DATE: " . $newdate,$headerFormat);
		
		//Display Per Branch
		$ctr_branch = 5;
		$ctr_rank_horizontal = 1;
		
		if($empRank!="0")
		{
			
			$arrRank = getRankType_ManPower($empRank);
		
			if($empStatus!="0")
			{
				$worksheet->setMerge(4,1,4,2);
				$worksheet->setMerge(4,3,4,4);
				$worksheet->write(4,1,strtoupper($arrRank["rankDesc"]),$headerFormat_rank);
			
				if($empStatus=='RG')
					$disStat = "REG";
				elseif($empStatus=='PR')
					$disStat = "PR";
				else
					$disStat = "CON";
					
				$worksheet->write(4,3,'GRAND TOTAL',$headerFormat_rank);
					
				$worksheet->write(5,1,$disStat,$headerFormat_empstat);
				$worksheet->write(5,2,'TOT',$headerFormat_empstat);
				$worksheet->write(5,3,$disStat,$headerFormat_empstat);
				$worksheet->write(5,4,'TOT',$headerFormat_empstat);
			}
			else
			{
				$worksheet->setMerge(4,1,4,4);
				$worksheet->setMerge(4,5,4,8);
				$worksheet->write(4,1,strtoupper($arrRank["rankDesc"]),$headerFormat_rank);
				$arr_empStat = array("RG", "PR", "CN");
				$worksheet->write(4,5,'GRAND TOTAL',$headerFormat_rank);
				foreach($arr_empStat as $arr_empStat_val)
				{
					$worksheet->write($ctr_branch,$ctr_rank_horizontal,$arr_empStat_val,$headerFormat_empstat);
					$ctr_rank_horizontal++;
				}
				$worksheet->write($ctr_branch,$ctr_rank_horizontal,'TOT',$headerFormat_empstat);
				
				
				$ctr_rank_horizontal+=1;
				foreach($arr_empStat as $arr_empStat_val)
				{
					$worksheet->write($ctr_branch,$ctr_rank_horizontal,$arr_empStat_val,$headerFormat_empstat);
					$ctr_rank_horizontal++;
				}
				$worksheet->write($ctr_branch,$ctr_rank_horizontal,'TOT',$headerFormat_empstat);
				
			}
		}
		else
		{
			$arrRank = getRankType_ManPower();
			if($empStatus!="0")
				$workSheet_Merge = 2;
			else
				$workSheet_Merge = 4;
				
			foreach($arrRank as $arrRank_disEmp_val)
			{
				if($empStatus!="0")
				{
					
					$worksheet->setMerge(4,$ctr_rank_horizontal,4,$workSheet_Merge);
					$worksheet->write(4,$ctr_rank_horizontal,strtoupper($arrRank_disEmp_val["rankDesc"]),$headerFormat_rank);
					$workSheet_Merge+=2;
					
					if($empStatus=='RG')
						$disStat = "REG";
					elseif($empStatus=='PR')
						$disStat = "PR";
					else
						$disStat = "CON";
						
					$worksheet->write(5,$ctr_rank_horizontal,$disStat,$headerFormat_empstat);
					$worksheet->write(5,$ctr_rank_horizontal+1,'TOT',$headerFormat_empstat);
					
					$ctr_rank_horizontal+=2;
				}
				else
				{
					
					$worksheet->setMerge(4,$ctr_rank_horizontal,4,$workSheet_Merge);
					$worksheet->write(4,$ctr_rank_horizontal,strtoupper($arrRank_disEmp_val["rankDesc"]),$headerFormat_rank);
					$workSheet_Merge+=4;
					
					$arr_empStat = array("RG", "PR", "CN");
					foreach($arr_empStat as $arr_empStat_val)
					{
						$worksheet->write(5,$ctr_rank_horizontal,$arr_empStat_val,$headerFormat_empstat);
						$ctr_rank_horizontal++;
					}
					$worksheet->write(5,$ctr_rank_horizontal,'TOT',$headerFormat_empstat);
					
					$ctr_rank_horizontal+=1;
				}
			}
			$worksheet->setMerge(4,$ctr_rank_horizontal,4,$workSheet_Merge);
			$worksheet->write(4,$ctr_rank_horizontal,'GRAND TOTAL',$headerFormat_rank);	
			
			if($empStatus!="0")
			{
				$worksheet->write(5,$ctr_rank_horizontal,$disStat,$headerFormat_empstat);
				$worksheet->write(5,$ctr_rank_horizontal+1,'TOT',$headerFormat_empstat);
			}
			else
			{
				$arr_empStat = array("RG", "PR", "CN");
				foreach($arr_empStat as $arr_empStat_val)
				{
					$worksheet->write(5,$ctr_rank_horizontal,$arr_empStat_val,$headerFormat_empstat);
					$ctr_rank_horizontal++;
				}
				$worksheet->write(5,$ctr_rank_horizontal,'TOT',$headerFormat_empstat);
			}
		}
		
		$ctr_branch = 7;
		foreach($arrEmp as $arrEmp_val)
		{
			if($disBranchCode!=$arrEmp_val["empBrnCode"])
			{
	
				$worksheet->write($ctr_branch-1,0,$arrEmp_val["brnDesc"],$headerFormat_branch);
				
				//GET DIVISION
				$arrDivision = getDistinctDivCode($arrEmp,$arrEmp_val["empBrnCode"]);
				$arrDivision = substr($arrDivision, 0, strlen($arrDivision)-1);
				$exp_arrDivision = explode("=",$arrDivision);
				
				foreach($exp_arrDivision as $exp_arrDivision_val)
				{
					$exp_cddesc = explode("*", $exp_arrDivision_val);
					$worksheet->write($ctr_branch,0,$exp_cddesc[1],$headerFormat_div);
					
					$arrDept = getDistinctDeptCode($arrEmp, $arrEmp_val["empBrnCode"], $exp_cddesc[0]);
					$arrDept = substr($arrDept, 0, strlen($arrDept)-1);
					$exp_arrDept = explode("=",$arrDept);
					
					foreach($exp_arrDept as $exp_arrDept_val)
					{
						$exp_divcddesc = explode("*", $exp_arrDept_val);
						$worksheet->write($ctr_branch+1,0,"         ".$exp_divcddesc[1],$headerFormat_dept);
						if($empRank!="0")
						{
							$arrRank = getRankType_ManPower($empRank);
							
							if($empStatus!="0")
							{
								if($empStatus=='RG')
									$empStat = "RG";
								elseif($empStatus=='PR')
									$empStat = "PR";
								else
									$empStat = "CN";
								
								foreach($arrEmp as $arrEmpdispEmp_val)
								{
									if(($arrRank["rankCode"]==$arrEmpdispEmp_val["empRank"]) && ($arrEmp_val["empBrnCode"]==$arrEmpdispEmp_val["empBrnCode"]) && ($exp_cddesc[0]==$arrEmpdispEmp_val["empDiv"])&& ($exp_divcddesc[0]==$arrEmpdispEmp_val["empDepCode"])&&($empStat==$arrEmpdispEmp_val["empStat"]))
									{
										$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$empStat]++;
										$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]]++;
										$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$empStat]++;
										$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]]++;
										
										$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat]++;
										$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]]++;
										$grand[$arrEmp_val["empBrnCode"]]++;
									}
								}
								
								
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$empStat],$headerFormat_dept);
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal+1,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]],$headerFormat_dept);
								
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal+2,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]],$headerFormat_dept);
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal+3,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],$headerFormat_dept);
								
							}
							else
							{
								$ctr_rank_horizontal = 1;
								$arr_empStat = array("RG", "PR", "CN");
								
								foreach($arr_empStat as $arr_empStat_val)
								{
									foreach($arrEmp as $arrEmpdispEmp_val)
									{
										if(($arrRank["rankCode"]==$arrEmpdispEmp_val["empRank"]) && ($arrEmp_val["empBrnCode"]==$arrEmpdispEmp_val["empBrnCode"]) && ($exp_cddesc[0]==$arrEmpdispEmp_val["empDiv"])&& ($exp_divcddesc[0]==$arrEmpdispEmp_val["empDepCode"])&&($arr_empStat_val==$arrEmpdispEmp_val["empStat"]))
										{
											$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$arr_empStat_val]++;
											$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]]++;
											$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val]++;
											$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]]++;
											$grand[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
											
											$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
											$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]]++;
											$grand[$arrEmp_val["empBrnCode"]]++;
										}
									}
									$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$arr_empStat_val],$headerFormat_dept);
									$ctr_rank_horizontal++;
								}
								
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]],$headerFormat_dept);
								
								$ctr_rank_horizontal = $ctr_rank_horizontal+1;
								foreach($arr_empStat as $arr_empStat_val)
								{
									$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val],$headerFormat_dept);
									$ctr_rank_horizontal++;
								}
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],$headerFormat_dept);
							}
						}
						else
						{
							$arrRank =  getRankType_ManPower();
							$ctr_rank_horizontal = 1;
							foreach($arrRank as $arrRank_disEmp_val)
							{
								if($empStatus!="0")
								{
									if($empStatus=='RG')
										$empStat = "RG";
									elseif($empStatus=='PR')
										$empStat = "PR";
									else
										$empStat = "CN";
									
									foreach($arrEmp as $arrEmpdispEmp_val)
									{
										if(($arrRank_disEmp_val["rankCode"]==$arrEmpdispEmp_val["empRank"]) && ($arrEmp_val["empBrnCode"]==$arrEmpdispEmp_val["empBrnCode"]) && ($exp_cddesc[0]==$arrEmpdispEmp_val["empDiv"])&& ($exp_divcddesc[0]==$arrEmpdispEmp_val["empDepCode"])&&($empStat==$arrEmpdispEmp_val["empStat"]))
										{
											$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat]++;
											$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]]++;
											$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$empStat]++;
											$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]]++;
											$grand[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
											
											$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat]++;
											$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$empStat]++;
											$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]]++;
											$grand[$arrEmp_val["empBrnCode"]]++;

										}
									}
									
									$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat],$headerFormat_dept);
									$worksheet->write($ctr_branch+1,$ctr_rank_horizontal+1,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_dept);
									
								}
								else
								{
									$arr_empStat = array("RG", "PR", "CN");
									foreach($arr_empStat as $arr_empStat_val)
									{
										foreach($arrEmp as $arrEmpdispEmp_val)
										{
											if(($arrRank_disEmp_val["rankCode"]==$arrEmpdispEmp_val["empRank"]) && ($arrEmp_val["empBrnCode"]==$arrEmpdispEmp_val["empBrnCode"]) && ($exp_cddesc[0]==$arrEmpdispEmp_val["empDiv"])&& ($exp_divcddesc[0]==$arrEmpdispEmp_val["empDepCode"])&&($arr_empStat_val==$arrEmpdispEmp_val["empStat"]))
											{
												$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
												$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]]++;
												$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val]++;
												$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]]++;
												$grand[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
												
												$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
												$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$arr_empStat_val]++;
												$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]]++;
												$grand[$arrEmp_val["empBrnCode"]]++;
											}
										}
										$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],$headerFormat_dept);
										$ctr_rank_horizontal++;
									}
									$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_dept);
								}
								if($empStatus!="0")
									$ctr_rank_horizontal+=2;
								else
									$ctr_rank_horizontal++;
							}
							
							if($empStatus!="0")
							{
								if($empStatus=='RG')
									$empStat = "RG";
								elseif($empStatus=='PR')
									$empStat = "PR";
								else
									$empStat = "CN";
								
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$empStat],$headerFormat_dept);
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal+1,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],$headerFormat_dept);
							}
							else
							{
								$arr_empStat = array("RG", "PR", "CN");
								foreach($arr_empStat as $arr_empStat_val)
								{
									$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val],$headerFormat_dept);
									$ctr_rank_horizontal++;
								}
								$worksheet->write($ctr_branch+1,$ctr_rank_horizontal,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],$headerFormat_dept);
							}										
						}
						$ctr_branch++;
					}
					
					
					$ctr_branch++;
				}
				$worksheet->write($ctr_branch,0,'BRANCH TOTAL',$headerFormat_branch);
				
				if($empRank!="0")
				{
					$arrRank = getRankType_ManPower($empRank);
					
					//DISPLAY EMP. STATUS
					if($empStatus!="0")
					{
						if($empStatus=='RG')
							$disStat = "REG";
						elseif($empStatus=='PR')
							$disStat = "PR";
						else
							$disStat = "CON";
						
						$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat],$headerFormat_rank);
						$worksheet->write($ctr_branch,$ctr_rank_horizontal+1,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_rank);
						
						$worksheet->write($ctr_branch,$ctr_rank_horizontal+2,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_rank);
						$worksheet->write($ctr_branch,$ctr_rank_horizontal+3,$grand[$arrEmp_val["empBrnCode"]],$headerFormat_rank);
					}
					else
					{
						$ctr_rank_horizontal = 1;
						$arr_empStat = array("RG", "PR", "CN");
						foreach($arr_empStat as $arr_empStat_val)
						{
							$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],$headerFormat_rank);
							$ctr_rank_horizontal++;
						}
						
						$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_rank);
								
						$ctr_rank_horizontal = $ctr_rank_horizontal+1;
						foreach($arr_empStat as $arr_empStat_val)
						{
							$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],$headerFormat_rank);
							$ctr_rank_horizontal++;
						}
							
						$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand[$arrEmp_val["empBrnCode"]],$headerFormat_rank);
						
						
					}
				}
				else
				{
					$arrRank =  getRankType_ManPower();
					$ctr_rank_horizontal = 1;
					foreach($arrRank as $arrRank_disEmp_val)
					{
						if($empStatus!="0")
						{
							if($empStatus=='RG')
								$empStat = "RG";
							elseif($empStatus=='PR')
								$empStat = "PR";
							else
								$empStat = "CN";
							
							
							$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat],$headerFormat_rank);
							$worksheet->write($ctr_branch,$ctr_rank_horizontal+1,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_rank);
							
						}
						else
						{
							$arr_empStat = array("RG", "PR", "CN");
							foreach($arr_empStat as $arr_empStat_val)
							{
								
								$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],$headerFormat_rank);
								$ctr_rank_horizontal++;
							}
							$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],$headerFormat_rank);
						
						}
						
						if($empStatus!="0")
							$ctr_rank_horizontal+=2;
						else
							$ctr_rank_horizontal++;
					}
					
					if($empStatus!="0")
					{
						if($empStatus=='RG')
							$empStat = "RG";
						elseif($empStatus=='PR')
							$empStat = "PR";
						else
							$empStat = "CN";
						
						$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$empStat],$headerFormat_rank);
						$worksheet->write($ctr_branch,$ctr_rank_horizontal+1,$grand[$arrEmp_val["empBrnCode"]],$headerFormat_rank);
					}
					else
					{
						$arr_empStat = array("RG", "PR", "CN");
						foreach($arr_empStat as $arr_empStat_val)
						{
							$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$arr_empStat_val],$headerFormat_rank);
							$ctr_rank_horizontal++;
						}
						$worksheet->write($ctr_branch,$ctr_rank_horizontal,$grand[$arrEmp_val["empBrnCode"]],$headerFormat_rank);
					}	
						
				}
				
				
				$ctr_branch+=3;
				$ctr_rank_horizontal = 1;
			}
			
			$disBranchCode=$arrEmp_val["empBrnCode"];
		}
		//End of Display Per Branch
	}
				
	
	
	
	
	$workbook->close();
		

?>