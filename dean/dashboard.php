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
    <span>Admin Dashboard</span>
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
      <span class="material-icons text-white text-4xl">event</span>
      <div>
        <p class="text-gray-300">Reservations</p>
        <h2 class="text-3xl font-bold text-white" id="totalReservations">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">hourglass_empty</span>
      <div>
        <p class="text-gray-300">Pending</p>
        <h2 class="text-3xl font-bold text-yellow-400" id="pendingReservations">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">check_circle</span>
      <div>
        <p class="text-gray-300">Confirmed</p>
        <h2 class="text-3xl font-bold text-green-400" id="confirmedReservations">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">flag</span>
      <div>
        <p class="text-gray-300">Completed</p>
        <h2 class="text-3xl font-bold text-blue-400" id="completedReservations">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">restaurant_menu</span>
      <div>
        <p class="text-gray-300">Active Menu</p>
        <h2 class="text-3xl font-bold text-white" id="activeMenuItems">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">local_offer</span>
      <div>
        <p class="text-gray-300">Promos</p>
        <h2 class="text-3xl font-bold text-white" id="totalPromos">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">groups</span>
      <div>
        <p class="text-gray-300">Group Deals</p>
        <h2 class="text-3xl font-bold text-white" id="totalGroupDeals">0</h2>
      </div>
    </div>

    <div class="bg-red-800 shadow rounded-xl p-4 flex items-center space-x-4">
      <span class="material-icons text-white text-4xl">₱</span>
      <div>
        <p class="text-gray-300">Total Sales</p>
        <h2 class="text-3xl font-bold text-green-400" id="totalSales">₱0.00</h2>
      </div>
    </div>

  </div>
</div>

<?php
include "../src/components/dean/footer.php";
?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="../static/js/admin/dashboard.js"></script>
<script src="../static/js/admin/table_design.js"></script>
