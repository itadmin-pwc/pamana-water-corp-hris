// JavaScript Document
	function isNumberInputEmpNoOnly(field, event) {
	  var empNo=document.frmEmpLoan.empNo.value;
	  var empName=document.frmEmpLoan.empName.value;
	  var optionId=document.frmEmpLoan.hide_option.value;
	  var fileName=document.frmEmpLoan.fileName.value;
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
			  'inq_emp_loans_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName,
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
		var optionId=document.frmEmpLoan.hide_option.value;
		var key, keyChar;
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		var fileName=document.frmEmpLoan.fileName.value;
		var groupType=document.frmEmpLoan.groupType.value;
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
				  'inq_emp_loans_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType,
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
	function getLoanType() {
		var loanTypeAll=document.frmEmpLoan.loanTypeAll.value;
		var hide_loanType=document.frmEmpLoan.hide_loanType.value;
		new Ajax.Request(
		  'inq_emp_loans_ajax.php?inputId=getLoanType&loanTypeAll='+loanTypeAll+'&hide_loanType='+hide_loanType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('typeLoan').innerHTML=req.responseText;
			 }
		  }
		);
	}
	
	function getEmpDept(inputId) {
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		new Ajax.Request(
		  'inq_emp_loans_ajax.php?inputId='+inputId+'&empNo='+empNo+'&empName='+empName,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('deptDept').innerHTML=req.responseText;
			 }
		  }
		);
	}
	function getEmpSect(inputId) {
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		new Ajax.Request(
		  'inq_emp_loans_ajax.php?inputId='+inputId+'&empNo='+empNo+'&empName='+empName,
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
		document.frmEmpLoan.hide_option.value = option_button;
		document.frmEmpLoan.submit();
	}
	function printLoanList() {
		var empNo=document.frmEmpLoan.empNo.value;
		var loanTypeAll=document.frmEmpLoan.loanTypeAll.value;
		var loanType=document.frmEmpLoan.loanType.value;
		var loanStatus=document.frmEmpLoan.loanStatus.value;
		var orderBy=document.frmEmpLoan.orderBy.value;
		
		document.frmEmpLoan.action = 'inq_emp_loans_list_pdf.php?empNo='+empNo+'&loanTypeAll='+loanTypeAll+'&loanType='+loanType+'&loanStatus='+loanStatus+'&orderBy='+orderBy;
		document.frmEmpLoan.target = "_blank";
		document.frmEmpLoan.submit();
		document.frmEmpLoan.action = "inq_emp_loans_list_ajax.php";
		document.frmEmpLoan.target = "_self";
	}
	function printLoanDedList() {
		var empNo=document.frmEmpLoan.empNoB.value;
		var lonTypeCd=document.frmEmpLoan.lonTypeCd.value;
		var lonRefNo=document.frmEmpLoan.lonRefNo.value;
		document.frmEmpLoan.action = 'inq_emp_loans_list_details_pdf.php?empNo='+empNo+'&lonTypeCd='+lonTypeCd+'&lonRefNo='+lonRefNo;
		document.frmEmpLoan.target = "_blank";
		document.frmEmpLoan.submit();
		document.frmEmpLoan.action = "inq_emp_loans_list_details_ajax.php";
		document.frmEmpLoan.target = "_self";
	}
	function printLoanTypeList() {
		document.frmEmpLoan.action = 'inq_loan_type_list_pdf.php';
		document.frmEmpLoan.target = "_blank";
		document.frmEmpLoan.submit();
		document.frmEmpLoan.action = "inq_emp_loans.php";
		document.frmEmpLoan.target = "_self";
	}
	function valSearchLoan() {
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		var loanTypeAll=document.frmEmpLoan.loanTypeAll.value;
		var loanType=document.frmEmpLoan.loanType.value;
		var orderBy=document.frmEmpLoan.orderBy.value;
		

		new Ajax.Request(
		  'inq_emp_loans_ajax.php?inputId=loanSearch&empNo='+empNo+'&empName='+empName+'&loanType='+loanType+'&loanTypeAll='+loanTypeAll+'&orderBy='+orderBy,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	function valSearchGovDed() {
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		var from=document.frmEmpLoan.txtfrDate.value;
		var to=document.frmEmpLoan.txttoDate.value;
		if (trim(empNo) == "") {
			alert('Emp. No. is required!');
			return false;	
		}
		if (trim(from) == "") {
			alert('From date is required!');
			return false;	
		}
		if (trim(to) == "") {
			alert('To date is required!');
			return false;	
		}

		new Ajax.Request(
		  'inq_emp_govded_ajax.php?inputId=searchGovDed&empNo='+empNo+'&empName='+empName+'&from='+from+'&to='+to,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	
	function valBack() {
		document.frmEmpLoan.action = 'inq_emp_loans.php';
		document.frmEmpLoan.submit();
	}
	
	function viewLoanInfo(empNo, loanCode, loanRefNo)
	{
		  new Ajax.Request(
		  'inq_emp_loans_ajax.php?inputId=empLoanInfo&empNo='+empNo+'&loanCode='+loanCode+'&loanRefNo='+loanRefNo,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function viewDetails(urlPara,empNoB,lonTypeCd,lonRefNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		var nUrl = "";
		nUrl="inq_emp_loans_list_details.php?empNoB="+empNoB+"&lonTypeCd="+lonTypeCd+"&lonRefNo="+lonRefNo; 
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:800, 
		height:450, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: "Employee Loans Deduction Details", 
		showEffect:Effect.Appear, 
		destroyOnClose: true,
		maximizable: false,
		hideEffect: Effect.SwitchOff, 
		draggable:true })
		viewDtl.setURL(nUrl);
		viewDtl.show(true);
		viewDtl.showCenter();	
		
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == viewDtl) {
		        viewDtl = null;
		        pager(URL,ele,'load',offset,isSearch,txtSrch,cmbSrch,urlPara,'../../../images/');
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}