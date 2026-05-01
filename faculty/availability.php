<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">event_available</span></div>
      <div>
        <h2>My Availability</h2>
        <p>Set the days and hours you are available to teach</p>
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

  <div class="pc-card mb-4">
    <div class="pc-card-body">
      <div class="flex items-start gap-3">
        <span class="material-icons text-red-800">info</span>
        <div class="text-sm text-gray-600">
          <p class="font-semibold text-gray-700 mb-1">How this works</p>
          <ul class="list-disc pl-5 space-y-0.5">
            <li>Check the box for each day you are available, then set your <strong>from</strong> and <strong>to</strong> times.</li>
            <li>Leave a day unchecked if you are <strong>not available</strong> on that day.</li>
            <li>When the Dean or Program Chair runs <strong>Auto-Generate Schedule</strong>, classes will only be plotted within the times you declare here.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="pc-card">
    <div class="pc-card-header">
      <div class="pc-card-title">
        <span class="material-icons">schedule</span>
        <span>Weekly Availability</span>
      </div>
    </div>
    <div class="pc-card-body">
      <form id="availabilityForm" class="space-y-5">
        <div id="availabilityGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>

        <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3 border-t border-gray-100">
          <button type="button" id="clearAllBtn" class="pc-btn pc-btn-neutral">
            <span class="material-icons">refresh</span> Clear All
          </button>
          <button type="submit" class="pc-btn pc-btn-primary">
            <span class="material-icons">save</span> Save Availability
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<?php
include "../src/components/faculty/footer.php";
?>

<script>
  var SESSION_USER_ID = <?= json_encode(intval($On_Session[0]['user_id'])) ?>;
</script>
<script src="../static/js/faculty/availability.js?v=<?=filemtime(__DIR__ . '/../static/js/faculty/availability.js')?>"></script>
