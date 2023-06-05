<?
##################################################
session_start(); 
include("../../../includes/db.inc.php");
include("../../../includes/common.php");
include("bank_remit.obj.php");
$bnkRmitObj = new bankRemitObj($_SESSION,$_GET);
$sessionVars = $bnkRmitObj->getSeesionVars();
$bnkRmitObj->validateSessions('','MODULES');

##################################################
	switch ($_GET['action']){
		case 'procAUBDbase':
			
				$path = $_GET['AUBDbase'];
				$file = basename($path);
				$type = explode(".",$file);
/*					include("../../../includes/adodb/adodb.inc.php");
					$db =& ADONewConnection('access');
*/
					$arrPayPeriod = $bnkRmitObj->getPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category'],"AND pdPayable = '".$bnkRmitObj->dateFormat($_GET['payPd'])."'");
					if ($arrPayPeriod['pdStat']=="C") {
						$hist = "hist";
					}
					$compCode = $_SESSION['company_code'];
					$payGrp = $_SESSION['pay_group'];
					$pdYear = $arrPayPeriod['pdYear'];
					$pdNumber = $arrPayPeriod['pdNumber'];
/*					$output_file = "AUB/dbEncrypt.mdb";
					copy('dbEncrypt.mdb', $output_file);
					$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=".realpath($output_file).";Uid=;Pwd=;";
					$db->Connect($dsn,'','');
*/
					/*$arrPayPeriod = $bnkRmitObj->getPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category'],"AND pdPayable = '".$bnkRmitObj->dateFormat($_GET['payPd'])."'");
					if ($arrPayPeriod['pdStat']=="C") {
						$hist = "hist";
					}					
					$qryGetPaySummry = "SELECT ps.compCode, ps.empNo, ps.netSalary, Replace(emp.empAcctNo,'-','') as empAcctNo,emp.empLastName + ' ' + emp.empFirstName AS empFullName, brn.brnLoc, brn.coCtr
									    FROM tblPayrollSummary$hist ps LEFT OUTER JOIN tblEmpMast emp 
									    ON ps.compCode = emp.compCode AND ps.empNo = emp.empNo 
									    LEFT OUTER JOIN tblBranch brn ON ps.compCode = brn.compCode AND brn.brnCode = ps.empBrnCode
										WHERE ps.compCode = '{$_SESSION['company_code']}'
										AND (ps.payGrp    = '{$_SESSION['pay_group']}') 
										AND (ps.pdYear    = '{$arrPayPeriod['pdYear']}') 
										AND (ps.pdNumber  = '{$arrPayPeriod['pdNumber']}') 
										AND (ps.empBnkCd  = '2') ";
					if($_GET['orderBy'] == 1){
					 $qryGetPaySummry .= "ORDER BY emp.empLastName ";
					}
					if($_GET['orderBy'] == 2){
					 $qryGetPaySummry .= "ORDER BY emp.empFirstName ";
					}
					if($_GET['orderBy'] == 3){
					 $qryGetPaySummry .= "ORDER BY ps.empNo ";
					}
					if($_GET['orderBy'] == 4){
					 $qryGetPaySummry .= "ORDER BY ps.empDepCode ";
					}
					$resGetPaySummry = $bnkRmitObj->execQry($qryGetPaySummry);
					$arrGetPaySummry = $bnkRmitObj->getArrRes($resGetPaySummry);
					
					$hoCntr =0;
					$stCntr =0;
					$empChecker = array();
					$totSalary = 0;
					$curdate=date('mdY');
					if (in_array($_SESSION['company_code'],explode(",",PG_PRICE_CLUB))) {
						$filename = "AUB/AUB-$curdate.csv";
						
					}
					if (file_exists($filename)) {
						unlink($filename);					
					}
					$file = fopen($filename,"x+","");
					foreach ((array)$arrGetPaySummry as $getPaySummryVal){
			
						if($getPaySummryVal['netSalary'] != 0){
							
							if(!in_array($getPaySummryVal['empNo'],$empChecker)){
								$totSalary += round($getPaySummryVal['netSalary'],2);
								$qryDbEncrypt = "Insert into tblEncrypt ([AcctNo],[Name],[Salary],[CoCntr]) values ('".str_replace("-","",$getPaySummryVal['empAcctNo'])."','{$getPaySummryVal['empFullName']}',{$getPaySummryVal['netSalary']},".(int)$getPaySummryVal['coCtr'].")";
								$str = "{$getPaySummryVal['empAcctNo']},{$getPaySummryVal['empFullName']},{$getPaySummryVal['netSalary']},".(int)$getPaySummryVal['coCtr'];
								fwrite($file,$str."\r\n");
								//$db->Execute($qryDbEncrypt);
								
								if($getPaySummryVal['brnLoc'] == 'HO'){
									$hoCntr++;
								}
								if($getPaySummryVal['brnLoc'] == 'ST'){
									$stCntr++;
								}
								$empChecker[] = $getPaySummryVal['empNo'];
							}
						}
					}
					fclose($file);*/
					if ($_GET['totSalary'] == "") {
						
						header("Location: http://{$_SERVER['REMOTE_ADDR']}/aub.php?compCode=$compCode&payGrp=$payGrp&pdYear=$pdYear&pdNumber=$pdNumber");
					} else	{
						$totSalary = number_format($totSalary,2);
						echo "
						alert('Total Salary: $totSalary');
						window.open('txtreport.php?act=1&file=$filename');";
					}	
					//echo json_encode($AUBRes);
			exit();
		break;
		case 'genBnkRmt':
			//$bnkRmitObj->getBankRemitData();
			
				if($bnkRmitObj->getBankRemitData() > 0){
					echo 1;
				}
				else{
					echo 0;
				}
				exit();
		break;
		case 'populateDept':
			switch ($_GET['parentObj']){
				case 'cmbDiv':
					echo	$bnkRmitObj->DropDownMenu(
								$bnkRmitObj->makeArr(
									$bnkRmitObj->getDepartment($_SESSION['company_code'],$_GET['divVal'],'','','2'),
									'deptCode','deptDesc',''
								),
								'cmbDept','','class="inputs" style="width:222px;" onchange="populateDept(this.id,\'sectCont\')"'
						  	);
					exit();
				break;
			}
			switch ($_GET['parentObj']){
				case 'cmbDept':
					echo	$bnkRmitObj->DropDownMenu(
								$bnkRmitObj->makeArr(
									$bnkRmitObj->getDepartment($_SESSION['company_code'],$_GET['divVal'],$_GET['deptVal'],'','3'),
									'sectCode','deptDesc',''
								),
								'cmbSect','','class="inputs" style="width:222px;" onchange="populateDept(this.id,\'\')"'
						  	);				
					exit();
				break;
			}
			exit();
		break; 
		case "txtreport":
			$compName=$bnkRmitObj->getCompany($_SESSION['company_code']);
				$bnkdata=$bnkRmitObj->getBankRemitDataMTC(1,MTC_BANK_CODE);
					/*$curdate=date('mdY');
					$filename = "C:/ETPS/payroll.dat";
					if (file_exists($filename)) {
						unlink($filename);					
					}
					$file = fopen($filename,"x+","");
					$recCount = 0;
					$totSal = 0;
					foreach ($bnkdata as $bnkValue) {
						$recCount++;
						//fixed value='2'
						$str ="2";
						
						//Company's depostory branch code (source branch)
						$str .="014";
						
						//bank code value="2" for (MBTC)
						$str .="26";
						
						//currency
						$str .="001";
						
						//payroll accounts' branch code (branch to be credited)
						$str .="014";
						
						//fixed value='0000000'
						$str .="0000000";
						
						//company name
						$str .=strtoupper($compName['compName']);
						if (strlen($compName['compName']) < 40) {
							$cmpnamelength = 40 - strlen($compName['compName']);
							$ctr=1;
							$space="";
							while ($ctr <= $cmpnamelength) {
								$space .=" ";
								$ctr++;
							}
						$str .=	$space;
						}
						
						//employee's bankaccout
						$str .=str_replace("-","",$bnkValue['empAcctNo']);
						
						//employee's salary
						$salary=str_replace(",","",(string)number_format($bnkValue['netSalary'],2));
						$totSal += round($salary,2);
						$salary=str_replace(".","",$salary);
						$salarylength=strlen($salary);
						if (strlen($salary) < 15) {
							$salarylength=15 - strlen($salary);
							$strzero="";
							$ctr=1;
							while ($ctr <= $salarylength) {
								$strzero .="0";
								$ctr++;
							}
							
							$str .= $strzero . $salary;
						}
						
						//fixed value="9"
						$str .="9";
						
	
						if (strlen($_SESSION['company_code']) < 5) {
							$compCodelength=5 - strlen($_SESSION['company_code']);
							$strzero="";
							$ctr=1;
							while ($ctr <= $compCodelength) {
								$strzero .="0";
								$ctr++;
							}
							
							$str .= $strzero . $_SESSION['company_code'];
						}
						$str .=date('mdY');
						
						fwrite($file,$str."\r\n");
					}
					//total salary
					$totSalary = number_format($totSal,2);
					if (strlen($totSal) < 15) {
						$totSallength=15 - strlen($totSal);
						$strzero="";
						$ctr=1;
						while ($ctr <= $totSallength) {
							$strzero .="0";
							$ctr++;
						}
						$totSal = str_replace(".","",$totSal);
						$totSal = $strzero . $totSal;
					}
					
					//record count
					if (strlen($recCount) < 6) {
						$recCountlength=6 - strlen($recCount);
						$strzero="";
						$ctr=1;
						while ($ctr <= $recCountlength) {
							$strzero .="0";
							$ctr++;
						}
						
						$recCount = $strzero . $recCount;
					}										
					fwrite($file,$recCount.$totSal."\r\n");
					fclose($file);*/
						if ($_GET['totSalary'] == "") {
							echo 'wil';
							header("Location: http://{$_SERVER['REMOTE_ADDR']}/mbtc.php?compCode=$compCode&payGrp=$payGrp&pdYear=$pdYear&pdNumber=$pdNumber");
						} else	{
							$totSalary = number_format($_GET['totSalary'],2);
							echo "
							alert('Total Salary: $totSalary');";
						}
			exit();
		break;		
	}
	
?>
<HTML>
	<HEAD>
<TITLE>
	<?=SYS_TITLE?>
</TITLE>
<style>
@import url('../../style/main_emp_loans.css');.style1 {font-family: Verdana}
</style>
<STYLE TYPE="text/css" MEDIA="screen">
@import url("../../../includes/calendar/calendar-blue.css");.style3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
}
</STYLE>
<script type='text/javascript' src='../../../includes/jSLib.js'></script>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		

</HEAD>
	<BODY>
	<form name="frmBnkRmit" id="frmBnkRmit" method="POST" action="<? echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
	  <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
	    <tr>
	      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Bank Remittance</td>
		</tr>
		<tr>
			<td class="parentGridDtl" >
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
		          <tr> 
		            <td width="18%" class="gridDtlLbl">Report Type</td>
		            <td width="1%" class="gridDtlLbl">:</td>
		            <td width="81%" class="gridDtlVal">
	            	<?
		            		$bnkRmitObj->DropDownMenu(array('','1'=>'PDF','2'=>'TEXT FILE'),'trnType',$trnType,'class="inputs" onchange="reptType(this.value)"'); 
		            	?></td>
	          </tr>
		          <tr>
		            <td class="gridDtlLbl">With Name?</td>
		            <td class="gridDtlLbl">&nbsp;</td>
		            <td class="gridDtlVal"><table width="200">
                      <tr>
                        <td class="gridDtlVal"><span class="style1">
                          <label>
                          <input type="radio" name="fname" value="1" id="fname_0">
                            Yes</label>
                        </span></td>
                      </tr>
                      <tr>
                        <td class="gridDtlVal"><span class="style1">
                          <label>
                          <input name="fname" type="radio" id="fname_1" value="0" checked>
                            No</label>
                        </span></td>
                      </tr>
                    </table></td>
              </tr>
		          <tr>
		            <td class="gridDtlLbl">Credit Date</td>
		            <td class="gridDtlLbl">&nbsp;</td>
		            <td class="gridDtlVal"><INPUT type="text" name="txtcDate" id="txtcDate" class="inputs" readonly >
	                <a href="#"><img src="../../../images/cal_new.gif" alt="" name="imgcDate" id="imgcDate" style="cursor: pointer;position:relative;top:3px;border:none;" title="Date Granted" type="image" /></a></td>
              </tr>
		          <tr> 
		            <td width="18%" class="gridDtlLbl">Emp. #</td>
		            <td width="1%" class="gridDtlLbl">:</td>
		            <td width="81%" class="gridDtlVal">
		            	<INPUT type="text" name="txtEmpNo" id="txtEmpNo" class="inputs" disabled>		            </td>
		          </tr>
		          <tr> 
		            <td class="gridDtlLbl">Employee Name </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal">
		            	<INPUT type="text" name="txtEmpName" id="txtEmpName" class="inputs" size="40" disabled>
		            	<?
		            		$bnkRmitObj->DropDownMenu(array('1'=>'LAST NAME','2'=>'FIRST NAME','3'=>'MIDDLE NAME'),'nameType',$nameType,'class="inputs" disabled'); 
		            	?>		            </td>
		          </tr>
		          <tr> 
		            <td class="gridDtlLbl">Division </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
						<?
							$bnkRmitObj->DropDownMenu(
								$bnkRmitObj->makeArr(
									$bnkRmitObj->getDepartment($_SESSION['company_code'],$division,'','','1'),
									'divCode','deptDesc',''
								),
								'cmbDiv',$division,'class="inputs" style="width:222px;" onchange="populateDept(this.id,\'deptCont\')" disabled'
						  	); 
						?>		            </td>
		          </tr>
		          <tr> 
		            <td class="gridDtlLbl">Department </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
						<font id="deptCont">
							<?
								$bnkRmitObj->DropDownMenu(
									$bnkRmitObj->makeArr(
										$bnkRmitObj->getDepartment($_SESSION['company_code'],$division,$department,'','2'),
										'deptCode','deptDesc',''
									),
								'cmbDept',$department,'class="inputs" style="width:222px;" onchange="populateDept(this.id,\'sectCont\')" disabled'
								);
							?>
						</font>					</td>
		          </tr>
		          <tr> 
		            <td class="gridDtlLbl">Section </td>
		            <td class="gridDtlLbl">:</td>
		            	<td class="gridDtlVal"> 
							<font id="sectCont">
								<?
									$bnkRmitObj->DropDownMenu(
										$bnkRmitObj->makeArr(
											$bnkRmitObj->getDepartment($_SESSION['company_code'],$division,$department,$section,'3'),
											'sectCode','deptDesc',''
										),
										'cmbSect',$section,'class="inputs" style="width:222px;" onchange="populateDept(this.id,\'\')" disabled' 
									);
								?>
							</font>		              </td>
		          </tr>
	<!--			  <tr> 
		            <td class="gridDtlLbl">Group </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal" colspan="4"> 
						<?
							$bnkRmitObj->DropDownMenu(array('1'=>'GROUP 1','2'=>'GROUP 2'),'groupType',$groupType,'class="inputs" disabled'); 
						?>
		            </td>
		          </tr>
				  <tr> 
		            <td class="gridDtlLbl">Category </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
		              <? 			
							$a = $bnkRmitObj->makeArr($bnkRmitObj->getCatArt($_SESSION['company_code']),'payCat','payCatDesc','-- All --');
							$bnkRmitObj->DropDownMenu($a,'catType',$catType,'class="inputs" disabled');
					  ?>
		            </td>
		          </tr>-->
		          <tr> 
				  <tr> 
		            <td class="gridDtlLbl">Branch</td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
						<? 	
							  $sqlbranch = "Select brnCode,brnDesc from tblBranch where compCode='{$_SESSION['company_code']}' AND (brnDefGrp='{$_SESSION['pay_group']}' or glCodeStr=901) order by brnDesc";
							  $arrBranch = $bnkRmitObj->getArrRes($bnkRmitObj->execQry($sqlbranch));
						
							$bnkRmitObj->DropDownMenu(
								$bnkRmitObj->makeArr($arrBranch
									,'brnCode','brnDesc','')
								,'cmbBranch','','class="inputs" disabled'
							);
						?>					</td>
		          </tr>
				  <tr> 
		            <td class="gridDtlLbl">Bank </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
						<? 	
							$bnkRmitObj->DropDownMenu(
								$bnkRmitObj->makeArr($bnkRmitObj->getPayBank($_SESSION['company_code'])
									,'bankCd','bankDesc','')
								,'cmbBank','','class="inputs" disabled'
							);
						?>					</td>
		          </tr>
				  <tr> 
		            <td class="gridDtlLbl">Payroll Period </td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
						<? 	
							$arrPayPd = $bnkRmitObj->makeArr($bnkRmitObj->getPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category'],''),'pdPayable','pdPayable','');				
							
							$qryGetCurrPeriod = $bnkRmitObj->getPeriod($_SESSION['company_code'],$_SESSION['pay_group'],$_SESSION['pay_category'],"AND pdStat = 'O' ");
							$payPd = $qryGetCurrPeriod['pdPayable'];
							$bnkRmitObj->DropDownMenu($arrPayPd,'payPd',$payPd,'class="inputs" disabled');
						?>					</td>
		          </tr>
				   <tr> 
		            <td class="gridDtlLbl">Order By</td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
		              <?
						$bnkRmitObj->DropDownMenu(array('1'=>'EMPLOYEE LAST NAME','2'=>'EMPLOYEE FIRST NAME','3'=>'EMPLOYEE NUMBER','4'=>'DEPARTMENT'),'orderBy',$orderBy,'class="inputs" disabled'); 
					  ?>					</td>
		          </tr>
				   <tr style="display:none;" id="TRAUBDB"> 
		            <td class="gridDtlLbl">AUB Database</td>
		            <td class="gridDtlLbl">:</td>
		            <td class="gridDtlVal"> 
						<INPUT type="file" name="AUBDbase" id="AUBDbase"> <FONT color="Red" id="fileIndicator">AUB purpose only</FONT>					</td>
		          </tr>		          
	        </table>
			<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
	           				 <INPUT type="button" name="btnGenRep" id="btnGenRep" value="PDF" class="inputs" onClick="genRep('genBnkRmt')" disabled>
	           				 <INPUT type="button" name="btnAubBnkAd" id="btnAubBnkAd" value="AUB" class="inputs" disabled onClick="return genAUB()">
	           				 <INPUT type="button" name="btnMbtcBnkAd" id="btnMbtcBnkAd" onClick="txtreport('genBnkRmt')" value="MBTC" class="inputs" disabled>
	           				 <INPUT type="button" name="btnBocBnk" id="btnBocBnk" onClick="genBOC()" value="BOC" class="inputs" disabled>
          			  		 <INPUT type="button" name="btnBdoBnk" id="btnBdoBnk" onClick="genBDO()" value="BDO" class="inputs" disabled>
					      <INPUT type="button" name="btnCbcBnk" id="btnCbcBnk" onClick="genCBC()" value="CBC" class="inputs" disabled>
						  <INPUT type="button" name="btnAUTHDEBIT" id="btnAUTHDEBIT" onClick="genAUTH()" value="AUTHORITY TO DEBIT" class="inputs" disabled>
						</CENTER>
					</td>
				  </tr>
			  </table> 
		</td>
		</tr> 
		<tr > 
			<td class="gridToolbarOnTopOnly" colspan="6" height="25">
	
			</td>
		</tr>
	</table>
<INPUT type="hidden" name="hdnFrmPd" id="hdnFrmPd" value="<?=$bnkRmitObj->dateFormat($qryGetCurrPeriod['pdFrmDate'])?>">
<INPUT type="hidden" name="hdnToPd" id="hdnToPd" value="<?=$bnkRmitObj->dateFormat($qryGetCurrPeriod['pdToDate'])?>">
<INPUT type="hidden" name="hdnPdYear" id="hdnPdYear" value="<?=$qryGetCurrPeriod['pdYear']?>">
<INPUT type="hidden" name="hdnPdNum" id="hdnPdNum" value="<?=$qryGetCurrPeriod['pdNumber']?>">
<INPUT type="hidden" name="aubProc" id="aubProc" >
<INPUT type="hidden" name="aubDbPath" id="aubDbPath">
	</form>
</BODY>
</HTML>
<SCRIPT>
<? if($_GET['totSalary']!="") {?>
		reptType(2);
		alert('Data Successfully Migrated.\n Total Salary <?=$_GET['totSalary']?>');
<? } ?>
	function populateDept(parentObjID,ReplaceObjId){

		var divVal = $F('cmbDiv'); 
		var divDept = $F('cmbDept');
		var divSect = $F('cmbSect');
		if(parentObjID == 'cmbDiv'){
			$('cmbSect').value=0;				 	
		}
		if(parentObjID == 'cmbDept'){
			if(divVal == 0){
				$('cmbDept').value=0;
				alert('Select Division First');
				$('cmbDiv').focus();
				return false;
			}
		}
		if(parentObjID == 'cmbSect'){
			if(divDept == 0){
				$('cmbSect').value=0;
				alert('Select Department First');
				$('cmbDept').focus();
				return false;
			}			
		}
		
		var params = '?action=populateDept&parentObj='+parentObjID+"&divVal="+divVal+"&deptVal="+divDept+"&sectVal="+divSect;

		var url = '<?=$_SERVER['PHP_SELF']?>'+params;

		var a = new Ajax.Request(url,{
			method : 'get',
			onComplete : function (req){
				$(ReplaceObjId).innerHTML=req.responseText;	
			},
			onCreate : function (){
				$(ReplaceObjId).innerHTML="<img src='../../../images/wait.gif'>";
			}
		});
	}
	
	function genRep(act){
		
		var frm = $('frmBnkRmit').serialize(true);
		if(frm['trnType'] == 0){
				alert('Report Type is Required');
				$('trnType').focus();
				return false;				
		}
		

		if(frm['cmbBank'] == 0){
				alert('Bank is Required');
				$('cmbBank').focus();
				return false;			
		}
		if(frm['payPd'] == 0){
				alert('Payroll Period is Required');
				$('payPd').focus();
				return false;			
		}
		
		new Ajax.Request('<?=$_SERVER['PHP_SELF']?>?action='+act,{
			method : 'get',
			parameters : $('frmBnkRmit').serialize(),
			onComplete : function(req){
				var intRes = parseInt(req.responseText);
				if(intRes == 1){
					window.open('bank_remit_pdf.php?'+$('frmBnkRmit').serialize());						
				}
				else{
					alert('NO RECORD FOUND');
					return false;
				}
			},
			onCreate : function(){
				$('btnGenRep').disabled=true;
			},
			onSuccess : function(){
				$('btnGenRep').disabled=false;
			}
		});
/*		if(frm['cmbDiv'] != 0){
			if(frm['cmbDept'] == 0){
				alert('Department is Required');
				$('cmbDept').focus();
				return false;
			}
			if(frm['cmbSect'] == 0){
				alert('section is Required');
				$('cmbSect').focus();
				return false;
			}
		}*/
	}
	
	function reptType(val){
		if(val == 0){
			$('txtEmpNo').disabled=true;
			$('txtEmpName').disabled=true;
			$('nameType').disabled=true;
			$('cmbDiv').disabled=true;
			$('cmbDept').disabled=true;
			$('cmbSect').disabled=true;
			$('cmbBranch').disabled=true;
			$('cmbBank').disabled=true;
			$('payPd').disabled=true;
			$('orderBy').disabled=true;	
			$('btnGenRep').disabled=true;		
			$('btnAubBnkAd').disabled=true;		
			$('btnMbtcBnkAd').disabled=true;
			$('btnBdoBnk').disabled=true;
			$('btnBocBnk').disabled=true;	
			$('btnAUTHDEBIT').disabled=true;	
		}
		if(val == 1){
			$('txtEmpNo').disabled=false;
			$('txtEmpName').disabled=false;
			$('nameType').disabled=false;
			$('cmbDiv').disabled=false;
			$('cmbDept').disabled=false;
			$('cmbSect').disabled=false;
			$('cmbBranch').disabled=false;
			$('cmbBank').disabled=false;
			$('payPd').disabled=false;
			$('orderBy').disabled=false;
			$('btnGenRep').disabled=false;
			$('btnAubBnkAd').disabled=true;		
			$('btnMbtcBnkAd').disabled=true;
			$('btnBocBnk').disabled=true;	
			$('btnBdoBnk').disabled=true;	
			$('btnCbcBnk').disabled=true;	
			$('btnCbcBnk').disabled=true;
			$('btnAUTHDEBIT').disabled=true;	
		}
		if(val == 2){
			$('txtEmpNo').disabled=true;
			$('txtEmpName').disabled=true;
			$('nameType').disabled=true;
			$('cmbDiv').disabled=true;
			$('cmbDept').disabled=true;
			$('cmbSect').disabled=true;
			$('cmbBranch').disabled=true;
			$('cmbBank').disabled=true;
			$('payPd').disabled=false;
			$('orderBy').disabled=false;			
			$('btnGenRep').disabled=true;
			$('btnAubBnkAd').disabled=false;	
			$('btnMbtcBnkAd').disabled=false;	
			$('btnBocBnk').disabled=false;	
			$('btnBdoBnk').disabled=false;
			$('btnCbcBnk').disabled=false;
			$('btnAUTHDEBIT').disabled=false;
		}
	}
	
	function genAUB(){
		
		
		if($F('payPd') == 0){
			alert('Payroll Period is Required');
			$('payPd').focus();
			return false;
		}
		location.href='http://<?php echo $_SERVER['REMOTE_ADDR']; ?>/aub.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value;
/*		new Ajax.Request(url,{
			method : 'get',
			parameters : $('frmBnkRmit').serialize(),
			onComplete : function (req){
				eval(req.responseText);
			},
			onCreate : function (){
				$('fileIndicator').innerHTML="Loading...";
			},
			onSuccess : function(){
				$('fileIndicator').innerHTML="AUB purpose only";
			}
		});
*/	}

	function genBOC(){
			
			
			if($F('payPd') == 0){
				alert('Payroll Period is Required');
				$('payPd').focus();
				return false;
			}
			location.href='http://<?php echo $_SERVER['REMOTE_ADDR']; ?>/boc.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value;
	/*		new Ajax.Request(url,{
				method : 'get',
				parameters : $('frmBnkRmit').serialize(),
				onComplete : function (req){
					eval(req.responseText);
				},
				onCreate : function (){
					$('fileIndicator').innerHTML="Loading...";
				},
				onSuccess : function(){
					$('fileIndicator').innerHTML="AUB purpose only";
				}
			});
	*/	}
	
	
	function genBDO()
	{
			if($F('payPd') == 0){
				alert('Payroll Period is Required');
				$('payPd').focus();
				return false;
			}
			location.href='http://<?php echo $_SERVER['REMOTE_ADDR']; ?>/bdo.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value;

	}

	function genCBC()
	{
			if($F('txtcDate') == ""){
				alert('Credit Date is Required');
				$('payPd').focus();
				return false;
			}			

			if($F('payPd') == 0){
				alert('Payroll Period is Required');
				$('payPd').focus();
				return false;
			}
//			location.href='http://192.168.110.24/cbc.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value;
			window.open('cbc_pdf.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value+'&cDate='+$('txtcDate').value);		

	}
	function txtreport(act) {
		var frm = $('frmBnkRmit').serialize(true);
		if(frm['payPd'] == 0){
				alert('Payroll Period is Required');
				$('payPd').focus();
				return false;			
		}
			if($F('payPd') == 0){
				alert('Payroll Period is Required');
				$('payPd').focus();
				return false;
			}
			
			<? if($_SESSION['company_code']<>'2') { ?>
				location.href='http://<?php echo $_SERVER['REMOTE_ADDR']; ?>/mbtc.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value;
			<? }else{ ?>
				location.href='http://<?php echo $_SERVER['REMOTE_ADDR']; ?>/mbtc.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value;
	
			<?}?>	
	}	
	
	function genAUTH(){
		if($F('payPd') == 0){
			alert('Payroll Period is Required');
			$('payPd').focus();
			return false;
		}
		window.open('authDebit_pdf.php?<?='compCode='.$_SESSION['company_code'].'&payGrp='.$_SESSION['pay_group'].'&payCat='.$_SESSION['pay_category']?>&payPd='+$('payPd').value);		
	}
</SCRIPT>
<SCRIPT>
		Calendar.setup({
				  inputField  : "txtcDate",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgcDate"       // ID of the button
			}
		)
		
</SCRIPT>