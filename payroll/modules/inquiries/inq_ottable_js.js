// JavaScript Document
// Created By 	: 	Genarra Jo - Ann S. Arong
// Date Created :	09 15 2009 4:01pm

function printOtTable()
{
	document.frmOtList.action = 'inq_ottable_pdf.php';
	document.frmOtList.target = "_blank";
	document.frmOtList.submit();
	document.frmOtList.action = "inq_ottable.php";
	document.frmOtList.target = "_self";
}