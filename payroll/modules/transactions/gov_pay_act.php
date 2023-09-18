<?
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("gov_pay.obj.php");

	$govPayObj = new govPayObj($_GET);
	$sessionVars = $govPayObj->getSeesionVars();
	$govPayObj->validateSessions('','MODULES');
	
	$arrcompName = $govPayObj->companyName();
	$compname	= $arrcompName["compName"];
	
	//echo $agencyCd."==".$w_pd_year."==".$w_pd_month."==".$w_or_no;
	
	if($_GET['act']=='EditGovPay')
	{
		/*Get Data*/
		$arrDetails = $govPayObj->getDetails($_GET['seqId']);
		$arr_datePaid = ($arrDetails['datePaid']!='') ? date('m/d/Y',strtotime($arrDetails['datePaid'])):"";
		$arr_remType = $arrDetails['agencyCd'];
		$arr_payPd = $arrDetails['pdMonth'];
		$arr_pdYear = $arrDetails['pdYear'];
		$arr_totAmtPaid = $arrDetails['totAmtPaid'];
		$arr_OrNo = $arrDetails['orNo'];
		$arr_bnkName = $arrDetails['bnkName'];
		$arr_BnkBrnch = $arrDetails['bnkBrnch'];
		$arr_BnkAdd = $arrDetails['bnkAdd'];
		$arr_PaidBy = $arrDetails['paidBy'];
		$arr_Remarks = $arrDetails['remarks'];
	}
	
	switch ($_GET['action']){
		case 'ADD':
			if($govPayObj->checkGovPayment($_GET['pdYear'],$_GET['pdMonth'],$_GET['orNo']) > 0)
			{
				echo "alert('Government Payment Already Exist.');";
			}
			else
			{
				/*Insert the Data*/
				if($govPayObj->addGovPayment($_GET['agencyCd'],$_GET['pdYear'],$_GET['pdMonth'],$_GET['bnkName'],$_GET['orNo'],$_GET['bnkBranch'],$_GET['bnkAdd'],$_GET['totAmtPaid'],$_GET['remarks'],$_GET['paidBy'],$_GET['datePaid']) == true){
					echo "alert('Government Payment Saved.');";
				}
				else{
					echo "alert('Government Payment Failed to Saved.');";
				}
			}
			
			exit();
		break;
		
		case 'EDIT':
			if($govPayObj->checkGovPayment_Update($_GET['agencyCd'],$_GET['pdYear'],$_GET['pdMonth'],$_GET['orNo'],'record') > 0)
			{
				/*Check Again the Data by getting its seq Id*/
				$arrseqId = $govPayObj->checkGovPayment_Update($_GET['agencyCd'],$_GET['pdYear'],$_GET['pdMonth'],$_GET['orNo'],'array');
				
				if($_GET['seqId']==$arrseqId["seqId"])
				{
					/*Update Data*/
					if($govPayObj->UpdateGovPayment($_GET['agencyCd'],$_GET['pdYear'],$_GET['pdMonth'],$_GET['bnkName'],$_GET['orNo'],$_GET['bnkBranch'],$_GET['bnkAdd'],$_GET['totAmtPaid'],$_GET['remarks'],$_GET['paidBy'],$_GET['seqId'],$_GET['datePaid'])>0)
					{
						echo "alert('Government Payment has been Updated.');";
						exit();
					}else{
						echo "alert('Government Payment Failed to Update.');";
						exit();
					}
				}
				else
				{
					echo "alert('Government Payment already exists.');";
					exit();
				}
			
			}
			else
			{
					/*Update Data*/
					if($govPayObj->UpdateGovPayment($_GET['agencyCd'],$_GET['pdYear'],$_GET['pdMonth'],$_GET['bnkName'],$_GET['orNo'],$_GET['bnkBranch'],$_GET['bnkAdd'],$_GET['totAmtPaid'],$_GET['remarks'],$_GET['paidBy'],$_GET['seqId'],$_GET['datePaid'])>0)
					{
						echo "alert('Government Payment has been Updated.');";
						exit();
					}else{
						echo "alert('Government Payment Failed to Update.');";
						exit();
					}
			}
		break;
	
	}
?>

<HTML>
<head>
<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->

</head>
	<BODY>
	<form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="frmgov" id="frmgov">
    <input type="hidden" name="w_seqId" value="<?php echo $_GET["seqId"]; ?>">
      <TABLE border="0" width="100%" height="95%" cellpadding="1" cellspacing="1" class="childGrid" >
        <tr>
            <td class="gridDtlLbl" align="left" width="30%">
                Company Name
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal" width="70%">
                <?php echo  $compname; ?>
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Remittance Type
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <?
					$arrGovAgencies = $govPayObj->makeArr($govPayObj->listGovAgencies(),'agencyCd','agencyDesc','');
                    $govPayObj->DropDownMenu($arrGovAgencies,'remType',$arr_remType,'style=width:150px; class="inputs"');
				?>
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Month
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
               
                <?php
					$arrPayPd = $govPayObj->makeArr($govPayObj->getAllPeriod(),'pdNumber','perMonth',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
                  	$govPayObj->DropDownMenu($arrPayPd,'payPd',$arr_payPd,'style=width:150px; class="inputs"');
				?>
                
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Year
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                 <?php
					$arrPayPd = $govPayObj->makeArr($govPayObj->getAllpdYear(),'pdYear','pdYear',''); // $module = 0 = 1st period, $modulo = 1 = 2nd period, $modulo = "" = both
                  	$govPayObj->DropDownMenu($arrPayPd,'pdYear',$arr_pdYear,'style=width:150px; class="inputs"');
				?>
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Total Amount Paid
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txttotAmtPaid" id="txttotAmtPaid" class="inputs" value="<?=$arr_totAmtPaid;?>">
            </td>
        </tr>

		<tr>
            <td class="gridDtlLbl" align="left" >
                Or. No.
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txtOrNo" id="txtOrNo" class="inputs" value="<?=$arr_OrNo;?>">
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Bank Name /<br> Gov. Agency
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txtBnkName" id="txtBnkName" class="inputs" style="width:250px;" value="<?=$arr_bnkName;?>">
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Bank Branch 
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txtBnkBrnch" id="txtBnkBrnch" class="inputs" style="width:250px;" value="<?=$arr_BnkBrnch;?>">
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Bank Address
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txtBnkAdd" id="txtBnkAdd" class="inputs" style="width:250px;" value="<?=$arr_BnkAdd;?>">
            </td>
        </tr>
        
        <tr>
            <td class="gridDtlLbl" align="left" >
                Paid By
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txtPaidBy" id="txtPaidBy" class="inputs" style="width:250px;" value="<?=$arr_PaidBy;?>">
            </td>
        </tr>
        
        <tr>
          <td class="gridDtlLbl" align="left" >Date Paid</td>
          <td class="gridDtlLbl" align="center">&nbsp;</td>
          <td class="gridDtlVal"><INPUT value="<?=$arr_datePaid?>" type='text' class='inputs' name='datePaid' id='datePaid' maxLength='10' readonly size="10"><a href="#"><img name="imgdatePaid" id="imgdatePaid" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
        </tr>
        <tr>
            <td class="gridDtlLbl" align="left" >
                Remarks
            </td>
            <td width="1%" class="gridDtlLbl" align="center">:</td>
            <td class="gridDtlVal">
                <INPUT type="text" name="txtRemarks" id="txtRemarks" class="inputs" style="width:250px;" value="<?=$arr_Remarks;?>">
            </td>
        </tr>
        
        <tr>
            <td align="center" class="childGridFooter" colspan="3">
                <?
					if($_GET['act']=='AddGovPay')
                    	$btnMaint = 'ADD';
					else
						$btnMaint = 'EDIT';
                ?>
                <INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateGovPay();">
                <input type='button' value='RESET' class='inputs' onClick="reset_page_add();">
            </td>
        </tr>
	</TABLE>
    </form>
    </BODY>
</HTML>
<script>
	function validateGovPay(){

		var govPay= $('frmgov').serialize(true);
		var numericExpWdec = /^([\d]+|[\d]+\.[\d]{1,2})$/;
		
		if(govPay['remType'] == 0){
			alert('Remittance Type is Required.');
			$('remType').focus();
			return false;			
		}
		
		if(govPay['payPd'] == 0){
			alert('Month is Required.');
			$('payPd').focus();
			return false;			
		}
		
		if(govPay['pdYear'] == 0){
			alert('Year is Required.');
			$('pdYear').focus();
			return false;			
		}
		
		if(govPay['txttotAmtPaid'] == ""){
			alert('Total Amount Paid is Required.');
			$('txttotAmtPaid').focus();
			return false;			
		}
		
		if(!govPay['txttotAmtPaid'].match(numericExpWdec)){
			alert('Invalid Total Amount Paid\nvalid : Numbers Only with two(2) decimal or without decimal.');
			$('txttotAmtPaid').focus();
			return false;			
		}
		if(govPay['txttotAmtPaid']==0){
			alert('Total Amount Paid should not be 0.');
			$('txttotAmtPaid').focus();
			return false;			
		}
		
		if(govPay['txtOrNo'] == ""){
			alert('Or. No. is Required.');
			$('txtOrNo').focus();
			return false;			
		}
		
		if(govPay['txtBnkName'] == ""){
			alert('Bank Name / Gov. Agency is Required.');
			$('txtBnkName').focus();
			return false;			
		}
		
		if(govPay['txtPaidBy'] == ""){
			alert('Paid By is Required.');
			$('txtPaidBy').focus();
			return false;			
		}
		
		if(govPay['datePaid'] == ""){
			alert('Date Paid is Required.');
			$('datePaid').focus();
			return false;			
		}	
	
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+govPay['btnMaint']+'&agencyCd='+govPay['remType']+'&pdYear='+govPay['pdYear']+'&pdMonth='+govPay['payPd']+'&orNo='+govPay['txtOrNo']+'&bnkName='+govPay['txtBnkName']+'&bnkBranch='+govPay['txtBnkBrnch']+'&bnkAdd='+govPay['txtBnkAdd']+'&totAmtPaid='+govPay['txttotAmtPaid']+'&remarks='+govPay['txtRemarks']+'&paidBy='+govPay['txtPaidBy']+'&seqId='+govPay['w_seqId']+'&datePaid='+govPay['datePaid'],{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);	
			}
		});	
	}
	
	function reset_page_add()
	{
		var a = $('frmgov').serialize();
		var c = $('frmgov').serialize(true);
		b = a.split('&');
		
		for(i=0;i<parseInt(b.length)-1;i++){
			d = b[i].split("=");
			document.frmgov[d[0]].value='';
		}
	}
</script>

<script>
	Calendar.setup({
			  inputField  : "datePaid",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgdatePaid"       // ID of the button
		}
	)	
</script>