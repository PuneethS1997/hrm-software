<?php
require_once __DIR__.'/../core/Database.php';

class Holiday extends Database {

    public function all()
    {
        return $this->db
        ->query("SELECT * FROM holidays ORDER BY holiday_date ASC")
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function store($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO holidays
            (title, holiday_date, type)
            VALUES (?,?,?)
        ");

        return $stmt->execute([
            $data['title'],
            $data['holiday_date'],
            $data['type']
        ]);
    }

    public function calendarData()
    {
        return $this->db->query("
            SELECT
            holiday_date AS start,
            DATE_ADD(holiday_date, INTERVAL 1 DAY) AS end,
            CONCAT('🎉 ',title) AS title
            FROM holidays
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

}
