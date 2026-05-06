$(document).ready(function() {

    // Helper: convert 24h to 12h format
    function formatTime12Hr(timeStr) {
        let [hours, minutes] = timeStr.split(':').map(Number);
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes.toString().padStart(2,'0')} ${ampm}`;
    }

    function escapeHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c =>
            ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])
        );
    }

    function programClass(program) {
        const p = (program || '').trim().toUpperCase().replace(/\s+/g, '');
        if (p === 'BSCE')  return 'prog-bsce';
        if (p === 'BSCOE') return 'prog-bscoe';
        if (p === 'BSEE')  return 'prog-bsee';
        if (p === 'BSECE') return 'prog-bsece';
        if (p === 'BSIE')  return 'prog-bsie';
        if (p === 'BSME')  return 'prog-bsme';
        return 'prog-other';
    }

    // Build a calendar-style grid HTML using the shared `pc-room-grid` + `pc-slot`
    // styles so it visually matches View Schedule.
    function buildScheduleGrid(entries, opts) {
        opts = opts || {};
        const days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        const startHour = 7, endHour = 21;
        const showProgramChip = !!opts.showProgramChip;
        const showRoomChip    = !!opts.showRoomChip;

        const map = {};
        days.forEach(d => map[d] = {});
        entries.forEach(e => {
            if (!e.time || !e.time.from || !e.time.to) return;
            const from = e.time.from.split(':').map(Number);
            const to   = e.time.to.split(':').map(Number);
            const startMin = from[0]*60 + from[1];
            const endMin   = to[0]*60 + to[1];
            const slots = (endMin - startMin) / 30;
            if (slots <= 0) return;
            map[e.day][startMin] = Object.assign({}, e, { rowspan: slots });
        });

        let html = '<div class="overflow-x-auto -mx-1 px-1"><table class="pc-room-grid">';
        html += '<thead><tr><th>Time</th>';
        days.forEach(d => { html += `<th>${d}</th>`; });
        html += '</tr></thead><tbody>';

        for (let h = startHour; h < endHour; h++) {
            for (let m = 0; m < 60; m += 30) {
                const slotMin = h*60 + m;
                const isLunch = slotMin >= 12*60 && slotMin < 13*60;
                const isHourMark = m === 0;
                const trCls = [
                    isLunch ? 'is-lunch' : '',
                    isHourMark ? 'hour-mark' : ''
                ].filter(Boolean).join(' ');

                const t1 = formatTime12Hr(`${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`);
                let endH = h, endM = m + 30;
                if (endM === 60) { endM = 0; endH++; }
                const t2 = formatTime12Hr(`${String(endH).padStart(2,'0')}:${String(endM).padStart(2,'0')}`);

                html += `<tr${trCls ? ` class="${trCls}"` : ''}>`;
                html += `<td class="time-col">${t1}<span class="block" style="font-size:.6rem;opacity:.7;font-weight:600;">${t2}</span></td>`;

                days.forEach(d => {
                    const cell = map[d][slotMin];
                    if (cell) {
                        const cls = programClass(cell.program);
                        const isMerged = Array.isArray(cell.cohorts) && cell.cohorts.length > 1;
                        const fromTo = `${formatTime12Hr(cell.time.from)} – ${formatTime12Hr(cell.time.to)}`;
                        const subjectName = (cell.subject_details && cell.subject_details.subject_name) || '';
                        const showSubName = subjectName && subjectName !== cell.subject;
                        const programChip = showProgramChip && cell.program
                            ? `<span class="pc-slot-prog">${escapeHtml(cell.program)}${cell.year_level ? ' Y' + escapeHtml(cell.year_level) : ''}</span>`
                            : '';
                        const roomLine = showRoomChip && cell.room
                            ? ` &middot; Room ${escapeHtml(cell.room)}`
                            : (cell.room ? ` &middot; Room ${escapeHtml(cell.room)}` : '');
                        const mergedTag = isMerged
                            ? `<span class="pc-slot-prog" style="background:#d1fae5;color:#065f46;">Merged: ${cell.cohorts.map(escapeHtml).join('/')}</span>`
                            : '';
                        html += `<td class="has-slot" rowspan="${cell.rowspan}">
                            <div class="pc-slot ${cls}">
                                <div class="pc-slot-subject">${escapeHtml(cell.subject)}</div>
                                ${showSubName ? `<div class="pc-slot-faculty" style="color:#374151;font-weight:600;">${escapeHtml(subjectName)}</div>` : ''}
                                ${cell.faculty ? `<div class="pc-slot-faculty">${escapeHtml(cell.faculty)}</div>` : ''}
                                <div class="pc-slot-time">${fromTo}${roomLine}</div>
                                ${programChip}
                                ${mergedTag}
                            </div>
                        </td>`;
                    } else {
                        let skip = false;
                        for (let prev = slotMin - 30; prev >= startHour*60; prev -= 30) {
                            const p = map[d][prev];
                            if (p && p.rowspan > (slotMin - prev)/30) { skip = true; break; }
                        }
                        if (!skip) html += `<td class="is-empty"></td>`;
                    }
                });
                html += '</tr>';
            }
        }
        html += '</tbody></table></div>';
        return html;
    }

    // Format year level as "1st Year", "2nd Year", etc. Returns '' when missing.
    function formatYearLevel(y) {
        const n = parseInt(y, 10);
        if (!Number.isFinite(n) || n <= 0) return '';
        const mod100 = n % 100;
        const mod10  = n % 10;
        let suffix = 'th';
        if (mod100 < 11 || mod100 > 13) {
            if (mod10 === 1) suffix = 'st';
            else if (mod10 === 2) suffix = 'nd';
            else if (mod10 === 3) suffix = 'rd';
        }
        return `${n}${suffix} Year`;
    }

    // Flatten /fetchAllSchedule into a list of entries.
    function flattenAllSchedules(data) {
        const out = [];
        (data || []).forEach(item => {
            const sch     = item.sch_schedule || {};
            const program = sch.program || '';
            const schYr   = sch.year_level || '';
            const sem     = sch.semester || '';
            const faculty = item.faculty_name || '';
            const days    = sch.schedule || {};
            Object.keys(days).forEach(day => {
                (days[day] || []).forEach(e => {
                    if (!e || !e.time) return;
                    // Fall back to the subject's curriculum year_level when the
                    // schedule-level value is missing (legacy rows can have it blank).
                    const yr = schYr || (e.subject_details && e.subject_details.year_level) || '';
                    out.push({
                        day, time: e.time,
                        subject: e.subject,
                        subject_details: e.subject_details,
                        faculty, room: e.room || '',
                        program, year_level: yr, semester: sem,
                        cohorts: e.cohorts || [],
                        sch_id: item.sch_id,
                        user_id: item.sch_user_id
                    });
                });
            });
        });
        return out;
    }

    // Same hex accents as design_system.php's .pc-slot.prog-XXX rules.
    const PROG_ACCENT = {
        'prog-bsce':  '#b91c1c',
        'prog-bscoe': '#1d4ed8',
        'prog-bsee':  '#b45309',
        'prog-bsece': '#6d28d9',
        'prog-bsie':  '#047857',
        'prog-bsme':  '#4338ca',
        'prog-other': '#374151',
    };

    function programChipsLegend(programs) {
        if (!programs || programs.length === 0) return '';
        let h = '<div class="pc-prog-legend mb-3"><span style="font-size:.65rem;font-weight:800;letter-spacing:.1em;color:#6b7280;align-self:center;">PROGRAMS:</span>';
        programs.forEach(p => {
            const cls   = programClass(p);
            const color = PROG_ACCENT[cls] || '#374151';
            h += `<span class="pc-prog-legend-item"><span class="dot" style="background:${color};"></span>${escapeHtml(p)}</span>`;
        });
        h += '</div>';
        return h;
    }

    // Read the active year-level filter (empty string = no filter)
    function getYearFilter() {
        return ($('#yearFilter').val() || '').trim();
    }

    // Group an array of entries by a key function. Returns Map (preserves insertion order).
    function groupBy(arr, keyFn) {
        const m = new Map();
        arr.forEach(item => {
            const k = keyFn(item);
            if (!m.has(k)) m.set(k, []);
            m.get(k).push(item);
        });
        return m;
    }

    // Renders one of: schedule_plot | faculty_plot | room_plot
    function renderPlotReport(kind) {
        $('#reportContainer').html('<p class="text-gray-500">Loading…</p>');
        $.ajax({
            url: "../controller/end-points/get_controller.php",
            type: "GET",
            data: { requestType: 'fetchAllSchedule' },
            dataType: "json",
            success: function (res) {
                let all = flattenAllSchedules(res.data || []);
                const yrFilter = getYearFilter();
                if (yrFilter) {
                    all = all.filter(e => String(e.year_level) === yrFilter);
                }
                if (all.length === 0) {
                    const note = yrFilter
                        ? `No schedules match the selected year (${formatYearLevel(yrFilter)}).`
                        : 'No schedules to display.';
                    $('#reportContainer').html(`<p class="text-gray-500">${note}</p>`);
                    return;
                }

                let header = '';
                let groups;
                let cardLabelFn;
                let gridOpts;

                let titleParts;
                if (kind === 'schedule_plot') {
                    header = '<h3 class="text-lg font-bold mb-3 text-red-900 flex items-center gap-2"><span class="material-icons">grid_view</span> Schedule Plot — by Program / Year / Semester</h3>';
                    all.sort((a,b) => (a.program+a.year_level+a.semester).localeCompare(b.program+b.year_level+b.semester));
                    groups = groupBy(all, e => `${e.program}|${e.year_level}|${e.semester}`);
                    titleParts = key => {
                        const [p, y, s] = key.split('|');
                        const yrLabel = formatYearLevel(y);
                        return { main: p, sub: s ? `${s} Semester` : '', extra: yrLabel };
                    };
                    gridOpts = { showProgramChip: false, showRoomChip: true };
                } else if (kind === 'faculty_plot') {
                    header = '<h3 class="text-lg font-bold mb-3 text-red-900 flex items-center gap-2"><span class="material-icons">co_present</span> Faculty Plot — by Instructor</h3>';
                    all.sort((a,b) => (a.faculty || '').localeCompare(b.faculty || ''));
                    groups = groupBy(all, e => e.faculty || '(unassigned)');
                    titleParts = key => ({ main: 'Faculty', sub: '', extra: key });
                    gridOpts = { showProgramChip: true, showRoomChip: true };
                } else { // room_plot
                    header = '<h3 class="text-lg font-bold mb-3 text-red-900 flex items-center gap-2"><span class="material-icons">meeting_room</span> Room Plot — by Room</h3>';
                    const roomEntries = all.filter(e => e.room && e.room.trim() !== '');
                    roomEntries.sort((a,b) => (a.room || '').localeCompare(b.room || ''));
                    groups = groupBy(roomEntries, e => e.room);
                    titleParts = key => ({ main: 'Room', sub: '', extra: key });
                    gridOpts = { showProgramChip: true, showRoomChip: false };
                }

                let html = header;
                if (groups.size === 0) {
                    html += '<p class="text-gray-500">Nothing to display for this view.</p>';
                    $('#reportContainer').html(html);
                    return;
                }

                groups.forEach((entries, key) => {
                    const programs = Array.from(new Set(entries.map(e => e.program).filter(Boolean))).sort();
                    const t = titleParts(key);
                    html += `<div class="pc-card mb-6 overflow-hidden" style="page-break-after:always;">`;
                    // Centered title block (matches View Schedule design)
                    html += `<div class="text-center border-b border-gray-100 p-4">
                        <h1 class="text-lg font-bold uppercase text-red-900">${escapeHtml(t.main)}</h1>
                        ${t.sub ? `<p class="text-sm font-semibold text-gray-600">${escapeHtml(t.sub)}</p>` : ''}
                        ${t.extra ? `<div class="bg-amber-100 text-amber-800 font-semibold py-1 px-2 rounded mt-2 inline-block capitalize">${escapeHtml(t.extra)}</div>` : ''}
                    </div>`;
                    html += `<div class="p-3 sm:p-4">`;
                    if (programs.length > 0) html += programChipsLegend(programs);
                    html += buildScheduleGrid(entries, gridOpts);
                    html += '</div></div>';
                });

                $('#reportContainer').html(html);
            },
            error: function () {
                $('#reportContainer').html('<p class="text-red-500">Failed to load report.</p>');
            }
        });
    }

    // -------------------- Render report by tab --------------------
    function renderReport(type) {
        if (type === 'schedule_plot' || type === 'faculty_plot' || type === 'room_plot') {
            renderPlotReport(type);
            return;
        }

        $('#reportContainer').html('<p class="text-gray-500">Loading...</p>');

        $.ajax({
            url: "../controller/end-points/get_controller.php",
            type: "GET",
            data: { requestType: `fetch${type.charAt(0).toUpperCase() + type.slice(1)}` },
            dataType: "json",
            success: function(res) {
                let html = '';
                const yrFilter = getYearFilter();

                // ---------- Curriculum ----------
                if(type === 'curriculum') {
                    const rows = yrFilter
                        ? res.filter(item => String(item.year_level) === yrFilter)
                        : res;
                    const titleSuffix = yrFilter ? ` — ${formatYearLevel(yrFilter)}` : '';
                    html += `<h3 class="text-lg font-bold mb-2">Curriculum Report${titleSuffix}</h3>`;
                    if (rows.length === 0) {
                        html += '<p class="text-gray-500">No curriculum entries match the selected year.</p>';
                    } else {
                        html += '<table class="table-auto border-collapse border border-gray-300 w-full mb-6">';
                        html += '<thead><tr class="bg-gray-200">';
                        html += '<th class="border px-2 py-1">Program</th><th class="border px-2 py-1">Year Level</th>';
                        html += '<th class="border px-2 py-1">Semester</th><th class="border px-2 py-1">Subject Code</th>';
                        html += '<th class="border px-2 py-1">Subject Name</th><th class="border px-2 py-1">Lec Hours</th>';
                        html += '<th class="border px-2 py-1">Lab Hours</th><th class="border px-2 py-1">Lec Units</th>';
                        html += '<th class="border px-2 py-1">Lab Units</th><th class="border px-2 py-1">Prerequisite</th></tr></thead><tbody>';
                        rows.forEach(item => {
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
                }

                // ---------- Schedule ----------
                else if(type === 'schedule') {
                    html += '<h3 class="text-lg font-bold mb-2">Schedule Report</h3>';
                    res.forEach(s => {
                        html += `<h4 class="font-semibold mt-2 capitalize">${s.user_fname} ${s.user_lname}</h4>`;
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

        $('.report-tab').removeClass('is-active');
        $(this).addClass('is-active');
    });

    // -------------------- Year-level filter --------------------
    $('#yearFilter').on('change', function () {
        const activeTab = $('.report-tab.is-active').data('report');
        if (activeTab) renderReport(activeTab);
    });

    // -------------------- Print button --------------------
    // Collect all stylesheet links and inline styles from the parent page so
    // the print window renders with the same look as the on-screen report.
    function collectParentStyles() {
        const out = [];
        document.querySelectorAll('link[rel="stylesheet"], style').forEach(el => {
            out.push(el.outerHTML);
        });
        // Carry over Material Icons + Google Fonts links if present
        document.querySelectorAll('link[href*="fonts.googleapis.com"], link[href*="material+icons" i]').forEach(el => {
            out.push(el.outerHTML);
        });
        return out.join('\n');
    }

    $('#printReport').click(function () {
        const printContents = document.getElementById('reportContainer').innerHTML;
        if (!printContents || !printContents.trim()) {
            alertify.warning('Walang laman ang report. Pumili muna ng tab.');
            return;
        }

        const activeTab     = $('.report-tab.is-active').text().trim() || 'Report';
        const isPlot        = $('#reportContainer .pc-room-grid').length > 0;
        const pageOrient    = isPlot ? 'landscape' : 'portrait';
        const styles        = collectParentStyles();
        const baseHref      = document.baseURI;
        const printedOn     = new Date().toLocaleString();
        const username      = ($('.pc-topbar-welcome .name').text() || '').trim();

        const w = window.open('', '_blank', 'height=900,width=1200');
        if (!w) {
            alertify.error('Hindi makapag-print. Paki-allow ang pop-ups para sa site na ito.');
            return;
        }

        w.document.open();
        w.document.write(`<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <base href="${baseHref}">
    <title>${activeTab} — Print</title>
    ${styles}
    <style>
        @page { size: A4 ${pageOrient}; margin: 12mm; }
        html, body { background: #fff !important; }
        body { font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif; color: #111827; margin: 0; }

        /* Print header */
        .print-header {
            display: flex; align-items: center; gap: .85rem;
            padding-bottom: .5rem; margin-bottom: .85rem;
            border-bottom: 2px solid #7f1d1d;
        }
        .print-header img { height: 52px; width: 52px; object-fit: contain; }
        .print-header h1 { font-size: 1.05rem; font-weight: 800; color: #7f1d1d; margin: 0; letter-spacing: .02em; }
        .print-header p  { font-size: .72rem; color: #4b5563; margin: 0; }
        .print-header .meta { margin-left: auto; text-align: right; font-size: .68rem; color: #6b7280; }

        /* Tame on-screen card chrome for paper */
        .pc-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; border-radius: 6px !important; }
        .pc-card.mb-6 { margin-bottom: 1rem !important; }

        /* Tables: keep rows together, repeat headers across pages */
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        tr, td, th { page-break-inside: avoid; }
        h1, h2, h3, h4 { page-break-after: avoid; }

        /* Schedule grid tightening for paper */
        .pc-room-grid { font-size: .68rem; }
        .pc-room-grid thead th { padding: .35rem .3rem; font-size: .6rem; }
        .pc-room-grid tbody td { padding: .2rem .3rem; }
        .pc-room-grid tbody td.time-col { font-size: .58rem; width: 4.5rem; }
        .pc-slot { padding: .3rem .4rem .3rem .55rem; }
        .pc-slot .pc-slot-subject { font-size: .68rem; line-height: 1.15; }
        .pc-slot .pc-slot-faculty { font-size: .58rem; }
        .pc-slot .pc-slot-time    { font-size: .52rem; }
        .pc-slot .pc-slot-prog    { font-size: .5rem; padding: .04rem .3rem; }

        /* Each plot block on its own page */
        .pc-card[style*="page-break-after"] { page-break-after: always; }
        .pc-card[style*="page-break-after"]:last-child { page-break-after: auto; }

        /* Force background colors / borders to print (Chromium/WebKit) */
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <img src="../static/logo.jpg" alt="Logo" onerror="this.style.display='none'">
        <div>
            <h1>Course Scheduling System</h1>
            <p>${activeTab} Report</p>
        </div>
        <div class="meta">
            ${username ? 'Generated by: <strong>' + username + '</strong><br>' : ''}
            Printed: ${printedOn}
        </div>
    </div>
    <div class="report-body">${printContents}</div>
</body>
</html>`);
        w.document.close();

        // Wait for the new window's resources (CSS, fonts) to finish loading
        // before triggering the native print dialog. Without this, the dialog
        // can fire against an unstyled DOM.
        const triggerPrint = () => {
            try { w.focus(); w.print(); } catch (e) { /* ignore */ }
        };
        if (w.document.readyState === 'complete') {
            setTimeout(triggerPrint, 400);
        } else {
            w.addEventListener('load', () => setTimeout(triggerPrint, 400));
        }
    });


    // -------------------- Default tab ----------
    renderReport('curriculum');
    $('.report-tab[data-report="curriculum"]').addClass('is-active');
});