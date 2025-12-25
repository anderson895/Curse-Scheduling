<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">All Accounts</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-gray-100 min-h-screen all_accounts_container">
    <!-- MAIN CONTENT HERE -->
     

</div>


<!-- EDIT MODAL -->
<div id="editAccountModal"
  class="fixed inset-0 hidden z-50 flex items-center justify-center
         bg-[rgba(0,0,0,0.5)]">

  <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6">
    <h2 class="text-xl font-bold mb-4 text-red-900">Edit Account</h2>

    <form id="editAccountForm" class="space-y-3">
      <input type="hidden" name="user_id" id="edit_user_id">

      <input type="text" id="edit_username"
        class="w-full p-2 border rounded" name="username" placeholder="Username" required>

      <input type="email" id="edit_email"
        class="w-full p-2 border rounded" name="email" placeholder="Email" required>

      <input type="text" id="edit_fname"
        class="w-full p-2 border rounded" name="first_name" placeholder="First Name" required>

      <input type="text" id="edit_mname"
        class="w-full p-2 border rounded" name="middle_name" placeholder="Middle Name">

      <input type="text" id="edit_lname"
        class="w-full p-2 border rounded" name="last_name" placeholder="Last Name" required>

      <div class="flex justify-end gap-2 pt-4">
        <button type="button" id="closeeditAccountModal"
          class="px-4 cursor-pointer py-2 bg-gray-300 rounded">
          Cancel
        </button>

        <button type="submit"
          class="px-4 py-2 bg-red-900 text-white rounded">
          Save
        </button>
      </div>
    </form>
  </div>
</div>


<?php

include "../src/components/dean/footer.php";
?>

<script src="../static/js/dean/all_accounts.js"></script>
