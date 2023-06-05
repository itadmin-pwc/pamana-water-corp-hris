// JavaScript Document
	function isNumberInputEmpNoOnly(field, event) {
	  	var empNo=document.frmTS.empNo.value;
	  	var empName=document.frmTS.empName.value;
	  	var optionId=document.frmTS.hide_option.value;
	  	var fileName=document.frmTS.fileName.value;
	  	var from=document.frmTS.txtfrDate.value;
	  	var to=document.frmTS.txttoDate.value;
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
			   'ts_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&optionId='+optionId+'&fileName='+fileName+'&thisValue=verifyEmp'+'&from='+from+'&to='+to,
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
	function option_button_click(id_ko) {
		var option_button = document.getElementById(id_ko).value;
		document.frmTS.hide_option.value = option_button;
		document.frmTS.submit();
	}


	function EvenReport() {
		var branch=document.frmTS.branch.value;
		var from=document.frmTS.txtfrDate.value;
	  	var to=document.frmTS.txttoDate.value;
	  	var empNo=document.frmTS.empNo.value;
	  	var bio=document.frmTS.bio.value;
		var Grp=document.frmTS.cmbGroup.value;
		var div = document.frmTS.cmbDivision.value;
		var dept = document.frmTS.cmbDepartment.value;
		var ext='&from='+from+'&to='+to+'&empNo='+empNo+'&bio='+bio+'&div='+div+'&dept='+dept;
		if (empNo=='') {
			if (branch==0) {
				alert('Branch is required.');	
				return false;
			}
			if (branch=='0001') {
				if (Grp==0) {
					alert('Pay Group is required.');	
					return false;
				} 
				else 
				{
					ext=ext+'&group='+Grp;
				}
				if(div==0){
					alert('Division is required.');
					return false;	
				}
				if(dept==0){
					alert('Department is required.');
					return false;	
				}			
			}
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=eventReport'+ext,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}
	
	function TSProoflist() {
		var branch=document.frmTS.branch.value;
		var Grp=document.frmTS.cmbGroup.value;
	  	var empNo=document.frmTS.empNo.value;
		var payPd = document.frmTS.cmbpayPd.value;
		var Cat = document.frmTS.cmbCategory.value;
		var div = document.frmTS.cmbDivision.value;
		var dept = document.frmTS.cmbDepartment.value;
		var ext='&empNo='+empNo+'&cat='+Cat+'&div='+div+'&dept='+dept;
		var url;
		
		if (branch=='0001') {
			if (Grp==0) {
				alert('Pay Group is required.');	
				return false;
			} else {
				ext=ext+'&group='+Grp;
			}
			if(div==0){
				alert('Division is required.');
				return false;	
			}
			if(dept==0){
				alert('Department is required.');
				return false;	
			}
		}
		
		if (empNo=='') {
			if (branch==0) {
				alert('Branch is required.');	
				return false;
			}
		}
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}	
		
		if(div==0 && dept==0){
			url = '&inputId=TSProoflist&payPd='+payPd;
		}
		else{
			url = '&inputId=TSProofListReport&payPd='+payPd;
		}	
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+ext+url,
		  {
			 asynchronous : true,  
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}
	
	function OB() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=OB&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}	
	
	function OverBreak() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		var breakHr = document.frmTS.cmbBreak.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		if(breakHr==0){
			alert('Break is required.');
			return false;	
		}
		
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=OverBreak&group='+grp+'&payPd='+payPd+'&breakHr='+breakHr,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}	
	
	
	function OT() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=OT&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}	
	
	function TS_Adjustment(id) {
		var grp = document.frmTS.cmbGroup.value;
		var frm = document.frmTS.txtfrDate.value;
		var to = document.frmTS.txttoDate.value;
		var url;
		if (grp==0) {
			alert('Group is required.');	
			return false;
		}
		if (frm=="") {
			alert('From date is required.');	
			return false;
		}
		if (to==0) {
			alert('To Date is required.');	
			return false;
		}
		if(id=="O"){
			url = "&id=O";		
		}
		else if(id=="A"){
			url = "&id=A";			
		}
		else if(id=="P"){
			url = "&id=P";
		}
		
		new Ajax.Request(
		  'ts_ajax.php?inputId=TS_Adjustment&frm='+frm+'&to='+to+'&group='+grp+url,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}			

	function TS_Adjustment_with_Amount(id) {
		var grp = document.frmTS.cmbGroup.value;
		var frm = document.frmTS.txtfrDate.value;
		var to = document.frmTS.txttoDate.value;
		if (grp==0) {
			alert('Group is required.');	
			return false;
		}
		if (frm=="") {
			alert('From date is required.');	
			return false;
		}
		if (to==0) {
			alert('To Date is required.');	
			return false;
		}
		if(id=="O"){
			url = "&id=O";		
		}
		else if(id=="A"){
			url = "&id=A";			
		}
		else if(id=="P"){
			url = "&id=P";
		}
		
		
		new Ajax.Request(
		  'ts_ajax.php?inputId=TS_Adjustment_with_Amount&frm='+frm+'&to='+to+'&group='+grp+url,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}			

	
	function TS_Corrections() {
		var branch = document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=TS_Corrections&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}
	
	function Earnings_Adjustment(){
		var grp = document.frmTS.cmbGroup.value;
		var frm = document.frmTS.txtfrDate.value;
		var to = document.frmTS.txttoDate.value;
		if (grp==0) {
			alert('Group is required.');	
			return false;
		}
		if (frm=="") {
			alert('From date is required.');	
			return false;
		}
		if (to==0) {
			alert('To Date is required.');	
			return false;
		}
		
		new Ajax.Request(
		  'ts_ajax.php?inputId=Earnings_Adjustment&frm='+frm+'&to='+to+'&group='+grp,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}

	function CS() {
		var branch = document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=CS&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}
	function OT_Prooflist() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=OT_Prooflist&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}	
	
	function Deductions() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}		
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=Deductions&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}
	
	function Leaves() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}	
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=Leaves&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}

	function legalPay() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}	
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=legalPay&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}

	function offSetHour() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}	
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=offSetHour&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}	
	
	function RestDay() {
		var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
		if (branch==0) {
			alert('Branch is required.');	
			return false;
		}		
		if (branch==0001) {
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		}
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=RestDay&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}	
	
	function Checkgroup(brnch) {
		if (brnch=='0001') {
			document.frmTS.cmbGroup.disabled=false;
			document.frmTS.cmbDivision.disabled=false;
			document.frmTS.cmbDepartment.disabled=false;
			grp = document.frmTS.cmbGroup.value;
			//dpt = document.frmTS.cmbDivision.value;
			if (grp!=0) {
				getpayPd('0001',grp);
			}
			//setDept(dpt);
		} else {
			document.frmTS.cmbGroup.value =0;
			document.frmTS.cmbDivision.value=0;
			document.frmTS.cmbDepartment.value=0;
			document.frmTS.cmbGroup.disabled=true;
			document.frmTS.cmbDivision.disabled=true;
			document.frmTS.cmbDepartment.disabled=true;
			getpayPd(brnch,'');
		}
	}

	function CheckGroupOverBreak(brnch) {
		if (brnch=='0001') {
			document.frmTS.cmbGroup.disabled=false;
			document.frmTS.cmbpayPd.disabled=false;
			grp = document.frmTS.cmbGroup.value;
			//dpt = document.frmTS.cmbDivision.value;
			if (grp!=0) {
				getpayPd('0001',grp);
			}
			//setDept(dpt);
		} else {
			document.frmTS.cmbGroup.value =0;
			document.frmTS.cmbpayPd.value=0;
			document.frmTS.cmbGroup.disabled=true;
			document.frmTS.cmbpayPd.disabled=true;
			getpayPd(brnch,'');
		}
	}

	function setGroup(grp){
		if (grp != '') 
			grp = '&grp='+grp
			new Ajax.Request(
			  'ts_ajax.php?inputId=openPayPeriod'+grp,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					$('divpayPd').innerHTML=req.responseText;
				 }
			  }
			);			
	}
	
	function getDept(dpt){
		if(dpt != ''){
			dpt = '&dpt='+dpt
			new Ajax.Request(
				'ts_ajax.php?inputId=department'+dpt,
				{
					asynchronous : true,
					onComplete   : function (req){
						$('divDept').innerHTML=req.responseText;	
					}	
				}
			);		
		}
	}
	
	function getpayPd(branch,grp) {
		if (grp != '') 
			grp = '&grp='+grp
		
			new Ajax.Request(
			  'ts_ajax.php?branch='+branch+'&inputId=payPd'+grp,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					$('divpayPd').innerHTML=req.responseText;
				 }
			  }
			);				
	}
	
	function ViolationsReport() {
		var branch=document.frmTS.branch.value;
		var from=document.frmTS.txtfrDate.value;
	  	var to=document.frmTS.txttoDate.value;
	  	var empNo=document.frmTS.empNo.value;
	  	var bio=document.frmTS.bio.value;
		var violations = document.frmTS.violations.value;
		var ext='&from='+from+'&to='+to+'&empNo='+empNo+'&bio='+bio+'&violations='+violations;
		if (empNo=='') {
			if (branch==0) {
				alert('Branch is required.');	
				return false;
			}
		}
		new Ajax.Request(
		  'ts_ajax.php?branch='+branch+'&inputId=violationsReport'+ext,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
			 }
		  }
		);		
	}


	function TSSummary() {
	// alert("hoy");
		//var branch=document.frmTS.branch.value;
		var grp = document.frmTS.cmbGroup.value;
		var payPd = document.frmTS.cmbpayPd.value;
				
		
			if (grp==0) {
				alert('Group is required.');	
				return false;
			}
		
		
		if (payPd==0) {
			alert('Payroll Period is required.');	
			return false;
		}

		new Ajax.Request(
		 'ts_ajax.php?inputId=TSSummary&group='+grp+'&payPd='+payPd,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);
				document. getElementById("tssum"). disabled = false;
			 },
			onCreate : function(){
				timedCount();
				$('tssum').disabled=true;
			},
			onSuccess: function (){
				stopCount();
				$('tssum').disabled=false;
				$('caption').innerHTML="";
			
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
		t=0;
	}	
	
	
	