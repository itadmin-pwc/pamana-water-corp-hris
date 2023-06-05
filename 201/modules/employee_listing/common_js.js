// JavaScript Document
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/
	function valSearchTS(thisValue) {
		var optionId=document.frmTS.hide_option.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var hide_empSect=document.frmTS.hide_empSect.value;
		var fileName=document.frmTS.fileName.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
		var ext ="";
		if (thisValue=='salary') {
			ext='&payCat='+document.frmTS.emppayCat.value;
		}
		new Ajax.Request(
		  'common_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&thisValue='+thisValue+'&empBrnCode='+empBrnCode+ext,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	
	function valSearch_Manpower(thisValue) {
		var optionId=document.frmTS.hide_option.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var fileName=document.frmTS.fileName.value;
		var empBrnCode = document.frmTS.empBrnCode.value;
		var empRank = document.frmTS.empRank.value;
		var cmbstatus = document.frmTS.cmbstatus.value;
		var monthfr=document.frmTS.monthfr.value;
		var monthto=document.frmTS.monthto.value;
		
		var parseStart = Date.parse(monthfr);
		var parseEnd = Date.parse(monthto);
		
	
		
		if(monthfr!="") 
		{
			if(monthto=="") 
			{
				alert("Please specify the To Date.");
				$('monthto').focus();
				return false;
			}
		}
		
	
		if(monthto!="") 
		{
			if(monthfr=="") 
			{
				alert("Please specify the From Date.");
				$('monthfr').focus();
				return false;
			}
		}
		
		if(parseStart > parseEnd) {
			alert("Start Date must not be greater than to End Date.");
			$('monthfr').focus();
			return false;
		}
		
		
		new Ajax.Request(
		  'common_ajax.php?&hide_empDept='+hide_empDept+'&inputId=empSearch&empDiv='+empDiv+'&empDept='+empDept+'&optionId='+optionId+'&fileName='+fileName+'&thisValue='+thisValue+'&empBrnCode='+empBrnCode+'&empStatus='+cmbstatus+'&empRank='+empRank+'&monthto='+monthto+'&monthfr='+monthfr,
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
	
	