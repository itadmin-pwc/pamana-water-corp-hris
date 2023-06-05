// JavaScript Document



	function checkEmpLoan(lonTypeCd,empNo) {
		new Ajax.Request(
		  'main_emp_loans_ajax.php?inputId=loanType&empNo='+empNo,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);	
	}
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
			  'main_emp_loans_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName,
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
				  'main_emp_loans_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName,
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
	function getEmpSearchNewEdit(inputId,optionId) {
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		if (optionId) { ///// kapag meron ng na search na employee at gustong ilipat sa edit or delete... itong id na to ang magfoforce para lumipat...
			document.frmEmpLoan.hide_option.value = optionId;
		}
		new Ajax.Request(
		  'main_emp_loans_ajax.php?inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&optionId='+optionId,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);	
	}
	function getEmpDept(inputId) {
		var empNo=document.frmEmpLoan.empNo.value;
		var empName=document.frmEmpLoan.empName.value;
		new Ajax.Request(
		  'main_emp_loans_ajax.php?inputId='+inputId+'&empNo='+empNo+'&empName='+empName,
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
		  'main_emp_loans_ajax.php?inputId='+inputId+'&empNo='+empNo+'&empName='+empName,
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
	function populateLoan(id_ko) {
		var empNo=document.frmEmpLoan.empNo.value;
		document.getElementById('empNo').value=empNo;
		document.frmEmpLoan.submit();
	}
	function valPrincToInt() {
		var loanPrinc=document.frmEmpLoan.loanPrinc.value;
		var loanInt=document.frmEmpLoan.loanInt.value;
		var loanTerms=document.frmEmpLoan.loanTerms.value;
		var loanPay=document.frmEmpLoan.loanPay.value;
		if (loanPrinc=="") {
			alert("Principal should have value.");
			document.getElementById('loanPrinc').value="0";
			return false;
		}
		if (loanInt=="") {
			alert("Interest should have value.");
			document.getElementById('loanInt').value="0";
			return false;
		}
		if (loanTerms=="") {
			alert("total terms should have value.");
			document.getElementById('loanTerms').value="0";
			return false;
		}
		if (loanPay=="") {
			alert("Payments should have value.");
			document.getElementById('loanPay').value="0";
			return false;
		}
		if (parseInt(loanPrinc)>parseInt(loanInt) && parseInt(loanInt)>0) {
			alert("Loan Amt Inclusive of Interest should not be less than Loan Amt.");
			document.getElementById('loanPrinc').value="0";
			return false;
		} else {
			if (parseInt(loanPay)>=parseInt(loanInt) && parseInt(loanInt)>0) {
				alert("Payments should not be greater than or equal to Total Amount w/ Interest.");
				document.getElementById('loanPay').value="0";
				loanPay=0;
			}
			var loanDedEx = loanPrinc / loanTerms;
			var loanDedIn = loanInt / loanTerms;
			var loanBal = loanInt - loanPay;
			loanDedEx = format_number(loanDedEx,2);
			loanDedIn = format_number(loanDedIn,2);
			loanBal = format_number(loanBal,2);
			document.getElementById('loanDedEx').value=loanDedEx;
			document.getElementById('loanDedIn').value=loanDedIn;
			document.getElementById('loanBal').value=loanBal;
		}
	}
	function valDeleteLoan() {
		var optionButton = document.frmEmpLoan.hide_option.value
		var empNo=document.frmEmpLoan.empNo.value;
		var loanType=document.frmEmpLoan.loanType.value;
		var loanRefNo=document.frmEmpLoan.loanRefNo.value;
		var confirm_delete = confirm("Are you sure you want to delete this loan? \r\nOk(YES)  Cancel(NO)");
		if (confirm_delete) {
			new Ajax.Request(
			  'main_emp_loans_ajax.php?inputId=deleteLoan&empNo='+empNo+'&loanType='+loanType+'&loanRefNo='+loanRefNo,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);
		} else {
			new Ajax.Request(
			  'main_emp_loans_ajax.php?inputId=cancelDeleteLoan&empNo='+empNo+'&loanType='+loanType+'&loanRefNo='+loanRefNo,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);
		}
	}
	function valUpdateLoan() {
		var optionButton = document.frmEmpLoan.hide_option.value
		var empNo=document.frmEmpLoan.empNo.value;
		var loanType=document.frmEmpLoan.loanType.value;
		loanType = loanType.replace('#','_');
		var loanRefNo=document.frmEmpLoan.loanRefNo.value;
		loanRefNo = loanRefNo.replace('#','_');		var loanPrinc=document.frmEmpLoan.loanPrinc.value;
		var loanInt=document.frmEmpLoan.loanInt.value;
		var loanStart=document.frmEmpLoan.loanStart.value;
		var loanEnd=document.frmEmpLoan.loanEnd.value;
		var loanPeriod=document.frmEmpLoan.loanPeriod.value;
		var loanTerms=document.frmEmpLoan.loanTerms.value;
		var loanDedEx=document.frmEmpLoan.loanDedEx.value;
		var loanDedIn=document.frmEmpLoan.loanDedIn.value;
		var loanPay=document.frmEmpLoan.loanPay.value;
		var loanPayNo=document.frmEmpLoan.loanPayNo.value;
		var loanBal=document.frmEmpLoan.loanBal.value;
		var dtGranted=document.frmEmpLoan.dtGranted.value;
		var loanLastPay=document.frmEmpLoan.loanLastPay.value;
		if (loanType=="" || loanType<=0) {
			alert("Select Loan Type.");
			document.getElementById('loanType').focus();
			return false;
		}
		if (loanRefNo=="") {
			alert("Loan Ref.No should have value.");
			document.getElementById('loanRefNo').focus();
			return false;
		}
		if (loanPrinc<=0) {
			alert("Loan Amt (Principal) should be greater than zero.");
			document.getElementById('loanPrinc').focus();
			return false;
		}
		if (loanInt<=0) {
			alert("Loan Total Amnt Inclusive of Interest should be greater than zero.");
			document.getElementById('loanInt').focus();
			return false;
		}
		if (loanStart=="") {
			alert("Loan Start Date should have value.");
			document.getElementById('loanStart').focus();
			return false;
		}
		if (loanPeriod=="" || loanPeriod<=0) {
			alert("Period of Deduction should have value.");
			document.getElementById('loanPeriod').focus();
			return false;
		}
		if (loanTerms<=0) {
			alert("Total No. of Payments should be greater than zero.");
			document.getElementById('loanTerms').focus();
			return false;
		}
		if (loanDedEx<=0) {
			alert("Deduction (Exclusive of Interest) should be greater than zero.");
			document.getElementById('loanDedEx').focus();
			return false;
		}
		if (loanDedIn<=0) {
			alert("Deduction (Inclusive of Interest) should be greater than zero.");
			document.getElementById('loanDedIn').focus();
			return false;
		}
		if (loanBal=="") {
			alert("Current Loan Balance should have value.");
			document.getElementById('loanBal').focus();
			return false;
		}
		new Ajax.Request(
		  'main_emp_loans_ajax.php?inputId=updateLoan&empNo='+empNo+'&loanType='+loanType+'&loanRefNo='+loanRefNo+'&loanPrinc='+loanPrinc+'&loanInt='+loanInt+'&loanStart='+loanStart+'&loanEnd='+loanEnd+'&loanPeriod='+loanPeriod+'&loanTerms='+loanTerms+'&loanDedEx='+loanDedEx+'&loanDedIn='+loanDedIn+'&loanPay='+loanPay+'&loanPayNo='+loanPayNo+'&loanBal='+loanBal+'&loanLastPay='+loanLastPay+'&dtGranted='+dtGranted,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	function valRefNo() {
		var optionButton = document.frmEmpLoan.hide_option.value
		var empNo=document.frmEmpLoan.empNo.value;
		var loanType=document.frmEmpLoan.loanType.value;
		var loanRefNo=document.frmEmpLoan.loanRefNo.value;
		
		if (loanRefNo>"") {
			new Ajax.Request(
			  'main_emp_loans_ajax.php?inputId=valRefNo&empNo='+empNo+'&loanType='+loanType+'&loanRefNo='+loanRefNo,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);
		}

	}
	function printEmpLoan() {
		var empNo=document.frmEmpLoan.empNo.value;
		var loanType=document.frmEmpLoan.loanType.value;
		var fileName=document.frmEmpLoan.fileName.value;
		document.frmEmpLoan.action = 'main_emp_loans_pdf.php?empNo='+empNo+'&loanType='+loanType;
		document.frmEmpLoan.target = "_blank";
		document.frmEmpLoan.submit();
		document.frmEmpLoan.action = fileName;
		document.frmEmpLoan.target = "_self";
	}
	function getEmpLoanSearch(inputId) {
		var empNo=document.frmEmpLoan.empNo.value;
		var loanType=document.frmEmpLoan.loanType.value;
		loanType = loanType.replace('#','_');
		new Ajax.Request(
		  'main_emp_loans_ajax.php?inputId='+inputId+'&empNo='+empNo+'&loanType='+loanType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}	
	
	function viewDetails(nUrl){
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width: 500, 
		height: 250, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: 'Loan Detailed Payments', 
		showEffect: Effect.Appear, 
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
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}	
	
	function PreTerminate(lonSeries) {
		var ans=confirm('Are you sure you want to Pre-Terminate this Loan?');
		if (ans==true) {
			new Ajax.Request(
			  'main_emp_loans_ajax.php?inputId=PreTerminate&lonSeries='+lonSeries,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);
		}
	}