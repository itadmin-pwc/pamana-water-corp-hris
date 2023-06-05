// JavaScript Document
	function processGenerateLoans() {
		var payPd=document.getElementById("payPd").value;;
		if (payPd=="" || payPd<0 || payPd=="0") {
			alert("Invalid Payroll Period.");
			return false;
		} else {
			new Ajax.Request(
			  'generate_loans.php?code=generateLoans&payPd='+payPd,
			  {
				 asynchronous : true,     
				 onComplete   : function (req){
					eval(req.responseText);
				 }
			  }
			);
		}
	}