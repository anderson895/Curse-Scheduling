<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">analytics</span></div>
      <div>
        <h2>Schedule Report</h2>
        <p>Generate printable reports across the system</p>
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

  <div class="pc-card mb-4">
    <div class="pc-card-body report-toolbar">
      <!-- Tabs -->
      <div class="flex flex-wrap gap-2">
        <button class="report-tab pc-btn pc-btn-secondary" data-report="curriculum">
          <span class="material-icons">menu_book</span> Curriculum
        </button>
        <button class="report-tab pc-btn pc-btn-secondary" data-report="schedule">
          <span class="material-icons">calendar_month</span> Schedule
        </button>
        <button class="report-tab pc-btn pc-btn-secondary" data-report="subjects">
          <span class="material-icons">book</span> Subjects
        </button>
        <button class="report-tab pc-btn pc-btn-secondary" data-report="users">
          <span class="material-icons">people</span> Users
        </button>
        <button class="report-tab pc-btn pc-btn-secondary" data-report="schedule_plot">
          <span class="material-icons">grid_view</span> Schedule Plot
        </button>
        <button class="report-tab pc-btn pc-btn-secondary" data-report="faculty_plot">
          <span class="material-icons">co_present</span> Faculty Plot
        </button>
        <button class="report-tab pc-btn pc-btn-secondary" data-report="room_plot">
          <span class="material-icons">meeting_room</span> Room Plot
        </button>
      </div>

      <!-- Year Level Filter + Print Button -->
      <div class="flex items-center gap-2 ml-auto">
        <label for="yearFilter" class="text-xs font-semibold uppercase tracking-wide text-gray-600 hidden sm:block">Year:</label>
        <select id="yearFilter" class="pc-select" style="min-width:9rem;">
          <option value="">All Years</option>
          <option value="1">1st Year</option>
          <option value="2">2nd Year</option>
          <option value="3">3rd Year</option>
          <option value="4">4th Year</option>
          <option value="5">5th Year</option>
        </select>
        <button id="printReport" class="pc-btn pc-btn-primary">
          <span class="material-icons">print</span> Print
        </button>
      </div>
    </div>
  </div>

  <!-- Report Container -->
  <div id="reportContainer" class="pc-card overflow-x-auto p-4">
    <!-- Report table will be injected here -->
  </div>
</div>

<style>
  /* Active state for report tabs (replaces inline color toggling) */
  .report-tab.is-active {
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 4px 12px -4px rgba(127, 29, 29, .55);
  }
</style>

<?php
include "../src/components/programchair/footer.php";
?>
<script src="../static/js/dean/report.js"></script>
