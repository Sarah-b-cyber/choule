<?php
require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';

$pdo = PdoChoule::getPdoChoule();
$erreur = null;

// On récupère email et token soit en GET (lors du clic sur le lien) soit en POST (quand on soumet le formulaire)
$email = $_GET['email'] ?? $_POST['email'] ?? null;
$token = $_GET['token'] ?? $_POST['token'] ?? null;

if (!$email || !$token) {
    die("Lien invalide.");
}

// On génère le token attendu
$secretKey = "MaSuperCleSecrete123"; 
$expectedToken = hash_hmac('sha256', $email, $secretKey);

// On compare les tokens
if (!hash_equals($expectedToken, trim($token))) {
    die("Lien invalide ou expiré.");
}

// On récupère l'utilisateur
$user = $pdo->getUserByEmail($email);
if (!$user) {
    die("Utilisateur non trouvé.");
}

$userId = $user['id'];

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_DEFAULT);
    $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_DEFAULT);

    if ($newPassword && $newPassword === $confirmPassword) {
        // Mise à jour du mot de passe
        $pdo->resetPassword($newPassword, $userId);

        // Affichage confirmation
        echo $twig->render('v_confirmation.twig', [
            'message' => "Mot de passe mis à jour avec succès 🎉",
            'redirect_url' => 'index.php?uc=connexion'
        ]);
        exit();
    } else {
        $erreur = "Les mots de passe ne correspondent pas.";
    }
}

// Affichage du formulaire avec email et token en hidden
echo $twig->render('v_mdp_reset.twig', [
    'erreur' => $erreur,
    'email' => $email,
    'token' => $token
]);
?>
