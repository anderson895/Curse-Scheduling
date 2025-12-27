$(document).ready(function () {
  const startHour = 7;   // 7:00 AM
  const endHour = 21;    // 9:00 PM
  const days = 6;

  function formatTime(hour, min) {
    const h = hour % 12 || 12;
    const ampm = hour >= 12 ? "PM" : "AM";
    return `${h}:${min.toString().padStart(2, "0")} ${ampm}`;
  }

  for (let hour = startHour; hour < endHour; hour++) {
    // bawat loop = 30 minutes
    for (let min = 0; min < 60; min += 30) {
      const startTime = formatTime(hour, min);

      let endHourCalc = hour;
      let endMinCalc = min + 30;

      if (endMinCalc === 60) {
        endMinCalc = 0;
        endHourCalc++;
      }

      const endTime = formatTime(endHourCalc, endMinCalc);

      let row = `<tr class="hover:bg-gray-100 transition">`;

      // TIME RANGE CELL
      row += `
        <td class="border px-2 py-1 font-semibold bg-gray-200 text-center whitespace-nowrap">
          ${startTime} – ${endTime}
        </td>
      `;

      // DAY CELLS
      for (let d = 0; d < days; d++) {
        row += `
          <td class="border h-10 cursor-pointer sched-cell"></td>
        `;
      }

      row += `</tr>`;
      $("#scheduleBody").append(row);
    }
  }

  // Click to highlight (for testing)
  $(".sched-cell").on("click", function () {
    $(this).toggleClass("bg-blue-300");
  });
});
