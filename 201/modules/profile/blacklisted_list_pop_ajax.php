<?
/*
	Created By		:	Genarra Jo - Ann S. Arong
	Date Created 	: 	03/24/2010
	Function		:	Blacklist Module (Pop Up Ajax) 
*/

session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("../../../includes/pager.inc.php");
include("blacklisted_obj.php");

$blackListObj = new blackListObj();
$sessionVars = $blackListObj->getSeesionVars();
$blackListObj->validateSessions('','MODULES');

$pager = new AjaxPager(2,'../../../images/');

$qryIntMaxRec = "Select blacklist_No, reason, dateEncoded, userId from tblBlacklistedEmp where empNo='".$_GET["empNo"]."' order by blackList_No";
$resIntMaxRec = $blackListObj->execQry($qryIntMaxRec);
$intMaxRec = $pager->_getMaxRec($resIntMaxRec);

$intLimit = $pager->_limit;
$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);


$qryBlackList = "SELECT *
				FROM tblBlacklistedEmp
				WHERE empNo='".$_GET["empNo"]."'
				ORDER BY blacklist_No ";

$resBlackList= $blackListObj->execQry($qryBlackList);
$arrBlackList = $blackListObj->getArrRes($resBlackList);
										
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
		
		<div class="niftyCorner" id="tsCont">
        	<input type="hidden" name="txtSrch" id="txtSrch" value="<?=$_GET['txtSrch']?>" style="height:8px;">
            <input type="hidden" name="cmbSrch" id="cmbSrch" value="<?=$_GET['srchType']?>" style="height:8px;">
        	
        	<input type="hidden" name="empNo" id="empNo" value="<?php echo $_GET["empNo"]; ?>">
            <table border="0" width="100%">
            	<tr>
                	<td class="gridDtlLbl">EMPLOYEE BLACKLIST INFORMATION</td>
                    
                </tr>
            </table>
        	<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="tblPrevEmp" >
            	<tr>
                	<td class="gridDtlLbl" align="center" width="5%">Blacklist No.</td>
                    <td class="gridDtlLbl" align="center" width="45%">Reason</td>
                    <td class="gridDtlLbl" align="center" width="20%">Date Encoded</td>
                    <td class="gridDtlLbl" align="center" width="20%">Encoded By</td>
                    <td class="gridDtlLbl" align="center" width="10%">Action</td>
                </tr>
                
                <?php
					foreach($arrBlackList as $arrBlackList_val)
					{
						echo "<tr class='rowDtlEmplyrLst'>";
							echo "<td class='gridDtlVal'><font class='gridDtlLblTxt'>".$arrBlackList_val["blacklist_No"]."</font></td>";
							echo "<td class='gridDtlVal'><font class='gridDtlLblTxt'>".strtoupper($arrBlackList_val["reason"])."</font></td>";
							echo "<td class='gridDtlVal'><font class='gridDtlLblTxt'>".date("m/d/Y", strtotime($arrBlackList_val["dateEncoded"]))."</font></td>";
							
							echo "<td class='gridDtlVal'><font class='gridDtlLblTxt'>".strtoupper($arrBlackList_val["empLastName"].", ".$arrBlackList_val["empFirstName"])."</font></td>";
							echo "<td align='center'>";
								echo '<span class="gridDtlVal">';
										echo '<img src="../../../images/application_get.png" border="0" OnClick="edit_info('.$arrBlackList_val["blacklist_No"].');" class="actionImg" title="Edit Blacklist Information"/>&nbsp;'; 
										
										if($_GET["mode"]!='v')
											echo '<img src="../../../images/prev_emp_dele.png" border="0" OnClick="del_blacklist('.$arrBlackList_val["blacklist_No"].');" class="actionImg" title="Delete Blacklist Information"/>&nbsp;'; 
										echo '<img src="../../../images/printer.png" border="0" OnClick="print_blacklist('.$arrBlackList_val["blacklist_No"].');" class="actionImg" title="Print Blacklist Information"/>&nbsp;'; 
										
								echo '</span>';
								
							echo "</td>";
						echo "</tr>";
					}
				?>
               
            </TABLE>
        	<span class="childGridFooter"><? $pager->_viewPagerButton("blacklisted_list_pop_ajax.php","TSCont",$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch',"&mode=".$_GET["mode"]."&empNo=".$_GET["empNo"]);?></span>       
		</div>
		<? $blackListObj->disConnect();?>
		
	</BODY>
</HTML>
<script>
	
</script>