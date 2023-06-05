<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');

$pager = new AjaxPager(20,'../../../images/');

$arrSrch = array('Ref No');
$qryIntMaxRec = "Select * from tblLonRefNo where compCode='{$_SESSION['company_code']}'";

$resIntMaxRec = $maintEmpObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qrylonrefList = "Select  * from tblLonRefNo where  compCode = '{$_SESSION['company_code']}' ";		  
         if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 0){
        		$qrylonrefList .= "AND lonRefNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        }
$qrylonrefList .= " Limit $intOffset,$intLimit";		
$reslonrefList = $maintEmpObj->execQry($qrylonrefList);
$reslonrefList = $maintEmpObj->getArrRes($reslonrefList);
?>

<HTML>
<head>


</head>
	<BODY>
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> REF. NO</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="8" align="center" class="gridToolbar">
                              <div align="left">
                       			
                                <a href="#" onClick="PopUp('lonref_act.php?act=AddRef','ADD REF. NO.','<?=$dedListVal['recNo']?>','lonRef_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Ref. No.</a> |
                      
                                  <FONT class="ToolBarseparator">|</font>
                                  
						          <?
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search
						<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">
						In
						<?=$maintEmpObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('lonref_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','','../../../images/')">                              
                                </div></td>
					  	  </tr>
						  	<tr>
								<td width="3%" class="gridDtlLbl" align="center">#</td>
								<td width="20%" class="gridDtlLbl" align="center">REF. No.</td>
								<td width="7%" class="gridDtlLbl" align="center">ACTION</td>
							</tr>
							<?
							if(count($reslonrefList) > 0){
								$i=0;
								foreach ($reslonrefList as $emplonrefVal){
								
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center">
								  <?=$i?>
							    </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal">
							      <div align="left">
							        <?=$emplonrefVal['lonRefNo']?>
						            </div></td>
								<td bgcolor="<?php echo $bgcolor; ?>" class="gridDtlVal"><div align="center"><a href="#" onClick="PopUp('lonref_act.php?act=EditRef&id=<?=$emplonrefVal['seqNo']?>','EDIT REF. NO.','<?=$dedListVal['recNo']?>','lonref_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch')"><img src="../../../images/application_form_edit.png"  border="0" class="actionImg" title="EDIT REF. NO." /></a>&nbsp;&nbsp;<img src="../../../images/application_form_delete.png" style="cursor:pointer" onClick="DeleteRefNo(<?=$emplonrefVal['seqNo']?>);" width="16" height="16"></div></td>
								</tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="20" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
							<tr>
								<td colspan="20" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton('lonref_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
		</div>
		<?$maintEmpObj->disConnect();?>
	</BODY>
</HTML>
