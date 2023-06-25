<?
class empProfileAllowanceObj extends commonObj{
	
	function toEmpProfileAllowance($compCode,$empNo){
			if($_GET['txtallwsked']==""){
				$sked="";
				}
			else
				{
				$sked=$_GET['txtallwsked'];	
				}	
			if($_GET['txtallowtag']=="M" || $_GET['txtallowtag']=="D"){
				$tag=$_GET['txtallowtag'];
				}
			else{
				$tag="";
				}
			$qry="Insert into tblAllowance_New 				
			(compCode,
			empNo,
			allowCode,
			allowAmt,
			allowSked,
			allowPayTag,
			allowStart,
			allowStat,
			sprtPS,
			allowTag,
			dateAdded,
			userAdded) 
			values('{$compCode}',
			'{$empNo}',
			'{$_GET['AllowType']}',
			'".(float)$_GET['txtAllwAmount']."',
			'{$sked}',
			'P',
			'{$this->dateFormat($_GET['txtAllwStart'])}',
			'A',
			'{$this->getAllowanceType($_GET['AllowType'])}',
			'{$tag}',
			'".$this->dateFormat(date("Y-m-d"))."',
			'{$_SESSION['user_id']}')";
			$resAllowance = $this->execQry($qry);
			if($resAllowance){
				return true;
				}
			else{
				return false;
				}	
		}
	
	function empProfileCheckAllowance($compCode,$empNo){
		$qry="Select * from tblAllowance_New where compCode='{$compCode}'
			and empNo='{$empNo}'
			and allowCode='{$_GET['AllowType']}'";
		return $this->execQry($qry);
	}	
		
		
	function updateEmpProfileAllowance($compCode,$empNo,$seriesNo){
			if($_GET['txtallwsked']==""){
				$sked="";
				}
			else
				{
				$sked=$_GET['txtallwsked'];	
				}	
			if($_GET['txtallowtag']=="M" || $_GET['txtallowtag']=="D"){
				$tag=$_GET['txtallowtag'];
				}
			else{
				$tag="";
				}		
			$qrys="Update tblAllowance_New set allowCode='{$_GET['AllowType']}',
			allowAmt='".(float)$_GET['txtAllwAmount']."',
			allowSked='{$sked}',
			allowPayTag='P',
			allowStart='{$_GET['txtAllwStart']}',
			allowStat='A',
			sprtPS='{$this->getAllowanceType($_GET['AllowType'])}',
			allowTag='{$tag}'
			where empNo='{$empNo}' and compCode='{$compCode}'  and allowSeries='{$seriesNo}'";	
			$resUpdateAllowance=$this->execQry($qrys);
			if($resUpdateAllowance){
				return true;
				}
			else{
				return false;
				}	
		}	
		
	function getEmployeeProfileAllowance($compCode,$empNo){
		$qry="SELECT tblAllowType.allowDesc,
		 	dbo.tblAllowance_New.compCode,
		  	dbo.tblAllowance_New.empNo, 
			dbo.tblAllowance_New.allowCode, 
			dbo.tblAllowance_New.allowAmt, 
			dbo.tblAllowance_New.allowSked, 
			dbo.tblAllowance_New.allowTaxTag, 
			dbo.tblAllowance_New.allowPayTag, 
			dbo.tblAllowance_New.allowStart, 
            dbo.tblAllowance_New.allowEnd, 
			dbo.tblAllowance_New.allowStat, 
			dbo.tblAllowance_New.allowSeries, 
			dbo.tblAllowance_New.sprtPS, 
			dbo.tblAllowance_New.allowTag, 
			dbo.tblAllowance_New.dateAdded, 
			dbo.tblAllowance_New.dateReleased, 
			dbo.tblAllowance_New.userAdded
			FROM  tblAllowType INNER JOIN
            dbo.tblAllowance_New ON tblAllowType.allowCode = dbo.tblAllowance_New.allowCode 
			where dbo.tblAllowance_New.compCode='{$compCode}' and dbo.tblAllowance_New.empNo='{$empNo}'";
		return $this->execQry($qry);
//		return $this->getArrRes($resQryEmpAllProf);
		}
		
	function deleteEmpProfileAllowance($wheres){
		if($wheres!=""){
			$where=$wheres;
			}
		else{
			$where="";
			}	
		$res="Delete from tblAllowance_New $where";
		$resDelete=$this->execQry($res);
		if($resDelete){
			return true;
			}
		else{
			return false;
			}	
		}
			
	function getAllowanceType($allowcode){
		if($allowcode!=""){
			$allowancecode=$allowcode;
			}
		else{
			$allowancecode="";
			}	
		$qry="Select * from tblAllowType where allowCode='$allowancecode' and compCode='".$_SESSION['company_code']."'";
		$res=$this->getSqlAssoc($this->execQry($qry));
		return $res['sprtPS'];
		}
	
	function getAllowSked($allowtype){
		if($allowtype!=""){
				$alltype=$allowtype;
			}
		else{
				$alltype="";
			}
		$res="Select * from tblAllowType where allowCode='$allowtype' and compCode='".$_SESSION['company_code']."'";	
		$resAllowance=$this->execQry($res);
		return $this->getArrRes($resAllowance);
		}	
	
	function getSpecificEmpProfAllow($where){
		if($where!=""){
			$wheres=$where;
			}
		else{
			$wheres="";
			}	
		$qry="Select * from tblAllowance_New $wheres";
		$resQry=$this->execQry($qry);
		return $this->getArrRes($resQry);
		}
}


?>