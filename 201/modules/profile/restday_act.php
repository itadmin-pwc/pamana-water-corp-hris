<?
session_start();
$act=$_GET['act'];
include("profile.obj.php");
$obj = new ProfileObj();
switch($_GET['code']) {
	case "add":
		if (in_array($_GET['date'],explode(",",$_SESSION['empRestDay'])))
		  {
				echo "alert('Rest Day already exist!');";
		  } else {
				if (trim($_SESSION['empRestDay'])=="") {
					$_SESSION['empRestDay']=$_GET['date'];
				} else {
					$_SESSION['empRestDay'] .="," . $_GET['date'];	
				}
				if ($_SESSION['profile_act'] =="View") {
					$res = $obj->restday($_SESSION['strprofile'],$_SESSION['oldcompCode'],$_SESSION['empRestDay']);
				}
				echo "alert('Rest Day Added!');";
		  }
		exit();
	break;
	case "delete":
		$check=strpos($_SESSION['empRestDay'],$_GET['date']);
		if ($check!=0) {
			$_SESSION['empRestDay']=str_replace(",".$_GET['date'],"",$_SESSION['empRestDay']);
		} else {
			$arrRD=explode(",",$_SESSION['empRestDay']);
			if (count($arrRD)==1) {
				$_SESSION['empRestDay']=str_replace($_GET['date'],"",$_SESSION['empRestDay']);
			} else {
				$_SESSION['empRestDay']=str_replace($_GET['date'].",","",$_SESSION['empRestDay']);
			}	
		}

		if ($_SESSION['profile_act'] =="View") {
			$res = $obj->restday($_SESSION['strprofile'],$_SESSION['oldcompCode'],$_SESSION['empRestDay']);
		}
		echo "alert('Rest Day Deleted!');";
		exit();
	break;
}
?>

<HTML>
<head>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->

<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<script src="../../../includes/validations.js"></script>		
		<STYLE>@import url('../../style/payroll.css');</STYLE>
        		<style type="text/css">
        <!--
        .headertxt {font-family: verdana; font-size: 11px;}
        -->
        </style>

</head>
	<BODY>
	<form name="form1" method="post" action="">
	  <TABLE border="0" width="100%" cellpadding="1" cellspacing="0" class="parentGrid">
        <tr>
          <td colspan="4" class="parentGridHdr">&nbsp;<img src="../../../images/grid.png">&nbsp;Rest Day</td>
        </tr>
        <tr>
          <td class="parentGridDtl"><table width="351" border="0" class="childGrid" cellpadding="2" cellspacing="1">
              <tr>
                <td colspan="3" height="15"></td>
              </tr>
              <tr>
                <td colspan="3" height="15"></td>
              </tr>
              <tr>
                <td colspan="3" height="15"></td>
              </tr>
              <tr>
                <td width="13%" class="headertxt" >Date</td>
                <td width="3%" class="headertxt">:</td>
                <td width="84%" class="gridDtlVal"><input class="inputs" name="date" id="date" value="<? echo $rdDate; ?>" disabled="true" type="text" size="25" maxlength="50" >
                  <a href="#"><img name="imgrdDate" id="imgrdDate" type="image" src="../../../images/cal_new.gif" title="Start Date" style="cursor: pointer;position:relative;top:3px;border:none;"></a> </td>
              </tr>
              <tr>
                <td><div id="dvtest">&nbsp;</div></td>
                <td>&nbsp;</td>
                <td><input name="button" type="button" class="inputs" id="button" onClick="saverestday();" value="SAVE"></td>
              </tr>
              <tr>
                <td colspan="3" height="15"></td>
              </tr>
              <tr>
                <td colspan="3" height="15"></td>
              </tr>
          </table></td>
        </tr>
      </TABLE>
        </form>
</BODY>
</HTML>
<SCRIPT>
		Calendar.setup({
				  inputField  : "date",      // ID of the input field
				  ifFormat    : "%m/%d/%Y",          // the date format
				  button      : "imgrdDate"       // ID of the button
			}
		)
</SCRIPT>