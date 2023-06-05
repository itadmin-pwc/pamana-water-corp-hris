<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	10/14/2009
		Function		:	Maintenance (Pop Up) for the User Defined Master
	*/
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("profile_content6.obj.php");
	
	$mainContent6Obj = new  mainContent6();
	$empNo = $_SESSION['strprofile'];
	
	$sessionVars = mainContent6::getSeesionVars();
	$getSession = new mainContent6($_GET,$sessionVars);
	$getSession->validateSessions('','MODULES');

	if($_SESSION['profile_act']=='Add')
	{
		unset($_SESSION['oldcompCode']);
	}
	
	if($_GET["act"] == 'Add')
	{
		$txtYear = date("Y");
	}
	
	if($_GET['btnCont6'] == 'Add')
	{
		$chkEmployerTin =  $mainContent6Obj->chkTinEmployer($_GET["txtTinNo"],$empNo,$_SESSION['oldcompCode']);
		if($chkEmployerTin>0)
		{
			echo "alert('Employer TIN No. already Exists.');";
		}
		else
		{
			$ins_Con6 =  $mainContent6Obj->insIntoPrevEmplr($empNo, $_SESSION['oldcompCode']);
			if($ins_Con6 == true){
				echo "alert('Successfully Saved.');";
			}
			else{
				echo "alert('Saving Failed.');";
			}
		}
		exit();
	}
	
	
	if($_GET["act"]=='Edit')
	{
		$getPrevEmplrContent = $mainContent6Obj->getPrevEmplrContent($_GET["seqNo"]);
		
		if($mainContent6Obj->getRecCount($getPrevEmplrContent)>=1)
		{
			$rowgetPrevEmplrContent =  $mainContent6Obj->getSqlAssoc($getPrevEmplrContent);
			$txtYear = $rowgetPrevEmplrContent["yearCd"];
			$txtPrevEmplr = $rowgetPrevEmplrContent["prevEmplr"];
			$txtAddr1 = $rowgetPrevEmplrContent["empAddr1"];
			$txtAddr2 = $rowgetPrevEmplrContent["empAddr2"];
			$txtAddr3 = $rowgetPrevEmplrContent["empAddr3"];
			$txtTinNo = $rowgetPrevEmplrContent["emplrTin"];
			$txtGrossTax = $rowgetPrevEmplrContent["prevEarnings"];
			$txtGrossNTax = $rowgetPrevEmplrContent["grossNonTax"];
			$txt13thTax = $rowgetPrevEmplrContent["tax13th"];
			$txt13thNTax = $rowgetPrevEmplrContent["nonTax13th"];
			$txtWith = $rowgetPrevEmplrContent["prevTaxes"];
			$txtMandatory = $rowgetPrevEmplrContent["nonTaxSss"];
		}
	}
	
	
	if($_GET['btnCont6'] == 'Edit')
	{
		$chkEmployerTin =  $mainContent6Obj->chkTinEmployer($_GET["txtTinNo"],$empNo,$_SESSION['oldcompCode']);
		if($chkEmployerTin>0)
		{
			$chkAgainTin =  $mainContent6Obj->chkAgainTin($_GET["txtTinNo"],$empNo,$_GET["seqNo"],$_SESSION['oldcompCode']);
			if($chkAgainTin==0)
			{
				$chkEmployerTin =  $mainContent6Obj->chkTinEmployer($_GET["txtTinNo"],$empNo,$_SESSION['oldcompCode']);
				if($chkEmployerTin>0)
				{
					echo "alert('Employer TIN No. already Exists.');";
				}
			}
			else
			{
				$updatePrevEmplr =  $mainContent6Obj->updatePrevEmplr($_GET["seqNo"]);
				if($updatePrevEmplr == true){
					echo "alert('Successfully Saved.');";
				}
				else{
					echo "alert('Saving Failed.');";
				}
			}
		}
		else
		{
			$updatePrevEmplr =  $mainContent6Obj->updatePrevEmplr($_GET["seqNo"]);
			if($updatePrevEmplr == true){
				echo "alert('Successfully Saved.');";
			}
			else{
				echo "alert('Saving Failed.');";
			}
		}
		exit();
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
    	<form name="popContent6" id="popContent6" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        
        	<?php
				echo "<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>\n";
					echo "<tr>";
						$empInfo = $mainContent6Obj->getUserInfo($sessionVars['compCode'],$empNo,'');
						$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."," : '';
						echo "<td align='center' colspan='3' class='prevEmpHeader'>".$empInfo['empNo'] . " - " . $empInfo['empFirstName'] . " " . $midName . " " . $empInfo['empLastName']."</td>";
					echo "</tr>";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Year</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txtYear' id='txtYear' maxlength='4' value=".$txtYear." ></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Previous Employer</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><textarea name='txtPrevEmpr' id='txtPrevEmpr' class='inputs'  style='width:95%;' cols='19' rows='2'>".$txtPrevEmplr."</textarea></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Address 1</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><textarea name='txtAdd1' id='txtAdd1' class='inputs'  style='width:95%;' cols='19' rows='2'>".$txtAddr1."</textarea></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Address 2</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><textarea name='txtAdd2' id='txtAdd2' class='inputs'  style='width:95%;' cols='19' rows='2'>".$txtAddr2."</textarea></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Address 3</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><textarea name='txtAdd3' id='txtAdd3' class='inputs'  style='width:95%;' cols='19' rows='2'>".$txtAddr3."</textarea></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Employer TIN No.</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txtTinNo' id='txtTinNo' maxlength='9' value=".$txtTinNo."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Gross Taxable</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txtGrossTax' id='txtGrossTax' value=".$txtGrossTax."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Gross Non - Taxable</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txtGrossNonTax' id='txtGrossNonTax' value=".$txtGrossNTax."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>13th Month Taxable</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txt13thTax' id='txt13thTax' value=".$txt13thTax."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>13th Month Non - Taxable</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txt13thNonTax' id='txt13thNonTax' value=".$txt13thNTax."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>Tax Withheld</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txtWith' id='txtWith' value=".$txtWith."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
							echo "<td width='40%' class='gridDtlLbl' align='left'>SSS, Pag Ibig, Philhealth</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td  width='59%' class='gridDtlVal'><input type='text' class='inputs' name='txtMandatory' id='txtMandatory' value=".$txtMandatory."></td>\n";
					echo "</tr>\n";
					
					echo "<tr>";
						echo "<td align='center' class='childGridFooter' colspan='3'>";
							echo "<input type='button' class= 'inputs' name='btnCont6' value='".$_GET["act"]."' onClick=\"validation();\">";
							echo "<input type='button' value='Reset' class='inputs' onClick='reset_page_add();'>";
						echo "</td>";
					echo "</tr>";
				echo "</table>";
			?>
            <input type="hidden" name="seqNo" value="<?php echo $_GET["seqNo"];?>">
        </form>
    </BODY>
</HTML>

<script>
	function validation()
	{
		
		var numericExp = /[0-9]+/;
		//var tinExp     = /^[0-9]{3,3}[0-9]{3,3}[0-9]{3,3}$/;
		var YearExp = /^[0-9]{4,4}$/;
		var frmpopContent6 = $('popContent6').serialize(true);
		
		if(!frmpopContent6["txtYear"].match(YearExp))
		{
			alert('Invalid Year\nvalid : 2009');
			$('txtYear').focus();
			return false;
		}	
		
		if(frmpopContent6["txtPrevEmpr"]=="")
		{
			alert('Previous Employer is required.');
			$('txtPrevEmpr').focus();
			return false;
		}
		
		if(frmpopContent6["txtAdd1"]=="")
		{
			alert('Address is required.');
			$('txtAdd1').focus();
			return false;
		}
		
		if(frmpopContent6["txtTinNo"]=="")
		{
			alert('Employer Tin No. is required.');
			$('txtTinNo').focus();
			return false;
		}
		
		if(isNaN(frmpopContent6["txtTinNo"]))
		{
			alert('Invalid Tin No.: Numbers Only.');
			$('txtTinNo').focus();
			return false;
		}
		
		
		
		
		if(frmpopContent6["txtGrossTax"]=="")
		{
			alert('Gross Taxable is required.');
			$('txtGrossTax').focus();
			return false;
		}
		
		if(!frmpopContent6["txtGrossTax"].match(numericExp))
		{
			alert('Invalid Gross Taxable: Numbers Only.');
			$('txtGrossTax').focus();
			return false;
		}	
		
		if(frmpopContent6["txtGrossNonTax"]=="")
		{
			alert('Gross Non Taxable is required.');
			$('txtGrossNonTax').focus();
			return false;
		}
		
		if(!frmpopContent6["txtGrossNonTax"].match(numericExp))
		{
			alert('Invalid Gross Non Taxable: Numbers Only.');
			$('txtGrossNonTax').focus();
			return false;
		}	
		
		if(frmpopContent6["txt13thTax"]=="")
		{
			alert('13th Month Taxable is required.');
			$('txt13thTax').focus();
			return false;
		}
		
		if(!frmpopContent6["txt13thTax"].match(numericExp))
		{
			alert('Invalid 13th Month Taxable: Numbers Only.');
			$('txt13thTax').focus();
			return false;
		}	
		
		if(frmpopContent6["txt13thNonTax"]=="")
		{
			alert('13th Month Non Taxable is required.');
			$('txt13thNonTax').focus();
			return false;
		}
		
		if(!frmpopContent6["txt13thNonTax"].match(numericExp))
		{
			alert('Invalid 13th Month Non Taxable: Numbers Only.');
			$('txt13thNonTax').focus();
			return false;
		}	
		
		if(frmpopContent6["txtWith"]=="")
		{
			alert('Tax Withheld is required.');
			$('txtWith').focus();
			return false;
		}
		
		if(!frmpopContent6["txtWith"].match(numericExp))
		{
			alert('Invalid Tax Withheld: Numbers Only.');
			$('txtWith').focus();
			return false;
		}	
		
		if(frmpopContent6["txtMandatory"]=="")
		{
			alert('SSS, Pag - Ibig and Philhealth is required.');
			$('txtMandatory').focus();
			return false;
		}
		
		if(!frmpopContent6["txtMandatory"].match(numericExp))
		{
			alert('Invalid SSS, Pag - Ibig and Philhealth: Numbers Only.');
			$('txtMandatory').focus();
			return false;
		}
		
		new Ajax.Request('<?php $_SESSION['PHP_SELF'];?>',{
			method : 'get',
			parameters : frmpopContent6,
			onComplete : function(req){
				eval(req.responseText);
			}
		});
	}
	
	function reset_page_add()
	{
		var a = $('popContent6').serialize();
		var c = $('popContent6').serialize(true);
		b = a.split('&');
		
		for(i=0;i<parseInt(b.length)-2;i++){
			d = b[i].split("=");
			document.popContent6[d[0]].value='';
		}
	}
</script>
