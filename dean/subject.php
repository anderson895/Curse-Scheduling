<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">subject</span></div>
      <div>
        <h2>Subjects</h2>
        <p>Manage all course subjects across programs</p>
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

    <!-- Card Header -->
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">subject</span>
        <span>Curriculum List</span>
      </div>
      <button id="addBtn" class="pc-btn pc-btn-primary">
        <span class="material-icons">add</span> Add Curriculum
      </button>
    </div>

    <!-- Filters -->
    <div class="pc-card-body">
      <div class="subject-filters">
        <div class="subject-filter-field">
          <label class="pc-label">Search</label>
          <div class="pc-search-wrap">
            <span class="material-icons">search</span>
            <input type="text" id="subjectSearch" placeholder="Search code or name..." class="pc-input">
          </div>
        </div>
        <div class="subject-filter-field">
          <label class="pc-label">Program</label>
          <select id="filterProgram" class="pc-select">
            <option value="">All Programs</option>
            <option value="BSCE">BSCE</option>
            <option value="BSCoE">BSCoE</option>
            <option value="BSEE">BSEE</option>
            <option value="BSECE">BSECE</option>
            <option value="BSIE">BSIE</option>
            <option value="BSME">BSME</option>
          </select>
        </div>
        <div class="subject-filter-field">
          <label class="pc-label">Year Level</label>
          <select id="filterYear" class="pc-select">
            <option value="">All Years</option>
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
            <option value="5">5th Year</option>
          </select>
        </div>
        <div class="subject-filter-field">
          <label class="pc-label">Semester</label>
          <select id="filterSemester" class="pc-select">
            <option value="">All Semesters</option>
            <option value="1st">1st Semester</option>
            <option value="2nd">2nd Semester</option>
            <option value="Summer">Summer</option>
          </select>
        </div>
        <button id="resetFilters" class="pc-btn pc-btn-neutral subject-filter-reset">
          <span class="material-icons">refresh</span> Reset
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="p-4 overflow-x-auto">
      <table class="pc-table">
        <thead>
          <tr>
            <th>Program</th>
            <th>Curriculum Year</th>
            <th>Year Level</th>
            <th>Semester</th>
            <th>Code</th>
            <th>Name</th>
            <th>Lec hrs</th>
            <th>Lab hrs</th>
            <th>Lec Units</th>
            <th>Lab Units</th>
            <th>Prerequisite</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody id="subjectTableBody">
          <tr><td colspan="12" class="text-center p-6 text-gray-500">Loading...</td></tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div id="pagination" class="flex flex-wrap justify-center mt-4 gap-2 px-4 sm:px-6 pb-4"></div>

  </div>
</div>

<!-- ADD SUBJECT MODAL -->
<div id="addSubjectModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-2xl overflow-y-auto max-h-[90vh]">
    <div class="pc-modal-header">
      <span class="material-icons">add_circle</span>
      <h2>Add Subject</h2>
    </div>
    <form id="addSubjectForm" class="space-y-4 p-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="pc-label">Program</label><input type="text" name="program" class="pc-input" required></div>
        <div><label class="pc-label">Curriculum Year</label><input type="text" name="curriculum_year" class="pc-input" required></div>
        <div><label class="pc-label">Year Level</label><input type="number" name="year_level" class="pc-input" required></div>
        <div><label class="pc-label">Semester</label><input type="text" name="semester" class="pc-input" required></div>
        <div><label class="pc-label">Subject Code</label><input type="text" name="subject_code" class="pc-input" required></div>
        <div><label class="pc-label">Subject Name</label><input type="text" name="subject_name" class="pc-input" required></div>
        <div><label class="pc-label">Lecture Hours</label><input type="number" name="lec_hours" class="pc-input" required></div>
        <div><label class="pc-label">Lab Hours</label><input type="number" name="lab_hours" class="pc-input" required></div>
        <div><label class="pc-label">Lecture Units</label><input type="number" name="lec_units" class="pc-input" required></div>
        <div><label class="pc-label">Lab Units</label><input type="number" name="lab_units" class="pc-input" required></div>
        <div class="sm:col-span-2"><label class="pc-label">Prerequisite</label><input type="text" name="prerequisite" class="pc-input"></div>
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
  <div class="pc-modal-card w-full max-w-2xl overflow-y-auto max-h-[90vh]">
    <div class="pc-modal-header">
      <span class="material-icons">edit</span>
      <h2>Edit Subject</h2>
    </div>
    <form id="editSubjectForm" class="space-y-4 p-6">
      <input type="hidden" name="subject_id" id="edit_subject_id">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="pc-label">Program</label><input type="text" name="program" id="edit_program" class="pc-input" required></div>
        <div><label class="pc-label">Curriculum Year</label><input type="text" name="curriculum_year" id="edit_curriculum_year" class="pc-input" required></div>
        <div><label class="pc-label">Year Level</label><input type="number" name="year_level" id="edit_year_level" class="pc-input" required></div>
        <div><label class="pc-label">Semester</label><input type="text" name="semester" id="edit_semester" class="pc-input" required></div>
        <div><label class="pc-label">Subject Code</label><input type="text" name="subject_code" id="edit_subject_code" class="pc-input" required></div>
        <div><label class="pc-label">Subject Name</label><input type="text" name="subject_name" id="edit_subject_name" class="pc-input" required></div>
        <div><label class="pc-label">Lecture Hours</label><input type="number" name="lec_hours" id="edit_lec_hours" class="pc-input" required></div>
        <div><label class="pc-label">Lab Hours</label><input type="number" name="lab_hours" id="edit_lab_hours" class="pc-input" required></div>
        <div><label class="pc-label">Lecture Units</label><input type="number" name="lec_units" id="edit_lec_units" class="pc-input" required></div>
        <div><label class="pc-label">Lab Units</label><input type="number" name="lab_units" id="edit_lab_units" class="pc-input" required></div>
        <div class="sm:col-span-2"><label class="pc-label">Prerequisite</label><input type="text" name="prerequisite" id="edit_prerequisite" class="pc-input"></div>
      </div>
      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeEditSubjectModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">update</span> Update</button>
      </div>
    </form>
  </div>
</div>

<?php
include "../src/components/dean/footer.php";
?>

<script src="../static/js/dean/subject.js"></script>
