<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once __DIR__ . '/../models/Dashboard.php';


class DashboardController {
  public function index() {
    AuthMiddleware::handle();

    $role = $_SESSION['user']['role'];
    $dash = new Dashboard();

    switch ($role) {

      case 'super_admin':
        $data = [
          'employees' => $dash->totalEmployees(),
          'attendance' => $dash->todayAttendance(),
          'leaves' => $dash->pendingLeaves(),
          'payroll' => $dash->monthlyPayroll(),
          // 'chart' => $dash->attendanceChart()
        ];
        require '../app/views/dashboard/super_admin.php';
        break;

      case 'admin':
        $data = [
          'employees' => $dash->totalEmployees(),
          'attendance' => $dash->todayAttendance(),
          'payroll' => $dash->monthlyPayroll(),
          'leaves' => $dash->pendingLeaves(),
          // 'chart' => $dash->attendanceChart()
        ];
        require '../app/views/dashboard/admin.php';
        break;

      case 'employee':
        $summary = (new Leave())->getLeaveSummary($_SESSION['user']['id']);
                $data = [
          'attendance' => $dash->myAttendance($_SESSION['user']['id']),
          'leaves' => $dash->myLeaves($_SESSION['user']['id']),
          'payroll' => $dash->myPayroll($_SESSION['user']['id'])

        ];
        require '../app/views/dashboard/employee.php';
        break;

      default:
        header('Location: /logout');
    }
      echo $_SESSION['user']['role'];

  }             

  public function admin() {
    AuthMiddleware::handle();
    RoleMiddleware::allow(['super_admin','admin']);
    echo "Admin Dashboard";
  }

  public function employee() {
    AuthMiddleware::handle();
    RoleMiddleware::allow(['employee']);
    echo "Employee Dashboard";
  }
}
