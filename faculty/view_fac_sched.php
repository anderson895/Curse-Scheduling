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

  <!-- Program Info -->
  <div class="text-center border-b">
    <h1 class="text-lg font-bold uppercase py-2 sch_schedule_title">
      Bachelor of Science in Mechanical Engineering
    </h1>
    <p class="text-sm font-semibold sch_schedule_sy">Second Semester – SY 2025–2026</p>
    <div class="bg-yellow-300 font-bold py-1 mt-2 sch_schedule_author">
      Engr. Christopher Lennon Dela Cruz
    </div>
  </div>

  <!-- Schedule Table -->
  <div class="overflow-x-auto">
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
