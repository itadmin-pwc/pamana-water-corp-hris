<?
/*$arrurl =  explode('/',$_SERVER['SCRIPT_NAME']);
if (in_array('201',$arrurl)) {
	header('Location: http://192.168.200.225/PG-HRIS-SYSTEM/201/index.php');
}
*/include("config.php");


class dbHandler {

	const HOST = 'localhost';
	
	const USER = 'root';
	
	const PASS = '';
	//const DBASE = 'PG_PAYROLL';

	var $conn_id;
	var $qry_id;
	var $dbConn;
	var $connID = false;
	
	function __construct(){
			$this->conn_id = mysql_connect(self::HOST,self::USER,self::PASS,false,65536);
			$this->dbConn = mysql_select_db('hris_office_warehouse');
		//$this->conn_id = mysqli_connect(self::HOST,self::USER,self::PASS,'hris_office_warehouse');
		return $this->conn_id;
	}

	function mysqliconnect(){
		if (!$this->connID)
			 $this->connID = mysqli_connect(self::HOST,self::USER,self::PASS,'hris_office_warehouse');
	
		return $this->connID;
	}

	function execQry($qry){
		$this->qry_id = mysql_query($qry,self::__construct());
		if (!$this->qry_id)
			echo "$qry\n";
		return $this->qry_id;
	}	
	
	function getRecCount($res){
		$cnt = mysql_num_rows($res);
		return $cnt;
	}

	function getSqlAssoc($res){
		$row = mysql_fetch_assoc($res);
		return $row;
	}
	
	function getArrRes($res=NULL){
		
		$arrRes = array();
		
		while($row = mysql_fetch_assoc($res)){
			$arrRes[] = $row;
			 
		}
		return $arrRes;
	}
		
   function getSQLObj($res=NULL){
   	
		return mysql_fetch_object($res);	
   }   	
	
   function getSQLArrayObj($res=NULL){
   	
        $objList = array();
		while($obj = $this->getSQLObj($res)) {
	       $objList[] = $obj;
		   
        }
		return $objList;
   }	
	
	function beginTran(){
		return $this->execQry("START TRANSACTION;");
	}
	
	function commitTran(){
		return $this->execQry("COMMIT");
	}
	
	function rollbackTran(){
		return $this->execQry("ROLLBACK");
	}   
   
	function disConnect(){
		return mysql_close($this->conn_id);
	}

	function DropDownMenu($arrRes,$id,$selected='',$attr){
	
		echo "<select id=\"$id\" name=\"$id\" $attr>\n";
			foreach ((array)$arrRes as $index => $value){
				echo "<option value=\"$index\" ";
				if($index == $selected){
					
					echo "selected=\"selected\">\n";
				}
				else{
					echo " >\n";
				}
					echo ucwords(strtoupper($value))."\n";
				echo "</option>\n";
			}
		echo "</select>\n";
	}	
	
	function execQryI($qry){
		$this->qry_id = mysqli_query($this->mysqliconnect(),$qry);
		if (!$this->qry_id)
			echo "$qry\n";
		return $this->qry_id;
	}	

	function execMultiQryI($qry){
		$this->qry_id = mysqli_multi_query($this->mysqliconnect(),$qry);
		if (!$this->qry_id)
			echo "$qry\n";
		return $this->qry_id;
	}
		
	function getRecCountI($res){
		$cnt = mysqli_num_rows($res);
		return $cnt;
	}

	function getSqlAssocI($res){
		$row = mysqli_fetch_assoc($res);
		return $row;
	}

	function next_result($res=""){
		mysqli_next_result($this->connID);
	}	
	function getArrResI($res=NULL){
		
		$arrRes = array();
		
		while($row = mysqli_fetch_assoc($res)){
			$arrRes[] = $row;
			 
		}
		return $arrRes;
	}	

	function beginTranI(){
		//var_dump($this->connID);
		//mysqli_autocommit($this->connID,FALSE);
		return true;
		//return $this->execQryI("START TRANSACTION");
	}
	
	function commitTranI(){
		//var_dump($this->connID);
		//mysqli_commit($this->connID);
		//$this->disConnectI();
		return true;
		//return $this->execQryI("COMMIT TRANSACTION");
	}
	
	function rollbackTranI(){
		return mysqli_rollback($this->connID);
		//return $this->execQryI("ROLLBACK TRANSACTION");
	}   
   
	function disConnectI(){
		return mysqli_close($this->connID);
	}


	function execqrybio($qry){
		
		$this->qryid=mysqli_query($this->mysqliconnect(),$qry) or die(mysqli_error($this->mysqliconnect()));
		
		if (!$this->qryid)
			echo "$qry\n";
		return $this->qryid;
		}
		function getArrResloadbio($result){
		
		$arrRes = array();
		
		while($row = mysqli_fetch_array($result)){
			$arrRes[] = $row;
		}
		return $arrRes;
	}
}


?>
