<?php
if (!isset($titre)) {
    $titre = "Gestion des Étudiants";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= h($titre) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <h1>Gestion Étudiants</h1>
            <?php if (connecte()): ?>
                <ul>
                    <li><a href="liste.php">Liste des étudiants</a></li>
                    <li><a href="ajouter.php">Ajouter un étudiant</a></li>
                    <li><a href="logout.php">Déconnexion (<?= h($_SESSION['user']) ?>)</a></li>
                </ul>
            <?php endif; ?>
        </nav>
    </header>
    <main class="container">