<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
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

  <!-- Toolbar -->
  <div class="pc-card mb-4">
    <div class="pc-card-body flex flex-col md:flex-row md:items-center md:justify-between gap-3">

      <div class="flex flex-wrap gap-2">
        <button id="openScheduleModal" class="pc-btn pc-btn-primary">
          <span class="material-icons">add</span> Create Schedule
        </button>
        <button id="openAutoGenModal" class="pc-btn pc-btn-success">
          <span class="material-icons">bolt</span> Auto-Generate Schedule
        </button>
      </div>

      <div class="pc-search-wrap w-full md:w-80">
        <span class="material-icons">search</span>
        <input type="text" id="scheduleSearch" placeholder="Search program, semester, instructor..." class="pc-input">
      </div>
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

  <div id="noSearchResult" class="hidden pc-empty mt-2">
    <span class="material-icons">search_off</span>
    <h3>No matching records</h3>
    <p>Try a different search term.</p>
  </div>


  <!-- CREATE SCHEDULE MODAL -->
  <div id="scheduleModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="pc-modal-card w-full sm:max-w-4xl md:max-w-6xl overflow-y-auto max-h-[90vh]">

      <div class="pc-modal-header">
        <span class="material-icons">edit_calendar</span>
        <h2>Create Schedule</h2>
      </div>

      <form id="scheduleForm" class="space-y-4 p-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
          <select id="sch_user_id" name="sch_user_id" class="pc-select" required>
            <option value="">Select Instructor</option>
          </select>

          <select id="program" name="program" class="pc-select" required>
            <option value="" disabled selected>Select Program</option>
            <option value="BSCE">BS Civil Engineering (BSCE)</option>
            <option value="BSCOE">BS Computer Engineering (BSCOE)</option>
            <option value="BSEE">BS Electrical Engineering (BSEE)</option>
            <option value="BSECE">BS Electronics Engineering (BSECE)</option>
            <option value="BSIE">BS Industrial Engineering (BSIE)</option>
            <option value="BSME">BS Mechanical Engineering (BSME)</option>
          </select>

          <select id="year_level" name="year_level" class="pc-select" required>
            <option value="" disabled selected>Select Year Level</option>
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
            <option value="5">5th Year</option>
          </select>

          <select id="semester" name="semester" class="pc-select" required>
            <option value="" disabled selected>Select Semester</option>
            <option value="1st Sem SY 2025-2026">1st Sem SY 2025-2026</option>
            <option value="2nd Sem SY 2025-2026">2nd Sem SY 2025-2026</option>
            <option value="Summer SY 2025-2026">Summer SY 2025-2026</option>
          </select>
        </div>

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

            <div class="subject-picker w-full sm:flex-1 sm:min-w-0">
              <input type="text" id="subjectSearch"
                placeholder="Search subject code or name..."
                autocomplete="off"
                class="pc-input text-sm">
              <input type="hidden" class="subjectSelect" value="">
            </div>
            <div id="subjectDropdown"
              class="fixed z-[9999] hidden bg-white border border-gray-300 rounded shadow-xl max-h-52 overflow-y-auto text-sm"
              style="min-width:320px;">
            </div>

            <div class="w-full sm:w-32 sm:shrink-0">
              <select class="hoursSelect pc-select cursor-pointer">
                <option value="0.5">30 mins</option>
                <option value="1">1 hour</option>
                <option value="1.5">1.5 hours</option>
                <option value="2">2 hours</option>
                <option value="2.5">2.5 hours</option>
                <option value="3">3 hours</option>
                <option value="3.5">3.5 hours</option>
                <option value="4">4 hours</option>
              </select>
            </div>

            <div class="w-full sm:w-32 sm:shrink-0">
              <input type="text" id="roomInput" placeholder="Room No. (e.g. 301)"
                class="pc-input text-sm" autocomplete="off">
            </div>

            <button type="button" id="addEntry" class="pc-btn pc-btn-success sm:shrink-0">
              <span class="material-icons">add</span> Add
            </button>
          </div>

          <ul id="entriesList" class="list-disc pl-5 max-h-32 overflow-y-auto text-sm text-gray-700"></ul>
        </div>

        <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3 border-t border-gray-100">
          <button type="button" id="closeScheduleModal" class="pc-btn pc-btn-neutral">Cancel</button>
          <button type="submit" class="pc-btn pc-btn-primary">
            <span class="material-icons">save</span> Save Schedule
          </button>
        </div>

      </form>
    </div>
  </div>


  <!-- AUTO-GENERATE SCHEDULE MODAL -->
  <div id="autoGenModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="pc-modal-card overflow-y-auto" style="width: 100%; max-width: 640px; max-height: 90vh;">

      <div class="pc-modal-header pc-modal-header-emerald">
        <span class="material-icons">bolt</span>
        <h2>Auto-Generate Schedule</h2>
      </div>

      <div class="p-6">
        <p class="text-sm text-gray-500 mb-4">
          Auto-plot major / professional courses while respecting Gen Ed and General-Engineering
          schedules already entered manually. Choose a curriculum year (since each program may have
          multiple curricula) and which tier of courses to plot.
        </p>

        <form id="autoGenForm" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Program</label>
              <select id="autoProgram" name="program" required class="pc-select mt-1">
                <option value="" disabled selected>Select Program</option>
                <option value="BSCE">BS Civil Engineering (BSCE)</option>
                <option value="BSCoE">BS Computer Engineering (BSCoE)</option>
                <option value="BSEE">BS Electrical Engineering (BSEE)</option>
                <option value="BSECE">BS Electronics Engineering (BSECE)</option>
                <option value="BSIE">BS Industrial Engineering (BSIE)</option>
                <option value="BSME">BS Mechanical Engineering (BSME)</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Curriculum Year</label>
              <select id="autoCurriculumYear" name="curriculum_year" class="pc-select mt-1">
                <option value="">Any (all curricula)</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Year Level</label>
              <select id="autoYearLevel" name="year_level" required class="pc-select mt-1">
                <option value="" disabled selected>Select Year</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
                <option value="5">5th Year</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Semester</label>
              <select id="autoSemester" name="semester" required class="pc-select mt-1">
                <option value="" disabled selected>Select Semester</option>
                <option value="1st">1st Semester</option>
                <option value="2nd">2nd Semester</option>
                <option value="Summer">Summer</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Course Tier</label>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
                <label class="pc-tier-pill">
                  <input type="radio" name="tier" value="gen_ed">
                  <span><strong>Gen Ed</strong><br><span class="text-[10px] text-gray-500">FCL, Rizal, Soc, etc.</span></span>
                </label>
                <label class="pc-tier-pill">
                  <input type="radio" name="tier" value="gen_eng">
                  <span><strong>General Engineering</strong><br><span class="text-[10px] text-gray-500">Chem, Phys, Math</span></span>
                </label>
                <label class="pc-tier-pill">
                  <input type="radio" name="tier" value="major" checked>
                  <span><strong>Major / Professional</strong><br><span class="text-[10px] text-gray-500">CpE, ME, CE...</span></span>
                </label>
              </div>
              <p class="text-[11px] text-gray-500 mt-1">Plotting order per revision: Gen Ed → General Engineering → Major. Run them in that order; later runs avoid earlier slots automatically.</p>
            </div>

            <div class="sm:col-span-2">
              <label class="pc-merge-toggle flex items-start gap-2 p-3 rounded-lg border border-emerald-200 bg-emerald-50 cursor-pointer">
                <input type="checkbox" id="autoMergePrograms" name="merge_across_programs" value="1" class="mt-1">
                <span class="text-sm">
                  <strong class="text-emerald-800">Merge identical courses across programs</strong><br>
                  <span class="text-[11px] text-gray-600">If the same subject code exists in other programs at the same year &amp; semester, plot it once and share the slot (e.g. <em>GEN 0103L Lab — EE/ECE</em>). All shared cohorts are checked for conflicts.</span>
                </span>
              </label>
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs font-semibold uppercase tracking-wide text-gray-600">Available Rooms</label>
              <div class="pc-room-picker mt-1">
                <div id="autoRoomsChips" style="display: contents;"></div>
                <input type="text" id="autoRoomsInput" class="pc-room-input"
                  placeholder="Type room and press Enter…" autocomplete="off">
              </div>
              <input type="hidden" id="autoRooms" name="rooms" value="">
              <div id="autoRoomsSuggest" class="pc-room-suggest">
                <span class="pc-room-suggest-label">Quick add:</span>
                <span class="text-xs text-gray-400" id="autoRoomsSuggestEmpty">Loading rooms…</span>
              </div>
              <p class="text-[11px] text-gray-500 mt-1">
                Manage room numbers from <a href="rooms.php" class="text-red-700 underline">Rooms</a>.
              </p>
            </div>
          </div>

          <div id="autoGenResult" class="hidden rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm"></div>

          <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
            <button type="button" id="closeAutoGenModal" class="pc-btn pc-btn-neutral">Cancel</button>
            <button type="submit" class="pc-btn pc-btn-success">
              <span class="material-icons">auto_awesome</span> Generate
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>

<?php
include "../src/components/dean/footer.php";
?>

<script src="../static/js/dean/fac_sched.js"></script>
