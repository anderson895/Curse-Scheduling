$(document).ready(function () {

  const dayOrder = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  const startHour = 7, endHour = 21;

  function formatTime24(t) {
    if (!t || t === '00:00') return '';
    const [h, m] = t.split(':').map(Number);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${String(m).padStart(2,'0')} ${ampm}`;
  }

  function timeToMinutes(t) {
    if (!t) return 0;
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
  }

  // =====================
  // LOAD ROOMS
  // =====================
  function loadRooms() {
    $('#roomsContainer').html('<div class="text-center text-gray-400 py-10">Loading rooms...</div>');

    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_room_schedules' },
      dataType: 'json',
      success: function (res) {
        if (res.status !== 200) {
          $('#roomsContainer').html('<div class="text-center text-red-500 p-4">Failed to load rooms.</div>');
          return;
        }

        const rooms = res.data;
        const roomNames = Object.keys(rooms);

        if (roomNames.length === 0) {
          $('#roomsContainer').html(`
            <div class="text-center text-gray-400 py-16">
              <span class="material-icons text-5xl mb-3 block">meeting_room</span>
              <p class="text-lg font-semibold">No rooms assigned yet.</p>
              <p class="text-sm mt-1">Add room numbers when creating or editing a schedule.</p>
            </div>`);
          $('#roomCount').text('0');
          return;
        }

        $('#roomCount').text(roomNames.length);

        // Build room selector tabs
        let tabs = '';
        roomNames.forEach((room, i) => {
          tabs += `<button class="room-tab cursor-pointer px-4 py-2 rounded-md text-sm font-semibold border transition
                     ${i === 0 ? 'bg-red-900 text-white border-red-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-red-50'}"
                   data-room="${room}">
                     <span class="material-icons text-sm align-middle mr-1">meeting_room</span>${room}
                   </button>`;
        });
        $('#roomTabs').html(tabs);

        // Render all room timetables (hidden except first)
        let allTables = '';
        roomNames.forEach((room, i) => {
          allTables += buildRoomTimetable(room, rooms[room], i === 0);
        });
        $('#roomsContainer').html(allTables);
      }
    });
  }

  // =====================
  // BUILD TIMETABLE
  // =====================
  function buildRoomTimetable(room, entries, visible) {

    // Build scheduleMap: day => startMin => entry
    let scheduleMap = {};
    dayOrder.forEach(d => scheduleMap[d] = {});

    entries.forEach(e => {
      const startMin = timeToMinutes(e.from);
      const endMin   = timeToMinutes(e.to);
      const slots    = Math.round((endMin - startMin) / 30);
      if (slots <= 0 || startMin === 0) return;
      scheduleMap[e.day] = scheduleMap[e.day] || {};
      // Keep first if conflict (same day same time)
      if (!scheduleMap[e.day][startMin]) {
        scheduleMap[e.day][startMin] = {
          subject: e.subject,
          faculty: e.faculty,
          program: e.program,
          semester: e.semester,
          from: e.from,
          to: e.to,
          rowspan: slots,
          sch_id: e.sch_id
        };
      }
    });

    // Build rows
    let tbody = '';
    for (let hour = startHour; hour < endHour; hour++) {
      for (let min = 0; min < 60; min += 30) {
        const slotMin  = hour * 60 + min;
        const endM     = min + 30 === 60 ? 0 : min + 30;
        const endH     = min + 30 === 60 ? hour + 1 : hour;
        const startLbl = formatSlot(hour, min);
        const endLbl   = formatSlot(endH, endM);

        tbody += `<tr class="hover:bg-gray-50">
          <td class="border px-2 py-1 text-xs font-semibold bg-gray-100 text-center whitespace-nowrap w-28">${startLbl} – ${endLbl}</td>`;

        dayOrder.forEach(day => {
          const entry = scheduleMap[day][slotMin];
          if (entry) {
            tbody += `<td class="border p-1 text-xs text-center bg-blue-100 font-semibold align-top" rowspan="${entry.rowspan}">
              <div class="font-bold text-blue-900">${entry.subject}</div>
              <div class="text-gray-600 text-[11px]">${entry.faculty}</div>
              <div class="text-gray-400 text-[10px]">${entry.program}</div>
            </td>`;
          } else {
            // check if covered by rowspan
            let skip = false;
            for (let prev = slotMin - 30; prev >= startHour * 60; prev -= 30) {
              if (scheduleMap[day][prev] && scheduleMap[day][prev].rowspan > (slotMin - prev) / 30) {
                skip = true; break;
              }
            }
            if (!skip) tbody += `<td class="border h-8 sched-cell"></td>`;
          }
        });

        tbody += `</tr>`;
      }
    }

    return `
      <div class="room-timetable ${visible ? '' : 'hidden'}" data-room="${room}">
        <div class="overflow-x-auto">
          <table class="min-w-full border-collapse text-sm">
            <thead>
              <tr class="bg-blue-900 text-white">
                <th class="border p-2 w-28 text-xs">TIME</th>
                ${dayOrder.map(d => `<th class="border p-2 text-xs">${d}</th>`).join('')}
              </tr>
            </thead>
            <tbody>${tbody}</tbody>
          </table>
        </div>
      </div>`;
  }

  function formatSlot(h, m) {
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${String(m).padStart(2,'0')} ${ampm}`;
  }

  // =====================
  // TAB SWITCHING
  // =====================
  $(document).on('click', '.room-tab', function () {
    const room = $(this).data('room');
    $('.room-tab').removeClass('bg-red-900 text-white border-red-900')
                  .addClass('bg-white text-gray-700 border-gray-300 hover:bg-red-50');
    $(this).addClass('bg-red-900 text-white border-red-900')
           .removeClass('bg-white text-gray-700 border-gray-300 hover:bg-red-50');

    $('.room-timetable').addClass('hidden');
    $(`.room-timetable[data-room="${room}"]`).removeClass('hidden');
  });

  // =====================
  // SEARCH FILTER
  // =====================
  $('#roomSearch').on('input', function () {
    const q = $(this).val().toLowerCase();
    $('.room-tab').each(function () {
      const room = $(this).data('room').toLowerCase();
      $(this).toggle(room.includes(q));
    });
  });

  // =====================
  // INIT
  // =====================
  loadRooms();
});
