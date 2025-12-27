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
            user_type: 'faculty'
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
                        <td class="p-3 font-semibold
                            ${acc.user_status == 1 ? 'text-green-600' : 'text-red-600'}">
                        ${acc.user_status == 1 ? 'Active' : 'Inactive'}
                        </td>

                        <td class="p-3 space-x-2">
                            <a href="view_fac_sched.php?user_id=${acc.user_id}"
                            class="btnToggle inline-block cursor-pointer bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 transition">
                            View Schedule
                            </a>


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


});
