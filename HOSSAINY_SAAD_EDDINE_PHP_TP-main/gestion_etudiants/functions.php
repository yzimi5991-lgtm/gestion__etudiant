<?php
/**
 * Fonctions communes de l'application Gestion des étudiants
 */

// Constantes requises (Exercice 2)
define('FICHIER', __DIR__ . '/data/etudiants.txt');
define('FILIERES', ['GI', 'IDIA', 'GE', 'GC']);
define('UTILISATEURS', [
    'admin'    => 'admin123',
    'prof'     => 'prof123',
    'etudiant' => 'etu123'
]);

// Démarrage de session (Exercice 2)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Protège une chaîne pour l'affichage HTML (Exercice 2)
 */
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si l'utilisateur est connecté (Exercice 2)
 */
function connecte(): bool {
    return !empty($_SESSION['login']);
}

/**
 * Exige une connexion active, sinon redirige vers index.php (Exercice 2)
 */
function exiger_connexion(): void {
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Supprime les espaces inutiles, points-virgules et retours à la ligne (Exercice 3)
 */
function nettoyer(string $s): string {
    return str_replace([';', "\r", "\n"], '', trim($s));
}

/**
 * Lit le fichier etudiants.txt et retourne un tableau associatif indexé par l'id (Exercice 3)
 * Format : id;nom;prenom;filiere;note;photo;email
 */
function lire_etudiants(): array {
    if (!file_exists(FICHIER)) {
        return [];
    }

    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lignes === false) {
        return [];
    }

    $etudiants = [];
    foreach ($lignes as $ligne) {
        $ligne = trim($ligne);
        if ($ligne === '') {
            continue;
        }

        $parts = explode(';', $ligne);
        if (count($parts) < 6) {
            continue;
        }

        $id = (int)$parts[0];
        $etudiants[$id] = [
            'id'      => $id,
            'nom'     => $parts[1] ?? '',
            'prenom'  => $parts[2] ?? '',
            'filiere' => $parts[3] ?? '',
            'note'    => isset($parts[4]) ? (float)$parts[4] : 0.0,
            'photo'   => $parts[5] ?? '',
            'email'   => $parts[6] ?? '' // Champ Bonus
        ];
    }

    return $etudiants;
}

/**
 * Reconstruit les lignes avec implode() et les écrit avec file_put_contents() (Exercice 3)
 */
function sauver_etudiants(array $liste): void {
    $dossier = dirname(FICHIER);
    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }

    $lignes = [];
    foreach ($liste as $e) {
        $lignes[] = implode(';', [
            (int)$e['id'],
            $e['nom'],
            $e['prenom'],
            $e['filiere'],
            $e['note'],
            $e['photo'] ?? '',
            $e['email'] ?? ''
        ]);
    }

    $contenu = !empty($lignes) ? implode(PHP_EOL, $lignes) . PHP_EOL : '';
    file_put_contents(FICHIER, $contenu);
}

/**
 * Récupère le thème actif depuis le cookie (Bonus)
 */
function get_theme(): string {
    return (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'sombre') ? 'sombre' : 'clair';
}


