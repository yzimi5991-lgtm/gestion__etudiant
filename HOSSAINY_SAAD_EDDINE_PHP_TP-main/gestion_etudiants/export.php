<?php
/**
 * Export de la liste des étudiants au format CSV (Bonus)
 */
require_once __DIR__ . '/functions.php';

// Protection de la page
exiger_connexion();

$etudiants = lire_etudiants();

// En-têtes HTTP pour forcer le téléchargement du fichier CSV
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="etudiants_' . date('Ymd_His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Ajout du BOM UTF-8 pour ouverture correcte dans Microsoft Excel
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Ligne d'en-tête CSV
fputcsv($output, ['ID', 'Nom', 'Prenom', 'Filiere', 'Note', 'Photo', 'Email'], ';');

// Écriture des données
foreach ($etudiants as $e) {
    fputcsv($output, [
        $e['id'],
        $e['nom'],
        $e['prenom'],
        $e['filiere'],
        number_format($e['note'], 2, '.', ''),
        $e['photo'] ?? '',
        $e['email'] ?? ''
    ], ';');
}

fclose($output);
exit;


