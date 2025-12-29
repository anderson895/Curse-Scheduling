$(document).ready(function() {

    // Helper: convert 24h to 12h format
    function formatTime12Hr(timeStr) {
        let [hours, minutes] = timeStr.split(':').map(Number);
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes.toString().padStart(2,'0')} ${ampm}`;
    }

    // -------------------- Render report by tab --------------------
    function renderReport(type) {
        $('#reportContainer').html('<p class="text-gray-500">Loading...</p>');

        $.ajax({
            url: "../controller/end-points/get_controller.php",
            type: "GET",
            data: { requestType: `fetch${type.charAt(0).toUpperCase() + type.slice(1)}` },
            dataType: "json",
            success: function(res) {
                let html = '';

                // ---------- Curriculum ----------
                if(type === 'curriculum') {
                    html += '<h3 class="text-lg font-bold mb-2">Curriculum Report</h3>';
                    html += '<table class="table-auto border-collapse border border-gray-300 w-full mb-6">';
                    html += '<thead><tr class="bg-gray-200">';
                    html += '<th class="border px-2 py-1">Program</th><th class="border px-2 py-1">Year Level</th>';
                    html += '<th class="border px-2 py-1">Semester</th><th class="border px-2 py-1">Subject Code</th>';
                    html += '<th class="border px-2 py-1">Subject Name</th><th class="border px-2 py-1">Lec Hours</th>';
                    html += '<th class="border px-2 py-1">Lab Hours</th><th class="border px-2 py-1">Lec Units</th>';
                    html += '<th class="border px-2 py-1">Lab Units</th><th class="border px-2 py-1">Prerequisite</th></tr></thead><tbody>';
                    res.forEach(item => {
                        html += `<tr>
                            <td class="border px-2 py-1">${item.program}</td>
                            <td class="border px-2 py-1">${item.year_level}</td>
                            <td class="border px-2 py-1">${item.semester}</td>
                            <td class="border px-2 py-1">${item.subject_code}</td>
                            <td class="border px-2 py-1">${item.subject_name}</td>
                            <td class="border px-2 py-1">${item.lec_hours}</td>
                            <td class="border px-2 py-1">${item.lab_hours}</td>
                            <td class="border px-2 py-1">${item.lec_units}</td>
                            <td class="border px-2 py-1">${item.lab_units}</td>
                            <td class="border px-2 py-1">${item.prerequisite || ''}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                }

                // ---------- Schedule ----------
                else if(type === 'schedule') {
                    html += '<h3 class="text-lg font-bold mb-2">Schedule Report</h3>';
                    res.forEach(s => {
                        html += `<h4 class="font-semibold mt-2">${s.user_fname} ${s.user_lname}</h4>`;
                        try {
                            let scheduleObj = JSON.parse(s.sch_schedule);
                            html += '<table class="table-auto border-collapse border border-gray-300 w-full mb-4">';
                            html += '<thead><tr class="bg-gray-200"><th class="border px-2 py-1">Day</th><th class="border px-2 py-1">Time</th><th class="border px-2 py-1">Subject</th></tr></thead><tbody>';
                            for (let day in scheduleObj.schedule) {
                                scheduleObj.schedule[day].forEach(slot => {
                                    let from = formatTime12Hr(slot.time.from);
                                    let to = formatTime12Hr(slot.time.to);
                                    html += `<tr>
                                        <td class="border px-2 py-1">${day}</td>
                                        <td class="border px-2 py-1">${from} - ${to}</td>
                                        <td class="border px-2 py-1">${slot.subject}</td>
                                    </tr>`;
                                });
                            }
                            html += '</tbody></table>';
                        } catch (e) {
                            html += '<p class="text-red-500">Invalid schedule format</p>';
                        }
                    });
                }

                // ---------- Subjects ----------
                else if(type === 'subjects') {
                    html += '<h3 class="text-lg font-bold mb-2">Subject Report</h3>';
                    html += '<table class="table-auto border-collapse border border-gray-300 w-full mb-6">';
                    html += '<thead><tr class="bg-gray-200"><th class="border px-2 py-1">Program</th><th class="border px-2 py-1">Subject Code</th>';
                    html += '<th class="border px-2 py-1">Subject Name</th><th class="border px-2 py-1">Lec Hours</th>';
                    html += '<th class="border px-2 py-1">Lab Hours</th><th class="border px-2 py-1">Lec Units</th>';
                    html += '<th class="border px-2 py-1">Lab Units</th><th class="border px-2 py-1">Prerequisite</th></tr></thead><tbody>';
                    res.forEach(item => {
                        html += `<tr>
                            <td class="border px-2 py-1">${item.program}</td>
                            <td class="border px-2 py-1">${item.subject_code}</td>
                            <td class="border px-2 py-1">${item.subject_name}</td>
                            <td class="border px-2 py-1">${item.lec_hours}</td>
                            <td class="border px-2 py-1">${item.lab_hours}</td>
                            <td class="border px-2 py-1">${item.lec_units}</td>
                            <td class="border px-2 py-1">${item.lab_units}</td>
                            <td class="border px-2 py-1">${item.prerequisite || ''}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                }

                // ---------- Users ----------
                else if(type === 'users') {
                    html += '<h3 class="text-lg font-bold mb-2">Users Report</h3>';
                    html += '<table class="table-auto border-collapse border border-gray-300 w-full">';
                    html += '<thead><tr class="bg-gray-200"><th class="border px-2 py-1">Username</th><th class="border px-2 py-1">Full Name</th>';
                    html += '<th class="border px-2 py-1">Email</th><th class="border px-2 py-1">Type</th><th class="border px-2 py-1">Status</th></tr></thead><tbody>';
                    res.forEach(u => {
                        html += `<tr>
                            <td class="border px-2 py-1">${u.user_username}</td>
                            <td class="border px-2 py-1">${u.user_fname} ${u.user_mname || ''} ${u.user_lname}</td>
                            <td class="border px-2 py-1">${u.user_email}</td>
                            <td class="border px-2 py-1">${u.user_type}</td>
                            <td class="border px-2 py-1">${u.user_status == 1 ? 'Active' : 'Disabled'}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                }

                $('#reportContainer').html(html);
            },
            error: function(err) {
                $('#reportContainer').html('<p class="text-red-500">Failed to load report.</p>');
                console.error(err);
            }
        });
    }

    // -------------------- Tab click --------------------
    $('.report-tab').click(function() {
        let reportType = $(this).data('report');
        renderReport(reportType);

        $('.report-tab').removeClass('bg-white text-red-900').addClass('bg-red-900 text-white');
        $(this).removeClass('bg-red-900 text-white').addClass('bg-white text-red-900');
    });

    // -------------------- Print button --------------------
    $('#printReport').click(function() {
        let printContents = document.getElementById('reportContainer').innerHTML;
        let w = window.open('', '', 'height=600,width=1000');
        w.document.write('<html><head><title>Reports</title></head><body>');
        w.document.write(printContents);
        w.document.write('</body></html>');
        w.document.close();
        w.print();
    });

    // -------------------- Default tab ----------
    renderReport('curriculum');
    $('.report-tab[data-report="curriculum"]').removeClass('bg-red-900 text-white').addClass('bg-white text-red-900');
});