<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="flex flex-col sm:flex-row justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
    <h2 class="text-xl font-bold text-white uppercase tracking-wide mb-2 sm:mb-0">Report</h2>
    <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
        <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
    </div>
</div>

<div class="p-4 sm:p-6 bg-gray-100 min-h-screen">
    <!-- Tabs and Print Button -->
    <div class="flex items-center justify-between mb-4">
        <!-- Tabs -->
        <div class="flex space-x-2">
            <button class="report-tab cursor-pointer px-4 py-2 rounded bg-white text-red-900 font-semibold" data-report="curriculum">Curriculum</button>
            <button class="report-tab cursor-pointer px-4 py-2 rounded bg-red-900 text-white font-semibold" data-report="schedule">Schedule</button>
            <button class="report-tab cursor-pointer px-4 py-2 rounded bg-red-900 text-white font-semibold" data-report="subjects">Subjects</button>
            <button class="report-tab cursor-pointer px-4 py-2 rounded bg-red-900 text-white font-semibold" data-report="users">Users</button>
        </div>

        <!-- Print Button -->
        <button id="printReport" class="cursor-pointer flex items-center px-4 py-2 bg-red-900 text-white rounded shadow">
            <span class="material-icons mr-2">print</span> Print
        </button>
    </div>

    <!-- Report Container -->
    <div id="reportContainer" class="overflow-x-auto">
        <!-- Report table will be injected here -->
    </div>
</div>

<?php
include "../src/components/programchair/footer.php";
?>
<script src="../static/js/dean/report.js"></script>