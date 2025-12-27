<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="flex flex-col sm:flex-row justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide mb-2 sm:mb-0">Schedule</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-4 sm:p-6 bg-gray-100 min-h-screen">

  <!-- Header & Create Button -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2 sm:gap-0">
    
    <button id="openScheduleModal" class="bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded shadow">
      + Create Schedule
    </button>
  </div>

  <!-- Schedule Table -->
  <div id="scheduleTable" class="overflow-x-auto bg-white rounded-lg shadow-md border border-gray-300"></div>

  <!-- CREATE SCHEDULE MODAL -->
  <div id="scheduleModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white w-full max-w-xl rounded-xl shadow-lg p-6 overflow-y-auto max-h-[90vh]">
      <h2 class="text-xl font-bold text-red-900 mb-4">Create Schedule</h2>

      <form id="scheduleForm" class="space-y-4">

        <!-- Select Faculty -->
        <select name="sch_user_id" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" required>
          <option value="">Select Faculty</option>
        </select>

        <!-- Program, Semester, Instructor -->
        <input type="text" name="program" placeholder="Program" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" required>
        <input type="text" name="semester" placeholder="Semester (e.g. 2nd Sem SY 2025-2026)" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" required>
        <input type="text" name="instructor" placeholder="Instructor Name" class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500" required>

        <!-- Schedule Builder -->
        <div id="scheduleBuilder" class="border p-3 rounded space-y-2 overflow-x-auto">
          <h4 class="font-bold mb-2">Add Schedule Entry</h4>
          <div class="flex flex-col sm:flex-row gap-2 items-start sm:items-center mb-2">

            <button type="button" id="addEntry" class="bg-green-600 hover:bg-green-500 text-white px-3 py-1 rounded mt-2 sm:mt-0">
              ADD
            </button>

            <select class="daySelect border p-2 rounded w-full sm:w-auto">
              <option value="Monday">Monday</option>
              <option value="Tuesday">Tuesday</option>
              <option value="Wednesday">Wednesday</option>
              <option value="Thursday">Thursday</option>
              <option value="Friday">Friday</option>
            </select>

            <select class="subjectSelect border p-2 rounded w-full sm:w-auto">
              <option value="Math">Math</option>
              <option value="English">English</option>
              <option value="Science">Science</option>
            </select>

            <!-- Input para sa hours -->
            <select class="hoursSelect border p-2 rounded w-full sm:w-auto">
              <option value="0.5">0.5 hour (30 mins)</option>
              <option value="1">1 hour</option>
              <option value="1.5">1.5 hours</option>
              <option value="2">2 hours</option>
              <option value="2.5">2.5 hours</option>
              <option value="3">3 hours</option>
            </select>


            
          </div>

          <ul id="entriesList" class="list-disc pl-5 max-h-32 overflow-y-auto"></ul>
        </div>


        <!-- Modal Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3">
          <button type="button" id="closeScheduleModal" class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-red-900 hover:bg-red-800 text-white rounded">Save Schedule</button>
        </div>

      </form>
    </div>
  </div>

</div>

<?php
include "../src/components/programchair/footer.php";
?>

<script src="../static/js/programchair/fac_sched.js"></script>
