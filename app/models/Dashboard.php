

<?php
require_once __DIR__ . '/../core/Database.php'; 

class Dashboard extends Database {

    public function __construct() {
        parent::__construct(); // VERY IMPORTANT
    }

    /* ================= ADMIN / SUPER ADMIN ================= */

    public function totalEmployees() {
        return $this->db->query("
            SELECT COUNT(*) FROM users WHERE status = 1
        ")->fetchColumn();
    }

    public function todayAttendance() {
        return $this->db->query("
            SELECT COUNT(*) FROM attendance
            WHERE DATE(login_time) = CURDATE()
        ")->fetchColumn();
    }

    public function pendingLeaves() {
        return $this->db->query("
            SELECT COUNT(*) FROM leaves WHERE status = 'pending'
        ")->fetchColumn();
    }

    public function monthlyPayroll() {
        return $this->db->query("
            SELECT IFNULL(SUM(net_salary),0)
            FROM payroll
            WHERE month = DATE_FORMAT(NOW(), '%Y-%m')
        ")->fetchColumn();
    }

//     public function attendanceChart() {

//     $stmt = $this->db->prepare("
//         SELECT 
//             DATE(date) as day,
//             SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count
//         FROM attendance
//         WHERE date >= CURDATE() - INTERVAL 6 DAY
//         GROUP BY DATE(date)
//         ORDER BY day ASC
//     ");

//     $stmt->execute();
//     return $stmt->fetchAll(PDO::FETCH_ASSOC);
//   }


    /* ================= EMPLOYEE ================= */

    public function myAttendance($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM attendance
            WHERE user_id = ?
              AND status = 'present'
              AND DATE_FORMAT(login_time, '%Y-%m') = DATE_FORMAT(NOW(), '%Y-%m')
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function myLeaves($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM leaves
            WHERE user_id = ?
              AND status IN ('pending','approved')
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public function myPayroll($userId) {
        $stmt = $this->db->prepare("
            SELECT net_salary
            FROM payroll
            WHERE user_id = ?
              AND month = DATE_FORMAT(NOW(), '%Y-%m')
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?? 0;
    }
}


