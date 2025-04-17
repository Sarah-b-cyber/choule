<?php
require_once 'src/config/twig.php';
require_once 'src/config/mail.php';  
require_once 'src/models/class.pdo.inc.php';
require_once 'src/models/ftc.inc.php';

$pdo = PdoChoule::getPdoChoule();
$erreurs = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    if ($email) {
        $user = $pdo->getUserByEmail($email);

        if ($user) {
            $userId = $user['id'];
            $secretKey = "MaSuperCleSecrete123";
            $token = hash_hmac('sha256', $email, $secretKey); // ✅ Correction
            
            // ✅ Correction du lien
            $resetLink = "http://localhost/choule/index.php?uc=mdp_reset&email=" . urlencode($email) . "&token=" . urlencode($token);

            $sujet = "Reinitialisation de votre mot de passe";
            $contenu = "<p>Bonjour {$user['name']},</p>
                        <p>Pour réinitialiser votre mot de passe, cliquez sur le lien suivant :</p>
                        <a href='$resetLink'>Réinitialiser mon mot de passe</a>
                        <p>Ce lien expirera dans 1 heure.</p>";

            if (envoyerMail($email, $sujet, $contenu)) {
                echo $twig->render('v_confirmation.twig', [
                    'message' => "Un email contenant la réinitialisation de votre mot de passe vous a été envoyé.",
                    'redirect_url' => 'index.php?uc=connexion'
                ]);
                exit();
            } else {
                $erreurs[] = "Erreur lors de l'envoi de l'email.";
            }
        } else {
            $erreurs[] = "Aucun utilisateur trouvé avec cet email.";
        }
    } else {
        $erreurs[] = "Veuillez entrer un email valide.";
    }
}

// Affichage de la vue
echo $twig->render('v_mdp_oublie.twig', [
    'erreurs' => $erreurs,
    'message' => $message
]);
