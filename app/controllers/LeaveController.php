<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/middleware/RoleMiddleware.php';
require_once '../app/models/Leave.php';

class LeaveController
{
    /* ================= APPLY LEAVE ================= */

    public function apply()
    {
        AuthMiddleware::handle();

        if ($_POST) {
            (new Leave())->apply($_POST, $_SESSION['user']['id']);

            echo json_encode(['success' => true]);
            exit;
        }

        $leaveModel = new Leave();

          $balance = $leaveModel->getBalance(
            $_SESSION['user']['id'],
            $_POST['leave_type_id']
          );

          if ($balance['balance'] <= 0 && $_POST['leave_type_id'] != 4) {
            echo json_encode([
              'success' => false,
              'message' => 'Paid leave exhausted. Only unpaid leave allowed.'
            ]);
            exit;
          }


        echo json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
        exit;
    }

    public function balance()
    {
        AuthMiddleware::handle();
    
        header('Content-Type: application/json');
    
        if (!isset($_GET['leave_type_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Leave type missing'
            ]);
            exit;
        }
    
        $leave = new Leave();
    
        $data = $leave->getBalance(
            $_SESSION['user']['id'],
            $_GET['leave_type_id']
        );
    
        echo json_encode($data);
        exit;
    }
    
    

    /* ================= EMPLOYEE HISTORY ================= */
    public function history()
    {
        AuthMiddleware::handle();
    
        $leave = new Leave();
    
        // Leave types
        $leaveTypes = $leave->getLeaveTypes();
    
        // Employee leave history
        $leaves = $leave->myLeaves($_SESSION['user']['id']);
    
        // Employee leave summary (from leave_balances table)
        $summary = $leave->getLeaveSummary($_SESSION['user']['id']);
    
        require '../app/views/leaves/history.php';
    }
    

    /* ================= ADMIN LIST ================= */

    public function admin()
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['admin','super_admin']);

        $leaveModel = new Leave();
        $leaves = $leaveModel->getAllForAdmin();

        require '../app/views/leaves/admin_list.php';
    }

    /* ================= APPROVE / REJECT ================= */

    public function action()
    {
        AuthMiddleware::handle();
        RoleMiddleware::allow(['admin','super_admin']);

        if (!isset($_POST['id'], $_POST['status'])) {
            header('Location: /crm-hrms/public/leaves/admin');
            exit;
        }

        try {
            (new Leave())->updateStatus(
                $_POST['id'],
                $_POST['status'],          // approved | rejected
                $_POST['remark'] ?? null,
                $_SESSION['user']['id']
            );
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /crm-hrms/public/leaves/admin');
        exit;
    }

    /* ================= CALENDAR ================= */

    public function calendar()
    {
        AuthMiddleware::handle();

        $data = (new Leave())->calendarData($_SESSION['user']['id']);
        echo json_encode($data);
    }

    public function enterpriseCalendar()
{
    AuthMiddleware::handle();

    $leave = new Leave();

    if ($_SESSION['user']['role'] == 'employee') {
        echo json_encode(
            $leave->enterpriseCalendarData($_SESSION['user']['id'])
        );
    } else {
        echo json_encode(
            $leave->enterpriseCalendarData()
        );
    }
}

public function storeLeaveType()
{
    $model = new LeaveModel();

    $model->storeLeaveType(
        $_POST['name'],
        $_POST['max_days'],
        $_POST['carry_forward']
    );

    echo json_encode(['status' => true]);
}

public function listLeaveTypes()
{
    $model = new LeaveModel();
    echo json_encode($model->getLeaveTypes());
}




}
