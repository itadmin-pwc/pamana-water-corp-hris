<?
session_start();
	session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("chart_obj.php");

$chartObj = new chartObj();
$arrDiv	= $chartObj->getDivisions();
$compCode = $_SESSION['company_code'];
$empDiv        			= $_GET['empDiv'];
if ($empDiv>"" && $empDiv>0) {
	$empDiv1 = " AND (empDiv = '{$empDiv}')";
	$div 	 = " AND (divCode = '{$empDiv}')";
} else {
	$empDiv1 = "";
}
header("Content-Type: xml;");
echo '<?xml version="1.0" encoding="UTF-8"?>';


	 $qry4below = "SELECT count(empNo) as ctr,empDiv from tblEmpMast where datediff(CURDATE(),dateHired) between 0 and 4 and empDiv<>0 $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv   ";
	$qry5to9 = "SELECT count(empNo) as ctr,empDiv from tblEmpMast where datediff(CURDATE(),dateHired) between 5 and 10 and empDiv<>0 $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv ";
	$qry10up = "SELECT count(empNo) as ctr,empDiv from tblEmpMast where datediff(CURDATE(),dateHired) > 10 and empDiv<>0 $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv";
	$res4below = $chartObj->getArrRes($chartObj->execQry($qry4below));
	$res5to9 = $chartObj->getArrRes($chartObj->execQry($qry5to9));
	$res10up = $chartObj->getArrRes($chartObj->execQry($qry10up));
?>
<chart>
	<series>
    	<value xid="1">Less 5 Yrs</value>
    	<value xid="2">5-10 Yrs</value>
    	<value xid="3">10+ Yrs</value>
    </series>
    <graphs>
<?
foreach ($arrDiv as $valDiv){
?>
  	<graph gid="<?=strtoupper($valDiv['deptShortDesc'])?>" title="<?=strtoupper($valDiv['deptShortDesc'])?>">
    	<value xid="1"><?=$chartObj->GetValue($res4below,$valDiv['divCode']);?></value>
    		<value xid="2"><?=$chartObj->GetValue($res5to9,$valDiv['divCode']);?></value>
    		<value xid="3"><?=$chartObj->GetValue($res10up,$valDiv['divCode']);?></value>
  		</graph>
<?
}
?>
	</graphs>
</chart>