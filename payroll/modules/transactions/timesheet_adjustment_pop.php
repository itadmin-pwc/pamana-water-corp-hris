<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/16/2010
		Function		:	TimeSheet Adjustment (Pop Up) 
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("timesheet_obj.php");
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	
	$arr_empInfo = $inqTSObj->getUserInfo($_SESSION["company_code"],$_GET["empNo"],'');
	$openPeriod_Sdate = $inqTSObj->getOpenPeriod($_SESSION["company_code"],$_SESSION['pay_group'],$_SESSION['pay_category']); 
			
	if ($_GET["payPd"]=="") {
		$openPeriod = $inqTSObj->getOpenPeriod($_SESSION["company_code"],$_SESSION['pay_group'],$_SESSION['pay_category']); 
		$payPayable = $openPeriod['pdPayable'];
		$payPd = $openPeriod['pdSeries'];
	}
	
	switch($_GET["action"])
	{
		case "add":
			$btnCaption = "Save";
		break;
		
		case "Save":
			$chktblTsCorr = $inqTSObj->checktblTsCorr($_GET["empNo"], "and tsDate='".date("Y-m-d", strtotime($tsDate))."'");
			$arrPayPd = $inqTSObj->getSlctdPd($_SESSION["company_code"],$_GET["payPd"]);
			
			
			if($chktblTsCorr!=0){
				echo "alert('Transaction made already exists.');";
				exit();
			}else{
				$addTsCorr = $inqTSObj->instbltsCorr('1',$_GET["empNo"], date("Y-m-d", strtotime($_GET["tsDate"])), $_GET["payPd"], $arr_empInfo["empPayGrp"], $arr_empInfo["empPayCat"], $hrsReg, $hrsAbsent, $hrsTardy, $hrsUt, $hrsOtLe8, $hrsOtGt8, $hrsNdLe8, $hrsNdGt8, $_GET["cmbTSCorrStat"], $_GET["empHrate"]);
				echo "alert('Transaction already save.');";
				exit();
			}
		break;
		
		case "edit";
			$arrEmpTsInfo = $inqTSObj->gettblTsCorrData($_GET["empNo"], date("Y-m-d", strtotime($_GET["tsDate"])));
			$getPdSeries = $inqTSObj->getPdSeries($arrEmpTsInfo["pdYear"],$arrEmpTsInfo["pdNumber"]);
			$payPd = $getPdSeries['pdSeries'];
			$cmbTSCorrStat = $arrEmpTsInfo["tsStat"];
			$regHrs= $arrEmpTsInfo["hrsReg"];
			$regHrsAbsent= $arrEmpTsInfo["hrsAbsent"];
			$regHrsTardy= $arrEmpTsInfo["hrsTardy"];
			$regHrsUt= $arrEmpTsInfo["hrsUt"];
			$regHrsOtLe8= $arrEmpTsInfo["hrsOtLe8"];
			$regHrsOtGt8= $arrEmpTsInfo["hrsOtGt8"];
			$regHrsNdLe8= $arrEmpTsInfo["hrsNdLe8"];
			$regHrsNdGt8= $arrEmpTsInfo["hrsNdGt8"];
			$btnCaption = "Update";
		break;
		
		case "Update":
			$addTsCorr = $inqTSObj->instbltsCorr('2',$_GET["empNo"], date("Y-m-d", strtotime($_GET["tsDate"])), $_GET["payPd"], $arr_empInfo["empPayGrp"], $arr_empInfo["empPayCat"], $hrsReg, $hrsAbsent, $hrsTardy, $hrsUt, $hrsOtLe8, $hrsOtGt8, $hrsNdLe8, $hrsNdGt8, $_GET["cmbTSCorrStat"], $_GET["empHrate"]);
			echo "alert('Transaction already save.');";
			exit();
		break;
		
	
				
		default:
			
		break;
	}
?>
<HTML>

	<HEAD> 
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
	</HEAD>
	<BODY>
    
    	<form name="tsadjustment" id="tsadjustment" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <input type="hidden" name="empHRate" id="empHRate" value="<?php echo $arr_empInfo["empHrate"]; ?>">
        <input type="hidden" name="empNo" id="empNo" value="<?php echo $_GET["empNo"]; ?>">
        <input type="hidden" name="tsDayType" id="tsDayType" value="<?php echo $tsDayType; ?>">
        <input type="hidden" name="s_date_coff" id="s_date_coff" value="<?php echo date("m/d/Y", strtotime($openPeriod_Sdate["pdToDate"])); ?>">
        
    	<?php
			echo "<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>\n";
					echo "<tr>";
						echo "<td align='center' colspan='3' class='prevEmpHeader'>".$_GET["empNo"]." - ".$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."."</td>";
					echo "</tr>";
													
					echo "<tr>\n";
						echo "<td width='30%' class='gridDtlLbl' align='left'>Time Sheet Date </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='59%' class='gridDtlVal'>";
						echo '<input type="text" value="'.($_GET["tsDate"]!=""?date("m/d/Y", strtotime($_GET["tsDate"])):"").'" onChange="valDateStartEnd(this.value,this.id,document.tsadjustment.txttsDate.value);" class="inputs" name="txttsDate" id="txttsDate" maxLength="10" '.($_GET["tsDate"]!=""?"disabled":"readonly").'  size="10" />';
							
						if($_GET["tsDate"]=="")
						{
							echo '<a href="#"><img name="imgtsdate" id="imgtsdate" src="../../../images/cal_new.png" title="TimeSheet Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a>';
						}
					echo "</tr>";
					
					echo "<tr>\n";
						echo "<td width='30%' class='gridDtlLbl' align='left'>Period </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='59%' class='gridDtlVal'>";
							echo "<div id='pdPay'> ";
									$arrPayPd = $inqTSObj->makeArr($inqTSObj->getPeriodGtOpnPer($payPayable),'pdSeries','pdPayable','');
												$inqTSObj->DropDownMenu($arrPayPd,'payPd',$payPd,$payPd_dis.'class="inputs" style="width:145px;"');
								
							echo"</div>";
						echo "</td>\n";
					echo "</tr>";
					
					echo "<tr>\n";
						echo "<td width='30%' class='gridDtlLbl' align='left'>Status </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='59%' class='gridDtlVal'>";
							$inqTSObj->DropDownMenu(array('A'=>'Active','H'=>'Held'),'cmbTSCorrStat',$cmbTSCorrStat,'class="inputs" style="width:145px;"');
						echo "</td>\n";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td colspan ='3'>";
						echo "<table border='0' width='70%'>";
							echo "<tr>";
								echo "<td width='30%' class='gridDtlVal' align='right'></td>";
								echo "<td width='1%' class='gridDtlVal' align='right'></td>";
								echo "<td width='59%' class='gridDtlVal' align='right'><b>HRS.</td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>Regular</td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsReg' id='txthrsReg' style='width:100%;' value='".$regHrs."' ></td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>Absent</td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsAbs' id='txthrsAbs' style='width:100%;' value='".$regHrsAbsent."' ></td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>Tardiness</td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsTard' id='txthrsTard' style='width:100%;' value='".$regHrsTardy ."' ></td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>Undertime</td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsUt' id='txthrsUt' style='width:100%;' value='".$regHrsUt ."' ></td>";
							echo "</tr>";
							
							
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>OT < 8 </td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsOtLe8' id='txthrsOtLe8' style='width:100%;' value='".$regHrsOtLe8."' ></td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>OT > 8</td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsOtGt8' id='txthrsOtGt8' style='width:100%;' value='".$regHrsOtGt8."' ></td>";
							echo "</tr>";
							
							
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>ND < 8 </td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsNdLe8' id='txthrsNdLe8' style='width:100%;' value='".$regHrsNdLe8."' ></td>";
							echo "</tr>";
							
							echo "<tr>";
								echo "<td width='30%' class='gridDtlLbl'>ND > 8</td>";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td width='30%' class='gridDtlVal' align='right'><input type='text' class='inputs' name='txthrsNdGt8' id='txthrsNdGt8' style='width:100%;' value='".$regHrsNdGt8."'></td>";
							echo "</tr>";
							
							
							
						echo "</table>";
					echo "</td>";
				echo "</tr>";
				
					echo "<tr>";
						echo "<td align='center' class='childGridFooter' colspan='3'>";
							echo "<input type='button' class= 'inputs' name='btnUserDef' value='Save' onClick=\"valFields('".$btnCaption."');\">";
							echo "<input type='button' value='Reset' class='inputs' onClick='reset_page_add();'>";
						echo "</td>";
					echo "</tr>";
					
				echo "</table>\n";
	
		?>
        </form>
    </BODY>
</HTML>




<script>
Calendar.setup({
		  inputField  : "txttsDate",      // ID of the input field
		  ifFormat    : "%m/%d/%Y",          // the date format
		  button      : "imgtsdate"       // ID of the button
	}
)
</script>




<script>	
	function valFields(action)
	{
		var frmtsadjustment = $('tsadjustment').serialize(true);
		
		
		var parseStart = Date.parse(frmtsadjustment["s_date_coff"]);
		var parseEnd = Date.parse(frmtsadjustment["txttsDate"]);
		
	
		if(parseEnd > parseStart) {
			alert("Transaction Date must not be greater than "+frmtsadjustment["s_date_coff"]+".\n");
			$('txttsDate').focus();
			return false;
		}
		
		if(frmtsadjustment["txttsDate"]=="")
		{
			alert('Time Sheet Date is required.');
			$('txttsDate').focus();
			return false;
		}
		
		if(frmtsadjustment["payPd"]=="0")
		{
			alert('Pay Period is required.');
			$('payPd').focus();
			return false;
		}	
		
		if(isNaN(frmtsadjustment["txthrsReg"]))
		{
			alert('Invalid Hrs. Regular: Numbers Only.');
			$('txthrsReg').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsReg"]>8)
		{
			alert('Hrs. Regular should not be greater than 8.');
			$('txthrsReg').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsReg"]!="")
		{
			if((frmtsadjustment["txthrsReg"]!=4) && (frmtsadjustment["txthrsReg"]!=8))
			{
				alert('Hrs. Regular should be 4 or 8 hrs.');
				$('txthrsReg').focus();
				return false;
			}	
		}	
		
		
		if(isNaN(frmtsadjustment["txthrsAbs"]))
		{
			alert('Invalid Hrs. Absent: Numbers Only.');
			$('txthrsAbs').focus();
			return false;
		}
		
		if(frmtsadjustment["txthrsAbs"]>8)
		{
			alert('Hrs. Absent should not be greater than 8.');
			$('txthrsAbs').focus();
			return false;
		}	
		
		
		if(frmtsadjustment["txthrsAbs"]>frmtsadjustment["txthrsReg"])
		{
			if(frmtsadjustment["txthrsReg"]!="")
			{
				alert('Hrs. Absent should not be greater than Hrs. Regular.');
				$('txthrsAbs').focus();
				return false;
			}
		}	
		
		if((frmtsadjustment["txthrsReg"]==8) && (frmtsadjustment["txthrsAbs"]!=""))
		{
			alert('Hrs. Regular is already 8 Hrs. \nHrs. Absent should be 0.');
			$('txthrsAbs').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsAbs"]!="")
		{
			if((frmtsadjustment["txthrsAbs"]!=4) && (frmtsadjustment["txthrsAbs"]!=8))
			{
				alert('Hrs. Absent should be 4 or 8 hrs.');
				$('txthrsAbs').focus();
				return false;
			}	
		}	
		
		
		if(isNaN(frmtsadjustment["txthrsTard"]))
		{
			alert('Invalid Hrs. Tardiness: Numbers Only.');
			$('txthrsTard').focus();
			return false;
		}
		
		if(isNaN(frmtsadjustment["txthrsUt"]))
		{
			alert('Invalid Hrs. Undertime: Numbers Only.');
			$('txthrsUt').focus();
			return false;
		}
		
		if(isNaN(frmtsadjustment["txthrsOtLe8"]))
		{
			alert('Invalid Hrs. OT < 8: Numbers Only.');
			$('txthrsOtLe8').focus();
			return false;
		}
		
		if(frmtsadjustment["txthrsOtLe8"]>8)
		{
			alert('Hrs. OT < 8 should not be greater than 8.');
			$('txthrsOtLe8').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsOtLe8"]<0)
		{
			alert('Hrs. OT < 8 should not be less than 1.');
			$('txthrsOtLe8').focus();
			return false;
		}	
		
		if(isNaN(frmtsadjustment["txthrsOtGt8"]))
		{
			alert('Invalid Hrs. OT > 8: Numbers Only.');
			$('txthrsOtGt8').focus();
			return false;
		}
		
		if(frmtsadjustment["txthrsOtGt8"]>8)
		{
			alert('Hrs. OT > 8 should not be greater than 8.');
			$('txthrsOtGt8').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsOtGt8"]<0)
		{
			alert('Hrs. OT > 8 should not be less than 1.');
			$('txthrsOtGt8').focus();
			return false;
		}	
	
		if(isNaN(frmtsadjustment["txthrsNdLe8"]))
		{
			alert('Invalid Hrs. Nd < 8: Numbers Only.');
			$('txthrsNdLe8').focus();
			return false;
		}
		
		if(frmtsadjustment["txthrsNdLe8"]>8)
		{
			alert('Hrs. ND < 8 should not be greater than 8.');
			$('txthrsNdLe8').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsNdLe8"]<0)
		{
			alert('Hrs. ND < 8 should not be less than 1.');
			$('txthrsNdLe8').focus();
			return false;
		}	
		
		if(isNaN(frmtsadjustment["txthrsNdGt8"]))
		{
			alert('Invalid Hrs. Nd > 8: Numbers Only.');
			$('txthrsNdGt8').focus();
			return false;
		}
		
		
		if(frmtsadjustment["txthrsNdGt8"]>8)
		{
			alert('Hrs. ND > 8 should not be greater than 8.');
			$('txthrsNdGt8').focus();
			return false;
		}	
		
		if(frmtsadjustment["txthrsNdGt8"]<0)
		{
			alert('Hrs. ND > 8 should not be less than 1.');
			$('txthrsNdGt8').focus();
			return false;
		}	
		
		if((frmtsadjustment["txthrsReg"]=="") && (frmtsadjustment["txthrsAbs"]=="") && (frmtsadjustment["txthrsTard"]=="") && (frmtsadjustment["txthrsUt"]=="") && (frmtsadjustment["txthrsOtLe8"]=="") && (frmtsadjustment["txthrsOtGt8"]=="") && (frmtsadjustment["txthrsNdLe8"]=="") && (frmtsadjustment["txthrsNdGt8"]==""))
		{
			alert('No Transactions to be Made.');
			return false;
		}
		
		new Ajax.Request('<?php $_SESSION['PHP_SELF'];?>?&action='+action+'&empNo='+$('empNo').value+'&tsDate='+$('txttsDate').value+'&hrsReg='+$('txthrsReg').value+'&hrsAbsent='+$('txthrsAbs').value+'&hrsTardy='+$('txthrsTard').value+'&hrsUt='+$('txthrsUt').value+'&hrsOtLe8='+$('txthrsOtLe8').value+'&hrsOtGt8='+$('txthrsOtGt8').value+'&hrsNdLe8='+$('txthrsNdLe8').value+'&hrsNdGt8='+$('txthrsNdGt8').value+'&empHrate='+$('empHRate').value+'&payPd='+$('payPd').value+'&cmbTSCorrStat='+$('cmbTSCorrStat').value,{
			method : 'get',
			onComplete : function(req){
				eval(req.responseText);
			}
		});
	}
	
	function reset_page_add()
	{
		var a = $('tsadjustment').serialize();
		var c = $('tsadjustment').serialize(true);
		b = a.split('&');
		
		for(i=0;i<parseInt(b.length)-2;i++){
			d = b[i].split("=");
			document.tsadjustment[d[0]].value='';
		}
	}
	
	
</script>
