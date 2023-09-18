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
	
	switch ($_GET['action']){
		
		case 'getEmpInfo':
				
				$qryEmpInfo = "SELECT *
						FROM tblEmpMast
						WHERE compCode='".$_SESSION["company_code"]."' 
						AND empNo = '{$_GET['empNo']}'
						";
				$empInfo = $blackListObj->getSqlAssoc($blackListObj->execQry($qryEmpInfo));
				
				if($empInfo == 0){
					echo 0;
				}
				else{
					
					$compName = $blackListObj->getCompanyName($empInfo["compCode"]);
		
					$arrBrnchName =  $blackListObj->getBrnchInfo($empInfo["empBrnCode"]);
					
					$empBrnchName = $arrBrnchName["brnDesc"];
					
					$arrPosDesc = $blackListObj->getpositionwil(" where posCode='".$empInfo["empPosId"]."'",2);
					$empPosDesc = $arrPosDesc["posDesc"];
					
					$arrDeptDesc = $blackListObj->getDeptDescGen($empInfo["compCode"],$arrPosDesc["divCode"],$arrPosDesc["deptCode"]);
					$empDeptDesc = $arrDeptDesc["deptDesc"];
					
					$empSssNo = $empInfo["empSssNo"];
					$empTinNo = $empInfo["empTin"];
					
					$empBDate = ($empInfo["empBday"]!=""?date("m/d/Y", strtotime($empInfo["empBday"])):"");
					$empDateHired = ($empInfo["dateHired"]!=""?date("m/d/Y", strtotime($empInfo["dateHired"])):"");
					$empDateResigned = ($empInfo["dateResigned"]!=""?date("m/d/Y", strtotime($empInfo["dateResigned"])):"");
					
					echo "$('txtlname').value='".htmlspecialchars(addslashes($empInfo['empLastName']))."';";
					echo "$('txtfname').value='".htmlspecialchars(addslashes($empInfo['empFirstName']))."';";
					echo "$('txtmname').value='".htmlspecialchars(addslashes($empInfo['empMidName']))."';";
					
					
					echo "$('txtcompid').value='".$compName."';";
					echo "$('txtempbrnch').value='".$empBrnchName."';";
					echo "$('txtempagency').value='".$arrGetInfo["agency"]."';";
					echo "$('txtempdept').value='".$empDeptDesc."';";
					echo "$('txtemppos').value='".$empPosDesc."';";
					echo "$('txtempsss').value='".$empInfo["empSssNo"]."';";
					echo "$('txtemptin').value='".$empInfo["empTin"]."';";
					echo "$('txtempbdate').value='".($empInfo["empBday"]!=""?date("m/d/Y", strtotime($empInfo["empBday"])):"")."';";
					echo "$('txtempdhired').value='".($empInfo["dateHired"]!=""?date("m/d/Y", strtotime($empInfo["dateHired"])):"")."';";
					echo "$('txtempdres').value='".($empInfo["dateResigned"]!=""?date("m/d/Y", strtotime($empInfo["dateResigned"])):"")."';";
					echo "$('txtdencode').value='".date("m/d/Y")."';";
					echo "$('txtencodeby').value='".$updatedBy."';";
				}
				exit();
		break;
		
		case "Save":
			$resChecker = $blackListObj->recordChecker("Select * from tblBlacklistedEmp where empLastName='".str_replace("'","''", $_GET["txtlname"])."' and empFirstName='".str_replace("'","''", $_GET["txtfname"])."' and empMidName='".str_replace("'","''", $_GET["txtmname"])."' and empSssNo='".str_replace("-",'',$_GET["txtempsss"])."'");
			if($resChecker){
				echo "alert('Record already exist.');";
				exit();	
			}
			else{
				$resSave = $blackListObj->insEmptblBlackListed($_GET["txtAddEmpNo"],$_GET["txtreason"],$_GET["txtempagency"],$sessionVars['empNo'],$_GET["txtlname"],$_GET["txtfname"],$_GET["txtmname"],$_GET["txtempbdate"],$_GET["txtempsss"],$_GET["txtemptin"],$_GET["txtempbrnch"],$_GET["txtempdhired"],$_GET["txtempdres"],$_GET["txtcompid"],$_GET["txtempdept"],$_GET["txtemppos"]);
				if($resSave == true){
					echo "alert('Successfully Saved.');";
				}else{
					echo "alert('Saving Failed.');";
				}
			}
			exit();
		break;
	}
?>

<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<STYLE>@import url('../../../js/themes/default.css');</STYLE>
		<STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>
		
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	
    <BODY>
		
		<div class="niftyCorner">
        <form name="frmTS" id="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF'];?>">
            <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="parentGrid">
                <tr>
                    <td colspan="4" class="parentGridHdr">
                        &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Blacklist 
                    </td>
                </tr>
                <tr>
                	<td>
                    	 <table border="1" width='100%' cellpadding='1' cellspacing='1' class='childGrid'>
                            <div id="blacklist" style="visibility:<?php echo $hidden; ?>;">
                             
                                <?php	
									$empDir = "'../profile/employee_lookup_blacklist.php'";
									//$empDir = "'../profile/employee_lookup.php'";
                                    echo "<tr>\n";
                                        echo "<td width='19%' class='gridDtlLbl' align='left'>Emp. No. </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        echo "<td  width='30%' class='gridDtlVal'>
											<input tabindex='11' type='text' class='inputs' name='txtAddEmpNo' id='txtAddEmpNo'  style='width:100%;' value='".$_GET["empNo"]."' onkeydown='getEmployee(event,this.value)' onclick='clearFld()' onfocus='clearFld()'></td>";
                                      	echo '<td  width="15%" class="gridDtlVal">
											<input type="button" name="btnSelEmp" value=".." onclick="empLookup('.$empDir.');">
                                       
									  <FONT color="#FF0000" class="gridDtlLbl" id="hlprMsg"></font></td>';
									echo "<tr>\n";
										
									echo "</tr>";
									
									echo "<tr>\n";
                                        echo "<td width='19%' class='gridDtlLbl' align='left'>Branch </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' name='txtempbrnch' id='txtempbrnch' style='width:100%;' ".$readonly." value='".$empBrnchName."'></td>\n";
                                        echo "<td width='15%' class='gridDtlLbl' align='left'>Agency </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        echo "<td  width='34%' class='gridDtlVal' colspan='2'><input type='text' class='inputs' name='txtempagency' id='txtempagency' style='width:100%;' value='".$empAgency."' ".($_GET["mode"]=='v'?"readonly":"")."></td>\n";
                                        
                                    echo "</tr>";
									
                                    echo "<tr>\n";
                                        echo "<td width='19%' class='gridDtlLbl' align='left'>Employee Name </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        	echo "
												<td  width='30%' class='gridDtlVal' colspan='4'>
												<input type='text' class='inputs' name='txtlname' id='txtlname' style='width:30%;' ".$readonly." value='".str_replace("�","&Ntilde;",$emplName)."' > ,
												<input type='text' class='inputs' name='txtfname' id='txtfname' style='width:30%;' ".$readonly." value='".str_replace("�","&Ntilde;",$empfName)."'>
												<input type='text' class='inputs' name='txtmname' id='txtmname' style='width:20%;' ".$readonly." value='".str_replace("�","&Ntilde;",$empmName)."'>
											";
                                        echo "</td>\n";
                                        echo "<td width='1%'></td>";
                                        echo "<td  width='30%'></td>\n";
                                    echo "</tr>";
                                    echo "<tr>\n";
                                        echo "<td width='19%' class='gridDtlLbl' align='left'>Company </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        echo "<td  width='30%' class='gridDtlVal' colspan='2'><input type='text' class='inputs' name='txtcompid' id='txtcompid' style='width:100%;' ".$readonly." value='".$compName."'></td>\n";
                                        echo "<td width='1%'></td>";
                                        echo "<td  width='30%'></td>\n";
                                    echo "</tr>";
                                    echo "<tr>\n";
                                        echo "<td width='19%' class='gridDtlLbl' align='left'>Department </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
										echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempdept' id='txtempdept' value='".$empDeptDesc."'></td>\n";
                                        echo "<td width='15%' class='gridDtlLbl' align='left'>Position </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                 		echo "<td  width='34%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtemppos' id='txtemppos' value='".$empPosDesc."'></td>\n";
                                    echo "</tr>";
                                    echo "<tr>\n";
                                        echo "<td width='19%' class='gridDtlLbl' align='left'>SSS No. </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        echo "<td  width='30%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtempsss' id='txtempsss' ".$readonly." value='".$empSssNo."'></td>\n";
                                        
                                        echo "<td width='15%' class='gridDtlLbl' align='left'>Tin No. </td>\n";
                                        echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
                                        echo "<td  width='34%' class='gridDtlVal'><input type='text' class='inputs' style='width:100%;' name='txtemptin' id='txtemptin' ".$readonly." value='".$empTinNo."'></td>\n";
                                    echo "</tr>";
                                    echo "<tr>\n";
                                        echo "<td colspan='6'>";
                                            echo "<table border='0' width='100%'>";
                                                echo "<tr>";
                                                   echo "<td width='10%' class='gridDtlLbl' align='left'>Birth Date </td>\n";
													echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
													echo '<td  width="20%" class="gridDtlVal">
															<input value="" type="text" onChange="valDateStartEnd(this.value,this.id,document.frmTS.txtempbdate.value);" class="inputs" name="txtempbdate" id="txtempbdate" maxLength="10" readonly size="10"/>
                                  							<a href="#"><img name="imgbDate" id="imgbDate" src="../../../images/cal_new.png" title="Birth Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
													</td>';
													 
													echo "<td width='10%' class='gridDtlLbl' align='left'>Date Hired </td>\n";
                                                    echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
												   	echo '<td  width="20%" class="gridDtlVal">
															<input value="" type="text" onChange="valDateStartEnd(this.value,this.id,document.frmTS.txtempdhired.value);" class="inputs" name="txtempdhired" id="txtempdhired" maxLength="10" readonly size="10"/>
															<a href="#"><img name="imgdhired" id="imgdhired" src="../../../images/cal_new.png" title="Date Hired" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
													</td>';
														
												    echo "<td width='20%' class='gridDtlLbl' align='left'>Date Resigned </td>\n";
                                                    echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
													
													echo '<td  width="20%" class="gridDtlVal">
														<input value="" type="text" onChange="valDateStartEnd(this.value,this.id,document.frmTS.txtempdres.value);" class="inputs" name="txtempdres" id="txtempdres" maxLength="10" readonly size="10"/>
														<a href="#"><img name="imgdres" id="imgdres" src="../../../images/cal_new.png" title="Date Resign" style="cursor: pointer;position:relative;top:3px;border:none;"></a>
													</td>';
														
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
                                                echo "<input type='button' class= 'inputs' name='btnUserDef' id='btnUserDef' value='Save' onClick=\"valFields('Save');\">";
                                                echo "<input type='button' value='Reset' class='inputs' onClick='clearFld();'>";
												$backpath = "location.href='blacklisted_list.php'";
                                            	echo "<INPUT type='button' name='btnBack' id='btnBack' value='Back' onclick=".$backpath." class='inputs'>";
											echo "</td>";
                                        echo "</tr>";
                                    }
                                    
                                ?>
                              
											
                                </div>
                            </table>
                    </td>
                </tr>
                
                 
               
            </TABLE>
		<? $blackListObj->disConnect();?>		
    </form>
	</div>
   	</BODY>

</HTML>
<SCRIPT>
	
	Calendar.setup({
			  inputField  : "txtempbdate",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgbDate"       // ID of the button
		}
	)	
	
	Calendar.setup({
			  inputField  : "txtempdhired",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgdhired"       // ID of the button
		}
	)	
	
	Calendar.setup({
			  inputField  : "txtempdres",      // ID of the input field
			  ifFormat    : "%m/%d/%Y",          // the date format
			  button      : "imgdres"       // ID of the button
		}
	)	
	

	function clearFld(){
		$('txtlname').value='';
		$('txtfname').value='';
		$('txtmname').value='';
		$('txtcompid').value='';
		$('txtempbrnch').value='';
		$('txtempagency').value='';
		$('txtempdept').value='';
		$('txtempdept').value='';
		$('txtemppos').value='';
		$('txtempsss').value='';
		$('txtemptin').value='';
		$('txtempbdate').value='';
		$('txtempdhired').value='';
		$('txtempdres').value='';
		
		
	} 
	
	function getEmployee(evt,eleVal){
		
		var param = '?action=getEmpInfo&empNo='+eleVal;
		
		var k = evt.keyCode | evt.which;
		
		switch(k){
			case 8:
				clearFld();
			break;
			case 13:
				new Ajax.Request('<?=$_SERVER['PHP_SELF']?>'+param,{
					method : 'get',
					onComplete : function (req){

						if(parseInt(req.responseText) == 0){
							$('hlprMsg').innerHTML=' No Record Found.';
							setTimeout(function(){
								$('hlprMsg').innerHTML='';
							},5000);
						} 
						else{
							eval(req.responseText);
						}
					},
					onCreate : function (){
						$('hlprMsg').innerHTML='Loading...';
					},
					onSuccess : function (){
						$('hlprMsg').innerHTML='';
					}
				})
			break;
		}
	}
	
	function valFields($btn)
	{
		var numericExp = /[0-9]+/;
		var frmBlackList = $('frmTS').serialize(true);
		
		if(frmBlackList['txtlname']=="")
		{
			alert('Employee Last Name is required. \nCheck his/her data in Personal Profile.');
			$('txtlname').focus();
			return false;
		}
		
		if(frmBlackList['txtfname']=="")
		{
			alert('Employee First Name is required. \nCheck his/her data in Personal Profile.');
			$('txtfname').focus();
			return false;
		}
		
		if(frmBlackList['txtmname']=="")
		{
			alert('Employee Middle Name is required. \nCheck his/her data in Personal Profile.');
			$('txtmname').focus();
			return false;
		}
		
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
		
		if(!frmBlackList['txtempsss'].match(numericExp))
		{
			alert('Sss No. should be numeric.');
			$('txtempsss').focus();
			return false;
		}	
		
		if(frmBlackList['txtemptin']=="")
		{
			alert('Tin No. is required. \nCheck his/her data in Personal Profile.');
			$('txtemptin').focus();
			return false;
		}
		
		if(!frmBlackList['txtemptin'].match(numericExp))
		{
			alert('Tin No. should be numeric.');
			$('txtempsss').focus();
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
</SCRIPT>