<?
session_start();
//include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
/*if ($_SESSION['user_level'] == 3) {
	$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
	
	if($userinfo['empLocCode']=='0001'){
		$brnCode = " $and empLocCode='".$userinfo['empLocCode']."'";
		$brnCodelist = " AND  empLocCode='".$userinfo['empLocCode']."'";
	}
	
	
	else{
		if($userinfo['empLocCode']!='0001'){
			$brnCode = " $and empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCodelist = " AND empBrnCode = '{$userinfo['empBrnCode']}' and empLocCode='".$userinfo['empLocCode']."'";
			$brnCode_View = 1;
		}
	}
}*/
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');

if(isset($_GET['btnSearchReg'])){
	//$viewRegNoCustNo = "and cust.custNo is null";	
	echo "alert('nhomer');";
}

if($brnCode_View ==""){
	$arrBrnch = $common->makeArr($common->getBranchByCompGrp(" and brnDefGrp='".$_SESSION["pay_group"]."' and compCode='".$_SESSION["company_code"]."'"),'brnCode','brnDesc','All');
}
$empStat = " AND empStat NOT IN('RS','IN','TR') and employmentTag='RG' $viewRegNoCustNo";
$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}' 
				 AND empPayGrp<>''
				 $empStat
				 and empPayCat<>0 ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }

$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT  emp.empNo,empLastName,empFirstName,empMidName,custNo
		FROM tblEmpMast emp left join tblCustomerNo cust on emp.empNo=cust.empNo
		WHERE  empPayCat<>0 $empStat and empPayCat<>''
		AND empPayGrp<>'' 
		AND emp.compCode = '{$sessionVars['compCode']}' ";
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND emp.empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
$qryEmpList .= " limit $intOffset,$intLimit";
$sqlEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($sqlEmpList);

?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Customer Master List</td>
		</tr>
		<tr>
			<td colspan="4" height="25">&nbsp;</td>
		</tr>
		<tr><td colspan="4">
        <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="7" class="gridToolbar" align="center">
<input type="button" name="btnSearchReg" id="btnSearchReg" value="Regular Employees with out Customer No."  class="inputs" onclick="showRegEmp('customer_list_allRegEmp.php?act=Add&empNo=<?=$empListVal['empNo']?>&custNo=<?=$empListVal['custNo']?>','List of Regular Employees w/o Customer Nnumber','','customer_list_ajax.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')">
					  <input type="button" name="btnSearchTrans" id="btnSearchTrans" value="Transferred Employees"  class="inputs" onclick="showTransEmp('transferred_employees.php?act=Add&amp;empNo=<?=$empListVal['empNo']?>&amp;custNo=<?=$empListVal['custNo']?>','Transferred Employees','','customer_list_ajax.php','empMastCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" /></td>
				</TABLE></td>
        </tr>
		<tr>
			<td colspan="4" height="25">&nbsp;</td>
		</tr>        
	</TABLE>
</div>
<?$common->disConnect();?>
