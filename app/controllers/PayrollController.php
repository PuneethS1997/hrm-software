<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once '../app/models/Payroll.php';
require_once '../app/models/User.php';
require_once '../app/models/Notification.php';
require_once '../app/services/EmailService.php';
require_once '../app/services/SmsService.php';

class PayrollController {

  public function index() {
    AuthMiddleware::handle();

    $month = $_GET['month'] ?? date('Y-m');
    $payroll = new Payroll();

    if ($_SESSION['user']['role'] === 'employee') {
      $data = $payroll->getPayroll($_SESSION['user']['id'], $month);
      require '../app/views/payroll/employee.php';
    } else {
      RoleMiddleware::allow(['super_admin','admin']);
      $data = $payroll->allPayrolls($month);
      require '../app/views/payroll/admin.php';
    }
  }

  public function generate() {
    AuthMiddleware::handle();
    RoleMiddleware::allow(['super_admin','admin']);

    if ($_POST) {
      $payroll = new Payroll();
      $payroll->generate(
        $_POST['user_id'],
        $_POST['month'],
        $_POST['basic_salary'],
        $_POST['allowances']
      );
      header('Location: /payroll?month=' . $_POST['month']);
      $payroll->generate($_POST);

      $user = (new User())->find($_POST['user_id']);

      // 🔔 Internal Notification
      (new Notification())->create(
        $user['id'],
        'Salary Generated',
        'Your salary slip is ready',
        '/payroll/slip'
      );

      // 📧 Email
      EmailService::send(
        $user['email'],
        'Salary Slip Generated',
        '<p>Your salary has been generated. Please login to download payslip.</p>'
      );

      // 📱 SMS
      SmsService::send(
        $user['mobile'],
        'Your salary has been generated. Login to download payslip.'
      );

      header('Location: /payroll');
      exit;
    }
    }

    require '../app/views/payroll/generate.php';
  }
}
