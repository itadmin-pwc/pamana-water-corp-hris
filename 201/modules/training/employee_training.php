<?php 
session_start();
	$userempno = $_SESSION['employee_number'];
	$compcode = $_SESSION['company_code'];
	if($_GET['action']=="redirect"){
		$_SESSION['empno'] = $userempno;
		$_SESSION['compcode'] = $compcode;
		//window.showModalDialog('data/alert.html',null,'dialogWidth:365px;dialogHeight:250px;center:1;scroll:0;help:0;status:0');"
		echo "window.open('http://".$_SERVER['HTTP_HOST']."/PG-ACADEME/', 'training','','');";
		exit();		
	}

?>
	<HEAD>
		<TITLE><?=SYS_TITLE?></TITLE>
		<SCRIPT type="text/javascript" src="../../../js/extjs/adapter/prototype/prototype.js"></SCRIPT>
		<SCRIPT type="text/javascript" src="../../../includes/jSLib.js"></SCRIPT>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/effects.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window.js"></script>
		<script type="text/javascript" src="../../../js/extjs/adapter/prototype/window_effects.js"></script>
		<script>
        function redirectPage(){
			new Ajax.Request(
				'employee_training.php?action=redirect',
				{
					method : 'get',
					asynchronous : true, 
					onComplete : function (req){
						eval(req.responseText);	
					}
				}
			);	
        }
        </script>
	</HEAD>

<body onLoad="redirectPage();">
</body>
