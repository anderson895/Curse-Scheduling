<?php
include "../src/components/dean/header.php";
include "../src/components/dean/nav.php";
?>

<!-- Top Bar -->
<div class="flex justify-between items-center bg-red-900 p-4 mb-6 rounded-md shadow-lg">
  <h2 class="text-xl font-bold text-white uppercase tracking-wide">Room Schedules</h2>
  <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-white font-bold shadow-md">
    <?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?>
  </div>
</div>

<div class="p-4 sm:p-6 bg-gray-100 min-h-screen">

  <!-- Summary bar -->
  <div class="bg-white rounded-lg shadow-md p-4 mb-5 flex items-center gap-3">
    <span class="material-icons text-red-900 text-3xl">meeting_room</span>
    <div>
      <div class="text-2xl font-bold text-red-900" id="roomCount">—</div>
      <div class="text-xs text-gray-500 uppercase tracking-wide">Total Rooms in Use</div>
    </div>
    <div class="ml-auto w-full sm:w-64">
      <input type="text" id="roomSearch" placeholder="Search room number..."
        class="w-full border p-2 rounded focus:ring-2 focus:ring-red-500 text-sm">
    </div>
  </div>

  <!-- Room Tabs -->
  <div id="roomTabs" class="flex flex-wrap gap-2 mb-4"></div>

  <!-- Room Timetable Container -->
  <div id="roomsContainer" class="bg-white rounded-lg shadow-md p-4 overflow-x-auto min-h-40"></div>

</div>

<?php include "../src/components/dean/footer.php"; ?>
<script src="../static/js/dean/rooms.js"></script>
