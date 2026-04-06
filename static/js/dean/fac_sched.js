$(document).ready(function () {

  // ==============================
  // OPEN / CLOSE MODAL
  // ==============================
  $('#openScheduleModal').click(() => {
    $('#scheduleModal').removeClass('hidden');
  });

  $('#closeScheduleModal').click(() => {
    $('#scheduleModal').addClass('hidden');
    $('#scheduleForm')[0].reset();
    $('#entriesList').empty();
    $('#subjectSearch').val('');
    $('.subjectSelect').val('');
    $('#subjectDropdown').addClass('hidden');
    $('#roomInput').val('');
    scheduleEntries = {};
    editId = null;
    entryCounter = 0;
  });

  // ==============================
  // LOAD Instructors (active only)
  // ==============================
  function loadInstructors() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_schedule' },
      dataType: 'json',
      success: function (res) {
        if (res.status === 200) {
          let options = '<option value="">Select Instructor</option>';
          res.data.forEach(f => {
            options += `<option class="capitalize" value="${f.user_id}">${f.user_fname} ${f.user_lname} (${f.user_type})</option>`;
          });
          $('select[name="sch_user_id"]').html(options);
        }
      }
    });
  }

  // ==============================
  // SEARCHABLE SUBJECT PICKER
  // ==============================
  let allSubjects = [];

  function loadSubjects() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_all_subjects' },
      dataType: 'json',
      success: function (res) {
        if (res.status === 200) {
          // Deduplicate by subject_code
          const seen = new Set();
          allSubjects = res.data.filter(s => {
            if (seen.has(s.subject_code)) return false;
            seen.add(s.subject_code);
            return true;
          });
        }
      }
    });
  }

  // Show dropdown when typing
  function positionDropdown() {
    const input = document.getElementById('subjectSearch');
    if (!input) return;
    const rect = input.getBoundingClientRect();
    const dd = document.getElementById('subjectDropdown');
    if (!dd) return;
    dd.style.top   = (rect.bottom + window.scrollY) + 'px';
    dd.style.left  = (rect.left  + window.scrollX) + 'px';
    dd.style.width = rect.width + 'px';
  }

  $(document).on('input', '#subjectSearch', function () {
    const q = $(this).val().trim().toLowerCase();
    if (q.length === 0) {
      $('#subjectDropdown').addClass('hidden').empty();
      $('.subjectSelect').val('');
      return;
    }
    const filtered = allSubjects.filter(s =>
      s.subject_code.toLowerCase().includes(q) ||
      s.subject_name.toLowerCase().includes(q)
    ).slice(0, 60);

    if (filtered.length === 0) {
      $('#subjectDropdown').html('<div class="p-2 text-gray-400 text-xs">No subjects found.</div>');
    } else {
      let html = '';
      filtered.forEach(s => {
        html += `<div class="subject-option px-3 py-2 hover:bg-red-50 cursor-pointer border-b border-gray-100 flex flex-col"
                    data-code="${s.subject_code}" data-name="${s.subject_name}">
                    <span class="font-semibold text-gray-800">${s.subject_code}</span>
                    <span class="text-gray-500 text-xs">${s.subject_name}</span>
                 </div>`;
      });
      $('#subjectDropdown').html(html);
    }
    positionDropdown();
    $('#subjectDropdown').removeClass('hidden');
  });

  // Reposition on scroll/resize
  $(window).on('scroll resize', function () {
    if (!$('#subjectDropdown').hasClass('hidden')) positionDropdown();
  });

  // Pick a subject from dropdown
  $(document).on('click', '.subject-option', function () {
    const code = $(this).data('code');
    const name = $(this).data('name');
    $('#subjectSearch').val(`${code} — ${name}`);
    $('.subjectSelect').val(code);
    $('#subjectDropdown').addClass('hidden');
  });

  // Close dropdown on outside click
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.subject-picker').length) {
      $('#subjectDropdown').addClass('hidden');
    }
  });

  // ==============================
  // ADD / REMOVE ENTRY
  // ==============================
  let scheduleEntries = {};
  let editId = null;
  let entryCounter = 0;

  $('#addEntry').click(() => {
    const day     = $('.daySelect').val();
    const subject = $('.subjectSelect').val();
    const hours   = parseFloat($('.hoursSelect').val());
    const room    = $('#roomInput').val().trim();

    if (!day)     return alert('Please select a day.');
    if (!subject) return alert('Please select a subject.');

    scheduleEntries[day] = scheduleEntries[day] || {};
    const totalHours = Object.values(scheduleEntries[day]).reduce((s, e) => s + e.hours, 0);
    if (totalHours + hours > 13) {
      return alert('Cannot add entry. Total hours per day cannot exceed 13 hours.');
    }

    const entryId = ++entryCounter;
    scheduleEntries[day][entryId] = { subject, hours, room };

    const label = $('#subjectSearch').val() || subject;
    $('#entriesList').append(`
      <li class="border border-gray-200 p-2 rounded mb-1 flex justify-between items-center bg-gray-50"
          data-day="${day}" data-id="${entryId}">
        <span>${day} ➜ <strong>${subject}</strong> (${hours % 1 === 0 ? hours + ' hr' : (hours*60) + ' mins'})${room ? ` — Room: ${room}` : ''}</span>
        <button type="button" class="removeEntry cursor-pointer text-red-600 font-bold px-1 rounded hover:bg-red-100 transition">×</button>
      </li>
    `);

    // Reset subject picker
    $('#subjectSearch').val('');
    $('.subjectSelect').val('');
    $('#roomInput').val('');
  });

  $(document).on('click', '.removeEntry', function () {
    const li  = $(this).closest('li');
    const day = li.data('day');
    const id  = li.data('id');
    if (scheduleEntries[day]?.[id]) delete scheduleEntries[day][id];
    li.remove();
  });

  // ==============================
  // LOAD SCHEDULES TABLE
  // ==============================
  function loadSchedules() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_schedules' },
      dataType: 'json',
      success: function (res) {
        if (res.status === 200) {
          if (res.data.length === 0) {
            $('#scheduleTable').html(`<div class="text-center text-gray-500 p-4">No records found.</div>`);
            return;
          }
          let html = `<table class="min-w-full border border-gray-300 bg-white shadow-md rounded-lg">
                        <thead class="bg-red-900 text-white">
                          <tr>
                            <th class="p-2 text-left">Program</th>
                            <th class="p-2 text-left">Semester</th>
                            <th class="p-2 text-left">Instructor</th>
                            <th class="p-2 text-left">Type</th>
                            <th class="p-2 text-left">Actions</th>
                          </tr>
                        </thead><tbody>`;
          res.data.forEach(sch => {
            html += `<tr class="schedule-row border-b hover:bg-gray-50">
              <td class="p-2">${sch.sch_schedule.program || ''}</td>
              <td class="p-2">${sch.sch_schedule.semester || ''}</td>
              <td class="p-2">${sch.faculty_name || ''}</td>
              <td class="p-2 capitalize">${sch.user_type || ''}</td>
              <td class="p-2 flex gap-2">
                <a href="view_fac_sched.php?sch_id=${sch.sch_id}"
                  class="bg-gray-500 hover:bg-gray-400 text-white px-3 py-1 rounded">View</a>
                <button class="editSchedule cursor-pointer bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded"
                  data-id="${sch.sch_id}">Edit</button>
                <button class="deleteSchedule cursor-pointer bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded"
                  data-id="${sch.sch_id}">Delete</button>
              </td>
            </tr>`;
          });
          html += `</tbody></table>`;
          $('#scheduleTable').html(html);
        }
      }
    });
  }

  // ==============================
  // SEARCH FILTER
  // ==============================
  $(document).on('keyup', '#scheduleSearch', function () {
    const value = $(this).val().toLowerCase();
    let visible = 0;
    $('.schedule-row').each(function () {
      const match = $(this).text().toLowerCase().includes(value);
      $(this).toggle(match);
      if (match) visible++;
    });
    visible === 0 ? $('#noSearchResult').removeClass('hidden') : $('#noSearchResult').addClass('hidden');
  });

  // ==============================
  // EDIT SCHEDULE
  // ==============================
  $(document).on('click', '.editSchedule', function () {
    editId = $(this).data('id');
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_schedules' },
      dataType: 'json',
      success: function (res) {
        const sch = res.data.find(s => s.sch_id == editId);
        if (!sch) return;

        $('#scheduleModal').removeClass('hidden');
        $('select[name="sch_user_id"]').val(sch.sch_user_id);
        $('#program').val(sch.sch_schedule.program);
        $('#semester').val(sch.sch_schedule.semester);

        scheduleEntries = {};
        $('#entriesList').empty();
        entryCounter = 0;

        function fmt(t) {
          let [h, m] = t.split(':').map(Number);
          const ap = h >= 12 ? 'PM' : 'AM';
          h = h % 12 || 12;
          return `${h}:${String(m).padStart(2,'0')} ${ap}`;
        }

        const dbSchedule = sch.sch_schedule.schedule || {};
        for (const day in dbSchedule) {
          dbSchedule[day].forEach(entry => {
            const entryId = ++entryCounter;
            scheduleEntries[day] = scheduleEntries[day] || {};
            scheduleEntries[day][entryId] = { subject: entry.subject, hours: entry.hours || 1, room: entry.room || '' };
            const timeStr = entry.time ? `${fmt(entry.time.from)} - ${fmt(entry.time.to)}` : '';
            const roomStr = entry.room ? ` — Room: ${entry.room}` : '';
            $('#entriesList').append(`
              <li class="border border-gray-200 p-2 rounded mb-1 flex justify-between items-center bg-gray-50"
                  data-day="${day}" data-id="${entryId}">
                <span>${day} ${timeStr} ➜ <strong>${entry.subject}</strong> (${entry.hours} hr)${roomStr}</span>
                <button type="button" class="removeEntry cursor-pointer text-red-600 font-bold px-1 rounded hover:bg-red-100 transition">×</button>
              </li>
            `);
          });
        }
      }
    });
  });

  // ==============================
  // DELETE SCHEDULE
  // ==============================
  $(document).on('click', '.deleteSchedule', function () {
    const sch_id = $(this).data('id');
    if (!confirm('Are you sure you want to delete this schedule?')) return;
    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: { requestType: 'delete_schedule', sch_id },
      dataType: 'json',
      success: function (res) { alert(res.message); loadSchedules(); }
    });
  });

  // ==============================
  // SAVE SCHEDULE (CREATE / UPDATE)
  // ==============================
  $('#scheduleForm').submit(function (e) {
    e.preventDefault();

    if (!editId) {
      const hasEntries = Object.keys(scheduleEntries).some(day =>
        Object.keys(scheduleEntries[day] || {}).length > 0
      );
      if (!hasEntries) return alert('Please add at least one schedule entry.');
    }

    const scheduleForDB = {};
    for (const day in scheduleEntries) {
      scheduleForDB[day] = {};
      for (const id in scheduleEntries[day]) {
        const e = scheduleEntries[day][id];
        scheduleForDB[day][id] = { subject: e.subject, hours: e.hours, room: e.room || '' };
      }
    }

    const payload = {
      requestType:  editId ? 'update_schedule' : 'create_schedule',
      sch_id:       editId,
      sch_user_id:  $('select[name="sch_user_id"]').val(),
      sch_schedule: JSON.stringify({
        program:  $('#program').val(),
        semester: $('#semester').val(),
        schedule: scheduleForDB
      })
    };

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: payload,
      dataType: 'json',
      success: function (res) {
        if (res.status === 'error') return alert('Error: ' + res.message);
        $('#scheduleModal').addClass('hidden');
        $('#scheduleForm')[0].reset();
        $('#entriesList').empty();
        $('#subjectSearch').val('');
        $('.subjectSelect').val('');
        $('#roomInput').val('');
        scheduleEntries = {};
        editId = null;
        entryCounter = 0;
        alert(res.message);
        loadSchedules();
      }
    });
  });

  // ==============================
  // INITIAL LOAD
  // ==============================
  loadInstructors();
  loadSubjects();
  loadSchedules();
});