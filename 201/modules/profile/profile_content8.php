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
	//if ($_SESSION['profile_act']!="View") {
		$cntUserDef = $mainUserDefObjObj->getNotInc($empNo);
		if(sizeof($cntUserDef)!=0)
		{
			$sizes="423px";
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
								echo "<input type='button' class='inputs' value='ADD' onclick=\"maintUserDefMast(document.getElementById('cmbCatType'),'Add',document.getElementById('cmbCatType').value,'none','".$empNo."',1)\">";
							echo "</td>";	
						echo "</tr>";
					echo "</table>";
				echo "</td>";
			echo "</tr>";
		}
		else{
			$sizes="423px";
			echo "<tr>";
				echo "<td class='parentGridDtl'>";
					echo "<table border='0' cellpadding='0' width=\"100%\">";
						echo "<tr>";
							echo '<td><div align="center"><FONT class="zeroMsg">NOTHING TO DISPLAY</font></div></td>';
						echo "</tr>";
					echo "</table>";
				echo "</td>";
			echo "</tr>";
			
			}
	?>
    <tr> 
        <td align="left" class="parentGridDtl" height="200" valign="top"><div style="height: <?=$sizes;?>; width:99%; overflow: auto;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              
         <?  //empNo='{$empNo}' and
                  $resContent=$mainUserDefObjObj->lookUpTables(" where tblEducationalBackground.catCode='1' and tblEducationalBackground.empNo='" .$empNo."'","1");
                       if($mainUserDefObjObj->getRecCount($resContent) > 0)
                       {
                           $rowContent = $mainUserDefObjObj->getArrRes($resContent);
                           foreach($rowContent as $rowContent_val=>$values)
                           {
       	 ?>
            <table border='0' width='100%' cellpadding='0' cellspacing='0'>
              <tr class='hdrInputsLvl' style='height:25px;'>
                <td align='center' width='5%'><img id='imgUsrInfo$values[catCode]' src='../../../images/grid.png'/></td>
                <td  width='78%'><b>
                  <? echo strtoupper($values['catDesc']);?>
                </b></td>
                <td align='center' width='10%'>
				<?
                 //if ($_SESSION['profile_act']!="View") {
				?>
                  <img style='cursor:pointer;' onclick="maintUserDefMast('<?=$values['catDesc']?>','Add','<?=$values['catCode']?>','none','<? $empNo?>',0)" class='toolbarImg' src='../../../images/application_form_add.png' title='Add  <?=$values['catDesc']?>  Information to Selected Employee' />
                <? 
				// }
				?>
                                </td>
              </tr>
              <tr id='usrInfo$values[catCode]' style='display:none;'></tr>
              <tr>
                <td colspan='4'><div id='divUsrInfo$values[catCode]'>
                  <table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>
                    <tr>
                      <td class='gridDtlLbl' width='16%' align='center' >SCHOOL TYPE</td>
                      <td width='41%' class='gridDtlLbl' align='center'>SCHOOL NAME</td>
                      <td width='15%' class='gridDtlLbl' align='center'>LICENSE No.</td>
                      <td width='22%' class='gridDtlLbl' align='center'>LICENSE NAME</td>
                      <?        
                                                //}
												//if ($_SESSION['profile_act']!="View") {  
											?>
                    </tr>
                    <?
                  $resTableContent=$mainUserDefObjObj->lookUpTablesData(" where tblEducationalBackground.empNo='".$values['empNo']."'","1");
                       if($mainUserDefObjObj->getRecCount($resTableContent) > 0)
                       {
                           $rowTableContent = $mainUserDefObjObj->getArrRes($resTableContent);
                           foreach($rowTableContent as $rowContent_val=>$value)
                           {	
					?>
                    <tr class='rowDtlEmplyrLst'>
                      <td class='gridDtlVal'><font class="gridDtlLblTxt"><? echo strtoupper($value['schooltype'])?></td>
                      <td class='gridDtlVal'><? echo strtoupper($value['typeDesc']);?></td>
                      <td class='gridDtlVal'><? echo strtoupper($value['licenseNumber']);?></td>
                      <td class='gridDtlVal'><? echo strtoupper($value['licenseName']);?></td>
                      <td width="6%" align='center'><img onclick="maintUserDefMast('<?=$values['catDesc']?>','Edit','<?=$value['catCode']?>','<?=$value['educationalBackgroundId']?>','<?=$empNo?>',0)" src='../../../images/application_form_edit.png' width='15' height='15' title='Edit <?=$values['catDesc'] . "/" . $value['catCode']?> Information' /> <img onclick="deleUserDefMst('<?=$value['educationalBackgroundId']?>','<?=$value['catCode']?>')" src='../../../images/application_form_delete.png' width='15' height='15' title='Delete <?=$values['typeDesc'];?> Information' /></td>
                    </tr>
                      <? 
						   }
					   }
					   ?>                    
                  </table>
                </div></td>
              </tr>
              <br />
            </table>
          <?
					}
			   }
			   else{
		  ?>  
          <tr><td><table border='0' width='100%' cellpadding='0' cellspacing='0'>
              <tr class='hdrInputsLvl' style='height:25px;'>
                <td align='center' width='5%'><img id='imgUsrInfo$values[catCode]' src='../../../images/grid.png'/></td>
                <td  width='78%'><b>EDUCATIONAL BACKGROUND/LICENSES</b></td>
                <td align='center' width='10%'>
                </td>
              </tr>
              <tr id='usrInfo$values[catCode]' style='display:none;'></tr>
              <tr>
                <td colspan='4'><div id='divUsrInfo$values[catCode]'>
                  <table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>
                    <tr>
                      <td class='gridDtlLbl' width='16%' align='center' >SCHOOL TYPE</td>
                      <td width='41%' class='gridDtlLbl' align='center'>SCHOOL NAME</td>
                      <td width='15%' class='gridDtlLbl' align='center'>LICENSE No.</td>
                      <td width='22%' class='gridDtlLbl' align='center'>LICENSE NAME</td>
                    </tr>
                    <tr class='rowDtlEmplyrLst'>
                      <td colspan="5" class='gridDtlVal' align="center">NO EDUCATIONAL BACKGROUND/LICENSES</td>
                      </tr>
                  </table>
                </div></td>
              </tr>
              <br />
            </table></td>
          
          </tr>  
          <?
			   }
				   //Disciplinary Action
                  $resdisciplinary=$mainUserDefObjObj->lookUpTables(" where tblDisciplinaryAction.empNo='".$empNo."'","7");
                       if($mainUserDefObjObj->getRecCount($resdisciplinary) > 0)
                       {
                           $rowdisciplinary = $mainUserDefObjObj->getArrRes($resdisciplinary);
                           foreach($rowdisciplinary as $rowdisciplinary_val=>$disciplinary)
                           {				   				   
		  ?>
          <tr>
            <td><table border='0' width='100%' cellpadding='0' cellspacing='0'>
              <tr class='hdrInputsLvl' style='height:25px;'>
                <td align='center' width='5%'><img id='imgUsrInfo$values[catCode]6' src='../../../images/grid.png'/></td>
                <td  width='78%'><b> <? echo strtoupper($disciplinary['catDesc']);?></b></td>
                <td align='center' width='10%'>
				<?
               //if ($_SESSION['profile_act']!="View") {
				?>
                  <img style='cursor:pointer;' onclick="maintUserDefMast('<?=$disciplinary['catDesc']?>','Add','<?=$disciplinary['catCode']?>','none','<? $empNo?>',0)" class='toolbarImg' src='../../../images/application_form_add.png' title='Add  <?=$disciplinary['catDesc']?>  Information to Selected Employee' />
                <? 
			   //}
				?>
                </td>
              </tr>
              <tr id='usrInfo$rowUserDef_val[catCode]10' style='display:none;'></tr>
              <tr>
                <td colspan='4'>
                  <table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>
                    <tr>
                      <td class='gridDtlLbl' width='11%' align='center' >DATE COMMITTED</td>
                      <td width='11%' class='gridDtlLbl' align='center'>DATE SERVE</td>
                      <td width='45%' class='gridDtlLbl' align='center'>VIOLATION</td>
                      <td width='14%' class='gridDtlLbl' align='center'>OFFENSE</td>
                      <td width='13%' class='gridDtlLbl' align='center'>SANCTION</td>
                      <?        
                                                //}
												//if ($_SESSION['profile_act']!="View") {  
											?>
                    </tr>
                    <?
                  $resTableContentDisciplinary=$mainUserDefObjObj->lookUpTablesData(" where tblDisciplinaryAction.empNo='".$disciplinary['empNo']."'","7");
                       if($mainUserDefObjObj->getRecCount($resTableContentDisciplinary) > 0)
                       {
                           $rowTableContentDisciplinary = $mainUserDefObjObj->getArrRes($resTableContentDisciplinary);
                           foreach($rowTableContentDisciplinary as $rowContentDisciplinary_val=>$disciplinaryContent)
                           {	
					?>
                    <tr class='rowDtlEmplyrLst'>
                      <td class='gridDtlVal'><? echo $mainUserDefObjObj->valDateArt($disciplinaryContent['date_commit']);?></td>
                      <td class='gridDtlVal'><? echo $mainUserDefObjObj->valDateArt($disciplinaryContent['date_serve']);?></td>
                      <td class='gridDtlVal'><? echo $disciplinaryContent['violation'];?></td>
                      <td class='gridDtlVal' align="center">
					  <? 
					  $arroff=array('','FIRST OFFENSE','SECOND OFFENSE','THIRD OFFENSE','FOURTH OFFENSE','FIFTH OFFENSE','SIXTH OFFENSE');
					  foreach($arroff as $offense=>$offdata){
						  if($disciplinaryContent['offense']==$offense){
							  echo $offdata;
							  }
						  }
					  ?></td>
                      <td class='gridDtlVal' align="center">
					  <? 
					  $arrsanc=array('','WRITTEN WARNING','1 DAY SUSPENSION','3 DAYS SUSPENSION','ONE WEEK SUSPENSION','TWO WEEKS SUSPENSION','30 DAYS SUSPENSION','DISMISSAL');
					  foreach($arrsanc as $sanction=>$sancdata){
						  if($disciplinaryContent['sanction']==$sanction){
							  echo $sancdata;
							  }
						  }
					  ?></td>
                      <td width="6%" align='center'><img onclick="maintUserDefMast('<?=$disciplinaryContent['catDesc']?>','Edit','<?=$disciplinaryContent['catCode']?>','<?=$disciplinaryContent['da_Id'];?>','<?=$empNo?>',0)" src='../../../images/application_form_edit.png' width='15' height='15' title='Edit <?=$disciplinaryContent['catDesc'];?> Information' /> <img onclick="deleUserDefMst('<?=$disciplinaryContent['da_Id']?>','<?=$disciplinaryContent['catCode']?>')" src='../../../images/application_form_delete.png' width='15' height='15' title='Delete <?=$disciplinaryContent['typeDesc'];?> Information' /></td>
                    </tr>
                    <? 
						   }
					   }
					   ?>
                  </table></td>
              </tr>
              <br />
            </table></td>
          </tr>
          <?
					}
			   }
			   else{
		  ?>
          <tr>
          	<td><table border='0' width='100%' cellpadding='0' cellspacing='0'>
              <tr class='hdrInputsLvl' style='height:25px;'>
                <td align='center' width='5%'><img id='imgUsrInfo$values[catCode]6' src='../../../images/grid.png'/></td>
                <td  width='78%'><b> DISCIPLINARY ACTION/CONDUCT</b></td>
                <td align='center' width='10%'>
                </td>
              </tr>
              <tr id='usrInfo$rowUserDef_val[catCode]10' style='display:none;'></tr>
              <tr>
                <td colspan='4'>
                  <table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>
                    <tr>
                      <td class='gridDtlLbl' width='11%' align='center' >DATE COMMITTED</td>
                      <td width='11%' class='gridDtlLbl' align='center'>DATE SERVE</td>
                      <td width='45%' class='gridDtlLbl' align='center'>VIOLATION</td>
                      <td width='14%' class='gridDtlLbl' align='center'>OFFENSE</td>
                      <td width='13%' class='gridDtlLbl' align='center'>SANCTION</td>
                    </tr>
                    <tr class='rowDtlEmplyrLst'>
                      <td colspan="6" class='gridDtlVal' align="center">NO DISCIPLINARY ACTION</td>
                    </tr>
                  </table></td>
              </tr>
              <br />
            </table></td>
          </tr>
          <?
			   }
				   //Employment data
                  $resemp=$mainUserDefObjObj->lookUpTables(" where tblEmployeeDataHistory.empNo='".$empNo."'","9");
                       if($mainUserDefObjObj->getRecCount($resemp) > 0)
                       {
                           $rowemp = $mainUserDefObjObj->getArrRes($resemp);
                           foreach($rowemp as $rowemp_val=>$emp)
                           {				   				   

		  ?>
          <tr>
            <td><table border='0' width='100%' cellpadding='0' cellspacing='0'>
              <tr class='hdrInputsLvl' style='height:25px;'>
                <td align='center' width='5%'><img id='imgUsrInfo$values[catCode]7' src='../../../images/grid.png'/></td>
                <td  width='78%'><b> <? echo strtoupper($emp['catDesc']);?></b></td>
                <td align='center' width='10%'>
				<?
                //if ($_SESSION['profile_act']!="View") {
				?>
                  <img style='cursor:pointer;' onclick="maintUserDefMast('<?=$emp['catDesc']?>','Add','<?=$emp['catCode']?>','none','<? $empNo?>',0)" class='toolbarImg' src='../../../images/application_form_add.png' title='Add  <?=$emp['catDesc']?>  Information to Selected Employee' />
                <? 
				//}
				?>
                </td>
              </tr>
              <tr id='usrInfo$rowUserDef_val[catCode]8' style='display:none;'></tr>
              <tr>
                <td colspan='4'>
                  <table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>
                    <tr>
                      <td class='gridDtlLbl' width='30%' align='center' >COMPANY</td>
                      <td width='34%' class='gridDtlLbl' align='center'>POSITION TITLE</td>
                      <td width='13%' class='gridDtlLbl' align='center'>DATE STARTED</td>
                      <td width='13%' class='gridDtlLbl' align='center'>DATE FINISHED</td>
                      <?        
                                                //}
												//if ($_SESSION['profile_act']!="View") {  
											?>
                    </tr>
                    <?
                  $resTableContentEmp=$mainUserDefObjObj->lookUpTablesData(" where tblEmployeeDataHistory.empNo='".$emp['empNo']."'","9");
                       if($mainUserDefObjObj->getRecCount($resTableContentEmp) > 0)
                       {
                           $rowTableContentEmp = $mainUserDefObjObj->getArrRes($resTableContentEmp);
                           foreach($rowTableContentEmp as $rowContentEmp_val=>$empContent)
                           {	
					?>
                    <tr class='rowDtlEmplyrLst'>
                      <td class='gridDtlVal'><font class="gridDtlLblTxt"><? echo $empContent['companyName'];?></td>
                      <td class='gridDtlVal'><? echo strtoupper($empContent['employeePosition'])?></td>
                      <td class='gridDtlVal'><? echo substr($empContent['startDate'],0,-7);?></td>
                      <td class='gridDtlVal'><? echo substr($empContent['endDate'],0,-7);?></td>
                      <td width="10%" align='center'><img onclick="maintUserDefMast('<?=$empContent['catDesc']?>','Edit','<?=$empContent['catCode']?>','<?=$empContent['employeeDataId'];?>','<?=$empNo?>',0)" src='../../../images/application_form_edit.png' width='15' height='15' title='Edit <?=$empContent['catDesc'];?> Information' /> <img onclick="deleUserDefMst('<?=$empContent['employeeDataId']?>','<?=$empContent['catCode']?>')" src='../../../images/application_form_delete.png' width='15' height='15' title='Delete <?=$empContent['typeDesc'];?> Information' /></td>
                    </tr>
                    <? 
						   }   
					   }
					   ?>
                  </table></td>
              </tr>
              <br />
            </table></td>
          </tr>
          <?
						}
				   }
				   else
				   {

		  ?>
          <tr>
          	<td><table border='0' width='100%' cellpadding='0' cellspacing='0'>
              <tr class='hdrInputsLvl' style='height:25px;'>
                <td align='center' width='5%'><img id='imgUsrInfo$values[catCode]7' src='../../../images/grid.png'/></td>
                <td  width='78%'><b>EMPLOYMENT BACKGROUND</b></td>
                <td align='center' width='10%'>
                </td>
              </tr>
              <tr id='usrInfo$rowUserDef_val[catCode]8' style='display:none;'></tr>
              <tr>
                <td colspan='4'>
                  <table  border='0' width='100%'  cellpadding='0' cellspacing='1' class='tblPrevEmp'>
                    <tr>
                      <td class='gridDtlLbl' width='30%' align='center' >COMPANY</td>
                      <td width='34%' class='gridDtlLbl' align='center'>POSITION TITLE</td>
                      <td width='13%' class='gridDtlLbl' align='center'>DATE STARTED</td>
                      <td width='13%' class='gridDtlLbl' align='center'>DATE FINISHED</td>
                    </tr>
                    <tr class='rowDtlEmplyrLst'>
                      <td colspan="5" class='gridDtlVal' align="center">NO PREVIOUS EMPLOYER</td>
                    </tr>
                  </table></td>
              </tr>
              <br />
            </table></td>
          </tr>
                   <? 
					}   
				   ?>          
        </table>
        </td>
    </tr>
</table>
