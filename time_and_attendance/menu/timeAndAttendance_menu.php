<?php
session_start();
include("../../includes/db.inc.php");
include("../../includes/common.php");

class PayrollMenuHandler extends commonObj {

 
   function getMenusObjs(){
   	
   	$seesionVars = $this->getSeesionVars();

   	 $arrUserLogInInfo = $this->getUserLogInInfo($seesionVars['compCode'],$seesionVars['empNo']);	 
   		
      $qry="select * from tblTmeInAttendanceMenu 
      		WHERE moduleStat = 'A' 
      		AND moduleId IN ($arrUserLogInInfo[pagesTNA]) ";
      $qry .= "ORDER BY menuOrder,moduleOrder ";
      $res = $this->execQry($qry);
    
     return $this->getSQLArrayObj($res);
   }
   
   function getModules($_sqlObjs=null){
        
		$sqlObjs = ($_sqlObjs==null) ? $this->getMenusObjs() : $_sqlObjs ;
		$_modules = array();
		foreach($sqlObjs as $obj){
			$_modules[] = $obj->moduleName;
		}
		return array_unique($_modules);
   }
   
   function getChildren($_module,$_sqlObjs){
   	
   	 global $compCode,$empNo;
   	 
   	 $arrUserLogInInfo = $this->getUserLogInInfo($compCode,$empNo);	

       $module = trim($_module);
	   $childrens = array();
       if($module!=""){
			foreach($_sqlObjs as $obj){
				if($module==$obj->moduleName){
				    $leaf =($obj->leaf==1) ? true : false;
/*					if($arrUserLogInInfo['userLevel']!=1 && $obj->isAdminPage==1){
						continue;  //skip or do not display page if user is not admin
					}*/
					$childrens[] = array(
						"text"=>$obj->label,
						"id"=>$obj->page,
						"leaf"=>$leaf 
					);
				}
			}
	   }
	   return $childrens;
   }
   
   function prepareMenu($_sqlObjs){
       $menus = array();
	   $mchecker = array();
	   $modules = $this->getModules($_sqlObjs);

       foreach($modules as $module){
	        foreach($_sqlObjs as $obj){
				if($module==$obj->moduleName && !in_array($module,$mchecker)){
				    $expanded = ($obj->expanded==1) ? true : false;
					$menus[] = array('text'=>$module,
					'expanded'=>$expanded,
					'children'=>$this->getChildren($module,$_sqlObjs));
					$mchecker[] = $module; // to avoid redundant 
				}
			}
		}
	  return $menus;
   }
   
   public function render(){

		$menuObjs = $this->getMenusObjs();
		$menus =    $this->prepareMenu($menuObjs);
		$js = json_encode($menus);
		echo $js;
   }
}

$menu = new PayrollMenuHandler();
$menu->render();
$menu->disConnect();


?>