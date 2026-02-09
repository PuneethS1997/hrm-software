<?php require '../app/views/layouts/header.php'; ?>
<?php require '../app/views/layouts/sidebar.php'; ?>

<div id="main-content" class="main-content">

<div class="chat-wrapper">

    <div id="chatBox"></div>

    <div class="chat-input">
        <input type="text" id="messageInput"
               placeholder="Type message...">

        <button onclick="sendMessage()">Send</button>
    </div>

</div>

</div>

<script>

const receiverId = <?= $data['receiverId'] ?>;

function loadMessages()
{
    fetch(`/crm-hrms/public/chat/fetch/${receiverId}`)
    .then(r=>r.json())
    .then(data=>{

        let html = '';

        data.forEach(m=>{
            html += `
            <div class="msg">
                <b>${m.sender_id}</b>
                ${m.message}
            </div>`;
        });

        document.getElementById('chatBox').innerHTML = html;
    });
}

function sendMessage()
{
    const msg = document.getElementById('messageInput').value;

    fetch('/crm-hrms/public/chat/send',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({
            receiver_id:receiverId,
            message:msg
        })
    })
    .then(()=>{

        document.getElementById('messageInput').value='';
        loadMessages();

    });
}

setInterval(loadMessages,2000);
loadMessages();

</script>

<?php require '../app/views/layouts/footer.php'; ?>
