<?
class positionObj extends commonObj{
	var $poscode;
	var $desc;
	var $sdesc;
	var $rank;
	var $level;
	var $div;
	var $dept;
	var $sect;
	var $stats;
	var $compCode;
	
	function getRank(){
		$sqlRank="Select * from tblRankType";
		$resRank=$this->execQry($sqlRank);
		return $this->getArrRes($resRank);
		}

	function setQry($tablename){
		if($res=$this->execQry($tablename))
			return true;
		else
			return false;		
		}

	function getEmpLevel(){
		$sqlEmpLevel="Select * from tblEmpLevel";
		$resEmpLevel=$this->execQry($sqlEmpLevel);
		return $this->getArrRes($resEmpLevel);
		}
		
	function recordChecker($where){
		if($where!=""){
			$wherec=$where;
			}
		else{
			$wherec="";
			}
			
		$qryCheck="Select * from tblPosition where posDesc='".str_replace("'","''",$_GET['Desc'])."'
		and posShortDesc='".str_replace("'","''",$_GET['shrtDesc'])."' 
		and rank='".$_GET['cmbRank']."' 
		and level='".$_GET['cmbEmpLevel']."' 
		and divCode='".str_replace("-","",$_GET['cmbDiv'])."' 
		and deptCode='".str_replace("-","",$_GET['cmbDept'])."' 
		and sectCode='".str_replace("-","",$_GET['cmbSection'])."'" . $wherec;
		$qryRes=$this->execQry($qryCheck);
		return $this->getRecCount($qryRes);
		}

	function toPosition(){
		$qry="Insert into tblPosition (compCode,rank,level,posDesc,posShortDesc,Active,divCode,deptCode,sectCode)
			  values('".$_SESSION['company_code']."','".$_GET['cmbRank']."','".$_GET['cmbEmpLevel']."',
			  	'".str_replace("'","''",$_GET['Desc'])."','".str_replace("'","''",$_GET['shrtDesc'])."',
				'".$_GET['cmbStat']."','".str_replace("-","",$_GET['cmbDiv'])."','".str_replace("-","",$_GET['cmbDept'])."',
				'".str_replace("-","",$_GET['cmbSection'])."')";
		return $this->execQry($qry);
		}
		
	function updatePosition($where){
		$qry="Update tblPosition set posDesc='".str_replace("'","''",$_GET['Desc'])."',
				posShortDesc='".str_replace("'","''",$_GET['shrtDesc'])."',rank='".$_GET['cmbRank']."',
				level='".$_GET['cmbEmpLevel']."',divCode='".str_replace("-","",$_GET['cmbDiv'])."',
				deptCode='".str_replace("-","",$_GET['cmbDept'])."',sectCode='".str_replace("-","",$_GET['cmbSection']."'" . $where);
		return $this->execQry($qry);
		}
	
	function getPosition($where=""){
		$qryShow="Select * from tblPosition $where";
		$showRes=$this->execQry($qryShow);
		if($this->getRecCount($showRes)>0){
			return $this->getArrRes($showRes);
			}
		else{
			return $this->getSqlAss($showRes);
			}	
		}
					
	function getQryDept($where=""){
		if($where!=""){
			$whereStatement=$where;
			}
		else{
			$whereStatement="";
			}
			
		$qry="SELECT * FROM tblDepartment $whereStatement";
		$resDept=$this->execQry($qry);
		return $resDept;
		}	
}
?>