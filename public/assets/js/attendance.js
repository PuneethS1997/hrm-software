console.log("attendance.js loaded");


// $("#checkInBtn").on("click", () => {
//   fetch("/crm-hrms/public/attendance/check-in")
//     .then(r => r.json())
//     .then(() => location.reload());
// });

// $("#checkOutBtn").on("click", () => {
//   fetch("/crm-hrms/public/attendance/check-out")
//     .then(r => r.json())
//     .then(() => location.reload());
// });




let uiInterval = null;
let syncInterval = null;

let loginTime = null;
let breakSeconds = 0;
let isOnBreak = false;
let isLoggedIn = false;

/* ---------- UI ---------- */

function updateTimerUI(seconds) {
  const el = document.getElementById('liveTimer');
  if (!el) return;

  const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
  const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
  const s = String(seconds % 60).padStart(2, '0');

  el.textContent = `${h}:${m}:${s}`;
}

function updateBreakUI(onBreak) {
  const dot = document.querySelector('.live-dot');
  if (!dot) return;

  dot.style.animationPlayState = onBreak ? 'paused' : 'running';
}

/* ---------- CALC ---------- */

// function calculateWorkedSeconds() {
//   if (!loginTime) return 0;

//   const now = new Date();
//   const login = new Date(loginTime);

//   const seconds =
//     Math.floor((now - login) / 1000) - breakSeconds;

//   return seconds > 0 ? seconds : 0;
// }

function calculateWorkedSeconds() {
  if (!loginTime) return 0;
  const now = new Date();
  return Math.max(
    Math.floor((now - new Date(loginTime)) / 1000) - breakSeconds,
    0
  );
}


/* ---------- TIMER ---------- */

// function stopUITimer() {
//   if (uiInterval) {
//     clearInterval(uiInterval);
//     uiInterval = null;
//   }
// }

// function startUITimer() {
//   stopUITimer();

//   uiInterval = setInterval(() => {
//     updateTimerUI(calculateWorkedSeconds());
//   }, 1000);
// }

function startUITimer() {
  stopUITimer();
  uiInterval = setInterval(() => {
    if (isLoggedIn && !isOnBreak) {
      updateTimerUI(calculateWorkedSeconds());
    }
  }, 1000);
}

function stopUITimer() {
  if (uiInterval) {
    clearInterval(uiInterval);
    uiInterval = null;
  }
}



/* ---------- SERVER ---------- */

// function syncFromServer() {
//   fetch('/crm-hrms/public/attendance/status')
//     .then(r => r.json())
//     .then(d => {
//       if (!d || !d.login_time) return;

//       loginTime = d.login_time;
//       breakSeconds = parseInt(d.break_seconds || 0);
//       isOnBreak = d.status === 'break';

//       updateTimerUI(calculateWorkedSeconds());
//       updateBreakUI(isOnBreak);

//       if (!isOnBreak && !uiInterval) {
//         startUITimer();
//       }

//       if (isOnBreak) {
//         stopUITimer();
//       }
//     });
// }

// function syncFromServer() {
//   fetch('/crm-hrms/public/attendance/status')
//     .then(r => r.json())
//     .then(d => {

//       // 🔒 LOGGED OUT = STOP EVERYTHING
//       if (!d || d.status === 'logged_out') {
//         stopUITimer();
//         stopAutoSync();
//         return;
//       }

//       loginTime = d.login_time;
//       breakSeconds = parseInt(d.break_seconds || 0);
//       isOnBreak = d.status === 'break';

//       updateTimerUI(calculateWorkedSeconds());
//       updateBreakUI(isOnBreak);

//       if (!isOnBreak && !uiInterval) startUITimer();
//       if (isOnBreak) stopUITimer();
//     });
// }

function syncFromServer() {
  fetch('/crm-hrms/public/attendance/status')
    .then(r => r.json())
    .then(d => {

      // 🔴 LOGGED OUT
      if (!d || d.status === 'logged_out') {
        hardLogout();
        return;
      }

      isLoggedIn = true;
      loginTime = d.login_time;
      breakSeconds = parseInt(d.break_seconds || 0);
      isOnBreak = d.status === 'break';

      updateTimerUI(calculateWorkedSeconds());
      updateBreakUI(isOnBreak);

      if (isOnBreak) stopUITimer();
      else startUITimer();
    });
}




function startAutoSync() {
  syncInterval = setInterval(syncFromServer, 60000);
}

/* ---------- ENTRY ---------- */

function initLiveTimer() {
  syncFromServer();
  startUITimer();
  startAutoSync();
}

/* ---------- ACTIONS ---------- */

// function attendance(action) {

//   // 🔥 IMMEDIATE UI CONTROL
//   if (action === 'break_start') {
//     isOnBreak = true;
//     stopUITimer();          // ⏸ HARD PAUSE
//     updateBreakUI(true);
//   }

//   if (action === 'break_end') {
//     isOnBreak = false;
//     startUITimer();         // ▶ RESUME
//     updateBreakUI(false);
//   }

//   if (action === 'logout') {
//     stopUITimer();        // ⛔ STOP TIMER
//     stopAutoSync();       // ⛔ STOP SERVER SYNC
//     resetUI();            // ⛔ RESET DISPLAY
//   }

//   fetch('/crm-hrms/public/attendance/action', {
//     method: 'POST',
//     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//     body: 'action=' + action
//   }).then(() => {
//     syncFromServer();       // server is still source of truth
//   });
// }

function attendance(action) {

  if (action === 'break_start') {
    isOnBreak = true;
    stopUITimer();
    updateBreakUI(true);
  }

  if (action === 'break_end') {
    isOnBreak = false;
    startUITimer();
    updateBreakUI(false);
  }

  if (action === 'logout') {
    fetch('/crm-hrms/public/attendance/action', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'action=logout'
    }).then(hardLogout);
    return;
  }

  fetch('/crm-hrms/public/attendance/action', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=' + action
  }).then(syncFromServer);
}




function logout() {
  fetch('/crm-hrms/public/attendance/logout')
    .then(() => {
      stopTimers();
      location.href = '/logout';
    });
}

function hardLogout() {
  stopUITimer();

  if (syncInterval) {
    clearInterval(syncInterval);
    syncInterval = null;
  }

  isLoggedIn = false;
  isOnBreak = false;
  loginTime = null;
  breakSeconds = 0;

  updateTimerUI(0);
  updateBreakUI(false);
}


/* ---------- CLEAN ---------- */

function stopTimers() {
  if (uiInterval) clearInterval(uiInterval);
  if (syncInterval) clearInterval(syncInterval);
}

/* ---------- INIT ---------- */

// document.addEventListener('DOMContentLoaded', () => {
//   initLiveTimer();
//   document.getElementById('todayDate').innerText =
//     new Date().toDateString();
// });

document.addEventListener('DOMContentLoaded', () => {
  syncFromServer();
  syncInterval = setInterval(syncFromServer, 60000);
});



// function updateBreakUI(onBreak) {
//   const live = document.getElementById('liveStatus');
//   const brk = document.getElementById('breakStatus');

//   if (!live || !brk) return;

//   if (onBreak) {
//     live.classList.add('d-none');
//     brk.classList.remove('d-none');
//   } else {
//     brk.classList.add('d-none');
//     live.classList.remove('d-none');
//   }
// }

function updateBreakUI(onBreak) {
  const live = document.getElementById('liveStatus');
  const brk = document.getElementById('breakStatus');
  if (!live || !brk) return;

  live.classList.toggle('d-none', onBreak);
  brk.classList.toggle('d-none', !onBreak);
}


function stopAutoSync() {
  if (syncInterval) {
    clearInterval(syncInterval);
    syncInterval = null;
  }
}

function resetUI() {
  updateTimerUI(0);
  updateBreakUI(false);

  const clock = document.getElementById('clockInTime');
  if (clock) clock.innerText = '';

  isOnBreak = false;
  loginTime = null;
  breakSeconds = 0;
}

function loadLiveAttendance() {
  fetch('/crm-hrms/public/attendance/liveAttendance')
    .then(r => r.json())
    .then(data => {
      let html = '';
      data.forEach(e => {
        let badge =
          e.status === 'active' ? 'success' :
            e.status === 'break' ? 'warning' : 'secondary';

        html += `
          <tr>
            <td>${e.name}</td>
            <td>${renderStatus(e)}</td>
            <td>${formatTime(e.login_time)}</td>
            <td>${formatDuration(e.total_minutes)}</td>
            </tr>`;
      });
      document.getElementById('attendanceBody').innerHTML = html;
    });
}

loadLiveAttendance();
setInterval(loadLiveAttendance, 10000);

// function renderStatus(status) {
//   if (status === 'active')
//     return `<span class="status status-live">🟢 LIVE</span>`;

//   if (status === 'break')
//     return `<span class="status status-break">🟡 ON BREAK</span>`;

//   return `<span class="status status-logout">🔴 LOGGED OUT</span>`;
// }

function sinceTime(time) {
  if (!time) return '';
  const t = new Date(time);
  return `<div class="since">Since ${t.toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit'
  })}</div>`;
}

function renderStatus(e) {

  if (e.status === 'active') {
    return `
      <div>
        <span class="status status-live">🟢 LIVE</span>
        ${sinceTime(e.login_time)}
      </div>`;
  }

  if (e.status === 'break') {
    return `
      <div>
        <span class="status status-break">🟡 ON BREAK</span>
        ${sinceTime(e.break_start)}
      </div>`;
  }

  return `
    <div>
      <span class="status status-logout">🔴 LOGGED OUT</span>
    </div>`;
}

function formatTime(time) {
  if (!time) return '-';
  return new Date(time).toLocaleTimeString([], {
    hour: '2-digit',
    minute: '2-digit'
  });
}

function formatDuration(minutes) {
  if (!minutes) return '00:00';

  const h = Math.floor(minutes / 60);
  const m = minutes % 60;

  return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
}
