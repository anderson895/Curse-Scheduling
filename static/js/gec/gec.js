$(document).ready(function () {

    const container = $('.all_accounts_container');

    // =============================
    // CARD SHELL + SEARCH INPUT
    // =============================
    container.html(`
        <div class="pc-card">
            <div class="pc-card-header">
                <div class="pc-card-title">
                    <span class="material-icons">groups</span>
                    <span>GEC Directory</span>
                </div>
                <div class="pc-search-wrap" style="min-width: 18rem;">
                    <span class="material-icons">search</span>
                    <input id="accountSearch" class="pc-input" placeholder="Search GEC by name, email, username...">
                </div>
            </div>
            <div class="pc-card-body" style="padding: 0;">
                <div id="accountsTableWrap" style="overflow-x: auto;"></div>
            </div>
        </div>
    `);

    const tableWrap = $('#accountsTableWrap');

    // =============================
    // FETCH ACCOUNTS
    // =============================
    $.ajax({
        url: '../controller/end-points/get_controller.php',
        method: 'GET',
        data: {
            requestType: 'get_all_accounts',
            user_type: 'gec'
        },
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

            // =============================
            // RENDER TABLE
            // =============================
            function renderTable(data) {

                if (!data || data.length === 0) {
                    tableWrap.html(`
                        <div class="pc-empty">
                            <span class="material-icons">person_search</span>
                            <h3>No GEC accounts found</h3>
                            <p>Try adjusting your search query.</p>
                        </div>
                    `);
                    return;
                }

                let rows = '';

                data.forEach(acc => {
                    const isActive = acc.user_status == 1;

                    rows += `
                        <tr>
                            <td><span class="pc-text-muted">#${acc.user_id}</span></td>
                            <td>${acc.user_username}</td>
                            <td>${acc.user_email}</td>
                            <td>${acc.user_fname}</td>
                            <td>${acc.user_mname || '<span class="pc-text-muted">—</span>'}</td>
                            <td>${acc.user_lname}</td>
                            <td><span class="pc-chip pc-chip-green">GEC</span></td>
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

            // =============================
            // SEARCH FILTER
            // =============================
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
    // OPEN EDIT MODAL
    // =============================
    $(document).on('click', '.btnEdit', function () {
        $('#edit_user_id').val($(this).data('id'));
        $('#edit_username').val($(this).data('username'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_fname').val($(this).data('fname'));
        $('#edit_mname').val($(this).data('mname'));
        $('#edit_lname').val($(this).data('lname'));
        $('#edit_type').val($(this).data('type'));

        $('#editAccountModal')
            .removeClass('hidden')
            .addClass('flex');
    });

    $('#closeeditAccountModal').on('click', function () {
        $('#editAccountModal')
            .addClass('hidden')
            .removeClass('flex');
    });

    // =============================
    // SUBMIT EDIT
    // =============================
    $('#editAccountForm').submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: $(this).serialize() + '&requestType=update_account',
            dataType: 'json',
            success: function (res) {
                alert(res.message || 'Updated successfully');
                location.reload();
            }
        });
    });

    // =============================
    // ENABLE / DISABLE ACCOUNT
    // =============================
    $(document).on('click', '.btnToggle', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');

        if (!confirm('Change account status?')) return;

        $.ajax({
            url: '../controller/end-points/post_controller.php',
            method: 'POST',
            data: {
                requestType: 'toggle_account_status',
                user_id: id,
                status: status == 1 ? 0 : 1
            },
            dataType: 'json',
            success: function (res) {
                alert(res.message || 'Status updated');
                location.reload();
            }
        });
    });

});
