$(document).ready(function () {

  // ==============================
  // OPEN / CLOSE MODAL
  // ==============================
  $('#openScheduleModal').click(() => {
    $('#scheduleModal').removeClass('hidden');
    $('.subjectSelect').prop('required', true); // Only subject is required
  });

  $('#closeScheduleModal').click(() => {
    $('#scheduleModal').addClass('hidden');
    $('#scheduleForm')[0].reset();
    $('#entriesList').empty();
    scheduleEntries = {};
    editId = null;
    entryCounter = 0;
  });

  // ==============================
  // LOAD Instructors
  // ==============================
  function loadInstructors() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_schedule_gec_details' },
      dataType: 'json',
      success: function (res) {
        if(res.status === 200) {
          let options = '<option value="">Select Instructor</option>';
          res.data.forEach(f => options += `<option class='capitalize' value="${f.user_id}">${f.user_fname} ${f.user_lname} (${f.user_type})</option>`);
          $('select[name="sch_user_id"]').html(options);
        }
      }
    });
  }

  // ==============================
  // LOAD SUBJECTS
  // ==============================
  function loadSubjects() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_all_subjects' },
      dataType: 'json',
      success: function (res) {
        if(res.status === 200) {
          let options = '';
          res.data.forEach(s => options += `<option value="${s.subject_code}">${s.subject_code} - ${s.subject_name}</option>`);
          $('.subjectSelect').html(options);
        }
      }
    });
  }

  // ==============================
  // ADD / REMOVE ENTRY
  // ==============================
  let scheduleEntries = {};
  let editId = null;
  let entryCounter = 0;

  $('#addEntry').click(() => {
  let day = $('.daySelect').val();
  let subject = $('.subjectSelect').val();
  let hours = parseFloat($('.hoursSelect').val());

  if (!day || !subject) return alert('Please select a day and subject');

  scheduleEntries[day] = scheduleEntries[day] || {};

  // Calculate total hours for this day
  let totalHours = Object.values(scheduleEntries[day]).reduce((sum, entry) => sum + entry.hours, 0);
 if (totalHours + hours > 13) {
  return alert('Cannot add entry. Total hours per day cannot exceed 13 hours.');
}

  let entryId = ++entryCounter;
  scheduleEntries[day][entryId] = { subject, hours };

  $('#entriesList').append(`
    <li class="border border-gray-200 p-2 rounded mb-1 flex justify-between items-center bg-gray-50" data-day="${day}" data-id="${entryId}">
      <span>${day} => ${subject} (${hours % 1 === 0 ? hours + ' hour' : (hours*60) + ' mins'})</span>
      <button type="button"
        class="removeEntry cursor-pointer text-red-600 font-bold px-1 rounded
              hover:bg-red-100 hover:text-red-800 hover:scale-110
              transition duration-200 ease-in-out">
        ×
      </button>
    </li>
  `);
});


  $(document).on('click', '.removeEntry', function() {
    let li = $(this).closest('li');
    let day = li.data('day');
    let id = li.data('id');
    if(scheduleEntries[day] && scheduleEntries[day][id]) delete scheduleEntries[day][id];
    li.remove();
  });

 function loadSchedules() {
  $.ajax({
    url: '../controller/end-points/get_controller.php',
    method: 'GET',
    data: { requestType: 'get_schedules_gec_details' },
    dataType: 'json',
    success: function(res) {
      if(res.status === 200) {
        if(res.data.length === 0) {
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
                  <a href="view_fac_sched.php?sch_id=${sch.sch_id}" class="viewSchedule pc-btn pc-btn-sm pc-btn-neutral">
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
// SEARCH FILTER + EMPTY RESULT
// ==============================
$(document).on('keyup', '#scheduleSearch', function () {
  let value = $(this).val().toLowerCase();
  let visibleCount = 0;

  $('.schedule-row').each(function () {
    let match = $(this).text().toLowerCase().indexOf(value) > -1;
    $(this).toggle(match);
    if (match) visibleCount++;
  });

  // Show / hide empty result message
  if (visibleCount === 0) {
    $('#noSearchResult').removeClass('hidden');
  } else {
    $('#noSearchResult').addClass('hidden');
  }
});




  // ==============================
  // EDIT SCHEDULE
  // ==============================
  $(document).on('click', '.editSchedule', function() {
    editId = $(this).data('id');
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_schedules' },
      dataType: 'json',
      success: function(res) {
        let sch = res.data.find(s => s.sch_id == editId);
        if(sch) {
          $('#scheduleModal').removeClass('hidden');
          $('select[name="sch_user_id"]').val(sch.sch_user_id);
          $('#program').val(sch.sch_schedule.program);
          $('#semester').val(sch.sch_schedule.semester);
          $('input[name="instructor"]').val(sch.sch_schedule.instructor);

          // Populate existing entries
          scheduleEntries = {};
          $('#entriesList').empty();
          entryCounter = 0;
          let dbSchedule = sch.sch_schedule.schedule || {};

          function formatTime(time24) {
            let [hours, minutes] = time24.split(':').map(Number);
            let ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${hours}:${minutes.toString().padStart(2,'0')} ${ampm}`;
          }

          for (let day in dbSchedule) {
            dbSchedule[day].forEach(entry => {
              let entryId = ++entryCounter;
              scheduleEntries[day] = scheduleEntries[day] || {};
              scheduleEntries[day][entryId] = { subject: entry.subject, hours: entry.hours || 1 };

              let start = entry.time.from;
              let end = entry.time.to;
              let formattedTime = `${formatTime(start)} - ${formatTime(end)}`;

              $('#entriesList').append(`
                <li class="border border-gray-200 p-2 rounded mb-1 flex justify-between items-center bg-gray-50" data-day="${day}" data-id="${entryId}">
                  <span>${day} ${formattedTime} => ${entry.subject} (${entry.hours} ${entry.hours % 1 === 0 ? 'hour' : 'mins'})</span>
                 <button type="button"
                  class="removeEntry cursor-pointer text-red-600 font-bold px-1 rounded
                        hover:bg-red-100 hover:text-red-800 hover:scale-110
                        transition duration-200 ease-in-out">
                  ×
                </button>


                </li>
              `);
            });
          }

          $('.subjectSelect').prop('required', false);
        }
      }
    });
  });

  // ==============================
  // DELETE SCHEDULE
  // ==============================
  $(document).on('click', '.deleteSchedule', function() {
    let sch_id = $(this).data('id');
    if(confirm('Are you sure you want to delete this schedule?')) {
      $.ajax({
        url: '../controller/end-points/post_controller.php',
        method: 'POST',
        data: { requestType: 'delete_schedule', sch_id },
        dataType: 'json',
        success: function(res) {
          alert(res.message);
          loadSchedules();
        }
      });
    }
  });

  // ==============================
  // CREATE / UPDATE SCHEDULE
  // ==============================
  $('#scheduleForm').submit(function(e) {
    e.preventDefault();

    

    if(!editId) {
      let hasEntries = Object.keys(scheduleEntries).some(day =>
        Object.keys(scheduleEntries[day] || {}).length > 0
      );
      if(!hasEntries) return alert('Please add at least one schedule entry.');
    }

    // Convert scheduleEntries to DB-friendly format
    let scheduleForDB = {};
    for (let day in scheduleEntries) {
        scheduleForDB[day] = {};
        for (let id in scheduleEntries[day]) {
            let entry = scheduleEntries[day][id];
            scheduleForDB[day][id] = { subject: entry.subject, hours: entry.hours };
        }
    }

    let payload = {
      requestType: editId ? 'update_schedule' : 'create_schedule',
      sch_id: editId,
      sch_user_id: $('select[name="sch_user_id"]').val(),
      sch_schedule: JSON.stringify({
        program: $("#program").val(),
        semester: $("#semester").val(),
        schedule: scheduleForDB
      })
    };

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: payload,
      dataType: 'json',
      success: function(res) {
        $('#scheduleModal').addClass('hidden');
        $('#scheduleForm')[0].reset();
        $('#entriesList').empty();
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
