$(document).ready(function () {
  const startHour = 7;
  const endHour = 21;
  const dayNames = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

  function formatTime(hour, min) {
    const h = hour % 12 || 12;
    const ampm = hour >= 12 ? "PM" : "AM";
    return `${h}:${min.toString().padStart(2,"0")} ${ampm}`;
  }

  function timeToMinutes(t) {
    let [time, ampm] = t.split(" ");
    let [h, m] = time.split(":").map(Number);
    if (ampm === "PM" && h !== 12) h += 12;
    if (ampm === "AM" && h === 12) h = 0;
    return h * 60 + m;
  }

  function minutesToHHMM(mins) {
    const h = Math.floor(mins / 60).toString().padStart(2,'0');
    const m = (mins % 60).toString().padStart(2,'0');
    return `${h}:${m}`;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const schId = urlParams.get("sch_id");

  // =====================
  // LOAD & RENDER SCHEDULE
  // =====================
  function loadSchedule() {
    $.ajax({
      url: "../controller/end-points/get_controller.php",
      type: "GET",
      data: { requestType: "fetchAllSchedule", sch_id: schId },
      dataType: "json",
      success: function (res) {
        if (res.status === 200 && res.data.length > 0) {
          $(".sch_schedule_title").text(res.data[0].sch_schedule.program || "");
          $(".sch_schedule_sy").text(res.data[0].sch_schedule.semester || "");
          $(".sch_schedule_author").text(res.data[0].faculty_name || "").removeClass("hidden");
          renderSchedule(res.data);
        } else {
          // No schedule yet — clear all placeholder text
          $(".sch_schedule_title").text("");
          $(".sch_schedule_sy").text("");
          $(".sch_schedule_author").text("").addClass("hidden");
          $("#scheduleBody").html(
            '<tr><td colspan="7" class="text-center text-gray-400 py-10 italic">No schedule has been set yet.</td></tr>'
          );
        }
      },
      error: function () { alert("Failed to load schedule"); }
    });
  }

  function renderSchedule(data) {
    $("#scheduleBody").empty();

    // Build scheduleMap: day => startMin => entry info (with index for editing)
    let scheduleMap = {};
    dayNames.forEach(day => scheduleMap[day] = {});

    data.forEach(item => {
      const sched = item.sch_schedule.schedule;
      const faculty = item.faculty_name;
      Object.keys(sched).forEach(day => {
        sched[day].forEach((entry, idx) => {
          const startMin = timeToMinutes(entry.time.from);
          const endMin   = timeToMinutes(entry.time.to);
          const slots    = (endMin - startMin) / 30;
          scheduleMap[day][startMin] = {
            subject_code: entry.subject,
            subject_name: entry.subject_details ? entry.subject_details.subject_name : entry.subject,
            subject_unit: entry.subject_details ? entry.subject_details.subject_unit : '',
            faculty,
            room: entry.room || '',
            rowspan: slots,
            entry_index: idx,
            time_from_24: entry.time.from,
            time_to_24:   entry.time.to,
            day: day
          };
        });
      });
    });

    for (let hour = startHour; hour < endHour; hour++) {
      for (let min = 0; min < 60; min += 30) {
        const startTime = formatTime(hour, min);
        let endH = hour, endM = min + 30;
        if (endM === 60) { endM = 0; endH++; }
        const endTime = formatTime(endH, endM);
        const slotMin = hour * 60 + min;

        let row = `<tr class="hover:bg-gray-100">
          <td class="border px-2 py-1 font-semibold bg-gray-200 text-center whitespace-nowrap">
            ${startTime} – ${endTime}
          </td>`;

        dayNames.forEach(day => {
          if (scheduleMap[day][slotMin]) {
            const entry = scheduleMap[day][slotMin];
            row += `<td class="border h-10 sched-cell text-center bg-blue-200 text-xs font-semibold align-top p-1"
                       rowspan="${entry.rowspan}">
                      <div class="font-bold">${entry.subject_code}</div>
                      <div class="text-gray-600">${entry.subject_name}</div>
                      <div class="text-[10px] text-gray-500">${entry.faculty}</div>
                      ${entry.room ? `<div class="text-[10px] font-semibold text-blue-700">Room: ${entry.room}</div>` : ''}
                      <button class="editEntryTime mt-1 bg-yellow-500 hover:bg-yellow-400 text-white text-[10px] px-2 py-0.5 rounded cursor-pointer"
                        data-sch-id="${schId}"
                        data-day="${entry.day}"
                        data-entry-index="${entry.entry_index}"
                        data-from="${entry.time_from_24}"
                        data-to="${entry.time_to_24}"
                        data-room="${entry.room}"
                        data-subject="${entry.subject_code}">
                        ✏ Edit
                      </button>
                    </td>`;
          } else {
            let skip = false;
            for (let prev = slotMin - 30; prev >= 7*60; prev -= 30) {
              if (scheduleMap[day][prev] && scheduleMap[day][prev].rowspan > (slotMin - prev)/30) {
                skip = true; break;
              }
            }
            if (!skip) row += `<td class="border h-10 sched-cell"></td>`;
          }
        });

        row += `</tr>`;
        $("#scheduleBody").append(row);
      }
    }
  }

  // =====================
  // EDIT TIME MODAL
  // =====================
  $(document).on('click', '.editEntryTime', function() {
    const btn = $(this);
    const sch_id      = btn.data('sch-id');
    const day         = btn.data('day');
    const entry_index = btn.data('entry-index');
    const from        = btn.data('from');
    const to          = btn.data('to');
    const subject     = btn.data('subject');

    const room = btn.data('room') || '';
    $('#editTimeSchId').val(sch_id);
    $('#editTimeDay').val(day);
    $('#editTimeEntryIndex').val(entry_index);
    $('#editTimeFrom').val(from);
    $('#editTimeTo').val(to);
    $('#editTimeRoom').val(room);
    $('#editTimeSubjectLabel').text(`${subject} — ${day}`);
    $('#editTimeConflict').addClass('hidden').text('');
    $('#editTimeModal').removeClass('hidden');
  });

  $('#closeEditTimeModal').click(function() {
    $('#editTimeModal').addClass('hidden');
  });

  // Real-time conflict check
  function checkConflict() {
    const sch_id  = $('#editTimeSchId').val();
    const day     = $('#editTimeDay').val();
    const from    = $('#editTimeFrom').val();
    const to      = $('#editTimeTo').val();
    if (!from || !to || from >= to) return;

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: { requestType: 'check_conflict', exclude_sch_id: sch_id, day, new_from: from, new_to: to },
      dataType: 'json',
      success: function(res) {
        // if (res.conflicts && res.conflicts.length > 0) {
        //   $('#editTimeConflict').removeClass('hidden')
        //     .text('⚠ Conflict with: ' + res.conflicts.join(', '));
        // } else {
        //   $('#editTimeConflict').addClass('hidden').text('');
        // }
      }
    });
  }

  $('#editTimeFrom, #editTimeTo').on('change', checkConflict);

  // Save edited time
  $('#editTimeForm').on('submit', function(e) {
    e.preventDefault();
    const payload = {
      requestType:  'edit_entry_time',
      sch_id:        $('#editTimeSchId').val(),
      day:           $('#editTimeDay').val(),
      entry_index:   $('#editTimeEntryIndex').val(),
      new_from:      $('#editTimeFrom').val(),
      new_to:        $('#editTimeTo').val(),
      new_room:      $('#editTimeRoom').val()
    };

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: payload,
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          $('#editTimeModal').addClass('hidden');
          alert(res.message);
          loadSchedule();
        } else {
          alert('Error: ' + res.message);
        }
      }
    });
  });

  loadSchedule();
});
