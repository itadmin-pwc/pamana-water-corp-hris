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
	$inqTSObj->compCode     = $compCode;
	$brnCode         		= $_POST['branch'];
	$month					= $_POST['cmbMonth'];
	$compName 		= $inqTSObj->getCompanyName($compCode);
############################ LETTER/LEGAL PORTRATE TOTAL WIDTH = 200 / 100 / 66
############################ LETTER LANDSCAPE TOTAL WIDTH = 265 / 132 / 88
############################ LEGAL LANDSCAPE TOTAL WIDTH = 310 / 155 / 103
####################### FOOTER LANDSCAPE LETTER AND LEGAL = 180
####################### FOOTER PORTRATE LETTER ONLY       = 260
####################### HEADER 10.0012
$pdf = new FPDF('L', 'mm', 'LETTER');
$pdf->SetFont('Courier', '', '10');
$TOTAL_WIDTH   			= 265;
$TOTAL_WIDTH_2 			= 132;
$TOTAL_WIDTH_3 			= 88;
$SPACES        			= 5;
$pdf->TOTAL_WIDTH       = 265;
$pdf->TOTAL_WIDTH_2     = 132;
$pdf->TOTAL_WIDTH_3     = 88;
$pdf->SPACES	       	= 5;
$page                   = 1;
############################ Q U E R Y ##################################
	if($brnCode==0){
		$sqlBr = "Select * from tblBranch where compCode='{$_SESSION['company_code']}' 
			and brnCode IN (Select brnCode from tblUserBranch where compCode='{$_SESSION['company_code']}' 
			and empNo='{$_SESSION['employee_number']}')";
	}
	else{
		$sqlBr = "SELECT * FROM tblBranch WHERE compCode = '{$_SESSION['company_code']}' and brnCode = '{$brnCode}'";
	}
	$resBr = mysql_query($sqlBr);
	$numBranches = mysql_num_rows($resBr);
	
	$userId= $inqTSObj->getSeesionVars();
	$dispUser = $inqTSObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$prntdBy = "Printed By : ".$dispUser["empFirstName"]." ".$dispUser["empLastName"];

	for($b=0;$b<$numBranches;$b++){
		$pdf->AddPage();
		HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);
		if($tmpBranch!=mysql_result($resBr,$b,"brnDesc")){
			if($tmpBranch!=""){
				//$pdf->Cell(0,5,"",0,1);
				$pdf->SetFont('Courier', '', '10');
			}	
			if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);
			$pdf->SetFont('Courier', 'B', '11');
			$pdf->Cell(0,5,mysql_result($resBr,$b,"brnShortDesc"),0,1);
			$pdf->SetFont('Courier', '', '10');
			
		}
		
				$sqlRD = "Select empNo, Concat(empLastName,', ',empFirstName,' ',empMidName) as name, 
					date_format(empBday,'%m/%d/%Y') as m, 
					deptDesc, empStat, deptShortDesc
					from tblEmpMast inner join tblDepartment on tblEmpMast.empDiv=tblDepartment.divCode and 
					tblEmpMast.empDepCode=tblDepartment.deptCode where tblDepartment.deptLevel='2' 
					and empBrnCode='".mysql_result($resBr,$b,"brnCode")."' and Month(empBday)='".$month."' and empStat in ('RG','PR','CN') order by day(empBday), empLastName";
				$resGetDealsList = mysql_query($sqlRD);
				$num = mysql_num_rows($resGetDealsList);

				$sqlBranches = "SELECT brnDesc FROM tblBranch 
					WHERE compCode = '{$_SESSION['company_code']}' and brnCode = '".mysql_result($resBr,$b,"brnCode")."'";
				$resBranches=mysql_query($sqlBranches);
			
				for ($i=0;$i<$num;$i++){ 
					//if ($tmpDept!=mysql_result($resGetDealsList,$i,"deptDesc")) {
					//	if ($tmpDept!="") {
					//		$pdf->Cell(50,4,"",0,1);
					//	}
					//}
						if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);
						$pdf->SetFont('Courier', '', '10');
					//	$pdf->Cell(50,4,"",0,1);
						//$pdf->Cell(50,5,"   ".mysql_result($resGetDealsList,$i,"deptDesc")." DEPARTMENT",0,1);
						$pdf->SetFont('Courier', '', '10');
					//}
					$pdf->Cell(50,4,"",0,0);
					$pdf->Cell(75,4,mysql_result($resGetDealsList,$i,"deptDesc"),0,0);
					$pdf->Cell(75,4,mysql_result($resGetDealsList,$i,"name"),0,0);
					$pdf->Cell(35,4,mysql_result($resGetDealsList,$i,"m"),0,1);
					$tmpDept = mysql_result($resGetDealsList,$i,"deptDesc");
					if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);	
				//}
				}
		$tmpBranch=mysql_result($resBr,$b,"brnDesc");
		if ($pdf->GetY() > 185) HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page++);
		$pdf->Cell(30,5,"",0,1);
		$pdf->Cell($TOTAL_WIDTH,5,"* * * Nothing follows. * * *",0,1,'C');
		$pdf->Cell(30,5,$prntdBy,0,1);
	}

$pdf->Output('Birthday Report.pdf','D');
function HEADER_FOOTER($pdf, $compCode, $compName,$TOTAL_WIDTH_3,$page) {
	$gmt = time() + (8 * 60 * 60);
	$newdate = date("m/d/Y h:iA", $gmt);
	//$pdf->AddPage();
	$pdf->Text(11,10,"RUN DATE: ".$newdate);
	$pdf->Text(11,14,"REPORT ID: LSTBDAY");
	$pdf->Text(120,10,$compName);
	$pdf->Text(120,14,"LIST OF BIRTHDAY CELEBRANT(S)");
	//$pdf->Text(120,14, "BRANCH: ".$brnName);
	$pdf->Cell(1,10,"",0,1,'R');
	$pdf->SetFont('Courier', 'B', '10');
	$pdf->Cell(50,5,"BRANCH",0,0);
	$pdf->Cell(75,5,"DEPARTMENT",0,0);
	$pdf->Cell(75,5,"NAME",0,0);
	$pdf->Cell(33,5,"BIRTH DATE",0,0);
	$pdf->SetFont('Courier', '', '10');
	$pdf->Cell(1,10,"",0,1,'R');
}
?>