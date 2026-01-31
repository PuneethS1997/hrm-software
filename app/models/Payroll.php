<?php
require_once '../app/models/Attendance.php';
require_once '../app/models/Leave.php';

class Payroll {
  private $db;

  public function __construct() {
    $this->db = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME,
      DB_USER,
      DB_PASS
    );
  }

  // Generate payroll for a user for a specific month
  public function generate($userId, $yearMonth, $basicSalary, $allowances = 0) {
    // Calculate deductions based on absent days
    $leaveModel = new Leave();
    $attendanceModel = new Attendance();

    // Count absent days
    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as absent_days 
       FROM attendance
       WHERE user_id=? AND status='absent' AND DATE_FORMAT(date,'%Y-%m')=?"
    );
    $stmt->execute([$userId, $yearMonth]);
    $absentDays = $stmt->fetch(PDO::FETCH_ASSOC)['absent_days'];

    // Assume deduction per day = basicSalary / 30
    $perDayDeduction = $basicSalary / 30;
    $deductions = $absentDays * $perDayDeduction;

    $netSalary = $basicSalary + $allowances - $deductions;

    // Insert into payroll table
    $stmt2 = $this->db->prepare(
        "INSERT INTO payroll
        (user_id, month, basic_salary, allowances, deductions, net_salary)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            allowances=?,
            deductions=?,
            net_salary=?"
        );

        return $stmt2->execute([
        $userId,
        $yearMonth,
        $basicSalary,
        $allowances,
        $deductions,
        $netSalary,
        $allowances,
        $deductions,
        $netSalary
        ]);

  }

  // Get payroll for employee
  public function getPayroll($userId, $yearMonth) {
    $stmt = $this->db->prepare(
      "SELECT * FROM payroll WHERE user_id=? AND month=?"
    );
    $stmt->execute([$userId, $yearMonth]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Admin: Get all payrolls for a month
  public function allPayrolls($yearMonth) {
    $stmt = $this->db->prepare(
      "SELECT p.*, u.name, u.email
       FROM payroll p
       JOIN users u ON u.id=p.user_id
       WHERE month=?
       ORDER BY u.name ASC"
    );
    $stmt->execute([$yearMonth]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
