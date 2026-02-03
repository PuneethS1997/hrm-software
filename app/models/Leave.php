<?php
require_once __DIR__ . '/../core/Database.php';

class Leave extends Database
{
    /* ================= APPLY LEAVE ================= */

    public function apply($data, $userId)
    {
        $stmt = $this->db->prepare("
            INSERT INTO leaves
            (user_id, leave_type_id, start_date, end_date, total_days, reason)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $userId,
            $data['leave_type_id'],
            $data['start_date'],
            $data['end_date'],
            $data['total_days'],
            $data['reason']
        ]);
    }

    /* ================= LEAVE TYPES ================= */

    public function getLeaveTypes()
    {
        return $this->db
            ->query("SELECT * FROM leave_types")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= EMPLOYEE LEAVES ================= */

    public function myLeaves($userId)
    {
        $stmt = $this->db->prepare("
            SELECT l.*, lt.name AS leave_type
            FROM leaves l
            JOIN leave_types lt ON lt.id = l.leave_type_id
            WHERE l.user_id = ?
            ORDER BY l.applied_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ================= ADMIN LIST ================= */

        public function getAllForAdmin()
    {
    $stmt = $this->db->prepare("
        SELECT
            l.id,
            l.user_id,
            l.start_date,
            l.end_date,
            l.total_days,
            l.total_days AS requested_days,
            l.reason,
            l.status,

            u.name AS name,
            lt.name AS leave_type,

            COALESCE(lb.balance, 0) AS balance

        FROM leaves l
        JOIN users u ON u.id = l.user_id
        JOIN leave_types lt ON lt.id = l.leave_type_id
        LEFT JOIN leave_balances lb
            ON lb.user_id = l.user_id

        WHERE l.status = 'pending'
        ORDER BY l.applied_at DESC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

    /* ================= APPROVE / REJECT ================= */

    public function updateStatus($id, $status, $remark, $adminId)
    {
        $this->db->beginTransaction();

        try {
            // update leave row
            $stmt = $this->db->prepare("
                UPDATE leaves SET
                    status = ?,
                    admin_remark = ?,
                    approved_by = ?,
                    approved_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$status, $remark, $adminId, $id]);

            if ($status === 'approved') {
                if (!$this->hasSufficientBalance($id)) {
                    throw new Exception('Insufficient leave balance');
                }
                $this->deductLeaveBalance($id);
            }

            if ($status === 'rejected') {
                // nothing to deduct
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /* ================= BALANCE CHECK ================= */

    public function hasSufficientBalance($leaveId)
    {
        $stmt = $this->db->prepare("
            SELECT l.total_days, lb.balance
            FROM leaves l
            JOIN leave_balances lb ON lb.user_id = l.user_id
            WHERE l.id = ?
        ");
        $stmt->execute([$leaveId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row && $row['balance'] >= $row['total_days'];
    }

    /* ================= DEDUCT BALANCE ================= */

    private function deductLeaveBalance($leaveId)
    {
        $stmt = $this->db->prepare("
            SELECT user_id, total_days
            FROM leaves
            WHERE id = ?
        ");
        $stmt->execute([$leaveId]);
        $leave = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$leave) {
            throw new Exception('Leave not found');
        }

        $stmt = $this->db->prepare("
            UPDATE leave_balances
            SET
                used = used + ?,
                balance = balance - ?
            WHERE user_id = ?
        ");
        $stmt->execute([
            $leave['total_days'],
            $leave['total_days'],
            $leave['user_id']
        ]);
    }

    /* ================= LEAVE SUMMARY ================= */

    public function getLeaveSummary($userId)
    {
        $stmt = $this->db->prepare("
            SELECT total, used, balance
            FROM leave_balances
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



}
