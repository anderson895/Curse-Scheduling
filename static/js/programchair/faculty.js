$(document).ready(function () {

    const container = $('.all_accounts_container');
    const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    // =============================
    // CARD SHELL + SEARCH INPUT
    // =============================
    container.html(`
        <div class="pc-card">
            <div class="pc-card-header">
                <div class="pc-card-title">
                    <span class="material-icons">school</span>
                    <span>Faculty Directory</span>
                </div>
                <div class="pc-search-wrap" style="min-width: 18rem;">
                    <span class="material-icons">search</span>
                    <input id="accountSearch" class="pc-input" placeholder="Search faculty by name, email, username...">
                </div>
            </div>
            <div class="pc-card-body" style="padding: 0;">
                <div id="accountsTableWrap" style="overflow-x: auto;"></div>
            </div>
        </div>
    `);

    const tableWrap = $('#accountsTableWrap');

    // =============================
    // FETCH ACCOUNTS + SUBJECTS (parallel)
    // =============================
    let allSubjects = [];

    $.ajax({
        url: '../controller/end-points/get_controller.php',
        method: 'GET',
        data: { requestType: 'fetchSubjects' },
        dataType: 'json',
        success: function (subs) {
            allSubjects = Array.isArray(subs) ? subs : (subs.data || []);
        }
    });

    $.ajax({
        url: '../controller/end-points/get_controller.php',
        method: 'GET',
        data: { requestType: 'get_all_accounts', user_type: 'faculty' },
        dataType: 'json',
        success: function (res) {

            if (res.status !== 200) {
                tableWrap.html(`
                    <div class="pc-empty">
                        <span class="material-icons">error_outline</span>
                        <h3>Failed to load data</h3>
                        <p>Please try refreshing the page.</p>
                    </div>
                `);
                return;
            }

            const accounts = res.data || [];

            function renderTable(data) {

                if (!data || data.length === 0) {
                    tableWrap.html(`
                        <div class="pc-empty">
                            <span class="material-icons">person_search</span>
                            <h3>No faculty accounts found</h3>
                            <p>Try adjusting your search query.</p>
                        </div>
                    `);
                    return;
                }

                let rows = '';

                data.forEach(acc => {
                    const isActive = acc.user_status == 1;
                    const fullName = [acc.user_fname, acc.user_lname].filter(Boolean).join(' ');

                    rows += `
                        <tr>
                            <td><span class="pc-text-muted">#${acc.user_id}</span></td>
                            <td>${acc.user_username}</td>
                            <td>${acc.user_email}</td>
                            <td>${acc.user_fname}</td>
                            <td>${acc.user_mname || '<span class="pc-text-muted">—</span>'}</td>
                            <td>${acc.user_lname}</td>
                            <td><span class="pc-chip pc-chip-blue">Faculty</span></td>
                            <td>
                                <span class="pc-chip ${isActive ? 'pc-chip-green' : 'pc-chip-gray'}">
                                    <span class="material-icons" style="font-size: .85rem; margin-right: .25rem;">
                                        ${isActive ? 'check_circle' : 'pause_circle'}
                                    </span>
                                    ${isActive ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                                    <button class="btnProfile pc-btn pc-btn-sm pc-btn-primary"
                                        data-id="${acc.user_id}"
                                        data-name="${fullName}"
                                        title="Edit availability and specializations">
                                        <span class="material-icons">badge</span> Profile
                                    </button>
                                    <button class="btnEdit pc-btn pc-btn-sm pc-btn-ghost"
                                        data-id="${acc.user_id}"
                                        data-username="${acc.user_username}"
                                        data-email="${acc.user_email}"
                                        data-fname="${acc.user_fname}"
                                        data-mname="${acc.user_mname || ''}"
                                        data-lname="${acc.user_lname}"
                                        data-type="${acc.user_type}">
                                        <span class="material-icons">edit</span> Edit
                                    </button>
                                    <button class="btnToggle pc-btn pc-btn-sm ${isActive ? 'pc-btn-danger' : 'pc-btn-success'}"
                                        data-id="${acc.user_id}"
                                        data-status="${acc.user_status}">
                                        <span class="material-icons">${isActive ? 'block' : 'check'}</span>
                                        ${isActive ? 'Disable' : 'Approve'}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                tableWrap.html(`
                    <table class="pc-table" style="border-radius: 0; box-shadow: none;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>First</th>
                                <th>Middle</th>
                                <th>Last</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                `);
            }

            renderTable(accounts);

            // Search filter
            $('#accountSearch').on('input', function () {
                const q = $(this).val().toLowerCase();
                const filtered = accounts.filter(acc =>
                    acc.user_username.toLowerCase().includes(q) ||
                    acc.user_email.toLowerCase().includes(q) ||
                    acc.user_fname.toLowerCase().includes(q) ||
                    acc.user_lname.toLowerCase().includes(q)
                );
                renderTable(filtered);
            });
        }
    });

    // =============================
    // BASIC EDIT MODAL (existing)
    // =============================
    $(document).on('click', '.btnEdit', function () {
        $('#edit_user_id').val($(this).data('id'));
        $('#edit_username').val($(this).data('username'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_fname').val($(this).data('fname'));
        $('#edit_mname').val($(this).data('mname'));
        $('#edit_lname').val($(this).data('lname'));
        $('#editAccountModal').removeClass('hidden').addClass('flex');
    });

    $('#closeeditAccountModal').on('click', function () {
        $('#editAccountModal').addClass('hidden').removeClass('flex');
    });

    $('#editAccountForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: $(this).serialize() + '&requestType=update_account',
            dataType: 'json',
            success: function (res) {
                pcToast(res.message || 'Updated successfully', 'success');
                setTimeout(() => location.reload(), 900);
            }
        });
    });

    // =============================
    // ENABLE / DISABLE
    // =============================
    $(document).on('click', '.btnToggle', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');
        if (!confirm('Change account status?')) return;

        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: { requestType: 'toggle_account_status', user_id: id, status: status == 1 ? 0 : 1 },
            dataType: 'json',
            success: function (res) {
                pcToast(res.message || 'Status updated', 'success');
                setTimeout(() => location.reload(), 900);
            }
        });
    });

    // ========================================================
    // FACULTY PROFILE: Availability + Specializations
    // ========================================================

    // ----- Build availability grid -----
    function buildAvailabilityGrid(availability) {
        const $grid = $('#availabilityGrid').empty();
        DAYS.forEach(day => {
            const win = (availability[day] && availability[day][0]) || { from: '', to: '' };
            const isOn = !!(win.from && win.to);
            $grid.append(`
                <div class="pc-card" style="padding: .85rem; box-shadow: none; border: 1px solid #e5e7eb;">
                    <label style="display:flex; align-items:center; gap:.5rem; font-weight:600; color:#374151; margin-bottom:.5rem;">
                        <input type="checkbox" class="dayToggle"
                            data-day="${day}"
                            style="width:1rem; height:1rem; accent-color:#7f1d1d;"
                            ${isOn ? 'checked' : ''}>
                        ${day}
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

    function readAvailabilityFromGrid() {
        const out = {};
        $('#availabilityGrid .dayToggle:checked').each(function () {
            const day = $(this).data('day');
            const from = $(`.dayFrom[data-day="${day}"]`).val();
            const to   = $(`.dayTo[data-day="${day}"]`).val();
            if (!from || !to) return;
            if (from >= to) return;
            out[day] = [{ from, to }];
        });
        return out;
    }

    // toggle window visibility
    $(document).on('change', '.dayToggle', function () {
        const day = $(this).data('day');
        $(`.dayWindow[data-day="${day}"]`).css('display', this.checked ? 'flex' : 'none');
    });

    // ----- Specialization chips -----
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
        const next = getCurrentChips().filter(c => c !== code);
        renderChips(next);
    });

    // Specialization search dropdown
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
        const next = Array.from(new Set([...getCurrentChips(), code]));
        renderChips(next);
        $subSearch.val('').focus();
    });

    // Hide dropdown when clicking elsewhere
    $(document).on('mousedown', function (e) {
        if (!$(e.target).closest('#metaSubjectSearch, #metaSubjectDropdown').length) {
            $subDrop.hide();
        }
    });

    // ----- Open/Close modal -----
    $(document).on('click', '.btnProfile', function () {
        const userId = $(this).data('id');
        const name   = $(this).data('name') || '';
        $('#meta_user_id').val(userId);
        $('#metaFacultyName').text(name);

        // reset state while loading
        buildAvailabilityGrid({});
        renderChips([]);
        $subSearch.val('');
        $subDrop.hide();

        $('#facultyMetaModal').removeClass('hidden').addClass('flex');

        $.ajax({
            url: '../controller/end-points/get_controller.php',
            method: 'GET',
            data: { requestType: 'get_faculty_meta', user_id: userId },
            dataType: 'json',
            success: function (res) {
                const meta = (res && res.data) || { availability: {}, availability_admin: {}, specializations: [] };
                buildAvailabilityGrid(meta.availability_admin || {});
                renderChips(Array.isArray(meta.specializations) ? meta.specializations : []);

                const self = meta.availability || {};
                const days = Object.keys(self);
                if (days.length) {
                    const summary = days.map(d => {
                        const win = (self[d] && self[d][0]) || {};
                        return win.from && win.to ? `${d} ${win.from}–${win.to}` : '';
                    }).filter(Boolean).join(' · ');
                    $('#facultySelfAvailSummary').text(summary || '(none)');
                    $('#facultySelfAvailNotice').removeClass('hidden');
                } else {
                    $('#facultySelfAvailNotice').addClass('hidden');
                }
            },
            error: function () {
                pcToast('Failed to load existing profile.', 'error');
            }
        });
    });

    $('#closeFacultyMetaModal').on('click', function () {
        $('#facultyMetaModal').addClass('hidden').removeClass('flex');
        $subDrop.hide();
    });

    // ----- Save -----
    $('#facultyMetaForm').submit(function (e) {
        e.preventDefault();

        const user_id = $('#meta_user_id').val();
        const availability = readAvailabilityFromGrid();
        const specializations = getCurrentChips();

        if (!Object.keys(availability).length) {
            pcToast('Please set at least one available day with valid hours.', 'warning');
            return;
        }

        const $btn = $(this).find('button[type=submit]').prop('disabled', true);

        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: {
                requestType: 'save_faculty_meta',
                user_id,
                availability:    JSON.stringify(availability),
                specializations: JSON.stringify(specializations)
            },
            dataType: 'json',
            success: function (res) {
                $btn.prop('disabled', false);
                if (res.status === 'success') {
                    pcToast(res.message || 'Faculty profile saved.', 'success');
                    $('#facultyMetaModal').addClass('hidden').removeClass('flex');
                } else {
                    pcToast(res.message || 'Failed to save profile.', 'error');
                }
            },
            error: function () {
                $btn.prop('disabled', false);
                pcToast('Network/server error while saving.', 'error');
            }
        });
    });

});
