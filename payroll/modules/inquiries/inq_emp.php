<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("inq_emp_obj.php");
$maintEmpObj = new inqEmpObj();
$sessionVars = $maintEmpObj->getSeesionVars();
$maintEmpObj->validateSessions('','MODULES');
$compCode = $_SESSION['company_code'];
include("inq_emp.trans.php");
##################################################
?>
<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<style>@import url('../../style/main_emp_loans.css');</style>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='inq_emp_js.js'></script>
</HEAD>
	<BODY>
<form action="<? echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmEmp">
  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		<td class="parentGridHdr">
			&nbsp;<img src="../../../images/grid.png">&nbsp;Employee Personel Information
		</td>
	</tr>
	<tr>
		
      <td class="parentGridDtl" > <table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
          <tr > 
            <td class="gridToolbar" colspan="7"> <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
              <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
              <input name='fileName' type='hidden' id='fileName' value="inq_emp.php">
              <font class="ToolBarseparator">
              <input name='hideUpload' type='hidden' id='hideUpload'>
              |</font> <a href="#" <? echo $printLoc; ?>> <img src="../../../images/<? echo $printImgFileName; ?>" align="absbottom" class="actionImg" title="Print Employee Information">Info</a>	
              <font class="ToolBarseparator">|</font> <a href="#" <? echo $printLoc2; ?>> 
              <img src="../../../images/<? echo $printImgFileName; ?>" align="absbottom" class="actionImg" title="Print Employee Information">Confi</a>	
              <font class="ToolBarseparator">|</font> <a href="#" <? echo $printLoc3; ?>> 
              <img src="../../../images/<? echo $printImgFileName2; ?>" align="absbottom" class="actionImg" title="Print Department Hierarchy">Dept 
              Hierarchy</a> </td>
          </tr>
          <tr> 
            <td width="9%" height="21" class="gridDtlLbl">Emp. #</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td width="19%" class="gridDtlVal"> <input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event);"> 
              <? //echo $option_menu; ?>            </td>
            <td width="71%" rowspan="3" class="gridDtlVal">
				
              <div align="right"><img src="../../../images/empImage/<? echo $printEmpImg; ?>" align="absbottom" class="actionImg" title=""></a>              </div></td>
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
			  
					$maintEmpObj->DropDownMenu(array('1'=>'GROUP 1','2'=>'GROUP 2','3'=>'ALL'),'groupType',$groupType,$groupType_dis); 
			  ?>            </td>
            <td width="0" colspan="3" class="gridDtlVal">&nbsp;</td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Category </td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"> 
              <? 			
								$a = $maintEmpObj->makeArr($maintEmpObj->getCatArt($compCode),'payCat','payCatDesc','ALL');
								$maintEmpObj->DropDownMenu($a,'catType',$catType,$catType_dis);
			  ?>            </td>
            <td width="71%" class="gridDtlVal"><div align="right">
                <input name="userfile" type="file" <? echo $printLoc5; ?>/>
                <input name="Upload" type="button" id="Upload" value="Upload" onClick="valUpload();" <? echo $printLoc5; ?>>
                <input name="viewCam" type="button" id="viewCam" value="View Cam" onClick="getViewCam();" <? echo $printLoc5; ?>>
                <input name="refresh" type="button" id="refresh" value="Refresh" onClick="refreshImage();" <? echo $printLoc5; ?>>
              </div></td>
          </tr>
          <tr > 
            <td  class="gridToolbarWithColor" colspan="7"><center>
              </center></td>
          </tr>
          <tr> 
            <td class="gridDtlLbl">Order By</td>
            <td class="gridDtlLbl">:</td>
            <td class="gridDtlVal"><font class="byOrder"> 
              <?
					$maintEmpObj->DropDownMenu(array('1'=>'EMPLOYEE NAME','2'=>'EMPLOYEE NUMBER','3'=>'DEPARTMENT'),'orderBy',$orderBy,$orderBy_dis); 
			  ?>
              </font></td>
            <td class="gridDtlVal"> <font class="byOrder">&nbsp; </font> </td>
          </tr>
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
							<input type="button" name="searchEmp" id="searchEmp" value="Search" <? echo $searchEmp_dis; ?> onClick="valSearchEmp();">	
						</CENTER>
					</td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#c3daf9; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>