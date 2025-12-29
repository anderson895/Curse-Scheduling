function loadDashboard() {
  $.ajax({
    url: '../controller/end-points/get_controller.php',
    method: 'GET',
    data: { requestType: 'fetchDashboard' },
    dataType: 'json',
    success: function (res) {
      // Convert strings to numbers
      const users = Number(res.total_users);
      const subjects = Number(res.total_subjects);
      const schedules = Number(res.total_schedules);

      // Update stats cards
      $("#totalUsers").text(users);
      $("#totalSubjects").text(subjects);
      $("#totalSchedules").text(schedules);

      // Render charts
      renderBarChart(users, subjects, schedules);
      renderDonutChart(users, subjects, schedules);
      renderRadarChart(users, subjects, schedules);

    }
  });
}

// Global chart variables
let barChart, donutChart, lineChart;

// Bar Chart
function renderBarChart(users, subjects, schedules) {
  const options = {
    chart: { type: 'bar', height: 350, toolbar: { show: false } },
    series: [{ name: 'Count', data: [users, subjects, schedules] }],
    xaxis: { categories: ['Users', 'Subjects', 'Schedules'] },
    colors: ['#b91c1c'],
    dataLabels: { enabled: true }
  };
  if (barChart) barChart.updateSeries([{ data: [users, subjects, schedules] }]);
  else {
    barChart = new ApexCharts(document.querySelector("#barChart"), options);
    barChart.render();
  }
}

// Donut Chart
function renderDonutChart(users, subjects, schedules) {
  const options = {
    chart: { type: 'donut', height: 350 },
    series: [users, subjects, schedules],
    labels: ['Users', 'Subjects', 'Schedules'],
    colors: ['#f87171', '#fb923c', '#facc15'],
    legend: { position: 'bottom' },
    dataLabels: { enabled: true },
    tooltip: { y: { formatter: function(val) { return val; } } }
  };
  if (donutChart) donutChart.updateSeries([users, subjects, schedules]);
  else {
    donutChart = new ApexCharts(document.querySelector("#donutChart"), options);
    donutChart.render();
  }
}




// Initial load
loadDashboard();
