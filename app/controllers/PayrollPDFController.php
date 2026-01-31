<?php
require_once '../app/models/Payroll.php';
require_once '../vendor/fpdf/fpdf.php'; // Use composer or manual include

class PayrollPDFController {

  public function generate() {
    $payroll = new Payroll();
    $data = $payroll->getPayroll($_GET['user_id'], $_GET['month']);

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'Salary Slip',0,1,'C');

    $pdf->SetFont('Arial','',12);
    $pdf->Ln(10);
    $pdf->Cell(50,10,'Name:',0,0);
    $pdf->Cell(0,10,$_GET['name'] ?? 'Employee',0,1);

    $pdf->Cell(50,10,'Basic Salary:',0,0);
    $pdf->Cell(0,10,$data['basic_salary'],0,1);

    $pdf->Cell(50,10,'Allowances:',0,0);
    $pdf->Cell(0,10,$data['allowances'],0,1);

    $pdf->Cell(50,10,'Deductions:',0,0);
    $pdf->Cell(0,10,$data['deductions'],0,1);

    $pdf->Cell(50,10,'Total Salary:',0,0);
    $pdf->Cell(0,10,$data['total_salary'],0,1);

    $pdf->Output('I','SalarySlip_'.$data['month'].'.pdf');
  }
}
