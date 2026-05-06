$(document).ready(function () {
  const startHour = 7;
  const endHour = 21;
  const dayNames = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  const LUNCH_FROM = 12 * 60, LUNCH_TO = 13 * 60;

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

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c =>
      ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])
    );
  }

  function escapeAttr(s) {
    return String(s ?? '').replace(/"/g, '&quot;');
  }

  function formatRange24(from, to) {
    if (!from || !to) return '';
    const [fh, fm] = from.split(':').map(Number);
    const [th, tm] = to.split(':').map(Number);
    return `${formatTime(fh, fm)} – ${formatTime(th, tm)}`;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const schId = urlParams.get("sch_id");

  let currentProgram = '';

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
          currentProgram = res.data[0].sch_schedule.program || '';
          $(".sch_schedule_title").text(currentProgram);
          $(".sch_schedule_sy").text(res.data[0].sch_schedule.semester || "");
          $(".sch_schedule_author").text(res.data[0].faculty_name || "").removeClass("hidden");
          renderSchedule(res.data);
        } else {
          currentProgram = '';
          $(".sch_schedule_title").text("");
          $(".sch_schedule_sy").text("");
          $(".sch_schedule_author").text("").addClass("hidden");
          $("#scheduleBody").html(
            '<tr><td colspan="7" class="text-center text-gray-400 py-10 italic">No schedule has been set yet.</td></tr>'
          );
        }
      },
      error: function () { pcToast("Failed to load schedule", "error"); }
    });
  }

  function renderSchedule(data) {
    $("#scheduleBody").empty();

    // Build scheduleMap: day => startMin => entry info
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
          if (slots <= 0) return;
          scheduleMap[day][startMin] = {
            subject_code: entry.subject,
            subject_name: entry.subject_details ? entry.subject_details.subject_name : entry.subject,
            faculty,
            room: entry.room || '',
            rowspan: slots,
            entry_index: idx,
            time_from_24: entry.time.from,
            time_to_24:   entry.time.to,
            day: day,
            cohorts: Array.isArray(entry.cohorts) ? entry.cohorts : []
          };
        });
      });
    });

    let bodyHtml = '';
    for (let hour = startHour; hour < endHour; hour++) {
      for (let min = 0; min < 60; min += 30) {
        const startTime = formatTime(hour, min);
        let endH = hour, endM = min + 30;
        if (endM === 60) { endM = 0; endH++; }
        const endTime = formatTime(endH, endM);
        const slotMin = hour * 60 + min;
        const isLunch = slotMin >= LUNCH_FROM && slotMin < LUNCH_TO;
        const rowCls = [
          isLunch ? 'is-lunch' : '',
          min === 0 ? 'hour-mark' : ''
        ].filter(Boolean).join(' ');

        let row = `<tr class="${rowCls}">
          <td class="time-col">${startTime}<span class="block text-[10px] opacity-60 font-medium">${endTime}</span></td>`;

        dayNames.forEach(day => {
          if (scheduleMap[day][slotMin]) {
            const entry = scheduleMap[day][slotMin];
            const cls = programClass(currentProgram);
            const isMerged = entry.cohorts && entry.cohorts.length > 1;
            const mergedTag = isMerged
              ? `<div class="pc-slot-merged" style="font-size:10px;font-weight:700;color:#065f46;background:#d1fae5;border-radius:4px;padding:1px 4px;display:inline-block;margin-top:2px;">Merged: ${entry.cohorts.map(escapeHtml).join(' / ')}</div>`
              : '';
            row += `<td class="has-slot" rowspan="${entry.rowspan}">
              <div class="pc-slot ${cls}" title="${escapeAttr(entry.subject_code)} — ${escapeAttr(entry.subject_name)}${isMerged ? ' (Merged: ' + entry.cohorts.join('/') + ')' : ''}">
                <div class="pc-slot-subject">${escapeHtml(entry.subject_code)}</div>
                ${entry.subject_name && entry.subject_name !== entry.subject_code
                  ? `<div class="pc-slot-faculty" style="color:#374151; font-weight:600;">${escapeHtml(entry.subject_name)}</div>` : ''}
                <div class="pc-slot-faculty">${escapeHtml(entry.faculty)}</div>
                <div class="pc-slot-time">${formatRange24(entry.time_from_24, entry.time_to_24)}${entry.room ? ` &middot; Room ${escapeHtml(entry.room)}` : ''}</div>
                ${mergedTag}
                <button class="editEntryTime pc-slot-edit"
                  data-sch-id="${schId}"
                  data-day="${escapeAttr(entry.day)}"
                  data-entry-index="${entry.entry_index}"
                  data-from="${escapeAttr(entry.time_from_24)}"
                  data-to="${escapeAttr(entry.time_to_24)}"
                  data-room="${escapeAttr(entry.room)}"
                  data-subject="${escapeAttr(entry.subject_code)}">
                  <span class="material-icons">edit</span> Edit
                </button>
              </div>
            </td>`;
          } else {
            let skip = false;
            for (let prev = slotMin - 30; prev >= 7*60; prev -= 30) {
              if (scheduleMap[day][prev] && scheduleMap[day][prev].rowspan > (slotMin - prev)/30) {
                skip = true; break;
              }
            }
            if (!skip) row += `<td class="is-empty"></td>`;
          }
        });

        row += `</tr>`;
        bodyHtml += row;
      }
    }
    $("#scheduleBody").html(bodyHtml);
  }

  // =====================
  // EDIT TIME MODAL
  // =====================
  $(document).on('click', '.editEntryTime', function(e) {
    e.stopPropagation();
    const btn = $(this);
    $('#editTimeSchId').val(btn.data('sch-id'));
    $('#editTimeDay').val(btn.data('day'));
    $('#editTimeEntryIndex').val(btn.data('entry-index'));
    $('#editTimeFrom').val(btn.data('from'));
    $('#editTimeTo').val(btn.data('to'));
    $('#editTimeRoom').val(btn.data('room') || '');
    $('#editTimeSubjectLabel').text(`${btn.data('subject')} — ${btn.data('day')}`);
    $('#editTimeConflict').addClass('hidden').text('');
    $('#editTimeModal').removeClass('hidden');
  });

  $('#closeEditTimeModal').click(function() {
    $('#editTimeModal').addClass('hidden');
  });

  function checkConflict() {
    const sch_id      = $('#editTimeSchId').val();
    const day         = $('#editTimeDay').val();
    const from        = $('#editTimeFrom').val();
    const to          = $('#editTimeTo').val();
    const room        = $('#editTimeRoom').val().trim();
    const entry_index = $('#editTimeEntryIndex').val();
    if (!from || !to || from >= to) return;

    $.ajax({
      url: '../controller/end-points/post_controller.php',
      method: 'POST',
      data: { requestType: 'check_conflict', exclude_sch_id: sch_id, day, new_from: from, new_to: to, room: room, entry_index: entry_index },
      dataType: 'json',
      success: function(res) {
        let warnings = [];
        if (res.conflicts && res.conflicts.length > 0) {
          warnings.push('⚠ Time conflict with: ' + res.conflicts.join(', '));
        }
        if (res.room_conflicts && res.room_conflicts.length > 0) {
          warnings.push('🚪 Room conflict: ' + res.room_conflicts.join(', '));
        }
        if (warnings.length > 0) {
          $('#editTimeConflict').removeClass('hidden').html(warnings.join('<br>'));
        } else {
          $('#editTimeConflict').addClass('hidden').text('');
        }
      }
    });
  }

  $('#editTimeFrom, #editTimeTo, #editTimeRoom').on('change keyup', checkConflict);

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
          pcToast(res.message || 'Schedule updated', 'success');
          loadSchedule();
        } else {
          pcToast('Error: ' + (res.message || 'Failed to save'), 'error');
        }
      }
    });
  });

  loadSchedule();
});
