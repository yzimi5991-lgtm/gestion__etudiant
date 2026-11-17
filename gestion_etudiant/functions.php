<?php
// Démarrage de la session
session_start();

// Constantes du projet
define('FICHIER', __DIR__ . '/data/etudiants.txt');
define('FILIERES', ['GI', 'IDIA', 'GE', 'GC']);
define('UTILISATEURS', [
    'admin' => 'password123',
    'sara' => 'sara2026'
]);

/**
 * Protège l'affichage contre les failles XSS
 */
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function connecte(): bool {
    return isset($_SESSION['user']);
}

/**
 * Exige la connexion, sinon redirige vers la page d'accueil (connexion)
 */
function exiger_connexion(): void {
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Nettoie une chaîne de caractères (supprime espaces, points-virgules et retours à la ligne)
 */
function nettoyer(string $s): string {
    return trim(str_replace([';', "\r", "\n"], '', $s));
}

/**
 * Lit les étudiants depuis le fichier texte et retourne un tableau associatif indexé par ID
 */
function lire_etudiants(): array {
    if (!file_exists(FICHIER)) {
        return [];
    }
    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $etudiants = [];
    foreach ($lignes as $ligne) {
        $parties = explode(';', $ligne);
        if (count($parties) === 6) {
            $id = (int)$parties[0];
            $etudiants[$id] = [
                'id' => $id,
                'nom' => $parties[1],
                'prenom' => $parties[2],
                'filiere' => $parties[3],
                'note' => (float)$parties[4],
                'photo' => $parties[5]
            ];
        }
    }
    return $etudiants;
}

/**
 * Sauvegarde le tableau des étudiants dans le fichier texte
 */
function sauver_etudiants(array $liste): void {
    $lignes = [];
    foreach ($liste as $etudiant) {
        $lignes[] = implode(';', [
            $etudiant['id'],
            $etudiant['nom'],
            $etudiant['prenom'],
            $etudiant['filiere'],
            $etudiant['note'],
            $etudiant['photo']
        ]);
    }
    file_put_contents(FICHIER, implode(PHP_EOL, $lignes) . (empty($lignes) ? '' : PHP_EOL));
}