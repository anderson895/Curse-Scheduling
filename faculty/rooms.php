<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">Available Rooms</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-4 sm:p-6 bg-gray-100 min-h-screen">

  <!-- Stats Bar -->
  <div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-lg shadow-md p-4 text-center">
      <div class="text-2xl font-bold text-red-900" id="statTotal">—</div>
      <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Total Rooms</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4 text-center">
      <div class="text-2xl font-bold text-green-600" id="statAvail">—</div>
      <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Available Now</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4 text-center">
      <div class="text-2xl font-bold text-red-600" id="statOccupied">—</div>
      <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Occupied Now</div>
    </div>
  </div>

  <!-- Legend -->
  <div class="flex items-center gap-4 mb-4 text-sm">
    <span class="flex items-center gap-1">
      <span class="inline-block w-4 h-4 rounded bg-green-100 border border-green-300"></span>
      <span class="text-gray-600">Free / Available</span>
    </span>
    <span class="flex items-center gap-1">
      <span class="inline-block w-4 h-4 rounded bg-red-100 border border-red-300"></span>
      <span class="text-gray-600">Occupied</span>
    </span>
    <span class="flex items-center gap-1">
      <span class="inline-block w-4 h-4 rounded bg-yellow-200 border border-yellow-400"></span>
      <span class="text-gray-600">Current time slot</span>
    </span>
  </div>

  <!-- Day Tabs -->
  <div id="dayTabs" class="flex flex-wrap gap-2 mb-4"></div>

  <!-- Availability Grid -->
  <div class="bg-white rounded-lg shadow-md overflow-x-auto">
    <table class="min-w-full border-collapse text-sm">
      <thead>
        <tr id="availHead"></tr>
      </thead>
      <tbody id="availGrid">
        <tr><td class="text-center text-gray-400 py-10">Loading...</td></tr>
      </tbody>
    </table>
  </div>

</div>

<?php include "../src/components/faculty/footer.php"; ?>
<script src="../static/js/faculty/rooms.js"></script>
