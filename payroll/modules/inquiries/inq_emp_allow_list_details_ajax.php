<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("inq_emp_allow_obj.php");

$inqEmpAllowObj = new inqEmpAllowObj();
$sessionVars = $inqEmpAllowObj->getSeesionVars();
$inqEmpAllowObj->validateSessions('','MODULES');

$pager = new AjaxPager(4,'../../../images/');

$arrSrch = array('EMPLOYEE NUMBER','LAST NAME','FIRST NAME');
$empNo = $_GET['empNo'];


$allowCode = $_GET['allowCode'];
$empAllow=$inqEmpAllowObj->getEmpAllow($empNo,$allowCode);
$qryIntMaxRec = "SELECT * FROM tblAllowanceBrkDwnHst
			     WHERE compCode = '{$sessionVars['compCode']}' AND empNo = '{$empNo}' AND allowCode = '{$allowCode}' ";

$resIntMaxRec = $inqEmpAllowObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

$qryLnDtlList = "SELECT TOP $intLimit *
		FROM tblAllowanceBrkDwnHst
		WHERE allowSeries NOT IN
        (SELECT TOP $intOffset allowSeries FROM tblAllowanceBrkDwnHst WHERE compCode = '{$sessionVars['compCode']}' AND empNo = '{$empNo}' AND allowCode = '{$allowCode}' ORDER BY pdYear,pdNumber ASC) 
				AND compCode = '{$sessionVars['compCode']}' AND empNo = '{$empNo}' AND allowCode = '{$allowCode}'  ORDER BY pdYear,pdNumber ASC";


$resLnDtlList = $inqEmpAllowObj->execQry($qryLnDtlList);
$arrLnDtlList = $inqEmpAllowObj->getArrRes($resLnDtlList);

$empInfo = $inqEmpAllowObj->getUserInfo($sessionVars['compCode'],$empNo,"");

//$empAllowBal = $inqEmpAllowObj->getEmpAllowBal($sessionVars['compCode'],$empNo,$allowCode);
?>

<HTML>
<head>
	<script type='text/javascript' src='inq_emp_allow_js.js'></script>
</head>
	<BODY>
		
		<div class="niftyCorner">
			
  <TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
    <tr> 
      <td colspan="6" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"><? echo $empNo."-".$empInfo['empLastName'].", ".$empInfo['empFirstName'][0].".".$empInfo['empMidName'][0].". ::: ".$inqEmpAllowObj->getAllowDesc($sessionVars['compCode'],$allowCode)?> 
        <div id="Layer1" style="position:absolute; left:123px; top:151px; width:182px; height:67px; z-index:1; visibility: hidden;"> 
          <INPUT type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" class="inputs">
          <?=$inqEmpAllowObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
        </div></td>
    </tr>
    <tr> 
      <td colspan="1" class="parentGridHdr"> 
        <table width="100%" border="0" class="parentGrid" cellpadding="1" cellspacing="0">
          <tr> 
            <td width="20%" class="gridDtlLbl" >Start Date </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="79%" class="gridDtlLbl" ><? echo ($empAllow["allowStart"]!=""?date("m/d/Y", strtotime($empAllow["allowStart"])):"");?></td>
          </tr>
          
          <tr> 
            <td width="20%" class="gridDtlLbl" >End Date </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="79%" class="gridDtlLbl" ><? echo ($empAllow["allowEnd"]!=""?date("m/d/Y", strtotime($empAllow["allowEnd"])):"");?></td>
          </tr>
          
          <tr> 
            <td width="20%" class="gridDtlLbl" >Schedule </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <?php
				if ($empAllow['allowSked']==1) $sked ="1st";
				if ($empAllow['allowSked']==2) $sked ="2nd";
				if ($empAllow['allowSked']==3) $sked ="Both";
			?>
            <td width="79%" class="gridDtlLbl" ><? echo $sked;?></td>
          </tr>
          
          <tr> 
            <td width="20%" class="gridDtlLbl" >Pay Tag </td>
            <td width="1%" class="gridDtlLbl">:</td>
            <?php
				if ($empAllow['allowPayTag']=='P') $pTag ="Permanent";
				if ($empAllow['allowPayTag']=='T') $pTag ="Temporary";
				
			?>
            <td width="79%" class="gridDtlLbl" ><? echo $pTag;?></td>
          </tr>
          
            <tr> 
            <td width="20%" class="gridDtlLbl" >Divide Tag </td>
            <td width="1%" class="gridDtlLbl">:</td>
           
            <td width="79%" class="gridDtlLbl" ><? echo ($empAllow['allowPayTag']=="Y"?"Yes":"No");?></td>
          </tr>
          
        </table></td>
    </tr>
    <tr> 
      <td class="parentGridDtl"> <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
	  		<td colspan="11" class="gridToolbar" align=""> &nbsp; <a href="#" onClick="printAllowEarnList(<?="'$empNo','$allowCode'";?>);"> 
              <img src="../../../images/printer.png" align="absbottom" class="actionImg" title="Print Allowance Earnings List"></a> 
            </td>
	  		
          <tr> 
            <td width="1%" class="gridDtlLbl" align="center">#</td>
            <td width="32%" class="gridDtlLbl" align="center">PAYROLL DATE</td>
            <td width="44%" class="gridDtlLbl" align="center">ALLOWANCE</td>
          </tr>
          <?
			if($inqEmpAllowObj->getRecCount($resLnDtlList) > 0){
				$i=0;
				foreach ($arrLnDtlList as $lnDtlListVal){
				$pdDate = $inqEmpAllowObj->getPayPd($sessionVars['compCode'],$lnDtlListVal['pdYear'],$lnDtlListVal['pdNumber'],$_SESSION['pay_category'],$_SESSION['pay_group']);
				$employee = $inqEmpAllowObj->getUserInfo($sessionVars['compCode'],$lnDtlListVal['empNo'],"");
				$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
				$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
				. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
		 ?>
          <tr bgcolor="<?php echo $bgcolor; ?>" <?php echo $on_mouse; ?>> 
            <td class="gridDtlVal">
              <?=$i?>
            </td>
            <td class="gridDtlVal"><font class="gridDtlLblTxt">
              <?=$inqEmpAllowObj->valDateArt($pdDate['pdPayable'])?>
              </font></td>
            <td class="gridDtlVal" align="center"><font class="gridDtlLblTxt">
              <?=$lnDtlListVal['allowAmt']?>
              </font></td>
          </tr>
          <?
								}
							}
							else{
							?>
          <tr> 
            <td colspan="4" align="center"> <FONT class="zeroMsg">NOTHING TO DISPLAY</font> 
            </td>
          </tr>
          <?}?>
          <tr> 
            <td colspan="6" align="center" class="childGridFooter"> 
              <? $pager->_viewPagerButton("inq_emp_allow_list_details_ajax.php","allowListCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','&empNo='.$empNo.'&allowCode='.$allowCode);?>
            </td>
          </tr>
        </TABLE></td>
    </tr>
  </TABLE>
		</div>
		<?$inqEmpAllowObj->disConnect();?>
		<form name="frmEmpAllow" method="post" >
		  <input type="hidden" name="empNo" id="empNo" value="<? echo $_GET['empNo']; ?>">
		  <input type="hidden" name="allowCode" id="allowCode" value="<? echo $_GET['allowCode']; ?>">
		</form>
	</BODY>
</HTML>
