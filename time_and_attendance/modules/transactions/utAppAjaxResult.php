<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/pager.inc.php");
include("../../../includes/common.php");
include("utApp.obj.php");

$UtAppObj = new UtAppObj($_GET,$_SESSION);
$UtAppObj->validateSessions('','MODULES');

$pager = new AjaxPager(15,'../../../images/');

$getUtApp      = $UtAppObj->getUtAppDtl;
$refNo         = $getUtApp['refNo'];
$otReason      = $getUtApp['utReason'];
$empNo         = $getUtApp['empNo'];
$dateFiled     = $getUtApp['dateFiled'];
$dateUt 	   = $getUtApp['dateUt'];
$UTOut         = $getUtApp['UTOut'];
$utStat       = $getUtApp['utStat'];

	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $UtAppObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND emp.empNo<>'".$_SESSION['employee_number']."' 
						 and empbrnCode IN (Select brnCode from tblTK_UserBranch 
						 					where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblTK_UserBranch 
											where empNo='{$_SESSION['employee_number']}' 
											AND compCode='{$_SESSION['company_code']}')";
	}

	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc 
					 from tblTK_UserBranch tblUB, tblBranch as tblbrn
					 where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' 
					 	and tblbrn.compCode='".$_SESSION["company_code"]."'
						and empNo='".$_SESSION['employee_number']."'
					 order by brnDesc";
	
	$resBrnches = $UtAppObj->execQry($queryBrnches);
	$arrBrnches = $UtAppObj->getArrRes($resBrnches);
	$arrBrnch = $UtAppObj->makeArr($arrBrnches,'brnCode','brnDesc','All');





	$qryIntMaxRec = "SELECT dtl.empNo
					 FROM tblTK_utApp as dtl 
					 LEFT JOIN tblEmpMast as emp ON dtl.compCode = emp.CompCode AND dtl.empNo = emp.empNo 
					 WHERE dtl.compCode = '{$_SESSION['company_code']}' $brnCodelist";
				if($_GET['srchType'] == 0){
						$qryIntMaxRec .= "AND dtl.utStat='A'";
					}
					if($_GET['srchType'] == 1){
						$qryIntMaxRec .= "AND dtl.utStat='H'";
					}
					if($_GET['srchType'] == 2){
						$qryIntMaxRec .= "AND dtl.refNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
					}
					if($_GET['srchType'] == 3){
						$qryIntMaxRec .= "AND dtl.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
					}
					if($_GET['srchType'] == 4){
						$qryIntMaxRec .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
					}
					if ($_GET['brnCd']!=0){
						$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
					}

	$qryIntMaxRec .= "ORDER BY emp.empLastName, dtl.refNo ";
		
	$resIntMaxRec = $UtAppObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);
	
	//if(empty($intOffset)){
	//	$intOffset = 0;
	//}
	
	$qryGetUtAppDtl = "SELECT dtl.compCode, dtl.empNo, dtl.utDate, dtl.refNo, dtl.dateFiled, dtl.utReason, dtl.utTimeOut,
							dtl.utStat, dtl.seqNo, dtl.userApproved, dtl.offTimeOut, emp.empFirstName,emp.empMidName,emp.empLastName,
							TIME_TO_SEC(TIMEDIFF(dtl.offTimeOut,dtl.utTimeOut))/60 as n
					   FROM tblTK_utApp as dtl 
					   LEFT JOIN tblEmpMast as emp ON dtl.compCode = emp.CompCode AND dtl.empNo = emp.empNo
					   WHERE dtl.compCode='{$_SESSION['company_code']}'";

	if($_GET['isSearch'] == 1){
	       	if($_GET['srchType'] == 0){
	        	$qryGetUtAppDtl .= "AND utStat='A'";
	        }
	       	if($_GET['srchType'] == 1){
	        	$qryGetUtAppDtl .= "AND utStat='H'";
	        }
	        if($_GET['srchType'] == 2){
        		$qryGetUtAppDtl .= "AND dtl.refNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 3){
        		$qryGetUtAppDtl .= "AND dtl.empNo LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if($_GET['srchType'] == 4){
        		$qryGetUtAppDtl .= "AND emp.empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			if ($_GET['brnCd']!=0){
				$qryGetUtAppDtl.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
	 }

	$qryGetUtAppDtl .= "ORDER BY emp.empLastName, dtl.utDate limit $intOffset,$intLimit";
	
	$resgetUtAppDtl = $UtAppObj->execQry($qryGetUtAppDtl);
	$arrgetUtAppDtl = $UtAppObj->getArrRes($resgetUtAppDtl);

?>

<TABLE border ="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
	<tr>
		
    <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Undertime 
      Application </td>
	</tr>
	<tr>
		
		<td colspan="6" class="gridToolbar">
			&nbsp;
			
			<a href="#" tabindex="4"><img class="toolbarImg" src='../../../images/refresh.gif'  onclick="pager('utAppAjaxResult.php','utAppCont','refresh',0,0,'','','','../../../images/'); validateMod('REFRESH');" title="Refresh"></a>		
		</td>
	
	</tr>
	<tr>
		<td class="parentGridDtl" valign="top">
			<!--header-->					
			<TABLE cellpadding="1" cellspacing="1" border="0" class="hdrTable" width="100%">
				<tr>
					<td class="hdrLblRow" colspan="16">
						<FONT class="hdrLbl">Application Detail</font>
					</td>
				</tr>
				
									
          		
					
				
				<tr>
					<td class="hdrInputsLvl">
						<a href="#" onclick="empLookup('../../../includes/employee_lookup_tna.php')">Employee No.</a>
					</td>
					<td class="hdrInputsLvl">
						:
					</td>
					<td width="375" class="gridDtlVal">
						<input tabindex="9" class="inputs" type="text" name="txtAddEmpNo" id="txtAddEmpNo" onKeyDown="getEmployee(event,this.value)">
					</td>
					<td class="hdrInputsLvl" width="6">&nbsp;</td>
					<td width="51" class="hdrInputsLvl">&nbsp;</td>
					
					<td width="466" colspan="4" class="gridDtlVal">&nbsp;</td>
			    <tr>
				<td class="hdrInputsLvl" width="127" >Employee Name</td>
					<td class="hdrInputsLvl" width="47">
						:
					</td>

					<td class="gridDtlVal" colspan="4"><input class="inputs" type="text" name="txtAddEmpName" id="txtAddEmpName" size="60" value="", readonly />
				    <input tabindex="10" class="inputs" type="hidden" name="dateFiled" id="dateFiled" size="10"
							 value="<? $format="Y-m-d";
										  $strf=date($format);
										  echo("$strf");
										?>" /></td>
					</tr>
				
			</TABLE>
			
			<TABLE width="100%" cellpadding="0" cellspacing="1" border="0" class="" align="center">
				<tr height="25px">
	                <td width="20%" class="gridDtlLbl" align="center">DATE OF UNDERTIME</td>
                    <td width="18%" class="gridDtlLbl" align="center">OFFICIAL SCHEDULE</td>
                    <td width="19%" class="gridDtlLbl" align="center">TIME OF DEPARTURE</td>
					<td width="37%" class="gridDtlLbl" align="center">REASON</td>
					<td width="6%" class="gridDtlLbl" align="center">ACTION</td>
				</tr>
				
				<tr>
					<td class="gridDtlVal" align="center">
						<INPUT tabindex="11" class="inputs" type="text" name="dateUt" id="dateUt" onKeyDown="checkShift(event,this.event)", readonly>
						<img src="../../../images/cal_new.gif" onClick="displayDatePicker('dateUt', this);" style="cursor:pointer;" width="20" height="14">
					</td>
					
					<td class="gridDtlVal" align="center">
					
					
						<INPUT tabindex="12" class="inputs" type="text" name="txtSched" id="txtSched"maxlength="5" onKeyDown="javascript:return dFilter(event.keyCode, this, '##:##');">
						
					</td>
					
					<td class="gridDtlVal" align="center">
						<INPUT tabindex="13" class="inputs" type="text" name="txtUtOut"  id="txtUtOut" onKeyDown="javascript:return dFilter(event.keyCode, this, '##:##');">
					</td>
          <td class="gridDtlVal" align="center"><?
						$reasons=$UtAppObj->getTblData("tblTK_Reasons "," and stat='A' and underTime='Y'"," order by reason","sqlArres");
						$arrReasons = $UtAppObj->makeArr($reasons,'reason_id','reason','');
						$UtAppObj->DropDownMenu($arrReasons,'cmbReasons',"","class='inputs'");
					?></td>
					
					<td class="gridDtlVal" align="center">

						<?
								$action = 'addDtl'; 
						?>

						<INPUT tabindex="16"class="inputs" type="button" name="btnSaveAddDtl" id="btnSaveAddDtl" value="SAVE" onclick="maintUtApp('utApp.php','utAppCont','<?=$action?>','<?=$intOffset?>','<?=$_GET['isSearch']?>','txtSrch','cmbSrch','','')">                                                                                                                                                                                                                                                                                    

					</td>
				</tr>
				<tr>
                	<td class="hdrLblRow" colspan="8" height="15px"></td>
                </tr>
                <tr>
                   <td class="hdrLblRow" colspan="8"><FONT class="hdrLbl">Summary of Application</font></td>
          		</tr>
          </TABLE>
	
	<table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
        
	  <tr>
			<td colspan="9" class="gridToolbar">
						Search<INPUT tabindex="15" type="text" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
            In 
            <?=$UtAppObj->DropDownMenu(array('Approved','Held','Ref No.','Employee No.','Last Name'),'cmbSrch',$_GET['srchType'],'class="inputs" tabindex="16"');?>
            <input tabindex="17" class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('utAppAjaxResult.php','utAppCont','Search',0,1,'txtSrch','cmbSrch','&refNo=<?=$_GET['refNo']?>&brnCd='+document.getElementById('brnCd').value,'../../../images/')"> 
         	<FONT class="ToolBarSeparator">|</font>
			<a href="#"  id="btnEdit"onClick=""><img class="toolbarImg" id="btnUpdate"  src="../../../images/application_form_edit.png" title="Update UT Application" onclick="getSeqNo()"></a> 
			<?
            if($_SESSION['user_release']=="Y"){
            ?>                                                		
			<FONT class="ToolBarseparator">|</font>
            <a href="#" id="editEarn" tabindex="2"><img class="toolbarImg" src="../../../images/edit_prev_emp.png"  onclick="updateUtTran('updateUtTran','utAppAjaxResult.php','utAppCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch');" title="Approved Undertime Application" ></a>
            <?
			}
			?>
            <FONT class="ToolBarseparator">|</font>
            <a href="#" id="deleLeave" tabindex="3"><img class="toolbarImg" src="../../../images/application_form_delete.png" title="Delete Undertime Application" onclick="delUtAppDtl('delUtAppDtl','utAppAjaxResult.php','utAppCont',<?=$intOffset?>,'',<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"></a>
			<FONT class="ToolBarseparator">|</font>
			<?=$UtAppObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs"');?>
		 </td>
		  
        <tr> 
		  <td width="2%" class="gridDtlLbl" align="center"><input type="checkbox" name="selAll" id="selAll" VALUE="1" onclick="this.value=checkAll(this.value);"/></td>
		  <td width="8%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
          <td width="19%" class="gridDtlLbl" align="center">EMPLOYEE NAME</td>
          <td width="9%" class="gridDtlLbl" align="center">DATE OF UNDERTIME</td>
		  <td width="8%" class="gridDtlLbl" align="center">OFFICIAL SCHEDULE</td>
          <td width="8%" class="gridDtlLbl" align="center">TIME OF DEPARTURE</td>
          <td width="7%" class="gridDtlLbl" align="center">UT (HR/MIN).</td>
		  <td width="29%" class="gridDtlLbl" align="center">REASON OF UNDERTIME</td>
          <td width="6%" class="gridDtlLbl" align="center">STATUS</td>
          
        </tr>
        <?
				if(@$UtAppObj->getRecCount($resgetUtAppDtl) > 0){

					$i=0;
					$ctr=1;
					foreach (@$arrgetUtAppDtl as $utAppDtlVal){

						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';
						$f_color = ($utAppDtlVal['utStat']=='A'?"#CC3300":"");
				?>
        <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>> 
          <td class="gridDtlVal" align="center">
          	 <?php
				if(($utAppDtlVal['utStat']=='H') || (($utAppDtlVal["userApproved"]==$_SESSION['employee_number']) && ($utAppDtlVal["utStat"]=='A')))
				{
					
			?>
          		<input class="inputs" type="checkbox" name="chkseq[]" value="<?=$utAppDtlVal['seqNo']?>" id="chkseq[]" />
                
            <?php
				}
			?>
          </td>
		  <td class="gridDtlVal" ><font color="<?=$f_color?>"><?=$utAppDtlVal['empNo']?></td>
		  <td class="gridDtlVal"><font color="<?=$f_color?>"> 
            <?=$utAppDtlVal['empLastName'].", ".$utAppDtlVal['empFirstName']?>
		   </td>
			
          <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"> 
		  <? 
		  	echo date("Y-m-d", strtotime($utAppDtlVal['utDate']));
		  ?>
          </td>
		  
		  <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$utAppDtlVal['offTimeOut']?></td>
          <td class="gridDtlVal" align="center"><font color="<?=$f_color?>"><?=$utAppDtlVal['utTimeOut']?></td>
          <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
		  <? 
          if($utAppDtlVal['n']<60){
		  	echo floor($utAppDtlVal['n'])." min(s)";	  
		  }
		  else{
			$chr = floor(abs($utAppDtlVal['n'])/60);
			$chr1 = (abs($utAppDtlVal['n']) - ($chr*60));
			if($chr==1 && $chr1==0){
				echo $chr." hr";	
			}
			else if($chr>1 && $chr1==0){
				echo $chr." hrs";
			}
			else{
				if($chr1<9){
					echo $chr.":0$chr1 hr(s)";		
				}
				else{
					echo $chr.":$chr1 hr(s)";	
				}
			}
		  }
		  ?></td>
          <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
          <?
			if(is_numeric($utAppDtlVal['utReason'])){
			$utRes=$UtAppObj->getTblData("tblTK_Reasons "," and stat='A' and reason_id='".$utAppDtlVal['utReason']."'"," order by reason","sqlAssoc");
				echo $utRes['reason'];	
			}
			else{
				echo strtoupper($utAppDtlVal['utReason']);	
			}
		  ?></td>
          <td class="gridDtlVal" align="center"><font color="<?=$f_color?>">
				<?
					echo ($utAppDtlVal['utStat'] =="H"? "Held":"Approved");
				?>
				
           </td>
          
        </tr>
        <?
			$ctr++;
						}
				}
				else{
				?>
        <tr> 
          <td colspan="8" align="center"> <font class="zeroMsg">NOTHING TO DISPLAY</font> 
          </td>
        </tr>
        <?}?>
        <tr> 
          <td colspan="10" align="center" class="childGridFooter"> 
            <?
						$pager->_viewPagerButton('utAppAjaxResult.php','utAppCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&refNo='.$_GET['refNo'].'&brnCd='.$_GET['brnCd']);
			?>
          </td>
        </tr>
    
		</TABLE>
				
</TABLE>

<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<?$UtAppObj->disConnect();?>
