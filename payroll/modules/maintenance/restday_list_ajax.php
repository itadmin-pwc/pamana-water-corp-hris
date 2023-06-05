<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");

$profileobj = new commonObj();

if (trim($_SESSION['empRestDay']) != "") {
	$restDayList =explode(",",$_SESSION['empRestDay']);
} else {
	$restDayList = array();
}

?>

<HTML>
<head>
<style type="text/css">
<!--
.style1 {
	font-family: verdana;
	font-size: 11px;
	font-weight: bold;
}
.style2 {font-size: 8px}
-->
</style>
</head>
	<BODY>
		
		<div class="niftyCorner">
        
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" >
				
				<tr>		
			  <td colspan="4" class="parentGridDtl"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td><span class="style1">REST DAY LIST</span></td>
                  <td>&nbsp;</td>
      <td>
      <!--
                                <div align="left">
                                  <div align="left"><span class="parentGridHdr">
                                  <div style="cursor:pointer;" onClick="viewDetails('restday_act.php','Add','<?=$dedListVal['recNo']?>','restday_list_ajax.php','RDlist',0,0,'txtSrch','cmbSrch')">
                                    <div align="right"><img src="../../../images/application_form_add.png" width="16" border="0" height="16"></div>
                                  </div>
                                  </span></div>
                                </div>
                                -->
                                                 </td>
                </tr>
              </table></td>
			  </tr>
				<tr>
					<td>
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="tblPrevEmp" >
							<tr>
								<td width="51%" class="gridDtlLbl" align="center">Date</td>
								<td width="49%" align="center" class="gridDtlLbl">Action</td>
							</tr>
							<?
							if(count($restDayList) > 0){
								$i=0;
								for($x=0; $x<count($restDayList); $x++){
								//$arrTotal = $profileobj->getTranDeductionsTotal($sessionVars['compCode'],$rdVal['refNo']);
								$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
								$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
								. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';						
							?>
							<tr class="rowDtlEmplyrLst">
								<td class="gridDtlVal"><div align="center"><font class="gridDtlLblTxt">
							    <?=$restDayList[$x];?>
							    </font></div></td>
								<td class="gridDtlVal" align="center"></td>
          
						  </tr>
							<?
								}
							}
							else{
							?>
							<tr>
								<td colspan="2" align="center">
									<FONT class="zeroMsg">NOTHING TO DISPLAY</font>								</td>
							</tr>
							<?}?>
				  </TABLE>				  </td>
				</tr>
			</TABLE>
            
	</div>
		<? $profileobj->disConnect();?>
</BODY>
</HTML>
