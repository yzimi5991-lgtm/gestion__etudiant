<?php
require_once 'functions.php';
exiger_connexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$etudiants = lire_etudiants();

if (isset($etudiants[$id])) {
    // Supprimer la photo physique si elle existe
    if (!empty($etudiants[$id]['photo']) && file_exists('uploads/' . $etudiants[$id]['photo'])) {
        unlink('uploads/' . $etudiants[$id]['photo']);
    }
    unset($etudiants[$id]);
    sauver_etudiants($etudiants);
    $_SESSION['flash'] = "Étudiant supprimé avec succès.";
} else {
    $_SESSION['flash'] = "Étudiant introuvable.";
}

header('Location: liste.php');
exit;