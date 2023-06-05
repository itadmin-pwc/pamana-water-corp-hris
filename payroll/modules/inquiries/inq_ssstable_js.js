// JavaScript Document
// Created By 	: 	Genarra Jo - Ann S. Arong
// Date Created :	09 15 2009

function printSSSTable()
{
	document.frmSSSList.action = 'inq_ssstable_pdf.php?hol_date=';
	document.frmSSSList.target = "_blank";
	document.frmSSSList.submit();
	document.frmSSSList.action = "inq_ssstable.php";
	document.frmSSSList.target = "_self";
}