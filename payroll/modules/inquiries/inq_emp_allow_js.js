// JavaScript Document
	function isNumberInputEmpNoOnly(field, event) {
	  var empNo=document.frmEmpAllow.empNo.value;
	  var empName=document.frmEmpAllow.empName.value;
	  var optionId=document.frmEmpAllow.hide_option.value;
	  var fileName=document.frmEmpAllow.fileName.value;
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
			  'inq_emp_allow_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName,
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
		var optionId=document.frmEmpAllow.hide_option.value;
		var key, keyChar;
		var empNo=document.frmEmpAllow.empNo.value;
		var empName=document.frmEmpAllow.empName.value;
		var fileName=document.frmEmpAllow.fileName.value;
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
				  'inq_emp_allow_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName,
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
		var empNo=document.frmEmpAllow.empNo.value;
		var empName=document.frmEmpAllow.empName.value;
		var empDiv=document.frmEmpAllow.empDiv.value;
		var empDept=document.frmEmpAllow.empDept.value;
		var empSect=document.frmEmpAllow.empSect.value;
		var hide_empDept=document.frmEmpAllow.hide_empDept.value;
		new Ajax.Request(
		  'inq_emp_allow_ajax.php?hide_empDept='+hide_empDept+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('deptDept').innerHTML=req.responseText;
			 }
		  }
		);
	}
	function getEmpSect(inputId) {
		var empNo=document.frmEmpAllow.empNo.value;
		var empName=document.frmEmpAllow.empName.value;
		var empDiv=document.frmEmpAllow.empDiv.value;
		var empDept=document.frmEmpAllow.empDept.value;
		var empSect=document.frmEmpAllow.empSect.value;
		var hide_empSect=document.frmEmpAllow.hide_empSect.value;
		new Ajax.Request(
		  'inq_emp_allow_ajax.php?hide_empSect='+hide_empSect+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
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
		document.frmEmpAllow.hide_option.value = option_button;
		document.frmEmpAllow.submit();
	}
	function printAllowList() {
		var empNo=document.frmEmpAllow.empNo.value;
		var empDiv=document.frmEmpAllow.empDiv.value;
		var empDept=document.frmEmpAllow.empDept.value;
		var empSect=document.frmEmpAllow.empSect.value;
		var allowType=document.frmEmpAllow.allowType.value;
		var orderBy=document.frmEmpAllow.orderBy.value;
		
		document.frmEmpAllow.action = 'inq_emp_allow_list_pdf.php?empNo='+empNo+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&allowType='+allowType+'&orderBy='+orderBy;
		document.frmEmpAllow.target = "_blank";
		document.frmEmpAllow.submit();
		document.frmEmpAllow.action = "inq_emp_allow_list_ajax.php";
		document.frmEmpAllow.target = "_self";
	}
	function printAllowTypeList() {
		document.frmEmpAllow.action = 'inq_allow_type_list_pdf.php';
		document.frmEmpAllow.target = "_blank";
		document.frmEmpAllow.submit();
		document.frmEmpAllow.action = "inq_emp_allow.php";
		document.frmEmpAllow.target = "_self";
	}
	function valBack() {
		document.frmEmpAllow.action = 'inq_emp_allow.php';
		document.frmEmpAllow.submit();
	}
	function valSearchAllow() {
		var empNo=document.frmEmpAllow.empNo.value;
		var empName=document.frmEmpAllow.empName.value;
		var allowType=document.frmEmpAllow.allowType.value;
		var orderBy=document.frmEmpAllow.orderBy.value;
		new Ajax.Request(
		  'inq_emp_allow_ajax.php?inputId=allowSearch&empNo='+empNo+'&empName='+empName+'&allowType='+allowType+'&orderBy='+orderBy,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function viewDetails(urlPara,empNo,allowCode,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		var nUrl = "";
		nUrl="inq_emp_allow_list_details.php?empNo="+empNo+"&allowCode="+allowCode; 
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:800, 
		height:500, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: "Employee Allowance Details", 
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
	
	function printAllowEarnList(empNo,allowCode)
	{
		window.open('inq_emp_allow_list_details_pdf.php?empNo='+empNo+'&allowCode='+allowCode);
	}