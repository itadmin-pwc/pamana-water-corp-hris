<?
session_start();
include("../../../includes/userErrorHandler.php");
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("position.obj.php");
//
$posObj = new positionObj();
$posObj->validateSessions('','MODULES');
$postMaintObj = new commonObj();
	
if($_GET['btnMaint'] == 'ADD'){
	if($posObj->recordChecker()>0){
		echo "alert('Record already exist.');";
		exit();
	}
	else{
		if($posObj->toPosition()){
			echo "alert('Position Successfully Saved');";
			exit();
			}
		else{
			echo "alert('Position Failed to Saved');";
			exit();
			}	
	}
	exit();
}

if($_GET['btnMaint']=='EDIT'){
	if($posObj->recordChecker(" and posCode<>'{$_GET['poscode']}'")>0){
		echo "alert('Record already exist.');";		
		exit();
	}
	else{	
		if($posObj->updatePosition(" where posCode='{$_GET['poscode']}'")){
			echo "alert('Position Successfully Updated.');";
			exit();
			}
		else{
			echo "alert('Position Failed to Saved.');";
			exit();
			}
	}
	exit();	
}

if($_GET['action']=='EDIT'){
	$showRes=$posObj->getPosition(" where posCode='{$_GET['poscode']}'");
		foreach($showRes as $listPost=>$position){
				$posObj->posCode=$position['posCode'];
				$posObj->desc=$position['posDesc'];
				$posObj->sdesc=$position['posShortDesc'];
				$posObj->div=$position['divCode'];
				$posObj->dept=$position['deptCode'];
				$posObj->sect=$position['sectCode'];
				$posObj->level=$position['level'];
				$posObj->rank=$position['rank'];
				$posObj->stats=$position['Active'];
				$posObj->compCode=$position['compCode'];
			}			
}

if($_GET['action'] == 'populatecmbdept'){   
	echo $posObj->DropDownMenu($posObj->makeArr2($posObj->getDepartment($_SESSION['company_code'],str_replace("-","",$_GET['divcode']),'','',2),'','deptCode','deptDesc',''),'cmbDept','','class="inputs" onchange="filterDepts(document.getElementById(\'cmbDiv\').value,this.value)" style="width:250px;"');
	exit();
}
if($_GET['action'] == 'populatecmbsection'){  
	echo $posObj->DropDownMenu($posObj->makeArr2($posObj->getDepartment($_SESSION['company_code'],$_GET['divcode'],str_replace("-","",$_GET['deptcode']),'',3),'','sectCode','deptDesc',''),'cmbSection','','class="inputs" style="width:250px;" onchange="filterSection(document.getElementById(\'cmbDiv\').value,document.getElementById(\'cmbDept\').value,this.value)"');
	exit();
}

?>
<HTML>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<STYLE>@import url('../../style/payroll.css');</STYLE>
	
	</HEAD>
<BODY>
		<FORM name="frmMaintPos" id="frmMaintPos" action="<?=$_SERVER['PHP_SELF']?>" method="post">
					
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
					<tr>
					  <td class="gridDtlLbl" align="left" >Division</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><span class="gridToolbar">
					    <?
                        $posObj->DropDownMenu($posObj->makeArr($posObj->getDepartment($_SESSION['company_code'],'','','',1),'divCode','deptDesc',''),'cmbDiv',$posObj->div,'class="inputs" onchange="populateCmbDept(this.value)" style="width:250px;"');
                        ?>
					 </span></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Department</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><font id='deptCont'>
					    <?
						$posObj->DropDownMenu($posObj->makeArr($posObj->getDepartment($posObj->compCode,$posObj->div,'','',2),'deptCode','deptDesc',''),'cmbDept',$posObj->dept,'class="inputs" onchange="filterDepts(document.getElementById(\'cmbDiv\').value,this.value)" style="width:250px;"');
                        ?>
					  </font></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Section</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><font id='SecCont'>
					    <?
						$posObj->DropDownMenu($posObj->makeArr($posObj->getDepartment($posObj->compCode,$posObj->div,$posObj->dept,'',3),'sectCode','deptDesc',''),'cmbSection',$posObj->sect,'class="inputs" style="width:250px;" onchange="filterSection(document.getElementById(\'cmbDiv\').value,document.getElementById(\'cmbDept\').value,this.value)"');
                        ?>
					  </font></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >Description</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><INPUT type="text" name="Desc" id="Desc" class="inputs" size="50" value="<?=$posObj->desc?>"></td>
					</tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Short Description </td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="shrtDesc" id="shrtDesc" class="inputs" size="50" value="<?=$posObj->sdesc?>"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Rank</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><? $posObj->DropDownMenu($posObj->makeArr($posObj->getRank(),'rankCode','rankDesc',''),'cmbRank',$posObj->rank,'class="inputs"' ); ?></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left" >Level</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><? $posObj->DropDownMenu($posObj->makeArr($posObj->getEmpLevel(),'empLevel','empLevelDesc',''),'cmbEmpLevel',$posObj->level,'class="inputs"' ); ?></td>
				  </tr>
					<tr>
						<td class="gridDtlLbl" align="left" >Status</td>
						<td width="1%" class="gridDtlLbl" align="center">:</td>
						<td class="gridDtlVal"><? $posObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$posObj->stats,'class="inputs"'); ?></td>
					</tr>
                    <?php // if($_SESSION['user_level']==1){ ?>
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
							<INPUT type="button" name="btnMaint" id="btnMaint" value="<?=$btnMaint?>" class="inputs" onClick="validateInputs(this.value,'<?=$_GET['poscode'];?>');">						</td>
					</tr>
                    <?php //} ?>
				</TABLE>
		  <INPUT type="hidden" name="hdnGlCod" id="hdnGlCod" value="<?=$_GET['glCode']?>">
</FORM>
</BODY>
</HTML>
<SCRIPT>
	function populateCmbDept(divcode){
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=populatecmbdept&divcode='+divcode,{
			method	: 'get',
			onComplete	: function(req){
				$('deptCont').innerHTML=req.responseText;
				},
			onCreate	: function(){
				$('deptCont').innerHTML='<img src="../../../images/wait.gif">' + ' loading...';
				}
			});
		}
	
	function filterDepts(divcode,deptcode){
		if($F('cmbDiv')==0){
				alert('Division is Required.');
				$('cmbDept').value=0;
				$('cmbDiv').focus();
		}
		else{
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action=populatecmbsection&divcode='+divcode+'&deptcode='+deptcode,{
				method	: 'get',
				onComplete	: function(req){
					$('SecCont').innerHTML=req.responseText;
					},
				onCreate	: function(){
					$('SecCont').innerHTML='<img src="../../../images/wait.gif">' + ' loading...';
					}				
				});
		}
		}
		
	function filterSection(divcode,deptcode,sectcode){
		if($F('cmbDiv')==0){
			alert('Division is Required.');
			$('cmbSection').value=0;
			$('cmbDiv').focus();
		}
		else if($F('cmbDept')==0){
			alert('Department is Required.');
			$('cmbSection').value=0;
			$('cmbDept').focus();
			}
		else{
			}
		}	

	function validateInputs(act,pcode){
		frm=$('frmMaintPos').serialize(true);
		if(frm['cmbDiv']==0){
			alert('Division is Required.');
			$('cmbSection').value=0;
			$('cmbDept').value=0;
			$('cmbDiv').focus();
			return false;
			}
		if(frm['cmbDept']==0){
			alert('Department is Required.');
			$('cmbSection').value=0;
			$('cmbDept').focus();
			return false;
			}
		if(frm['cmbSection']==0){
			alert('Section is Required.');
			$('cmbSection').focus();
			return false;
			}			
		if(trim(frm['Desc'])==""){
			alert('Description is Required.');
			$('Desc').focus();
			return false;
			}
		if(trim(frm['shrtDesc'])==""){
			alert('Short Description is Required.');
			$('shrtDesc').focus();
			return false;			
			}	
		if(frm['cmbRank']==0){
			alert('Rank is Required.');
			$('cmbRank').focus();
			return false;
			}
		if(frm['cmbEmpLevel']==0){
			alert('Employee Level is Required.');
			$('cmbEmpLevel').focus();
			return false;
			}		
		if(frm['cmbStat']==0){
			alert('Status is Required.');
			$('cmbStat').focus();
			return false;
			}					
			new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?poscode='+pcode,{
				method	:	'get',
				parameters	: $('frmMaintPos').serialize(),
				onComplete	: function(req){
					eval(req.responseText);
				}		
			});		
	}
</SCRIPT>