function trim(s){ 
	exp = /^\s+|\s+$/i
	return s.replace(exp,''); 
}

function killContextMenu() 
{
  return false;
}
//document.oncontextmenu = killContextMenu;

function disableRightClick(){
	
	function right(e){
		if(navigator.appName == 'Netscape'){
			myevent = e;
			 if(myevent.which == 2 || myevent.which == 3){
	
				document.oncontextmenu = mischandler;
				return false;
			 }
		}
		else if(navigator.appName == 'Microsoft Internet Explorer'){
			myevent = event;
			if(myevent.button == 2 || myevent.button == 4){
				document.oncontextmenu = mischandler;
				return false;			
			}
		}
	}
	
	function mischandler(){
	   return false;
	}
	
	window.onmousedown = right;
	window.onmouseup = right;
}

function startTime(){
	var today=new Date();
	var h=today.getHours();
	var m=today.getMinutes();
	var s=today.getSeconds();
	// add a zero in front of numbers<10
	m=checkTime(m);
	s=checkTime(s);
	document.getElementById('currTime').innerHTML=h+":"+m+":"+s;
	t=setTimeout('startTime()',500);
}


function checkTime(i){
	if (i<10){
	  i="0" + i;
	}
	return i;
}



function format_number(pnumber,decimals){
	if (isNaN(pnumber)) { return 0};
	if (pnumber=='') { return 0};
	
	var snum = new String(pnumber);
	var sec = snum.split('.');
	var whole = parseFloat(sec[0]);
	var result = '';
	
	if(sec.length > 1){
		
		var dec = new String(sec[1]);
		dec = String(parseFloat(sec[1])/Math.pow(10,(dec.length - decimals)));
		dec = String(whole + Math.round(parseFloat(dec))/Math.pow(10,decimals));
		var dot = dec.indexOf('.');
		if(dot == -1){
			dec += '.'; 
			dot = dec.indexOf('.');
		}
		while(dec.length <= dot + decimals) { dec += '0'; }
		result = dec;
	} else{
		var dot;
		var dec = new String(whole);
		dec += '.';
		dot = dec.indexOf('.');		
		while(dec.length <= dot + decimals) { dec += '0'; }
		result = dec;
	}	
	return result;
}

function pager(URL,ele,action,intOffSet,isSearch,txtSearch,cmbSearch,extra,imgPath,evt){

	if(action == 'getPage'){
		if(evt.which != 13){
			return false;
		}
	}
	if((txtSearch != '') && (cmbSearch != '')){ 
		var params = "?action="+action+"&offSet="+intOffSet+"&isSearch="+isSearch+"&txtSrch="+$F(txtSearch)+"&srchType="+$F(cmbSearch)+extra;
	}
	else{
		var params = "?action="+action+"&offSet="+intOffSet+"&isSearch="+isSearch+extra;
	}

	var url = URL+params;
	
	new Ajax.Request(url,{
		method : 'get',
		onComplete : function (req){
			$(ele).innerHTML=req.responseText;
			focusHandler(action,extra);
		},
		onCreate : function (){
			if(action == 'load'){
				$('indicator1').innerHTML="<img src='"+imgPath+"wait.gif'>";
			}
			else{
				$('indicator2').src=imgPath+"wait.gif";
			}
		},
		onSuccess : function (){
			if(action == 'load'){
				$('indicator1').innerHTML="";
			}
			else{
				$('indicator2').innerHTML="";
			}
		}		
	});
}

function empLookup(fileLkUp){
	
	var empLukup = new Window({
		
	id: "empLukUp",
	className : 'mac_os_x',
	width:500, 
	height:365, 
	zIndex: 100, 
	resizable: false, 
	title: "Employee Lookup", 
	minimizable:true,
	showEffect:Effect.Appear, 
	destroyOnClose: true,
	maximizable: false,
	hideEffect: Effect.SwitchOff, 
	draggable:true })
	empLukup.setAjaxContent(fileLkUp,'','');
	empLukup.show(true);
	empLukup.showCenter();
	clearFld();
	
	myObserver = {
		onDestroy: function(eventName, win) {
		
		  if (win == empLukup) {
		    empLukup = null;
		    Windows.removeObserver(this);
		  }
		}
	}
	Windows.addObserver(myObserver);	
}

function passEmpNo(fld,fldVal){
	Windows.getWindow('empLukUp').close();
	$(fld).value=fldVal
	$(fld).focus();
}

///////////ART ADD////////////////////
function isNumberInput(field, event) {
  var key, keyChar;

  if (window.event)
	key = window.event.keyCode;
  else if (event)
	key = event.which;
  else
	return true;
  // Check for special characters like backspace
  if (key == null || key == 0 || key == 8 || key == 13 || key == 27)
	return true;
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
function isNumberInput2Decimal(field, event) {
  var key, keyChar;

  if (window.event)
	key = window.event.keyCode;
  else if (event)
	key = event.which;
  else
	return true;
  // Check for special characters like backspace
  if (key == null || key == 0 || key == 8 || key == 13 || key == 27)
	return true;
  // Check to see if it's a number
  keyChar =  String.fromCharCode(key);
  if ((/\d/.test(keyChar)) || (/\./.test(keyChar))) 
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
function val2DecNo(val,id) {
	var numExp = /^(\d+\.\d{0,2}|\d+)$/;
	if(!val.match(numExp)) {
		alert("Require Numeric only \r\n       or \r\ndecimal places should not be greater than two(2)");
		document.getElementById(id).value="";
		document.getElementById(id).focus();
		return false;
	}
}
function valDateToCurrDate(val,id) {
	var todayDate = new Date();
	var parseVal= Date.parse(val);
	var parseTodayDate = Date.parse(todayDate);
	
	if(parseVal > parseTodayDate) {
		alert("Date must not be greater than to Current Date.");
		document.getElementById(id).value="";
		return false;
	}
}
function valDateStartEnd(valStart,idStart,valEnd) {
	var todayDate = new Date();
	var parseStart = Date.parse(valStart);
	var parseEnd = Date.parse(valEnd);
	var parseTodayDate = Date.parse(todayDate);
	
	if(parseStart > parseEnd) {
		alert("Start Date must not be greater than to End Date.");
		document.getElementById(idStart).value="";
		return false;
	}
}
function valDateStartEndToCurrDate(valStart,idStart,valEnd) {
	var todayDate = new Date();
	var parseStart = Date.parse(valStart);
	var parseEnd = Date.parse(valEnd);
	var parseTodayDate = Date.parse(todayDate);
	 /////////// dont accept greater than to current date
	if(parseLoanStart > parseTodayDate) {
		alert("Start Date must not be greater than to Current Date.");
		document.frmEmpLoan.loanStart.value="";
		return false;
	}
	if(parseLoanEnd > parseTodayDate) {
		alert("End Date must not be greater than to Current Date.");
		document.frmEmpLoan.loanEnd.value="";
		return false;
	}
	if(parseStart > parseEnd) {
		alert("Start Date must not be greater than to End Date.");
		document.getElementById(idStart).value="";
		return false;
	}
}
function valNullVal(val,id) {	
	if(val == "") {
		alert("Data should have value.");
		document.getElementById(id).value="0";
		return false;
	}
}
