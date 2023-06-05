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
	  var groupType=document.frmTS.groupType.value;
	  var orderBy=document.frmTS.orderBy.value;
	  var catType=document.frmTS.catType.value;
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
			  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType,
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
		var groupType=document.frmTS.groupType.value;
		var orderBy=document.frmTS.orderBy.value;
		var catType=document.frmTS.catType.value;
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
				  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType+'&payPd='+payPd,
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
		var empName=document.frmTS.empName.value;
		var empDiv=document.frmTS.empDiv.value;
		var empDept=document.frmTS.empDept.value;
		var empSect=document.frmTS.empSect.value;
		var orderBy=document.frmTS.orderBy.value;
		var groupType=document.frmTS.groupType.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		document.frmTS.action = 'timesheet_list_pdf.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd;
		document.frmTS.target = "_blank";
		document.frmTS.submit();
		document.frmTS.action = "timesheet_list_ajax.php";
		document.frmTS.target = "_self";
	}
	function printEarningsList() {
		var isSearch=document.frmTSko.isSearch2.value;
		var srchType=document.frmTSko.srchType2.value;
		var txtSrch=document.frmTSko.txtSrch2.value;
		document.frmTSko.action = 'earnings_list_pdf.php?isSearch='+isSearch+'&srchType='+srchType+'&txtSrch='+txtSrch;
		document.frmTSko.target = "_blank";
		document.frmTSko.submit();
		document.frmTSko.action = "timesheet_list_ajax.php";
		document.frmTSko.target = "_self";
	}
	function printDeductionsList() {
		var isSearch=document.frmTSko.isSearch2.value;
		var srchType=document.frmTSko.srchType2.value;
		var txtSrch=document.frmTSko.txtSrch2.value;
		document.frmTSko.action = 'deductions_list_pdf.php?isSearch='+isSearch+'&srchType='+srchType+'&txtSrch='+txtSrch;
		document.frmTSko.target = "_blank";
		document.frmTSko.submit();
		document.frmTSko.action = "timesheet_list_ajax.php";
		document.frmTSko.target = "_self";
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
		var groupType=document.frmTS.groupType.value;
		var orderBy=document.frmTS.orderBy.value;
		var catType=document.frmTS.catType.value;
		var payPd=document.frmTS.payPd.value;
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		}
		new Ajax.Request(
		  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType+'&payPd='+payPd+'&thisValue='+thisValue,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);
	}
	function viewDetails(nUrl,option,recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		if (nUrl=="restday_act.php") {
			option=option+" Rest Day";
			wd=380;
			ht=210;
		} else {
			option=option+" Contact";
			wd=450;
			ht=190;
		}
		nUrl=nUrl+"?recNo="+recNo+"&act="+option;
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:wd, 
		height:ht, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: option, 
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
		        pager(URL,ele,'load',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function deleContact(recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch,contactName){
		var deleContact = confirm('Are you sure do you want to delete?\nContact : ' +contactName);
		if(deleContact == true){
			new Ajax.Request('ajax.php?code=delcontact&recNo='+recNo,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager(URL,ele,'load',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');

				},
				onCreate : function (){
					$('indicator2').src="../../../images/wait.gif";
				},
				onSuccess : function (){
					$('indicator2').innerHTML='';
				}				
			})
		}
	}	

	function PopUp(nUrl,option,recNo,URL,ele,offset,isSearch,txtSrch,cmbSrch){
		if (option=="ADD BANK" || option=="EDIT BANK") {
			var wd = 448;
			var ht = 198;
		} else if (option=="ADD HOLIDAY" || option=="EDIT HOLIDAY") {
			var wd = 448;
			var ht = 198;
		} else if (option=="DETAILED COMPANY INFO") {
			var wd = 448;
			var ht = 278;
		} else if (option=="ADD COMPANY" || option=="EDIT COMPANY") {
			var wd = 440;
			var ht = 393;
		} else if (option=="GENERATE PAY PERIOD") {
			var wd = 440;
			var ht = 123;
		} else if (option=="ADD DAY TYPE" || option=="EDIT DAY TYPE") {
			var wd = 440;
			var ht = 103;
		} else if (option=="ADD LOAN TYPE" || option=="EDIT LOAN TYPE") {
			var wd = 440;
			var ht = 180;
		} else if (option=="ADD TAX EXEMPTION" || option=="EDIT TAX EXEMPTION") {
			var wd = 440;
			var ht = 128;
		} else if (option=="ADD ALLOWANCE TYPE" || option=="EDIT ALLOWANCE TYPE") {
			var wd = 440;
			var ht = 180;
		}else if (option=="ADD CUSTOMER NO." || option=="EDIT CUSTOMER NO.") {
			var wd = 440;
			var ht = 118;
		} else if (option=="EDIT PAY PERIOD") {
			var wd = 440;
			var ht = 203;
		} else if (option=="EDIT REF. NO." || option=="ADD REF. NO.") {
			var wd = 440;
			var ht = 103;
		}
		var viewDtl = new Window({
		id: "viewDtl",
		className : 'mac_os_x',
		width:wd, 
		height:ht, 
		zIndex: 100, 
		resizable: false, 
		minimizable : false,
		title: option, 
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
		        pager(URL,ele,'load',offset,isSearch,txtSrch,cmbSrch,'','../../../images/');
				Windows.removeObserver(this);
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}
	
	function DeleteRec(Delurl,Listurl,label) {
	var chDel = confirm("Are you sure you want to delete "+label+"?");	
		if (chDel) {
				params = Delurl;
				new Ajax.Request(params,{
					method : 'get',
					onComplete : function (req){
					eval(req.responseText);
					pager(Listurl,'TSCont','load',0,0,'','','','../../../images/');
					}	
				});
				
		}
	}