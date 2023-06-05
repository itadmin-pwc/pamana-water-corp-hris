<?
session_start();
include("db.inc.php");
include("common.php");
include("pager.inc.php");

$common = new commonObj();
$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

if(!empty($_GET['tmpCompCode'])){
	$compCode =  $_GET['tmpCompCode'];
}
else{
	$compCode =  $_SESSION['company_code'];
}

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');

$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$compCode}' ";
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
        }
if ($_SESSION['pay_category'] == 9) {		
	$qryIntMaxRec .=	"AND empStat IN ('RS','EOC','AWOL')  and empPayGrp='{$_SESSION['pay_group']}' and empNo IN (Select empNo from tblLastPayEmp where compCode='{$_SESSION['company_code']}')
					 ";
} else {					 
	$qryIntMaxRec .=	"AND empStat NOT IN('RS','IN','TR','EOC','AWOL')  and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
					 ";

}
		
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT TOP $intLimit *
		FROM tblEmpMast
		WHERE empNo NOT IN
        (SELECT TOP $intOffset empNo FROM tblEmpMast WHERE compCode = '{$compCode}' and empPayGrp='{$_SESSION['pay_group']}' "; 

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        }  

if ($_SESSION['pay_category'] == 9) 
{
	$qryEmpList .= "AND empStat IN('RS','IN','TR','EOC','AWOL') 
				ORDER BY empLastName) 
				AND compCode = '{$compCode}' ";
}
else
{
		$qryEmpList .= "AND empStat NOT IN('RS','IN','TR','EOC','AWOL') 
				ORDER BY empLastName) 
				AND compCode = '{$compCode}' ";
}

        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        }
if ($_SESSION['pay_category'] == 9) {		
	 $qryEmpList .=	"AND empStat IN ('RS','IN','TR','EOC','AWOL')  and empPayGrp='{$_SESSION['pay_group']}' and empNo IN (Select empNo from tblLastPayEmp where compCode='{$_SESSION['company_code']}')
					 ORDER BY empLastName";
} else {					 
	$qryEmpList .=	"AND empStat NOT IN('RS','IN','TR','EOC','AWOL')  and empPayGrp='{$_SESSION['pay_group']}' and empPayCat='{$_SESSION['pay_category']}'
					 ORDER BY empLastName";

}
				 
//echo 	$qryEmpList;
$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);
?>

			
<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
	<td colspan="7" class="gridToolbar">
		Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
		<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onclick="pager('../../../includes/employee_lookupAjaxResult.php','empLukupCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">
	</td>
	<tr>
		<td width="20%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
		<td width="70%" class="gridDtlLbl" align="center">NAME</td>
	</tr>
	<?
	if($common->getRecCount($resEmpList) > 0){
		$i=0;
		foreach ($arrEmpList as $empListVal){
			
		$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
		$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
		. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
	?>
	<tr style="cursor:pointer;" bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?> onclick="passEmpNo('txtAddEmpNo','<?=$empListVal['empNo']?>');">
		<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
		<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=str_replace('Ñ','&Ntilde;',$empListVal['empLastName']. ", " . $empListVal['empFirstName'] . " " . $empListVal['empMidName'])?></font></td>
	</tr>
	<?
		}
	}
	else{
	?>
	<tr>
		<td colspan="7" align="center">
			<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
		</td>
	</tr>
	<?}?>
	<tr>
		<td colspan="7" align="center" class="childGridFooter">
			<? $pager->_viewPagerButton('../../../includes/employee_lookupAjaxResult.php','empLukupCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&tmpCompCode='.$_GET['tmpCompCode']);?>
		</td>
	</tr>
</TABLE>
				

<?$common->disConnect();?>