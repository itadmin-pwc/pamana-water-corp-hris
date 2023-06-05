<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("migration.obj.php");
define('DOWNLOAD_PATH',  SYS_NAME.'/payroll/modules/migration/errors');

$mirationObj = new mirationObj($_GET,$_SESSION);
$mirationObj->validateSessions('','MODULES');

if(isset($_POST['btnUpload'])) {
	
	/*$userPass = base64_encode('SLABADO');
	$decodePass = base64_decode($userPass);
	echo $decodePass."".$userPass."GENARRA";*/

	/*$error = $_FILES["fileUpload"]["error"];*/

	if ($error == UPLOAD_ERR_OK) 
	{
		$tmp_name = $_FILES["fileUpload"]["tmp_name"];
		if($tmp_name!="")
		{
			$name = $_FILES["fileUpload"]["name"];
			$size = $_FILES["fileUpload"]["size"];				
			move_uploaded_file($tmp_name, "allowance.mdb");
			
			include("../../../includes/adodb/adodb.inc.php"); 
			$db =& ADONewConnection('access');
			$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath("allowance.mdb");
			$db->Connect($dsn,'','');
			
			$resGetAllowList = $mirationObj->getAllowlist($db);
			$i=0;
			
			
			while(!$resGetAllowList->EOF){
			
				$checkEmp = $mirationObj->checkEmpNoToEmpMast_Updated($resGetAllowList->fields['empNo'], $_POST["empBrnCode"]);
						if($checkEmp != "0"){
							if((float)$resGetAllowList->fields['allowAmnt'] != 0){
								//$delEmptblAllowPar = $mirationObj->deltblAllow_Paradox($resGetAllowList->fields['empNo']);						
								$tmpNewAllowCode = $mirationObj->getEquivAllwCode($resGetAllowList->fields['allowCode']);
								if($tmpNewAllowCode != "0"){
								
								if($resGetAllowList->fields['allwFreq'] == '1ST HALF'){
									$sked = '1';
								}
								if($resGetAllowList->fields['allwFreq'] == '2ND HALF'){
									$sked = '2';
								}
								if($resGetAllowList->fields['allwFreq'] == 'BOTH'){
									$sked = '3';
								}									
							
								$arrempDateHired = $mirationObj->getEmployeeList($_SESSION["company_code"]," and empNo='".$resGetAllowList->fields['empNo']."'");
								$arrAllowsprtpstag = $mirationObj->getAllowSprtPs($tmpNewAllowCode['allowCodeNew']);
								$qryToNewAllowTable = "INSERT INTO tblAllowance_Paradox(compCode,
																				empNo,
																				allowCode,
																				allowamt,
																				allowSked,
																				allowPayTag,
																				allowStat,
																				allowStart,
																				sprtPS
																			   )
																			   VALUES
																			   ('{$checkEmp['compCode']}',
																			   '{$resGetAllowList->fields['empNo']}',
																			   '{$tmpNewAllowCode['allowCodeNew']}',
																			   '{$resGetAllowList->fields['allowAmnt']}',
																			   '{$sked}',
																			   'P',
																			   'A',
																			   '".($arrempDateHired!=""?$arrempDateHired["dateHired"]:"")."',
																			   '".$arrAllowsprtpstag["sprtPS"]."'
																			   );"; 
								//$resToNewAllowTable = $mirationObj->execQry($qryToNewAllowTable);
								$i++;
									
								}
							}
						}
						
					$resGetAllowList->MoveNext();
					unset($checkEmp,$tmpNewAllowCode,$sked);
				}
				
			if($resToNewAllowTable)
			{
				echo "<script>alert('$i record/s successfully added to the Employee Master File.');</script>";
			}
			
			$rep_fileName = "updated_allowance_migration_pdf.php?empBrnCode=".$_POST["empBrnCode"];
		}
		else
		{
			echo "<script language='javascript'>alert('Select the file to be Uploaded.');</script>";
		}
	}
}
?>
<HTML>
	<HEAD>
<TITLE>
	<?=SYS_TITLE?>
</TITLE>
	<style>@import url('../../style/main_emp_loans.css');</style>
	<script type='text/javascript' src='../../../includes/jSLib.js'></script>
	<script type='text/javascript' src='../../../includes/prototype.js'></script>

</HEAD>
	<BODY>
	<form action="<? echo $_SERVER['../transactions/PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="frmTS">
	  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
	    <tr>
	      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Updated Allowance</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
				<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                    <tr> 
                        <td class="gridDtlLbl">Branch </td>
                        <td class="gridDtlLbl">:</td>
                        <td class="gridDtlVal"> 
                            <? 					
                                $arrBranch = $mirationObj->makeArr($mirationObj->getBrnchArt($compCode),'brnCode','brnDesc','');
                                $mirationObj->DropDownMenu($arrBranch,'empBrnCode',$empBrnCode,$empBrnCode_dis);
                            ?>
                        </td>
                    </tr>
                    <tr> 
                        <td width="18%" class="gridDtlLbl">File</td>
                        <td width="1%" class="gridDtlLbl">:</td>
                        <td width="81%" class="gridDtlVal">
                            <input name="fileUpload" type="file" id="fileUpload"> 
                            <input name="btnUpload" id="btnUpload" value="Upload" class="inputs" type="submit">  	
                        </td>
                    </tr>
		        </table>
				
                <br>
    			<iframe src="<?php echo $rep_fileName; ?>" height="380px;" width="99%">
                	 
                </iframe>
			</td>
		</tr> 
		<tr > 
			<td class="gridToolbarOnTopOnly" colspan="6" height="25">
			</td>
		</tr>
	</table>
	</form>	
	</BODY>
</HTML>
