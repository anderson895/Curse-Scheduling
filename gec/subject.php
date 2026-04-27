<?php
include "../src/components/gec/header.php";
include "../src/components/gec/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">subject</span></div>
      <div>
        <h2>Subjects</h2>
        <p>Manage all course subjects</p>
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
        <span class="material-icons">subject</span>
        <span>Subject List</span>
      </div>
      <button id="addBtn" class="pc-btn pc-btn-primary">
        <span class="material-icons">add</span> Add Subject
      </button>
    </div>

    <div class="p-4 overflow-x-auto">
      <table class="pc-table">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Units</th>
            <th>Type</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="subjectTableBody">
          <tr><td colspan="5" class="text-center p-6 text-gray-500">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- ADD SUBJECT MODAL -->
<div id="addSubjectModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-md">
    <div class="pc-modal-header">
      <span class="material-icons">add_circle</span>
      <h2>Add Subject</h2>
    </div>
    <form id="addSubjectForm" class="space-y-4 p-6">
      <div>
        <label class="pc-label">Subject Code</label>
        <input type="text" name="subject_code" required class="pc-input">
      </div>
      <div>
        <label class="pc-label">Subject Name</label>
        <input type="text" name="subject_name" required class="pc-input">
      </div>
      <div>
        <label class="pc-label">Units</label>
        <input type="number" name="units" required class="pc-input">
      </div>
      <div>
        <label class="pc-label">Type</label>
        <select name="subject_type" required class="pc-select">
          <option value="" disabled selected>Select Type</option>
          <option value="Major">Major</option>
          <option value="Minor">Minor</option>
        </select>
      </div>

      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeAddSubjectModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">save</span> Save</button>
      </div>
    </form>
  </div>
</div>


<!-- EDIT SUBJECT MODAL -->
<div id="editSubjectModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-md">
    <div class="pc-modal-header">
      <span class="material-icons">edit</span>
      <h2>Edit Subject</h2>
    </div>
    <form id="editSubjectForm" class="space-y-4 p-6">
      <input type="hidden" name="subject_id" id="edit_subject_id">

      <div>
        <label class="pc-label">Subject Code</label>
        <input type="text" name="subject_code" id="edit_subject_code" required class="pc-input">
      </div>
      <div>
        <label class="pc-label">Subject Name</label>
        <input type="text" name="subject_name" id="edit_subject_name" required class="pc-input">
      </div>
      <div>
        <label class="pc-label">Units</label>
        <input type="number" name="units" id="edit_subject_unit" required class="pc-input">
      </div>
      <div>
        <label class="pc-label">Type</label>
        <select name="subject_type" id="edit_subject_type" required class="pc-select">
          <option value="" disabled>Select Type</option>
          <option value="Major">Major</option>
          <option value="Minor">Minor</option>
        </select>
      </div>

      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeEditSubjectModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">update</span> Update</button>
      </div>
    </form>
  </div>
</div>


<?php
include "../src/components/gec/footer.php";
?>

<script src="../static/js/gec/subject.js"></script>
