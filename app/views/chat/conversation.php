<div class="container mt-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-semibold">
      Chat
    </div>

    <div class="card-body" id="chat-box" style="height:400px; overflow-y:scroll;">

      <?php foreach($conversation as $msg): ?>
      <div class="d-flex <?= $msg['sender_id'] == $_SESSION['user']['id'] ? 'justify-content-end' : 'justify-content-start' ?> mb-2">
        <div class="p-2 rounded <?= $msg['sender_id'] == $_SESSION['user']['id'] ? 'bg-primary text-white' : 'bg-light' ?>">
          <?= htmlspecialchars($msg['message']) ?><br>
          <small class="text-muted"><?= date('H:i', strtotime($msg['created_at'])) ?></small>
        </div>
      </div>
      <?php endforeach; ?>

    </div>

    <div class="card-footer">
      <form id="chat-form" class="d-flex" method="post">
        <input type="hidden" name="receiver_id" value="<?= $receiverId ?>">
        <input type="text" name="message" class="form-control me-2" placeholder="Type a message..." required>
        <button class="btn btn-primary">Send</button>
      </form>
    </div>
  </div>
</div>

<form id="chatForm" enctype="multipart/form-data">
  <input type="hidden" name="receiver_id" value="<?= $receiverId ?>">

  <div class="input-group mt-2">
    <input type="file" name="file" class="form-control">
    <input type="text" name="message" class="form-control" placeholder="Type a message">
    <button class="btn btn-primary">Send</button>
  </div>
</form>

<?php foreach ($conversation as $msg): ?>
  <div class="<?= $msg['sender_id'] == $_SESSION['user']['id'] ? 'text-end' : '' ?>">
    
    <?php if ($msg['file_path']): ?>
      <a href="<?= $msg['file_path'] ?>" target="_blank" class="badge bg-info">
        📎 <?= htmlspecialchars($msg['file_name']) ?>
      </a>
    <?php endif; ?>

    <?php if ($msg['message']): ?>
      <div class="badge bg-secondary"><?= htmlspecialchars($msg['message']) ?></div>
    <?php endif; ?>

  </div>
<?php endforeach; ?>

<?php if ($msg['sender_id'] == $_SESSION['user']['id']): ?>
  <small class="text-muted">
    <?= $msg['seen_at'] ? '✔✔ Seen' : '✔ Delivered' ?>
  </small>
<?php endif; ?>


