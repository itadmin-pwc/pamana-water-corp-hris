<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("deductions.obj.php");


$deductionsObj = new maintDeduct($_GET,$_SESSION);
$deductionsObj->validateSessions('','MODULES');


if(isset($_GET['action'])){
	switch ($_GET['action']){
		case 'NEWREFNO':
				$lastDedrefNo = $deductionsObj->getDedLastRefNo();
				$newDedRefNo = $lastDedrefNo['dedRefNo']+1;
				$deductionsObj->updateDedLastRefNo($newDedRefNo);
				echo "$('refNo').value=$newDedRefNo";
			exit();
		break;
		case 'getEmpInfo':
				if ($_SESSION['pay_category'] != 9) {	
					$empInfo = $deductionsObj->getUserInfo($_SESSION['company_code'],$_GET['empNo'],'and empPayCat='.$_SESSION["pay_category"].' and empPayGrp='.$_SESSION["pay_group"].'');
				} else {
					$qryEmpInfo = "SELECT tblEmpMast.*
							FROM  tblLastPayEmp INNER JOIN tblEmpMast ON tblLastPayEmp.compCode = tblEmpMast.compCode 
							AND tblLastPayEmp.empNo = tblEmpMast.empNo
							WHERE tblLastPayEmp.compCode='{$_SESSION['company_code']}'
							AND empPayGrp='{$_SESSION['pay_group']}'
							AND tblLastPayEmp.empNo = '{$_GET['empNo']}'
							";
					$empInfo = $deductionsObj->getSqlAssoc($deductionsObj->execQry($qryEmpInfo));
				}	
				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				if($empInfo['empPayGrp'] == 1){ $payGroup = '1-One';}
				if($empInfo['empPayGrp'] == 2){ $payGroup = '2-Two';}
				
				if($empInfo == 0){
					echo 0;
				}
				else{
					$payCat = $deductionsObj->getPayCat($_SESSION['company_code'],"AND payCat = '{$_SESSION['pay_category']}'");
					echo "$('txtAddEmpName').value='".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName $empInfo[empLastName]';";
					echo "$('txtAddPayGrp').value='$payGroup';";
					echo "$('txtAddPayCat').value='$payCat[payCat]-$payCat[payCatDesc]';";
					echo "$('txtAddCntrlNo').value=0;";
					echo "$('txtAddCntrlNo').select();";
				}
				exit();
		break;
		case 'addHdrDtlMid':
				if($deductionsObj->checkDedHeader() > 0){
					echo 1;//"alert('Reference Number Already Exist...')";
				}
				else{
					if($deductionsObj->addDedHeader() == true && $deductionsObj->addDedDetail(1) == true){
						echo 2;//"alert('Deductions Successfully Saved')";
					}
					else{
						if($deductionsObj->addDedHeader() == false){
							echo 3;//"alert('Deductions Header Failed Saved')";
						}
						if($deductionsObj->addDedDetail(1) == false){
							echo 4;//"alert(' Deductions Detail Failed Saved')";
						}
					}
				}
				exit();
		break;
		case 'UpthdrAddDtl':
				if($deductionsObj->checkDedDetail() > 0){
					echo 1;//"alert('Employee Already Exist In Deduction Detail...');";
				}
				else {
					if($deductionsObj->addDedDetail(2) == true && $deductionsObj->updateDedHeader() == true){
						echo 2;//"alert('Additional Detail Successfully Saved')";
					}
					else {
						if($deductionsObj->updateDedHeader() == false){
							echo 3;//"alert('Deductions Header Failed Saved')";
						}
						if($deductionsObj->addDedDetail(2) == false){
							echo 4;//"alert(' Deductions Detail Failed Saved')";
						}				
					}
				}
				exit();
		break;
		case 'deleDedDtl':
				if($deductionsObj->deleDedDtl() == true){
					echo "alert('Detail #$_GET[empNo] Successfully Deleted')";
				}
				else{
					echo "alert('Detail #$_GET[empNo] Delete Failed')";
				}
				exit();
		break;
		case 'editRef':
				$getHeader = $deductionsObj->getDedTranHEader();
				if(!empty($getHeader)){
					$refNo = $getHeader['refNo'];
				}
				else{
					$refNo = 0;
				}
				echo $refNo;
				exit();
		break;
		case 'deleDeduc':
				if($deductionsObj->deleDeduc() == true){
					echo "alert('Deductions Entry #$_GET[refNo] Successfully Deleted');";
				}
				else{
					echo "alert('Deductions Entry #$_GET[refNo] Deletion Failed');";
				}
				exit();
		break;
		case 'updtHdr':
				if($deductionsObj->updateDedHeader() == true){
					echo "alert('Header Successfully Updated');";
				}
				else{
					echo "alert('Header UPdating Failed');";
				}
				exit();
		break;
	}
}
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
			
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url('../../../js/themes/mac_os_x.css');</STYLE>
		

	</HEAD>
	<BODY>
		<FORM name='frmDeduct' id="frmDeduct" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="deductionsCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT language="eng" type="text/javascript" charset="UTF-8">
	//disableRightClick()
	
	//option button control 
	function validateMod(mode){

		if(mode == 'EDITRENO'){
			$('newDeduc').innerHTML="<img src='../../../images/application_add_2.png' class='toolbarImg'>";
			$('deleDeduc').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			$('btnUpdtHdr').disabled=true;
			$('refNo').readOnly=false;
			$('refLookup').disabled=false;
			$('btnSaveAddDtl').disabled=true;
			$('refNo').focus();
		}
		if(mode == 'REFRESH'){
			Windows.getWindow('refWin').close();
		}
	}
	
	function newRef(act){
	pager('deductionsAjaxResult.php','deductionsCont','refresh',0,0,'','','','../../../images/');  	
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				$('editDeduc').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>";
				$('deleDeduc').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
				$('cmbTrnType').focus();
			},
			onCreate : function(){
				$('refNoCont').innerHTML='Loading...';
			},
			onSuccess : function(){
				$('refNoCont').innerHTML='';
			}
		});
	}
	
	function editRefNo(act,refVal,evt){
		
		var k = evt.keyCode | evt.which;
		
		param = '?action='+act+"&refNo="+refVal;
		
		if(k == 13){
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					refNo = parseInt(req.responseText);
					if(refNo != 0){
						pager('deductionsAjaxResult.php','deductionsCont',act,0,0,'','','&refNo='+refNo,'../../../images/');  
					}
					else{
						alert('NO RECORD FOUND');
						return false;
					}
				},
				onCreate : function (){
					$('refNoCont').innerHTML='Loading...';
				},
				onSuccess : function (){
					$('refNoCont').innerHTML='';
				}
			})			
		}
	}
	
	function viewLookup(){

		var RefWin = new Window({
			id: "refWin",
			className : 'mac_os_x',
			width:600, 
			height:400, 
			zIndex: 100, 
			resizable: false, 
			title: "Deductions Reference Lookup", 
			minimizable:true,
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true 
		})
			RefWin.setAjaxContent('reference_lookup.php?opnr=deduc','','');
			//RefWin.show(true);
			RefWin.showCenter(true);
			
			//$('editDeduc').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>" 
			
		  myObserver = {
		    onDestroy: function(eventName, win) {

		      if (win == RefWin) {
		        RefWin = null;
		        Windows.removeObserver(this);
		         $('refNo').focus();
		      }
		    }
		  }
		  Windows.addObserver(myObserver);
	}	
	
	function passRefNo(refNoVal){
		pager('deductionsAjaxResult.php','deductionsCont','editRef',0,0,'','','&refNo='+refNoVal,'../../../images/');  
		Windows.getWindow('refWin').close();
	}
	
	pager('deductionsAjaxResult.php','deductionsCont','load',0,0,'','','','../../../images/');  
	
	function getEmployee(evt,eleVal){
		
		var param = '?action=getEmpInfo&empNo='+eleVal;
		
		var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					onComplete : function (req){

						if(parseInt(req.responseText) == 0){
							$('hlprMsg').innerHTML='No Record Found';
							setTimeout(function(){
								$('hlprMsg').innerHTML='Additional Detail';
							},5000);
						} 
						else{
							eval(req.responseText);
						}
					},
					onCreate : function (){
						$('hlprMsg').innerHTML='Loading...';
					},
					onSuccess : function (){
						$('hlprMsg').innerHTML='Additional Detail';
					}
				})
			break;
		}
	}
	
	function clearFld(){
		$('txtAddEmpName').value='';
		$('txtAddPayGrp').value='';
		$('txtAddPayCat').value='';		
	}
	
	function maintDeductions(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,id,empName){
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		var empNo = '';
		var cntrlNo = '';
		var extraParam = '';
		var param = '';
		
		var arrEle = $('frmDeduct').serialize(true);
		
		if(action == 'addHdrDtlMid' || action == 'UpthdrAddDtl'){//addHdrDtlMid //UpthdrAddDtl
			
			
			if(arrEle['refNo'] == ''){
				alert('Reference Number is Required');
				$('newDeduc').focus();
				return false;
			}
			if(arrEle['cmbTrnType'] == 0){
				alert('Transaction Type is Required');
				$('cmbTrnType').focus();
				return false;		
			}
			if(arrEle['dedRem'] == ""){
				alert('Remarks is Required');
				$('dedRem').focus();
				return false;		
			}
			if(arrEle['cmbPeriod'] == 0){
				alert('Period is Required');
				$('cmbPeriod').focus();
				return false;		
			}
			//middle add button
			if(action == 'addHdrDtlMid' || action == 'UpthdrAddDtl'){
				if(arrEle['txtAddEmpNo'] == ''){
					alert('Employee is Required');
					$('txtAddEmpNo').focus();
					return false;
				}	
				if(arrEle['txtAddEmpName'] == "" || arrEle['txtAddPayGrp'] == "" || arrEle['txtAddPayCat'] == ""){
					alert('Employee Information is Required');
					$('txtAddEmpNo').focus();
					return false;				
				}	
				if(arrEle['txtAddAmnt'] == '' || arrEle['txtAddAmnt'] == 0){
					alert('Amount is Required');
					$('txtAddAmnt').focus();
					return false;
				}
				if(!arrEle['txtAddAmnt'].match(numericExpWdec)){
					alert('Invalid Amount\nvalid : Numbers Only with two(2) decimal or without decimal');
					$('txtAddAmnt').focus();
					return false;					
				}		
			
			}
		}//end addHdrDtlMid //UpthdrAddDtl
		
		if(action == 'deleDedDtl'){//deleDedDtl

			empNo = $('txtEmpNo'+id).innerHTML;
			cntrlNo = $('txtCntrlNo'+id).innerHTML;
			
			extraParam = '&empNo='+empNo+'&cntrlNo='+cntrlNo;
			
			deleDedDtl = confirm('Are you sure do you want to delete ?\nEmployee : '+ empName);
			if(deleDedDtl == false){
				return false;
			}

		}//end deleDedDtl
		
		if(action == 'deleDeduc'){
			deleEarn = confirm('Do You Want to Delete Dedcutions Entry #'+arrEle['refNo']);
			if(deleEarn == false){
				return false;
			}
		}
		
		new Ajax.Request(URL+"?action="+action+extraParam,{
			asynchronous : true ,
			parameters :$('frmDeduct').serialize(),
			method : 'get',
			onComplete : function (req){
				var resTxt = parseInt(req.responseText);
				
				switch (action){
					
					case 'addHdrDtlMid'://
						switch (resTxt){
							case 1:
								alert('Reference Number Already Exist...');
							break;
							case 2:
								alert('Deductions Successfully Saved');
							break;	
							case 3:
								alert('Deductions Header Saving Saved');
							break;	
							case 4:
								alert(' Deductions Detail Saving Saved');
							break;		
						}
					break;
					case 'UpthdrAddDtl':
						switch (resTxt){
							case 1:
								alert('Employee Already Exist In Deduction Detail...');
								$('txtAddEmpNo').value='';
								$('txtAddEmpNo').focus();
								$('indicator2').src='../../../images/done.gif';
								clearFld();
								return false;
							break;
							case 2:
								alert('Deductions Successfully Saved');
							break;	
							case 3:
								alert('Deductions Header Saving Saved');
							break;	
							case 4:
								alert(' Deductions Detail Saving Saved');
							break;		
						}
					break;
					case 'deleDedDtl':
							eval(req.responseText);
							//continue;
					break;
					case 'deleDeduc':
							//continue;
							eval(req.responseText);
					break;
					case 'updtHdr':
							//continue;
							eval(req.responseText);
					break;
				}
				pager('deductionsAjaxResult.php',ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra+"&refNo="+arrEle['refNo'],'../../../images/');
			},
			onCreate : function (){
				$('indicator2').src="../../../images/wait.gif";
			},
			onSuccess : function (){
				$('indicator2').innerHTML='';
			}
		});
	}
	
	function focusHandelr(act){
		switch(act){
			case 'addHdrDtlMid':
				$('txtAddEmpNo').focus();
			break;
			case 'editRef':
				$('txtAddEmpNo').focus();
			break;
			case 'UpthdrAddDtl':
				$('txtAddEmpNo').focus();
			break;
			case 'deleDedDtl':
				$('txtAddEmpNo').focus();
			break;
		}
	}
</SCRIPT>

