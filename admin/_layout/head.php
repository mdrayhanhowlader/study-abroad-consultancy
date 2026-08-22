<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$adminUser = App\Auth::user();
$adminPage = $adminPage ?? '';
$site = App\DataAdapter::site();

/* Compute admin base path for assets so CSS/JS load from any subdirectory */
$adminBase = '';
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/admin/dashboard.php';
$relPath = preg_replace('#^.*?/admin/#', '', $scriptPath) ?? '';
$depth = substr_count($relPath, '/');
if ($depth > 0) {
    $adminBase = str_repeat('../', $depth);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#006837">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Manami Admin">
  <title><?= h($pageTitle ?? 'Admin') ?> — Manami Education</title>
  <meta name="robots" content="noindex, nofollow">
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            paper:    '#FAF8F4', 'paper-2': '#F4F1EA',
            ink:      { DEFAULT: '#1E242B', 2: '#4A5159', 3: '#8A857C' },
            hair:     { DEFAULT: '#E5E1D9', 2: '#D7D2C7' },
            brand:    { DEFAULT: '#006837', dark: '#004d28', light: '#cce8d6' },
            navy:     { DEFAULT: '#1A2B6D', dark: '#101f4f', light: '#d6dcec' },
            accent:   { DEFAULT: '#E31E24', dark: '#a8131a', light: '#f7c8ca' },
            gold:     '#B8923B',
          },
          fontFamily: {
            display: ['"Playfair Display"', 'Georgia', 'serif'],
            sans:    ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
          },
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,500;1,600;1,700&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="<?= h($adminBase) ?>assets/css/style.css" rel="stylesheet">
  <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>
</head>
<body class="font-sans bg-paper text-ink antialiased">
<div class="min-h-screen flex" x-data="{ sideOpen: false }" @click.away="sideOpen=false">
