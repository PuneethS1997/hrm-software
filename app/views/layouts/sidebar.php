<?php
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? '';
?>

<div id="sidebar" class="sidebar">
  <div class="sidebar-header">
    <span class="logo">CRM-HRMS</span>
  </div>

  <ul class="sidebar-menu">

    <li>
      <a href="<?= BASE_URL ?>/dashboard">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <?php if ($role === 'super_admin' || $role === 'admin'): ?>
      <!-- <li>
        <a href="<?= BASE_URL ?>/employees/list">
          <i class="bi bi-people"></i>
          <span>Employees</span>
        </a>
      </li> -->

      <li>
        <a href="<?= BASE_URL ?>/employees/create" data-title="Add Employee">
          <i class="bi bi-person-plus"></i>
          <span class="text">Add Employee</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>/employees/list" data-title=" Employee List">
          <i class="bi bi-people"></i>
          <span class="text"> Employee List</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>/employees/trash" data-title=" Employee Trash">
          <i class="bi bi-trash"></i> <span class="text"> Trash </span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>/attendance/attendance_list">
          <i class="bi bi-calendar-check"></i>
          <span>Attendance</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>/leaves/admin">
          <i class="bi bi-calendar-check"></i>
          <span>Leaves </span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class="bi bi-cash-stack"></i>
          <span>Payroll</span>
        </a>
      </li>
    <?php endif; ?>

    <?php if ($role === 'employee'): ?>
      <li>
        <a href="#">
          <i class="bi bi-clock-history"></i>
          <span>My Attendance</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>/leaves/history">
          <i class="bi bi-calendar-event"></i>
          <span>My Leaves</span>
        </a>
      </li>

      <li>
        <a href="#">
          <i class="bi bi-wallet2"></i>
          <span>My Payroll</span>
        </a>
      </li>
    <?php endif; ?>

    <li class="logout">
      <a href="<?= BASE_URL ?>/logout">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </a>
    </li>



  </ul>
</div>