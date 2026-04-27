<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">calendar_month</span></div>
      <div>
        <h2>Schedule</h2>
        <p>Plot and review faculty schedules</p>
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
    <div class="pc-card-body">
      <button id="openScheduleModal" class="pc-btn pc-btn-primary">
        <span class="material-icons">add</span> Create Schedule
      </button>
    </div>
  </div>

  <!-- Schedule Table -->
  <div class="pc-card">
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">event</span>
        <span>Faculty Schedules</span>
      </div>
    </div>
    <div id="scheduleTable" style="overflow-x: auto;"></div>
  </div>

  <!-- CREATE SCHEDULE MODAL -->
  <div id="scheduleModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="pc-modal-card w-full sm:max-w-3xl md:max-w-4xl overflow-y-auto max-h-[90vh]">
      <div class="pc-modal-header">
        <span class="material-icons">edit_calendar</span>
        <h2>Create Schedule</h2>
      </div>

      <form id="scheduleForm" class="space-y-4 p-6">
        <select name="sch_user_id" class="pc-select" required>
          <option value="">Select Instructor</option>
        </select>

        <input type="text" name="program" placeholder="Program" class="pc-input" required>
        <input type="text" name="semester" placeholder="Semester (e.g. 2nd Sem SY 2025-2026)" class="pc-input" required>

        <!-- Schedule Builder -->
        <div id="scheduleBuilder" class="rounded-lg border border-gray-200 bg-gray-50 p-4 space-y-3 overflow-x-auto">
          <h4 class="pc-section-title text-sm"><span class="material-icons text-base">add_circle</span> Add Schedule Entry</h4>
          <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center mb-2">

            <div class="w-full sm:w-36 sm:shrink-0">
              <select class="daySelect pc-select cursor-pointer">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
              </select>
            </div>

            <div class="w-full sm:flex-1 sm:min-w-0">
              <select class="subjectSelect pc-select cursor-pointer">
                <option value="Math">Math</option>
                <option value="English">English</option>
                <option value="Science">Science</option>
              </select>
            </div>

            <div class="w-full sm:w-32 sm:shrink-0">
              <select class="hoursSelect pc-select cursor-pointer">
                <option value="0.5">30 mins</option>
                <option value="1">1 hour</option>
                <option value="1.5">1.5 hours</option>
                <option value="2">2 hours</option>
                <option value="2.5">2.5 hours</option>
                <option value="3">3 hours</option>
              </select>
            </div>

            <button type="button" id="addEntry" class="pc-btn pc-btn-success sm:shrink-0">
              <span class="material-icons">add</span> Add
            </button>
          </div>

          <ul id="entriesList" class="list-disc pl-5 max-h-32 overflow-y-auto text-sm text-gray-700"></ul>
        </div>


        <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3 border-t border-gray-100">
          <button type="button" id="closeScheduleModal" class="pc-btn pc-btn-neutral">Cancel</button>
          <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">save</span> Save Schedule</button>
        </div>

      </form>
    </div>
  </div>

</div>

<?php
include "../src/components/programchair/footer.php";
?>

<script src="../static/js/programchair/fac_sched.js"></script>
