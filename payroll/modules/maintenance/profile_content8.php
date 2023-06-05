<?php
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("profile_userdef.obj.php");
	$mainUserDefObjObj = new  mainUserDefObj();
	$empNo = $_SESSION['strprofile'];

?>
<table align="center"  cellpadding="0" cellspacing="2" border="0" class="childGrid" width="100%">
	
    <?php
	if ($_SESSION['profile_act']!="View") {
		$cntUserDef = $mainUserDefObjObj->getNotInc($empNo);
		if(sizeof($cntUserDef)!=0)
		{
			echo "<tr>";
				echo "<td class='parentGridDtl'>";
					echo "<table border='0' cellpadding='0'>";
						echo "<tr>";
							echo "<td class='hdrInputsLvl'>Type :</td>";
							echo "<td>";
							
								$mainUserDefObjObj->DropDownMenu(
											$mainUserDefObjObj->makeArr(
												$mainUserDefObjObj->getNotInc($empNo),'catCode','catDesc',''),'cmbCatType','','');
							echo "</td>";
							echo "<td>";
								echo "<input type='button' class='inputs' value='ADD' 'disabled' onclick=\"maintUserDefMast(document.getElementById('cmbCatType'),'Add',document.getElementById('cmbCatType').value,'none','".$empNo."',1)\">";
							echo "</td>";	
						echo "</tr>";
					echo "</table>";
				echo "</td>";
			echo "</tr>";
		}
	}
	?>
    
  
    <tr> 
        <td align="left" class="parentGridDtl" height="200" valign="top">
            <div style="height: 250px; width:99%; overflow: auto;">
            <?php
				
                
				$rs_getUserDefRef = $mainUserDefObjObj->getListUsrDefRef($empNo);
                $row_getUserDefRef = $mainUserDefObjObj->getArrRes($rs_getUserDefRef);
               
                echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";
                    foreach ($row_getUserDefRef as $rowUserDef_val)
                    {
                        $exp_getUserDef_ColumnName = explode("|",$mainUserDefObjObj->getUserDef_ColumnName($rowUserDef_val["catCode"]));
                        $col_label = explode(',',$exp_getUserDef_ColumnName[0]);
                        $col_field = explode(',',$exp_getUserDef_ColumnName[1]);
                        $order_by  = explode(',',$exp_getUserDef_ColumnName[2]);
                        
                        $sizeof_td = (sizeof($col_label)+1)-2;
                        
                        echo "<tr class='hdrInputsLvl' style='height:25px;'>";
                                //echo "<td align='center' width='5%'><img id='imgUsrInfo$rowUserDef_val[catCode]' src='../../../images/folder.gif' style='cursor:pointer;' onclick=\"viewUsrInfo('$rowUserDef_val[catCode]')\"></td>\n";
                                echo "<td  width='78%'><b>".strtoupper($rowUserDef_val["catDesc"])."</b></td>\n";
                                echo "<td align='center' width='10%'>";
                                if ($_SESSION['profile_act']!="View") {
									echo     "<img style='cursor:pointer;' onclick=\"maintUserDefMast('$rowUserDef_val[catDesc]','Add',$rowUserDef_val[catCode],'none','".$empNo."',0)\" class='toolbarImg' src='../../../images/application_form_add.png' title='Add ".$rowUserDef_val["catDesc"]." Information to Selected Employee'>
                                        <img style='cursor:pointer;' onclick=\"printEmpInfo()\" class='toolbarImg' src='../../../images/printer.png' title='Print Employee ".$rowUserDef_val["catDesc"]." Information'>";
                        			echo      "</td>";
									}
                        echo "</tr>\n";
                        
                        //echo "<tr id='usrInfo$rowUserDef_val[catCode]' style='display:none;'>\n";
                            echo "<tr>\n";
                            echo "<td colspan='4'>\n";
                                echo "<div id='divUsrInfo$rowUserDef_val[catCode]'>\n";
                                    
                                    $resContent = $mainUserDefObjObj->UserDefMast_Con($empNo,$exp_getUserDef_ColumnName[1],$rowUserDef_val["catCode"],$exp_getUserDef_ColumnName[2]);
                                    echo "<table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>";
                                            echo "<tr>";
                                                $col_width = (float) 90/sizeof($col_label);
                                                $col_width = $col_width."%";
                                                
                                                foreach($col_label as $col_label_val)
                                                {
                                                    echo "<td class='gridDtlLbl' width='$col_width' align='center' >".strtoupper($col_label_val)."</td>";	
                                                }
												if ($_SESSION['profile_act']!="View") {                                                
													echo "<td width='10%' class='gridDtlLbl' align='center'>ACTION</td>";
												}	
                                            echo "</tr>";	
                                            
                                            if($mainUserDefObjObj->getRecCount($resContent) > 0)
                                            {
                                                $rowContent = $mainUserDefObjObj->getArrRes($resContent);
                                                foreach($rowContent as $rowContent_val)
                                                {
                                                    echo "<tr class='rowDtlEmplyrLst'>";
                                                        foreach($col_field as $cnt_index => $cnt_value)
                                                        {
                                                            $chk_IfDate = ((($col_field[$cnt_index]=="date1")||($col_field[$cnt_index]=="date2"))?1:0); 
                                                            if($chk_IfDate=='1')
                                                            {
                                                                echo "<td class='gridDtlVal'><font class='gridDtlLblTxt'>".($rowContent_val[$col_field[$cnt_index]]!=""?date("m/d/Y", strtotime($rowContent_val[$col_field[$cnt_index]])):"")."</td>";
                                                            }
                                                            else
                                                            {
                                                                $chk_tblLookUp = $mainUserDefObjObj->tblLookUp(str_replace(" ","",$col_label[$cnt_index]),$rowContent_val[$col_field[$cnt_index]]);
                                                                echo "<td class='gridDtlVal'><font class='gridDtlLblTxt'>".($chk_tblLookUp!=""?strtoupper($chk_tblLookUp):strtoupper($rowContent_val[$col_field[$cnt_index]]))."</td>";
                                                            }
                                                        }
														if ($_SESSION['profile_act']!="View") {
                                                        echo "<td align='center'>";
                                                        	echo    "<img onclick=\"maintUserDefMast('".$rowUserDef_val["catDesc"]."','Edit','".$rowUserDef_val["catCode"]."','".$rowContent_val["recNo"]."','".$empNo."',0)\" src='../../../images/application_form_edit.png' width='15' height='15' title='Edit ".$lstEmpInfoOpt["catDesc"]." Information'>
                                                            <img onclick=\"deleUserDefMst('".$rowContent_val["recNo"]."')\" src='../../../images/application_form_delete.png' width='15' height='15' title='Delete ".$lstEmpInfoOpt["catDesc"]." Information'>";
                                                    		echo  "</td>";	
														}	
                                                    echo "</tr>";
                                                }
                                            }
                                            else
                                            {
                                                    $sizeof_colspan = sizeof($col_field)+1;
                                                    echo "<tr class='rowDtlEmplyrLst'>";
                                                        echo "<td colspan='$sizeof_colspan' align='center' class='gridDtlVal'><font  class='prevEmpZeroMsg'>NOTHING TO DISPLAY</td>";
                                                    echo "</tr>";
                                            }
                                            
                                    echo "</table>";
                                echo "</div>\n";
                            echo "</td>\n";
                        echo "</tr>\n";
						echo "<br>";
                    }
                echo "</table>";
            ?>
        </td>
    </tr>
</table>