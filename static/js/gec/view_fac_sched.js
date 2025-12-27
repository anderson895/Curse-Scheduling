$(document).ready(function () {
  const startHour = 7;   // 7:00 AM
  const endHour = 21;    // 9:00 PM
  const days = 6;
  const dayNames = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

  function formatTime(hour, min) {
    const h = hour % 12 || 12;
    const ampm = hour >= 12 ? "PM" : "AM";
    return `${h}:${min.toString().padStart(2,"0")} ${ampm}`;
  }

  function convertTo12Hour(time24) {
    let [h, m] = time24.split(":").map(Number);
    let ampm = h >= 12 ? "PM" : "AM";
    h = h % 12 || 12;
    return `${h}:${m.toString().padStart(2,"0")} ${ampm}`;
  }

  // Build time grid
  for (let hour = startHour; hour < endHour; hour++) {
    for (let min = 0; min < 60; min += 30) {
      const startTime = formatTime(hour, min);
      let endH = hour, endM = min + 30;
      if (endM === 60) { endM = 0; endH++; }
      const endTime = formatTime(endH, endM);

      let row = `<tr class="hover:bg-gray-100">
        <td class="border px-2 py-1 font-semibold bg-gray-200 text-center whitespace-nowrap">
          ${startTime} – ${endTime}
        </td>`;

      for (let d = 0; d < dayNames.length; d++) {
        row += `<td class="border h-10 sched-cell text-center"
                    data-day="${dayNames[d]}"
                    data-from="${startTime}"
                    data-to="${endTime}">
                </td>`;
      }

      row += `</tr>`;
      $("#scheduleBody").append(row);
    }
  }

  // Get sch_id from URL
  const urlParams = new URLSearchParams(window.location.search);
  const schId = urlParams.get("sch_id"); // e.g., ?sch_id=10

  // Fetch schedule by sch_id
  $.ajax({
    url: "../controller/end-points/get_controller.php",
    type: "GET",
    data: { requestType: "fetchAllSchedule", sch_id: schId },
    dataType: "json",
    success: function (res) {
        
        $(".sch_schedule_title").text(res.data[0].sch_schedule.program);
        $(".sch_schedule_sy").text(res.data[0].sch_schedule.semester);
        $(".sch_schedule_author").text(res.data[0].faculty_name);

       

        
      if (res.status === 200) {
        renderSchedule(res.data);
      }
    },
    error: function () {
      alert("Failed to load schedule");
    }
  });

  // Render schedule into grid
function renderSchedule(data) {
  const days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  
  // Build a map: day => startTime => {subject, faculty, rowspan}
  let scheduleMap = {};
  days.forEach(day => scheduleMap[day] = {});

  data.forEach(item => {
    const sched = item.sch_schedule.schedule;
    const faculty = item.faculty_name;

    Object.keys(sched).forEach(day => {
      sched[day].forEach(entry => {
        const startMin = timeToMinutes(entry.time.from);
        const endMin   = timeToMinutes(entry.time.to);
        const slots = (endMin - startMin) / 30;

        scheduleMap[day][startMin] = {
          subject_code: entry.subject,
          subject_name: entry.subject_details ? entry.subject_details.subject_name : entry.subject,
          subject_unit: entry.subject_details ? entry.subject_details.subject_unit : '',
          subject_type: entry.subject_details ? entry.subject_details.subject_type : '',
          faculty,
          rowspan: slots
        };
      });
    });
  });

  // Now rebuild the table
  $("#scheduleBody").empty();

  for (let hour = 7; hour < 21; hour++) {
    for (let min = 0; min < 60; min += 30) {
      const startTime = formatTime(hour, min);
      let endH = hour, endM = min + 30;
      if (endM === 60) { endM = 0; endH++; }
      const endTime = formatTime(endH, endM);

      let row = `<tr class="hover:bg-gray-100">
        <td class="border px-2 py-1 font-semibold bg-gray-200 text-center whitespace-nowrap">
          ${startTime} – ${endTime}
        </td>`;

      days.forEach(day => {
        const slotMin = timeToMinutes(startTime);

        if (scheduleMap[day][slotMin]) {
          // First cell of a class
          const entry = scheduleMap[day][slotMin];
          row += `<td class="border h-10 sched-cell text-center bg-blue-300 text-xs font-semibold"
                     rowspan="${entry.rowspan}">
                    <div>${entry.subject_code}</div>
                    <div>${entry.subject_name}</div>
                    
                    <div class="text-[10px]">${entry.faculty}</div>
                  </td>`;
        } else {
          // Check if inside a merged rowspan
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



  // Convert HH:MM AM/PM to minutes
  function timeToMinutes(t) {
    let [time, ampm] = t.split(" ");
    let [h, m] = time.split(":").map(Number);
    if (ampm === "PM" && h !== 12) h += 12;
    if (ampm === "AM" && h === 12) h = 0;
    return h * 60 + m;
  }

});