<?php
include "src/components/header.php";
?>

<!-- Page Wrapper -->
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4 py-8">

  <!-- Login Card -->
  <div class="relative w-full max-w-md bg-white border border-red-900 rounded-2xl shadow-xl p-8 space-y-6">

    <!-- Logo and Title -->
    <div class="flex flex-col items-center space-y-3">
      <h1 class="text-3xl font-extrabold text-red-900 tracking-wide">
        Course Scheduling Login
      </h1>
    </div>

    <!-- Spinner Overlay -->
    <div id="spinner"
         class="absolute inset-0 flex items-center justify-center z-50 bg-white/80"
         style="display:none;">
      <div class="w-12 h-12 border-4 border-red-900 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Login Form -->
    <form id="frmLogin" method="POST" class="space-y-5">

      <div>
        <label for="username" class="block text-red-900 font-semibold mb-1">
          Username
        </label>
        <input type="text" id="username" name="username" required
               class="w-full px-4 py-2 rounded-lg bg-gray-50 text-gray-800
                      border border-red-900
                      focus:outline-none focus:ring-2 focus:ring-red-800"/>
      </div>

      <div>
        <label for="password" class="block text-red-900 font-semibold mb-1">
          Password
        </label>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-2 rounded-lg bg-gray-50 text-gray-800
                      border border-red-900
                      focus:outline-none focus:ring-2 focus:ring-red-800"/>
      </div>

      <button type="submit" id="btnLogin"
              class="w-full cursor-pointer bg-red-900 text-white font-bold
                     py-2 rounded-full hover:bg-red-800 transition">
        Login
      </button>
    </form>


  </div>
</div>

<?php
include "src/components/footer.php";
?>
<script src="static/js/login.js"></script>
