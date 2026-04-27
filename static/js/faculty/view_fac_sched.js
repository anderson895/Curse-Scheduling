$(document).ready(function () {
  const startHour = 7;
  const endHour   = 21;
  const dayNames  = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  const LUNCH_FROM = 12 * 60, LUNCH_TO = 13 * 60;

  function formatTime(hour, min) {
    const h    = hour % 12 || 12;
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

  if (!schId || schId === "noschedule" || schId === "null") {
    showEmpty();
    return;
  }

  $.ajax({
    url:      "../controller/end-points/get_controller.php",
    type:     "GET",
    data:     { requestType: "fetchAllSchedule", sch_id: schId },
    dataType: "json",
    success: function (res) {
      if (res.status === 200 && res.data && res.data.length > 0) {
        currentProgram = res.data[0].sch_schedule.program || '';
        $(".sch_schedule_title").text(currentProgram);
        $(".sch_schedule_sy").text(res.data[0].sch_schedule.semester || "");
        $(".sch_schedule_author").text(res.data[0].faculty_name || "").removeClass("hidden");
        renderSchedule(res.data);
      } else {
        showEmpty();
      }
    },
    error: function () {
      showEmpty();
    }
  });

  function showEmpty() {
    $(".sch_schedule_title").text("");
    $(".sch_schedule_sy").text("");
    $(".sch_schedule_author").text("").addClass("hidden");
    $("#scheduleTableWrap").addClass("hidden");
    $("#noScheduleMsg").removeClass("hidden");
  }

  function renderSchedule(data) {
    $("#scheduleBody").empty();
    $("#noScheduleMsg").addClass("hidden");
    $("#scheduleTableWrap").removeClass("hidden");

    let scheduleMap = {};
    dayNames.forEach(day => scheduleMap[day] = {});

    data.forEach(item => {
      const sched   = item.sch_schedule.schedule || {};
      const faculty = item.faculty_name;

      Object.keys(sched).forEach(day => {
        sched[day].forEach(entry => {
          if (!entry.time) return;
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
            time_from_24: entry.time.from,
            time_to_24:   entry.time.to,
            day: day
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
            row += `<td class="has-slot" rowspan="${entry.rowspan}">
              <div class="pc-slot ${cls}" title="${escapeAttr(entry.subject_code)} — ${escapeAttr(entry.subject_name)}">
                <div class="pc-slot-subject">${escapeHtml(entry.subject_code)}</div>
                ${entry.subject_name && entry.subject_name !== entry.subject_code
                  ? `<div class="pc-slot-faculty" style="color:#374151; font-weight:600;">${escapeHtml(entry.subject_name)}</div>` : ''}
                <div class="pc-slot-faculty">${escapeHtml(entry.faculty)}</div>
                <div class="pc-slot-time">${formatRange24(entry.time_from_24, entry.time_to_24)}${entry.room ? ` &middot; Room ${escapeHtml(entry.room)}` : ''}</div>
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
});
