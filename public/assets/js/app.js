console.log("app.js loaded");

// login page
function togglePassword() {
  const pwd = document.getElementById('password');
  const icon = document.getElementById('eyeIcon');
  if (pwd.type === 'password') {
    pwd.type = 'text';
    icon.classList.replace('bi-eye', 'bi-eye-slash');
  } else {
    pwd.type = 'password';
    icon.classList.replace('bi-eye-slash', 'bi-eye');
  }
}

// header file





// toggle sidebar
// document.addEventListener("DOMContentLoaded", function () {

//   const toggleBtn = document.getElementById("toggleSidebar");
//   const sidebar   = document.getElementById("sidebar");
//   const content   = document.getElementById("main-content");

//   if (!toggleBtn || !sidebar || !content) return;

//   toggleBtn.addEventListener("click", function () {

//     if (window.innerWidth <= 768) {
//       // 📱 MOBILE
//       sidebar.classList.toggle("show");
//     } else {
//       // 💻 DESKTOP
//       sidebar.classList.toggle("collapsed");
//       content.classList.toggle("expanded");
//     }

//   });

// });

// document.addEventListener("DOMContentLoaded", function () {

//   const toggleBtn = document.getElementById("toggleSidebar");
//   const sidebar = document.getElementById("sidebar");
//   const content = document.querySelector(".main-content"); // ✅ FIX

//   if (!toggleBtn || !sidebar || !content) {
//     console.warn("Sidebar toggle elements missing");
//     return;
//   }

//   toggleBtn.addEventListener("click", function () {

//     if (window.innerWidth <= 768) {
//       // 📱 Mobile
//       sidebar.classList.toggle("show");
//     } else {
//       // 💻 Desktop
//       sidebar.classList.toggle("collapsed");
//       content.classList.toggle("expanded");
//     }

//   });

// });

// employee edit 


document.addEventListener("DOMContentLoaded", function () {

  // EDIT BUTTON CLICK
  document.querySelectorAll('.editEmployeeBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.id;

      fetch(`/crm-hrms/public/employees/fetch/${id}`)
        .then(res => res.json())
        .then(data => {
          document.getElementById("edit_id").value = data.id;
          document.getElementById("edit_employee_code").value = data.employee_code;
          document.getElementById("edit_name").value = data.name;
          document.getElementById("edit_email").value = data.email;
          document.getElementById("edit_phone").value = data.phone;
          document.getElementById("edit_job_role").value = data.job_role;
          document.getElementById("edit_department").value = data.department;
          document.getElementById("edit_joining_date").value = data.joining_date;


          new bootstrap.Modal(
            document.getElementById('editEmployeeModal')
          ).show();
        });
    });
  });

  // SHOW TOAST FUNCTION
  function showToast(message, type = 'success') {
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastEl.role = 'alert';
    toastEl.ariaLive = 'assertive';
    toastEl.ariaAtomic = 'true';
    toastEl.innerHTML = `
          <div class="d-flex">
              <div class="toast-body">${message}</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
      `;
    const container = document.getElementById('toastContainer');
    container.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', () => {
      toastEl.remove();
    });
  }

  // function showToast(message, type = 'success') {
  //   const toast = document.getElementById('toast');
  //   if (!toast) return;

  //   toast.textContent = message;
  //   toast.className = 'toast-msg show';

  //   if (type === 'success') toast.classList.add('toast-success');
  //   if (type === 'error') toast.classList.add('toast-error');
  //   if (type === 'warning') toast.classList.add('toast-warning');

  //   setTimeout(() => {
  //     toast.className = 'toast-msg';
  //   }, 3000);
  // }


  // UPDATE BUTTON CLICK
  const updateBtn = document.getElementById("updateEmployeeBtn");

  updateBtn.addEventListener("click", function (e) {
    e.preventDefault(); // Stop form submit

    const form = document.getElementById("editEmployeeForm");
    const formData = new FormData(form);

    fetch("/crm-hrms/public/employees/update", {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        console.log("SERVER RESPONSE:", data);

        if (data.status === "success") {
          showToast("Employee updated successfully", "success");

          // Update table row dynamically
          const id = form.edit_id.value;
          const row = document.querySelector(`#employeeRow${id}`);
          if (row) {
            row.querySelector('.emp_code').textContent = form.edit_employee_code.value;
            row.querySelector('.emp_name').textContent = form.edit_name.value;
            row.querySelector('.emp_email').textContent = form.edit_email.value;
            row.querySelector('.emp_phone').textContent = form.edit_phone.value;
            row.querySelector('.emp_role').textContent = form.edit_job_role.value;
            row.querySelector('.emp_department').textContent = form.edit_department.value;

            // Highlight updated row
            row.classList.add('table-success');
            setTimeout(() => row.classList.remove('table-success'), 2000);
          }


          // Hide modal
          // const modal = bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal'));
          // modal.hide();

        } else {
          showToast("Update failed: " + data.msg, "danger");
        }
      })
      .catch(err => {
        console.error("FETCH ERROR:", err);
        showToast("Fetch error: " + err, "danger");
      });
  });

});

// Data Table
$(document).ready(function () {
  if ($('#employeeTable').length) {
    $('#employeeTable').DataTable({
      pageLength: 10,
      lengthMenu: [10, 25, 50, 100],
      ordering: true
    });
  }
});


// emplyoee deleting

let deleteType = null;
let deleteId = null;

/* ---------- SINGLE DELETE ---------- */
$(document).on('click', '.delete-btn', function () {
  deleteType = 'single';
  deleteId = $(this).data('id');

  $('#deleteMessage').text(
    'Are you sure you want to delete this employee?'
  );

  new bootstrap.Modal(
    document.getElementById('deleteConfirmModal')
  ).show();
});

// /* ---------- BULK DELETE ---------- */
$('#bulkDeleteBtn').on('click', function () {

  let ids = [];
  $('.trashChk:checked').each(function () {
    ids.push($(this).val());
  });

  if (ids.length === 0) {
    alert('Please select employees');
    return;
  }

  deleteType = 'bulk';
  deleteId = ids;

  $('#deleteMessage').text(
    'Are you sure you want to delete selected employees?'
  );

  new bootstrap.Modal(
    document.getElementById('deleteConfirmModal')
  ).show();
});

// /* ---------- CONFIRM DELETE ---------- */
$('#confirmDeleteBtn').on('click', function () {

  let url = '';
  let postData = {};

  if (deleteType === 'single') {
    url = '/crm-hrms/public/employees/delete';
    postData = { id: deleteId };
  }

  if (deleteType === 'bulk') {
    url = '/crm-hrms/public/employees/bulk-delete';
    postData = { ids: deleteId };
  }

  $.ajax({
    url: url,
    type: 'POST',
    data: postData,
    dataType: 'json',
    success: function (res) {
      console.log(res);

      if (res.status === 'success') {
        $('#deleteConfirmModal').modal('hide');
        alert('Deleted successfully');
        location.reload();
      } else {
        alert(res.msg || 'Delete failed');
      }
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      alert('Server error');
    }
  });
});

// INDIVIDUAL SOFT DELETE
document.addEventListener('click', e => {
  if (!e.target.classList.contains('deleteBtn')) return;

  const id = e.target.dataset.id;

  Swal.fire({
    title: 'Delete employee?',
    text: 'You can undo this for 5 seconds',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete'
  }).then(result => {
    if (!result.isConfirmed) return;

    fetch('/crm-hrms/public/employees/softdelete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
      .then(res => res.text())
      .then(text => {
        console.log("RAW RESPONSE:", text);
        return JSON.parse(text);
      })
      .then(data => {
        if (data.status === 'success') {
          document.querySelector(`tr[data-id="${id}"]`)?.remove();
          showUndoToast([id]);   // ✅ correct function
        } else {
          showToast(data.msg, 'error');
        }
      })
      .catch(err => {
        console.error(err);
        showToast('Delete failed', 'error');
      });

  });
});

// BULK SOFT DELETE
document.getElementById("bulkDeleteBtn")?.addEventListener("click", () => {
  const ids = [...document.querySelectorAll(".rowCheck:checked")]
    .map(cb => cb.value);

  if (!ids.length) return alert("Select employees");

  Swal.fire({
    title: "Delete employees?",
    text: "Undo available for 5 seconds",
    icon: "warning",
    showCancelButton: true
  }).then(res => {
    if (!res.isConfirmed) return;

    fetch("/crm-hrms/public/employees/bulkSoftDelete", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ ids })
    })
      .then(r => r.json())
      .then(() => {
        showUndoToast(ids);
        ids.forEach(id =>
          document.querySelector(`tr[data-id="${id}"]`)?.remove()
        );
      });
  });
});






// Restore single
document.addEventListener('click', function (e) {
  if (!e.target.classList.contains('restoreBtn')) return;

  const id = e.target.dataset.id;

  fetch('/crm-hrms/public/employees/restore', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${id}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        showToast('Employee restored', 'success');
        e.target.closest('tr').remove();
      } else {
        showToast(data.msg, 'error');
      }
    });
});


// Bulk restore


document.addEventListener("DOMContentLoaded", () => {
  const bulkBtn = document.getElementById('bulkRestoreBtn');

  if (bulkBtn) { // Safety check to prevent the error
    bulkBtn.addEventListener("click", () => {
      const ids = [...document.querySelectorAll('.trashChk:checked')]
        .map(cb => cb.value);

      if (!ids.length) return;

      const formData = new FormData();
      ids.forEach(id => formData.append('ids[]', id));

      fetch('/crm-hrms/public/employees/bulkRestore', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            showToast('Employees restored', 'success');
            location.reload();
          }
        });
    });
  }
});



// skeleton loader
window.addEventListener('load', function () {
  const skeleton = document.getElementById('tableSkeleton');
  const table = document.getElementById('employeesTable');

  if (skeleton) skeleton.classList.add('d-none');
  if (table) table.classList.remove('d-none');
});

// csv upload
let selectedCSV = null;

const dropZone = document.getElementById('csvDropZone');
const fileInput = document.getElementById('csvFileInput');
const fileNameEl = document.getElementById('fileName');
const selectedFileBox = document.getElementById('selectedFile');
const uploadBtn = document.getElementById('startUploadBtn');

const progressWrap = document.getElementById('uploadProgressWrap');
const progressBar = document.getElementById('uploadProgressBar');
const progressText = document.getElementById('uploadProgressText');

/* CLICK TO SELECT */
dropZone.addEventListener('click', () => fileInput.click());

/* DRAG EVENTS */
['dragenter', 'dragover'].forEach(evt => {
  dropZone.addEventListener(evt, e => {
    e.preventDefault();
    dropZone.classList.add('dragover');
  });
});

['dragleave', 'drop'].forEach(evt => {
  dropZone.addEventListener(evt, e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
  });
});

/* DROP FILE */
dropZone.addEventListener('drop', e => {
  const file = e.dataTransfer.files[0];
  handleCSV(file);
});

/* FILE INPUT */
fileInput.addEventListener('change', e => {
  handleCSV(e.target.files[0]);
});

function handleCSV(file) {
  if (!file || !file.name.endsWith('.csv')) {
    Swal.fire('Invalid file', 'Please select a CSV file', 'warning');
    return;
  }

  selectedCSV = file;
  fileNameEl.textContent = file.name;
  selectedFileBox.classList.remove('d-none');
  uploadBtn.classList.remove('d-none');
}

/* UPLOAD */
uploadBtn.addEventListener('click', () => {
  if (!selectedCSV) return;

  const formData = new FormData();
  formData.append('csv', selectedCSV);

  progressWrap.classList.remove('d-none');
  progressText.classList.remove('d-none');

  progressBar.style.width = '0%';
  progressText.innerText = 'Uploading… 0%';

  const xhr = new XMLHttpRequest();
  xhr.open('POST', '/crm-hrms/public/employees/bulkUpload', true);

  xhr.upload.onprogress = function (e) {
    if (e.lengthComputable) {
      const percent = Math.round((e.loaded / e.total) * 100);
      progressBar.style.width = percent + '%';
      progressText.innerText = `Uploading… ${percent}%`;
    }
  };

  xhr.onload = function () {
    try {
      const res = JSON.parse(xhr.responseText);

      if (res.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Upload completed',
          html: `
            Inserted: <b>${res.inserted}</b><br>
            Skipped: <b>${res.skipped}</b>
          `
        }).then(() => location.reload());
      } else {
        Swal.fire('Error', res.msg || 'Upload failed', 'error');
      }
    } catch (err) {
      console.error(xhr.responseText);
      Swal.fire('Error', 'Invalid server response', 'error');
    }
  };

  xhr.onerror = function () {
    Swal.fire('Error', 'Upload failed', 'error');
  };

  xhr.send(formData);
});


// attendance
document.addEventListener("DOMContentLoaded", () => {
  if (document.querySelector("#attendanceTable")) {
    new DataTable("#attendanceTable", {
      pageLength: 25,
      ordering: true
    });

  }


});


// leave modules



// document.addEventListener('DOMContentLoaded', function () {
//   const leaveForm = document.getElementById('leaveForm');
//   if (!leaveForm) return;

//   leaveForm.addEventListener('submit', function (e) {
//     e.preventDefault(); // This stops the reload

//     // Create FormData from the current form (this)
//     const formData = new FormData(this);

//     fetch('/crm-hrms/public/leaves/apply', {
//       method: 'POST',
//       body: formData
//       // IMPORTANT: Do NOT set 'Content-Type' header manually; 
//       // the browser needs to set the boundary for multipart/form-data.
//     })
//       .then(res => res.json())
//       .then(data => {
//         if (data.success) {
//           // Verify 'bootstrap' is loaded globally before calling this
//           const canvasElement = document.getElementById('applyLeaveCanvas');
//           if (canvasElement) {
//             const canvas = bootstrap.Offcanvas.getOrCreateInstance(canvasElement);
//             canvas.hide();
//           }

//           showToast('Leave applied successfully ✅', 'success');
//           setTimeout(() => location.reload(), 1200);
//         } else {
//           showToast(data.message || 'Error', 'error');
//         }
//       })
//       .catch(err => {
//         console.error('Fetch error:', err);
//         showToast('Server error occurred', 'error');
//       });
//   });
// });



// remaininh leaves warning
const startDate = document.getElementById('start_date');
const endDate = document.getElementById('end_date');
const totalDaysInput = document.getElementById('total_days');
const leaveType = document.getElementById('leaveType');
const warningBox = document.getElementById('leaveWarning');

function calculateDays() {
  if (!startDate.value || !endDate.value) return;

  const start = new Date(startDate.value);
  const end = new Date(endDate.value);

  if (end < start) {
    totalDaysInput.value = '';
    warningBox.classList.remove('d-none');
    warningBox.innerText = 'End date cannot be before start date';
    return;
  }

  const days =
    Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;

  totalDaysInput.value = days;

  checkLeaveBalance(days);
}

function checkLeaveBalance(days) {
  const selected = leaveType.options[leaveType.selectedIndex];
  const balance = selected?.dataset?.balance;

  if (!balance) return;

  if (days > balance) {
    warningBox.classList.remove('d-none');
    warningBox.innerHTML =
      `⚠ You are applying for <b>${days}</b> days but only <b>${balance}</b> days available.`;
  } else {
    warningBox.classList.add('d-none');
  }
}

startDate.addEventListener('change', calculateDays);
endDate.addEventListener('change', calculateDays);
leaveType.addEventListener('change', () => {
  if (totalDaysInput.value) {
    checkLeaveBalance(parseInt(totalDaysInput.value));
  }
});





// sidear meu
document.addEventListener("DOMContentLoaded", function () {

  initSidebar();
  initTheme();
  initEmployeeEdit();

});


document.addEventListener("DOMContentLoaded", function () {

  console.log("APP JS ACTIVE");

  const toggleBtn = document.getElementById("menuToggle");
  const sidebar = document.getElementById("sidebar");
  const main = document.getElementById("main-content");

  if (!toggleBtn) {
    console.warn("menuToggle button not found");
    return;
  }

  toggleBtn.addEventListener("click", function () {

    console.log("TOGGLE CLICKED");

    if (window.innerWidth <= 768) {

      sidebar.classList.toggle("show");

    } else {

      sidebar.classList.toggle("collapsed");

      if (main) {
        main.classList.toggle("expanded");
      }

    }

  });

});


/* ===========================
   🌙 THEME SYSTEM
=========================== */

document.addEventListener("DOMContentLoaded", function () {

  const themeBtn = document.getElementById("themeToggle");

  function initTheme() {

    if (localStorage.getItem("theme") === "dark") {
      document.body.classList.add("dark-mode");
    }

  }

  initTheme();

  if (themeBtn) {

    themeBtn.addEventListener("click", function () {

      document.body.classList.toggle("dark-mode");

      localStorage.setItem(
        "theme",
        document.body.classList.contains("dark-mode")
          ? "dark"
          : "light"
      );

    });

  }

});




