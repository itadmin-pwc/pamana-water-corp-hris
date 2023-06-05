// JavaScript Document
	function isNumberInputEmpNoOnly(field, event) {
	  var empNo=document.frmEmp.empNo.value;
	  var empName=document.frmEmp.empName.value;
	  var optionId=document.frmEmp.hide_option.value;
	  var fileName=document.frmEmp.fileName.value;
	  var groupType=document.frmEmp.groupType.value;
	  var orderBy=document.frmEmp.orderBy.value;
	  var catType=document.frmEmp.catType.value;
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
			  'inq_emp_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
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
		var optionId=document.frmEmp.hide_option.value;
		var key, keyChar;
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empDept=document.frmEmp.hide_empDept.value;
		var hide_empSect=document.frmEmp.hide_empSect.value;
		var fileName=document.frmEmp.fileName.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
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
				  'inq_emp_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
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
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empDept=document.frmEmp.hide_empDept.value;
		new Ajax.Request(
		  'inq_emp_ajax.php?hide_empDept='+hide_empDept+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$('deptDept').innerHTML=req.responseText;
			 }
		  }
		);
	}
	function getEmpSect(inputId) {
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empSect=document.frmEmp.hide_empSect.value;
		new Ajax.Request(
		  'inq_emp_ajax.php?hide_empSect='+hide_empSect+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
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
		document.frmEmp.hide_option.value = option_button;
		document.frmEmp.submit();
	}
	function printEmpList() {
		var empNo=document.frmEmpList.empNo.value;
		var empDiv=document.frmEmpList.empDiv.value;
		var empDept=document.frmEmpList.empDept.value;
		var empSect=document.frmEmpList.empSect.value;
		var orderBy=document.frmEmpList.orderBy.value;
		var groupType=document.frmEmpList.groupType.value;
		var catType=document.frmEmpList.catType.value;
		document.frmEmpList.action = 'inq_emp_list_pdf.php?empNo='+empNo+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType;
		document.frmEmpList.target = "_blank";
		document.frmEmpList.submit();
		document.frmEmpList.action = "inq_emp_list_ajax.php";
		document.frmEmpList.target = "_self";
	}
	function printEmpStat() {
		var empNo=document.frmEmpList.empNo.value;
		var empDiv=document.frmEmpList.empDiv.value;
		var empDept=document.frmEmpList.empDept.value;
		var empSect=document.frmEmpList.empSect.value;
		var orderBy=document.frmEmpList.orderBy.value;
		var groupType=document.frmEmpList.groupType.value;
		var catType=document.frmEmpList.catType.value;
		document.frmEmpList.action = 'inq_emp_stat_pdf.php?empNo='+empNo+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType;
		document.frmEmpList.target = "_blank";
		document.frmEmpList.submit();
		document.frmEmpList.action = "inq_emp_list_ajax.php";
		document.frmEmpList.target = "_self";
	}
	function printDeptHierarchy() {
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		document.frmEmp.action = 'inq_dept_hierarchy_pdf.php?empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect;
		document.frmEmp.target = "_blank";
		document.frmEmp.submit();
		document.frmEmp.action = "inq_emp.php";
		document.frmEmp.target = "_self";
	}
	function printHolidayCalendar() 
	{
		var hol_date = document.frmEmp.List_holidays.value;
		document.frmEmp.action = 'inq_holiday_calendar_pdf.php?hol_date='+hol_date;
		document.frmEmp.target = "_blank";
		document.frmEmp.submit();
		document.frmEmp.action = "inq_emp.php";
		document.frmEmp.target = "_self";
	}
	function printPrevList() 
	{
		var prevEmpNo = document.frmEmp.prevEmpNo.value;
		document.frmEmp.action = 'inq_prev_employer_list_pdf.php?prevEmpNo='+prevEmpNo;
		document.frmEmp.target = "_blank";
		document.frmEmp.submit();
		document.frmEmp.action = "inq_emp.php";
		document.frmEmp.target = "_self";
	}
	function printPrevInfo(prevCode) 
	{
		var prevEmpNo = document.frmEmp.prevEmpNo.value;
		document.frmEmp.action = 'inq_prev_employer_info_pdf.php?prevEmpNo='+prevEmpNo+'&prevCode='+prevCode;
		document.frmEmp.target = "_blank";
		document.frmEmp.submit();
		document.frmEmp.action = "inq_emp.php";
		document.frmEmp.target = "_self";
	}
	function valBack() {
		document.frmEmp.action = 'inq_emp.php';
		document.frmEmp.submit();
	}
	function returnEmpList() {
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
		document.frmEmp.action = 'inq_emp_list.php?inputId=new_&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType;
		document.frmEmp.submit();
	}
	function valSearchEmp() {
		var optionId=document.frmEmp.hide_option.value;
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empDept=document.frmEmp.hide_empDept.value;
		var hide_empSect=document.frmEmp.hide_empSect.value;
		var fileName=document.frmEmp.fileName.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
		new Ajax.Request(
		  'inq_emp_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	function printEmpInfo() {
		var empNo=document.frmEmp.empNo.value;
		document.frmEmp.action = 'inq_emp_pdf.php?empNo='+empNo;
		document.frmEmp.target = "_blank";
		document.frmEmp.submit();
		document.frmEmp.action = "inq_emp.php";
		document.frmEmp.target = "_self";
	}
	function printEmpConfi() {
		var empNo=document.frmEmp.empNo.value;
		document.frmEmp.action = 'inq_emp_confi_pdf.php?empNo='+empNo;
		document.frmEmp.target = "_blank";
		document.frmEmp.submit();
		document.frmEmp.action = "inq_emp.php";
		document.frmEmp.target = "_self";
	}
	function getEmpHist(empNo) {
		new Ajax.Request(
		  'inq_emp_ajax.php?inputId=empHistSearch&empNo='+empNo,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
			 	//$('imgPrevEmp').innerHTML=req.responseText;
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function cursor(val)
	{
	trail.innerHTML=val;
	trail.style.visibility="visible";
	trail.style.position="absolute";
	trail.style.left=event.clientX+10;
	trail.style.top=event.clientY;
	}
	
	function hidecursor()
	{
	trail.style.visibility="hidden";
	}
	
	function valUpload() {
		var optionId=document.frmEmp.hide_option.value;
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empDept=document.frmEmp.hide_empDept.value;
		var hide_empSect=document.frmEmp.hide_empSect.value;
		var fileName=document.frmEmp.fileName.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
		var userfile=document.frmEmp.userfile.value;
		if (userfile=="") {
			alert('Invalid file name!');
			return false;
		}
		new Ajax.Request(
		  'inq_emp_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empUpload&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	function getViewCam() {
		var optionId=document.frmEmp.hide_option.value;
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empDept=document.frmEmp.hide_empDept.value;
		var hide_empSect=document.frmEmp.hide_empSect.value;
		var fileName=document.frmEmp.fileName.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
		var userfile=document.frmEmp.userfile.value;
		
		new Ajax.Request(
		  'inq_emp_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=viewCam&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	function refreshImage() {
		var optionId=document.frmEmp.hide_option.value;
		var empNo=document.frmEmp.empNo.value;
		var empName=document.frmEmp.empName.value;
		var empDiv=document.frmEmp.empDiv.value;
		var empDept=document.frmEmp.empDept.value;
		var empSect=document.frmEmp.empSect.value;
		var hide_empDept=document.frmEmp.hide_empDept.value;
		var hide_empSect=document.frmEmp.hide_empSect.value;
		var fileName=document.frmEmp.fileName.value;
		var groupType=document.frmEmp.groupType.value;
		var orderBy=document.frmEmp.orderBy.value;
		var catType=document.frmEmp.catType.value;
		var userfile=document.frmEmp.userfile.value;
		
		new Ajax.Request(
		  'inq_emp_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=refresh&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}