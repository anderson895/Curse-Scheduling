<?php
include "../src/components/programchair/header.php";
include "../src/components/programchair/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">meeting_room</span></div>
      <div>
        <h2>Room Schedules</h2>
        <p>Per-room weekly timetable across all schedules</p>
      </div>
    </div>
    <div class="pc-topbar-meta">
      <div class="pc-topbar-welcome hidden sm:block">
        <p class="small">Welcome,</p>
        <p class="name"><?=ucfirst($On_Session[0]['user_username'])?></p>
      </div>
      <div class="pc-avatar"><?php echo strtoupper(substr($On_Session[0]['user_username'], 0, 1)); ?></div>
    </div>
  </div>
</div>

<div class="p-2 sm:p-4">

  <!-- Summary bar -->
  <div class="pc-card mb-5">
    <div class="pc-card-body flex items-center gap-3">
      <div class="pc-stat-icon" style="background: rgba(127,29,29,.08); border-color: rgba(127,29,29,.2);">
        <span class="material-icons" style="color: #7f1d1d;">meeting_room</span>
      </div>
      <div>
        <div class="text-2xl font-extrabold text-red-900" id="roomCount">—</div>
        <div class="text-xs text-gray-500 uppercase tracking-wide">Total Rooms in Use</div>
      </div>
      <div class="ml-auto relative w-full sm:w-72">
        <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">search</span>
        <input type="text" id="roomSearch" placeholder="Search room number..." class="pc-input pl-9 text-sm">
      </div>
    </div>
  </div>

  <!-- Room Tabs -->
  <div id="roomTabs" class="flex flex-wrap gap-2 mb-4"></div>

  <!-- Room Timetable Container -->
  <div id="roomsContainer" class="pc-card overflow-x-auto p-4 min-h-40"></div>

</div>

<?php include "../src/components/programchair/footer.php"; ?>
<script src="../static/js/programchair/rooms.js?v=<?=filemtime(__DIR__ . '/../static/js/programchair/rooms.js')?>"></script>
