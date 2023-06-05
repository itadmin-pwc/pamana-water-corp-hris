<?
include("config.php");



class dbHandler {

	const HOST = 'PG-PAYSRV';
	
	const USER = 'pg_payroll';
	
	const PASS = 'pg_2011';
	const DBASE = 'PG_PAYROLL';
	
	var $conn_id;

	var $qry_id;
	
	var $dbConn;
	
	function __construct(){
		
		$this->conn_id = mysql_connect(self::HOST,self::USER,self::PASS);
		if($this->conn_id){
			$this->dbConn = mysql_select_db(self::DBASE);	
		}
		
		return $this->conn_id;
	}

	function execQry($qry){
	
		$this->qry_id = mysql_query($qry,self::__construct());
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
		return $this->execQry("BEGIN TRAN");
	}
	
	function commitTran(){
		return $this->execQry("COMMIT TRAN");
	}
	
	function rollbackTran(){
		return $this->execQry("ROLLBACK TRAN");
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
	
	
}
?>
