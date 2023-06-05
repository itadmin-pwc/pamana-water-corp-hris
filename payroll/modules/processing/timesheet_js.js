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
	  var topType = document.frmTS.topType.value;
	  
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
			   'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+"&thisValue=verifyEmp&payPd="+payPd+'&topType='+topType,
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
		var topType = document.frmTS.topType.value;
		
		
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
		
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&thisValue='+thisValue+'&topType='+topType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function procRemTextfile(thisValue, inputId){
		var payPd=document.frmTS.payPd.value;
		
		var selComp = document.frmTS.selComp.value;
		var remType = document.frmTS.topType.value;
		var loc 	= document.frmTS.location.value; 
		if(selComp==0)
		{
			alert("Select a Company.");
			return false;
		}
		
		
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
		
		
		new Ajax.Request(
		  'timesheet_ajax.php?inputId='+inputId+'&payPd='+payPd+'&selComp='+selComp+'&remType='+remType+'&location='+loc,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 },
			onCreate : function(){
				timedCount();
			},
			onSuccess: function (){
				$('caption').innerHTML="";
				stopCount();
			}				 
		  }
		);
	}

	var m=0;
	var s=0;
	var t;	

	function timedCount(){

		if(s == 60){
			m = m+1;
		}	
		if(s == 60){
			s =0;
		}

		$('caption').innerHTML="<font size='2'>"+m+":"+s+ " <blink>Loading...</blink></font> " +'<br><img src="../../../images/progress2.gif">';
		s=s+1;
		t=setTimeout("timedCount()",1000);
	}
	
	function stopCount(){
		clearTimeout(t);
	}	