<body class="bg-gray-100">
  
<!-- Layout Wrapper -->
<div class="min-h-screen flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-red-900 shadow-lg w-64 lg:w-1/5 xl:w-1/6 p-6 space-y-6 lg:static fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- Sidebar Header -->
    <div class="flex flex-wrap justify-center items-center space-x-4 p-4 bg-red-800 rounded-lg shadow-inner hover:shadow-xl transition-shadow duration-300 max-w-full">
      <img src="../static/logo.jpg" alt="Logo" class="w-20 h-20 rounded-full border-2 border-white shadow-sm transform transition-transform duration-300 hover:scale-105">
      <h1 class="text-base sm:text-lg md:text-xl font-bold text-white tracking-tight text-center">
        <?=ucfirst($On_Session[0]['user_type'])?>
      </h1>
    </div>

    <!-- Navigation -->
    <nav class="space-y-4 text-left text-white overflow-y-auto h-[calc(100vh-120px)]">
      
      <a href="dashboard" class="nav-link flex items-center space-x-3 hover:bg-red-800 px-3 py-2 rounded-md transition">
        <span class="material-icons">dashboard</span>
        <span>Dashboard</span>
      </a>


      

      <button id="toggleAccounts" class="w-full cursor-pointer flex items-center justify-between px-3 py-2 hover:bg-red-800 rounded-md transition">
        <div class="flex items-center space-x-3">
          <span class="material-icons">manage_accounts</span>
          <span>Accounts</span>
        </div>
        <span id="accounts_dropdownIcon" class="material-icons">expand_more</span>
      </button>

      <div id="accountsDropdown" class="ml-8 space-y-2 hidden">
        <a href="create_account" class="flex items-center space-x-2 px-4 py-2 hover:bg-red-800 rounded-md transition">
          <span class="material-icons text-sm">person_add</span>
          <span>Create</span>
        </a>
        <a href="faculty" class="flex items-center space-x-2 px-4 py-2 hover:bg-red-800 rounded-md transition">
          <span class="material-icons text-sm">school</span>
          <span>Faculty</span>
        </a>
        <a href="gec" class="flex items-center space-x-2 px-4 py-2 hover:bg-red-800 rounded-md transition">
          <span class="material-icons text-sm">groups</span>
          <span>GEC</span>
        </a>
        <a href="all" class="flex items-center space-x-2 px-4 py-2 hover:bg-red-800 rounded-md transition">
          <span class="material-icons text-sm">list</span>
          <span>All</span>
        </a>
      </div>

    

      <a href="gec_sched" class="nav-link flex items-center space-x-3 hover:bg-red-800 px-3 py-2 rounded-md transition">
        <span class="material-icons">event_note</span>
        <span>GEC Schedule</span>
      </a>

      <a href="fac_sched" class="nav-link flex items-center space-x-3 hover:bg-red-800 px-3 py-2 rounded-md transition">
        <span class="material-icons">table_view</span>

        <span>Schedule</span>
      </a>

      <a href="report" class="nav-link flex items-center space-x-3 hover:bg-red-800 px-3 py-2 rounded-md transition">
        <span class="material-icons">calendar_month</span>
        <span>General Engineering Schedule</span>
      </a>

      <a href="settings" class="nav-link flex items-center space-x-3 hover:bg-red-800 px-3 py-2 rounded-md transition">
        <span class="material-icons">analytics</span>
        <span>Schedule Report</span>
      </a>

      

     

      <a href="logout" class="flex items-center space-x-3 hover:bg-red-800 px-3 py-2 rounded-md transition">
        <span class="material-icons">logout</span>
        <span>Logout</span>
      </a>

    </nav>
  </aside>

  <!-- Overlay for Mobile Sidebar -->
  <div id="overlay" class="fixed inset-0 bg-red-900/50 hidden lg:hidden z-40"></div>

  <!-- Main Content -->
  <main class="flex-1 bg-gray-100 p-8 lg:p-12 overflow-auto h-screen">
    <!-- Mobile menu button -->
    <button id="menuButton" class="lg:hidden text-white bg-red-800/20 hover:bg-red-800/30 p-2 rounded-md transition duration-300 mb-4">
      <span class="material-icons">menu</span> 
    </button>

  <div class="min-h-screen">



   
<!-- JavaScript -->
<script>
  // Dropdown toggle logic
  $("#toggleAccounts").click(function () {
    $("#accountsDropdown").slideToggle(300);
    const icon = $("#accounts_dropdownIcon");
    icon.text(icon.text() === "expand_more" ? "expand_less" : "expand_more");
  });



   

  

  // Mobile menu logic
  const menuButton = document.getElementById('menuButton');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  menuButton.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  });

  // Active URL highlighting including dropdown links
const allLinks = document.querySelectorAll('.nav-link, #accountsDropdown a'); // include dropdown links
const currentPath = window.location.pathname.split("/").pop(); // get current file/page name

allLinks.forEach(link => {
  const linkHref = link.getAttribute('href');

  // Check if current page matches link href
  if (currentPath === linkHref) {
    // Highlight the active link
    link.classList.add('text-[#FFD700]', 'bg-white/10');

    // If it’s inside the Accounts dropdown, open it
    if (link.closest('#accountsDropdown')) {
      const dropdown = document.getElementById('accountsDropdown');
      dropdown.style.display = 'block';
      document.getElementById('accounts_dropdownIcon').textContent = 'expand_less';
    }

    // You can add other dropdowns similarly
  }
});

</script>