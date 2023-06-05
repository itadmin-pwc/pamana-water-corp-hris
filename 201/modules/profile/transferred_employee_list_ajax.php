<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("transferred.obj.php");

$transObj = new transferredObj($_GET,$_SESSION);
$sessionVars = $transObj->getSeesionVars();
$transObj->validateSessions('','MODULES');

if($payGrp!="")
	$where = " and empPayGrp<>'".$payGrp."'";
else
	$where = "";

if($_GET['action']=="delete"){
	if($transObj->delTransEmp($_GET['seqNo'])){
		echo "alert('Succcessfully deleted the selected employee.');";	
		echo "location.href = 'transferred_employee_list.php';";
	}
	else{
		echo "alert('Failed to delete the selected employee.');";
	}	
	exit();
}	

if($_GET['action']=="emptrans"){
	if($transObj->releaseTrans()){
		echo "alert('Transfer successfully completed.');";
		echo "location.href = 'transferred_employee_list.php';";
		exit();
	}
	else{
		echo "alert('Transfer failed.');";	
		exit();
	}
	exit();
}
$arrListTransEmp = $transObj->getListTransEmp();
?>

<HTML>
<head>
	<script type='text/javascript' src='movement.js'></script>
   
    <style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 12px;
}
.link {
	color:#06F;
	cursor: pointer;
	
}
-->
    </style>
</head>
	
    
<BODY onLoad="" >
	<div class="niftyCorner">
    <form action="" method="post" name="frmMinWage" id="frmMinWage">
    	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
        	<tr>
      			<td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp; LIST OF TRANSFERRED EMPLOYEES</td>
        	</tr>
        	
           <tr>
				<td class="parentGridDtl">
					<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                    	<tr>
                            <td class="gridToolbar" align="left" height="20" colspan="12">
                              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                   <tr class="style1">
                                     <td><span class="link" onClick="maintPrevEmp('Add','','');">Add Employee</span></td>
                                     <td align="right"><input type="button" name="btnTransfer" id="btnTransfer" value="Transfer" onClick="transEmployee();"></td>
                                   </tr>
                            </table></td>
                        </tr>
                        
                         <tr>
                           <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" onChange="CheckAll()"value="1"  name="chAll" id="chAll"></td>
                            <td width="9%" class="gridDtlLbl" align="center">EMP.NO.</td>
                            <td width="18%" align="center" class="gridDtlLbl">EMPLOYEE NAME</td>
                           	<td width="21%" class="gridDtlLbl" align="center">OLD BRANCH</td>
                            <td width="22%" class="gridDtlLbl" align="center">NEW BRANCH</td>
                            <td width="11%" class="gridDtlLbl" align="center">STATUS</td>
                            <td width="10%" class="gridDtlLbl" align="center">DATE TRANSFERRED</td>
                            <td width="7%" class="gridDtlLbl" align="center">&nbsp;</td>

                         </tr>
                         
                         <?php
						 	$q = 1;
							$ctr=0;
						 	if(count($arrListTransEmp)>0)
							{
								
								foreach($arrListTransEmp as $val)
								{
									$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
									$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
									echo "<tr style='height:25px;' bgcolor='".$bgcolor."' ".$on_mouse.">";
										
										echo "<td height='20' align='center' class='gridDtlVal'>";
										//if($val['status']!='T'){
										echo "<input type='checkbox' value='".$val['empNo']."' onClick='check(this.name);' name='chTran".$ctr."' id='chTran".$ctr."'>";
										//$ctr++;	
										//}
										echo "</td>";
										echo "<td height='20' align='center' class='gridDtlVal'>".$val["empNo"]."</td>";
										echo "<td height='20' class='gridDtlVal'>".htmlentities($val["empLastName"]).", ".htmlentities($val["empFirstName"])." ".substr($val["empMidName"],0,1)."."."</td>";
										echo "<td height='20' class='gridDtlVal'>".$val["branch_old"]."</td>";
										echo "<td height='20' align = 'left' class='gridDtlVal'>".$val["branch_new"]."</td>";
										echo "<td height='10' align='center' class='gridDtlVal'>".($val['status']=='T'?"TRANSFERRED":"Queued")."</td>";
										echo "<td height='20' align = 'center' class='gridDtlVal'>".$transObj->valDateArt($val["dateAdded"])."</td>";										
										if ($val['status']=='T')
											echo "<td height='10' align='center' class='gridDtlVal'></td>";
										else
											echo "<td height='10' align='center' class='gridDtlVal' title='Remove from queued list.'><span class='link' onClick=\"DeleteTransEmp('".$val['seqNo']."')\">"."Cancel"."</span></td>";
									echo "</tr>";
									$ctr++;	
									$i++;
									$q++;
								}
							}
							else{				
						 ?>
                         	<tr><td colspan="8" class="zeroMsg" align="center">Nothing to display</td>
                            </tr>
							<?
							}
							?>                         
                    </TABLE>
                 </td>
            </tr>
            <tr><input type="hidden" value="<?=$ctr;?>" name="chCtr" id="chCtr">
            </tr>
            
           
      
    	</TABLE>
        </form>
	</div>
<?$transObj->disConnect();?>
</BODY>
</HTML>
