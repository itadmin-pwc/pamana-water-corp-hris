<?
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("gov_pay.obj.php");

	$govPayObj = new govPayObj($_GET);
	$sessionVars = $govPayObj->getSeesionVars();
	$govPayObj->validateSessions('','MODULES');
	
	
	$pager = new AjaxPager(50,'../../../images/');

	$arrSrch = array('REMITTANCE TYPE','YEAR','MONTH','BANK','OR. NO.','DATE CREATED','PAID BY');
	$arrOrderBy = array('','REMITTANCE TYPE','YEAR','MONTH','BANK','OR. NO.','DATE CREATED','PAID BY');
	
	if($_GET['isSearch']=='1')
	{
		if($_GET['srchType'] == 0)
		{
			if($_GET['txtSrch']!="")
			{
				//Search the code to tblGovAgencies
				$arrgovAgencyType = $govPayObj->getgovAgencyCode($_GET['txtSrch'],1);
				$govAgencyType =  $arrgovAgencyType["agencyCd"];
				
				if($govAgencyType!="")
					$where = " and agencyCd='".$govAgencyType."'";
			}
		}
		
		if($_GET['srchType'] == 1)
		{
			if($_GET['txtSrch']!="")
			{
				$chkifInt = is_numeric($_GET['txtSrch']);
				if($chkifInt==1)
					$where.= " and pdYear='".$_GET['txtSrch']."'";
				else
					$errorMsg = "Search Filter Year, should be year format.";
			}
		}
		
		if($_GET['srchType'] == 2)
		{
			if($_GET['txtSrch']!="")
			{
				$chkifInt = is_numeric($_GET['txtSrch']);
				if($chkifInt==1)
					$where.= " and pdMonth='".$_GET['txtSrch']."'";
				else
					$errorMsg = "Search Filter Month, should be month format e.g. 1 - January, 2 - February.";
			}
		}
			
		if($_GET['srchType'] == 3)
			$where.= " and bnkName like '".$_GET['txtSrch']."%'";
		
		if($_GET['srchType'] == 4)
		{
			if($_GET['txtSrch']!="")
			{
				$where.= " and orNo='".$_GET['txtSrch']."'";
			}
		}
		
		
		if($_GET['srchType'] == 5)
			$where.= " and dateCreated like '".$_GET['txtSrch']."%'";
		
		if($_GET['srchType'] == 6)
			$where.= " and paidBy like '".$_GET['txtSrch']."%'";
	}
	
	$qryIntMaxRec = "Select * from tblGovPayments where compCode='".$_SESSION["company_code"]."' $where and remStatus='A' order by  agencyCd,pdYear DESC,pdMonth ";
	$resIntMaxRec = $govPayObj->execQry($qryIntMaxRec);
	$intMaxRec = $pager->_getMaxRec($resIntMaxRec);
	
	$intLimit = $pager->_limit;
	$intOffset = $pager->_watToDo($_GET['action'],$_GET['offSet'],$_GET['isSearch']);

	$qryGovPayments = "Select * from tblGovPayments where compCode='".$_SESSION["company_code"]."' $where and remStatus='A' 
					order by agencyCd,pdYear DESC,pdMonth Limit $intOffset,$intLimit";//pdYear,
	$qryGovPayments.="";
	
	$resGovPayments = $govPayObj->execQry($qryGovPayments);
	$arrGovPayments = $govPayObj->getArrRes($resGovPayments);
	
	
?>

<HTML>
<head>
</head>
	<BODY>
       <form name="frmGovPaymentList" action="" method="get">
		<div class="niftyCorner">
			<TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
				<tr>
					
			  <td colspan="4" class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png"> Government Payments Entry</td>
				</tr>
				<tr>
					<td class="parentGridDtl">
						<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
						  	<tr>
						  	  <td colspan="10" align="center" class="gridToolbar">
                       			<div align="left"><a href="#" onClick="PopUp('gov_pay_act.php?act=AddGovPay','Add Government Payments','1','government_payments_list_ajax.php','TSCont',<?=$intOffset?>,<?=$_GET['isSearch']?>,'txtSrch','cmbSrch','')" class="anchor" ><img class="anchor" src="../../../images/add.gif">Add Government Payment</a>|
                      				<FONT class="ToolBarseparator"></font>
									  <?
                                        /*if(isset($_GET['action']) != 'load' || isset($_GET['action']) != 'refresh'){
                                            
                                            if(isset($_GET['srchType']) ){ 
                                                $srchType = $_GET['srchType'];
                                            }
                                        }*/
									?>
									Search
									<INPUT type="text" name="txtSrch" id="txtSrch" value="<? if(isset($_GET['txtSrch'])){echo $_GET['txtSrch'];} ?>" class="inputs">
									In
                                    <?=$govPayObj->DropDownMenu($arrSrch,'cmbSrch',$_GET['srchType'],'class="inputs"');?>
									|
                      				<FONT class="ToolBarseparator"></font>
                                   <!-- Order By 
                                   <?=$govPayObj->DropDownMenu($arrOrderBy,'cmbOrderBy','','class="inputs"');?>-->
                                     <INPUT class="inputs" type="button" name="btnSrch" id="btnSrch" value="SEARCH"  onClick="pager('government_payments_list_ajax.php','TSCont','Search',0,1,'txtSrch','cmbSrch','../../../images/')" >                       
                               		  <font color="#FF0000"><blink><?php  echo $errorMsg;?></blink></font>
                                </div></td>
					  	  	</tr>
                            
						  	<tr style="height:20px;">
								<td width="3%" class="gridDtlLbl" align="left">#</td>
                                <td width="10%" class="gridDtlLbl" align="left">REMITTANCE TYPE</td>
                                <td width="5%" class="gridDtlLbl" align="left">YEAR</td>
                                <td width="5%" class="gridDtlLbl" align="left">MONTH</td>
                    
                                <td width="15%" class="gridDtlLbl" align="left">COLLECTING BANK</td>
                                <td width="10%" class="gridDtlLbl" align="right">OR. NO.</td>
                                <td width="10%" class="gridDtlLbl" align="right">TOTAL AMOUNT PAID</td>
                                <td width="10%" class="gridDtlLbl" align="left">DATE CREATED</td>
                                <td width="15%" class="gridDtlLbl" align="left">PAID BY</td>
                                <td width="5%" class="gridDtlLbl" align="left"></td>
							</tr>
							
                            <?php
								if($govPayObj->getRecCount($resGovPayments) > 0)
								{
									$b = 1;
									foreach($arrGovPayments as $arrGovPayments_val)
									{
										$bgcolor = ($i++ % 2) ? "#FFFFFF" : "#F8F8FF";
										$on_mouse = ' onmouseover="this.style.backgroundColor=\'' . '#F0F0F0' . '\';"'
										. ' onmouseout="this.style.backgroundColor=\'' . $bgcolor  . '\';"';	
										
											
										$agencyName = $govPayObj->getgovAgencyCode($arrGovPayments_val["agencyCd"],0);
										$month = $arrGovPayments_val["pdMonth"].'/'.date('m').'/'.date('Y');
										echo "<tr style='height:20px;' bgcolor='".$bgcolor."' $on_mouse>";
											echo '<td class="gridDtlVal">'.$i.'</td>';
											echo '<td class="gridDtlVal">'.$agencyName["agencyDesc"].'</td>';
											echo '<td class="gridDtlVal">'.$arrGovPayments_val["pdYear"].'</td>';
											echo '<td class="gridDtlVal">'.date('M', strtotime($month)).'</td>';
											echo '<td class="gridDtlVal" align="left">'.strtoupper($arrGovPayments_val["bnkName"]).'</td>';
											echo '<td class="gridDtlVal" align="right">'.$arrGovPayments_val["orNo"].'</td>';
											echo '<td class="gridDtlVal" align="right">'.number_format($arrGovPayments_val["totAmtPaid"]).'</td>';
											echo '<td class="gridDtlVal">'.date('m/d/Y', strtotime($arrGovPayments_val["dateCreated"])).'</td>';
											echo '<td class="gridDtlVal" align="left">'.strtoupper($arrGovPayments_val["paidBy"]).'</td>';
											
											//$qrywhere = " agencyCd='".$agencyName["agencyDesc"]."' and pdYear='".$arrGovPayments_val["pdYear"]."' and pdMonth='".$arrGovPayments_val["pdMonth"]."' and orNo='".$arrGovPayments_val["orNo"]."'";
											$seqId = $arrGovPayments_val["seqId"];
											echo "<td align='center'>";
													echo    "<img onclick=\"PopUp('gov_pay_act.php?act=EditGovPay&seqId=$seqId','Edit Government Payments','1','government_payments_list_ajax.php','TSCont','".$intOffset."','".$_GET['isSearch']."','txtSrch','cmbSrch')\" src='../../../images/application_form_edit.png' width='15' height='15' title='Edit Information'>
													<img onclick=\"deleGovPayment('".$seqId."')\" src='../../../images/application_form_delete.png' width='15' height='15' title='Delete Information'>";
													echo  "</td>";	
										
										echo "</tr>";
										$b++;
									}
								}
								else
								{
									echo'
									<tr>
										<td colspan="10" align="center">
											<FONT class="zeroMsg">NOTHING TO DISPLAY</font>
										</td>
									</tr>';
								}
							?>
                            
							<tr>
								<td colspan="20" align="center" class="childGridFooter">
									<? $pager->_viewPagerButton('government_payments_list_ajax.php','TSCont',$intOffset,$_GET['isSearch'],'txtSrch','cmbSrch','');?>								</td>
							</tr>
						</TABLE>
				  </td>
				</tr>
			</TABLE>
            </form>
		</div>
		<? $govPayObj->disConnect();?>
	</BODY>
</HTML>
