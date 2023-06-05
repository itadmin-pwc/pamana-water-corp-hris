<?
$conn = mysqli_connect("localhost","root","","hris");



//$rs = mysqli_query($conn,"Update tblEmpMast set compCode=2 where empNo='010002428'");
$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='166000001' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='166000001' and cast(tsDate as date)= '2014-03-08'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='140000752' and cast(tsDate as date)= '2014-03-04' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='140000752' and cast(tsDate as date)= '2014-03-09'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='146000022' and cast(tsDate as date)= '2014-03-04' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='146000022' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='150001098' and cast(tsDate as date)= '2014-03-02' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='150001098' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='146000016' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='146000016' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='030001249' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='030001249' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='430000055' and cast(tsDate as date)= '2014-03-02' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='430000055' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='420000057' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='420000057' and cast(tsDate as date)= '2014-03-09'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='350000252' and cast(tsDate as date)= '2014-03-04' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='350000252' and cast(tsDate as date)= '2014-03-09'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='224000020' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='224000020' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='000900002' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='000900002' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='001400005' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='001400005' and cast(tsDate as date)= '2014-03-08'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='154000010' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='154000010' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='220000711' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='220000711' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='450000010' and cast(tsDate as date)= '2014-03-04' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='450000010' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='206000003' and cast(tsDate as date)= '2014-03-03' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='206000003' and cast(tsDate as date)= '2014-03-11'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='167000009' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='167000009' and cast(tsDate as date)= '2014-03-09'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='210001522' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='210001522' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='186000019' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='186000019' and cast(tsDate as date)= '2014-03-08'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='154000029' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='154000029' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='000800005' and cast(tsDate as date)= '2014-03-04' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='000800005' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='143000026' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='143000026' and cast(tsDate as date)= '2014-03-08'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='243000004' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='243000004' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='03' where empNo='450000058' and cast(tsDate as date)= '2014-03-06' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='450000058' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='140000091' and cast(tsDate as date)= '2014-03-05' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='140000091' and cast(tsDate as date)= '2014-03-09'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='', shftLunchOut='', shftLunchIn='', shftBreakOut='', shftBreakIn='', shftTimeOut='',dayType='01' where empNo='146000022' and cast(tsDate as date)= '2014-03-04' and compCode='2'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";

$sql = "Update tblTK_Timesheet set crdTag='Y',shftTimeIn='00:00', shftLunchOut='00:00', shftLunchIn='00:00', shftBreakOut='00:00', shftBreakIn='00:00', shftTimeOut='00:00',dayType='02' where compCode='2' and empNo='146000022' and cast(tsDate as date)= '2014-03-07'; ";
$rs = mysqli_multi_query($conn,$sql);
echo var_dump($rs);echo "<br>";



?>