<?php
include "../src/components/gec/header.php";
include "../src/components/gec/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">table_view</span></div>
      <div>
        <h2>View Schedule</h2>
        <p>Detailed weekly schedule</p>
      </div>
    </div>
    <div class="pc-topbar-meta">
      <a href="fac_sched" class="pc-btn pc-btn-secondary"><span class="material-icons">arrow_back</span> Back</a>
      <div class="pc-avatar"><?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?></div>
    </div>
  </div>
</div>

<div class="p-2 sm:p-4 view_sched_container">

  <div class="pc-card overflow-hidden">

    <!-- Program Info -->
    <div class="text-center border-b border-gray-100 p-4">
      <h1 class="text-lg font-bold uppercase text-red-900 sch_schedule_title"></h1>
      <p class="text-sm font-semibold text-gray-600 sch_schedule_sy"></p>
      <div class="bg-amber-100 text-amber-800 font-semibold py-1 px-2 rounded mt-2 inline-block sch_schedule_author capitalize hidden"></div>
    </div>

    <!-- Schedule Table -->
    <div class="overflow-x-auto">
      <table class="pc-week-table">
        <thead>
          <tr>
            <th class="w-28">TIME</th>
            <th>Monday</th>
            <th>Tuesday</th>
            <th>Wednesday</th>
            <th>Thursday</th>
            <th>Friday</th>
            <th>Saturday</th>
          </tr>
        </thead>
        <tbody id="scheduleBody"></tbody>
      </table>
    </div>
  </div>

</div>

<?php
include "../src/components/gec/footer.php";
?>


<script src="../static/js/gec/view_fac_sched.js"></script>
