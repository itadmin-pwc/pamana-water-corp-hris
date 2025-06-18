<?
header('Content-Type: text/html; charset=iso-8859-1');

session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$common = new commonObj();
if ($_SESSION['user_level'] == 3) {
	$userinfo = $common->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
	$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		   $brnCodelist = " AND empNo<>'".$_SESSION['employee_number']."' and empbrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
}
elseif ($_SESSION['user_level'] == 2) {
	$brnCodelist = " AND empbrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
}

$array_userPayCat = explode(',', $_SESSION['user_payCat']);

if(in_array(9,$array_userPayCat))
{
	$where_empStat = " AND empStat<>'USER'";
}
else
{
	$where_empStat = " AND empStat NOT IN('RS','IN','TR','USER')";
}


//alejocode viewing of CONFI PAF for PAYROLL DEPT ONLY
//create user for confi access
//change $_SESSION['employee_number']!='**********' to the users id number 
if($_SESSION['employee_number']!='999999999' && $_SESSION['Confiaccess'] != "Y"){
	//$user_payCat_view = " AND empPayCat IN (1,2,3,9)";
	$user_payCat_view = " AND empPayCat IN (1,3,9)";
}else{
	$user_payCat_view = " AND empPayCat ='2'";
	//$user_payCat_view = " AND empPayCat <> 'A' AND empPayCat IN (1,2,3,9)";
}
//alejocode viewing of CONFI PAF for PAYROLL DEPT ONLY

//access all for admin
if($_SESSION['user_level'] == 1) {
	$user_payCat_view = " AND empPayCat IN (1,2,3,9)";
}

$pager = new AjaxPager(20,'../../../images/');

$sessionVars = $common->getSeesionVars();

//variable declaration 
$preEmplyrVal =0;
$srchType = 0;

$arrSrch = array('LAST NAME','FIRST NAME','EMPLOYEE NUMBER');

if($brnCode_View ==""){
	$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblUserBranch tblUB, tblBranch as tblbrn
						where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
						and empNo='".$_SESSION['employee_number']."'
						order by brnDesc";
	
	$resBrnches = $common->execQry($queryBrnches);
	$arrBrnches = $common->getArrRes($resBrnches);
	$arrBrnch = $common->makeArr($arrBrnches,'brnCode','brnDesc','All');
}

$qryIntMaxRec = "SELECT * FROM tblEmpMast 
			     WHERE compCode = '{$sessionVars['compCode']}' 
				 $brnCodelist $where_empStat
				 and empPayCat<>0 $user_payCat_view";
				
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
        		$qryIntMaxRec .= "AND empNo LIKE '{$_GET['txtSrch']}%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryIntMaxRec .= "AND empLastName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryIntMaxRec .= "AND empFirstName LIKE '".str_replace("'","''",$_GET['txtSrch'])."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryIntMaxRec.= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
		
$resIntMaxRec = $common->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryEmpList = "SELECT * FROM tblEmpMast
		WHERE compCode= '{$sessionVars['compCode']}'
		and empPayCat<>0 $where_empStat $user_payCat_view "; 
        if($_GET['isSearch'] == 1){
        	if($_GET['srchType'] == 2){
        		$qryEmpList .= "AND empNo LIKE '".trim($_GET['txtSrch'])."%' ";
        	}
        	if($_GET['srchType'] == 0){
        		$qryEmpList .= "AND empLastName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
        	if($_GET['srchType'] == 1){
        		$qryEmpList .= "AND empFirstName LIKE '".str_replace("'","''",trim($_GET['txtSrch']))."%' ";
        	}
			
			if ($_GET['brnCd']!=0) 
			{
				$qryEmpList .= " AND empbrnCode='".$_GET["brnCd"]."' ";
			}
        }
$qryEmpList .=	" $brnCodelist ORDER BY empStat, empbrnCode, empLastName limit $intOffset,$intLimit";

$resEmpList = $common->execQry($qryEmpList);
$arrEmpList = $common->getArrRes($resEmpList);
$payGrp = $common->getProcGrp();
?>
<div class="niftyCorner">
	<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
		<tr>
			<td colspan="4" class="parentGridHdr">
				&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Master List / Personal Profile
			</td>
		</tr>
		<tr>
			<td class="parentGridDtl" valign="top">
			
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<td colspan="8" class="gridToolbar">
                         <FONT class="ToolBarseparator">&nbsp;</font>
						<?
					/*	if($_GET['action']=='Search'){
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}*/
						if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
							
							if(isset($_GET['srchType']) ){ 
								$srchType = $_GET['srchType'];
							}
						}
							
							
					
						?>
						Search<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">In<?=$common->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
						<?php if($brnCode_View==""){echo  "Branch |";}?> <? if($brnCode_View ==""){
							$branchDesc = $common->DropDownMenu(str_replace("�","N",$arrBrnch),'brnCd',$_GET['brnCd'],'class="inputs"');
							echo $brnDes =  $branchDesc;
							}?>
						<INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH" onClick="pager('profile_list_ajax.php','empMastCont','Search',0,1,'txtSrch','cmbSrch','&brnCd='+document.getElementById('brnCd').value,'','../../../images/')">
					
                    </td>
					<tr>
						<td width="2%" class="gridDtlLbl" align="center">#</td>
						<td width="11%" class="gridDtlLbl" align="center">EMPLOYEE NO.</td>
						<td width="33%" class="gridDtlLbl" align="center">NAME</td>
						<td width="35%" class="gridDtlLbl" align="center">BRANCH</td>
                        <td width="10%" class="gridDtlLbl" align="center">STATUS</td>
						<td width="9%" align="center" class="gridDtlLbl">ACTION</td>
					</tr>
					<?
					if($common->getRecCount($resEmpList) > 0){
						$i=0;
						foreach ($arrEmpList as $empListVal){
							
						$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
						$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
						. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
					?>
					<tr  bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>>
						<td class="gridDtlVal"><?=$i?></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empNo']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt"><?=$empListVal['empLastName']. ", " . $empListVal['empFirstName'] ." ". $empListVal['empMidName']?></font></td>
						<td class="gridDtlVal"><font class="gridDtlLblTxt">
						  <?
						  $brnch['brnDesc'] = $common->getInfoBranch($empListVal['empBrnCode'],$empListVal['compCode']);
						  echo $branch = str_replace("�","&Ntilde;",$brnch['brnDesc']);
						  ?>
						</font></td>
						<td class="gridDtlVal" align="center"><font class="gridDtlLblTxt"><?
						$status = $common->execQry("Select * from tblSeparatedEmployees where empNo='".$empListVal['empNo']."'");
						$resStatus = $common->getSqlAssoc($status);
						if($empListVal['empStat']=='RS' && $empListVal['dateResigned']!=""){
							if($resStatus['natureCode']!=""){
								if($resStatus['natureCode']==1){
									echo "AWOL";		
								}	
								elseif($resStatus['natureCode']==3 || $resStatus['natureCode']==6){
									echo "RESIGNED";	
								}
								elseif($resStatus['natureCode']==5){
									echo "TERMINATED";	
								}
							}
							else{
								echo "RESIGNED";		
							}
						}
						elseif($empListVal['empStat']=='IN'){
							echo "TRANSFERRED";
						}
						elseif($empListVal['empStat']=='AWOL'){
							echo "AWOL";
						}
						elseif($empListVal['empStat']=='TR'){
							echo "TERMINATED";
						}
						elseif($empListVal['empStat']=="RS" && $empListVal['endDate']!="" && $empListVal['dateResigned']=="" ){
							echo "EOC";
						}
						elseif($empListVal['empStat']=='RG'){
							echo "ACTIVE";
						}

						?></font></td>
                        
                        
                        <td class="gridDtlVal" >
							<table border="0" width="70%" align="center">
                            	<tr align="center" >
                                	<td>
                                    	<a href="#" onClick="location.href='profile.php?act=View&empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_magnify.png" title="View Employee Information"></a>                                    </td>
                                	<?php 
									//echo $empListVal['empPayGrp'] . '==' . $payGrp;
										if ($_SESSION['employee_number'] == '123') {
								   ?>
                                    <td>
                                    	<a href="#" onClick="location.href='profile.php?act=Edit&empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_edit.png" title="Edit Employee Information" /></a>
                                    </td>                   
                                  <? }
								  	if ($payGrp == $empListVal['empPayGrp']) 
									{
								   ?>
                                	 <td class="gridDtlVal" align="left">
                                            <!--<a href="#" onClick="location.href='profile_actionlist.php?empNo=<?=$empListVal['empNo']?>&compCode=<?=$empListVal['compCode']?>'"><img class="toolbarImg" src="../../../images/application_form_add.png" title="PAF"></a> -->                                   
                                      		 <a href="#" onClick="checkifRes(<?php echo "'".$empListVal['empNo']."','".$empListVal['empStat']."','".$empListVal['compCode']."'";?>);"><img class="toolbarImg" src="../../../images/application_form_add.png" title="PAF"></a>                                    </td>
								
                                   
									<? } else {?>
                                     <td class="gridDtlVal" align="left">
                                            <a href="#" ><img class="toolbarImg" src="../../../images/application_form_add2.png" title="PAF"></a>                                    </td>
                                    
                                    <?}?>
                                  

                            	</tr>
                            </table>
                      </td>
					</tr>
					
					<?
						}
					}
					else{
					?>
					<tr>
						<td colspan="8" align="center">
							<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
						</td>
					</tr>
					<?}?>
					<tr>
						<td colspan="8" align="center" class="childGridFooter">
							<? $pager->_viewPagerButton("profile_list_ajax.php",'empMastCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&brnCd='.$_GET["brnCd"],'');?>
						</td>
					</tr>
				</TABLE>
				
			</td>
		</tr>
	</TABLE>
</div>
<?$common->disConnect();?>
<form name="frmTS" method="post">
<input type="hidden" name="brnCd" id="brnCd" value="<?php echo $_GET['brnCd']; ?>">
</form>