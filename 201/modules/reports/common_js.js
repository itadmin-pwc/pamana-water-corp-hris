// JavaScript Document
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/26/2010
	Function		:	Common Trans, js, obj, ajax instead of useing timesheet 
*/

	function generatera1_validate(repType)
	{
		var empBrnch=document.frmTS.empBrnCode.value;
		var monthfr=document.frmTS.monthfr.value;
		var monthto=document.frmTS.monthto.value;
		if(repType!='listMinWage')
		{
			var cmbgroup=document.frmTS.cmbgroup.value;
		}
		var parseStart = Date.parse(monthfr);
		var parseEnd = Date.parse(monthto);
		
		
		if(empBrnch==0)
		{
			alert("Please specify the Company.");
			$('empBrnCode').focus();
			return false;	
		}
		
		if(cmbgroup==0){
			alert('Please Select Group.');
			$('cmbgroup').focus();
			return false;	
		}
		
		if(monthfr=="") 
		{
			alert("Please specify the From Date.");
			$('monthfr').focus();
			return false;
		}
		
		if(monthfr!="") 
		{
			if(monthto=="") 
			{
				alert("Please specify the To Date.");
				$('monthto').focus();
				return false;
			}
		}
		
		if(monthto=="") 
		{
			alert("Please specify the To Date.");
			$('monthto').focus();
			return false;
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
		
		if(repType=='listMinWage')
		{
			new Ajax.Request(
			  'common_ajax.php?inputId=empMinWageList&empBrnCode='+empBrnch+'&monthto='+monthto+'&monthfr='+monthfr+'&listType='+repType+'&cmbgroup='+cmbgroup,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);	
		}
		else
		{
			new Ajax.Request(
			  'common_ajax.php?inputId=empSearch_ra1&empBrnCode='+empBrnch+'&monthto='+monthto+'&monthfr='+monthfr+'&listType='+repType+'&cmbgroup='+cmbgroup,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);	
		}
	}
	
	function blacklist_isNumberInputEmpNoOnly(field, event)
	{
		var empBrnch=document.frmTS.empBrnCode.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empPos=document.frmTS.empPos.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var optionId=document.frmTS.hide_option.value;
		var fileName=document.frmTS.fileName.value;
		
		var txtSearch=document.frmTS.txtSearch.value;
		var srchType=document.frmTS.srchType.value;
		var monthfr=document.frmTS.monthfr.value;
		var monthto=document.frmTS.monthto.value;
		
		var parseStart = Date.parse(monthfr);
		var parseEnd = Date.parse(monthto);
		
		
		var key, keyChar;
		if (window.event)
			key = window.event.keyCode;
		else if (event)
			key = event.which;
		else
			return true;
		
	
		if (key == null || key == 0 || key == 8 || key == 27 || key == 13) 
		{
			if (key == 13) 
			{
				if(txtSearch!=""){
					if(srchType==0){
						alert("Please specify the filter.");
						$('srchType').focus();
						return false;
					}
				}
					
				if(srchType>0){
					if(txtSearch==""){
						alert("Please specify the data to be search.");
						$('txtSearch').focus();
						return false;
					}
					else{
						if(srchType==6)
						{
							if(!Date.parse(txtSearch)){
								alert("Date Hired should be in date format.\nmm/dd/yyyy");
								$('txtSearch').focus();
								return false;
							}
						}
						if(srchType==7)
						{
							if(!Date.parse(txtSearch)){
								alert("Date Resigned should be in date format.\nmm/dd/yyyy");
								$('txtSearch').focus();
								return false;
							}
						}
					}
				}
				
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
			      'common_ajax.php?inputId=empSearch_blacklist&empBrnCode='+empBrnch+'&monthto='+monthto+'&monthfr='+monthfr+'&srchType='+srchType+'&txtSearch='+txtSearch+'&fileName='+fileName+'&optionId='+optionId+'&hide_empDept='+hide_empDept+'&empPos='+empPos+'&empDept='+empDept+'&empDiv='+empDiv,
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
	}
	
	function blacklist_validate()
	{
		var empBrnch=document.frmTS.empBrnCode.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empPos=document.frmTS.empPos.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var optionId=document.frmTS.hide_option.value;
		var fileName=document.frmTS.fileName.value;
		
		var txtSearch=document.frmTS.txtSearch.value;
		var srchType=document.frmTS.srchType.value;
		var monthfr=document.frmTS.monthfr.value;
		var monthto=document.frmTS.monthto.value;
		
		var parseStart = Date.parse(monthfr);
		var parseEnd = Date.parse(monthto);
		
		if(empBrnch==0){
			alert("Please select branch.");
			//$('empBrnch').focus();
			return false;	
		}
		
		if(txtSearch!=""){
			if(srchType==0){
				alert("Please specify the filter.");
				$('srchType').focus();
				return false;
			}
		}
			
		if(srchType>0){
			if(txtSearch==""){
				alert("Please specify the data to be search.");
				$('txtSearch').focus();
				return false;
			}
			else{
				if(srchType==6)
				{
					if(!Date.parse(txtSearch)){
						alert("Date Hired should be in date format.\nmm/dd/yyyy");
						$('txtSearch').focus();
						return false;
					}
				}
				if(srchType==7)
				{
					if(!Date.parse(txtSearch)){
						alert("Date Resigned should be in date format.\nmm/dd/yyyy");
						$('txtSearch').focus();
						return false;
					}
				}
			}
		}
		
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
			  'common_ajax.php?inputId=empSearch_blacklist&empBrnCode='+empBrnch+'&monthto='+monthto+'&monthfr='+monthfr+'&srchType='+srchType+'&txtSearch='+txtSearch+'&fileName='+fileName+'&optionId='+optionId+'&hide_empDept='+hide_empDept+'&empPos='+empPos+'&empDept='+empDept+'&empDiv='+empDiv,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);	
		
		return true;
		
	}
	
	function blacklist_isNumberInputEmpNoOnly(field, event)
	{
		var empBrnch=document.frmTS.empBrnCode.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empPos=document.frmTS.empPos.value;
		var hide_empDept=document.frmTS.hide_empDept.value;
		var optionId=document.frmTS.hide_option.value;
		var fileName=document.frmTS.fileName.value;
		
		var txtSearch=document.frmTS.txtSearch.value;
		var srchType=document.frmTS.srchType.value;
		var monthfr=document.frmTS.monthfr.value;
		var monthto=document.frmTS.monthto.value;
		
		var parseStart = Date.parse(monthfr);
		var parseEnd = Date.parse(monthto);
		
		
		var key, keyChar;
		if (window.event)
			key = window.event.keyCode;
		else if (event)
			key = event.which;
		else
			return true;
		
	
		if (key == null || key == 0 || key == 8 || key == 27 || key == 13) 
		{
			if (key == 13) 
			{
				if(txtSearch!=""){
					if(srchType==0){
						alert("Please specify the filter.");
						$('srchType').focus();
						return false;
					}
				}
					
				if(srchType>0){
					if(txtSearch==""){
						alert("Please specify the data to be search.");
						$('txtSearch').focus();
						return false;
					}
					else{
						if(srchType==6)
						{
							if(!Date.parse(txtSearch)){
								alert("Date Hired should be in date format.\nmm/dd/yyyy");
								$('txtSearch').focus();
								return false;
							}
						}
						if(srchType==7)
						{
							if(!Date.parse(txtSearch)){
								alert("Date Resigned should be in date format.\nmm/dd/yyyy");
								$('txtSearch').focus();
								return false;
							}
						}
					}
				}
				
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
			      'common_ajax.php?inputId=empSearch_blacklist&empBrnCode='+empBrnch+'&monthto='+monthto+'&monthfr='+monthfr+'&srchType='+srchType+'&txtSearch='+txtSearch+'&fileName='+fileName+'&optionId='+optionId+'&hide_empDept='+hide_empDept+'&empPos='+empPos+'&empDept='+empDept+'&empDiv='+empDiv,
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
	
	function option_button_click(id_ko) {
		var option_button = document.getElementById(id_ko).value;
		document.frmTS.hide_option.value = option_button;
		document.frmTS.submit();
	}
	
	