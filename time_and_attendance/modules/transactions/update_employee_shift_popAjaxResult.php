<?php
	/*
		Created By	:	Genarra Jo-Ann S. Arong
		Date Created : 	08252010
	*/
	
	session_start();
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("../../../includes/pager.inc.php");
	include("transaction_obj.php");


	$UpdateShiftTypeObj = new transactionObj();
	$sessionVars = $UpdateShiftTypeObj->getSeesionVars();
	$UpdateShiftTypeObj->validateSessions('','MODULES');

	$arr_Day = array(1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday');
	
	if($brnCode_View ==""){
		$queryBrnches = "Select empNo,tblUB.brnCode as brnCode, brnDesc from tblUserBranch tblUB, tblBranch as tblbrn
							where tblUB.brnCode=tblbrn.brnCode and tblUB.compCode='".$_SESSION["company_code"]."' and tblbrn.compCode='".$_SESSION["company_code"]."'
							and empNo='".$_SESSION['employee_number']."'
							order by brnDesc";
		
		$resBrnches = $UpdateShiftTypeObj->execQry($queryBrnches);
		$arrBrnches = $UpdateShiftTypeObj->getArrRes($resBrnches);
		$arrBrnch = $UpdateShiftTypeObj->makeArr($arrBrnches,'brnCode','brnDesc','All');
	}
	
	if ($_SESSION['user_level'] == 3) 
	{
		$userinfo = $UpdateShiftTypeObj->getUserHeaderInfo($_SESSION['employee_number'],$_SESSION['employee_id']);
		$and = ($_GET['isSearch'] == 1) ? 'AND' : 'Where';	
		$brnCodelist = " AND empMast.empNo<>'".$_SESSION['employee_number']."' and empbrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	elseif ($_SESSION['user_level'] == 2) 
	{
		$brnCodelist = " AND empbrnCode IN (Select brnCode from tblUserBranch where empNo='{$_SESSION['employee_number']}' AND compCode='{$_SESSION['company_code']}')";
	}
	
	$array_userPayCat = explode(',', $_SESSION['user_payCat']);
	
	if(in_array(9,$array_userPayCat))
	{
		$where_empStat = "";
	}
	else
	{
		$where_empStat = " AND empStat NOT IN('RS','IN','TR')";
	}
	
	$user_payCat_view = " AND empPayCat IN ({$_SESSION['user_payCat']})";
	
	
	$qryDisEmp = "SELECT empMast.empNo, empLastName, empFirstName, empMidName, shiftDesc
					FROM tblEmpMast empMast, tblTK_EmpShift empShift, tblTK_ShiftHdr empShiftHdr
					 WHERE empMast.compCode = '{$sessionVars['compCode']}'  and empShift.compCode = '{$sessionVars['compCode']}' 
					 and empShiftHdr.compCode = '{$sessionVars['compCode']}'
					 and empMast.empNo=empShift.empNo and empShift.shiftCode = empShiftHdr.shiftCode
					 $brnCodelist $where_empStat
					 and empPayCat<>0 $user_payCat_view";
					 if ($_GET['brnCd']!=0) 
					{
						$qryDisEmp .= " AND empbrnCode='".$_GET["brnCd"]."' ";
					}
	$qryDisEmp .= " order by empLastName, empFirstName";
	$resDisEmp = $UpdateShiftTypeObj->execQry($qryDisEmp);
	$arrDisEmp = $UpdateShiftTypeObj->getArrRes($resDisEmp);


	$arr_ShiftCode_Dtl = $UpdateShiftTypeObj->getTblData("tblTK_ShiftDtl", " and shftCode='".$_GET["shiftCode"]."'", "", "");
	
	switch($_GET["action"])
	{
		
		case "AppMassUpdate":
			$appMassUpdate =  $UpdateShiftTypeObj->MassUpdateSchedule($brnCodelist,$where_empStat,$user_payCat_view,$_GET);	
		break;
	}
	
	$arrayDay = array(1=>'Mon', 2=>'Tue', 3=>'Wed', 4=>'Thu', 5=>'Fri', 6=>'Sat', 7=>'Sun');
?>


<TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
               
    <tr>
        <td align='center' colspan='6' class='prevEmpHeader'>
           Mass Update Employee Shift Schedule
        </td>  
    </tr> 
    
    <tr style="height:20px;">
        <td width='15%' class='gridDtlLbl' align='left'>Select Branch </td>
        <td width='1%' class='gridDtlLbl' align='center'>:</td>
        <td  width='35%' class='gridDtlVal'>
             <?php if($brnCode_View=="")?> <? if($brnCode_View ==""){echo $UpdateShiftTypeObj->DropDownMenu($arrBrnch,'brnCd',$_GET['brnCd'],'class="inputs" onChange=getListofEmp();');}?>
          
        </td>
        
        <td width='15%' class='gridDtlLbl' align='left'>Update Shift Code To</td>
        <td width='1%' class='gridDtlLbl' align='center'>:</td>
            
        <td  width='35%' class='gridDtlVal'>
           <?php
                $arrShifts = $UpdateShiftTypeObj->makeArr($UpdateShiftTypeObj->getListShift(),'shiftCode','shiftDesc','');
                $UpdateShiftTypeObj->DropDownMenu($arrShifts,'shiftcode',$_GET["shiftCode"],' onChange=getShiftCodeDetail();  '.((($_GET["disShifts"]=='1')&&(sizeof($arrDisEmp)!=""))?"":"disabled").'' );
            ?>
        </td>
    </tr>
    
    <tr>
        <td colspan="3">
            <div id="Panel1" style="height: 200px; width:99%; overflow-y: scroll;">
                <?php
                    echo "<table border='1' width='100%' cellpadding='1' cellspacing='1' style='border-collapse:collapse;'>";
                        echo "<tr style='height:25px;'>";
                            echo "<td class='fntTblHdr' style='font-size:11px;'>Employee Name</td>";
                            echo "<td class='fntTblHdr' style='font-size:11px;'>Shift Description</td>";
                        echo "</tr>";
                        
                        if($arrDisEmp!="")
                        {
                            foreach($arrDisEmp as $arrDisEmp_val)
                            {
                                echo "<tr style='height:20px;' class='gridToolbar'>";
                                    echo "<td>".str_replace("Ñ","&Ntilde;", $arrDisEmp_val["empLastName"]).", ".$arrDisEmp_val["empFirstName"]."</td>";
                                    echo "<td>".$arrDisEmp_val["shiftDesc"]."</td>";
                                echo "<tr>";
                            }
                        }
                    echo "</table>";
                ?>
            </div>
        
        </td>
        
        <td colspan="3">
            <div id="Panel1" style="height: 200px; width:99%; overflow-y: scroll;">
                <?php
                    echo "<table border='1' width='100%' cellpadding='1' cellspacing='1' style='border-collapse:collapse;'>";
                        echo "<tr style='height:25px;'>";
                            echo "<td class='fntTblHdr' style='font-size:11px;'>Day</td>";
                            echo "<td class='fntTblHdr' style='font-size:11px;'>Time In</td>";
							echo "<td class='fntTblHdr' style='font-size:11px;'>Lunch Out</td>";
							echo "<td class='fntTblHdr' style='font-size:11px;'>Lunch In</td>";
							echo "<td class='fntTblHdr' style='font-size:11px;'>Brk. Out</td>";
							echo "<td class='fntTblHdr' style='font-size:11px;'>Brk. In</td>";
							echo "<td class='fntTblHdr' style='font-size:11px;'>Time Out</td>";
                        echo "</tr>";
                        
                        if($arr_ShiftCode_Dtl!="")
                        {
                            foreach($arr_ShiftCode_Dtl as $arr_ShiftCode_Dtl_val)
                            {
                                echo "<tr style='height:20px;' class='gridToolbar'>";
                                    echo "<td>".$arrayDay[$arr_ShiftCode_Dtl_val["dayCode"]]."</td>";
                                    echo "<td>".$arr_ShiftCode_Dtl_val["shftTimeIn"]."</td>";
									echo "<td>".$arr_ShiftCode_Dtl_val["shftLunchOut"]."</td>";
									echo "<td>".$arr_ShiftCode_Dtl_val["shftLunchIn"]."</td>";
									echo "<td>".$arr_ShiftCode_Dtl_val["shftBreakOut"]."</td>";
									echo "<td>".$arr_ShiftCode_Dtl_val["shftBreakIn"]."</td>";
									echo "<td>".$arr_ShiftCode_Dtl_val["shftTimeOut"]."</td>";
									
                                echo "<tr>";
                            }
                        }
                    echo "</table>";
                ?>
            </div>
        
        </td>
    </tr>
    
    <tr>
        <td colspan="6"  class='childGridFooter' align="center">
            <input type='button' class= 'inputs' name='btnMassUpdate' value='Mass Update the Schedule' <?php echo ((($_GET["disShifts"]=='1')&&(sizeof($arrDisEmp)!="")&&(sizeof($arr_ShiftCode_Dtl)!=""))?"":"disabled"); ?> onClick="saveMssUpdate();">
        </td>
    </tr>
</TABLE>
<INPUT type="hidden" name="hdnTrnsType" id="hdnTrnsType" value="<?=$hdnTrnsType?>">
<? $UpdateShiftTypeObj->disConnect();?>

<script>
	
</script>

