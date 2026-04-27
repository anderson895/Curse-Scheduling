$(document).ready(function () {

  const dayOrder = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
  const startHour = 7, endHour = 21;
  const LUNCH_FROM = 12 * 60, LUNCH_TO = 13 * 60;

  function timeToMinutes(t) {
    if (!t) return 0;
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
  }

  function formatSlot(h, m) {
    const ampm = h >= 12 ? 'PM' : 'AM';
    const h12  = h % 12 || 12;
    return `${h12}:${String(m).padStart(2,'0')} ${ampm}`;
  }

  function formatRange(from, to) {
    if (!from || !to) return '';
    const [fh, fm] = from.split(':').map(Number);
    const [th, tm] = to.split(':').map(Number);
    return `${formatSlot(fh, fm)} – ${formatSlot(th, tm)}`;
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

  // =====================
  // LOAD ROOMS
  // =====================
  function loadRooms() {
    $('#roomsContainer').html('<div class="text-center text-gray-400 py-10"><span class="material-icons animate-spin align-middle">autorenew</span> Loading rooms...</div>');

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
        const roomNames = Object.keys(rooms).sort((a, b) =>
          a.localeCompare(b, undefined, { numeric: true })
        );

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

        // Tabs
        let tabs = '';
        roomNames.forEach((room, i) => {
          tabs += `<button class="room-tab pc-tab ${i === 0 ? 'is-active' : ''}" data-room="${escapeHtml(room)}">
                     <span class="material-icons">meeting_room</span>${escapeHtml(room)}
                   </button>`;
        });
        $('#roomTabs').html(tabs);

        // Legend (programs that actually appear)
        const seenPrograms = new Set();
        roomNames.forEach(r => (rooms[r] || []).forEach(e => {
          if (e.program) seenPrograms.add(e.program.trim().toUpperCase());
        }));

        // Tables (first visible)
        let allTables = '';
        roomNames.forEach((room, i) => {
          allTables += buildRoomTimetable(room, rooms[room], i === 0);
        });
        $('#roomsContainer').html(buildLegend(seenPrograms) + allTables);
      }
    });
  }

  // =====================
  // LEGEND
  // =====================
  function buildLegend(seen) {
    if (!seen.size) return '';
    const items = [
      { key: 'BSCE',  cls: 'prog-bsce'  },
      { key: 'BSCoE', cls: 'prog-bscoe' },
      { key: 'BSEE',  cls: 'prog-bsee'  },
      { key: 'BSECE', cls: 'prog-bsece' },
      { key: 'BSIE',  cls: 'prog-bsie'  },
      { key: 'BSME',  cls: 'prog-bsme'  }
    ].filter(p => seen.has(p.key.toUpperCase()));

    if (!items.length) return '';
    return `<div class="pc-prog-legend mb-3">
      <span class="text-xs font-bold text-gray-500 uppercase tracking-wide self-center mr-1">Programs:</span>
      ${items.map(p => `
        <span class="pc-prog-legend-item ${p.cls}" style="--pc-slot-accent: var(--pc-slot-accent);">
          <span class="dot"></span>${p.key}
        </span>`).join('')}
    </div>`;
  }

  // =====================
  // BUILD TIMETABLE
  // =====================
  function buildRoomTimetable(room, entries, visible) {
    const safeRoom = escapeHtml(room);

    // scheduleMap: day => startMin => entry
    let scheduleMap = {};
    dayOrder.forEach(d => scheduleMap[d] = {});

    (entries || []).forEach(e => {
      const startMin = timeToMinutes(e.from);
      const endMin   = timeToMinutes(e.to);
      const slots    = Math.round((endMin - startMin) / 30);
      if (slots <= 0 || startMin === 0) return;
      if (!scheduleMap[e.day]) return;
      if (!scheduleMap[e.day][startMin]) {
        scheduleMap[e.day][startMin] = {
          subject: e.subject,
          faculty: e.faculty,
          program: e.program,
          semester: e.semester,
          from: e.from,
          to: e.to,
          rowspan: slots
        };
      }
    });

    // Body rows
    let tbody = '';
    for (let hour = startHour; hour < endHour; hour++) {
      for (let min = 0; min < 60; min += 30) {
        const slotMin  = hour * 60 + min;
        const endM     = min + 30 === 60 ? 0 : min + 30;
        const endH     = min + 30 === 60 ? hour + 1 : hour;
        const startLbl = formatSlot(hour, min);
        const endLbl   = formatSlot(endH, endM);

        const isLunch  = slotMin >= LUNCH_FROM && slotMin < LUNCH_TO;
        const isHourMark = min === 0;

        const rowCls = [
          isLunch ? 'is-lunch' : '',
          isHourMark ? 'hour-mark' : ''
        ].filter(Boolean).join(' ');

        tbody += `<tr class="${rowCls}">
          <td class="time-col">${startLbl}<span class="block text-[10px] opacity-60 font-medium">${endLbl}</span></td>`;

        dayOrder.forEach(day => {
          const entry = scheduleMap[day][slotMin];
          if (entry) {
            const cls = programClass(entry.program);
            tbody += `<td class="has-slot" rowspan="${entry.rowspan}">
              <div class="pc-slot ${cls}" title="${escapeHtml(entry.subject)} — ${escapeHtml(entry.faculty)}">
                <div class="pc-slot-subject">${escapeHtml(entry.subject)}</div>
                <div class="pc-slot-faculty">${escapeHtml(entry.faculty)}</div>
                <div class="pc-slot-time">${formatRange(entry.from, entry.to)}</div>
                ${entry.program ? `<span class="pc-slot-prog">${escapeHtml(entry.program)}</span>` : ''}
              </div>
            </td>`;
          } else {
            // covered by a rowspan above?
            let skip = false;
            for (let prev = slotMin - 30; prev >= startHour * 60; prev -= 30) {
              const prevEntry = scheduleMap[day][prev];
              if (prevEntry && prevEntry.rowspan > (slotMin - prev) / 30) {
                skip = true; break;
              }
            }
            if (!skip) tbody += `<td class="is-empty"></td>`;
          }
        });

        tbody += `</tr>`;
      }
    }

    return `
      <div class="room-timetable ${visible ? '' : 'hidden'}" data-room="${safeRoom}">
        <div class="overflow-x-auto -mx-1 px-1">
          <table class="pc-room-grid">
            <thead>
              <tr>
                <th>Time</th>
                ${dayOrder.map(d => `<th>${d}</th>`).join('')}
              </tr>
            </thead>
            <tbody>${tbody}</tbody>
          </table>
        </div>
      </div>`;
  }

  // =====================
  // TAB SWITCHING
  // =====================
  $(document).on('click', '.room-tab', function () {
    const room = $(this).data('room');
    $('.room-tab').removeClass('is-active');
    $(this).addClass('is-active');
    $('.room-timetable').addClass('hidden');
    $(`.room-timetable[data-room="${room}"]`).removeClass('hidden');
  });

  // =====================
  // SEARCH FILTER
  // =====================
  $('#roomSearch').on('input', function () {
    const q = $(this).val().toLowerCase();
    $('.room-tab').each(function () {
      const room = String($(this).data('room')).toLowerCase();
      $(this).toggle(room.includes(q));
    });
  });

  // =====================
  // INIT
  // =====================
  loadRooms();
});
