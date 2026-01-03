<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<div class="p-4 sm:p-6 bg-gray-100 min-h-screen">

  <!-- SUBJECT CARD -->
  <div class="bg-white rounded-xl shadow-lg">

    <!-- Card Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-4 sm:px-6 py-4 border-b border-gray-200 gap-4">
      <h3 class="text-lg font-semibold text-red-900">Curriculum List</h3>
      <button id="addBtn"
        class="flex items-center gap-2 bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-md shadow text-sm sm:text-base">
        <span class="material-icons text-sm">add</span>
        Add Curriculum
      </button>
    </div>

    <!-- Search -->
    <div class="p-4 sm:p-6">
      <input type="text" id="subjectSearch" placeholder="Search subjects..."
        class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500 focus:outline-none text-sm sm:text-base">
    </div>

    <!-- Card Body -->
    <div class="p-4 sm:p-6 overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm sm:text-base">
        <thead class="bg-red-900 text-white text-xs sm:text-sm">
          <tr>
            <th class="p-2 sm:p-3 text-left">Program</th>
            <th class="p-2 sm:p-3 text-left">Curriculum Year</th>
            <th class="p-2 sm:p-3 text-left">Year Level</th>
            <th class="p-2 sm:p-3 text-left">Semester</th>
            <th class="p-2 sm:p-3 text-left">Code</th>
            <th class="p-2 sm:p-3 text-left">Name</th>
            <th class="p-2 sm:p-3 text-left">Lec hrs</th>
            <th class="p-2 sm:p-3 text-left">Lab hrs</th>
            <th class="p-2 sm:p-3 text-left">Lec Units</th>
            <th class="p-2 sm:p-3 text-left">Lab Units</th>
            <th class="p-2 sm:p-3 text-left">Prerequisite</th>
            <th class="p-2 sm:p-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="subjectTableBody" class="divide-y">
          <tr>
            <td colspan="12" class="text-center p-6 text-gray-500">
              Loading...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="flex flex-wrap justify-center mt-4 gap-2 px-4 sm:px-6"></div>

  </div>
</div>

<!-- ADD SUBJECT MODAL -->
<div id="addSubjectModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4 sm:p-6">
  <div class="bg-white w-full max-w-lg sm:max-w-md rounded-xl shadow-lg p-6 overflow-y-auto max-h-[90vh]">
    <h2 class="text-xl font-bold text-red-900 mb-4">Add Subject</h2>
    <form id="addSubjectForm" class="space-y-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="block text-gray-700 text-sm sm:text-base">Program</label><input type="text" name="program" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Curriculum Year</label><input type="text" name="curriculum_year" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Year Level</label><input type="number" name="year_level" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Semester</label><input type="text" name="semester" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Subject Code</label><input type="text" name="subject_code" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Subject Name</label><input type="text" name="subject_name" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lecture Hours</label><input type="number" name="lec_hours" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lab Hours</label><input type="number" name="lab_hours" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lecture Units</label><input type="number" name="lec_units" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lab Units</label><input type="number" name="lab_units" class="w-full border p-2 rounded" required></div>
        <div class="sm:col-span-2"><label class="block text-gray-700 text-sm sm:text-base">Prerequisite</label><input type="text" name="prerequisite" class="w-full border p-2 rounded"></div>
      </div>
      <div class="flex justify-end gap-2 pt-4 flex-wrap">
        <button type="button" id="closeAddSubjectModal" class="cursor-pointer px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Cancel</button>
        <button type="submit" class="cursor-pointer px-4 py-2 bg-red-900 text-white rounded-md hover:bg-red-800">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT SUBJECT MODAL -->
<div id="editSubjectModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4 sm:p-6">
  <div class="bg-white w-full max-w-lg sm:max-w-md rounded-xl shadow-lg p-6 overflow-y-auto max-h-[90vh]">
    <h2 class="text-xl font-bold text-red-900 mb-4">Edit Subject</h2>
    <form id="editSubjectForm" class="space-y-4">
      <input type="hidden" name="subject_id" id="edit_subject_id">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="block text-gray-700 text-sm sm:text-base">Program</label><input type="text" name="program" id="edit_program" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Curriculum Year</label><input type="text" name="curriculum_year" id="edit_curriculum_year" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Year Level</label><input type="number" name="year_level" id="edit_year_level" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Semester</label><input type="text" name="semester" id="edit_semester" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Subject Code</label><input type="text" name="subject_code" id="edit_subject_code" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Subject Name</label><input type="text" name="subject_name" id="edit_subject_name" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lecture Hours</label><input type="number" name="lec_hours" id="edit_lec_hours" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lab Hours</label><input type="number" name="lab_hours" id="edit_lab_hours" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lecture Units</label><input type="number" name="lec_units" id="edit_lec_units" class="w-full border p-2 rounded" required></div>
        <div><label class="block text-gray-700 text-sm sm:text-base">Lab Units</label><input type="number" name="lab_units" id="edit_lab_units" class="w-full border p-2 rounded" required></div>
        <div class="sm:col-span-2"><label class="block text-gray-700 text-sm sm:text-base">Prerequisite</label><input type="text" name="prerequisite" id="edit_prerequisite" class="w-full border p-2 rounded"></div>
      </div>
      <div class="flex justify-end gap-2 pt-4 flex-wrap">
        <button type="button" id="closeEditSubjectModal" class="cursor-pointer px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Cancel</button>
        <button type="submit" class="cursor-pointer px-4 py-2 bg-red-900 text-white rounded-md hover:bg-red-800">Update</button>
      </div>
    </form>
  </div>
</div>

<?php
include "../src/components/dean/footer.php";
?>

<!-- JS -->
<script src="../static/js/dean/subject.js"></script>
