<?
#####TIME AND ATTENCEN######
define('SYS_NAME_TNA','time_and_attendance');

#####201######
define('SYS_NAME_201','201');

#####201######
define('SYS_NAME_PAYROLL','payroll');

#####SYSTEM NAME#####
define('SYS_NAME','PAMANA-WATER-CORP-HRIS');


#####SYSTEM TITLE#####
define('SYS_TITLE','PAMANA-WATER-CORP-HRIS :: VER. 1.0');

//overtime constant value
define('OTRG','0221');//ot regular day
define('OTRD','0222');//ot rest day
define('OTLH','0223');//ot legal holiday
define('OTSH','0224');//ot special holiday
define('OTLHRD','0225');//ot legal holiday + rest day
define('OTSPRD','0226');//ot special holiday + rest day
define('OTRDGT8','0233');//ot rest day > 8 hours
define('OTLHGT8','0234');//ot legal holiday > 8 hours
define('OTSPGT8','0235');//ot special holiday > 8 hours
define('OTLHRDGT8','0236');//ot legal holiday + rest day > 8 hours
define('OTSPRDGT8','0237');//ot speacial holiday + rest day > 8 h
define('OTLHSH','0238');//ot legal +special holiday
define('OTLHSHGT8','0239');//ot legal+special holiday > 8 hours
define('OTLHSHRD','0240');//ot legal+special holiday + rest day
define('OTLHSHRDGT8','0241');//ot legal+special holiday + rest day > 8
//night differential constant value
define('NDRG','0327');//regular
define('NDRD','0328');//rest day
define('NDLH','0329');//legal holiday
define('NDSP','0330');//special holiday
define('NDLHRD','0331');//legal holiday + rest day
define('NDSPRD','0332');//special holiday + rest day
define('NDRDGT8','0338');//rest day > 8 hours
define('NDLHGT8','0339');//legal holiday > 8 hours
define('NDSHGT8','0340');//specail hoiday > 8 hours
define('NDLHRDGT8','0341');//legal holiday + rest day > 8hours
define('NDSPRDGT8','0342');//special holiday + rest day > 8 hours
define('NDLHSH','0343');//legal+special holiday
define('NDLHSHGT8','0344');//legal+special  holiday > 8 hours NDLHSHRD
define('NDLHSHRD','0345');//legal+special holiday+ rest day
define('NDLHSHRDGT8','0346');//legal+special holiday + rest day> 8 hours NDLHSHRD


define('EARNINGS_ND','0300');//Night Differential
define('EARNINGS_OT','0200');//overtime
define('EARNINGS_TARD','0111');//Tardiness
define('EARNINGS_UT','0112');//undertime
define('EARNINGS_ABS','0113');//Absence
define('EARNINGS_BASIC','0100');//Basic
define('EARNINGS_LEGALPAY','0410');//Basic

/*trnRecode*/
define('EARNINGS_RECODEBASIC','0100');//Basic
define('EARNINGS_RECODEOT','0200');//Ot
define('EARNINGS_RECODEND','0300');//Nd
define('EARNINGS_RECODEHP','0400');//Holiday Pay
define('EARNINGS_RECODEVLENCASH', '0500'); //Vl EncashMent
define('EARNINGS_RECODEVLWPAY', '0600'); //Vl With Pay
define('EARNINGS_RECODESLWPAY', '0700'); //SL With Pay
define('EARNINGS_RECODEADJ', '0800'); //Adjustment
define('EARNINGS_RECODEALLOW', '8100'); //Allowance
define('EARNINGS_RECODEOTHERS', '1200'); //Others / Other Earnings
define('EARNINGS_VLOP','0114');//Absence
define('EARNINGS_SLOP','0115');//Basic



//allownace constant value
define('ALLW_THIRTEEN_MONTH_ALLOWANCE','1100');//13 month (allowance)
define('ALLW_ADVANCES','8101');//ADVANCES
define('ALLW_ALLOWANCE','8102');//ALLOWANCE
define('ALLW_BONUS','8103');//BONUS
define('ALLW_CASH_BOND','8105');//CASH BOND
define('ALLW_CASHIER_ALLOWANCE','8104');//CAHIER ALLOWANCE
define('ALLW_ECOLA','8106');//ECOLA
define('ALLW_GASOLINE_ALLOWANCE','8107');//GASOLINE ALLOWANCE
define('ALLW_REVIV_ALLOWANCE','8109');//region 4 ALLOWANCE
define('ALLW_RELOCATION_ALLOWANCE','8108');//RELOCATION ALLOWANCE
define('ALLW_TRAINING_ALLOWANCE','8110');//TRAINING ALLOWANCE
define('ALLW_TRANSPORTATION_ALLOWANCE','8111');//TRANSPORTATION ALLOWANCE
define('ALLW_ECOLA3','8113');//ECOLA ALLOWANCE

//Deductions
define('WTAX','5100');//Withholding Tax
define('SSS_CONTRIB','5200');//SSS Contribution
define('SSS_PROVEFUND','5201'); // SSS Provident Fund 2021
define('PHILHEALTH_CONTRIB','5300');//PhilHealth Contribution
define('PAGIBIG_CONTRIB','5400');//Pagibig Contribution
//loans
define('LOAN_SSS_SALARY','5500');//SSS Salary Loan
define('LOAN_SSS_CALAMITY','5600');//SSS Calamity Loan
define('LOAN_PAGIBIG_SALARY','5700');//Pagibig Salary Loan
define('LOAN_PAGIBIG_MULTI','5800');//Pagibig Multi Purpose Loan
define('LOAN_COMPANY_SALARY','8006');//Company Salary Loan
define('LOAN_CASH_BOND','8005');//Cash Bond
define('LOAN_PAYMENT_SHORTAGE','8007');//Payment for Shortages
define('LOAN_CAR_LOAN','8003');//Car Loan
define('LOAN_AR_OTHERS','8002');//A/R Others
define('LOAN_CASH_ADVANCE','0806');//Cash Advance
define('LOAN_TELEPHONE','8008');//Telephone
define('LOAN_AP_CASH_BOND','8001');//A/P Cash Bond
define('LOAN_CSI','8009');//CSI
define('LOAN_CSP','8025');//CSP

define('LOAN_CSI_TypeCd','39');//CSI
define('LOAN_CSP_TypeCd','301');//CSP

//other deductions
define('OTHER_DED_HDMF','5900');//HDMF Adjustment
define('OTHER_DED_SSS_ADJ','6000');//Sss Adjustment

define('PG_PRICE_CLUB','2,6,7,8,9');//PG PRICE CLUB
define('PG_JR','1');//PG JR		

define('MTC_BANK_CODE','1');//PG JR		

//Other Earnings / Adjustments
define('ADJ_BASIC','0801');//Adjustment to Basic
define('ADJ_BASIC_TAXCD','Y');//Adjustment to Basic Tax Code
define('ADJ_OT','0802');//Adjustment to Ot
define('ADJ_OT_TAXCD','Y');//Adjustment to Ot Tax Cd
define('ADJ_ND','0803');//Adjustment to Nd
define('ADJ_ND_TAXCD','Y');//Adjustment to Nd Tax Cd
define('QTAX_ADJ','Y');//if N = Adjustment to Basic is Not Taxable, else Taxable;
define('ADJ_ADVANCES','8119');//Adjustment to Advances;

//Images for Remittances
define('SSS_HEADER','../../../images/sss_rem_form_header.JPG');
define('SSS_FOOTER','../../../images/sss_rem_form_footer.JPG');	
define('PAG_HEADER','../../../images/pag_rem_form_header.JPG');
define('PAG_FOOTER','../../../images/pag_rem_form_footer.JPG');
define('PAG_BODY','../../../images/pag_rem_form_body.JPG');
define('PHIC_HEADER','../../../images/phic_rem_form_header.JPG');
define('PHIC_FOOTER','../../../images/phic_rem_form_footer.JPG');
define('PHIC_BODY','../../../images/phic_rem_form_body.JPG');
define('PHIC_LOGO','../../../images/phic_logo.JPG');
define('RA1_HEADER','../../../images/r1a_header.JPG');
define('RA1_FOOTER','../../../images/r1a_footer.JPG');
define('ER2_HEADER','../../../images/er2_header.JPG');
define('ER2_FOOTER','../../../images/er2_footer.JPG');
define('PAGLOAN_HEADER','../../../images/pag_loan_form_header.JPG');
define('PAGLOAN_BODY','../../../images/pag_rem_form_body_2.JPG');
define('PG_LOGO','../../../images/ow-logo.JPG');
define('PPCI_LOGO','../../../images/ppci_logo.JPG');
define('PGJR_LOGO','../../../images/acacialogo.JPG');
define('DF_LOGO','../../../images/df_logo.JPG');
define('CIDIS_HEADER_BG','../../../images/cidis_header_bg.JPG');
define('DIVISION_HEADER','../../../images/division_header.JPG');
//Configuration to Payroll Processing
define('MEALALLOWCODE','22');//Adjustment to Basic


//Time and Attendance 
define('FAIL_LCHOUT','10');// Failure to Lunch Out
define('FAIL_LCHIN','11');// Failure to Lunch In
define('FAIL_LCHINOUT','12');// Failure to Lunch In and Out
define('FAIL_ABSENT','01');// Failure to Lunch In and Out
define('FAIL_LOGIN','04');// Failure to Lunch In and Out
define('FAIL_LOGOUT','05');// Failure to Lunch In and Out
define('FAIL_SKIPLUNCH','09');// Failure to Lunch In and Out

//download path
define('DOWNLOAD_PATH',  'TIMESHEETS/TS_NOT_WITHIN_CUTOFF');
define('DOWNLOAD_PATH_TS',  'TIMESHEETS/TS_BADJ_OTADJ_ALLOW_PDF');


//Pay Category
define('EXEC', '1');
define('CONFI', '2');
define('NONCONFI', '3');


//Payroll Dept Signatory
define('PAYROLLDEPT_SIGNATORY', 'MICHELLE C. LATO');

?>
