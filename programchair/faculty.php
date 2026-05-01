<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">school</span></div>
      <div>
        <h2>Faculty</h2>
        <p>Manage faculty accounts, availability, and specializations</p>
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

<div class="p-2 sm:p-4 all_accounts_container">
    <!-- MAIN CONTENT HERE -->
     

</div>


<!-- FACULTY PROFILE MODAL: Availability + Specializations (auto-scheduling) -->
<div id="facultyMetaModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-3xl overflow-y-auto max-h-[90vh]">

    <div class="pc-modal-header">
      <span class="material-icons">badge</span>
      <div>
        <h2 class="leading-none">Faculty Profile</h2>
        <p class="text-xs text-red-100/85 mt-0.5" id="metaFacultyName">—</p>
      </div>
    </div>

    <form id="facultyMetaForm" class="space-y-5 p-6">
      <input type="hidden" name="user_id" id="meta_user_id">

      <div>
        <h3 class="pc-section-title text-sm mb-1"><span class="material-icons text-base">event_available</span> Weekly Availability</h3>
        <p class="text-xs text-gray-500 mb-3">Set the days and hours this faculty is available to teach. Leave a day blank if unavailable.</p>
        <div id="availabilityGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
      </div>

      <div>
        <h3 class="pc-section-title text-sm mb-2"><span class="material-icons text-base">workspace_premium</span> Specializations</h3>
        <div class="mb-3" style="position: relative; width: 100%; max-width: 28rem;">
          <span class="material-icons"
            style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%);
                   color:#9ca3af; font-size:1.15rem; pointer-events:none; line-height:1;">search</span>
          <input type="text" id="metaSubjectSearch"
            placeholder="Search subject code or name..." autocomplete="off"
            style="width:100%; padding:.6rem .75rem .6rem 2.4rem;
                   border:1px solid #e5e7eb; border-radius:.55rem;
                   background:#fff; font-size:.9rem; color:#1f2937;">
          <div id="metaSubjectDropdown"
            class="hidden bg-white border border-gray-300 rounded shadow-xl max-h-52 overflow-y-auto text-sm"
            style="min-width:320px;"></div>
        </div>
        <div id="metaSpecChips" class="flex flex-wrap gap-2 min-h-[2rem]"></div>
      </div>

      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeFacultyMetaModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary">
          <span class="material-icons">save</span> Save Profile
        </button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT MODAL -->
<div id="editAccountModal"
  class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)]">

  <div class="pc-modal-card w-full max-w-lg">
    <div class="pc-modal-header">
      <span class="material-icons">manage_accounts</span>
      <h2>Edit Account</h2>
    </div>

    <form id="editAccountForm" class="space-y-3 p-6">
      <input type="hidden" name="user_id" id="edit_user_id">

      <input type="text" id="edit_username" class="pc-input" name="username" placeholder="Username" required>
      <input type="email" id="edit_email" class="pc-input" name="email" placeholder="Email" required>
      <input type="text" id="edit_fname" class="pc-input" name="first_name" placeholder="First Name" required>
      <input type="text" id="edit_mname" class="pc-input" name="middle_name" placeholder="Middle Name">
      <input type="text" id="edit_lname" class="pc-input" name="last_name" placeholder="Last Name" required>

      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeeditAccountModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">save</span> Save</button>
      </div>
    </form>
  </div>
</div>

<?php
include "../src/components/programchair/footer.php";
?>

<script src="../static/js/programchair/faculty.js?v=<?=filemtime(__DIR__ . '/../static/js/programchair/faculty.js')?>"></script>
