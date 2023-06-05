<?php
	/*
		Created By		:	Genarra Jo - Ann S. Arong
		Date Created 	: 	10/23/2009
		Function		:	Maintenance (Pop Up) for Previous Employer Module
		
		$empNo = Employee Number
		$compCode = Employee Company Code
	*/
	
	class mainContent6 extends commonObj
	{
		function getPrevEmpContent($empNo, $compCode)
		{
			$qrygetCont = "SELECT   prevEmplr, empAddr1, emplrTin, prevTaxes, prevEarnings, tax13th,yearCd,seqNo
						  FROM     tblPrevEmployer
						  WHERE empNo='".$empNo."' and compCode='".$compCode."'";
			$resgetCont = $this->execQry($qrygetCont);
			return $this->getArrRes($resgetCont);
		}
		
		function insIntoPrevEmplr($empNo, $compCode)
		{
			 $qry_insIntoPrevEmplr = "INSERT INTO tblPrevEmployer(compCode,empNo,prevEmplr,empAddr1,empAddr2,empAddr3,emplrTin,prevEarnings,prevTaxes,prevStat,grossNonTax,nonTax13th,nonTaxSss,tax13th,yearCd, empBasic_Curr,empBasic_Prev, empTypeTag, userAdded, dateAdded)
							VALUES ('".$compCode."','".$empNo."','".strtoupper(str_replace("'","''",stripslashes($_GET["txtPrevEmpr"])))."','".strtoupper(str_replace("'","''",stripslashes($_GET["txtAdd1"])))."','".strtoupper(str_replace("'","''",stripslashes($_GET["txtAdd2"])))."','".strtoupper(str_replace("'","''",stripslashes($_GET["txtAdd3"])))."','".str_replace("'","''",stripslashes($_GET["txtTinNo"]))."','".str_replace("'","''",stripslashes($_GET["txtGrossTax"]))."','".str_replace("'","''",stripslashes($_GET["txtWith"]))."','A','".str_replace("'","''",stripslashes($_GET["txtGrossNonTax"]))."','".str_replace("'","''",stripslashes($_GET["txt13thNonTax"]))."','".str_replace("'","''",stripslashes($_GET["txtMandatory"]))."','".str_replace("'","''",stripslashes($_GET["txt13thTax"]))."','".$_GET["txtYear"]."','".str_replace("'","''",stripslashes($_GET["txtempBasic"]))."','".str_replace("'","''",stripslashes($_GET["txtempBasic_prev"]))."', '".str_replace("'","''",stripslashes($_GET["opt"]))."', '".$_SESSION['employee_number']."', '".date("m/d/Y")."')";
			
			
			$res_insIntoPrevEmplr = $this->execQry($qry_insIntoPrevEmplr);
			if($res_insIntoPrevEmplr){
				return true;
			}
			else{
				return false;
			}
		}
		
		function chkTinEmployer($emplyrTin,$empNo,$empCompCode)
		{
				
			$qrychkTinEmployer = "Select emplrTin from tblPrevEmployer where emplrTin='".$emplyrTin."' and empNo='".$empNo."' and compCode='".$empCompCode."'";
			$reschkTinEmployer = $this->execQry($qrychkTinEmployer);
			if($reschkTinEmployer){
				return $this->getRecCount($reschkTinEmployer);
			}
		}
		
		function getPrevEmplrContent($seqNo)
		{
			$qrygetPrevEmplrContent = "Select * from tblPrevEmployer where seqNo='".$seqNo."'";
			$resgetPrevEmplrContent = $this->execQry($qrygetPrevEmplrContent);
			return $resgetPrevEmplrContent ;
		}
		
		function updatePrevEmplr($seqNo)
		{
			$qryupdatePrevEmplr = "Update tblPrevEmployer set prevEmplr='".strtoupper(str_replace("'","''",stripslashes($_GET["txtPrevEmpr"])))."',
									empAddr1='".strtoupper(str_replace("'","''",stripslashes($_GET["txtAdd1"])))."',
									empAddr2='".strtoupper(str_replace("'","''",stripslashes($_GET["txtAdd2"])))."',
									empAddr3='".strtoupper(str_replace("'","''",stripslashes($_GET["txtAdd3"])))."',
									emplrTin='".strtoupper(str_replace("'","''",stripslashes($_GET["txtTinNo"])))."',
									prevEarnings='".strtoupper(str_replace("'","''",stripslashes($_GET["txtGrossTax"])))."',
									prevTaxes='".strtoupper(str_replace("'","''",stripslashes($_GET["txtWith"])))."',
									grossNonTax='".strtoupper(str_replace("'","''",stripslashes($_GET["txtGrossNonTax"])))."',
									nonTax13th='".strtoupper(str_replace("'","''",stripslashes($_GET["txt13thNonTax"])))."',
									nonTaxSss='".strtoupper(str_replace("'","''",stripslashes($_GET["txtMandatory"])))."',
									tax13th='".strtoupper(str_replace("'","''",stripslashes($_GET["txt13thTax"])))."',
									yearCd='".strtoupper(str_replace("'","''",stripslashes($_GET["txtYear"])))."',
									empBasic_Curr='".strtoupper(str_replace("'","''",stripslashes($_GET["txtempBasic"])))."',
									empBasic_Prev='".strtoupper(str_replace("'","''",stripslashes($_GET["txtempBasic_prev"])))."',
									empTypeTag='".str_replace("'","''",stripslashes($_GET["opt"]))."',
									userUpdated= '".$_SESSION['employee_number']."', 
									dateUpdated='".date("m/d/Y")."'
									where seqNo='".$seqNo."'";
			$resupdatePrevEmplr = $this->execQry($qryupdatePrevEmplr);
			if($resupdatePrevEmplr){
				return true;
			}
			else{
				return false;
			}
		}
		
		function chkAgainTin($emplyrTin,$empNo,$seqNo,$empCompCode)
		{
			$qrychkAgainTin = "Select emplrTin from tblPrevEmployer where emplrTin='".$emplyrTin."' and empNo='".$empNo."' and seqNo='".$seqNo."' and compCode='".$empCompCode."'";
			$reschkAgainTin = $this->execQry($qrychkAgainTin);
			
			return $this->getRecCount($reschkAgainTin);
		}
		
		function delPrevEmplr($seqNo)
		{
			$qryDelPrevEmplr = "Delete from tblPrevEmployer where seqNo='".$seqNo."'";
			$res_DelPrevEmplr = $this->execQry($qryDelPrevEmplr);
			if($res_DelPrevEmplr)
				return true;
			else
				return false;
		}
	}
		
?>