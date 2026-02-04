<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $title ?? 'CRM-HRMS' ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Chart.js (used in dashboards) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Data Table -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

  <!-- calender view -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


  <!-- Custom Global CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
<style>

/* .topbar {
  height:60px;
  background:#fff;
  border-bottom:1px solid #e5e7eb;
} */

.dropdown{
    cursor: pointer;
}
</style>
</head>
<body>

<div class="topbar d-flex align-items-center justify-content-between px-3 fixed-top">

  <!-- LEFT -->
  <div class="d-flex align-items-center gap-3">
    <button id="toggleSidebar" class="btn btn-sm btn-outline-light">
      ☰
    </button>
    <span class="fw-semibold text-white">CRM‑HRMS</span>
  </div>

  <!-- CENTER SEARCH -->
  <div class="top-search">
    <input type="text" class="form-control form-control-sm"
           placeholder="Find people, documents, and more">
  </div>

  <!-- RIGHT -->
  <?php if ($user): ?>
  <div class="d-flex align-items-center gap-4 text-white">

    <!-- TIME + STATUS -->
    <div class="d-flex align-items-center gap-2">
      <span id="liveTime" class="fw-semibold"></span>
      <span class="badge bg-success">WORKING</span>

      <!-- STATUS TOGGLE BUTTON -->
      <button class="btn btn-sm btn-outline-light"
              data-bs-toggle="modal"
              data-bs-target="#statusModal">
        ⏷
      </button>
    </div>

    <!-- NOTIFICATION -->
    <i class="bi bi-bell position-relative fs-5">
      <span class="position-absolute top-0 start-100 translate-middle badge bg-danger">
        3
      </span>
    </i>

    <!-- USER DROPDOWN -->
    <div class="dropdown">
      <a class="dropdown-toggle text-white text-decoration-none"
         data-bs-toggle="dropdown">
        <?= htmlspecialchars($user['name']) ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li class="dropdown-item-text"><?= ucfirst($user['role']) ?></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a href="<?= BASE_URL ?>/logout" class="dropdown-item text-danger">
            Logout
          </a>
        </li>
      </ul>
    </div>

  </div>
  <?php endif; ?>
</div>

<div class="modal fade" id="statusModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5>Work Status</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
     <!-- LIVE STATUS -->
        <div id="liveStatus" class="d-flex justify-content-center align-items-center gap-2">
          <span class="live-dot"></span>
          <span class="text-success fw-semibold">LIVE</span>
        </div>

        <!-- BREAK STATUS -->
        <div id="breakStatus"
            class="d-none justify-content-center align-items-center gap-2">
          <span class="break-dot"></span>
          <span class="text-warning fw-semibold">ON BREAK</span>
        </div>


      <div class="text-center mb-2">
        <div class="fw-semibold text-muted" id="workDate"></div>
        <div class="fs-2 fw-bold" id="liveTimer">00:00:00</div>
        <div class="text-success small" id="clockInTime"></div>
      </div>


        <div class="mb-2 fw-semibold">
          📅 <span id="todayDate"></span>
        </div>

        <div class="mb-3">
          ⏱ Total Working:
          <strong id="totalTime">00:00</strong>
        </div>

        <button class="btn btn-success  mb-2" onclick="attendance('login')">
          Login
        </button>

        <button class="btn btn-warning  mb-2" onclick="attendance('break_start')">
          Break
        </button>

        <button class="btn btn-info  mb-2" onclick="attendance('break_end')">
          Resume
        </button>

        <button class="btn btn-danger " onclick="attendance('logout')">
          Logout
        </button>

      </div>
    </div>
  </div>
</div>



