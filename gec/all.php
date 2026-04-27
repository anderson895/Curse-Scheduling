<?php
include "../src/components/gec/header.php";
include "../src/components/gec/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">people</span></div>
      <div>
        <h2>All Accounts</h2>
        <p>Browse every account in the system</p>
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

<div class="p-2 sm:p-4 all_accounts_container"></div>


<!-- EDIT MODAL -->
<div id="editAccountModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
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
include "../src/components/gec/footer.php";
?>

<script src="../static/js/gec/all_accounts.js"></script>
