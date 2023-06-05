<?
class empProfileTrainingsObj extends commonObj{
	
	function toTrainings($compcode,$empNo){
		$trns=$this->beginTran();
		$qry="Insert into tblTrainings (trainingFrom,
		trainingTo,
		trainingTitle,
		trainingCost,
		trainingBond,
		effectiveFrom,
		effectiveTo,
		empNo,
		compCode,
		date_Added,
		user_Added) values('{$_GET['txtTrainingStart']}',
		'{$_GET['txtTrainingEnd']}',
		'".str_replace("'","''",$_GET['txtTitle'])."',
		'{$_GET['txtCost']}',
		'{$_GET['cmbBond']}',
		'{$_GET['txtEffectivityStart']}',
		'{$_GET['txtEffectivityEnd']}',
		'{$empNo}',
		'{$compcode}',
		'".date("Y-m-d")."',
		'{$_SESSION['user_id']}')";
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
	
	function updateTrainings($compcode,$empNo,$tid){
		$trns=$this->beginTran();
		$qry="Update tblTrainings set trainingFrom='{$_GET['txtTrainingStart']}',
		trainingTo='{$_GET['txtTrainingEnd']}',
		trainingCost='{$_GET['txtCost']}',
		trainingTitle='".str_replace("'","''",$_GET['txtTitle'])."',
		trainingBond='{$_GET['cmbBond']}',
		effectiveFrom='{$_GET['txtEffectivityStart']}',
		effectiveTo='{$_GET['txtEffectivityEnd']}'
		 where empNo='{$empNo}' and compCode='{$compcode}' and training_Id='{$tid}'";
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
		
	function getSpecificTraining($where){
		if($where!=""){
			$where=$where;
			}
		else{
			$where="";
			}	
		$qry="Select * from tblTrainings $where";
		$qryRes=$this->execQry($qry);
		if($this->getRecCount($qryRes)>0){
			return $this->getArrRes($qryRes);
			}
		}
		
	function deleteTraining($where=""){
		if($where!=""){
			$where=$where;
			}
		else{
			$where="";
			}	
		$trns=$this->beginTran();
		$qry="Delete from tblTrainings $where";
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
	}
?>