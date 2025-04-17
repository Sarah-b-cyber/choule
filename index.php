<?php 
session_start(); // Toujours démarrer la session en premier

require_once __DIR__ . '/src/models/class.pdo.inc.php';
require_once __DIR__ . '/src/models/ftc.inc.php';

// Initialisation de Twig
require_once __DIR__ . '/vendor/autoload.php'; // Assure-toi que Twig est bien installé avec Composer
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/src/vues');
$twig = new \Twig\Environment($loader, [
    'cache' => false // Mettre en cache pour la prod, false pour le dev
]);

$pdo = PdoChoule::getPdoChoule(); 

// Récupération du paramètre 'uc' (unité de contrôle) avec une sécurité supplémentaire
$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'accueil';
if (isset($_SESSION['user'])) {
    $temps_inactivite = 600; // 10 minutes (600 secondes)
    
    if (time() - $_SESSION['user']['last_activity'] > $temps_inactivite) {
        session_destroy();
        header('Location: index.php?uc=connexion&action=demandeConnexion&message=inactif');
        exit();
    } else {
        $_SESSION['user']['last_activity'] = time(); // Reset timer
    }
}

// Gérer la déconnexion proprement
if ($uc === 'deconnexion') {
    session_unset();   // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    setcookie(session_name(), '', time() - 42000, '/'); // Supprime le cookie de session
    
    header('Location: index.php?message=deconnecte');
    exit();
}

// Routeur pour charger le bon contrôleur
switch ($uc) {
    case 'connexion':
        include 'src/controleurs/c_connexion.php';
        break;

    case 'accueil':
        include 'src/controleurs/c_accueil.php';
        break;

    case 'accueilfid':
        include 'src/controleurs/c_accueilfid.php';
        break;

    case 'admin':
        include 'src/controleurs/c_admin.php';
        break; 

    case 'admin2':
        include 'src/controleurs/c_admin2.php';
        break; 

    case 'dons': 
        include 'src/controleurs/c_don.php';
        break;

    case 'newdonateur': 
        include 'src/controleurs/c_newdonateur.php';
        break;

    case 'contact': 
        include 'src/controleurs/c_contact.php';
        break;

    case 'merci': 
        include 'src/controleurs/c_merci.php';
        break;

    case 'profil':
        include 'src/controleurs/c_profil.php';
        break;

    case 'mdp_oublie':
         include 'src/controleurs/c_mdp_oublie.php';
          break;

    case 'mdp_reset':
            include 'src/controleurs/c_mdp_reset.php';
            break;

     case 'horaire':
            require_once 'src/controleurs/c_horaires.php';
             $controller = new HorairesController();
            
                // Vérifier si on a une action spécifique
            $action = $_GET['action'] ?? 'afficher';
            
            if ($action === 'upload') {
                $controller->upload();
            } else {
            $controller->afficher();
                }
                break;

    case 'inscription':
                require 'src/controleurs/c_inscription.php';
                break;
            
    default:
        include 'src/controleurs/c_accueil.php';
        break;
}

// Vérification de la session (debug si besoin)
if (!empty($_SESSION)) {
    error_log(print_r($_SESSION, true)); // Enregistre la session dans le log PHP
}

// Passer la session à Twig
echo $twig->render('base.twig', [
    'session' => $_SESSION,
    'message' => $_GET['message'] ?? null
]);
