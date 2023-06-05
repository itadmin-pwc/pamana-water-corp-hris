// JavaScript Document
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

	function isNumberInputEmpNoOnly(field, event) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var optionId=document.frmTS.hide_option.value;
		var fileName=document.frmTS.fileName.value;
		var payPd=document.frmTS.payPd.value; 
		
	  	var empBrnCode = document.frmTS.sel_BrnCode.value;
		
		var locType = document.frmTS.locType.value;
	  
		var key, keyChar;
		
		if (window.event)
			key = window.event.keyCode;
		else if (event)
			key = event.which;
		else
			return true;
		
		// Check for special characters like backspace
		if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
			if (key == 13) {
				new Ajax.Request(
				  'common_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&thisValue=verifyEmp&fileName='+fileName+'&payPd='+payPd+'&empBrnCode='+empBrnCode+'&locType='+locType,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);	
			}
			return true;
		}
		// Check to see if it's a number
		keyChar =  String.fromCharCode(key);
		if (/\d/.test(keyChar)) 
		{
			window.status = "";
			return true;
		} 
		else 
		{
			window.status = "Field accepts numbers only.";
			return false;
		}
	}
	
	function valSearchTS(thisValue) {
		
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode = document.frmTS.sel_BrnCode.value;
		
		var tbl
		if (thisValue=="Payslip") {
			tbl=document.frmTS.tblEarnType.value;
		}

		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
			
			if((thisValue=='searchTS2')||(thisValue=='searchTS7')||(thisValue=='searchTS6')||(thisValue=='searchTS9')||(thisValue=='searchTS10')){
				var reportType=document.frmTS.reportType.value;
				var topType = document.frmTS.topType.value;
				
				
				new Ajax.Request(
				  'common_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&payPd='+payPd+'&thisValue='+thisValue+'&reportType='+reportType+'&topType='+topType+'&empBrnCode='+empBrnCode,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
			}else{
				new Ajax.Request(
				  'common_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&payPd='+payPd+'&thisValue='+thisValue+'&tbl='+tbl+'&empBrnCode='+empBrnCode,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
			}
	}
	
	function getEmpDept(inputId) {
		
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empDept='+hide_empDept+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('deptDept').innerHTML=req.responseText;
			 }
		  }
		);
	}
	
	function getEmpSect(inputId) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('deptSect').innerHTML=req.responseText;
			 }
		  }
		);

	}
	
	function getPayPd(inputId) {
		var payPd=document.frmTS.payPd.value;
		var hide_payPd=document.frmTS.hide_payPd.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_payPd='+hide_payPd+'&inputId='+inputId+'&payPd='+payPd+'&groupType='+groupType+'&catType='+catType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('pdPay').innerHTML=req.responseText;
			 }
		  }
		);

	}
	
	function option_button_click(id_ko) {
		var option_button = document.getElementById(id_ko).value;
		document.frmTS.hide_option.value = option_button;
		document.frmTS.submit();
	}
	
	