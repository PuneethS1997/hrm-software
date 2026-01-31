// setInterval(fetchNotifications, 5000);

// function fetchNotifications() {
//   fetch('/notifications/fetch')
//     .then(res => res.json())
//     .then(data => {
//       document.getElementById('notifCount').innerText = data.length || '';
//       let html = '';
//       data.forEach(n => {
//         html += `
//           <li>
//             <a href="${n.link}" onclick="markRead(${n.id})" class="dropdown-item">
//               <strong>${n.title}</strong><br>
//               <small>${n.message}</small>
//             </a>
//           </li>
//         `;
//       });
//       document.getElementById('notifList').innerHTML = html || '<li class="text-center">No notifications</li>';
//     });
// }

// function markRead(id) {
//   fetch('/notifications/read/' + id);
// }
