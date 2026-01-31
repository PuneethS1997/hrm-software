function showToast(message, type = 'success') {
  let toast = document.createElement('div');
  toast.className = `toast-msg toast-${type}`;
  toast.innerHTML = message;

  document.body.appendChild(toast);

  setTimeout(() => toast.classList.add('show'), 100);

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}


// function showUndoToast(ids) {
//   const toast = document.createElement("div");
//   toast.className = "undo-toast";
//   toast.innerHTML = `
//     Employees deleted
//     <button id="undoBtn">Undo</button>
//   `;

//   document.body.appendChild(toast);

//   const autoClose = setTimeout(() => {
//     toast.remove();
//   }, 5000);

//   document.getElementById("undoBtn").onclick = () => {
//     clearTimeout(autoClose);

//     fetch("/crm-hrms/public/employees/bulkUndo", {
//       method: "POST",
//       headers: { "Content-Type": "application/json" },
//       body: JSON.stringify({ ids })
//     })
//     .then(res => res.json())
//     .then(data => {
//       if (data.status === 'success') {
//         toast.remove();

//         // ✅ FAST & SAFE
//         location.reload();
//       }
//     });
//   };
// }

function showUndoToast(ids) {
  const toast = document.createElement("div");
  toast.className = "undo-toast";
  toast.innerHTML = `
    <span>Employees deleted</span>
    <button id="undoBtn">Undo</button>
  `;

  document.body.appendChild(toast);

  let undone = false;

  const timer = setTimeout(() => {
    if (!undone) {
      toast.remove();
      showToast('Employees deleted successfully', 'success');

      // ✅ auto refresh list after delete confirmed
      setTimeout(() => {
        location.reload();
      }, 800);
    }
  }, 5000);

  document.getElementById("undoBtn").onclick = () => {
    undone = true;
    clearTimeout(timer);

    fetch("/crm-hrms/public/employees/bulkUndo", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ids })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        toast.remove();
        showToast('Undo successful', 'success');

        setTimeout(() => {
          location.reload();
        }, 800);
      }
    });
  };
}


