<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">View Schedule</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-gray-100 min-h-screen view_sched_container">
<div class="bg-white rounded-lg shadow-md overflow-hidden">

  <!-- Program Info — blank by default, filled by JS -->
  <div class="text-center " id="scheduleHeader">
    <h1 class="text-lg font-bold uppercase py-2 sch_schedule_title"></h1>
    <p class="text-sm font-semibold sch_schedule_sy"></p>
    <div class="bg-yellow-300 font-bold py-1 mt-2 sch_schedule_author capitalize hidden"></div>
  </div>

  <!-- No schedule empty state (shown by JS when no data) -->
  <div id="noScheduleMsg" class="hidden">
    <div class="flex flex-col items-center justify-center py-24 px-6 text-center">
      <div class="bg-red-50 rounded-full p-7 mb-6">
        <svg class="w-14 h-14 text-red-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25
               2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18
               0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021
               18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25
               2.25 0 0121 11.25v7.5" />
        </svg>
      </div>
      <h3 class="text-2xl font-bold text-gray-700 mb-2">No Schedule Yet</h3>
      <p class="text-gray-400 text-sm max-w-sm leading-relaxed">
        Your schedule has not been set yet.<br>
        Please wait for your <span class="font-semibold text-gray-500">Dean</span> or
        <span class="font-semibold text-gray-500">Program Chair</span> to assign your schedule.
      </p>
    </div>
  </div>

  <!-- Schedule Table (hidden when no data) -->
  <div class="overflow-x-auto" id="scheduleTableWrap">
    <table class="min-w-full border-collapse text-sm">
      <thead>
        <tr class="bg-blue-900 text-white">
          <th class="border p-2 w-28">TIME</th>
          <th class="border p-2">Monday</th>
          <th class="border p-2">Tuesday</th>
          <th class="border p-2">Wednesday</th>
          <th class="border p-2">Thursday</th>
          <th class="border p-2">Friday</th>
          <th class="border p-2">Saturday</th>
        </tr>
      </thead>
      <tbody id="scheduleBody"></tbody>
    </table>
  </div>

</div>
</div>

<?php
include "../src/components/faculty/footer.php";
?>

<script src="../static/js/faculty/view_fac_sched.js"></script>