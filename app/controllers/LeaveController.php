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

        echo json_encode([
            'success' => false,
            'message' => 'Invalid request'
        ]);
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
}
