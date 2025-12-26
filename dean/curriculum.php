<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<div class="p-6 bg-gray-100 min-h-screen">

  <!-- SUBJECT CARD -->
  <div class="bg-white rounded-xl shadow-lg">

    <!-- Card Header -->
    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-red-900">Curriculum</h3>

      <button id="addBtn"
        class="flex cursor-pointer items-center gap-2 bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-md shadow">
        <span class="material-icons text-sm">add</span>
        Add Curriculum
      </button>
    </div>

    <!-- CARD BODY -->
    <div id="curriculumTable" class="p-6">
      <table class="min-w-full border border-gray-300 bg-white shadow-md rounded-lg">
        <thead class="bg-red-900 text-white">
          <tr>
            <th class="p-2 text-left">Year/Semester</th>
            <th class="p-2 text-left">Subject Code</th>
            <th class="p-2 text-left">Subject Name</th>
            <th class="p-2 text-left">Units</th>
            <th class="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="curriculumBody"></tbody>
      </table>
    </div>

  </div>
</div>


<!-- ADD CURRICULUM MODAL -->
<div id="addCurriculumModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
  <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 mx-auto my-auto">
    <h2 class="text-xl font-bold text-red-900 mb-4">Add Curriculum</h2>
    <form id="addCurriculumForm">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Year/Semester</label>
        <input type="text" name="year_semester" class="w-full border rounded px-2 py-1" required>
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Subjects</label>
        <select name="subject_id[]" id="subjectSelect" multiple class="w-full border rounded px-2 py-1" required>
          <!-- Options loaded via AJAX -->
        </select>
        <p class="text-xs text-gray-500 mt-1">Hold Ctrl (or Cmd) to select multiple subjects.</p>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" id="closeAddCurriculumModal" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-red-900 text-white rounded">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT CURRICULUM MODAL -->
<div id="editCurriculumModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
  <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 mx-auto my-auto">
    <h2 class="text-xl font-bold text-red-900 mb-4">Edit Curriculum</h2>
    <form id="editCurriculumForm">
      <input type="hidden" name="id">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Year/Semester</label>
        <input type="text" name="year_semester" class="w-full border rounded px-2 py-1">
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Subjects</label>
        <select name="subject_id[]" id="editSubjectSelect" multiple class="w-full border rounded px-2 py-1">
          <!-- Options loaded via AJAX -->
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" id="closeEditCurriculumModal" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-red-900 text-white rounded">Update</button>
      </div>
    </form>
  </div>
</div>


<?php
include "../src/components/dean/footer.php";
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script src="../static/js/dean/curriculum.js"></script>
