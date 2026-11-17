<?php
require_once 'functions.php';

// Si déjà connecté, rediriger vers la liste
if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
$dernier_user = $_COOKIE['dernier_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $mdp = $_POST['mdp'] ?? '';

    if (isset(UTILISATEURS[$login]) && UTILISATEURS[$login] === $mdp) {
        $_SESSION['user'] = $login;
        // Créer un cookie valable 7 jours
        setcookie('dernier_user', $login, time() + 7 * 24 * 3600);
        header('Location: liste.php');
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
        $dernier_user = $login; // Conserver la saisie
    }
}

$titre = "Connexion";
include 'header.php';
?>

<div class="card">
    <h2>Connexion</h2>
    <?php if ($erreur): ?>
        <p class="error"><?= h($erreur) ?></p>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div>
            <label for="login">Login :</label>
            <input type="text" id="login" name="login" value="<?= h($dernier_user) ?>" required>
        </div>
        <div>
            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
</div>

<?php include 'footer.php'; ?>