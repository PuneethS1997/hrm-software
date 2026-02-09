<?php
class Chat {
  private $db;

  public function __construct() {
    $this->db = new PDO(
      "mysql:host=".DB_HOST.";dbname=".DB_NAME,
      DB_USER,
      DB_PASS
    );
  }

    /* Send Message */
    public function sendMessage($data)
    {
        $sql = "INSERT INTO messages
                (sender_id, receiver_id, message,
                 file_path, file_name, file_type)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['sender_id'],
            $data['receiver_id'],
            $data['message'],
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['file_type'] ?? null
        ]);
    }

    /* Get Conversation */
    public function getConversation($user1, $user2)
    {
        $sql = "SELECT *
                FROM messages
                WHERE
                (sender_id = ? AND receiver_id = ?)
                OR
                (sender_id = ? AND receiver_id = ?)
                ORDER BY created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user1, $user2, $user2, $user1]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Mark Seen */
    public function markSeen($sender, $receiver)
    {
        $sql = "UPDATE messages
                SET seen_at = NOW(), is_read = 1
                WHERE sender_id = ?
                AND receiver_id = ?
                AND is_read = 0";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sender, $receiver]);
    }

}
