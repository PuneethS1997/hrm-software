<?php
require_once '../app/models/Attendance.php';

class LogoutController {
  public function index() {
    if (isset($_SESSION['user'])) {
      $attendance = new Attendance();
      $attendance->markLogout($_SESSION['user']['id']);
    }

    session_destroy();
    header('Location: /auth/login');
  }
}
