<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">menu_book</span></div>
      <div>
        <h2>Curriculum</h2>
        <p>Maintain program curricula by year level and semester</p>
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

  <div class="pc-card">
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">menu_book</span>
        <span>Curriculum List</span>
      </div>

      <button id="addBtn" class="pc-btn pc-btn-primary">
        <span class="material-icons">add</span> Add Curriculum
      </button>
    </div>

    <div id="curriculumTable" class="p-4 overflow-x-auto">
      <table class="pc-table">
        <thead>
          <tr>
            <th>Year/Semester</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Units</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="curriculumBody"></tbody>
      </table>
    </div>
  </div>
</div>


<!-- ADD CURRICULUM MODAL -->
<div id="addCurriculumModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-md">
    <div class="pc-modal-header">
      <span class="material-icons">add_circle</span>
      <h2>Add Curriculum</h2>
    </div>
    <form id="addCurriculumForm" class="p-6 space-y-4">
      <div>
        <label class="pc-label">Year/Semester</label>
        <input type="text" name="year_semester" class="pc-input" required>
      </div>
      <div>
        <label class="pc-label">Subjects</label>
        <select name="subject_id[]" id="subjectSelect" multiple class="pc-select" required></select>
        <p class="text-xs text-gray-500 mt-1">Hold Ctrl (or Cmd) to select multiple subjects.</p>
      </div>
      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeAddCurriculumModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">save</span> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT CURRICULUM MODAL -->
<div id="editCurriculumModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-md">
    <div class="pc-modal-header">
      <span class="material-icons">edit</span>
      <h2>Edit Curriculum</h2>
    </div>
    <form id="editCurriculumForm" class="p-6 space-y-4">
      <input type="hidden" name="id">
      <div>
        <label class="pc-label">Year/Semester</label>
        <input type="text" name="year_semester" class="pc-input">
      </div>
      <div>
        <label class="pc-label">Subjects</label>
        <select name="subject_id[]" id="editSubjectSelect" multiple class="pc-select"></select>
      </div>
      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeEditCurriculumModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">update</span> Update</button>
      </div>
    </form>
  </div>
</div>


<?php
include "../src/components/programchair/footer.php";
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script src="../static/js/programchair/curriculum.js"></script>
