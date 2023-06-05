// JavaScript Document
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	10/09/2010
	Function		:	Common Trans, js, obj, ajax instead of using timesheet 
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
		var shiftCode = document.frmTS.shiftcode.value;
	  	
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
				  'common_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&thisValue=verifyEmp&fileName='+fileName+'&empBrnCode='+empBrnCode+'&shiftCode='+shiftCode,
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
		var empNo=document.frmTS.empNo.value;
		var optionId=document.frmTS.hide_option.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
		var shiftCode = document.frmTS.shiftcode.value;
		
		new Ajax.Request(
		  'common_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&thisValue='+thisValue+'&empBrnCode='+empBrnCode+'&shiftCode='+shiftCode+'&empNo='+empNo,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function getEmpDept(inputId) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		new Ajax.Request(
		  'common_ajax.php?hide_empDept='+hide_empDept+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
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
		  'common_ajax.php?hide_empSect='+hide_empSect+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('deptSect').innerHTML=req.responseText;
			 }
		  }
		);

	}
	
	
	function option_button_click(id_ko) {
		var option_button = document.getElementById(id_ko).value;
		document.frmTS.hide_option.value = option_button;
		document.frmTS.submit();
	}
	
	