<?php
include "src/components/header.php";
?>
<?php include "src/components/design_system.php"; ?>

<div class="pc-auth-shell">

  <div class="pc-auth-card relative">

    <!-- Spinner Overlay -->
    <div id="spinner"
         class="absolute inset-0 flex items-center justify-center z-50 bg-white/80"
         style="display:none;">
      <div class="w-12 h-12 border-4 border-red-900 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Header -->
    <div class="pc-auth-header">
      <img src="static/logo.jpg" alt="Logo">
      <h1>Course scheduling</h1>
      <p>Sign in to continue</p>
    </div>

    <!-- Login Form -->
    <div class="pc-auth-body">
      <form id="frmLogin" method="POST" class="space-y-4">

        <div>
          <label for="username" class="pc-label">Username</label>
          <input type="text" id="username" name="username" required class="pc-input" autocomplete="username">
        </div>

        <div>
          <label for="password" class="pc-label">Password</label>
          <input type="password" id="password" name="password" required class="pc-input" autocomplete="current-password">
        </div>

        <button type="submit" id="btnLogin" class="pc-btn pc-btn-primary w-full justify-center">
          <span class="material-icons">login</span> Login
        </button>
      </form>

      <div class="text-center text-sm text-gray-600 mt-5 pt-4 border-t border-gray-100">
        Don't have an account?
        <a href="register" class="pc-text-red font-bold hover:underline ml-1">Register</a>
      </div>
    </div>

  </div>
</div>

<?php
include "src/components/footer.php";
?>
<script src="static/js/login.js"></script>
