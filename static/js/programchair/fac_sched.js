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
            $('#scheduleTable').html(`
              <div class="pc-empty">
                <span class="material-icons">event_busy</span>
                <h3>No schedules yet</h3>
                <p>Create or auto-generate a schedule to get started.</p>
              </div>
            `);
            return;
          }

          const typeChipMap = {
            dean: 'pc-chip-red',
            'program chair': 'pc-chip-amber',
            programchair: 'pc-chip-amber',
            faculty: 'pc-chip-blue',
            gec: 'pc-chip-green'
          };

          let rows = '';
          res.data.forEach(sch => {
            const program  = sch.sch_schedule.program || '';
            const semester = sch.sch_schedule.semester || '';
            const userType = (sch.user_type || '').toLowerCase();
            const chipCls  = typeChipMap[userType] || 'pc-chip-gray';
            const typeLabel = (sch.user_type || '').replace(/\b\w/g, c => c.toUpperCase()) || '—';

            rows += `
              <tr class="schedule-row">
                <td>${program ? `<span class="pc-chip pc-chip-red">${program}</span>` : '<span class="pc-text-muted">—</span>'}</td>
                <td>${semester || '<span class="pc-text-muted">—</span>'}</td>
                <td>${sch.faculty_name || '<span class="pc-text-muted">—</span>'}</td>
                <td><span class="pc-chip ${chipCls}">${typeLabel}</span></td>
                <td>
                  <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                    <a href="view_fac_sched.php?sch_id=${sch.sch_id}" class="pc-btn pc-btn-sm pc-btn-neutral">
                      <span class="material-icons">visibility</span> View
                    </a>
                    <button class="editSchedule pc-btn pc-btn-sm pc-btn-ghost" data-id="${sch.sch_id}">
                      <span class="material-icons">edit</span> Edit
                    </button>
                    <button class="deleteSchedule pc-btn pc-btn-sm pc-btn-danger" data-id="${sch.sch_id}">
                      <span class="material-icons">delete</span> Delete
                    </button>
                  </div>
                </td>
              </tr>
            `;
          });

          $('#scheduleTable').html(`
            <table class="pc-table" style="border-radius: 0; box-shadow: none;">
              <thead>
                <tr>
                  <th>Program</th>
                  <th>Semester</th>
                  <th>Instructor</th>
                  <th>Role</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>${rows}</tbody>
            </table>
          `);
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
        $('#year_level').val(sch.sch_schedule.year_level || '');
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
        program:    $('#program').val(),
        year_level: $('#year_level').val(),
        semester:   $('#semester').val(),
        schedule:   scheduleForDB
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
  // ROOM PICKER (chip-style for Available Rooms)
  // Quick-add buttons + initial chip list are loaded from /rooms endpoint.
  // ==============================
  const roomPicker = (function () {
    const $hidden  = $('#autoRooms');
    const $chips   = $('#autoRoomsChips');
    const $input   = $('#autoRoomsInput');
    const $suggest = $('#autoRoomsSuggest');
    let DEFAULT    = $hidden.val() || '';

    function getRooms() {
      return $chips.find('.pc-room-chip').map((_, el) => $(el).data('room').toString()).get();
    }

    function sync() {
      const arr = getRooms();
      $hidden.val(arr.join(','));
      $suggest.find('.pc-quick-room').each(function () {
        const r = $(this).data('room').toString();
        $(this).toggleClass('is-added', arr.includes(r));
      });
    }

    function addRoom(raw) {
      const room = String(raw || '').trim().toUpperCase();
      if (!room) return;
      if (!/^[A-Z0-9-]+$/.test(room)) return;
      if (getRooms().includes(room)) return;
      $chips.append(
        '<span class="pc-room-chip" data-room="' + room + '">' +
          '<span class="material-icons">meeting_room</span>' + room +
          '<button type="button" aria-label="Remove ' + room + '">' +
            '<span class="material-icons">close</span>' +
          '</button>' +
        '</span>'
      );
      sync();
    }

    function reset(value) {
      $chips.empty();
      $input.val('');
      const v = value !== undefined ? value : DEFAULT;
      v.split(',').map(s => s.trim()).filter(Boolean).forEach(addRoom);
    }

    function loadFromDb() {
      $.ajax({
        url: '../controller/end-points/get_controller.php',
        method: 'GET',
        data: { requestType: 'get_rooms', active: 1 },
        dataType: 'json',
        success: function (res) {
          const rooms = (res && res.data) ? res.data.map(r => r.room_name) : [];
          const $emp = $('#autoRoomsSuggestEmpty');
          if ($emp.length) $emp.remove();
          $suggest.find('.pc-quick-room').remove();
          if (rooms.length === 0) {
            $suggest.append('<span class="text-xs text-gray-400">No rooms yet — add some in Rooms.</span>');
            DEFAULT = '';
            reset('');
            return;
          }
          rooms.forEach(r => {
            $suggest.append('<button type="button" class="pc-quick-room" data-room="' + r + '">' + r + '</button>');
          });
          DEFAULT = rooms.join(',');
          reset(DEFAULT);
        }
      });
    }

    loadFromDb();

    $input.on('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ',' || e.key === ' ') {
        e.preventDefault();
        const v = $(this).val();
        if (v) { addRoom(v); $(this).val(''); }
      } else if (e.key === 'Backspace' && !$(this).val()) {
        $chips.find('.pc-room-chip').last().remove();
        sync();
      }
    });
    $input.on('blur', function () {
      const v = $(this).val();
      if (v) { addRoom(v); $(this).val(''); }
    });

    $chips.on('click', '.pc-room-chip button', function () {
      $(this).closest('.pc-room-chip').remove();
      sync();
    });

    $suggest.on('click', '.pc-quick-room', function () {
      if ($(this).hasClass('is-added')) return;
      addRoom($(this).data('room'));
    });

    return { reset, addRoom, loadFromDb };
  })();

  // ==============================
  // CURRICULUM YEAR LOADER (per program; falls back to all years)
  // ==============================
  function loadCurriculumYears(program) {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_curriculum_years', program: program || '' },
      dataType: 'json',
      success: function (res) {
        let html = '<option value="">Any (all curricula)</option>';
        (res.data || []).forEach(y => {
          html += '<option value="' + y + '">' + y + '</option>';
        });
        $('#autoCurriculumYear').html(html);
      }
    });
  }
  $(document).on('change', '#autoProgram', function () {
    loadCurriculumYears($(this).val());
  });

  // ==============================
  // AUTO-GENERATE SCHEDULE
  // ==============================
  $('#openAutoGenModal').click(() => {
    $('#autoGenResult').addClass('hidden').empty();
    $('#autoGenModal').removeClass('hidden');
    loadCurriculumYears($('#autoProgram').val());
  });

  $('#closeAutoGenModal').click(() => {
    $('#autoGenModal').addClass('hidden');
    $('#autoGenForm')[0].reset();
    $('#autoGenResult').addClass('hidden').empty();
    $('input[name=tier][value=major]').prop('checked', true);
    roomPicker.reset();
  });

  $('#autoGenForm').submit(function (e) {
    e.preventDefault();

    const program         = $('#autoProgram').val();
    const year_level      = $('#autoYearLevel').val();
    const semester        = $('#autoSemester').val();
    const rooms           = $('#autoRooms').val();
    const tier            = $('input[name=tier]:checked').val() || 'major';
    const curriculum_year = $('#autoCurriculumYear').val() || '';
    const merge_across_programs = $('#autoMergePrograms').is(':checked') ? 1 : 0;

    if (!program || !year_level || !semester) {
      return alert('Please choose program, year level, and semester.');
    }
    if (!rooms) {
      return alert('Please add at least one room (or add some in Rooms).');
    }

    const tierLabel = { gen_ed:'Gen Ed', gen_eng:'General Engineering', major:'Major / Professional' }[tier] || tier;
    const mergeNote = merge_across_programs ? ' (with cross-program merging)' : '';
    if (!confirm('Auto-generate ' + tierLabel + ' for ' + program + ' Year ' + year_level + ' (' + semester + ')' + mergeNote + '?')) return;

    const $btn = $(this).find('button[type=submit]').prop('disabled', true).text('Generating...');

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: {
        requestType: 'auto_generate_schedule',
        program, year_level, semester, rooms, tier, curriculum_year, merge_across_programs
      },
      dataType: 'json',
      success: function (res) {
        $btn.prop('disabled', false).text('Generate');

        if (res.success === false || res.status === 'error') {
          $('#autoGenResult')
            .removeClass('hidden')
            .html(`<div class="text-red-700 font-semibold">${res.message || 'Generation failed.'}</div>`);
          return;
        }

        let html = `<div class="text-emerald-700 font-semibold mb-2">${res.message || 'Done.'}</div>`;
        if (res.saved && res.saved.length) {
          html += `<div class="text-xs text-gray-600 mb-2">Saved schedule rows: ${res.saved.length}</div>`;
        }
        if (res.unassigned && res.unassigned.length) {
          html += `<div class="text-xs font-semibold text-amber-700 mt-2">Could not auto-assign:</div>
                   <ul class="list-disc pl-5 text-xs text-gray-700">`;
          res.unassigned.forEach(u => {
            html += `<li><strong>${u.subject_code}</strong> — ${u.subject_name} (${u.hours}h): ${u.reason}</li>`;
          });
          html += '</ul>';
        }
        if (res.merged && res.merged.length) {
          html += `<div class="text-xs font-semibold text-emerald-700 mt-2">Merged across programs:</div>
                   <ul class="list-disc pl-5 text-xs text-gray-700">`;
          res.merged.forEach(m => {
            html += `<li><strong>${m.subject_code}</strong> shared by: ${m.cohorts.join(', ')}</li>`;
          });
          html += '</ul>';
        }
        $('#autoGenResult').removeClass('hidden').html(html);

        loadSchedules();
      },
      error: function () {
        $btn.prop('disabled', false).text('Generate');
        $('#autoGenResult').removeClass('hidden')
          .html('<div class="text-red-700 font-semibold">Network/server error during generation.</div>');
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