$(document).ready(function () {

  const dayOrder  = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  const startHour = 7, endHour = 21;

  let allRoomData   = {};   // room => [ entries ]
  let allRoomNames  = [];
  let selectedDay   = dayOrder[new Date().getDay() - 1] || "Monday";

  // =====================
  // HELPERS
  // =====================
  function timeToMinutes(t) {
    if (!t || t === '00:00') return 0;
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
  }

  function formatSlot(h, m) {
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${String(m).padStart(2,'0')} ${ampm}`;
  }

  function nowMinutes() {
    const n = new Date();
    return n.getHours() * 60 + n.getMinutes();
  }

  function todayName() {
    const days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    return days[new Date().getDay()];
  }

  // =====================
  // LOAD DATA
  // =====================
  function loadRooms() {
    $('#availGrid').html('<tr><td colspan="99" class="text-center text-gray-400 py-10">Loading rooms...</td></tr>');

    $.ajax({
      url: '../controller/end-points/get_controller.php',
      method: 'GET',
      data: { requestType: 'get_room_schedules' },
      dataType: 'json',
      success: function (res) {
        if (res.status !== 200) {
          $('#availGrid').html('<tr><td colspan="99" class="text-center text-red-400 py-6">Failed to load data.</td></tr>');
          return;
        }

        allRoomData  = res.data;
        allRoomNames = Object.keys(allRoomData).sort();

        if (allRoomNames.length === 0) {
          $('#availGrid').html(`<tr><td colspan="99" class="text-center text-gray-400 py-16 italic">
            No rooms have been assigned to any schedule yet.</td></tr>`);
          updateStats(0, 0, 0);
          return;
        }

        buildDayTabs();
        renderGrid();
      }
    });
  }

  // =====================
  // DAY TABS
  // =====================
  function buildDayTabs() {
    const today = todayName();
    let html = '';
    dayOrder.forEach(day => {
      const isToday   = day === today;
      const isActive  = day === selectedDay;
      html += `<button class="day-tab cursor-pointer px-4 py-2 rounded-md text-sm font-semibold border transition
                 ${isActive ? 'bg-red-900 text-white border-red-900' : 'bg-white text-gray-700 border-gray-300 hover:bg-red-50'}"
               data-day="${day}">
                 ${day.substring(0,3)}${isToday ? ' <span class="text-yellow-300 text-[10px]">TODAY</span>' : ''}
               </button>`;
    });
    $('#dayTabs').html(html);
  }

  $(document).on('click', '.day-tab', function () {
    selectedDay = $(this).data('day');
    $('.day-tab').removeClass('bg-red-900 text-white border-red-900')
                  .addClass('bg-white text-gray-700 border-gray-300');
    $(this).addClass('bg-red-900 text-white border-red-900')
           .removeClass('bg-white text-gray-700 border-gray-300');
    renderGrid();
  });

  // =====================
  // RENDER GRID
  // =====================
  function renderGrid() {
    const day      = selectedDay;
    const isToday  = day === todayName();
    const nowMin   = isToday ? nowMinutes() : -1;

    // Build occupied map: room => Set of minutes occupied
    let occupiedMap = {}; // room => { startMin => {subject, faculty, rowspan} }
    allRoomNames.forEach(room => {
      occupiedMap[room] = {};
      (allRoomData[room] || []).forEach(e => {
        if (e.day !== day) return;
        const s = timeToMinutes(e.from);
        const en = timeToMinutes(e.to);
        const slots = Math.round((en - s) / 30);
        if (slots <= 0 || s === 0) return;
        occupiedMap[room][s] = { subject: e.subject, faculty: e.faculty, rowspan: slots, from: e.from, to: e.to };
      });
    });

    // Count available rooms right now
    let availNow = 0, occupiedNow = 0;
    allRoomNames.forEach(room => {
      let busy = false;
      Object.keys(occupiedMap[room]).forEach(startMin => {
        const entry = occupiedMap[room][startMin];
        const endMin = parseInt(startMin) + entry.rowspan * 30;
        if (nowMin >= parseInt(startMin) && nowMin < endMin) busy = true;
      });
      if (busy) occupiedNow++; else availNow++;
    });
    updateStats(allRoomNames.length, availNow, occupiedNow);

    // Build header
    let headHtml = `<th class="border p-2 bg-blue-900 text-white text-xs w-28 sticky left-0 z-10">TIME</th>`;
    allRoomNames.forEach(room => {
      headHtml += `<th class="border p-2 bg-blue-900 text-white text-xs whitespace-nowrap">
        <span class="material-icons text-sm align-middle">meeting_room</span> ${room}
      </th>`;
    });
    $('#availHead').html(headHtml);

    // Build body rows
    let bodyHtml = '';
    for (let hour = startHour; hour < endHour; hour++) {
      for (let min = 0; min < 60; min += 30) {
        const slotMin  = hour * 60 + min;
        const endM     = min + 30 === 60 ? 0 : min + 30;
        const endH     = min + 30 === 60 ? hour + 1 : hour;
        const isNow    = isToday && nowMin >= slotMin && nowMin < slotMin + 30;

        const timeLbl  = `${formatSlot(hour, min)} – ${formatSlot(endH, endM)}`;
        const rowClass = isNow ? 'bg-yellow-50' : 'hover:bg-gray-50';

        bodyHtml += `<tr class="${rowClass}">
          <td class="border px-2 py-1 text-xs font-semibold bg-gray-100 text-center whitespace-nowrap sticky left-0 z-10
                     ${isNow ? '!bg-yellow-200 font-bold text-yellow-900' : ''}">
            ${timeLbl}${isNow ? ' <span class="block text-[10px] text-yellow-700">▶ NOW</span>' : ''}
          </td>`;

        allRoomNames.forEach(room => {
          const entry = occupiedMap[room][slotMin];
          if (entry) {
            // Occupied — show subject info
            bodyHtml += `<td class="border p-1 text-xs text-center bg-red-100 font-semibold align-top" rowspan="${entry.rowspan}">
              <div class="font-bold text-red-800">${entry.subject}</div>
              <div class="text-gray-500 text-[10px]">${entry.faculty}</div>
              <span class="inline-block mt-1 bg-red-200 text-red-800 text-[10px] px-1 rounded">OCCUPIED</span>
            </td>`;
          } else {
            // Check if covered by rowspan above
            let skip = false;
            for (let prev = slotMin - 30; prev >= startHour * 60; prev -= 30) {
              const e = occupiedMap[room][prev];
              if (e && e.rowspan > (slotMin - prev) / 30) { skip = true; break; }
            }
            if (!skip) {
              bodyHtml += `<td class="border h-8 text-center text-[10px] text-green-600 font-semibold bg-green-50">
                <span class="material-icons text-[14px] align-middle">check_circle</span> Free
              </td>`;
            }
          }
        });

        bodyHtml += `</tr>`;
      }
    }

    $('#availGrid').html(bodyHtml);
  }

  // =====================
  // STATS BAR
  // =====================
  function updateStats(total, avail, occupied) {
    $('#statTotal').text(total);
    $('#statAvail').text(avail);
    $('#statOccupied').text(occupied);
  }

  // =====================
  // INIT
  // =====================
  loadRooms();

  // Auto-refresh every 60 seconds to keep "NOW" indicator accurate
  setInterval(function () {
    if (allRoomNames.length > 0) renderGrid();
  }, 60000);
});
