<?
session_start();
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("maintenance_obj.php");

$maintEmpObj = new maintenanceObj();
$articleStat = 'A';
if ($_GET['act'] == "EditArticle") {
	$articleinfo = $maintEmpObj->getArticle($_GET['articleId']);
	$articleStat = $articleinfo['stat'];
}

switch($_GET['code']) {
	case "AddArticle":
		$qryadd=$maintEmpObj->recordChecker("Select * from tblArticle where article='{$_GET['txtarticle']}' and sections='{$_GET['txtsection']}' and violation='{$_GET['txtviolation']}' and compCode='{$_SESSION['company_code']}'");
		if($qryadd){
			echo "alert('Article Already exist.');";
			exit();			
			}
		else{	
			if ($maintEmpObj->Article("Add",$_GET)){
				echo "alert('Article Successfully Added.');";
			}
			else{
				echo "alert('Error Adding Article.');";
			}
		}
	exit();	
	break;
	case "EditArticle":
		$qryedit=$maintEmpObj->recordChecker("Select * from tblArticle where article='{$_GET['txtarticle']}' and sections='{$_GET['txtsection']}' and violation='{$_GET['txtviolation']}' and compCode='{$_SESSION['company_code']}' and article_Id!='{$_GET['txtarticleid']}'");
		if($qryedit){
			echo "alert('Article Already Exist.');";
			exit();
			}
		else{	
			if ($maintEmpObj->Article("Edit",$_GET)){
				echo "alert('Article Successfully Updated.');";
			}
			else{
				echo "alert('Error Updating Article.');";
			}
		}
		exit();
	break;
//	case "DeleteBank":
//		if ($maintEmpObj->Bank("Delete",$_GET))
//			echo "alert('Bank Successfully Deleted.');";
//		else
//			echo "alert('Error Deleting Bank.');";
//
//		exit();
//	break;
}

?>

<HTML>
<head>
<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
<STYLE>@import url('../../style/payroll.css');</STYLE>
<style type="text/css">
<!--
	.headertxt {font-family: verdana; font-size: 11px;}
.style2 {font-family: verdana}
.style3 {font-size: 11px}
-->
</style>

</head>
	<BODY>
	<form action="" method="post" name="frmarticle" id="frmarticle">
      <table width="430" border="0" class="childGrid" cellpadding="2" cellspacing="1">
        
        <tr>
          <td width="29%" class="gridDtlLbl style2 style3" >Article</td>
          <td width="2%" class="gridDtlLbl style2 style3">:</td>
          <td width="69%" class="gridDtlVal"><input value="<?=$articleinfo['article']?>" type="text" name="txtarticle" id="txtarticle" class="inputs" size="15">
          <input type="hidden" value="<?=$_GET['act'];?>" name="code" id="code">
          <input type="hidden" name="txtarticleid" value="<?=$_GET['articleId']?>" id="txtarticleid"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl style2 style3" >Section</td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td class="gridDtlVal"><input value="<?=$articleinfo['sections']?>" type="text" name="txtsection" id="txtsection" class="inputs" size="15"></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Violation</span></td>
          <td class="gridDtlLbl"><span class="headertxt">:</span></td>
          <td><span class="gridDtlVal">
            <textarea name="txtviolation" cols="40" rows="3" class="inputs" id="txtviolation"><?=$articleinfo['violation']?>
            </textarea>
          </span></td>
        </tr>
        <tr>
          <td class="gridDtlLbl"><span class="headertxt">Status</span></td>
          <td class="gridDtlLbl style2 style3">:</td>
          <td><? $maintEmpObj->DropDownMenu(array('','A'=>'Active','H'=>'Held'),'cmbStat',$articleStat,'class="inputs"'); ?></td>
        </tr>
        <tr>
          <td class="childGridFooter"><div id="dvtest">&nbsp;</div></td>
          <td class="childGridFooter">&nbsp;</td>
          <td class="childGridFooter">&nbsp;
          <?php if($_SESSION['user_level']==1){ ?>
          <input name="Reset" type="reset" class="inputs" id="button" value="Reset">
          <input type="button" class="inputs" onClick="savearticle();" name="save" id="save" value="Submit">
          <?php } ?>
          </td>
        </tr>
      </table>
    </form>
    </BODY>
</HTML>
<script>
	function savearticle() {
		var empInputs = $('frmarticle').serialize(true);
		if (empInputs['txtarticle'] == "") {
			alert('Article is required.');
			$('txtarticle').focus();
            return false;		
		}        
		if (empInputs['txtsection']==""){
			alert('Section is required.');
			$('txtsection').focus();
			return false;		
		}
		if (empInputs['txtviolation']==""){
			alert('Violation is required.');
			$('txtviolation').focus();
			return false;
		}
		if (empInputs['cmbStat'] == 0) {
			alert('Status is required.');
			$('cmbStat').focus();
            return false;		
		}        
		params = 'article_act.php';
		new Ajax.Request(params,{
			method : 'get',
			parameters : $('frmarticle').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			}	
		});
	}	
</script>
