<body class="bg-gray-100">

<!-- Layout Wrapper -->
<div class="min-h-screen flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <aside id="sidebar" class="pc-sidebar shadow-lg w-64 lg:w-1/5 xl:w-1/6 p-6 space-y-6 lg:static fixed inset-y-0 left-0 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">

    <!-- Sidebar Header -->
    <div class="flex flex-col items-center text-center p-4 rounded-xl shadow-inner bg-white/5 border border-white/10 backdrop-blur-sm">
      <img src="../static/logo.jpg" alt="Logo" class="w-20 h-20 rounded-full border-2 border-white/70 shadow-md transform transition-transform duration-300 hover:scale-105 mb-2">
      <h1 class="text-base sm:text-lg font-bold text-white tracking-wide leading-tight">
        <?=ucfirst($On_Session[0]['user_type'])?>
      </h1>
      <p class="text-xs text-red-100/80 mt-0.5 truncate w-full">
        <?=htmlspecialchars($On_Session[0]['user_username'])?>
      </p>
    </div>

    <!-- Navigation -->
    <nav class="space-y-4 text-left text-white overflow-y-auto h-[calc(100vh-120px)]">

      <a href="view_fac_sched.php?sch_id=<?= !empty($sch_id) ? $sch_id : 'noschedule' ?>"
        class="nav-link flex items-center space-x-3 px-3 py-2 rounded-md transition">
        <span class="material-icons">table_view</span>
        <span>Schedule</span>
      </a>

      <a href="rooms" class="nav-link flex items-center space-x-3 px-3 py-2 rounded-md transition">
        <span class="material-icons">meeting_room</span>
        <span>Available Rooms</span>
      </a>

      <a href="logout" class="flex items-center space-x-3 hover:bg-white/10 px-3 py-2 rounded-md transition">
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

  // Active URL highlighting (file name based, ignoring query string)
  const allLinks = document.querySelectorAll('.nav-link');
  const currentFile = window.location.pathname.split("/").pop().replace(/\.php$/, '');

  allLinks.forEach(link => {
    const href = (link.getAttribute('href') || '').split('?')[0].replace(/\.php$/, '');
    if (currentFile && href === currentFile) {
      link.classList.add('pc-active');
    }
  });
</script>
