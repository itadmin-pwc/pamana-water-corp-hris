<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	10/14/2009
		Function		:	Maintenance (Pop Up) for the User Defined Master
	*/
	session_start();
	
	
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("profile_userdef.obj.php");
	
	$mainUserDefObjObj = new  mainUserDefObj();

	$sessionVars =  mainUserDefObj::getSeesionVars();
	$getSession = new mainUserDefObj($_GET,$sessionVars);
	$getSession->validateSessions('','MODULES');

	$empNo = $_SESSION['strprofile'];

	if($_SESSION['profile_act']=='Add')
	{
		unset($_SESSION['oldcompCode']);
	}
	
if($_GET['btnUserDef'] == 'Add')
{
	$col_field = explode(",",$_GET["tblUserDef_Fields"]);
	$catCode = $_GET["tblUserDef_CatCode"];
	
	$fields = explode(",",$_GET["tblUserDef_Value"]);
	
	foreach($fields as $fields_index=>$fields_value)
	{
		$val_chk.=$col_field[$fields_index]."="."'".str_replace("'","''",stripslashes($_GET[str_replace(" ","",$fields[$fields_index])]))."' AND ";
		$val_ins.="'".strtoupper(str_replace("'","''",stripslashes($_GET[str_replace(" ","",$fields[$fields_index])])))."',";
	}
	$chk_Fields = substr($val_chk,0,$val_chk.length - 4);
	$ins_Fields = substr($val_ins,0,$val_ins.length - 1);
	$chk_ConUserDef = $mainUserDefObjObj->chk_ConUserDef($_GET["tblUserDef_Fields"],$catCode,$empNo,$_SESSION['oldcompCode'],$chk_Fields);
	
	if($chk_ConUserDef!='0')
	{
		echo "alert('Record already exists.');";
	}
	else
	{
		$ins_UserDef = $mainUserDefObjObj->addEmp_Info($_GET["tblUserDef_Fields"],$catCode,$empNo,$_SESSION['oldcompCode'],$ins_Fields);
		if($ins_UserDef == true){
			echo "alert('Successfully Saved.');";
		}
		else{
			echo "alert('Saving Failed.');";
		}
	}
	
	exit();
}


if($_GET['btnUserDef'] == 'Edit')
{
	$col_field = explode("," ,$_GET["tblUserDef_Fields"]);
	$fields = explode(",",$_GET["tblUserDef_Value"]);
	$catCode = $_GET["tblUserDef_CatCode"];
	
	
	foreach($col_field as $col_label_index=>$col_label_value)
	{
		$val_check.=$col_field[$col_label_index]."="."'".str_replace("'","''",stripslashes($_GET[str_replace(" ","",$fields[$col_label_index])]))."' AND ";
		$val_update.=$col_field[$col_label_index]."="."'".strtoupper(str_replace("'","''",stripslashes($_GET[str_replace(" ","",$fields[$col_label_index])])))."',";
	} 
	$val_check = substr($val_check,0,$val_check.length - 4);
	$val_update = substr($val_update,0,$val_update.length-1);
	$chk_ConUserDef = $mainUserDefObjObj->chk_ConUserDef($_GET["tblUserDef_Fields"],$catCode,$empNo,$_SESSION['oldcompCode'],$val_check);
	if($chk_ConUserDef == true)
	{
		$chk_AgainCont = $mainUserDefObjObj->chk_AgainCon($_GET["tblUserDef_Fields"],$catCode,$empNo,$_SESSION['oldcompCode'],$val_check);
		if($chk_AgainCont==0)
		{
			$chk_ConUserDef = $mainUserDefObjObj->chk_ConUserDef($_GET["tblUserDef_Fields"],$catCode,$empNo,$_SESSION['oldcompCode'],$val_check);
			if($chk_ConUserDef == true)
			{
				echo "alert('Record already exists.');";
			}
		} 
		else
		{
			$up_UserDef = $mainUserDefObjObj->UpdateTblDefMast($val_update);
			if($up_UserDef == true){
				echo "alert('Successfully Saved.');";
			}
			else{
				echo "alert('Saving Failed.');";
			}
		}
	}
	else
	{
		$up_UserDef = $mainUserDefObjObj->UpdateTblDefMast($val_update);
		if($up_UserDef == true){
			echo "alert('Successfully Saved.');";
		}
		else{
			echo "alert('Saving Failed.');";
		}
	}	
	exit();
}


?>
<HTML>
	<HEAD> 
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
		<!--calendar lib-->
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
		<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
        
		<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
		<!--end calendar lib-->
	</HEAD>
	<BODY>
    	<form name="userDefinedPop" id="userDefinedPop" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        	<?php
				$exp_catCode = explode("|",$mainUserDefObjObj->getUserDef_ColumnName($_GET["catCode"]));
				$col_label = explode(",",$exp_catCode[0]);
				$col_fields = explode(",",$exp_catCode[1]);
				$input_types = explode(",",$exp_catCode[3]);
				
				echo "<table border='0' width='100%' cellpadding='1' cellspacing='1' class='childGrid'>\n";
					echo "<tr>";
						$empInfo = $mainUserDefObjObj->getUserInfo($sessionVars['compCode'],$_GET["empNo"],'');
						$midName = (!empty($empInfo['empMidName'])) ? substr($empInfo['empMidName'],0,1)."," : '';
						echo "<td align='center' colspan='3' class='prevEmpHeader'>".$empInfo['empNo'] . " - " . $empInfo['empFirstName'] . " " . $midName . " " . $empInfo['empLastName']."</td>";
					echo "</tr>";
						

					foreach($col_label as $col_label_index=>$col_label_value)
					{
						$input_type = $mainUserDefObjObj->form_input($input_types[$col_label_index],$col_label_value,$_GET["act"],$col_fields[$col_label_index]);
						echo "<tr>\n";
							echo "<td class='gridDtlLbl' align='left'>".$col_label_value."</td>\n";
							echo "<td width='1%' class='gridDtlLbl' align='center'>:</td>";
							echo "<td class='gridDtlVal'>".$input_type."</td>\n";
						echo "</tr>\n";
						
						if((int)$input_types[$col_label_index] == 3)
						{
							?>
                            	<script>
									Calendar.setup({
											  inputField  : "<?php echo str_replace(" ","",$col_label_value);?>",      // ID of the input field
											  ifFormat    : "%m/%d/%Y",          // the date format
											  button      : "<?php echo str_replace(" ","","img".$col_label_value);?>"       // ID of the button
										}
									)
								</script>
                            <?php
						}
						
					}
					
					echo "<tr>";
						echo "<td align='center' class='childGridFooter' colspan='3'>";
							echo "<input type='hidden' name='tblUserDef_Fields' value='".$exp_catCode[1]."'";
							echo "<input type='hidden' name='tblUserDef_Value' value='".$exp_catCode[0]."'";
							echo "<input type='hidden' name='tblUserDef_RecNo' value='".$_GET["recNo"]."'";
							echo "<input type='hidden' name='tblUserDef_EmpNo' value='".$_GET["empNo"]."'";
							echo "<input type='hidden' name='tblUserDef_CatCode' value='".$_GET["catCode"]."'";
							echo "<input type='button' class= 'inputs' name='btnUserDef' value='".$_GET["act"]."' onClick=\"validation('".$input_names."');\">";
							echo "<input type='button' value='Reset' class='inputs' onClick='reset_page_add();'>";
						echo "</td>";
					echo "</tr>";
				echo "</table>\n";
			?>
        </form>
    </BODY>
</HTML>

<script>
	function validation(name)
	{
		var numericExp = /[0-9]+/;
		var frmuserDefinedPop = $('userDefinedPop').serialize(true);
		
		
		var a = $('userDefinedPop').serialize();
		var c = $('userDefinedPop').serialize(true);
		b = a.split('&');
		
		fieldName = c['tblUserDef_Value'].split(',');
		
		for(i=0;i<parseInt(b.length)-1;i++){
			d = b[i].split("=");
			
			
			if(trim(c[d[0]]) == ""){
				alert(fieldName[i] + " is required.");
				$(d[0]).focus();
				return false;
			}
			
			if(d[0]=='DaysofSuspension')
			{
				if(!frmuserDefinedPop['DaysofSuspension'].match(numericExp)){
					alert('Invalid Days of Suspension : Numbers Only.');
					$('DaysofSuspension').focus();
					return false;
				}		
			}
			
			if(d[0]=='LicenseNumber')
			{
				if(!frmuserDefinedPop['LicenseNumber'].match(numericExp)){
					alert('Invalid License Number : Numbers Only.');
					$('LicenseNumber').focus();
					return false;
				}		
			}
			
			if(d[0]=='Qty')
			{
				if(!frmuserDefinedPop['Qty'].match(numericExp)){
					alert('Invalid Qty : Numbers Only.');
					$('Qty').focus();
					return false;
				}		
			}

		}
		
		new Ajax.Request('<?php $_SESSION['PHP_SELF'];?>',{
			method : 'get',
			parameters : c,
			onComplete : function(req){
				eval(req.responseText);
			}
		});
	}
	
	function reset_page_add()
	{
		var a = $('userDefinedPop').serialize();
		var c = $('userDefinedPop').serialize(true);
		b = a.split('&');
		
		for(i=0;i<parseInt(b.length)-6;i++){
			d = b[i].split("=");
			document.userDefinedPop[d[0]].value='';
		}
		
	}
</script>
