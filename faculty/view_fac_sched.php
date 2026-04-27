<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">table_view</span></div>
      <div>
        <h2>View Schedule</h2>
        <p>Your weekly teaching schedule</p>
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

<div class="p-2 sm:p-4 view_sched_container">

  <div class="pc-card overflow-hidden">

    <!-- Program Info -->
    <div class="text-center border-b border-gray-100 p-4" id="scheduleHeader">
      <h1 class="text-lg font-bold uppercase text-red-900 sch_schedule_title"></h1>
      <p class="text-sm font-semibold text-gray-600 sch_schedule_sy"></p>
      <div class="bg-amber-100 text-amber-800 font-semibold py-1 px-2 rounded mt-2 inline-block sch_schedule_author capitalize hidden"></div>
    </div>

    <!-- No schedule empty state -->
    <div id="noScheduleMsg" class="hidden">
      <div class="flex flex-col items-center justify-center py-24 px-6 text-center">
        <div class="bg-red-50 rounded-full p-7 mb-6">
          <span class="material-icons" style="font-size:3.5rem;color:#fca5a5;">event_busy</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-700 mb-2">No Schedule Yet</h3>
        <p class="text-gray-400 text-sm max-w-sm leading-relaxed">
          Your schedule has not been set yet.<br>
          Please wait for your <span class="font-semibold text-gray-500">Dean</span> or
          <span class="font-semibold text-gray-500">Program Chair</span> to assign your schedule.
        </p>
      </div>
    </div>

    <!-- Schedule Table -->
    <div class="p-3 sm:p-4" id="scheduleTableWrap">
      <div class="overflow-x-auto -mx-1 px-1">
        <table class="pc-room-grid">
          <thead>
            <tr>
              <th>Time</th>
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
</div>

<?php
include "../src/components/faculty/footer.php";
?>

<script src="../static/js/faculty/view_fac_sched.js"></script>
