// JavaScript Document
// Created By 	: 	Genarra Jo - Ann S. Arong
// Date Created :	09 15 2009 4:01pm

function printTaxTable()
{
	document.frmTaxList.action = 'inq_taxtable_pdf.php';
	document.frmTaxList.target = "_blank";
	document.frmTaxList.submit();
	document.frmTaxList.action = "inq_taxtable.php";
	document.frmTaxList.target = "_self";
}
function printSMTaxTable()
{
	document.frmTaxList.action = 'inq_sm_taxtable_pdf.php';
	document.frmTaxList.target = "_blank";
	document.frmTaxList.submit();
	document.frmTaxList.action = "inq_sm_taxtable.php";
	document.frmTaxList.target = "_self";
}