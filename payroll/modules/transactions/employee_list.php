<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='timesheet_js.js'></script>
<STYLE>@import url('../../style/maintenance_employee.css');</STYLE>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
<STYLE>@import url('../../../js/themes/default.css');</STYLE>
<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
		<div id="TSCont"></div>
		<div id="indicator1" align="center"></div>
</body>
</html>
<SCRIPT>
	pager("employee_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
	function ResigneEmp(){
		var params = $('frmAccessRights').serialize(true);
		if (params['chempNO']==undefined) {
			alert('Please select an employee.');
			return false;
		}
		new Ajax.Request('employee_list_ajax.php?action=resigned&empList='+params['chempNO']+'&pdNumber='+params['pdNumber']+'&pdYear='+params['pdYear'],{
			method : 'get',
			onComplete : function (req){
				intRes = parseInt(req.responseText);
				if(intRes == 1){
					alert('Successfully queud');
					pager("employee_list_ajax.php","TSCont",'load',0,0,'','','','../../../images/');  
				}
				else{
					alert('Saving Failed');
				}
			},
			onCreate : function (){
				$('Submit').disabled=true;
			},
			onSuccess : function (){
				$('Submit').disabled=false;	
			}
		});
	}
	
	function checkAll(forcnt,chldCnt){
			for(i=0;i<=chldCnt-1;i++){
				$('chempNO'+i).checked=true;
			}
	}
	
	function unCheckAll(forcnt,chldCnt){
			for(i=0;i<=chldCnt-1;i++){
				$('chempNO'+i).checked=false;
			}
	}
	
	
</SCRIPT>
