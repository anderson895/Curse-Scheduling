$(document).ready(function () {

    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const $grid = $('#availabilityGrid');
    let allSubjects = [];

    // ----- AVAILABILITY GRID -----
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
        $(`.dayBadge[data-day="${day}"]`)
            .text(on ? 'Available' : 'Not available')
            .css({ background: on ? '#dcfce7' : '#f3f4f6', color: on ? '#166534' : '#6b7280' });
    });

    $('#clearAllBtn').on('click', function () {
        if (!confirm('Clear all availability? This will mark all days as not available.')) return;
        buildGrid({});
    });

    // ----- SPECIALIZATIONS -----
    function renderChips(codes) {
        const $chips = $('#metaSpecChips').empty();
        if (!codes.length) {
            $chips.html('<span class="pc-text-muted" style="font-size:.85rem;">No specializations selected yet.</span>');
            return;
        }
        codes.forEach(code => {
            const subj = allSubjects.find(s => s.subject_code === code);
            const label = subj ? `${subj.subject_code} — ${subj.subject_name}` : code;
            $chips.append(`
                <span class="pc-chip pc-chip-red" data-code="${code}" style="padding:.3rem .55rem;">
                    ${label}
                    <button type="button" class="removeChip" data-code="${code}"
                        style="margin-left:.4rem; background:transparent; border:none; cursor:pointer; color:#7f1d1d; display:inline-flex;">
                        <span class="material-icons" style="font-size:.95rem;">close</span>
                    </button>
                </span>
            `);
        });
    }

    function getCurrentChips() {
        return $('#metaSpecChips .pc-chip').map((_, el) => $(el).data('code').toString()).get();
    }

    $(document).on('click', '.removeChip', function () {
        const code = $(this).data('code').toString();
        renderChips(getCurrentChips().filter(c => c !== code));
    });

    const $subSearch = $('#metaSubjectSearch');
    const $subDrop   = $('#metaSubjectDropdown');

    function positionDropdown() {
        const r = $subSearch[0].getBoundingClientRect();
        $subDrop.css({
            position: 'fixed',
            top: (r.bottom + 4) + 'px',
            left: r.left + 'px',
            width: r.width + 'px',
            zIndex: 9999
        });
    }

    $subSearch.on('input focus', function () {
        const q = $(this).val().toLowerCase().trim();
        const selected = new Set(getCurrentChips());
        const matches = allSubjects
            .filter(s => !selected.has(s.subject_code))
            .filter(s => !q || (s.subject_code || '').toLowerCase().includes(q) || (s.subject_name || '').toLowerCase().includes(q))
            .slice(0, 30);

        if (!matches.length) {
            $subDrop.html('<div style="padding:.65rem; color:#6b7280; font-size:.85rem;">No matching subjects.</div>');
        } else {
            $subDrop.empty();
            matches.forEach(s => {
                $subDrop.append(`
                    <div class="subjectOption" data-code="${s.subject_code}"
                         style="padding:.55rem .75rem; cursor:pointer; border-bottom:1px solid #f3f4f6;">
                        <div style="font-weight:600; color:#7f1d1d; font-size:.82rem;">${s.subject_code}</div>
                        <div style="color:#4b5563; font-size:.78rem;">${s.subject_name || ''}</div>
                    </div>
                `);
            });
        }
        positionDropdown();
        $subDrop.removeClass('hidden').show();
    });

    $(document).on('mousedown', '.subjectOption', function (e) {
        e.preventDefault();
        const code = $(this).data('code').toString();
        renderChips(Array.from(new Set([...getCurrentChips(), code])));
        $subSearch.val('').focus();
    });

    $(document).on('mousedown', function (e) {
        if (!$(e.target).closest('#metaSubjectSearch, #metaSubjectDropdown').length) {
            $subDrop.hide();
        }
    });

    // ----- LOAD -----
    function loadSubjects() {
        return $.ajax({
            url: '../controller/end-points/get_controller.php',
            method: 'GET',
            data: { requestType: 'fetchSubjects' },
            dataType: 'json'
        }).then(function (subs) {
            allSubjects = Array.isArray(subs) ? subs : (subs.data || []);
        });
    }

    function loadProfile() {
        return $.ajax({
            url: '../controller/end-points/get_controller.php',
            method: 'GET',
            data: { requestType: 'get_faculty_meta', user_id: SESSION_USER_ID },
            dataType: 'json'
        }).then(function (res) {
            const meta = (res && res.data) || { availability: {}, specializations: [] };
            buildGrid(meta.availability || {});
            renderChips(Array.isArray(meta.specializations) ? meta.specializations : []);
        }, function () {
            buildGrid({});
            renderChips([]);
            if (typeof pcToast === 'function') pcToast('Failed to load existing profile.', 'error');
        });
    }

    // ----- SAVE -----
    $('#profileForm').on('submit', function (e) {
        e.preventDefault();
        const { availability, error } = readGrid();
        if (error) {
            if (typeof pcToast === 'function') pcToast(error, 'warning'); else alert(error);
            return;
        }
        const specializations = getCurrentChips();

        const $btn = $(this).find('button[type=submit]').prop('disabled', true);
        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: {
                requestType: 'save_my_profile',
                availability:    JSON.stringify(availability),
                specializations: JSON.stringify(specializations)
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false);
                const msg = res.message || (res.status === 'success' ? 'Profile saved.' : 'Failed to save.');
                if (typeof pcToast === 'function') {
                    pcToast(msg, res.status === 'success' ? 'success' : 'error');
                } else {
                    alert(msg);
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                if (typeof pcToast === 'function') pcToast('Network/server error while saving.', 'error');
                else alert('Network/server error while saving.');
            }
        });
    });

    // ----- INIT -----
    buildGrid({});
    renderChips([]);
    loadSubjects().always(loadProfile);
});
