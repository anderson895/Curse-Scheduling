<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>

<div class="pc-topbar">
  <div class="pc-topbar-inner">
    <div class="pc-topbar-title">
      <div class="pc-topbar-icon"><span class="material-icons">badge</span></div>
      <div>
        <h2>My Profile</h2>
        <p>Set your weekly availability and teaching specializations</p>
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
            <li><strong>Availability</strong> — check the days you can teach and set your from/to times. Uncheck a day to mark yourself unavailable.</li>
            <li><strong>Specializations</strong> — pick the subjects you can teach. Auto-Generate prefers faculty whose specializations match the subject.</li>
            <li>The Dean's <strong>Auto-Generate Schedule</strong> uses both to plot your classes only inside your declared windows and matched subjects.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <form id="profileForm" class="space-y-4">

    <!-- AVAILABILITY -->
    <div class="pc-card">
      <div class="pc-card-header">
        <div class="pc-card-title">
          <span class="material-icons">event_available</span>
          <span>Weekly Availability</span>
        </div>
      </div>
      <div class="pc-card-body">
        <div id="availabilityGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
      </div>
    </div>

    <!-- SPECIALIZATIONS -->
    <div class="pc-card">
      <div class="pc-card-header">
        <div class="pc-card-title">
          <span class="material-icons">workspace_premium</span>
          <span>Specializations</span>
        </div>
      </div>
      <div class="pc-card-body">
        <div class="mb-3" style="position: relative; width: 100%; max-width: 28rem;">
          <span class="material-icons"
            style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%);
                   color:#9ca3af; font-size:1.15rem; pointer-events:none; line-height:1;">search</span>
          <input type="text" id="metaSubjectSearch"
            placeholder="Search subject code or name..." autocomplete="off"
            style="width:100%; padding:.6rem .75rem .6rem 2.4rem;
                   border:1px solid #e5e7eb; border-radius:.55rem;
                   background:#fff; font-size:.9rem; color:#1f2937;">
          <div id="metaSubjectDropdown"
            class="hidden bg-white border border-gray-300 rounded shadow-xl max-h-52 overflow-y-auto text-sm"
            style="min-width:320px;"></div>
        </div>
        <div id="metaSpecChips" class="flex flex-wrap gap-2 min-h-[2rem]"></div>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 pt-1">
      <button type="button" id="clearAllBtn" class="pc-btn pc-btn-neutral">
        <span class="material-icons">refresh</span> Clear Availability
      </button>
      <button type="submit" class="pc-btn pc-btn-primary">
        <span class="material-icons">save</span> Save Profile
      </button>
    </div>

  </form>

</div>

<?php
include "../src/components/faculty/footer.php";
?>

<script>
  var SESSION_USER_ID = <?= json_encode(intval($On_Session[0]['user_id'])) ?>;
</script>
<script src="../static/js/faculty/profile.js?v=<?=filemtime(__DIR__ . '/../static/js/faculty/profile.js')?>"></script>
