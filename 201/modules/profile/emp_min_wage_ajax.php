<?
/*
	Created By		: 	Genarra Jo-Ann S. Arong
	Date Created 	:	04/06/2011
	Description		:	Mass Update of Employees that should be Minimum Wage based on minWage of tblBranch
*/
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("emp_min_wage_obj.php");

$minObj = new minWageObj($_GET,$_SESSION);
$sessionVars = $minObj->getSeesionVars();
$minObj->validateSessions('','MODULES');

$payGrp = $minObj->getProcGrp();
if($payGrp!="")
	$where = " and empPayGrp='".$payGrp."'";
else
	$where = "";
	

$arrListMinWage = $minObj->getListMinWage($where);





if(count($arrListMinWage)>0)
{	
	if($_SESSION['user_level']!=3)
		$dis_Button = "";
	else
		$dis_Button = "disabled";
}
else
	if($_SESSION['user_level']!=3)
		$dis_Button = "disabled";
	else
		$dis_Button = "";
?>

<HTML>
<head>
	<script type='text/javascript' src='movement.js'></script>
	<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
    <style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 11px;
}
-->
    </style>
</head>
	
    
<BODY onLoad="" >
	<div class="niftyCorner">
    <form action="" method="post" name="frmMinWage" id="frmMinWage">
    	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
        	<tr>
      			<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp; LIST OF EMPLOYEES TO BE TAGGED AS MINIMUM WAGE EARNER</td>
        	</tr>
        	
           <tr>
				<td class="parentGridDtl">
					<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                    	<tr>
                            <td class="gridToolbar" align="right" colspan="9">
                                 <input type="button" class="style1" name="btnUpdate" id="btnUpdate" <?=$dis_Button?>  onClick="UpdateMinWage();" value="Update Minimum Wage Tag">
                            </td>
                        </tr>
                        
                         <tr>
                            <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" onChange="CheckEmpAll();"value="1"  name="chAll" id="chAll"></td>
                            <td width="8%" class="gridDtlLbl" align="center">EMP.NO.</td>
                            <td width="15%" align="center" class="gridDtlLbl">EMPLOYEE NAME</td>
                           	<td width="20%" class="gridDtlLbl" align="center">BRANCH</td>
                            <td width="10%" class="gridDtlLbl" align="center">EMP. STATUS</td>
                            <td width="10%" class="gridDtlLbl" align="center">EMP. DAILY RATE</td>
                            <td width="10%" class="gridDtlLbl" align="center">BRANCH - MIN. WAGE</td>
                         </tr>
                         
                         <?php
						 	$q = 1;
						 	if(count($arrListMinWage)>0)
							{
								foreach($arrListMinWage as $arrListMinWage_val)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
									
									echo "<tr style='height:25px;' bgcolor='".$bgcolor."' ".$on_mouse.">";
										echo "<td height='20' align='center' class='gridDtlVal'><input type='checkbox' value='".$arrListMinWage_val["empNo"]."*".$arrListMinWage_val["minWage"]."' name='chkMinWage".$q."' id='chkMinWage".$q."'></td>";
										echo "<td height='20' align='center' class='gridDtlVal'>".$arrListMinWage_val["empNo"]."</td>";
										echo "<td height='20' class='gridDtlVal'>".htmlentities($arrListMinWage_val["empLastName"]).", ".htmlentities($arrListMinWage_val["empFirstName"])."</td>";
										echo "<td height='20' class='gridDtlVal'>".$arrListMinWage_val["brnDesc"]."</td>";
										echo "<td height='20' align = 'left' class='gridDtlVal'>".$arrListMinWage_val["empStat"]."</td>";
										echo "<td height='20' align='right' class='gridDtlVal'>".($_SESSION['user_level']!=3?$arrListMinWage_val["empDrate"]:"")."</td>";
										echo "<td height='20' align='right' class='gridDtlVal'>".$arrListMinWage_val["minWage"]."</td>";
									echo "</tr>";

									$i++;
									$q++;
								}
							}
												
						 ?>
                         <tr>
                         
                         </tr>
                         <input type="hidden" value="<?=$i;?>" name="chCtr" id="chCtr">
                    </TABLE>
                 </td>
            </tr>
            
           
      
    	</TABLE>
        </form>
	</div>
<?$minObj->disConnect();?>
</BODY>
</HTML>
