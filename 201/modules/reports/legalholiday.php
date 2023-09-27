<HTML>
	<HEAD>
<TITLE>
<?=SYS_TITLE?>
</TITLE>
<script>
function legalHoliday(){
	document.frmHoliday.action="legalholiday_pdf.php?years="+document.frmHoliday.years.value;
	document.frmHoliday.target="_blank";
	document.frmHoliday.submit();
	document.frmHoliday.action="legalholiday.php";
	document.frmHoliday.target="_self";	
}
</script>
<style>@import url('../../style/reports.css');</style>
<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
<script type='text/javascript' src='../../../includes/prototype.js'></script>
<link rel="stylesheet" type="text/css" href="../../style/payroll.css"></link>
<!--calendar lib-->
<script type="text/javascript" src="../../../includes/calendar/calendar.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-en.js"></script>
<script type="text/javascript" src="../../../includes/calendar/calendar-setup.js"></script>		
<STYLE TYPE="text/css" MEDIA="screen">@import url("../../../includes/calendar/calendar-blue.css");</STYLE>
<!--end calendar lib-->
<script type='text/javascript' src='movement.js'></script>
</HEAD>
	<BODY>
<form name="frmHoliday" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
  <table  cellpadding="0" cellspacing="1" class="parentGrid" width="100%">
    <tr>
		
      <td class="parentGridHdr"> &nbsp;<img src="../../../images/grid.png">&nbsp;Legal Holiday</td>
	</tr>
	<tr>
		<td class="parentGridDtl" >
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid" >       
          <tr>
            <td width="18%" class="gridDtlLbl">Select Year</td>
            <td width="1%" class="gridDtlLbl">:</td>
            <td class="gridDtlVal">
            <select name="years" id="years">
            <option value="">All</option>
            <?
            for($y=date("Y");$y>2000;$y--){
			?>
            <option value="<?=$y;?>"<? if($_POST['years']==$y) echo "selected";?>><?=$y;?></option>
            <?
			}
			?>
            </select>
                          </td>
            </tr>          
               
        </table>
<br>
			  <TABLE border="0" width="100%" cellpadding="1" cellspacing="1" class="childGrid">
				  <tr>
					<td>
						<CENTER>
                <input type="button" class="inputs" name="salary" id="salary" value="Print Legal Holiday" onClick="legalHoliday();">
              </CENTER>
					</td>
				  </tr>
			  </table> 
	</td>
	</tr> 
	<tr > 
		<td class="gridToolbarOnTopOnly" colspan="6">
			<CENTER>
          <BLINK> 
	          <input name="msg" id="msg" type="text" size="100" style="color:RED; background-color:#fff; height:18px; text-align: center;  border:0px solid;" value="<? echo $msg; ?>">
          </BLINK> 
        </CENTER>	
		</td>
	</tr>
</table>
</form>
</BODY>
</HTML>
