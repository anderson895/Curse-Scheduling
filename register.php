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
         class="absolute inset-0 flex items-center justify-center z-50 bg-white/80 hidden">
      <div class="w-12 h-12 border-4 border-red-900 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Login Form -->
    <form id="createAccountForm" class="space-y-4">

      <!-- Username -->
      <div>
        <label for="username" class="block text-gray-700 font-medium">Username</label>
        <input type="text" name="username" id="username" required
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 font-medium">Email</label>
        <input type="email" name="email" id="email" required
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- First Name -->
      <div>
        <label for="first_name" class="block text-gray-700 font-medium">First Name</label>
        <input type="text" name="first_name" id="first_name" required
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Middle Name (Optional) -->
      <div>
        <label for="middle_name" class="block text-gray-700 font-medium">Middle Name (Optional)</label>
        <input type="text" name="middle_name" id="middle_name"
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Last Name -->
      <div>
        <label for="last_name" class="block text-gray-700 font-medium">Last Name</label>
        <input type="text" name="last_name" id="last_name" required
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- New Password -->
      <div>
        <label for="password" class="block text-gray-700 font-medium">New Password</label>
        <input type="password" name="password" id="password" required
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="confirm_password" class="block text-gray-700 font-medium">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required
               class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
      </div>

      <!-- Type -->
      <div>
        <label for="type" class="block text-gray-700 font-medium">Type</label>
        <select name="type" id="type" required
                class="w-full mt-1 p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
          <option value="">Select user type</option>
          <option value="faculty">Faculty</option>
          <option value="program chair">Program Chair</option>
          <option value="gec">GEC</option>
          <option value="dean">Dean</option>
        </select>
      </div>

      <!-- Submit Button -->
      <div>
        <button type="submit"
                class="w-full bg-red-900 text-white font-bold py-2 px-4 rounded-md hover:bg-red-800 transition-colors">
          Create Account
        </button>
      </div>
    </form>

    <!-- Footer / Links -->
    <div class="text-center text-sm text-red-900 mt-4">
      <a href="login" class="text-red-800 font-bold hover:underline">Back</a>
    </div>

  </div>
</div>

<?php
include "src/components/footer.php";
?>


<script src="static/js/register.js"></script>
