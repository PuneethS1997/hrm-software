<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once '../app/models/Attendance.php';

class AttendanceController {

    private $attendance;

    public function __construct()
    {
        $this->attendance = new Attendance();
    }

    public function attendance_list()
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['super_admin', 'admin']);

        $date = $_GET['date'] ?? date('Y-m-d');
        $userId = $_GET['user_id'] ?? null;

        $records = $this->attendance->getAdminAttendance($date, $userId);

        require '../app/views/attendance/attendance_list.php';
    }

  public function today()
    {
        AuthMiddleware::handle();
        $data = $this->attendance->today($_SESSION['user']['id']);
        require '../app/views/attendance/today.php';
    }

    public function liveAttendance() {
      AuthMiddleware::handle();
      RoleMiddleware::allow(['super_admin', 'admin']);
      
      $attendance = new Attendance();
      echo json_encode($attendance->getLiveAttendance());
    }
    

   public function checkIn()
{
    AuthMiddleware::handle();
    $this->attendance->checkIn($_SESSION['user']['id']);
    echo json_encode(['status' => 'success']);
}

public function checkOut()
{
    AuthMiddleware::handle();
    $this->attendance->checkOut($_SESSION['user']['id']);
    echo json_encode(['status' => 'success']);
}

// public function status() {
//     header('Content-Type: application/json');
//     AuthMiddleware::handle();
  
//     $attendance = new Attendance();
//     $today = $attendance->getToday($_SESSION['user']['id']);
  
//     if (!$today) {
//       echo json_encode(['status' => 'not_logged_in']);
//       exit;
//     }
  
//     echo json_encode([
//       'status'        => 'active',
//       'login_time'      => $today['login_time'],
//       'logout_time'     => $today['logout_time'],
//       'break_start'   => $today['break_start'],
//       'break_end'     => $today['break_end'],
//       'work_seconds' => $attendance->getLiveWorkSeconds($today)
//     ]);
//     exit;
//   }
  

  public function action() {
    AuthMiddleware::handle();

    $attendance = new Attendance();
    $userId = $_SESSION['user']['id'];
    $action = $_POST['action'] ?? '';

    // $today = $attendance->getToday($userId);
    $current = $attendance->getStatus($userId);


    switch ($action) {
      case 'login':
        if (!$current) {
          $attendance->clockIn($userId);
        }
        break;

      case 'break_start':
        if ($current) {
          $attendance->startBreak($current['id']);
        }
        break;

      case 'break_end':
        if ($current) {
          $attendance->endBreak($current['id']);
        }
        break;

      case 'logout':
        if ($current) {
          $attendance->clockOut($current['id']);
        }
        break;
    }

    echo json_encode(['success' => true]);
  }

  
  public function status() {
    AuthMiddleware::handle();

    $attendance = new Attendance();
    echo json_encode(
      $attendance->getStatus($_SESSION['user']['id'])
    );
  }

}
