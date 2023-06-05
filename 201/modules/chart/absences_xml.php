<?
session_start();
	session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("chart_obj.php");

$chartObj = new chartObj();
$arrDiv	= $chartObj->getDivisions();
$compCode = $_SESSION['company_code'];
$empNo = $_GET['empNo'];
$Year = $_GET['Year'];
header("Content-Type: xml;");
echo '<?xml version="1.0" encoding="UTF-8"?>';


	 $qryAbsences = "SELECT sum(hrsAbsent) as absent,month(tsdate) as month from tblTimesheethist where empNo='$empNo' and hrsAbsent>0 and compCode='$compCode' and Year(tsdate)='$Year' group by month(tsdate)  order by month(tsdate)";
	 $qryTardy = "SELECT sum(hrsTardy) as tardy,month(tsdate) as month from tblTimesheethist where empNo='$empNo' and hrsTardy>0 and compCode='$compCode' and Year(tsdate)='$Year' group by month(tsdate) order by month(tsdate)";
	 $qryUT = "SELECT sum(hrsUt) as UT,month(tsdate) as month from tblTimesheethist where empNo='$empNo' and hrsUt>0 and compCode='$compCode' and Year(tsdate)='$Year' group by month(tsdate) order by month(tsdate)";
	$resAbsenses = $chartObj->getArrRes($chartObj->execQry($qryAbsences));
	$resTardy = $chartObj->getArrRes($chartObj->execQry($qryTardy));
	$resUT = $chartObj->getArrRes($chartObj->execQry($qryUT));
?>
<chart>
	<series>
        <value xid="0"></value>
        <value xid="1">Jan</value>
    	<value xid="2">Feb</value>
    	<value xid="3">Mar</value>
    	<value xid="4">Apr</value>
    	<value xid="5">May</value>
    	<value xid="6">Jun</value>
    	<value xid="7">Jul</value>
    	<value xid="8">Aug</value>
    	<value xid="9">Sep</value>
    	<value xid="10">Oct</value>
    	<value xid="11">Nov</value>
    	<value xid="12">Dec</value>
    </series>
    <graphs>
  	<? if ($_GET['act']=='Absences') { ?>    
		<graph title="Absences" fill_alpha="60" line_width="1" bullet="round" color="#FCD202">
        <value xid="0">0</value>
<?
	for($i=1; $i<=12; $i++) {
		if (count($resAbsenses) != 0) {
			foreach($resAbsenses as $valAbsences) {
				if ($valAbsences['month']==$i) {
	?>
				<value xid="<?=$i?>"><?=$valAbsences['absent']?></value>
	<?			} else { ?>
				<value xid="<?=$i?>">0</value>
			<? }
			}
		} else {?>
			<value xid="<?=$i?>">0</value>		
		<? }	
	}
?>
  		</graph>
	<? } elseif ($_GET['act']=='Tardiness') { ?>            
		<graph title="Tardiness" fill_alpha="60" line_width="1" bullet="round" color="#FF9E01">
        <value xid="0">0</value>
<?
	for($i=1; $i<=12; $i++) {
		if (count($resTardy) != 0) {
			foreach($resTardy as $valTardy) {
				if ($valTardy['month']==$i) {
	?>
				<value xid="<?=$i?>"><?=$valTardy['tardy']?></value>
	<?			} else { ?>
				<value xid="<?=$i?>">0</value>
			<? }
			}
		} else {?>
			<value xid="<?=$i?>">0</value>		
		<? }	
	}
?>
  		</graph>
<? } elseif ($_GET['act']=='Undertime') { ?>            
	<graph title="Undertime" fill_alpha="60" line_width="1" bullet="round" color="#0D8ECF">
        <value xid="0">0</value>
<?
	for($i=1; $i<=12; $i++) {
		if (count($resUT) != 0) {
			foreach($resUT as $valUT) {
				if ($valUT['month']==$i) {
	?>
				<value xid="<?=$i?>"><?=$valUT['UT']?></value>
	<?			} else { ?>
				<value xid="<?=$i?>">0</value>
			<? }
			}
		} else {?>
			<value xid="<?=$i?>">0</value>		
		<? }	
	}
?>
  		</graph>            
<? }?>    
        
	</graphs>
</chart>