<?php
require_once __DIR__ . '/../core/Auth.php';
Auth::init();
$currentUser = Auth::user();
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Xvilo Hosting — Hébergement SA-MP</title>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Russo+One&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90' fill='%23ff0000'>X</text></svg>">
  <link rel="stylesheet" href="/style.css" />
</head>
<body>
  <nav class="navbar">
    <div class="container nav-inner">
      <a href="/" class="logo">
        <img src="https://i.postimg.cc/hGHBgg77/In-Shot-20260616-023140548.jpg" alt="Xvilo Hosting" style="height:44px;width:auto;">
      </a>
      <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
      <ul class="nav-links" id="navLinks">
        <li><a href="/#pricing">Plans</a></li>
        <li><a href="/#features">Features</a></li>
        <li><a href="/#faq">FAQ</a></li>
        <li><a href="#" class="btn btn-discord" onclick="window.open('https://discord.gg/E9HjMePMsalBUEHScBfiw4','_blank');return false;">Support</a></li>
        <?php if ($currentUser): ?>
          <li><a href="/dashboard.php">Dashboard</a></li>
          <li><span style="color:var(--accent);font-size:13px;"><?= htmlspecialchars($currentUser['name']) ?></span></li>
          <li><a href="/auth/logout.php" class="btn btn-discord">Déconnexion</a></li>
        <?php else: ?>
          <li><a href="/auth/login.php">Connexion</a></li>
          <li><a href="/auth/register.php" class="btn btn-primary" style="padding:8px 18px;">S'inscrire</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
