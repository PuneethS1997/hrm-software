<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once '../app/models/Leave.php';

class LeaveController {

  // public function apply() {
  //   AuthMiddleware::handle();
  
  //   $leave = new Leave();
  //   $leaveTypes = $leave->getLeaveTypes();
  
  //   if ($_POST) {
  //     $leave->apply($_POST, $_SESSION['user']['id']);
  //     header('Location: /crm-hrms/public/leaves/history');
  //     exit;
  //   }
  
  //   require '../app/views/leaves/apply.php';
  // }
  public function apply() {
    AuthMiddleware::handle();

    if ($_POST) {
        $leave = new Leave();
        $leave->apply($_POST, $_SESSION['user']['id']);

        echo json_encode([
            'success' => true
        ]);
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

  
  

  public function history() {
    AuthMiddleware::handle();
    $leave = new Leave();
    $leaveTypes = $leave->getLeaveTypes();
    $leaves = $leave->myLeaves($_SESSION['user']['id']);
    $leaveBalance = [
      1 => 10, // Casual
      2 => 5,  // Sick
      3 => 8,  // Paid
      4 => 999 // Unpaid
    ];
    
    

    require '../app/views/leaves/history.php';
  }

  public function admin() {
    AuthMiddleware::handle();
    RoleMiddleware::allow(['admin','super_admin']);

    $leave = new Leave();
    $leaves = $leave->pending();

    require '../app/views/leaves/admin_list.php';
  }

  public function action()
  {
      AuthMiddleware::handle();
      RoleMiddleware::allow(['admin','super_admin']);
  
      if ($_POST) {
          (new Leave())->updateStatus(
              $_POST['id'],                // leave id
              $_POST['status'],            // approved / rejected
              $_POST['remark'] ?? null,    // admin remark
              $_SESSION['user']['id']      // admin user id
          );
      }

      $leave = new Leave();

      if ($_POST['status'] === 'approved') {
        if (!$leave->hasSufficientBalance($_POST['id'])) {
          $_SESSION['error'] = 'Insufficient leave balance';
          header('Location: /crm-hrms/public/leaves/admin');
          exit;
        }
      }
      if ($_POST['status'] === 'rejected') {
        $leave->rollbackBalance($_POST['id']);
      }
      
     
  }
  

  public function balance() {
    AuthMiddleware::handle();
  
    $leave = new Leave();
    $data = $leave->getBalance(
      $_SESSION['user']['id'],
      $_GET['leave_type_id']
    );
  
    echo json_encode($data);
  }
  
  public function approveReject() {
    AuthMiddleware::handle();
    RoleMiddleware::allow(['admin','super_admin']);
  
    (new Leave())->updateStatus(
      $_POST['leave_id'],
      $_POST['status'],      // approved | rejected
      $_POST['remark'],
      $_SESSION['user']['id']
    );

    if ($_POST['status'] === 'approved') {
      // Example: mark unpaid leave as absent
      if ($_POST['leave_type'] == 'Unpaid Leave') {
        (new Attendance())->markLeaveAsAbsent(
          $_POST['user_id'],
          $_POST['start_date'],
          $_POST['end_date']
        );
      }
    }
    
  
    echo json_encode(['success' => true]);
  }




  public function calendar() {
    AuthMiddleware::handle();
  
    $data = (new Leave())->calendarData($_SESSION['user']['id']);
    echo json_encode($data);
  }
  
  
}
