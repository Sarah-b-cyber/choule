<?php
require_once 'src/config/twig.php';
require_once 'src/config/mail.php';  // ✅ Ajout de PHPMailer
require_once 'src/models/class.pdo.inc.php';
require_once 'src/models/ftc.inc.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader('src/vues'));

$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT) ?? 'demandeInscription';
$path_inscription = 'index.php?uc=inscription&action=valideInscription';

$pdo = PdoChoule::getPdoChoule(); // Connexion à la base de données

switch ($action) {
    case 'demandeInscription':
        echo $twig->render('v_inscription.twig', [
            'path_inscription' => $path_inscription
        ]);
        break;

    case 'valideInscription':
        // Récupération des données du formulaire
        $name = filter_input(INPUT_POST, 'name', FILTER_DEFAULT);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_DEFAULT);
        $login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_DEFAULT); // ✅ Correction ici
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $confirm_mdp = filter_input(INPUT_POST, 'confirm_mdp', FILTER_DEFAULT);

        $erreurs = [];

        // Vérifications
        if (!$name || !$prenom || !$login || !$mdp || !$confirm_mdp || !$email) {
            $erreurs[] = 'Veuillez remplir tous les champs.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = 'L\'email n\'est pas valide.';
        } elseif ($mdp !== $confirm_mdp) {
            $erreurs[] = 'Les mots de passe ne correspondent pas.';
        } elseif ($pdo->loginExiste($login)) {
            $erreurs[] = 'Cet identifiant est déjà utilisé.';
        } elseif ($pdo->emailExiste($email)) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        } else {
            if ($pdo->inscrireUtilisateur($login, $mdp, $name, $prenom, $email)) {
                //  Envoi de l'email après inscription réussie
                $sujet = "Bienvenue sur notre site, $prenom ! ";
                $message = "
                    <h2>Bonjour $prenom,</h2>
                    <p>Merci de vous être inscrit sur notre site. Votre compte est maintenant actif !</p>
                    <p>Vous pouvez vous connecter ici : <a href='http://localhost/choule/index.php?uc=connexion'>Connexion</a></p>
                    <p>À bientôt ! 🚀</p>
                ";
            
                envoyerMail($email, $sujet, $message);
            
                // ✅ Afficher un message et rediriger après quelques secondes
                echo $twig->render('v_confirmation.twig', [
                    'message' => "Votre compte a bien été créé ! 🎉 Un email de confirmation vous a été envoyé. 📩",
                    'redirect_url' => 'index.php?uc=connexion'
                    
                ]);
                exit();
            }
            
            
            // Inscription de l'utilisateur
        }

        // Affichage des erreurs si besoin
        echo $twig->render('v_inscription.twig', [
            'erreurs' => $erreurs,
            'path_inscription' => $path_inscription
        ]);
        break;
}
