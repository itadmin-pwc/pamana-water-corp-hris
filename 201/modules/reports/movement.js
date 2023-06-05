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
		var orderBy=document.frmTS.orderBy.value;
   	    var pafType=document.frmTS.cmbType.value;
	  	var from=document.frmTS.txtfrDate.value;
	  	var to=document.frmTS.txttoDate.value;
		//var salary=document.frmTS.withSalary.value;
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
			   'movement_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&thisValue=verifyEmp'+'&from='+from+'&to='+to+'&pafType='+pafType,
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
				  'movement_ajax.php?thisValue=verifyEmp&hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy,
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
		  'movement_ajax.php?hide_empDept='+hide_empDept+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
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
		  'movement_ajax.php?hide_empSect='+hide_empSect+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
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
		  'movement_ajax.php?hide_payPd='+hide_payPd+'&inputId='+inputId+'&payPd='+payPd+'&groupType='+groupType+'&catType='+catType,
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
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		document.frmTS.action = 'movement_list_pdf.php?empNo='+empNo+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "movement_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printEarningsList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		document.frmTS.action = 'earnings_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "movement_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printDeductionsList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		document.frmTS.action = 'deductions_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "movement_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printTaxList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var table=document.frmTS.table.value;
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+'&table='+table+'&url=tax_list_pdf.php';
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "tax_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printSssList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var table=document.frmTS.table.value;
		
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+'&table='+table+'&url=sss_list_pdf.php';
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "sss_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printPhilhealthList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var table=document.frmTS.table.value;
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+'&table='+table+'&url=philhealth_list_pdf.php';
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "philhealth_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printEmpMovement() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var pafType=document.frmTS.pafType.value;
		var from=document.frmTS.from.value;
		var to=document.frmTS.to.value;
		var type=document.frmTS.type.value;
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&url=paf_list_pdf.php&pafType='+pafType+'&from='+from+'&to='+to+'&type='+type;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "paf_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printSalary() {
		
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var code=document.frmTS.code.value;
		var from=document.frmTS.from.value;
		var to=document.frmTS.to.value;
		var type=document.getElementById('type').value;
		
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&url=salary_list_pdf.php&code='+code+'&from='+from+'&to='+to+'&type='+type;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "paf_list_ajax.php";
		document.frmTS.target = "_self";
	}	
	function printSssLoanList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var table=document.frmTS.table.value;
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+'&table='+table+'&url=sss_loan_list_pdf.php';
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "sss_loan_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printPagibigLoanList() {
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		var table=document.frmTS.table.value;
		document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+'&table='+table+'&url=pagibig_loan_list_pdf.php';
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "pagibig_loan_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function valBack() {
		document.frmTS.action = 'movement.php';
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
		document.frmTS.action = 'movement_list.php?inputId=new_&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType;
		document.frmTS.submit();
	}
	function valSearchTS(thisValue) {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		switch(thisValue) {
			case "paf":	
				strURL = '&pafType='+document.frmTS.cmbType.value+'&group='+document.frmTS.group.value;
			break;
			case "paf_prooflist":	
				strURL = '&pafType='+document.frmTS.cmbType.value;
			break;
			case "released_paf":	
				strURL = '&pafType='+document.frmTS.cmbType.value;
			break;
			case "held_paf_prooflist_excel":
				strURL = '&pafType='+document.frmTS.cmbType.value;
			break;	
			case "released_paf_excel":
				strURL = '&pafType='+document.frmTS.cmbType.value;
			break;
			case "paf_excel":
				strURL = '&pafType='+document.frmTS.cmbType.value+'&group='+document.frmTS.group.value;
			break;
			case "salary":	
				if(document.frmTS.code.value=="0")
				{
					alert("Type is required.");
					return false;
				}
				else
				{
					strURL = '&code='+document.frmTS.code.value;
					strURL = strURL+'&type='+document.frmTS.ReportType.value;
				}
			break;
			case "new_emp":
				if(document.frmTS.txtfrDate.value=="" || document.frmTS.txttoDate.value=="")
				{
					alert("From/To Date is required.");
					return false;
				}			
				strURL = strURL+'&type='+document.frmTS.ReportType.value+'&group='+document.frmTS.cmbGrp.value;
			break;
			case "new_emp_excel":
				if(document.frmTS.txtfrDate.value=="" || document.frmTS.txttoDate.value=="")
				{
					alert("From/To Date is required.");
					return false;
				}			
				strURL = strURL+'&type='+document.frmTS.ReportType.value+'&group='+document.frmTS.cmbGrp.value;
			break;
		}
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var orderBy=document.frmTS.orderBy.value;
		var from=document.frmTS.txtfrDate.value;
		var to=document.frmTS.txttoDate.value;
		new Ajax.Request(
		  'movement_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+strURL+'&thisValue='+thisValue+'&from='+from+'&to='+to,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	
	function EmpStatus() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var Status=document.frmTS.cmbType.value;
		var from=document.frmTS.txtfrDate.value;
		var to=document.frmTS.txttoDate.value;

		new Ajax.Request(
		  'movement_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&thisValue=EmpStatus&status='+Status+'&from='+from+'&to='+to,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	
	function ResignedEmp(id) {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var from=document.frmTS.txtfrDate.value;
		var to=document.frmTS.txttoDate.value;
		if(id=="salaryPDF"){
			window.open('resigned_emp_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&from='+from+'&to='+to);
		}
		if(id=="salaryExcel"){
	   		window.open('resigned_emp_excel.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&from='+from+'&to='+to);
		}
		
	}	

	function EOCEmp() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var from=document.frmTS.txtfrDate.value;
		var to=document.frmTS.txttoDate.value;
	    window.open('eoc_emp_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&from='+from+'&to='+to);
	}	


	function COE() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var Type=document.frmTS.cmbType.value;
		var salary=document.frmTS.withSalary.value;
		var course=document.frmTS.txtCourse.value;
		var school=document.frmTS.txtSchool.value;
		var signatory=document.frmTS.txtsignatory.value;
		var position=document.frmTS.txtposition.value;
		if (Type=="") {
			alert('Type is required.');
			return false;
		}
		if (empNo=="") {
			alert('Employee No. is required.');
			return false;
		}
		if(Type==6){
			if(course==""){
				alert('Please enter Course.');
				return false;
			}	
			if(school==""){
				alert('Please enter School.');
				return false;	
			}
		}
		new Ajax.Request(
		  'movement_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&optionId='+optionId+'&fileName='+fileName+'&thisValue=COE&type='+Type+'&salary='+salary+'&course='+course+'&school='+school+'&signatory='+signatory+'&position='+position,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}		
	
	function CS() {
		var optionId=document.frmTS.hide_option.value;
		var empNo=document.frmTS.empNo.value;
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var Type=document.frmTS.cmbType.value;
		if (Type=="") {
			alert('Report type is required.');
			return false;
		}
		if (empNo=="") {
			alert('Employee No. is required.');
			return false;
		}
		new Ajax.Request(
		  'movement_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&optionId='+optionId+'&fileName='+fileName+'&thisValue=CS&type='+Type,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}			
	
	function EmpTenure(form) {
		var optionId=document.frmTS.hide_option.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var empDept=document.frmTS.empDept.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		new Ajax.Request(
		  'movement_ajax.php?hide_empDept='+hide_empDept+'&inputId=empSearch&empDept='+empDept+'&optionId='+optionId+'&thisValue=EmpTenure&empDiv='+empDiv+'&form='+form,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	

	function EmpHeadCount(form) {
		var optionId=document.frmTS.hide_option.value;
		var empDiv=document.frmTS.empDiv.value;
		var strURL;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var empDept=document.frmTS.empDept.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		new Ajax.Request(
		  'movement_ajax.php?hide_empDept='+hide_empDept+'&inputId=empSearch&empDept='+empDept+'&optionId='+optionId+'&thisValue=HeadCount&empDiv='+empDiv+'&form='+form,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	


function valSearchEmp() {
		var optionId=document.frmEmp.hide_option.value;
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var fileName=document.frmEmp.fileName.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
		
	
		new Ajax.Request(
		  'inq_emp_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
		
	}	
	
	//added by Nhomer
	function printEmpBranch(){
		var qryBranch=document.frmTS.cmbBranch.value;
//		alert(qryBranch);
		document.frmTS.action = 'inq_emp_branch.php?qryBranch='+qryBranch;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "inq_emp.php";
		document.frmTS.target = "_self";
		}
		
	function printEmpConfiBranch(){
		var qryBranch=document.frmTS.cmbBranch.value;
//		alert(qryBranch);
		document.frmTS.action = 'inq_emp_confi_pdf_branch.php?qryBranch='+qryBranch;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "inq_emp.php";
		document.frmTS.target = "_self";
		}
		
	function printEmpInfo() {
		var empNo=document.frmTS.empNo.value;
		document.frmTS.action = 'inq_emp_pdf.php?empNo='+empNo;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "inq_emp.php";
		document.frmTS.target = "_self";
	}
	function printEmpConfi() {
		var empNo=document.frmTS.empNo.value;
		document.frmTS.action = 'inq_emp_confi_pdf.php?empNo='+empNo;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "inq_emp.php";
		document.frmTS.target = "_self";
	}

	function Absences() {
		var empNo=document.frmTS.empNo.value;
		if (empNo=="") {
			alert('Employee No. is required.');	
			return false;
		}
		document.frmTS.action = '../chart/absences.php?empNo='+empNo;
		document.frmTS.submit();
	}
	
	function restday() {
		var branch=document.frmTS.branch.value;
		var ext='';
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		} else if(branch==1) {
			ext ='&group='+document.frmTS.group.value;
		}
		new Ajax.Request(
		  'movement_ajax.php?branch='+branch+'&inputId=restday'+ext,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}
	
	//added by Nhomer
	function loadPersonnel(id){
		new Ajax.Request('inq_emp.php?id='+id,{
			asynchronous	: true,
			onCreate	: function(){
				$('personnelId').innerHTML='loading.....';
				},
			onComplete	: function(req){
					$('personnelId').innerHTML=req.responseText;
				},	
			});		
		}
		
	function generatePdf(id){
		var params='movement_ajax.php?qryBranch='+id+'&action=filterbranches';
		new Ajax.Request(params,{
			asynchronous	:	true,
			onComplete	:	function(req){
				eval(req.responseText);
				}
			});
		}	
		
		