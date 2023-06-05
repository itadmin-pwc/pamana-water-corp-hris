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
		
		$this->SetFont('Arial','B','8'); 
		$this->Cell(150,4,'HEAD COUNT SUMMARY AS OF '.date("F j, Y h:i:s A", strtotime($this->rundate)),'','1');
		$this->Ln();
	}

####Set up details/data	
	function Data($arrRank) {
		$this->SetFont('Arial','B','8'); 
		$this->Cell(110,6,$this->company,'1','1','C');
		$this->Cell(110,6,$this->branch,'1','1','C');
		$this->Cell(55,5,'RANK','1','0','C');
		$this->Cell(10,5,'RG','1','0','C');
		$this->Cell(10,5,'PR','1','0','C');
		$this->Cell(10,5,'CN','1','0','C');
		$this->Cell(10,5,'IN','1','0','C');	
		$this->SetFont('Arial','B','8'); 	
		$this->Cell(15,5,'TOTAL','1','1','C');		
		$this->Cell(110,3,'','1','1');
		$this->Cell(110,5,'OFFICE','1','1','L');
		$this->Cell(110,3,'','1','1');
		$this->Cell(110,5,'HEAD OFFICE BASED','1','1');
			if($this->type=='0001'){
				$as='0001';
				$ab='';
			}
			else{
				$as='';
				$ab=$this->type;			
			}
			$qryHO = "Select empStat,empRank,count(empRank)rankCnt,employmentTag as empTag  from tblEmpMast emp inner join tblRankType rank on emp.empRank = rank.rankCode where empBrnCode='".$as."' and empStat IN ('RG','RS') group by empStat,empRank,employmentTag order by empStat,empRank";
			$resHO = $this->getArrRes($this->execQry($qryHO));
			
		foreach($arrRank as $valRank){
			$this->SetFont('Arial','','7'); 
			$this->Cell(55,5,"     ".$valRank['rankDesc'],'LR','0','','0');
			$cnt=0;
			$totrfHO=0;
			$totrfSB=0;
			$totrfSO=0;
			$totsupHO=0;
			$totsupSB=0;
			$totsupSO=0;
			$totoffHO=0;
			$totoffSB=0;
			$totoffSO=0;
			$totmanHO=0;
			$totmanSB=0;
			$totmanSO=0;
			$totsrmanHO=0;
			$totsrmanSB=0;
			$totsrmanSO=0;
			$gtotalHO=0;
			$gtotalSB=0;
			$gtotalSO=0;
			
			foreach($resHO as $valHO){
				switch($valHO['empRank']) {
					case 1:
						if ($valHO['empStat'] == 'RS')
							$rfCnt['IN'] = (int)$rfCnt['IN'] + $valHO['rankCnt'];
						else
							$rfCnt[$valHO['empTag']] =(int)$rfCnt[$valHO['empTag']] + $valHO['rankCnt'];
					break;
					case 2:
						if ($valHO['empStat'] == 'RS')
							$supCnt['IN'] = (int)$supCnt['IN'] + $valHO['rankCnt'];
						else
							$supCnt[$valHO['empTag']] =(int)$supCnt[$valHO['empTag']] + $valHO['rankCnt'];
					break;
					case 3:
						if ($valHO['empStat'] == 'RS')
							$offCnt['IN'] = (int)$offCnt['IN'] + $valHO['rankCnt'];
						else
							$offCnt[$valHO['empTag']] =(int)$offCnt[$valHO['empTag']] + $valHO['rankCnt'];
					break;
					case 4:
						if ($valHO['empStat'] == 'RS')
							$ManCnt['IN'] = (int)$ManCnt['IN'] + $valHO['rankCnt'];
						else
							$ManCnt[$valHO['empTag']] =(int)$ManCnt[$valHO['empTag']] + $valHO['rankCnt'];
					break;
					case 5:
						if ($valHO['empStat'] == 'RS')
							$SrMnCnt['IN'] = (int)$SrMnCnt['IN'] + $valHO['rankCnt'];
						else
							$SrMnCnt[$valHO['empTag']] =(int)$SrMnCnt[$valHO['empTag']] + $valHO['rankCnt'];
					break;
					case 6:
						if ($valHO['empStat'] == 'RS')
							$ExecCnt['IN'] = (int)$ExecCnt['IN'] + $valHO['rankCnt'];
						else
							$ExecCnt[$valHO['empTag']] =(int)$ExecCnt[$valHO['empTag']] + $valHO['rankCnt'];
					break;
				}
			}
			switch($valRank['rankCode']) {
				case 1:
					$this->Cell(10,5,(int)$rfCnt['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$rfCnt['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$rfCnt['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$rfCnt['RG']+(int)$rfCnt['PR']+(int)$rfCnt['CN'],'LR',1,'C','0');	
				break;
				case 2:
					$this->Cell(10,5,(int)$supCnt['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$supCnt['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$supCnt['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$supCnt['RG']+(int)$supCnt['PR']+(int)$supCnt['CN'],'LR',1,'C','0');	
				break;
				case 3:
					$this->Cell(10,5,(int)$offCnt['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$offCnt['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$offCnt['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$offCnt['RG']+(int)$offCnt['PR']+(int)$offCnt['CN'],'LR',1,'C','0');	
				break;
				case 4:
					$this->Cell(10,5,(int)$ManCnt['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ManCnt['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ManCnt['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$ManCnt['RG']+(int)$ManCnt['PR']+(int)$ManCnt['CN'],'LR',1,'C','0');	
				break;
				case 5:
					$this->Cell(10,5,(int)$SrMnCnt['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$SrMnCnt['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$SrMnCnt['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$SrMnCnt['RG']+(int)$SrMnCnt['PR']+(int)$SrMnCnt['CN'],'LR',1,'C','0');	
				break;
				case 6:
					$this->Cell(10,5,(int)$ExecCnt['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ExecCnt['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ExecCnt['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$ExecCnt['RG']+(int)$ExecCnt['PR']+(int)$ExecCnt['CN'],'LR',1,'C','0');	
				break;
			}	
			$totrfHO=	(int)$rfCnt['RG'] +	(int)$supCnt['RG'] + (int)$offCnt['RG'] + (int)$ManCnt['RG'] + (int)$SrMnCnt['RG'] + (int)$ExecCnt['RG'];
			$totsupHO=	(int)$rfCnt['PR'] +	(int)$supCnt['PR'] + (int)$offCnt['PR'] + (int)$ManCnt['PR'] + (int)$SrMnCnt['PR'] + (int)$ExecCnt['PR'];
			$totoffHO=	(int)$rfCnt['CN'] +	(int)$supCnt['CN'] + (int)$offCnt['CN'] + (int)$ManCnt['CN'] + (int)$SrMnCnt['CN'] + (int)$ExecCnt['CN'];
			//$totmanHO=	(int)$rfCnt['IN'] +	(int)$supCnt['IN'] + (int)$offCnt['IN'] + (int)$ManCnt['IN'] + (int)$SrMnCnt['IN'];
			$totsrmanHO =	(int)$totrfHO+(int)$totsupHO+(int)$totoffHO;//+(int)$totmanHO;
			unset($rfCnt,$supCnt,$offCnt,$ManCnt,$SrMnCnt,$ExecCnt);

		}
		$this->Cell(110,0,'','1','1');		
		//$this->Ln(2);
		$this->SetFont('Arial','B','8'); 			
		$this->Cell(110,5,'STORE BASED','1','1');
		
			$qrySB = "Select empStat,empRank,count(empRank)rankCnt,employmentTag as empTag from tblEmpMast emp inner join tblRankType rank on emp.empRank = rank.rankCode where empBrnCode='".$ab."' and empStat IN ('RG','RS') and emp.empDiv<>'7' group by empStat,empRank,employmentTag order by empStat,empRank";
			$resSB = $this->getArrRes($this->execQry($qrySB));
		
		foreach($arrRank as $valRank){
			$this->SetFont('Arial','','7'); 
			$this->Cell(55,5,"     ".$valRank['rankDesc'],'LR','0','','0');
			$cnt=0;
			foreach($resSB as $valSB){
				switch($valSB['empRank']) {
					case 1:
						if ($valSB['empStat'] == 'RS')
							$rfCntSB['IN'] = (int)$rfCntSB['IN'] + $valSB['rankCnt'];
						else
							$rfCntSB[$valSB['empTag']] =(int)$rfCntSB[$valSB['empTag']] + $valSB['rankCnt'];
					break;
					case 2:
						if ($valSB['empStat'] == 'RS')
							$supCntSB['IN'] = (int)$supCntSB['IN'] + $valSB['rankCnt'];
						else
							$supCntSB[$valSB['empTag']] =(int)$supCntSB[$valSB['empTag']] + $valSB['rankCnt'];
					break;
					case 3:
						if ($valSB['empStat'] == 'RS')
							$offCntSB['IN'] = (int)$offCntSB['IN'] + $valSB['rankCnt'];
						else
							$offCntSB[$valSB['empTag']] =(int)$offCntSB[$valSB['empTag']] + $valSB['rankCnt'];
					break;
					case 4:
						if ($valSB['empStat'] == 'RS')
							$ManCntSB['IN'] = (int)$ManCntSB['IN'] + $valSB['rankCnt'];
						else
							$ManCntSB[$valSB['empTag']] =(int)$ManCntSB[$valSB['empTag']] + $valSB['rankCnt'];
					break;
					case 5:
						if ($valSB['empStat'] == 'RS')
							$SrMnCntSB['IN'] = (int)$SrMnCntSB['IN'] + $valSB['rankCnt'];
						else
							$SrMnCntSB[$valSB['empTag']] =(int)$SrMnCntSB[$valSB['empTag']] + $valSB['rankCnt'];
					break;
					case 6:
						if ($valSB['empStat'] == 'RS')
							$ExecCntSB['IN'] = (int)$ExecCntSB['IN'] + $valSB['rankCnt'];
						else
							$ExecCntSB[$valSB['empTag']] =(int)$ExecCntSB[$valSB['empTag']] + $valSB['rankCnt'];
					break;
				}
			}
			switch($valRank['rankCode']) {
				case 1:
					$this->Cell(10,5,(int)$rfCntSB['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$rfCntSB['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$rfCntSB['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$rfCntSB['RG']+(int)$rfCntSB['PR']+(int)$rfCntSB['CN'],'LR',1,'C','0');	
				break;
				case 2:
					$this->Cell(10,5,(int)$supCntSB['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$supCntSB['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$supCntSB['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$supCntSB['RG']+(int)$supCntSB['PR']+(int)$supCntSB['CN'],'LR',1,'C','0');	
				break;
				case 3:
					$this->Cell(10,5,(int)$offCntSB['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$offCntSB['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$offCntSB['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$offCntSB['RG']+(int)$offCntSB['PR']+(int)$offCntSB['CN'],'LR',1,'C','0');	
				break;
				case 4:
					$this->Cell(10,5,(int)$ManCntSB['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ManCntSB['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ManCntSB['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$ManCntSB['RG']+(int)$ManCntSB['PR']+(int)$ManCntSB['CN'],'LR',1,'C','0');	
				break;
				case 5:
					$this->Cell(10,5,(int)$SrMnCntSB['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$SrMnCntSB['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$SrMnCntSB['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$SrMnCntSB['RG']+(int)$SrMnCntSB['PR']+(int)$SrMnCntSB['CN'],'LR',1,'C','0');	
				break;
				case 6:
					$this->Cell(10,5,(int)$ExecCntSB['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ExecCntSB['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ExecCntSB['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$ExecCntSB['RG']+(int)$ExecCntSB['PR']+(int)$ExecCntSB['CN'],'LR',1,'C','0');	
				break;
			}
			$totrfSB=	(int)$rfCntSB['RG'] +	(int)$supCntSB['RG'] + (int)$offCntSB['RG'] + (int)$ManCntSB['RG'] + (int)$SrMnCntSB['RG'] + (int)$ExecCntSB['RG'];	
			$totsupSB=	(int)$rfCntSB['PR'] +	(int)$supCntSB['PR'] + (int)$offCntSB['PR'] + (int)$ManCntSB['PR'] + (int)$SrMnCntSB['PR'] + (int)$ExecCntSB['PR'];		
			$totoffSB=	(int)$rfCntSB['CN'] +	(int)$supCntSB['CN'] + (int)$offCntSB['CN'] + (int)$ManCntSB['CN'] + (int)$SrMnCntSB['CN'] + (int)$ExecCntSB['CN'];		
			//$totmanSB=	(int)$rfCntSB['IN'] +	(int)$supCntSB['IN'] + (int)$offCntSB['IN'] + (int)$ManCntSB['IN'] + (int)$SrMnCntSB['IN'];		
			$totsrmanSB = (int)$totrfSB+(int)$totsupSB+(int)$totoffSB;//+(int)$totmanSB;
			unset($rfCntSB,$supCntSB,$offCntSB,$ManCntSB,$SrMnCntSB,$ExecCntSB);
		}
		$this->SetFont('Arial','B','8'); 		
		$this->Cell(110,3,'','1','1');
		$this->Cell(55,5,'TOTAL','1','0');
		$this->Cell(10,5,(int)$totrfHO+(int)$totrfSB,'1','0','C','0');	
		$this->Cell(10,5,(int)$totsupHO+(int)$totsupSB,'1','0','C','0');	
		$this->Cell(10,5,(int)$totoffHO+(int)$totoffSB,'1','0','C','0');	
		$this->Cell(10,5,(int)$totmanHO+(int)$totmanSB,'1','0','C','0');	
		$this->Cell(15,5,(int)$totsrmanHO+(int)$totsrmanSB,'1',1,'C','0');	

		$this->Cell(110,0,'','1','1');		
		$this->Ln(2);
		$this->SetFont('Arial','B','8'); 			
		$this->Cell(110,5,'OPERATIONS','1','1');
		$this->Cell(110,3,'','1','1');
			$qrySO = "Select empStat,empRank,count(empRank)rankCnt,employmentTag as empTag from tblEmpMast emp inner join tblRankType rank on emp.empRank = rank.rankCode where empBrnCode='".$ab."' and empStat IN ('RG','RS') and emp.empDiv='7' group by empStat,empRank,employmentTag order by empStat,empRank";
			$resSO = $this->getArrRes($this->execQry($qrySO));
		
		foreach($arrRank as $valRank){
			$this->SetFont('Arial','','7'); 
			$this->Cell(55,5,"     ".$valRank['rankDesc'],'LR','0','','0');
			$cnt=0;
			foreach($resSO as $valSO){
				switch($valSO['empRank']) {
					case 1:
						if ($valSO['empStat'] == 'RS')
							$rfCntSO['IN'] = (int)$rfCntSO['IN'] + $valSO['rankCnt'];
						else
							$rfCntSO[$valSO['empTag']] =(int)$rfCntSO[$valSO['empTag']] + $valSO['rankCnt'];
					break;
					case 2:
						if ($valSO['empStat'] == 'RS')
							$supCntSO['IN'] = (int)$supCntSO['IN'] + $valSO['rankCnt'];
						else
							$supCntSO[$valSO['empTag']] =(int)$supCntSO[$valSO['empTag']] + $valSO['rankCnt'];
					break;
					case 3:
						if ($valSO['empStat'] == 'RS')
							$offCntSO['IN'] = (int)$offCntSO['IN'] + $valSO['rankCnt'];
						else
							$offCntSO[$valSO['empTag']] =(int)$offCntSO[$valSO['empTag']] + $valSO['rankCnt'];
					break;
					case 4:
						if ($valSO['empStat'] == 'RS')
							$ManCntSO['IN'] = (int)$ManCntSO['IN'] + $valSO['rankCnt'];
						else
							$ManCntSO[$valSO['empTag']] =(int)$ManCntSO[$valSO['empTag']] + $valSO['rankCnt'];
					break;
					case 5:
						if ($valSO['empStat'] == 'RS')
							$SrMnCntSO['IN'] = (int)$SrMnCntSO['IN'] + $valSO['rankCnt'];
						else
							$SrMnCntSO[$valSO['empTag']] =(int)$SrMnCntSO[$valSO['empTag']] + $valSO['rankCnt'];
					break;
					case 6:
						if ($valSO['empStat'] == 'RS')
							$ExecCntSO['IN'] = (int)$ExecCntSO['IN'] + $valSO['rankCnt'];
						else
							$ExecCntSO[$valSO['empTag']] =(int)$ExecCntSO[$valSO['empTag']] + $valSO['rankCnt'];
					break;
				}
			}
			switch($valRank['rankCode']) {
				case 1:
					$this->Cell(10,5,(int)$rfCntSO['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$rfCntSO['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$rfCntSO['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$rfCntSO['RG']+(int)$rfCntSO['PR']+(int)$rfCntSO['CN'],'LR',1,'C','0');	
				break;
				case 2:
					$this->Cell(10,5,(int)$supCntSO['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$supCntSO['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$supCntSO['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$supCntSO['RG']+(int)$supCntSO['PR']+(int)$supCntSO['CN'],'LR',1,'C','0');	
				break;
				case 3:
					$this->Cell(10,5,(int)$offCntSO['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$offCntSO['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$offCntSO['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$offCntSO['RG']+(int)$offCntSO['PR']+(int)$offCntSO['CN'],'LR',1,'C','0');	
				break;
				case 4:
					$this->Cell(10,5,(int)$ManCntSO['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ManCntSO['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ManCntSO['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$ManCntSO['RG']+(int)$ManCntSO['PR']+(int)$ManCntSO['CN'],'LR',1,'C','0');	
				break;
				case 5:
					$this->Cell(10,5,(int)$SrMnCntSO['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$SrMnCntSO['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$SrMnCntSO['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$SrMnCntSO['RG']+(int)$SrMnCntSO['PR']+(int)$SrMnCntSO['CN'],'LR',1,'C','0');	
				break;
				case 6:
					$this->Cell(10,5,(int)$ExecCntSO['RG'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ExecCntSO['PR'],'LR','0','C','0');	
					$this->Cell(10,5,(int)$ExecCntSO['CN'],'LR','0','C','0');	
					$this->Cell(10,5,'0','LR','0','C','0');	
					$this->Cell(15,5,(int)$ExecCntSO['RG']+(int)$ExecCntSO['PR']+(int)$ExecCntSO['CN'],'LR',1,'C','0');	
				break;
			}	
			$totrfSO=(int)$rfCntSO['RG'] +	(int)$supCntSO['RG'] + (int)$offCntSO['RG'] + (int)$ManCntSO['RG'] + (int)$SrMnCntSO['RG'] + (int)$ExecCntSO['RG'];		
			$totsupSO=	(int)$rfCntSO['PR'] +	(int)$supCntSO['PR'] + (int)$offCntSO['PR'] + (int)$ManCntSO['PR'] + (int)$SrMnCntSO['PR'] + (int)$ExecCntSO['PR'];	
			$totoffSO=	(int)$rfCntSO['CN'] +	(int)$supCntSO['CN'] + (int)$offCntSO['CN'] + (int)$ManCntSO['CN'] + (int)$SrMnCntSO['CN'] + (int)$ExecCntSO['CN'];	
			//$totmanSO=	(int)$rfCntSO['IN'] +	(int)$supCntSO['IN'] + (int)$offCntSO['IN'] + (int)$ManCntSO['IN'] + (int)$SrMnCntSO['IN'];	
			$totsrmanSO=	(int)$totrfSO+(int)$totsupSO+(int)$totoffSO;//+(int)$totmanSO;	
			unset($rfCntSO,$supCntSO,$offCntSO,$ManCntSO,$SrMnCntSO,$ExecCntSO);
				
			}
		$this->SetFont('Arial','B','8'); 		
		$this->Cell(110,3,'','1','1');
		$this->Cell(55,5,'TOTAL','1','0');
		$this->Cell(10,5,(int)$totrfSO,'1','0','C','0');	
		$this->Cell(10,5,(int)$totsupSO,'1','0','C','0');	
		$this->Cell(10,5,(int)$totoffSO,'1','0','C','0');	
		$this->Cell(10,5,(int)$totmanSO,'1','0','C','0');	
		$this->Cell(15,5,(int)$totsrmanSO,'1',1,'C','0');	
		
		$this->Ln(4);
		$this->SetFont('Arial','B','8'); 	
		$this->Cell(55,5,'OUTSOURCED','1','0','L','0');
		$this->Cell(10,5,'TOTAL','1','1','C','0');
		$this->SetFont('Arial','','7'); 	
		$this->Cell(55,5,'     BAGGER','LR','0','L','0');
		$this->Cell(10,5,'','LR','1','C','0');
		$this->Cell(55,5,'     JANITOR','LR','0','L','0');
		$this->Cell(10,5,'','LR','1','C','0');
		$this->Cell(55,5,'     INTERNAL SECURITY','LR','0','L','0');
		$this->Cell(10,5,'','LR','1','C','0');
		$this->Cell(55,5,'     SECURITY GUARD','LR','0','L','0');
		$this->Cell(10,5,'','LR','1','C','0');
		$this->Cell(55,1,'','1','0','L','0');
		$this->Cell(10,1,'','1','1','C','0');
		$this->SetFont('Arial','B','8');
		$this->Cell(55,5,'TOTAL','1','0','L','0');
		$this->Cell(10,5,'','1','1','C','0');		
		$this->Ln(4);
		$this->SetFont('Arial','B','8'); 	
		$this->Cell(55,5,'CORPORATE MERCHANDISER','1','0','L','0');
		$this->Cell(10,5,'TOTAL','1','1','C','0');
		$this->SetFont('Arial','','7'); 	
		$this->Cell(55,5,'     STATIONARY','LR','0','L','0');
		$this->Cell(10,5,'','LR','1','C','0');
		$this->Cell(55,5,'     ROVING','LR','0','L','0');
		$this->Cell(10,5,'','LR','1','C','0');
		$this->Cell(55,1,'','1','0','L','0');
		$this->Cell(10,1,'','1','1','C','0');
		$this->SetFont('Arial','B','8');
		$this->Cell(55,5,'TOTAL','1','0','L','0');
		$this->Cell(10,5,'','1','1','C','0');		
		$this->Ln(2);
		$this->SetFont('Arial','B','8'); 	
		$this->Cell(55,5,'SUMMARY','0','0','L','0');
		$this->Cell(10,5,'TOTAL','0','1','C','0');
		$this->SetFont('Arial','','7'); 	
		$this->Cell(55,3,'     OFFICE BASED','0','0','L','0');
		$this->SetFont('Arial','B','8'); 
		$this->Cell(10,3,(int)$totsrmanHO+(int)$totsrmanSB,'','1','C','0');
		$this->SetFont('Arial','','7'); 	
		$this->Cell(55,3,'     OPERATIONS BASED','0','0','L','0');
		$this->SetFont('Arial','B','8'); 
		$this->Cell(10,3,(int)$totsrmanSO,'0','1','C','0');
		$this->SetFont('Arial','','7'); 	
		$this->Cell(55,3,'     OUTSOURCED','0','0','L','0');
		$this->SetFont('Arial','B','8');
		$this->Cell(10,3,'0','','1','C','0');
		$this->SetFont('Arial','','7');
		$this->Cell(55,3,'     CORPORATE MERCHANDISERS','0','0','L','0');
		$this->SetFont('Arial','B','8');
		$this->Cell(10,3,'0','','1','C','0');
		$this->Ln(2);
		$this->SetFont('Arial','B','8'); 
		$this->Cell(55,5,'     GRAND TOTAL','0','0','L','0');
		$this->Cell(10,5,(int)$totsrmanHO+(int)$totsrmanSB+(int)$totsrmanSO,'','1','C','0');
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
		$this->Cell(195,1,'','T');
		$this->Ln();
		$this->SetFont('Courier','B',9);
		$this->Cell(235,6,"Generated By : ".$this->printedby['empFirstName']." ".$this->printedby["empLastName"]);
	}
	
	
}
####Initialize object
$pdf=new PDF('P', 'mm', 'LETTER');
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
$pdf->branch = $psObj->getBranchName($_SESSION['company_code'],$_GET['type']);
$pdf->printedby = $psObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
$pdf->rundate=$psObj->currentDateArt();

####Set up for next page
$pdf->AddPage();

####Set up to get all data/details
		$pdf->Data($arrRank);
		
####Set up to show data	
$pdf->Output('HEAD_COUNT_SUMMARY_PROOFLIST.pdf','D');
?>