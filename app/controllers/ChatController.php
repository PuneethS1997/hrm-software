<?php
require_once '../app/middleware/AuthMiddleware.php';
require_once '../app/models/Chat.php';

class ChatController {

  // Chat user list
  // public function index() {
  //   AuthMiddleware::handle();
  //   require '../app/views/chat/index.php';
  // }

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
  /* Chat Page */
  public function index($receiverId)
  {
      $data['receiverId'] = $receiverId;

      require '../app/views/chat/index.php';
  }

  /* Send Message */
  public function send()
  {
      $input = json_decode(file_get_contents("php://input"), true);

      $data = [
          'sender_id' => $_SESSION['user']['id'],
          'receiver_id' => $input['receiver_id'],
          'message' => $input['message']
      ];

      $this->chat->sendMessage($data);

      echo json_encode(['status' => 'success']);
  }

  /* Fetch Messages */
  public function fetch($receiverId)
  {
      $sender = $_SESSION['user']['id'];

      $messages = $this->chat
          ->getConversation($sender, $receiverId);

      $this->chat->markSeen($receiverId, $sender);

      echo json_encode($messages);
  }
}
