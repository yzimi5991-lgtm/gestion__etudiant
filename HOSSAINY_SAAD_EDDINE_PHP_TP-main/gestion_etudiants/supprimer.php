<?php
/**
 * Suppression d'un étudiant (Exercice 10)
 */
require_once __DIR__ . '/functions.php';

// Protection de la page
exiger_connexion();

// Conversion de l'identifiant reçu en entier (Exercice 10)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$liste = lire_etudiants();

if ($id > 0 && isset($liste[$id])) {
    $etu = $liste[$id];
    $nom_complet = ucfirst($etu['prenom']) . ' ' . strtoupper($etu['nom']);

    // Suppression de la photo sur le disque si elle existe (Exercice 10)
    if (!empty($etu['photo'])) {
        $photo_path = __DIR__ . '/uploads/' . $etu['photo'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }

    // Retirer l'étudiant du tableau avec unset() (Exercice 10)
    unset($liste[$id]);

    // Réécriture du fichier texte (Exercice 3)
    sauver_etudiants($liste);

    // Message flash de confirmation
    $_SESSION['flash'] = "L'étudiant « " . $nom_complet . " » a été supprimé avec succès.";
} else {
    $_SESSION['flash'] = "Impossible de supprimer : étudiant introuvable.";
}

// Redirection vers la liste
header('Location: liste.php');
exit;


