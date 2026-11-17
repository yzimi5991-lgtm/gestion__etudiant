<?php
/**
 * Formulaire académique d'inscription / modification (Exercices 8, 9, 10 et Bonus)
 */
require_once __DIR__ . '/functions.php';

// Protection de la page
exiger_connexion();

// Détection du mode : Modification (si 'id' présent) ou Inscription (Exercice 10)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = ($id > 0);

$liste = lire_etudiants();

// Si mode modification, vérifier que l'étudiant existe
if ($is_edit && !isset($liste[$id])) {
    $_SESSION['flash'] = "L'étudiant demandé n'a pas été trouvé dans le registre.";
    header('Location: liste.php');
    exit;
}

// Initialisation des champs
$nom            = $is_edit ? $liste[$id]['nom'] : '';
$prenom         = $is_edit ? $liste[$id]['prenom'] : '';
$filiere        = $is_edit ? $liste[$id]['filiere'] : (FILIERES[0] ?? 'GI');
$note           = $is_edit ? (string)$liste[$id]['note'] : '';
$email          = $is_edit ? ($liste[$id]['email'] ?? '') : '';
$photo_actuelle = $is_edit ? ($liste[$id]['photo'] ?? '') : '';

$erreurs = [];

// Traitement du formulaire POST (Exercice 8)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = isset($_POST['nom']) ? nettoyer($_POST['nom']) : '';
    $prenom  = isset($_POST['prenom']) ? nettoyer($_POST['prenom']) : '';
    $filiere = isset($_POST['filiere']) ? nettoyer($_POST['filiere']) : '';
    $note    = isset($_POST['note']) ? trim($_POST['note']) : '';
    $email   = isset($_POST['email']) ? nettoyer($_POST['email']) : '';

    // Contrôles côté serveur (Exercice 8)
    if ($nom === '') {
        $erreurs[] = "Le nom de l'étudiant est obligatoire.";
    }

    if ($prenom === '') {
        $erreurs[] = "Le prénom de l'étudiant est obligatoire.";
    }

    if (!in_array($filiere, FILIERES)) {
        $erreurs[] = "La filière d'ingénieur choisie n'est pas valide.";
    }

    $note_normalisee = str_replace(',', '.', $note);
    if (!is_numeric($note_normalisee) || (float)$note_normalisee < 0 || (float)$note_normalisee > 20) {
        $erreurs[] = "La note doit être un nombre décimal compris entre 0.00 et 20.00.";
    }

    // Validation email avec filter_var() (Bonus)
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'adresse email saisie n'est pas valide.";
    }

    // Unicité du couple (nom + prénom) (Bonus)
    foreach ($liste as $existing_id => $etu) {
        if ($is_edit && (int)$existing_id === $id) {
            continue;
        }
        if (strcasecmp($etu['nom'], $nom) === 0 && strcasecmp($etu['prenom'], $prenom) === 0) {
            $erreurs[] = "Un étudiant portant déjà le nom « " . h($nom) . " » et le prénom « " . h($prenom) . " » existe déjà.";
            break;
        }
    }

    // Téléversement de la photo (Exercice 9)
    $photo_nom = $photo_actuelle;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['photo']['tmp_name'];
            $file_size = $_FILES['photo']['size'];
            $file_ext  = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

            if (!in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
                $erreurs[] = "Format de photo non autorisé (seuls .jpg, .jpeg et .png sont acceptés).";
            } elseif ($file_size > 1048576) {
                $erreurs[] = "La taille de la photo ne doit pas dépasser 1 Mo.";
            } else {
                $uploads_dir = __DIR__ . '/uploads/';
                if (!is_dir($uploads_dir)) {
                    mkdir($uploads_dir, 0777, true);
                }

                $nouveau_nom = uniqid('etu_') . '.' . $file_ext;
                $destination = $uploads_dir . $nouveau_nom;

                if (move_uploaded_file($file_tmp, $destination)) {
                    // Supprimer l'ancienne photo du disque si elle existait (Exercice 10)
                    if (!empty($photo_actuelle)) {
                        $ancienne = $uploads_dir . $photo_actuelle;
                        if (file_exists($ancienne)) {
                            unlink($ancienne);
                        }
                    }
                    $photo_nom = $nouveau_nom;
                } else {
                    $erreurs[] = "Erreur lors de l'enregistrement de la photo sur le serveur.";
                }
            }
        } else {
            $erreurs[] = "Erreur lors de l'envoi du fichier photo (Code " . (int)$_FILES['photo']['error'] . ").";
        }
    }

    // Si aucune erreur, enregistrement
    if (empty($erreurs)) {
        if ($is_edit) {
            $liste[$id] = [
                'id'      => $id,
                'nom'     => $nom,
                'prenom'  => $prenom,
                'filiere' => $filiere,
                'note'    => (float)$note_normalisee,
                'photo'   => $photo_nom,
                'email'   => $email
            ];
            $_SESSION['flash'] = "La fiche pédagogique de l'étudiant « " . ucfirst($prenom) . " " . strtoupper($nom) . " » a été modifiée avec succès.";
        } else {
            $nouvel_id = empty($liste) ? 1 : max(array_keys($liste)) + 1;
            $liste[$nouvel_id] = [
                'id'      => $nouvel_id,
                'nom'     => $nom,
                'prenom'  => $prenom,
                'filiere' => $filiere,
                'note'    => (float)$note_normalisee,
                'photo'   => $photo_nom,
                'email'   => $email
            ];
            $_SESSION['flash'] = "L'étudiant « " . ucfirst($prenom) . " " . strtoupper($nom) . " » a été inscrit avec succès.";
        }

        sauver_etudiants($liste);
        header('Location: liste.php');
        exit;
    }
}

$labels_filieres = [
    'GI'   => 'Génie Informatique (GI)',
    'IDIA' => 'Ingénierie des Données & IA (IDIA)',
    'GE'   => 'Génie Électrique (GE)',
    'GC'   => 'Génie Civil (GC)'
];

$titre = $is_edit ? "Modifier une fiche &bull; Portail Académique" : "Nouvelle Inscription &bull; Portail Académique";
include 'header.php';
?>

<div class="app-breadcrumb">
    <span>Accueil</span> &rsaquo; 
    <a href="liste.php">Annuaire des Étudiants</a> &rsaquo; 
    <span class="active"><?= $is_edit ? "Fiche Étudiant #" . $id : "Nouvelle Inscription" ?></span>
</div>

<div class="app-form-wrapper">
    <div class="app-card form-main-card">
        <div class="form-title-bar">
            <div>
                <h1 class="card-title"><?= $is_edit ? "✏️ Modification de la Fiche Pédagogique" : "➕ Fiche Pédagogique d'Inscription" ?></h1>
                <p class="card-subtitle">
                    <?= $is_edit ? "Mise à jour des coordonnées et de la note de l'étudiant ID #" . $id : "Formulaire d'enregistrement d'un étudiant au registre académique." ?>
                </p>
            </div>
            <span class="form-inst-tag">Cycle d'Ingénieurs</span>
        </div>

        <?php if (!empty($erreurs)): ?>
            <div class="app-alert app-alert-danger" role="alert">
                <div class="alert-title"><strong>⚠️ Veuillez corriger les erreurs suivantes :</strong></div>
                <ul class="error-list">
                    <?php foreach ($erreurs as $err): ?>
                        <li><?= h($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $is_edit ? 'ajouter.php?id=' . $id : 'ajouter.php' ?>" enctype="multipart/form-data" class="app-form" novalidate>
            
            <div class="form-fieldset">
                <div class="fieldset-header">
                    <span class="fieldset-icon">👤</span>
                    <span class="fieldset-title">1. État Civil &amp; Identité</span>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nom" class="form-label">Nom de famille <span class="required">*</span></label>
                        <input
                            type="text"
                            id="nom"
                            name="nom"
                            class="form-control"
                            placeholder="Ex: Benali"
                            value="<?= h($nom) ?>"
                            required
                        >
                        <small class="field-hint">Enregistré et mis en majuscules automatiquement.</small>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="prenom" class="form-label">Prénom <span class="required">*</span></label>
                        <input
                            type="text"
                            id="prenom"
                            name="prenom"
                            class="form-control"
                            placeholder="Ex: Sara"
                            value="<?= h($prenom) ?>"
                            required
                        >
                        <small class="field-hint">Première lettre mise en majuscule.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Institutionnel (Bonus)</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        placeholder="Ex: s.benali@etudiant.ma"
                        value="<?= h($email) ?>"
                    >
                </div>
            </div>

            <div class="form-fieldset">
                <div class="fieldset-header">
                    <span class="fieldset-icon">🎓</span>
                    <span class="fieldset-title">2. Cursus &amp; Évaluation Académique</span>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="filiere" class="form-label">Filière d'Ingénieur <span class="required">*</span></label>
                        <select id="filiere" name="filiere" class="form-control" required>
                            <?php foreach (FILIERES as $fil): ?>
                                <option value="<?= h($fil) ?>" <?= ($filiere === $fil) ? 'selected' : '' ?>>
                                    <?= h($labels_filieres[$fil] ?? $fil) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="note" class="form-label">Note d'Évaluation (sur 20) <span class="required">*</span></label>
                        <input
                            type="number"
                            id="note"
                            name="note"
                            class="form-control"
                            step="0.01"
                            min="0"
                            max="20"
                            placeholder="Ex: 14.50"
                            value="<?= h($note) ?>"
                            required
                        >
                        <small class="field-hint">Note de validation : &ge; 10.00 / 20.</small>
                    </div>
                </div>
            </div>

            <div class="form-fieldset">
                <div class="fieldset-header">
                    <span class="fieldset-icon">📷</span>
                    <span class="fieldset-title">3. Photo d'Identité de l'Étudiant</span>
                </div>

                <div class="form-group">
                    <label for="photo" class="form-label">Fichier photo (facultatif, max 1 Mo)</label>
                    <input
                        type="file"
                        id="photo"
                        name="photo"
                        class="form-control-file"
                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                    >
                    <small class="field-hint">Formats acceptés : JPG, JPEG, PNG.</small>

                    <?php if ($is_edit && !empty($photo_actuelle) && file_exists(__DIR__ . '/uploads/' . $photo_actuelle)): ?>
                        <div class="current-photo-card">
                            <span class="photo-info-label">Photo actuelle :</span>
                            <div class="photo-info-body">
                                <img src="uploads/<?= h($photo_actuelle) ?>" alt="Photo de l'étudiant" class="photo-ronde">
                                <code><?= h($photo_actuelle) ?></code>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-actions-row">
                <button type="submit" class="app-btn app-btn-primary">
                    <?= $is_edit ? '💾 Enregistrer les Modifications' : '➕ Valider l\'Inscription' ?>
                </button>
                <a href="liste.php" class="app-btn app-btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>


