
<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<!-- EDIT TIME MODAL (Dean/Program Chair) -->
<div id="editTimeModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 p-4">
  <div class="pc-modal-card w-full max-w-md">
    <div class="pc-modal-header">
      <span class="material-icons">schedule</span>
      <h2>Edit Schedule Time</h2>
    </div>
    <div class="p-6">
      <p id="editTimeSubjectLabel" class="text-sm text-gray-600 mb-4 font-semibold"></p>
      <div id="editTimeConflict" class="hidden bg-red-50 border border-red-300 text-red-700 text-sm rounded p-2 mb-3"></div>
      <form id="editTimeForm" class="space-y-4">
        <input type="hidden" id="editTimeSchId">
        <input type="hidden" id="editTimeDay">
        <input type="hidden" id="editTimeEntryIndex">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Start Time</label>
          <input type="time" id="editTimeFrom" class="pc-input" min="07:00" max="21:00" required>
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Room Number</label>
          <input type="text" id="editTimeRoom" class="pc-input" placeholder="e.g. 301">
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">End Time</label>
          <input type="time" id="editTimeTo" class="pc-input" min="07:00" max="21:00" required>
        </div>
        <div class="flex gap-2 justify-end pt-3 border-t border-gray-100">
          <button type="button" id="closeEditTimeModal" class="pc-btn pc-btn-neutral">Cancel</button>
          <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">save</span> Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    <div class="p-3 sm:p-4">
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
include "../src/components/programchair/footer.php";
?>


<script src="../static/js/programchair/view_fac_sched.js?v=<?=filemtime(__DIR__ . '/../static/js/programchair/view_fac_sched.js')?>"></script>
