<?
class empProfilePerformanceObj extends commonObj{
	
	
	function toPerformance($compcode,$empNo){
			$trns=$this->beginTran();	
			if($_GET['txtoldemprate']==""){
				$oldemprate=0;
				}
			else{
				$oldemprate=$_GET['txtoldemprate'];
				}	
			if($_GET['txtnewemprate']==""){
				$newemprate=0;
				}
			else{
				$newemprate=$_GET['txtnewemprate'];
				}		
			$qry="Insert into tblPerformance (performanceFrom,
			performanceTo,
			performanceNumerical,
			performanceAdjective,
			performancePurpose,
			empNo,
			compCode,
			date_Added,
			user_Added,
			old_empDrate,
			new_empDrate,
			remarks) values('{$_GET['txtPerformanceStart']}',
			'{$_GET['txtPerformanceEnd']}',
			'{$_GET['cmbNumerical']}',
			'{$_GET['cmbNumerical']}',
			'{$_GET['cmbPurpose']}',
			'{$empNo}','{$compcode}',
			'".date("Y-m-d")."',
			'{$_SESSION['user_id']}',
			'".$oldemprate."',
			'".$newemprate."',
			'". str_replace("'","''",$_GET['txtremarks'])."')";
			if($trns){
				$trns=$this->execQry($qry);
				}
			if(!$trns){
				$trns=$this->rollbackTran();
				return false;
				}
			else{
				$trns=$this->commitTran();
				return true;
				}	
		}
	
	function updatePerformance($compcode,$empNo,$perid){
		$trns=$this->beginTran();
		if($_GET['txtoldemprate']==""){
			$oldemprate=0;
			}
		else{
			$oldemprate=$_GET['txtoldemprate'];
			}	
		if($_GET['txtnewemprate']==""){
			$newemprate=0;
			}
		else{
			$newemprate=$_GET['txtnewemprate'];
			}				
		$qry="Update tblPerformance set performanceFrom='{$_GET['txtPerformanceStart']}',
		performanceTo='{$_GET['txtPerformanceEnd']}',
		performanceNumerical='{$_GET['cmbNumerical']}',
		performanceAdjective='{$_GET['cmbNumerical']}',
		performancePurpose='{$_GET['cmbPurpose']}',
		old_empDrate='{$oldemprate}',
		new_empDrate='{$newemprate}',
		remarks='".str_ireplace("'","''",$_GET['txtremarks'])."' 
		where compCode='{$compcode}' and empNo='{$empNo}' and performance_Id='{$perid}'";
		if($trns){
				$trns=$this->execQry($qry);
			}
		if(!$trns){
				$trns=$this->rollbackTran();
				return false;
			}	
		else{
				$trns=$this->commitTran();
				return true;
			}	
		}	
		
	function deleteEmpPerformance($where=""){
		if($where!=""){
			$where=$where;
			}
		else{
			$where="";
			}	
		$trns=$this->beginTran();
		$qry="Delete from tblPerformance $where";
		if($trns){
			$trns=$this->execQry($qry);	
			}
		if(!$trns){
			$trns=$this->rollbackTran();
			return false;
			}	
		else{
			$trns=$this->commitTran();
			return true;
			}	
		
		}	
		
	function getSpecificEmpPerformance($where=""){
		if($where!=""){
			$where=$where;
		}	
		else{
			$where="";
			}
		$qry="Select * from tblPerformance $where";
		$resqry=$this->execQry($qry);	
		if($this->getRecCount($resqry)>0){
			return $this->getArrRes($resqry);
			}
	}
}
?>