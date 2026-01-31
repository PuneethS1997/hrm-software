<div class="container-fluid py-3">
  <div class="row">
    <div class="col-md-4 border-end">
      <h6 class="fw-bold">Employees</h6>
      <ul class="list-group">
        <!-- Loop users here -->
        <li class="list-group-item chat-user" data-id="2">
          John Doe
          <span class="badge bg-success float-end">●</span>
        </li>
      </ul>
    </div>

    <div class="col-md-8">
      <div id="chatBox" class="border rounded p-3" style="height:400px; overflow-y:auto"></div>

      <div class="input-group mt-2">
        <input type="text" id="message" class="form-control" placeholder="Type message">
        <button class="btn btn-primary" onclick="sendMessage()">Send</button>
      </div>
    </div>
  </div>
</div>

<?php foreach ($users as $user): ?>
  <li class="list-group-item chat-user" data-id="<?= $user['id'] ?>">
    <?= $user['name'] ?>
    <span class="float-end">
      <?= $user['online'] ? '🟢' : '⚪' ?>
    </span>
  </li>
<?php endforeach; ?>



<script src="/assets/js/chat.js"></script>
