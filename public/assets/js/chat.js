// document.getElementById('chat-form').addEventListener('submit', function(e) {
//   e.preventDefault();
//   let formData = new FormData(this);

//   fetch('/chat/send', {
//     method: 'POST',
//     body: formData
//   }).then(() => {
//     document.querySelector('input[name="message"]').value = '';
//     loadChat();
//   });
// });

// function loadChat() {
//   fetch(window.location.href)
//     .then(res => res.text())
//     .then(html => {
//       document.getElementById('chat-box').innerHTML = new DOMParser().parseFromString(html,'text/html').getElementById('chat-box').innerHTML;
//       document.getElementById('chat-box').scrollTop = document.getElementById('chat-box').scrollHeight;
//     });
// }

// // Poll every 3 seconds
// setInterval(loadChat, 3000);
// let activeUser = null;

// document.querySelectorAll('.chat-user').forEach(user => {
//   user.onclick = () => {
//     activeUser = user.dataset.id;
//     loadMessages();
//     setInterval(loadMessages, 3000);
//   };
// });

// function loadMessages() {
//   if (!activeUser) return;

//   fetch('/chat/fetch?user=' + activeUser)
//     .then(res => res.json())
//     .then(data => {
//       let html = '';
//       data.forEach(m => {
//         html += `<div class="${m.sender_id == USER_ID ? 'text-end' : ''}">
//           <span class="badge bg-${m.sender_id == USER_ID ? 'primary' : 'secondary'}">
//             ${m.message}
//           </span>
//         </div>`;
//       });
//       document.getElementById('chatBox').innerHTML = html;
//     });
// }

// function sendMessage() {
//   fetch('/chat/send', {
//     method: 'POST',
//     headers: {'Content-Type': 'application/x-www-form-urlencoded'},
//     body: `to=${activeUser}&message=${message.value}`
//   }).then(() => message.value = '');
// }
// document.getElementById('chatForm').onsubmit = function(e) {
//   e.preventDefault();

//   let formData = new FormData(this);

//   fetch('/chat/send', {
//     method: 'POST',
//     body: formData
//   }).then(() => {
//     this.reset();
//     loadMessages();
//   });
// };
