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
		
			 $this->connID = mysqli_connect(self::HOST,self::USER,self::PASS,'hris_office_warehouse_testdb');
		
		return $this->connID;
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
		$this->qry_id = mysqli_query(self::__construct(),$qry);
		if (!$this->qry_id)
			echo "$qry\n";
		return $this->qry_id;
	}	

	function execMultiQryI($qry){
		$this->qry_id = mysqli_multi_query(self::__construct(),$qry);
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
		mysqli_next_result(self::__construct());
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
		mysqli_autocommit(self::__construct(),FALSE);
		return true;
		//return $this->execQryI("START TRANSACTION");
	}
	
	function commitTranI(){
		//var_dump($this->connID);
		mysqli_commit(self::__construct());
		//$this->disConnectI();
		return true;
		//return $this->execQryI("COMMIT TRANSACTION");
	}
	
	function rollbackTranI(){
		return mysqli_rollback(self::__construct());
		//return $this->execQryI("ROLLBACK TRANSACTION");
	}   
   
	function disConnectI(){
		return mysqli_close(self::__construct());
	}
	
}


?>
