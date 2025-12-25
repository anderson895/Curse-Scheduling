<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">Create Account</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-gray-100 min-h-screen">
    <!-- MAIN CONTENT HERE -->
     <div class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-md">
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
            <label for="password" class="block text-gray-700 font-medium">Confirm Password</label>
            <input type="password" name="password" id="confirm_password" required
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
        </div>

</div>

<?php
include "../src/components/dean/footer.php";
?>

<script src="../static/js/dean/create_account.js"></script>
