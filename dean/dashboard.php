<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<!-- Top Bar -->
<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">dashboard</span></div>
      <div>
        <h2>Dashboard</h2>
        <p>Overview of users, subjects, and schedules</p>
      </div>
    </div>
    <div class="pc-topbar-meta">
      <div class="pc-topbar-welcome hidden sm:block">
        <p class="small">Welcome,</p>
        <p class="name"><?=ucfirst($On_Session[0]['user_username'])?></p>
      </div>
      <div class="pc-avatar"><?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?></div>
    </div>
  </div>
</div>

<div class="p-2 sm:p-4">

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 mb-8">

    <div class="pc-stat pc-stat-red">
      <div class="pc-stat-icon"><span class="material-icons">people</span></div>
      <div>
        <p class="pc-stat-label">Users</p>
        <h2 class="pc-stat-value" id="totalUsers">0</h2>
      </div>
    </div>

    <div class="pc-stat pc-stat-emerald">
      <div class="pc-stat-icon"><span class="material-icons">book</span></div>
      <div>
        <p class="pc-stat-label">Subjects</p>
        <h2 class="pc-stat-value" id="totalSubjects">0</h2>
      </div>
    </div>

    <div class="pc-stat pc-stat-amber">
      <div class="pc-stat-icon"><span class="material-icons">schedule</span></div>
      <div>
        <p class="pc-stat-label">Schedules</p>
        <h2 class="pc-stat-value" id="totalSchedules">0</h2>
      </div>
    </div>

  </div>

  <!-- Combined Charts Card -->
  <div class="pc-card mb-6">
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">insert_chart</span>
        <span>Analytics</span>
      </div>
    </div>
    <div class="pc-card-body">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div><div id="barChart" style="min-height:300px;"></div></div>
        <div><div id="donutChart" style="min-height:300px;"></div></div>
      </div>
    </div>
  </div>

</div>

<?php
include "../src/components/dean/footer.php";
?>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="../static/js/dean/dashboard.js"></script>
