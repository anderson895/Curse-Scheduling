$(document).ready(function () {

    const container = $('.all_accounts_container');

    // =============================
    // SEARCH INPUT
    // =============================
    container.html(`
        <div class="mb-4">
            <input id="accountSearch"
                class="w-full p-2 border rounded"
                placeholder="Search accounts...">
        </div>
    `);

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
                container.append(`
                    <p class="text-red-500 text-center py-4">
                        Failed to load data
                    </p>
                `);
                return;
            }

            const accounts = res.data || [];

            // =============================
            // RENDER TABLE
            // =============================
            function renderTable(data) {

                // REMOVE PREVIOUS TABLE / MESSAGE
                $('#accountsTable').remove();

                // 🔴 NO RECORDS FOUND
                if (!data || data.length === 0) {
                    container.append(`
                        <div id="accountsTable" class="text-center py-8 text-gray-500">
                            No records found
                        </div>
                    `);
                    return;
                }

                let html = `
                <table class="min-w-full border border-gray-300 bg-white shadow-md rounded-lg">
                    <thead class="bg-red-900 text-white">
                        <tr>
                            <th class="p-3">ID</th>
                            <th class="p-3">Username</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">First</th>
                            <th class="p-3">Middle</th>
                            <th class="p-3">Last</th>
                            <th class="p-3">Type</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                data.forEach(acc => {
                    html += `
                    <tr class="border-t">
                        <td class="p-3">${acc.user_id}</td>
                        <td class="p-3">${acc.user_username}</td>
                        <td class="p-3">${acc.user_email}</td>
                        <td class="p-3">${acc.user_fname}</td>
                        <td class="p-3">${acc.user_mname || '-'}</td>
                        <td class="p-3">${acc.user_lname}</td>
                        <td class="p-3 capitalize">${acc.user_type}</td>
                        <td class="p-3">
                            ${acc.user_status == 1 ? 'Active' : 'Inactive'}
                        </td>
                        <td class="p-3 space-x-2">
                            <button class="btnEdit cursor-pointer bg-blue-600 text-white px-3 py-1 rounded"
                                data-id="${acc.user_id}"
                                data-username="${acc.user_username}"
                                data-email="${acc.user_email}"
                                data-fname="${acc.user_fname}"
                                data-mname="${acc.user_mname || ''}"
                                data-lname="${acc.user_lname}"
                                data-type="${acc.user_type}">
                                Edit
                            </button>

                            <button class="btnToggle cursor-pointer bg-red-600 text-white px-3 py-1 rounded"
                                data-id="${acc.user_id}"
                                data-status="${acc.user_status}">
                                ${acc.user_status == 1 ? 'Disable' : 'Approve'}
                            </button>
                        </td>
                    </tr>
                    `;
                });

                html += `
                    </tbody>
                </table>
                `;

                container.append(`<div id="accountsTable">${html}</div>`);
            }

            // INITIAL LOAD
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
