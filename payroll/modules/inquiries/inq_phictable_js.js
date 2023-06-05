// JavaScript Document
// Created By 	: 	Genarra Jo - Ann S. Arong
// Date Created :	09 15 2009 4:01pm

function printPhicTable()
{
	document.frmPhicList.action = 'inq_phictable_pdf.php?hol_date=';
	document.frmPhicList.target = "_blank";
	document.frmPhicList.submit();
	document.frmPhicList.action = "inq_phictable.php";
	document.frmPhicList.target = "_self";
}