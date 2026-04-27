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
      html += `<button class="day-tab pc-tab ${isActive ? 'is-active' : ''}" data-day="${day}">
                 ${day.substring(0,3)}${isToday ? ' <span class="text-[10px] ml-1 ' + (isActive ? 'text-yellow-300' : 'text-red-700') + '">TODAY</span>' : ''}
               </button>`;
    });
    $('#dayTabs').html(html);
  }

  $(document).on('click', '.day-tab', function () {
    selectedDay = $(this).data('day');
    $('.day-tab').removeClass('is-active');
    $(this).addClass('is-active');
    buildDayTabs();
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
    let headHtml = `<th>Time</th>`;
    allRoomNames.forEach(room => {
      headHtml += `<th><span class="material-icons">meeting_room</span> ${room}</th>`;
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
        const rowCls   = [
          min === 0 ? 'hour-mark' : '',
          isNow ? 'is-now' : ''
        ].filter(Boolean).join(' ');

        bodyHtml += `<tr class="${rowCls}">
          <td class="time-col">
            ${formatSlot(hour, min)}
            <span class="block text-[10px] opacity-60 font-medium">${formatSlot(endH, endM)}</span>
            ${isNow ? '<span class="rooms-now-tag">NOW</span>' : ''}
          </td>`;

        allRoomNames.forEach(room => {
          const entry = occupiedMap[room][slotMin];
          if (entry) {
            bodyHtml += `<td class="has-slot rooms-occupied" rowspan="${entry.rowspan}">
              <div class="pc-slot">
                <div class="pc-slot-subject">${entry.subject}</div>
                <div class="pc-slot-faculty">${entry.faculty}</div>
                <span class="rooms-status-tag is-occupied">
                  <span class="material-icons">do_not_disturb_on</span> Occupied
                </span>
              </div>
            </td>`;
          } else {
            let skip = false;
            for (let prev = slotMin - 30; prev >= startHour * 60; prev -= 30) {
              const e = occupiedMap[room][prev];
              if (e && e.rowspan > (slotMin - prev) / 30) { skip = true; break; }
            }
            if (!skip) {
              bodyHtml += `<td class="rooms-free">
                <span class="rooms-status-tag is-free">
                  <span class="material-icons">check_circle</span> Free
                </span>
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
