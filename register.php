<?php
include "src/components/header.php";
?>
<?php include "src/components/design_system.php"; ?>

<div class="pc-auth-shell">

  <div class="pc-auth-card pc-auth-card-wide relative">

    <!-- Spinner Overlay -->
    <div id="spinner"
         class="absolute inset-0 flex items-center justify-center z-50 bg-white/80 hidden">
      <div class="w-12 h-12 border-4 border-red-900 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Header -->
    <div class="pc-auth-header">
      <img src="static/logo.jpg" alt="Logo">
      <h1>Create Account</h1>
      <p>Register to access the system</p>
    </div>

    <!-- Form -->
    <div class="pc-auth-body">
      <form id="createAccountForm" class="grid grid-cols-1 sm:grid-cols-2 gap-3">

        <div>
          <label for="username" class="pc-label">Username</label>
          <input type="text" name="username" id="username" required class="pc-input">
        </div>

        <div>
          <label for="email" class="pc-label">Email</label>
          <input type="email" name="email" id="email" required class="pc-input">
        </div>

        <div>
          <label for="first_name" class="pc-label">First Name</label>
          <input type="text" name="first_name" id="first_name" required class="pc-input">
        </div>

        <div>
          <label for="middle_name" class="pc-label">Middle Name (Optional)</label>
          <input type="text" name="middle_name" id="middle_name" class="pc-input">
        </div>

        <div class="sm:col-span-2">
          <label for="last_name" class="pc-label">Last Name</label>
          <input type="text" name="last_name" id="last_name" required class="pc-input">
        </div>

        <div>
          <label for="password" class="pc-label">New Password</label>
          <input type="password" name="password" id="password" required class="pc-input">
        </div>

        <div>
          <label for="confirm_password" class="pc-label">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirm_password" required class="pc-input">
        </div>

        <div class="sm:col-span-2">
          <label for="type" class="pc-label">Type</label>
          <select name="type" id="type" required class="pc-select">
            <option value="">Select user type</option>
            <option value="faculty">Faculty</option>
            <option value="gec">GEC</option>
          </select>
        </div>

        <div class="sm:col-span-2 mt-2">
          <button type="submit" class="pc-btn pc-btn-primary w-full justify-center">
            <span class="material-icons">person_add</span> Create Account
          </button>
        </div>
      </form>

      <div class="text-center text-sm text-gray-600 mt-5 pt-4 border-t border-gray-100">
        Already have an account?
        <a href="login" class="pc-text-red font-bold hover:underline ml-1">Back to login</a>
      </div>
    </div>

  </div>
</div>

<?php
include "src/components/footer.php";
?>


<script src="static/js/register.js"></script>
