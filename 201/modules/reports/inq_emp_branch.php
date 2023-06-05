<?php
####Include files
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("timesheet_obj.php");
include("../../../includes/pdf/fpdf.php");

####Create class
class PDF extends FPDF
{

####Variable declaration	
	var $arrEmpInfo; 
	var $dispDivDesc;
	var $dispDeptDesc;
	var $dispSectDesc;
	var $otherInfo;
	var $catName;
	var $locName;
	var $brnchName;
	var $postion;
	var $empContacts;
	var $curdate;
	var $compName;
	var $printedby;
	var $arrEmpOtherInfos;
	var $countOtherInfos;
	var $branch;
	var $empBranch;
	
####Set up header	
	function Header()
	{
		$this->Cell(80,5,$this->compName);
		$this->Cell(70,5,"Run Date: ".$this->curdate);
		$this->Cell(30,5,'Page '.$this->PageNo().' of {nb}',0,1);
		$this->Cell(80,5,$this->branch);
		$this->Cell(70,5,"Employee Personnel Information",0,0);		
		$this->Cell(40,5,"Report ID: EMPLN001",0,1);
		$this->Ln(5);
	}
	
####Set up to get branches	
	function empBranches(){
		foreach($this->empBranch as $employeeBranch){
				$this->AddPage();
				$this->empInfo($employeeBranch,$otherInfo);
			}	
	}

####Set up to show data/details
	function empInfo($employeeBranch){
		$otherInfo = $this->empOtherInfos($employeeBranch['empNo']);
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"G E N E R A L    I N F O R M A T I O N",'TB',1);
		$this->SetFont('Courier', '', '10');
		$this->Cell(66,5,"Employee Number           :",0,0);
		$this->Cell(66,5,$employeeBranch['empNo'],0,1);
		$this->Cell(66,5,"Last Name                 :",0,0);
		$this->Cell(66,5,$employeeBranch['empLastName'],0,1);
		$this->Cell(66,5,"First Name                :",0,0);
		$this->Cell(66,5,$employeeBranch['empFirstName'],0,1);
		$this->Cell(66,5,"Middle Name               :",0,0);
		$this->Cell(66,5,$employeeBranch['empMidName'],0,1);
		$this->Ln();

		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"C O N T A C T   I N F O R M A T I O N",'TB',1);
		$this->SetFont('Courier', '', '10');
		$this->address = $this->empAddress($employeeBranch['empNo']);
		foreach($this->address as $empAddress){
				$address1=$empAddress['empAddr1'];
				$address2=$empAddress['empAddr2'];
				$municipality=$empAddress['municipalityDesc'];
				$province=$empAddress['provinceDesc'];
				$eperson=$empAddress['empECPerson'];
				$enumber=$empAddress['empECNumber'];
			}
		$this->Cell(66,5,"Home No./St.              :",0,0);
		$this->Cell(66,5,$address1,0,1);
		$this->Cell(66,5,"Barangay                  :",0,0);
		$this->Cell(66,5,$address2,0,1);
		$this->Cell(66,5,"Municipality/City         :",0,0);
		$this->Cell(66,5,$municipality,0,1);
		$this->Cell(66,5,"Province/Region           :",0,0);
		$this->Cell(66,5,$province,0,1);
		$this->Cell(66,5,"Emergency Contact Person  :",0,0);
		$this->Cell(66,5,$eperson,0,1);
		$this->Cell(66,5,"Contact Number            :",0,0);
		$this->Cell(66,5,$enumber,0,1);
		$this->empContacts	= $this->empContactswil($employeeBranch['empNo']);
		foreach ($this->empContacts as $empContactsValue) {
			$DescLen=26-strlen($empContactsValue['contactDesc']);
			$DescSpace="";
			for ($ctr=0; $ctr<$DescLen; $ctr++) {
				$DescSpace .=" ";
			}
			$this->Cell(66,5,$empContactsValue['contactDesc']."$DescSpace:",0,0);
			$this->Cell(66,5,$empContactsValue['contactName']."1",0,1);
		}
		$this->Ln();
		
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"E M P L O Y E E   P R O F I L E",'TB',1);		
		$this->SetFont('Courier', '', '10');
		$h="";
		$w="";
		if(trim($employeeBranch['empHeight'])!=""){
			if(strpos("'",$employeeBranch['empHeight'])===1){
				$h=$employeeBranch['empHeight'];
				}
			else{
				$h=$employeeBranch['empHeight'] . " ft";	
				}
		}
		if(trim($employeeBranch['empWeight'])!=""){
			if(strpos("K",$employeeBranch['empWeight'])===1 || strpos("k",$employeeBranch['empWeight'])===1 || strpos("K",$employeeBranch['empWeight'])===1 || strpos("g",$employeeBranch['empWeight'])===1){
				$w=$employeeBranch['empWeight'];
				}
			else{
				$w=$employeeBranch['empWeight'] . " kg/lbs";	
				}	
		}
		$this->Cell(66,5,"Gender                    :",0,0);
		$this->Cell(66,5,$otherInfo['empSex'],0,1);
		$this->Cell(66,5,"Nick Name                 :",0,0);
		$this->Cell(66,5,$employeeBranch['empNickName'],0,1);
		$this->Cell(66,5,"Birth Place               :",0,0);
		$this->Cell(66,5,$employeeBranch['empBplace'],0,1);
		$this->Cell(66,5,"Birthday                  :",0,0);
		$this->Cell(66,5,$this->valDate($employeeBranch['empBday']),0,1);
		$this->Cell(66,5,"Civil Status              :",0,0);
		$this->Cell(66,5,$otherInfo['empMarStat'],0,1);
		$this->Cell(66,5,"Height                    :",0,0);
		$this->Cell(66,5,$h,0,1);
		$this->Cell(66,5,"Weight                    :",0,0);
		$this->Cell(66,5,$w,0,1);
		$this->Cell(66,5,"Citizenship               :",0,0);
		$this->Cell(66,5,$otherInfo['citizenDesc'],0,1);
		$this->Cell(66,5,"Religion                  :",0,0);
		$this->Cell(66,5,$otherInfo['relDesc'],0,1);
		$this->Cell(66,5,"Blood Type                :",0,0);
		$this->Cell(66,5,$employeeBranch['empBloodType'],0,1);
		$this->Cell(66,5,"TAX Exemption             :",0,0);
		$this->Cell(66,5,$otherInfo['teuDesc'],0,1);
		$this->Cell(66,5,"SSS Number                :",0,0);
		$this->Cell(66,5,$employeeBranch['empSssNo'],0,1);
		$this->Cell(66,5,"Phil Health Number        :",0,0);
		$this->Cell(66,5,$employeeBranch['empPhicNo'],0,1);
		$this->Cell(66,5,"TIN Number                :",0,0);
		$this->Cell(66,5,$employeeBranch['empTin'],0,1);
		$this->Cell(66,5,"Pag-ibig Number           :",0,0);
		$this->Cell(66,5,$employeeBranch['empPagibig'],0,1);
		$this->Ln();

		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"E D U C A T I O N A L   I N F O R M A T I O N",'TB',1);	
		$this->Cell(195,6,"School Type   School Name                                    License No.  License Name",0,1);	
		$this->SetFont('Courier', '', '8');
		$this->showEducs($employeeBranch['empNo']);
		$this->Ln();
		
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"E M P L O Y M E N T   B A C K G R O U N D",'TB',1);	
		$this->Cell(195,6,"Company                     Position Title                   From          To",0,1);	
		$this->SetFont('Courier', '', '8');
		$this->showEmployment($employeeBranch['empNo']);
		$this->Ln();
		
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"P E R F O R M A N C E    R E C O R D S",'TB',1);	
		$this->Cell(195,6,"Numerical/Adjective Rating   Purpose       Review Period     New Rate     Remarks",0,1);	
		$this->SetFont('Courier', '', '8');
		$this->showPerformance($employeeBranch['empNo']);
		$this->Ln();	
			
		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"T R A I N I N G    R E C O R D S",'TB',1);	
		$this->Cell(195,6,"Title                       Cost      Bond       Training Period      Bond Effectivity",0,1);	
		$this->SetFont('Courier', '', '8');
		$this->showTrainings($employeeBranch['empNo']);
		$this->Ln();		

		$this->SetFont('Courier', 'B', '10');
		$this->Cell(195,6,"D I S C I P L I N A R Y   A C T I O N / C O N D U C T",'TB',1);	
		$this->Cell(195,6,"Violation                               Offense         Sanction        Effectivity",0,1);	
		$this->SetFont('Courier', '', '8');
		$this->showDisciplinary($employeeBranch['empNo']);

		$this->SetFont('Courier', '', '10');
		$this->Cell(195,5,"*** End of Report ****",0,1,'C');
		$this->Ln();	
		
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
	}
	
####Function to show educational background	
	function showEducs($empNum){
	$resEduc = $this->getEducationalBackground($empNum);	
	if($this->getRecCount($resEduc)>0){
		$resQry=$this->getArrRes($resEduc);
		foreach($resQry as $EducVal => $empEducation){
			$type=$empEducation['schoolType'];
			$school=$empEducation['typeDesc'];
			$licensenum=$empEducation['licenseNumber'];
			$licesename=$empEducation['licenseName'];
					$this->Cell(30,5,$type,0,0);
					$this->Cell(99,5,$school,0,0);
					$this->Cell(28,5,$licensenum,0,0);
					$this->Cell(30,5,$licesename,0,1);		

		}
	}
	else{
			$this->Cell(200,5,"No Education Information Found!",0,0,'C');
		}
	}

####Function to show employment history
	function showEmployment($empNum){
		$resEmployment=$this->getEmploymentHistory($empNum);
		if($this->getRecCount($resEmployment)>0){
				$resQry=$this->getArrRes($resEmployment);
				foreach($resQry as $EmpVal => $empEmployment){
					$company=$empEmployment['companyName'];
					$position=$empEmployment['employeePosition'];
					$dfrom=$empEmployment['startDate'];
					$dto=$empEmployment['endDate'];		
					  // Mark start coords
						$x = $this->GetX();
						$y = $this->GetY();
						$y1 = $this->GetY();
						$this->MultiCell(60,5,$company,0,L);
				        $y2 = $this->GetY();
				        $yH = $y2 - $y1;
				        $this->SetXY($x + 60, $this->GetY() - $yH);				   
						//$this->Cell(60,5,$company,0,0);
						$this->Cell(65,5,$position,0,0);
						$this->Cell(30,5,$this->valDate($dfrom),0,0);
						$this->Cell(30,5,$this->valDate($dto),0,1);
						if(strlen($company)>33){
						$this->Ln();
						}
					}
			}
		else{
			$this->Cell(200,5,"No Employment Background Found!",0,0,'C');
			}		
	}	

####Function to show disciplinary actions
	function showDisciplinary($empNum){
		$resDisciplinary=$this->getDisciplinary($empNum);
		if($this->getRecCount($resDisciplinary)>0){
				$resQry=$this->getArrRes($resDisciplinary);
				foreach($resQry as $EmpVal => $empDisciplinary){
					$violation=$empDisciplinary['violation'];
					$arroff=array('','FIRST OFFENSE','SECOND OFFENSE','THIRD OFFENSE','FOURTH OFFENSE','FIFTH OFFENSE','SIXTH OFFENSE');
					  foreach($arroff as $offenses=>$offdata){
						  if($empDisciplinary['offense']==$offenses){
							 	$offense=$offdata;
							  }
						  }

					$arrsanc=array('','WRITTEN WARNING','1 DAY SUSPENSION','3 DAYS SUSPENSION','ONE WEEK SUSPENSION','TWO WEEKS SUSPENSION','30 DAYS SUSPENSION','DISMISSAL');
					  foreach($arrsanc as $sanctions=>$sancdata){
						  if($empDisciplinary['sanction']==$sanctions){
							  $sanction=$sancdata;
							  }
						  }

					$suspension=$this->valDate($empDisciplinary['suspensionFrom']) . " - " . $this->valDate($empDisciplinary['suspensionTo']);						
					  // Mark start coords
						$x = $this->GetX();
						$y = $this->GetY();
						$y1 = $this->GetY();
						$this->MultiCell(80,5,$violation,0,L);
				        $y2 = $this->GetY();
				        $yH = $y2 - $y1;
				        $this->SetXY($x + 80, $this->GetY() - $yH);				   
						$this->Cell(34,5,$offense,0,0);
						$this->Cell(35,5,$sanction,0,0);
						$this->Cell(42,5,$suspension,0,1);
						if(strlen($violation)>46){
						$this->Ln();
							}
							
					}
			}
		else{
			$this->Cell(200,5,"No Disciplinary Action/Conduct Found!",0,0,'C');
			}		
	}	

####Function to show performance
	function showPerformance($empNum){
		$resPerformance=$this->getPerformance($empNum);
		if($this->getRecCount($resPerformance)>0){
			$resQry=$this->getArrRes($resPerformance);
			foreach($resQry  as $empPer=>$performance){
				$date=$this->valDate($performance['performanceFrom']) . " - " . $this->valDate($performance['performanceTo']);
				$newRate=$performance['new_empDrate'];
				$rem=$performance['remarks'];
				$arrNumerical=array('','96% - 100%','91% - 95%','85% - 90%','80% - 84%','80% and below');
				foreach($arrNumerical as $performances=>$perdata){
					if($performance['performanceNumerical']==$performances){
						$perf=$perdata;
						}
					}
				$arrAdjective=array('','Outstanding','Above Average','Average','Below Average','Poor');
				foreach($arrAdjective as $empAdjective=>$adjectives){
					if($performance['performanceAdjective']==$empAdjective){
						$adject=$adjectives;
						}
					}	
				$arrPurpose=array('','Probationary','Regularization','Merit Increase','Salary Alignment','Promotion');
				foreach($arrPurpose as $emppurpose=>$purdata){
					if($performance['performancePurpose']==$emppurpose){
						$purposes=$purdata;
						}
					}	
			$this->Cell(60,5,$perf."/".$adject,0,0);
			$this->Cell(25,5,$purposes,0,0);
			$this->Cell(48,5,$date,0,0);	
			$this->Cell(20,5,$newRate,0,0);					
			$this->MultiCell(40,5,$rem,0,1);												
			}	
		}	
		else{
			$this->Cell(200,5,"No Performance Record Found",0,0,'C');
			}
	}

####Functions to show trainings
	function showTrainings($empNum){
		$resTrainings=$this->getTrainings($empNum);
		if($this->getRecCount($resTrainings)>0){
			$resQry=$this->getArrRes($resTrainings);
			foreach($resQry as $empTrain=>$trainings){
				$dates=$this->valDate($trainings['trainingFrom'])." - ".$this->valDate($trainings['trainingTo']);
				$dateeffectivity=$this->valDate($trainings['effectiveFrom'])." - ".$this->valDate($trainings['effectiveTo']);
				$title=$trainings['trainingTitle'];
				$cost=$trainings['trainingCost'];
				  // Mark start coords
				  $x = $this->GetX();
				  $y = $this->GetY();
        		  $y1 = $this->GetY();
					$arrYears=array('','1 year','2 years','3 years','4 years','5 years');
					foreach($arrYears as $trainingsYears=>$years){
						if($trainings['trainingBond']==$trainingsYears){
							$trainingBonds=$years;
							}
						}   
				  $this->MultiCell(60, 5, $title, 0,L); 
				  $y2 = $this->GetY();
				  $yH = $y2 - $y1;
				  $this->SetXY($x + 60, $this->GetY() - $yH);				   
				  $this->Cell(20,5,$cost,0,0);
				  $this->Cell(25,5,$trainingBonds,0,0);
				  $this->Cell(45,5,$dates,0,0);
				  $this->Cell(35,5,$dateeffectivity,0,1); 
				  if(strlen($title)>34){
				  $this->Ln();
					  }
				}
			}	
	}


####Functions to format date
	function valDate($date) {
		if ($date=="") {
			$newDate = "";
		} else {
			$newDate = date("m/d/Y",strtotime($date));
		}
		return $newDate;
	}
	
####Set up footer	
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(195,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(195,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}	
	
}

####Initialize objects
define('FPDF_FONTPATH','../../../includes/pdf/font/');
$pdf=new PDF('P', 'mm', 'LEGAL');
$pdf->SetFont('Courier', '', '10');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();

####Query to get branch
$pdf->branch 			= $psObj->getInfoBranch($_GET['qryBranch'],$_SESSION['company_code']);
$pdf->empBranch			= $psObj->getEmpBranch($_SESSION['company_code']," and empBrnCode='".$_GET['qryBranch']."' and empStat NOT IN ('RS', 'IN', 'TR')");
$pdf->curdate			= $psObj->currentDateArt();
$pdf->compName			= $psObj->getCompanyName($_SESSION['company_code']);
$pdf->AliasNbPages();
$pdf->printedby 		= $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate			= $psObj->currentDateArt();
$pdf->empBranches();

####Ouput report
$pdf->Output('EMPLOYEE_INFORMATION.pdf','D');
?>