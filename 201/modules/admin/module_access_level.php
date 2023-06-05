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
	}
	else
	{
		$usrPayCat = "";
	}
	
	
	if($_GET["btnUserDef"]!="")
	{
		$usrAccLevel = $_GET["accesslevel"];
		
			$updateAccessLevel = $moduleAccssRghts->updateUserLevel($usrAccLevel,$_GET["empNo"]);
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
        
    	<?php
			echo "<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>\n";
					echo "<tr>";
						echo "<td align='center' colspan='3' class='prevEmpHeader'>".$_GET["empNo"]." - ".$arr_empInfo["empLastName"].", ".$arr_empInfo["empFirstName"]." ".$arr_empInfo["empMidName"][0]."."."</td>";
					echo "</tr>";
					
					echo "<tr>";
						echo "<td class='gridDtlLbl'>User Access Level</td>";								
					echo "</tr>";
					
                        //Check if the Employee Already Exists in the User Table
                        $rsUsrExists = $moduleAccssRghts->chkUser($_SESSION["company_code"],$_SESSION['employee_number']);
	    				$chkUsrExists = $moduleAccssRghts->getSqlAssoc($rsUsrExists);

						if($chkUsrExists['userLevel']==1){
						echo "<tr>";	
							echo "<td  class='gridDtlVal'><input type='radio' name='accesslevel' id='accesslevel' value='1'";
									if($usrPayCat==1)
										 echo "checked";
									else
										 echo "Unchecked";
							echo ">"."Super User"."</td>";
						}
						echo "</tr>";	
						echo "<tr>";	
							echo "<td  class='gridDtlVal'><input type='radio' name='accesslevel' id='accesslevel' value='2'";
									if($usrPayCat==2)
										 echo "checked";
									else
										 echo "Unchecked";
							echo ">"."User (Add / Edit / Delete Record)"."</td>";
						echo "</tr>";
						echo "<tr>";	
							echo "<td  class='gridDtlVal'><input type='radio' name='accesslevel' id='accesslevel' value='3'";
									if($usrPayCat==3)
										 echo "checked";
									else
										 echo "Unchecked";
							echo ">"."User (View Only)"."</td>";
						echo "</tr>";
//					}
					
				
					echo "<tr>";
						echo "<td align='center' class='childGridFooter' colspan='3'>";
							echo "<input type='submit' class= 'inputs' name='btnUserDef' value='Save' >";
						echo "</td>";
					echo "</tr>";
					
				echo "</table>\n";
	
		?>
    	</form>
    </BODY>
</HTML>