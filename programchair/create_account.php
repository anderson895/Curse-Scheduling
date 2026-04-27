<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">person_add</span></div>
      <div>
        <h2>Create Account</h2>
        <p>Register a new user (faculty, GEC, program chair, or dean)</p>
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
  <div class="max-w-3xl mx-auto pc-card">
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">badge</span>
        <span>New Account Details</span>
      </div>
    </div>
    <div class="pc-card-body">
      <form id="createAccountForm" class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        <div>
          <label for="username" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Username</label>
          <input type="text" name="username" id="username" required class="pc-input mt-1">
        </div>

        <div>
          <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Email</label>
          <input type="email" name="email" id="email" required class="pc-input mt-1">
        </div>

        <div>
          <label for="first_name" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">First Name</label>
          <input type="text" name="first_name" id="first_name" required class="pc-input mt-1">
        </div>

        <div>
          <label for="middle_name" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Middle Name (Optional)</label>
          <input type="text" name="middle_name" id="middle_name" class="pc-input mt-1">
        </div>

        <div class="sm:col-span-2">
          <label for="last_name" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Last Name</label>
          <input type="text" name="last_name" id="last_name" required class="pc-input mt-1">
        </div>

        <div>
          <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">New Password</label>
          <input type="password" name="password" id="password" required class="pc-input mt-1">
        </div>

        <div>
          <label for="confirm_password" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">Confirm Password</label>
          <input type="password" name="password" id="confirm_password" required class="pc-input mt-1">
        </div>

        <div class="sm:col-span-2">
          <label for="type" class="block text-xs font-semibold uppercase tracking-wide text-gray-600">User Type</label>
          <select name="type" id="type" required class="pc-select mt-1">
            <option value="">Select user type</option>
            <option value="faculty">Faculty</option>
            <option value="program chair">Program Chair</option>
            <option value="gec">GEC</option>
            <option value="dean">Dean</option>
          </select>
        </div>

        <div class="sm:col-span-2 flex justify-end pt-3 border-t border-gray-100">
          <button type="submit" class="pc-btn pc-btn-primary">
            <span class="material-icons">person_add</span> Create Account
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php
include "../src/components/programchair/footer.php";
?>

<script src="../static/js/programchair/create_account.js"></script>
