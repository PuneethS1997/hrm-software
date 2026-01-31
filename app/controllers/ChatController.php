<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/Chat.php';

class ChatController {

  // Chat user list
  public function index() {
    AuthMiddleware::handle();
    require '../app/views/chat/index.php';
  }

  // Open a conversation
  public function conversation($receiverId) {
    AuthMiddleware::handle();

    $chat = new Chat();
    $conversation = $chat->fetch(
      $_SESSION['user']['id'],
      $receiverId
    );

    $chat->markRead($receiverId, $_SESSION['user']['id']);
    
  // ✔✔ Seen
  $chat->markSeen($receiverId, $_SESSION['user']['id']);

    require '../app/views/chat/conversation.php';
  }

  // Send message
 public function send() {
  AuthMiddleware::handle();

  $chat = new Chat();

  if (!empty($_FILES['file']['name'])) {
    $chat->sendFile(
      $_SESSION['user']['id'],
      $_POST['receiver_id'],
      $_POST['message'] ?? '',
      $_FILES['file']
    );
  } else {
    $chat->send(
      $_SESSION['user']['id'],
      $_POST['receiver_id'],
      $_POST['message']
    );
  }
}


  // Fetch messages (AJAX polling)
  public function fetch() {
    AuthMiddleware::handle();

    echo json_encode(
      (new Chat())->fetch(
        $_SESSION['user']['id'],
        $_GET['user']
      )
    );
  }



}
