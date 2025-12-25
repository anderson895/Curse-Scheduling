<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="static/favicon.ico">
  <link href="./src/output.css" rel="stylesheet" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <title>Course Scheduling System</title>
</head>
<body class="bg-gray-100 text-red-900">

<!-- Navbar -->
<nav class=" fixed w-full z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">

    <!-- Mobile Hamburger Menu Button -->
    <div class="md:hidden">
      <button id="menu-toggle" class="text-white focus:outline-none focus:ring-2 focus:ring-red-800 rounded">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>
</nav>

<!-- Fullscreen Mobile Menu -->
<div id="mobile-menu" class="md:hidden fixed inset-0 bg-red-900 text-white hidden z-40 flex flex-col items-center justify-center space-y-6 px-4 transition duration-300 ease-in-out">

  <!-- Close Button -->
  <button id="close-menu" class="absolute top-5 right-5 text-white hover:text-red-300 text-3xl focus:outline-none">
    &times;
  </button>

  <!-- Logo and Title -->
  <div class="flex flex-col items-center space-y-2 mb-4">
    <img src="static/logo.jpg" alt="Logo" class="w-20 h-20 rounded-full border-2 border-white shadow">
    <h2 class="text-2xl font-extrabold tracking-wide text-white">GrillBook</h2>
  </div>

  <!-- Navigation Buttons -->
  <a href="login" class="w-full max-w-xs bg-white text-red-900 text-lg py-2 rounded-full font-bold text-center hover:bg-gray-200 transition">Login</a>
  <a href="register" class="w-full max-w-xs bg-white text-red-900 text-lg py-2 rounded-full font-bold text-center hover:bg-gray-200 transition">Register</a>
</div>

<!-- JS toggle -->
<script>
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-menu');
  const closeBtn = document.getElementById('close-menu');

  toggle.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });

  closeBtn.addEventListener('click', () => {
    menu.classList.add('hidden');
  });
</script>
