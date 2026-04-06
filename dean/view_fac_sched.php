
<!-- EDIT TIME MODAL (Dean/Program Chair) -->
<div id="editTimeModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 p-4">
  <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
    <h2 class="text-lg font-bold text-red-900 mb-1">Edit Schedule Time</h2>
    <p id="editTimeSubjectLabel" class="text-sm text-gray-600 mb-4 font-semibold"></p>
    <div id="editTimeConflict" class="hidden bg-red-100 border border-red-400 text-red-700 text-sm rounded p-2 mb-3"></div>
    <form id="editTimeForm" class="space-y-4">
      <input type="hidden" id="editTimeSchId">
      <input type="hidden" id="editTimeDay">
      <input type="hidden" id="editTimeEntryIndex">
      <div>
        <label class="block text-sm font-semibold mb-1">Start Time</label>
        <input type="time" id="editTimeFrom" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" min="07:00" max="21:00" required>
      </div>
      <div>
        <label class="block text-sm font-semibold mb-1">Room Number</label>
        <input type="text" id="editTimeRoom" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" placeholder="e.g. 301">
      </div>
      <div>
        <label class="block text-sm font-semibold mb-1">End Time</label>
        <input type="time" id="editTimeTo" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" min="07:00" max="21:00" required>
      </div>
      <div class="flex gap-2 justify-end">
        <button type="button" id="closeEditTimeModal" class="cursor-pointer bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
        <button type="submit" class="cursor-pointer bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded">Save Changes</button>
      </div>
    </form>
  </div>
</div>
<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
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
    <h1 class="text-lg font-bold uppercase py-2 sch_schedule_title"></h1>
    <p class="text-sm font-semibold sch_schedule_sy"></p>
    <div class="bg-yellow-300 font-bold py-1 mt-2 sch_schedule_author capitalize hidden"></div>
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
include "../src/components/programchair/footer.php";
?>


<script src="../static/js/dean/view_fac_sched.js"></script>
