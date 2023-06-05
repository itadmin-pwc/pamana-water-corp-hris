<?
################### INCLUDE FILE #################
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	include("../../../includes/pdf/fpdf.php");
	define('FPDF_FONTPATH','../../../includes/pdf/font/');
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$compCode = $_SESSION['company_code'];
	$inqTSObj->compCode      = $compCode;
	$inqTSObj->empNo         = $_GET['empNo'];
	$inqTSObj->empName       = $_GET['empName'];
	$inqTSObj->empDiv        = $_GET['empDiv'];
	$inqTSObj->empDept       = $_GET['empDept'];
	$inqTSObj->empSect       = $_GET['empSect'];
	$inqTSObj->orderBy       = $_GET['orderBy'];
	$empNo         			= $_GET['empNo'];
	$empName       			= $_GET['empName'];
	$empDiv        			= $_GET['empDiv'];
	$empDept       			= $_GET['empDept'];
	$empSect       			= $_GET['empSect'];
	$from					= $_GET['from'];
	$to						= $_GET['to'];
################ GET TOTAL RECORDS ###############

############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200
############################ LETTER LANDSCAPE TOTAL WIDTH = 265
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
	$pdf = new FPDF('L', 'mm', 'LEGAL');
	$pdf->SetFont('Courier', '', '8');
	$TOTAL_WIDTH   			= 335;
	$TOTAL_WIDTH_2 			= 53;
	$TOTAL_WIDTH_3 			= 88;
	$SPACES        			= 6;
	$pdf->TOTAL_WIDTH       = 335;
	$pdf->TOTAL_WIDTH_2     = 53;
	$pdf->TOTAL_WIDTH_3     = 88;
	$pdf->SPACES	       	= 6;
############################ Q U E R Y ##################################
	if ($empNo>"") {$empNo1 = " AND (empNo LIKE '{$empNo}%')";} else {$empNo1 = "";}
	//if ($empName>"") {$empName1 = " AND (empLastName LIKE '{$empName}%' OR empFirstName LIKE '{$empName}%' OR empMidName LIKE '{$empName}%')";} else {$empName1 = "";}
	if ($empDiv>"" && $empDiv>0) {$empDiv1 = " AND (empDiv = '{$empDiv}')";} else {$empDiv1 = "";}
	if ($empDept>"" && $empDept>0) {$empDept1 = " AND (empDepCode = '{$empDept}')";} else {$empDept1 = "";}
	if ($empSect>"" && $empSect>0) {$empSect1 = " AND (empSecCode = '{$empSect}')";} else {$empSect1 = "";}
	if ($from != "" && $to!= "" ) {
		$empStatDatefilter = " AND effectivitydate between '$from' AND '$to'";
		$dt = "Resigned Date $from - $to";
	}	
		
	$sqlDiv = "Select deptShortDesc,divCode from tblDepartment where deptLevel='1' and deptStat='A' and compCode='{$_SESSION['company_code']}'";
	$resDiv = $inqTSObj->execQry($sqlDiv);
	$arrDiv = $inqTSObj->getArrRes($resDiv);
	$qryEmpList = "SELECT DISTINCT tblPAF_EmpStatushist.empNo, tblPAF_EmpStatushist.effectivitydate, tblEmpMast.empLastName, 
					tblEmpMast.empFirstName, tblEmpMast.empMidName, tblEmpMast.dateHired, tblEmpMast.empPayType, tblEmpMast.empDrate, 
					tblEmpMast.empMrate, tblBranch.brnShortDesc, tblPosition.posShortDesc, tblEmpMast.employmentTag
					FROM tblPAF_EmpStatushist 
					INNER JOIN tblEmpMast ON tblPAF_EmpStatushist.compCode = tblEmpMast.compCode 
					AND tblPAF_EmpStatushist.empNo = tblEmpMast.empNo 
					INNER JOIN tblBranch ON tblPAF_EmpStatushist.compCode = tblBranch.compCode 
					AND tblEmpMast.empBrnCode = tblBranch.brnCode 
					INNER JOIN tblPosition ON tblPAF_EmpStatushist.compCode = tblPosition.compCode 
					AND tblEmpMast.empPosId = tblPosition.posCode
					WHERE ((tblEmpMast.empStat = 'RS') or (tblEmpMast.empStat = 'IN')) 
					AND ((tblEmpMast.dateResigned is not null ) 
					OR (tblEmpMast.endDate is not null)) $empStatDatefilter 
					AND (tblPAF_EmpStatushist.compCode = '{$compCode}') 
					AND empBrnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' 
					AND empNo='{$_SESSION['employee_number']}')
					$empNo1 $status $empStatDatefilter $empName1 $empDiv1 $empName1 $empDept1 $empSect1
					ORDER BY tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName
					 ";
	$resEmpList = $inqTSObj->execQry($qryEmpList);
	$arrEmpList = $inqTSObj->getArrRes($resEmpList);
HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$ctr=1;
$GTot = 0;
############################### LOOPING THE PAGES ###########################
foreach ($arrEmpList as $val){
	$name = $val['empLastName'] . ", " . $val['empFirstName']." ".$val['empMidName'][0].".";			
	if ($val['empPayType']=="M") 
		$Salary = $val['empMrate']."/month";
	elseif ($val['empPayType']=="D") 
		$Salary = $val['empDrate']."/day";
	else
		$Salary = $val['empMrate'];
	if($val['employmentTag']=="RG")
		$emptag="Regular";
	elseif($val['employmentTag']=="PR")
		$emptag="Probationary";
	elseif($val['employmentTag']=="CN")
		$emptag="Contractual";
	else
		$emptag="--";	
	if($val['reason']!="")
		$reason=$val['reason'];
	else
		$reason="--";					
	
	$qryNatures = $inqTSObj->execQry("Select * from tblSeparatedEmployees inner join tblNatures on tblSeparatedEmployees.natureCode=tblNatures.natureCode where empNo='{$val['empNo']}'");
	$resNature = $inqTSObj->getSqlAssoc($qryNatures);
	
		if($resNature['reason']==1){
			$payreason="Promotion";
		}
		elseif($resNature['reason']==2){
			$payreason="Merit Increase";	
		}
		elseif($resNature['reason']==5){
			$payreason="Gov't Mandate";	
		}
		elseif($resNature['reason']==4){
			$payreason="Salary Increase";	
		}
		elseif($resNature['reason']==6){
			$payreason="Alignment";	
		}
		elseif($resNature['reason']==7){
			$payreason="Regularization";	
		}
		elseif($resNature['reason']==8){
			$payreason="Probationary";	
		}
		else{
			$payreason=$resNature['reason'];	
		}
		
	$pdf->Cell(35,$SPACES,trim($val['brnShortDesc']),0,0);
	$pdf->Cell(20,$SPACES,$val['empNo'],0,0);
	$pdf->Cell(60,$SPACES,$name,0,0,'L');
	$pdf->Cell(20,$SPACES,date('m/d/Y',strtotime($val['dateHired'])),0,0,'L');
	$pdf->Cell(30,$SPACES,$emptag,0,0,'L');
	$pdf->Cell(55,$SPACES,$val['posShortDesc'],0,0,'L');
	$pdf->Cell(40,$SPACES,($resNature['Description']=="" ? "-----":$resNature['Description']),0,0,'L');
	$pdf->Cell(50,$SPACES,($payreason=="" ? "-----":$payreason),0,0,'L');
	$pdf->MultiCell(50,$SPACES,date('m/d/Y',strtotime($val['effectivitydate'])),0,1,'L');
//	$pdf->Cell(1,$SPACES,"",0,1,'L');
	$ctr++;
	if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);

}
#########################################################################
if ($pdf->GetY() > 190) HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt);
$pdf->Ln(5);
$pdf->Cell($TOTAL_WIDTH,$SPACES,"* * * End of Report * * *",0,1,'C');
$pdf->Cell(10,$SPACES,"Total Record/s = ".($ctr-1),0,1);
#########################################################################
$pdf->Output('separated_employees.pdf','D');


function HEADER_FOOTER($pdf, $inqTSObj, $compCode, $TOTAL_WIDTH, $dt) {
	############################## ADD PAGE AND COMPUTE #####################
	$pdf->AddPage();
	############################ H E A D E R ################################
	$currDate 		= "Run Date: ".$inqTSObj->currentDateArt();
	$compName 		= $inqTSObj->getCompanyName($compCode);
	$reppages 		= "";
	$repId    		= "Report ID: SEEMP";
	
	$repTitle 		= "Final Prooflist of Separated Employees";
	$rdate			= $dt;
	$refNo    		= ""; 
	$dtlLabelDown   = " Branch              Emp. No.    Employee                           Date Hired  Resigned Status   Position                         Nature of Separation   Reason                     		 Effectivity";
	$dtlLabelDown2   = "";
	#########################################################################
	$pdf->Text(10,10,$currDate);
	$pdf->Text(145,10,$compName);
	//if ($reppages=="") $lstPge = ""; else $lstPge = " of ".$reppages;
	$pdf->Text(270,10,$lstPge);
	$pdf->Text(10,15,$repId);
	$pdf->Text(145,15,$repTitle);
	$pdf->Text(170,15,$refNo);
	$pdf->Text(280,15,$rdate);
	$pdf->Text(10,23,$dtlLabelDown);
	########################### F O O T E R  ################################
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];
	
	$footerHt = 208; //////////////PORTRATE LETTER ONLY
	$pdf->Line(10,$footerHt-6,$TOTAL_WIDTH+6,$footerHt-6);
	$pdf->Text(10,$footerHt,$prntdBy);
	$pdf->Text(160,$footerHt,'Approved By:');
	$pdf->Ln(22);
}
?>