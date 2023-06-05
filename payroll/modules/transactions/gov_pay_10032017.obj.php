<?
	class govPayObj extends commonObj {
		
		function companyName()
		{
			$qryCompName = "Select * from tblCompany where compCode='".$_SESSION["company_code"]."'";
			$rsCompName = $this->execQry($qryCompName);
			
			return $this->getSqlAssoc($rsCompName);
		}
		
		function listGovAgencies()
		{
			$qryGovAgencies = "Select * from tblGovAgencies order by agencyDesc";
			$rsGovAgencies = $this->execQry($qryGovAgencies);
			
			return $this->getArrRes($rsGovAgencies);
		}
		
		function getAllPeriod() 
		{ 
			$fieldName = "date_format(pdPayable,'%M') AS perMonth";
			$groupby = "date_format(pdPayable,'%M')";
			
						
			$qry = "SELECT $fieldName , 
					date_format(pdPayable,'%m') AS pdNumber
					FROM tblPayPeriod
					WHERE compCode = '".$_SESSION["company_code"]."' AND payGrp = '".$_SESSION["pay_group"]."' AND payCat = '".$_SESSION["pay_category"]."'
					and pdYear='".date("Y")."'
					and date_format(pdPayable,'%M') is not null 
					GROUP BY concat(date_format(pdPayable,'%M') , ' ' , date_format(pdPayable,'%Y')),$groupby 
					ORDER BY MAX(pdPayable)";
			
			$res = $this->execQry($qry);
			return $this->getArrRes($res);
		}
		
		function getAllpdYear()
		{
			$qrypdYear = "Select distinct(pdYear) as pdYear from tblPayPeriod
							where compCode='".$_SESSION["company_code"]."'
							and payGrp='".$_SESSION["pay_group"]."'
							and payCat='".$_SESSION["pay_category"]."'
							order by pdYear desc
							";
			$respdYear = $this->execQry($qrypdYear);
			return $this->getArrRes($respdYear);
		}
		
		function checkGovPayment($pdYear,$pdMonth,$orNo)
		{
			$qryGovPay = "Select * from tblGovPayments
							where compCode='".$_SESSION["company_code"]."'
							and pdYear='".$pdYear."'
							and pdMonth='".$pdMonth."'
							and orNo='".$orNo."'";
			
			$resGovPay = $this->execQry($qryGovPay);
			return $this->getRecCount($resGovPay);
		}
		
		function addGovPayment($agencyCd,$pdYear,$pdMonth,$bnkName,$orNo,$bnkBrnch,$bnkAdd,$totAmtPaid,$remarks,$paidBy,$datePaid)
		{
			//echo $_SESSION["company_code"].",".$agencyCd.",".$pdYear.",".$pdMonth.",".$bnkName.",".$orNo.",".$bnkBrnch.",".$bnkAdd.",".$totAmtPaid.",".date("Y-m-d").",".$remarks.",".$paidBy;
			$bnkName = ($bnkName!=''?"'".str_replace("'","''",stripslashes($bnkName))."'":"NULL");
			$bnkBrnch = ($bnkBrnch!=''?"'".str_replace("'","''",stripslashes($bnkBrnch))."'":"NULL");
			$bnkAdd = ($bnkAdd!=''?"'".str_replace("'","''",stripslashes($bnkAdd))."'":"NULL");
			$remarks = ($remarks!=''?"'".str_replace("'","''",stripslashes($remarks))."'":"NULL");
			
			$qryIns = "Insert into tblGovPayments(compCode,agencyCd,pdYear,
						pdMonth,bnkName,orNo,bnkBrnch,bnkAdd,totAmtPaid,dateCreated,remarks,
						paidBy,remStatus,datePaid) values ('".$_SESSION['company_code']."','".$agencyCd."','".$pdYear."',
						'".$pdMonth."',".$bnkName.",'".$orNo."',".$bnkBrnch.",".$bnkAdd.",'".sprintf("%01.2f",$totAmtPaid)."','".date("m/d/Y")."',".$remarks.",
						'".str_replace("'","''",stripslashes($paidBy))."','A','$datePaid')";
			$resIns = $this->execQry($qryIns);
			if($resIns){
				return true;
			}
			else {
				return false;
			}	
		}
		
		function getgovAgencyCode($searchf,$type)
		{
			if($type=='1')
				$where = " agencyDesc like '".$searchf."%'";
			else
				$where = " agencyCd='".$searchf."'";
				
			$qrygovAgencyCode = "Select * from tblGovAgencies where $where";
			$rsgovAgencyCode = $this->execQry($qrygovAgencyCode);
			
			return $this->getSqlAssoc($rsgovAgencyCode);
		}
		
		function getDetails($seqId)
		{
			$qryDetails = "Select * from tblGovPayments 
							where
							seqId='".$seqId."'";
			$rsDetails = $this->execQry($qryDetails);
			
			return $this->getSqlAssoc($rsDetails);
		}
		
		function checkGovPayment_Update($agencyCd,$pdYear,$pdMonth,$orNo,$sqlcommand)
		{
			
				
			$qryChk  = "Select * from tblGovPayments where compCode='".$_SESSION["company_code"]."' and agencyCd='".$agencyCd."' and pdYear='".$pdYear."' and pdMonth='".$pdMonth."' and orNo='".$orNo."' ";
			$resChk= $this->execQry($qryChk);
			
			if($sqlcommand=='record')
				return $this->getRecCount($resChk);
			else
				return $this->getSqlAssoc($resChk);
				
		}
		
		function UpdateGovPayment($agencyCd,$pdYear,$pdMonth,$bnkName,$orNo,$bnkBrnch,$bnkAdd,$totAmtPaid,$remarks,$paidBy,$seqId,$datePaid)
		{
			$bnkName = ($bnkName!=''?"'".str_replace("'","''",stripslashes($bnkName))."'":"NULL");
			$bnkBrnch = ($bnkBrnch!=''?"'".str_replace("'","''",stripslashes($bnkBrnch))."'":"NULL");
			$bnkAdd = ($bnkAdd!=''?"'".str_replace("'","''",stripslashes($bnkAdd))."'":"NULL");
			$remarks = ($remarks!=''?"'".str_replace("'","''",stripslashes($remarks))."'":"NULL");
			
			$qryUpdate = "Update tblGovPayments set 
						agencyCd='".$agencyCd."',
						pdYear='".$pdYear."',
						pdMonth='".$pdMonth."',
						bnkName=".$bnkName.",
						orNo='".$orNo."',
						bnkBrnch=".$bnkBrnch.",
						bnkAdd=".$bnkAdd.",
						totAmtPaid='".$totAmtPaid."',
						remarks=".$remarks.",
						paidBy='".str_replace("'","''",stripslashes($paidBy))."',
						datePaid='".$datePaid."'
						where 
						compCode='".$_SESSION["company_code"]."' and
						seqId='".$seqId."'";
			$rsUpdate = $this->execQry($qryUpdate);
			if($rsUpdate){
				return true;
			}
			else {
				return false;
			}	
		}
		
		function delGovPayment($seqId)
		{
			$qryDel = "Update tblGovPayments set remStatus='D' where seqId='".$seqId."' and compCode='".$_SESSION["company_code"]."'";
			$rsDel = $this->execQry($qryDel);
			if($rsDel){
				return true;
			}
			else {
				return false;
			}	
		}
	}
?>
