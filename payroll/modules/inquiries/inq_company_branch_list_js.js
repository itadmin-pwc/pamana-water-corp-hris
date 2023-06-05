// JavaScript Document
// Created By 	: 	Genarra Jo - Ann S. Arong
// Date Created :	09 16 2009 9:14am

function printBranchList()
{
	var Comp_Cd=document.frmCompList.compCodeBranch.value;
	document.frmCompList.action = 'inq_company_branch_list_pdf.php?Comp_Cd='+Comp_Cd;
	document.frmCompList.target = "_blank";
	document.frmCompList.submit();
	document.frmCompList.action = "inq_company_branch_list.php";
	document.frmCompList.target = "_self";
}
function printBranchInfo(branchCode)
{
	var Comp_Cd=document.frmCompList.compCodeBranch.value;
	document.frmCompList.action = 'inq_company_branch_info_pdf.php?Comp_Cd='+Comp_Cd+'&branchCode='+branchCode;
	document.frmCompList.target = "_blank";
	document.frmCompList.submit();
	document.frmCompList.action = "inq_company_branch_list.php";
	document.frmCompList.target = "_self";
}
