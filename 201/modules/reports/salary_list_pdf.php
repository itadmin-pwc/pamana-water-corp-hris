<?php
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
include("../../../includes/pdf/fpdf.php");
class PDF extends FPDF
{
	var $printedby;
	var $company;
	var $rundate;
	var $title;
	var $reportlabel;
	var $codeName;
	
	function Header()
	{
		//260
		$this->SetFont('Arial','','10'); 
		$this->Cell(70,5,"Run Date: " . $this->rundate,'0');
		$this->Cell(140,5,$this->company,'0');
		$this->Cell(50,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');		
		$this->Ln();
		
		if ($_GET['from'] != "" && $_GET['to'] != "") 
		{
			$fromdt = $_GET['from'];
			$todt = $_GET['to'];
			$dateStr = " from : ".$fromdt." to : ".$todt ;
		} 
		$this->Cell(70,5,"Report ID: SALINCREASE");
		$repName = "Salary Increase Report By";
		$this->Cell(140,5, $repName." ".$this->codeName." ".$dateStr,'0');
		
		
		
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial','B','10'); 
		if ( $_GET['code'] != "6" ) 
		{
			
			$this->Cell(12.5,6,'#',0,'','C');
			$this->Cell(40,6,'EMP. NO.',0);
			$this->Cell(65,6,'EMPLOYEE NAME',0);
			$this->Cell(32,6,'EFF. DATE',0);
			$this->Cell(50,6,'POSITION',0);
			$this->Cell(32.5,6,'OLD SALARY',0,0,'R');
			$this->Cell(32.5,6,'NEW SALARY',0,0,'R');
			$this->Cell(32.5,6,'AMT INCREASE',0,0,'R');
			$this->Cell(32,6,'% Increase',0,0,'R');
			$this->Ln();
			
		} 
		else
		{
			
			$this->Cell(12.5,6,'#',0,'','C');
			$this->Cell(40,6,'EMP. NO.',0);
			$this->Cell(65,6,'EMPLOYEE NAME',0);
			$this->Cell(32,6,'EFF. DATE',0);
			$this->Cell(50,6,'OLD POSITION',0,0,'R');
			$this->Cell(50,6,'NEW POSITION',0,0,'R');
			$this->Cell(40,6,'OLD DIVISION',0,0,'R');
			$this->Cell(40,6,'NEW DIVISION',0,0,'R');
				
			$this->Ln();
		}
	}
	
	function getpositionwil($where) 
	{
		$sqlpos="Select * from tblPosition $where";
		$res=$this->execQry($sqlpos);
		$arr=$this->getSqlAssoc($res);
		return $arr;	
	}	    
	
	function getDivDescArt($compCode, $empDiv){
		$qryGetDiv = "SELECT * FROM tblDepartment
					     WHERE compCode = '{$compCode}'
					     AND divCode = '{$empDiv}' 
						 AND deptLevel = 1 
						 AND deptStat = 'A'
					    ";
		$resGetDiv = $this->execQry($qryGetDiv);
		return $this->getSqlAssoc($resGetDiv);
	}
	
	function DisplayDetails($arrEmpList)
	{
		$this->SetFont('Arial','','11'); 
		$emp_ctr = 1;
		$oldrate = 0;
		$newrate = 0;
		$amntincrease = 0;
		$percentincrease = 0;
		foreach($arrEmpList as $arrEmpList_val)
		{
			if($disEmpNo!=$arrEmpList_val["empNo"])
			{
				$this->Cell(12.5,6,$emp_ctr.".",0,'','C');
				$this->Cell(40,6,$arrEmpList_val["empNo"],0);
				$this->Cell(65,6,$arrEmpList_val["empLastName"].", ".$arrEmpList_val["empFirstName"]." ".$arrEmpList_val["empMidName"][0].".",0);
				$this->Cell(32,6,date("m/d/Y",strtotime($arrEmpList_val["effectivityDate"]) ),0, 0);
				
				if ( $_GET['code'] != "6" ) 
				{
					$this->Cell(50,6,trim(substr($arrEmpList_val["posDesc"], 0,15)),0);
					$this->Cell(32.5,6,number_format($arrEmpList_val["old_empMrate"],2),0,0,'R');
					$this->Cell(32.5,6,number_format($arrEmpList_val["new_empMrate"],2),0,0,'R');
					$oldrate = $oldrate + $arrEmpList_val["old_empMrate"];
					$newrate = $newrate + $arrEmpList_val["new_empMrate"];
				}
				else
				{
					$arr_oldPosDescDescr = $this->getpositionwil(" where compCode='".$_SESSION["company_code"]."' and posCode='".$arrEmpList_val["old_posCode"]."'");
					$arr_newPosDescDescr =   $this->getpositionwil(" where compCode='".$_SESSION["company_code"]."' and posCode='".$arrEmpList_val["new_posCode"]."'");
					$this->Cell(50,6,trim(substr($arr_oldPosDescDescr["posDesc"], 0,15)),0,0,'R');
					$this->Cell(50,6,trim(substr($arr_newPosDescDescr["posDesc"], 0,15)),0,0,'R');
				}
				
	
				if ( $_GET['code'] != "6" ) 
				{
					$this->Cell(32.5,6,number_format($arrEmpList_val["amtincrease"],2),0,0,'R');
					$this->Cell(32.5,6,number_format($arrEmpList_val["percentincrease"],2),0,1,'R');
					$amntincrease = $amntincrease + $arrEmpList_val["amtincrease"];
					$percentincrease = $percentincrease + $arrEmpList_val["percentincrease"];
				}
				else
				{
					$arr_oldDiv = $this->getDivDescArt($_SESSION["company_code"], $arrEmpList_val["old_divCode"]);
					$arr_newDiv = $this->getDivDescArt($_SESSION["company_code"], $arrEmpList_val["new_divCode"]);
					$this->Cell(40,6,trim(substr($arr_oldDiv["deptDesc"], 0,15)),0,0,'R');
					$this->Cell(40,6,trim(substr($arr_newDiv["deptDesc"], 0,15)),0,1,'R');
				}
				$emp_ctr++;
			}
			$disEmpNo = $arrEmpList_val["empNo"];
		}
		if ( $_GET['code'] != "6" ) 
		{
				$this->SetFont('Arial','B','11'); 
				$this->Cell(12.5,6,"",0,'','C');
				$this->Cell(40,6,"",0);
				$this->Cell(65,6,"",0);
				$this->Cell(32,6,"",0, 0);
				$this->Cell(50,6,"TOTAL :",0);
				$this->Cell(32.5,6,number_format($oldrate,2),'T',0,'R');
				$this->Cell(32.5,6,number_format($newrate,2),'T',0,'R');
				$this->Cell(32.5,6,number_format($amntincrease,2),'T',0,'R');
				$this->Cell(32.5,6,number_format((($percentincrease/($emp_ctr-1))),2),'T',0,'R');
				$this->Ln();

		}
		else{
				$this->Cell(12.5,6,"",0,'','C');
				$this->Cell(40,6,"",0);
				$this->Cell(65,6,"",0);
				$this->Cell(32,6,"",0, 0);
				$this->Cell(50,6,"",0);
				$this->Cell(32.5,6,"",0,0,'R');
				$this->Cell(32.5,6,"",0,0,'R');
				$this->Cell(32.5,6,"",0,0,'R');
				$this->Cell(32.5,6,"",0,0,'R');
				$this->Ln();
		}
	}
	
	function Data_CBA_MERIT($ctr,$empNo,$empName,$payCat,$effectivitydate,$position,$old_salary,$new_salary,$amtincrease) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(30,6,$empName,0);
		$this->Cell(30,6,date("m/d/Y",strtotime($effectivitydate)),0);
		$this->Cell(30,6,$position,0);
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($payCat == 1) 
				$old_salary = $new_salary = $amtincrease = "--";
		}
		
		$this->Cell(25,6,$old_salary,0,0,'R');
		$this->Cell(25,6,$new_salary,0,0,'R');
		$this->Cell(30,6,$amtincrease,0,0,'R');
		$this->Ln();	
	}
	function Data_Salary($ctr,$empNo,$empName,$payCat,$effectivitydate,$position,$old_salary,$new_salary,$amtincrease,$percntincrease) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(30,6,$empName,0);
		$this->Cell(30,6,date("m/d/Y",strtotime($effectivitydate)),0);
		$percntincrease = number_format($percntincrease,2) . "%";
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($payCat == 1) 
				$old_salary = $new_salary = $amtincrease = $percntincrease = "--";
		}
		$this->Cell(25,6,$old_salary,0,0,'R');
		$this->Cell(25,6,$new_salary,0,0,'R');
		$this->Cell(30,6,$amtincrease,0,0,'R');
		$this->Cell(30,6,$percntincrease ,0,0,'R');
		$this->Ln();	
	}
	function Data_Promotion($ctr,$empNo,$empName,$payCat,$effectivitydate,$old_position,$new_position,$old_div,$new_div,$old_salary,$new_salary) {
		$this->SetFont('Courier','','9'); 
		$this->Cell(8,6,$ctr,0,'','C');
		$this->Cell(20,6,$empNo,0);
		$this->Cell(30,6,$empName,0);
		$this->Cell(30,6,date("m/d/Y",strtotime($effectivitydate)),0);
		$this->Cell(40,6,$old_position,0,0);
		$this->Cell(40,6,$new_position,0,0);
		$this->Cell(40,6,$old_div,0,0);
		if (!in_array(1,explode(',',$_SESSION['user_payCat'])))  {
			if ($payCat == 1) 
				$old_salary = $new_salary = "--";
		}
		$this->Cell(45,6,$new_div,0,0);
		$this->Cell(20,6,$old_salary,0,0,'R');
		$this->Cell(10,6,'',0,0,'R');
		$this->Cell(20,6,$new_salary,0,0,'R');
		$this->Ln();	
	}
	
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(335,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Printed By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
}
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();



$pdf=new PDF('L', 'mm', 'LEGAL');
//Column titles
//Data loading


$cmbDiv = $_GET['empDiv'];
$empDept = $_GET['empDept'];
$empSect = $_GET['empSect'];
$pafType = $_GET['pafType'];


$empNo1 = ($empNo>""?" AND (tblEmpMast.empNo LIKE '{$empNo}%')":"");
$cmbDiv1 = ($cmbDiv>"" && $cmbDiv>0 ? " AND (empDiv = '{$cmbDiv}')":"");
$empDept1 = ($empDept>"" && $empDept>0 ? " AND (empDepCode = '{$empDept}')":"");
$empSect1 = ($empSect>"" && $empSect>0 ? " AND (empSecCode = '{$empSect}')":"");
$empName1=($empName>""?" AND ($nameType LIKE '{$empName}%')":"");


$type = ($_GET['type']==1) ? "hist" : "";
//$pafStat = ($_GET['type']==1) ? " AND stat = 'R'" : "";


if ($_GET['code']!=0) {

	$arrReason = $psObj->getReasonCd($_GET['code'],$sessionVars['compCode']);
	$codeName = $arrReason['reasonDesc'];
	if($_GET['code']!=6)
		$reasonfilter = "and reasonCd = '{$arrReason['reasonCd']}'";
	
	
}


if ($_GET['from'] != "" && $_GET['to'] != "") 
{
	$fromdt = date('Y-m-d',strtotime($_GET['from']));
	$todt = date('Y-m-d',strtotime($_GET['to']));
	if($_GET['code']!=6)
		$datefilter = " and tblPAF_PayrollRelated$type.dateadded >= '$fromdt' and tblPAF_PayrollRelated$type.dateadded <='$todt' and new_empMrate>0";
	else
		$datefilter = " and tblPAF_Position$type.dateadded >= '$fromdt' and tblPAF_Position$type.dateadded <='$todt'";
}

$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();
$pdf->title = $codeName . " Report";
$pdf->codeName = $codeName;
$pdf->type = $type;

if($_GET['code']!=6)
	$fieldName = ", old_empMrate, new_empMrate, tblPAF_PayrollRelated$type.new_empMrate - tblPAF_PayrollRelated$type.old_empMrate AS amtincrease, (((new_empMrate - old_empMrate) / old_empMrate) * 100) as percentincrease";
else
	$fieldName = ", old_posCode, new_posCode, old_divCode, new_divCode";
	
if($_GET['code']!=6)
{
	$add_tbName = ",tblPAF_PayrollRelated$type ";
	$add_fieldName = "tblPAF_PayrollRelated$type";
}
else
{
	$add_tbName = ",tblPAF_Position$type ";
	$add_fieldName = "tblPAF_Position$type";
}
	
$qryIntMaxRec ="SELECT 
				empPayCat,
				tblEmpMast.empNo, 
				tblPosition.posDesc, 
				tblEmpMast.empLastName, 
				tblEmpMast.empFirstName, 
				tblEmpMast.empMidName,deptDesc ,
				effectivityDate
				$fieldName
				FROM 
				tblEmpMast,tblPosition,tblDepartment $add_tbName
				WHERE
				tblEmpMast.compCode='".$_SESSION["company_code"]."' and tblPosition.compCode='".$_SESSION["company_code"]."' and tblDepartment.compCode='".$_SESSION["company_code"]."'  and
				tblEmpMast.compCode = tblPosition.compCode and
				tblEmpMast.compCode = tblDepartment.compCode and
				tblEmpMast.empPosId = tblPosition.posCode AND 
				tblEmpMast.empDiv = tblDepartment.divCode and (tblDepartment.deptLevel = '1') AND
				empBrnCode IN (Select brnCode from tblUserBranch where compCode='".$_SESSION["company_code"]."' and empNo='".$_SESSION['employee_number']."') AND
				tblEmpMast.empNo IN (Select empNo from tblPAF_PayrollRelated$type where compCode='".$_SESSION["company_code"]."' and new_empMrate>0) 
				
				and $add_fieldName.compCode='".$_SESSION["company_code"]."'
				and $add_fieldName.compCode=tblEmpMast.compCode 
				and $add_fieldName.empNo = tblEmpMast.empNo
				and empStat='RG' 
				and employmentTag IN ('RG','PR','CN')
				
				 
				$pafStat
				$reasonfilter
				$datefilter
				$cmbDiv1 $empDept1 $empSect1
				order by empLastName,empFirstName,empMidName";
$resEmpList = $psObj->execQry($qryIntMaxRec);
$arrEmpList = $psObj->getArrRes($resEmpList);

if(count($arrEmpList)>=1)
{
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$arrprintedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	
	$pdf->DisplayDetails($arrEmpList);
}


$pdf->Output('SALARY_INCREASE_PROOFLIST.pdf','D');



?>
