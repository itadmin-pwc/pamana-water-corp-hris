

	function validateTabs(act){

		var empInputs = $('frmViewEditEmp').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		var sssExp     = /[0-9]{10}/;
		var tinExp     = /[0-9]{9}/;
		var numericExp = /[0-9]+/;

		if(trim(empInputs['txtempNo']) == ""){
			alert('Employee No. is Required.');
			focusTab(1);
			$('txtempNo').focus();
			return false;
		}
        if(trim(empInputs['chempNo']) == "1"){
			alert('Employee No. is already used');
			$('txtempNo').focus();
			$('txtempNo').select();
			focusTab(1);
			return false;
		}		
		if(trim(empInputs['txtlname']) == ""){
			alert('Employee Last Name is Required.');
			focusTab(1);
			$('txtlname').focus();
			return false;
		}

		if(trim(empInputs['txtfname']) == ""){
			alert('Employee First Name is Required.');
			$('txtfname').focus();
			focusTab(1);
			return false;
		}
		if(trim(empInputs['txtmname']) == ""){
			alert('Employee Middle Name is Required.');
			$('txtmname').focus();
			focusTab(1);
			return false;
		}		
		if(empInputs['cmbcompny'] == 0){
			alert('Company is Required.');
			$('cmbcompny').focus();
			focusTab(1);
			return false;
		}

		if(empInputs['cmbbranch'] == 0){
			alert('Branch is Required.');
			$('cmbbranch').focus();
			focusTab(1);
			return false;
		}
		if(empInputs['cmblocation'] == 0){
			alert('Location is Required.');
			$('cmblocation').focus();
			focusTab(1);
			return false;
		}		

		if(trim(empInputs['txtadd1']) == ""){
			alert('Address is Required.');
			$('txtadd1').focus();
			focusTab(2);
			return false;
		}

		if(trim(empInputs['txtadd2']) == ""){
			alert('Contact Info is Required.');
			$('txtadd2').focus();
			focusTab(2);
			return false;
		}
		if(empInputs['cmbcity'] == 0){
			alert('City is Required.');
			$('cmbcity').focus();
			focusTab(2);
			return false;
		}
	
		if(empInputs['Birthday_M'] == -1 || empInputs['Birthday_D'] == -1 || empInputs['Birthday_Y'] == -1){
			alert('Invalid Birthday');
			if(empInputs['Birthday_M'] == -1){
				$('Birthday_M').focus();
			}
			if(empInputs['Birthday_D'] == -1){
				$('Birthday_D').focus();	
			}
			if(empInputs['Birthday_Y'] == -1){
				$('Birthday_Y').focus();
			}
			focusTab(3);
			return false;
		}
	
		if(empInputs['cmbbloodtype'] == 0){
			alert('Blood Type is Required.');
			$('cmbbloodtype').focus();
			focusTab(3);
			return false;
		}	
        if(trim(empInputs['txtsss']) == ""){
			alert('SSS No. is Required.');
			$('txtsss').focus();
			focusTab(4);
			return false;
        }
		if(!empInputs['txtsss'].match(sssExp)){
			alert('Invalid SSS No.\nvalid : 1212345671');
			$('txtsss').focus();
			$('txtsss').select();
			focusTab(4);
			return false;
		}
/*        if(trim(empInputs['chsss']) == "1"){
			alert('SSS No. is already used');
			$('txtsss').focus();
			$('txtsss').select();
			focusTab(4);
			return false;
		}
*/        if(trim(empInputs['chsss']) == "2"){
			alert('SSS No. is blacklisted');
			focusTab(4);
			return false;
		}
		if(trim(empInputs['txtphilhealth']) == ""){
			alert('Phil Health No. is Required.');
			$('txtphilhealth').focus();
			focusTab(4);
			return false;
		}	
		if(trim(empInputs['txtphilhealth']) != ""){
			if(!empInputs['txtphilhealth'].match(numericExp)){
				alert('Invalid Phil Health No.\nvalid : Numbers Only');
				$('txtphilhealth').focus();
				$('txtphilhealth').select();
				focusTab(4);
				return false;			
			}
		}		
/*		if(trim(empInputs['chphilhealth']) == "1"){
			alert('Phil Health No. is already used.');
			$('txtphilhealth').focus();
			focusTab(4);
			return false;
		}*/
		
		if(trim(empInputs['txttax']) == ""){
			alert('Tax ID No. is Required.');
			$('txttax').focus();
			focusTab(4);
			return false;
		}
		if(!empInputs['txttax'].match(tinExp)){
			alert('Invalid Tax ID No.\nvalid : 123123123');
			$('txttax').focus();
			$('txttax').select();
			focusTab(4);
			return false;			
		}		
/*		if(trim(empInputs['chtaxid']) == "1"){
			alert('Tax ID No. is already used.');
			$('txttax').focus();
			focusTab(4);
			return false;
		}*/

        if(trim(empInputs['txthdmf']) == ""){
            alert('HDMF No. is Required.');
            $('txthdmf').focus();
			focusTab(4);
			return false;
        }
		if(trim(empInputs['txthdmf']) != ""){
			if(!empInputs['txthdmf'].match(numericExp)){
				alert('Invalid HDMF No.\nvalid : Numbers Only');
				$('txthdmf').focus();
				$('txthdmf').select();
				focusTab(4);
				return false;			
			}
		}		
/*		if(trim(empInputs['chhdmf']) == "1"){
            alert('HDMF No. is already used.');
            $('txthdmf').focus();
			focusTab(4);
			return false;
        }*/
        if(empInputs['cmbbank'] == 0){
            alert('Bank Type is Required.');
            $('cmbbank').focus();
			focusTab(4);
            return false;
        }		
        if(trim(empInputs['txtbankaccount']) == ""){
            alert('Bank Account No. is Required.');
            $('txtbankaccount').focus();
			focusTab(4);
            return false;
        }	
		if(trim(empInputs['txtbankaccount']) != ""){
			if(!empInputs['txtbankaccount'].match(numericExp)){
				alert('Invalid Bank Account No.\nvalid : Numbers Only');
				$('txtbankaccount').focus();
				$('txtbankaccount').select();
				focusTab(4);
				return false;			
			}
		}		
		if(empInputs['cmbposition'] == 0){
			alert('Position is Required.');
			$('cmbposition').focus();
			focusTab(5);
			return false;
		}		
        if(empInputs['cmbstatus'] == 0){
            alert('Status is Required.');
            $('cmbstatus').focus();
			focusTab(5);
            return false;
        }
        if(empInputs['cmbeffectivity_M'] == -1 || empInputs['cmbeffectivity_D'] == -1 || empInputs['cmbeffectivity_Y'] == -1){
            alert('Invalid Date for Effectivity');
            if(empInputs['cmbeffectivity_M'] == -1){
                $('cmbeffectivity_M').focus();
            }
            if(empInputs['cmbeffectivity_D'] == -1){
                $('cmbeffectivity_D').focus();	
            }
            if(empInputs['cmbeffectivity_Y'] == -1){
                $('cmbeffectivity_Y').focus();
            }
			focusTab(5);
            return false;
        }
        if(trim(empInputs['txtsalary']) == ""){
            alert('Salary is Required.');
            $('txtsalary').focus();
			focusTab(6);
            return false;
        }
        if(empInputs['cmbpstatus'] == 0){
            alert('Payroll Status is Required.');
            $('cmbpstatus').focus();
			focusTab(6);
            return false;
        }
		
		if(!empInputs['txtsalary'].match(numericExpWdec)){
			alert('Invalid Salary\nvalid : Numbers Only with two(2) decimal or without decimal');
			$('txtsalary').focus();
			focusTab(6);
			return false;
		}		
		
        if(empInputs['cmbgroup'] == 0){
            alert('Group is Required.');
            $('cmbgroup').focus();
			focusTab(6);
            return false;
        }
        if(empInputs['cmbCategory'] == 0){
            alert('Category is Required.');
            $('cmbCategory').focus();
			focusTab(6);
            return false;
        }		

		if(trim(empInputs['chAcctNo']) == "1" || trim(empInputs['chsss']) == "1" || trim(empInputs['chphilhealth']) == "1" || trim(empInputs['chtaxid']) == "1" || trim(empInputs['chhdmf']) == "1"){
			var msg;
			if(trim(empInputs['chhdmf']) == "1"){
				msg ='HDMF No.';
			}
			if(trim(empInputs['chtaxid']) == "1"){
				if (msg != "") {
					msg = msg + ',';
				} 
				msg = msg + 'Tax ID No.';
			}
			if(trim(empInputs['chphilhealth']) == "1"){
				if (msg != "") {
					msg = msg + ',';
				} 
				msg = msg + ',Phil Health No.';
			}
			if(trim(empInputs['chsss']) == "1"){
				if (msg != "") {
					msg = msg + ',';
				} 
				msg = msg + ',SSS No.';
			}
			if(trim(empInputs['chAcctNo']) == "1"){
				if (msg != "") {
					msg = msg + ',';
				} 
				msg = msg + ',Bank Account No.';
			}
			
			var duplicateno = confirm(msg+' is/are already used. Do you want to proceed?');
			if (duplicateno==false) {
				focusTab(4);				
				return false;
			}
		}
}

	var numTabs = 8;
	
	function focusTab(idNum)
	{
	  var tabId = 'tab' + idNum;
	  var conId = 'content' + idNum;
	
	  var tabEle = document.getElementById(tabId);
	  var conEle = document.getElementById(conId);
	
	  chClss(tabId, 'tab' + idNum + '-focus');
	  chClss(conId, 'content' + idNum + '-focus');
	
	  for (var i = 1; i <= numTabs; i++)
	  {
		if (i != idNum)
		{
		  chClss('tab' + i, 'tab' + i);
		  chClss('content' + i, 'content' + i);
	
		  document.getElementById('tab' + i).style.zIndex = 2;
		  document.getElementById('content' + i).style.zIndex = 1;
		}
	  }
	
	  tabEle.style.zIndex = 99;
	  conEle.style.zIndex = 98;
	}
	
	function chClss(eleId, newClass)
	{
	  var theEle;
	
	  if (document.getElementById) theEle = document.getElementById(eleId);
	  if (!theEle || typeof(theEle.className) == 'undefined') return false;
	
	  theEle.className = newClass;
	
	  return true;
	}


	function checkmarital(){
        if(document.getElementById("cmbmaritalstatus").value == "SG"){
			document.getElementById("txtspouse").disabled=true;
        }
		else {
			document.getElementById("txtspouse").disabled=false;
		}
	}
	
	function checkrate(){
        if(document.getElementById("cmbpstatus").value == "D"){
			document.getElementById("txtsalary").readOnly=true;
			document.getElementById("txtdailyrate").readOnly=false;
        }
		else if(document.getElementById("cmbpstatus").value == "M") {
			document.getElementById("txtsalary").readOnly=false;
			document.getElementById("txtdailyrate").readOnly=true;

		}
	}	
	
	function getresult(id,url,code,dv) {
		url=url+"?&id="+id+"&code="+code;
		new Ajax.Request(
		  url,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$(dv).innerHTML=req.responseText;
			 }
		  }
		);
	}

	function getcompany(compCode) {
		$('company_code').value=compCode;
	}

	function getPosInfo(posCode) {
		
		url="profile.obj.php?code=cddivision&posCode="+posCode+"&company_code="+$('company_code').value;
		new Ajax.Request(
		  url,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				eval(req.responseText);	
			 }
		  }
		);
	}

	function getsalary(id,url,code,dv,rate) {
		url=url+"?&id="+id+"&code="+code+"&rate="+rate;
		new Ajax.Request(
		  url,
		  {
			 asynchronous : true,     
			 onComplete   : function (req){
				$(dv).innerHTML=req.responseText;
			 }
		  }
		);
	
	}


	function savecontact(empNo,act,recNo,url) {
		var contacttype=document.getElementById("cmbcontacttype").value;
		var contactdesc=document.getElementById("txtdesc").value;
		var str;
		url=url+"?contacttype="+contacttype+"&contactdesc="+contactdesc+"&code=cdsavecontact&empNo="+empNo+"&act="+act+"&recNo="+recNo;
		var errcon=0;
		if(contacttype == 0){
			alert('Contact Type is Required.');
			document.getElementById("cmbcontacttype").focus();
			errcon=1;
		}

		if(contactdesc == "" && errcon==0){
			alert('Contact Description is Required.');
			document.getElementById("txtdesc").focus();
			errcon=2;
		}

		if (act=="Add") { str="Added";} else {str="Updated";}
		if (errcon==0) {
		new Ajax.Request(url,{
			onComplete : function(req){
				eval(req.responseText);	
				document.getElementById("cmbcontacttype").value=0;
				document.getElementById("txtdesc").value="";
				alert("Record "+str);
			}
		});
		}
	}


	function checkno(field,value,type,label,dv){
		cmbbank = document.getElementById('cmbbank').value;
		if(field == "empAcctNo") {
			value=document.getElementById('txtbankaccount').value;
			value=value+"'_and_empBankCd='"+cmbbank+"";
		}
		if (field == "empSssNo") {
			value = document.getElementById('txtsss').value+","+document.getElementById('txtlname').value+','+document.getElementById('txtfname').value+','+document.getElementById('txtmname').value;
		}
		params = 'profile.obj.php?code=cdcheckno&table=tblEmpMast&field='+field+"&value="+value+"&type="+type+"&dv="+dv+"&label="+label;
		new Ajax.Request(params,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);	
			}	
		})
	}
	
	
	function computeRates(Rate,compcode,cat){
		
		params = 'profile.obj.php?code=cdsalary&Rate='+Rate+'&compcode='+compcode+'&cat='+cat;
		new Ajax.Request(params,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		})
	}
	
	function saverestday(){
		rdDate=document.getElementById('date').value;
        if(rdDate == ""){
            alert('Date is Required.');
            return false;
        }		
		params = 'restday_act.php?code=add&date='+rdDate;
		new Ajax.Request(params,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				
			}	
		})
	}

	function delrestday(rdDate){
		var deleContact = confirm('Are you sure do you want to delete?\nRest Day : ' +rdDate);
		if(deleContact == true){
			params = 'restday_act.php?code=delete&date='+rdDate;
			new Ajax.Request(params,{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);
					pager("restday_list_ajax.php","RDlist",'load',0,0,'','','','../../../images/');  				
				}	
			})
		}
	}
	
