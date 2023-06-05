<?
/*
	Date Created	:	072010
	Created By		:	Genarra Arong
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("transaction_obj.php");


?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
        <script type="text/javascript" src="../../../includes/calendar.js"></script>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
    	
		<FORM name="frmMassUp" id="frmMassUp" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			
            <div id="updateEmpShiftCont"></div>
			
            
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	pager('update_employee_shift_popAjaxResult.php','updateEmpShiftCont','load',0,0,'','','&empNo='<?=$_GET["empNo"]?>,'../../../images/');  
	
	function getListofEmp()
	{
		var brnCd= document.frmMassUp.brnCd.value;
		pager('update_employee_shift_popAjaxResult.php','updateEmpShiftCont','load',0,0,'','','&action=getListEmployee&disShifts=1&brnCd='+brnCd,'../../../images/');  
	}
	
	function getShiftCodeDetail()
	{
		var brnCd= document.frmMassUp.brnCd.value;
		var shiftCode= document.frmMassUp.shiftcode.value;
		pager('update_employee_shift_popAjaxResult.php','updateEmpShiftCont','load',0,0,'','','&action=getShiftCodeDtl&disShifts=1&brnCd='+brnCd+'&shiftCode='+shiftCode,'../../../images/'); 
		
	}
	
	function saveMssUpdate()
	{
		var conMassUpdate = confirm('Are you sure you want apply the Selected Shift Code to the current Shift Codes of the employee based on the Selected Branch?');
		
		var brnCd= document.frmMassUp.brnCd.value;
		var shiftCode= document.frmMassUp.shiftcode.value;
		
		if(conMassUpdate == true){
				pager('update_employee_shift_popAjaxResult.php','updateEmpShiftCont','load',0,0,'','','&action=AppMassUpdate&disShifts=1&brnCd='+brnCd+'&shiftCode='+shiftCode,'../../../images/'); 
		
				
		}
	}
</SCRIPT>