<?php
include "../src/components/gec/header.php";
include "../src/components/gec/nav.php";
?>

<?php
// Kunin ang sch_id mula sa schedule array
$sch_id = $schedule[0]['sch_id'] ?? null;
?>

<script>
    // JS auto-redirect
    var schId = <?= json_encode($sch_id) ?>;
    if (schId) {
        window.location.href = "fac_sched.php?sch_id=" + schId;
    } else {
        alert("No schedule ID found.");
    }
</script>

<?php
include "../src/components/gec/footer.php";
?>
