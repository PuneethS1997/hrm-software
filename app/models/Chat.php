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

  // Send message
  public function sendMessage($sender, $receiver, $message) {
    $stmt = $this->db->prepare(
      "INSERT INTO chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)"
    );
    return $stmt->execute([$sender, $receiver, $message]);
  }

  // Get conversation between 2 users
  public function getConversation($user1, $user2) {
    $stmt = $this->db->prepare(
      "SELECT c.*, u.name AS sender_name
       FROM chat_messages c
       JOIN users u ON u.id = c.sender_id
       WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?)
       ORDER BY created_at ASC"
    );
    $stmt->execute([$user1, $user2, $user2, $user1]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Mark messages as read
//   public function markRead($user1, $user2) {
//     $stmt = $this->db->prepare(
//       "UPDATE chat_messages SET is_read=1 
//        WHERE sender_id=? AND receiver_id=? AND is_read=0"
//     );
//     return $stmt->execute([$user2, $user1]);
//   }

//    public function send($from, $to, $message) {
//     $stmt = $this->prepare("
//       INSERT INTO messages (sender_id, receiver_id, message)
//       VALUES (?, ?, ?)
//     ");
//     return $stmt->execute([$from, $to, $message]);
//   }

  public function fetch($user1, $user2) {
    $stmt = $this->prepare("
      SELECT * FROM messages
      WHERE 
        (sender_id=? AND receiver_id=?)
        OR
        (sender_id=? AND receiver_id=?)
      ORDER BY id ASC
    ");
    $stmt->execute([$user1, $user2, $user2, $user1]);
    return $stmt->fetchAll();
  }

  public function markRead($from, $to) {
    $stmt = $this->prepare("
      UPDATE messages 
      SET is_read=1 
      WHERE sender_id=? AND receiver_id=?
    ");
    $stmt->execute([$from, $to]);
  }

  public function sendFile($from, $to, $message, $file) {

  $uploadDir = '../public/uploads/chat/';
  $fileName = time().'_'.$file['name'];
  $filePath = $uploadDir.$fileName;

  move_uploaded_file($file['tmp_name'], $filePath);

  $stmt = $this->prepare("
    INSERT INTO messages 
    (sender_id, receiver_id, message, file_path, file_name, file_type)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  return $stmt->execute([
    $from,
    $to,
    $message,
    '/uploads/chat/'.$fileName,
    $file['name'],
    $file['type']
  ]);
}

public function send($from, $to, $message) {
  $stmt = $this->prepare("
    INSERT INTO messages (sender_id, receiver_id, message, delivered_at)
    VALUES (?, ?, ?, NOW())
  ");
  $stmt->execute([$from, $to, $message]);
}

public function markSeen($from, $to) {
  $stmt = $this->prepare("
    UPDATE messages
    SET seen_at = NOW()
    WHERE sender_id=? AND receiver_id=? AND seen_at IS NULL
  ");
  $stmt->execute([$from, $to]);
}


}
