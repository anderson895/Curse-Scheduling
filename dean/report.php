<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
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
      <div class="pc-tabs">
        <button class="report-tab pc-tab" data-report="curriculum">
          <span class="material-icons">menu_book</span> Curriculum
        </button>
        <button class="report-tab pc-tab" data-report="schedule">
          <span class="material-icons">calendar_month</span> Schedule
        </button>
        <button class="report-tab pc-tab" data-report="subjects">
          <span class="material-icons">book</span> Subjects
        </button>
        <button class="report-tab pc-tab" data-report="users">
          <span class="material-icons">people</span> Users
        </button>
      </div>

      <!-- Print Button -->
      <button id="printReport" class="pc-btn pc-btn-primary">
        <span class="material-icons">print</span> Print
      </button>
    </div>
  </div>

  <!-- Report Container -->
  <div id="reportContainer" class="pc-card overflow-x-auto p-4">
    <!-- Report table will be injected here -->
  </div>
</div>

<?php
include "../src/components/dean/footer.php";
?>


<script src="../static/js/dean/report.js"></script>
