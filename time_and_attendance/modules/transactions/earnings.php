<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("earnings.obj.php");

$earningsObj = new earningsObj($_GET,$_SESSION);
$earningsObj->validateSessions('','MODULES');


if(isset($_GET['action'])){
	switch ($_GET['action']){
		case 'NEWREFNO':
				$lastEarnRefNo = $earningsObj->getEarnLastRefNo();
				if($lastEarnRefNo != false){
					
					$newEarnRefNo = $lastEarnRefNo['earnRefNo']+1;
					if($earningsObj->updateEarbLastRefNo($newEarnRefNo) == true){;
						echo "$('refNo').value=$newEarnRefNo;";				
					}
					else{
						echo "alert('Error Updating Earnings Reference Number');";
					}
				}else{
						echo "alert('Error Selecting Last Earnings Reference Number');";
				}
			exit();
		break;
		case 'getEmpInfo':
				if ($_SESSION['pay_category'] != 9) {	
					$empInfo = $earningsObj->getUserInfo($_SESSION['company_code'],$_GET['empNo'],'');
					
				} else {
					$qryEmpInfo = "SELECT tblEmpMast.*
							FROM  tblLastPayEmp INNER JOIN tblEmpMast ON tblLastPayEmp.compCode = tblEmpMast.compCode 
							AND tblLastPayEmp.empNo = tblEmpMast.empNo
							WHERE tblLastPayEmp.compCode='{$_SESSION['company_code']}'
							AND emppayGrp in (Select payGrp from tblProcGrp where status='A')
							AND tblLastPayEmp.empNo = '{$_GET['empNo']}'
							";
					$empInfo = $earningsObj->getSqlAssoc($earningsObj->execQry($qryEmpInfo));
					
				}	
				$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."." : '';
				if($empInfo['empPayGrp'] == 1){ $payGroup = '1-One';}
				if($empInfo['empPayGrp'] == 2){ $payGroup = '2-Two';}
				
				if($empInfo == 0){
					echo 0;
				}
				else{
					
					$payCat = $earningsObj->getPayCat($_SESSION['company_code'],"AND payCat = '".$empInfo['empPayCat']."'");
					echo "$('txtAddEmpName').value='".htmlspecialchars(addslashes($empInfo['empFirstName']))." $midName $empInfo[empLastName]';";
					echo "$('txtAddPayGrp').value='$payGroup';";
					echo "$('txtAddPayCat').value='$payCat[payCat]-$payCat[payCatDesc]';";
					echo "$('hdnPayCat').value='$payCat[payCat]';";
					echo "$('txtAddCntrlNo').value=0;";
					echo "$('txtAddCntrlNo').select();";
				}
				exit();
		break;
		case 'addHdrDtlMid':
				$checkHeader = $earningsObj->checkEarnHeader();
	
				if($checkHeader > -1){
	
					if($checkHeader > 0){
						echo 6;
					}
					else{
						$addHeader =  $earningsObj->addEarnHeader();
						$addDtl    =  $earningsObj->addEarnDetail(1);
						if($addHeader == true && $addDtl == true){
							echo 2;//successfuuly saved
						}
						else{
							if($addHeader == false){
								echo 3;//error saving header;
							}
							else if($addDtl == false){
								echo 4;//error saving detail
							}
							else{
								echo 5;//error saving header and detail
							}
						}
					}
				}
				else{
					echo 1;//error query if header exist;
				}
				exit();
		break;
		case 'UpthdrAddDtl':
				if($earningsObj->checkEarnDetail() > -1){
					if($earningsObj->checkEarnDetail() > 0){
						echo 1;//"alert('Employee Already Exist In Deduction Detail...');";
					}
					else {
						if($earningsObj->addEarnDetail(2) == true && $earningsObj->updateEarnHeader() == true){
							echo 2;//"alert('Additional Detail Successfully Saved')";
						}
						else {
							if($earningsObj->updateEarnHeader() == false){
								echo 3;//"alert('Deductions Header Deletion Failed')";
							}
							if($earningsObj->addEarnDetail(2) == false){
								echo 4;//"alert(' Deductions Detail Deletion Saved')";
							}				
						}
					}		
				}else{
					echo 5;//error checking detail
				}
				exit();
		break;
		case 'deleEarnDtl':
				if($earningsObj->deleEarnDtl() == true){
					echo "alert('Detail #$_GET[empNo] Successfully Deleted')";
				}
				else{
					echo "alert('Detail #$_GET[empNo] Deletion Failed')";
				}
				exit();
		break;
		case 'editRef':
				$getHeader = $earningsObj->getEarnTranHeader();
				if(!empty($getHeader)){
					$refNo = $getHeader['refNo'];
				}
				else{
					$refNo = 0;
				}
				echo $refNo;
				exit();
		break;
		case 'deleEarn':
				if($earningsObj->deleEarn() == true){
					echo "alert('Earnings Entry #$_GET[refNo] Successfully Deleted');";
				}
				else{
					echo "alert('Earnings Entry #$_GET[refNo] Deletion Failed');";
				}
				exit();
		break;
		case 'updtHdr':
				if($earningsObj->updateEarnHeader() == true){
					echo "alert('Header Successfully Updated');";
				}
				else{
					echo "alert('Header Updating Failed');";
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
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		

	</HEAD>
	<BODY>
		<FORM name='frmEarn' id="frmEarn" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div id="earningsCont"></div>
			<div id="indicator1" align="center"></div>
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	disableRightClick()
	
	function validateMod(mode){
		if(mode == 'EDITRENO'){
			$('newEarn').innerHTML="<img src='../../../images/application_add_2.png' class='toolbarImg'>";
			$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
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
		pager('earningsAjaxResult.php','earningsCont','refresh',0,0,'','','','../../../images/');  	
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);
				$('cmbTrnType').focus();
				$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>";
				$('deleEarn').innerHTML="<img src='../../../images/application_form_delete_2.png' class='toolbarImg'>";	
			},
			onCreate : function(){
				$('refNoCont').innerHTML='Loading...';
			},
			onSuccess : function(){
				$('refNoCont').innerHTML='';
			}
		});
	}
	
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
	
	function editRefNo(act,refVal,evt){
		
		var k = evt.keyCode | evt.which;
		
		param = '?action='+act+"&refNo="+refVal;
		
		if(k == 13){
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
				method : 'get',
				onComplete : function (req){
					refNo = parseInt(req.responseText);
					if(refNo != 0){
						pager('earningsAjaxResult.php','earningsCont',act,0,0,'','','&refNo='+refNo,'../../../images/');  
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
			title: "Earnings Reference Look - Up", 
			minimizable:true,
			showEffect:Effect.Appear, 
			destroyOnClose: true,
			maximizable: false,
			hideEffect: Effect.SwitchOff, 
			draggable:true })
			RefWin.setAjaxContent('reference_lookup.php?opnr=earn','','');
			//RefWin.show(true);
			RefWin.showCenter(true);
			
			//$('editEarn').innerHTML="<img src='../../../images/application_form_edit_2.png' class='toolbarImg'>" 
			//$('refLookup').disabled=true;
			
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
		Windows.getWindow('refWin').close();
		pager('earningsAjaxResult.php','earningsCont','editRef',0,0,'','','&refNo='+refNoVal,'../../../images/');  
	}	
	
	function clearFld(){
		$('txtAddEmpName').value='';
		$('txtAddPayGrp').value='';
		$('txtAddPayCat').value='';		
	}	
		
pager('earningsAjaxResult.php','earningsCont','load',0,0,'','','','../../../images/');  


function maintEarnings(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,id,empName){
	
		
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2}|-[\d]+|-[\d]+\.[\d]{1,2})$/;
		var empNo = '';
		var cntrlNo = '';
		var extraParam = '';
		var param = '';
		
		var arrEle = $('frmEarn').serialize(true);
		
		if(action == 'addHdrDtlMid' || action == 'UpthdrAddDtl' || action == 'updtHdr'){//addHdrDtlMid //UpthdrAddDtl
			
			if(arrEle['refNo'] == ''){
				alert('Reference Number is Required');
				$('newEarn').focus();
				return false;
			}
			if(arrEle['cmbTrnType'] == 0){
				alert('Transaction Type is Required');
				$('cmbTrnType').focus();
				return false;		
			}
			if(arrEle['earnRem'] == ""){
				alert('Remarks is Required');
				$('earnRem').focus();
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
		
		if(action == 'deleEarnDtl'){//deleDedDtl

			empNo = $('txtEmpNo'+id).innerHTML;
			cntrlNo = $('txtCntrlNo'+id).innerHTML;
			
			extraParam = '&empNo='+empNo+'&cntrlNo='+cntrlNo;
			
			deleDedDtl = confirm('Are you sure do you want to delete ?\nEmployee : ' +empName);
			if(deleDedDtl == false){
				return false;
			}

		}//end deleDedDtl
		
		if(action == 'deleEarn'){
			deleEarn = confirm('Do You Want to Delete Earnings Entry #'+arrEle['refNo']);
			if(deleEarn == false){
				return false;
			}
		}
		
		new Ajax.Request(URL+"?action="+action+extraParam,{
			asynchronous : true ,
			parameters :$('frmEarn').serialize(),
			method : 'get',
			onComplete : function (req){
				var resTxt = parseInt(req.responseText);

				switch (action){					
					case 'addHdrDtlMid'://
						switch (resTxt){
							case 1:
								alert('Error in Query if Header Existing');
							break;
							case 2:
								alert('Earnings Successfully Saved');
							break;	
							case 3:
								alert('Earnings Header Saving Failed');
							break;	
							case 4:
								alert('Earnings Detail Saving Failed');
							break;	
							case 5:
								alert('Earnings Header And Detail Deletion Failed');
							break;	
							case 6:
								alert('Header Alredy Exist');
							break;	
						}
					break;
					case 'UpthdrAddDtl':
						switch (resTxt){
							case 1:
								alert('Employee Already Exist In Earnings Detail...');
								$('txtAddEmpNo').value='';
								$('txtAddEmpNo').focus();
								$('indicator2').src='../../../images/done.gif';
								clearFld();
								return false;
							break;
							case 2:
								alert('Earnings Successfully Saved');
							break;	
							case 3:
								alert('Earnings Header Saving Failed');
							break;	
							case 4:
								alert('Earnings Detail Saving Failed');
							break;	
							case 5:
								alert('Error Query Checking Details');
							break;		
						}
					break;
					case 'deleDedDtl':
							//continue;
							eval(req.responseText);
					break;
					case 'deleEarn':
							//continue;
							eval(req.responseText);
					break;
					case 'updtHdr':
							//continue;
							eval(req.responseText);
					break;
				}
				pager('earningsAjaxResult.php',ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra+"&refNo="+arrEle['refNo'],'../../../images/');
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
			case 'deleEarnDtl':
				$('txtAddEmpNo').focus();
			break;
		}
	}	
	
</SCRIPT>