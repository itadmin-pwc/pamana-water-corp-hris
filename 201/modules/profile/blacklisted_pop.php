<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	03/24/2010
		Function		:	Blacklist Module (Pop Up) 
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("blacklisted_obj.php");
	
	$blackListObj = new blackListObj();
	$sessionVars = $blackListObj->getSeesionVars();
	$blackListObj->validateSessions('','MODULES');
	
	$arrUserInfo = $blackListObj->getUserHeaderInfo($sessionVars['empNo'],$_SESSION['employee_id']); 
	$updatedBy = $arrUserInfo['empFirstName']." ".$arrUserInfo['empLastName'];
	$dateUpdated = date("m/d/Y");
	
	function getEmpArrInfo($blackListNo)
	{
		extract($GLOBALS);
		global $compName,$empBlackListNo,$empName,$empBrnchName,$empAgency,$empPosDesc,$empDeptDesc,$empSssNo,$empTinNo,$empBDate,$empDateHired,$empDateResigned,$empreason,$empno;
		
		$arrGetInfo = $blackListObj->getInfo($blackListNo);
		$empno=$arrGetInfo["empNo"];
		$empBlackListNo = $blackListNo;
		$empName = $arrGetInfo["empLastName"].", ".$arrGetInfo["empFirstName"]." ".$arrGetInfo["empMidName"];
		
			
		$compName = $arrGetInfo["compCode"];
		$empBrnchName = $arrGetInfo["empBrnCode"];
		$empPosDesc = $arrGetInfo["empPosId"];
		$empDeptDesc = $arrGetInfo["empDepCode"];
		$empAgency = $arrGetInfo['agency'];
		
		$empSssNo = $arrGetInfo["empSssNo"];
		$empTinNo = $arrGetInfo["empTin"];
		
		$empBDate = ($arrGetInfo["empBday"]!=""?date("m/d/Y", strtotime($arrGetInfo["empBday"])):"");
		$empDateHired = ($arrGetInfo["dateHired"]!=""?date("m/d/Y", strtotime($arrGetInfo["dateHired"])):"");
		$empDateResigned = ($arrGetInfo["dateResigned"]!=""?date("m/d/Y", strtotime($arrGetInfo["dateResigned"])):"");
		$empreason = $arrGetInfo["reason"];
		
	}
	
	function getEmpArrInfo2()
	{
		extract($GLOBALS);
		
		$arrGetInfo = $blackListObj->getInfo($_GET["blacklistid"]);
		$compName = $arrGetInfo["compCode"];
		$empBrnchName = $arrGetInfo["empBrnCode"];
		$empPosDesc = $arrGetInfo["empPosId"];
		$empDeptDesc = $arrGetInfo["empDepCode"];
		$readonly = "readonly";
		echo "$('txtblacklistno').value='".$arrGetInfo["blacklist_No"]."';";
		echo "$('txtempname').value='".strtoupper($arrGetInfo["emprLname"].", ".$arrGetInfo["emprFname"]." ".$arrGetInfo["emprMidName"])."';";
		echo "$('txtcompid').value='".$compName."';";
		echo "$('txtempbrnch').value='".$empBrnchName."';";
		
		echo "$('txtempagency').value='".$arrGetInfo["agency"]."';";
		echo "$('txtempdept').value='".$empDeptDesc."';";
		echo "$('txtemppos').value='".$empPosDesc."';";
		echo "$('txtempsss').value='".$arrGetInfo["empSssNo"]."';";
		echo "$('txtemptin').value='".$arrGetInfo["empTin"]."';";
		echo "$('txtempbdate').value='".($arrGetInfo["empBday"]!=""?date("m/d/Y", strtotime($arrGetInfo["empBday"])):"")."';";
		echo "$('txtempdhired').value='".($arrGetInfo["dateHired"]!=""?date("m/d/Y", strtotime($arrGetInfo["dateHired"])):"")."';";
		echo "$('txtempdres').value='".($arrGetInfo["dateResigned"]!=""?date("m/d/Y", strtotime($arrGetInfo["dateResigned"])):"")."';";
		echo "$('txtreason').value='".$arrGetInfo["reason"]."';";
		echo "$('txtdencode').value='".date("m/d/Y")."';";
		echo "$('txtencodeby').value='".$updatedBy."';";
	}
	//Get Employee Information
	if($_GET["mode"]=='a')
	{
		$arrEmpInfo = $blackListObj->getEmpInfo($_GET["empNo"]);
		if($arrEmpInfo["empNo"]!="")
		{
			$readonly = "readonly";
			$empno =$arrEmpInfo["empNo"];
			$empName = $arrEmpInfo["empLastName"].", ".$arrEmpInfo["empFirstName"]." ".$arrEmpInfo["empMidName"];
			
			$compName = $arrGetInfo["compCode"];
			$empBrnchName = $arrGetInfo["empBrnCode"];
			$empPosDesc = $arrGetInfo["empPosId"];
			$empDeptDesc = $arrGetInfo["empDepCode"];
			
			
			$empSssNo = $arrEmpInfo["empSssNo"];
			$empTinNo = $arrEmpInfo["empTin"];
			$empBDate = ($arrEmpInfo["empBday"]!=""?date("m/d/Y", strtotime($arrEmpInfo["empBday"])):"");
			$empDateHired = ($arrEmpInfo["dateHired"]!=""?date("m/d/Y", strtotime($arrEmpInfo["dateHired"])):"");
			$empDateResigned = ($arrEmpInfo["dateResigned"]!=""?date("m/d/Y", strtotime($arrEmpInfo["dateResigned"])):"");
		}
		else
		{
			$readonly = "";
		}
		$dateEncode = date("m/d/Y");
		$encodedBy = $arrUserInfo['empFirstName']." ".$arrUserInfo['empLastName'];
		$btnUserDef = "Save";
	
	}
	elseif(($_GET["mode"]=='e')||($_GET["mode"]=='v'))
	{
			//Check How many record
			//echo $_GET["empNo"];
			$res_noOfRecords = $blackListObj->chkEmpNoRecords($_GET["empNo"]);
			$noOfRecords = $blackListObj->getRecCount($res_noOfRecords);
			$arrnoOfRecords = $blackListObj->getSqlAssoc($res_noOfRecords);
			
			if($noOfRecords>1)
			{
				$div_visibility = "1";
				if($_GET["blacklistid"]!="")
				{
					getEmpArrInfo2();
					exit();
				}
				else
				{
					getEmpArrInfo($arrnoOfRecords["blacklist_No"]);
				}
			}
			elseif($noOfRecords==1)
			{
				$div_visibility = "1";
				if($_GET["blacklistid"]!="")
				{
					getEmpArrInfo2();
					exit();
				}
				else
				{
					getEmpArrInfo($arrnoOfRecords["blacklist_No"]);
				}
			}
			
		$btnUserDef = "Edit";
	}
	
	
	if($_GET["action"]=="Save")
	{
		//Check if Data Exists
		$resSave = $blackListObj->insEmptblBlackListed($_GET["txtempid"],$_GET["txtreason"],$_GET["txtempagency"],$sessionVars['empNo']);
		if($resSave == true){
			echo "alert('Successfully Saved.');";
		}else{
			echo "alert('Saving Failed.');";
		}
		exit();
		
	}
	
	if($_GET["action"]=="Edit")
	{
		//Check if Data Exists
		$resUpdate = $blackListObj->uptEmptblBlackListed($_GET["txtblacklistno"],$_GET["txtreason"],$_GET["txtempagency"],$sessionVars['empNo']);
		if($resUpdate == true)
		{
			echo "alert('Successfully Saved.');";
			echo "pager('blacklisted_list_pop_ajax.php','tsCont','load',0,0,'','','&empNo=".$_GET["txtempid"]."','../../../images/'); "; 
		}	
		else{
			echo "alert('Saving Failed.');";
		}exit();
	}
	
	if($_GET["action"]=="Delete")
	{
		$resDelete = $blackListObj->delEmptblBlackListed($_GET["blacklistid"]);
		if($resDelete == true){
			echo "$('txtblacklistno').value='';";
			echo "$('txtempname').value='';";
			echo "$('txtcompid').value='';";
			echo "$('txtempbrnch').value='';";
			echo "$('txtempagency').value='';";
			echo "$('txtempdept').value='';";
			echo "$('txtemppos').value='';";
			echo "$('txtempsss').value='';";
			echo "$('txtemptin').value='';";
			echo "$('txtempbdate').value='';";
			echo "$('txtempdhired').value='';";
			echo "$('txtempdres').value='';";
			echo "$('txtreason').value='';";
			echo "$('txtdencode').value='';";
			echo "$('txtencodeby').value='';";
			echo "alert('Record successfully deleted.');";
			$res_noOfRecords = $blackListObj->chkEmpNoRecords($_GET["empNo"]);
			$noOfRecords = $blackListObj->getRecCount($res_noOfRecords);
			if($noOfRecords==""){
				echo "$('txtempid').value='';";
				echo "alert('No Blacklist record exists.');";
			}
				
		}
		else
		{
			echo "alert('Delete of record failed.');";
		}
		
		exit();
	}
?>
<HTML>

	<HEAD> 
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../../payroll/style/payroll.css');</STYLE>
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
        
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
    	

    	<form name="frmBlackList" id="frmBlackList" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <?php 
			if($div_visibility==1)
				echo "<br>";
		?>
        <table border="0" width='100%' cellpadding='1' cellspacing='1' class='childGrid'>
        <div id="blacklist" style="visibility:<?php echo $hidden; ?>;">
        	<?php
				echo "<tr>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>Emp. No. </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' name='txtempid' id='txtempid'  style='width:100%;' readonly value='".$empno."'></td>\n";
					
					echo "<td width='19%' class='gridDtlLbl' align='left'>Blacklist No.</td>\n";
					echo "<td width='1%'>:</td>";
					echo "<td  width='30%'><input type='text' class='inputs' name='txtblacklistno' id='txtblacklistno'  style='width:50%;' readonly value='".$empBlackListNo."'></td>\n";
				echo "</tr>";
				echo "<tr>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>Employee Name </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					
						
						if(($readonly=="")&&($_GET["mode"]=='a'))
						{
							echo "
								<td  width='30%' class='gridDtlVal' colspan='4'>
								<input type='text' class='inputs' name='txtlname' id='txtlname' style='width:30%;' ".$readonly." value='".str_replace("Ñ","&Ntilde;",$emplName)."'> ,
								<input type='text' class='inputs' name='txtfname' id='txtfname' style='width:30%;' ".$readonly." value='".str_replace("Ñ","&Ntilde;",$empfName)."'>
								<input type='text' class='inputs' name='txtmname' id='txtmname' style='width:20%;' ".$readonly." value='".str_replace("Ñ","&Ntilde;",$empmName)."'>
							";
						}
						else
						{
							echo "
								<td  width='30%' class='gridDtlVal' colspan='2'>
								<input type='text' class='inputs' name='txtempname' id='txtempname' style='width:100%;' readonly value='".str_replace("Ñ","&Ntilde;",$empName)."'>
							";
						}
						
					echo "</td>\n";
					echo "<td width='1%'></td>";
					echo "<td  width='30%'></td>\n";
				echo "</tr>";
				echo "<tr>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>Company </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo '<td  width="30%" class="gridDtlVal" colspan="2"><input type="text" class="inputs" name="txtcompid" id="txtcompid" style="width:100%;" '.$readonly.' value="'.$compName.'"></td>'."\n";
					echo "<td width='1%'></td>";
					echo "<td  width='30%'></td>\n";
				echo "</tr>";
				echo "<tr>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>Branch </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' name='txtempbrnch' id='txtempbrnch' style='width:100%;' ".$readonly." value='".$empBrnchName."'></td>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>Agency </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo '<td  width="30%" class="gridDtlVal" colspan="2"><input type="text" class="inputs" name="txtempagency" id="txtempagency" style="width:100%;" value="'.$empAgency.'"' .($_GET["mode"]=='v'?"readonly":"").'></td>'."\n";
					
				echo "</tr>";
				
				echo "<tr>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>Department </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempdept' id='txtempdept' readonly value='".$empDeptDesc."'></td>\n";
					
					echo "<td width='19%' class='gridDtlLbl' align='left'>Position </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtemppos' id='txtemppos' readonly value='".$empPosDesc."'></td>\n";
				echo "</tr>";
				echo "<tr>\n";
					echo "<td width='19%' class='gridDtlLbl' align='left'>SSS No. </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempsss' id='txtempsss' ".$readonly." value='".$empSssNo."'></td>\n";
					
					echo "<td width='19%' class='gridDtlLbl' align='left'>Tin No. </td>\n";
					echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
					echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtemptin' id='txtemptin' ".$readonly." value='".$empTinNo."'></td>\n";
				echo "</tr>";
				echo "<tr>\n";
					echo "<td colspan='6'>";
						echo "<table border='0' width='100%'>";
							echo "<tr>";
								echo "<td width='10%' class='gridDtlLbl' align='left'>Birth Date </td>\n";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td  width='20%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempbdate' id='txtempbdate' ".$readonly." value='".$empBDate."'></td>\n";
								
								
								echo "<td width='10%' class='gridDtlLbl' align='left'>Date Hired </td>\n";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td  width='20%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempdhired' id='txtempdhired' ".$readonly." value='".$empDateHired."'></td>\n";
								
								echo "<td width='20%' class='gridDtlLbl' align='left'>Date Resigned </td>\n";
								echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
								echo "<td  width='20%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempdres' id='txtempdres' ".$readonly." value='".$empDateResigned."'></td>\n";
							echo "</tr>";
						echo "</table>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>\n";
					echo "<td colspan='6'>";
						echo "<fieldset class='gridDtlLbl'>";
        					echo "<legend>Reason</legend>";
							echo "<textarea style='width:100%; height:30px;' name='txtreason' id='txtreason' class='inputs' cols='19' rows='2' ".($_GET["mode"]=='v'?"readonly":"").">".$empreason."</textarea>";
						echo "</fieldset>";
					echo"</td>";
				echo "</tr>";
				if($_GET["mode"]=='a')
				{
					echo "<tr>\n";
						echo "<td width='19%' class='gridDtlLbl' align='left'>Date Encode </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtdencode' id='txtdencode' readonly value='".$dateEncode."'></td>\n";
						
						echo "<td width='19%' class='gridDtlLbl' align='left'>Encoded By </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtencodeby' id='txtencodeby' readonly value='".$encodedBy."'></td>\n";
					echo "</tr>";
				}
				else
				{
					echo "<tr>\n";
						echo "<td width='19%' class='gridDtlLbl' align='left'>Date Updated </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtdencode' id='txtdencode' readonly value='".$dateUpdated."'></td>\n";
						
						echo "<td width='19%' class='gridDtlLbl' align='left'>Updated By </td>\n";
						echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
						echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtencodeby' id='txtencodeby' readonly value='".$updatedBy."'></td>\n";
					echo "</tr>";
				}
				
				if($_GET["mode"]!='v')
				{
					echo "<tr>";
						echo "<td align='center' class='childGridFooter' colspan='6'>";
							echo "<input type='button' class= 'inputs' name='btnUserDef' id='btnUserDef' value='Save' onClick=\"valFields('".$btnUserDef."');\">";
							echo "<input type='button' value='Reset' class='inputs' onClick='reset_val();'>";
						echo "</td>";
					echo "</tr>";
				}
				
			?>
            </div>
        </table>
    	
        </form>
        
    </BODY>
</HTML>

<SCRIPT>
	
	pager("blacklisted_list_pop_ajax.php","TSCont",'load',0,0,'','','&mode=<?=$_GET["mode"]?>&empNo=<?=$_GET['empNo']?>','../../../images/');  
	
</SCRIPT>
<script>
	
	function valFields($btn)
	{
		var numericExp = /[0-9]+/;
		var frmBlackList = $('frmBlackList').serialize(true);
		
		
		if(frmBlackList['txtempid']=="")
		{
			alert('No Blacklist record exists to the selected employee.\nNo Data to be saved.');
			$('txtempid').focus();
			return false;
		}
		
		if(frmBlackList['txtempname']=="")
		{
			alert('Employee Name is required. \nCheck his/her data in Personal Profile.');
			$('txtempname').focus();
			return false;
		}
		
		if(frmBlackList['txtempsss']=="")
		{
			alert('Sss No. is required. \nCheck his/her data in Personal Profile.');
			$('txtempsss').focus();
			return false;
		}
		
		if(frmBlackList['txtemptin']=="")
		{
			alert('Tin No. is required. \nCheck his/her data in Personal Profile.');
			$('txtemptin').focus();
			return false;
		}
		
		if(frmBlackList['txtreason']=="")
		{
			alert('Reason is required.');
			$('txtreason').focus();
			return false;
		}
		
		
		new Ajax.Request('<?php $_SESSION['PHP_SELF'];?>?&action='+$btn,{
			method : 'get',
			parameters : frmBlackList,
			onComplete : function(req){
				eval(req.responseText);
			}
		});
	}
	
	
	function edit_info(blacklistid)
	{
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&mode=e&blacklistid='+blacklistid+'&empNo=<?=$_GET['empNo']?>',{
			method : 'get',
			onComplete : function (req){
				eval(req.responseText);	
			}			
		})
	}
	
	function reset_val()
	{
		document.frmBlackList.txtreason.value='';
		document.frmBlackList.txtempagency.value='';
	}
	
	function del_blacklist(blacklistid)
	{
		var ans = confirm('Are you sure do you want to delete the selected record? ');
		if(ans == true)
		{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?&action=Delete&blacklistid='+blacklistid+'&empNo=<?=$_GET['empNo']?>',{
				method : 'get',
				onComplete : function (req){
					eval(req.responseText);	
					pager("blacklisted_list_pop_ajax.php",'tsCont','load',0,0,'','','&empNo=<?=$_GET['empNo']?>','../../../images/');  
				}			
			})
		}
	}
	
	function print_blacklist(blacklistid)
	{
		document.frmBlackList.action = 'blacklist_ind_pdf.php?&blacklistid='+blacklistid+'&empNo=<?=$_GET['empNo']?>';
		document.frmBlackList.target = "_blank";
		document.frmBlackList.submit();
	}
	
</script>
