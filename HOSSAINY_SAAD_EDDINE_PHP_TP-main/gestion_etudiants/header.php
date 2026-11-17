<?php
/**
 * En-tête HTML commun - Portail Pédagogique Universitaire (Exercice 2)
 */
require_once __DIR__ . '/functions.php';
$theme = get_theme();
?>
<!DOCTYPE html>
<html lang="fr" data-theme="<?= h($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titre) ? h($titre) : 'Portail Académique - Gestion des étudiants' ?></title>
    <!-- Police officielle Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>

<!-- En-tête institutionnel académique -->
<header class="app-main-header">
    <div class="app-top-bar">
        <div class="app-branding-block">
            <a href="liste.php" class="brand-crest-box" title="Portail Académique des Étudiants">
                <div class="brand-crest-icon">🎓</div>
            </a>
            <div class="app-title-box">
                <span class="app-univ-name">Plateforme Pédagogique Universitaire</span>
                <span class="app-school-name">Portail de Gestion des Étudiants</span>
            </div>
        </div>

        <div class="app-top-right">
            <div class="app-year-pill">
                <span class="year-dot"></span>
                <span>Année Universitaire <strong>2026 &ndash; 2027</strong></span>
            </div>
            <button id="theme-toggle" class="app-theme-btn" type="button" title="Changer le thème (Clair / Sombre)" aria-label="Changer le thème">
                <span class="theme-icon">🌓</span>
            </button>
        </div>
    </div>

    <?php if (connecte()): ?>
    <!-- Barre de navigation académique (Exercice 2) -->
    <nav class="app-nav-bar" aria-label="Navigation principale">
        <div class="app-nav-container">
            <ul class="app-site-nav">
                <li class="app-nav-item <?= (basename($_SERVER['PHP_SELF']) === 'liste.php') ? 'app-active' : '' ?>">
                    <a href="liste.php">
                        <span class="nav-icon">📋</span>
                        <span>Annuaire des Étudiants</span>
                    </a>
                </li>
                <li class="app-nav-item <?= (basename($_SERVER['PHP_SELF']) === 'ajouter.php' && !isset($_GET['id'])) ? 'app-active' : '' ?>">
                    <a href="ajouter.php">
                        <span class="nav-icon">➕</span>
                        <span>Nouvelle Inscription</span>
                    </a>
                </li>
                <li class="app-nav-item">
                    <a href="export.php">
                        <span class="nav-icon">📥</span>
                        <span>Exporter CSV</span>
                    </a>
                </li>
            </ul>

            <div class="app-user-box">
                <div class="app-user-tag">
                    <span class="user-status-dot"></span>
                    <span>Utilisateur : <strong><?= h($_SESSION['login'] ?? '') ?></strong></span>
                </div>
                <a href="logout.php" class="app-logout-btn" title="Fermer la session">
                    Déconnexion
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
</header>

<main class="main-wrapper">


