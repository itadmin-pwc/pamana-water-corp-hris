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
		$usrPayCat = $arrChkUsers['userLevel'];
		if($arrChkUsers['releaseTag'] == 'Y') {
			$usrPayCat = 4;
		}
	}
	else
	{
		$usrPayCat = "";
	}
	
	if($_GET["btnUserDef"]!="")
	{
		$usrAccLevel = $_GET["accesslevel"];
		$updateAccessLevel = $moduleAccssRghts->updateUserLevel($usrAccLevel, $_GET["empNo"]);
		if($updateAccessLevel==1)
			echo "<script>alert('User Access Level Already Updated.');</script>";
		else
			echo "<script>alert('User Access Level Failed To Update.');</script>";
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
        
    	<?php if(isset($_GET["empNo"])): ?>
			<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>
				<tr>
					<td align='center' colspan='3' class='prevEmpHeader'>
						<?php echo $_GET["empNo"]; ?> - <?php echo $arr_empInfo["empLastName"]; ?>, <?php echo $arr_empInfo["empFirstName"]; ?> <?php echo $arr_empInfo["empMidName"][0] . "."; ?>
					</td>
				</tr>
				
				<tr>
					<td class='gridDtlLbl'>User Access Level</td>
				</tr>
				
				<?php
				$rsUsrExists = $moduleAccssRghts->chkUser($_SESSION["company_code"], $_SESSION['employee_number']);
				$chkUsrExists = $moduleAccssRghts->getSqlAssoc($rsUsrExists);

				if($chkUsrExists['userLevel'] == 1): ?>
					<tr>
						<td class='gridDtlVal'>
							<input type='radio' name='accesslevel' id='accesslevel' value='1' <?php echo ($usrPayCat == 1) ? 'checked' : ''; ?>>
							Super User
						</td>
					</tr>
				<?php endif; ?>

				<tr>
					<td class='gridDtlVal'>
						<input type='radio' name='accesslevel' id='accesslevel' value='2' <?php echo ($usrPayCat == 2) ? 'checked' : ''; ?>>
						User (Create / Update / Delete Record) Approver
					</td>
				</tr>
				<tr>
					<td class='gridDtlVal'>
						<input type='radio' name='accesslevel' id='accesslevel' value='4' <?php echo ($usrPayCat == 4) ? 'checked' : ''; ?>>
						User (Create / Update / Delete Record) Timekeeper
					</td>
				</tr>
				<tr>
					<td class='gridDtlVal'>
						<input type='radio' name='accesslevel' id='accesslevel' value='3' <?php echo ($usrPayCat == 3) ? 'checked' : ''; ?>>
						User (Create / Update / Delete Record) Own Record
					</td>
				</tr>
				
				<tr>
					<td align='center' class='childGridFooter' colspan='3'>
						<input type='submit' class= 'inputs' name='btnUserDef' value='Save' >
					</td>
				</tr>
			</table>
		<?php endif; ?>
    	</form>
    </BODY>
</HTML>