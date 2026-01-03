<?php
include "../src/components/faculty/header.php";
include "../src/components/faculty/nav.php";
?>



<script>
    // JS auto-redirect
    var schId = <?= json_encode($sch_id) ?>;
    if (schId) {
        window.location.href = "view_fac_sched.php?sch_id=" + schId;
    } else {
        window.location.href = "view_fac_sched.php?sch_id=noschedule";
    }
</script>

<?php
include "../src/components/faculty/footer.php";
?>
