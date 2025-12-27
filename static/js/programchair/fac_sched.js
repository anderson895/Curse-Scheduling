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
  // LOAD FACULTY
  // ==============================
  function loadFaculty() {
    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_schedule' },
      dataType: 'json',
      success: function (res) {
        if(res.status === 200) {
          let options = '<option value="">Select Faculty</option>';
          res.data.forEach(f => options += `<option value="${f.user_id}">${f.user_fname} ${f.user_lname}</option>`);
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
    let entryId = ++entryCounter;
    scheduleEntries[day][entryId] = { subject, hours };

    $('#entriesList').append(`
      <li class="border border-gray-200 p-2 rounded mb-1 flex justify-between items-center bg-gray-50" data-day="${day}" data-id="${entryId}">
        <span>${day} => ${subject} (${hours % 1 === 0 ? hours + ' hour' : (hours*60) + ' mins'})</span>
        <button type="button" class="removeEntry text-red-600 font-bold px-1 rounded">×</button>
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

  // ==============================
// LOAD EXISTING SCHEDULES
// ==============================
function loadSchedules() {
  $.ajax({
    url: '../controller/end-points/get_controller.php',
    method: 'GET',
    data: { requestType: 'get_schedules' },
    dataType: 'json',
    success: function(res) {
      if(res.status === 200) {
        let html = `<table class="min-w-full border border-gray-300 bg-white shadow-md rounded-lg">
                      <thead class="bg-red-900 text-white">
                        <tr>
                          <th class="p-2 text-left">Program</th>
                          <th class="p-2 text-left">Semester</th>
                          <th class="p-2 text-left">Instructor</th>
                          <th class="p-2 text-left">Type</th>
                          <th class="p-2 text-left">Actions</th>
                        </tr>
                      </thead>
                      <tbody>`;
        res.data.forEach(sch => {
          html += `<tr class="border-b hover:bg-gray-50">
                    <td class="p-2">${sch.sch_schedule.program || ''}</td>
                    <td class="p-2">${sch.sch_schedule.semester || ''}</td>
                    <td class="p-2">${sch.faculty_name || ''}</td>
                    <td class="p-2 capitalize">${sch.user_type || ''}</td>
                    <td class="p-2 flex gap-2">
                      <a href="view_fac_sched.php?sch_id=${sch.sch_id}" 
                         class="viewSchedule bg-gray-500 hover:bg-gray-400 text-white px-3 py-1 rounded">View</a>
                      <button class="editSchedule cursor-pointer bg-yellow-500 hover:bg-yellow-400 text-white px-3 py-1 rounded" data-id="${sch.sch_id}">Edit</button>
                      <button class="deleteSchedule cursor-pointer bg-red-600 hover:bg-red-500 text-white px-3 py-1 rounded" data-id="${sch.sch_id}">Delete</button>
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
          $('input[name="program"]').val(sch.sch_schedule.program);
          $('input[name="semester"]').val(sch.sch_schedule.semester);
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
                  <button type="button" class="removeEntry text-red-600 font-bold px-1 rounded">×</button>
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
        program: $('input[name="program"]').val(),
        semester: $('input[name="semester"]').val(),
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
  loadFaculty();
  loadSubjects();
  loadSchedules();

});
