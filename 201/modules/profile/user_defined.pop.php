<?
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	10/14/2009
		Function		:	Maintenance (Pop Up) for the User Defined Master
		Edited BY 		: 	Nhomer G. Cabico
	*/
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("profile_userdef.obj.php");
	
	$mainUserDefObjObj = new  mainUserDefObj();
	$maintEmpObj = new commonObj();
	$sessionVars =  mainUserDefObj::getSeesionVars();
	$getSession = new mainUserDefObj($_GET,$sessionVars);
	$getSession->validateSessions('','MODULES');

	$empNo = $_SESSION['strprofile'];

	if($_SESSION['profile_act']=='Add')
	{
		unset($_SESSION['oldcompCode']);
	}

$dcode=$_GET['recNo'] ? $_GET['recNo'] : "";	

if($_GET['act']=="Edit"){
	//Educational Background
	switch($_GET['catCode']){
		case "1";
			$resTableContent=$mainUserDefObjObj->lookUpTablesData(" where tblEducationalBackground.educationalBackgroundId='".$_GET['recNo']."'","1");
			if($mainUserDefObjObj->getRecCount($resTableContent) > 0)
			{
				$rowTableContent = $mainUserDefObjObj->getArrRes($resTableContent);
				foreach($rowTableContent as $rowContent_val=>$value)
				{
					$schoolid=$value['type'];
					$schoolname=$value['schoolId'];
					$datestart=$maintEmpObj->valDateArt($value['dateStarted']);
					$datefinish=$maintEmpObj->valDateArt($value['dateCompleted']);
					$licenseNumber=$value['licenseNumber'];
					$licenseName=$value['licenseName'];
					$dateIssued=$maintEmpObj->valDateArt($value['dateIssued']);
					$dateExpired=$maintEmpObj->valDateArt($value['dateExpired']);
				}
			}
		break;

		case "7";
			$resTableContent=$mainUserDefObjObj->lookUpTablesData(" where tblDisciplinaryAction.da_Id='".$_GET['recNo']."'","7");
			if($mainUserDefObjObj->getRecCount($resTableContent) > 0)
			{
				$rowTableContent = $mainUserDefObjObj->getArrRes($resTableContent);
				foreach($rowTableContent as $rowContent_val=>$value)
				{
					$datecommitted=$maintEmpObj->valDateArt($value['date_commit']);
					$dateserve=$maintEmpObj->valDateArt($value['date_serve']);
					$article=$value['article_id'];
					$section=$value['section_id'];
					$offense=$value['offense'];
					$sanction=$value['sanction'];
					$suspensionfrom=$maintEmpObj->valDateArt($value['suspensionFrom']);
					$suspensionto=$maintEmpObj->valDateArt($value['suspensionTo']);
				}
				$resarticle=$maintEmpObj->getArticle("Select article_Id,violation from tblArticle where article_Id='{$section}' and stat='A'");
				foreach($resarticle as $rowArticle=>$articles){
					$violation=$articles['violation'];
					}
			}
		break;

		case "9";
			$resTableContent=$mainUserDefObjObj->lookUpTablesData(" where tblEmployeeDataHistory.employeeDataId='".$_GET['recNo']."'","9");
			if($mainUserDefObjObj->getRecCount($resTableContent) > 0)
			{
				$rowTableContent = $mainUserDefObjObj->getArrRes($resTableContent);
				foreach($rowTableContent as $rowContent_val=>$value)
				{
					if($value['companyName']!=""){
						$company=$value['companyName'];
					}
					else{
						$company="Not Applicable";	
					}
					$compposition=$value['employeePosition'];
					$compdatestart=$maintEmpObj->valDateArt($value['startDate']);
					$compdateend=$maintEmpObj->valDateArt($value['endDate']);
				}
			}
		break;

	}
	
	
//	exit();
}	
if($_GET['btnUserDef']=="Add"){ 
	$resChecker="";
	switch($_GET['catcode']){
		case "1":
		$resChecker=$mainUserDefObjObj->recordChecker("Select * from tblEducationalBackground where type='" . $_GET['cmbSchoolType'] . "' and schoolId='" . $_GET['cmbSchool'] . "' and empNo='" . $empNo . "'");
		if($resChecker){
			echo "alert('Educational background already exist.')";
			exit();
		}
		else{
			$fields="";
			$values="";	
			if($_GET['txtobtaindate']!=""){
				$fields.=",dateIssued";
				$values.=",'{$_GET['txtobtaindate']}'";
				}
			if($_GET['txtexpirydate']!=""){
				$fields.=",dateExpired";
				$values.=",'{$_GET['txtexpirydate']}'";
				}	
			$result=$mainUserDefObjObj->setQry("Insert into tblEducationalBackground (type,schoolId,dateStarted,dateCompleted,empNo,catCode,licenseNumber,licenseName $fields) values('" . $_GET['cmbSchoolType'] . "','" . $_GET['cmbSchool'] . "','" . trim($_GET['txtstartdate']) . "','" . trim($_GET['txtfinisdate']) . "','" . $empNo . "','" . $_GET['catcode'] . "','" . str_replace("'","''",$_GET['txtlicenseno']) . "','" . str_replace("'","''",$_GET['txtlicensename']) . "'$values)");
			if($result){
				echo "alert('Educational background/license sucessfully saved.');";	
				}
			else{
				echo "alert('Educational background/license failed to save.');";
				}	
		}
		exit();
		break;			
		
		case "7":	
			if($_GET['txtdatefrom']!=""){
				$field.=",suspensionFrom";
				$value.=",'{$_GET['txtdatefrom']}'";		
			}
			if($_GET['txtdateto']!=""){
				$field.=",suspensionTo";
				$value.=",'{$_GET['txtdateto']}'";	
			}
			$resChecker=$mainUserDefObjObj->recordChecker("Select * from tblDisciplinaryAction where date_commit='".$_GET['txtdatecommit']."' and date_serve='".$_GET['txtdateserve']."' and article_id='".$_GET['cmbArticle']."' and section_id='".$_GET['cmbSection']."' and offense='".$_GET['cmbOffense']."' and sanction='".$_GET['cmbSanction']."' and suspensionFrom='".$_GET['txtdatefrom']."' and suspensionTo='".$_GET['txtdateto']."' and empNo='".$empNo."'");
			if($resChecker){
				echo "alert('Disciplinary action already exist.');";
				exit();
				}
			else{			
				$result=$mainUserDefObjObj->setQry("Insert into tblDisciplinaryAction (date_commit,date_serve,article_id,section_id,offense,sanction,empNo,catCode $field) values('{$_GET['txtdatecommit']}','{$_GET['txtdateserve']}','{$_GET['cmbArticle']}','{$_GET['cmbSection']}','{$_GET['cmbOffense']}','{$_GET['cmbSanction']}','{$empNo}','{$_GET['catcode']}' $value)");
			if($result){
				echo "alert('Disciplinary action sucessfully saved.');";
				}
			else{
				echo "alert('Disciplinary action failed to save.');";
				}	
			}
			exit();
			break;
						
		case "9":
		$resChecker=$mainUserDefObjObj->recordChecker("Select * from tblEmployeeDataHistory where companyName='" . str_replace("'","''",$_GET['txtcompany']) . "' and employeePosition='" . str_replace("'","''",$_GET['txtcompposition']) . "' and empNo='" . $empNo . "'");
		if($resChecker){
			echo "alert('Record already exist.')";
			exit();	
		}
		else{
			$fields="";
			$values="";
			if( $_GET['txtcompdatestart']!=""){
				$fields.= ",startDate";
				$values.= ",'{$_GET['txtcompdatestart']}'";
				}
			if( $_GET['txtcompdateend']!=""){
				$fields.= ",endDate";
				$values.= ",'{$_GET['txtcompdateend']}'";
				}	
			$result=$mainUserDefObjObj->setQry("Insert into tblEmployeeDataHistory (companyName,employeePosition,empNo,catCode $fields) values('" . str_replace("'","''",$_GET['txtcompany']) . "','" . str_replace("'","''",$_GET['txtcompposition']) . "','" . $empNo . "','" . $_GET['catcode'] . "' $values)");	
			if($result){
				echo "alert('Employment data history sucessfully saved.');";
				}
			else{
				echo "alert('Employment data history failed to save.');";
				}
		}
		exit();
		break;
	}
}

if($_GET['btnUserDef']=="Edit"){
	$resChecker="";
	switch($_GET['catcode']){
		case "1":
		$resChecker=$mainUserDefObjObj->recordChecker("Select * from tblEducationalBackground where type='" . $_GET['cmbSchoolType'] . "' and schoolId='" . $_GET['cmbSchool'] . "' and empNo='" . $empNo . "' and educationalBackgroundId<>'" . $_GET['datacode'] . "'");
		if($resChecker){
			echo "alert('Educational background already exist.');";
			exit();	
		}
		else{
			$fields="";
			if($_GET['txtobtaindate']!=""){
				$fields.=",dateIssued='{$_GET['txtobtaindate']}'";
				}
			if($_GET['txtexpirydate']!=""){
				$fields.=",dateExpired='{$_GET['txtexpirydate']}'";
				}	
			$result=$mainUserDefObjObj->setQry("Update tblEducationalBackground set type='" . $_GET['cmbSchoolType'] . "',schoolId='" . $_GET['cmbSchool'] . "',dateStarted='" . trim($_GET['txtstartdate']) . "',dateCompleted='" . trim($_GET['txtfinisdate']) . "',licenseNumber='" . str_replace("'","''",$_GET['txtlicenseno']) . "',licenseName='" . str_replace("'","''",$_GET['txtlicensename']) . "'" . $fields . " where educationalBackgroundId='" . $_GET['datacode'] . "'");
			if($result){
				echo "alert('Educational background sucessfully updated.');";
				}
			else{
				echo "alert('Educational background failed to update.')";
				}	
		}
		exit();
		break;	
						
		case "7":
		$resChecker=$mainUserDefObjObj->recordChecker("Select * from tblDisciplinaryAction where date_commit='".$_GET['txtdatecommit']."' and date_serve='".$_GET['txtdateserve']."' and article_id='".$_GET['cmbArticle']."' and section_id='".$_GET['cmbSection']."' and offense='".$_GET['cmbOffense']."' and sanction='".$_GET['cmbSanction']."' and suspensionFrom='".$_GET['txtdatefrom']."' and suspensionTo='".$_GET['txtdateto']."' and empNo='".$empNo."' and da_Id<>'".$_GET['datacode']."'");
		if($resChecker){
			echo "alert('Disciplinary action already exist.');";
			exit();
			}
		else{	
			$result=$mainUserDefObjObj->setQry("Update tblDisciplinaryAction set date_commit='" . $_GET['txtdatecommit'] . "',date_serve='" . $_GET['txtdateserve'] . "',article_id='" . $_GET['cmbArticle'] . "',section_id='" . $_GET['cmbSection'] . "',offense='" . $_GET['cmbOffense'] . "',sanction='" . $_GET['cmbSanction'] . "',suspensionFrom='" . $_GET['txtdatefrom'] . "',suspensionTo='" . $_GET['txtdateto'] . "' where da_Id='" . $_GET['datacode'] . "'");
			if($result){
				echo "alert('Disciplinary action sucessfully updated.');";
				}
			else{
				echo "alert('Disciplinary action failed to update.');";
				}
		}
		exit();
		break;	
			
		case "9";
		$resChecker=$mainUserDefObjObj->recordChecker("Select * from tblEmployeeDataHistory where companyName='" . str_replace("'","''",$_GET['txtcompany']) . "' and employeePosition='" . str_replace("'","''",$_GET['txtcompposition']) . "' and empNo='" . $empNo . "' and employeeDataId<>'" . $_GET['datacode'] . "'");
		if($resChecker){
			echo "alert('Employment data already exist.');";	
			exit();
		}
		else{
			$value="";
			if($_GET['txtcompdatestart']!=""){
				$value.=",startDate='{$_GET['txtcompdatestart']}'";
				}
			if($_GET['txtcompdateend']!=""){
				$value.=",endDate='{$_GET['txtcompdateend']}'";
				}	
			$result=$mainUserDefObjObj->setQry("Update tblEmployeeDataHistory set companyName='" . str_replace("'","''",$_GET['txtcompany']) . "',employeePosition='" . str_replace("'","''",$_GET['txtcompposition']) . "'" . $value . " where employeeDataId='" . $_GET['datacode'] . "'") ;
			if($result){
				echo "alert('Employment data history sucessfully updated.');";
				}
			else{
				echo "alert('Employment data history failed to update.')";
				}	
		}
		exit();
		break;		
	}
		
}		
//function getRefNo(){
//	$refno = $maintEmpObj->getRefNo($compCode);
//}
if($_GET['transtype']=="section"){
	echo $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getArticle("SELECT * FROM  dbo.tblArticle where article='{$_GET['valid']}' and stat='A'"),'article_Id','sections',''),'cmbSection','','class="inputs" style="width:115px;" onChange="processViolation(this.value);"');
	exit();
	}
	
if($_GET['transtype']=="violations"){
	$qryval=$maintEmpObj->getArticle("SELECT * FROM  dbo.tblArticle where article_Id='{$_GET['valid']}'");
	foreach($qryval as $val=>$violations){
			echo $violations['violation'];	
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
	<BODY onLoad="compData();">
    	<form name="userDefinedPop" id="userDefinedPop" action="<?=$_SERVER['PHP_SELF']?>" method="post">
                <table border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
					<tr>
						<td align="center" colspan="3" class="prevEmpHeader"></td>
					</tr>
                    <?
					if($_GET['catCode']==1)
					{
					?>
					<tr>
					  <td class="gridDtlLbl" align="left">Type</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
					  <? 
					  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getDefLookup(" where type='EducType'"),'seqId','typeDesc',''),'cmbSchoolType',$schoolid,'class="inputs" style="width:350px;"');
					  ?></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">School Name</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
					  <? 
					  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getDefLookup(" where type='SchoolName'"),'seqId','typeDesc',''),'cmbSchool',$schoolname,'class="inputs" style="width:350px;"');
					  ?>
				      </td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Started</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="txtstartdate" id="txtstartdate" class="inputs" value="<?=$datestart;?>" readonly>
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtstartdate" id="imgtxtstartdate" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Finished</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="txtfinisdate" id="txtfinisdate" class="inputs" value="<?=$datefinish;?>" readonly>
				    <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtfinisdate" id="imgtxtfinisdate" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">License Number</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtlicenseno" type="text" class="inputs" id="txtlicenseno" size="30" value="<?=$licenseNumber;?>"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">License Name</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtlicensename" type="text" class="inputs" id="txtlicensename" size="50" value="<?=$licenseName;?>"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Obtained</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="txtobtaindate" id="txtobtaindate" class="inputs" readonly value="<?=$dateIssued;?>">
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtobtaindate" id="imgtxtobtaindate" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Expiry Date</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="txtexpirydate" id="txtexpirydate" class="inputs" readonly value="<?=$dateExpired;?>">
				    <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtexpirydate" id="imgtxtexpirydate" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
                  <?
					}
                    if($_GET['catCode']==7){
					?>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Committed</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtdatecommit" type="text" class="inputs" id="txtdatecommit" value="<?=$datecommitted;?>" size="15" readonly>
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgdatecommit" id="imgdatecommit" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Served</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtdateserve" type="text" class="inputs" id="txtdateserve" value="<?=$dateserve?>" size="15" readonly>
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgdateserve" id="imgdateserve" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Article</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
                      <? 
					  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getArticle("SELECT DISTINCT article,compCode,stat FROM         tblArticle WHERE compCode='{$_SESSION['company_code']}' and stat='A'"),'article','article',''),'cmbArticle',$article,'class="inputs" style="width:115px;" onChange="processSection(this.value);"');
					  ?>
                      </td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Section</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><div id="sectionid">
                      <? 
					  $maintEmpObj->DropDownMenu($maintEmpObj->makeArr($maintEmpObj->getArticle("SELECT * FROM  dbo.tblArticle where article='{$article}' and stat='A'"),'article_Id','sections',''),'cmbSection',$section,'class="inputs" style="width:115px;" onChange="processViolation(this.value);"');
					  ?>
                      </div></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Violation</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><fieldset style="width:330px" class="inputs"><div id="violationid"><?=$violation;?></div></fieldset></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Offense</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
                      <? 
					  $maintEmpObj->DropDownMenu(array('','First Offense','Second Offense','Third Offense','Fourth Offense','Fifth Offense','Sixth Offense'),'cmbOffense',$offense,'class="inputs" style="width:350px;"');
					  ?>
                      </td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Sanction</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal">
                      <? 
					  $maintEmpObj->DropDownMenu(array('','Written Warning','1 Day Suspension','3 Days Suspension','1 Week Suspension','2 Week Suspension','30 Days Suspension','Dismissal'),'cmbSanction',$sanction,'class="inputs" style="width:350px;"');
					  ?>
                      </td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Suspension From</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtdatefrom" type="text" class="inputs" id="txtdatefrom" value="<?=$suspensionfrom;?>" size="15" readonly>
				      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgdatefrom" id="imgdatefrom" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Suspension To</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtdateto" type="text" class="inputs" id="txtdateto" value="<?=$suspensionto?>" size="15" readonly>
				      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgdateto" id="imgdateto" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
                  <?
					}
                    if($_GET['catCode']==9){
					?>
					<tr>
					  <td class="gridDtlLbl" align="left">Company Name</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtcompany" type="text" class="inputs" id="txtcompany" size="50" value="<?=$company;?>"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Position Title</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input name="txtcompposition" type="text" class="inputs" id="txtcompposition" size="50" value="<?=$compposition;?>"></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Started</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="txtcompdatestart" id="txtcompdatestart" class="inputs" readonly value="<?=$compdatestart;?>">
                      <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtcompdatestart" id="imgtxtcompdatestart" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
					<tr>
					  <td class="gridDtlLbl" align="left">Date Finished</td>
					  <td class="gridDtlLbl" align="center">:</td>
					  <td class="gridDtlVal"><input type="text" name="txtcompdateend" id="txtcompdateend" class="inputs" readonly value="<?=$compdateend;?>">
				    <a href="#"><img src="../../../images/cal_new.png" width="20" height="14" name="imgtxtcompdateend" id="imgtxtcompdateend" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
				  </tr>
                  <?
					}
				  ?>
					<tr>
					  <td colspan="3" height="2"></td>
				  </tr>
                    <tr>
						<td align="center" class="childGridFooter" colspan="3">
							<input type="button" class= "inputs" name="btnUserDef" id="btnUserDef" value="<?=$_GET["act"]?>" onClick="datavalidations(this.value,'<?=$_GET['catCode']?>','<?=$dcode;?>');" title="<?=$_GET['catCode'] . "/" . $_GET["act"];?>">
							<input type="reset" value="Reset" class="inputs">
						</td>
					</tr>
				</table>
        </form>
    </BODY>
</HTML>

<script>
	<?
	if($_GET['catCode']==9){
	?>
	function compData(){
		var empInputs = $('userDefinedPop').serialize(true);
		if(empInputs['txtcompany']==""){
			$('txtcompany').value='No Previous Employer';	
			$('txtcompany').focus();
		}
	}
	<?
	}
	?>
	function datavalidations(actss,catcode,datacode)
	{
		var empInputs = $('userDefinedPop').serialize(true);
		var actss=empInputs['btnUserDef'];
		if(catcode==1){
			if(empInputs['cmbSchoolType']==0){
				alert('School Type is Required.');
				$('cmbSchoolType').focus();
				return false;	
				}
			if(empInputs['cmbSchool']==0){
				alert('School is Required.');
				$('cmbSchool').focus();
				return false;
				}
			if(trim(empInputs['txtstartdate'])==""){
				alert('Start Date is Required.');
				return false;
				}	
			if(trim(empInputs['txtfinisdate'])==""){
				alert('Finished Date is Required.');
				return false;
				}	
		}
		//if(catcode==7){
		//	alert(document.getElementById('btnUserDef').value);
		//	}
		if(catcode==7){
			if(trim(empInputs['txtdatecommit'])==""){
				alert('Date Committed is Required.');
				$('imgdatecommit').focus();
				return false;
				}	
			if(trim(empInputs['txtdateserve'])==""){
				alert('Date Serve is Required.');
				$('txtdateserve').focus();
				return false;
				}	
			if(empInputs['cmbArticle']==0){
				alert('Article is Required.');
				$('cmbArticle').focus();
				return false;
				}
			if(empInputs['cmbSection']==0){
				alert('Section is required.');
				$('cmbSection').focus();
				return false;
				}		
			if(empInputs['cmbOffense']==0){
				alert('Offense is Required.');
				$('cmbOffense').focus();
				return false;
				}	
			if(empInputs['cmbSanction']==0){
				alert('Sanction is Required.');
				$('cmbSanction').focus();
				return false;
				}	
			if(empInputs['cmbSanction']!=1 && empInputs['cmbSanction']!=7){	
				if(trim(empInputs['txtdatefrom'])==""){
					alert('Suspension From is Required.');
					$('txtdatefrom').focus();
					return false;
					}	
				if(trim(empInputs['txtdateto'])==""){
					alert('Suspension To is Required.');
					$('txtdateto').focus();
					return false;
					}	
			}
		}
		if(catcode==9){
			if(trim(empInputs['txtcompany'])==""){
				alert('Company Name is Required.');
				$('txtcompany').focus();
				return false;
				}
			if(trim(empInputs['txtcompposition'])==""){
				alert('Position is Required.');
				$('txtcompposition').focus();
				return false;
				}
		}
		
		new Ajax.Request('user_defined.pop.php?catcode='+catcode+'&datacode='+datacode,{
				method : 'get',
				parameters : $('userDefinedPop').serialize(),
				onComplete : function(req){
					eval(req.responseText);
				}
			});
	}
	
	function processSection(secid){
		var params='user_defined.pop.php?transtype=section&valid='+secid;
		new Ajax.Request(params,{
			method : 'get',
			//parameters : $('userDefinedPop').serialize(),
			onComplete : function(req){
				$('sectionid').innerHTML=req.responseText;
				$('violationid').innerHTML='';
				},
			onCreate : function(){
				$('sectionid').innerHTML='loading data.....';
				}	
			});
		}
	
	function processViolation(valid){
		var params='user_defined.pop.php?transtype=violations&valid='+valid;
		new Ajax.Request(params,{
			method : 'get',
			//parameters : $('userDefinedPop').serialize(),
			onComplete : function(req){
				 $('violationid').innerHTML=req.responseText;
				 },
			onCreate : function(){
				 $('violationid').innerHTML='loading data......'; 
				 }				 
			});
		}	
		
	//Educational Background	
	<? if($_GET['catCode']==1){?>
	Calendar.setup({
	  inputField  : "txtstartdate",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgtxtstartdate"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtfinisdate",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgtxtfinisdate"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtobtaindate",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgtxtobtaindate"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtexpirydate",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgtxtexpirydate"       // ID of the button
	}
	)
	<? } if($_GET['catCode']==7){?>
	Calendar.setup({
	  inputField  : "txtdatecommit",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgdatecommit"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtdateserve",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgdateserve"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtdatefrom",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgdatefrom"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtdateto",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgdateto"       // ID of the button
	}
	)
	//Employment data
	<? } if($_GET['catCode']==9){?>
	Calendar.setup({
	  inputField  : "txtcompdatestart",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgtxtcompdatestart"       // ID of the button
	}
	)
	Calendar.setup({
	  inputField  : "txtcompdateend",      // ID of the input field
	  ifFormat    : "%Y-%m-%d",          // the date format
	  button      : "imgtxtcompdateend"       // ID of the button
	}
	)
	<? }?>
	
</script>