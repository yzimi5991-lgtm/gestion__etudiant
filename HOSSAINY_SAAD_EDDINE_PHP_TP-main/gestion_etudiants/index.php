<?php
/**
 * Page de connexion - Portail Académique Universitaire (Exercices 4 et 5)
 */
require_once __DIR__ . '/functions.php';

// Si l'utilisateur est déjà connecté, redirection vers la liste (Exercice 4)
if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
// Pré-remplissage du login avec le cookie s'il existe (Exercice 5)
$login = $_COOKIE['dernier_user'] ?? '';

// Traitement POST (Exercice 4)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = isset($_POST['login']) ? nettoyer($_POST['login']) : '';
    $mdp = $_POST['mdp'] ?? '';

    // Vérification dans la constante UTILISATEURS
    if (isset(UTILISATEURS[$login]) && UTILISATEURS[$login] === $mdp) {
        $_SESSION['login'] = $login;

        // Cookie dernier_user valable 7 jours (Exercice 5)
        setcookie('dernier_user', $login, time() + (7 * 24 * 60 * 60), '/');

        header('Location: liste.php');
        exit;
    } else {
        $erreur = "Identifiant ou mot de passe incorrect. Veuillez vérifier vos accès.";
    }
}

$titre = "Connexion &bull; Portail Académique";
include 'header.php';
?>

<div class="app-auth-container">
    <div class="app-card app-auth-card">
        <div class="auth-brand-header">
            <div class="auth-single-logo-box">
                <div class="auth-crest-emblem">🎓</div>
            </div>
            <span class="auth-category-tag">Espace Pédagogique</span>
            <h1 class="auth-main-title">Portail d'Authentification</h1>
            <p class="auth-desc">Accès sécurisé à la gestion académique des étudiants.</p>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="app-alert app-alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span><?= h($erreur) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php" class="app-form" novalidate>
            <div class="form-group">
                <label for="login" class="form-label">Identifiant (Login) <span class="required">*</span></label>
                <div class="input-icon-box">
                    <span class="input-prefix-icon">👤</span>
                    <input
                        type="text"
                        id="login"
                        name="login"
                        class="form-control"
                        placeholder="Ex: admin, prof, etudiant"
                        value="<?= h($login) ?>"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="mdp" class="form-label">Mot de passe <span class="required">*</span></label>
                <div class="input-icon-box">
                    <span class="input-prefix-icon">🔑</span>
                    <input
                        type="password"
                        id="mdp"
                        name="mdp"
                        class="form-control"
                        placeholder="Entrez votre mot de passe"
                        required
                    >
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="app-btn app-btn-primary btn-block">
                    <span>Se connecter au portail</span>
                    <span class="btn-arrow">&rarr;</span>
                </button>
            </div>
        </form>

        <div class="auth-demo-accounts">
            <div class="demo-title">Comptes de test (Constante <code>UTILISATEURS</code>) :</div>
            <div class="demo-grid">
                <div class="demo-account"><span>Administrateur :</span> <code>admin / admin123</code></div>
                <div class="demo-account"><span>Professeur :</span> <code>prof / prof123</code></div>
                <div class="demo-account"><span>Étudiant :</span> <code>etudiant / etu123</code></div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>


