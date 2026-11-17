<?php
/**
 * Annuaire Pédagogique des Étudiants (Exercices 6 et 7)
 */
require_once __DIR__ . '/functions.php';

// Protection de la page (Exercice 6)
exiger_connexion();

// Récupération et suppression du message flash (Exercice 6)
$message_flash = $_SESSION['flash'] ?? null;
if (isset($_SESSION['flash'])) {
    unset($_SESSION['flash']);
}

// Lecture des étudiants depuis le fichier (Exercice 3)
$etudiants = lire_etudiants();

// Recherche GET 'q' (Exercice 7)
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q !== '') {
    $etudiants = array_filter($etudiants, function($e) use ($q) {
        return (stripos($e['nom'], $q) !== false) || (stripos($e['prenom'], $q) !== false);
    });
}

// Tri GET 'tri' (Exercice 7)
$tris_valides = ['ajout', 'nom', 'filiere', 'note'];
$tri = isset($_GET['tri']) && in_array($_GET['tri'], $tris_valides) ? $_GET['tri'] : 'ajout';

if ($tri === 'nom') {
    usort($etudiants, function($a, $b) {
        return strcasecmp($a['nom'], $b['nom']);
    });
} elseif ($tri === 'filiere') {
    usort($etudiants, function($a, $b) {
        return strcasecmp($a['filiere'], $b['filiere']);
    });
} elseif ($tri === 'note') {
    usort($etudiants, function($a, $b) {
        return $b['note'] <=> $a['note']; // Décroissant
    });
} else {
    // Par ordre d'ajout (identifiant croissant)
    usort($etudiants, function($a, $b) {
        return $a['id'] <=> $b['id'];
    });
}

// Calcul des indicateurs pédagogiques (Exercice 7)
$nb_affiches = count($etudiants);
if ($nb_affiches > 0) {
    $colonne_notes = array_column($etudiants, 'note');
    $somme_notes = array_sum($colonne_notes);
    $moyenne_generale = $somme_notes / $nb_affiches;
    $meilleure_note = max($colonne_notes);
} else {
    $moyenne_generale = 0.0;
    $meilleure_note = null;
}

// Moyennes par filière d'ingénieur (Bonus)
$stats_filieres = [];
$labels_filieres = [
    'GI'   => 'Génie Informatique',
    'IDIA' => 'Ingénierie des Données & IA',
    'GE'   => 'Génie Électrique',
    'GC'   => 'Génie Civil'
];

foreach (FILIERES as $f) {
    $notes_f = [];
    foreach ($etudiants as $e) {
        if ($e['filiere'] === $f) {
            $notes_f[] = $e['note'];
        }
    }
    if (!empty($notes_f)) {
        $stats_filieres[$f] = [
            'count'   => count($notes_f),
            'moyenne' => array_sum($notes_f) / count($notes_f),
            'label'   => $labels_filieres[$f] ?? $f
        ];
    }
}

$titre = "Annuaire Pédagogique &bull; Gestion des Étudiants";
include 'header.php';
?>

<div class="app-breadcrumb">
    <span>Accueil</span> &rsaquo; 
    <span>Portail Académique</span> &rsaquo; 
    <span class="active">Annuaire des Étudiants</span>
</div>

<div class="app-section-header">
    <div class="header-text-block">
        <span class="section-tag">Gestion Académique</span>
        <h1 class="section-title">Annuaire &amp; Délibérations des Étudiants</h1>
        <p class="section-subtitle">
            Suivi des dossiers académiques, filières d'ingénieurs et résultats d'évaluation des étudiants.
        </p>
    </div>
    <div class="header-actions-block">
        <a href="ajouter.php" class="app-btn app-btn-primary">
            <span>➕ Nouvelle Inscription</span>
        </a>
        <a href="export.php" class="app-btn app-btn-secondary">
            <span>📥 Exporter en CSV</span>
        </a>
    </div>
</div>

<?php if ($message_flash): ?>
    <div class="app-alert app-alert-success" role="alert">
        <span class="alert-icon">✅</span>
        <span><?= h($message_flash) ?></span>
    </div>
<?php endif; ?>

<!-- Grille des statistiques (Exercice 7) -->
<section class="app-stats-grid">
    <div class="app-stat-card">
        <div class="stat-icon-box stat-icon-amber">
            <span>🎓</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Inscrits Répertoriés</span>
            <div class="stat-number">
                <?= $nb_affiches ?> <span class="stat-unit">étudiant(s)</span>
            </div>
        </div>
    </div>

    <div class="app-stat-card">
        <div class="stat-icon-box stat-icon-slate">
            <span>📈</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Moyenne Générale</span>
            <div class="stat-number">
                <?= ($nb_affiches > 0) ? number_format($moyenne_generale, 2) : '0.00' ?>
                <span class="stat-unit">/ 20</span>
            </div>
        </div>
    </div>

    <div class="app-stat-card">
        <div class="stat-icon-box stat-icon-gold">
            <span>🏆</span>
        </div>
        <div class="stat-content">
            <span class="stat-label">Meilleure Note</span>
            <div class="stat-number">
                <?= ($meilleure_note !== null) ? number_format($meilleure_note, 2) : 'N/A' ?>
                <span class="stat-unit">/ 20</span>
            </div>
        </div>
    </div>
</section>

<!-- Moyenne des notes par filière (Bonus) -->
<?php if (!empty($stats_filieres)): ?>
<div class="app-card app-filieres-box">
    <div class="filieres-bar-title">
        <span class="bar-icon">🏛️</span>
        <strong>Filières d'ingénieurs &bull; Répartition et moyennes des notes :</strong>
    </div>
    <div class="filieres-pills-row">
        <?php foreach ($stats_filieres as $filiere_code => $f_info): ?>
            <div class="filiere-pill" title="<?= h($f_info['label']) ?>">
                <span class="badge-filiere badge-<?= strtolower(h($filiere_code)) ?>"><?= h($filiere_code) ?></span>
                <span class="filiere-meta">
                    <span class="filiere-effectif"><?= $f_info['count'] ?> étu.</span>
                    <span class="filiere-moyenne <?= ($f_info['moyenne'] >= 10) ? 'text-success' : 'text-danger' ?>">
                        Moy. <strong><?= number_format($f_info['moyenne'], 2) ?>/20</strong>
                    </span>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Formulaire de recherche et tri (Exercice 7) -->
<div class="app-card filter-card">
    <form method="GET" action="liste.php" class="app-filter-form">
        <div class="filter-col filter-search">
            <label for="search-input" class="filter-label">Recherche Pédagogique</label>
            <div class="input-icon-box">
                <span class="input-prefix-icon">🔍</span>
                <input
                    type="text"
                    id="search-input"
                    name="q"
                    class="form-control"
                    placeholder="Filtrer en direct par nom ou prénom..."
                    value="<?= h($q) ?>"
                    autocomplete="off"
                >
            </div>
        </div>

        <div class="filter-col filter-sort">
            <label for="tri-select" class="filter-label">Ordre d'Affichage</label>
            <select id="tri-select" name="tri" class="form-control">
                <option value="ajout" <?= ($tri === 'ajout') ? 'selected' : '' ?>>Ordre d'ajout (ID)</option>
                <option value="nom" <?= ($tri === 'nom') ? 'selected' : '' ?>>Nom (Alphabétique)</option>
                <option value="filiere" <?= ($tri === 'filiere') ? 'selected' : '' ?>>Filière d'ingénieur</option>
                <option value="note" <?= ($tri === 'note') ? 'selected' : '' ?>>Note décroissante (Mérite)</option>
            </select>
        </div>

        <div class="filter-col filter-actions">
            <button type="submit" class="app-btn app-btn-primary">Filtrer</button>
            <?php if ($q !== '' || $tri !== 'ajout'): ?>
                <a href="liste.php" class="app-btn app-btn-link">Réinitialiser</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tableau des étudiants (Exercice 6) -->
<div class="app-card table-card">
    <div class="table-card-header">
        <div class="table-header-title">Registre des Inscriptions Académiques</div>
        <span class="table-badge"><?= $nb_affiches ?> étudiant(s)</span>
    </div>
    <div class="table-responsive">
        <table class="table table-etudiants">
            <thead>
                <tr>
                    <th style="width: 70px;">Photo</th>
                    <th>Nom (Majuscules)</th>
                    <th>Prénom</th>
                    <th>Filière</th>
                    <th>Note</th>
                    <th>Email Institutionnel</th>
                    <th class="text-right" style="min-width: 170px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($etudiants)): ?>
                    <tr class="empty-row">
                        <td colspan="7" class="text-center empty-cell">
                            <div class="empty-state">
                                <span class="empty-icon">📁</span>
                                <p>Aucun étudiant ne correspond aux critères.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($etudiants as $etu): ?>
                        <?php
                            $classe_note = ($etu['note'] >= 10) ? 'note-reussie' : 'note-echec';
                            $mention = ($etu['note'] >= 10) ? 'Admis' : 'Ajourné';
                            $photo_file = !empty($etu['photo']) ? 'uploads/' . $etu['photo'] : '';
                            $has_photo = !empty($etu['photo']) && file_exists(__DIR__ . '/' . $photo_file);
                        ?>
                        <tr>
                            <td class="col-photo">
                                <?php if ($has_photo): ?>
                                    <img src="<?= h($photo_file) ?>" alt="Photo de <?= h($etu['prenom']) ?>" class="photo-ronde" loading="lazy">
                                <?php else: ?>
                                    <div class="photo-placeholder" title="Pas de photo">
                                        <?= strtoupper(substr($etu['prenom'], 0, 1) . substr($etu['nom'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="col-nom">
                                <span class="student-id">#<?= (int)$etu['id'] ?></span>
                                <strong class="student-lastname"><?= strtoupper(h($etu['nom'])) ?></strong>
                            </td>
                            <td class="col-prenom">
                                <span class="student-firstname"><?= ucfirst(h($etu['prenom'])) ?></span>
                            </td>
                            <td class="col-filiere">
                                <span class="badge-filiere badge-<?= strtolower(h($etu['filiere'])) ?>" title="<?= h($labels_filieres[$etu['filiere']] ?? $etu['filiere']) ?>">
                                    <?= h($etu['filiere']) ?>
                                </span>
                            </td>
                            <td class="col-note">
                                <div class="note-box">
                                    <span class="badge-note <?= $classe_note ?>">
                                        <?= number_format($etu['note'], 2) ?> / 20
                                    </span>
                                    <span class="note-status-sub <?= ($etu['note'] >= 10) ? 'text-success' : 'text-danger' ?>">
                                        <?= $mention ?>
                                    </span>
                                </div>
                            </td>
                            <td class="col-email">
                                <?php if (!empty($etu['email'])): ?>
                                    <a href="mailto:<?= h($etu['email']) ?>" class="student-email-link"><?= h($etu['email']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td class="col-actions text-right">
                                <a href="ajouter.php?id=<?= (int)$etu['id'] ?>" class="btn-action btn-edit" title="Modifier la fiche">
                                    ✏️ Modifier
                                </a>
                                <a href="supprimer.php?id=<?= (int)$etu['id'] ?>" class="btn-action btn-delete btn-confirm-delete" title="Supprimer l'étudiant">
                                    🗑️ Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

