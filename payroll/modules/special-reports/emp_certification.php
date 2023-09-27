<?
##################################################    $inqTSObj->DropDownMenu(array('S'=>'Sss','PAG'=>'Pag-Ibig','PH'=>'Philhealth'),'conType',$conType,$conType_dis); 
	session_start(); 
	include("../../../includes/db.inc.php");
	include("../../../includes/common.php");
	include("timesheet_obj.php");
	
	$inqTSObj = new inqTSObj();
	$sessionVars = $inqTSObj->getSeesionVars();
	$inqTSObj->validateSessions('','MODULES');
	$compCode = $_SESSION['company_code'];
	include("timesheet.trans.php");
##################################################
    
?>
<HTML>
    <HEAD>
        <TITLE>
            <?=SYS_TITLE?>
        </TITLE>
        <style>@import url('../../style/main_emp_loans.css');</style>
        <script type='text/javascript' src='../../../includes/jSLib.js'></script>
        <script type='text/javascript' src='../../../includes/prototype.js'></script>
       <!--calendar lib-->
        <script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
        <script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>     
        <STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
        <!--end calendar lib  <script type='text/javascript' src='timesheet_js.js'></script> -->
       
    </HEAD>
    <BODY>
        <form name="frmTS" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" name="empDiv" id="empDiv" value="0">
            <input type="hidden" name="empDept" id="empDept" value="0">
            <input type="hidden" name="hide_empDept" id="hide_empDept" value="0">
            <input type="hidden" name="empSect" id="empSect" value="0">
            <input type="hidden" name="hide_empSect" id="hide_empSect" value="0">
            <input type="hidden" name="orderBy" id="orderBy" value="0">
            
            <table cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
                <tr>
                    <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Employee Certification of Government Contribution</td>
                </tr>
            
                <tr>
                    <td class="parentGridDtl" >
                        <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >
                            <tr > 
                                <td class="gridToolbar" colspan="6"> 
                                    <input name="hide_option" type="hidden" id="hide_option" value="<? echo $option_menu; ?>"> 
                                    <? echo $new_; ?>&nbsp;&nbsp;<? echo $refresh_; ?> <input name='updateFlag' type='hidden' id='updateFlag'> 
                                    <input name='fileName' type='hidden' id='fileName' value="emp_certification.php">            
                                </td>
                            </tr>
                            
                           
                            <tr> 
                                <td width="18%" class="gridDtlLbl">Emp. #</td>
                                <td width="1%" class="gridDtlLbl">:</td>
                                <td width="81%" class="gridDtlVal">
                                    <input class="inputs" name="empNo" id="empNo" value="<? echo $empNo; ?>" <? echo $empNo_dis; ?> type="text" size="12" maxlength="11" onKeyPress="return isNumberInputEmpNoOnly(this, event,'searchTS2');"> 
                                </td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Employee Name </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal">
                                    <input class="inputs" name="empName" id="empName" value="<? echo htmlspecialchars($empName); ?>" <? echo $empName_dis; ?> type="text" size="25" maxlength="50">
                                    
                                </td>
                            </tr>
            
                            <tr > 
                                <td  class="gridToolbarWithColor" colspan="6">
                                    <center></center>
                                </td>
                            </tr>
                            
                            <tr> 
                                <td class="gridDtlLbl">Contribution Type </td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal" colspan="4"> 
                                <?  
                                    $inqTSObj->DropDownMenu(array('S'=>'Sss','PAG'=>'Pag-Ibig','PH'=>'Philhealth'),'conType',$conType,$conType_dis); 
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="gridDtlLbl">From</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(document.frmTS.monthto.value,document.frmTS.monthto.id,this.value );" class='inputs' name='monthfr' id='monthfr' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgfrDate" id="imgfrDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                            </tr>
                            
                             <tr>
                                <td class="gridDtlLbl">To</td>
                                <td class="gridDtlLbl">:</td>
                                <td class="gridDtlVal"><input value="" type='text' onChange="valDateStartEnd(document.frmTS.monthfr.value,document.frmTS.monthfr.id,this.value);" class='inputs' name='monthto' id='monthto' maxLength='10' readonly size="10"/>
                                  <a href="#"><img name="imgtoDate" id="imgtoDate" src="../../../images/cal_new.png" title="From Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a></td>
                            </tr>
                            
            
                            
                        </table>
                        <br>
                        <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
                            <tr>
                                <td>
                                    <CENTER>
                                        <input type="button" class="inputs" name="btnempCert" id="btnempCert" <? echo $btnempCert_dis; ?> value="Generate Certification Report" onClick="valSearchTS();">
                                    </CENTER>
                                </td>
                            </tr>
                        </table> 
                    </td>
                </tr> 
            
       </table>
   </form>
</BODY>
</HTML>
<script>
    Calendar.setup({
              inputField  : "monthfr",      // ID of the input field
              ifFormat    : "%m/%d/%Y",          // the date format
              button      : "imgfrDate"       // ID of the button
        }
    )   
    Calendar.setup({
              inputField  : "monthto",      // ID of the input field
              ifFormat    : "%m/%d/%Y",          // the date format
              button      : "imgtoDate"       // ID of the button
        }
    )  

    // JavaScript Document
    function isNumberInputEmpNoOnly(field, event) {
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var hide_empDept=document.frmTS.hide_empDept.value;
        var hide_empSect=document.frmTS.hide_empSect.value;
        var optionId=document.frmTS.hide_option.value;
        var fileName=document.frmTS.fileName.value;
        var orderBy=document.frmTS.orderBy.value;
        
        var conType = document.frmTS.conType.value;
        var monthto=document.frmTS.monthto.value;
        var monthfr=document.frmTS.monthfr.value;
        
    
    
      var key, keyChar;
      if (window.event)
        key = window.event.keyCode;
      else if (event)
        key = event.which;
      else
        return true;
        
    
    // Check for special characters like backspace
    if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
        if (key == 13) {
        new Ajax.Request(
               'timesheet_ajax.php?inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+"&thisValue=verifyEmp&monthfr="+monthfr+'&monthto='+monthto+'&conType='+conType,
              {
                 asynchronous : true,     
                 onComplete   : function (req){
                    eval(req.responseText);
                 }
              }
            );  
        }
        return true;
      }
      // Check to see if it's a number
      keyChar =  String.fromCharCode(key);
      if (/\d/.test(keyChar)) 
        {
         window.status = "";
         return true;
        } 
      else 
       {
        window.status = "Field accepts numbers only.";
        return false;
       }
    }
    function getEmpSearch(event) {
        var optionId=document.frmTS.hide_option.value;
        var key, keyChar;
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var hide_empDept=document.frmTS.hide_empDept.value;
        var hide_empSect=document.frmTS.hide_empSect.value;
        var fileName=document.frmTS.fileName.value;
        var orderBy=document.frmTS.orderBy.value;
        
         var conType = document.frmTS.conType.value;
          var monthto=document.frmTS.monthto.value;
          var monthfr=document.frmTS.monthfr.value;
          var cmbName=document.frmTS.cmbName.value;
          
          if (window.event)
            key = window.event.keyCode;
          else if (event)
            key = event.which;
          else
            return true;
          // Check for special characters like backspace
          if (key == null || key == 0 || key == 8 || key == 27 || key == 13) {
            if (key == 13) {
                new Ajax.Request(
                  'timesheet_ajax.php?hide_empSect='+hide_empSect+'&hide_empDept='+hide_empDept+'&inputId=empSearch&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&optionId='+optionId+'&fileName='+fileName+'&orderBy='+orderBy+'&payPd='+payPd+'&monthfr='+monthfr+'&monthto='+monthto+'&conType='+conType,
                  {
                     asynchronous : true,     
                     onComplete   : function (req){
                        eval(req.responseText);
                     }
                  }
                );
            }
          }
    }
    
    function getEmpDept(inputId) {
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var hide_empDept=document.frmTS.hide_empDept.value;
        new Ajax.Request(
          'timesheet_ajax.php?hide_empDept='+hide_empDept+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
          {
             asynchronous : true,     
             onComplete   : function (req){
                $('deptDept').innerHTML=req.responseText;
             }
          }
        );
    }
    function getEmpSect(inputId) {
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var hide_empSect=document.frmTS.hide_empSect.value;
        new Ajax.Request(
          'timesheet_ajax.php?hide_empSect='+hide_empSect+'&inputId='+inputId+'&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect,
          {
             asynchronous : true,     
             onComplete   : function (req){
                $('deptSect').innerHTML=req.responseText;
             }
          }
        );

    }
    function getPayPd(inputId) {
        var payPd=document.frmTS.payPd.value;
        var hide_payPd=document.frmTS.hide_payPd.value;
        var groupType=document.frmTS.groupType.value;
        var catType=document.frmTS.catType.value;
        new Ajax.Request(
          'timesheet_ajax.php?hide_payPd='+hide_payPd+'&inputId='+inputId+'&payPd='+payPd+'&groupType='+groupType+'&catType='+catType,
          {
             asynchronous : true,     
             onComplete   : function (req){
                $('pdPay').innerHTML=req.responseText;
             }
          }
        );

    }
    function option_button_click(id_ko) {
        var option_button = document.getElementById(id_ko).value;
        document.frmTS.hide_option.value = option_button;
        document.frmTS.submit();
    }
    
    function printSssList() {
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var orderBy=document.frmTS.orderBy.value;
        var groupType=document.frmTS.groupType.value;
        var catType=document.frmTS.catType.value;
        var payPd=document.frmTS.payPd.value;
        var table=document.frmTS.table.value;
        
        document.frmTS.action = 'frmreport.php?empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&orderBy='+orderBy+'&groupType='+groupType+'&catType='+catType+'&payPd='+payPd+'&table='+table+'&url=sss_list_pdf.php';
        document.frmTS.target = "_blank";
        document.frmTS.submit();
        document.frmTS.action = "sss_list_ajax.php";
        document.frmTS.target = "_self";
    }
    
    
    function valBack() {
        document.frmTS.action = 'timesheet.php';
        document.frmTS.submit();
    }
    function returnEmpList() {
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var groupType=document.frmTS.groupType.value;
        var orderBy=document.frmTS.orderBy.value;
        var catType=document.frmTS.catType.value;
        document.frmTS.action = 'timesheet_list.php?inputId=new_&empNo='+empNo+'&empName='+empName+'&empDiv='+empDiv+'&empDept='+empDept+'&empSect='+empSect+'&groupType='+groupType+'&orderBy='+orderBy+'&catType='+catType;
        document.frmTS.submit();
    }
    function valSearchTS() {
        var optionId=document.frmTS.hide_option.value;
        var empNo=document.frmTS.empNo.value;
        var empName=document.frmTS.empName.value;
        var empDiv=document.frmTS.empDiv.value;
        var empDept=document.frmTS.empDept.value;
        var empSect=document.frmTS.empSect.value;
        var hide_empDept=document.frmTS.hide_empDept.value;
        var hide_empSect=document.frmTS.hide_empSect.value;
        var fileName=document.frmTS.fileName.value;
        var orderBy=document.frmTS.orderBy.value;
        var conType = document.frmTS.conType.value;
        var monthto=document.frmTS.monthto.value;
        var monthfr=document.frmTS.monthfr.value;
        
        if(empNo=="")
        {
            alert("Please encode/search the Employee No. of the Employee.");
            return false;   
        }
        
        if (monthfr=="" || monthfr<0 || monthfr=="0") {
            alert("Invalid Monthy Coverage.");
            return false;
        }
        
        if (monthto=="" || monthto<0 || monthto=="0") {
            alert("Invalid Monthy Coverage.");
            return false;
        }
        
        new Ajax.Request(
      window.location.href ='emp_certification_pdf.php?empNo='+empNo+'&conType='+conType+'&monthfr='+monthfr+'&monthto='+monthto,
          {
             asynchronous : true,     
             onComplete   : function (req){
                eval(req.responseText);
             }
          }
        );
    } 
</script>