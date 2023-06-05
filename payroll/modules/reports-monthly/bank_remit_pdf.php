<?
//programmer : vincent c de torres;
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("bank_remit.obj.php");
include('../../../includes/pdf/fpdf.php');

define('FPDF_FONTPATH','../../../includes/pdf/font/');

$bnkRmitObj = new bankRemitObj($_SESSION,$_GET);
$sessionVars = $bnkRmitObj->getSeesionVars();
//$bnkRmitObj->validateSessions('','MODULES');

$userInfo = $bnkRmitObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);

$payPdSlctd = $bnkRmitObj->getPayPeriod($_SESSION['company_code'],"AND payGrp = '{$_SESSION['pay_group']}' AND payCat = '{$_SESSION['pay_category']}' AND pdPayable = '{$_GET['payPd']}'");

 $qryGetPaySum = "SELECT ps.empNo,ps.netSalary,emp.empLastName,emp.empMidName,emp.empFirstName,emp.empAcctNo,sprtAllow
					  FROM tblPayrollSummaryHist as ps LEFT JOIN tblEmpMast as emp
					  ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo
				  WHERE ps.compCode = '{$_SESSION['company_code']}'
				  AND ps.payGrp = '{$_SESSION['pay_group']}'
				  AND ps.payCat = '{$_SESSION['pay_category']}'
				  AND ps.pdYear = '{$payPdSlctd['pdYear']}'
				  AND ps.pdNumber = '{$payPdSlctd['pdNumber']}' 
				  AND ps.empBnkCd = '{$_GET['cmbBank']}' ";
if(trim($_GET['txtEmpNo']) != ""){
	$qryGetPaySum .= "AND ps.empNo = '{$_GET['txtEmpNo']}' ";
}
if(trim($_GET['txtEmpName']) != ""){
	if($_GET['nameType'] == 1){
		$qryGetPaySum .= "AND emp.empLastName LIKE '{$_GET['txtEmpName']}%' ";
	}
	if($_GET['nameType'] == 2){
		$qryGetPaySum .= "AND emp.empFirstName LIKE '{$_GET['txtEmpName']}%' ";
	}
	if($_GET['nameType'] == 3){
		$qryGetPaySum .= "AND emp.empMidName LIKE '{$_GET['txtEmpName']}%' ";
	}
}
if($_GET['cmbDiv'] != 0){
	$qryGetPaySum .= "AND ps.empDivCode = '{$_GET['cmbDiv']}%' ";
}
if($_GET['cmbDept'] != 0){
	$qryGetPaySum .= "AND ps.empDepCode = '{$_GET['cmbDept']}%' ";
}
if($_GET['cmbSect'] != 0){
	$qryGetPaySum .= "AND ps.empSecCode = '{$_GET['cmbSect']}%' ";
}
if($_GET['orderBy'] == 1){
 $qryGetPaySum .= "ORDER BY emp.empLastName ";
}
if($_GET['orderBy'] == 2){
 $qryGetPaySum .= "ORDER BY emp.empFirstName ";
}
if($_GET['orderBy'] == 3){
 $qryGetPaySum .= "ORDER BY ps.empNo ";
}
if($_GET['orderBy'] == 4){
 $qryGetPaySum .= "ORDER BY ps.empDepCode ";
}
echo $qryGetPaySum;
$resGetPaySum = $bnkRmitObj->execQry($qryGetPaySum);

while($rowGetPaySum = $bnkRmitObj->getSqlAssoc($resGetPaySum)){
	$arrPaySum[] = array('EMPNAME'=>$rowGetPaySum['empLastName'].",".$rowGetPaySum['empFirstName'],
					     'ACCTNO'=>$rowGetPaySum['empAcctNo'],
					     'EMPID'=>$rowGetPaySum['empNo'],
					     'ALLOW'=>$rowGetPaySum['sprtAllow'],
					     'TOTAMNT'=>$rowGetPaySum['netSalary']);
}

function rptHeader($title,$bankName,$frmDt,$toDt,$branch){//function for header
	global $pdf;
	$pdf->SetFont('arial','B',12);	
	$pdf->Cell(200,4,$title,0,1,'C');
	$pdf->SetFont('courier','',10);	
	$pdf->Cell(200,4,'PERIOD OF : ' . $frmDt . ' TO ' . $toDt . '                   BANK   : '. $bankName,0,1,'L');
	$pdf->Cell(200,4,'AS OF     : ' . date('l,F d,Y') . '                   BRANCH :'. $branch,0,0,'L');
}

function DetailLabel(){//function for detail label
	global $pdf;
	$pdf->Ln(5);
	$pdf->SetFont('courier','B',10);	
	$pdf->cell(10,5,'#',1,0,'C');
	$pdf->cell(70,5,'EMPLOYEE NAME',1,'','C');
	$pdf->cell(40,5,'ACCOUNT NO.',1,'','C');
	$pdf->cell(30,5,'EMP NO.',1,'','C');
	$pdf->cell(25,5,'ALLOWANCE',1,0,'C');	
	$pdf->cell(25,5,'NET SALARY',1,1,'C');	
	$pdf->SetFont('courier','',9);	
	
}//end of function for detail label

function rprtFtr($user){
	global $pdf;
	$pdf->Text(10,272,'Printed By  : '. $user);	
	$pdf->Text(10,275,'Date Printed: '.date('m/d/Y'));	
}

function ReportPager($pageCtr,$totalPage){//function for pager
	global $pdf;
	$pdf->Text(175,275,'Page(s) '. $pageCtr . ' Of ' . $totalPage);		
	
}//end of funtion for pager


$pdf = new FPDF('P','mm','LETTER');

$recordPerPage = 70;
$addtional = 70;
$totalPage = ceil($bnkRmitObj->getRecCount($resGetPaySum)/$recordPerPage);

if($bnkRmitObj->getRecCount($resGetPaySum) > 0){
$i=0;
$index=0;
while ($i<=$totalPage-1) {

$pdf->AddPage();

$bnkName = $bnkRmitObj->getEmpBankArt($_SESSION['company_code'],$_GET['cmbBank']);
$brnchDesc = $bnkRmitObj->getEmpBranchArt($_SESSION['company_code'],$_GET['cmbBranch']);
rptHeader('BANK ADVICE AUTHORIZATION',$bnkName['bankDesc'],$_GET['hdnFrmPd'],$_GET['hdnToPd'],$brnchDesc['brnDesc']);
DetailLabel();

	for($ctr=$index;$ctr<$recordPerPage;$ctr++){
		if($ctr<= $bnkRmitObj->getRecCount($resGetPaySum)-1){
			$pdf->cell(10,3,$ctr+1,1,0,'');
			$pdf->cell(70,3,$arrPaySum[$ctr]['EMPNAME'],1,0,'L');
			$pdf->cell(40,3,$arrPaySum[$ctr]['ACCTNO'],1,0,'L');
			$pdf->cell(30,3,$arrPaySum[$ctr]['EMPID'],1,0,'L');
			$pdf->cell(25,3,number_format($arrPaySum[$ctr]['ALLOW'],2,'.',','),1,0,'R');
			$pdf->cell(25,3,number_format($arrPaySum[$ctr]['TOTAMNT'],2,'.',','),1,1,'R');
			$totAmountPerAcct += (float)$arrPaySum[$ctr]['TOTAMNT'];
		}
	}
	
	//footer detail
	rprtFtr($userInfo['empNo']. "-" .$userInfo['empLastName'].",".$userInfo['empFirstName']);
	//end of footer detail		
	
	//for pagination
	$pageCtr = $i+1;
	ReportPager($pageCtr,$totalPage);	
	//end of pagination	
	
	//computation for looping of data
	$index = $index+$addtional;
	$recordPerPage = $recordPerPage+$addtional;	
	$i++;
	//end of computation for looping of data
}
$pdf->SetFont('courier','B',9);	
$pdf->cell(200,3,'TOTAL PER ACCOUNT :                                                                      '.number_format($totAmountPerAcct,2,'.',','),1,1,'L');
$pdf->SetFont('courier','',9);	
if($recordPerPage = $totalPage){//section for last page
	
	$pdf->cell(200,5,'***** END OF REPORT *****',0,1,'C');
}
$pdf->Output();
}
else{
	$pdf->AddPage();
		$pdf->SetFont('courier','',20);	
		$pdf->cell(200,5,'***** NO RECORD FOUND *****',0,1,'C');
	$pdf->Output();
}
?>