<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("minimumGroup.obj.php");

$deptObj = new deptObj($_GET,$_SESSION);
$deptObj->validateSessions('','MODULES');

if($_GET['action'] == 'EDIT'){
	$arrMinGroup = $deptObj->getMinGroup($_GET['minGroupID']);
	$Desc = $arrMinGroup['minGroupName'];
	$stat = $arrMinGroup['stat'];
}

if($_GET['btnMaint'] == 'ADD'){
	if($deptObj->checkMinGroup() > 0){
		echo 1;//already exist
	}
	else{
		if($deptObj->toMinGroup() == true){
			echo 2;//successfully saved
		}else{
			echo 3;//saving failed
		}
	}
	exit();
}
if($_GET['btnMaint'] == 'EDIT'){
	if($deptObj->updtMinGroup() == true){
		echo 4;//successfully updated
	}
	else{
		echo 5;//updating failed
	}
	exit();
}


?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
        <script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
        <script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
        <script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
                <STYLE>@import url('../../../js/themes/default.css');</STYLE>
                <STYLE>@import url("../../../js/themes/mac_os_x.css");</STYLE>	
        <STYLE>@import url('../../style/payroll.css');</STYLE>
        <STYLE TYPE="text/css" MEDIA="screen">
        @import url("../../../includes/calendar/calendar-blue.css");.style3 {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-size: 11px;
            font-weight: bold;
        }
        </STYLE>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	
	</HEAD>
	<BODY>
		<FORM name="frmMaintSect" id="frmMaintSect" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
						<td width="41%" align="left" class="gridDtlLbl" >
							Group Description						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td width="58%" class="gridDtlVal">
							<INPUT type="text" name="Desc" id="Desc" class="inputs" size="40" value="<?=$Desc?>">						</td>
					</tr>
					<tr>
                      <td class="gridDtlLbl" align="left" >Status</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><? $deptObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$stat,'class="inputs"'); ?></td>
				  </tr>
<!--					<tr>
						<td class="gridDtlLbl" align="left" >
							Status
						</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal">
						</td>
					</tr>-->
                    <?php if($_SESSION['user_level']==1){ ?>
					<tr>
						<td align="center" class="childGridFooter" colspan="3">
							<?
								if($_GET['action'] == 'EDIT'){
									$btnMaint = 'EDIT';
								}
								if($_GET['action'] == 'ADD'){
									$btnMaint = 'ADD';
								}
							?>
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateDiv(this.value,'<?=$_GET['minGroupID']?>')">						</td>
					</tr>
                    <?php } ?>
				</TABLE>
	</FORM>
</BODY>
</HTML>
<SCRIPT>
	function validateDiv(act,minGroupID){
		frm = $('frmMaintSect').serialize(true);
		if(trim(frm['Desc']) == ''){
			alert('Description is Required');
			$('Desc').focus();
			return false;
		}
		if(trim(frm['cmbStat']) == 0){
			alert('Status is Required');
			$('cmbStat').focus();
			return false;
		}		
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?minGroupID='+minGroupID,{
			method : 'get',
			parameters : $('frmMaintSect').serialize(),
			onComplete : function(req){
				var resTxt = parseInt(req.responseText);
				switch(resTxt){
					case 1: alert('Division Already Exist'); break;
					case 2: alert('Successfully Saved'); break;
					case 3: alert('Saving Failed'); break;
					case 4: alert('Successfully Updated'); break;
					case 5: alert('Updating Failed'); break;
				}
			},
			onCreate : function(){
				$('btnMaint').disabled=true;
				$('btnMaint').value='Loading...';
			},
			onSuccess : function(){
				$('btnMaint').disabled=false;
				$('btnMaint').value=act;				
			}
		});
	}
</SCRIPT>