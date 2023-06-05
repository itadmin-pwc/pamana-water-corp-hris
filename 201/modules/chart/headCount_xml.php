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


	$qryReg = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where empStat= 'RG' $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv,empDepCode   ";
	$qryProb = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where empStat='PR' $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv,empDepCode ";
	$qryCon = "SELECT count(empNo) as ctr,empDiv,empDepCode from tblEmpMast where empStat='CN' $empDiv1 $empDept1 AND (compCode = '{$compCode}') group by empDiv,empDepCode";
	$resReg = $chartObj->getArrRes($chartObj->execQry($qryReg));
	$resProb = $chartObj->getArrRes($chartObj->execQry($qryProb));
	$resCon = $chartObj->getArrRes($chartObj->execQry($qryCon));
?>
<chart>
	<series>
    	<value xid="1">Regular</value>
    	<value xid="2">Probationary</value>
    	<value xid="3">Contractual</value>
    </series>
    <graphs>
<?
foreach ($arrDiv as $valDiv){
?>
  	<graph gid="<?=strtoupper($valDiv['deptShortDesc'])?>" title="<?=strtoupper($valDiv['deptShortDesc'])?>">
    	<value xid="1"><?=$chartObj->GetValue2($resReg,$valDiv['divCode']);?></value>
    		<value xid="2"><?=$chartObj->GetValue2($resProb,$valDiv['divCode']);?></value>
    		<value xid="3"><?=$chartObj->GetValue2($resCon,$valDiv['divCode']);?></value>
  		</graph>
<?
}
?>
	</graphs>
</chart>