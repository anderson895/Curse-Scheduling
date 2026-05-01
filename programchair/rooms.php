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
        <div class="text-xs text-gray-500 uppercase tracking-wide">Total Active Rooms</div>
      </div>
      <div class="ml-auto pc-search-wrap w-full sm:w-72">
        <span class="material-icons">search</span>
        <input type="text" id="roomSearch" placeholder="Search room number..." class="pc-input">
      </div>
    </div>
  </div>

  <!-- Manage Rooms (CRUD) -->
  <div class="pc-card mb-5">
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">tune</span>
        <span>Manage Rooms</span>
      </div>
      <button id="openAddRoomModal" class="pc-btn pc-btn-primary">
        <span class="material-icons">add</span> Add Room
      </button>
    </div>
    <div class="p-4 overflow-x-auto">
      <table class="pc-table">
        <thead>
          <tr>
            <th>Room</th>
            <th>Type</th>
            <th>Capacity</th>
            <th>Status</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="manageRoomsBody"></tbody>
      </table>
    </div>
  </div>

  <!-- Room Tabs -->
  <div id="roomTabs" class="flex flex-wrap gap-2 mb-4"></div>

  <!-- Room Timetable Container -->
  <div id="roomsContainer" class="pc-card overflow-x-auto p-4 min-h-40"></div>

</div>

<!-- ADD / EDIT ROOM MODAL -->
<div id="roomFormModal" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-[rgba(0,0,0,0.5)] p-4">
  <div class="pc-modal-card w-full max-w-md">
    <div class="pc-modal-header">
      <span class="material-icons">meeting_room</span>
      <h2 id="roomFormTitle">Add Room</h2>
    </div>
    <form id="roomForm" class="p-6 space-y-4">
      <input type="hidden" name="room_id" id="room_id" value="">
      <div>
        <label class="pc-label">Room Number / Name</label>
        <input type="text" name="room_name" id="room_name" class="pc-input" placeholder="e.g. 306, ME-LAB-1" required>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="pc-label">Type</label>
          <select name="room_type" id="room_type" class="pc-select">
            <option value="lecture">Lecture</option>
            <option value="laboratory">Laboratory</option>
            <option value="lecture+lab">Lecture + Lab</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div>
          <label class="pc-label">Capacity</label>
          <input type="number" name="capacity" id="capacity" class="pc-input" min="0" value="0">
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
        <button type="button" id="closeRoomFormModal" class="pc-btn pc-btn-neutral">Cancel</button>
        <button type="submit" class="pc-btn pc-btn-primary"><span class="material-icons">save</span> Save</button>
      </div>
    </form>
  </div>
</div>

<?php include "../src/components/programchair/footer.php"; ?>
<script src="../static/js/programchair/rooms.js?v=<?=filemtime(__DIR__ . '/../static/js/programchair/rooms.js')?>"></script>
