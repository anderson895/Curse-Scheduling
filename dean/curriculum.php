<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">Curriculum</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-gray-100 min-h-screen all_accounts_container">
    <!-- MAIN CONTENT HERE -->
    <div class="flex justify-end mb-4">
        <button id="addCurriculumBtn"
            class="flex items-center gap-2 bg-red-900 hover:bg-red-800 text-white px-4 py-2 rounded-md shadow">
            <span class="material-icons">add</span>
            Add Curriculum
        </button>
    </div>


</div>





<?php

include "../src/components/dean/footer.php";
?>

<script src="../static/js/dean/curriculum.js"></script>
