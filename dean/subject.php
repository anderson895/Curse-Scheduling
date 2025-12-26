<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>


<div class="p-6 bg-gray-100 min-h-screen">

  <!-- SUBJECT CARD -->
  <div class="bg-white rounded-xl shadow-lg">

    <!-- Card Header -->
    <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-red-900">Subject List</h3>

      <button id="addBtn"
        class="flex cursor-pointer items-center gap-2 bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-md shadow">
        <span class="material-icons text-sm">add</span>
        Add Subject
      </button>
    </div>

    <!-- Card Body -->
    <div class="p-6 overflow-x-auto">
      <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
        <thead class="bg-red-900 text-white">
          <tr>
            <th class="p-3 text-left">Code</th>
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Units</th>
            <th class="p-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="subjectTableBody" class="divide-y">
          <tr>
            <td colspan="4" class="text-center p-6 text-gray-500">
              Loading...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</div>



<!-- ADD SUBJECT MODAL -->
<div id="addSubjectModal"
  class="fixed inset-0 hidden z-50 items-center justify-center bg-[rgba(0,0,0,0.5)] transition-opacity duration-300">

  <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
    <h2 class="text-xl font-bold text-red-900 mb-4">Add Subject</h2>

    <form id="addSubjectForm" class="space-y-4">
      <!-- Subject Code -->
      <div>
        <label class="block text-sm font-semibold mb-1">Subject Code</label>
        <input type="text" name="subject_code" required
          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Subject Name -->
      <div>
        <label class="block text-sm font-semibold mb-1">Subject Name</label>
        <input type="text" name="subject_name" required
          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Units -->
      <div>
        <label class="block text-sm font-semibold mb-1">Units</label>
        <input type="number" name="units" required
          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Buttons -->
      <div class="flex justify-end gap-2 pt-4">
        <button type="button" id="closeAddSubjectModal"
          class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">
          Cancel
        </button>
        <button type="submit"
          class="px-4 py-2 bg-red-900 text-white rounded-md hover:bg-red-800">
          Save
        </button>
      </div>
    </form>
  </div>
</div>











<!-- EDIT SUBJECT MODAL -->
<div id="editSubjectModal"
  class="fixed inset-0 hidden z-50 items-center justify-center bg-[rgba(0,0,0,0.5)] transition-opacity duration-300">

  <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
    <h2 class="text-xl font-bold text-red-900 mb-4">Edit Subject</h2>

    <form id="editSubjectForm" class="space-y-4">
      <input type="hidden" name="subject_id" id="edit_subject_id">

      <div>
        <label class="block text-sm font-semibold mb-1">Subject Code</label>
        <input type="text" name="subject_code" id="edit_subject_code" required
          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Subject Name</label>
        <input type="text" name="subject_name" id="edit_subject_name" required
          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-red-500">
      </div>

      <div>
        <label class="block text-sm font-semibold mb-1">Units</label>
        <input type="number" name="units" id="edit_subject_unit" required
          class="w-full border rounded-md p-2 focus:ring-2 focus:ring-red-500">
      </div>

      <div class="flex justify-end gap-2 pt-4">
        <button type="button" id="closeEditSubjectModal"
          class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">
          Cancel
        </button>
        <button type="submit"
          class="px-4 py-2 bg-red-900 text-white rounded-md hover:bg-red-800">
          Update
        </button>
      </div>
    </form>
  </div>
</div>







<?php

include "../src/components/dean/footer.php";
?>

<script src="../static/js/dean/subject.js"></script>