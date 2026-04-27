<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">meeting_room</span></div>
      <div>
        <h2>Available Rooms</h2>
        <p>Live room availability across the week</p>
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

  <!-- Stats Bar -->
  <div class="rooms-stats-grid mb-5">
    <div class="pc-card rooms-stat rooms-stat--total">
      <div class="pc-card-body flex items-center gap-3">
        <div class="rooms-stat-icon"><span class="material-icons">meeting_room</span></div>
        <div>
          <div class="rooms-stat-value" id="statTotal">—</div>
          <div class="rooms-stat-label">Total Rooms</div>
        </div>
      </div>
    </div>
    <div class="pc-card rooms-stat rooms-stat--avail">
      <div class="pc-card-body flex items-center gap-3">
        <div class="rooms-stat-icon"><span class="material-icons">check_circle</span></div>
        <div>
          <div class="rooms-stat-value" id="statAvail">—</div>
          <div class="rooms-stat-label">Available Now</div>
        </div>
      </div>
    </div>
    <div class="pc-card rooms-stat rooms-stat--occupied">
      <div class="pc-card-body flex items-center gap-3">
        <div class="rooms-stat-icon"><span class="material-icons">do_not_disturb_on</span></div>
        <div>
          <div class="rooms-stat-value" id="statOccupied">—</div>
          <div class="rooms-stat-label">Occupied Now</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Legend -->
  <div class="rooms-legend-card">
    <div class="rooms-legend-title">
      <span class="material-icons">info</span> Legend
    </div>
    <div class="rooms-legend-list">
      <div class="rooms-legend-row">
        <span class="rooms-legend-swatch is-free"></span>
        <div class="rooms-legend-text">
          <span class="rooms-legend-name">Free / Available</span>
          <span class="rooms-legend-desc">No class is scheduled in this room during this time slot.</span>
        </div>
      </div>
      <div class="rooms-legend-row">
        <span class="rooms-legend-swatch is-occupied"></span>
        <div class="rooms-legend-text">
          <span class="rooms-legend-name">Occupied</span>
          <span class="rooms-legend-desc">A class is scheduled in this room at this time — reflects the room's booking status.</span>
        </div>
      </div>
      <div class="rooms-legend-row">
        <span class="rooms-legend-swatch is-now"></span>
        <div class="rooms-legend-text">
          <span class="rooms-legend-name">Current time slot</span>
          <span class="rooms-legend-desc">Highlights the 30-minute slot covering the current time (real-time indicator).</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Day Tabs -->
  <div id="dayTabs" class="pc-tabs mb-4"></div>

  <!-- Availability Grid -->
  <div class="pc-card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="pc-room-grid rooms-avail-grid">
        <thead>
          <tr id="availHead"></tr>
        </thead>
        <tbody id="availGrid">
          <tr><td class="text-center text-gray-400 py-10">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php include "../src/components/faculty/footer.php"; ?>
<script src="../static/js/faculty/rooms.js"></script>
