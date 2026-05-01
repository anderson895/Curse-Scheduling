$(document).ready(function () {

    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const $grid = $('#availabilityGrid');

    function buildGrid(availability) {
        $grid.empty();
        DAYS.forEach(day => {
            const win = (availability[day] && availability[day][0]) || { from: '', to: '' };
            const isOn = !!(win.from && win.to);
            $grid.append(`
                <div class="pc-card" style="padding: .9rem; box-shadow: none; border: 1px solid #e5e7eb;">
                    <label style="display:flex; align-items:center; gap:.5rem; font-weight:600; color:#374151; margin-bottom:.6rem;">
                        <input type="checkbox" class="dayToggle"
                            data-day="${day}"
                            style="width:1rem; height:1rem; accent-color:#7f1d1d;"
                            ${isOn ? 'checked' : ''}>
                        ${day}
                        <span class="dayBadge" data-day="${day}"
                            style="margin-left:auto; font-size:.7rem; padding:.15rem .5rem; border-radius:9999px;
                                   background:${isOn ? '#dcfce7' : '#f3f4f6'}; color:${isOn ? '#166534' : '#6b7280'};">
                            ${isOn ? 'Available' : 'Not available'}
                        </span>
                    </label>
                    <div class="dayWindow" data-day="${day}" style="display:${isOn ? 'flex' : 'none'}; gap:.4rem; align-items:center;">
                        <input type="time" class="pc-input dayFrom" data-day="${day}" value="${win.from || '07:00'}" style="font-size:.85rem;">
                        <span style="color:#6b7280; font-size:.8rem;">to</span>
                        <input type="time" class="pc-input dayTo" data-day="${day}" value="${win.to || '17:00'}" style="font-size:.85rem;">
                    </div>
                </div>
            `);
        });
    }

    function readGrid() {
        const out = {};
        let invalid = null;
        $('#availabilityGrid .dayToggle:checked').each(function () {
            const day = $(this).data('day');
            const from = $(`.dayFrom[data-day="${day}"]`).val();
            const to   = $(`.dayTo[data-day="${day}"]`).val();
            if (!from || !to) { invalid = `Please set both from and to time for ${day}.`; return; }
            if (from >= to) { invalid = `${day}: "from" time must be earlier than "to" time.`; return; }
            out[day] = [{ from, to }];
        });
        return { availability: out, error: invalid };
    }

    $(document).on('change', '.dayToggle', function () {
        const day = $(this).data('day');
        const on  = this.checked;
        $(`.dayWindow[data-day="${day}"]`).css('display', on ? 'flex' : 'none');
        const $badge = $(`.dayBadge[data-day="${day}"]`);
        $badge.text(on ? 'Available' : 'Not available')
              .css({ background: on ? '#dcfce7' : '#f3f4f6', color: on ? '#166534' : '#6b7280' });
    });

    $('#clearAllBtn').on('click', function () {
        if (!confirm('Clear all availability? This will mark all days as not available.')) return;
        buildGrid({});
    });

    function loadAvailability() {
        $.ajax({
            url: '../controller/end-points/get_controller.php',
            method: 'GET',
            data: { requestType: 'get_faculty_meta', user_id: SESSION_USER_ID },
            dataType: 'json',
            success: function (res) {
                const meta = (res && res.data) || { availability: {} };
                buildGrid(meta.availability || {});
            },
            error: function () {
                buildGrid({});
                if (typeof pcToast === 'function') pcToast('Failed to load existing availability.', 'error');
            }
        });
    }

    $('#availabilityForm').on('submit', function (e) {
        e.preventDefault();
        const { availability, error } = readGrid();
        if (error) {
            if (typeof pcToast === 'function') pcToast(error, 'warning'); else alert(error);
            return;
        }

        const $btn = $(this).find('button[type=submit]').prop('disabled', true);
        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: {
                requestType: 'save_my_availability',
                availability: JSON.stringify(availability)
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false);
                if (res.status === 'success') {
                    if (typeof pcToast === 'function') pcToast(res.message || 'Availability saved.', 'success');
                    else alert(res.message || 'Availability saved.');
                } else {
                    if (typeof pcToast === 'function') pcToast(res.message || 'Failed to save.', 'error');
                    else alert(res.message || 'Failed to save.');
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                if (typeof pcToast === 'function') pcToast('Network/server error while saving.', 'error');
                else alert('Network/server error while saving.');
            }
        });
    });

    loadAvailability();
});
