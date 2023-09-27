<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("movement_obj.php");
$inqTSObj = new inqTSObj();
$sessionVars = $inqTSObj->getSeesionVars();
$inqTSObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("movement.trans.php");
##################################################
?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/reports.css');</style>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
	<BODY>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		<td class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Personel Information
		</td>
	</tr>
	<tr>
		
      <td class="parentGridDtl" ><table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="inq_emp.php">
              <font class="ToolBarseparator">
              <input name='hideUpload' type='hidden' id='hideUpload'>
              |</font> <a href="#" <? echo $printLoc; ?>> <img src="../../../images/<? echo $printImgFileName; ?>" align="absbottom" class="actionImg" title="Print Employee Information">Info</a>	
              <font class="ToolBarseparator">|</font> <a href="#" <? echo $printLoc2; ?>> 
              <img src="../../../images/<? echo $printImgFileName; ?>" align="absbottom" class="actionImg" title="Print Employee Information">Confi</a></td>
          </tr>
          <tr> 
            <td height="21" colspan="3" class="gridDtlLbl">Search by Individual</td>
            <td width="30%" rowspan="3" class="gridDtlVal">&nbsp;</td>
          </tr>          
          <tr> 
            <td width="11%" height="21" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="23%" class="gridDtlVal"><div id="personnelId"><input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"></div> 
              <? //echo $option_menu; ?>            </td>
            <td width="35%" rowspan="3" class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Employee Name </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50" onKeyPress="getEmpSearch(event);"></td>
          </tr>
          
          <tr> 
            <td class="gridDtlLbl">Group </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"> 
              <?  
			  
					$inqTSObj->DropDownMenu(array('1'=>'GROUP 1'),'groupType',$groupType,$groupType_dis); 
			  ?>            <input type="hidden" name="empDiv" id="empDiv">
              <input type="hidden" name="empDept" id="empDept">
              <input type="hidden" name="empSect" id="empSect">
              <input name="hide_empDept" type="hidden" id="hide_empDept" value="<? echo $empDept; ?>">
              <input name="hide_empSect" type="hidden" id="hide_empSect" value="<? echo $empDept; ?>">
              <input type="hidden" name="cmbType" id="cmbType">
              <span class="gridToolbar">
              <input name='txtfrDate' type='hidden' id='txtfrDate'>
              <input name='txttoDate' type='hidden' id='txttoDate'>
              </span></td>
            <td colspan="3" class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Category </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"> 
              <? 			
								$a = $inqTSObj->makeArr($inqTSObj->getCatArt($compCode),'payCat','payCatDesc','ALL');
								$inqTSObj->DropDownMenu($a,'catType',$catType,$orderBy_dis);
			  ?>            </td>
            <td width="30%" class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Order By</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder"> 
              <?
					$inqTSObj->DropDownMenu(array('1'=>'EMPLOYEE NAME','2'=>'EMPLOYEE NUMBER','3'=>'DEPARTMENT'),'orderBy',$orderBy,$orderBy_dis); 
			  ?>
              </font></td>
            <td class="gridDtlVal"> <font class="byOrder">&nbsp; </font> </td>
          </tr>
          <tr > 
            <td  class="gridToolbarWithColor" colspan="7"><center>
              </center></td>
          </tr>
          <tr>
            <td height="21" colspan="3" class="gridDtlLbl">Search by Branch</td>
            <td class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Branches</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder"> 
            <select name="cmbBranch" id="cmbBranch" <? echo $cmbReportOption;?> onChange="generatePdf(this.value);">
            <option value=""></option>
            <? 
			$res=$inqTSObj->getBranch($compCode);
			foreach($res as $valBranch =>$resBranch){
			?>
            <option value="<? echo $resBranch['brnCode'];?>"<? if($_GET['qryBranch']==$resBranch['brnCode']) echo "selected";?>><? echo $resBranch['brnDesc'];?></option>
            <?
			}
			?>
            </select>
              </font></td>
            <td class="gridDtlVal"> <font class="byOrder">&nbsp; </font> </td>
          </tr>
        </table></td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:white; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
