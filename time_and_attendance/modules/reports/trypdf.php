<?php
require('file://///192.168.200.225/www/HRIS/includes/pdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(300,400,'HELLO THERE');
$pdf->Output();
?>