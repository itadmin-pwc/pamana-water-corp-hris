<?php
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("profile_content6.obj.php");
	$mainContent6Obj = new  mainContent6();
	$empNo = $_SESSION['strprofile'];
	
	$sessionVars = mainContent6::getSeesionVars();
	$getSession = new mainContent6($_GET,$sessionVars);
	$getSession->validateSessions('','MODULES');

?>

<table align="center"  cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
	<tr>
    	<td  class="parentGridDtl" height="200" valign="top">
			<? if ($_SESSION['profile_act']!="View") { ?>
        	<table border="0" width="100%">
            	<tr>
					<?php
                         echo "<td align='right' >
                                    <img style='cursor:pointer;' onclick=\"mainContent6('Add','0')\" class='toolbarImg' src='../../../images/application_form_add.png' title='Add Previous Employer'>
                                    <img style='cursor:pointer;' onclick=\"printContent6()\" class='toolbarImg' src='../../../images/printer.png' title='Print Previous Employer Information'>
                              &nbsp;
							  </td>";
                    ?>
                 </tr>
            </table>
            <? } ?>
            <div style="height: 250px; width:99%; overflow: auto;">
                <table border="0" width="100%" cellpadding="0" cellspacing="1" class='tblPrevEmp'>
                	<tr>
                    	<td class='gridDtlLbl' width='10%' align='center' >YEAR</td>
                        <td class='gridDtlLbl' width='20%' align='center' >PREVIOUS EMPLOYER</td>
                        <td class='gridDtlLbl' width='23%' align='center' >ADDRESS</td>
                        <td class='gridDtlLbl' width='13%' align='center' >EMPLOYER <br> TIN NO.</td>
                        <td class='gridDtlLbl' width='10%' align='center' >GROSS <br> TAXABLE</td>
                        <td class='gridDtlLbl' width='10%' align='center' >TAX WITHELD</td>
                        <td class='gridDtlLbl' width='10%' align='center' >13TH MONTH <br> TAXABLE</td>
                        <? if ($_SESSION['profile_act']!="View") { ?>	
                        <td class='gridDtlLbl' width='7%' align='center' >ACTION</td>
                        <? } ?>
                    </tr>
                    
                    <?php	
						$getArr = $mainContent6Obj->getPrevEmpContent($empNo,$sessionVars['compCode']);
						
						if(sizeof($getArr)>0)
						{
							foreach($getArr as $getArr_val)
							{
								echo "<tr class='rowDtlEmplyrLst' style='height:15px;'>";
									echo "<td class='gridDtlVal'>".$getArr_val["yearCd"]."</td>";
									echo "<td class='gridDtlVal'>".strtoupper($getArr_val["prevEmplr"])."</td>";
									echo "<td class='gridDtlVal'>".strtoupper($getArr_val["empAddr1"])."</td>";
									echo "<td class='gridDtlVal'>".strtoupper($getArr_val["emplrTin"])."</td>";
									echo "<td align='right' class='gridDtlVal'>".strtoupper(number_format($getArr_val["prevTaxes"],2))."</td>";
									echo "<td align='right' class='gridDtlVal'>".strtoupper(number_format($getArr_val["prevEarnings"],2))."</td>";
									echo "<td align='right' class='gridDtlVal'>".strtoupper(number_format($getArr_val["tax13th"],2))."</td>";
									if ($_SESSION['profile_act']!="View") {
								   		echo "<td align='center'>
										<img onclick=\"mainContent6('Edit','".$getArr_val["seqNo"]."')\" src='../../../images/application_form_edit.png' width='15' height='15' title='Edit Previous Employer Content'>
										<img onclick=\"delePrevEmplr('".$getArr_val["seqNo"]."')\" src='../../../images/application_form_delete.png' width='15' height='15' title='Delete Previous Employer'>
									</td>";	
									}
								echo "</tr>";	
							}
						}
						else
						{
							echo "<tr class='rowDtlEmplyrLst'>";
								echo "<td colspan='8' align='center' class='gridDtlVal'><font  class='prevEmpZeroMsg'>NOTHING TO DISPLAY</td>";
							echo "</tr>";
						}
                    ?>
                </table>
            </div>
           
        </td>
    </tr>	
    
    		
    
</table>