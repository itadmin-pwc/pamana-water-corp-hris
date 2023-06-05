<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/19/2010
		Function		:	User Access
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("module_access_right.obj.php");

	$moduleAccssRghts = new moduleAccessRightsObj($_SESSION,$_GET);
	$sessionVars = $moduleAccssRghts->getSeesionVars();
	$moduleAccssRghts->validateSessions('','MODULES');
	
	$arr_empInfo = $moduleAccssRghts->getUserInfo($_SESSION["company_code"],$_GET["empNo"],'');
	
	
	$rsChkUsers = $moduleAccssRghts->chkUser($_GET["compCode"],$_GET["empNo"]);
	if($moduleAccssRghts->getRecCount($rsChkUsers)>=1)
	{
		$arrChkUsers = $moduleAccssRghts->getSqlAssoc($rsChkUsers);
		$usrPayCat = $arrChkUsers["category"];
	}
	else
	{
		$usrPayCat = "";
	}
	
	function confirmCheckedPayCat($chk_payCat,$val)
	{
		$payCat=explode(',',$chk_payCat);
		foreach($payCat as $payCatval)
		if($val==$payCatval) return 'checked';
	}

	
	if($_GET["btnUserDef"]!="")
	{
		$usrAcc = $_GET["accesspop"];
		
		if($usrAcc!="")
		{
			foreach($usrAcc as $usrAcc_val)
			{
				$usrCat.= $usrAcc_val.",";
				
			}
			$usrPayCat = substr($usrCat,0,strlen($usrCat) - 1);
		}
		else
		{
			$usrPayCat = "NULL";
		}
		
			$upUsrAccount = $moduleAccssRghts->updateUsrCat($usrPayCat,$_GET["empNo"]);
			if($upUsrAccount==1)
				echo "<script>alert('User Password Already Updated.');</script>";
			else
				echo "<script>alert('Unsuccessful Password Updated.');</script>";
		
		
		
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
    
    	<form name="moduleAccess" id="moduleAccess" action="<?=$_SERVER['PHP_SELF']?>" method="get">
        <input type="hidden" name="empNo" value="<?php echo $_GET["empNo"]; ?>">
        
    	<?php
			echo "<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>\n";
					echo "<tr>";
						echo "<td align='center' colspan='3' class='prevEmpHeader'>".$_GET["empNo"]." - ".$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."."</td>";
					echo "</tr>";
					
					echo "<tr>";
						echo "<td class='gridDtlLbl'>User Category Access</td>";								
					echo "</tr>";
					
					$arrPayCat = $moduleAccssRghts->getPayCategory();
					foreach($arrPayCat as $arrPayCat_val)
					{
						echo "<tr>";
							echo "<td  class='gridDtlVal'><input type='checkbox' name='accesspop[]' id='accesspop[]' value='".$arrPayCat_val["payCat"]."' ".confirmCheckedPayCat($usrPayCat,trim($arrPayCat_val["payCat"])).">".$arrPayCat_val["payCatDesc"]."</td>";								
						echo "</tr>";
					}
					
				
					echo "<tr>";
						echo "<td align='center' class='childGridFooter' colspan='3'>";
							echo "<input type='button' class= 'inputs' name='chkAll' value='Check All' onClick=\"SelDeSelAllEmp(true);\">";
							echo "<input type='button' class= 'inputs' name='deSel' value='Un-Check All' onClick=\"SelDeSelAllEmp(false);\">";
							echo "<input type='submit' class= 'inputs' name='btnUserDef' value='Save' >";
						echo "</td>";
					echo "</tr>";
					
				echo "</table>\n";
	
		?>
        </form>
    </BODY>
</HTML>

<script>
	function SelDeSelAllEmp(is_sel_all)
	{
		var chkpayCat = document.moduleAccess.elements['accesspop[]'];
		
		for (var i=0; i<chkpayCat.length; i++)
			chkpayCat[i].checked = is_sel_all?true:false;
	}
	
	
	
</script>



	

