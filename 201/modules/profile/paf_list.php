<?
session_start();
error_reporting(1);
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("profile_paf_obj.php");

$pafObj = new pafObj($_GET,$_SESSION);
$sessionVars = $pafObj->getSeesionVars();
$pafObj->validateSessions('','MODULES');
$cnt = $_GET['chCtr'];
switch($_GET['action']) {
	case "R":
			if ($pafObj->ReleasePAF('R'))
				echo "
					pager('paf_list_ajax.php','TSCont','load',0,0,'','','&stat=R','../../../images/'); 			
					alert('PAF Records Status successfully changed.');";
			else
				echo "alert('Error changing PAF Status.');";	
		exit();	
	break;
	case "H":
			if ($pafObj->ReleasePAF('H'))
				echo "
					pager('paf_list_ajax.php','TSCont','load',0,0,'','','&stat=H','../../../images/'); 			
					alert('PAF Records Status successfully changed.');";
			else
				echo "alert('Error changing PAF Status.');";	
			
			exit();	
	break;
	case "U":
			
			if ($pafObj->ProcessPAF()) {
				echo "
					pager('paf_list_ajax.php','TSCont','load',0,0,'','','&stat=P','../../../images/'); 			
					alert('PAF successfully processed.');";
			} else {
				echo "alert('Error processing PAF .');";	
			}	
		exit();	
	break;
	case "UP":
			
			
	$Release = $pafObj->ReleasePAF('R');
			if ($Release){

				$Process = $pafObj->ProcessPAF();
			}
			
			if ($Release && $Process){
				echo "successPAF();";
			}
			else{
				echo "alert('Error processing PAF!');";	
				if($pafObj->rollbackPAF()){
					echo "alert('All errors occured during the process has been rolled back! Kindly re-post the PAF...');";	
				}
				else{
					echo "alert('Failed to rollback the errors! Please call IT Department to fix the error...');";	
				}
			}
		exit();	
	break;	
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type='text/javascript' src='movement.js'></script>
		<STYLE>@import url('../../style/reports.css');</STYLE>
	</HEAD>
	<BODY>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
	</BODY>
</HTML>
<SCRIPT>
	pager("paf_list_ajax.php","TSCont",'load',0,0,'','','&stat=H','../../../images/');  
	function ChangeStat(stat) {
		pager("paf_list_ajax.php","TSCont",'load',0,0,'','','&stat='+stat,'../../../images/');
	}
	
	function CheckAll(){
		var cnt = $('chCtr').value;
		var chkcnt=1;
		for(i=0;i<=cnt;i++){
			if ($('chAll').checked==false) {
				$('chPAF'+i).checked=false;
				$('checker').value = chkcnt++;
			} else {
				$('chPAF'+i).checked=true;
				$('checker').value = chkcnt++;
			}
		}
	}

	function Release(act) {
		if (act=='U') {
			if ($('pafStat').value=='H') {
				alert('Please Release first the selected PAF(s) before posting.');
				return false;
			}
		}
		var cnt = $('chCtr').value;
		if ($('checker').value==0) {
			alert('Please select PAF.');
			return false;
		}
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			parameters : $('frmPAF').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			},
			onCreate : function(){
				$('btnRelUp').disabled=true;	
			},
		});				
	}
	
	function printPAF() {
		if ($('checker').value==0) {
			alert('Please select PAF.');
			return false;
		}
		window.open('paf_pdf.php?'+$('frmPAF').serialize());
/*		new Ajax.Request('paf_pdf.php',{
			method : 'get',
			parameters : $('frmPAF').serialize(),
			onComplete : function (req){
				eval(req.responseText);	
			}
		});				
*/	}
	
	function check(name) {
		var cnt = $('chCtr').value;
		var chkcnt=1;
		$('checker').value = 0;
		for(i=0;i<=cnt;i++){
			if ($('chPAF'+i).checked==true) {
				$('checker').value = chkcnt++;
				;
			} 
		}
		//var chkno =chkcnt;
	}
	
	function successPAF()
	{
		alert("PAF successfully processed.");
		pager("paf_list_ajax.php","TSCont",'load',0,0,'','','&stat=H','../../../images/');  
	}

</SCRIPT>