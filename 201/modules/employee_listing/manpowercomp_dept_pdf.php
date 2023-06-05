<?php
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/24/2010
		Function		:	Blacklist Module (Pop Up) 
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("common_obj.php");
	include("../../../includes/pdf/fpdf.php");
	
	$payrollTypeObj = new inqTSObj();
	$sessionVars = $payrollTypeObj->getSeesionVars();
	$payrollTypeObj->validateSessions('','MODULES');
	
	class PDF extends FPDF
	{
		function Header()
		{
			$gmt = time() + (8 * 60 * 60);
			$newdate = date("m/d/Y h:iA", $gmt);
		
			$this->SetFont('Arial','','10'); 
			$this->Cell(70,5,"Run Date: " . $newdate,'0','');
			$hTitle = " Man Power Head Count Report ".$this->disDate."";
			$this->Cell(140,5,$hTitle,'0','','C');
			$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
			$this->Ln();
			
			$this->Cell(70,5,"Report ID: MANPOWERHCDEPT");
			
			$this->Ln();
			$this->Cell(50,3,'','');
			$this->Ln(5);
		}
		
		function getRankType($empRank="")
		{
			$whereRank = ($empRank!=""?" and rankCode='".$empRank."'":"");
			$qryRank =	"SELECT  rankCode, rankDesc FROM tblRankType 
						WHERE  compCode='".$_SESSION["company_code"]."' and rankCode<>'5' $whereRank";
			
			$rsRank = $this->execQry($qryRank);
			
			if($empRank!="")
				return $this->getSqlAssoc($rsRank);
			else
				return $this->getArrRes($rsRank);
			
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
		
		function displayContent($arrEmp)
		{
			$this->Ln(5);
			
			
			
			foreach($arrEmp as $arrEmp_val)
			{
				if($disBranchCode!=$arrEmp_val["empBrnCode"])
				{
					$this->SetFont('Arial','B','10');
					//330
					$this->Cell(40,7,$arrEmp_val["brnDesc"],'0','0','L');
					$this->Ln();
					
					$this->Cell(60,7,'','','0','L');
					
					//DISPLAY RANK / EMP. STATUS
					if($this->empRank!="0")
					{
						$arrRank = $this->getRankType($this->empRank);
						$this->Cell(180,7,strtoupper($arrRank["rankDesc"]),'1','0','C');
						$this->Cell(90,7,'GRAND TOTAL','1','1','C');
						$this->Cell(60,7,'','','0','L');
						
						//DISPLAY EMP. STATUS
						if($this->empStatus!="0")
						{
							if($this->empStatus=='RG')
								$disStat = "REG";
							elseif($this->empStatus=='PR')
								$disStat = "PR";
							else
								$disStat = "CON";
								
							$this->Cell(150,7,strtoupper($disStat),'1','0','C');
							$this->Cell(30,7,'TOT','1','0','C');
							
							$this->Cell(60,7,strtoupper($disStat),'1','0','C');
							$this->Cell(30,7,'TOT','1','1','C');
						}
						else
						{
							$this->Cell(45,7,'REG','1','0','C');
							$this->Cell(45,7,'PR','1','0','C');
							$this->Cell(45,7,'CON','1','0','C');
							$this->Cell(45,7,'TOT','1','0','C');
							
							$this->Cell(22.5,7,'REG','1','0','C');
							$this->Cell(22.5,7,'PR','1','0','C');
							$this->Cell(22.5,7,'CON','1','0','C');
							$this->Cell(22.5,7,'TOT','1','1','C');
							
							
						}
						
						
					}
					else
					{
						$arrRank =  $this->getRankType();
						foreach($arrRank as $arrRank_val)
							$this->Cell(50,7,strtoupper($arrRank_val["rankDesc"]),'1','0','C');
						
						$this->Cell(70,7,'GRAND TOTAL','1','1','C');
						$this->Cell(60,7,'','','0','C');
						
						foreach($arrRank as $arrRank_val)
						{
							if($this->empStatus!="0")
							{
								if($this->empStatus=='RG')
									$disStat = "REG";
								elseif($this->empStatus=='PR')
									$disStat = "PR";
								else
									$disStat = "CON";
									
								$this->Cell(30,7,strtoupper($disStat),'1','0','C');
								$this->Cell(20,7,'TOT','1','0','C');
								
								
							}
							else
							{
								$this->Cell(12.5,7,'REG','1','0','C');
								$this->Cell(12.5,7,'PR','1','0','C');
								$this->Cell(12.5,7,'CON','1','0','C');
								$this->Cell(12.5,7,'TOT','1','0','C');
							}
							
						}
						if($this->empStatus!="0")
						{
							$this->Cell(40,7,strtoupper($disStat),'1','0','C');
							$this->Cell(30,7,'TOT','1','1','C');
						}
						else
						{
							$this->Cell(17.5,7,'REG','1','0','C');
							$this->Cell(17.5,7,'PR','1','0','C');
							$this->Cell(17.5,7,'CON','1','0','C');
							$this->Cell(17.5,7,'TOT','1','0','C');
						}
					}
					
					$this->Ln();
					
					//echo $arrEmp_val["brnDesc"]."<br><br>";
					
					$arrDivision = $this->getDistinctDivCode($arrEmp,$arrEmp_val["empBrnCode"]);
					$arrDivision = substr($arrDivision, 0, strlen($arrDivision)-1);
					$exp_arrDivision = explode("=",$arrDivision);
			
					foreach($exp_arrDivision as $exp_arrDivision_val)
					{
						$exp_cddesc = explode("*", $exp_arrDivision_val);
						$this->Cell(5,7,"",'','0','L');
						$this->Cell(60,7,"DIVISION : ".$exp_cddesc[1],'','1','L');
						
						//echo "".$exp_cddesc[1]."<br>";
						
						$arrDept = $this->getDistinctDeptCode($arrEmp, $arrEmp_val["empBrnCode"], $exp_cddesc[0]);
						$arrDept = substr($arrDept, 0, strlen($arrDept)-1);
						$exp_arrDept = explode("=",$arrDept);
						
						foreach($exp_arrDept as $exp_arrDept_val)
						{
							$exp_divcddesc = explode("*", $exp_arrDept_val);
							$this->Cell(10,7,"",'','0','L');
							$this->Cell(50,7,trim(substr($exp_divcddesc[1], 0,20)) ,'1','','L');
							
							//echo "&nbsp;&nbsp;DEPT : ".$exp_divcddesc[1]."<br>";
							
							if($this->empRank!="0")
							{
								$arrRank = $this->getRankType($this->empRank);
								//echo $arrRank_disEmp_val["rankDesc"]."<br>";
									
								if($this->empStatus!="0")
								{
									if($this->empStatus=='RG')
										$empStat = "RG";
									elseif($this->empStatus=='PR')
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
											//echo "&nbsp;&nbsp;&nbsp;&nbsp;".$arrEmpdispEmp_val["empLastName"]."=".$arrEmpdispEmp_val["empFirstName"]."=".$arrEmpdispEmp_val["empDepCode"]."<br>";
										}
										
									}
									
									$this->Cell(150,7,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$empStat],'1','0','C');
									$this->Cell(30,7,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]],'1','0','C');
									
									$this->Cell(60,7,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]],'1','0','C');
									$this->Cell(30,7,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],'1','1','C');
							

								}
								else
								{
									$arr_empStat = array("RG", "PR", "CN");
									foreach($arr_empStat as $arr_empStat_val)
									{
										//echo $arr_empStat_val."<br>";
										foreach($arrEmp as $arrEmpdispEmp_val)
										{
											if(($arrRank["rankCode"]==$arrEmpdispEmp_val["empRank"]) && ($arrEmp_val["empBrnCode"]==$arrEmpdispEmp_val["empBrnCode"]) && ($exp_cddesc[0]==$arrEmpdispEmp_val["empDiv"])&& ($exp_divcddesc[0]==$arrEmpdispEmp_val["empDepCode"])&&($arr_empStat_val==$arrEmpdispEmp_val["empStat"]))
											{
												$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$arr_empStat_val]++;
												$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]]++;
												$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val]++;
												$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]]++;
												$grand[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
												//echo "&nbsp;&nbsp;&nbsp;&nbsp;".$arrEmpdispEmp_val["empLastName"]."=".$arrEmpdispEmp_val["empFirstName"]."=".$arrEmpdispEmp_val["empDepCode"]."<br>";
												
												$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
												$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]]++;
												$grand[$arrEmp_val["empBrnCode"]]++;
											}
										}
										$this->Cell(45,7,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]."-".$arr_empStat_val],'1','0','C');
									}
									$this->Cell(45,7,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank["rankCode"]],'1','0','C');
									
									foreach($arr_empStat as $arr_empStat_val)
										$this->Cell(22.5,7,$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val],'1','0','C');
									
									$this->Cell(22.5,7,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],'1','1','C');
								}
							}
							else
							{
								$arrRank =  $this->getRankType();
								foreach($arrRank as $arrRank_disEmp_val)
								{
									//echo $arrRank_disEmp_val["rankDesc"]."<br>";
									if($this->empStatus!="0")
									{
										if($this->empStatus=='RG')
											$empStat = "RG";
										elseif($this->empStatus=='PR')
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

												//echo "&nbsp;&nbsp;&nbsp;&nbsp;".$arrEmpdispEmp_val["empLastName"]."=".$arrEmpdispEmp_val["empFirstName"]."=".$arrEmpdispEmp_val["empDepCode"]."<br>";
												
											}
										}
										$this->Cell(30,7,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat],'1','0','C');
										$this->Cell(20,7,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]],'1','0','C');
										
									}
									else
									{
										$arr_empStat = array("RG", "PR", "CN");
										foreach($arr_empStat as $arr_empStat_val)
										{
											//echo $arr_empStat_val."<br>";
											foreach($arrEmp as $arrEmpdispEmp_val)
											{
												if(($arrRank_disEmp_val["rankCode"]==$arrEmpdispEmp_val["empRank"]) && ($arrEmp_val["empBrnCode"]==$arrEmpdispEmp_val["empBrnCode"]) && ($exp_cddesc[0]==$arrEmpdispEmp_val["empDiv"])&& ($exp_divcddesc[0]==$arrEmpdispEmp_val["empDepCode"])&&($arr_empStat_val==$arrEmpdispEmp_val["empStat"]))
												{
													$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
													$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]]++;
													$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val]++;
													$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]]++;
													$grand[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
													//echo "&nbsp;&nbsp;&nbsp;&nbsp;".$arrEmpdispEmp_val["empLastName"]."=".$arrEmpdispEmp_val["empFirstName"]."=".$arrEmpdispEmp_val["empDepCode"]."<br>";
													
													$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val]++;
													$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$arr_empStat_val]++;
													$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]]++;
													$grand[$arrEmp_val["empBrnCode"]]++;
												}
											}
											$this->Cell(12.5,7,$total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],'1','0','C');
										}
										$this->Cell(12.5,7,$sub_total_rank[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arrRank_disEmp_val["rankCode"]],'1','0','C');
									}
								}
								if($this->empStatus!="0")
								{
									if($this->empStatus=='RG')
										$empStat = "RG";
									elseif($this->empStatus=='PR')
										$empStat = "PR";
									else
										$empStat = "CN";
									
									$this->Cell(40,7,$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$empStat],'1','0','C');
									$this->Cell(30,7,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],'1','1','C');
								}
								else
								{
									$arr_empStat = array("RG", "PR", "CN");
									foreach($arr_empStat as $arr_empStat_val)
										$this->Cell(17.5,7,$sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]."-".$arr_empStat_val],'1','0','C');
									
									$this->Cell(17.5,7,$grand_total[$arrEmp_val["empBrnCode"]."-".$exp_cddesc[0]."-".$exp_divcddesc[0]],'1','1','C');
								}
						
							}
							
							}
						
						
					}
					
					$this->Ln();
					$this->Cell(60,7,'BRANCH GRAND TOTAL' ,'1','','L');
					
					if($this->empRank!="0")
					{
						$arrRank = $this->getRankType($this->empRank);
						
						//DISPLAY EMP. STATUS
						if($this->empStatus!="0")
						{
							if($this->empStatus=='RG')
								$disStat = "REG";
							elseif($this->empStatus=='PR')
								$disStat = "PR";
							else
								$disStat = "CON";
								
							$this->Cell(150,7,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$empStat],'1','0','C');
							$this->Cell(30,7,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],'1','0','C');
							
							$this->Cell(60,7,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],'1','0','C');
							$this->Cell(30,7,$grand[$arrEmp_val["empBrnCode"]],'1','1','C');
						}
						else
						{
							$arr_empStat = array("RG", "PR", "CN");
							foreach($arr_empStat as $arr_empStat_val)
								$this->Cell(45,7,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],'1','0','C');
							
							$this->Cell(45,7,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]],'1','0','C');
							
							foreach($arr_empStat as $arr_empStat_val)
								$this->Cell(22,7,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_disEmp_val["rankCode"]."-".$arr_empStat_val],'1','0','C');
							
							$this->Cell(22.5,7,$grand[$arrEmp_val["empBrnCode"]],'1','1','C');
						}
					}
					else
					{
						$arrRank =  $this->getRankType();
						
						foreach($arrRank as $arrRank_val)
						{
							if($this->empStatus!="0")
							{
								if($this->empStatus=='RG')
									$empStat = "RG";
								elseif($this->empStatus=='PR')
									$empStat = "PR";
								else
									$empStat = "CN";
									
								$this->Cell(30,7,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_val["rankCode"]."-".$empStat],'1','0','C');
								$this->Cell(20,7,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_val["rankCode"]],'1','0','C');
								
								
							}
							else
							{
								$arr_empStat = array("RG", "PR", "CN");
								foreach($arr_empStat as $arr_empStat_val)
									$this->Cell(12.5,7,$grand_sub_total_rank_stat[$arrEmp_val["empBrnCode"]."-".$arrRank_val["rankCode"]."-".$arr_empStat_val],'1','0','C');
								
								$this->Cell(12.5,7,$grand_sub_total_rank[$arrEmp_val["empBrnCode"]."-".$arrRank_val["rankCode"]],'1','0','C');
							}
							
						}
					
						if($this->empStatus!="0")
						{
							$this->Cell(40,7,$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$empStat],'1','0','C');
							$this->Cell(30,7,$grand[$arrEmp_val["empBrnCode"]],'1','1','C');
						}
						else
						{
						
							$arr_empStat = array("RG", "PR", "CN");
							foreach($arr_empStat as $arr_empStat_val)
								$this->Cell(17.5,7,$grand_sub_total_stat[$arrEmp_val["empBrnCode"]."-".$arr_empStat_val],'1','0','C');
							
							$this->Cell(17.5,7,$grand[$arrEmp_val["empBrnCode"]],'1','0','C');
							
							
						}
					}
					
					
					/*print_r($total);
					echo "<br><br>";
					print_r($sub_total_rank);
					echo "<br><br>";
					print_r($sub_total_rank_stat);
					echo "<br><br>";
					print_r($grand_total);
					*/
					$this->AddPage();
				}
				$disBranchCode = $arrEmp_val["empBrnCode"];
				
			}
		}
		
		function Footer()
		{
			$this->SetY(-20);
			$this->Cell(330,1,'','T');
			$this->Ln();
			$this->SetFont('Arial','B',9);
			$this->Cell(330,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
		}
	}
	
	
	
	$pdf = new PDF('L', 'mm', 'LEGAL');
	
	$empBrnCode = $_GET['empBrnCode'];
	$empDiv = $_GET['empDiv'];
	$empDept = $_GET['empDept'];
	$empRank = $_GET['empRank'];
	$empStatus = $_GET['empStatus'];
	$monthfr =  $_GET["monthfr"];
	$monthto =  $_GET["monthto"];
	
	if(($monthfr!="") && ($monthfr!=""))
		$pdf->disDate =  " as of ".date("m/d/Y", strtotime($monthfr))." - ".date("m/d/Y", strtotime($monthto));
	
	$pdf->empRank = $empRank;
	$pdf->empStatus = $empStatus;
	
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empBrnCode!="0") {$empBrnCode1 = " AND (empBrnCode = '{$empBrnCode}')";} else {$empBrnCode1 = "";}
	if ($empRank>"" && $empRank>0) {$empRank1 = " AND (empRank = '{$empRank}')";} else {$empRank1 = "";}
	if (($empStatus!="" ) && ($empStatus!="0")) {$empStatus1 = " AND (employmentTag = '{$empStatus}')";} else {$empStatus1 = "";}
	if (($monthfr!="" ) && ($monthto!="")) {$empDateHired = " AND (dateHired between '".date("m/d/Y", strtotime($monthfr))."' and '".date("m/d/Y", strtotime($monthto))."')";} else {$empDateHired = "";}
		
	
	
	 $sqlEmp = "SELECT empNo, empBrnCode, brnDesc, empDiv, divi.deptdesc as divDesc, empDepCode, 
	 				dept.deptDesc as deptDesc, empLastName, empFirstName, empMidName, empStat, empRank 
				FROM tblEmpMast empMast, tblBranch branch, tblDepartment divi, tblDepartment dept
				WHERE 
				(empMast.compCode = '".$_SESSION["company_code"]."')   
				$empDiv1 $empDept1 $empBrnCode1 $empRank1 $empStatus1 $empDateHired AND
				branch.compCode='".$_SESSION["company_code"]."' and empMast.empBrnCode=branch.brnCode AND 
				divi.compCode='".$_SESSION["company_code"]."' and empMast.empDiv = divi.divCode AND divi.deptLevel='1' AND
				dept.compCode='".$_SESSION["company_code"]."' and empMast.empDiv=dept.divCode and empMast.empDepCode=dept.deptCode 
					and dept.deptLevel='2' and empStat='RG'
				order by brnDesc, divDesc, deptDesc,  empLastName, empFirstName, empMidName ";   
	$resEmp = $payrollTypeObj->execQry($sqlEmp);	
	$arrEmp = $payrollTypeObj->getArrRes($resEmp);
	
	if($payrollTypeObj->getRecCount($resEmp)>0)
	{
		$pdf->AliasNbPages();
		$pdf->printedby = $payrollTypeObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
		$pdf->AddPage();
		$pdf->displayContent($arrEmp);
	}	
	
$pdf->Output('Manpower Head Count By Dept.pdf','D');
?>