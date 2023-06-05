<?
//author : vincent c de torres
//date : 7/30/2009
function DropMonth($id = "", $selected = 0, $event = ""){
	
	if ($selected == 0)
		$selected = date("m");
	$months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	echo "<select name=\"$id" . "\" id=\"$id\"";
	if (!empty($event))
		echo " $event";
	echo ">\n";
	echo "<option value=\"-1\" selected> </option>\n";
	for ($cnt = 1; $cnt <= 12; $cnt++){
        if($cnt< 10)
            $cnt_val = '0'.$cnt;
        else
            $cnt_val = $cnt;
		echo "<option value=\"$cnt_val\" ";
		if ($selected == $cnt_val)
			echo "selected";
		echo ">" . $months[$cnt-1] . "</option>\n";
	}
	echo "</select>";
}

function DropDay($id = "", $selected = 0, $event = ""){
	
	if ($selected == 0)
		$selected = date("d");

	echo "<select name=\"$id" . "\" id=\"$id\" style=\"width:62px;\"";
	if (!empty($event))
		echo " $event";
	echo ">\n";
	echo "<option value=\"-1\" selected> </option>\n";
	for ($cnt = 1; $cnt <= 31; $cnt++){
		if ($cnt < 10)
			$cnt = "0".$cnt;
		echo "<option value=\"$cnt\" ";
		if ($selected == $cnt)
			echo "selected";
		echo ">";		
		echo "$cnt</option>\n";
	}
	echo "</select>";
}

function DropYear($id = "", $selected = 0, $event = ""){
	
	if ($selected == 0)
		$selected = date("Y");
		
	echo "<select name=\"$id" . "\" id=\"$id\" style=\"width:70px;\"";
	if (!empty($event))
		echo " $event";
	echo ">\n";
	echo "<option value=\"-1\"> </option>\n";
	for ($cnt = 1950; $cnt <= date("Y")+10; $cnt++){
		echo "<option value=\"$cnt\" ";
		if ($selected == $cnt)
			echo "selected";
		echo ">$cnt</option>\n";
	}
	echo "</select>";
}

function DropDate($id = "", $MM = 0, $DD = 0, $YY = 0, $event = ""){
	
	DropMonth($id . "_M", $MM, $event);
	DropDay($id . "_D", $DD, $event);
	DropYear($id . "_Y", $YY, $event);
}
?>
<STYLE type="text/css">
	.myDatePicker {
		font-size  : 11px;
		font-family: Verdana;
	}
</STYLE>

<SCRIPT type="text/javascript">
	validateCalendar('myDatePicker_M','myDatePicker_D','myDatePicker_Y');//onload
	function validateCalendar(monthSelector,daySelector,yearSelector){

		var monthValue = parseInt(document.getElementById(monthSelector).value);
		var dayValue   = parseInt(document.getElementById(daySelector).value);
		var yearValue  = parseInt(document.getElementById(yearSelector).value);
		var	DayDropLen = document.getElementById(daySelector).length;
		
		var tmpDate      = new Date(yearValue,monthValue);
		var LstdayOfMnth = tmpDate.toUTCString();
		LstdayOfMnth     = LstdayOfMnth.split(' ');
		LstdayOfMnth     = parseInt(LstdayOfMnth[1]);
		
		var diff = (DayDropLen-1)-LstdayOfMnth;
			
		if(diff > 0){
			for(i=1;i<=diff;i++){
				document.getElementById(daySelector).remove(LstdayOfMnth+1);
			}			
		}
		else{
			tmpDiff = diff.toString();
			tmpDiff = tmpDiff.split('-');
			tmpDiff = parseInt(tmpDiff[1]);

			for(i=1;i<=tmpDiff;i++){
				var y=document.createElement('option');
				y.text=(DayDropLen-1)+i;
				var x=document.getElementById(daySelector).add(y,null);
				try{
				  x.add(y,null); // standards compliant
				}
				catch(ex){
				  x.add(y); // IE only
				}
			}
		}

		if(dayValue > LstdayOfMnth){
			document.getElementById(daySelector).value=LstdayOfMnth;
		}	
	}
		
</SCRIPT>