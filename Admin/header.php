<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($page_title ?? 'SignagePro') ?></title>
<link rel="stylesheet" href="style.css">
<script>if(localStorage.getItem('signage-theme')==='dark')document.documentElement.classList.add('dark-preload');</script>
</head>
<body>
<div class="app">
<aside class="sidebar">
  <div class="sidebar-logo"><img class="logo-img" src="bridge-logo.png" alt="Bridge Advertising & Printing"></div>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="quotation_create.php">New Quotation</a>
    <a href="quotations.php">Quotations</a>
    <a href="customers.php">Customers</a>
    <a href="materials.php">Materials & Prices</a>
    <a href="settings.php">Company Settings</a>
  </nav>
  <div class="side-bottom"><small><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></small><a href="logout.php">Logout</a></div>
</aside>
<main class="main">
<header class="topbar">
  <div><strong><?= htmlspecialchars($page_title ?? 'Dashboard') ?></strong></div>
  <div class="topbar-actions">
    <?php if (($page_title ?? '') === 'Dashboard'): ?>
      <button type="button" id="themeToggle" class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle light and dark mode">
        <span id="themeIcon">🌙</span><span id="themeText">Dark Mode</span>
      </button>
    <?php endif; ?>
    <div class="user"><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></div>
  </div>
</header>
<div class="content">
<script>
(function(){
  const saved=localStorage.getItem('signage-theme');
  if(saved==='dark') document.body.classList.add('dark');
  updateThemeButton();
})();
function toggleTheme(){
  const isDark=document.body.classList.toggle('dark');
  localStorage.setItem('signage-theme',isDark?'dark':'light');
  updateThemeButton();
}
function updateThemeButton(){
  const dark=document.body.classList.contains('dark');
  const icon=document.getElementById('themeIcon'), text=document.getElementById('themeText');
  if(icon) icon.textContent=dark?'☀️':'🌙';
  if(text) text.textContent=dark?'Light Mode':'Dark Mode';
}
</script>

