<?php
####Include files
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
include("../../../includes/pdf/fpdf.php");

####Create class 
class PDF extends FPDF
{
####Declare variables	
	public $company;
	public $rundate;	
	public $dfrom;
	public $dto;
	public $type;
	public $branch;
####Set up header	
	function Header()
	{
//		$this->dfrom = $_GET['dfrom'];	
//		$this->dto = $_GET['dto'];
		$this->type = $_GET['type'];
		
		$this->SetFont('Arial','B','7'); 
		$this->Cell(150,4,'HEADCOUNT PER DIVISION AS OF '.date("F j, Y h:i:s A", strtotime($this->rundate)),'','1');
		$this->Cell(150,4,$this->company,'','1');
		$this->Image(DIVISION_HEADER,'9','20','200','10');
		$this->Ln(11.5);
	}

####Set up details/data	
	function Data($arrRank) {
		$this->SetFont('Arial','B','8'); 
		$this->Cell(197.6,3,'','1','1');
		$qryDiv = "Select * from tblDepartment where deptLevel='1' order by deptDesc";
		$resDiv = $this->getArrRes($this->execQry($qryDiv));
		$rfRGSumTotal = $supRGSumTotal = $offRGSumTotal = $mnRGSumTotal = $srRGSumTotal = $execRGSumTotal = 0;
		$rfPRSumTotal = $supPRSumTotal = $offPRSumTotal = $mnPRSumTotal = $srPRSumTotal = $execPRSumTotal = 0;
		$rfCNSumTotal = $supCNSumTotal = $offCNSumTotal = $mnCNSumTotal = $srCNSumTotal = $execCNSumTotal = 0;
		$rfRGSumTotalSB = $supRGSumTotalSB = $offRGSumTotalSB = $mnRGSumTotalSB = $srRGSumTotalSB = $execRGSumTotalSB = 0;
		$rfPRSumTotalSB = $supPRSumTotalSB = $offPRSumTotalSB = $mnPRSumTotalSB = $srPRSumTotalSB = $execPRSumTotalSB = 0;
		$rfCNSumTotalSB = $supCNSumTotalSB = $offCNSumTotalSB = $mnCNSumTotalSB = $srCNSumTotalSB = $execCNSumTotalSB = 0;
		foreach($resDiv as $valDiv){
			$this->SetFont('Arial','B','8'); 
			$this->Cell(197.6,5,$valDiv['deptDesc'],'1','1');
			
			$qryDept = "Select * from tblDepartment where deptLevel='2' and divCode='{$valDiv['divCode']}' order by deptDesc";
			$resDept = $this->getArrRes($this->execQry($qryDept));
			$rfRGTot=$supRGTot=$offRGTot=$mnRGTot=$srmnRGTot=$execRGTot=0;
			$rfPRTot=$supPRTot=$offPRTot=$mnPRTot=$srmnPRTot=$execPRTot=0;
			$rfCNTot=$supCNTot=$offCNTot=$mnCNTot=$srmnCNTot=$execCNTot=0;
			$rfRGTotSB=$supRGTotSB=$offRGTotSB=$mnRGTotSB=$srmnRGTotSB=$execRGTotSB=0;
			$rfPRTotSB=$supPRTotSB=$offPRTotSB=$mnPRTotSB=$srmnPRTotSB=$execPRTotSB=0;
			$rfCNTotSB=$supCNTotSB=$offCNTotSB=$mnCNTotSB=$srmnCNTotSB=$execCNTotSB=0;
			$rgTotal=$prTotal=$cnTotal=$rgTotalSB=$prTotalSB=$cnTotalSB=0;

			foreach($resDept as $valDept){
				$this->SetFont('Arial','','7'); 
				$this->Cell(43.7,5,$valDept['deptShortDesc'],'1','0');
					$this->SetFont('Arial','','7'); 
					$qryResult = "Select empStat,empRank,count(empRank)rankCnt,employmentTag as empTag, empDiv, empDepCode  
					from tblEmpMast emp 
					inner join tblRankType rank on emp.empRank = rank.rankCode 
					where empBrnCode='0001' and empStat='RG' and empDiv='{$valDept['divCode']}' and empDepCode='{$valDept['deptCode']}' 
					group by empStat,empRank,employmentTag, empDiv, empDepCode order by empStat,empRank";
					$resResult = $this->getArrRes($this->execQry($qryResult));
					$rfCnt['RG'] = $rfCnt['PR']= $rfCnt['CN'] = "";
					$supCnt['RG']= $supCnt['PR']= $supCnt['CN'] = "";
					$offCnt['RG']= $offCnt['PR']= $offCnt['CN'] = "";
					$mnCnt['RG']= $mnCnt['PR']= $mnCnt['CN'] = "";
					$srmnCnt['RG']= $srmnCnt['PR']= $srmnCnt['CN'] = "";
					$execCnt['RG']= $execCnt['PR']= $execCnt['CN'] = "";
		
					$qryResultSB = "Select empStat,empRank,count(empRank)rankCnt,employmentTag as empTag, empDiv, empDepCode  
					from tblEmpMast emp 
					inner join tblRankType rank on emp.empRank = rank.rankCode 
					where empBrnCode<>'0001' and empStat='RG' and empDiv='{$valDept['divCode']}' and empDepCode='{$valDept['deptCode']}' 
					group by empStat,empRank,employmentTag, empDiv, empDepCode order by empStat,empRank";
					$resResultSB = $this->getArrRes($this->execQry($qryResultSB));
					$rfCntSB['RG'] = $rfCntSB['PR']= $rfCntSB['CN'] = "";
					$supCntSB['RG']= $supCntSB['PR']= $supCntSB['CN'] = "";
					$offCntSB['RG']= $offCntSB['PR']= $offCntSB['CN'] = "";
					$mnCntSB['RG']= $mnCntSB['PR']= $mnCntSB['CN'] = "";
					$srmnCntSB['RG']= $srmnCntSB['PR']= $srmnCntSB['CN'] = "";
					$execCntSB['RG']= $execCntSB['PR']= $execCntSB['CN'] = "";
					
					foreach($resResultSB as $valResultSB){
						switch ($valResultSB['empRank']){
							case "1":
								$rfCntSB[$valResultSB['empTag']] = (int)$rfCntSB[$valResultSB['empTag']] + $valResultSB['rankCnt'];
							break;	
							case "2":
								$supCntSB[$valResultSB['empTag']] = (int)$supCntSB[$valResultSB['empTag']] + $valResultSB['rankCnt'];
							break;		
							case "3":
								$offCntSB[$valResultSB['empTag']] = (int)$offCntSB[$valResultSB['empTag']] + $valResultSB['rankCnt'];
							break;		
							case "4":
								$mnCntSB[$valResultSB['empTag']] = (int)$mnCntSB[$valResultSB['empTag']] + $valResultSB['rankCnt'];
							break;		
							case "5":
								$srmnCntSB[$valResultSB['empTag']] = (int)$srmnCntSB[$valResultSB['empTag']] + $valResultSB['rankCnt'];
							break;		
							case "6":
								$execCntSB[$valResultSB['empTag']] = (int)$execCntSB[$valResultSB['empTag']] + $valResultSB['rankCnt'];
							break;		
						}	
					}
		
					
					foreach($resResult as $valResult){
						switch ($valResult['empRank']){
							case "1":
								$rfCnt[$valResult['empTag']] = (int)$rfCnt[$valResult['empTag']] + $valResult['rankCnt'];
							break;	
							case "2":
								$supCnt[$valResult['empTag']] = (int)$supCnt[$valResult['empTag']] + $valResult['rankCnt'];
							break;		
							case "3":
								$offCnt[$valResult['empTag']] = (int)$offCnt[$valResult['empTag']] + $valResult['rankCnt'];
							break;		
							case "4":
								$mnCnt[$valResult['empTag']] = (int)$mnCnt[$valResult['empTag']] + $valResult['rankCnt'];
							break;		
							case "5":
								$srmnCnt[$valResult['empTag']] = (int)$srmnCnt[$valResult['empTag']] + $valResult['rankCnt'];
							break;		
							case "6":
								$execCnt[$valResult['empTag']] = (int)$execCnt[$valResult['empTag']] + $valResult['rankCnt'];
							break;		
						}	
					}
					$this->SetFont('Arial','','8'); 
					$this->Cell(27.9,5,'Rank & File','1','');
					$rfRGSumTotal=$rfRGSumTotal+(int)$rfCnt['RG'];
					$rfPRSumTotal=$rfPRSumTotal+(int)$rfCnt['PR'];
					$rfCNSumTotal=$rfCNSumTotal+(int)$rfCnt['CN'];
					$rfRGSumTotalSB=$rfRGSumTotalSB+(int)$rfCntSB['RG'];
					$rfPRSumTotalSB=$rfPRSumTotalSB+(int)$rfCntSB['PR'];
					$rfCNSumTotalSB=$rfCNSumTotalSB+(int)$rfCntSB['CN'];
					$this->Cell(10.5,5,(int)$rfCnt['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$rfCnt['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$rfCnt['CN'],'1','','C');
					$this->Cell(10.5,5,(int)$rfCntSB['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$rfCntSB['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$rfCntSB['CN'],'1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(12.5,5,(int)$rfCnt['RG']+(int)$rfCnt['PR']+(int)$rfCnt['CN']+(int)$rfCntSB['RG']+(int)$rfCntSB['PR']+(int)$rfCntSB['CN'],'1','','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(13.5,5,'0','1','','C');
					$this->Cell(12,5,'0','1','','C');
					$this->Cell(11.5,5,'0','1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(13.5,5,(int)$rfCnt['RG']+(int)$rfCnt['PR']+(int)$rfCnt['CN']+(int)$rfCntSB['RG']+(int)$rfCntSB['PR']+(int)$rfCntSB['CN'],'1','1','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(43.6,5,'','LR','0');
					$this->Cell(28,5,'Supervisor','1','');
					$supRGSumTotal=$supRGSumTotal+(int)$supCnt['RG'];
					$supPRSumTotal=$supPRSumTotal+(int)$supCnt['PR'];
					$supCNSumTotal=$supCNSumTotal+(int)$supCnt['CN'];
					$supRGSumTotalSB=$supRGSumTotalSB+(int)$supCntSB['RG'];
					$supPRSumTotalSB=$supPRSumTotalSB+(int)$supCntSB['PR'];
					$supCNSumTotalSB=$supCNSumTotalSB+(int)$supCntSB['CN'];
					$this->Cell(10.5,5,(int)$supCnt['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$supCnt['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$supCnt['CN'],'1','','C');
					$this->Cell(10.5,5,(int)$supCntSB['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$supCntSB['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$supCntSB['CN'],'1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(12.5,5,(int)$supCnt['RG']+(int)$supCnt['PR']+(int)$supCnt['CN']+(int)$supCntSB['RG']+(int)$supCntSB['PR']+(int)$supCntSB['CN'],'1','','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(13.5,5,'0','1','','C');
					$this->Cell(12,5,'0','1','','C');
					$this->Cell(11.5,5,'0','1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(13.5,5,(int)$supCnt['RG']+(int)$supCnt['PR']+(int)$supCnt['CN']+(int)$supCntSB['RG']+(int)$supCntSB['PR']+(int)$supCntSB['CN'],'1','1','C');	
					$this->SetFont('Arial','','8'); 				
					$this->Cell(43.6,5,'','LR','0');
					$this->Cell(28,5,'Officer','1','');
					$offRGSumTotal=$offRGSumTotal+(int)$offCnt['RG'];
					$offPRSumTotal=$offPRSumTotal+(int)$offCnt['PR'];
					$offCNSumTotal=$offCNSumTotal+(int)$offCnt['CN'];
					$offRGSumTotalSB=$offRGSumTotalSB+(int)$offCntSB['RG'];
					$offPRSumTotalSB=$offPRSumTotalSB+(int)$offCntSB['PR'];
					$offCNSumTotalSB=$offCNSumTotalSB+(int)$offCntSB['CN'];
					$this->Cell(10.5,5,(int)$offCnt['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$offCnt['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$offCnt['CN'],'1','','C');
					$this->Cell(10.5,5,(int)$offCntSB['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$offCntSB['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$offCntSB['CN'],'1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(12.5,5,(int)$offCnt['RG']+(int)$offCnt['PR']+(int)$offCnt['CN']+(int)$offCntSB['RG']+(int)$offCntSB['PR']+(int)$offCntSB['CN'],'1','','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(13.5,5,'0','1','','C');
					$this->Cell(12,5,'0','1','','C');
					$this->Cell(11.5,5,'0','1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(13.5,5,(int)$offCnt['RG']+(int)$offCnt['PR']+(int)$offCnt['CN']+(int)$offCntSB['RG']+(int)$offCntSB['PR']+(int)$offCntSB['CN'],'1','1','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(43.6,5,'','LR','0');
					$this->Cell(28,5,'Manager','1','');
					$mnRGSumTotal=$mnRGSumTotal+(int)$mnCnt['RG'];
					$mnPRSumTotal=$mnPRSumTotal+(int)$mnCnt['PR'];
					$mnCNSumTotal=$mnCNSumTotal+(int)$mnCnt['CN'];
					$mnRGSumTotalSB=$mnRGSumTotalSB+(int)$mnCntSB['RG'];
					$mnPRSumTotalSB=$mnPRSumTotalSB+(int)$mnCntSB['PR'];
					$mnCNSumTotalSB=$mnCNSumTotalSB+(int)$mnCntSB['CN'];
					$this->Cell(10.5,5,(int)$mnCnt['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$mnCnt['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$mnCnt['CN'],'1','','C');
					$this->Cell(10.5,5,(int)$mnCntSB['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$mnCntSB['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$mnCntSB['CN'],'1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(12.5,5,(int)$mnCnt['RG']+(int)$mnCnt['PR']+(int)$mnCnt['CN']+(int)$mnCntSB['RG']+(int)$mnCntSB['PR']+(int)$mnCntSB['CN'],'1','','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(13.5,5,'0','1','','C');
					$this->Cell(12,5,'0','1','','C');
					$this->Cell(11.5,5,'0','1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(13.5,5,(int)$mnCnt['RG']+(int)$mnCnt['PR']+(int)$mnCnt['CN']+(int)$mnCntSB['RG']+(int)$mnCntSB['PR']+(int)$mnCntSB['CN'],'1','1','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(43.6,5,'','LR','0');
					$this->Cell(28,5,'Sr. Manager','1','');
					$srRGSumTotal=$srRGSumTotal+(int)$srmnCnt['RG'];
					$srPRSumTotal=$srPRSumTotal+(int)$srmnCnt['PR'];
					$srCNSumTotal=$srCNSumTotal+(int)$srmnCnt['CN'];
					$srRGSumTotalSB=$srRGSumTotalSB+(int)$srmnCntSB['RG'];
					$srPRSumTotalSB=$srPRSumTotalSB+(int)$srmnCntSB['PR'];
					$srCNSumTotalSB=$srCNSumTotalSB+(int)$srmnCntSB['CN'];
					$this->Cell(10.5,5,(int)$srmnCnt['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$srmnCnt['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$srmnCnt['CN'],'1','','C');
					$this->Cell(10.5,5,(int)$srmnCntSB['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$srmnCntSB['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$srmnCntSB['CN'],'1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(12.5,5,(int)$srmnCnt['RG']+(int)$srmnCnt['PR']+(int)$srmnCnt['CN']+(int)$srmnCntSB['RG']+(int)$srmnCntSB['PR']+(int)$srmnCntSB['CN'],'1','','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(13.5,5,'0','1','','C');
					$this->Cell(12,5,'0','1','','C');
					$this->Cell(11.5,5,'0','1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(13.5,5,(int)$srmnCnt['RG']+(int)$srmnCnt['PR']+(int)$srmnCnt['CN']+(int)$srmnCntSB['RG']+(int)$srmnCntSB['PR']+(int)$srmnCntSB['CN'],'1','1','C');
					
					$this->SetFont('Arial','','8'); 
					$this->Cell(43.6,5,'','LR','0');
					$this->Cell(28,5,'Executive','1','');
					$execRGSumTotal=$execRGSumTotal+(int)$execCnt['RG'];
					$execPRSumTotal=$execPRSumTotal+(int)$execCnt['PR'];
					$execCNSumTotal=$execCNSumTotal+(int)$execCnt['CN'];
					$execRGSumTotalSB=$execRGSumTotalSB+(int)$execCntSB['RG'];
					$execPRSumTotalSB=$execPRSumTotalSB+(int)$execCntSB['PR'];
					$execCNSumTotalSB=$execCNSumTotalSB+(int)$execCntSB['CN'];
					$this->Cell(10.5,5,(int)$execCnt['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$execCnt['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$execCnt['CN'],'1','','C');
					$this->Cell(10.5,5,(int)$execCntSB['RG'],'1','','C');
					$this->Cell(10.5,5,(int)$execCntSB['PR'],'1','','C');
					$this->Cell(10.5,5,(int)$execCntSB['CN'],'1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(12.5,5,(int)$execCnt['RG']+(int)$execCnt['PR']+(int)$execCnt['CN']+(int)$execCntSB['RG']+(int)$execCntSB['PR']+(int)$execCntSB['CN'],'1','','C');
					$this->SetFont('Arial','','8'); 
					$this->Cell(13.5,5,'0','1','','C');
					$this->Cell(12,5,'0','1','','C');
					$this->Cell(11.5,5,'0','1','','C');
					$this->SetFont('Arial','B','8'); 
					$this->Cell(13.5,5,(int)$execCnt['RG']+(int)$execCnt['PR']+(int)$execCnt['CN']+(int)$execCntSB['RG']+(int)$execCntSB['PR']+(int)$execCntSB['CN'],'1','1','C');
					
					$rfRGTot=$rfRGTot+(int)$rfCnt['RG']; 
					$supRGTot=$supRGTot+(int)$supCnt['RG'];
					$offRGTot=$offRGTot+(int)$offCnt['RG'];
					$mnRGTot=$mnRGTot+(int)$mnCnt['RG'];
					$srmnRGTot=$srmnRGTot+(int)$srmnCnt['RG'];
					$execRGTot=$execRGTot+(int)$execCnt['RG'];
					
					$rfPRTot=$rfPRTot+(int)$rfCnt['PR'];
					$supPRTot=$supPRTot+(int)$supCnt['PR'];
					$offPRTot=$offPRTot+(int)$offCnt['PR'];
					$mnPRTot=$mnPRTot+(int)$mnCnt['PR'];
					$srmnPRTot=$srmnPRTot+(int)$srmnCnt['PR'];
					$execPRTot=$execPRTot+(int)$execCnt['PR'];
					
					$rfCNTot=$rfCNTot+(int)$rfCnt['CN'];
					$supCNTot=$supCNTot+(int)$supCnt['CN'];
					$offCNTot=$offCNTot+(int)$offCnt['CN'];
					$mnCNTot=$mnCNTot+(int)$mnCnt['CN'];
					$srmnCNTot=$srmnCNTot+(int)$srmnCnt['CN'];
					$execCNTot=$execCNTot+(int)$execCnt['CN'];
					
					$rfRGTotSB=$rfRGTotSB+(int)$rfCntSB['RG']; 
					$supRGTotSB=$supRGTotSB+(int)$supCntSB['RG'];
					$offRGTotSB=$offRGTotSB+(int)$offCntSB['RG'];
					$mnRGTotSB=$mnRGTotSB+(int)$mnCntSB['RG'];
					$srmnRGTotSB=$srmnRGTotSB+(int)$srmnCntSB['RG'];
					$execRGTotSB=$execRGTotSB+(int)$execCntSB['RG'];
					
					$rfPRTotSB=$rfPRTotSB+(int)$rfCntSB['PR'];
					$supPRTotSB=$supPRTotSB+(int)$supCntSB['PR'];
					$offPRTotSB=$offPRTotSB+(int)$offCntSB['PR'];
					$mnPRTotSB=$mnPRTotSB+(int)$mnCntSB['PR'];
					$srmnPRTotSB=$srmnPRTotSB+(int)$srmnCntSB['PR'];
					$execPRTotSB=$execPRTotSB+(int)$execCntSB['PR'];
					
					$rfCNTotSB=$rfCNTotSB+(int)$rfCntSB['CN'];
					$supCNTotSB=$supCNTotSB+(int)$supCntSB['CN'];
					$offCNTotSB=$offCNTotSB+(int)$offCntSB['CN'];
					$mnCNTotSB=$mnCNTotSB+(int)$mnCntSB['CN'];
					$srmnCNTotSB=$srmnCNTotSB+(int)$srmnCntSB['CN'];
					$execCNTotSB=$execCNTotSB+(int)$execCntSB['CN'];	
			}
			$this->Cell(71.6,5,'TOTAL','1','','C');
			$this->Cell(10.5,5,$rgTotal=(int)$rfRGTot+(int)$supRGTot+(int)$offRGTot+(int)$mnRGTot+(int)$srmnRGTot+(int)$execRGTot,'1','','C');
			$this->Cell(10.5,5,$prTotal=(int)$rfPRTot+(int)$supPRTot+(int)$offPRTot+(int)$mnPRTot+(int)$srmnPRTot+(int)$execPRTot,'1','','C');
			$this->Cell(10.5,5,$cnTotal=(int)$rfCNTot+(int)$supCNTot+(int)$offCNTot+(int)$mnCNTot+(int)$srmnCNTot+(int)$execCNTot,'1','','C');
			$this->Cell(10.5,5,$rgTotalSB=(int)$rfRGTotSB+(int)$supRGTotSB+(int)$offRGTotSB+(int)$mnRGTotSB+(int)$srmnRGTotSB+(int)$execRGTotSB,'1','','C');
			$this->Cell(10.5,5,$prTotalSB=(int)$rfPRTotSB+(int)$supPRTotSB+(int)$offPRTotSB+(int)$mnPRTotSB+(int)$srmnPRTotSB+(int)$execPRTotSB,'1','','C');
			$this->Cell(10.5,5,$cnTotalSB=(int)$rfCNTotSB+(int)$supCNTotSB+(int)$offCNTotSB+(int)$mnCNTotSB+(int)$srmnCNTotSB+(int)$execCNTotSB,'1','','C');
			$this->Cell(12.5,5,(int)$rgTotal+(int)$prTotal+(int)$cnTotal+(int)$rgTotalSB+(int)$prTotalSB+(int)$cnTotalSB,'1','','C');
			$this->Cell(13.5,5,'0','1','','C');
			$this->Cell(12,5,'0','1','','C');
			$this->Cell(11.5,5,'0','1','','C');
			$this->SetFont('Arial','B','8'); 
			$this->Cell(13.5,5,(int)$rgTotal+(int)$prTotal+(int)$cnTotal+(int)$rgTotalSB+(int)$prTotalSB+(int)$cnTotalSB,'1','1','C');
			//$this->Cell(197.6,3,'','','1');
			$rgSumTotal = (int)$rgSumTotal+(int)$rgTotal;
		}
		$this->ln(5);
		$this->Cell(197.6,5,'SUMMARY','','1');
		$this->Cell(197.6,5,'                                                          DIRECT                         INDIRECT                                                                    DIRECT                          INDIRECT','','1');
		$this->Cell(197.6,5,'HEAD OFFICE                         R           P           C                                                    STORE BASED                 R           P           C           ','','1');
		$this->SetFont('Arial','','8'); 
		$this->Cell(35,3,'Rank and File','','');
		$this->Cell(10.5,3,$rfRGSumTotal,'','','C');
		$this->Cell(12,3,$rfPRSumTotal,'','','C');
		$this->Cell(10,3,$rfCNSumTotal,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','');
		$this->Cell(30,3,'Rank and File','','');
		$this->Cell(10.5,3,$rfRGSumTotalSB,'','','C');
		$this->Cell(12,3,$rfPRSumTotalSB,'','','C');
		$this->Cell(10,3,$rfCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(20,3,'0','','1');
		$this->Cell(35,3,'Supervisor','','');
		$this->Cell(10.5,3,$supRGSumTotal,'','','C');
		$this->Cell(12,3,$supPRSumTotal,'','','C');
		$this->Cell(10,3,$supCNSumTotal,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','');
		$this->Cell(30,3,'Supervisor','','');
		$this->Cell(10.5,3,$supRGSumTotalSB,'','','C');
		$this->Cell(12,3,$supPRSumTotalSB,'','','C');
		$this->Cell(10,3,$supCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(20,3,'0','','1');
		$this->Cell(35,3,'Officer','','');
		$this->Cell(10.5,3,$offRGSumTotal,'','','C');
		$this->Cell(12,3,$offPRSumTotal,'','','C');
		$this->Cell(10,3,$offCNSumTotal,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','');
		$this->Cell(30,3,'Officer','','');
		$this->Cell(10.5,3,$offRGSumTotalSB,'','','C');
		$this->Cell(12,3,$offPRSumTotalSB,'','','C');
		$this->Cell(10,3,$offCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(20,3,'0','','1');
		$this->Cell(35,3,'Manager','','');
		$this->Cell(10.5,3,$mnRGSumTotal,'','','C');
		$this->Cell(12,3,$mnPRSumTotal,'','','C');
		$this->Cell(10,3,$mnCNSumTotal,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','');
		$this->Cell(30,3,'Manager','','');
		$this->Cell(10.5,3,$mnRGSumTotalSB,'','','C');
		$this->Cell(12,3,$mnPRSumTotalSB,'','','C');
		$this->Cell(10,3,$mnCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(20,3,'0','','1');
		$this->Cell(35,3,'Sr. Manager','','');
		$this->Cell(10.5,3,$srRGSumTotal,'','','C');
		$this->Cell(12,3,$srPRSumTotal,'','','C');
		$this->Cell(10,3,$srCNSumTotal,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','');
		$this->Cell(30,3,'Sr. Manager','','');
		$this->Cell(10.5,3,$srRGSumTotalSB,'','','C');
		$this->Cell(12,3,$srPRSumTotalSB,'','','C');
		$this->Cell(10,3,$srCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(30,3,'0','','1');
		$this->Cell(35,3,'Executive','','');
		$this->Cell(10.5,3,$execRGSumTotal,'','','C');
		$this->Cell(12,3,$execPRSumTotal,'','','C');
		$this->Cell(10,3,$execCNSumTotal,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','0');
		$this->Cell(30,3,'Executive','','');
		$this->Cell(10.5,3,$execRGSumTotalSB,'','','C');
		$this->Cell(12,3,$execPRSumTotalSB,'','','C');
		$this->Cell(10,3,$execCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(30,3,'0','','1');
		$this->SetFont('Arial','B','8'); 
		$this->Cell(35,5,'Total','','');
		$this->Cell(10.5,5,$TotalRG=(int)$rfRGSumTotal+(int)$supRGSumTotal+(int)$offRGSumTotal+(int)$mnRGSumTotal+(int)$srRGSumTotal+(int)$execRGSumTotal,'','','C');
		$this->Cell(12,5,$TotalPR=(int)$rfPRSumTotal+(int)$supPRSumTotal+(int)$offPRSumTotal+(int)$mnPRSumTotal+(int)$srPRSumTotal+(int)$execPRSumTotal,'','','C');
		$this->Cell(10,5,$TotalCN=(int)$rfCNSumTotal+(int)$supCNSumTotal+(int)$offCNSumTotal+(int)$mnCNSumTotal+(int)$srCNSumTotal+(int)$execCNSumTotal,'','','C');
		$this->Cell(13,5,'','','');
		$this->Cell(23,3,'0','','');		
		$this->Cell(30,5,'Total','','');
		$this->Cell(10.5,5,$TotalRGSB=(int)$rfRGSumTotalSB+(int)$supRGSumTotalSB+(int)$offRGSumTotalSB+(int)$mnRGSumTotalSB+(int)$srRGSumTotalSB+(int)$execRGSumTotalSB,'','','C');
		$this->Cell(12,5,$TotalPRSB=(int)$rfPRSumTotalSB+(int)$supPRSumTotalSB+(int)$offPRSumTotalSB+(int)$mnPRSumTotalSB+(int)$srPRSumTotalSB+(int)$execPRSumTotalSB,'','','C');
		$this->Cell(10,5,$TotalCNSB=(int)$rfCNSumTotalSB+(int)$supCNSumTotalSB+(int)$offCNSumTotalSB+(int)$mnCNSumTotalSB+(int)$srCNSumTotalSB+(int)$execCNSumTotalSB,'','','C');
		$this->Cell(13,3,'','','');
		$this->Cell(23,3,'0','','');
		$this->Ln(10);
		$this->Cell(197.6,5,'                                                           HEAD OFFICE       STORE BASED             TOTAL','','1');
		$this->Cell(20,5,'','','');
		$this->Cell(25,5,'DIRECT','','');
		$this->Cell(25,5,$gandTotal=(int)$TotalRG+(int)$TotalPR+(int)$TotalCN,'','','C');
		$this->Cell(25,5,$gandTotalSB=(int)$TotalRGSB+(int)$TotalPRSB+(int)$TotalCNSB,'','','C');
		$this->Cell(25,5,(int)$gandTotal+(int)$gandTotalSB,'','1','C');
		$this->Cell(20,5,'','','');
		$this->Cell(25,5,'INDIRECT','','');
		$this->Cell(25,5,'0','','','C');
		$this->Cell(25,5,'0','','','C');
		$this->Cell(25,5,'0','','1','C');
	}
	
####Function to set up multiline cell	
	function setMultiLine($w,$s,$field,$obj,$pos){
		$x=$this->GetX();
		$y=$this->GetY();
		$y1=$this->GetY();
		$this->MultiCell($w,$s,$field,$obj,$pos);
		$y2=$this->GetY();
		$yh=$y2-$y1;
		$this->SetXY($x+$w,$this->GetY()-$yh);	
	}
	
####Function to get username	
	function GetUsername($arrUsers,$uid) {
		if ($uid != "") {
			foreach($arrUsers as $val) {
				if($val['userId'] == $uid)
					$uname = $val['empLastName'] . ", " . $val['empFirstName'];
			}
			return $uname;
		} else {
			return " N/A";
		}
	}
	
	
####Function to set up footer	
	function Footer()
	{
		$this->SetY(-20);
		$this->Cell(197.6,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Generated By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
	
	
}
####Initialize object
$pdf=new PDF('P', 'mm', 'LEGAL');
$psObj=new inqTSObj();
$sessionVars = $psObj->getSeesionVars();

####Query to limit the output to encoder
$qryuser=$psObj->getUserLogInInfo($_SESSION['company_code'],$_SESSION['employee_number']);
if($qryuser['userLevel']==3){
	$userview = $qryuser['userId'];
	$ulevel="3";
}

####Query to show user
$sqlUsers = "SELECT tblEmpMast.empLastName, tblEmpMast.empFirstName, tblEmpMast.empMidName, tblUsers.userId FROM tblUsers INNER JOIN tblEmpMast ON tblUsers.empNo = tblEmpMast.empNo AND tblUsers.compCode = tblEmpMast.compCode where tblEmpMast.compCode='{$_SESSION['company_code']}'";
$arrUsers = $psObj->getArrRes($psObj->execQry($sqlUsers));

$qryRank = "Select * from tblRankType";
$resRank = $psObj->execQry($qryRank);
$arrRank = $psObj->getArrRes($resRank);

####Set up footer
$pdf->AliasNbPages();
$pdf->reportlabel = $reportLabel;
$pdf->company = $psObj->getCompanyName($_SESSION['company_code']);
$pdf->branch = $psObj->getBranchName($_SESSION['company_code'],$_GET['branch']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();

####Set up for next page
$pdf->AddPage();

####Set up to get all data/details
		$pdf->Data($arrRank);
		
####Set up to show data	
$pdf->Output('HEAD_COUNT_DIVISION_PROOFLIST.pdf','D');
?>