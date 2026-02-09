<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Attendance.php';

class AuthController {

    public function login() {

        // If already logged in → go to dashboard
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if ($_POST) {

            $user = new User();

            if ($user->login($_POST['email'], $_POST['password'])) {

                // Mark attendance login
                $attendance = new Attendance();
                $attendance->checkIn($_SESSION['user']['id']);

                // Redirect to dashboard
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            }

            // Optional: error message
            $error = "Invalid email or password";
        }

        require __DIR__ . '/../views/auth/login.php';
    }



public function logout()
{
    session_start();
    $_SESSION = [];
    session_destroy();

    header('Location: ' . BASE_URL);
    exit;
}


}
