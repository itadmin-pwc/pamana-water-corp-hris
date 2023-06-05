<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	04/12/2011
		Function		:	User Branch Access
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
	
	
	$arrChkUserBranch = $moduleAccssRghts->chkUserBranch($_GET["empNo"]);
	if(count($arrChkUserBranch)>=1)
		$arrChkUserBranch = $arrChkUserBranch;
	else
		$arrChkUserBranch = "";
	
	
	
	function confirmCheckedBranch($arr_UserBranch,$val)
	{
		if($arr_UserBranch!="")
		{
			foreach($arr_UserBranch as $arr_UserBranch_val)
				if($val==$arr_UserBranch_val["brnCode"]) return 'checked';
		}
	}

	
	if($_GET["btnUserDef"]!="")
	{
		$cnt = 0;
		$usrAcc = $_GET["accesspop"];
		if($usrAcc!="")
		{
			$insQryBranchdel = "Delete from tblTK_UserBranch where compCode='".$_SESSION["company_code"]."' and empNo='".$_GET["empNo"]."';";
			$moduleAccssRghts->execQry($insQryBranchdel);
			foreach($usrAcc as $usrAcc_val)
			{
				$insQryBranch= "Insert into tblTK_UserBranch(compCode, empNo, brnCode) values('".$_SESSION["company_code"]."', '".$_GET["empNo"]."','".$usrAcc_val."');";
				$moduleAccssRghts->execQry($insQryBranch);
				$cnt=1;
			}
			
		}
		else
		{
			$cnt = 0;
		}
		
		if($cnt!=0)
		{
			//$execQryIns = $moduleAccssRghts->execQry($insQryBranch);
			//if($execQryIns)
				echo "<script>alert('User Branch Access already updated.');</script>";
		}
		else{
				echo "<script>alert('No Update to User Branch Access.');</script>";
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
    
    	<form name="moduleAccess" id="moduleAccess" action="<?=$_SERVER['PHP_SELF']?>" method="get">
        <input type="hidden" name="empNo" value="<?php echo $_GET["empNo"]; ?>">
        
    	<?php
			echo "<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>\n";
					echo "<tr>";
						echo "<td align='center' colspan='3' class='prevEmpHeader'>".$_GET["empNo"]." - ".$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."."</td>";
					echo "</tr>";
					
					echo "<tr>";
						echo "<td class='gridDtlLbl'>User Branch Access</td>";								
					echo "</tr>";
					
					//$arrBranch = $moduleAccssRghts->getBrnchArt();
					$arrBranch = $moduleAccssRghts->getBranchByCompGrp(" and tnaTag='Y' ");
					foreach($arrBranch as $arrBranch_val)
					{
						echo "<tr>";
							echo "<td  class='gridDtlVal'><input type='checkbox' name='accesspop[]' id='accesspop[]' value='".$arrBranch_val["brnCode"]."' ".confirmCheckedBranch($arrChkUserBranch,trim($arrBranch_val["brnCode"])).">".$arrBranch_val["brnDesc"]."</td>";								
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



	

