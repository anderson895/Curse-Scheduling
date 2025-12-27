<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">Faculty Schedule</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-gray-100 min-h-screen all_accounts_container">

    
</div>

<?php
include "../src/components/programchair/footer.php";
?>

<script src="../static/js/programchair/fac_sched.js"></script>
