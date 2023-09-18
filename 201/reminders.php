<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created	: 	04/07/2011
	Description		;	List of Reminders for User
*/
session_start();
include("../includes/db.inc.php");
include("../includes/common.php");
include("../includes/pager.inc.php");
include("reminders_obj.php");

$remObj = new remObj();
$sessionVars = $remObj->getSeesionVars();
$remObj->validateSessions('','MODULES');

$arrListReminders = $remObj->listReminders();

//Get Other Reminders
$arrgetOtherListofReminders = $remObj->otherQryRem();
$arrgetOtherListofReminders =  substr($arrgetOtherListofReminders,0,strlen($arrgetOtherListofReminders) - 1);
$arrExpRem = explode("+", $arrgetOtherListofReminders);
?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('style/payroll.css');</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
		<FORM name="frmMaintEmpAllow" id="frmMaintEmpAllow" action="<?=$_SERVER['PHP_SELF']?>" method="post">
			<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
                <tr>
                	<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../images/icon-small-warning.png"> LIST OF REMINDERS</td>
                </tr>
                
                <tr>
                	<td class="parentGridDtl">
                		<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                			<tr style="height:25px;">
                				<td width="20%" class="gridDtlLbl" align="center">DATE</td>
								<td width="80%" class="gridDtlLbl" align="center">DESCRIPTION</td>
							</tr>
                            
                            <?php
								foreach($arrListReminders as $arrListReminders_val)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
								
									echo "<tr style='height:20px;' bgcolor='".$bgcolor."' '".$on_mouse."'>";
										echo "<td align='center' valign='top' class='gridDtlVal'><font color='red'>".date("m/d/Y", strtotime($arrListReminders_val["remDate"]))."</td>";
										echo "<td align='left' valign='top' class='gridDtlVal'>".strtoupper($arrListReminders_val["remDesc"])."</td>";
									echo "</tr>";
								}
								
								if($arrExpRem>0)
								{
									foreach($arrExpRem as $arrExpRem_val)
									{
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
										$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
									
											echo "<tr style='height:30px;'  bgcolor='".$bgcolor."' '".$on_mouse."'>";
												echo "<td align='center' valign='top' class='gridDtlVal'><font color='red'>".date("m/d/Y")."</td>";
												echo "<td align='left' valign='top' class='gridDtlVal'>".strtoupper($arrExpRem_val)."</td>";
											echo "</tr>";
									}
								}
							?>
                        </TABLE>
                    </td>
                </tr>
            </TABLE>		 
			</div>	
		</FORM>
	</BODY>
</HTML>
<SCRIPT>
	
	
</SCRIPT>