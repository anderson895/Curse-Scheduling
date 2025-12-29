<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">Dashboard</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-6 bg-gray-100 min-h-screen">
  <h1 class="text-2xl font-bold mb-6 text-red-900 flex items-center space-x-2">
    <span class="material-icons text-red-900">insert_chart</span>
    <span>Dashboard</span>
  </h1>

  <!-- Stats Grid -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">person</span>
      <div>
        <p class="text-gray-300">Users</p>
        <h2 class="text-3xl font-bold text-white" id="totalUsers">0</h2>
      </div>
    </div>  

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">book</span>
      <div>
        <p class="text-gray-300">Subjects</p>
        <h2 class="text-3xl font-bold text-white" id="totalSubjects">0</h2>
      </div>
    </div>  

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">schedule</span>
      <div>
        <p class="text-gray-300">Schedules</p>
        <h2 class="text-3xl font-bold text-white" id="totalSchedules">0</h2>
      </div>
    </div>  

  </div>

<!-- Combined Charts Card -->
<div class="bg-white rounded-xl shadow p-6 mb-6">

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Bar Chart -->
    <div>
      <div id="barChart" style="min-height:300px;"></div>
    </div>

    <!-- Donut Chart -->
    <div>
      <div id="donutChart" style="min-height:300px;"></div>
    </div>
  </div>
</div>



</div>

<?php
include "../src/components/dean/footer.php";
?>

<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="../static/js/dean/dashboard.js"></script>
