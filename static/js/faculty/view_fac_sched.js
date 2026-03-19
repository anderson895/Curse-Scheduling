$(document).ready(function () {
  const startHour = 7;
  const endHour   = 21;
  const dayNames  = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

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

  // Get sch_id from URL
  const urlParams = new URLSearchParams(window.location.search);
  const schId = urlParams.get("sch_id");

  // If no valid sch_id, show empty state immediately
  if (!schId || schId === "noschedule" || schId === "null") {
    showEmpty();
    return;
  }

  // Fetch schedule
  $.ajax({
    url:      "../controller/end-points/get_controller.php",
    type:     "GET",
    data:     { requestType: "fetchAllSchedule", sch_id: schId },
    dataType: "json",
    success: function (res) {
      if (res.status === 200 && res.data && res.data.length > 0) {
        // Fill in header info
        $(".sch_schedule_title").text(res.data[0].sch_schedule.program || "");
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

          scheduleMap[day][startMin] = {
            subject_code: entry.subject,
            subject_name: entry.subject_details ? entry.subject_details.subject_name : entry.subject,
            faculty,
            rowspan: slots
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
});
