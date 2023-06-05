// JavaScript Document
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
		var orderBy=document.frmTS.orderBy.value;
		
	  	var empBrnCode = document.frmTS.empBrnCode.value;
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
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&thisValue=verifyEmp&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&empBrnCode='+empBrnCode+'&locType='+locType,
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
	
	function getEmpSearch(event) {
		var optionId=document.frmTS.hide_option.value;
		var key, keyChar;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
		  
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
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
			}
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
	
	function printTSList() {
		var empNo=document.frmTS.empNo.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		document.frmTS.action = 'timesheet_list_pdf.php?empNo='+empNo+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&payPd='+payPd;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "timesheet_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function printEarningsList(repType,tbl) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var reportType = document.frmTS.reportType.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
	    var locType = document.frmTS.locType.value;
		
		document.frmTS.action = 'earnings_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+"&repType="+repType+"&tbl="+tbl+'&reportType='+reportType+'&empBrnCode='+empBrnCode+'&locType='+locType;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "timesheet_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function printDeductionsList(repType,tbl) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
	    var locType = document.frmTS.locType.value;
		
		document.frmTS.action = 'deductions_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+"&repType="+repType+"&tbl="+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "timesheet_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function printOtNdList(repType,tbl) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
	    var locType = document.frmTS.locType.value;
		
		document.frmTS.action = 'ot_nd_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+"&repType="+repType+"&tbl="+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "ot_nd_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function printUtTardiList(repType,tbl) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
	    var locType = document.frmTS.locType.value;
		
		document.frmTS.action = 'ut_tardi_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+"&repType="+repType+"&tbl="+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "ut_tardi_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function printAllowList(repType,tbl) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		document.frmTS.action = 'allowance_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+"&repType="+repType+"&tbl="+tbl;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "allowance_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	
	function printDeductionsTypeList(repType,tbl) {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
	    var locType = document.frmTS.locType.value;
		
		document.frmTS.action = 'deductions_type_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+"&repType="+repType+'&tbl='+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "deductions_type_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function printDenomList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		var reportType = document.frmTS.reportType.value;
		document.frmTS.action = 'denomination_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&payPd='+payPd+'&reportType='+reportType;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "denomination_list_ajax.php";
		document.frmTS.target = "_self";
	}
	
	function valBack() {
		document.frmTS.action = 'timesheet.php';
		document.frmTS.submit();
	}
	
	function returnEmpList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var groupType=document.frmTS.groupType.value;
		var orderBy=document.frmTS.orderBy.value;
		var catType=document.frmTS.catType.value;
		document.frmTS.action = 'timesheet_list.php?inputId=new_&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType;
		document.frmTS.submit();
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
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
		var locType = document.frmTS.locType.value;
		
		if (thisValue=="searchTS11" || thisValue=="searchTS12") {
			var frmDate=new Date(document.frmTS.monthfr.value);
			var toDate=new Date(document.frmTS.monthto.value);
			
			if(document.frmTS.monthfr.value == ""){
				alert("From Date is required.");
				return false;
			}
			
			if(document.frmTS.monthto.value == ""){
				alert("To Date is required.");
				return false;
			}
			
			if(frmDate.getFullYear()!=toDate.getFullYear())
			{
				alert("Year of the Date fields should be the same.");
				return false;
			}
			
			if(frmDate>toDate)
			{
				alert("From Date should not be greater than the To Date.");
				return false;
			}
			
			
		}
		
		var tbl;
		var prName;
		if (thisValue=="Payslip") {
			tbl=document.frmTS.tblEarnType.value;
		}
		if (thisValue=="searchTS10") {
			if (document.getElementById("chname_0").checked)
				prName=1;
			else
				prName=0;
		}		

		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
			if((thisValue=='searchTS2')||(thisValue=='searchTS7')||(thisValue=='searchTS6')||(thisValue=='searchTS9')||(thisValue=='searchTS10')){
				var reportType=document.frmTS.reportType.value;
				var topType = document.frmTS.topType.value;
				new Ajax.Request(
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+'&reportType='+reportType+'&topType='+topType+'&empBrnCode='+empBrnCode+'&locType='+locType+'&prName='+prName,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
			}
			else if(thisValue=='searchTS11')
			{
				new Ajax.Request(
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+'&tbl='+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType+'&fromDate='+document.frmTS.monthfr.value+'&toDate='+document.frmTS.monthto.value+'&prName='+prName,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
			}
			else if(thisValue=='searchTS12')
			{
				new Ajax.Request(
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+'&tbl='+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType+'&fromDate='+document.frmTS.monthfr.value+'&toDate='+document.frmTS.monthto.value,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  });
			}
			else{
				new Ajax.Request(
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+'&tbl='+tbl+'&empBrnCode='+empBrnCode+'&locType='+locType+'&prName='+prName,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
			}
			//var tbl = document.frmTS.tblEarnType.value;
	}

	

	function PaySlip(act) {
		
	
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		var locType=document.frmTS.locType.value;
		var tbl
		var extUrl
		var thisValue = "Payslip";
		tbl=document.frmTS.tblEarnType.value;
		extUrl='&empBrnCode='+document.frmTS.empBrnCode.value+'&locType='+locType+'&act='+act;

		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}

		
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+'&tbl='+tbl+extUrl,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function LastPay() {
		
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var payPd=document.frmTS.payPd.value
		var locType=document.frmTS.locType.value;
		var tbl
		var extUrl
		var thisValue = "LastPay";
		extUrl='&empBrnCode='+document.frmTS.empBrnCode.value+'&locType='+locType;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&payPd='+payPd+'&thisValue='+thisValue+extUrl,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	
	function LastPayExcel() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var payPd=document.frmTS.payPd.value
		var locType=document.frmTS.locType.value;
		var tbl
		var extUrl
		var thisValue = "LastPayExcel";
		extUrl='&empBrnCode='+document.frmTS.empBrnCode.value+'&locType='+locType;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&payPd='+payPd+'&thisValue='+thisValue+extUrl,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	
	function RFP() {
		
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var payPd=document.frmTS.payPd.value
		var locType=document.frmTS.locType.value;
		var tbl
		var extUrl
		var thisValue = "rfp";
		extUrl='&empBrnCode='+document.frmTS.empBrnCode.value+'&locType='+locType;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&payPd='+payPd+'&thisValue='+thisValue+extUrl,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}		
	function GLBooking(typeRep) {
		var payPd=document.frmTS.payPd.value;
		var empBrnCode=document.frmTS.empBrnCode.value;
		var locType=document.frmTS.locType.value;
				new Ajax.Request(
				  'timesheet_ajax.php?inputId='+typeRep+'&payPd='+payPd+'&empBrnCode='+empBrnCode+'&locType='+locType,
				  {
					 asynchronous : true,     
					 onComplete   : function (req){
						eval(req.responseText);
					 }
				  }
				);
	}
	function DeductedLoans() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode=document.frmTS.empBrnCode.value;
		var locType=document.frmTS.locType.value;
		var lonType=document.frmTS.lonType.value;
		var thisValue = 'DedLoans';
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
		branch ='&empBrnCode='+empBrnCode+'&locType='+locType+'&lonType='+lonType;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+branch,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}		

	function LoansReport() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var orderBy=document.frmTS.orderBy.value;
		var payPd=document.frmTS.payPd.value;
		var empBrnCode=document.frmTS.empBrnCode.value;
		var locType=document.frmTS.locType.value;
		var lonType=document.frmTS.lonType.value;
		var thisValue = 'LoansReport';
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
		branch ='&empBrnCode='+empBrnCode+'&locType='+locType+'&lonType='+lonType;
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+branch,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}		


	function DailyLoans() {
		
		var optionId=document.frmTS.hide_option.value;
		var from=document.frmTS.txtfrDate.value;
		var to=document.frmTS.txttoDate.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		
			new Ajax.Request(
			  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=DailyLoan&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&from='+from+'&to='+to,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);
	}			
	function TotSalary() {
		
		var payPd=document.frmTS.payPd.value
		var brnCode=document.frmTS.empBrnCode.value
		var extUrl;
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}		
		new Ajax.Request(
		  'timesheet_ajax.php?inputId=empSearch&payPd='+payPd+'&thisValue=TotSal&salbrnCode='+brnCode,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	
function LoansPerTypePerStore(repType) {
		
		var optionId=document.frmTS.hide_option.value;
		var from=document.frmTS.txtfrDate.value;
		var to=document.frmTS.txttoDate.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var branch=document.frmTS.branch.value;
		var lonType=document.frmTS.lonType.value;
		
		if (repType == 'pdf')
			location.href=('loans_per_store_pdf.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=DailyLoan&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&lonType='+lonType+'&branch='+branch+'&from='+from+'&to='+to);
		else	
			location.href=('loans_per_store_excel.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=DailyLoan&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&lonType='+lonType+'&branch='+branch+'&from='+from+'&to='+to);

	}	